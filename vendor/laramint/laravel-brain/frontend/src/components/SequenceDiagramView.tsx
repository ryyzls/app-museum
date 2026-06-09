import { useRef, useState } from 'react'
import type { SequenceDiagram } from '../types/graph'
import { ExportModal } from './ExportModal'
import { domToPng, downloadPng } from '../utils/exportUtils'
import { sequenceDiagramToMermaid } from '../utils/sequenceUtils'
import '../flowchart.css'

interface Props {
  diagram: SequenceDiagram
  title?: string
  theme?: 'dark' | 'light'
  compact?: boolean
}

const ACTOR_W = 110
const HEADER_H = 52
const ROW_H = 38
const PADDING = 16

export function SequenceDiagramView({ diagram, title, theme = 'dark' }: Props) {
  const [showMermaid, setShowMermaid] = useState(false)
  const [exporting, setExporting] = useState(false)
  const rootRef = useRef<HTMLDivElement>(null)

  const isDark = theme === 'dark'

  const totalW = PADDING * 2 + diagram.actors.length * ACTOR_W
  const totalH = HEADER_H + diagram.messages.length * ROW_H + ROW_H + HEADER_H

  const actorCx = (i: number) => PADDING + i * ACTOR_W + ACTOR_W / 2
  const msgY = (i: number) => HEADER_H + i * ROW_H + ROW_H / 2

  // Theme-based colors
  const textColor = isDark ? '#e0e0e0' : '#1a1a1a'
  const dimColor = isDark ? '#888' : '#999'
  const lifelineColor = isDark ? 'rgba(255,255,255,0.10)' : 'rgba(0,0,0,0.12)'
  const bgColor = isDark ? '#0d0f14' : '#ffffff'
  const returnColor = isDark ? 'rgba(255,255,255,0.35)' : 'rgba(0,0,0,0.30)'

  const markerId = isDark ? 'seq-arrow-dark' : 'seq-arrow-light'
  const markerReturnId = isDark ? 'seq-arrow-return-dark' : 'seq-arrow-return-light'
  const markerAsyncId = isDark ? 'seq-arrow-async-dark' : 'seq-arrow-async-light'
  const arrowColor = isDark ? '#a0aec0' : '#555'
  const asyncColor = isDark ? '#b39ddb' : '#7c4dff'

  const handleExportPng = async () => {
    if (!rootRef.current) return
    setExporting(true)
    try {
      const dataUrl = await domToPng(rootRef.current, bgColor)
      downloadPng(dataUrl, `${(title ?? 'sequence').replace(/[^a-z0-9]/gi, '_')}_sequence.png`)
    } finally {
      setExporting(false)
    }
  }

  if (diagram.actors.length === 0) {
    return <div className="flowchart-empty"><span>No sequence data available</span></div>
  }

  return (
    <>
      <div className="flowchart-export-bar">
        <button
          className="flowchart-export-btn"
          onClick={handleExportPng}
          disabled={exporting}
          title="Export as PNG"
        >
          {exporting ? '⏳' : '🖼'} PNG
        </button>
        <button
          className="flowchart-export-btn"
          onClick={() => setShowMermaid(true)}
          title="Export as Mermaid"
        >
          🧜 Mermaid
        </button>
      </div>

      <div className="seq-diagram-root" ref={rootRef}>
        <svg
          className="seq-diagram-svg"
          viewBox={`0 0 ${totalW} ${totalH}`}
          width="100%"
          style={{ background: bgColor, display: 'block' }}
          xmlns="http://www.w3.org/2000/svg"
        >
          <defs>
            {/* Solid arrowhead */}
            <marker
              id={markerId}
              markerWidth="8" markerHeight="6"
              refX="7" refY="3"
              orient="auto"
            >
              <polygon points="0 0, 8 3, 0 6" fill={arrowColor} />
            </marker>
            {/* Open/return arrowhead */}
            <marker
              id={markerReturnId}
              markerWidth="8" markerHeight="6"
              refX="7" refY="3"
              orient="auto"
            >
              <polyline points="0 0, 8 3, 0 6" fill="none" stroke={returnColor} strokeWidth="1.5" />
            </marker>
            {/* Async arrowhead */}
            <marker
              id={markerAsyncId}
              markerWidth="8" markerHeight="6"
              refX="7" refY="3"
              orient="auto"
            >
              <polygon points="0 0, 8 3, 0 6" fill={asyncColor} />
            </marker>
          </defs>

          {/* Actor header boxes */}
          {diagram.actors.map((actor, i) => {
            const cx = actorCx(i)
            const bw = ACTOR_W - 8
            const bx = cx - bw / 2
            const maxChars = Math.floor(bw / 6.5)
            const displayLabel = actor.label.length > maxChars
              ? actor.label.substring(0, maxChars - 1) + '…'
              : actor.label
            return (
              <g key={actor.id}>
                <rect
                  x={bx} y={4}
                  width={bw} height={HEADER_H - 10}
                  rx={5}
                  fill={isDark ? '#1a1d24' : '#f5f5f5'}
                  stroke={actor.color}
                  strokeWidth={1.5}
                />
                <text
                  x={cx} y={HEADER_H / 2 - 4}
                  textAnchor="middle"
                  dominantBaseline="middle"
                  fontSize={10.5}
                  fontFamily="system-ui, sans-serif"
                  fill={actor.color}
                  fontWeight="600"
                >
                  {displayLabel}
                </text>
                <text
                  x={cx} y={HEADER_H - 12}
                  textAnchor="middle"
                  dominantBaseline="middle"
                  fontSize={8}
                  fontFamily="system-ui, sans-serif"
                  fill={dimColor}
                >
                  {actor.type}
                </text>
              </g>
            )
          })}

          {/* Lifelines */}
          {diagram.actors.map((actor, i) => (
            <line
              key={`life-${actor.id}`}
              x1={actorCx(i)} y1={HEADER_H}
              x2={actorCx(i)} y2={totalH - HEADER_H}
              stroke={lifelineColor}
              strokeWidth={1}
              strokeDasharray="4 4"
            />
          ))}

          {/* Bottom actor boxes (mirror of top) */}
          {diagram.actors.map((actor, i) => {
            const cx = actorCx(i)
            const bw = ACTOR_W - 8
            const bx = cx - bw / 2
            const by = totalH - HEADER_H + 4
            const maxChars = Math.floor(bw / 6.5)
            const displayLabel = actor.label.length > maxChars
              ? actor.label.substring(0, maxChars - 1) + '…'
              : actor.label
            return (
              <g key={`bottom-${actor.id}`}>
                <rect
                  x={bx} y={by}
                  width={bw} height={HEADER_H - 10}
                  rx={5}
                  fill={isDark ? '#1a1d24' : '#f5f5f5'}
                  stroke={actor.color}
                  strokeWidth={1.5}
                />
                <text
                  x={cx} y={by + HEADER_H / 2 - 8}
                  textAnchor="middle"
                  dominantBaseline="middle"
                  fontSize={10.5}
                  fontFamily="system-ui, sans-serif"
                  fill={actor.color}
                  fontWeight="600"
                >
                  {displayLabel}
                </text>
                <text
                  x={cx} y={by + HEADER_H - 18}
                  textAnchor="middle"
                  dominantBaseline="middle"
                  fontSize={8}
                  fontFamily="system-ui, sans-serif"
                  fill={dimColor}
                >
                  {actor.type}
                </text>
              </g>
            )
          })}

          {/* Messages */}
          {diagram.messages.map((msg, i) => {
            const y = msgY(i)
            const x1 = actorCx(msg.fromIndex)
            const x2 = actorCx(msg.toIndex)
            const goingRight = x2 > x1
            const gap = 6
            const ax1 = goingRight ? x1 + gap : x1 - gap
            const ax2 = goingRight ? x2 - gap : x2 + gap

            const isReturn = msg.isReturn === true
            const isAsync = msg.isAsync === true

            const stroke = isReturn ? returnColor : isAsync ? asyncColor : arrowColor
            const dashArray = isReturn ? '5 3' : isAsync ? '6 3' : undefined
            const marker = isReturn ? markerReturnId : isAsync ? markerAsyncId : markerId

            const labelX = (x1 + x2) / 2
            // Estimate chars that fit: span / ~6 SVG units per char at fontSize 9
            const spanPx = Math.abs(x2 - x1) - gap * 2
            const maxChars = Math.max(10, Math.floor(spanPx / 6))
            const labelText = msg.label.length > maxChars
              ? msg.label.substring(0, maxChars - 1) + '…'
              : msg.label

            return (
              <g key={i}>
                <line
                  x1={ax1} y1={y}
                  x2={ax2} y2={y}
                  stroke={stroke}
                  strokeWidth={isReturn ? 1 : 1.5}
                  strokeDasharray={dashArray}
                  markerEnd={`url(#${marker})`}
                />
                {msg.label && (
                  <text
                    x={labelX}
                    y={y - 6}
                    textAnchor="middle"
                    fontSize={9}
                    fontFamily="system-ui, sans-serif"
                    fill={isReturn ? dimColor : textColor}
                    opacity={isReturn ? 0.75 : 1}
                  >
                    {labelText}
                  </text>
                )}
              </g>
            )
          })}
        </svg>
      </div>

      {showMermaid && (
        <ExportModal
          mermaidCode={sequenceDiagramToMermaid(diagram, title ?? 'sequence')}
          filename={`${(title ?? 'sequence').replace(/[^a-z0-9]/gi, '_')}_sequence.mmd`}
          title={title ?? 'Sequence Diagram'}
          onClose={() => setShowMermaid(false)}
        />
      )}
    </>
  )
}
