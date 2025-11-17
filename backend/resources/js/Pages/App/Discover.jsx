import MainAppShell from '../../Layouts/MainAppShell'
import { router, usePage } from '@inertiajs/react'
import { useState } from 'react'
import Modal from '../../Components/Modal'

export default function Discover() {
  const { props } = usePage()
  const [cards, setCards] = useState(props.cards || [])
  const [superLikeModal, setSuperLikeModal] = useState(false)
  const [boostModal, setBoostModal] = useState(false)
  const [pendingUser, setPendingUser] = useState(null)
  async function act(id, type) {
    if (type === 'super_like') {
      setPendingUser(id)
      const r = await fetch(route('app.like'), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest','Content-Type':'application/json' }, body: JSON.stringify({ to_user_id: id, type }) })
      if (r.status === 402) { setSuperLikeModal(true); return }
      setCards(prev => prev.filter(c => c.id !== id))
      return
    }
    router.post(route('app.like'), { to_user_id: id, type }, { preserveScroll: true })
    setCards(prev => prev.filter(c => c.id !== id))
  }
  async function doBoost() {
    setBoostModal(true)
  }
  async function confirmBoost() {
    const r = await fetch(route('app.boost'), { method: 'POST', headers: { 'X-Requested-With':'XMLHttpRequest' } })
    if (r.status === 402) return
    setBoostModal(false)
  }
  async function confirmSuperLike() {
    if (!pendingUser) { setSuperLikeModal(false); return }
    const r = await fetch(route('app.like'), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest','Content-Type':'application/json' }, body: JSON.stringify({ to_user_id: pendingUser, type: 'super_like' }) })
    if (r.status === 402) return
    setCards(prev => prev.filter(c => c.id !== pendingUser))
    setPendingUser(null)
    setSuperLikeModal(false)
  }
  return (
    <MainAppShell>
      <div className="max-w-2xl mx-auto">
        <h1 className="text-2xl font-bold mb-4">Discover</h1>
        <div className="space-y-4">
          {cards.map(c => (
            <div key={c.id} className="bg-white rounded-2xl shadow hover:shadow-lg overflow-hidden">
              <div className="relative">
                {c.photo ? (
                  <img src={c.photo} alt={c.name} loading="lazy" className="w-full h-64 object-cover" />
                ) : (
                  <div className="w-full h-64 bg-gradient-to-br from-pink-100 to-purple-100" />
                )}
                <div className="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent p-4 text-white">
                  <div className="flex items-center gap-2 text-lg font-semibold">
                    <span>{c.name}</span>
                    {c.boosted && <span className="px-2 py-0.5 text-xs bg-purple-600/80 rounded">Boosted</span>}
                  </div>
                  <div className="text-sm opacity-90">{typeof c.distanceKm === 'number' ? `${c.distanceKm} km away` : ''}</div>
                </div>
              </div>
              <div className="p-4 flex items-center justify-between">
                <div className="flex items-center gap-3">
                  <button className="px-4 py-2 rounded-full border border-gray-300" onClick={() => act(c.id, 'pass')}>Pass</button>
                  <button className="px-4 py-2 rounded-full bg-pink-600 text-white" onClick={() => act(c.id, 'like')}>Like</button>
                  <button className="px-4 py-2 rounded-full bg-pink-500 text-white" onClick={() => act(c.id, 'super_like')}>Super Like</button>
                </div>
                <button className="px-4 py-2 rounded-full bg-purple-600 text-white" onClick={() => doBoost()}>Boost</button>
              </div>
            </div>
          ))}
        </div>
        <Modal show={superLikeModal} onClose={() => setSuperLikeModal(false)}>
          <div className="p-4">
            <div className="text-lg font-semibold mb-2">Get more Super Likes</div>
            <p className="text-gray-600 mb-4">Buy 1 Super Like for {usePage().props.settings?.price_super_like || 50} Credits?</p>
            <div className="flex justify-end gap-2">
              <a href={route('app.premium')} className="px-4 py-2 bg-pink-600 text-white rounded">Buy Credits</a>
              <button className="px-4 py-2 border rounded" onClick={() => setSuperLikeModal(false)}>No Thanks</button>
            </div>
          </div>
        </Modal>
        <Modal show={boostModal} onClose={() => setBoostModal(false)}>
          <div className="p-4">
            <div className="text-lg font-semibold mb-2">Boost your profile</div>
            <p className="text-gray-600 mb-4">Boost for 30 minutes! Cost: {usePage().props.settings?.price_boost || 100} Credits.</p>
            <div className="flex justify-end gap-2">
              <button className="px-4 py-2 bg-purple-600 text-white rounded" onClick={confirmBoost}>Boost Now</button>
              <a href={route('app.premium')} className="px-4 py-2 border rounded">Get Credits</a>
            </div>
          </div>
        </Modal>
      </div>
    </MainAppShell>
  )
}