if(process.env.NODE_ENV !== 'production'){
	// http://mockjs.com/
	// 只有不是开发环境 才使用 mock模拟ajax
	//require('mockData/index.js');
}


require('plug/bootstrap/index.js');
require('plug/placeholder.js');

var initFn = $('script').eq(-1).data('init');

var pages = {
	comProductDetail: require('page/comProductDetail.js'),
	productManage: require('page/productManage.js'),
	productManageService: require('page/productManageService.js')
}

if(pages[initFn]){
	pages[initFn].init();
}

