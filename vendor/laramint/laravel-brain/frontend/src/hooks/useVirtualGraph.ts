import { useEffect, useState, useMemo } from 'react'
import type { GraphElement } from '../types/graph'

/**
 * useVirtualGraph
 * For very large graphs, we don't want to dump 1000+ elements into the view at once.
 * This hook progressively adds elements to the graph in chunks.
 */
export function useVirtualGraph(elements: GraphElement[], threshold = 300) {
  const [limit, setLimit] = useState(threshold)

  // Adjust state during render if elements change (avoids Effect cascading render)
  const [prevElements, setPrevElements] = useState(elements)
  if (elements !== prevElements) {
    setPrevElements(elements)
    setLimit(threshold)
  }

  // Progressively increase limit using requestIdleCallback if available, or setTimeout
  useEffect(() => {
    if (limit >= elements.length) return

    const win = window as unknown as { 
      requestIdleCallback?: (cb: IdleRequestCallback) => number;
      cancelIdleCallback?: (handle: number) => void;
    }

    const nextFrame = win.requestIdleCallback 
      ? win.requestIdleCallback.bind(win)
      : (cb: IdleRequestCallback) => setTimeout(() => cb({ didTimeout: false, timeRemaining: () => 0 }), 100)

    const handle = nextFrame(() => {
      setLimit(prev => Math.min(prev + 200, elements.length))
    })

    return () => {
      if (win.cancelIdleCallback) win.cancelIdleCallback(handle as number)
      else clearTimeout(handle as number)
    }
  }, [limit, elements.length])

  const virtualElements = useMemo(() => {
    if (elements.length <= threshold) return elements
    return elements.slice(0, limit)
  }, [elements, limit, threshold])

  return virtualElements
}
