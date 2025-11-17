import { Link } from '@inertiajs/react'

export default function Start() {
  return (
    <div className="min-h-screen bg-white grid grid-cols-1 lg:grid-cols-2">
      <div className="flex items-center justify-center px-8 py-10">
        <div className="max-w-md w-full">
          <h1 className="text-4xl font-bold mb-3">Welcome to Binge</h1>
          <p className="text-gray-700 mb-6">Complete onboarding to access the app.</p>
          <Link href={route('onboarding.photos')} className="px-5 py-3 bg-pink-600 text-white rounded-full">Get Started</Link>
        </div>
      </div>
      <div className="hidden lg:flex items-center justify-center bg-gradient-to-br from-pink-50 to-purple-100">
        <div className="max-w-lg px-10 py-10">
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