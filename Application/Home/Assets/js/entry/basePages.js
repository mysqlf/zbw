let { combinePageModule } = require('modules/util');

require('modules/globalEffect').init();

combinePageModule({
	index: require('page/index'),
	serviceIndex: require('page/serviceIndex'),
	socialSecurity: require('page/socialSecurity'),
	servicepoint: require('page/servicepoint')
})
