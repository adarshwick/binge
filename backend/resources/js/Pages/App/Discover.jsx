import MainAppShell from '../../Layouts/MainAppShell'
import { router, usePage } from '@inertiajs/react'
import { useState } from 'react'

export default function Discover() {
  const { props } = usePage()
  const [cards, setCards] = useState(props.cards || [])
  function act(id, type) {
    router.post(route('app.like'), { to_user_id: id, type }, { preserveScroll: true })
    setCards(prev => prev.filter(c => c.id !== id))
  }
  return (
    <MainAppShell>
      <div className="max-w-md mx-auto">
        <h1 className="text-2xl font-bold mb-4">Discover</h1>
        <div className="space-y-3">
          {cards.map(c => (
            <div key={c.id} className="bg-white border rounded p-4">
              {c.photo && (
                <img src={c.photo} alt={c.name} loading="lazy" className="w-full h-48 object-cover rounded mb-2" />
              )}
              <div className="font-medium flex items-center gap-2">
                {c.name}
                {c.boosted && <span className="px-2 py-0.5 text-xs bg-purple-100 text-purple-700 rounded">Boosted</span>}
              </div>
              <div className="text-sm text-gray-500">{c.subtitle}{typeof c.distanceKm === 'number' ? ` • ${c.distanceKm} km away` : ''}</div>
              <div className="mt-3 flex items-center gap-3">
                <button className="px-4 py-2 rounded border" onClick={() => act(c.id, 'pass')}>Pass</button>
                <button className="px-4 py-2 rounded bg-blue-600 text-white" onClick={() => act(c.id, 'super_like')}>Super Like</button>
                <button className="px-4 py-2 rounded bg-green-600 text-white" onClick={() => act(c.id, 'like')}>Like</button>
                <button className="px-4 py-2 rounded bg-purple-600 text-white" onClick={() => router.post('/app/boost', {})}>Boost</button>
              </div>
            </div>
          ))}
        </div>
      </div>
    </MainAppShell>
  )
}