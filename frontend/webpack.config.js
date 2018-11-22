// const webpack = require('webpack');
const HtmlWebpackPlugin = require('html-webpack-plugin');
// 分离 css 文件
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const path = require('path');

const devMode = process.env.NODE_ENV !== 'production';

let config = {
  mode: 'production',
  performance: {
    hints: 'warning',
    maxAssetSize: 250000, //单文件超过250k，命令行告警
    maxEntrypointSize: 250000, //首次加载文件总和超过250k，命令行告警
  },
  cache: true,
  // 输入文件则默认为 src/index.js，输出为 dist/main.js
  entry: {
    login: __dirname + '/js/login/main.js',
    builds: path.resolve('./js/builds/main.js'),
    profile: path.resolve('./js/profile/main.ts'),
    demo: path.resolve('./js/demo/main.js'),
    sse: path.resolve('./js/sse/main.js'),
    websocket: path.resolve('./js/websocket/main.js'),
  },
  output: {
    path: devMode
      ? __dirname + '/../public/assets/'
      : __dirname + '/../public/assets/[hash]/',
    filename: 'js/[name].js',
    // pathinfo: true
    // publicPath: 'https://cdn.example.com/assets/js/[hash]/',
    publicPath: devMode ? '/assets/' : '/assets/[hash]/',
  },
  devtool: 'none',
  devServer: {
    contentBase: '../public',
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
      template: path.resolve('./html/demo/source.html'), // 模板地址
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
    }),
    new HtmlWebpackPlugin({
      template: path.resolve('./html/login/index.html'),
      filename: path.resolve('../public/login/index.html'),
      showErrors: true,
      chunks: ['login'], // 只包括指定的 js
    }),
    new HtmlWebpackPlugin({
      template: path.resolve('./html/login/hello.html'),
      filename: path.resolve('../public/login/hello.html'),
      showErrors: true,
      chunks: ['login'], // 只包括指定的 js
    }),
    new HtmlWebpackPlugin({
      template: path.resolve('./html/profile/index.html'),
      filename: path.resolve('../public/profile/index.html'),
      showErrors: true,
      chunks: ['profile'], // 只包括指定的 js
    }),
    new HtmlWebpackPlugin({
      template: path.resolve('./html/sse/index.html'),
      filename: path.resolve('../public/sse/index.html'),
      showErrors: true,
      chunks: ['sse'], // 只包括指定的 js
    }),
    new HtmlWebpackPlugin({
      template: path.resolve('./html/websocket/index.html'),
      filename: path.resolve('../public/websocket/index.html'),
      showErrors: true,
      chunks: ['websocket'], // 只包括指定的 js
    }),
    new HtmlWebpackPlugin({
      template: path.resolve('./html/index.html'),
      filename: path.resolve('../public/index.html'),
      showErrors: true,
      chunks: ['index*'], // 只包括指定的 js
    }),
    // 分离 css 文件
    new MiniCssExtractPlugin({
      filename: 'css/[name].css',
      chunkFilename: '[id].css',
    }),
  ],
  optimization: {
    minimize: true, //是否进行代码压缩
    noEmitOnErrors: true, //取代 new webpack.NoEmitOnErrorsPlugin()，编译错误时不打印输出资源。
  },
  // Webpack 中所有类型的文件都是模块，包括JS、CSS、图片、字体、JSON...
  // 万物皆模块
  module: {
    // https://github.com/webpack-contrib
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
        ], // 注意顺序，由下向上执行
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
          // {
          //   // file-loader 可以解析项目中的 url 引入（不仅限于css），
          //   // 根据我们的配置，将图片拷贝到相应的路径，再根据我们的配置，
          //   // 修改打包后文件引用路径，使之指向正确的文件。
          //   loader: 'file-loader',
          //   options: {
          //     name: '[path][name].[ext]',
          //     content: '',
          //     // outputPath: 'images/',
          //     publicPath: 'assets/images/'
          //   },
          // },
          {
            // 在处理图片和进行 base64 编码的时候，需要使用 url-loader
            // 作用是编码
            // A loader for webpack which transforms files into base64 URIs.
            // url-loader works like file-loader,
            // but can return a DataURL if the file is smaller than a byte limit.
            // css: background: url("./../assets/imgs/1.jpg") no-repeat;
            loader: 'url-loader',
            options: {
              limit: 8192, // 表示小于 8kb 的图片转为 base64,大于 8kb 的是路径
              name: '[name].[ext]',
              content: '',
              outputPath: 'images/',
              // publicPath: '/assets/images/',
              // https://cdn.ci.khs1994.com/assets/images/
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
