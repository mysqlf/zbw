let { concatApi } = require('./config'),
    api = {
        // 获取客服列表
        getServiceList: '/Service-Members-cServideList',
        // 设定客服
        setService: '/Service-Members-setService',
        // 企业服务详情 （服务状态）
        comSetService: '/Service-Members-comSetService',
        // 企业服务详情 （服务城市）
        comAddLocation: '/Service-Members-comAddLocation',
        // 服务城市
        wLocation: '/Service-Members-wLocation',
        // 企业服务
        selectProduct: '/Service-Customer-selectProduct',
        // 服务状态设定 （添加切换合同）
        conSetService: '/Service-Customer-setService',
        // 切换合同保存
        addContractChange: '/Service-Customer-addContractChange',
        // 企业客户导出
        comMembersList: '/Service-Members-comMembersList',
        // 删除企业服务详情列表
        deleteLocation: '/Service-Customer-deleteLocation',
        // 企业服务详情保存
        editProductOrder: '/Service-Customer-editProductOrder',
        // 切换合同是否过期
        contractIsTurn: '/Service-Customer-isTurnID'
    }

// 用户信息动作
module.exports = concatApi(api);
