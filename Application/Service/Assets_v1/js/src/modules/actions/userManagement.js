var baseAct = require('./base');


module.exports = {
	// 获取客服
	getCService: function(data, cb){
		var opts = {
			data: data,
			url: '/Service-Members-cServideList'
		}
		return baseAct.ajax(opts,cb);
	},
	// 保存客服
	saveCService: function(data, cb){
		var opts = {
			data: data,
			url: '/Service-Members-comDisAdmin'
		}
		return baseAct.ajax(opts,cb);
	},
	// 设置价格
	setPrice: function(data, cb){
		var opts = {
			data: data,
			url: '/Service-Members-comSetPrice'
		}
		return baseAct.ajax(opts,cb);
	},
	// 设置服务
	setService: function(data, cb){var opts = {
			data: data,
			url: '/Service-Members-comSetService'
		}
		return baseAct.ajax(opts,cb);
	},
	// 获取参保地
	wLocation: function(data, cb){var opts = {
			data: data,
			url: '/Service-Members-wLocation'
		}
		return baseAct.ajax(opts,cb);
	},
	// 添加参保地
	addLocation: function(data, cb){var opts = {
			data: data,
			url: '/Service-Members-comAddLocation'
		}
		return baseAct.ajax(opts,cb);
	},
	comPayment: function(data, cb){
		var opts = {
			data: data,
			url: '/Service-Members-comPayment'
		}
		return baseAct.ajax(opts,cb);
	}
	
}