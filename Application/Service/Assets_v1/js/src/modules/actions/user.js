var baseAct = require('./base');

// 用户信息动作
module.exports = {
	// 修改密码
	modifyPsw: function(data, cb){
		var opts = {
			data: data,
			url: '/Service-User-setPassword'
		}
		return baseAct.ajax(opts,cb);
	},
	// 登录
	login: function(data, cb){
		var opts = {
			data: data,
			url: '/Service-User-login'
		}
		return baseAct.ajax(opts,cb);
	},
	// 保存个人信息
	saveAccountInfo: function(data, cb){
		var opts = {
			data: data,
			url: '/Service-User-accountInfo'
		}
		return baseAct.ajax(opts,cb);
	},
	saveBankInfo: function(data, cb){
		var opts = {
			data: data,
			url: '/Service-User-bankInfo'
		}
		return baseAct.ajax(opts,cb);
	},
	// 添加子帐号
	addTeam: function(data, cb){
		var opts = {
			data: data,
			url: '/Service-User-addChildAccount'
		}
		
		return baseAct.ajax(opts,cb);
	},
	// 编辑子帐号
	editTeam: function(data, cb){
		var opts = {
			data: data,
			url: '/Service-User-childAccountInfo'
		}
		return baseAct.ajax(opts,cb);
	},
	// 删除子帐号
	delTeam: function(data, cb){
		var opts = {
			data: data,
			url: '/Service-User-delAccount'
		}
		return baseAct.ajax(opts,cb);
	}
}