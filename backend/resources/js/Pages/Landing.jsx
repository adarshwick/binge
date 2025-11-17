import { Link, usePage } from '@inertiajs/react'

export default function Landing() {
  const { props } = usePage()
  const seoTitle = props.settings?.seo_title || 'Binge'
  const seoDesc = props.settings?.seo_description || 'Meet, match, and chat.'
  return (
    <div className="min-h-screen bg-white">
      <header className="px-6 py-4 border-b flex items-center justify-between">
        <div className="flex items-center gap-2">
          <div className="h-8 w-8 bg-pink-500 rounded-full" />
          <span className="font-semibold text-lg">{seoTitle}</span>
        </div>
        <Link href={route('login')} className="text-pink-600 font-medium">Login / Launch Web App</Link>
      </header>
      <div className="grid grid-cols-1 lg:grid-cols-2 min-h-[calc(100vh-120px)]">
        <main className="flex flex-col justify-center px-8 py-10">
          <h1 className="text-4xl md:text-5xl font-bold mb-4">Find your match</h1>
          <p className="text-gray-600 max-w-xl mb-8">{seoDesc}</p>
          <div className="flex flex-wrap items-center gap-4">
            <a href="#" className="px-5 py-3 bg-black text-white rounded">Download on App Store</a>
            <a href="#" className="px-5 py-3 bg-green-600 text-white rounded">Get it on Google Play</a>
            <Link href={route('login')} className="px-5 py-3 bg-pink-600 text-white rounded">Start Now</Link>
          </div>
        </main>
        <aside className="hidden lg:flex items-center justify-center bg-gradient-to-br from-pink-50 to-purple-100">
          <div className="max-w-lg px-10 py-10">
            <div className="text-2xl font-semibold mb-3">Why {seoTitle}?</div>
            <ul className="text-gray-700 space-y-2">
              <li>• Smart discovery with distance, age, and preferences</li>
              <li>• Boosts and Super Likes to stand out</li>
              <li>• Real-time chat with voice and video</li>
              <li>• Verified profiles and premium features</li>
            </ul>
          </div>
        </aside>
      </div>
      <footer className="px-6 py-6 border-t text-sm text-gray-500 flex items-center justify-center gap-4">
        <Link href={route('privacy')}>Privacy</Link>
        <span>•</span>
        <Link href={route('terms')}>Terms</Link>
      </footer>
    </div>
  )
}