import MainAppShell from '../../Layouts/MainAppShell'
import { usePage } from '@inertiajs/react'

export default function LikedMe() {
  const { props } = usePage()
  const items = props.items || []
  const needsUpgrade = props.needsUpgrade
  return (
    <MainAppShell>
      <div className="max-w-4xl mx-auto">
        <h1 className="text-2xl font-bold mb-4">People Who Liked You</h1>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {items.map(u => (
            <div key={u.id} className="bg-white rounded-2xl shadow text-center overflow-hidden">
              {u.photo ? (
                <img src={u.photo} alt={u.name} loading="lazy" className={(needsUpgrade ? 'blur-sm' : '') + ' w-full h-32 object-cover'} />
              ) : (
                <div className={(needsUpgrade ? 'blur-sm' : '') + ' h-32 bg-gradient-to-br from-pink-100 to-purple-100'} />
              )}
              <div className={(needsUpgrade ? 'blur-sm' : '') + ' p-3 font-medium'}>{u.name}</div>
            </div>
          ))}
        </div>
        {needsUpgrade && (
          <div className="mt-4 text-center">
            <a href={route('app.premium')} className="inline-block px-4 py-2 bg-pink-600 text-white rounded-full">Upgrade to see who liked you</a>
          </div>
        )}
      </div>
    </MainAppShell>
  )
}
