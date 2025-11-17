import MainAppShell from '../../Layouts/MainAppShell'
import { usePage } from '@inertiajs/react'

export default function LikedMe() {
  const { props } = usePage()
  const items = props.items || []
  const needsUpgrade = props.needsUpgrade
  return (
    <MainAppShell>
      <div className="auth-card">
        <h1 className="text-2xl font-bold mb-4">People Who Liked You</h1>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {items.map(u => (
            <div key={u.id} className="card text-center overflow-hidden">
              {u.photo ? (
                <img src={u.photo} alt={u.name} loading="lazy" className={(needsUpgrade ? 'blur-sm' : '') + ' w-full h-32 object-cover'} />
              ) : (
                <div className={(needsUpgrade ? 'blur-sm' : '') + ' h-32'} style={{backgroundImage:'linear-gradient(135deg,#ffe4ef,#f3e8ff)'}} />
              )}
              <div className={(needsUpgrade ? 'blur-sm' : '') + ' p-3 font-medium'}>{u.name}</div>
            </div>
          ))}
        </div>
        {needsUpgrade && (
          <div className="mt-4 text-center">
            <a href={route('app.premium')} className="btn btn-primary">Upgrade to see who liked you</a>
          </div>
        )}
      </div>
    </MainAppShell>
  )
}
