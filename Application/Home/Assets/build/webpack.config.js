var webpack = require('webpack');
var path = require('path');
var CommonsChunkPlugin = require('webpack/lib/optimize/CommonsChunkPlugin');
var HtmlWebpackPlugin = require('html-webpack-plugin');
var UglifyJsPlugin = require('webpack/lib/optimize/UglifyJsPlugin');
var glob = require('glob');

var clearFile = require('./clearFile');
var dirname = path.join(__dirname, '../');

try{
    // 删除文件
    clearFile(path.join(dirname, 'js/dist/'));
} catch(e){

}

var nodeRoot = path.join(dirname, 'node_modules');

// 获取js路径下的文件名称 和 路径
var jsPaths = getFileName('../js/*');

var paths = {
    css : path.join(dirname, 'css'),
    layer: 'plug/layer/layer'
}

// 合并对象
Object.assign(paths, jsPaths);


var NODE_ENV = 'development'//production  development
// 生成环境
var process = new webpack.DefinePlugin({
    'process.env': {
        NODE_ENV: JSON.stringify(NODE_ENV)
    }
});

var entries = getEntry('../js/entry/**/*.js', '..\\\\js\\\\entry\\\\');

var chunks = Object.keys(entries);

// 配各模块可以用的变量 不用
var globalVar =  new webpack.ProvidePlugin({
    $: "jquery",
    jQuery: "jquery",
    "window.jQuery": "jquery",
    'layer': 'layer'
});

var plugins = [
    process,
    new CommonsChunkPlugin({
        name: 'common', // 将公共模块提取，
        chunks: chunks,
        minChunks: 2 // 提取所有entry共同依赖的模块
    }),
    globalVar
];

if(NODE_ENV == 'production'){

    plugins.push(new UglifyJsPlugin({// 压缩
        compress: {
            warnings: false
        },
        mangle: {
            except: ['$super', '$', 'exports', 'require', '$scope']//不会被混淆
        }
    }))
}

module.exports = {
	node:{
		fs: 'empty'
	},
    entry: entries,
    output: {
        path: '../js/dist',
        publicPath: '/Application/Home/Assets/js/dist/',
        filename: '[name].bundle.js',
        chunkFilename: '[id].chunk.js'
    },
    //plugins: [process],
    progress:false,
    plugins: plugins,
    resolve: {
    	// 文件后缀名
        extensions: ['', '.js', '.json', '.coffee'],
        // 设置别名
        alias: paths
    },

    module: {
        loaders: [
            {
                test: /\.(jpg|png|gif)$/,
                loader: "url?limit=8192"
            },
        	{
                test: /\.vue$/,
                loader: 'tpl-loader-ie8',
                query: {
                    minimize: false
                }
            },
            {
                test: /\.css$/,
                loader: 'style-loader!css-loader'
                /*loader: 'style!css'*/
            },
            {
                test: /\.less$/,
                loader: 'style-loader!css-loader!less-loader'
            }, {
                test: /\.scss$/,
                loader: 'style-loader!css-loader!sass-loader'
            },
			// 支持es6语法
            {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/,
                query: {
                    compact: false,
                    presets: ['es2015', 'stage-0'],
                    plugins: ['transform-runtime']
                }
            }
        ]
    }
};

function getEntry(globPath, pathDir) {
    var files = glob.sync(globPath);
    var entries = {},
        entry, dirname,dirname2, basename, pathname, extname;

    for (var i = 0; i < files.length; i++) {
        entry = files[i];
        dirname = path.dirname(entry);
        extname = path.extname(entry);
        basename = path.basename(entry, extname);
        pathname = path.join(dirname, basename);
        pathname = pathDir ? pathname.replace(new RegExp('^' + pathDir), '') : pathname;

        if(basename.indexOf('_') !== 0){
            // 如果文件开头为下划线 不做入口  _app.js
            entries[pathname] = './' + entry;
        }
        
    }

    return entries;
}

function getFileName(globPath) {
    var files = glob.sync(globPath);
    var entries = {},
        entry, pathname;

    for (var i = 0; i < files.length; i++) {
        entry = files[i].split('/');
        pathname = entry[entry.length-1];
        entries[pathname] = path.join(__dirname, globPath.replace('/*', '/' + pathname));
    }

    return entries;
}

