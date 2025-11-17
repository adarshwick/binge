import MainAppShell from '../../Layouts/MainAppShell'
import { usePage } from '@inertiajs/react'

export default function Matches() {
  const { props } = usePage()
  const items = props.items || []
  return (
    <MainAppShell>
      <div className="auth-card">
        <h1 className="text-2xl font-bold mb-4">Matches</h1>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {items.map(({ id, user }) => (
            <a key={user.id} href={route('app.user', { user_id: user.id })} className="card text-center overflow-hidden">
              {user.photo ? (<img src={user.photo} alt={user.name} loading="lazy" style={{width:'100%',height:'8rem',objectFit:'cover'}} />) : (<div style={{height:'8rem',backgroundImage:'linear-gradient(135deg,#ffe4ef,#f3e8ff)'}} />)}
              <div className="p-3 font-medium">{user?.name}</div>
            </a>
          ))}
        </div>
      </div>
    </MainAppShell>
  )
}