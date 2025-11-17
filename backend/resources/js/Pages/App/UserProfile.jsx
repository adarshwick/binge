import MainAppShell from '../../Layouts/MainAppShell'
import { router, usePage } from '@inertiajs/react'
import Modal from '../../Components/Modal'
import { useState } from 'react'

export default function UserProfile() {
  const { props } = usePage()
  const user = props.user || {}
  const photos = props.photos || []
  const answers = props.answers || []
  const [reportOpen, setReportOpen] = useState(false)
  const [reason, setReason] = useState('')
  return (
    <MainAppShell>
      <div className="auth-card">
        <div className="flex items-center justify-between mb-3">
          <h1 className="text-2xl font-bold">{user.name}</h1>
          <div className="flex gap-2">
            <button className="btn btn-ghost" onClick={() => router.post(route('app.block'), { user_id: user.id })}>Block</button>
            <button className="btn btn-ghost" onClick={() => router.post(route('app.match.unmatch', { user_id: user.id }))}>Unmatch</button>
            <button className="btn btn-danger" onClick={() => setReportOpen(true)}>Report</button>
          </div>
        </div>
        {user.bio && <p className="text-gray-700 mb-3">{user.bio}</p>}
        <div className="grid grid-cols-3 gap-2 mb-4">
          {photos.map(p => (
            <img key={p.id} src={p.url} alt="" style={{width:'100%',height:'6rem',objectFit:'cover',borderRadius:'1rem'}} />
          ))}
        </div>
        {answers.length > 0 && (
          <div className="card space-y-2">
            {answers.map((a, i) => (
              <div key={i}>
                <div className="text-sm font-medium">{a.prompt}</div>
                <div className="text-gray-700">{a.answer}</div>
              </div>
            ))}
          </div>
        )}
      </div>
      <Modal show={reportOpen} onClose={() => setReportOpen(false)}>
        <div className="p-4">
          <div className="text-lg font-semibold mb-2">Report User</div>
          <input className="input mb-3" placeholder="Reason" value={reason} onChange={e => setReason(e.target.value)} />
          <div className="flex justify-end gap-2">
            <button className="btn btn-ghost" onClick={() => setReportOpen(false)}>Cancel</button>
            <button className="btn btn-danger" onClick={() => {
              if (!reason.trim()) return
              router.post(route('app.report'), { user_id: user.id, reason })
              setReportOpen(false)
              setReason('')
            }}>Submit</button>
          </div>
        </div>
      </Modal>
    </MainAppShell>
  )
}
