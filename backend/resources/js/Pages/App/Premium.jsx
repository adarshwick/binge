import MainAppShell from '../../Layouts/MainAppShell'
import { router } from '@inertiajs/react'

export default function Premium({ mode, plans = [], creditPacks = [], subscription }) {
  const params = new URLSearchParams(window.location.search)
  const success = params.get('success') === '1'
  const canceled = params.get('canceled') === '1'
  return (
    <MainAppShell>
      <div className="max-w-5xl mx-auto">
        <h1 className="text-3xl font-bold mb-2">Go Premium</h1>
        <p className="text-gray-600 mb-2">Business Model: {mode}</p>
        <p className="text-gray-600 mb-6">Credits: {window?.Inertia?.page?.props?.auth?.creditBalance ?? 0}</p>
        {success && (
          <div className="card" style={{background:'#ecfdf5',color:'#166534'}}>Payment succeeded. Your purchase will reflect shortly.</div>
        )}
        {canceled && (
          <div className="card" style={{background:'#fffbeb',color:'#a16207'}}>Payment canceled.</div>
        )}
        {subscription && (
          <div className="mb-6 p-4 rounded-2xl bg-green-50 text-green-700 shadow">
            <div className="font-medium">Active Plan: {subscription.plan}</div>
            {subscription.ends_at && <div>Expires: {new Date(subscription.ends_at).toLocaleString()}</div>}
          </div>
        )}
        {!!plans.length && (
          <div className="mb-8">
            <h2 className="text-xl font-semibold mb-3">Subscription Plans</h2>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              {plans.map((p, i) => (
                <div key={i} className="card">
                  <div className="flex items-center justify-between mb-2">
                    <div className="font-semibold text-lg">{p.name}</div>
                    <div className="px-3 py-1 rounded-full bg-pink-100 text-pink-700 text-sm">{p.price}</div>
                  </div>
                  <ul className="text-sm text-gray-700 list-disc ml-5">
                    {p.features.map((f, j) => <li key={j}>{f}</li>)}
                  </ul>
                  <div className="mt-3 flex gap-2">
                    <button className="btn btn-primary" onClick={() => router.post(route('app.premium.subscribe'), { plan_id: p.id })}>Choose</button>
                    <button className="btn btn-ghost" onClick={() => fetch(route('app.premium.stripe_checkout'), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ type: 'subscription', id: p.id }) }).then(r => r.json()).then(j => window.location.href = j.url)}>Stripe</button>
                    <button className="btn btn-ghost" onClick={() => fetch(route('app.premium.paypal_order'), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ type: 'subscription', id: p.id }) }).then(r => r.json()).then(j => alert(`PayPal order: ${j.id}`))}>PayPal</button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}
        {!!creditPacks.length && (
          <div>
            <h2 className="text-xl font-semibold mb-3">Credit Packs</h2>
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
              {creditPacks.map((c, i) => (
                <div key={i} className="card text-center">
                  <div className="font-semibold mb-1">{c.name}</div>
                  <div className="text-pink-600 mb-2">{c.price}</div>
                  <div className="mt-2 flex gap-2 justify-center">
                    <button className="btn btn-outline" onClick={() => router.post(route('app.premium.credits'), { pack_id: c.id })}>Buy</button>
                    <button className="btn btn-ghost" onClick={() => fetch(route('app.premium.stripe_checkout'), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ type: 'credits', id: c.id }) }).then(r => r.json()).then(j => window.location.href = j.url)}>Stripe</button>
                    <button className="btn btn-ghost" onClick={() => fetch(route('app.premium.paypal_order'), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ type: 'credits', id: c.id }) }).then(r => r.json()).then(j => {
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