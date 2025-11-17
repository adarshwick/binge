import MainAppShell from '../../Layouts/MainAppShell'
import { usePage } from '@inertiajs/react'

export default function Billing() {
  const { props } = usePage()
  const payments = props.payments || []
  const subscriptions = props.subscriptions || []
  return (
    <MainAppShell>
      <div className="max-w-3xl mx-auto">
        <h1 className="text-2xl font-bold mb-4">Billing</h1>
        <div className="mb-6">
          <h2 className="text-xl font-semibold mb-2">Subscriptions</h2>
          <div className="space-y-2">
            {subscriptions.map((s, i) => (
              <div key={i} className="bg-white border rounded p-3 flex justify-between">
                <div>
                  <div className="font-medium">{s.plan}</div>
                  <div className="text-sm text-gray-500">{s.status}</div>
                </div>
                <div className="text-sm text-gray-600">
                  <div>Start: {s.starts_at ? new Date(s.starts_at).toLocaleString() : '-'}</div>
                  <div>End: {s.ends_at ? new Date(s.ends_at).toLocaleString() : '-'}</div>
                </div>
              </div>
            ))}
          </div>
        </div>
        <div>
          <h2 className="text-xl font-semibold mb-2">Payments</h2>
          <div className="space-y-2">
            {payments.map(p => (
              <div key={p.id} className="bg-white border rounded p-3 flex justify-between">
                <div>
                  <div className="font-medium">{p.type} • {p.gateway}</div>
                  <div className="text-sm text-gray-500">{p.status}</div>
                </div>
                <div className="text-sm text-gray-600">
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