module.exports = {
  purge: {
    enabled: true,
    content: [
      './src/**/*.html',
    ]
  },
  theme: {
    extend: {},
  },
  future: {
    removeDeprecatedGapUtilities: true,
  },
  experimental: {
    uniformColorPalette: true,
  },
  variants: {},
  plugins: [
    require('@tailwindcss/ui'),
  ],
}
