// 内容管理
let { combinePageModule } = require('modules/util');

combinePageModule({
	companyInfo: require('page/company_info'),
	articleList: require('page/article_list'),
	updateArticle: require('page/arcticle_update'),
	adImg: require('page/ad_img')
})
