import MainAppShell from '../../Layouts/MainAppShell'
import { usePage, router } from '@inertiajs/react'

export default function ProfileEdit() {
  const { props } = usePage()
  const photos = props.photos || []
  function upload(e) {
    const file = e.target.files?.[0]
    if (!file) return
    const form = new FormData()
    form.append('photo', file)
    router.post(route('app.profile.photos.upload'), form)
  }
  return (
    <MainAppShell>
      <div className="max-w-xl mx-auto">
        <h1 className="text-2xl font-bold mb-4">Edit Profile</h1>
        <form className="space-y-4">
          <div>
            <label className="block text-sm font-medium mb-1">Bio</label>
            <textarea className="w-full border rounded px-3 py-2" rows={4} />
          </div>
          <div>
            <label className="block text-sm font-medium mb-1">Photos</label>
            <input type="file" accept="image/*" onChange={upload} />
            <div className="grid grid-cols-3 gap-2 mt-3">
              {photos.map(p => (
                <div key={p.id} className="relative">
                  <img src={p.url} alt="" className="w-full h-24 object-cover rounded" />
                  <button type="button" className="absolute top-1 right-1 bg-white border rounded px-2 py-1 text-xs" onClick={() => router.delete(route('app.profile.photos.delete', { id: p.id }))}>Delete</button>
                </div>
              ))}
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium mb-1">Prompts</label>
            <input className="w-full border rounded px-3 py-2" placeholder="Add prompt answers" />
          </div>
          <button type="submit" className="px-4 py-2 bg-green-600 text-white rounded">Save</button>
        </form>
      </div>
    </MainAppShell>
  )
}