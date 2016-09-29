let { concatApi } = require('./config'),
	api = {
		// 获取模板规则
		getTemplateRule: '/Service-Business-getTemplateRule',
		// 获取社保类型
		getTemplateClassify: '/Service-Business-getTemplateClassify',
		toggleRulesStauts: '/Service-Rules-status',
		// 保存规则
		saveRules: '/Service-Rules-save'
	}

// 用户信息动作
module.exports = concatApi(api);