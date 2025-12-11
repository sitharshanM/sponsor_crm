import React, { useState, useEffect } from 'react'

function AnimatedNumber({ value, duration = 1000, delay = 0 }) {
  const [displayValue, setDisplayValue] = useState(0)

  useEffect(() => {
    const timer = setTimeout(() => {
      const startTime = Date.now()
      const startValue = 0
      const endValue = value

      const animate = () => {
        const now = Date.now()
        const elapsed = now - startTime
        const progress = Math.min(elapsed / duration, 1)
        
        // Easing function for smooth animation
        const easeOutQuart = 1 - Math.pow(1 - progress, 4)
        const currentValue = Math.floor(startValue + (endValue - startValue) * easeOutQuart)
        
        setDisplayValue(currentValue)

        if (progress < 1) {
          requestAnimationFrame(animate)
        } else {
          setDisplayValue(endValue)
        }
      }

      animate()
    }, delay)

    return () => clearTimeout(timer)
  }, [value, duration, delay])

  return <span>{displayValue}</span>
}

export default AnimatedNumber

