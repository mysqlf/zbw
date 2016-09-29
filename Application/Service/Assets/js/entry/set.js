// 设置模块

let { combinePageModule } = require('modules/util');

combinePageModule({
	account_info: require('page/account_info'),
	team_manage: require('page/team_manage'),
	bank_info: require('page/bank_info')
})
