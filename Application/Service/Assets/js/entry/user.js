// 用户模块

let { combinePageModule } = require('modules/util');

combinePageModule({
	login: require('page/login')
})
