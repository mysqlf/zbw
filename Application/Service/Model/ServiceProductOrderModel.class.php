<?php

namespace Service\Model;
use Think\Model;

/**
 * 服务产品订单模型
 */
class ServiceProductOrderModel extends Model{
	
	/**
	 * getEffectiveServiceProductOrderByProductId function
	 * 根据产品订单获取生效的服务商产品订单
	 * @param int $userId 用户ID
	 * @param int $serviceProductId 产品ID
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getEffectiveServiceProductOrderByProductId($userId = 0,$serviceProductId = 0){
		if ($userId > 0 && $serviceProductId > 0) {
			//确认付款以及服务中
			//$condition = array('spo.user_id'=>$userId,'spo.product_id'=>$serviceProductId,'spo.state'=>1,'spo.service_state'=>2,'spo.overtime'=>array('egt',date('Y-m-d')));
			$condition = array('spo.user_id'=>$userId,'spo.product_id'=>$serviceProductId,'spo.service_state'=>2,'spo.overtime'=>array('egt',date('Y-m-d')));
			$result = $this->alias('spo')->field('spo.*,sp.company_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = spo.product_id')->where($condition)->order('create_time asc')->find();
			
			if ($result) {
				return $result;
			}else if (null === $result) {
				return $result;
			}else if (false === $result) {
				wlog($this->getDbError());
				$this->error = $this->getDbError();
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
	 * getEffectiveServiceProductOrder function
	 * 获取生效的服务商产品订单
	 * @param int $userId 用户ID
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getEffectiveServiceProductOrder($userId = 0, $isSalary = false){
		if ($userId > 0) {
			//确认付款以及服务中
			//$condition = array('spo.user_id'=>$userId,'spo.state'=>1,'spo.service_state'=>2,'spo.overtime'=>array('egt',date('Y-m-d')));
			$condition = array('spo.user_id'=>$userId,'spo.service_state'=>2,'spo.overtime'=>array('egt',date('Y-m-d')));
			if ($isSalary) {
				$condition['spo.is_salary'] = 1;
			}
			//$result = $this->alias('spo')->field('spo.id,spo.order_no,spo.user_id,spo.product_id,spo.template_id,spo.overtime,spo.inc_bill_month_state,spo.inc_create_bill_date,spo.inc_payment_month_state,spo.inc_abort_payment_date,spo.inc_invoice,spo.is_salary,spo.sala_bill_month_state,spo.sala_create_bill_date,spo.sala_payment_month_state,spo.sala_abort_payment_date,spo.sala_invoice,spo.diff_amount,spo.admin_id,sp.name as product_name,sp.company_id as service_company_id,ci.company_name,t.name as template_name,t.soc_deadline as template_soc_deadline,t.soc_payment_type as template_soc_payment_type,t.soc_payment_month as template_soc_payment_month,t.pro_deadline as template_pro_deadline,t.pro_payment_type as template_pro_payment_type,t.pro_payment_month as template_pro_payment_month')->join('left join '.C('DB_PREFIX').'service_product as sp on spo.product_id = sp.id and spo.user_id = '.$userId)->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.id ')->join('left join '.C('DB_PREFIX').'template as t on t.id = spo.template_id ')->where($condition)->select();
			$result = $this->alias('spo')->field('spo.id,spo.user_id,spo.product_id,spo.overtime,spo.is_salary,spo.diff_amount,spo.turn_id,spo.is_turn,sp.name as product_name,sp.company_id as service_company_id,ci.company_name')->join('left join '.C('DB_PREFIX').'service_product as sp on spo.product_id = sp.id and spo.user_id = '.$userId)->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.id ')->where($condition)->select();
			
			if ($result) {
				//过滤未生效的切换订单
				$tempResult = array();
				foreach ($result as $key => $value) {
					$tempResult[$value['id']] = $value;
				}
				foreach ($tempResult as $key => $value) {
					if ($value['turn_id'] && isset($tempResult[$value['turn_id']])) {
						unset($tempResult[$value['turn_id']]);
					}
				}
				$result = $tempResult;
				return $result;
			}else if (null === $result) {
				return $result;
			}else if (false === $result) {
				wlog($this->getDbError());
				$this->error = $this->getDbError();
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
	 * getEffectiveServiceProductOrderUser function
	 * 获取生效的服务商产品订单用户
	 * @param int $companyId 服务商信息id
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getEffectiveServiceProductOrderUser($companyId = 0, $isSalary = false){
		if ($companyId > 0) {
			$serviceProduct = M('ServiceProduct');
			$serviceProductResult = $serviceProduct->field(true)->where(array('company_id'=>$companyId))->select();
			$productIdArray = array();
			if ($serviceProductResult) {
				foreach ($serviceProductResult as $key => $value) {
					$productIdArray[] = $value['id'];
				}
			}
			if ($productIdArray) {
				//确认付款以及服务中
				//$condition = array('spo.product_id'=>array('in',$productIdArray),'spo.state'=>1,'spo.service_state'=>2,'spo.overtime'=>array('egt',date('Y-m-d')));
				$condition = array('spo.product_id'=>array('in',$productIdArray),'spo.service_state'=>2,'spo.overtime'=>array('egt',date('Y-m-d')));
				if ($isSalary) {
					$condition['spo.is_salary'] = 1;
				}
				//$result = $this->alias('spo')->field('spo.id,spo.user_id,spo.product_id,spo.overtime,spo.is_salary,spo.diff_amount,sp.name as product_name,sp.company_id as service_company_id,ci.id as company_id,ci.company_name')->join('left join '.C('DB_PREFIX').'service_product as sp on spo.product_id = sp.id and sp.company_id = '.$companyId)->join('left join '.C('DB_PREFIX').'company_info as ci on spo.user_id = ci.user_id ')->where($condition)->select();
				$result = $this->alias('spo')->field('spo.user_id,ci.id as company_id,ci.company_name')->join('left join '.C('DB_PREFIX').'company_info as ci on spo.user_id = ci.user_id ')->where($condition)->group('user_id')->select();
				
				if ($result) {
					return $result;
				}else if (null === $result) {
					return $result;
				}else if (false === $result) {
					wlog($this->getDbError());
					$this->error = $this->getDbError();
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
	 * getEffectiveServiceProductOrderLocationByProductId function
	 * 根据产品ID获取生效的服务商产品订单
	 * @param int $userId 用户ID
	 * @param int $serviceProductId 服务产品ID
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getEffectiveServiceProductOrderLocationByProductId($userId = 0,$serviceProductId = 0){
		if ($userId > 0 && $serviceProductId > 0) {
			//确认付款以及服务中
			//$condition = array('spo.user_id'=>$userId,'spo.product_id'=>$serviceProductId,'spo.state'=>1,'spo.service_state'=>2,'spo.overtime'=>array('egt',date('Y-m-d')));
			$condition = array('spo.user_id'=>$userId,'spo.product_id'=>$serviceProductId,'spo.service_state'=>2,'spo.overtime'=>array('egt',date('Y-m-d')));
			$result = $this->alias('spo')->field('spo.id')->where($condition)->order('create_time asc')->find();
			
			if ($result) {
				$warrantyLocationResult = $this->table(C('DB_PREFIX').'warranty_location')->field('id,location,soc_service_price,pro_service_price,af_service_price
')->where(array('service_product_order_id'=>$result['id'],'state'=>0))->select();
				if ($warrantyLocationResult) {
					$location = D('Location');
					foreach ($warrantyLocationResult as $key => $value) {
						/*$result['warranty_location'][$value['id']]['location'] = $value['location'];
						$result['warranty_location'][$value['id']]['locationValue'] = showAreaName($value['location']);
						$locationResult = $location->field('id,name,level')->where(array('id'=>array('between',array($value['location']+1,$value['location']+9999)),'state'=>1))->select();
						if ($locationResult) {
							foreach ($locationResult as $kk => $vv) {
								$result['warranty_location'][$value['id']]['locationArray'][$vv['id']] = $result['warranty_location'][$value['id']]['locationValue'].'-'.$vv['name'];
							}
						}*/
						if (0 == $value['location'] % 1000000) {
							$tempLocation = $value['location'];
							$locationResult = $location->alias('l')->field('l.id,l.name,l.level,t.soc_deadline as template_soc_deadline,t.soc_payment_type as template_soc_payment_type,t.soc_payment_month as template_soc_payment_month,t.pro_deadline as template_pro_deadline,t.pro_payment_type as template_pro_payment_type,t.pro_payment_month as template_pro_payment_month')->join('left join '.C('DB_PREFIX').'template as t on t.location=l.id')->where(array('l.id'=>$tempLocation,'l.state'=>1))->select();
						}else {
							$tempLocation = ($value['location']/1000<<0)*1000;
							$locationResult = $location->alias('l')->field('l.id,l.name,l.level,t.soc_deadline as template_soc_deadline,t.soc_payment_type as template_soc_payment_type,t.soc_payment_month as template_soc_payment_month,t.pro_deadline as template_pro_deadline,t.pro_payment_type as template_pro_payment_type,t.pro_payment_month as template_pro_payment_month')->join('left join '.C('DB_PREFIX').'template as t on t.location=l.id')->where(array('l.id'=>array('between',array($tempLocation+1,$tempLocation+9999)),'l.state'=>1))->select();
						}
						//dump($location->_sql());
						if ($locationResult) {
							//$template = D('Template');
							//$templateResult = $template->getTemplateByCondition(array('location'=>$value['location'],'state'=>1));
							/*if ($templateResult) {
								$orderDate = date('Ymd')>=intval(date('Ym').str_pad($templateResult['deadline'],2,'0',STR_PAD_LEFT))?date('Y-m',strtotime('+1 month', strtotime(date('Y-m')))):date('Y-m');
								//$maxPaymentMonth = 1 == $templateResult['payment_type']?$orderDate:date('Y-m',strtotime('+1 month', strtotime($orderDate)));
								//$minPaymentMonth = date('Y-m',strtotime('-'.$templateResult['payment_month'].' month', strtotime($maxPaymentMonth)));
								$minPaymentMonth = date('Y-m',strtotime('-'.$templateResult['payment_month'].' month', strtotime($orderDate)));
							}*/
							//$locationValue = showAreaName($value['location']);
							$locationValue = showAreaName($tempLocation);
							foreach ($locationResult as $kk => $vv) {
								//$orderDate[1] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($vv['template_soc_deadline'],2,'0',STR_PAD_LEFT)))))?date('Y-m',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Y-m',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
								//$orderDate[2] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($vv['template_pro_deadline'],2,'0',STR_PAD_LEFT)))))?date('Y-m',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Y-m',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
								$orderDate[1] = get_handle_month($vv['template_soc_deadline'],'-');
								$orderDate[2] = get_handle_month($vv['template_pro_deadline'],'-');
								
								//$minPaymentMonth[1] = date('Y-m',strtotime('-'.$vv['template_soc_payment_month'].' month', strtotime($orderDate[1])));
								//$minPaymentMonth[2] = date('Y-m',strtotime('-'.$vv['template_pro_payment_month'].' month', strtotime($orderDate[2])));
								$maxPaymentMonth[1] = 1 == $vv['template_soc_payment_type']?$orderDate[1]:date('Y-m',strtotime('+1 month', strtotime($orderDate[1])));
								$maxPaymentMonth[2] = 1 == $vv['template_pro_payment_type']?$orderDate[2]:date('Y-m',strtotime('+1 month', strtotime($orderDate[2])));
								$minPaymentMonth[1] = date('Y-m',strtotime('-'.$vv['template_soc_payment_month'].' month', strtotime($maxPaymentMonth[1])));
								$minPaymentMonth[2] = date('Y-m',strtotime('-'.$vv['template_pro_payment_month'].' month', strtotime($maxPaymentMonth[2])));
								$result['warranty_location'][$vv['id']]['warrantyLocationId'] = $value['id'];
								$result['warranty_location'][$vv['id']]['socServicePrice'] = $value['soc_service_price'];
								$result['warranty_location'][$vv['id']]['proServicePrice'] = $value['pro_service_price'];
								$result['warranty_location'][$vv['id']]['ssServicePrice'] = $value['soc_service_price'] + $value['pro_service_price'];
								$result['warranty_location'][$vv['id']]['afServicePrice'] = $value['af_service_price'];
								$result['warranty_location'][$vv['id']]['location'] = $vv['id'];
								$result['warranty_location'][$vv['id']]['locationValue'] = (0 == $value['location'] % 1000000)?$locationValue:$locationValue.'-'.$vv['name'];
								//$result['warranty_location'][$vv['id']]['deadline'] = $vv['deadline'];
								//$result['warranty_location'][$vv['id']]['paymentMonthNum'] = $vv['payment_month'];
								$result['warranty_location'][$vv['id']]['deadline'] = array(1=>$vv['template_soc_deadline'],2=>$vv['template_pro_deadline']);
								$result['warranty_location'][$vv['id']]['paymentMonthNum'] = array(1=>$vv['template_soc_payment_month'],2=>$vv['template_pro_payment_month']);
								$result['warranty_location'][$vv['id']]['orderDate'] = $orderDate;
								//$result['warranty_location'][$vv['id']]['maxPaymentMonth'] = $orderDate;
								$result['warranty_location'][$vv['id']]['maxPaymentMonth'] = $maxPaymentMonth;
								$result['warranty_location'][$vv['id']]['minPaymentMonth'] = $minPaymentMonth;
							}
						}
					}
				}
				return $result;
			}else if (null === $result) {
				return $result;
			}else if (false === $result) {
				wlog($this->getDbError());
				$this->error = $this->getDbError();
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