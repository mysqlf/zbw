/*首页接口*/
let { concatApi } = require('./config'),
    api = {
        //首页查询工具请求
        changeData: '/Index-changeData',
        //首页最新资讯
        getArticleList: '/Index-getArticleList',
        //详情页立即支付
        payFor: '/SocialSecurity-productDetail'
    }
    // 通行证接口
module.exports = concatApi(api);
