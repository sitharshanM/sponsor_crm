import React, { useEffect } from 'react'
import { BrowserRouter as Router, Routes, Route, Navigate, useLocation } from 'react-router-dom'
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

// Page transition wrapper
function PageTransition({ children }) {
  return <div className="page-enter">{children}</div>
}

function PrivateRoute({ children }) {
  const { isAuthenticated, loading } = useAuth()
  
  if (loading) {
    return <div className="loading-container"><div className="spinner"></div></div>
  }
  
  return isAuthenticated ? children : <Navigate to="/login" />
}

function AppRoutes() {
  const location = useLocation()
  
  return (
    <Routes location={location}>
      <Route path="/login" element={<PageTransition><Login /></PageTransition>} />
      <Route path="/" element={<PrivateRoute><Layout /></PrivateRoute>}>
        <Route index element={<PageTransition><Dashboard /></PageTransition>} />
        <Route path="sponsors" element={<PageTransition><Sponsors /></PageTransition>} />
        <Route path="sponsors/:id" element={<PageTransition><SponsorView /></PageTransition>} />
        <Route path="sponsors/:id/edit" element={<PageTransition><SponsorEdit /></PageTransition>} />
        <Route path="sponsors/add" element={<PageTransition><SponsorAdd /></PageTransition>} />
        <Route path="interactions" element={<PageTransition><Interactions /></PageTransition>} />
        <Route path="import" element={<PageTransition><Import /></PageTransition>} />
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

