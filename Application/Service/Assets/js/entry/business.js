
let { combinePageModule } = require('modules/util');

combinePageModule({
	salary: require('page/salary'),
	company_declare: require('page/company_declare')
})
