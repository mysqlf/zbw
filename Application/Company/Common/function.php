<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: rohochan <rohochan@gmail.com> <http://blog.rohochan.com>
// +----------------------------------------------------------------------

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean	 检测结果
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
 * @return integer	段落总数
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
 * @return string	  解析过的url
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





/**********智保易企业中心公共库**********/
/**
 * deal_inc 
 * 处理社保和公积金数据将json数据解析然后进行计算处理
 * @param  [array] $data [社保和公积金数组]
 * @return [array]       
 */
function deal_inc($data){
	if(false!==$data&&!empty($data)){
        //解析
        foreach ($data as $k => $v) {
            foreach ($v['inc'] as $key => $value) {
                $tmp=json_decode($value['insurance_detail'],true);
                $data[$k][$value['payment_type']]=$tmp;
                #$data[$k]['price']=$value[0]['price']+$value[1]['price'];
            }    
        }
        $count=0;
        //处理
        foreach ($data as $k => $v) {
            /*$tmp=true;
            foreach ($v['1']['items'] as $ks => $vs) {
                if ($vs['name']=='残障金') {
                   $data[$k]['disabled']=$vs['total'];
                   $tmp=false;
                }
            }
            if ($tmp) {
                foreach ($v['2'] as $ks => $vs) {
                    if ($vs['name']=='残障金') {
                       $data[$k]['disabled']=$vs['total'];
                    }
                }
            }*/
            $arr=array('1'=>'报增','2'=>'在保','3'=>'报减');
            #$data[$k]['pro_cost']=(isset($v['1']['pro_cost'])?$v['1']['pro_cost']:'0')+(isset($v['2']['pro_cost'])?$v['2']['pro_cost']:'0');//工本费
            $data[$k]['type']=$arr[$v['inc'][0]['type']].'/'.$arr[$v['inc'][1]['type']];
        
            $data[$k]['service_price']=$v['inc'][0]['service_price']+$v['inc'][1]['service_price'];
            $data[$k]['price']=$v['inc'][0]['price']+$v['inc'][1]['price']+$v['inc'][0]['service_price']+$v['inc'][1]['service_price'];
            $count=$data[$k]['price']+$count;
            $data[$k]['pay_date']=substr($v['pay_date'],0,4).'/'.substr($v['pay_date'],4,2);
            $data[$k]['location']=showAreaName($v['location']);
            $data[$k]['soc_company']=isset($v['1']['company'])?$v['1']['company']:'/';//社保
            $data[$k]['soc_person']=isset($v['1']['person'])?$v['1']['person']:'/';//社保
            $data[$k]['pro_company']=isset($v['2']['company'])?$v['2']['company']:'/';//公积金
            $data[$k]['pro_person']=isset($v['2']['person'])?$v['2']['person']:'/';//公积金
            $data[$k]['person']=(isset($v['1']['person'])?$v['1']['person']:'0')+(isset($v['2']['person'])?$v['2']['person']:'0');
            $data[$k]['company']=(isset($v['1']['company'])?$v['1']['company']:'0')+(isset($v['2']['company'])?$v['2']['company']:'0');
            unset($data[$k]['inc']);
        }
	}
	return $data;
}

/**
 * 根据比例字符串和比例值，计算是否合法比例
 * @param int $scale 比例
 * @param string $rule 规则
 * @return boolean $result 结果
 * @date 2016-08-03
 * @author RohoChan<[email]rohochan@gmail.com[/email]>
 */
function check_scale($scale,$rule = ''){
	$rule = get_scale($rule);
	$scale = floatval($scale);
	if (1 == $rule['type']) {
		//if (2 == count($rule['scale']) && ($scale >= reset($rule['scale']) && $scale <= end($rule['scale']))) {
		if (2 == count($rule['scale']) && ($scale >= $rule['scale'][0] && $scale <= $rule['scale'][1])) {
			$result = true;
		}else {
			$result = false;
		}
	}else if(2 == $rule['type']){
		if (in_array($scale,$rule['scale'])) {
			$result = true;
		}else {
			$result = false;
		}
	}else {
		$result = false;
	}
    return $result;
}

/**
 * 根据字符串获取比例规则
 * @param string $rule 规则
 * @return array $result 结果
 * @date 2016-08-03
 * @author RohoChan<[email]rohochan@gmail.com[/email]>
 */
function get_scale($rule = ''){
	$rule = str_replace('%','',$rule);
	if (strstr($rule,'-')) {
		$result = array('type'=>1,'scale'=>explode('-',$rule));
	}else if (strstr($rule,',')) {
		$result = array('type'=>2,'scale'=>explode(',',$rule));
	}else {
		$result = array('type'=>2,'scale'=>explode(',',$rule));
	}
    return $result;
}

/**
 * 根据数字索引获取Excel列名
 * @param int $columnNumber Excel列数字索引
 * @return string $columnName Excel列名
 * @date 2016-08-03
 * @author RohoChan<[email]rohochan@gmail.com[/email]>
 */
function get_excel_column_name($columnNumber){
    $dividend = $columnNumber;
    $columnName = '';
    while ($dividend > 0){
        $modulo = ($dividend - 1) % 26;
        $columnName = chr(65 + $modulo) . $columnName;
        $dividend = intval(($dividend - $modulo) / 26);
    }
    return $columnName;
}

/**
 * 根据Excel列名获取数字索引
 * @param string $columnName Excel列名
 * @return string $columnIndex Excel列数字索引
 * @date 2016-08-03
 * @author RohoChan<[email]rohochan@gmail.com[/email]>
 */
function get_excel_column_index($columnName){
	$columnIndex = 0;
    $strLength = strlen($columnName);
    for ($i=0; $i < $strLength ; $i++) { 
    	$columnIndex += (ord($columnName[$i])-64)*pow(26,$strLength-$i-1);
    }
    return $columnIndex;
}

/**
 * 数字转字母 （类似于Excel列标）
 * @param Int $index 索引值
 * @param Int $start 字母起始值
 * @return String 返回字母
 * @author Anyon Zou <Anyon@139.com>
 * @date 2013-08-15 20:18
 */
function IntToChr($index, $start = 65) {
    $str = '';
    if (floor($index / 26) > 0) {
        $str .= IntToChr(floor($index / 26)-1);
    }
    return $str . chr($index % 26 + $start);
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
	$page->setConfig('theme','<span class="page_num">共%TOTAL_PAGE%页 %HEADER%</span> <div class="page_btn">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%</div>');
	$page->setConfig('prev','上一页');
	$page->setConfig('next','下一页');
	$page->setConfig('last','末页');
	$page->setConfig('first','首页');
	$page->lastSuffix=false;
	$page->rollPage=5;
	//$show = $page->show();// 分页显示输出
	return $page;
}

/**
 * 检测企业用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author rohochan <rohochan@gmail.com>
 */
function is_company_login(){
	/*$user = session('user_auth');
	if (empty($user)) {
		return 0;
	} else {
		return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
	}*/
	
	//$company_id = session('company_id');
	$companyInfo = session('company_user');
	if (empty($companyInfo)) {
		return false;
	} else {
		return $companyInfo;
	}
}

/**
 * 检查$pos(推荐位的值)是否包含指定推荐位$contain
 * @param number $pos 推荐位的值
 * @param number $contain 指定推荐位
 * @return boolean true 包含 ， false 不包含
 * @author rohochan <rohochan@gmail.com>
 */
function check_position($pos = 0, $contain = 0){
	if(empty($pos) || empty($contain)){
		return false;
	}
	
	//将两个参数进行按位与运算，不为0则表示$contain属于$pos
	$res = $pos & $contain;
	if($res !== 0){
		return true;
	}else{
		return false;
	}
}

/**
 * 获取对应状态的文字信息
 * @param int $status
 * @return string 状态文字 ，false 未获取到
 * @author rohochan <rohochan@gmail.com>
 */
function get_order_per_status_value($status = null){
	if(!isset($status)){
		return false;
	}
	switch ($status){
		//审核状态
		case -1 : return	'审核失败';	break;
		case 0  : return	'审核中';	break;
		case 1  : return	'审核成功';	break;
		case 2  : return	'办理失败';	break;
		case 3  : return	'办理成功';	break;
		case -4 : return	'缴纳失败';	break;
		case 4  : return	'缴纳成功';	break;
		case -9 : return	'撤销';		break;
		default : return	false;		break;
	}
}

/**
 * 获取对应状态的文字信息
 * @param int $status
 * @param string $type 
 * @param int $detailState 是否生效 -1否 0是
 * @return string 状态文字 ，false 未获取到
 * @author rohochan <rohochan@gmail.com>
 */
function get_status_value($status = null,$type = null,$detailState = 0){
	if(!isset($status) || !isset($type)){
		return false;
	}
	if ('ServiceOrderDetailState' == $type) {
		//服务订单明细审核状态
		if (-1 == $detailState) {
			 return '挂起';
		}else {
			switch ($status){
				case -1 : return	'审核失败';	break;
				case  0 : return	'审核中';	break;
				case  1 : return	'审核成功';	break;
				case  2 : return	'调整通过';	break;
				case -3 : return	'办理失败';	break;
				case  3 : return	'办理成功';	break;
				case -4 : return	'缴纳失败';	break;
				case  4 : return	'缴纳成功';	break;
				case -5 : return	'缴纳异常';	break;
				case -9 : return	'已撤销';	break;
				default : return	false;		break;
			}
		}
	}else if ('ServiceOrderDetailType' == $type) {
		//服务订单明细在保状态
		switch ($status){
			//在保状态 1报增 2报减 3在保
			case 1  : return	'报增';	break;
			case 2  : return	'报减';	break;
			case 3  : return	'在保';	break;
			default : return	false;	break;
		}
	}else if ('ServiceOrderDetailPaymentType' == $type) {
		//服务订单明细参保类型 
		switch ($status){
			case 1  : return	'社保';	break;
			case 2  : return	'公积金';	break;
			case 3  : return	'残障金';	break;
			case 4  : return	'其他金额';	break;
			default : return	false;	break;
		}
	}else if ('ServiceOrderSalaryState' == $type) {
		//工资服务订单明细审核状态
		if ((-1 == $detailState)) {
			return	'挂起';
		}else {
			switch ($status){
				case -2 : return	'发放失败';	break;
				case -1 : return	'审核失败';	break;
				case  0 : return	'待审核';	break;
				case  1 : return	'审核成功';	break;
				case  2 : return	'发放成功';	break;
				case -9 : return	'已撤销';	break;
				default : return	false;		break;
			}
		}
	}else if ('ServiceBillState' == $type) {
		//账单状态
		switch ($status){
			case  0  : return	'未支付';	break;
			case  1  : return	'已支付';	break;
			case -1  : return	'账单失败';	break;
			case  2  : return	'确认支付';	break;
			default : return	false;	break;
		}
	}else if ('PersonBaseState' == $type) {
		//个人参保状态
		switch ($status){
			//在保状态 1报增 2报减 3在保
			case 0  : return	'停保';	break;
			case 1  : return	'报增';	break;
			case 2  : return	'报减';	break;
			case 3  : return	'在保';	break;
			default : return	false;	break;
		}
	}else if ('ProductOrderState' == $type) {
		//产品服务订单状态
		switch ($status){
			case -9 : return	'已删除';	break;
			case -2 : return	'已撤销';	break;
			case -1 : return	'支付失败';	break;
			case  0 : return	'未支付';	break;
			case  1 : return	'已支付';	break;
			case  2 : return	'确认付款';	break;
			default : return	false;	break;
		}
	}else if ('ProductOrderServiceState' == $type) {
		//产品服务订单状态
		switch ($status){
			case -1 : return	'停止服务';	break;
			case  0 : return	'未签约';	break;
			case  1 : return	'已签约';	break;
			case  2 : return	'服务中';	break;
			case  3 : return	'服务结束';	break;
			default : return	false;	break;
		}
	}else {
		return	false;
	}
}

/**
 * 生成验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function create_verify($config = array(), $id = 1){
	$verify = new \Think\Verify($config);
	$verify->entry($id);
}

/**
 * @param $m 模型对象
 * @param $where 查询条件
 * @param int $pageSize 分页条数
 * @return \Think\Page 分页对象
 */
function getPage(&$m,$where,$pageSize=10){
	$m1=clone $m;//浅复制一个模型
	$count = $m->where($where)->count();//连惯操作后会对join等操作进行重置
	$m=$m1;//为保持在为定的连惯操作，浅复制一个模型
	$p=new \Think\Page($count,$pageSize);
	$p->lastSuffix=false;
	$p->rollPage=5;
	$p->setConfig('header','共%TOTAL_PAGE%页  共%TOTAL_ROW%条记录');
	$p->setConfig('prev','上一页');
	$p->setConfig('next','下一页');
	$p->setConfig('last','末页');
	$p->setConfig('first','首页');
	$p->setConfig('theme','<span class="fr">%HEADER%</span> <div class="page fr">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </div>');
	$m->limit($p->firstRow,$p->listRows);
	return $p;
}