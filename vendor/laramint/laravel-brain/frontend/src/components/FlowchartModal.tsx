import { useEffect } from 'react'
import { FlowchartView } from './FlowchartView'
import type { FlowStep } from '../types/graph'

interface Props {
  steps: FlowStep[]
  title: string
  isFatMethod?: boolean
  onClose: () => void
}

export function FlowchartModal({ steps, title, isFatMethod, onClose }: Props) {
  // Close on Escape
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
            <span className="modal-icon">⛓</span>
            <div>
              <h2>{title}</h2>
              <span className="modal-sub">Method Flow Visualization</span>
            </div>
          </div>
          <button className="modal-close" onClick={onClose} title="Close (Esc)">×</button>
        </div>
        
        <div className="modal-body flowchart-modal-body">
          <FlowchartView steps={steps} isFatMethod={isFatMethod} />
        </div>
      </div>
    </div>
  )
}
