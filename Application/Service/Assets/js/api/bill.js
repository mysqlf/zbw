let { concatApi } = require('./config'),
	api = {
		// 企业账单付款
		comBillpayment: '/Service-Bill-invoice'
	}

// 账单api
module.exports = concatApi(api);

