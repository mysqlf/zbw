
let { concatApi } = require('./config'),
	api = {
		// 删除文章
		removeArticle: '/Service-Article-changeStatus',
		// 保存企业详情 和文章
		uplateArticle: '/Service-Article-update'
	}

// 用户信息动作
module.exports = concatApi(api);