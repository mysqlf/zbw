var baseAct = require('./base');

// 账单动作
module.exports = {
	// 企业账单付款
	comBillpayment: function(data, cb){
		var opts = {
			data: data,
			url: '/Service-Bill-comBillpayment'
		}
		return baseAct.ajax(opts,cb);
	}
}