let { concatApi } = require('./config'),
	api = {
		//在线新增提交请求
		submitFrom:'/Service-Business-toIncrease',
		//会员套餐请求
		vipUrl:'/Service-Business-getLocation',
		//参保地请求
		locationUrl:'/Service-Business-getTemplateClassify',
		//社保类型请求
		socUrl:'/Service-Business-getTemplateRule',
		//公积金请求
		proUrl:'/Service-Business-getTemplateRule',
		//缴费信息请求
		costUrl:'/Service-Business-calculateCost',
		//身份证号带入请求
		idCard:'/Service-Business-getPersonBaseByCardNum',
		//详情页请求
		detailUrl:'/Service-Business-insurancePayDateDetail',
		//编辑表单提交路径
		editFrom:'/Service-Business-insuranceInfoDetail',
		//批量报增提交路径
		batchFrom:'/Service-Business-toIncreaseBatch',
		//社保公积金已审核支付接口
		payFrom:'/Service-Business-createPayOrder',
		//撤销公积金
		cancelUrl:'/Service-Business-cancel',
		//批量导入工资单接口
		salaryForm:'Service-Business-importSalary',
		// 获取办理数据
		getServiceInsuranceDetail: 'Service-Business-getServiceInsuranceDetail',
		//保存办理数据
		operateInsuranceOrder: 'Service-Business-operateInsuranceOrder',
		//获取套餐
		getSalaryServiceProductOrder: 'Service-Business-getSalaryServiceProductOrder'
	}

module.exports = concatApi(api);




