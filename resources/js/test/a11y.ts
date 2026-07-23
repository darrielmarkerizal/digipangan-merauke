import { configureAxe } from 'vitest-axe'

export const axe = configureAxe({
  rules: {
    region: { enabled: false },
    'landmark-one-main': { enabled: false },
    'page-has-heading-one': { enabled: false },
  },
})
