import MainAppShell from '../../Layouts/MainAppShell'
import { usePage, router } from '@inertiajs/react'
import { useState } from 'react'

export default function ProfileEdit() {
  const { props } = usePage()
  const photos = props.photos || []
  const [bio, setBio] = useState(props.auth?.user?.bio || '')
  function upload(e) {
    const file = e.target.files?.[0]
    if (!file) return
    const form = new FormData()
    form.append('photo', file)
    router.post(route('app.profile.photos.upload'), form)
  }
  return (
    <MainAppShell>
      <div className="max-w-2xl mx-auto">
        <h1 className="text-2xl font-bold mb-4">Edit Profile</h1>
        <form className="space-y-4 card" onSubmit={e => { e.preventDefault(); router.post(route('app.profile.bio'), { bio }) }}>
          <div>
            <label className="block text-sm font-medium mb-1">Bio</label>
            <textarea className="textarea" rows={4} value={bio} onChange={e => setBio(e.target.value)} />
          </div>
          <div>
            <label className="block text-sm font-medium mb-1">Photos</label>
            <input type="file" accept="image/*" onChange={upload} />
            <div className="grid grid-cols-3 gap-2 mt-3">
              {photos.map(p => (
                <div key={p.id} className="relative">
                  <img src={p.url} alt="" style={{width:'100%',height:'6rem',objectFit:'cover',borderRadius:'1rem'}} />
                  <button type="button" className="btn btn-ghost" style={{position:'absolute',top:'.25rem',right:'.25rem',padding:'.25rem .5rem',fontSize:'.75rem'}} onClick={() => router.delete(route('app.profile.photos.delete', { id: p.id }))}>Delete</button>
                </div>
              ))}
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium mb-1">Prompts</label>
            <input className="input" placeholder="Add prompt answers" />
          </div>
          <button type="submit" className="btn">Save</button>
        </form>
      </div>
    </MainAppShell>
  )
}