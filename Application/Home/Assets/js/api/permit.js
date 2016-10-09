/*通行证接口*/
// let origin = document.origin ? document.origin : document.protocol + document.host;

let { concatApi } = require('./config'),
	api = {
		// 找回密码第一步
		getBackPass1: '/Member-getBackPass',
		//找回密码第二步
		getBackPass2: '/Member-getBackPass2',
		//找回密码第三步 修改密码
		modifyPassword: '/Member-modifyPassword',
		//注册第一步（提交用户名和密码）
		firmRegister: '/Member-firmRegister',
		// //注册第二步（提交用户名和密码）
		firmRegister2: '/Member-firmRegister2',
		//注册第二步（提交用户名和密码）
		firmLogin: '/Member-firmLogin',
		// 服务商登录
		serviceLogin: '/Service-User-login'
	}

// 通行证接口
module.exports = concatApi(api);




