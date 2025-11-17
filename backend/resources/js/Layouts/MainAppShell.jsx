import { Link, usePage, Head } from '@inertiajs/react'
import { useEffect } from 'react'

export default function MainAppShell({ children }) {
  const { url } = usePage()
  const nav = [
    { href: route('app.discover'), label: 'Discover' },
    { href: '/app/matches', label: 'Matches' },
    { href: '/app/chat', label: 'Chat' },
    { href: '/app/profile', label: 'Profile' },
    ...(usePage().props.auth?.features?.hasSeeWhoLikedMe ? [{ href: route('app.liked_me'), label: 'Liked Me' }] : []),
  ]
  return (
    <div className="min-h-screen bg-gray-50 flex">
      <Head>
        <title>{usePage().props.settings?.seo_title || 'Binge'}</title>
        {usePage().props.settings?.seo_description && (
          <meta name="description" content={usePage().props.settings.seo_description} />
        )}
        <link rel="manifest" href="/manifest.webmanifest" />
      </Head>
      <aside className="hidden md:flex md:w-64 border-r bg-white">
        <nav className="w-full p-4 space-y-2">
          {nav.map(n => (
            <Link key={n.href} href={n.href} className="block px-3 py-2 rounded hover:bg-gray-100">
              {n.label}
            </Link>
          ))}
        </nav>
      </aside>
      <main className="flex-1 grid grid-cols-1 lg:grid-cols-3">
        {useEffect(() => {
          const cap = window?.Capacitor
          const AdMob = cap?.Plugins?.AdMob
          const settings = usePage().props.settings
          if (!AdMob || !settings) return
          const shouldShow = settings.monetization_mode && (settings.monetization_mode === 'free' || settings.monetization_mode === 'hybrid')
          if (!shouldShow || !settings.admob_banner_id) return
          AdMob.showBanner({ adId: settings.admob_banner_id })
        }, [])}
        {useEffect(() => {
          if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js').catch(() => {})
          }
        }, [])}
        <div className="lg:col-span-2 flex flex-col">
          <div className="flex-1 p-4">
            {children}
          </div>
          {usePage().props.settings?.analytics_snippet && (
            <div dangerouslySetInnerHTML={{ __html: usePage().props.settings.analytics_snippet }} />
          )}
          {usePage().props.settings?.monetization_mode && (usePage().props.settings?.monetization_mode === 'free' || usePage().props.settings?.monetization_mode === 'hybrid') && !usePage().props.auth?.features?.hasAdFree && (
            <div className="px-4 py-2">
              <div className="bg-white border rounded p-2">
                {usePage().props.settings?.ads_web_snippet ? (
                  <div dangerouslySetInnerHTML={{ __html: usePage().props.settings.ads_web_snippet }} />
                ) : (
                  <div className="text-sm text-gray-500">Ad slot</div>
                )}
              </div>
            </div>
          )}
          <div className="px-4 pb-2 text-sm text-gray-600">Credits: {usePage().props.auth?.creditBalance ?? 0}</div>
          <nav className="md:hidden border-t bg-white flex justify-around py-2">
            {nav.map(n => (
              <Link key={n.href} href={n.href} className="text-sm">
                {n.label}
              </Link>
            ))}
          </nav>
        </div>
        <aside className="hidden lg:flex lg:col-span-1 items-center justify-center bg-gradient-to-br from-pink-50 to-purple-100">
          <div className="max-w-sm px-6 py-6">
            <div className="text-2xl font-semibold mb-3">Welcome to {usePage().props.settings?.seo_title || 'Binge'}</div>
            <p className="text-gray-700 mb-4">{usePage().props.settings?.seo_description || 'Meet, match, and chat.'}</p>
            <ul className="text-gray-700 space-y-2">
              <li>• Swipe discovery and premium boosts</li>
              <li>• Real-time chat, voice notes, and video</li>
              <li>• Verified profiles and safety features</li>
            </ul>
          </div>
        </aside>
      </main>
    </div>
  )
}