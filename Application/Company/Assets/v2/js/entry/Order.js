
let { combinePageModule } = require('modules/util');

combinePageModule({
	index: require('page/Order/index.js'),
	salalist:require('page/Order/salalist.js'),
	genBillList:require('page/Order/genBillList.js'),
	ngenBillList:require('page/Order/ngenBillList.js'),
	paysalary:require('page/Order/paysalary.js')
})
