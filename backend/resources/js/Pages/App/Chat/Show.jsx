import ChatLayout from './Layout'
import { router, usePage } from '@inertiajs/react'
import { useEffect, useRef, useState } from 'react'

export default function Show({ match_id }) {
  const { props } = usePage()
  const messages = props.messages || []
  const user = props.auth?.user
  const [msgs, setMsgs] = useState(messages)
  const [hasMore, setHasMore] = useState(props.has_more)
  const [typing, setTyping] = useState(false)
  const [inputValue, setInputValue] = useState('')
  const typingTimerRef = useRef(null)
  const [callVisible, setCallVisible] = useState(false)
  const [audioOnly, setAudioOnly] = useState(false)
  const localVideoRef = useRef(null)
  const remoteVideoRef = useRef(null)
  const pcRef = useRef(null)
  const localStreamRef = useRef(null)
  const pollTimerRef = useRef(null)
  const chimeSessionRef = useRef(null)
  const agoraClientRef = useRef(null)
  const agoraLocalTracksRef = useRef(null)
  const [firebaseMessages, setFirebaseMessages] = useState([])
  useEffect(() => {
    if (!window.Echo) return
    window.Echo.private(`match.${match_id}`).listen('MessageSent', (e) => {
      router.reload({ only: ['messages'], preserveScroll: true })
    })
    window.Echo.private(`match.${match_id}`).listen('Typing', (e) => {
      if (e?.userId !== user?.id) {
        setTyping(true)
        setTimeout(() => setTyping(false), 1500)
      }
    })
    window.Echo.private(`match.${match_id}`).listen('MessagesRead', (e) => {
      router.reload({ only: ['messages'], preserveScroll: true })
    })
  }, [match_id])
  useEffect(() => {
    setMsgs(props.messages || [])
    setHasMore(props.has_more)
  }, [props.messages, props.has_more])
  useEffect(() => {
    if (usePage().props.settings?.chat_provider === 'firebase' && window.ChatProvider?.subscribeMessages) {
      const unsub = window.ChatProvider.subscribeMessages(match_id, setFirebaseMessages)
      return () => unsub && unsub()
    }
  }, [match_id])
  async function startWebRTCCall(audio) {
    setAudioOnly(!!audio)
    setCallVisible(true)
    const stream = await navigator.mediaDevices.getUserMedia({ video: !audio, audio: true })
    localStreamRef.current = stream
    if (localVideoRef.current && !audio) localVideoRef.current.srcObject = stream
    const s = usePage().props.settings || {}
    let iceServers = [{ urls: 'stun:stun.l.google.com:19302' }]
    if (s.webrtc_ice_servers) {
      try { iceServers = JSON.parse(s.webrtc_ice_servers) } catch (e) {}
    } else {
      if (s.webrtc_stun_url) iceServers = [{ urls: s.webrtc_stun_url }]
      if (s.webrtc_turn_url && s.webrtc_turn_username && s.webrtc_turn_password) iceServers.push({ urls: s.webrtc_turn_url, username: s.webrtc_turn_username, credential: s.webrtc_turn_password })
    }
    const pc = new RTCPeerConnection({ iceServers })
    pcRef.current = pc
    stream.getTracks().forEach(t => pc.addTrack(t, stream))
    pc.ontrack = e => {
      if (remoteVideoRef.current) remoteVideoRef.current.srcObject = e.streams[0]
    }
    const offer = await pc.createOffer()
    await pc.setLocalDescription(offer)
    await fetch(route('app.webrtc.offer', { match_id }), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' }, body: JSON.stringify({ sdp: offer.sdp }) })
    pollTimerRef.current = setInterval(async () => {
      const r = await fetch(route('app.webrtc.poll', { match_id }))
      const j = await r.json()
      if (j.answer && pcRef.current) {
        await pcRef.current.setRemoteDescription({ type: 'answer', sdp: j.answer })
        clearInterval(pollTimerRef.current)
      }
    }, 1500)
  }
  async function acceptWebRTCCall() {
    setCallVisible(true)
    const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true })
    localStreamRef.current = stream
    if (localVideoRef.current) localVideoRef.current.srcObject = stream
    const s = usePage().props.settings || {}
    let iceServers = [{ urls: 'stun:stun.l.google.com:19302' }]
    if (s.webrtc_ice_servers) {
      try { iceServers = JSON.parse(s.webrtc_ice_servers) } catch (e) {}
    } else {
      if (s.webrtc_stun_url) iceServers = [{ urls: s.webrtc_stun_url }]
      if (s.webrtc_turn_url && s.webrtc_turn_username && s.webrtc_turn_password) iceServers.push({ urls: s.webrtc_turn_url, username: s.webrtc_turn_username, credential: s.webrtc_turn_password })
    }
    const pc = new RTCPeerConnection({ iceServers })
    pcRef.current = pc
    stream.getTracks().forEach(t => pc.addTrack(t, stream))
    pc.ontrack = e => {
      if (remoteVideoRef.current) remoteVideoRef.current.srcObject = e.streams[0]
    }
    const r = await fetch(route('app.webrtc.poll', { match_id }))
    const j = await r.json()
    if (j.offer) {
      await pc.setRemoteDescription({ type: 'offer', sdp: j.offer })
      const ans = await pc.createAnswer()
      await pc.setLocalDescription(ans)
      await fetch(route('app.webrtc.answer', { match_id }), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' }, body: JSON.stringify({ sdp: ans.sdp }) })
    }
  }
  function endWebRTCCall() {
    if (pollTimerRef.current) clearInterval(pollTimerRef.current)
    if (pcRef.current) pcRef.current.close()
    if (localStreamRef.current) localStreamRef.current.getTracks().forEach(t => t.stop())
    pcRef.current = null
    localStreamRef.current = null
    setCallVisible(false)
  }

  async function startChimeVideo() {
    const sdk = await import('amazon-chime-sdk-js')
    const r = await fetch(route('app.chime.join', { match_id }), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const j = await r.json()
    setCallVisible(true)
    const logger = new sdk.ConsoleLogger('Chime', 1)
    const deviceController = new sdk.DefaultDeviceController(logger)
    const configuration = new sdk.MeetingSessionConfiguration(j.meeting, j.attendee)
    const meetingSession = new sdk.DefaultMeetingSession(configuration, logger, deviceController)
    chimeSessionRef.current = meetingSession
    const audioVideo = meetingSession.audioVideo
    const devices = await audioVideo.listAudioInputDevices()
    if (devices[0]) await audioVideo.chooseAudioInputDevice(devices[0].deviceId)
    const vDevices = await audioVideo.listVideoInputDevices()
    if (vDevices[0]) await audioVideo.chooseVideoInputDevice(vDevices[0].deviceId)
    const observer = {
      videoTileDidUpdate: tile => {
        if (!tile || !tile.tileId) return
        const el = tile.localTile ? localVideoRef.current : remoteVideoRef.current
        if (el) audioVideo.bindVideoElement(tile.tileId, el)
      }
    }
    audioVideo.addObserver(observer)
    await audioVideo.start()
    await audioVideo.startLocalVideoTile()
  }

  async function startAgoraCall() {
    const { appId, token, channel } = await fetch(route('app.video.start', { match_id }), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json())
    const AgoraRTC = await import('agora-rtc-sdk-ng')
    const client = AgoraRTC.createClient({ mode: 'rtc', codec: 'vp8' })
    agoraClientRef.current = client
    await client.join(appId || '', channel, token || null, user?.id)
    const micTrack = await AgoraRTC.createMicrophoneAudioTrack()
    const camTrack = await AgoraRTC.createCameraVideoTrack()
    agoraLocalTracksRef.current = [micTrack, camTrack]
    setCallVisible(true)
    if (localVideoRef.current) localVideoRef.current.srcObject = new MediaStream([camTrack.getMediaStreamTrack()])
    await client.publish([micTrack, camTrack])
    client.on('user-published', async (remoteUser, mediaType) => {
      await client.subscribe(remoteUser, mediaType)
      if (mediaType === 'video' && remoteVideoRef.current) {
        const track = remoteUser.videoTrack
        track.play(remoteVideoRef.current)
      }
    })
  }

  function endProviderCall() {
    if (chimeSessionRef.current) {
      chimeSessionRef.current.audioVideo.stop()
      chimeSessionRef.current.audioVideo.stopLocalVideoTile()
      chimeSessionRef.current = null
    }
    if (agoraClientRef.current) {
      if (agoraLocalTracksRef.current) {
        agoraLocalTracksRef.current.forEach(t => t.close())
        agoraLocalTracksRef.current = null
      }
      agoraClientRef.current.leave()
      agoraClientRef.current = null
    }
    setCallVisible(false)
  }
  async function send() {
    if (!inputValue.trim()) return
    if (usePage().props.settings?.chat_provider === 'firebase' && window.ChatProvider?.sendMessage) {
      await window.ChatProvider.sendMessage(match_id, { sender_id: user?.id, type: 'text', content: inputValue })
    } else {
      router.post(route('app.chat.send', { match_id }), { content: inputValue }, { preserveScroll: true })
    }
    setInputValue('')
  }
  return (
    <ChatLayout>
      <div className="bg-white border rounded h-96 flex flex-col">
        <div className="border-b px-4 py-2 flex items-center justify-between">
          <div className="font-semibold flex items-center gap-3">
            <span>Conversation {match_id}</span>
            {usePage().props.other_id && (
              <a className="text-blue-600 underline" href={route('app.user', { user_id: usePage().props.other_id })}>View Profile</a>
            )}
          </div>
          <div className="flex items-center gap-3">
            {typing && <div className="text-sm text-gray-500">Typing...</div>}
            {usePage().props.settings?.feature_video_chat && (
              <>
                {usePage().props.settings?.video_provider === 'webrtc' ? (
                  <>
                    <button className="text-blue-600" onClick={() => startWebRTCCall(false)}>Start Video</button>
                    <button className="text-blue-600" onClick={() => startWebRTCCall(true)}>Start Audio</button>
                    <button className="text-blue-600" onClick={acceptWebRTCCall}>Accept Call</button>
                    {callVisible && <button className="text-red-600" onClick={endWebRTCCall}>End</button>}
                  </>
                ) : usePage().props.settings?.video_provider === 'chime' ? (
                  <>
                    <button className="text-blue-600" onClick={startChimeVideo}>Start Video</button>
                    {callVisible && <button className="text-red-600" onClick={endProviderCall}>End</button>}
                  </>
                ) : (
                  <>
                    <button className="text-blue-600" onClick={startAgoraCall}>Start Video</button>
                    {callVisible && <button className="text-red-600" onClick={endProviderCall}>End</button>}
                  </>
                )}
              </>
            )}
          </div>
        </div>
        <div className="flex-1 p-4 space-y-3 overflow-auto">
          {(usePage().props.settings?.chat_provider === 'firebase' && window.ChatProvider)
            ? firebaseRender()
            : (
              <>
                {hasMore && (
                  <button className="text-xs text-blue-600" onClick={async () => {
                    const before = msgs[0]?.id
                    const r = await fetch(route('app.chat.messages', { match_id, before }), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    const j = await r.json()
                    setMsgs([...(j.messages || []), ...msgs])
                    setHasMore(!!j.has_more)
                  }}>Load more</button>
                )}
                {msgs.map(m => (
                  <div key={m.id} className={(m.sender_id === user?.id ? 'self-end bubble bubble-me' : 'self-start bubble bubble-other')}>
                    {m.type === 'voice' ? (<audio src={m.content} controls />) : (m.type === 'image' ? (<img src={m.content} alt="image" className="max-w-full rounded"/>) : m.content)}
                    {m.sender_id === user?.id && (
                      <div className="mt-1 text-[10px] opacity-75">
                        {m.read_at ? 'Read' : (m.delivered_at ? 'Delivered' : 'Sent')}
                      </div>
                    )}
                  </div>
                ))}
              </>
            )}
        </div>
        {callVisible && (
          <div className="p-2 grid grid-cols-2 gap-2 border-t">
            {!audioOnly && <video ref={localVideoRef} autoPlay muted className="w-full h-40 bg-black" />}
            <video ref={remoteVideoRef} autoPlay className="w-full h-40 bg-black" />
          </div>
        )}
        <div className="border-t p-3 flex items-center gap-2">
          <input className="input" placeholder="Type a message" value={inputValue} onChange={e => {
            setInputValue(e.target.value)
            if (typingTimerRef.current) clearTimeout(typingTimerRef.current)
            typingTimerRef.current = setTimeout(() => {
              router.post(route('app.chat.typing', { match_id }), {}, { preserveScroll: true })
            }, 400)
          }} />
          <button className="btn btn-ghost">GIF</button>
          <input id="image_input" type="file" accept="image/*" className="hidden" onChange={async e => {
            const file = e.target.files?.[0]
            if (!file) return
            const form = new FormData()
            form.append('image', file)
            if (usePage().props.settings?.chat_provider === 'firebase' && window.ChatProvider?.sendMessage) {
              const r = await fetch(route('app.chat.image', { match_id }), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: form })
              const j = await r.json()
              await window.ChatProvider.sendMessage(match_id, { sender_id: user?.id, type: 'image', content: j.url })
            } else {
              router.post(route('app.chat.image', { match_id }), form, { preserveScroll: true })
            }
            e.target.value = ''
          }} />
          <button className="btn btn-ghost" onClick={() => document.getElementById('image_input').click()}>Image</button>
          {usePage().props.settings?.feature_voice_notes && (
            <>
              <input id="voice_input" type="file" accept="audio/*" className="hidden" onChange={e => {
                const file = e.target.files?.[0]
                if (!file) return
                const form = new FormData()
                form.append('voice', file)
                router.post(route('app.chat.voice', { match_id }), form, { preserveScroll: true })
              }} />
              <button className="btn btn-ghost" onClick={() => document.getElementById('voice_input').click()}>Voice</button>
            </>
          )}
          <button className="btn btn-primary" onClick={send}>Send</button>
        </div>
      </div>
    </ChatLayout>
  )
}
  function firebaseRender() {
    return firebaseMessages.map(m => (
      <div key={m.id} className={(m.sender_id === user?.id ? 'self-end bg-pink-600 text-white' : 'self-start bg-gray-100 text-gray-900') + ' max-w-xs px-3 py-2 rounded'}>
        {m.type === 'voice' ? (<audio src={m.content} controls />) : (m.type === 'image' ? (<img src={m.content} alt="image" className="max-w-full rounded"/>) : m.content)}
      </div>
    ))
  }