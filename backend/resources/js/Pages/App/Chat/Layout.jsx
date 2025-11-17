import MainAppShell from '../../../Layouts/MainAppShell'
import { usePage, router } from '@inertiajs/react'
import { useEffect } from 'react'

export default function ChatLayout({ children }) {
  const { props } = usePage()
  const list = props.list || []
  useEffect(() => {
    if (!window.Echo) return
    const subs = []
    list.forEach(m => {
      subs.push(window.Echo.private(`match.${m.id}`).listen('MessageSent', () => {
        router.reload({ only: ['list'] })
      }))
    })
    return () => {
      // cleanup not strictly necessary for Echo private channels here
    }
  }, [list])
  return (
    <MainAppShell>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div className="md:col-span-1">
          <h2 className="text-xl font-semibold mb-2">Conversations</h2>
          <div className="card overflow-hidden">
            {list.map(m => (
              <a key={m.id} href={`/app/chat/${m.id}`} className="block px-4 py-3 hover:bg-gray-50">
                <div className="flex items-center gap-3">
                  <div className="avatar">
                    {String(m.name || '').slice(0,1).toUpperCase()}
                  </div>
                  <div className="flex-1">
                    <div className="flex items-center justify-between">
                      <div className="font-medium">{m.name}</div>
                      {m.unread > 0 && (
                        <span className="badge" style={{background:'#ec4899',color:'#fff'}}>{m.unread}</span>
                      )}
                    </div>
                    <div className="text-sm text-gray-500 truncate">{m.last}</div>
                  </div>
                </div>
              </a>
            ))}
          </div>
        </div>
        <div className="md:col-span-2">
          {children}
        </div>
      </div>
    </MainAppShell>
  )
}