const defaultTheme = require('tailwindcss/defaultTheme');
const gutenberg = require('tailwindcss-gutenberg');

module.exports = {
  important: true,

  theme: {
    screens: {
      sm: '640px',
      md: '768px',
      lg: '1024px',
      xl: '1280px',
    },
    maxWidth: {
      content: '672px',
      container: '1200px',
      ...defaultTheme.maxWidth,
    },
    colors: {
      transparent: 'transparent',
      primary: '#5c6ac4',
      secondary: '#007ace',
      red: '#de3618',
      white: '#ffffff',
      black: '#000000',
      'gray-light': '#e6e6e6',
      'gray-medium': '#cacaca',
      'gray-dark': '#8a8a8a',
    },
    fontFamily: {
      sans: [...defaultTheme.fontFamily.sans],
      serif: [...defaultTheme.fontFamily.serif],
      mono: [...defaultTheme.fontFamily.mono],
    },
    fontSize: {
      'xs': '12px',
      'sm': '14px',
      'base': '16px',
      'lg': '18px',
      'xl': '20px',
      '2xl': '24px',
      '3xl': '30px',
      '4xl': '36px',
      '5xl': '48px',
      '6xl': '64px',
    },
    spacing: {
      '0': '0',
      '1': '8px',
      '2': '12px',
      '3': '16px',
      '4': '24px',
      '5': '32px',
      '6': '48px',
    },
    gutenberg: (theme) => ({
      colors: {
        primary: theme('colors.primary'),
        secondary: theme('colors.secondary'),
        black: theme('colors.black'),
        white: theme('colors.white'),
      },
      foregroundColors: [
        theme('colors.black'),
        theme('colors.white'),
      ],
      fontSizes: {
        xs: theme('fontSize.xs'),
        sm: theme('fontSize.sm'),
        base: theme('fontSize.base'),
        xl: theme('fontSize.xl'),
        xxl: theme('fontSize.2xl'),
      },
      alignments: {
        scrollbarWidth: '0px',
        contentWidth: theme('maxWidth.content'),
        maxWidth: '1600px',

        alignfull: true,
        alignwide: {
          gutter: theme('spacing.1'),
          sizer: 1.25,
        },
        alignleftright: {
          minWidth: theme('screens.sm'),
          margin: theme('spacing.2'),
        },
      },
    }),
  },
  variants: {},
  plugins: [
    gutenberg.colors,
    gutenberg.fontSizes,
    gutenberg.foregroundColors,
    gutenberg.alignments,
    gutenberg.admin,
  ],
};
