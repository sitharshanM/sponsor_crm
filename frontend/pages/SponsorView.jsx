import React, { useState, useEffect } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import api from '../services/api'
import './SponsorView.css'

function SponsorView() {
  const { id } = useParams()
  const navigate = useNavigate()
  const [sponsor, setSponsor] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    fetchSponsor()
  }, [id])

  const fetchSponsor = async () => {
    try {
      const response = await api.get(`/sponsors/${id}`)
      setSponsor(response.data)
    } catch (error) {
      console.error('Error:', error)
    } finally {
      setLoading(false)
    }
  }

  if (loading) {
    return <div className="loading"><div className="spinner"></div></div>
  }

  if (!sponsor) {
    return <div>Sponsor not found</div>
  }

  return (
    <div className="sponsor-view-page">
      <div className="page-header">
        <div>
          <Link to="/sponsors" className="back-link">
            <i className="fas fa-arrow-left"></i> Back to Sponsors
          </Link>
          <h1>{sponsor.company_name}</h1>
        </div>
        <Link to={`/sponsors/${id}/edit`} className="btn btn-primary">
          <i className="fas fa-edit"></i> Edit
        </Link>
      </div>

      <div className="info-grid">
        <div className="card">
          <h2>Company Information</h2>
          <div className="info-list">
            <div className="info-item">
              <label>Company Name</label>
              <div>{sponsor.company_name}</div>
            </div>
            {sponsor.industry && (
              <div className="info-item">
                <label>Industry</label>
                <div>{sponsor.industry}</div>
              </div>
            )}
            {sponsor.sponsor_type && (
              <div className="info-item">
                <label>Sponsor Type</label>
                <div>{sponsor.sponsor_type}</div>
              </div>
            )}
            <div className="info-item">
              <label>Status</label>
              <div>
                <span className={`badge badge-${sponsor.status.replace('_', '-')}`}>
                  {sponsor.status.replace('_', ' ')}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div className="card">
          <h2>Contact Information</h2>
          <div className="info-list">
            {sponsor.contact_person && (
              <div className="info-item">
                <label>Contact Person</label>
                <div>{sponsor.contact_person}</div>
              </div>
            )}
            {sponsor.email && (
              <div className="info-item">
                <label>Email</label>
                <div>
                  <a href={`mailto:${sponsor.email}`} className="link">
                    {sponsor.email}
                  </a>
                </div>
              </div>
            )}
            {sponsor.phone && (
              <div className="info-item">
                <label>Phone</label>
                <div>
                  <a href={`tel:${sponsor.phone}`} className="link">
                    {sponsor.phone}
                  </a>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  )
}

export default SponsorView

