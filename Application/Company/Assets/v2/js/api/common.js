
let { concatApi } = require('./config'),
	api = {
		//头部消息数量
		msgCount: '/Company-Information-msgCount'
	}

module.exports = concatApi(api);