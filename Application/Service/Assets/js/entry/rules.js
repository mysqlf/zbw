// 缴费规则管理

let { combinePageModule } = require('modules/util');

combinePageModule({
	rulesAdd: require('page/rules_add'),
	rulesList: require('page/rules_list')
})
