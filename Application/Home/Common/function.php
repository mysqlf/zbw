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
function filter_value($str){
	$str=strip_tags($str);
	$str=substr($str,0,40);
	$str=cleanHex($str);
	return $str;
}

function cleanHex($input){
    $clean = preg_replace("![\][xX]([A-Fa-f0-9]{1,3})!", "",$input);
    return $clean;
}
/**
 * merge_array function
 * 合并数组
 * param array $foo
 * param array $bar
 * @return mixed
 * @author rohochan
 **/
function merge_array($foo = [], $bar = []){
	if (is_array($foo) || is_array($bar)) {
		foreach ($foo as $key => $value) {
			$result[] = $value;
		}
		foreach ($bar as $key => $value) {
			$result[] = $value;
		}
		return $result;
	}else {
		return false;
	}
}

function _substrCut($user_name){
	$strLength  = mb_strlen($user_name, 'utf-8');
	$start = ceil($strLength/2)-1;
	$end = $start+3;
	$firstStr   = mb_substr($user_name, 0,$start,'utf-8');
	$lastStr    = mb_substr($user_name, $end, $strLength,'utf-8');
	return $firstStr.'***'.$lastStr;
}

/**
 * 获取用户所在城市   
 * @Author   JieJie
 * @DataTime 2016-03-17T14:53:23+0800
 * @return   string
 */
function getAddress(){
	$ip = get_client_ip();
	$url = 'http://api.map.baidu.com/location/ip?ak=RvufqHb1h9WY4qwhBmGWs2Wv&ip='. $ip.'&coor=bd09ll';
	$result = get($url);
	$address = explode('|', $result['address']);
	return $address[2];
}

//获取最新咨询列表
function getCateList($flag,$limit,$map='',$field='*'){
	$category = D('Category')->info($flag);
	if($category['display'] == 0) return false; //该分类禁止显示
	$Document = D('Document');
	$limit  = isset($limit) ? $limit : $category['list_row'];
	$list = $Document->page(1, $limit)->where($map)->lists($category['id'],'`id` DESC',1,$field);
	foreach ($list as $key => $value) 
	{
		$list[$key]['create_time'] = date("Y-m-d",$value['create_time']);
	}
	return $list;
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = ''){
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


/**  * 系统    /**
 * 通用调用API接口函数
 * @param string $url   API接口的地址
 */
function get($url)
{
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
	$res = curl_exec ( $ch);
	curl_close ( $ch);
	$res = json_decode ( $res, true );
	return $res;
}

/**
 * [is_active 获取链接是否为当前页]
 * @param  [type]  $controller_name [控制器名]
 * @param  [type]  $action_name     [操作名]
 * @return boolean                  [description]
 */
function is_active($controller_name,$action_name)
{
	$controller = strtolower(CONTROLLER_NAME);
	$action = strtolower(ACTION_NAME);
	return $controller == $controller_name && $action==$action_name;
}

/**
 * [channel_list 获取服务商频道及文章]
 * @param  integer $limit         [频道记录数]
 * @param  [string]  $field         [文章字段]
 * @param  integer $article_limit [文章列表数]
 * @return [type]                 [description]
 */
function channel_list($cid,$limit=3,$field=null,$article_limit = 5)
{
	//获取分类
	$channel = M('service_article_category','zbw_')->where('company_id='.$cid)->limit($limit)->order('update_time DESC')->select();
	$map['company_id'] = $cid;//session('cid');
	$map['status'] = 1;
	$Aritcle = M('service_article','zbw_');
	$field = $field ? $field : 'id,title,category_id,update_time';
	//获取文章
	foreach ($channel as $key => $value) 
	{
		$map['category_id'] = $value['id'];
		$channel[$key]['article_list'] = $Aritcle->where($map)->field($field)->order('update_time DESC')->limit($article_limit)->select();
	}
	return $channel;
}

function artitcle_category($category_id){

	$result = M('service_article_category', 'zbw_')->field('title')->where(array('id'=> $category_id, 'status'=> 1))->find();
	return $result['title'];
}

/**
 * 搜索URL拼接
 */
function url_splite($flag){
	$quer_str = $_SERVER['QUERY_STRING'];
	$url = '?';

	if(empty($quer_str))
	{
		return $url = '?';
	}

	$str = explode('&', $quer_str);
	foreach ($str as $key => $value) {
		if(strpos($value, $flag) === false){
			$url .= $value.'&';
		}
	}


	return $url;
}