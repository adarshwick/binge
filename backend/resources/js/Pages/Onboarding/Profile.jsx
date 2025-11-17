import { useState } from 'react'
import { Link, usePage } from '@inertiajs/react'

export default function Profile() {
  const { props } = usePage()
  const prompts = props.prompts || []
  const [bio, setBio] = useState('')
  const [selected, setSelected] = useState([])
  const [answers, setAnswers] = useState({})
  function togglePrompt(p) {
    setSelected(prev => prev.find(x => x.id === p.id) ? prev.filter(x => x.id !== p.id) : prev.length < 3 ? [...prev, p] : prev)
  }
  function setAnswer(p, v) {
    setAnswers(prev => ({ ...prev, [p.id]: v }))
  }
  return (
    <div className="min-h-screen bg-white px-6 py-6">
      <div className="max-w-xl mx-auto">
        <h1 className="text-2xl font-bold mb-3">Build Your Profile</h1>
        <textarea value={bio} onChange={e => setBio(e.target.value)} className="w-full border rounded p-3 mb-4" rows={4} placeholder="Write a short bio" />
        <p className="text-sm text-gray-600 mb-2">Select 3 profile prompts:</p>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
          {prompts.map(p => (
            <button key={p.id} onClick={() => togglePrompt(p)} className={`text-left px-3 py-2 border rounded ${selected.find(x => x.id === p.id) ? 'bg-pink-50 border-pink-500' : ''}`}>{p.text}</button>
          ))}
        </div>
        {selected.map(p => (
          <div key={p.id} className="mb-2">
            <label className="block text-xs text-gray-600 mb-1">Answer: {p.text}</label>
            <input className="w-full border rounded px-3 py-2" onChange={e => setAnswer(p, e.target.value)} />
          </div>
        ))}
        <div className="flex justify-between">
          <Link href={route('onboarding.photos')} className="px-4 py-2 border rounded">Back</Link>
          <Link href={route('onboarding.filters')} data={{ answers: JSON.stringify(answers), bio }} className="px-4 py-2 bg-pink-600 text-white rounded">Next</Link>
        </div>
      </div>
    </div>
  )
}