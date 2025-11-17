import MainAppShell from '../../Layouts/MainAppShell'
import { Link, usePage } from '@inertiajs/react'

export default function Profile() {
  const { props } = usePage()
  const user = props.auth?.user || {}
  const photos = props.photos || []
  return (
    <MainAppShell>
      <div className="auth-card">
        <h1 className="text-2xl font-bold mb-4">My Profile</h1>
        <div className="card mb-4">
          <div className="grid grid-cols-3 gap-2 mb-2">
            {photos.length ? photos.slice(0,3).map(p => (
              <img key={p.id} src={p.url} alt="" style={{width:'100%',height:'6rem',objectFit:'cover',borderRadius:'1rem'}} />
            )) : <div className="h-24 bg-gray-100 rounded col-span-3" />}
          </div>
          <div className="font-medium">{user.name}</div>
          <p className="text-gray-600">{user.bio || 'Add a short bio in Edit Profile.'}</p>
        </div>
        <div className="flex gap-2">
          <Link href={route('app.profile.edit')} className="btn btn-primary">Edit Profile</Link>
          <Link href={route('dashboard')} className="btn btn-ghost">Settings</Link>
        </div>
      </div>
    </MainAppShell>
  )
}