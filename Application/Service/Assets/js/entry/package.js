// 套餐管理

let { combinePageModule } = require('modules/util');

combinePageModule({
	packageList: require('page/package_list'),
	productManageService: require('page/productManageService')
})




