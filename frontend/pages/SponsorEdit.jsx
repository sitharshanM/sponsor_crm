import React, { useState, useEffect } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import api from '../services/api'
import './SponsorForm.css'

function SponsorEdit() {
  const { id } = useParams()
  const navigate = useNavigate()
  const [formData, setFormData] = useState({
    company_name: '',
    contact_person: '',
    email: '',
    phone: '',
    industry: '',
    sponsor_type: '',
    status: 'new'
  })
  const [loading, setLoading] = useState(false)
  const [fetching, setFetching] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    fetchSponsor()
  }, [id])

  const fetchSponsor = async () => {
    try {
      const response = await api.get(`/sponsors/${id}`)
      setFormData(response.data)
    } catch (error) {
      console.error('Error:', error)
    } finally {
      setFetching(false)
    }
  }

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    })
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError('')
    setLoading(true)

    try {
      await api.put(`/sponsors/${id}`, formData)
      navigate(`/sponsors/${id}`)
    } catch (err) {
      setError(err.response?.data?.error || 'Failed to update sponsor')
    } finally {
      setLoading(false)
    }
  }

  if (fetching) {
    return <div className="loading"><div className="spinner"></div></div>
  }

  return (
    <div className="sponsor-form-page">
      <div className="page-header">
        <h1>Edit Sponsor</h1>
      </div>

      <div className="card">
        {error && (
          <div className="alert alert-error">
            <i className="fas fa-exclamation-circle"></i>
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="sponsor-form">
          <div className="form-grid">
            <div className="form-group">
              <label>Company Name <span className="required">*</span></label>
              <input
                type="text"
                name="company_name"
                value={formData.company_name}
                onChange={handleChange}
                required
              />
            </div>

            <div className="form-group">
              <label>Contact Person</label>
              <input
                type="text"
                name="contact_person"
                value={formData.contact_person || ''}
                onChange={handleChange}
              />
            </div>

            <div className="form-group">
              <label>Email</label>
              <input
                type="email"
                name="email"
                value={formData.email || ''}
                onChange={handleChange}
              />
            </div>

            <div className="form-group">
              <label>Phone</label>
              <input
                type="text"
                name="phone"
                value={formData.phone || ''}
                onChange={handleChange}
              />
            </div>

            <div className="form-group">
              <label>Industry</label>
              <input
                type="text"
                name="industry"
                value={formData.industry || ''}
                onChange={handleChange}
              />
            </div>

            <div className="form-group">
              <label>Sponsor Type</label>
              <input
                type="text"
                name="sponsor_type"
                value={formData.sponsor_type || ''}
                onChange={handleChange}
              />
            </div>
          </div>

          <div className="form-group">
            <label>Status</label>
            <select
              name="status"
              value={formData.status}
              onChange={handleChange}
            >
              <option value="new">New</option>
              <option value="interested">Interested</option>
              <option value="in_progress">In Progress</option>
              <option value="closed">Closed</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>

          <div className="form-actions">
            <button type="submit" className="btn btn-primary" disabled={loading}>
              {loading ? 'Saving...' : (
                <>
                  <i className="fas fa-save"></i> Save Changes
                </>
              )}
            </button>
            <button
              type="button"
              className="btn btn-secondary"
              onClick={() => navigate(`/sponsors/${id}`)}
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  )
}

export default SponsorEdit

