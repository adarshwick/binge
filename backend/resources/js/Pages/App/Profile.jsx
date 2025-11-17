import MainAppShell from '../../Layouts/MainAppShell'
import { Link } from '@inertiajs/react'

export default function Profile() {
  return (
    <MainAppShell>
      <div className="max-w-xl mx-auto">
        <h1 className="text-2xl font-bold mb-4">My Profile</h1>
        <div className="bg-white border rounded p-4 mb-4">
          <div className="h-32 bg-gray-100 rounded mb-2" />
          <div className="font-medium">Your Name</div>
          <p className="text-gray-600">Short bio goes here.</p>
        </div>
        <div className="flex gap-2">
          <Link href={route('app.profile.edit')} className="px-4 py-2 bg-pink-600 text-white rounded">Edit Profile</Link>
          <Link href={route('dashboard')} className="px-4 py-2 border rounded">Settings</Link>
        </div>
      </div>
    </MainAppShell>
  )
}