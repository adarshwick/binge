import MainAppShell from '../../Layouts/MainAppShell'
import { usePage } from '@inertiajs/react'

export default function LikedMe() {
  const { props } = usePage()
  const items = props.items || []
  return (
    <MainAppShell>
      <h1 className="text-2xl font-bold mb-4">People Who Liked You</h1>
  <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
        {items.map(u => (
          <div key={u.id} className="bg-white border rounded p-4 text-center">
            {u.photo ? (
              <img src={u.photo} alt={u.name} loading="lazy" className="w-full h-24 object-cover rounded mb-2" />
            ) : (
              <div className="h-24 bg-gray-100 rounded mb-2" />
            )}
            <div className="font-medium">{u.name}</div>
          </div>
        ))}
  </div>
    </MainAppShell>
  )
}
