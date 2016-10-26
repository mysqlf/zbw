<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

/**
 * 系统调试设置
 * 项目正式部署后请设置为false
 */
define('APP_DEBUG', false);

/**
 * 应用目录设置
 * 安全期间，建议安装调试完成后移动到非WEB目录
 */ 
define ( 'APP_PATH', './Application/' );

if(!is_file(APP_PATH . 'User/Conf/config.php')){
	header('Location: ./install.php');
	exit;
}
/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define ( 'RUNTIME_PATH', './Runtime/' );


/**
 * 域名设置
 * 项目正式部署后设置为正式域名
 */
//项目测试域名
define('APP_DOMAIN','http://www.zhibaoyi.com/');
//项目正式域名
//define('APP_DOMAIN','');

//智通通行证接口测试域名
//define('ACCOUNT_DOMAIN','pt.chitone.cc/');
//define('ACCOUNT_DOMAIN','test2.hr5156.com/');
//智通通行证接口正式域名
//define('ACCOUNT_DOMAIN','weixin.hr5156.com/');

//微信接口测试域名
//define('APP_URL','test.job5156.com/');
//微信接口正式域名
//define('APP_URL','weixin.job5156.com/');

//短信接口测试域名
//define('SMS_DOMAIN','http://test.job5156.com/');
//短信接口正式域名
define('SMS_DOMAIN','http://api.job5156.com/');

/**
 * 引入核心入口
 * ThinkPHP亦可移动到WEB以外的目录
 */


function filter_vars(&$value)
{
    $value = preg_replace("/(javascript:)?on(click|load|key|mouse|error|abort|move|unload|change|dblclick|move|reset|resize|submit)/i","",$value);
    $value = preg_replace("/(.*?)<\/script>/si","",$value);
    $value = preg_replace("/(.*?)<\/iframe>/si","",$value);
    $value = preg_replace ("//iesU", '', $value);
    if (!get_magic_quotes_gpc())
    {
        $value = addslashes($value);
    }
}
require '../ThinkPHPOT/ThinkPHP.php';