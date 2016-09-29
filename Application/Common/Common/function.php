<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

// OneThink常量定义
const ONETHINK_VERSION    = '1.1.141101';
const ONETHINK_ADDON_PATH = './Addons/';


/**
 * 获取编码对应的文字信息
 * @param int $code
 * @param string $type 
 * @param int $extra 额外判断
 * @return string 状态文字 ，false 未获取到
 * @date 2016-07-20
 * @author RohoChan<[email]rohochan@gmail.com[/email]>
 */
function get_code_value($code = null,$type = null,$extra = 0){
	if(!isset($code) || !isset($type)){
		//return false;
		return '';
	}
	$value = '';
	switch($type){
		case 'ServiceOrderSalaryState';
			//个人参保审信息核状态
			switch ($code){
				case  0 : $value = '未审核';	break;
				case  1 : $value = '审核成功';	break;
				case -1 : $value = '审核失败';	break;
				case  2 : $value = '支付成功';	break;
				case -2 : $value = '支付失败';	break;
				case  3 : $value = '发放成功';	break;
				case -3 : $value = '发放失败';	break;
				case -8 : $value = '作废';		break;
				case -9 : $value = '已撤销';	break;
				default : $value = '';			break;
			}
			break;
		case 'PersonInsuranceInfoOperateState';
			//个人参保审信息核状态
			switch ($code){
				/*case  0 : $value = '待支付';	break;
				case  1 : $value = '支付成功';	break;
				case -1 : $value = '支付失败';	break;
				case  2 : $value = '办理成功';	break;
				case -2 : $value = '办理失败';	break;
				case  3 : $value = '缴纳成功';	break;
				case -3 : $value = '缴纳失败';	break;
				case -8 : $value = '作废';		break;
				case -9 : $value = '已撤销';	break;
				default : $value = '';			break;*/
				case  0 : $value = '未审核';	break;
				case  1 : $value = '审核成功';	break;
				case -1 : $value = '审核失败';	break;
				case  2 : $value = '支付成功';	break;
				case -2 : $value = '支付失败';	break;
				case  3 : $value = '办理完成';	break;
				case -8 : $value = '作废';		break;
				case -9 : $value = '已撤销';	break;
				default : $value = '';			break;
			}
			break;
		case 'ServiceInsuranceDetailState';
			//服务订单明细审核状态
			if (-1 == $extra) {
				 $value = '挂起';
			}else {
				switch ($code){
					/*case  0 : $value = '待支付';	break;
					case  1 : $value = '支付成功';	break;
					case -1 : $value = '支付失败';	break;
					case  2 : $value = '办理成功';	break;
					case -2 : $value = '办理失败';	break;
					case  3 : $value = '缴纳成功';	break;
					case -3 : $value = '缴纳失败';	break;
					case -8 : $value = '作废';		break;
					case -9 : $value = '已撤销';	break;
					default : $value = '';			break;*/
					
					case  0 : $value = '未审核';	break;
					case  1 : $value = '审核成功';	break;
					case -1 : $value = '审核失败';	break;
					case  2 : $value = '支付成功';	break;
					case -2 : $value = '支付失败';	break;
					case  3 : $value = '办理成功';	break;
					case -3 : $value = '办理失败';	break;
					case -4 : $value = '缴费异常';	break;
					case -8 : $value = '作废';		break;
					case -9 : $value = '已撤销';	break;
					default : $value = '';			break;
				}
			}
			break;
		case 'ServiceInsuranceDetailType';
			//个人参保状态
			switch ($code){
				case  1 : $value = '报增';		break;
				case  2 : $value = '在保';		break;
				case  3 : $value = '报减';		break;
				default : $value = '';			break;
			}
			break;
		case 'PersonInsuranceState';
			//个人参保状态
			switch ($code){
				case  0 : $value = '未参保';	break;
				case  1 : $value = '报增';		break;
				case  2 : $value = '在保';		break;
				case  3 : $value = '报减';		break;
				case  4 : $value = '停保';		break;
				default : $value = '';			break;
			}
			break;
		case 'PersonBaseResidenceType';
			//个人户口性质
			switch ($code){
				case  1 : $value = '农村';		break;
				case  2 : $value = '城镇';		break;
				default : $value = '';			break;
			}
			break;
		case 'PersonBaseGender';
			//个人性别
			switch ($code){
				case  1 : $value = '男';		break;
				case  2 : $value = '女';		break;
				default : $value = '';			break;
			}
			break;
		case 'InsuranceType';
			//参保类型
			switch ($code){
				case  1 : $value = '社保';		break;
				case  2 : $value = '公积金';	break;
				default : $value = '';			break;
			}
			break;
		default:
			break;
	}
	return $value;
}
/**
 * 根据报增减截止日计算办理年月
 * @param string $deadline 报增减截止日
 * @param string $separator 时间分隔符,默认为空
 * @return string
 * @date 2016-09-26
 * @author RohoChan<[email]rohochan@gmail.com[/email]>
 */
function get_handle_month($deadline,$separator='') {
	return date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($deadline,2,'0',STR_PAD_LEFT)))))?date('Y'.$separator.'m',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Y'.$separator.'m',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
}

/**
 * 根据UA设定文件名header
 * @param string $ua 浏览器用户代理
 * @param string $filename 文件名
 * @param string $application 文件类型
 * @return void
 * @date 2016-09-26
 * @author RohoChan<[email]rohochan@gmail.com[/email]>
 */
function set_filename_header($ua = '', $filename = '', $application='vnd.ms-execl') {
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
	header("Content-Type:application/force-download");
	header("Content-Type:application/octet-stream");
	header("Content-Type:application/download");
	//header("Content-Type:application/vnd.ms-execl");
	header("Content-Type:application/".$application);
	header("Content-Transfer-Encoding:binary");
    if (preg_match("/MSIE/", $ua)) {
    	//$fileName=iconv('utf-8', 'gb2312', $fileName);
        $filename = urlencode($filename);
        $filename = str_replace("+", "%20", $filename);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
    } else if (preg_match("/Firefox/", $ua)) {  
        header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
    } else {  
        header('Content-Disposition: attachment; filename="' . $filename . '"');
    }
}

/**
 * 获取地区编码对应的值
 * @param string $location 地区编码
 * @return array $value 结果
 * @date 2016-09-07
 * @author RohoChan<[email]rohochan@gmail.com[/email]>
 */
function get_location_value($code) {
	$cityCode = ($code/1000<<0)*1000;
	$value = showAreaName($cityCode);
	if ($code != ($cityCode)) {
		$location = D('Location');
		$locationResult = $location->field('id,name,level')->where(array('id'=>$code,'state'=>1))->find();
		if ($locationResult['name']) {
			$value .= '-'.$locationResult['name'];
		}
	}
	return $value;
}

/**
 * url安全的base64编码
 * @param string $str 数据
 * @return array $data 结果
 * @date 2016-08-30
 * @author RohoChan<[email]rohochan@gmail.com[/email]>
 */
function urlsafe_base64encode($str) {
	$data = base64_encode($str);
	$data = str_replace(array('+','/','='),array('-','_',''),$data);
	return $data;
}

/**
 * url安全的base64解码
 * @param string $str 数据
 * @return array $data 结果
 * @date 2016-08-30
 * @author RohoChan<[email]rohochan@gmail.com[/email]>
 */
function urlsafe_base64decode($str) {
	$data = str_replace(array('-','_'),array('+','/'),$str);
	$mod4 = strlen($data) % 4;
	if ($mod4) {
	   $data .= substr('====', $mod4);
	}
	return base64_decode($data);
}

/**
 * 比较两个字符串,去除重复数据
 * @param string $data1 数据1
 * @param string $data2 数据2
 * @return array $result 结果
 * @date 2016-08-23
 * @author RohoChan<[email]rohochan@gmail.com[/email]>
 */
function str_unique($data1,$data2,$prefix=','){
	if ($data2) {
		$data1 = explode($prefix,$data1);
		$data2 = explode($prefix,$data2);
		$result = array();
		for ($i=1; $i <= 2; $i++) { 
			$name = 'data'.$i;
			foreach ($$name as $key => $value) {
				$result['key'][$value] = $value;
			}
		}
		if ($result) {
			foreach ($result['key'] as $key => $value) {
				$result['value'][$key] = date('Y年m月',strtotime(int_to_date($value,'-')));
			}
		}
	}else {
		$result = explode($prefix,$data1);
	}
	return $result;
}

/**
  * string_to_number
  * 字符串转换为数字
  * @access default
  * @param string $str 字符串
  * @return int
  * @date 2016-07-19
  * @author RohoChan<[email]rohochan@gmail.com[/email]>
  **/
function string_to_number($str){
	return preg_replace('/\D/s', '', $str);
}

/**
 * 获取编码对应的文字信息
 * @param int $residenceLocation 户口所在地
 * @param int $insuranceLocation 参保地
 * @return string 状态文字 ，false 未获取到
 * @date 2016-07-20
 * @author RohoChan<[email]rohochan@gmail.com[/email]>
 */
function get_residence_type($residenceLocation,$insuranceLocation){
	if ($residenceLocation && $insuranceLocation) {
		if ($residenceLocation == $insuranceLocation) {
			return '本地';
		}else {
			return '外地';
		}
	}else {
		return false;
	}
}

//验证身份证是否有效
function validateIDCard($IDCard) {
	if (strlen($IDCard) == 18) {
		return check18IDCard($IDCard);
	} elseif ((strlen($IDCard) == 15)) {
		$IDCard = convertIDCard15to18($IDCard);
		return check18IDCard($IDCard);
	} else {
		return false;
	}
}

//计算身份证的最后一位验证码,根据国家标准GB 11643-1999
function calcIDCardCode($IDCardBody) {
	if (strlen($IDCardBody) != 17) {
		return false;
	}
	
	//加权因子 
	$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
	//校验码对应值 
	$code = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
	$checksum = 0;
	
	for ($i = 0; $i < strlen($IDCardBody); $i++) {
		$checksum += substr($IDCardBody, $i, 1) * $factor[$i];
	}
	
	return $code[$checksum % 11];
}

// 将15位身份证升级到18位 
function convertIDCard15to18($IDCard) {
	if (strlen($IDCard) != 15) {
		return false;
	} else {
		// 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码 
		if (array_search(substr($IDCard, 12, 3), array('996', '997', '998', '999')) !== false) {
			$IDCard = substr($IDCard, 0, 6) . '18' . substr($IDCard, 6, 9);
		} else {
			$IDCard = substr($IDCard, 0, 6) . '19' . substr($IDCard, 6, 9);
		}
	}
	$IDCard = $IDCard . calcIDCardCode($IDCard);
	return $IDCard;
}

// 18位身份证校验码有效性检查 
function check18IDCard($IDCard) {
	if (strlen($IDCard) != 18) {
		return false;
	}
	
	$IDCardBody = substr($IDCard, 0, 17); //身份证主体
	$IDCardCode = strtoupper(substr($IDCard, 17, 1)); //身份证最后一位的验证码
	
	if (calcIDCardCode($IDCardBody) != $IDCardCode) {
		return false;
	} else {
		return true;
	}
}

/**
 * 根据身份证获取年龄
 * @param int $idCard 身份证
 * @return string 年龄 ，false 未获取到
 * @author rohochan <rohochan@gmail.com>
 */
function get_age_by_idCard($idCard = null){
	if(!isset($idCard)){
		return false;
	}else {
		if (validateIDCard($idCard)) {
			$year =substr($idCard,6,4);
			$month =substr($idCard,10,2);
			$day =substr($idCard,12,2);
			$birthday = getdate(strtotime($year.'-'.$month.'-'.$day));
			$now = getdate();
			
			//未过生日(默认)
			$factor = -1;
			//已过生日
			if($now['mon']>$birthday['mon']){
				$factor=0;
			}else if($now['mon'] == $birthday['mon'] && $now['mday'] >= $birthday['mday']){
				$factor=0;
			}
			$age = $now['year'] - $birthday['year'] + $factor;
			//$age = floor((time()-strtotime($year.'-'.$month.'-'.$day))/(365.25*24*3600));
			return $age;
		}else {
			return false;
		}
	}
}

/**
 * 根据身份证获取生日
 * @param int $idCard 身份证
 * @return string 生日 ，false 未获取到
 * @author rohochan <rohochan@gmail.com>
 */
function get_birthday_by_idCard($idCard = null){
	if(!isset($idCard)){
		return false;
	}else {
		if (validateIDCard($idCard)) {
			$year =substr($idCard,6,4);
			$month =substr($idCard,10,2);
			$day =substr($idCard,12,2);
			$birthday = date('Y-m-d H:i:s',strtotime($year.'-'.$month.'-'.$day));
			return $birthday;
		}else {
			return false;
		}
	}
}

/**
 * 根据身份证获取性别
 * @param int $idCard 身份证
 * @return string 性别 1男，2女，false 未获取到
 * @author rohochan <rohochan@gmail.com>
 */
function get_gender_by_idCard($idCard = null){
	if(!isset($idCard)){
		return false;
	}else {
		if (validateIDCard($idCard)) {
			$gender =substr($idCard,16,1);
			return 0 == $gender % 2?2:1;
		}else {
			return false;
		}
	}
}

/**
 * 计算月份相差的月份数
 * @param int $startDate 开始月份
 * @param int $endDate 结束月份
 * @return int 月数 ，false 未获取到
 * @author rohochan <rohochan@gmail.com>
 */
function get_different_by_month($startDate,$endDate){
	if(!isset($startDate) || !isset($endDate)){
		return false;
	}else {
		$year = substr($endDate,0,4) - substr($startDate,0,4);
		$month = substr($endDate,-2,2) - substr($startDate,-2,2);
		return $year*12 + $month +1;
	}
}

/**
 * 获取两个月份之间的所有月份
 * @param int $startDate 开始月份
 * @param int $endDate 结束月份
 * @return array 月数 ，false 未获取到
 * @author rohochan <rohochan@gmail.com>
 */
function get_months_by_month($startDate,$endDate){
	if(!isset($startDate) || !isset($endDate)){
		return false;
	}else {
		$year = substr($endDate,0,4) - substr($startDate,0,4);
		$month = substr($endDate,-2,2) - substr($startDate,-2,2);
		$months = $year*12 + $month +1;
		$startYear= substr($startDate,0,4);
		$startMonth = substr($startDate,-2,2);
		
		$monthsArray = array();
		for ($i=0; $i < $months; $i++) {
			$temp = ($startMonth+$i)%12;
			$monthsArray[] = ($startYear+intval(($startMonth+$i-1)/12)).str_pad((0 == $temp?12:$temp),2,'0',STR_PAD_LEFT);
		}
		return $monthsArray;
	}
}

/**
 * 整形形式年月转化为日期形式年月
 * @param int $date 年月
 * @param string $separator
 * @return string 年月 ，false 未获取到
 * @author rohochan <rohochan@gmail.com>
 */
function int_to_date($date,$separator = '/'){
	if(!isset($date) || !isset($separator)){
		return false;
	}else {
		if (6 == strlen($date)) {
			$year = substr($date,0,4);
			$month = substr($date,-2,2);
			return $year.$separator.$month;
			//return substr_replace($date,$separator,4,0);
		}else {
			return false;
		}
	}
}

/**
 * 日期形式年月转化为整形形式年月
 * @param str $date 年月
 * @param string $separator
 * @return array 月数 ，false 未获取到
 * @author rohochan <rohochan@gmail.com>
 */
function date_to_int($date){
	if(!isset($date)){
		return false;
	}else {
		if (7 == strlen($date)) {
			$year = substr($date,0,4);
			$month = substr($date,-2,2);
			return $year.$month;
		}else {
			return false;
		}
	}
}

/**
 * 下载本地文件
 * @param  array    $file     文件信息数组
 * @param  callable $callback 下载回调函数，一般用于增加下载次数
 * @param  string   $args     回调函数参数
 * @return boolean            下载失败返回false
 */
function downLocalFile($file, $callback = null, $args = null){
	$file['url'] = DIRECTORY_SEPARATOR=='\\'?iconv('UTF-8','GB2312',$file['url']):$file['url'];
	if(is_file($file['url'])){
		/* 调用回调函数新增下载数 */
		is_callable($callback) && call_user_func($callback, $args);
		
		$length = $file['size']?$file['size']:filesize($file['url']);
		//$type = pathinfo($file, PATHINFO_EXTENSION);
		
		/* 执行下载 */ //TODO: 大文件断点续传
		header("Content-Description: File Transfer");
		header('Content-type: ' . $file['type']);
		header('Content-Length:' . $length);
		if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
			header('Content-Disposition: attachment; filename="' . rawurlencode($file['name']) . '"');
		} else {
			header('Content-Disposition: attachment; filename="' . $file['name'] . '"');
		}
		readfile($file['url']);
		exit;
	} else {
		echo '文件已被删除！';
		return false;
	}
}

/**
  * wlog
  * 记录日志
  * @access default
  * @param string $str
  * @return void
  * @date 2016-06-30
  * @author RohoChan<[email]rohochan@gmail.com[/email]>
  **/
function wlog($str){
	if (is_array($str)) {
		$str = print_r($str,true);
	}
	$fp = fopen(LOG_PATH.MODULE_NAME.'/log_'.date('Y-m-d',time()).'.txt','a+');
	fwrite($fp, "".date('Y-m-d H:i:s',time()).":\n".$str."\n\n");
	fclose($fp);
}

function paylog($str){
	$file  = RUNTIME_PATH.'Logs/Company/pay_log_'.date('Y-m-d',time()).'.log';
	$f = fopen($file, 'a');
	fputs($f, $str."\r\n");
	fclose($f);
}

/**
 * get_deadline function
 * 根据截止时间获取倒计时
 * @param string $dateTime 时间字符串
 * @return mixed
 * @author rohochan <rohochan@gmail.com>
 **/
function get_deadline($dateTime = ''){
	if (!empty($dateTime)) {
		$deadlineSeconds = strtotime($dateTime)-time();
		if ($deadlineSeconds > 0) {
			$days = intval($deadlineSeconds/86400);
			$hours = intval($deadlineSeconds%86400/3600);
			$minutes = intval($deadlineSeconds%3600/60);
			$seconds = intval($deadlineSeconds%60);
			return sprintf('%d天%d时%d分%d秒',$days,$hours,$minutes,$seconds);
		}else {
			return '已截止';
		}
	}else {
		return false;
	}
}
/**
 * clean_temp_by_companyId function
 * 根据企业用户ID清除临时文件夹
 * @param int $companyId 企业用户ID
 * @return void
 * @author rohochan <rohochan@gmail.com>
 **/
function clean_temp_by_companyId($companyId = 0){
	$path = getFilePath($companyId,'./Uploads/Company/','temp');
	$files = glob($path.'*');
	foreach ($files as $file) {
		if (is_file($file)) {
			unlink($file);
		}
	}
}

/**
 * get_idCardImg_by_baseId function
 * 根据个人信息ID获取身份证图片
 * @param int $baseId 个人信息ID
 * @return void
 * @author rohochan <rohochan@gmail.com>
 **/
function get_idCardImg_by_baseId($baseId = 0){
	if ($baseId ) {
		$path = getFilePath($baseId,'./Uploads/Person/','IDCard');
		$idCardFront = $path.'idCardFront.jpg';
		$idCardBack = $path.'idCardBack.jpg';
		$result = array();
		if (file_exists($idCardFront)) {
			$result['idCardFront'] = ltrim($idCardFront,'.');
		}else {
			//$result['idCardFront'] = '/Application/static/Home/images/identity.jpg';//默认图片
			$result['idCardFront'] = '/Application/Company/Assets/v2/images/idcard1.png';//默认图片
		}
		if (file_exists($idCardBack)) {
			$result['idCardBack'] = ltrim($idCardBack,'.');
		}else {
			//$result['idCardBack'] = '/Application/static/Home/images/identity.jpg';//默认图片
			$result['idCardBack'] = '/Application/Company/Assets/v2/images/idcard2.png';//默认图片
		}
		return $result;
	}else {
		//$this->error = '非法参数!';
		return false;
	}
}

/**
 * get_companyFile_by_companyId function
 * 根据企业用户ID获取企业证件和授权协议文档
 * @param int $companyId 企业用户ID
 * @return void
 * @author rohochan <rohochan@gmail.com>
 **/
function get_companyFile_by_companyId($companyId = 0){
	if ($companyId) {
		$path = getFilePath($companyId,'./Uploads/Company/','info');
		//$fileNameArray = array(1=>'business_license',2=>'tax_cegistration_certificate',3=>'taxpayer_qualification_certificate',4=>'account_opening_license');
		$fileNameArray = C('COMPANY_INFO_FILE_NAME');
		foreach ($fileNameArray as $key => $value) {
			$fileUrl = $path.$value.'.jpg';
			if (file_exists($fileUrl)) {
				$result[$value] = ltrim($fileUrl,'.');
			}else {
				$result[$value] = '';//默认文件
			}
		}
		return $result;
	}else {
		return false;
	}
}

/**
 * 移动文件
 * @param  string $oldFile 旧文件路径
 * @param  string $newFile 新文件路径
 * @return array status是否成功 info信息
 * @author rohochan <rohochan@gmail.com>
 */
function move($oldFile,$newFile){
	if(!copy($oldFile,$newFile)){
		return array('status'=>0,'info'=>'复制新文件失败!');
	}
	if (!@unlink($oldFile)) {
		return array('status'=>0,'info'=>'删除原文件失败!');
	}
	return array('status'=>1,'info'=>'移动新文件成功!');
	
	/*$result = array('status'=>1,'info'=>'移动新文件成功!');
	copy($oldFile,$newFile) || $result = array('status'=>0,'info'=>'复制新文件失败!');
	@unlink($oldFile) || $result = array('status'=>0,'info'=>'删除原文件失败!');
	return $result;*/
}

/**
 * rsa function
 * rsa加解密
 * @param string $str 字符串
 * @param int type 加解密类型 1:加密 2:解密 3:base64与json编码后加密 4:解密后分别进行base64与json解码
 * @return array $result 执行结果
 * @author rohochan<rohochan@gmail.com>
 **/
function rsa($str = '', $type = 1){
	vendor('rsa.lib.Rsa');
	$path = VENDOR_PATH.'rsa/key';
	$rsa = new \Rsa($path);
	switch ($type) {
		case 1:
			$result = $rsa->pubEncrypt($str);
			break;
		case 2:
			$result = $rsa->privDecrypt($str);
			break;
		case 3:
			$result = $rsa->pubEncrypt(base64_encode(json_encode($str)));
			break;
		case 4:
			$result = json_decode(base64_decode($rsa->privDecrypt($str)),true);
			break;
		default:
			$result = 'illegal operation';
			break;
	}
	return $result;
}

/**
 * aes function
 * aes加解密
 * @param string $str 字符串
 * @param int type 加解密类型 1:加密 2:解密 3:base64与json编码后加密 4:解密后分别进行base64与json解码
 * @param string $password 密码
 * @param string $nBits 加密位数(128, 192, 256)
 * @return array $result 执行结果
 * @author rohochan<rohochan@gmail.com>
 **/
function aes($str = '', $type = 1, $password='', $nBits = 256){
	vendor('aes.lib.aes');
	switch ($type) {
		case 1:
			$result = AESEncryptCtr($str, $password, $nBits) ;
			break;
		case 2:
			$result = AESDecryptCtr($str, $password, $nBits);
			break;
		case 3:
			$result = AESEncryptCtr(base64_encode(json_encode($str)), $password, $nBits);
			break;
		case 4:
			$result = json_decode(base64_decode(AESDecryptCtr($str, $password, $nBits)),true);
		case 5:
			$result = AESEncryptCtr(urlsafe_base64encode(json_encode($str)), $password, $nBits);
			break;
		case 6:
			$result = json_decode(urlsafe_base64decode(AESDecryptCtr($str, $password, $nBits)),true);
			break;
		default:
			$result = 'illegal operation';
			break;
	}
	return $result;
}

/**
 * GUID function
 * 生成GUID
 * @return string  GUID
 * @author rohochan<rohochan@gmail.com>
 **/
function GUID(){
	if (function_exists('com_create_guid') === true){
		return trim(com_create_guid(), '{}');
	}
	return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

/**
 * create_order_sn function
 * 创建订单
 * @param $prefix 前缀
 * @return string
 * @author rohochan<rohochan@gmail.com>
 **/
function create_order_sn($prefix = ''){
	//16位
	/*$year_code = array('A','B','C','D','E','F','G','H','I','J');
	return $prefix.$year_code[intval(date('Y'))-2016].strtoupper(dechex(date('m'))).date('d').substr(time(),-5).substr(microtime(),2,5).sprintf('%02d',rand(0,99));*/
	
	//20位不带用户ID
	//return $prefix.time().substr(microtime(),2,6).sprintf('%04d',rand(0,9999));
	//20位带用户ID
	//return $prefix.time().substr(microtime(),2,6).sprintf('%08d',is_login()).sprintf('%02d',rand(0,99));
	//16位
	//return $prefix.time().substr(microtime(),2,3).sprintf('%03d',rand(0,999));
	
	//20位带显式时间不带用户ID
	return $prefix.date('ymd').sprintf('%05d',time()-strtotime(date("Y-m-d"))).substr(microtime(),2,6).sprintf('%03d',rand(0,999));
	//20位带显式时间带用户ID
	//return $prefix.date('ymd').sprintf('%05d',time()-strtotime(date("Y-m-d"))).sprintf('%08d',is_login()).sprintf('%02d',rand(0,99));
	//16位带显式时间
	//return $prefix.date('ymd').sprintf('%05d',time()-strtotime(date("Y-m-d"))).substr(microtime(),2,3).sprintf('%02d',rand(0,99));
}

/**
 * @param int $amount 应发工资
 * @param int $month 计算税的月数
 * @param int $start 免征税额
 * @return float 个人所得税
 */
function detailsaly ($amount=0,$month=1,$start = 3500){
	$beyond = $amount - $start;
	$tax = 0.00;
	if ($beyond < 0) return $tax;
	$beyond = abs($beyond);
	if ($beyond >= 0 && $beyond < 1500){
		$tax = round($beyond*3/100*$month , 2);
	}else if ($beyond >= 1500 && $beyond < 4500){
		$tax = round(($beyond*10/100-105)*$month , 2);
	}else if ($beyond >= 4500 && $beyond < 9000){
		$tax = round(($beyond*20/100-555)*$month , 2);
	}else if ($beyond >= 9000  && $beyond < 35000){
		$tax = round(($beyond*25/100-1005)*$month , 2);
	}else if ($beyond >= 35000 && $beyond < 55000){
		$tax = round(($beyond*30/100-2755)*$month , 2);
	}else if ($beyond >= 55000 && $beyond < 80000){
		$tax = round(($beyond*35/100-5505)*$month , 2);
	}else{
		$tax = round(($beyond*45/100-13505)*$month , 2);
	}
	return $tax;
}

/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

/**
 * 加密用户资料
 * @param  string $cardnum 需要加密的字符串
 * @param  integer $type    需要加密的类型
 * @param  string  $default 默认返回字符
 * @return string 加密后的字符串
 */
function hidecard($cardnum,$type=1,$default=""){
	if(empty($cardnum)) return $default;
	if($type==1) $cardnum = substr($cardnum,0,3).str_repeat("*",12).substr($cardnum,strlen($cardnum)-4);//身份证
	elseif($type==2) $cardnum = substr($cardnum,0,3).str_repeat("*",5).substr($cardnum,strlen($cardnum)-4);//手机号
	elseif($type==3) $cardnum = str_repeat("*",strlen($cardnum)-4).substr($cardnum,strlen($cardnum)-4);//银行卡
	elseif($type==4) $cardnum = substr($cardnum,0,3).str_repeat("*",strlen($cardnum)-3);//用户名
	elseif($type==5) $cardnum = mb_strcut($cardnum,0,3,"utf-8").str_repeat("*",3).mb_strcut($cardnum,strlen($cardnum)-3,strlen($cardnum),"utf-8");//新用户名
	elseif($type=6){
		$str = explode('@', $cardnum);
		$str[0] = substr($str[0],0,strlen($str[0])-3).str_repeat("*",3);
		return $str[0].'@'.$str[1];
	}
	return $cardnum;
}

function mkFilePath($id, $prefixpath = '', $classfile = '')
{//{{{
	$id = strval($id);
	$path = substr($id, 0, (strlen($id)-4));
	!$path && $path = 0;
	$path = $prefixpath . $path;
	//echo "path=$path<br>";
	if(!file_exists($path))
	{
		@mkdir($path);
		touch($path.'/index.html');
	}
	$path .= "/" . $id;
	//echo "path=$path<br>";
	if(!file_exists($path))
	{
		@mkdir($path);
		touch($path.'/index.html');
	}
	if (strlen($classfile) > 0)
	{
		$path .= "/" . $classfile;
		if(!file_exists($path))
		{
			@mkdir($path);
			touch($path.'/index.html');
		}
	}
	$path .= "/";
	return $path;
}

function getFilePath($id, $prefixpath = '', $classfile = '')
{//{{{
	$id = strval($id);
	$path = substr($id, 0, (strlen($id)-4));
	!$path && $path = 0;
	$path = $prefixpath . $path;
	$path .= "/" . $id;
	if (strlen($classfile) > 0)
	{
		$path .= "/" . $classfile;
	}
	$path .= "/";
	return $path;
}

/**  * 系统    /**
 * 系统邮件发送函数
 * @param string $to    接收邮件者邮箱
 * @param string $name  接收邮件者名称
 * @param string $subject 邮件主题 
 * @param string $body    邮件内容
 * @param string $attachment 附件列表
 * @return boolean 
 */
function think_send_mail($to, $name, $subject = '', $body = '', $attachment = null)
{
	$config = C('THINK_EMAIL');
	vendor('PHPMailer.class#phpmailer'); //从PHPMailer目录导class.phpmailer.php类文件
	$mail             = new PHPMailer(); //PHPMailer对象
	$mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
	$mail->IsSMTP();  // 设定使用SMTP服务
	$mail->IsHTML(true); 
	$mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能
											   // 1 = errors and messages
											   // 2 = messages only
	$mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
	//$mail->SMTPSecure = 'ssl';               // 使用安全协议
	$mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
	$mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
	$mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
	$mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
	$mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
	$replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
	$replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
	$mail->AddReplyTo($replyEmail, $replyName);
	$mail->Subject    = $subject;
	//邮件内容
	$mail->MsgHTML($body);
	//收件邮箱、姓名
	$mail->AddAddress($to, $name);
	if(is_array($attachment))// 添加附件
	{ 
		foreach ($attachment as $file)
		{
			is_file($file) && $mail->AddAttachment($file);
		}
	}
	return $mail->Send() ? true : $mail->ErrorInfo;
}


function initRedis ()
{
	if (!extensioned('redis')) return false;
	$connect = C('REDIS_CONNECT');
	$redis = new Redis();
	$redis->connect($connect['ip'], $connect['port']);
	if ($connect['auth']) $redis->auth($connect['auth']);
	return $redis;
}


function extensioned ($extension)
{
	if (!in_array($extension,get_loaded_extensions())) return false;
	return true;
}

/**系统
 * 手机格式验证
 * @param string $mobile  手机号码
 * @return boolean 
 */
function mobileFormat($mobile){
	if (preg_match("/^0?(13[0-9]|15[0-9]|17[0-9]|18[0-9]|14[57])[0-9]{8}$/",$mobile)){
		return true;
	}else{
		return false;
	}
}

/**系统
 * 邮箱格式验证
 * @param string $email  邮箱
 * @return boolean 
 */
function emailFormat($email){
	if (preg_match("/^([A-Za-z0-9])([\w\-\.])*@(vip\.)?([\w\-])+(\.)(com|com\.cn|net|cn|net\.cn|org|biz|info|gov|gov\.cn|edu|edu\.cn|biz|cc|tv|me|co|so|tel|mobi|asia|pw|la|tm)$/",$email)){
		return true;
	}else{
		return false;
	}
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_login(){
	$user = session('user_auth');
	if (empty($user)) {
		return 0;
	} else {
		return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
	}
}

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_administrator($uid = null){
	$uid = is_null($uid) ? is_login() : $uid;
	return $uid && (intval($uid) === C('USER_ADMINISTRATOR'));
}

/**
 * 验证电话号码
 * @Author   JieJie
 * @DataTime 2016-03-10T10:34:18+0800
 * @param    string   $tel  需要验证的电话号码
 * @param    string   $type 验证类型 sj tel 400
 * @return   boolean 
 */
function isTel($tel,$type=''){  
	$regxArr = array(  
		'sj'  =>  '/^(\+?86-?)?(18|15|13|14|17)[0-9]{9}$/',  
		'tel' =>  '/^(010|02\d{1}|0[3-9]\d{2})-\d{7,9}(-\d+)?$/',  
		'400' =>  '/^400(-\d{3,4}){2}$/',  
	);  
	if($type && isset($regxArr[$type])){  
		return preg_match($regxArr[$type], $tel) ? true:false;  
	}  
	foreach($regxArr as $regx){  
		if(preg_match($regx, $tel )){  
			return true;  
		}  
	}  
	return false;  
}  

/**
 * [setExcelHead 设置excel表头]
 * @param array $data 数据
 *  @return  excel对象
 */
function setExcelHead($data){
	vendor('PHPExcel.PHPExcel');
    $objExcel = new \PHPExcel();  
    $objExcel->getProperties()
             ->setCreator(($data['creator']?:'智保易'))
             ->setLastModifiedBy(($data['lastModifiedBy']?:'智保易'))
             ->setTitle(($data['title']?:'Office 2007 XLSX Document'))
             ->setSubject(($data['subject']?:'Office 2007 XLSX Document'))
             ->setDescription(($data['description']?:'Document for Office 2007 XLSX'))
             ->setKeywords(($data['keywords']?:'office 2007 openxml'))
             ->setCategory(($data['category']?:'Result file'));
    return $objExcel;
}
/**
 * [setExcelTextFont 表头加粗]
 * @param [string] $rang     [加粗范围]
 * @param [object] $objExcel [excel对象]
 * @return excel对象
 */
function setExcelTextFont($rang,$objExcel){
	$styleArray = array(
            'font' => array(
                'bold' => true
            )
        );
    $objExcel->getActiveSheet()->getStyle($rang)->applyFromArray($styleArray);
    return $objExcel;
}
/**
 * [setExcelWidth 设置表格宽度]
 * @param [array] $width    [宽度数组]
 * @param [object] $objExcel [excel对象]
 * @return excel对象
 */
function setExcelWidth($width,$objExcel){
	foreach ($width as $key => $value) {
		$objExcel->getActiveSheet()->getColumnDimension($key)->setWidth($value);
	}
	return $objExcel;
}
/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function str2arr($str, $glue = ','){
	return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function arr2str($arr, $glue = ','){
	return implode($glue, $arr);
}

/**
 * 字符串截取，支持中文和其他编码
 * @param string $str 需要转换的字符串
 * @param int    $start 开始位置
 * @param int    $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
	$old_len = mb_strlen($str,$charset);
	if(function_exists("mb_substr"))
		$slice = mb_substr($str, $start, $length, $charset);
	elseif(function_exists('iconv_substr')) {
		$slice = iconv_substr($str,$start,$length,$charset);
		if(false === $slice) {
			$slice = '';
		}
	}else{
		$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("",array_slice($match[0], $start, $length));
	}
	$new_len = mb_strlen($slice,$charset);
	if($suffix && $old_len!=$new_len) return $slice.'...';
	return $slice;
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt($data, $key = '', $expire = 0) {
	$key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
	$data = base64_encode($data);
	$x    = 0;
	$len  = strlen($data);
	$l    = strlen($key);
	$char = '';

	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) $x = 0;
		$char .= substr($key, $x, 1);
		$x++;
	}

	$str = sprintf('%010d', $expire ? $expire + time():0);

	for ($i = 0; $i < $len; $i++) {
		$str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
	}
	return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_decrypt($data, $key = ''){
	$key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
	$data   = str_replace(array('-','_'),array('+','/'),$data);
	$mod4   = strlen($data) % 4;
	if ($mod4) {
	   $data .= substr('====', $mod4);
	}
	$data   = base64_decode($data);
	$expire = substr($data,0,10);
	$data   = substr($data,10);

	if($expire > 0 && $expire < time()) {
		return '';
	}
	$x      = 0;
	$len    = strlen($data);
	$l      = strlen($key);
	$char   = $str = '';

	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) $x = 0;
		$char .= substr($key, $x, 1);
		$x++;
	}

	for ($i = 0; $i < $len; $i++) {
		if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
			$str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
		}else{
			$str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
		}
	}
	return base64_decode($str);
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data) {
	//数据类型检测
	if(!is_array($data)){
		$data = (array)$data;
	}
	ksort($data); //排序
	$code = http_build_query($data); //url编码并生成query字符串
	$sign = sha1($code); //生成签名
	return $sign;
}

/**
* 对查询结果集进行排序
* @access public
* @param array $list 查询结果
* @param string $field 排序的字段名
* @param array $sortby 排序类型
* asc正向排序 desc逆向排序 nat自然排序
* @return array
*/
function list_sort_by($list,$field, $sortby='asc') {
   if(is_array($list)){
	   $refer = $resultSet = array();
	   foreach ($list as $i => $data)
		   $refer[$i] = &$data[$field];
	   switch ($sortby) {
		   case 'asc': // 正向排序
				asort($refer);
				break;
		   case 'desc':// 逆向排序
				arsort($refer);
				break;
		   case 'nat': // 自然排序
				natcasesort($refer);
				break;
	   }
	   foreach ( $refer as $key=> $val)
		   $resultSet[] = &$list[$key];
	   return $resultSet;
   }
   return false;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
	// 创建Tree
	$tree = array();
	if(is_array($list)) {
		// 创建基于主键的数组引用
		$refer = array();
		foreach ($list as $key => $data) {
			$refer[$data[$pk]] =& $list[$key];
		}
		foreach ($list as $key => $data) {
			// 判断是否存在parent
			$parentId =  $data[$pid];
			if ($root == $parentId) {
				$tree[] =& $list[$key];
			}else{
				if (isset($refer[$parentId])) {
					$parent =& $refer[$parentId];
					$parent[$child][] =& $list[$key];
				}
			}
		}
	}
	return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order='id', &$list = array()){
	if(is_array($tree)) {
		foreach ($tree as $key => $value) {
			$reffer = $value;
			if(isset($reffer[$child])){
				unset($reffer[$child]);
				tree_to_list($value[$child], $child, $order, $list);
			}
			$list[] = $reffer;
		}
		$list = list_sort_by($list, $order, $sortby='asc');
	}
	return $list;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
	$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
	return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 设置跳转页面URL
 * 使用函数再次封装，方便以后选择不同的存储方式（目前使用cookie存储）
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function set_redirect_url($url){
	cookie('redirect_url', $url);
}

/**
 * 获取跳转页面URL
 * @return string 跳转页URL
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_redirect_url(){
	$url = cookie('redirect_url');
	return empty($url) ? __APP__ : $url;
}

/**
 * 处理插件钩子
 * @param string $hook   钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook,$params=array()){
	\Think\Hook::listen($hook,$params);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name){
	$class = "Addons\\{$name}\\{$name}Addon";
	return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 */
function get_addon_config($name){
	$class = get_addon_class($name);
	if(class_exists($class)) {
		$addon = new $class();
		return $addon->getConfig();
	}else {
		return array();
	}
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function addons_url($url, $param = array()){
	$url        = parse_url($url);
	$case       = C('URL_CASE_INSENSITIVE');
	$addons     = $case ? parse_name($url['scheme']) : $url['scheme'];
	$controller = $case ? parse_name($url['host']) : $url['host'];
	$action     = trim($case ? strtolower($url['path']) : $url['path'], '/');

	/* 解析URL带的参数 */
	if(isset($url['query'])){
		parse_str($url['query'], $query);
		$param = array_merge($query, $param);
	}

	/* 基础参数 */
	$params = array(
		'_addons'     => $addons,
		'_controller' => $controller,
		'_action'     => $action,
	);
	$params = array_merge($params, $param); //添加额外参数

	return U('Addons/execute', $params);
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL,$format='Y-m-d H:i'){
	$time = $time === NULL ? NOW_TIME : intval($time);
	return date($format, $time);
}

/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_username($uid = 0){
	static $list;
	if(!($uid && is_numeric($uid))){ //获取当前登录用户名
		return session('user_auth.username');
	}

	/* 获取缓存数据 */
	if(empty($list)){
		$list = S('sys_active_user_list');
	}

	/* 查找用户信息 */
	$key = "u{$uid}";
	if(isset($list[$key])){ //已缓存，直接使用
		$name = $list[$key];
	} else { //调用接口获取用户信息
		$User = new User\Api\UserApi();
		$info = $User->info($uid);
		if($info && isset($info[1])){
			$name = $list[$key] = $info[1];
			/* 缓存用户 */
			$count = count($list);
			$max   = C('USER_MAX_CACHE');
			while ($count-- > $max) {
				array_shift($list);
			}
			S('sys_active_user_list', $list);
		} else {
			$name = '';
		}
	}
	return $name;
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
function get_nickname($uid = 0){
	static $list;
	if(!($uid && is_numeric($uid))){ //获取当前登录用户名
		return session('user_auth.username');
	}

	/* 获取缓存数据 */
	if(empty($list)){
		$list = S('sys_user_nickname_list');
	}

	/* 查找用户信息 */
	$key = "u{$uid}";
	if(isset($list[$key])){ //已缓存，直接使用
		$name = $list[$key];
	} else { //调用接口获取用户信息
		$info = M('Member')->field('nickname')->find($uid);
		if($info !== false && $info['nickname'] ){
			$nickname = $info['nickname'];
			$name = $list[$key] = $nickname;
			/* 缓存用户 */
			$count = count($list);
			$max   = C('USER_MAX_CACHE');
			while ($count-- > $max) {
				array_shift($list);
			}
			S('sys_user_nickname_list', $list);
		} else {
			$name = '';
		}
	}
	return $name;
}

/**
 * 获取分类信息并缓存分类
 * @param  integer $id    分类ID
 * @param  string  $field 要获取的字段名
 * @return string         分类信息
 */
function get_category($id, $field = null){
	static $list;

	/* 非法分类ID */
	if(empty($id) || !is_numeric($id)){
		return '';
	}

	/* 读取缓存数据 */
	if(empty($list)){
		$list = S('sys_category_list');
	}

	/* 获取分类名称 */
	if(!isset($list[$id])){
		$cate = M('Category')->find($id);
		if(!$cate || 1 != $cate['status']){ //不存在分类，或分类被禁用
			return '';
		}
		$list[$id] = $cate;
		S('sys_category_list', $list); //更新缓存
	}
	return is_null($field) ? $list[$id] : $list[$id][$field];
}

/* 根据ID获取分类标识 */
function get_category_name($id){
	return get_category($id, 'name');
}

/* 根据ID获取分类名称 */
function get_category_title($id){
	return get_category($id, 'title');
}

/**
 * 获取顶级模型信息
 */
function get_top_model($model_id=null){
	$map   = array('status' => 1, 'extend' => 0);
	if(!is_null($model_id)){
		$map['id']  =   array('neq',$model_id);
	}
	$model = M('Model')->where($map)->field(true)->select();
	foreach ($model as $value) {
		$list[$value['id']] = $value;
	}
	return $list;
}

/**
 * 获取文档模型信息
 * @param  integer $id    模型ID
 * @param  string  $field 模型字段
 * @return array
 */
function get_document_model($id = null, $field = null){
	static $list;

	/* 非法分类ID */
	if(!(is_numeric($id) || is_null($id))){
		return '';
	}

	/* 读取缓存数据 */
	if(empty($list)){
		$list = S('DOCUMENT_MODEL_LIST');
	}

	/* 获取模型名称 */
	if(empty($list)){
		$map   = array('status' => 1, 'extend' => 1);
		$model = M('Model')->where($map)->field(true)->select();
		foreach ($model as $value) {
			$list[$value['id']] = $value;
		}
		S('DOCUMENT_MODEL_LIST', $list); //更新缓存
	}

	/* 根据条件返回数据 */
	if(is_null($id)){
		return $list;
	} elseif(is_null($field)){
		return $list[$id];
	} else {
		return $list[$id][$field];
	}
}

/**
 * 解析UBB数据
 * @param string $data UBB字符串
 * @return string 解析为HTML的数据
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function ubb($data){
	//TODO: 待完善，目前返回原始数据
	return $data;
}

/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action 行为标识
 * @param string $model 触发行为的模型名
 * @param int $record_id 触发行为的记录id
 * @param int $user_id 执行行为的用户id
 * @return boolean
 * @author huajie <banhuajie@163.com>
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null){

	//参数检查
	if(empty($action) || empty($model) || empty($record_id)){
		return '参数不能为空';
	}
	if(empty($user_id)){
		$user_id = is_login();
	}

	//查询行为,判断是否执行
	$action_info = M('Action')->getByName($action);
	if($action_info['status'] != 1){
		return '该行为被禁用或删除';
	}

	//插入行为日志
	$data['action_id']      =   $action_info['id'];
	$data['user_id']        =   $user_id;
	$data['action_ip']      =   ip2long(get_client_ip());
	$data['model']          =   $model;
	$data['record_id']      =   $record_id;
	$data['create_time']    =   NOW_TIME;

	//解析日志规则,生成日志备注
	if(!empty($action_info['log'])){
		if(preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)){
			$log['user']    =   $user_id;
			$log['record']  =   $record_id;
			$log['model']   =   $model;
			$log['time']    =   NOW_TIME;
			$log['data']    =   array('user'=>$user_id,'model'=>$model,'record'=>$record_id,'time'=>NOW_TIME);
			foreach ($match[1] as $value){
				$param = explode('|', $value);
				if(isset($param[1])){
					$replace[] = call_user_func($param[1],$log[$param[0]]);
				}else{
					$replace[] = $log[$param[0]];
				}
			}
			$data['remark'] =   str_replace($match[0], $replace, $action_info['log']);
		}else{
			$data['remark'] =   $action_info['log'];
		}
	}else{
		//未定义日志规则，记录操作url
		$data['remark']     =   '操作url：'.$_SERVER['REQUEST_URI'];
	}

	M('ActionLog')->add($data);

	if(!empty($action_info['rule'])){
		//解析行为
		$rules = parse_action($action, $user_id);

		//执行行为
		$res = execute_action($rules, $action_info['id'], $user_id);
	}
}

/**
 * 解析行为规则
 * 规则定义  table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
 *              field->要操作的字段；
 *              condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
 *              rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
 *              cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
 *              max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
 * 单个行为后可加 ； 连接其他规则
 * @param string $action 行为id或者name
 * @param int $self 替换规则里的变量为执行用户的id
 * @return boolean|array: false解析出错 ， 成功返回规则数组
 * @author huajie <banhuajie@163.com>
 */
function parse_action($action = null, $self){
	if(empty($action)){
		return false;
	}

	//参数支持id或者name
	if(is_numeric($action)){
		$map = array('id'=>$action);
	}else{
		$map = array('name'=>$action);
	}

	//查询行为信息
	$info = M('Action')->where($map)->find();
	if(!$info || $info['status'] != 1){
		return false;
	}

	//解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
	$rules = $info['rule'];
	$rules = str_replace('{$self}', $self, $rules);
	$rules = explode(';', $rules);
	$return = array();
	foreach ($rules as $key=>&$rule){
		$rule = explode('|', $rule);
		foreach ($rule as $k=>$fields){
			$field = empty($fields) ? array() : explode(':', $fields);
			if(!empty($field)){
				$return[$key][$field[0]] = $field[1];
			}
		}
		//cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
		if(!array_key_exists('cycle', $return[$key]) || !array_key_exists('max', $return[$key])){
			unset($return[$key]['cycle'],$return[$key]['max']);
		}
	}

	return $return;
}

/**
 * 执行行为
 * @param array $rules 解析后的规则数组
 * @param int $action_id 行为id
 * @param array $user_id 执行的用户id
 * @return boolean false 失败 ， true 成功
 * @author huajie <banhuajie@163.com>
 */
function execute_action($rules = false, $action_id = null, $user_id = null){
	if(!$rules || empty($action_id) || empty($user_id)){
		return false;
	}

	$return = true;
	foreach ($rules as $rule){

		//检查执行周期
		$map = array('action_id'=>$action_id, 'user_id'=>$user_id);
		$map['create_time'] = array('gt', NOW_TIME - intval($rule['cycle']) * 3600);
		$exec_count = M('ActionLog')->where($map)->count();
		if($exec_count > $rule['max']){
			continue;
		}

		//执行数据库操作
		$Model = M(ucfirst($rule['table']));
		$field = $rule['field'];
		$res = $Model->where($rule['condition'])->setField($field, array('exp', $rule['rule']));

		if(!$res){
			$return = false;
		}
	}
	return $return;
}

//基于数组创建目录和文件
function create_dir_or_files($files){
	foreach ($files as $key => $value) {
		if(substr($value, -1) == '/'){
			mkdir($value);
		}else{
			@file_put_contents($value, '');
		}
	}
}

if(!function_exists('array_column')){
	function array_column(array $input, $columnKey, $indexKey = null) {
		$result = array();
		if (null === $indexKey) {
			if (null === $columnKey) {
				$result = array_values($input);
			} else {
				foreach ($input as $row) {
					$result[] = $row[$columnKey];
				}
			}
		} else {
			if (null === $columnKey) {
				foreach ($input as $row) {
					$result[$row[$indexKey]] = $row;
				}
			} else {
				foreach ($input as $row) {
					$result[$row[$indexKey]] = $row[$columnKey];
				}
			}
		}
		return $result;
	}
}

/**
 * 获取表名（不含表前缀）
 * @param string $model_id
 * @return string 表名
 * @author huajie <banhuajie@163.com>
 */
function get_table_name($model_id = null){
	if(empty($model_id)){
		return false;
	}
	$Model = M('Model');
	$name = '';
	$info = $Model->getById($model_id);
	if($info['extend'] != 0){
		$name = $Model->getFieldById($info['extend'], 'name').'_';
	}
	$name .= $info['name'];
	return $name;
}

/**
 * 获取属性信息并缓存
 * @param  integer $id    属性ID
 * @param  string  $field 要获取的字段名
 * @return string         属性信息
 */
function get_model_attribute($model_id, $group = true,$fields=true){
	static $list;

	/* 非法ID */
	if(empty($model_id) || !is_numeric($model_id)){
		return '';
	}

	/* 获取属性 */
	if(!isset($list[$model_id])){
		$map = array('model_id'=>$model_id);
		$extend = M('Model')->getFieldById($model_id,'extend');

		if($extend){
			$map = array('model_id'=> array("in", array($model_id, $extend)));
		}
		$info = M('Attribute')->where($map)->field($fields)->select();
		$list[$model_id] = $info;
	}

	$attr = array();
	if($group){
		foreach ($list[$model_id] as $value) {
			$attr[$value['id']] = $value;
		}
		$model     = M("Model")->field("field_sort,attribute_list,attribute_alias")->find($model_id);
		$attribute = explode(",", $model['attribute_list']);
		if (empty($model['field_sort'])) { //未排序
			$group = array(1 => array_merge($attr));
		} else {
			$group = json_decode($model['field_sort'], true);

			$keys = array_keys($group);
			foreach ($group as &$value) {
				foreach ($value as $key => $val) {
					$value[$key] = $attr[$val];
					unset($attr[$val]);
				}
			}

			if (!empty($attr)) {
				foreach ($attr as $key => $val) {
					if (!in_array($val['id'], $attribute)) {
						unset($attr[$key]);
					}
				}
				$group[$keys[0]] = array_merge($group[$keys[0]], $attr);
			}
		}
		if (!empty($model['attribute_alias'])) {
			$alias  = preg_split('/[;\r\n]+/s', $model['attribute_alias']);
			$fields = array();
			foreach ($alias as &$value) {
				$val             = explode(':', $value);
				$fields[$val[0]] = $val[1];
			}
			foreach ($group as &$value) {
				foreach ($value as $key => $val) {
					if (!empty($fields[$val['name']])) {
						$value[$key]['title'] = $fields[$val['name']];
					}
				}
			}
		}
		$attr = $group;
	}else{
		foreach ($list[$model_id] as $value) {
			$attr[$value['name']] = $value;
		}
	}
	return $attr;
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string  $name 格式 [模块名]/接口名/方法名
 * @param  array|string  $vars 参数
 */
function api($name,$vars=array()){
	$array     = explode('/',$name);
	$method    = array_pop($array);
	$classname = array_pop($array);
	$module    = $array? array_pop($array) : 'Common';
	$callback  = $module.'\\Api\\'.$classname.'Api::'.$method;
	if(is_string($vars)) {
		parse_str($vars,$vars);
	}
	return call_user_func_array($callback,$vars);
}

/**
 * 根据条件字段获取指定表的数据
 * @param mixed $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @param string $table 需要查询的表
 * @author huajie <banhuajie@163.com>
 */
function get_table_field($value = null, $condition = 'id', $field = null, $table = null){
	if(empty($value) || empty($table)){
		return false;
	}

	//拼接参数
	$map[$condition] = $value;
	$info = M(ucfirst($table))->where($map);
	if(empty($field)){
		$info = $info->field(true)->find();
	}else{
		$info = $info->getField($field);
	}
	return $info;
}

/**
 * 获取链接信息
 * @param int $link_id
 * @param string $field
 * @return 完整的链接信息或者某一字段
 * @author huajie <banhuajie@163.com>
 */
function get_link($link_id = null, $field = 'url'){
	$link = '';
	if(empty($link_id)){
		return $link;
	}
	$link = M('Url')->getById($link_id);
	if(empty($field)){
		return $link;
	}else{
		return $link[$field];
	}
}

/**
 * 获取文档封面图片
 * @param int $cover_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 * @author huajie <banhuajie@163.com>
 */
function get_cover($cover_id, $field = null){
	if(empty($cover_id)){
		return false;
	}
	$picture = M('Picture')->where(array('status'=>1))->getById($cover_id);
	if($field == 'path'){
		if(!empty($picture['url'])){
			$picture['path'] = $picture['url'];
		}else{
			$picture['path'] = __ROOT__.$picture['path'];
		}
	}
	return empty($field) ? $picture : $picture[$field];
}

/**
 * 检查$pos(推荐位的值)是否包含指定推荐位$contain
 * @param number $pos 推荐位的值
 * @param number $contain 指定推荐位
 * @return boolean true 包含 ， false 不包含
 * @author huajie <banhuajie@163.com>
 */
function check_document_position($pos = 0, $contain = 0){
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
 * 获取数据的所有子孙数据的id值
 * @author 朱亚杰 <xcoolcc@gmail.com>
 */

function get_stemma($pids,Model &$model, $field='id'){
	$collection = array();

	//非空判断
	if(empty($pids)){
		return $collection;
	}

	if( is_array($pids) ){
		$pids = trim(implode(',',$pids),',');
	}
	$result     = $model->field($field)->where(array('pid'=>array('IN',(string)$pids)))->select();
	$child_ids  = array_column ((array)$result,'id');

	while( !empty($child_ids) ){
		$collection = array_merge($collection,$result);
		$result     = $model->field($field)->where( array( 'pid'=>array( 'IN', $child_ids ) ) )->select();
		$child_ids  = array_column((array)$result,'id');
	}
	return $collection;
}

/**
 * 验证分类是否允许发布内容
 * @param  integer $id 分类ID
 * @return boolean     true-允许发布内容，false-不允许发布内容
 */
function check_category($id){
	if (is_array($id)) {
		$id['type']	=	!empty($id['type'])?$id['type']:2;
		$type = get_category($id['category_id'], 'type');
		$type = explode(",", $type);
		return in_array($id['type'], $type);
	} else {
		$publish = get_category($id, 'allow_publish');
		return $publish ? true : false;
	}
}

/**
 * 检测分类是否绑定了指定模型
 * @param  array $info 模型ID和分类ID数组
 * @return boolean     true-绑定了模型，false-未绑定模型
 */
function check_category_model($info){
	$cate   =   get_category($info['category_id']);
	$array  =   explode(',', $info['pid'] ? $cate['model_sub'] : $cate['model']);
	return in_array($info['model_id'], $array);
}
function sex ($key)
{
	return adminState()['sex'][$key];
}
function property ($key)
{
	return adminState()['property'][$key];
}
function industry ($key)
{
	return adminState()['industry'][$key];
}
function employee_number($key)
{
	return adminState()['employee_number'][$key];
}

/**
 * [getZoning 获取地区信息]
 * @return [type] [description]
 */

	function getZoning ()
	{
		$zoning = S('ptimeZoning');
		if(!$zoning){
			$location = M('location','zbw_');
			$data = $location->where('1')->select();
			foreach ($data as $key => $value) {
				$_data[$value['id']] = $value;
			}
			S('ptimeZoning', $_data ,3600*24*7);
		}
		return $zoning;
	}
	/**
 * [showAreaName 地址代号转换成中文描述]
 * @param  [type] $code [description]
 * @return [type]       [description]
 */
/**
 * [showAreaName 地址代号转换成中文描述]
 * @param  [type] $code [description]
 * @return [type]       [description]
 */
function showAreaName($code){
	if ($code) {
		$area = getZoning();
		return trim($area[$code]['name'],'"');
	}else {
		return '';
	}
}

/**
 * [showAreaName 地址代号转换成中文描述]
 * @param  [type] $code [description]
 * @return [type]       [description]
 */
function showAreaName1($code, $level= 3){
	$area = getZoning();
	$result = '';
	if($code%1000000 == 0){
		$result = trim($area[$code]['name'],'"');
	}else if($code%10000 == 0){
		$result = trim($area[intval($code/1000000)*1000000]['name'],'"').'-'.trim($area[$code]['name'],'"');
	}else if($code%100 == 0){
		if(intval($code/1000000)*1000000 == intval($code/10000)*10000){
			$result = trim($area[intval($code/10000)*10000]['name'],'"').'-'.trim($area[$code]['name'],'"');            
		}else{
			if($level == 2){
				$result = trim($area[intval($code/1000000)*1000000]['name'],'"').'-'.trim($area[intval($code/10000)*10000]['name'],'"');
			}else{
				$result = trim($area[intval($code/1000000)*1000000]['name'],'"').'-'.trim($area[intval($code/10000)*10000]['name'],'"').'-'.trim($area[$code]['name'],'"'); 
			}          
		}
	}
	return $result;
}

/*
 * 分页
 */
function showpage($count,$num)
{
	$Page = new \Think\Page($count,$num);
	$Page->setConfig('first','首页');
	$Page->setConfig('prev','上一页');
	$Page->setConfig('next','下一页');
	$Page->setConfig('last','最后一页');
	$Page->lastSuffix = false;//最后一页不显示总条数
	$Page->rollPage = 5;
	$Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
	return $Page->show();
}
function adminState()
{
	return array('bill'   => array(-2=>'支付过期',-1=>'账单失败',0=>'未支付','已支付','确认支付'),
		'sex' => array(1=>'男','女'),
		'industry' => array(1=>'互联网/电子商务' , '计算机软件' , '计算机硬件' , '电子/微电子技术/集成电路' , '通讯/电信业' , '快速消费品' , '服装/纺织/皮革' , '金融业（银行、保险、证券、投资、基金）' , '家具/家电/玩具/礼品' , '贸易/商务/进出口' , '生产/制造/加工' , '房地产/建筑/建材/工程' , '钢铁/机械/设备/重工' , '交通/运输/物流•快递' , '广告/创意/设计' , '批发/零售（超市、百货、商场、专卖店）' , '汽车/摩托车及零配件' , '仪器仪表/电工设备/工业自动化' , '医药/生物工程' , '餐饮/酒店/旅游' , '橡胶/塑胶/五金' , '印刷/包装/造纸' , '电力/电气/水利' , '石油/化工/地质' , '办公设备/文体休闲用品/家居用品' , '法律/法务' , '法律/法务' , '艺术/文体' , '娱乐/体育/休闲' , '教育/培训/科研院所' , '咨询与调查业（顾问/企业管理/知识产权）' , '咨询与调查业（顾问/企业管理/知识产权）' , '咨询与调查业（顾问/企业管理/知识产政府/公用事业/社区服务' , '农、林、牧、副、渔业' , '协会/社团/非营利机构' , 38=>'IT服务（系统/数据/维护）' , '网络游戏' , '珠宝/首饰/钟表' , '会计/审计' , '信托/担保/拍卖/典当' , '奢侈品/收藏品/工艺品' , '物业管理/商业中心' , '外包服务' , '人力资源服务' , '检测/认证' , '租赁服务' , 50=>'环保' , '航天/航空' , '多元化业务集团' , '家居/室内设计/装潢' , '公关/市场推广/会展' , '能源/矿产/采掘/冶炼' , 37=>'其他'),
		'property' => array(1=>'外资企业' , '中外合营（合资、合作）' , '台资企业' , '港资企业' , '私营·民营企业' , '股份制企业' , '跨国公司（集团）' , '国有企业' , '事业单位' ,'社会团体' ,'政府机关' ,'其他' ),
		'employee_number' => array(1=>'1~100' , '101~200' , '201~500' , '501~1000' , '1001~2000', '2000以上'),
		'sorder' => array(),
		'warranty'=> array(1=>'报增','在保','报减','停保'),
		'adminstate'=>array(-1=>'停用',0=>'暂停','启用',-9=>'删除'),
		'group'=>array(1=>'管理员','财务','客服'),
		'product_type'=>array(1=>'企业产品','个人产品','企业体验','个人体验'),
		'product_state'=>array(-1=>'已停用',0=>'已下架',1=>'发布中'),
		'product_order_state'=>array(0=>'未支付',1=>'已支付',-1=>'支付失败',-9=>'删除',-2=>'撤销',2=>'确认付款'),
		'service_state'=>array(-1=>'停止服务',0=>'未签约',1=>'已签约',2=>'服务中',3=>'服务完成'),
		'add_service_state'=>array(-9=>'删除',0=>'已下架',1=>'发布中'),
		'residence_type'=> array(1=>'农村', 2=>'城镇'),
		'pay_order_type'=> array(1=>'服务订单', 2=> '社保公积金订单', 3=> '代发工资订单'),
		'pay_order_state'=> array(0=>'待支付', 1=> '支付成功 ', 2=> '支付失败'),
		'pay_order_pay_type'=> array(1=>'线上支付', 2=> '线下支付'),
		'applicable_object'=>array(1=>'个体工商户/个人', '20人以下的企业用户','100人的企业用户','100-500人的企业用户','500人以上的企业用户'),
		);


}


