import React, { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import api from '../services/api'
import './SponsorForm.css'

function SponsorAdd() {
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
  const [error, setError] = useState('')

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
      const response = await api.post('/sponsors', formData)
      navigate(`/sponsors/${response.data.id}`)
    } catch (err) {
      setError(err.response?.data?.error || 'Failed to create sponsor')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="sponsor-form-page">
      <div className="page-header">
        <h1>Add New Sponsor</h1>
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
                placeholder="Enter company name"
              />
            </div>

            <div className="form-group">
              <label>Contact Person</label>
              <input
                type="text"
                name="contact_person"
                value={formData.contact_person}
                onChange={handleChange}
                placeholder="Contact person name"
              />
            </div>

            <div className="form-group">
              <label>Email</label>
              <input
                type="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                placeholder="email@example.com"
              />
            </div>

            <div className="form-group">
              <label>Phone</label>
              <input
                type="text"
                name="phone"
                value={formData.phone}
                onChange={handleChange}
                placeholder="+1 (555) 123-4567"
              />
            </div>

            <div className="form-group">
              <label>Industry</label>
              <input
                type="text"
                name="industry"
                value={formData.industry}
                onChange={handleChange}
                placeholder="e.g., Technology, Finance"
              />
            </div>

            <div className="form-group">
              <label>Sponsor Type</label>
              <input
                type="text"
                name="sponsor_type"
                value={formData.sponsor_type}
                onChange={handleChange}
                placeholder="e.g., Gold, Silver, Bronze"
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
              {loading ? 'Creating...' : (
                <>
                  <i className="fas fa-save"></i> Create Sponsor
                </>
              )}
            </button>
            <button
              type="button"
              className="btn btn-secondary"
              onClick={() => navigate('/sponsors')}
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  )
}

export default SponsorAdd

