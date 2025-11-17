import MainAppShell from '../../Layouts/MainAppShell'
import { usePage } from '@inertiajs/react'

export default function Matches() {
  const { props } = usePage()
  const items = props.items || []
  return (
    <MainAppShell>
      <div className="max-w-4xl mx-auto">
        <h1 className="text-2xl font-bold mb-4">Matches</h1>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {items.map(({ id, user }) => (
            <a key={user.id} href={route('app.user', { user_id: user.id })} className="block bg-white rounded-2xl shadow hover:shadow-lg overflow-hidden text-center">
              {user.photo ? (
                <img src={user.photo} alt={user.name} loading="lazy" className="w-full h-32 object-cover" />
              ) : (
                <div className="h-32 bg-gradient-to-br from-pink-100 to-purple-100" />
              )}
              <div className="p-3 font-medium">{user?.name}</div>
            </a>
          ))}
        </div>
      </div>
    </MainAppShell>
  )
}