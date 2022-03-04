module.exports = {
  content: [
	'public/site/*.php',
	'public/site/templates/*.php',
	'public/site/templates/**/*.php',
	'public/site/templates/**/*.js',
  ],
  theme: {
    extend: {
      fontSize: {
        'xxs': '.65rem',
        '4xl': '2rem',
        '5xlb': ['2.8rem', '3.2rem' ],
        '8xl': ['5.5rem', '1.1em'],
        'base': ['1rem', '1.35rem']
      },
      fontFamily: {
        'sans': ['Moderat'],
        'sansBold': ['Moderat-Bold'],
        'serif': ['Morion'],
      },
      spacing: {
        '30': '7.5rem',
        '97': '28rem',
      },
      width: {
        '99': '45rem',
      },
      colors:{
        'verde-sa': {  DEFAULT: '#0E9B7E',  '50': '#6FF2D7',  '100': '#5CF0D2',  '200': '#37EDC7',  '300': '#15E6BB',  '400': '#11C09C',  '500': '#0E9B7E',  '600': '#096854',  '700': '#05342A',  '800': '#000101',  '900': '#000000'},
        'blu-sa': {  DEFAULT: '#273583',  '50': '#8693DB',  '100': '#7785D6',  '200': '#5769CD',  '300': '#3A4EC2',  '400': '#3042A2',  '500': '#273583',  '600': '#1A2458',  '700': '#273a48',  '800': '#000101',  '900': '#000000'},
      },
      rotate:{
        '270': '270deg',
      },
      lineHeight: {
        'snug': '1.4rem',
        'snug1': '2.4rem',
        '12': '2.7rem',
      },
      translate: {
        '5/6': '83.333333%',
      }
    },
  },
  plugins: [],
}
