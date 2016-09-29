// 套餐管理

let { concatApi } = require('./config'),
	api = {
		// 删除套餐
		delProduct: '/Service-Product-delProduct',
		// 删除增值服务
		delService: '/Service-Product-delService',
		// 添加增值服务
		addProductService: '/Service-Product-serviceDetail',
		// 编辑增值服务
		editProductService: '/Service-Product-serviceInformation',
		// 保存企业详情
		saveProductDetail: '/Service-Product-productDetail'
	}

// 用户信息动作
module.exports = concatApi(api);