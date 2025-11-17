import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link, Head, usePage } from '@inertiajs/react';

export default function GuestLayout({ children }) {
    const { props } = usePage()
    const seoTitle = props.settings?.seo_title || 'Binge'
    const seoDesc = props.settings?.seo_description || 'Meet, match, and chat.'
    return (
        <div className="layout-split">
            <Head>
                <title>{seoTitle}</title>
                {seoDesc && (<meta name="description" content={seoDesc} />)}
                <link rel="manifest" href="/manifest.webmanifest" />
            </Head>
            <div className="pane">
                <div className="auth-card">
                    <div className="flex items-center gap-3 mb-6">
                        <Link href="/">
                            <ApplicationLogo className="h-10 w-10 fill-current text-pink-600" />
                        </Link>
                        <span className="text-xl font-semibold">{seoTitle}</span>
                    </div>
                    <div className="card">
                        {children}
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
                    <div className="text-3xl font-bold mb-4">Find your match</div>
                    <p className="text-gray-700 mb-6">{seoDesc}</p>
                    <ul className="text-gray-600 space-y-2">
                        <li>• Smart discovery with distance and preferences</li>
                        <li>• Real-time chat, voice notes, and video</li>
                        <li>• Verified profiles and premium perks</li>
                    </ul>
                </div>
            </div>
            {props.settings?.analytics_snippet && (
                <div dangerouslySetInnerHTML={{ __html: props.settings.analytics_snippet }} />
            )}
        </div>
    );
}
