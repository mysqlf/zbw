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
 * 用户参保信息模型
 */
class PersonInsuranceModel extends RelationModel{
    protected $tablePrefix = 'zbw_';
    /**
     * [getInsuranceCountByCondition 统计社保公积金人数]
     * @param  [type] $data [description]
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public function getInsuranceCountByCondition($data,$type){
		if (is_array($data)) {
			$condition = array();
			switch ($type) {
				//所有
				case '-1':
					break;
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
					//$this->error = '参数错误！';
					//return false;
					break;
			}
			$socpi = $this->field(true)->where(array('payment_type'=>1,'user_id'=>$data['user_id']))->select(false);
			$propi = $this->field(true)->where(array('payment_type'=>2,'user_id'=>$data['user_id']))->select(false);
			$Count = $this->table($socpi.' as socpi')->join('left join '.$propi.' as propi on socpi.base_id = propi.base_id and socpi.user_id = propi.user_id')->join('left join '.C('DB_PREFIX').'person_base as pb on socpi.base_id = pb.id or propi.base_id = pb.id')->where($condition)->count();
			return $Count;
		}
	}
	/**
	 * getInsuranceListByCondition function
	 * 根据条件获取参保列表
	 * param array $data 条件数组
	 * param int $type 类型 0未参保，1报增，2在保，3报减，4停保
	 * param int $pageSize 分页大小，默认10
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getInsuranceListByCondition($data,$type,$pageSize = 10){
		if (is_array($data)) {
			$condition = array();
			switch ($type) {
				//所有
				case '-1':
					break;
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
					//$this->error = '参数错误！';
					//return false;
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
			
			//dump($condition);
			
			$socpi = $this->field(true)->where(array('payment_type'=>1,'user_id'=>$data['user_id']))->select(false);
			$propi = $this->field(true)->where(array('payment_type'=>2,'user_id'=>$data['user_id']))->select(false);
			
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
							/*$templateRuleArray[$value['id']]['classifyMixedArray'] = explode('|',$value['classify_mixed']);
							if ($templateRuleArray[$value['id']]['classifyMixedArray']) {
								foreach ($templateRuleArray[$value['id']]['classifyMixedArray']  as $key => $value) {
									$templateClassifyIds[$value] = $value;
								}
							}*/
						}
						//TODO:是否缓存所有数据
						/*if ($templateClassifyIds) {
							$templateClassify = D('TemplateClassify');
							$templateClassifyResult = $templateClassify->field('id,name,type,template_id')->where(array('id'=>array('in',$templateClassifyIds)))->select();
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
							}
						}*/
					}
				}
				
				foreach ($result as $key => $value) {
					//$result[$key]['socpiClassifyMixedValue'] = $templateRuleArray[$value['socpi_rule_id']]['classifyMixedValue'];
					//$result[$key]['propiClassifyMixedValue'] = $templateRuleArray[$value['propi_rule_id']]['classifyMixedValue'];
					$result[$key]['socpiRuleName'] = $value['socpi_rule_id']>0?$templateRuleArray[$value['socpi_rule_id']]['ruleName']:'';
					$result[$key]['propiRuleName'] = $value['propi_rule_id']>0?$templateRuleArray[$value['propi_rule_id']]['ruleName']:'';
					$result[$key]['socpiLocationValue'] = $value['socpi_location']>0?showAreaName($value['socpi_location']):'';
					$result[$key]['propiLocationValue'] = $value['propi_location']>0?showAreaName($value['propi_location']):'';
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
			$this->error = '非法参数！';
			return false;
		}
	}
	
	/**
	 * getInsuranceDetailByCondition function
	 * 根据条件获取参保详情
	 * param array $data 条件数组
	 * param int $pageSize 分页大小，默认10
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getInsuranceDetailByCondition($data){
		if (is_array($data)) {
			$socpi = $this->field(true)->where(array('base_id'=>$data['base_id'],'payment_type'=>1,'user_id'=>$data['user_id']))->select(false);
			$propi = $this->field(true)->where(array('base_id'=>$data['base_id'],'payment_type'=>2,'user_id'=>$data['user_id']))->select(false);
			
			$result = $this->table($socpi.' as socpi')
					->field('IFNULL(socpi.base_id,propi.base_id) as base_id,IFNULL(socpi.user_id,propi.user_id) as user_id,IF(socpi.modify_time>propi.modify_time,socpi.modify_time,propi.modify_time) as modify_time,pb.person_name,pb.card_num,pb.residence_location,pb.residence_type,socsp.name as soc_product_name,socci.company_name as socci_service_company_name,prosp.name as pro_product_name,proci.company_name as proci_service_company_name,socpi.id as socpi_id,socpi.rule_id as socpi_rule_id,socpi.product_id as socpi_product_id,socpi.location as socpi_location,socpi.start_month as socpi_start_month,socpi.end_month as socpi_end_month,socpi.amount as socpi_amount,socpi.payment_info as socpi_payment_info,socpi.payment_type as socpi_payment_type,socpi.state as socpi_state,propi.id as propi_id,propi.rule_id as propi_rule_id,propi.product_id as propi_product_id,propi.location as propi_location,propi.start_month as propi_start_month,propi.end_month as propi_end_month,propi.amount as propi_amount,propi.payment_info as propi_payment_info,propi.payment_type as propi_payment_type,propi.state as propi_state')
					->join(' left join '.$propi.' as propi on socpi.base_id = propi.base_id and socpi.user_id = propi.user_id')
					->join('left join '.C('DB_PREFIX').'person_base as pb on socpi.base_id = pb.id or propi.base_id = pb.id')
					->join('left join '.C('DB_PREFIX').'service_product as socsp on socpi.product_id = socsp.id')
					->join('left join '.C('DB_PREFIX').'company_info as socci on socsp.company_id = socci.id')
					->join('left join '.C('DB_PREFIX').'service_product as prosp on propi.product_id = prosp.id')
					->join('left join '.C('DB_PREFIX').'company_info as proci on prosp.company_id = proci.id')
					->order('modify_time desc ')->find();
			
			if ($result) {
				//查询订单信息
				$condition = array('pii.base_id'=>$data['base_id'],'pii.user_id'=>$data['user_id']);
				$data['pay_date'] && $condition['sid.pay_date'] = $data['pay_date'];
				
				$pii = $this->table(C('DB_PREFIX').'person_insurance_info as pii')->field('pii.rule_id as pii_rule_id,pii.location as pii_location,pii.payment_info as pii_payment_info,pii.payment_type as pii_payment_type,tr.name as tr_name,tr.classify_mixed as tr_classify_mixed,tr.rule as tr_rule,so.order_no as so_order_no,so.state as so_state,so.diff_amount as so_diff_amount,sid.id as sid_id,sid.insurance_id,sid.service_order_id,sid.type as sid_type,sid.amount as sid_amount,sid.pay_date as sid_pay_date,sid.note as sid_note,sid.state as sid_state,sid.create_time as sid_create_time,spo.id as spo_id,spo.product_id as spo_product_id,spo.price as spo_price,spo.modify_price as spo_modify_price,wl.soc_service_price as wl_soc_service_price,wl.pro_service_price as wl_pro_service_price,wl.af_service_price as wl_af_service_price')->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_id = pii.id')->join('left join '.C('DB_PREFIX').'service_order as so on so.id = sid.service_order_id')->join('left join '.C('DB_PREFIX').'template_rule as tr on tr.id = pii.rule_id')->join('left join '.C('DB_PREFIX').'service_product_order as spo on spo.product_id = pii.product_id and spo.service_state = 2 and spo.user_id = '.$data['user_id'])->join('left join '.C('DB_PREFIX').'warranty_location as wl on wl.service_product_order_id = spo.id and wl.location = pii.location')->where($condition)->order('sid.pay_date desc, sid.create_time asc')->select();
				$piiArray = array();
				if ($pii) {
					/* $json = json_encode(array('amount'=>100.00,'month'=>3));
					 * $SocInsure = new Calculate();
					 * $json = $SocInsure->detail($rule , $json , 1);
					 * 公积金实例
					 * $json = json_encode(array('amount'=>2000.00,'month'=>3 , 'personScale'=>'5%' , 'companyScale'=>'5%' 'cardno'=>''));
					 * $SocInsure = new Calculate();
					 * $json = $SocInsure->detail($rule , $json , 2);
					 */
					$calculate = new \Common\Model\Calculate();
					foreach ($pii as $key => $value) {
						//计算社保和公积金数据
						$calculateArray = array('amount'=>$value['sid_amount'],'month'=>1);
						if (2 == $value['pii_payment_type']) {
							$paymentInfo = json_decode($value['pii_payment_info'],true);
							$calculateArray['personScale'] = $paymentInfo['personScale'];
							$calculateArray['companyScale'] = $paymentInfo['companyScale'];
							$calculateArray['cardno'] = $paymentInfo['cardno'];
						}
						
						$value['calculateResult'] = json_decode($calculate->detail($value['tr_rule'],json_encode($calculateArray),$value['pii_payment_type']),true);
						$value['calculateResult'] = 0 === $value['calculateResult']['state'] ? $value['calculateResult']['data']:array();
						$piiArray[$value['sid_pay_date']][$value['pii_payment_type']] = $value;
					}
				}
				$result['pii'] = $piiArray;
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
			//dump($this->_sql());
			//dump($result);
			
			if ($result && $isGetPaymentMonth) {
				$condition = array('pii.base_id'=>$data['base_id'],'pii.user_id'=>$data['user_id']);
				//$pii = $this->table(C('DB_PREFIX').'person_insurance_info as pii')->field('sid.pay_date as sid_pay_date')->join('left join '.C('DB_PREFIX').'service_order_insurance as soi on soi.insurance_id = pii.id')->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.service_order_insurance_id = soi.id')->join('left join '.C('DB_PREFIX').'service_order as so on so.id = soi.service_order_id')->where($condition)->order('sid.pay_date asc')->group('sid.pay_date')->select();
				//$pii = $this->table(C('DB_PREFIX').'person_insurance_info as pii')->field('sid.pay_date as sid_pay_date')->join('left join '.C('DB_PREFIX').'service_order_insurance as soi on soi.insurance_id = pii.id')->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.service_order_insurance_id = soi.id')->where($condition)->order('sid.pay_date asc')->group('sid.pay_date')->select();
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
	 * getInsurancePayDateDetailByCondition function
	 * 根据条件获取个人参保信息
	 * param array $data 条件数组
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getInsurancePayDateDetailByCondition($data){
		if (is_array($data)) {
			//$condition = array('pii.base_id'=>$data['base_id'],'pii.user_id'=>$data['user_id'],'sid.pay_date'=>$data['pay_date']);
			$condition = array('pii.base_id'=>$data['base_id'],'pii.user_id'=>$data['user_id']);
			$data['pay_date'] && $condition['sid.pay_date'] = $data['pay_date'];
			
			$pii = $this->table(C('DB_PREFIX').'person_insurance_info as pii')
					//->field('pii.rule_id as pii_rule_id,pii.location as pii_location,pii.payment_info as pii_payment_info,pii.payment_type as pii_payment_type,pb.person_name as pb_person_name,pb.card_num as pb_card_num,pb.residence_location as pb_residence_location,pb.residence_type as pb_residence_type,pb.mobile as pb_mobile,pb.gender as pb_gender,tr.name as tr_name,tr.classify_mixed as tr_classify_mixed,tr.rule as tr_rule,so.order_no as so_order_no,so.state as so_state,so.diff_amount as so_diff_amount,soi.insurance_id as soi_insurance_id,soi.service_order_id as soi_service_order_id,sid.id as sid_id,sid.type as sid_type,sid.amount as sid_amount,sid.pay_date as sid_pay_date,sid.note as sid_note,sid.state as sid_state,sid.is_hang_up as sid_is_hang_up,sid.create_time as sid_create_time,spo.id as spo_id,spo.product_id as spo_product_id,spo.price as spo_price,spo.modify_price as spo_modify_price,wl.soc_service_price as wl_soc_service_price,wl.pro_service_price as wl_pro_service_price,wl.af_service_price as wl_af_service_price')
					//->field('pii.rule_id as pii_rule_id,pii.location as pii_location,pii.payment_info as pii_payment_info,pii.payment_type as pii_payment_type,pb.person_name as pb_person_name,pb.card_num as pb_card_num,pb.residence_location as pb_residence_location,pb.residence_type as pb_residence_type,pb.mobile as pb_mobile,pb.gender as pb_gender,tr.name as tr_name,tr.classify_mixed as tr_classify_mixed,tr.rule as tr_rule,soi.insurance_id as soi_insurance_id,sid.id as sid_id,sid.type as sid_type,sid.amount as sid_amount,sid.pay_date as sid_pay_date,sid.note as sid_note,sid.state as sid_state,sid.is_hang_up as sid_is_hang_up,sid.create_time as sid_create_time,spo.id as spo_id,spo.product_id as spo_product_id,spo.price as spo_price,spo.modify_price as spo_modify_price,wl.soc_service_price as wl_soc_service_price,wl.pro_service_price as wl_pro_service_price,wl.af_service_price as wl_af_service_price')
					->field('pii.rule_id as pii_rule_id,pii.location as pii_location,pii.payment_info as pii_payment_info,pii.payment_type as pii_payment_type,pb.person_name as pb_person_name,pb.card_num as pb_card_num,pb.residence_location as pb_residence_location,pb.residence_type as pb_residence_type,pb.mobile as pb_mobile,pb.gender as pb_gender,tr.template_id as tr_template_id,tr.company_id as tr_company_id,tr.name as tr_name,tr.classify_mixed as tr_classify_mixed,tr.rule as tr_rule,sid.id as sid_id,sid.type as sid_type,sid.amount as sid_amount,sid.pay_date as sid_pay_date,sid.note as sid_note,sid.state as sid_state,sid.is_hang_up as sid_is_hang_up,sid.replenish as sid_replenish,sid.create_time as sid_create_time,spo.id as spo_id,spo.product_id as spo_product_id,spo.price as spo_price,spo.modify_price as spo_modify_price,wl.soc_service_price as wl_soc_service_price,wl.pro_service_price as wl_pro_service_price,wl.af_service_price as wl_af_service_price')
					->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id = pii.base_id')
					//->join('left join '.C('DB_PREFIX').'service_order_insurance as soi on soi.insurance_id = pii.id')
					->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_info_id = pii.id')
					//->join('left join '.C('DB_PREFIX').'service_order as so on so.id = soi.service_order_id')
					->join('left join '.C('DB_PREFIX').'template_rule as tr on tr.id = pii.rule_id')
					->join('left join '.C('DB_PREFIX').'service_product_order as spo on spo.product_id = pii.product_id and spo.service_state = 2 and spo.user_id = '.$data['user_id'])
					//->join('left join '.C('DB_PREFIX').'service_product as sp on spo.product_id = sp.id')
					->join('left join '.C('DB_PREFIX').'warranty_location as wl on wl.service_product_order_id = spo.id and wl.location = pii.location')
					->where($condition)->order('sid.pay_date desc, sid.create_time asc')->select();
			if ($pii) {
				
				$calculate = new \Common\Model\Calculate();
				$templateRule = D('TemplateRule');
				$result = array();
				foreach ($pii as $key => $value) {
					//服务费
					$servicePrice = array();
					$servicePrice[1] = $value['wl_soc_service_price'];
					$servicePrice[2] = $value['wl_pro_service_price'];
					//dump($servicePrice);
					//dump($value['pii_payment_type']);
					$value['wl_ss_service_price'] += $servicePrice[$value['pii_payment_type']];
					//计算社保和公积金数据
					$calculateArray = array('amount'=>$value['sid_amount'],'month'=>1);
					//残障金
					$disRuleResult = $templateRule->getTemplateRuleByCondition(array('template_id'=>$value['tr_template_id'],'company_id'=>$value['tr_company_id'],'type'=>3,'state'=>1));
					//dump($value['tr_template_id']);
					//dump($value['tr_company_id']);
					//dump($value['sid_replenish']);
					//dump($templateRule->_sql());
					//dump($disRuleResult);
					if (2 == $value['pii_payment_type']) {
						$paymentInfo = json_decode($value['pii_payment_info'],true);
						$calculateArray['personScale'] = $paymentInfo['personScale'];
						$calculateArray['companyScale'] = $paymentInfo['companyScale'];
						$calculateArray['cardno'] = $paymentInfo['cardno'];
					}
					$value['calculateResult'] = json_decode($calculate->detail($value['tr_rule'],json_encode($calculateArray),$value['pii_payment_type'],$disRuleResult['rule'],$value['sid_replenish']),true);
					$value['calculateResult'] = 0 === $value['calculateResult']['state'] ? $value['calculateResult']['data']:array();
					
					$value['sidTypeValue'] = get_code_value($value['sid_type'],'ServiceInsuranceDetailType');
					$value['sidStateValue'] = get_code_value($value['sid_state'],'ServiceInsuranceDetailState',$value['sid_is_hang_up']);
					$value['pbResidenceTypeValue'] = get_code_value($value['pb_residence_type'],'PersonBaseResidenceType');
					$value['sidCreateTimeValue'] = substr($value['sid_create_time'],0,16);
					
					
					
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