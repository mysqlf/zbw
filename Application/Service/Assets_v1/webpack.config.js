var webpack = require('webpack');
var path = require('path');

var lib = path.join(__dirname, './js/src/lib');
var entry = path.join(__dirname, './js/entry');
var tpl = path.join(__dirname, './js/src/tpl');
var page = path.join(__dirname, './js/src/page');
var plug = path.join(__dirname, './js/src/plug');
var modules = path.join(__dirname, './js/src/modules');
var mockData = path.join(__dirname, './js/src/mockData');// 模拟数据请求

var nodeRoot = path.join(__dirname, 'node_modules');

var NODE_ENV = 'development'//production  development
// 生成环境
var process = new webpack.DefinePlugin({
    'process.env': {
        NODE_ENV: JSON.stringify(NODE_ENV)
    }
});

var CommonsChunkPlugin = require('webpack/lib/optimize/CommonsChunkPlugin');
var HtmlWebpackPlugin = require('html-webpack-plugin');
var UglifyJsPlugin = require('webpack/lib/optimize/UglifyJsPlugin');


// 配各模块可以用的变量 不用
var globalVar =  new webpack.ProvidePlugin({
    $: "jquery",
    jQuery: "jquery",
    "window.jQuery": "jquery",
    'layer': 'layer'
})


var plugins = [
    process,
    new CommonsChunkPlugin('common.js'),
    globalVar
    
]

if(NODE_ENV == 'production'){
    /*
     * 如果使用vue-loader  需要在压缩时候 去掉 
     * node_modules文件夹下
     * content = htmlMinifier.minify(content, minimizeOptions); vue-html-loader 第90行
     * 原因
     * 但模板 出现非法属性 如 <input {{if xxx}}checked{{/if}}/> 无法压缩
     * vue-loader 使用html-minifier造成的
     */
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
    entry: {
        order: entry + '/order.js',
        bill: entry + '/bill.js',
        user: entry + '/user.js',
        set: entry + '/set.js',
        CM: entry + '/CM.js',
        product: entry + '/product.js'
    },
    output: {
        path: './js/dist',
        publicPath: '/Application/Service/Assets/js/dist/',
        filename: '[name].bundle.js'
    },
    //plugins: [process],
    plugins: plugins,
    resolve: {
    	// 文件后缀名
        extensions: ['', '.js', '.json', '.coffee'],
        // 设置别名
        alias: {
        	tpl: tpl,
        	lib : lib,
			entry : entry,
			page : page,
			plug : plug,
			modules : modules,
			mockData: mockData,
            layer: plug + '/layer-v2.1/layer/layer.js',
            editor: plug + '/ueditor',
            uploader: plug + '/webuploader'

        }
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
                loader: 'style!css'
            },
			// 支持es6语法 使用 var a = b = 2 这种语法会报 b 未定义错误
            /*{
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/,
                query: {
                    compact: false,
                    presets: ['es2015']
                }
            }*/
        ]
    }
}
