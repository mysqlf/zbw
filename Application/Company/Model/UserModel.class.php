<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: rohochan <rohochan@gmail.com> <http://blog.rohochan.com>
// +----------------------------------------------------------------------
namespace Company\Model;
use Think\Model\RelationModel;

/**
 * 企业用户模型
 */
class UserModel extends RelationModel{
	protected $tablePrefix = 'zbw_';
	
	/* 用户模型自动完成 */
	/*protected $_auto = array(
		array('login', 0, self::MODEL_INSERT),
		array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
		array('reg_time', NOW_TIME, self::MODEL_INSERT),
		array('last_login_ip', 0, self::MODEL_INSERT),
		array('last_login_time', 0, self::MODEL_INSERT),
		array('status', 1, self::MODEL_INSERT),
	);*/

	protected $_link = array(
		'CompanyInfo'=>array(
			'mapping_type'		=> self::HAS_ONE,
			'class_name'		=> 'CompanyInfo',
			//'mapping_name'	=> 'CompanyInfo',
			'foreign_key'		=> 'user_id'
		)
	);
	
	/**
	 * getMemberStatus function
	 * 获取会员状态
	 * @access public
	 * @param int $cuid 企业用户ID
	 * @return int 是否会员 -1不是 0过期 1:社保会员 2:公积金会员 4:代发工资会员 3:1社保会员+2公积金会员 5:1社保会员+4代发工资会员 6:2公积金会员+4代发工资会员 7:1社保会员+2公积金会员+4代发工资会员
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getMemberStatus($cuid){
		if (isset($cuid) && $cuid > 0) {
			$serviceProductOrder = D('ServiceProductOrder');
			//$serviceProductOrderResult = $serviceProductOrder->where(array('user_id'=>$cuid,'state'=>1,'service_state'=>2))->select();
			$serviceProductOrderResult = $serviceProductOrder->where(array('user_id'=>$cuid,'service_state'=>2))->select();
			if ($serviceProductOrderResult) {
				//计算到期时间
				$now = time();
				$memberStatus = 0;
				foreach ($serviceProductOrderResult as $key => $value) {
					$temptime = intval(strtotime($value['overtime']));
					//判断会员类型
					if ($temptime>$now) {
						if (1 == $value['is_salary']) {//代发工资
							$memberStatus = 7;
						}else {
							$memberStatus = $memberStatus | 5;
						}
					}
				}
			}else {
				$memberStatus = -1;
			}
			return $memberStatus;
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}
	
	/**
	 * isMember function
	 * 判断是否会员
	 * @access public
	 * @param int $cuid 企业用户ID
	 * @return boolean
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function isMember($cuid = 0){
		if (isset($cuid) && $cuid > 0) {
			$status = $this->getMemberStatus($cuid);
			if (false !== $status) {
				$result = array();
				$result['status'] = $status;
				$result['isNonMember'] = -1 === $status?true:false;
				$result['isExpiredMember'] = 0 === $status?true:false;
				
				if ($result['isNonMember'] || $result['isExpiredMember']) {
					$result['isSocMember'] = false;
					$result['isProMember'] = false;
					$result['isSalMember'] = false;
				}else {
					$result['isSocMember'] = check_position($status,1);
					$result['isProMember'] = check_position($status,2);
					$result['isSalMember'] = check_position($status,4);
				}
				return $result;
			}else {
				$this->error = '非法参数！';
				return false;
			}
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}

}
