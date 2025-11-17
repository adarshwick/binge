import MainAppShell from '../../Layouts/MainAppShell'
import { router } from '@inertiajs/react'

export default function Premium({ mode, plans = [], creditPacks = [], subscription }) {
  const params = new URLSearchParams(window.location.search)
  const success = params.get('success') === '1'
  const canceled = params.get('canceled') === '1'
  return (
    <MainAppShell>
      <div className="max-w-4xl mx-auto">
        <h1 className="text-2xl font-bold mb-2">Go Premium</h1>
        <p className="text-gray-600 mb-2">Business Model: {mode}</p>
        <p className="text-gray-600 mb-6">Credits: {window?.Inertia?.page?.props?.auth?.creditBalance ?? 0}</p>
        {success && (
          <div className="mb-4 p-3 border rounded bg-green-50 text-green-700">Payment succeeded. Your purchase will reflect shortly.</div>
        )}
        {canceled && (
          <div className="mb-4 p-3 border rounded bg-yellow-50 text-yellow-700">Payment canceled.</div>
        )}
        {subscription && (
          <div className="mb-6 p-4 border rounded bg-green-50 text-green-700">
            <div className="font-medium">Active Plan: {subscription.plan}</div>
            {subscription.ends_at && <div>Expires: {new Date(subscription.ends_at).toLocaleString()}</div>}
          </div>
        )}
        {!!plans.length && (
          <div className="mb-8">
            <h2 className="text-xl font-semibold mb-3">Subscription Plans</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {plans.map((p, i) => (
                <div key={i} className="bg-white border rounded p-4">
                  <div className="flex items-center justify-between mb-2">
                    <div className="font-medium">{p.name}</div>
                    <div className="text-pink-600">{p.price}</div>
                  </div>
                  <ul className="text-sm text-gray-600 list-disc ml-5">
                    {p.features.map((f, j) => <li key={j}>{f}</li>)}
                  </ul>
                  <div className="mt-3 flex gap-2">
                    <button className="px-4 py-2 bg-pink-600 text-white rounded" onClick={() => router.post(route('app.premium.subscribe'), { plan_id: p.id })}>Choose</button>
                    <button className="px-4 py-2 border rounded" onClick={() => fetch(route('app.premium.stripe_checkout'), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ type: 'subscription', id: p.id }) }).then(r => r.json()).then(j => window.location.href = j.url)}>Stripe</button>
                    <button className="px-4 py-2 border rounded" onClick={() => fetch(route('app.premium.paypal_order'), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ type: 'subscription', id: p.id }) }).then(r => r.json()).then(j => alert(`PayPal order: ${j.id}`))}>PayPal</button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}
        {!!creditPacks.length && (
          <div>
            <h2 className="text-xl font-semibold mb-3">Credit Packs</h2>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              {creditPacks.map((c, i) => (
                <div key={i} className="bg-white border rounded p-4">
                  <div className="font-medium mb-1">{c.name}</div>
                  <div className="text-pink-600 mb-2">{c.price}</div>
                  <div className="mt-2 flex gap-2">
                    <button className="px-4 py-2 bg-purple-600 text-white rounded" onClick={() => router.post(route('app.premium.credits'), { pack_id: c.id })}>Buy</button>
                    <button className="px-4 py-2 border rounded" onClick={() => fetch(route('app.premium.stripe_checkout'), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ type: 'credits', id: c.id }) }).then(r => r.json()).then(j => window.location.href = j.url)}>Stripe</button>
                    <button className="px-4 py-2 border rounded" onClick={() => fetch(route('app.premium.paypal_order'), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ type: 'credits', id: c.id }) }).then(r => r.json()).then(j => {
                      const link = (j.links || []).find(l => l.rel === 'approve')
                      if (link?.href) window.location.href = link.href
                    })}>PayPal</button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}
      </div>
    </MainAppShell>
  )
}