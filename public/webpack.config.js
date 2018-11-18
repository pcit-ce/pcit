'use strict';

const webpack = require('webpack');
const HtmlWebpackPlugin = require('html-webpack-plugin');

// const webpack = require('webpack');
const path = require('path');

let config = {
  mode: 'production',
  performance: {
    hints: 'warning',
    maxAssetSize: 250000, //单文件超过250k，命令行告警
    maxEntrypointSize: 250000, //首次加载文件总和超过250k，命令行告警
  },
  cache: true,
  entry: {
    builds: path.resolve('./js/builds/main.js'),
    login: __dirname + '/js/login/main.js',
    profile: __dirname + '/js/profile/main.ts',
    demo: path.resolve('./js/demo/main.js'),
  },
  output: {
    path: __dirname + '/assets/js',
    filename: '[name].js',
    // pathinfo: true
    // publicPath: 'https://cdn.example.com/assets/[hash]/',
  },
  devtool: 'none',
  devServer: {
    contentBase: './',
    historyApiFallback: true,
    inline: true,
    hot: true,
  },
  plugins: [
    // new webpack.DefinePlugin({
    //   'process.env.NODE_ENV': JSON.stringify('production'),
    // }),

    new HtmlWebpackPlugin({
      title: 'Demo',
      template: path.resolve('./demo/source.html'),
      filename: path.resolve('./demo/index.html'),
      showErrors: true,
      chunks: ['demo'], // 只包括指定的 js
      // excludeChunks: ['demo'], // 排除指定的 js
    }),
  ],
  optimization: {
    minimize: true, //是否进行代码压缩
    noEmitOnErrors: true, //取代 new webpack.NoEmitOnErrorsPlugin()，编译错误时不打印输出资源。
  },
  module: {
    rules: [
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader'], // 注意顺序
        // use [{loader:'style-loader'}]
        // exclude:
      },
      {
        test: /\.ts$/,
        use: 'ts-loader',
      },
      {
        test: /\.(gif|jpg|png)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '[path][name].[ext]',
              content: '',
              // outputPath: 'images/'
            },
          },
          {
            loader: 'url-loader',
            options: {
              limit: 8192,
            },
          },
        ],
      },
    ],
  },
};

module.exports = (env, argv) => {
  if (argv.mode === 'development') {
    // config.devtool = 'source-map';
  }

  if (argv.mode === 'production') {
    //
  }

  return config;
};
