
/*全局接口*/
let { concatApi } = require('./config'),
api = {
	//获取头部消息数量
	getMsgCount:'/Company-Information-msgCount',

}
// 通行证接口
module.exports = concatApi(api);