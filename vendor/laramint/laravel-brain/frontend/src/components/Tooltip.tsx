import {
  autoUpdate,
  flip,
  FloatingPortal,
  offset,
  shift,
  useDismiss,
  useFloating,
  useFocus,
  useHover,
  useInteractions,
  useMergeRefs,
  useRole,
} from '@floating-ui/react'
import type { Placement } from '@floating-ui/react'
import { cloneElement, isValidElement, useState, type ReactElement, type ReactNode } from 'react'

export interface TooltipProps {
  /** Tooltip body (plain text or light markup). */
  content: ReactNode
  /** Single React element that receives hover/focus listeners and ref. */
  children: ReactElement
  placement?: Placement
  /** When true, no tooltip is shown and listeners are disabled. */
  disabled?: boolean
  className?: string
}

export function Tooltip({ content, children, placement = 'top', disabled = false, className }: TooltipProps) {
  const [open, setOpen] = useState(false)

  const { refs, floatingStyles, context } = useFloating({
    open: disabled ? false : open,
    onOpenChange: setOpen,
    placement,
    middleware: [offset(8), flip(), shift({ padding: 8 })],
    whileElementsMounted: autoUpdate,
  })

  const hover = useHover(context, {
    move: false,
    enabled: !disabled,
    delay: { open: 280, close: 80 },
  })
  const focus = useFocus(context, { enabled: !disabled })
  const dismiss = useDismiss(context)
  const role = useRole(context, { role: 'tooltip' })
  const { getReferenceProps, getFloatingProps } = useInteractions([hover, focus, dismiss, role])

  // eslint-disable-next-line react-hooks/refs
  const mergedRef = useMergeRefs([refs.setReference])

  if (!isValidElement(children)) {
    return <>{children}</>
  }

  const child = children as ReactElement

  return (
    <>
      {cloneElement(child, {
        ref: mergedRef,
        ...getReferenceProps(),
      } as Record<string, unknown>)}
      {open && !disabled && (
        <FloatingPortal>
          <div
            // eslint-disable-next-line react-hooks/refs
            ref={refs.setFloating}
            style={floatingStyles}
            className={['floating-tooltip', className].filter(Boolean).join(' ')}
            {...getFloatingProps()}
          >
            {content}
          </div>
        </FloatingPortal>
      )}
    </>
  )
}
