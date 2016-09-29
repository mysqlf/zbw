var baseAct = require('./base');

module.exports = {
	// 添加企业产品
	addComProduct: function(data, cb){
		var opts = {
			data: data,
			url: '/Service-Product-productDetail'
		}
		return baseAct.ajax(opts,cb);
	},
	// 删除企业产品
	delProduct:function(data, cb){
		var opts = {
			data: data,
			url: '/Service-Product-delProduct'
		}
		return baseAct.ajax(opts,cb);
	},
	// 删除增值服务
	delService:function(data, cb){
		var opts = {
			data: data,
			url: '/Service-Product-delService'
		}
		return baseAct.ajax(opts,cb);
	},
	// 添加增值服务
	addProductService:function(data, cb){
		var opts = {
			data: data,
			url: '/Service-Product-serviceDetail'
		}
		return baseAct.ajax(opts,cb);
	},
	// 编辑
	editProductService:function(data, cb){
		var opts = {
			data: data,
			url: '/Service-Product-serviceInfo'
		}
		return baseAct.ajax(opts,cb);
	},
	// 获取城市地址
	getCityLocaltion:function(data, cb){
		var opts = {
			data: data,
			url: '/Service-Product-selectLocation',
			error: null
		}
		return baseAct.ajax(opts,cb);
	}
}