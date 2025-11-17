import { useState } from 'react'
import { Link, router, usePage } from '@inertiajs/react'

export default function Photos() {
  const [files, setFiles] = useState([])
  const [selfie, setSelfie] = useState(null)
  const { props } = usePage()
  const settings = props?.settings || {}
  const verificationMode = settings?.verification_mode || 'optional'
  function onPick(e) {
    setFiles(Array.from(e.target.files || []))
  }
  async function takePhoto() {
    try {
      const cap = window?.Capacitor
      const Camera = cap?.Plugins?.Camera
      const CameraResultType = cap?.Plugins?.CameraResultType
      if (!Camera || !CameraResultType) return
      const result = await Camera.getPhoto({ quality: 70, allowEditing: false, resultType: CameraResultType.Uri })
      if (result?.webPath) {
        const res = await fetch(result.webPath)
        const blob = await res.blob()
        const file = new File([blob], `photo_${Date.now()}.jpg`, { type: blob.type })
        setFiles(prev => [...prev, file])
      }
    } catch {}
  }
  return (
    <div className="min-h-screen bg-white px-6 py-6">
      <div className="max-w-xl mx-auto">
        <h1 className="text-2xl font-bold mb-2">Upload 2–9 Photos</h1>
        <p className="text-gray-600 mb-4">Use camera or choose from gallery on mobile.</p>
        <div className="border rounded p-4 mb-4">
          <div className="flex items-center gap-3 mb-3">
            <button className="px-3 py-2 border rounded" onClick={takePhoto}>Take Photo</button>
            <input type="file" multiple accept="image/*" onChange={onPick} />
          </div>
          <div className="grid grid-cols-3 gap-2 mt-4">
            {files.map((f, i) => (
              <div key={i} className="h-24 bg-gray-100 flex items-center justify-center text-xs text-gray-500">
                {f.name}
              </div>
            ))}
          </div>
          <div className="mt-3">
            <button className="px-3 py-2 bg-pink-600 text-white rounded" onClick={async () => {
              for (const f of files) {
                const form = new FormData()
                form.append('photo', f)
                await fetch(route('app.profile.photos.upload'), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: form })
              }
              setFiles([])
              alert('Photos uploaded')
            }}>Upload Selected</button>
          </div>
        </div>
        {verificationMode !== 'off' && (
          <div className="border rounded p-4 mb-4">
            <h2 className="font-medium mb-2">Selfie for Verification {verificationMode === 'optional' ? '(optional)' : '(required)'}</h2>
            <input type="file" accept="image/*" onChange={e => setSelfie(e.target.files?.[0] || null)} />
            <button className="mt-2 px-4 py-2 border rounded" onClick={() => {
              if (!selfie) return
              const form = new FormData()
              form.append('photo', selfie)
              router.post(route('onboarding.selfie'), form)
            }}>Submit Selfie</button>
          </div>
        )}
        <div className="flex justify-between">
          <Link href={route('onboarding.start')} className="px-4 py-2 border rounded">Back</Link>
          <Link href={route('onboarding.profile')} className="px-4 py-2 bg-pink-600 text-white rounded">Next</Link>
        </div>
      </div>
    </div>
  )
}