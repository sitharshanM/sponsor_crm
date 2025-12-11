import axios from 'axios'

// Use direct API endpoints that work with PHP built-in server
const getBaseURL = () => {
  if (window.location.origin.includes('localhost:3000')) {
    return 'http://localhost:8000/api'
  }
  return '/api'
}

const api = axios.create({
  baseURL: getBaseURL(),
  headers: {
    'Content-Type': 'application/json',
  },
  withCredentials: true
})

// Override request to use direct endpoints that work with PHP built-in server
api.interceptors.request.use((config) => {
  let url = config.url || ''
  
  // Remove leading slash if present for easier matching
  if (url.startsWith('/')) {
    url = url.substring(1)
  }
  
  // Convert auth/login to auth.php?action=login
  if (url.startsWith('auth/')) {
    const action = url.replace('auth/', '')
    config.url = `auth.php?action=${action}`
  } else if (url === 'auth' || url === 'auth/check') {
    config.url = 'auth.php?action=check'
  }
  
  // Convert sponsors/:id to sponsors.php?id=:id
  if (url.startsWith('sponsors/')) {
    const match = url.match(/sponsors\/(\d+)/)
    if (match) {
      config.url = `sponsors.php?id=${match[1]}`
    }
  } else if (url === 'sponsors') {
    config.url = 'sponsors.php'
  }
  
  return config
})

// Response interceptor
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default api

