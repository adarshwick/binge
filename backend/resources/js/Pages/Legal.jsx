import DOMPurify from 'dompurify'

export default function Legal({ type, content }) {
  const title = type === 'terms' ? 'Terms of Service' : 'Privacy Policy'
  const safe = content ? DOMPurify.sanitize(content, { USE_PROFILES: { html: true } }) : null
  return (
    <div className="min-h-screen bg-white">
      <div className="max-w-3xl mx-auto px-6 py-10">
        <h1 className="text-3xl font-bold mb-6">{title}</h1>
        {safe ? (
          <div className="prose max-w-none" dangerouslySetInnerHTML={{ __html: safe }} />
        ) : (
          <div className="prose max-w-none">
            <p>
              This content is managed from the Admin Panel. Placeholder text is shown until the administrator publishes official {title.toLowerCase()} content.
            </p>
          </div>
        )}
      </div>
    </div>
  )
}