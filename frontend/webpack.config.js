'use strict';

// const webpack = require('webpack');
const HtmlWebpackPlugin = require('html-webpack-plugin');
// 分离 css 文件
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

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
    sse: path.resolve('./js/sse/main.js'),
    websocket: path.resolve('./js/websocket/main.js'),
  },
  output: {
    path: __dirname + '/../public/assets/js',
    filename: '[name].js',
    // pathinfo: true
    // publicPath: 'https://cdn.example.com/assets/[hash]/',
    publicPath: '/assets/js',
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
      template: path.resolve('./html/demo/source.html'),
      filename: path.resolve('../public/demo/index.html'),
      showErrors: true,
      chunks: ['demo'], // 只包括指定的 js
      // excludeChunks: ['demo'], // 排除指定的 js
    }),
    new HtmlWebpackPlugin({
      template: path.resolve('./html/builds/index.html'),
      filename: path.resolve('../public/builds/index.html'),
      showErrors: true,
      chunks: ['builds'], // 只包括指定的 js
      // excludeChunks: ['demo'], // 排除指定的 js
    }),
    new HtmlWebpackPlugin({
      template: path.resolve('./html/login/index.html'),
      filename: path.resolve('../public/login/index.html'),
      showErrors: true,
      chunks: ['login'], // 只包括指定的 js
      // excludeChunks: ['demo'], // 排除指定的 js
    }),
    new HtmlWebpackPlugin({
      template: path.resolve('./html/login/hello.html'),
      filename: path.resolve('../public/login/hello.html'),
      showErrors: true,
      chunks: ['login'], // 只包括指定的 js
      // excludeChunks: ['demo'], // 排除指定的 js
    }),
    new HtmlWebpackPlugin({
      template: path.resolve('./html/profile/index.html'),
      filename: path.resolve('../public/profile/index.html'),
      showErrors: true,
      chunks: ['profile'], // 只包括指定的 js
      // excludeChunks: ['demo'], // 排除指定的 js
    }),
    new HtmlWebpackPlugin({
      template: path.resolve('./html/sse/index.html'),
      filename: path.resolve('../public/sse/index.html'),
      showErrors: true,
      chunks: ['sse'], // 只包括指定的 js
      // excludeChunks: ['demo'], // 排除指定的 js
    }),
    new HtmlWebpackPlugin({
      template: path.resolve('./html/websocket/index.html'),
      filename: path.resolve('../public/websocket/index.html'),
      showErrors: true,
      chunks: ['websocket'], // 只包括指定的 js
      // excludeChunks: ['demo'], // 排除指定的 js
    }),
    new HtmlWebpackPlugin({
      template: path.resolve('./html/index.html'),
      filename: path.resolve('../public/index.html'),
      showErrors: true,
      chunks: ['index*'], // 只包括指定的 js
      // excludeChunks: ['demo'], // 排除指定的 js
    }),
    // 分离 css 文件
    new MiniCssExtractPlugin({
      filename: '[name].css',
      chunkFilename: '[id].css',
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
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              publicPath: '../',
            },
          },
          // 'style-loader',
          // 代码中无需再使用 style-loader。如果使用将会报错：window is not define
          // style-loader 与 MiniCssExtractPlugin 冲突
          'css-loader',
        ], // 注意顺序
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
