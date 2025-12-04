import React from 'react'
import { useAuth } from '../contexts/AuthContext'
import './TopBar.css'

function TopBar() {
  const { user, logout } = useAuth()

  return (
    <header className="topbar">
      <div className="topbar-content">
        <div className="topbar-left">
          <h1 className="topbar-title">Dashboard</h1>
        </div>
        <div className="topbar-right">
          {user && (
            <div className="topbar-user">
              <div className="topbar-user-info">
                <span className="topbar-user-name">{user.username}</span>
              </div>
              <button onClick={logout} className="topbar-logout">
                <i className="fas fa-sign-out-alt"></i>
                Logout
              </button>
            </div>
          )}
        </div>
      </div>
    </header>
  )
}

export default TopBar

