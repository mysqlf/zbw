let { combinePageModule } = require('modules/util');

combinePageModule({
	getBackPass: require('page/getBackPass'),
	getBackPass2: require('page/getBackPass2'),
	getBackPass3: require('page/getBackPass3'),
	firmRegister1: require('page/firmRegister1'),
	firmRegister2: require('page/firmRegister2'),
	login: require('page/login')
})
