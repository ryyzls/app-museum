import { useState, useEffect, useRef } from 'react'
import { downloadText } from '../utils/exportUtils'

interface Props {
  mermaidCode: string
  filename: string
  title: string
  onClose: () => void
}

export function ExportModal({ mermaidCode, filename, title, onClose }: Props) {
  const [copied, setCopied] = useState(false)
  const textareaRef = useRef<HTMLTextAreaElement>(null)

  // Close on Escape
  useEffect(() => {
    const onKey = (e: KeyboardEvent) => { if (e.key === 'Escape') onClose() }
    window.addEventListener('keydown', onKey)
    return () => window.removeEventListener('keydown', onKey)
  }, [onClose])

  const handleCopy = async () => {
    try {
      await navigator.clipboard.writeText(mermaidCode)
      setCopied(true)
      setTimeout(() => setCopied(false), 2000)
    } catch {
      textareaRef.current?.select()
      document.execCommand('copy')
      setCopied(true)
      setTimeout(() => setCopied(false), 2000)
    }
  }

  const handleDownload = () => downloadText(mermaidCode, filename)

  const handleOpenLive = () => {
    // Mermaid Live Editor accepts base64-encoded state
    const state = JSON.stringify({ code: mermaidCode, mermaid: '{}', autoSync: true })
    const encoded = btoa(unescape(encodeURIComponent(state)))
    window.open(`https://mermaid.live/edit#base64:${encoded}`, '_blank')
  }

  return (
    <div className="export-overlay" onClick={(e) => { if (e.target === e.currentTarget) onClose() }}>
      <div className="export-modal">
        {/* Header */}
        <div className="export-modal-header">
          <div className="export-modal-title">
            <span className="export-modal-icon">🗺</span>
            <div>
              <h2>{title}</h2>
              <span className="export-modal-sub">Mermaid Flowchart</span>
            </div>
          </div>
          <button className="export-modal-close" onClick={onClose} title="Close (Esc)">×</button>
        </div>

        {/* Action buttons */}
        <div className="export-modal-actions">
          <button className="export-btn export-btn--primary" onClick={handleCopy}>
            {copied ? '✓ Copied!' : '⎘ Copy Code'}
          </button>
          <button className="export-btn export-btn--secondary" onClick={handleDownload}>
            ↓ Download .mmd
          </button>
          <button className="export-btn export-btn--accent" onClick={handleOpenLive}>
            ↗ Open in Mermaid Live
          </button>
        </div>

        {/* Preview hint */}
        <div className="export-modal-hint">
          Paste this code at{' '}
          <a href="https://mermaid.live" target="_blank" rel="noreferrer">mermaid.live</a>
          {' '}to render the diagram, or use any Mermaid-compatible tool.
        </div>

        {/* Code block */}
        <div className="export-code-wrapper">
          <div className="export-code-lang">mermaid</div>
          <textarea
            ref={textareaRef}
            className="export-code"
            value={mermaidCode}
            readOnly
            spellCheck={false}
            onClick={(e) => (e.target as HTMLTextAreaElement).select()}
          />
        </div>

        {/* Stats */}
        <div className="export-modal-stats">
          <span>{mermaidCode.split('\n').length} lines</span>
          <span>{(mermaidCode.length / 1024).toFixed(1)} KB</span>
        </div>
      </div>
    </div>
  )
}
