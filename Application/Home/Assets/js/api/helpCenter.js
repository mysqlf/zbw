/*帮助中心*/
let { concatApi } = require('./config'),
api = {
	//获取资讯列表数据
	getInformationList:'/Article-lists',

}
// 通行证接口
module.exports = concatApi(api);