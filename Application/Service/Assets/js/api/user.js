let { concatApi } = require('./config'),
	api = {
		// 修改密码
		modifyPsw: '/Service-User-setPassword',
		// 登录
		login: '/Service-User-login',
		saveAccountInfo: '/Service-User-accountInfo',
		saveBankInfo: '/Service-Manage-bankInfo',
		// 添加子帐号
		addTeam: '/Service-Manage-addChildAccount',
		// 编辑子帐号
		editTeam: '/Service-Manage-childAccountInfo',
		// 删除子帐号
		delTeam: '/Service-User-delAccount'
	}

// 用户信息动作
module.exports = concatApi(api);