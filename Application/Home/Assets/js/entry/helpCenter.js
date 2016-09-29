let { combinePageModule } = require('modules/util');

require('modules/globalEffect').init();

combinePageModule({
	help: require('page/help'),
	information: require('page/information')
})
