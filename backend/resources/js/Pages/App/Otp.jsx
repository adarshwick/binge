import MainAppShell from '../../Layouts/MainAppShell'
import { router } from '@inertiajs/react'
import { useState } from 'react'

export default function Otp() {
  const [phone, setPhone] = useState('')
  const [code, setCode] = useState('')
  return (
    <MainAppShell>
      <div className="max-w-md mx-auto">
        <h1 className="text-2xl font-bold mb-4">Phone Verification</h1>
        <div className="bg-white rounded-2xl shadow p-4 mb-4">
          <label className="block text-sm mb-1">Phone</label>
          <input className="w-full border rounded-2xl px-3 py-2 mb-3" value={phone} onChange={e => setPhone(e.target.value)} placeholder="Enter phone number" />
          <button className="px-4 py-2 border rounded-full" onClick={() => router.post(route('app.otp.send'), { phone })}>Send Code</button>
        </div>
        <div className="bg-white rounded-2xl shadow p-4">
          <label className="block text-sm mb-1">Code</label>
          <input className="w-full border rounded-2xl px-3 py-2 mb-3" value={code} onChange={e => setCode(e.target.value)} placeholder="Enter code" />
          <button className="px-4 py-2 bg-green-600 text-white rounded-full" onClick={() => router.post(route('app.otp.verify'), { code })}>Verify</button>
        </div>
      </div>
    </MainAppShell>
  )
}
