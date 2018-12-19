const path = require('path');
const webpack = require('webpack');

module.exports = {
  mode : 'production',
  entry : {
    monitor : './src/js/monitor.js'
  },
  plugins : [
      new webpack.ProvidePlugin({
        $ : "jquery",
        jQuery : "jquery",
        "window.jQuery" : "jquery"
      }), new webpack.ProvidePlugin({
        bootstrap : "bootstrap"
      })
  ],
  output : {
    path : path.resolve(__dirname, 'web/js'),
    filename : 'bundle.js'
  },
  module : {
    rules : [
      {
        test : /\.(scss)$/,
        use : [
            {
              loader : 'style-loader'
            }, {
              loader : 'css-loader'
            }, {
              loader : 'postcss-loader',
              options : {
                plugins : function () {
                  return [
                    require('autoprefixer')
                  ];
                }
              }
            }, {
              loader : 'sass-loader'
            }
        ]
      },
      {
        test: /\.css$/,
        loaders: ["style-loader", "css-loader"]
      }
    ]
  }
};
