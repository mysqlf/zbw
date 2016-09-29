// 业务管理模块
let { concatApi } = require('./config'),

	api = {
		// 批量审核、批量发放
		updateSalaryOrder: 'Service-Business-updateSalaryOrder',
		// 删除代发工资
		deleteSalaryOrder: 'Service-Business-deleteSalaryOrder',
		
		updateInsuranceOrder: 'Service-Business-updateInsuranceOrder'
	}


module.exports = concatApi(api);