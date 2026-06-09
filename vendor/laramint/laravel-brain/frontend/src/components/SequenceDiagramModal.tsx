import { useEffect } from 'react'
import { SequenceDiagramView } from './SequenceDiagramView'
import type { SequenceDiagram } from '../types/graph'

interface Props {
  diagram: SequenceDiagram
  title: string
  theme?: 'dark' | 'light'
  onClose: () => void
}

export function SequenceDiagramModal({ diagram, title, theme, onClose }: Props) {
  useEffect(() => {
    const onKey = (e: KeyboardEvent) => { if (e.key === 'Escape') onClose() }
    window.addEventListener('keydown', onKey)
    return () => window.removeEventListener('keydown', onKey)
  }, [onClose])

  return (
    <div className="modal-overlay" onClick={(e) => { if (e.target === e.currentTarget) onClose() }}>
      <div className="modal-container modal-container--large">
        <div className="modal-header">
          <div className="modal-title">
            <span className="modal-icon">⇄</span>
            <div>
              <h2>{title}</h2>
              <span className="modal-sub">Sequence Diagram</span>
            </div>
          </div>
          <button className="modal-close" onClick={onClose} title="Close (Esc)">×</button>
        </div>

        <div className="modal-body sequence-modal-body">
          <SequenceDiagramView diagram={diagram} title={title} theme={theme} compact={false} />
        </div>
      </div>
    </div>
  )
}
