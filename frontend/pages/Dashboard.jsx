import React, { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import api from '../services/api'
import AnimatedNumber from '../components/AnimatedNumber'
import './Dashboard.css'

function Dashboard() {
  const [sponsors, setSponsors] = useState([])
  const [loading, setLoading] = useState(true)
  const [stats, setStats] = useState({
    total: 0,
    new: 0,
    interested: 0,
    in_progress: 0,
    closed: 0
  })

  useEffect(() => {
    fetchSponsors()
  }, [])

  const fetchSponsors = async () => {
    try {
      const response = await api.get('/sponsors')
      const data = response.data
      setSponsors(data)
      
      const statsData = {
        total: data.length,
        new: data.filter(s => s.status === 'new').length,
        interested: data.filter(s => s.status === 'interested').length,
        in_progress: data.filter(s => s.status === 'in_progress').length,
        closed: data.filter(s => s.status === 'closed').length
      }
      setStats(statsData)
    } catch (error) {
      console.error('Error fetching sponsors:', error)
    } finally {
      setLoading(false)
    }
  }

  if (loading) {
    return <div className="loading"><div className="spinner"></div></div>
  }

  return (
    <div className="dashboard">
      <div className="dashboard-header">
        <h1>Dashboard</h1>
        <Link to="/sponsors/add" className="btn btn-primary">
          <i className="fas fa-plus"></i> Add Sponsor
        </Link>
      </div>

      <div className="stats-grid">
        <div className="stat-card">
          <div className="stat-icon" style={{ background: 'rgba(62, 207, 142, 0.1)', color: 'var(--sb-primary)' }}>
            <i className="fas fa-building"></i>
          </div>
          <div className="stat-content">
            <div className="stat-value">
              <AnimatedNumber value={stats.total} duration={1200} delay={200} />
            </div>
            <div className="stat-label">Total Sponsors</div>
          </div>
        </div>

        <div className="stat-card">
          <div className="stat-icon" style={{ background: 'rgba(100, 116, 139, 0.1)', color: 'var(--sb-text-muted)' }}>
            <i className="fas fa-circle"></i>
          </div>
          <div className="stat-content">
            <div className="stat-value">
              <AnimatedNumber value={stats.new} duration={1200} delay={400} />
            </div>
            <div className="stat-label">New</div>
          </div>
        </div>

        <div className="stat-card">
          <div className="stat-icon" style={{ background: 'rgba(79, 70, 229, 0.1)', color: 'var(--sb-accent)' }}>
            <i className="fas fa-eye"></i>
          </div>
          <div className="stat-content">
            <div className="stat-value">
              <AnimatedNumber value={stats.interested} duration={1200} delay={600} />
            </div>
            <div className="stat-label">Interested</div>
          </div>
        </div>

        <div className="stat-card">
          <div className="stat-icon" style={{ background: 'rgba(245, 158, 11, 0.1)', color: 'var(--sb-warning)' }}>
            <i className="fas fa-spinner"></i>
          </div>
          <div className="stat-content">
            <div className="stat-value">
              <AnimatedNumber value={stats.in_progress} duration={1200} delay={800} />
            </div>
            <div className="stat-label">In Progress</div>
          </div>
        </div>

        <div className="stat-card">
          <div className="stat-icon" style={{ background: 'rgba(16, 185, 129, 0.1)', color: 'var(--sb-success)' }}>
            <i className="fas fa-check-circle"></i>
          </div>
          <div className="stat-content">
            <div className="stat-value">
              <AnimatedNumber value={stats.closed} duration={1200} delay={1000} />
            </div>
            <div className="stat-label">Closed</div>
          </div>
        </div>
      </div>

      <div className="card">
        <div className="card-header">
          <h2>Recent Sponsors</h2>
          <Link to="/sponsors" className="btn-link">View All</Link>
        </div>
        {sponsors.length === 0 ? (
          <div className="empty-state">
            <i className="fas fa-inbox"></i>
            <p>No sponsors yet</p>
            <Link to="/sponsors/add" className="btn btn-primary">Add First Sponsor</Link>
          </div>
        ) : (
          <div className="table-container">
            <table>
              <thead>
                <tr>
                  <th>Company</th>
                  <th>Contact</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {sponsors.slice(0, 10).map((sponsor) => (
                  <tr key={sponsor.id}>
                    <td>
                      <strong>{sponsor.company_name}</strong>
                      {sponsor.industry && (
                        <div className="text-muted">{sponsor.industry}</div>
                      )}
                    </td>
                    <td>
                      {sponsor.contact_person && (
                        <div>{sponsor.contact_person}</div>
                      )}
                      {sponsor.email && (
                        <div className="text-muted">{sponsor.email}</div>
                      )}
                    </td>
                    <td>
                      <span className={`badge badge-${sponsor.status.replace('_', '-')}`}>
                        {sponsor.status.replace('_', ' ')}
                      </span>
                    </td>
                    <td>
                      <Link to={`/sponsors/${sponsor.id}`} className="btn-icon">
                        <i className="fas fa-eye"></i>
                      </Link>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  )
}

export default Dashboard

