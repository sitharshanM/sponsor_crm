import React, { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import api from '../services/api'
import './Sponsors.css'

function Sponsors() {
  const [sponsors, setSponsors] = useState([])
  const [loading, setLoading] = useState(true)
  const [search, setSearch] = useState('')

  useEffect(() => {
    fetchSponsors()
  }, [])

  const fetchSponsors = async () => {
    try {
      const response = await api.get('/sponsors')
      setSponsors(response.data)
    } catch (error) {
      console.error('Error:', error)
    } finally {
      setLoading(false)
    }
  }

  const filteredSponsors = sponsors.filter(sponsor =>
    sponsor.company_name.toLowerCase().includes(search.toLowerCase()) ||
    (sponsor.contact_person && sponsor.contact_person.toLowerCase().includes(search.toLowerCase())) ||
    (sponsor.email && sponsor.email.toLowerCase().includes(search.toLowerCase()))
  )

  if (loading) {
    return <div className="loading"><div className="spinner"></div></div>
  }

  return (
    <div className="sponsors-page">
      <div className="page-header">
        <h1>Sponsors</h1>
        <Link to="/sponsors/add" className="btn btn-primary">
          <i className="fas fa-plus"></i> Add Sponsor
        </Link>
      </div>

      <div className="card">
        <div className="search-bar">
          <i className="fas fa-search"></i>
          <input
            type="text"
            placeholder="Search sponsors..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
          />
        </div>

        {filteredSponsors.length === 0 ? (
          <div className="empty-state">
            <i className="fas fa-inbox"></i>
            <p>{search ? 'No sponsors found' : 'No sponsors yet'}</p>
            {!search && (
              <Link to="/sponsors/add" className="btn btn-primary">Add First Sponsor</Link>
            )}
          </div>
        ) : (
          <div className="table-container">
            <table>
              <thead>
                <tr>
                  <th>Company</th>
                  <th>Contact</th>
                  <th>Email</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {filteredSponsors.map((sponsor) => (
                  <tr key={sponsor.id}>
                    <td>
                      <strong>{sponsor.company_name}</strong>
                      {sponsor.industry && (
                        <div className="text-muted">{sponsor.industry}</div>
                      )}
                    </td>
                    <td>{sponsor.contact_person || '-'}</td>
                    <td>
                      {sponsor.email ? (
                        <a href={`mailto:${sponsor.email}`} className="link">
                          {sponsor.email}
                        </a>
                      ) : (
                        '-'
                      )}
                    </td>
                    <td>{sponsor.sponsor_type || '-'}</td>
                    <td>
                      <span className={`badge badge-${sponsor.status.replace('_', '-')}`}>
                        {sponsor.status.replace('_', ' ')}
                      </span>
                    </td>
                    <td>
                      <div className="action-buttons">
                        <Link to={`/sponsors/${sponsor.id}`} className="btn-icon" title="View">
                          <i className="fas fa-eye"></i>
                        </Link>
                        <Link to={`/sponsors/${sponsor.id}/edit`} className="btn-icon" title="Edit">
                          <i className="fas fa-edit"></i>
                        </Link>
                      </div>
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

export default Sponsors

