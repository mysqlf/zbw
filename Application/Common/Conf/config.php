<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array(
    /* 模块相关配置 */
    'AUTOLOAD_NAMESPACE' => array('Addons' => ONETHINK_ADDON_PATH), //扩展模块列表
    'MODULE_DENY_LIST'   => array('Common','User','Install'),
    'MODULE_ALLOW_LIST'  => array('Home','Service','Company','Admin','Cron'),

    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => '`WV^,<0wTC=lZXdr)&[k9L.m2*I}_KFp4h|BoPYi', //默认数据加密KEY

    /* 用户相关设置 */
    'USER_MAX_CACHE'     => 1000, //最大缓存用户数
    'USER_ADMINISTRATOR' => 1, //管理员用户ID

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => false, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 2, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '-', //PATHINFO URL分割符
    'VAR_FILTERS' => 'filter_vars', //过滤用户传递的参数（$_GET,$_POST）
    'DEFAULT_FILTER' => 'htmlspecialchars,trim',//全局传递参数过滤

    /* 全局过滤配置 */

    /* 图片上传相关配置 */
    'IMG_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 2*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Company/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => true, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ),
    /* 数据库配置 */
 /*   'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => '192.168.70.212', // 服务器地址
    'DB_NAME'   => 'zbw0824', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'root',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'onethink_', // 数据库表前缀*/

    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => '192.168.70.167', // 服务器地址
    'DB_NAME'   => 'zby', // 数据库名
    'DB_USER'   => 'zbytest', // 用户名
    'DB_PWD'    => 'ji3Tgy3fy',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'onethink_', // 数据库表前缀

    'THINK_EMAIL' => array(
        'SMTP_HOST'   => '192.168.2.151', //SMTP服务器
        'SMTP_PORT'   => 25, //SMTP服务器端口
        'SMTP_USER'   => 'messages-noreply@mx.job5156.com', //SMTP服务器用户名
        'SMTP_PASS'   => '5-d_QKQiy787ZHRQr2nZpN7kw', //SMTP服务器密码
        'FROM_EMAIL'  => 'messages-noreply@mx.job5156.com', //发件人EMAIL
        'FROM_NAME'   => '智保网', //发件人名称
        'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
        'REPLY_NAME'  => '', //回复名称（留空则为发件人名称）
    ),

//    'STATUS_DETAIL' => array (
//        'bill'   => array(-2=>'支付过期',-1=>'账单失败',0=>'未支付','已支付','确认支付'),
//        'sorder' => array(),
//        'warranty'=> array(1=>'报增','报减','在保')
//    ),
    'REDIS_CONNECT'   => array (
        'port' => '6379',
        //'ip'   => '192.168.8.197',
        'ip'   => '192.168.70.182',
        'auth' => 'job5156RedisMaster182',
        //'ip'   => '192.168.2.183',
        //'auth' => 'job5156RedisMaster183',
    ),
    /* 文档模型配置 (文档模型核心配置，请勿更改) */
    'DOCUMENT_MODEL_TYPE' => array(2 => '主题', 1 => '目录', 3 => '段落'),
    
    'WEB_SITE_PATH' => './Uploads/WebSite/',
    
    /*企业行业分类*/
    'INDUSTRY' => array(1=>'互联网/电子商务' , '计算机软件' , '计算机硬件' , '电子/微电子技术/集成电路' , '通讯/电信业' , '快速消费品' , '服装/纺织/皮革' , '金融业（银行、保险、证券、投资、基金）' , '家具/家电/玩具/礼品' , '贸易/商务/进出口' , '生产/制造/加工' , '房地产/建筑/建材/工程' , '钢铁/机械/设备/重工' , '交通/运输/物流•快递' , '广告/创意/设计' , '批发/零售（超市、百货、商场、专卖店）' , '汽车/摩托车及零配件' , '仪器仪表/电工设备/工业自动化' , '医药/生物工程' , '餐饮/酒店/旅游' , '橡胶/塑胶/五金' , '印刷/包装/造纸' , '电力/电气/水利' , '石油/化工/地质' , '办公设备/文体休闲用品/家居用品' , '法律/法务' , '法律/法务' , '艺术/文体' , '娱乐/体育/休闲' , '教育/培训/科研院所' , '咨询与调查业（顾问/企业管理/知识产权）' , '咨询与调查业（顾问/企业管理/知识产权）' , '咨询与调查业（顾问/企业管理/知识产政府/公用事业/社区服务' , '农、林、牧、副、渔业' , '协会/社团/非营利机构' , 38=>'IT服务（系统/数据/维护）' , '网络游戏' , '珠宝/首饰/钟表' , '会计/审计' , '信托/担保/拍卖/典当' , '奢侈品/收藏品/工艺品' , '物业管理/商业中心' , '外包服务' , '人力资源服务' , '检测/认证' , '租赁服务' , 50=>'环保' , '航天/航空' , '多元化业务集团' , '家居/室内设计/装潢' , '公关/市场推广/会展' , '能源/矿产/采掘/冶炼' , 37=>'其他'),
    
    /*企业性质*/
    'PROPERTY' => array(1=>'外资企业', 2=>'中外合营（合资、合作）', 3=>'台资企业', 4=>'港资企业', 5=>'私营·民营企业', 6=>'股份制企业', 7=>'跨国公司（集团）', 8=>'国有企业', 9=>'事业单位', 10=>'社会团体', 11=>'政府机关', 20=>'其他' ),
    
    /*企业信息图片名称*/
    'COMPANY_INFO_FILE_NAME' => array(
        1=>'business_license',2=>'tax_cegistration_certificate',3=>'taxpayer_qualification_certificate',4=>'account_opening_license'
    ),
    
    /*参保办理天数*/
    'INSURANCE_HANDLE_DAYS' => 3
);
