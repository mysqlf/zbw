
let { combinePageModule } = require('modules/util');

combinePageModule({
	toIncrease: require('page/toIncrease'),
	insuranceDetail: require('page/insuranceDetail'),
	toIncreaseBatch: require('page/toIncreaseBatch')
})

