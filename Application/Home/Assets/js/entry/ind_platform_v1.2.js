let { combinePageModule } = require('modules/util');
require('modules/globalEffect').init();

combinePageModule({
	ind_platform: require('page/platform/index')
});
