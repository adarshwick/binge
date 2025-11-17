import { Link } from '@inertiajs/react'

export default function Start() {
  return (
    <div className="min-h-screen bg-white flex items-center justify-center px-6">
      <div className="max-w-md w-full text-center">
        <h1 className="text-3xl font-bold mb-2">Welcome to Binge</h1>
        <p className="text-gray-600 mb-6">Complete onboarding to access the app.</p>
        <Link href={route('onboarding.photos')} className="px-5 py-3 bg-pink-600 text-white rounded">Get Started</Link>
      </div>
    </div>
  )
}