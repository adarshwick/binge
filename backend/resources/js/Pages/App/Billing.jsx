import MainAppShell from '../../Layouts/MainAppShell'
import { usePage } from '@inertiajs/react'

export default function Billing() {
  const { props } = usePage()
  const payments = props.payments || []
  const subscriptions = props.subscriptions || []
  return (
    <MainAppShell>
      <div className="max-w-4xl mx-auto">
        <h1 className="text-2xl font-bold mb-4">Billing</h1>
        <div className="mb-6">
          <h2 className="text-xl font-semibold mb-2">Subscriptions</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {subscriptions.map((s, i) => (
              <div key={i} className="bg-white rounded-2xl shadow p-4">
                <div className="flex items-center justify-between mb-2">
                  <div className="font-semibold">{s.plan}</div>
                  <span className={`px-3 py-1 rounded-full text-xs ${s.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'}`}>{s.status}</span>
                </div>
                <div className="text-sm text-gray-700">
                  <div>Start: {s.starts_at ? new Date(s.starts_at).toLocaleString() : '-'}</div>
                  <div>End: {s.ends_at ? new Date(s.ends_at).toLocaleString() : '-'}</div>
                </div>
              </div>
            ))}
          </div>
        </div>
        <div>
          <h2 className="text-xl font-semibold mb-2">Payments</h2>
          <div className="space-y-3">
            {payments.map(p => (
              <div key={p.id} className="bg-white rounded-2xl shadow p-4 flex justify-between">
                <div>
                  <div className="font-semibold">{p.type} • {p.gateway}</div>
                  <div className="text-sm text-gray-600">{p.status}</div>
                </div>
                <div className="text-sm text-gray-700 text-right">
                  <div>{p.currency} {Number(p.amount).toFixed(2)}</div>
                  <div>{new Date(p.created_at).toLocaleString()}</div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </MainAppShell>
  )
}
