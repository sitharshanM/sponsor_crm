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
  const url = config.url || ''
  
  // Convert /api/auth/login to /api/auth.php?action=login
  if (url.includes('/auth/')) {
    const parts = url.split('/auth/')
    if (parts.length === 2) {
      config.url = `/auth.php?action=${parts[1]}`
    }
  } else if (url === '/auth' || url.endsWith('/auth')) {
    config.url = '/auth.php?action=check'
  }
  
  // Convert /api/sponsors/:id to /api/sponsors.php?id=:id
  if (url.includes('/sponsors/')) {
    const match = url.match(/\/sponsors\/(\d+)/)
    if (match) {
      config.url = `/sponsors.php?id=${match[1]}`
    }
  } else if (url === '/sponsors' || url.endsWith('/sponsors')) {
    config.url = '/sponsors.php'
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

