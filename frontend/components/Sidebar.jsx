import React from 'react'
import { Link, useLocation } from 'react-router-dom'
import { useAuth } from '../contexts/AuthContext'
import './Sidebar.css'

function Sidebar() {
  const location = useLocation()
  const { user } = useAuth()

  const menuItems = [
    { path: '/', icon: 'fa-home', label: 'Dashboard' },
    { path: '/sponsors', icon: 'fa-users', label: 'Sponsors' },
    { path: '/interactions', icon: 'fa-comments', label: 'Interactions' },
    { path: '/import', icon: 'fa-file-excel', label: 'Import Excel' },
  ]

  return (
    <aside className="sidebar">
      <div className="sidebar-header">
        <div className="sidebar-logo">
          <i className="fas fa-building"></i>
          <span>Sponsor CRM</span>
        </div>
      </div>
      
      <nav className="sidebar-nav">
        {menuItems.map((item) => {
          const isActive = location.pathname === item.path || 
            (item.path !== '/' && location.pathname.startsWith(item.path))
          return (
            <Link
              key={item.path}
              to={item.path}
              className={`sidebar-item ${isActive ? 'active' : ''}`}
            >
              <i className={`fas ${item.icon}`}></i>
              <span>{item.label}</span>
            </Link>
          )
        })}
        
        <Link to="/sponsors/add" className="sidebar-item sidebar-item-primary">
          <i className="fas fa-plus-circle"></i>
          <span>Add Sponsor</span>
        </Link>
      </nav>

      {user && (
        <div className="sidebar-footer">
          <div className="sidebar-user">
            <div className="sidebar-user-avatar">
              {user.username.charAt(0).toUpperCase()}
            </div>
            <div className="sidebar-user-info">
              <div className="sidebar-user-name">{user.username}</div>
              <div className="sidebar-user-email text-muted">{user.email || ''}</div>
            </div>
          </div>
        </div>
      )}
    </aside>
  )
}

export default Sidebar

