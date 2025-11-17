import MainAppShell from '../../Layouts/MainAppShell'
import { router, usePage } from '@inertiajs/react'

export default function UserProfile() {
  const { props } = usePage()
  const user = props.user || {}
  const photos = props.photos || []
  const answers = props.answers || []
  return (
    <MainAppShell>
      <div className="max-w-xl mx-auto">
        <div className="flex items-center justify-between mb-3">
          <h1 className="text-2xl font-bold">{user.name}</h1>
          <div className="flex gap-2">
            <button className="px-3 py-2 border rounded" onClick={() => router.post(route('app.block'), { user_id: user.id })}>Block</button>
            <button className="px-3 py-2 border rounded" onClick={() => {
              const reason = prompt('Enter report reason') || 'inappropriate'
              router.post(route('app.report'), { user_id: user.id, reason })
            }}>Report</button>
          </div>
        </div>
        {user.bio && <p className="text-gray-700 mb-3">{user.bio}</p>}
        <div className="grid grid-cols-3 gap-2 mb-4">
          {photos.map(p => (
            <img key={p.id} src={p.url} alt="" className="w-full h-24 object-cover rounded" />
          ))}
        </div>
        {answers.length > 0 && (
          <div className="space-y-2">
            {answers.map((a, i) => (
              <div key={i}>
                <div className="text-sm font-medium">{a.prompt}</div>
                <div className="text-gray-700">{a.answer}</div>
              </div>
            ))}
          </div>
        )}
      </div>
    </MainAppShell>
  )
}