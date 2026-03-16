const path = require('path');

module.exports = {
  entry: {
    bundle: './src/index.js',
  },
  externals: {
    underscore: '_',
    react: ['vendor', 'React'],
    'react-dom': ['vendor', 'ReactDOM'],
    jquery: 'jQuery',
    '@wordpress/hooks': ['vendor', 'wp', 'hooks'],
    '@wordpress/i18n': ['vendor', 'wp', 'i18n'],
  },
  module: {
    rules: [
      {
        test: /\.jsx?$/,
        exclude: /node_modules/,
        use: [
          {
            loader: 'thread-loader',
            options: {
              workers: -1,
            },
          },
          {
            loader: 'babel-loader',
            options: {
              compact: false,
              presets: [
                [
                  '@babel/preset-env',
                  {
                    modules: false,
                    targets: '> 5%',
                  },
                ],
                '@babel/preset-react',
              ],
              cacheDirectory: false,
            },
          },
        ],
      },
    ],
  },
  resolve: {
    extensions: ['.js', '.jsx'],
  },
  output: {
    filename: 'rubicon-maps-divi5.js',
    path: path.resolve(__dirname, 'build'),
  },
};
