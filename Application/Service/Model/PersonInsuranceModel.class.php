<?php

namespace Service\Model;
use Think\Model;

/**
 * 个人参保模型
 */
class PersonInsuranceModel extends Model{
	/**
	 * getPersonList function
	 * $data 条件数组
	 * param int $type 类型 0未参保，1报增，2在保，3报减，4停保
	 * param int $pageSize 分页大小，默认10
	 * @return void
	 * @author rohochan
	 **/
	public function getPersonList($data,$type,$pageSize = 10){
		if (is_array($data) && $data['service_company_id']) {
			$condition = array();
			switch ($type) {
				//未参保
				case '0':
					//$condition['_string'] = ' (socpi.state = 0 or socpi.state is null) and (propi.state = 0 or propi.state is null) ';
					$condition['_string'] = ' socpi.state = 0 and propi.state = 0 ';
					break;
				//报增
				case '1':
					$condition['_complex'] = array('_logic'=>'or','socpi.state'=>$type,'propi.state'=>$type);
					break;
				//在保
				case '2':
					$condition['_complex'] = array('_logic'=>'or','socpi.state'=>$type,'propi.state'=>$type);
					break;
				//报减
				case '3':
					$condition['_complex'] = array('_logic'=>'or','socpi.state'=>$type,'propi.state'=>$type);
					break;
				//停保
				case '4':
					$condition['_complex'] = array('_logic'=>'or','socpi.state'=>$type,'propi.state'=>$type);
					break;
				default:
					break;
			}
			//dump($condition);
			if (!empty($data['location'])) {
				//$condition['_complex'] = array('_logic'=>'or','socpi.location'=>$data['location'],'propi.location'=>$data['location']);
				$condition['_string'] .= $condition['_string']?' and (socpi.location = '.$data['location'].' or propi.location = '.$data['location'].')':'socpi.location = '.$data['location'].' or propi.location = '.$data['location'];
			}
			if (!empty($data['person_name'])) {
				$condition['pb.person_name'] = array('like','%'.$data['person_name'].'%');
			}
			if (!empty($data['card_num'])) {
				$condition['pb.card_num'] = array('like','%'.$data['card_num'].'%');
			}
			
			//客服只能看当前用户信息
			if (!empty($data['account_info'])) {
				if (3 == $data['account_info']['group']) {
					$adminId = getServiceAdminId($data['account_info']['user_id']);
					if ($adminId) {
						$userServiceProvider = D('UserServiceProvider');
						$userServiceProviderResultl = $userServiceProvider->field('GROUP_CONCAT(distinct user_id) as user_id')->where(['company_id'=>$data['account_info']['company_id'],'admin_id'=>$adminId,'state'=>1,'user_id'=>['gt',0]])->find();
						if ($userServiceProviderResultl['user_id']) {
							$condition['socpi.user_id'] = ['in',$userServiceProviderResultl['user_id']];
							$condition['propi.user_id'] = ['in',$userServiceProviderResultl['user_id']];
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
				$socpi = $this->field(true)->where(array('payment_type'=>1,'product_id'=>array('in',$productIdArray)))->select(false);
				$propi = $this->field(true)->where(array('payment_type'=>2,'product_id'=>array('in',$productIdArray)))->select(false);
				
				$pageCount = $this->table($socpi.' as socpi')->join('left join '.$propi.' as propi on socpi.base_id = propi.base_id and socpi.user_id = propi.user_id')->join('left join '.C('DB_PREFIX').'person_base as pb on socpi.base_id = pb.id or propi.base_id = pb.id')->where($condition)->count();
				$page = get_page($pageCount,$pageSize);
				//TODO:分别查询社保和公积金数据再循环处理数据提高速度
				$result = $this->field('IFNULL(socpi.base_id,propi.base_id) as base_id,IFNULL(socpi.user_id,propi.user_id) as user_id,IF(socpi.modify_time>=propi.modify_time or propi.modify_time is null,socpi.modify_time,propi.modify_time) as modify_time,pb.person_name,pb.card_num,pb.residence_location,pb.residence_type,pb.audit as pb_audit,socsp.name as soc_product_name,socci.company_name as socci_service_company_name,prosp.name as pro_product_name,proci.company_name as proci_service_company_name,socpi.id as socpi_id,socpi.rule_id as socpi_rule_id,socpi.product_id as socpi_product_id,socpi.location as socpi_location,socpi.start_month as socpi_start_month,socpi.end_month as socpi_end_month,socpi.amount as socpi_amount,socpi.payment_info as socpi_payment_info,socpi.payment_type as socpi_payment_type,socpi.state as socpi_state,propi.id as propi_id,propi.rule_id as propi_rule_id,propi.product_id as propi_product_id,propi.location as propi_location,propi.start_month as propi_start_month,propi.end_month as propi_end_month,propi.amount as propi_amount,propi.payment_info as propi_payment_info,propi.payment_type as propi_payment_type,propi.state as propi_state')->table($socpi.' as socpi')
						->join('left join '.$propi.' as propi on socpi.base_id = propi.base_id and socpi.user_id = propi.user_id')
						->join('left join '.C('DB_PREFIX').'person_base as pb on socpi.base_id = pb.id or propi.base_id = pb.id')
						->join('left join '.C('DB_PREFIX').'service_product as socsp on socpi.product_id = socsp.id')
						->join('left join '.C('DB_PREFIX').'company_info as socci on socsp.company_id = socci.id')
						->join('left join '.C('DB_PREFIX').'service_product as prosp on propi.product_id = prosp.id')
						->join('left join '.C('DB_PREFIX').'company_info as proci on prosp.company_id = proci.id')
						->order('modify_time desc ')
						->where($condition)->limit($page->firstRow,$page->listRows)->select();
				
				if (!empty($result)) {
					//获取参保人参保状态
					$ruleIds = array();
					foreach ($result as $key => $value) {
						$ruleIds[$value['socpi_rule_id']] = $value['socpi_rule_id'];
						$ruleIds[$value['propi_rule_id']] = $value['propi_rule_id'];
					}
					$ruleIds = implode(',',array_filter($ruleIds));
					//$ruleIds = implode(',',array_unique($ruleIds));
					if ($ruleIds) {
						$templateRule = D('TemplateRule');
						$templateRuleResult = $templateRule->field('id,name,type,classify_mixed')->where(array('id'=>array('in',$ruleIds)))->select();
						if ($templateRuleResult) {
							$templateClassifyIds = array();
							$templateRuleArray = array();
							foreach ($templateRuleResult as $key => $value) {
								$templateRuleArray[$value['id']]['ruleName'] = $value['name'];
							}
						}
					}
					
					foreach ($result as $key => $value) {
						//$result[$key]['socpiClassifyMixedValue'] = $templateRuleArray[$value['socpi_rule_id']]['classifyMixedValue'];
						//$result[$key]['propiClassifyMixedValue'] = $templateRuleArray[$value['propi_rule_id']]['classifyMixedValue'];
						$result[$key]['socpiRuleName'] = $templateRuleArray[$value['socpi_rule_id']]['ruleName'];
						$result[$key]['propiRuleName'] = $templateRuleArray[$value['propi_rule_id']]['ruleName'];
						$result[$key]['socpiLocationValue'] = showAreaName($value['socpi_location']);
						$result[$key]['propiLocationValue'] = showAreaName($value['propi_location']);
						$result[$key]['count'] = ($type == $value['socpi_state']?1:0)+($type == $value['propi_state']?1:0);
					}
				}
				
				//dump($this->_sql());
				
				if ($result || null === $result) {
					return array('data'=>$result,'page'=>$page->show());
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
			$this->error = '非法参数!';
			return false;
		}
	}
	
	/**
	 * getPersonInsuranceByCondition function
	 * 根据条件获取个人参保信息
	 * param array $data 条件数组
	 * param boolean $isGetPaymentMonth 是否获取缴纳年月数据
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getPersonInsuranceByCondition($data,$isGetPaymentMonth = true){
		if (is_array($data)) {
			$socpi = $this->field(true)->where(array('base_id'=>$data['base_id'],'payment_type'=>1,'user_id'=>$data['user_id']))->select(false);
			$propi = $this->field(true)->where(array('base_id'=>$data['base_id'],'payment_type'=>2,'user_id'=>$data['user_id']))->select(false);
			
			$result = $this->table($socpi.' as socpi')
					->field('IFNULL(socpi.base_id,propi.base_id) as base_id,IFNULL(socpi.user_id,propi.user_id) as user_id,pb.person_name,pb.card_num,pb.residence_location,pb.residence_type,pb.mobile,pb.gender,socpi.id as socpi_id,socpi.rule_id as socpi_rule_id,socpi.product_id as socpi_product_id,socpi.location as socpi_location,socpi.start_month as socpi_start_month,socpi.end_month as socpi_end_month,socpi.amount as socpi_amount,socpi.payment_info as socpi_payment_info,socpi.payment_type as socpi_payment_type,socpi.state as socpi_state,propi.id as propi_id,propi.rule_id as propi_rule_id,propi.product_id as propi_product_id,propi.location as propi_location,propi.start_month as propi_start_month,propi.end_month as propi_end_month,propi.amount as propi_amount,propi.payment_info as propi_payment_info,propi.payment_type as propi_payment_type,propi.state as propi_state')
					->join(' left join '.$propi.' as propi on socpi.base_id = propi.base_id and socpi.user_id = propi.user_id')
					->join('left join '.C('DB_PREFIX').'person_base as pb on socpi.base_id = pb.id or propi.base_id = pb.id')
					->find();
			
			if ($result && $isGetPaymentMonth) {
				$condition = array('pii.base_id'=>$data['base_id'],'pii.user_id'=>$data['user_id']);
				$pii = $this->table(C('DB_PREFIX').'person_insurance_info as pii')->field('sid.pay_date as sid_pay_date')->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_info_id = pii.id')->where($condition)->order('sid.pay_date asc')->group('sid.pay_date')->select();
				//dump($this->_sql());
				$piiArray = array();
				if ($pii) {
					foreach ($pii as $key => $value) {
						if ($value['sid_pay_date']) {
							$piiArray[$value['sid_pay_date']] = $value['sid_pay_date'];
						}
					}
				}
				$result['sid_pay_date'] = $piiArray;
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
	 * getInsuranceStatus function
	 * 获取参保人参保状态
	 * param int $userId 用户id
	 * param int $baseId 个人信息id
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getInsuranceStatus($userId = 0,$baseId = 0){
		
		if ($userId > 0 && $baseId > 0) {
			$socPersonInsuranceResult = $this->field('id,state')->where(array('user_id'=>$userId,'base_id'=>$baseId,'payment_type'=>1))->find();
			$proPersonInsuranceResult = $this->field('id,state')->where(array('user_id'=>$userId,'base_id'=>$baseId,'payment_type'=>2))->find();
			
			$socState = intval($socPersonInsuranceResult['state']);
			$proState = intval($proPersonInsuranceResult['state']);
			
			$result = array();
			$result['increase'] = false;
			$result['editIncrease'] = false;
			$result['editInsurance'] = false;
			$result['reduce'] = false;
			if ((0 == $socState || 4 == $socState) && (0 == $proState || 4 == $proState)) {
				$result['increase'] = true;
			//}else if (((0 == $socState || 1 == $socState || 4 == $socState) &&1 == $proState) || ((0 == $proState || 1 == $proState || 4 == $proState) &&1 == $socState)) {
			}else if ((0 == $socState || 1 == $socState || 4 == $socState) || (0 == $proState || 1 == $proState || 4 == $proState)) {
				$result['editIncrease'] = true;
			}else if (2 == $socState || 2 == $proState) {
				$result['reduce'] = true;
				$result['editInsurance'] = true;
			}
			return $result;
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}
	
}