
let { combinePageModule } = require('modules/util');

combinePageModule({
	toIncrease: require('page/Insurance/toIncrease.js'),
	toIncreaseBatch:require('page/Insurance/toIncreaseBatch.js'),
	insuranceList:require('page/Insurance/insuranceList.js'),
	insuranceDetail:require('page/Insurance/insuranceDetail.js')
})
