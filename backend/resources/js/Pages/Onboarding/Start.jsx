import { Link } from '@inertiajs/react'

export default function Start() {
  return (
    <div className="layout-split">
      <div className="pane">
        <div className="auth-card">
          <h1 className="text-4xl font-bold mb-3">Welcome to Binge</h1>
          <p className="text-gray-700 mb-6">Complete onboarding to access the app.</p>
          <Link href={route('onboarding.photos')} className="btn btn-primary">Get Started</Link>
        </div>
      </div>
      <div className="brand-pane pane hidden lg:flex">
        <div className="max-w-lg">
          <div className="text-2xl font-semibold mb-3">What to expect</div>
          <ul className="text-gray-700 space-y-2">
            <li>• Add photos and a short bio</li>
            <li>• Set your discovery preferences</li>
            <li>• Verify your profile for trust</li>
          </ul>
        </div>
      </div>
    </div>
  )
}