import React, { useState, useRef } from 'react'
import { useNavigate } from 'react-router-dom'
import api from '../services/api'
import './Import.css'

function Import() {
  const navigate = useNavigate()
  const [file, setFile] = useState(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const [success, setSuccess] = useState(null)
  const [dragActive, setDragActive] = useState(false)
  const fileInputRef = useRef(null)

  const handleDrag = (e) => {
    e.preventDefault()
    e.stopPropagation()
    if (e.type === 'dragenter' || e.type === 'dragover') {
      setDragActive(true)
    } else if (e.type === 'dragleave') {
      setDragActive(false)
    }
  }

  const handleDrop = (e) => {
    e.preventDefault()
    e.stopPropagation()
    setDragActive(false)

    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      handleFileSelect(e.dataTransfer.files[0])
    }
  }

  const handleFileSelect = (selectedFile) => {
    // Validate file type
    const validTypes = [
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      'application/vnd.ms-excel',
      'application/octet-stream'
    ]
    const isValidType = validTypes.includes(selectedFile.type) || 
                       /\.(xlsx|xls)$/i.test(selectedFile.name)

    if (!isValidType) {
      setError('Invalid file type. Please upload .xlsx or .xls files only.')
      return
    }

    // Check file size (limit to 100MB for web upload)
    if (selectedFile.size > 100 * 1024 * 1024) {
      setError('File is too large. Maximum size is 100MB. For larger files, use the CLI import script.')
      return
    }

    setFile(selectedFile)
    setError('')
    setSuccess(null)
  }

  const handleFileInputChange = (e) => {
    if (e.target.files && e.target.files[0]) {
      handleFileSelect(e.target.files[0])
    }
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    
    if (!file) {
      setError('Please select a file to upload')
      return
    }

    setError('')
    setSuccess(null)
    setLoading(true)

    try {
      const formData = new FormData()
      formData.append('file', file)

      const baseURL = window.location.origin.includes('localhost:3000') 
        ? 'http://localhost:8000/api' 
        : '/api'

      const response = await fetch(`${baseURL}/import.php`, {
        method: 'POST',
        body: formData,
        credentials: 'include'
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(data.error || 'Failed to import file')
      }

      if (data.success) {
        setSuccess({
          imported: data.imported,
          skipped: data.skipped,
          totalRows: data.total_rows,
          errors: data.errors || []
        })
        setFile(null)
        if (fileInputRef.current) {
          fileInputRef.current.value = ''
        }
      } else {
        throw new Error(data.error || 'Import failed')
      }
    } catch (err) {
      setError(err.message || 'Failed to import file. Please try again.')
      console.error('Import error:', err)
    } finally {
      setLoading(false)
    }
  }

  const handleBrowseClick = () => {
    fileInputRef.current?.click()
  }

  return (
    <div className="import-page">
      <div className="page-header">
        <h1>Import Excel</h1>
        <button
          className="btn btn-secondary"
          onClick={() => navigate('/sponsors')}
        >
          <i className="fas fa-arrow-left"></i> Back to Sponsors
        </button>
      </div>

      <div className="card">
        <div className="import-info">
          <h3>Excel File Format</h3>
          <p>Your Excel file should have the following columns (headers in first row):</p>
          <ul>
            <li><strong>Company Name</strong> <span className="required">(required)</span> - Company or organization name</li>
            <li><strong>Contact Person</strong> (optional) - Name of contact person</li>
            <li><strong>Email</strong> (optional) - Email address</li>
            <li><strong>Phone</strong> (optional) - Phone number</li>
            <li><strong>Industry</strong> (optional) - Industry sector</li>
            <li><strong>Type</strong> or <strong>Sponsor Type</strong> (optional) - Type of sponsor</li>
            <li><strong>Status</strong> (optional) - new, interested, in_progress, closed, rejected</li>
          </ul>
          <p className="note">
            <strong>Note:</strong> For files larger than 100MB, use the CLI import script instead.
          </p>
        </div>

        {error && (
          <div className="alert alert-error">
            <i className="fas fa-exclamation-circle"></i>
            {error}
          </div>
        )}

        {success && (
          <div className="alert alert-success">
            <i className="fas fa-check-circle"></i>
            <div>
              <strong>Import completed successfully!</strong>
              <div className="import-stats">
                <div>Total rows processed: <strong>{success.totalRows}</strong></div>
                <div>Successfully imported: <strong>{success.imported}</strong></div>
                <div>Skipped: <strong>{success.skipped}</strong></div>
                {success.errors.length > 0 && (
                  <div className="import-errors">
                    <strong>Some errors occurred:</strong>
                    <ul>
                      {success.errors.slice(0, 5).map((err, idx) => (
                        <li key={idx}>{err}</li>
                      ))}
                      {success.errors.length > 5 && (
                        <li>... and {success.errors.length - 5} more errors</li>
                      )}
                    </ul>
                  </div>
                )}
              </div>
              <button
                className="btn btn-primary mt-2"
                onClick={() => navigate('/sponsors')}
              >
                View Sponsors
              </button>
            </div>
          </div>
        )}

        <form onSubmit={handleSubmit} className="import-form">
          <div
            className={`file-drop-zone ${dragActive ? 'drag-active' : ''} ${file ? 'has-file' : ''}`}
            onDragEnter={handleDrag}
            onDragLeave={handleDrag}
            onDragOver={handleDrag}
            onDrop={handleDrop}
            onClick={handleBrowseClick}
          >
            <input
              ref={fileInputRef}
              type="file"
              accept=".xlsx,.xls"
              onChange={handleFileInputChange}
              style={{ display: 'none' }}
            />
            <div className="drop-zone-content">
              <i className="fas fa-cloud-upload-alt"></i>
              {file ? (
                <>
                  <p className="file-name">
                    <i className="fas fa-file-excel"></i> {file.name}
                  </p>
                  <p className="file-size">{(file.size / 1024 / 1024).toFixed(2)} MB</p>
                  <button
                    type="button"
                    className="btn-link"
                    onClick={(e) => {
                      e.stopPropagation()
                      setFile(null)
                      if (fileInputRef.current) {
                        fileInputRef.current.value = ''
                      }
                    }}
                  >
                    Remove file
                  </button>
                </>
              ) : (
                <>
                  <p>Drag and drop your Excel file here</p>
                  <p className="text-muted">or</p>
                  <button type="button" className="btn btn-secondary">
                    Browse Files
                  </button>
                  <p className="text-muted">Supports .xlsx and .xls files (max 100MB)</p>
                </>
              )}
            </div>
          </div>

          <div className="form-actions">
            <button
              type="submit"
              className="btn btn-primary"
              disabled={loading || !file}
            >
              {loading ? (
                <>
                  <i className="fas fa-spinner fa-spin"></i> Importing...
                </>
              ) : (
                <>
                  <i className="fas fa-upload"></i> Import Sponsors
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

      <div className="card info-card">
        <h3>
          <i className="fas fa-info-circle"></i> For Large Files (2GB+)
        </h3>
        <p>Use the command-line import script for better performance:</p>
        <pre>
          <code>php import_excel.php /path/to/file.xlsx --skip-header --batch-size=1000</code>
        </pre>
        <p><strong>Options:</strong></p>
        <ul>
          <li><code>--skip-header</code> - Skip first row (header)</li>
          <li><code>--batch-size=N</code> - Process N rows at a time (default: 1000)</li>
          <li><code>--start-row=N</code> - Start from row N</li>
          <li><code>--max-rows=N</code> - Import maximum N rows</li>
        </ul>
      </div>
    </div>
  )
}

export default Import

