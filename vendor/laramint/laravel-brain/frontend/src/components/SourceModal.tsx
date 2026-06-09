import { useEffect } from 'react'
import { SourceView } from './SourceView'

interface Props {
  filePath: string
  highlightLine?: number
  theme: 'dark' | 'light'
  onClose: () => void
}

export function SourceModal({ filePath, highlightLine, theme, onClose }: Props) {
  // Close on Escape
  useEffect(() => {
    const onKey = (e: KeyboardEvent) => { if (e.key === 'Escape') onClose() }
    window.addEventListener('keydown', onKey)
    return () => window.removeEventListener('keydown', onKey)
  }, [onClose])

  const title = filePath.split('/').pop() || 'Source Code'

  return (
    <div className="modal-overlay" onClick={(e) => { if (e.target === e.currentTarget) onClose() }}>
      <div className="modal-container modal-container--large">
        <div className="modal-header">
          <div className="modal-title">
            <span className="modal-icon">📄</span>
            <div>
              <h2>{title}</h2>
              <span className="modal-sub">{filePath}</span>
            </div>
          </div>
          <button className="modal-close" onClick={onClose} title="Close (Esc)">×</button>
        </div>
        
        <div className="modal-body source-modal-body">
          <SourceView filePath={filePath} highlightLine={highlightLine} theme={theme} />
        </div>
      </div>
    </div>
  )
}
