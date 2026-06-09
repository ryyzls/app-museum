import type { TabEntry } from '../types/graph'
import { Tooltip } from './Tooltip'

interface TabGroup {
  name: string
  list: TabEntry[]
}

interface Props {
  groups: TabGroup[]
  activeId: string | null
  loadingId: string | null
  onSelect: (tab: TabEntry) => void
}

export function TabBar({ groups, activeId, loadingId, onSelect }: Props) {
  return (
    <div className="tab-bar">
      {groups.map((group) => (
        <div key={group.name} className="tab-group">
          <Tooltip content="Grouped route files or categories from your project manifest.">
            <div className="tab-group-header">
              {group.name}
            </div>
          </Tooltip>
          <div className="tab-group-content">
            {group.list.map((tab) => {
              const isActive = tab.id === activeId
              const isLoading = tab.id === loadingId
              return (
                <Tooltip key={tab.id} content={`Open lifecycle for ${tab.label}. Graph: ${tab.nodeCount} nodes, ${tab.edgeCount} edges. Badge: ${tab.routeCount} route(s).`}>
                  <button
                    type="button"
                    className={`tab-item ${isActive ? 'tab-item--active' : ''}`}
                    onClick={() => onSelect(tab)}
                  >
                    <span className="tab-label">{tab.label}</span>
                    <span className="tab-badge">
                      {isLoading ? '…' : tab.routeCount}
                    </span>
                  </button>
                </Tooltip>
              )
            })}
          </div>
        </div>
      ))}
    </div>
  )
}
