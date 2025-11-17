import MainAppShell from '../../Layouts/MainAppShell'
import { usePage } from '@inertiajs/react'

export default function Matches() {
  const { props } = usePage()
  const items = props.items || []
  return (
    <MainAppShell>
      <h1 className="text-2xl font-bold mb-4">Matches</h1>
  <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
        {items.map(({ id, user }) => (
          <a key={user.id} href={route('app.user', { user_id: user.id })} className="block bg-white border rounded p-4 text-center">
            {user.photo ? (
              <img src={user.photo} alt={user.name} loading="lazy" className="w-full h-24 object-cover rounded mb-2" />
            ) : (
              <div className="h-24 bg-gray-100 rounded mb-2" />
            )}
            <div className="font-medium">{user?.name}</div>
          </a>
        ))}
  </div>
    </MainAppShell>
  )
}