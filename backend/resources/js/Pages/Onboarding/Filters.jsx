import { useState, useEffect } from 'react'
import { Link, router, usePage } from '@inertiajs/react'

export default function Filters() {
  const [age, setAge] = useState([18, 35])
  const [distance, setDistance] = useState(25)
  const [gender, setGender] = useState('any')
  const [latlng, setLatlng] = useState({ lat: null, lng: null })
  const { props } = usePage()
  const answers = props.answers || {}
  const bio = props.bio || ''
  useEffect(() => {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(pos => setLatlng({ lat: pos.coords.latitude, lng: pos.coords.longitude }))
    }
  }, [])
  return (
    <div className="layout-split">
      <div className="pane">
        <h1 className="text-2xl font-bold mb-3">Discovery Filters</h1>
        <label className="block mb-4">Age Range: {age[0]}–{age[1]}
          <input className="w-full" type="range" min="18" max="60" value={age[0]} onChange={e => setAge([+e.target.value, age[1]])} />
          <input className="w-full" type="range" min="18" max="60" value={age[1]} onChange={e => setAge([age[0], +e.target.value])} />
        </label>
        <label className="block mb-4">Distance: {distance} km
          <input className="w-full" type="range" min="1" max="100" value={distance} onChange={e => setDistance(+e.target.value)} />
        </label>
        <label className="block mb-6">Gender
          <select value={gender} onChange={e => setGender(e.target.value)} className="select">
            <option value="any">Any</option>
            <option value="female">Female</option>
            <option value="male">Male</option>
            <option value="nonbinary">Non-binary</option>
          </select>
        </label>
        <div className="flex justify-between">
          <Link href={route('onboarding.profile')} className="btn btn-ghost">Back</Link>
          <button
            className="btn"
            onClick={() => router.post(route('onboarding.complete'), {
              pref_min_age: age[0], pref_max_age: age[1], pref_distance_km: distance, pref_gender: gender, lat: latlng.lat, lng: latlng.lng, answers, bio
            })}
          >Finish</button>
        </div>
      </div>
      <div className="brand-pane pane hidden lg:flex">
        <div className="max-w-lg">
          <div className="text-2xl font-semibold mb-3">Tune your preferences</div>
          <ul className="text-gray-700 space-y-2">
            <li>• Choose an age range</li>
            <li>• Limit distance to nearby matches</li>
            <li>• Pick your preferred gender</li>
          </ul>
        </div>
      </div>
    </div>
  )
}