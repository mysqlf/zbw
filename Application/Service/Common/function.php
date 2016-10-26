<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */
/*
 * ajax返回数据类型
 */
function ajaxJson ($status , $msg , $data = array())
{
    // $info = array();
    // $info['status'] = $status;
    // $info['msg']    = $msg;
    // $info['data']   = $data;
    // return $info;
    die(json_encode(array('status'=>$status , 'msg'=>$msg , 'data'=>$data)));
}

/**
  * get_page
  * 获取分页数据
  * @access default
  * @param string $pageCount 分页总记录数
  * @param string $pageSize 分页大小，默认10
  * @return object
  * @date 2016-06-30
  * @author RohoChan<[email]rohochan@gmail.com[/email]>
  **/
function get_page($pageCount = 0,$pageSize = 10){
	$page = new \Think\Page($pageCount,$pageSize);// 实例化分页类 传入总记录数和每页显示的记录数(10)
	$page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% 共%TOTAL_PAGE%页 %HEADER%');
	$page->setConfig('prev','上一页');
	$page->setConfig('next','下一页');
	$page->setConfig('last','末页');
	$page->setConfig('first','首页');
	$page->lastSuffix=false;
	$page->rollPage=5;
	//$show = $page->show();// 分页显示输出
	return $page;
}

/*
 * 分页函数
 */
//function showpage($count,$num)
//{
//    $Page = new \Think\Page($count,$num);
//    $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
//    return $Page->show();
//}

function redirecterror()
{
    redirect('Service-Relate-error');
}

function getModeleName()
{
    list($module,$action) = explode('-',$_SERVER['PATH_INFO']);
    return $module;
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function isloginstate(){
    $user = session('AccountInfo');
    if (!empty($user))
    {
        if(ACTION_NAME == 'login')
        {
            redirect('Service-Service-index');
        }
        if($user['type'] != 1)
        {
            $authState = 0;
            $user['auth'] = json_decode($user['auth']);
            list($module,$action) = explode('-',$_SERVER['PATH_INFO']);
            foreach ($user['auth'] as $k=>$v)
            {
                if($module == $v)
                {
                    $authState = 1;
                }
            }
            if($authState == 0)
            {
                if(IS_POST)
                {
                    header('Content-Type:application/json; charset=utf-8');
                    exit(json_encode(array('status'=>-100001)));
                }
                redirecterror();
            }
        }
        if($user['state'] == 1)
        {
            return $user;
        }
    }
    else
    {
        if( ACTION_NAME !== 'login' )
        {
            if(IS_POST)
            {
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode(array('status'=>-100001)));
            }
            redirect('Service-User-login');
        }
    }
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/**
 * 获取列表总行数
 * @param  string  $category 分类ID
 * @param  integer $status   数据状态
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_list_count($category, $status = 1){
    static $count;
    if(!isset($count[$category])){
        $count[$category] = D('Document')->listCount($category, $status);
    }
    return $count[$category];
}

/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_part_count($id){
    static $count;
    if(!isset($count[$id])){
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_nav_url($url){
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;        
        default:
            $url = U($url);
            break;
    }
    return $url;
}

function getMonthNum( $date1, $date2, $tags='-' ){
    // if(!$date1) $date1 = date('Y-m-d', NOW_THME);
     $date1 = explode($tags,$date1);
     $date2 = explode($tags,$date2);
     return abs($date1[0] - $date2[0]) * 12 + abs($date1[1] - $date2[1]);
}

function getCreateTime(){
    return date('Y-m-d H:i:s', NOW_TIME);
}

    /**
     * 客服名称
     */
    function serviceAdminName($admin_id){
        return M('service_admin')->getFieldById($admin_id,  'name');
    }

    /**
     * 参保地数量
     */
    
    /**
     * 金额格式化
     */
    // function moneyNumberformat($str){
    //     if(empty($str)) return '/';
    //     return number_format(floatval($str), 2);
    // }

    /**
     * 底部企业信息
     */
    function publicFooter($cid){
        $info = S('com'.$cid);
        if(empty($info)){
             $result = M('company_info')->alias('ci')->field('company_address,tel_city_code,tel_local_number, contact_phone')->where(array('id'=> $cid))->find();

             S('com'.$cid, json_encode($result), 24*3600);
        }else{
            return json_decode($info, true);
        }
    }

    /**
     * uid 取 service_admin id
     */
     function getServiceAdminId($uid){
        return M('service_admin')->getFieldByUser_id($uid, 'id');

    }