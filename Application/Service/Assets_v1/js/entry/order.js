if(process.env.NODE_ENV !== 'production'){
	// http://mockjs.com/
	// 只有不是开发环境 才使用 mock模拟ajax
	//require('mockData/index.js');
}


require('plug/bootstrap/index.js');
require('plug/placeholder.js');

var initFn = $('script').eq(-1).data('init');

var pages = {
	payroll: require('page/payroll.js'),
	addAudit: require('page/addAudit.js')
}

if(pages[initFn]){
	pages[initFn].init();
}

