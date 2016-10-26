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
 * 参保信息模型
 */
class PersonInsuranceInfoModel extends RelationModel{
    protected $tablePrefix = 'zbw_';
    //protected $tableName = 'person_insurance_info';

    /**
     * getIncreaseCount function
     * 获取1报增 3报减 2在保人数
     * @param int $companyId 企业用户ID
     * @param array $state 状态
     * @return void
     **/
    public function getIncreaseCount($userId,$state='1'){
        if ($userId) {
            $count=$this->field('base_id')->where(array('user_id'=>$userId,'state'=>$state,'operate_state'=>array('gt','0')))
            			->group('base_id')
            			->select();
            #return $this->_sql();
            return count($count);
        }else{
            $this->error = '非法参数!';
            return false;
        }
    }
    
	/**
	 * getInsuranceListByCondition function
	 * 根据条件获取参保列表
	 * param array $data 条件数组
	 * param int $type 类型 0未参保，1报增，2在保，3报减，4停保
	 * param int $pageSize 分页大小，默认10
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getInsuranceListByCondition($data,$type,$pageSize = 10){
		if (is_array($data)) {
			$condition = array();
			switch ($type) {
				//未参保
				case 0:
					$condition['_string'] = ' (socpii.state = '.$type.' or socpii.state is null) and (propii.state = '.$type.' or propii.state is null) ';
					break;
					
				//报增
				case 1:
					$condition['_complex'] = array('_logic'=>'or','socpii.state'=>$type,'propii.state'=>$type);
					break;
					
				//在保
				case 2:
					//$condition['_complex'] = array('_logic'=>'or','socpii.state'=>$type,'propii.state'=>$type);
					$condition['_string'] = ' (socpii.state = '.$type.' or socpii.state is null) and (propii.state = '.$type.' or propii.state is null) ';
					break;
					
				//报减
				case 3:
					$condition['_complex'] = array('_logic'=>'or','socpii.state'=>$type,'propii.state'=>$type);
					break;
				
				//停保
				case 4:
					//$condition['_complex'] = array('_logic'=>'or','socpii.state'=>$type,'propii.state'=>$type);
					$condition['_string'] = ' (socpii.state = '.$type.' or socpii.state is null) and (propii.state = '.$type.' or propii.state is null) ';
					break;
				
				default:
					$this->error = '参数错误！';
					return false;
					break;
			}
			
			if (!empty($data['location'])) {
				//$condition['socpii.location'] = $data['location'];
				//$condition['propii.location'] = $data['location'];
				$condition['_complex'] = array('_logic'=>'or','socpii.location'=>$data['location'],'propii.location'=>$data['location']);
			}
			if (!empty($data['person_name'])) {
				$condition['pb.person_name'] = $data['person_name'];
			}
			
			//dump($condition);
			
			//$socpii = $this->alias('pii')->field('pii.*,cib.company_id,cib.pro_order_id')->join('left join '.C('DB_PREFIX').'company_insurance_buy as cib on cib.insurance_id = pii.id')->where(array('pii.payment_type'=>1,'cib.company_id'=>$data['company_id']))->select(false);
			//$propii = $this->alias('pii')->field('pii.*,cib.company_id,cib.pro_order_id')->join('left join '.C('DB_PREFIX').'company_insurance_buy as cib on cib.insurance_id = pii.id')->where(array('pii.payment_type'=>2,'cib.company_id'=>$data['company_id']))->select(false);
			$socpii = $this->field(true)->where(array('payment_type'=>1,'user_id'=>$data['user_id']))->order('create_time desc, id desc')->select(false);
			$socpii = $this->table($socpii.' as pii')->group('base_id,user_id')->order('create_time DESC,modify_time DESC')->select(false);
			$propii = $this->field(true)->where(array('payment_type'=>2,'user_id'=>$data['user_id']))->order('create_time desc, id desc')->select(false);
			$propii = $this->table($propii.' as pii')->group('base_id,user_id')->order('create_time DESC,modify_time DESC')->select(false);
			
			$pageCount = $this->table($socpii.' as socpii')
					->join('left join '.$propii.' as propii on socpii.base_id = propii.base_id and socpii.user_id = propii.user_id')
					->join('left join '.C('DB_PREFIX').'person_base as pb on socpii.base_id = pb.id or propii.base_id = pb.id')
					->join('left join '.C('DB_PREFIX').'service_product as sp on socpii.product_id = sp.id or propii.product_id = sp.id')
					->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.id')
					->where($condition)->count();
			$page = get_page($pageCount,$pageSize);
			
			
			$result = $this->field('IFNULL(socpii.base_id,propii.base_id) as base_id,IFNULL(socpii.product_id,propii.product_id) as product_id,IFNULL(socpii.user_id,propii.user_id) as user_id,IF(socpii.modify_time>propii.modify_time,socpii.modify_time,propii.modify_time) as modify_time,pb.person_name,pb.card_num,sp.name as product_name,ci.company_name as service_company_name,socpii.id as socpiii_id,socpii.rule_id as socpiii_rule_id,socpii.location as socpiii_location,socpii.start_month as socpiii_start_month,socpii.end_month as socpiii_end_month,socpii.amount as socpiii_amount,socpii.payment_info as socpiii_payment_info,socpii.payment_type as socpiii_payment_type,socpii.audit as socpiii_audit,socpii.state as socpiii_state,propii.id as propii_id,propii.rule_id as propii_rule_id,propii.location as propii_location,propii.start_month as propii_start_month,propii.end_month as propii_end_month,propii.amount as propii_amount,propii.payment_info as propii_payment_info,propii.payment_type as propii_payment_type,propii.audit as propii_audit,propii.state as propii_state')->table($socpii.' as socpii')
					->join('left join '.$propii.' as propii on socpii.base_id = propii.base_id and socpii.user_id = propii.user_id')
					->join('left join '.C('DB_PREFIX').'person_base as pb on socpii.base_id = pb.id or propii.base_id = pb.id')
					->join('left join '.C('DB_PREFIX').'service_product as sp on socpii.product_id = sp.id or propii.product_id = sp.id')
					->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.id')
					->order('modify_time desc ')
					->where($condition)->limit($page->firstRow,$page->listRows)->select();
			
			if (!empty($result)) {
				//获取参保人参保状态
				$ruleIds = array();
				foreach ($result as $key => $value) {
					$ruleIds[$value['socpiii_rule_id']] = $value['socpiii_rule_id'];
					$ruleIds[$value['propii_rule_id']] = $value['propii_rule_id'];
				}
				$ruleIds = implode(',',array_filter($ruleIds));
				//dump($ruleIds);
				//$ruleIds = implode(',',array_unique($ruleIds));
				if ($ruleIds) {
					$templateRule = D('TemplateRule');
					$templateRuleResult = $templateRule->field('id,type,classify_mixed')->where(array('id'=>array('in',$ruleIds)))->select();
					//dump($templateRuleResult);
					if ($templateRuleResult) {
						$templateClassifyIds = array();
						$templateRuleArray = array();
						foreach ($templateRuleResult as $key => $value) {
							$templateRuleArray[$value['id']]['classifyMixedArray'] = explode('|',$value['classify_mixed']);
							if ($templateRuleArray[$value['id']]['classifyMixedArray']) {
								foreach ($templateRuleArray[$value['id']]['classifyMixedArray']  as $key => $value) {
									$templateClassifyIds[$value] = $value;
								}
							}
						}
						//dump($templateClassifyIds);
						//TODO:是否缓存所有数据
						if ($templateClassifyIds) {
							$templateClassify = D('TemplateClassify');
							$templateClassifyResult = $templateClassify->field('id,name,type,template_id')->where(array('id'=>array('in',$templateClassifyIds)))->select();
							//dump($templateClassifyResult);
							if ($templateClassifyResult) {
								$templateClassifyNameArray = array();
								foreach ($templateClassifyResult as $key => $value) {
									$templateClassifyNameArray[$value['id']] = $value['name'];
								}
								if ($templateClassifyNameArray) {
									foreach ($templateRuleArray as $k => $v) {
										foreach ($v['classifyMixedArray'] as $kk => $vv) {
											$templateRuleArray[$k]['classifyMixedValue'][] = $templateClassifyNameArray[$vv];
										}
										if ($templateRuleArray[$k]['classifyMixedValue']) {
											$templateRuleArray[$k]['classifyMixedValue'] = implode('/',$templateRuleArray[$k]['classifyMixedValue']);
										}
									}
								}
								//dump($templateClassifyNameArray);
								//dump($templateRuleArray);
							}
						}
					}
				}
				//dump($templateRuleArray);
				
				foreach ($result as $key => $value) {
					$result[$key]['socpiiiClassifyMixedValue'] = $templateRuleArray[$value['socpiii_rule_id']]['classifyMixedValue'];
					$result[$key]['propiiClassifyMixedValue'] = $templateRuleArray[$value['propii_rule_id']]['classifyMixedValue'];
					$result[$key]['socpiiiLocationValue'] = showAreaName($value['socpiii_location']);
					$result[$key]['propiiLocationValue'] = showAreaName($value['propii_location']);
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
			$this->error = '非法参数！';
			return false;
		}
	}
	
	/**
	 * getInsuranceDetailByCondition function
	 * 根据条件获取参保详情
	 * param array $data 条件数组
	 * param int $type 类型 0未参保，1报增，2在保，3报减，4停保
	 * param int $pageSize 分页大小，默认10
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getInsuranceDetailByCondition($data,$type,$pageSize = 10){
		if (is_array($data)) {
			$socpii = $this->field(true)->where(array('base_id'=>$data['base_id'],'payment_type'=>1,'user_id'=>$data['user_id']))->select(false);
			$propii = $this->field(true)->where(array('base_id'=>$data['base_id'],'payment_type'=>2,'user_id'=>$data['user_id']))->select(false);
			
			$result = $this->table($socpii.' as socpii')
					->field('IFNULL(socpii.base_id,propii.base_id) as base_id,IFNULL(socpii.product_id,propii.product_id) as product_id,IFNULL(socpii.user_id,propii.user_id) as user_id,IF(socpii.modify_time>propii.modify_time,socpii.modify_time,propii.modify_time) as modify_time,pb.person_name,pb.card_num,sp.name as product_name,ci.company_name as service_company_name,socpii.id as socpiii_id,socpii.rule_id as socpiii_rule_id,socpii.location as socpiii_location,socpii.start_month as socpiii_start_month,socpii.end_month as socpiii_end_month,socpii.amount as socpiii_amount,socpii.payment_info as socpiii_payment_info,socpii.payment_type as socpiii_payment_type,socpii.audit as socpiii_audit,socpii.state as socpiii_state,propii.id as propii_id,propii.rule_id as propii_rule_id,propii.location as propii_location,propii.start_month as propii_start_month,propii.end_month as propii_end_month,propii.amount as propii_amount,propii.payment_info as propii_payment_info,propii.payment_type as propii_payment_type,propii.audit as propii_audit,propii.state as propii_state')
					->join(' left join '.$propii.' as propii on socpii.base_id = propii.base_id and socpii.user_id = propii.user_id')
					->join('left join '.C('DB_PREFIX').'person_base as pb on socpii.base_id = pb.id or propii.base_id = pb.id')
					->join('left join '.C('DB_PREFIX').'service_product as sp on socpii.product_id = sp.id or propii.product_id = sp.id')
					->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.id')
					->order('modify_time desc ')->select();
			
			//dump($result);
			
			if ($result) {
				//查询订单信息
				$serviceInsuranceDetail = D('ServiceInsuranceDetail');
				foreach ($result as $key => $value) {
					//$result[$key]['service_order'] = $serviceInsuranceDetail->alias('sid')->field('so.*,sid.*')->join('left join '.C('DB_PREFIX').'service_order as so on so.id = sid.service_order_id')->where(array('sid.insurance_id'=>$result['id']))->select();
					
					$result[$key]['socpiii_service_order'] = $this->table(C('DB_PREFIX').'service_insurance_detail as sid')->field('so.*,sid.*')->join('left join '.C('DB_PREFIX').'service_order as so on so.id = sid.service_order_id')->where(array('sid.insurance_id'=>$result[$key]['socpiii_id']))->select();
					$result[$key]['propii_service_order'] = $this->table(C('DB_PREFIX').'service_insurance_detail as sid')->field('so.*,sid.*')->join('left join '.C('DB_PREFIX').'service_order as so on so.id = sid.service_order_id')->where(array('sid.insurance_id'=>$result[$key]['propii_id']))->select();
				}
			}
			
			//dump($this->_sql());
			
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
	 * addPersonInsurance function
	 * 添加参保详情
	 * param array $data 数据
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function addPersonInsurance($data){
		if (is_array($data)) {
			$personInsurance = D('PersonInsurance');
			$personInsuranceResult = $personInsurance->field('id,start_month')->where(array('user_id'=>$data['user_id'],'base_id'=>$data['base_id'],'payment_type'=>$data['payment_type']))->find();
			$personInsuranceData = $data;
			$personInsuranceData['end_month'] = 0;
			unset($personInsuranceData['handle_month']);
			if ($personInsuranceResult) {
				//更新数据
				unset($personInsuranceData['user_id']);
				unset($personInsuranceData['base_id']);
				unset($personInsuranceData['create_time']);
				/*if(!empty($personInsuranceResult['start_month'])){
					unset($personInsuranceData['start_month']);
				}*/
				if (1 == $data['state']) {
					$personInsuranceUpdateResult = $personInsurance->where(array('id'=>$personInsuranceResult['id']))->save($personInsuranceData);
				}else {
					$personInsuranceUpdateResult = $personInsurance->where(array('id'=>$personInsuranceResult['id']))->save(array('state'=>$data['state'],'end_month'=>0,'modify_time'=>$data['modify_time']));
				}
				$personInsuranceId = $personInsuranceResult['id'];
			}else {
				//新增数据
				$personInsuranceId = $personInsurance->add($personInsuranceData);
			}
			
			if ($personInsuranceId > 0) {
				$data['insurance_id'] = $personInsuranceId;
				$data['operate_state'] = 0;//未审核
				//$data['pay_order_id'] = 0;//无支付订单
				//$result = $this->field('id')->where(array('insurance_id'=>$personInsuranceId,'user_id'=>$data['user_id'],'base_id'=>$data['base_id'],'payment_type'=>$data['payment_type'],'handle_month'=>$data['handle_month']))->find();
				$result = $this->field('id,state,operate_state')->where(array('insurance_id'=>$personInsuranceId,'payment_type'=>$data['payment_type'],'handle_month'=>$data['handle_month']))->find();
				if ($result) {
					if (2 > $result['operate_state']) {
						//更新数据
						unset($data['create_time']);
						$saveResult = $this->where(array('id'=>$result['id']))->save($data);
						if ($saveResult || 0 === $saveResult) {
							return $result['id'];
						}else if (false === $saveResult) {
							$this->error = '系统内部错误！';
							wlog($this->getDbError());
							return false;
						}else {
							$this->error = '未知错误！';
							return false;
						}
					}else {
						if (3 == $result['state']) {
							$this->error = '当前月已报减成功,暂时不能报增！';
						}else {
							$this->error = '当前月不能报增！';
						}
						return false;
					}
				}else {
					//新增数据
					$saveResult = $this->add($data);
					if ($saveResult) {
						return $saveResult;
					}else if (false === $saveResult) {
						$this->error = '系统内部错误！';
						wlog($this->getDbError());
						return false;
					}else {
						$this->error = '未知错误！';
						return false;
					}
				}
			}else {
				$this->error = '保存失败！';
				return false;
			}
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}
	
	/**
	 * updatePersonInsurance function
	 * 更新参保详情
	 * param array $data 数据
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function updatePersonInsurance($data){
		if (is_array($data)) {
			$personInsurance = D('PersonInsurance');
			$personInsuranceResult = $personInsurance->field('id,start_month')->where(array('user_id'=>$data['user_id'],'base_id'=>$data['base_id'],'payment_type'=>$data['payment_type']))->find();
			$personInsuranceData = $data;
			unset($personInsuranceData['handle_month']);
			if ($personInsuranceResult) {
				//更新数据
				unset($personInsuranceData['user_id']);
				unset($personInsuranceData['base_id']);
				unset($personInsuranceData['create_time']);
				/*if(!empty($personInsuranceResult['start_month'])){
					unset($personInsuranceData['start_month']);
				}*/
				if (1 == $data['state']) {
					$personInsuranceUpdateResult = $personInsurance->where(array('id'=>$personInsuranceResult['id']))->save($personInsuranceData);
				}else {
					$personInsuranceUpdateResult = $personInsurance->where(array('id'=>$personInsuranceResult['id']))->save(array('state'=>$data['state'],'modify_time'=>$data['modify_time']));
				}
				$personInsuranceId = $personInsuranceResult['id'];
			}else {
				//新增数据
				$personInsuranceId = $personInsurance->add($personInsuranceData);
			}
			
			if ($personInsuranceId > 0) {
				$data['insurance_id'] = $personInsuranceId;
				//$result = $this->field('id')->where(array('insurance_id'=>$personInsuranceId,'user_id'=>$data['user_id'],'base_id'=>$data['base_id'],'payment_type'=>$data['payment_type'],'handle_month'=>$data['handle_month']))->find();
				$result = $this->field('id')->where(array('insurance_id'=>$personInsuranceId,'payment_type'=>$data['payment_type'],'handle_month'=>$data['handle_month']))->find();
				if ($result) {
					//更新数据
					unset($data['create_time']);
					$saveResult = $this->where(array('id'=>$result['id']))->save($data);
					if ($saveResult || 0 === $saveResult) {
						return $result['id'];
					}else if (false === $saveResult) {
						$this->error = '系统内部错误！';
						wlog($this->getDbError());
						return false;
					}else {
						$this->error = '未知错误！';
						return false;
					}
				}else {
					//新增数据
					$saveResult = $this->add($data);
					if ($saveResult) {
						return $saveResult;
					}else if (false === $saveResult) {
						$this->error = '系统内部错误！';
						wlog($this->getDbError());
						return false;
					}else {
						$this->error = '未知错误！';
						return false;
					}
				}
			}else {
				$this->error = '保存失败！';
				return false;
			}
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}
	
	/**
	 * getInsuranceCount function
	 * 根据条件获取参保订单列表
	 * param int $userId 用户id
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getInsuranceCount($userId){
		$countResult = array();
		$countResult[0] = $this->alias('pii')->field('pii.base_id,pii.handle_month')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->where(array('pb.user_id'=>$userId,'pii.operate_state'=>0,'pii.state'=>array('neq',0)))->group('pii.base_id, pii.handle_month')->select();
		//$countResult[2] = $this->alias('pii')->field('pii.base_id,pii.handle_month')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->where(array('pb.user_id'=>$userId,'pii.operate_state'=>1,'pii.pay_order_id'=>array('gt',0)))->group('pii.base_id, pii.handle_month')->select();
		//$countResult[2] = $this->alias('pii')->field('pii.base_id,pii.handle_month')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->where(array('pb.user_id'=>$userId,'pii.operate_state'=>1))->group('pii.base_id, pii.handle_month')->select();
		//$countResult[3] = $this->alias('pii')->field('pii.base_id,pii.handle_month')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->where(array('pb.user_id'=>$userId,'pii.operate_state'=>2))->group('pii.base_id, pii.handle_month')->select();
		$countResult[2] = $this->alias('pii')->field('pii.base_id,pii.handle_month,sid.pay_date')->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_info_id = pii.id')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->where(array('pb.user_id'=>$userId,'sid.state'=>1))->group('pii.base_id, pii.handle_month, sid.pay_date')->select();
		$countResult[3] = $this->alias('pii')->field('pii.base_id,pii.handle_month,sid.pay_date')->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_info_id = pii.id')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->where(array('pb.user_id'=>$userId,'sid.state'=>2))->group('pii.base_id, pii.handle_month, sid.pay_date')->select();
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
			if ('' !== $type) {
				if ('0' == $type) {
					$condition['pii.operate_state'] = array('eq',0);
					$condition['pii.state'] = array('neq','0');
				}else if ('1' == $type) {
					$condition['pii.operate_state'] = array('in','1,-1');
				}else if ('2' == $type) {
					$condition['pii.operate_state'] = array('eq',1);
					//$condition['pii.pay_order_id'] = array('gt',0);
				}else if ('3' == $type) {
					$condition['pii.operate_state'] = array('eq',2);
				}else if ('4' == $type) {
					$condition['pii.operate_state'] = array('eq',3);
				}
			}
			if (!empty($data['user_id'])) {
				$condition['pb.user_id'] = $data['user_id'];
			}
			if (!empty($data['product_id'])) {
				$condition['pii.product_id'] = $data['product_id'];
			}
			if (isset($data['state']) && '' !== $data['state'] && null !== $data['state']) {
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
			if (!empty($data['admin_id'])) {
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
			}
			if (!empty($data['person_name'])) {
				$condition['pb.person_name'] = array('like','%'.$data['person_name'].'%');
			}
			if (!empty($data['card_num'])) {
				$condition['pb.card_num'] = array('like','%'.$data['card_num'].'%');
			}
			if (isset($condition['pii.operate_state'])) {
				$condition['pii.operate_state'] = array($condition['pii.operate_state'],array('neq',-8),array('neq',-9),'and');
			}else {
				$condition['pii.operate_state'] = array(array('neq',-8),array('neq',-9),'and');
			}
			$pageResult = $this->alias('pii')->field('pii.base_id,pii.handle_month')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->where($condition)->group('pii.base_id, pii.handle_month')->select();
			$pageCount = count($pageResult);
			$page = get_page($pageCount,$pageSize);
			
			/*for ($i=1; $i <= 2; $i++) { 
				$condition['payment_type'] = $i;
				$piiCount[$i] = $this->alias('pii')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->where($condition)->count('pii.id');
			}
			$joinType = $piiCount[1]>=$piiCount[2]?'left':'right';*/
			
			$joinArray = [1=>'left',2=>'right'];
			for ($i=1; $i <= 2; $i++) { 
				$condition['payment_type'] = $i;
				//$piiSql[$i] = $this->alias('pii')->field('pii.*,pb.person_name,pb.card_num,pb.mobile,pb.audit,sp.name as product_name,ci.id as company_id,ci.company_name,po.order_no as pay_order_no,po.transaction_no as pay_transaction_no,po.pay_deadline as pay_deadline')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id = sp.company_id')->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=pii.pay_order_id')->where($condition)->order('create_time desc ')->select(false);
				$piiSql[$i] = $this->alias('pii')->field('pii.*,pb.person_name,pb.card_num,pb.mobile,pb.audit,sp.name as product_name,ci.id as company_id,ci.company_name')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id = sp.company_id')->where($condition)->order('create_time desc ')->select(false);
			}
			//dump($piiSql);
			//$result = $this->field('IFNULL(socpii.base_id,propii.base_id) as base_id,IFNULL(socpii.user_id,propii.user_id) as user_id,IF(socpii.modify_time>=propii.modify_time or propii.modify_time is null,socpii.modify_time,propii.modify_time) as modify_time,IFNULL(socpii.person_name,propii.person_name) as person_name,IFNULL(socpii.card_num,propii.card_num) as card_num,IFNULL(socpii.audit,propii.audit) as audit,IFNULL(socpii.product_name,propii.product_name) as product_name,IFNULL(socpii.company_name,propii.company_name) as company_name,IFNULL(socpii.location,propii.location) as location,socpii.id as socpii_id,socpii.rule_id as socpii_rule_id,socpii.product_id as socpii_product_id,socpii.location as socpii_location,socpii.start_month as socpii_start_month,socpii.handle_month as socpii_handle_month,socpii.pay_date as socpii_pay_date,socpii.amount as socpii_amount,socpii.payment_info as socpii_payment_info,socpii.payment_type as socpii_payment_type,socpii.state as socpii_state,socpii.operate_state as socpii_operate_state,socpii.pay_order_id as socpii_pay_order_id,socpii.pay_order_no as socpii_pay_order_no,socpii.pay_transaction_no as socpii_pay_transaction_no,socpii.pay_deadline as socpii_pay_deadline,socpii.remark as socpii_remark,socpii.create_time as socpii_create_time,propii.id as propii_id,propii.rule_id as propii_rule_id,propii.product_id as propii_product_id,propii.location as propii_location,propii.start_month as propii_start_month,propii.handle_month as propii_handle_month,propii.pay_date as propii_pay_date,propii.amount as propii_amount,propii.payment_info as propii_payment_info,propii.payment_type as propii_payment_type,propii.state as propii_state,propii.operate_state as propii_operate_state,propii.pay_order_id as propii_pay_order_id,propii.pay_order_no as propii_pay_order_no,propii.pay_transaction_no as propii_pay_transaction_no,propii.pay_deadline as propii_pay_deadline,propii.remark as propii_remark,propii.create_time as propii_create_time')->table($piiSql[1].' as socpii')->join($joinType.' join '.$piiSql[2].' as propii on socpii.base_id = propii.base_id and socpii.handle_month = propii.handle_month')->limit($page->firstRow,$page->listRows)->order('modify_time desc ')->select();
			
			$piiUnionSql = array();
			for ($i=1; $i <= 2 ; $i++) { 
				//$piiUnionSql[$i] = $this->field('IFNULL(socpii.base_id,propii.base_id) as base_id,IFNULL(socpii.user_id,propii.user_id) as user_id,IF(socpii.modify_time>=propii.modify_time or propii.modify_time is null,socpii.modify_time,propii.modify_time) as modify_time,IFNULL(socpii.person_name,propii.person_name) as person_name,IFNULL(socpii.card_num,propii.card_num) as card_num,IFNULL(socpii.audit,propii.audit) as audit,IFNULL(socpii.mobile,propii.mobile) as mobile,IFNULL(socpii.product_name,propii.product_name) as product_name,IFNULL(socpii.company_id,propii.company_id) as company_id,IFNULL(socpii.company_name,propii.company_name) as company_name,IFNULL(socpii.location,propii.location) as location,socpii.id as socpii_id,socpii.rule_id as socpii_rule_id,socpii.product_id as socpii_product_id,socpii.location as socpii_location,socpii.start_month as socpii_start_month,socpii.handle_month as socpii_handle_month,socpii.pay_date as socpii_pay_date,socpii.amount as socpii_amount,socpii.payment_info as socpii_payment_info,socpii.payment_type as socpii_payment_type,socpii.state as socpii_state,socpii.operate_state as socpii_operate_state,socpii.pay_order_id as socpii_pay_order_id,socpii.pay_order_no as socpii_pay_order_no,socpii.pay_transaction_no as socpii_pay_transaction_no,socpii.pay_deadline as socpii_pay_deadline,socpii.remark as socpii_remark,socpii.create_time as socpii_create_time,propii.id as propii_id,propii.rule_id as propii_rule_id,propii.product_id as propii_product_id,propii.location as propii_location,propii.start_month as propii_start_month,propii.handle_month as propii_handle_month,propii.pay_date as propii_pay_date,propii.amount as propii_amount,propii.payment_info as propii_payment_info,propii.payment_type as propii_payment_type,propii.state as propii_state,propii.operate_state as propii_operate_state,propii.pay_order_id as propii_pay_order_id,propii.pay_order_no as propii_pay_order_no,propii.pay_transaction_no as propii_pay_transaction_no,propii.pay_deadline as propii_pay_deadline,propii.remark as propii_remark,propii.create_time as propii_create_time')->table($piiSql[1].' as socpii')->join($joinArray[$i].' join '.$piiSql[2].' as propii on socpii.base_id = propii.base_id and socpii.handle_month = propii.handle_month')->order('modify_time desc ')->select(false);
				$piiUnionSql[$i] = $this->field('IFNULL(socpii.base_id,propii.base_id) as base_id,IFNULL(socpii.user_id,propii.user_id) as user_id,IF(socpii.modify_time>=propii.modify_time or propii.modify_time is null,socpii.modify_time,propii.modify_time) as modify_time,IFNULL(socpii.person_name,propii.person_name) as person_name,IFNULL(socpii.card_num,propii.card_num) as card_num,IFNULL(socpii.audit,propii.audit) as audit,IFNULL(socpii.mobile,propii.mobile) as mobile,IFNULL(socpii.product_name,propii.product_name) as product_name,IFNULL(socpii.company_id,propii.company_id) as company_id,IFNULL(socpii.company_name,propii.company_name) as company_name,IFNULL(socpii.location,propii.location) as location,socpii.id as socpii_id,socpii.rule_id as socpii_rule_id,socpii.product_id as socpii_product_id,socpii.location as socpii_location,socpii.start_month as socpii_start_month,socpii.handle_month as socpii_handle_month,socpii.pay_date as socpii_pay_date,socpii.amount as socpii_amount,socpii.payment_info as socpii_payment_info,socpii.payment_type as socpii_payment_type,socpii.state as socpii_state,socpii.operate_state as socpii_operate_state,socpii.remark as socpii_remark,socpii.create_time as socpii_create_time,propii.id as propii_id,propii.rule_id as propii_rule_id,propii.product_id as propii_product_id,propii.location as propii_location,propii.start_month as propii_start_month,propii.handle_month as propii_handle_month,propii.pay_date as propii_pay_date,propii.amount as propii_amount,propii.payment_info as propii_payment_info,propii.payment_type as propii_payment_type,propii.state as propii_state,propii.operate_state as propii_operate_state,propii.remark as propii_remark,propii.create_time as propii_create_time')->table($piiSql[1].' as socpii')->join($joinArray[$i].' join '.$piiSql[2].' as propii on socpii.base_id = propii.base_id and socpii.handle_month = propii.handle_month')->order('modify_time desc ')->select(false);
			}
			//$result = $this->field(true)->table($piiUnionSql[1].' as piia')->union($piiUnionSql[2].'')->limit($page->firstRow,$page->listRows)->select();
			//$result = $this->table($piiUnionSql[1].' as piia')->union($piiUnionSql[2].'')->select();
			
			$piiUnionSql = $this->table($piiUnionSql[1].' as piia')->union($piiUnionSql[2].'')->select(false);
			$result = $this->table($piiUnionSql.'as pii')->limit($page->firstRow,$page->listRows)->select();
			
			if ($result) {
				foreach ($result as $key => $value) {
					//$result[$key]['socpiiLocationValue'] = showAreaName($value['socpii_location']);
					//$result[$key]['propiiLocationValue'] = showAreaName($value['propii_location']);
					//$result[$key]['socpiiPayDeadline'] = get_deadline($value['socpii_pay_deadline']);
					//$result[$key]['propiiPayDeadline'] = get_deadline($value['propii_pay_deadline']);
					$result[$key]['locationValue'] = showAreaName($value['location']);
					$result[$key]['count'] = ($value['socpii_id']?1:0)+($value['propii_id']?1:0);
				}
			}
			//dump($result);
			if ($result || null === $result) {
				return array('data'=>$result,'page'=>$page->show(),'count'=>$this->getInsuranceCount($data['user_id']));
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
			if ('' !== $type) {
				/*if ('0' == $type) {
					$condition['pii.operate_state'] = array('eq',0);
					$condition['pii.state'] = array('neq','0');
				}else if ('1' == $type) {
					$condition['pii.operate_state'] = array('in','1,-1');
				}else if ('2' == $type) {
					$condition['pii.operate_state'] = array('eq',1);
				}else if ('3' == $type) {
					$condition['pii.operate_state'] = array('eq',2);
				}else if ('4' == $type) {
					$condition['pii.operate_state'] = array('eq',3);
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
			if (isset($data['state']) && '' !== $data['state'] && null !== $data['state']) {
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
			if (!empty($data['admin_id'])) {
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
			}
			if (!empty($data['person_name'])) {
				$condition['pb.person_name'] = array('like','%'.$data['person_name'].'%');
			}
			if (!empty($data['card_num'])) {
				$condition['pb.card_num'] = array('like','%'.$data['card_num'].'%');
			}
			if (isset($condition['pii.operate_state'])) {
				$condition['pii.operate_state'] = array($condition['pii.operate_state'],array('neq',-8),array('neq',-9),'and');
			}else {
				$condition['pii.operate_state'] = array(array('neq',-8),array('neq',-9),'and');
			}
			//$pageResult = $this->alias('pii')->field('pii.base_id,pii.handle_month')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->where($condition)->group('pii.base_id, pii.handle_month')->select();
			
			$pageResult = $this->alias('pii')->field('pii.base_id,pii.handle_month,sid.pay_date')->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_info_id = pii.id')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->where($condition)->group('pii.base_id, pii.handle_month, sid.pay_date')->select();
			//dump($pageResult);
			//dump($this->_sql());
			$pageCount = count($pageResult);
			$page = get_page($pageCount,$pageSize);
			$joinArray = [1=>'left',2=>'right'];
			for ($i=1; $i <= 2; $i++) { 
				$condition['pii.payment_type'] = $i;
				$piiSql[$i] = $this->alias('pii')->field('pii.*,sid.id as sid_id,sid.pay_date as sid_pay_date,sid.state as sid_state,pb.person_name,pb.card_num,pb.mobile,pb.audit,sp.name as product_name,ci.id as company_id,ci.company_name,po.order_no as pay_order_no,po.transaction_no as pay_transaction_no,po.pay_deadline as pay_deadline')->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_info_id = pii.id')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id = sp.company_id')->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=sid.pay_order_id')->where($condition)->order('create_time desc ')->select(false);
			}
			//dump($piiSql);
			$piiUnionSql = array();
			for ($i=1; $i <= 2 ; $i++) {
				$piiUnionSql[$i] = $this->field('IFNULL(socpii.base_id,propii.base_id) as base_id,IFNULL(socpii.user_id,propii.user_id) as user_id,IF(socpii.modify_time>=propii.modify_time or propii.modify_time is null,socpii.modify_time,propii.modify_time) as modify_time,IFNULL(socpii.person_name,propii.person_name) as person_name,IFNULL(socpii.card_num,propii.card_num) as card_num,IFNULL(socpii.audit,propii.audit) as audit,IFNULL(socpii.mobile,propii.mobile) as mobile,IFNULL(socpii.product_name,propii.product_name) as product_name,IFNULL(socpii.company_id,propii.company_id) as company_id,IFNULL(socpii.company_name,propii.company_name) as company_name,IFNULL(socpii.location,propii.location) as location,socpii.id as socpii_id,socpii.rule_id as socpii_rule_id,socpii.product_id as socpii_product_id,socpii.location as socpii_location,socpii.start_month as socpii_start_month,socpii.handle_month as socpii_handle_month,socpii.pay_date as socpii_pay_date,socpii.amount as socpii_amount,socpii.payment_info as socpii_payment_info,socpii.payment_type as socpii_payment_type,socpii.state as socpii_state,socpii.operate_state as socpii_operate_state,socpii.sid_id as socpii_sid_id,socpii.sid_pay_date as socpii_sid_pay_date,socpii.sid_state as socpii_sid_state,socpii.pay_order_id as socpii_pay_order_id,socpii.pay_order_no as socpii_pay_order_no,socpii.pay_transaction_no as socpii_pay_transaction_no,socpii.pay_deadline as socpii_pay_deadline,socpii.remark as socpii_remark,socpii.create_time as socpii_create_time,propii.id as propii_id,propii.rule_id as propii_rule_id,propii.product_id as propii_product_id,propii.location as propii_location,propii.start_month as propii_start_month,propii.handle_month as propii_handle_month,propii.pay_date as propii_pay_date,propii.amount as propii_amount,propii.payment_info as propii_payment_info,propii.payment_type as propii_payment_type,propii.state as propii_state,propii.operate_state as propii_operate_state,propii.sid_id as propii_sid_id,propii.sid_pay_date as propii_sid_pay_date,propii.sid_state as propii_sid_state,propii.pay_order_id as propii_pay_order_id,propii.pay_order_no as propii_pay_order_no,propii.pay_transaction_no as propii_pay_transaction_no,propii.pay_deadline as propii_pay_deadline,propii.remark as propii_remark,propii.create_time as propii_create_time')->table($piiSql[1].' as socpii')->join($joinArray[$i].' join '.$piiSql[2].' as propii on socpii.base_id = propii.base_id and socpii.handle_month = propii.handle_month and socpii.sid_pay_date = propii.sid_pay_date')->order('modify_time desc ')->select(false);
			}
			//$result = $this->field(true)->table($piiUnionSql[1].' as piia')->union($piiUnionSql[2].'')->limit($page->firstRow,$page->listRows)->select();
			//$result = $this->table($piiUnionSql[1].' as piia')->union($piiUnionSql[2].'')->select();
			//dump($piiUnionSql);
			$piiUnionSql = $this->table($piiUnionSql[1].' as piia')->union($piiUnionSql[2].'')->select(false);
			$result = $this->table($piiUnionSql.'as pii')->limit($page->firstRow,$page->listRows)->select();
				
			if ($result) {
				foreach ($result as $key => $value) {
					//$result[$key]['socpiiPayDeadline'] = get_deadline($value['socpii_pay_deadline']);
					//$result[$key]['propiiPayDeadline'] = get_deadline($value['propii_pay_deadline']);
					$result[$key]['locationValue'] = showAreaName($value['location']);
					$result[$key]['count'] = ($value['socpii_id']?1:0)+($value['propii_id']?1:0);
				}
			}
			//dump($result);
			if ($result || null === $result) {
				return array('data'=>$result,'page'=>$page->show(),'count'=>$this->getInsuranceCount($data['user_id']));
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
	 * getServiceOrderDetailByCondition function
	 * 根据条件获取服务订单详情
	 * param array $data 条件数组
	 * param int $type 类型 0未参保，1报增，2在保，3报减，4停保
	 * param boolean $isGetOrderDetail 是否获取参保订单详情
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getServiceOrderDetailByCondition($data,$type,$isGetOrderDetail = true){
		if (is_array($data)) {
			$result = array();
			$result[1] = $this->alias('pii')->field(true)->where(array('pii.user_id'=>$data['user_id'],'pii.base_id'=>$data['base_id'],'pii.payment_type'=>1,'state'=>$type))->order('create_time desc, id desc')->find();
			$result[2] = $this->alias('pii')->field(true)->where(array('pii.user_id'=>$data['user_id'],'pii.base_id'=>$data['base_id'],'pii.payment_type'=>2,'state'=>$type))->order('create_time desc, id desc')->find();
			if ($result && $isGetOrderDetail) {
				$serviceInsuranceDetail = D('ServiceInsuranceDetail');
				foreach ($result as $key => $value) {
					if ($value) {
						//$result[$key]['data'] = $serviceInsuranceDetail->field(true)->where(array('insurance_id'=>$value['id'],'type'=>$type))->order('create_time desc')->select();
						$result[$key]['data'] = $serviceInsuranceDetail->field(true)->where(array('insurance_info_id'=>$value['id']))->order('create_time desc, id desc')->select();
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
	 * getWarrantyServiceOrderDetail function
	 * 获取在保服务订单详情
	 * param array $data 条件数组
	 * param boolean $isGetOrderDetail 是否获取参保订单详情
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getWarrantyServiceOrderDetail($data,$isGetOrderDetail = true){
		if (is_array($data)) {
			$result = array();
			$result[1] = $this->alias('pii')->field('pii.*,sp.company_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->where(array('pii.user_id'=>$data['user_id'],'pii.base_id'=>$data['base_id'],'pii.payment_type'=>1,'_complex'=>array('pii.state'=>2,array('pii.state'=>1,'pii.operate_state'=>3),'_logic'=>'or')))->order('pii.create_time desc, pii.id desc')->find();
			$result[2] = $this->alias('pii')->field('pii.*,sp.company_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = pii.product_id')->where(array('pii.user_id'=>$data['user_id'],'pii.base_id'=>$data['base_id'],'pii.payment_type'=>2,'_complex'=>array('pii.state'=>2,array('pii.state'=>1,'pii.operate_state'=>3),'_logic'=>'or')))->order('pii.create_time desc, pii.id desc')->find();
			if ($result && $isGetOrderDetail) {
				$serviceInsuranceDetail = D('ServiceInsuranceDetail');
				foreach ($result as $key => $value) {
					if ($value) {
						$result[$key]['data'] = $serviceInsuranceDetail->field(true)->where(array('insurance_info_id'=>$value['id']))->order('create_time desc, id desc')->select();
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
						/*$diffCronResult = $diffCron->field(true)->where(array('detail_id'=>$value['sid_id'],'type'=>3))->find();
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
						}*/
						
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
	 * getPersonInsuranceInfo function
	 * 根据条件获取服务订单详情
	 * param array $data 条件数组
	 * param int $type 类型 0未参保，1报增，2在保，3报减，4停保
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getPersonInsuranceInfo($data,$type){
		if (is_array($data)) {
			$result = array();
			$result[1] = $this->alias('pii')->field(true)->where(array('pii.user_id'=>$data['user_id'],'pii.base_id'=>$data['base_id'],'pii.payment_type'=>1,'state'=>$type))->order('create_time desc, id desc')->find();
			$result[2] = $this->alias('pii')->field(true)->where(array('pii.user_id'=>$data['user_id'],'pii.base_id'=>$data['base_id'],'pii.payment_type'=>2,'state'=>$type))->order('create_time desc, id desc')->find();
			
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
	 * getPersonInsuranceInfoByHandleMonth function
	 * 根据办理年月条件获取服务订单详情
	 * param array $data 条件数组
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getPersonInsuranceInfoByHandleMonth($data){
		if (is_array($data)) {
			$result = array();
			$result[1] = $this->alias('pii')->field(true)->where(array('pii.user_id'=>$data['user_id'],'pii.base_id'=>$data['base_id'],'pii.payment_type'=>1,'handle_month'=>$data['handle_month']))->order('create_time desc, id desc')->find();
			$result[2] = $this->alias('pii')->field(true)->where(array('pii.user_id'=>$data['user_id'],'pii.base_id'=>$data['base_id'],'pii.payment_type'=>2,'handle_month'=>$data['handle_month']))->order('create_time desc, id desc')->find();
			
			if ($result || null === $result) {
				$template = D('Template');
				foreach ($result as $key => $value) {
					/*if ($value['location']) {
						$location = ($value['location']/1000<<0)*1000;
						$templateResult = $template->getTemplateByCondition(array('location'=>$location+100));
						if ($templateResult && $templateResult['soc_deadline']) {
							$handleMonth = $value['handle_month'];
							$payDeadline = date('Y-m-d',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(int_to_date($handleMonth,'-').'-'.sprintf('%02d',$templateResult['soc_deadline']))));
							$result[$key]['payDeadline'] = $payDeadline;
							//$result[$key]['whetherToOperate'] = time() <= strtotime($payDeadline)+($result[1]['operate_state']>=2 || $result[2]['operate_state']>=2?C('INSURANCE_HANDLE_DAYS')*86400:0);
							$result[$key]['whetherToOperate'] = time() <= strtotime($payDeadline);
						}
					}else {
						$result[$key]['whetherToOperate'] = true;
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
					}else {
						$result[1]['whetherToOperate'] = $result[2]['whetherToOperate'] = true;
					}
				}
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
	 * cancel function
	 * 撤销
	 * param array $data 数据数组
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function cancel($data){
		if (is_array($data)) {
			//if (1 == $data['type']) {
				//撤销报增
				//审核中或审核失败才能撤销
				$personInsuranceInfoResult = $this->field(true)->where(array('id'=>array('in',$data['id']),'base_id'=>$data['baseId'],'operate_state'=>array(0,-1,'or')))->select();
				if ($personInsuranceInfoResult) {
					$nowTime = date('Y-m-d H:i:s');
					$serviceInsuranceDetail = D('ServiceInsuranceDetail');
					$this->startTrans();
					$result = array();
					$result['totalCount'] = 0;
					$result['successCount'] = 0;
					foreach ($personInsuranceInfoResult as $key => $value) {
						$result['totalCount'] ++;
						$personInsuranceInfoSaveResult = $this->where(array('id'=>$value['id']))->save(array('operate_state'=>-9,'modify_time'=>$nowTime));
						if (false !== $personInsuranceInfoSaveResult) {
							$serviceInsuranceDetailSaveResult = $serviceInsuranceDetail->where(array('insurance_info_id'=>$value['id'],'state'=>array(0,-1,'or')))->save(array('state'=>-9,'modify_time'=>$nowTime));
							if (false !== $serviceInsuranceDetailSaveResult) {
								//TODO:撤销后更新PersonInsurance表数据
								$result['successCount'] ++;
							}
						}
					}
					if (0 != $result['totalCount'] && $result['totalCount'] == $result['successCount']) {
						$this->commit();
						$result['info'] = '撤销成功！';
						return $result;
					}else {
						$this->rollback();
						$this->error = '撤销失败！';
						return false;
					}
				}else {
					$this->error = '审核状态错误！';
					return false;
				}
			//}else if (2 == $data['type']) {
				//撤销报减
			//}else {
    		//	$this->error = '类型错误！';
    		//	return false;
			//}
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
	
}