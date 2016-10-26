<?php

namespace Service\Model;
use Think\Model;

/**
 * 个人参保明细模型
 */
class PersonInsuranceInfoModel extends Model{
	
	/**
	 * getInsuranceCount function
	 * 根据条件获取参保订单列表
	 * param int $userId 用户id
	 * param int $userType 用户类型
	 * param array $productIdArray 产品id数组
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getInsuranceCount($userId = 0, $userType = 1, $productIdArray = array()){
		$countResult = array();
		$countResult[0] = $this->alias('pii')->field('pii.base_id,pii.handle_month')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'user as u on u.id=pii.user_id')->where(array('u.type'=>$userType,'pii.product_id'=>array('in',$productIdArray),'pii.operate_state'=>0,'pii.state'=>array('neq',0),'pii.user_id'=>($userId?$userId:array('gt',0))))->group('pii.base_id, pii.handle_month')->select();
		//$countResult[2] = $this->alias('pii')->field('pii.base_id,pii.handle_month')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'user as u on u.id=pii.user_id')->where(array('u.type'=>$userType,'pii.product_id'=>array('in',$productIdArray),'pii.operate_state'=>1,'pii.user_id'=>($userId?$userId:array('gt',0))))->group('pii.base_id, pii.handle_month')->select();
		//$countResult[3] = $this->alias('pii')->field('pii.base_id,pii.handle_month')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'user as u on u.id=pii.user_id')->where(array('u.type'=>$userType,'pii.product_id'=>array('in',$productIdArray),'pii.operate_state'=>2,'pii.user_id'=>($userId?$userId:array('gt',0))))->group('pii.base_id, pii.handle_month')->select();
		
		$countResult[2] = $this->alias('pii')->field('pii.base_id,pii.handle_month,sid.pay_date')->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_info_id = pii.id')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.user_id=pii.user_id')->join('left join '.C('DB_PREFIX').'user as u on u.id=pii.user_id')->where(array('u.type'=>$userType,'pii.product_id'=>array('in',$productIdArray),'sid.state'=>1,'pii.user_id'=>($userId?$userId:array('gt',0))))->group('pii.base_id, pii.handle_month,sid.pay_date')->select();
		$countResult[3] = $this->alias('pii')->field('pii.base_id,pii.handle_month,sid.pay_date')->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_info_id = pii.id')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.user_id=pii.user_id')->join('left join '.C('DB_PREFIX').'user as u on u.id=pii.user_id')->where(array('u.type'=>$userType,'pii.product_id'=>array('in',$productIdArray),'sid.state'=>2,'pii.user_id'=>($userId?$userId:array('gt',0))))->group('pii.base_id, pii.handle_month,sid.pay_date')->select();
		foreach ($countResult as $key => $value) {
			$countResult[$key] = count($value);
		}
		return $countResult;
	}
	
	/**
	 * getInsuranceOrderListByCondition function
	 * 根据条件获取参保订单列表
	 * param array $data 条件数组
	 * param int $type 类型 0待审核，1已审核，2待付款，3待办理，4已办理
	 * param int $pageSize 分页大小，默认10
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getInsuranceOrderListByCondition($data,$type,$pageSize = 10){
		if (is_array($data)) {
			$condition = array();
			if (!empty($data['user_type'])) {
				$condition['u.type'] = $data['user_type'];
			}
			if ('' !== $type) {
				if ('0' == $type) {
					$condition['pii.operate_state'] = array('eq',0);
					$condition['pii.state'] = array('neq','0');
				}else if ('1' == $type) {
					$condition['pii.operate_state'] = array('in','1,-1');
				}else if ('2' == $type) {
					$condition['pii.operate_state'] = 1;
					//$condition['pii.pay_order_id'] = array('gt',0);
				}else if ('3' == $type) {
					$condition['pii.operate_state'] = 2;
				}else if ('4' == $type) {
					$condition['pii.operate_state'] = 3;
				}
			}
			if (!empty($data['user_id'])) {
				$condition['pb.user_id'] = $data['user_id'];
			}
			if (!empty($data['product_id'])) {
				$condition['pii.product_id'] = $data['product_id'];
			}
			if ('' !== $data['state'] && null !== $data['state']) {
				//$condition['pii.state'] = $data['state'];
				if ($condition['pii.state']) {
					$condition['pii.state'] = array($condition['pii.state'],$data['state'],'and');
				}else {
					$condition['pii.state'] = $data['state'];
				}
			}
			if (!empty($data['handle_month'])) {
				$condition['pii.handle_month'] = array('eq',$data['handle_month']);
			}
			if (!empty($data['start_time'])) {
				$condition['pii.create_time'] = array('egt',$data['start_time']);
			}
			if (!empty($data['start_time'])) {
				$condition['pii.create_time'] = array('elt',$data['end_time']);
			}
			if (!empty($data['company_id'])) {
				$condition['sp.company_id'] = array('eq',$data['company_id']);
			}
			/*if (!empty($data['admin_id'])) {
				$userServiceProvider = D('UserServiceProvider');
				$userServiceProviderResult = $userServiceProvider->field('company_id')->where(array('user_id'=>$data['user_id'],'admin_id'=>$data['admin_id'],'state'=>1))->select();
				$companyIds = array();
				foreach ($userServiceProviderResult as $key => $value) {
					$companyIds[] = $value['company_id'];
				}
				if ($companyIds) {
					if ($condition['sp.company_id']) {
						$condition['sp.company_id'] = array(array('in',$companyIds),$condition['sp.company_id'],'and');
					}else {
						$condition['sp.company_id'] = array('in',$companyIds);
					}
				}
			}*/
			if (!empty($data['person_name'])) {
				$condition['pb.person_name'] = array('like','%'.$data['person_name'].'%');
			}
			if (!empty($data['card_num'])) {
				$condition['pb.card_num'] = array('like','%'.$data['card_num'].'%');
			}
			if (!empty($data['company_name'])) {
				$condition['ci.company_name'] = array('like','%'.$data['company_name'].'%');
			}
			/*if (!empty($data['order_no'])) {
				$condition['po.order_no'] = array('like','%'.$data['order_no'].'%');
			}*/
			if ($condition['pii.operate_state']) {
				$condition['pii.operate_state'] = array($condition['pii.operate_state'],array('neq',-8),array('neq',-9),'and');
			}else {
				$condition['pii.operate_state'] = array(array('neq',-8),array('neq',-9),'and');
			}
			
			//客服只能看当前用户信息
			if (!empty($data['account_info'])) {
				if (3 == $data['account_info']['group']) {
					$adminId = getServiceAdminId($data['account_info']['user_id']);
					if ($adminId) {
						$userServiceProvider = D('UserServiceProvider');
						$userServiceProviderResultl = $userServiceProvider->field('GROUP_CONCAT(distinct user_id) as user_id')->where(['company_id'=>$data['account_info']['company_id'],'admin_id'=>$adminId,'state'=>1,'user_id'=>['gt',0]])->find();
						if ($userServiceProviderResultl['user_id']) {
							$condition['pii.user_id'] = ['in',$userServiceProviderResultl['user_id']];
						}else {
							return array();
						}
					}else {
						return array();
					}
				}
			}
			
			//dump($condition);
			
			$serviceProduct = M('ServiceProduct');
			$serviceProductResult = $serviceProduct->field(true)->where(array('company_id'=>$data['service_company_id']))->select();
			$productIdArray = array();
			if ($serviceProductResult) {
				foreach ($serviceProductResult as $key => $value) {
					$productIdArray[] = $value['id'];
				}
			}
			if ($productIdArray) {
				if ($condition['pii.product_id']) {
					$condition['pii.product_id'] = array($condition['pii.product_id'],array('in',$productIdArray));
				}else {
					$condition['pii.product_id'] = array('in',$productIdArray);
				}
				//$pageResult = $this->alias('pii')->field('pii.base_id,pii.handle_month')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.user_id=pii.user_id')->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=pii.pay_order_id')->join('left join '.C('DB_PREFIX').'user as u on u.id=pii.user_id')->where($condition)->group('pii.base_id, pii.handle_month')->select();
				$pageResult = $this->alias('pii')->field('pii.base_id,pii.handle_month')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.user_id=pii.user_id')->join('left join '.C('DB_PREFIX').'user as u on u.id=pii.user_id')->where($condition)->group('pii.base_id, pii.handle_month')->select();
				$pageCount = count($pageResult);
				$page = get_page($pageCount,$pageSize);
				
				/*for ($i=1; $i <= 2; $i++) { 
					$condition['payment_type'] = $i;
					$piiCount[$i] = $this->alias('pii')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.user_id=pii.user_id')->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=pii.pay_order_id')->join('left join '.C('DB_PREFIX').'user as u on u.id=pii.user_id')->where($condition)->count('pii.id');
				}
				$joinType = $piiCount[1]>=$piiCount[2]?'left':'right';*/
				
				$joinArray = [1=>'left',2=>'right'];
				for ($i=1; $i <= 2; $i++) { 
					$condition['payment_type'] = $i;
					//$piiSql[$i] = $this->alias('pii')->field('pii.*,pb.person_name,pb.card_num,pb.mobile,pb.audit,sp.name as product_name,ci.id as company_id,ci.company_name,po.order_no as pay_order_no,po.transaction_no as pay_transaction_no,po.pay_deadline as pay_deadline')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.user_id=pii.user_id')->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=pii.pay_order_id')->join('left join '.C('DB_PREFIX').'user as u on u.id=pii.user_id')->where($condition)->order('create_time desc ')->select(false);
					$piiSql[$i] = $this->alias('pii')->field('pii.*,pb.person_name,pb.card_num,pb.mobile,pb.audit,sp.name as product_name,ci.id as company_id,ci.company_name')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.user_id=pii.user_id')->join('left join '.C('DB_PREFIX').'user as u on u.id=pii.user_id')->where($condition)->order('create_time desc ')->select(false);
				}
				//$result = $this->field('IFNULL(socpii.base_id,propii.base_id) as base_id,IFNULL(socpii.user_id,propii.user_id) as user_id,IF(socpii.modify_time>=propii.modify_time or propii.modify_time is null,socpii.modify_time,propii.modify_time) as modify_time,IFNULL(socpii.person_name,propii.person_name) as person_name,IFNULL(socpii.card_num,propii.card_num) as card_num,IFNULL(socpii.audit,propii.audit) as audit,IFNULL(socpii.mobile,propii.mobile) as mobile,IFNULL(socpii.product_name,propii.product_name) as product_name,IFNULL(socpii.company_id,propii.company_id) as company_id,IFNULL(socpii.company_name,propii.company_name) as company_name,IFNULL(socpii.location,propii.location) as location,socpii.id as socpii_id,socpii.rule_id as socpii_rule_id,socpii.product_id as socpii_product_id,socpii.location as socpii_location,socpii.start_month as socpii_start_month,socpii.handle_month as socpii_handle_month,socpii.pay_date as socpii_pay_date,socpii.amount as socpii_amount,socpii.payment_info as socpii_payment_info,socpii.payment_type as socpii_payment_type,socpii.state as socpii_state,socpii.operate_state as socpii_operate_state,socpii.pay_order_id as socpii_pay_order_id,socpii.pay_order_no as socpii_pay_order_no,socpii.pay_transaction_no as socpii_pay_transaction_no,socpii.pay_deadline as socpii_pay_deadline,socpii.remark as socpii_remark,socpii.create_time as socpii_create_time,propii.id as propii_id,propii.rule_id as propii_rule_id,propii.product_id as propii_product_id,propii.location as propii_location,propii.start_month as propii_start_month,propii.handle_month as propii_handle_month,propii.pay_date as propii_pay_date,propii.amount as propii_amount,propii.payment_info as propii_payment_info,propii.payment_type as propii_payment_type,propii.state as propii_state,propii.operate_state as propii_operate_state,propii.pay_order_id as propii_pay_order_id,propii.pay_order_no as propii_pay_order_no,propii.pay_transaction_no as propii_pay_transaction_no,propii.pay_deadline as propii_pay_deadline,propii.remark as propii_remark,propii.create_time as propii_create_time')->table($piiSql[1].' as socpii')->join($joinType.' join '.$piiSql[2].' as propii on socpii.base_id = propii.base_id and socpii.handle_month = propii.handle_month')->limit($page->firstRow,$page->listRows)->order('modify_time desc ')->select();
				$piiUnionSql = array();
				for ($i=1; $i <= 2 ; $i++) { 
					//$piiUnionSql[$i] = $this->field('IFNULL(socpii.base_id,propii.base_id) as base_id,IFNULL(socpii.user_id,propii.user_id) as user_id,IF(socpii.modify_time>=propii.modify_time or propii.modify_time is null,socpii.modify_time,propii.modify_time) as modify_time,IFNULL(socpii.person_name,propii.person_name) as person_name,IFNULL(socpii.card_num,propii.card_num) as card_num,IFNULL(socpii.audit,propii.audit) as audit,IFNULL(socpii.mobile,propii.mobile) as mobile,IFNULL(socpii.product_name,propii.product_name) as product_name,IFNULL(socpii.company_id,propii.company_id) as company_id,IFNULL(socpii.company_name,propii.company_name) as company_name,IFNULL(socpii.location,propii.location) as location,socpii.id as socpii_id,socpii.rule_id as socpii_rule_id,socpii.product_id as socpii_product_id,socpii.location as socpii_location,socpii.start_month as socpii_start_month,socpii.handle_month as socpii_handle_month,socpii.pay_date as socpii_pay_date,socpii.amount as socpii_amount,socpii.payment_info as socpii_payment_info,socpii.payment_type as socpii_payment_type,socpii.state as socpii_state,socpii.operate_state as socpii_operate_state,socpii.pay_order_id as socpii_pay_order_id,socpii.pay_order_no as socpii_pay_order_no,socpii.pay_transaction_no as socpii_pay_transaction_no,socpii.pay_deadline as socpii_pay_deadline,socpii.remark as socpii_remark,socpii.create_time as socpii_create_time,propii.id as propii_id,propii.rule_id as propii_rule_id,propii.product_id as propii_product_id,propii.location as propii_location,propii.start_month as propii_start_month,propii.handle_month as propii_handle_month,propii.pay_date as propii_pay_date,propii.amount as propii_amount,propii.payment_info as propii_payment_info,propii.payment_type as propii_payment_type,propii.state as propii_state,propii.operate_state as propii_operate_state,propii.pay_order_id as propii_pay_order_id,propii.pay_order_no as propii_pay_order_no,propii.pay_transaction_no as propii_pay_transaction_no,propii.pay_deadline as propii_pay_deadline,propii.remark as propii_remark,propii.create_time as propii_create_time')->table($piiSql[1].' as socpii')->join($joinArray[$i].' join '.$piiSql[2].' as propii on socpii.base_id = propii.base_id and socpii.handle_month = propii.handle_month')->order('modify_time desc ')->select(false);
					$piiUnionSql[$i] = $this->field('IFNULL(socpii.base_id,propii.base_id) as base_id,IFNULL(socpii.user_id,propii.user_id) as user_id,IF(socpii.modify_time>=propii.modify_time or propii.modify_time is null,socpii.modify_time,propii.modify_time) as modify_time,IFNULL(socpii.person_name,propii.person_name) as person_name,IFNULL(socpii.card_num,propii.card_num) as card_num,IFNULL(socpii.audit,propii.audit) as audit,IFNULL(socpii.mobile,propii.mobile) as mobile,IFNULL(socpii.product_name,propii.product_name) as product_name,IFNULL(socpii.company_id,propii.company_id) as company_id,IFNULL(socpii.company_name,propii.company_name) as company_name,IFNULL(socpii.location,propii.location) as location,socpii.id as socpii_id,socpii.rule_id as socpii_rule_id,socpii.product_id as socpii_product_id,socpii.location as socpii_location,socpii.start_month as socpii_start_month,socpii.handle_month as socpii_handle_month,socpii.pay_date as socpii_pay_date,socpii.amount as socpii_amount,socpii.payment_info as socpii_payment_info,socpii.payment_type as socpii_payment_type,socpii.state as socpii_state,socpii.operate_state as socpii_operate_state,socpii.remark as socpii_remark,socpii.create_time as socpii_create_time,propii.id as propii_id,propii.rule_id as propii_rule_id,propii.product_id as propii_product_id,propii.location as propii_location,propii.start_month as propii_start_month,propii.handle_month as propii_handle_month,propii.pay_date as propii_pay_date,propii.amount as propii_amount,propii.payment_info as propii_payment_info,propii.payment_type as propii_payment_type,propii.state as propii_state,propii.operate_state as propii_operate_state,propii.remark as propii_remark,propii.create_time as propii_create_time')->table($piiSql[1].' as socpii')->join($joinArray[$i].' join '.$piiSql[2].' as propii on socpii.base_id = propii.base_id and socpii.handle_month = propii.handle_month')->order('modify_time desc ')->select(false);
				}
				//$result = $this->field(true)->table($piiUnionSql[1].' as piia')->union($piiUnionSql[2].'')->limit($page->firstRow,$page->listRows)->select();
				//$result = $this->table($piiUnionSql[1].' as piia')->union($piiUnionSql[2].'')->select();
				
				$piiUnionSql = $this->table($piiUnionSql[1].' as piia')->union($piiUnionSql[2].'')->select(false);
				$result = $this->table($piiUnionSql.'as pii')->limit($page->firstRow,$page->listRows)->select();
				//dump($piiUnionSql);
				//dump($this->_sql());
				
				if ($result) {
					$template = D('Template');
					foreach ($result as $key => $value) {
						//$result[$key]['socpiiLocationValue'] = showAreaName($value['socpii_location']);
						//$result[$key]['propiiLocationValue'] = showAreaName($value['propii_location']);
						//$result[$key]['socpiiPaymentDeadline'] = get_deadline($value['socpii_payment_deadline']);
						//$result[$key]['propiiPaymentDeadline'] = get_deadline($value['propii_payment_deadline']);
						$result[$key]['socpiiPayDeadline'] = get_deadline($value['socpii_pay_deadline']);
						$result[$key]['propiiPayDeadline'] = get_deadline($value['propii_pay_deadline']);
						$result[$key]['locationValue'] = showAreaName($value['location']);
						$result[$key]['count'] = ($value['socpii_id']?1:0)+($value['propii_id']?1:0);
						
						/*if ($value['location']) {
							$location = ($value['location']/1000<<0)*1000;
							$templateResult = $template->getTemplateByCondition(array('location'=>$location+100));
							if ($templateResult && $templateResult['soc_deadline']) {
								$handleMonth = $value['socpii_handle_month'];
								$payDeadline = date('Y-m-d',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(int_to_date($handleMonth,'-').'-'.sprintf('%02d',$templateResult['soc_deadline']))));
								$result[$key]['payDeadline'] = $payDeadline;
								$result[$key]['whetherToOperate'] = time() <= strtotime($payDeadline)+(in_array($type,[3,4])?C('INSURANCE_HANDLE_DAYS')*86400:0);
							}
						}*/
						
						if ($result[$key]['socpii_rule_id']) {
							$templateRule = D('TemplateRule');
							$templateRuleResult = $templateRule->field(true)->getById($result[$key]['socpii_rule_id']);
							if ($templateRuleResult && $templateRuleResult['deadline']) {
								$handleMonth = $value['socpii_handle_month'];
								$payDeadline = date('Y-m-d',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(int_to_date($handleMonth,'-').'-'.sprintf('%02d',$templateRuleResult['deadline']))));
								$result[$key]['payDeadline'] = $payDeadline;
								$result[$key]['whetherToOperate'] = time() <= strtotime($payDeadline)+(in_array($type,[3,4])?C('INSURANCE_HANDLE_DAYS')*86400:0);
								//$result[$key]['whetherToOperate'] = time() <= strtotime($payDeadline)+($result['socpii_operate_state']>=2 || $result['propii_operate_state']>=2?C('INSURANCE_HANDLE_DAYS')*86400:0);
							}
						}
					}
				}
				
				if ($result || null === $result) {
					return array('data'=>$result,'page'=>$page->show(),'count'=>$this->getInsuranceCount($condition['pii.user_id'],$data['user_type'],$productIdArray));
				}else if (false === $result) {
					wlog($this->getDbError());
					$this->error = '系统内部错误！';
					return false;
				}else {
					$this->error = '未知错误！';
					return false;
				}
			}else {
				return array();
				$this->error = '产品数据错误！';
				return false;
			}
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}
	
	/**
	 * getInsuranceDetailListByCondition function
	 * 根据条件获取参保订单列表
	 * param array $data 条件数组
	 * param int $type 类型 0待审核，1已审核，2待付款，3待办理，4已办理
	 * param int $pageSize 分页大小，默认10
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getInsuranceDetailListByCondition($data,$type,$pageSize = 10){
		if (is_array($data)) {
			$condition = array();
			if (!empty($data['user_type'])) {
				$condition['u.type'] = $data['user_type'];
			}
			if ('' !== $type) {
				/*if ('0' == $type) {
					$condition['pii.operate_state'] = array('eq',0);
					$condition['pii.state'] = array('neq','0');
				}else if ('1' == $type) {
					$condition['pii.operate_state'] = array('in','1,-1');
				}else if ('2' == $type) {
					$condition['pii.operate_state'] = 1;
					//$condition['pii.pay_order_id'] = array('gt',0);
				}else if ('3' == $type) {
					$condition['pii.operate_state'] = 2;
				}else if ('4' == $type) {
					$condition['pii.operate_state'] = 3;
				}*/
				if ('0' == $type) {
					$condition['pii.operate_state'] = array('eq',0);
					$condition['pii.state'] = array('neq','0');
				}else if ('1' == $type) {
					$condition['pii.operate_state'] = array('in','1,-1');
				}else if ('2' == $type) {
					$condition['sid.state'] = 1;
				}else if ('3' == $type) {
					$condition['sid.state'] = 2;
				}else if ('4' == $type) {
					$condition['sid.state'] = array('in','3,-3,-4');
				}
			}
			if (!empty($data['user_id'])) {
				$condition['pb.user_id'] = $data['user_id'];
			}
			if (!empty($data['product_id'])) {
				$condition['pii.product_id'] = $data['product_id'];
			}
			if ('' !== $data['state'] && null !== $data['state']) {
				//$condition['pii.state'] = $data['state'];
				if ($condition['pii.state']) {
					$condition['pii.state'] = array($condition['pii.state'],$data['state'],'and');
				}else {
					$condition['pii.state'] = $data['state'];
				}
			}
			if (!empty($data['handle_month'])) {
				$condition['pii.handle_month'] = array('eq',$data['handle_month']);
			}
			if (!empty($data['start_time'])) {
				$condition['pii.create_time'] = array('egt',$data['start_time']);
			}
			if (!empty($data['start_time'])) {
				$condition['pii.create_time'] = array('elt',$data['end_time']);
			}
			if (!empty($data['company_id'])) {
				$condition['sp.company_id'] = array('eq',$data['company_id']);
			}
			/*if (!empty($data['admin_id'])) {
				$userServiceProvider = D('UserServiceProvider');
				$userServiceProviderResult = $userServiceProvider->field('company_id')->where(array('user_id'=>$data['user_id'],'admin_id'=>$data['admin_id'],'state'=>1))->select();
				$companyIds = array();
				foreach ($userServiceProviderResult as $key => $value) {
					$companyIds[] = $value['company_id'];
				}
				if ($companyIds) {
					if ($condition['sp.company_id']) {
						$condition['sp.company_id'] = array(array('in',$companyIds),$condition['sp.company_id'],'and');
					}else {
						$condition['sp.company_id'] = array('in',$companyIds);
					}
				}
			}*/
			if (!empty($data['person_name'])) {
				$condition['pb.person_name'] = array('like','%'.$data['person_name'].'%');
			}
			if (!empty($data['card_num'])) {
				$condition['pb.card_num'] = array('like','%'.$data['card_num'].'%');
			}
			if (!empty($data['company_name'])) {
				$condition['ci.company_name'] = array('like','%'.$data['company_name'].'%');
			}
			if (!empty($data['order_no'])) {
				$condition['po.order_no'] = array('like','%'.$data['order_no'].'%');
			}
			if ($condition['pii.operate_state']) {
				$condition['pii.operate_state'] = array($condition['pii.operate_state'],array('neq',-8),array('neq',-9),'and');
			}else {
				$condition['pii.operate_state'] = array(array('neq',-8),array('neq',-9),'and');
			}
			
			//客服只能看当前用户信息
			if (!empty($data['account_info'])) {
				if (3 == $data['account_info']['group']) {
					$adminId = getServiceAdminId($data['account_info']['user_id']);
					if ($adminId) {
						$userServiceProvider = D('UserServiceProvider');
						$userServiceProviderResultl = $userServiceProvider->field('GROUP_CONCAT(distinct user_id) as user_id')->where(['company_id'=>$data['account_info']['company_id'],'admin_id'=>$adminId,'state'=>1,'user_id'=>['gt',0]])->find();
						if ($userServiceProviderResultl['user_id']) {
							$condition['pii.user_id'] = ['in',$userServiceProviderResultl['user_id']];
						}else {
							return array();
						}
					}else {
						return array();
					}
				}
			}
			
			//dump($condition);
			
			$serviceProduct = M('ServiceProduct');
			$serviceProductResult = $serviceProduct->field(true)->where(array('company_id'=>$data['service_company_id']))->select();
			$productIdArray = array();
			if ($serviceProductResult) {
				foreach ($serviceProductResult as $key => $value) {
					$productIdArray[] = $value['id'];
				}
			}
			if ($productIdArray) {
				if ($condition['pii.product_id']) {
					$condition['pii.product_id'] = array($condition['pii.product_id'],array('in',$productIdArray));
				}else {
					$condition['pii.product_id'] = array('in',$productIdArray);
				}
				//$pageResult = $this->alias('pii')->field('pii.base_id,pii.handle_month')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.user_id=pii.user_id')->join('left join '.C('DB_PREFIX').'user as u on u.id=pii.user_id')->where($condition)->group('pii.base_id, pii.handle_month')->select();
				$pageResult = $this->alias('pii')->field('pii.base_id,pii.handle_month,sid.pay_date')->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_info_id = pii.id')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.user_id=pii.user_id')->join('left join '.C('DB_PREFIX').'user as u on u.id=pii.user_id')->where($condition)->group('pii.base_id, pii.handle_month,sid.pay_date')->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=sid.pay_order_id')->select();
				$pageCount = count($pageResult);
				$page = get_page($pageCount,$pageSize);
				
				$joinArray = [1=>'left',2=>'right'];
				for ($i=1; $i <= 2; $i++) { 
					$condition['pii.payment_type'] = $i;
					$piiSql[$i] = $this->alias('pii')->field('pii.*,sid.id as sid_id,sid.pay_date as sid_pay_date,sid.state as sid_state,pb.person_name,pb.card_num,pb.mobile,pb.audit,sp.name as product_name,ci.id as company_id,ci.company_name,po.order_no as pay_order_no,po.transaction_no as pay_transaction_no,po.pay_deadline as pay_deadline')->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_info_id = pii.id')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.user_id=pii.user_id')->join('left join '.C('DB_PREFIX').'user as u on u.id=pii.user_id')->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=sid.pay_order_id')->where($condition)->order('create_time desc ')->select(false);
				}
				$piiUnionSql = array();
				for ($i=1; $i <= 2 ; $i++) { 
					$piiUnionSql[$i] = $this->field('IFNULL(socpii.base_id,propii.base_id) as base_id,IFNULL(socpii.user_id,propii.user_id) as user_id,IF(socpii.modify_time>=propii.modify_time or propii.modify_time is null,socpii.modify_time,propii.modify_time) as modify_time,IFNULL(socpii.person_name,propii.person_name) as person_name,IFNULL(socpii.card_num,propii.card_num) as card_num,IFNULL(socpii.audit,propii.audit) as audit,IFNULL(socpii.mobile,propii.mobile) as mobile,IFNULL(socpii.product_name,propii.product_name) as product_name,IFNULL(socpii.company_id,propii.company_id) as company_id,IFNULL(socpii.company_name,propii.company_name) as company_name,IFNULL(socpii.location,propii.location) as location,socpii.id as socpii_id,socpii.rule_id as socpii_rule_id,socpii.product_id as socpii_product_id,socpii.location as socpii_location,socpii.start_month as socpii_start_month,socpii.handle_month as socpii_handle_month,socpii.pay_date as socpii_pay_date,socpii.amount as socpii_amount,socpii.payment_info as socpii_payment_info,socpii.payment_type as socpii_payment_type,socpii.state as socpii_state,socpii.operate_state as socpii_operate_state,socpii.sid_id as socpii_sid_id,socpii.sid_pay_date as socpii_sid_pay_date,socpii.sid_state as socpii_sid_state,socpii.pay_order_id as socpii_pay_order_id,socpii.pay_order_no as socpii_pay_order_no,socpii.pay_transaction_no as socpii_pay_transaction_no,socpii.pay_deadline as socpii_pay_deadline,socpii.remark as socpii_remark,socpii.create_time as socpii_create_time,propii.id as propii_id,propii.rule_id as propii_rule_id,propii.product_id as propii_product_id,propii.location as propii_location,propii.start_month as propii_start_month,propii.handle_month as propii_handle_month,propii.pay_date as propii_pay_date,propii.amount as propii_amount,propii.payment_info as propii_payment_info,propii.payment_type as propii_payment_type,propii.state as propii_state,propii.operate_state as propii_operate_state,propii.sid_id as propii_sid_id,propii.sid_pay_date as propii_sid_pay_date,propii.sid_state as propii_sid_state,propii.pay_order_id as propii_pay_order_id,propii.pay_order_no as propii_pay_order_no,propii.pay_transaction_no as propii_pay_transaction_no,propii.pay_deadline as propii_pay_deadline,propii.remark as propii_remark,propii.create_time as propii_create_time')->table($piiSql[1].' as socpii')->join($joinArray[$i].' join '.$piiSql[2].' as propii on socpii.base_id = propii.base_id and socpii.handle_month = propii.handle_month and socpii.sid_pay_date = propii.sid_pay_date')->order('modify_time desc ')->select(false);
				}
				//$result = $this->field(true)->table($piiUnionSql[1].' as piia')->union($piiUnionSql[2].'')->limit($page->firstRow,$page->listRows)->select();
				//$result = $this->table($piiUnionSql[1].' as piia')->union($piiUnionSql[2].'')->select();
				
				$piiUnionSql = $this->table($piiUnionSql[1].' as piia')->union($piiUnionSql[2].'')->select(false);
				$result = $this->table($piiUnionSql.'as pii')->limit($page->firstRow,$page->listRows)->select();
				//dump($piiUnionSql);
				//dump($this->_sql());
				
				if ($result) {
					//$template = D('Template');
					foreach ($result as $key => $value) {
						//$result[$key]['socpiiLocationValue'] = showAreaName($value['socpii_location']);
						//$result[$key]['propiiLocationValue'] = showAreaName($value['propii_location']);
						//$result[$key]['socpiiPaymentDeadline'] = get_deadline($value['socpii_payment_deadline']);
						//$result[$key]['propiiPaymentDeadline'] = get_deadline($value['propii_payment_deadline']);
						$result[$key]['socpiiPayDeadline'] = get_deadline($value['socpii_pay_deadline']);
						$result[$key]['propiiPayDeadline'] = get_deadline($value['propii_pay_deadline']);
						$result[$key]['locationValue'] = showAreaName($value['location']);
						$result[$key]['count'] = ($value['socpii_id']?1:0)+($value['propii_id']?1:0);
						
						/*if ($value['location']) {
							$location = ($value['location']/1000<<0)*1000;
							$templateResult = $template->getTemplateByCondition(array('location'=>$location+100));
							if ($templateResult && $templateResult['soc_deadline']) {
								$handleMonth = $value['socpii_handle_month'];
								$payDeadline = date('Y-m-d',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(int_to_date($handleMonth,'-').'-'.sprintf('%02d',$templateResult['soc_deadline']))));
								$result[$key]['payDeadline'] = $payDeadline;
								$result[$key]['whetherToOperate'] = time() <= strtotime($payDeadline)+(in_array($type,[3,4])?C('INSURANCE_HANDLE_DAYS')*86400:0);
							}
						}*/
						if ($result[$key]['socpii_rule_id']) {
							$templateRule = D('TemplateRule');
							$templateRuleResult = $templateRule->field(true)->getById($result[$key]['socpii_rule_id']);
							if ($templateRuleResult && $templateRuleResult['deadline']) {
								$handleMonth = $value['socpii_handle_month'];
								$payDeadline = date('Y-m-d',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(int_to_date($handleMonth,'-').'-'.sprintf('%02d',$templateRuleResult['deadline']))));
								$result[$key]['payDeadline'] = $payDeadline;
								$result[$key]['whetherToOperate'] = time() <= strtotime($payDeadline)+(in_array($type,[3,4])?C('INSURANCE_HANDLE_DAYS')*86400:0);
								//$result[$key]['whetherToOperate'] = time() <= strtotime($payDeadline)+($result['socpii_operate_state']>=2 || $result['propii_operate_state']>=2?C('INSURANCE_HANDLE_DAYS')*86400:0);
							}
						}
					}
					
				}
				
				if ($result || null === $result) {
					return array('data'=>$result,'page'=>$page->show(),'count'=>$this->getInsuranceCount($condition['pii.user_id'],$data['user_type'],$productIdArray));
				}else if (false === $result) {
					wlog($this->getDbError());
					$this->error = '系统内部错误！';
					return false;
				}else {
					$this->error = '未知错误！';
					return false;
				}
			}else {
				return array();
				$this->error = '产品数据错误！';
				return false;
			}
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}
	
	/**
	 * getPersonInsuranceInfoByHandleMonth function
	 * 根据办理年月条件获取服务订单详情
	 * param array $data 条件数组
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getPersonInsuranceInfoByHandleMonth($data){
		if (is_array($data)) {
			$result = array();
			if ($data['handle_month']) {
				$result[1] = $this->alias('pii')->field(true)->where(array('pii.user_id'=>$data['user_id'],'pii.base_id'=>$data['base_id'],'pii.payment_type'=>1,'pii.handle_month'=>$data['handle_month']))->order('pii.create_time desc, pii.id desc')->find();
				$result[2] = $this->alias('pii')->field(true)->where(array('pii.user_id'=>$data['user_id'],'pii.base_id'=>$data['base_id'],'pii.payment_type'=>2,'pii.handle_month'=>$data['handle_month']))->order('pii.create_time desc, pii.id desc')->find();
				//$result[1] = $this->alias('pii')->field('pii.*,po.order_no as po_order_no, po.transaction_no as po_transaction_no, po.state as po_state')->join('left join '.C('DB_PREFIX').'pay_order as po on po.id = pii.pay_order_id')->where(array('pii.user_id'=>$data['user_id'],'pii.base_id'=>$data['base_id'],'pii.payment_type'=>1,'pii.handle_month'=>$data['handle_month']))->order('pii.create_time desc, pii.id desc')->find();
				//$result[2] = $this->alias('pii')->field('pii.*,po.order_no as po_order_no, po.transaction_no as po_transaction_no, po.state as po_state')->join('left join '.C('DB_PREFIX').'pay_order as po on po.id = pii.pay_order_id')->where(array('pii.user_id'=>$data['user_id'],'pii.base_id'=>$data['base_id'],'pii.payment_type'=>2,'pii.handle_month'=>$data['handle_month']))->order('pii.create_time desc, pii.id desc')->find();
			}else {
				$result[1] = $this->alias('pii')->field(true)->where(array('pii.user_id'=>$data['user_id'],'pii.base_id'=>$data['base_id'],'pii.payment_type'=>1))->order('pii.handle_month desc, pii.create_time desc, pii.id desc')->find();
				$result[2] = $this->alias('pii')->field(true)->where(array('pii.user_id'=>$data['user_id'],'pii.base_id'=>$data['base_id'],'pii.payment_type'=>2))->order('pii.handle_month desc, pii.create_time desc, pii.id desc')->find();
			}
			
			if ($result) {
				/*$template = D('Template');
				foreach ($result as $key => $value) {
					if ($value['location']) {
						$location = ($value['location']/1000<<0)*1000;
						$templateResult = $template->getTemplateByCondition(array('location'=>$location+100));
						//dump($templateResult);
						if ($templateResult && $templateResult['soc_deadline']) {
							$handleMonth = $value['handle_month'];
							$payDeadline = date('Y-m-d',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(int_to_date($handleMonth,'-').'-'.sprintf('%02d',$templateResult['soc_deadline']))));
							$result[$key]['payDeadline'] = $payDeadline;
							$result[$key]['whetherToOperate'] = time() <= strtotime($payDeadline)+($result[1]['operate_state']>=2 || $result[2]['operate_state']>=2?C('INSURANCE_HANDLE_DAYS')*86400:0);
						}
					}
				}*/
				if ($result[1]['rule_id']) {
					$templateRule = D('TemplateRule');
					$templateRuleResult = $templateRule->field(true)->getById($result[1]['rule_id']);
					if ($templateRuleResult && $templateRuleResult['deadline']) {
						$handleMonth = $result[1]['handle_month'];
						$payDeadline = date('Y-m-d',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(int_to_date($handleMonth,'-').'-'.sprintf('%02d',$templateRuleResult['deadline']))));
						$result[1]['payDeadline'] = $result[2]['payDeadline'] = $payDeadline;
						$result[1]['whetherToOperate'] = $result[2]['whetherToOperate'] = time() <= strtotime($payDeadline)+($result[1]['operate_state']>=2 || $result[2]['operate_state']>=2?C('INSURANCE_HANDLE_DAYS')*86400:0);
					}
				}
			}
			
			if ($result || null === $result) {
				return $result;
			}else if (false === $result) {
				wlog($this->getDbError());
				$this->error = '系统内部错误！';
				return false;
			}else {
				$this->error = '未知错误！';
				return false;
			}
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}
	
	/**
	 * getInsurancePayDateDetailByCondition function
	 * 根据条件获取个人参保信息
	 * param array $data 条件数组
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getInsurancePayDateDetailByCondition($data){
		if (is_array($data)) {
			$condition = array('pii.base_id'=>$data['base_id'],'pii.user_id'=>$data['user_id'],'sid.state'=>array('not in',array(-8,-9)));
			$data['pay_date'] && $condition['sid.pay_date'] = $data['pay_date'];
			$data['pii_id'] && $condition['pii.id'] = $data['pii_id'];
			
			$pii = $this->alias('pii')
					->field('pii.rule_id as pii_rule_id,pii.handle_month as pii_handle_month,pii.location as pii_location,pii.template_location as pii_template_location,pii.payment_info as pii_payment_info,pii.payment_type as pii_payment_type,pb.person_name as pb_person_name,pb.card_num as pb_card_num,pb.residence_location as pb_residence_location,pb.residence_type as pb_residence_type,pb.mobile as pb_mobile,pb.gender as pb_gender,tr.template_id as tr_template_id,tr.company_id as tr_company_id,tr.name as tr_name,tr.classify_mixed as tr_classify_mixed,tr.rule as tr_rule,sid.id as sid_id,sid.type as sid_type,sid.price as sid_price,sid.service_price as sid_service_price,sid.amount as sid_amount,sid.pay_date as sid_pay_date,sid.payment_info as sid_payment_info,sid.note as sid_note,sid.state as sid_state,sid.is_hang_up as sid_is_hang_up,sid.replenish as sid_replenish,sid.insurance_detail as sid_insurance_detail,sid.create_time as sid_create_time,spo.id as spo_id,spo.product_id as spo_product_id,spo.price as spo_price,spo.modify_price as spo_modify_price,po.order_no as po_order_no,po.transaction_no as po_transaction_no,po.state as po_state,wl.soc_service_price as wl_soc_service_price,wl.pro_service_price as wl_pro_service_price,wl.af_service_price as wl_af_service_price')
					->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')
					->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_info_id = pii.id')
					->join('left join '.C('DB_PREFIX').'template_rule as tr on tr.id = pii.rule_id')
					->join('left join '.C('DB_PREFIX').'service_product_order as spo on spo.product_id = pii.product_id and spo.service_state = 2 and spo.user_id = '.$data['user_id'])
					//->join('left join '.C('DB_PREFIX').'service_product as sp on spo.product_id = sp.id')
					->join('left join '.C('DB_PREFIX').'pay_order as po on po.id = sid.pay_order_id')
					->join('left join '.C('DB_PREFIX').'warranty_location as wl on wl.service_product_order_id = spo.id and wl.location = pii.location')
					->where($condition)->order('sid.pay_date desc, sid.create_time asc')->select();
			if ($pii) {
				$result = array();
				foreach ($pii as $key => $value) {
					//服务费
					$calculateResult = json_decode($value['sid_insurance_detail'],true);
					//$value['wl_ss_service_price'] = $value['sid_service_price'];
					$value['sidTypeValue'] = get_code_value($value['sid_type'],'ServiceInsuranceDetailType');
					$value['sidStateValue'] = get_code_value($value['sid_state'],'ServiceInsuranceDetailState',$value['sid_is_hang_up']);
					$value['sid_payment_info'] = json_decode($value['sid_payment_info'],true);
					$value['pbResidenceTypeValue'] = get_code_value($value['pb_residence_type'],'PersonBaseResidenceType');
					$value['piiLocationValue'] = showAreaName($value['pii_location']);
					$value['piiHandleMonthValue'] = int_to_date($value['pii_handle_month']);
					$value['sidCreateTimeValue'] = substr($value['sid_create_time'],0,16);
					$value['calculateResult'] = $calculateResult;
					
					$result[$value['sid_pay_date']][$value['pii_payment_type']] = $value;
				}
			}
			
			if ($result || null === $result) {
				return $result;
			}else if (false === $result) {
				wlog($this->getDbError());
				$this->error = '系统内部错误！';
				return false;
			}else {
				$this->error = '未知错误！';
				return false;
			}
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}
	
	/**
	 * getInsurancePayDateDetailByPiiId function
	 * 根据PiiId获取个人参保信息
	 * param array $data 条件数组
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getInsurancePayDateDetailByPiiId($data){
		if (is_array($data)) {
			//$condition = array('pii.base_id'=>$data['base_id'],'pii.user_id'=>$data['user_id'],'sid.pay_date'=>$data['pay_date']);
			$condition = array('pii.base_id'=>$data['base_id'],'pii.user_id'=>$data['user_id'],'sid.state'=>array('not in',array(-8,-9)));
			$data['pay_date'] && $condition['sid.pay_date'] = $data['pay_date'];
			$data['pii_id'] && $condition['pii.id'] = $data['pii_id'];
			
			$pii = $this->alias('pii')
					->field('pii.rule_id as pii_rule_id,pii.handle_month as pii_handle_month,pii.location as pii_location,pii.template_location as pii_template_location,pii.payment_info as pii_payment_info,pii.payment_type as pii_payment_type,pb.person_name as pb_person_name,pb.card_num as pb_card_num,pb.residence_location as pb_residence_location,pb.residence_type as pb_residence_type,pb.mobile as pb_mobile,pb.gender as pb_gender,tr.template_id as tr_template_id,tr.company_id as tr_company_id,tr.name as tr_name,tr.classify_mixed as tr_classify_mixed,tr.rule as tr_rule,sid.id as sid_id,sid.type as sid_type,sid.price as sid_price,sid.service_price as sid_service_price,sid.amount as sid_amount,sid.pay_date as sid_pay_date,sid.payment_info as sid_payment_info,sid.note as sid_note,sid.state as sid_state,sid.is_hang_up as sid_is_hang_up,sid.replenish as sid_replenish,sid.insurance_detail as sid_insurance_detail,sid.create_time as sid_create_time,spo.id as spo_id,spo.product_id as spo_product_id,spo.price as spo_price,spo.modify_price as spo_modify_price,po.order_no as po_order_no,po.transaction_no as po_transaction_no,po.state as po_state,wl.soc_service_price as wl_soc_service_price,wl.pro_service_price as wl_pro_service_price,wl.af_service_price as wl_af_service_price')
					->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')
					->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_info_id = pii.id')
					->join('left join '.C('DB_PREFIX').'template_rule as tr on tr.id = pii.rule_id')
					->join('left join '.C('DB_PREFIX').'service_product_order as spo on spo.product_id = pii.product_id and spo.service_state = 2 and spo.user_id = '.$data['user_id'])
					//->join('left join '.C('DB_PREFIX').'service_product as sp on spo.product_id = sp.id')
					->join('left join '.C('DB_PREFIX').'pay_order as po on po.id = sid.pay_order_id')
					->join('left join '.C('DB_PREFIX').'warranty_location as wl on wl.service_product_order_id = spo.id and wl.location = pii.location')
					->where($condition)->order('sid.pay_date desc, sid.create_time asc')->select();
			if ($pii) {
				//dump($this->_sql());
				//dump($pii);
				$result = array();
				$result['servicePrice'] = 0;
				$result['companyCost'] = 0;
				$result['personCost'] = 0;
				$result['totalCost'] = 0;
				$result['rule_id'] = array();
				$result['monthNum'] = 0;
				$diffCron = D('DiffCron');
				foreach ($pii as $key => $value) {
					//服务费
					$calculateResult = json_decode($value['sid_insurance_detail'],true);
					if ($value['sid_pay_date'] && $calculateResult) {
						//$value['wl_ss_service_price'] = $value['sid_service_price'];
						/*$value['sidTypeValue'] = get_code_value($value['sid_type'],'ServiceInsuranceDetailType');
						$value['sidStateValue'] = get_code_value($value['sid_state'],'ServiceInsuranceDetailState',$value['sid_is_hang_up']);
						$value['sid_payment_info'] = json_decode($value['sid_payment_info'],true);
						$value['pbResidenceTypeValue'] = get_code_value($value['pb_residence_type'],'PersonBaseResidenceType');
						$value['piiLocationValue'] = showAreaName($value['pii_location']);
						$value['piiHandleMonthValue'] = int_to_date($value['pii_handle_month']);
						$value['sidCreateTimeValue'] = substr($value['sid_create_time'],0,16);
						$value['calculateResult'] = $calculateResult;
						$result[$value['sid_pay_date']][$value['pii_payment_type']] = $value;*/
						$result['rule_id'][$value['pii_payment_type']] = $value['pii_rule_id'];
						//$result['servicePrice'] += $value['sid_service_price'];
						//$result['companyCost'] += $calculateResult['company'];
						//$result['personCost'] += $calculateResult['person'];
						
						//查询缴纳异常状态
						$diffCronResult = $diffCron->field(true)->where(array('detail_id'=>$value['sid_id'],'type'=>3))->find();
						$diffCronArray = array();
						if ($diffCronResult) {
							$diffCronResult['message_body'] = json_decode($diffCronResult['message_body'],true);
							if ($diffCronResult['message_body']) {
								foreach ($diffCronResult['message_body'] as $kk => $vv) {
									$diffCronArray[$vv['name']] = $vv;
								}
							}
						}
						if ($diffCronArray) {
							foreach ($calculateResult['items'] as $kk => $vv) {
								$calculateResult['items'][$kk]['company']['handle_result'] = (is_null($diffCronArray[$vv['name']]['company'])?true:$diffCronArray[$vv['name']]['company']);
								$calculateResult['items'][$kk]['person']['handle_result'] = (is_null($diffCronArray[$vv['name']]['person'])?true:$diffCronArray[$vv['name']]['person']);
							}
						}else {
							foreach ($calculateResult['items'] as $kk => $vv) {
								$calculateResult['items'][$kk]['company']['handle_result'] = true;
								$calculateResult['items'][$kk]['person']['handle_result'] = true;
							}
						}
						
						$calculateData = array('state'=>0,'msg'=>'操作成功','sid_id'=>$value['sid_id'],'sid_state'=>$value['sid_state'],'sidStateValue'=>get_code_value($value['sid_state'],'ServiceInsuranceDetailState',$value['sid_is_hang_up']),'isHandleException'=>(in_array($value['sid_state'],array(3,-4)) && time()<=strtotime(date('Y-m-',strtotime('+1 month',strtotime(int_to_date($value['pii_handle_month'],'-')))).'20')),'sid_service_price'=>$value['sid_service_price'],'company_cost'=>$calculateResult['company'],'person_cost'=>$calculateResult['person'],'data'=>$calculateResult);
						$result['data'][$value['sid_pay_date']][$value['pii_payment_type']] = $calculateData;

					}
				}
				foreach ($result['data'] as $k => $v) {
					foreach ($v as $kk => $vv) {
						$result['servicePrice'] += $vv['sid_service_price'];
						$result['companyCost'] += $vv['company_cost'];
						$result['personCost'] += $vv['person_cost'];
					}
				}
				$result['totalCost'] = $result['servicePrice'] + $result['companyCost'] + $result['personCost'];
				$result['monthNum'] = count($result['data']);
			}
			
			if ($result || null === $result) {
				return $result;
			}else if (false === $result) {
				wlog($this->getDbError());
				$this->error = '系统内部错误！';
				return false;
			}else {
				$this->error = '未知错误！';
				return false;
			}
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}
	
	/**
	 * getLastPersonInsuranceInfo function
	 * 根据最近一条参保信息
	 * param array $data 条件数组
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getLastPersonInsuranceInfo($data){
		if (is_array($data)) {
			$result = $this->field(true)->where(array('user_id'=>$data['user_id'],'base_id'=>$data['base_id'],'payment_type'=>$data['payment_type'],'id'=>array('neq',$data['id'])))->order('create_time desc')->find();
			
			if ($result || null === $result) {
				return $result;
			}else if (false === $result) {
				wlog($this->getDbError());
				$this->error = '系统内部错误！';
				return false;
			}else {
				$this->error = '未知错误！';
				return false;
			}
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}
	
	/**
	 * updatePersonInsuranceInfoByCondition function
	 * 根据条件更新参保订单
	 * @param array $data 条件数组
	 * @param array $type 类型 1审批成功 -1审批失败 3办理成功 -3办理失败
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function updatePersonInsuranceInfoByConditionOld($data,$type){
		if (is_array($data) && is_array($data['id']) && in_array($type,array(1,-1,3,-3))) {
			$serviceProduct = M('ServiceProduct');
			$serviceProductResult = $serviceProduct->field(true)->where(array('company_id'=>$data['service_company_id']))->select();
			$productIdArray = array();
			if ($serviceProductResult) {
				foreach ($serviceProductResult as $key => $value) {
					$productIdArray[] = $value['id'];
				}
			}
			if ($productIdArray) {
				$stateArray = array(-1=>array('in','0,-1,1'), 1=>array('in','0,-1,1'), -3=>array('in','2,-3,3'), 3=>array('in','2,-3,3'));
				$condition = array('id'=>array('in',$data['id']),'product_id'=>array('in',$productIdArray),'operate_state'=>$stateArray[$type],'state'=>array('in',array(1,2,3)));
				//$condition = array('id'=>array('in',$data['id']),'product_id'=>array('in',$productIdArray));
				if (-3 == $type) {
					$condition['state'] = array('in',array(1,2));
				}
				$personInsuranceInfoResult = $this->field(true)->where($condition)->order('id desc')->select();
				//dump($personInsuranceInfoResult);die;
				//if (count($data['id']) == count($personInsuranceInfoResult)) {
					$this->startTrans();
					$saveResult = $this->where($condition)->save(array('operate_state'=>(-3 == $type?3:$type),'remark'=>$data['remark'],'modify_time'=>date('Y-m-d H:i:s')));
					if (false !== $saveResult) {
						$template = D('Template');
						$serviceInsuranceDetail = D('ServiceInsuranceDetail');
						//dump($data['id']);
						$condition = array('insurance_info_id'=>array('in',$data['id']),'state'=>$stateArray[$type]);
						$serviceInsuranceDetailResult = $serviceInsuranceDetail->field(true)->where($condition)->order('insurance_info_id desc')->select();
						$serviceInsuranceDetailSaveResult = $serviceInsuranceDetail->where($condition)->save(array('state'=>$type,'note'=>$data['remark'],'modify_time'=>date('Y-m-d H:i:s')));
						//dump($serviceInsuranceDetailResult);
						if ($serviceInsuranceDetailResult && false !== $serviceInsuranceDetailSaveResult) {
							$personInsuranceInfoArray = array();
							foreach ($personInsuranceInfoResult as $key => $value) {
								$personInsuranceInfoArray['baseId'][$value['base_id']] = $value['base_id'];
								$personInsuranceInfoArray['payment_type_id'][$value['payment_type']][$value['id']] = $value['id'];
								if ($value['pay_order_id']) {
									$personInsuranceInfoArray['id'][$value['id']] = $value['id'];
									$personInsuranceInfoArray['pay_order_id'][$value['pay_order_id']] = $value['pay_order_id'];
									//$personInsuranceInfoArray['pay_order_id_piiid'][$value['pay_order_id']][] = $value['id'];
								}
							}
							//dump($personInsuranceInfoArray);
							$serviceInsuranceDetailArray = array();
							foreach ($serviceInsuranceDetailResult as $key => $value) {
								$serviceInsuranceDetailArray[$value['insurance_info_id']]['data'][$value['id']] = $value;
								//计算明细费用
								$serviceInsuranceDetailArray[$value['insurance_info_id']]['amount'] += ($value['price']+$value['service_price']);
							}
							$payOrder = D('PayOrder');
							switch ($type) {
								case -1:
									if ($personInsuranceInfoArray['id']) {
										$personInsuranceInfoSaveResult = $this->where(array('id'=>array('in',$personInsuranceInfoArray['id'])))->save(array('pay_order_id'=>0,'modify_time'=>date('Y-m-d H:i:s')));
										if (false !== $personInsuranceInfoSaveResult) {
											$result = array();
											$result['totalCount'] = 0;
											$result['successCount'] = 0;
											foreach ($personInsuranceInfoArray['pay_order_id'] as $key => $value) {
												$result['totalCount'] ++;
												$amount = $this->alias('pii')->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_info_id=pii.id')->where(array('pii.pay_order_id'=>$value,'sid.state'=>array('gt',0)))->sum('sid.price + sid.service_price');
												$payOrderSaveResult = $payOrder->where(array('id'=>$value))->save(array('amount'=>$amount));
												if (false !== $payOrderSaveResult) {
													$result['successCount'] ++;
												}
											}
											if ($result['totalCount'] == $result['successCount']) {
												$this->commit();
												//$result = true;
											}else {
												$this->rollback();
												$this->error = '操作失败!';
												$result = false;
											}
										}else {
											$this->rollback();
											$this->error = '操作失败!';
											$result = false;
										}
									}else {
										$this->commit();
										$result = true;
									}
									break;
								case 1:
									$result = array();
									$result['totalCount'] = 0;
									$result['successCount'] = 0;
									$personBase = D('PersonBase');
									$personBaseSaveResult = $personBase->where(array('id'=>array('in',$personInsuranceInfoArray['baseId'])))->save(array('audit'=>1));
									//dump($personBaseSaveResult);
									if (false !== $personBaseSaveResult) {
										//dump($personInsuranceInfoResult);
										foreach ($personInsuranceInfoResult as $key => $value) {
											$result['totalCount'] ++;
											$location = ($value['location']/1000<<0)*1000;
											$templateResult = $template->getTemplateByCondition(array('location'=>$location+100));
											//dump($templateResult);
											if ($templateResult && $templateResult['soc_deadline']) {
												$handleMonth = $value['handle_month'];
												$payDeadline = date('Y-m-d',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(int_to_date($handleMonth,'-').'-'.sprintf('%02d',$templateResult['soc_deadline']))));
												if (time() <= strtotime($payDeadline)) {
													//写入支付订单数据
													//dump($data['service_company_id']);
													$amount = in_array($value['operate_state'],array(0,-1))?$serviceInsuranceDetailArray[$value['id']]['amount']:0;
													$payOrderData = array();
													$payOrderData['user_id'] = $value['user_id'];
													$payOrderData['company_id'] = $data['service_company_id'];
													$payOrderData['type'] = 2;
													$payOrderData['location'] = $location;
													$payOrderData['handle_month'] = $handleMonth;
													$payOrderData['amount'] = $amount;
													$payOrderData['pay_deadline'] = $payDeadline;
													//dump($payOrderData);
													$payOrderResult = $payOrder->savePayOrder($payOrderData);
													if ($payOrderResult) {
														$payOrderId = $payOrderResult;
														$personInsuranceInfoSaveResult = $this->where(array('id'=>$value['id']))->save(array('pay_order_id'=>$payOrderId,'modify_time'=>date('Y-m-d H:i:s')));
														if (false !== $personInsuranceInfoSaveResult) {
															$amount = $this->alias('pii')->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_info_id=pii.id')->where(array('pii.pay_order_id'=>$payOrderId,'sid.state'=>array('gt',0)))->sum('sid.price + sid.service_price');
															$payOrderSaveResult = $payOrder->where(array('id'=>$payOrderId))->save(array('amount'=>$amount));
															if (false !== $payOrderSaveResult) {
																$result['successCount'] ++;
															}
														}
													}else {
														$this->rollback();
														$this->error = $payOrder->getError();
														return false;
													}
												}else {
													$this->rollback();
													$this->error = '超出截止时间!';
													return false;
												}
											}else {
												$this->rollback();
												$this->error = '模板数据错误!';
												return false;
											}
										}
										//dump($result['totalCount']);
										//dump($result['successCount']);
										//if (0 != $result['totalCount'] && $result['totalCount'] == $result['successCount']) {
										if ($result['totalCount'] == $result['successCount']) {
											$this->commit();
											//$result = true;
										}else {
											$this->rollback();
											$this->error = '操作失败!';
											$result = false;
										}
									}else {
										$this->rollback();
										$this->error = '操作失败!';
										$result = false;
									}
									break;
								case -3:
									//办理失败写入差额
									$diffCron = D('DiffCron');
									$diffCron->_type = 1;
									for ($i=1; $i <= 2; $i++) {
										$diffCron->_item = $i;
							        	$diffCron->_sign = array('insurance_info_id'=>implode(',',$personInsuranceInfoArray['payment_type_id'][$i]));
							        	$diffCron->diffCron();
									}
									$this->commit();
									$result = true;
									break;
								case 3:
									//办理成功写入差额
									$diffCron = D('DiffCron');
									$diffCron->_type = 1;
									for ($i=1; $i <= 2; $i++) { 
										$diffCron->_item = $i;
							        	$diffCron->_unsign = array('insurance_info_id'=>implode(',',$personInsuranceInfoArray['payment_type_id'][$i]));
							        	$diffCron->diffCron();
									}
									$this->commit();
									$result = true;
									break;
								default:
									$this->rollback();
									$this->error = '类型错误';
									$resul = false;
									break;
							}
							return $result;
						}else {
							$this->rollback();
							$this->error = '操作失败!';
							return false;
						}
					}else {
						$this->rollback();
						$this->error = '操作失败!';
						return false;
					}
				//}else {
				//	$this->error = '所选数据状态没有变化!';
				//	return false;
				//}
			}else {
				$this->error = '产品数据错误！';
				return false;
			}
		}else {
			$this->error = '非法参数!';
			return false;
		}
	}
	
	/**
	 * updatePersonInsuranceInfoByCondition function
	 * 根据条件更新参保订单
	 * @param array $data 条件数组
	 * @param array $type 类型 1审批成功 -1审批失败 3办理成功 -3办理失败
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function updatePersonInsuranceInfoByCondition($data,$type){
		if (is_array($data) && is_array($data['id']) && in_array($type,array(1,-1,3,-3))) {
			$serviceProduct = M('ServiceProduct');
			$serviceProductResult = $serviceProduct->field(true)->where(array('company_id'=>$data['service_company_id']))->select();
			$productIdArray = array();
			if ($serviceProductResult) {
				foreach ($serviceProductResult as $key => $value) {
					$productIdArray[] = $value['id'];
				}
			}
			if ($productIdArray) {
				$stateArray = array(-1=>array('in','0,-1,1'), 1=>array('in','0,-1,1'), -3=>array('in','2,-3,3'), 3=>array('in','2,-3,3'));
				$condition = array('id'=>array('in',$data['id']),'product_id'=>array('in',$productIdArray),'operate_state'=>$stateArray[$type],'state'=>array('in',array(1,2,3)));
				//$condition = array('id'=>array('in',$data['id']),'product_id'=>array('in',$productIdArray));
				if (-3 == $type) {
					$condition['state'] = array('in',array(1,2));
				}
				$personInsuranceInfoResult = $this->field(true)->where($condition)->order('id desc')->select();
				//dump($personInsuranceInfoResult);die;
				//if (count($data['id']) == count($personInsuranceInfoResult)) {
					$this->startTrans();
					$saveResult = $this->where($condition)->save(array('operate_state'=>(-3 == $type?3:$type),'remark'=>$data['remark'],'modify_time'=>date('Y-m-d H:i:s')));
					if (false !== $saveResult) {
						//$template = D('Template');
						$templateRule = D('TemplateRule');
						$serviceInsuranceDetail = D('ServiceInsuranceDetail');
						//dump($data['id']);
						$condition = array('insurance_info_id'=>array('in',$data['id']),'state'=>$stateArray[$type]);
						if (-3 == $type) {
							$condition['type'] = array('in',array(1,2));
						}
						$serviceInsuranceDetailResult = $serviceInsuranceDetail->field(true)->where($condition)->order('insurance_info_id desc')->select();
						$serviceInsuranceDetailSaveResult = $serviceInsuranceDetail->where($condition)->save(array('state'=>$type,'note'=>$data['remark'],'modify_time'=>date('Y-m-d H:i:s')));
						//dump($serviceInsuranceDetailResult);
						//dump($serviceInsuranceDetail->_Sql());
						if ($serviceInsuranceDetailResult && false !== $serviceInsuranceDetailSaveResult) {
							$personInsuranceInfoArray = array();
							foreach ($personInsuranceInfoResult as $key => $value) {
								$personInsuranceInfoArray['baseId'][$value['base_id']] = $value['base_id'];
								$personInsuranceInfoArray['payment_type_id'][$value['payment_type']][$value['id']] = $value['id'];
							}
							//dump($personInsuranceInfoArray);
							$serviceInsuranceDetailArray = array();
							foreach ($serviceInsuranceDetailResult as $key => $value) {
								$serviceInsuranceDetailArray['insurance_info_id'][$value['insurance_info_id']]['data'][$value['id']] = $value;
								//计算明细费用
								$serviceInsuranceDetailArray['insurance_info_id'][$value['insurance_info_id']]['amount'] += ($value['price']+$value['service_price']);
								if ($value['pay_order_id']) {
									$serviceInsuranceDetailArray['id'][$value['id']] = $value['id'];
									$serviceInsuranceDetailArray['pay_order_id'][$value['pay_order_id']] = $value['pay_order_id'];
								}
							}
							$payOrder = D('PayOrder');
							switch ($type) {
								case -1:
									if ($serviceInsuranceDetailArray['id']) {
										$serviceInsuranceDetailSaveResult = $serviceInsuranceDetail->where(array('id'=>array('in',$serviceInsuranceDetailArray['id'])))->save(array('pay_order_id'=>0,'modify_time'=>date('Y-m-d H:i:s')));
										if (false !== $serviceInsuranceDetailSaveResult) {
											$result = array();
											$result['totalCount'] = 0;
											$result['successCount'] = 0;
											foreach ($serviceInsuranceDetailArray['pay_order_id'] as $key => $value) {
												$result['totalCount'] ++;
												$amount = $serviceInsuranceDetail->where(array('pay_order_id'=>$value,'state'=>array('gt',0)))->sum('price + service_price');
												$payOrderSaveResult = $payOrder->where(array('id'=>$value))->save(array('amount'=>$amount));
												if (false !== $payOrderSaveResult) {
													$result['successCount'] ++;
												}
											}
											if ($result['totalCount'] == $result['successCount']) {
												$this->commit();
												//$result = true;
											}else {
												$this->rollback();
												$this->error = '操作失败!';
												$result = false;
											}
										}else {
											$this->rollback();
											$this->error = '操作失败!';
											$result = false;
										}
									}else {
										$this->commit();
										$result = true;
									}
									break;
								case 1:
									$result = array();
									$result['totalCount'] = 0;
									$result['successCount'] = 0;
									$personBase = D('PersonBase');
									$personBaseSaveResult = $personBase->where(array('id'=>array('in',$personInsuranceInfoArray['baseId'])))->save(array('audit'=>1));
									//dump($personBaseSaveResult);
									if (false !== $personBaseSaveResult) {
										//dump($personInsuranceInfoResult);
										foreach ($personInsuranceInfoResult as $key => $value) {
											$result['totalCount'] ++;
											$location = ($value['location']/1000<<0)*1000;
											//$templateResult = $template->getTemplateByCondition(array('location'=>$location+100));
											if (1 == $value['payment_type']) {
												$ruleId = $value['rule_id'];
											}else {
												$ruleId = $this->where(array('user_id'=>$value['user_id'],'base_id'=>$value['base_id'],'handle_month'=>$value['handle_month'],'payment_type'=>1))->getField('rule_id');
											}
											$templateRuleResult = $templateRule->field(true)->getById($ruleId);
											if ($templateRuleResult && $templateRuleResult['deadline']) {
												$handleMonth = $value['handle_month'];
												$payDeadline = date('Y-m-d',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(int_to_date($handleMonth,'-').'-'.sprintf('%02d',$templateRuleResult['deadline']))));
												if (time() <= strtotime($payDeadline)) {
													//写入支付订单数据
													//dump($data['service_company_id']);
													$amount = in_array($value['operate_state'],array(0,-1))?$serviceInsuranceDetailArray['insurance_info_id'][$value['id']]['amount']:0;
													$payOrderData = array();
													$payOrderData['user_id'] = $value['user_id'];
													$payOrderData['company_id'] = $data['service_company_id'];
													$payOrderData['type'] = 2;
													$payOrderData['location'] = $location;
													$payOrderData['handle_month'] = $handleMonth;
													$payOrderData['amount'] = $amount;
													$payOrderData['pay_deadline'] = $payDeadline;
													//dump($payOrderData);
													$payOrderResult = $payOrder->savePayOrder($payOrderData);
													if ($payOrderResult) {
														$payOrderId = $payOrderResult;
														$serviceInsuranceDetailSaveResult = $serviceInsuranceDetail->where(array('insurance_info_id'=>$value['id']))->save(array('pay_order_id'=>$payOrderId,'modify_time'=>date('Y-m-d H:i:s')));
														if (false !== $serviceInsuranceDetailSaveResult) {
															$amount = $serviceInsuranceDetail->where(array('pay_order_id'=>$payOrderId,'state'=>array('gt',0)))->sum('price + service_price');
															$payOrderSaveResult = $payOrder->where(array('id'=>$payOrderId))->save(array('amount'=>$amount));
															if (false !== $payOrderSaveResult) {
																$result['successCount'] ++;
															}
														}
													}else {
														$this->rollback();
														$this->error = $payOrder->getError();
														return false;
													}
												}else {
													$this->rollback();
													$this->error = '超出截止时间!';
													return false;
												}
											}else {
												$this->rollback();
												$this->error = '模板数据错误!';
												return false;
											}
										}
										//dump($result['totalCount']);
										//dump($result['successCount']);
										//if (0 != $result['totalCount'] && $result['totalCount'] == $result['successCount']) {
										if ($result['totalCount'] == $result['successCount']) {
											$this->commit();
											//$result = true;
										}else {
											$this->rollback();
											$this->error = '操作失败!';
											$result = false;
										}
									}else {
										$this->rollback();
										$this->error = '操作失败!';
										$result = false;
									}
									break;
								case -3:
									//办理失败写入差额
									$diffCron = D('DiffCron');
									$diffCron->_type = 1;
									for ($i=1; $i <= 2; $i++) {
										$diffCron->_item = $i;
							        	$diffCron->_sign = array('insurance_info_id'=>implode(',',$personInsuranceInfoArray['payment_type_id'][$i]));
							        	$diffCron->diffCron();
									}
									$this->commit();
									$result = true;
									break;
								case 3:
									//办理成功写入差额
									$diffCron = D('DiffCron');
									$diffCron->_type = 1;
									for ($i=1; $i <= 2; $i++) { 
										$diffCron->_item = $i;
							        	$diffCron->_unsign = array('insurance_info_id'=>implode(',',$personInsuranceInfoArray['payment_type_id'][$i]));
							        	$diffCron->diffCron();
									}
									$this->commit();
									$result = true;
									break;
								default:
									$this->rollback();
									$this->error = '类型错误';
									$resul = false;
									break;
							}
							return $result;
						}else {
							$this->rollback();
							$this->error = '操作失败!';
							return false;
						}
					}else {
						$this->rollback();
						$this->error = '操作失败!';
						return false;
					}
				//}else {
				//	$this->error = '所选数据状态没有变化!';
				//	return false;
				//}
			}else {
				$this->error = '产品数据错误！';
				return false;
			}
		}else {
			$this->error = '非法参数!';
			return false;
		}
	}
	
	
	/**
	 * updatePersonInsuranceInfoByConditionByDetailId function
	 * 根据条件更新参保订单
	 * @param array $data 条件数组
	 * @param array $type 类型 1审批成功 -1审批失败 3办理成功 -3办理失败
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function updatePersonInsuranceInfoByConditionByDetailId($data,$type){
		//if (is_array($data) && is_array($data['id']) && in_array($type,array(1,-1,3,-3))) {
		if (is_array($data) && is_array($data['id']) && in_array($type,array(3,-3))) {
			$serviceProduct = M('ServiceProduct');
			$serviceProductResult = $serviceProduct->field(true)->where(array('company_id'=>$data['service_company_id']))->select();
			$productIdArray = array();
			if ($serviceProductResult) {
				foreach ($serviceProductResult as $key => $value) {
					$productIdArray[] = $value['id'];
				}
			}
			if ($productIdArray) {
				$stateArray = array(-1=>array('in','0,-1,1'), 1=>array('in','0,-1,1'), -3=>array('in','2,-3,3'), 3=>array('in','2,-3,3'));
				$serviceInsuranceDetail = D('ServiceInsuranceDetail');
				$insuranceInfoIdResult = $serviceInsuranceDetail->field('distinct insurance_info_id')->where(array('id'=>array('in',$data['id']),'state'=>$stateArray[$type]))->order('insurance_info_id desc')->select();
				$insuranceInfoIdArray = array();
				if ($insuranceInfoIdResult) {
					foreach ($insuranceInfoIdResult as $key => $value) {
						$insuranceInfoIdArray[$value['insurance_info_id']] = $value['insurance_info_id'];
					}
				}else {
					$this->error = '数据错误!';
					return false;
				}
				
				$condition = array('id'=>array('in',$insuranceInfoIdArray),'product_id'=>array('in',$productIdArray),'operate_state'=>$stateArray[$type],'state'=>array('in',array(1,2,3)));
				if (-3 == $type) {
					$condition['state'] = array('in',array(1,2));
				}
				$personInsuranceInfoResult = $this->field(true)->where($condition)->order('id desc')->select();
				//dump($personInsuranceInfoResult);die;
				$this->startTrans();
				$saveResult = $this->where($condition)->save(array('operate_state'=>(-3 == $type?3:$type),'remark'=>$data['remark'],'modify_time'=>date('Y-m-d H:i:s')));
				if (false !== $saveResult) {
					$condition = array('id'=>array('in',$data['id']),'state'=>$stateArray[$type]);
					if (-3 == $type) {
						$condition['type'] = array('in',array(1,2));
					}
					$serviceInsuranceDetailResult = $serviceInsuranceDetail->field(true)->where($condition)->order('insurance_info_id desc')->select();
					$serviceInsuranceDetailSaveResult = $serviceInsuranceDetail->where($condition)->save(array('state'=>$type,'note'=>$data['remark'],'modify_time'=>date('Y-m-d H:i:s')));
					if ($serviceInsuranceDetailResult && false !== $serviceInsuranceDetailSaveResult) {
						$personInsuranceInfoArray = array();
						foreach ($personInsuranceInfoResult as $key => $value) {
							$personInsuranceInfoArray['baseId'][$value['base_id']] = $value['base_id'];
							$personInsuranceInfoArray['payment_type_id'][$value['payment_type']][$value['id']] = $value['id'];
						}
						//dump($personInsuranceInfoArray);
						$serviceInsuranceDetailArray = array();
						foreach ($serviceInsuranceDetailResult as $key => $value) {
							$serviceInsuranceDetailArray['insurance_info_id'][$value['insurance_info_id']]['data'][$value['id']] = $value;
							//计算明细费用
							$serviceInsuranceDetailArray['insurance_info_id'][$value['insurance_info_id']]['amount'] += ($value['price']+$value['service_price']);
							if ($value['pay_order_id']) {
								$serviceInsuranceDetailArray['id'][$value['id']] = $value['id'];
								$serviceInsuranceDetailArray['pay_order_id'][$value['pay_order_id']] = $value['pay_order_id'];
								$serviceInsuranceDetailArray['payment_type_id'][$value['payment_type']][$value['id']] = $value['id'];
							}
						}
						$payOrder = D('PayOrder');
						switch ($type) {
							case -3:
								//办理失败写入差额
								$diffCron = D('DiffCron');
								$diffCron->_type = 1;
								for ($i=1; $i <= 2; $i++) {
									$diffCron->_item = $i;
						        	//$diffCron->_sign = array('insurance_info_id'=>implode(',',$personInsuranceInfoArray['payment_type_id'][$i]));
						        	$diffCron->_sign = array('detail_id'=>implode(',',$serviceInsuranceDetailArray['payment_type_id'][$i]));
						        	$diffCron->diffCron();
								}
								$this->commit();
								$result = true;
								break;
							case 3:
								//办理成功写入差额
								$diffCron = D('DiffCron');
								$diffCron->_type = 1;
								for ($i=1; $i <= 2; $i++) { 
									$diffCron->_item = $i;
						        	//$diffCron->_unsign = array('insurance_info_id'=>implode(',',$personInsuranceInfoArray['payment_type_id'][$i]));
						        	$diffCron->_unsign = array('detail_id'=>implode(',',$serviceInsuranceDetailArray['payment_type_id'][$i]));
						        	$diffCron->diffCron();
								}
								$this->commit();
								$result = true;
								break;
							default:
								$this->rollback();
								$this->error = '类型错误';
								$resul = false;
								break;
						}
						return $result;
					}else {
						$this->rollback();
						$this->error = '操作失败!';
						return false;
					}
				}else {
					$this->rollback();
					$this->error = '操作失败!';
					return false;
				}
			}else {
				$this->error = '产品数据错误！';
				return false;
			}
		}else {
			$this->error = '非法参数!';
			return false;
		}
	}
	
	/**
	 * approvePersonInsuranceInfo function
	 * 审核参保订单
	 * @param array $data 条件数组
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function approvePersonInsuranceInfoOld($data){
		if (is_array($data) && is_array($data['data'])) {
			$serviceProduct = M('ServiceProduct');
			$serviceProductResult = $serviceProduct->field(true)->where(array('company_id'=>$data['service_company_id']))->select();
			$productIdArray = array();
			if ($serviceProductResult) {
				foreach ($serviceProductResult as $key => $value) {
					$productIdArray[] = $value['id'];
				}
			}
			if ($productIdArray) {
				$stateArray = array(-1=>array('in','0,-1,1'), 1=>array('in','0,-1,1'), -3=>array('in','2,-3,3'), 3=>array('in','2,-3,3'));
				$this->startTrans();
				$template = D('Template');
				$serviceInsuranceDetail = D('ServiceInsuranceDetail');
				$payOrder = D('PayOrder');
				$personBase = D('PersonBase');
				$result = array();
				$result['totalCount'] = 0;
				$result['successCount'] = 0;
				foreach ($data['data'] as $key => $value) {
					if ('' !== ($value['operate_state'])) {
						$condition = array('id'=>$value['id'],'product_id'=>array('in',$productIdArray),'operate_state'=>$stateArray[$value['operate_state']]);
						$personInsuranceInfoResult = $this->field(true)->where($condition)->find();
						if ($personInsuranceInfoResult) {
							$saveResult = $this->where($condition)->save(array('operate_state'=>$value['operate_state'],'remark'=>$value['remark'],'modify_time'=>date('Y-m-d H:i:s')));
							$condition = array('insurance_info_id'=>$value['id'],'state'=>$stateArray[$value['operate_state']],'payment_type'=>$key);
							$serviceInsuranceDetailResult = $serviceInsuranceDetail->field(true)->where($condition)->order('insurance_info_id desc')->select();
							$serviceInsuranceDetailSaveResult = $serviceInsuranceDetail->where($condition)->save(array('state'=>$value['operate_state'],'note'=>$value['remark'],'modify_time'=>date('Y-m-d H:i:s')));
							//dump($serviceInsuranceDetailResult);
							//dump($serviceInsuranceDetailSaveResult);
							//dump($serviceInsuranceDetail->_sql());
							if (false !== $saveResult && false !== $serviceInsuranceDetailSaveResult && $serviceInsuranceDetailResult) {
								//dump($serviceInsuranceDetail->_sql());
								//dump($serviceInsuranceDetailResult);
								$result['totalCount'] ++;
								if (-1 == $value['operate_state']) {
									if ($personInsuranceInfoResult['pay_order_id']) {
										$personInsuranceInfoSaveResult = $this->where(array('id'=>$personInsuranceInfoResult['id']))->save(array('pay_order_id'=>0,'modify_time'=>date('Y-m-d H:i:s')));
										if (false !== $personInsuranceInfoSaveResult) {
											$amount = $this->alias('pii')->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_info_id=pii.id')->where(array('pii.pay_order_id'=>$personInsuranceInfoResult['pay_order_id'],'sid.state'=>array('gt',0)))->sum('sid.price + sid.service_price');
											$payOrderSaveResult = $payOrder->where(array('id'=>$personInsuranceInfoResult['pay_order_id']))->save(array('amount'=>$amount));
											//$personBaseResult = $personBase->where(['id'=>$personInsuranceInfoResult['base_id'],'audit'=>[0]])->save(['audit'=>-1]);
											if (false !== $payOrderSaveResult && false !== $personBaseResult) {
												$result['successCount'] ++;
											}
										}
									}else {
										$result['successCount'] ++;
									}
								}else if (1 == $value['operate_state']) {
									$location = ($personInsuranceInfoResult['location']/1000<<0)*1000;
									$templateResult = $template->getTemplateByCondition(array('location'=>$location+100));
									if ($templateResult && $templateResult['soc_deadline']) {
										$handleMonth = $personInsuranceInfoResult['handle_month'];
										$payDeadline = date('Y-m-d',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(int_to_date($handleMonth,'-').'-'.sprintf('%02d',$templateResult['soc_deadline']))));
										if (time() <= strtotime($payDeadline)) {
											//dump($payDeadline);
											//写入支付订单数据
											$amount = 0;
											$payOrderData = array();
											$payOrderData['user_id'] = $personInsuranceInfoResult['user_id'];
											$payOrderData['company_id'] = $data['service_company_id'];
											$payOrderData['type'] = 2;
											$payOrderData['location'] = $location;
											$payOrderData['handle_month'] = $handleMonth;
											$payOrderData['amount'] = $amount;
											$payOrderData['pay_deadline'] = $payDeadline;
											//dump($payOrderData);
											$payOrderResult = $payOrder->savePayOrder($payOrderData);
											$personBaseResult = $personBase->where(['id'=>$personInsuranceInfoResult['base_id']])->save(['audit'=>1]);
											if ($payOrderResult) {
												$payOrderId = $payOrderResult;
												$personInsuranceInfoSaveResult = $this->where(array('id'=>$personInsuranceInfoResult['id']))->save(array('pay_order_id'=>$payOrderId,'modify_time'=>date('Y-m-d H:i:s')));
												if (false !== $personInsuranceInfoSaveResult) {
													$amount = $this->alias('pii')->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_info_id=pii.id')->where(array('pii.pay_order_id'=>$payOrderId,'sid.state'=>array('gt',0)))->sum('sid.price + sid.service_price');
													$payOrderSaveResult = $payOrder->where(array('id'=>$payOrderId))->save(array('amount'=>$amount));
													if (false !== $payOrderSaveResult) {
														$result['successCount'] ++;
													}
												}
											}else {
												$this->rollback();
												$this->error = $payOrder->getError();
												return false;
											}
										}else {
											$this->rollback();
											$this->error = '超出截止时间!';
											return false;
										}
									}else {
										$this->rollback();
										$this->error = '模板数据错误!';
										return false;
									}
								}
							}else {
								$this->rollback();
								$this->error = '操作失败!';
								return false;
							}
						}else {
							//$this->rollback();
							//$this->error = '状态错误！';
							//return false;
						}
					}
				}
				if ($result['totalCount'] == $result['successCount']) {
					$this->commit();
				}else {
					$this->rollback();
					$this->error = '操作失败!';
					$result = false;
				}
				return $result;
			}else {
				$this->error = '产品数据错误！';
				return false;
			}
		}else {
			$this->error = '非法参数!';
			return false;
		}
	}
	
	/**
	 * approvePersonInsuranceInfo function
	 * 审核参保订单
	 * @param array $data 条件数组
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function approvePersonInsuranceInfo($data){
		if (is_array($data) && is_array($data['data'])) {
			$serviceProduct = M('ServiceProduct');
			$serviceProductResult = $serviceProduct->field(true)->where(array('company_id'=>$data['service_company_id']))->select();
			$productIdArray = array();
			if ($serviceProductResult) {
				foreach ($serviceProductResult as $key => $value) {
					$productIdArray[] = $value['id'];
				}
			}
			if ($productIdArray) {
				$stateArray = array(-1=>array('in','0,-1,1'), 1=>array('in','0,-1,1'), -3=>array('in','2,-3,3'), 3=>array('in','2,-3,3'));
				$this->startTrans();
				//$template = D('Template');
				$templateRule = D('TemplateRule');
				$serviceInsuranceDetail = D('ServiceInsuranceDetail');
				$payOrder = D('PayOrder');
				$personBase = D('PersonBase');
				$result = array();
				$result['totalCount'] = 0;
				$result['successCount'] = 0;
				foreach ($data['data'] as $key => $value) {
					if ('' !== ($value['operate_state'])) {
						$condition = array('id'=>$value['id'],'product_id'=>array('in',$productIdArray),'operate_state'=>$stateArray[$value['operate_state']]);
						$personInsuranceInfoResult = $this->field(true)->where($condition)->find();
						if ($personInsuranceInfoResult) {
							$saveResult = $this->where($condition)->save(array('operate_state'=>$value['operate_state'],'remark'=>$value['remark'],'modify_time'=>date('Y-m-d H:i:s')));
							$condition = array('insurance_info_id'=>$value['id'],'state'=>$stateArray[$value['operate_state']],'payment_type'=>$key);
							$serviceInsuranceDetailResult = $serviceInsuranceDetail->field(true)->where($condition)->order('insurance_info_id desc')->select();
							$serviceInsuranceDetailSaveResult = $serviceInsuranceDetail->where($condition)->save(array('state'=>$value['operate_state'],'note'=>$value['remark'],'modify_time'=>date('Y-m-d H:i:s')));
							//dump($serviceInsuranceDetailResult);
							//dump($serviceInsuranceDetailSaveResult);
							//dump($serviceInsuranceDetail->_sql());
							if (false !== $saveResult && false !== $serviceInsuranceDetailSaveResult && $serviceInsuranceDetailResult) {
								$result['totalCount'] ++;
								if (-1 == $value['operate_state']) {
									$serviceInsuranceDetailArray = array();
									foreach ($serviceInsuranceDetailResult as $kk => $vv) {
										if ($vv['pay_order_id']) {
											$serviceInsuranceDetailArray['id'][$vv['id']] = $vv['id'];
											$serviceInsuranceDetailArray['pay_order_id'][$vv['pay_order_id']] = $vv['pay_order_id'];
										}
									}
									if ($serviceInsuranceDetailArray['pay_order_id']) {
										$serviceInsuranceDetailSaveResult = $serviceInsuranceDetail->where(array('id'=>['in',$serviceInsuranceDetailArray['id']]))->save(array('pay_order_id'=>0,'modify_time'=>date('Y-m-d H:i:s')));
										if (false !== $serviceInsuranceDetailSaveResult) {
											foreach ($serviceInsuranceDetailArray['pay_order_id'] as $kkk => $vvv) {
												$amount = $serviceInsuranceDetail->where(array('pay_order_id'=>$vvv,'state'=>array('gt',0)))->sum('price + service_price');
												$payOrderSaveResult = $payOrder->where(array('id'=>$vvv))->save(array('amount'=>$amount));
												if (false !== $payOrderSaveResult) {
													$result['successCount'] ++;
												}
											}
										}
									}else {
										$result['successCount'] ++;
									}
								}else if (1 == $value['operate_state']) {
									$location = ($personInsuranceInfoResult['location']/1000<<0)*1000;
									//$templateResult = $template->getTemplateByCondition(array('location'=>$location+100));
									if (1 == $personInsuranceInfoResult['payment_type']) {
										$ruleId = $personInsuranceInfoResult['rule_id'];
									}else {
										$ruleId = $this->where(array('user_id'=>$personInsuranceInfoResult['user_id'],'base_id'=>$personInsuranceInfoResult['base_id'],'handle_month'=>$personInsuranceInfoResult['handle_month'],'peyment_type'=>1))->getField('rule_id');
									}
									$templateRuleResult = $templateRule->field(true)->getById($ruleId);
									if ($templateRuleResult && $templateRuleResult['deadline']) {
										$handleMonth = $personInsuranceInfoResult['handle_month'];
										$payDeadline = date('Y-m-d',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(int_to_date($handleMonth,'-').'-'.sprintf('%02d',$templateRuleResult['deadline']))));
										if (time() <= strtotime($payDeadline)) {
											//dump($payDeadline);
											//写入支付订单数据
											$amount = 0;
											$payOrderData = array();
											$payOrderData['user_id'] = $personInsuranceInfoResult['user_id'];
											$payOrderData['company_id'] = $data['service_company_id'];
											$payOrderData['type'] = 2;
											$payOrderData['location'] = $location;
											$payOrderData['handle_month'] = $handleMonth;
											$payOrderData['amount'] = $amount;
											$payOrderData['pay_deadline'] = $payDeadline;
											//dump($payOrderData);
											$payOrderResult = $payOrder->savePayOrder($payOrderData);
											$personBaseResult = $personBase->where(['id'=>$personInsuranceInfoResult['base_id']])->save(['audit'=>1]);
											if ($payOrderResult) {
												$payOrderId = $payOrderResult;
												$serviceInsuranceDetailSaveResult = $serviceInsuranceDetail->where(array('insurance_info_id'=>$personInsuranceInfoResult['id']))->save(array('pay_order_id'=>$payOrderId,'modify_time'=>date('Y-m-d H:i:s')));
												if (false !== $serviceInsuranceDetailSaveResult) {
													$amount = $serviceInsuranceDetail->where(array('pay_order_id'=>$payOrderId,'state'=>array('gt',0)))->sum('price + service_price');
													$payOrderSaveResult = $payOrder->where(array('id'=>$payOrderId))->save(array('amount'=>$amount));
													if (false !== $payOrderSaveResult) {
														$result['successCount'] ++;
													}
												}
											}else {
												$this->rollback();
												$this->error = $payOrder->getError();
												return false;
											}
										}else {
											$this->rollback();
											$this->error = '超出截止时间!';
											return false;
										}
									}else {
										$this->rollback();
										$this->error = '模板数据错误!';
										return false;
									}
								}
							}else {
								$this->rollback();
								$this->error = '操作失败!';
								return false;
							}
						}else {
							//$this->rollback();
							//$this->error = '状态错误！';
							//return false;
						}
					}
				}
				if ($result['totalCount'] == $result['successCount']) {
					$this->commit();
				}else {
					$this->rollback();
					$this->error = '操作失败!';
					$result = false;
				}
				return $result;
			}else {
				$this->error = '产品数据错误！';
				return false;
			}
		}else {
			$this->error = '非法参数!';
			return false;
		}
	}
	
	/**
	 * handlePersonInsuranceInfo function
	 * 办理参保订单
	 * @param array $data 条件数组
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function handlePersonInsuranceInfo($data){
		if (is_array($data)) {
			$serviceProduct = M('ServiceProduct');
			$serviceProductResult = $serviceProduct->field(true)->where(array('company_id'=>$data['service_company_id']))->select();
			$productIdArray = array();
			if ($serviceProductResult) {
				foreach ($serviceProductResult as $key => $value) {
					$productIdArray[] = $value['id'];
				}
			}
			if ($productIdArray) {
				$stateArray = array(-1=>array('in','0,-1,1'), 1=>array('in','0,-1,1'), -3=>array('in','2,-3,3'), 3=>array('in','2,-3,3'));
				$this->startTrans();
				//dump($data);
				$serviceInsuranceDetail = D('ServiceInsuranceDetail');
				$result = array();
				$result['totalCount'] = 0;
				$result['successCount'] = 0;
				$serviceInsuranceDetailArray = array();
				foreach ($data['data'] as $k => $v) {
					if ($v['data']) {
						$condition = array('id'=>$v['id'],'product_id'=>array('in',$productIdArray),'operate_state'=>$stateArray[3]);
						$personInsuranceInfoResult = $this->field(true)->where($condition)->find();
						//dump($personInsuranceInfoResult);
						if ($personInsuranceInfoResult) {
							//$saveResult = $this->where($condition)->fetchSql(true)->save(array('operate_state'=>3,'modify_time'=>date('Y-m-d H:i:s')));
							$saveResult = $this->where($condition)->save(array('operate_state'=>3,'modify_time'=>date('Y-m-d H:i:s')));
							if (false !== $saveResult) {
								ksort($v['data']);
								$firstDetailId = reset($v['data'])['id'];
								//dump($firstDetailId);
								foreach ($v['data'] as $kk => $vv) {
									//dump($k);
									//dump($vv);
									$result['totalCount'] ++;
									$serviceInsuranceDetailArray['id'][$k][$vv['operate_state']][$vv['id']] = $vv['id'];
									if (1 == $v['buyCard'] && $vv['id'] == $firstDetailId) {
										$serviceInsuranceDetailArray['detail_id'][$k][1][$vv['id']] = $vv['id'];//缴纳工本费
									}else {
										$serviceInsuranceDetailArray['detail_id'][$k][0][$vv['id']] = $vv['id'];//不缴工本费
									}
									
									$condition = array('id'=>$vv['id'],'insurance_info_id'=>$v['id'],'state'=>$stateArray[$vv['operate_state']],'payment_type'=>$k);
									$serviceInsuranceDetailSaveResult = $serviceInsuranceDetail->where($condition)->save(array('state'=>$vv['operate_state'],'is_hang_up'=>$vv['is_hang_up'],'note'=>$vv['remark'],'modify_time'=>date('Y-m-d H:i:s')));
									//dump($condition);
									//dump($serviceInsuranceDetailSaveResult);
									if (false !== $serviceInsuranceDetailSaveResult) {
										$result['successCount'] ++;
									}
									//$serviceInsuranceDetailResult = $serviceInsuranceDetail->field(true)->where($condition)->order('insurance_info_id desc')->select();
								}
								
							}else {
								$this->rollback();
								$this->error = '操作失败!';
								return false;
							}
						}else {
							$this->rollback();
							$this->error = '状态错误！';
							return false;
						}
					}
				}
				//dump($serviceInsuranceDetailArray);//die;
				if ($result['totalCount'] == $result['successCount']) {
					//办理写入差额
					$diffCron = D('DiffCron');
					$diffCron->_type = 1;
					//dump($serviceInsuranceDetailArray);
					//dump($serviceInsuranceDetailArray['id']);
					//dump($serviceInsuranceDetailArray['detail_id']);
					for ($i=1; $i <= 2; $i++) {
						$diffCron->_item = $i;
			        	$diffCron->_sign = array('detail_id'=>implode(',',$serviceInsuranceDetailArray['id'][$i][-3]));//办理失败
			        	$diffCron->_unsign = array('detail_id'=>implode(',',$serviceInsuranceDetailArray['id'][$i][3]));//办理成功
			        	$diffCron->diffCron();
					}
					//写入工本费
					$diffCron->_type = 4;
					for ($i=1; $i <= 2; $i++) {
						$diffCron->_item = $i;
			        	$diffCron->_sign = array('detail_id'=>implode(',',$serviceInsuranceDetailArray['detail_id'][$i][1]));//缴纳工本费
			        	$diffCron->_unsign = array('detail_id'=>implode(',',$serviceInsuranceDetailArray['detail_id'][$i][0]));//不缴工本费
			        	//dump($diffCron->_sign);
			        	//dump($diffCron->_unsign);
			        	$diffCron->diffCron();
					}
					$this->commit();
				}else {
					$this->rollback();
					$this->error = '操作失败!';
					$result = false;
				}
				return $result;
			}else {
				$this->error = '产品数据错误！';
				return false;
			}
		}else {
			$this->error = '非法参数!';
			return false;
		}
	}
	
	/**
	 * paymentExceptionPersonInsuranceInfo function
	 * 缴费异常参保订单
	 * @param array $data 条件数组
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function paymentExceptionPersonInsuranceInfo($data){
		if (is_array($data) && is_array($data['data'])) {
			$totalCount = array(0=>0,1=>0,2=>0);
			$successCount = array(0=>0,1=>0,2=>0);
			foreach ($data['data'] as $k => $v) {
				foreach ($v['data'] as $kk => $vv) {
					$totalCount[0] ++;
					$totalCount[$k] ++;
					$data['data'][$k]['data'][$kk]['company'] = (1 == $vv['company']?true:false);
					$data['data'][$k]['data'][$kk]['person'] = (1 == $vv['person']?true:false);
					if (1 == $vv['company'] && 1 == $vv['person']) {
						$successCount[0] ++;
						$successCount[$k] ++;
					}
				}
			}
			if ($totalCount[0] == $successCount[0]) {
				$this->error = '没有缴纳异常!';
				return false;
			}
			
			$serviceProduct = M('ServiceProduct');
			$serviceProductResult = $serviceProduct->field(true)->where(array('company_id'=>$data['service_company_id']))->select();
			$productIdArray = array();
			if ($serviceProductResult) {
				foreach ($serviceProductResult as $key => $value) {
					$productIdArray[] = $value['id'];
				}
			}
			if ($productIdArray) {
				$this->startTrans();
				$serviceInsuranceDetail = D('ServiceInsuranceDetail');
				$result = array();
				$result['totalCount'] = 0;
				$result['successCount'] = 0;
				$serviceInsuranceDetailArray = array();
				foreach ($data['data'] as $k => $v) {
					$result['totalCount'] ++;
					if ($totalCount[$k] != $successCount[$k]) {
						/*if ($v['id']) {
							$condition = array('id'=>$v['id'],'insurance_info_id'=>$v['insurance_info_id'],'state'=>array('in','3,-4'),'payment_type'=>$k);
						}else {
							$condition = array('insurance_info_id'=>$v['insurance_info_id'],'pay_date'=>$v['pay_date'],'state'=>array('in','3,-4'),'payment_type'=>$k);
						}*/
						$condition = array('id'=>$v['id'],'insurance_info_id'=>$v['insurance_info_id'],'state'=>array('in','3,-4'),'payment_type'=>$k);
						$serviceInsuranceDetailResult = $serviceInsuranceDetail->where($condition)->find();
						$serviceInsuranceDetailSaveResult = $serviceInsuranceDetail->where($condition)->save(array('state'=>-4,'modify_time'=>date('Y-m-d H:i:s')));
						if ($serviceInsuranceDetailResult && false !== $serviceInsuranceDetailSaveResult) {
							$serviceInsuranceDetailArray[$k]['detail_id'] = $v['id'];
							$serviceInsuranceDetailArray[$k]['messageBody'] = $v['data'];
							$result['successCount'] ++;
						}
					}else {
						$result['successCount'] ++;
					}
				}
				if ($result['totalCount'] == $result['successCount']) {
					//缴纳异常写入差额
					$diffCron = D('DiffCron');
					$diffCron->_type = 3;
					foreach ($serviceInsuranceDetailArray as $key => $value) {
						$diffCron->_item = $key;
						$diffCron->_sign = array('detail_id'=>$value['detail_id']);//缴纳异常
			        	$diffCron->_messageBody = $value['messageBody'];
			        	$diffCron->diffCron();
					}
					$this->commit();
				}else {
					$this->rollback();
					$this->error = '操作失败!';
					$result = false;
				}
				return $result;
			}else {
				$this->error = '产品数据错误！';
				return false;
			}
		}else {
			$this->error = '非法参数!';
			return false;
		}
	}
}