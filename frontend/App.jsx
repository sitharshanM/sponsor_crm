import React from 'react'
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom'
import Layout from './components/Layout'
import Login from './pages/Login'
import Dashboard from './pages/Dashboard'
import Sponsors from './pages/Sponsors'
import SponsorView from './pages/SponsorView'
import SponsorAdd from './pages/SponsorAdd'
import SponsorEdit from './pages/SponsorEdit'
import Interactions from './pages/Interactions'
import Import from './pages/Import'
import { AuthProvider, useAuth } from './contexts/AuthContext'
import './App.css'

function PrivateRoute({ children }) {
  const { isAuthenticated, loading } = useAuth()
  
  if (loading) {
    return <div className="loading-container"><div className="spinner"></div></div>
  }
  
  return isAuthenticated ? children : <Navigate to="/login" />
}

function AppRoutes() {
  return (
    <Routes>
      <Route path="/login" element={<Login />} />
      <Route path="/" element={<PrivateRoute><Layout /></PrivateRoute>}>
        <Route index element={<Dashboard />} />
        <Route path="sponsors" element={<Sponsors />} />
        <Route path="sponsors/:id" element={<SponsorView />} />
        <Route path="sponsors/:id/edit" element={<SponsorEdit />} />
        <Route path="sponsors/add" element={<SponsorAdd />} />
        <Route path="interactions" element={<Interactions />} />
        <Route path="import" element={<Import />} />
      </Route>
    </Routes>
  )
}

function App() {
  return (
    <AuthProvider>
      <Router>
        <AppRoutes />
      </Router>
    </AuthProvider>
  )
}

export default App

