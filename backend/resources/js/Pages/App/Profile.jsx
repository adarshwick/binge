import MainAppShell from '../../Layouts/MainAppShell'
import { Link, usePage } from '@inertiajs/react'

export default function Profile() {
  const { props } = usePage()
  const user = props.auth?.user || {}
  const photos = props.photos || []
  return (
    <MainAppShell>
      <div className="max-w-xl mx-auto">
        <h1 className="text-2xl font-bold mb-4">My Profile</h1>
        <div className="bg-white border rounded p-4 mb-4">
          <div className="grid grid-cols-3 gap-2 mb-2">
            {photos.length ? photos.slice(0,3).map(p => (
              <img key={p.id} src={p.url} alt="" className="w-full h-24 object-cover rounded" />
            )) : <div className="h-24 bg-gray-100 rounded col-span-3" />}
          </div>
          <div className="font-medium">{user.name}</div>
          <p className="text-gray-600">{user.bio || 'Add a short bio in Edit Profile.'}</p>
        </div>
        <div className="flex gap-2">
          <Link href={route('app.profile.edit')} className="px-4 py-2 bg-pink-600 text-white rounded">Edit Profile</Link>
          <Link href={route('dashboard')} className="px-4 py-2 border rounded">Settings</Link>
        </div>
      </div>
    </MainAppShell>
  )
}