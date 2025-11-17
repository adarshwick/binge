import { Link } from '@inertiajs/react'

export default function Landing() {
  return (
    <div className="min-h-screen bg-white flex flex-col">
      <header className="px-6 py-4 border-b flex items-center justify-between">
        <div className="flex items-center gap-2">
          <div className="h-8 w-8 bg-pink-500 rounded-full" />
          <span className="font-semibold text-lg">Binge</span>
        </div>
        <Link href={route('login')} className="text-pink-600 font-medium">Login / Launch Web App</Link>
      </header>

      <main className="flex-1 flex flex-col items-center justify-center text-center px-6">
        <h1 className="text-3xl md:text-5xl font-bold mb-4">Chameleon — One Codebase, Three Platforms</h1>
        <p className="text-gray-600 max-w-2xl mb-8">
          Launch a cross-platform dating app with a single responsive frontend and a powerful Laravel backend. Edit once, deploy to Web, iOS, and Android.
        </p>
        <div className="flex flex-wrap items-center justify-center gap-4">
          <a href="#" className="px-5 py-3 bg-black text-white rounded">Download on App Store</a>
          <a href="#" className="px-5 py-3 bg-green-600 text-white rounded">Get it on Google Play</a>
          <Link href={route('login')} className="px-5 py-3 bg-pink-600 text-white rounded">Login / Launch Web App</Link>
        </div>
      </main>

      <footer className="px-6 py-6 border-t text-sm text-gray-500 flex items-center justify-center gap-4">
        <Link href={route('privacy')}>Privacy</Link>
        <span>•</span>
        <Link href={route('terms')}>Terms</Link>
      </footer>
    </div>
  )
}