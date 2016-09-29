var baseAct = require('./base');


module.exports = {
	// 查看工资
	viewPayroll: function(data, cb){
		var opts = {
			data: data,
			url: '/Service-Order-salaryAudit'
		}
		return baseAct.ajax(opts,cb);
	},
	savePayroll: function(data, cb){
		var opts = {
			data: data,
			url: '/Service-Order-salaryData'
		}
		return baseAct.ajax(opts,cb);
	},
	// 撤销
	delPayroll: function(data, cb){
		var opts = {
			data: data,
			url: '/Service-Order-salaryRevoke'
		}
		return baseAct.ajax(opts,cb);
	}
}