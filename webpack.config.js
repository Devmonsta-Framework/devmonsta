const path = require('path');

module.exports = {
    mode: 'production',
  entry: {
      'dm-init-scripts': __dirname + '/core/options/assets/src/init-scripts/index.js',
      'dm-vendor-scripts': __dirname + '/core/options/assets/src/vendor-scripts/index.js'
  },
  output: {
    path: path.resolve(__dirname, 'core/options/assets/', 'js'),
    filename: '[name].bundle.js'
  },
  module: {
    rules: [
      {
        test: /\.m?js$/,
        exclude: /(node_modules|bower_components)/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env']
          }
        }
      }
    ]
  },
  externals: {
    "jquery": "jQuery"
  }
};