import { Link, usePage } from '@inertiajs/react'

export default function Landing() {
  const { props } = usePage()
  const seoTitle = props.settings?.seo_title || 'Binge'
  const seoDesc = props.settings?.seo_description || 'Meet, match, and chat.'
  return (
    <div className="layout-split">
      <div className="pane">
        <div className="auth-card">
          <div className="flex items-center justify-between mb-8">
            <div className="flex items-center gap-2">
              <div className="h-8 w-8 bg-pink-500 rounded-full" />
              <span className="font-semibold text-lg">{seoTitle}</span>
            </div>
            <Link href={route('login')} className="text-pink-600 font-medium">Login</Link>
          </div>
          <div className="card">
            <h1 className="text-4xl font-bold mb-3">Find your match</h1>
            <p className="text-gray-700 mb-6">{seoDesc}</p>
            <div className="flex flex-wrap items-center gap-3">
              <a href="#" className="btn btn-ghost">App Store</a>
              <a href="#" className="btn btn-ghost">Google Play</a>
              <Link href={route('login')} className="btn btn-primary">Start Now</Link>
            </div>
          </div>
          <div className="mt-6 text-center text-sm text-gray-500">
            <Link href={route('privacy')}>Privacy</Link>
            <span className="mx-2">•</span>
            <Link href={route('terms')}>Terms</Link>
          </div>
        </div>
      </div>
      <div className="brand-pane pane hidden lg:flex">
        <div className="max-w-lg">
          <div className="text-2xl font-semibold mb-3">Why {seoTitle}?</div>
          <ul className="text-gray-700 space-y-2">
            <li>• Smart discovery with distance, age, and preferences</li>
            <li>• Boosts and Super Likes to stand out</li>
            <li>• Real-time chat with voice and video</li>
            <li>• Verified profiles and premium features</li>
          </ul>
        </div>
      </div>
    </div>
  )
}