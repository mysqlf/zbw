let { concatApi } = require('./config'),
	api = {
		// 确认到款
		confirmMoney: '/Service-PayOrder-confirmPayment',
		// 生成订单
		generateOrder: '/Service-User-login'
	}

// 用户信息动作
module.exports = concatApi(api);