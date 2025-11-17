import ChatLayout from './Layout'
import { usePage } from '@inertiajs/react'

export default function Index() {
  const { props } = usePage()
  const list = props.list || []
  return (
    <ChatLayout>
      <div className="hidden md:block">
        <div className="bg-white border rounded">
          {list.length === 0 ? (
            <div className="h-96 flex items-center justify-center text-gray-500">Select a conversation to start chatting</div>
          ) : (
            <div className="p-4 text-gray-700">Choose a chat from the left</div>
          )}
        </div>
      </div>
      <div className="md:hidden">
        <div className="text-gray-500">Select a conversation from the list</div>
      </div>
    </ChatLayout>
  )
}