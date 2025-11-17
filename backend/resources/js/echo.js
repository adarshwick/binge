import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { initializeApp } from 'firebase/app'
import { getFirestore, doc, onSnapshot } from 'firebase/firestore'

const settings = window?.Inertia?.page?.props?.settings || {}

if (settings.chat_provider === 'firebase') {
  const firebaseConfig = {
    apiKey: settings.firebase_api_key,
    projectId: settings.firebase_project_id,
    appId: settings.firebase_app_id,
  }
  const app = initializeApp(firebaseConfig)
  const db = getFirestore(app)
  window.ChatProvider = {
    subscribeConversation: (matchId, cb) => onSnapshot(doc(db, 'conversations', String(matchId)), (snap) => cb(snap.data())),
  }
} else {
  window.Pusher = Pusher
  window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_REVERB_KEY || 'local',
    wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
    wsPort: Number(import.meta.env.VITE_REVERB_PORT || 8080),
    forceTLS: false,
    enabledTransports: ['ws'],
  })
}
