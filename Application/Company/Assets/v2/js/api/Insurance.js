let { concatApi } = require('./config'),
	api = {
		//在线新增提交请求
		submitFrom:'/Company-Insurance-toIncrease',
		//会员套餐请求
		vipUrl:'/Company-Insurance-getLocation',
		//参保地请求
		locationUrl:'/Company-Insurance-getTemplateClassify',
		//社保类型请求
		socUrl:'/Company-Insurance-getTemplateRule',
		//公积金请求
		proUrl:'/Company-Insurance-getTemplateRule',
		//缴费信息请求
		costUrl:'/Company-Insurance-calculateCost',
		//身份证号带入请求
		idCard:'/Company-Insurance-getPersonBaseByCardNum',
		//详情页请求
		detailUrl:'/Company-Insurance-insurancePayDateDetail',
		//编辑表单提交路径
		editFrom:'/Company-Insurance-editIncrease',
		//批量报增提交路径
		batchFrom:'/Company-Insurance-toIncreaseBatch',
		//社保公积金已审核支付接口
		payFrom:'/Company-Insurance-createPayOrder',
		//撤销公积金
		cancelUrl:'/Company-Insurance-cancel',
		//信息详情
		msgDetail:'/Company-Information-msgDetail',
		//企业信息提交接口
		companyForm:'/Company-Account-companyInfo',
		//批量导入工资单接口
		salaryForm:'/Company-Salary-importSalary',
		//报减
		subUrl:'/Company-Insurance-toReduce',
		//获取当前的
		itemUrl:'Company-Insurance-getReduceItem',
		//付款明细立即支付接口
		payUrl:'/Company-Order-getPriceByOrderid',
		// 账号设置修改密码
		changePassWord:'/Company-Account-changePassword',
		//账号设置绑定邮箱
		bindEmail:'/Company-Account-changeEmail',
		getOrderBank: '/Company-Order-getOrderBank'
	}

module.exports = concatApi(api);




