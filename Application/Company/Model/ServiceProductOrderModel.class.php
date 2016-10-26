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
 * 产品订单模型
 */
class ServiceProductOrderModel extends RelationModel{
	protected $tablePrefix = 'zbw_';
	//protected $tableName = 'service_product_order';
	//protected $trueTableName = 'zbw_service_product_order';
	
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
		'ServiceProduct'=>array(
			'mapping_type'		=> self::BELONGS_TO,
			'class_name'		=> 'ServiceProduct',
			//'mapping_name'	=> 'ServiceProduct',
			'foreign_key'		=> 'product_id'
		)
	);
	
	/**
	 * getMemberOrderList function
	 * 获取会员订单列表
	 * @param int $userId 用户ID
	 * param int $pageSize 分页大小，默认10
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getMemberOrderList($userId = 0,$pageSize = 10){
		if ($userId > 0) {
			//$condition = array('spo.user_id'=>$userId,'spo.state'=>1,'spo.service_state'=>array('in','2,3'),'spo.overtime'=>array('gt',date('Y-m-d')));
			$condition = array('spo.user_id'=>$userId,'spo.service_state'=>array('in','2,3'),'spo.overtime'=>array('gt',date('Y-m-d')));
			$pageCount = $this->alias('spo')->join('left join '.C('DB_PREFIX').'service_product as sp on spo.product_id = sp.id and spo.user_id = '.$userId)->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.id ')->where($condition)->count('spo.id');
			$page = get_page($pageCount,$pageSize);
			
			$result = $this->alias('spo')->field('spo.*,sp.name as product_name,sp.location,sp.company_id as service_company_id,ci.company_name')->join('left join '.C('DB_PREFIX').'service_product as sp on spo.product_id = sp.id and spo.user_id = '.$userId)->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.id ')->where($condition)->order('spo.create_time desc')->limit($page->firstRow,$page->listRows)->select();
			if ($result) {
				return array('data'=>$result,'page'=>$page->show());
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
	 * getMemberDetail function
	 * 获取会员详情
	 * @param int $userId 用户ID
	 * @param int $serviceProductOrderId 服务产品订单ID
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getMemberDetail($userId = 0, $serviceProductOrderId = 0){
		if ($userId > 0 && $serviceProductOrderId > 0) {
			//$condition = array('spo.id'=>$serviceProductOrderId,'spo.user_id'=>$userId,'spo.state'=>1,'spo.service_state'=>2,'spo.overtime'=>array('gt',date('Y-m-d')));
			$condition = array('spo.id'=>$serviceProductOrderId,'spo.user_id'=>$userId,'spo.service_state'=>2,'spo.overtime'=>array('gt',date('Y-m-d')));
			$result = $this->alias('spo')->field('spo.*,sp.name as product_name,sp.location,sp.company_id as service_company_id,ci.company_name,sa.qq,tsp.name as turn_product_name')->join('left join '.C('DB_PREFIX').'service_product as sp on spo.product_id = sp.id and spo.user_id = '.$userId)->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.id ')->join('left join '.C('DB_PREFIX').'service_admin as sa on spo.admin_id = sa.id')->join('left join '.C('DB_PREFIX').'service_product_order as tspo on spo.turn_id = tspo.id')->join('left join '.C('DB_PREFIX').'service_product as tsp on tspo.product_id = tsp.id and tspo.user_id = '.$userId)->where($condition)->find();
			if ($result) {
				$warrantyLocation = D('warranty_location');
				//$warrantyLocationResult = $warrantyLocation->alias('wl')->field('wl.id,wl.location,wl.soc_service_price,wl.pro_service_price,wl.af_service_price,t.soc_deadline as template_soc_deadline,t.soc_payment_type as template_soc_payment_type,t.soc_payment_month as template_soc_payment_month,t.pro_deadline as template_pro_deadline,t.pro_payment_type as template_pro_payment_type,t.pro_payment_month as template_pro_payment_month')->join('left join '.C('DB_PREFIX').'template as t on wl.location = t.location and t.type = 1')->where(array('wl.service_product_order_id'=>$result['id'],'wl.state'=>0))->select();
				$warrantyLocationResult = $warrantyLocation->alias('wl')->field('wl.id,wl.location,wl.soc_service_price,wl.pro_service_price,wl.af_service_price')->where(array('wl.service_product_order_id'=>$result['id'],'wl.state'=>0))->select();
				foreach ($warrantyLocationResult as $key => $value) {
					//转换城市编号为城市名
					$warrantyLocationResult[$key]['locationValue'] = showAreaName($value['location']);
				}
				$result['warrantyLocation'] = $warrantyLocationResult;
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
	 * getServiceProductOrderList function
	 * 获取服务商产品订单列表
	 * @param int $userId 用户ID
	 * param int $pageSize 分页大小，默认10
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getServiceProductOrderList($userId = 0,$pageSize='10'){
		if ($userId > 0) {
			$condition = array('spo.user_id'=>$userId,'spo.overtime'=>array('neq','0000-00-00'));
			$pageCount = $this->alias('spo')
							->join('left join '.C('DB_PREFIX').'service_product as sp on spo.product_id = sp.id and spo.user_id = '.$userId)
							->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.id ')
							->where($condition)
							->count('spo.id');
			$page = get_page($pageCount,$pageSize);
			
			$result = $this->alias('spo')
							->field('spo.*,sp.name as product_name,sp.location,sp.company_id as service_company_id,ci.company_name,po.order_no,po.id as pid')
							->join('left join '.C('DB_PREFIX').'service_product as sp on spo.product_id = sp.id and spo.user_id = '.$userId)
							->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.id ')
							->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=spo.pay_order_id')
							->where($condition)
							->order('spo.create_time desc')
							->limit($page->firstRow,$page->listRows)
							->select();
			if ($result) {
				return array('data'=>$result,'page'=>$page->show());
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
	 * [filterMyProOrder 根据条件筛选购买的服务]
	 * @param  [array] $where    [条件参数]
	 * @param  string $pageSize [description]
	 * @return [type]           [description]
	 */
	public function filterMyProOrder($where,$pageSize='10'){
		if (count($where)>=2) {
			$where['spo.overtime']=array('neq','0000-00-00');
			$pageCount=$this->alias('spo')
							->join('left join '.C('DB_PREFIX').'service_product as sp on spo.product_id=sp.id')
							->where($where)
							->count('spo.id');
			if ($pageCount>0) {
				$page=get_page($pageCount,$pageSize);
				$result=$this->alias('spo')
								->field('spo.*,sp.name as product_name,sp.location,sp.company_id as service_company_id,ci.company_name,po.order_no')
								->join('left join '.C('DB_PREFIX').'service_product as sp on spo.product_id=sp.id')
								->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id=sp.company_id')
								->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=spo.pay_order_id')
								->where($where)
								->order('spo.create_time desc')
								->limit($page->firstRow,$page->listRows)
								->select();
				return array('data'=>$result,'page'=>$page->show());
			}else{
				$this->error="没有记录";
				return false;
			}
		}else{
			$this->error="参数错误";
			return false;
		}
	}

	/**
	 * getServiceProductOrderDetail function
	 * 获取服务商产品订单详情
	 * @param int $userId 用户ID
	 * @param int $serviceProductOrderId 服务产品订单ID
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getServiceProductOrderDetail($user_id = 0, $serviceProductOrderId = 0){
		if ($user_id > 0 && $serviceProductOrderId > 0) {
			$condition = array('spo.id'=>$serviceProductOrderId,'spo.user_id'=>$user_id);
			$result = $this->alias('spo')
							->field('spo.*,sp.name as product_name,sp.location,sp.company_id as service_company_id,ci.company_name,sp.product_detail,sa.qq,sa.name as saname')
							->join('left join '.C('DB_PREFIX').'service_product as sp on spo.product_id = sp.id and spo.user_id = '.$user_id)
							->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.id ')
							->join('left join '.C('DB_PREFIX').'user_service_provider as usp on usp.company_id=sp.company_id and usp.user_id='.$user_id)
							->join('left join '.C('DB_PREFIX').'service_admin as sa on sa.id=usp.admin_id')
							->where($condition)
							->find();
			
			if ($result) {
				if ($result['turn_id']!='') {
					$result['turn']=$this->alias('spo')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id=spo.product_id')->where(array('spo.id'=>$result['turn_id']))->getField('sp.name');
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
	
	/**
	 * getServiceProductOrderPayInfo function
	 * 获取服务商产品订单支付信息
	 * @param int $userId 用户ID
	 * @param int $serviceProductOrderId 服务产品订单ID
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/

	public function getServiceProductOrderPayInfo($userId = 0, $serviceProductOrderId = 0){
		if ($userId > 0 && $serviceProductOrderId > 0) {
			$condition = array('spo.id'=>$serviceProductOrderId,'spo.user_id'=>$userId);
			$result = $this->alias('spo')->field('spo.id,spo.price,spo.modify_price,sp.name as product_name,sp.location,sp.company_id as service_company_id,ci.company_name,sa.qq,sp.payment_detail,cb.bank,cb.account_name,cb.account,cb.branch')->join('left join '.C('DB_PREFIX').'service_product as sp on spo.product_id = sp.id and spo.user_id = '.$userId)->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.id ')->join('left join '.C('DB_PREFIX').'company_bank as cb on sp.company_id = ci.id ')->join('left join '.C('DB_PREFIX').'service_admin as sa on spo.admin_id = sa.id')->where($condition)->find();
			
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
	 * @param boolean $isSalary 是否代发工资
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
	 * getEffectiveServiceProductOrderLocation function
	 * 获取生效的服务商产品订单
	 * @param int $userId 用户ID
	 * @param int $serviceProductOrderId 服务产品订单ID
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getEffectiveServiceProductOrderLocation($userId = 0,$serviceProductOrderId = 0){
		if ($userId > 0 && $serviceProductOrderId > 0) {
			//确认付款以及服务中
			//$condition = array('spo.id'=>$serviceProductOrderId,'spo.user_id'=>$userId,'spo.state'=>1,'spo.service_state'=>2,'spo.overtime'=>array('egt',date('Y-m-d')));
			$condition = array('spo.id'=>$serviceProductOrderId,'spo.user_id'=>$userId,'spo.service_state'=>2,'spo.overtime'=>array('egt',date('Y-m-d')));
			$result = $this->alias('spo')->field('spo.id')->where($condition)->find();
			
			if ($result) {
				$warrantyLocationResult = $this->table(C('DB_PREFIX').'warranty_location')->field('id,location')->where(array('service_product_order_id'=>$serviceProductOrderId,'state'=>0))->select();
				if ($warrantyLocationResult) {
					foreach ($warrantyLocationResult as $key => $value) {
						$result['warranty_location'][$value['id']]['location'] = $value['location'];
						$result['warranty_location'][$value['id']]['locationValue'] = showAreaName($value['location']);
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
	
	/**
	 * getAllEffectiveServiceProductOrderLocation function
	 * 获取所有有效订单的参保地
	 * @param int $userId 用户ID
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getAllEffectiveServiceProductOrderLocation($userId = 0){
		if ($userId > 0) {
			//确认付款以及(服务中或服务结束)
			//$condition = array('spo.user_id'=>$userId,'spo.state'=>1,'spo.service_state'=>array('in','2,3'));
			$condition = array('spo.user_id'=>$userId,'spo.service_state'=>array('in','2,3'));
			$result = $this->alias('spo')->field('spo.id,spo.turn_id,spo.is_turn')->where($condition)->select();
			
			if ($result) {
				$serviceProductOrderIdArray = array();
				foreach ($result as $key => $value) {
					$serviceProductOrderIdArray[$value['id']] = $value['id'];
				}
				$result = array();
				$warrantyLocationResult = $this->table(C('DB_PREFIX').'warranty_location')->field('id,location')->where(array('service_product_order_id'=>array('in',$serviceProductOrderIdArray),'state'=>0))->select();
				if ($warrantyLocationResult) {
					foreach ($warrantyLocationResult as $key => $value) {
						//$result[$value['location']] = showAreaName($value['location']);
						$value['location'] = ($value['location']/1000<<0)*1000;
						$result[$value['location']] = showAreaName($value['location']);
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
			$result = $this->alias('spo')->field('spo.id,spo.turn_id,spo.is_turn')->where($condition)->order('create_time asc')->find();
			
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
						$isSpecLocation = ($value['location'] == 47000000 || ($value['location'] > 40000000 && $value['location'] < 46000000));
						if ($isSpecLocation) {
							$tempLocation = $value['location'];
							//$locationResult = $location->alias('l')->field('l.id,l.name,l.level,t.soc_deadline as template_soc_deadline,t.soc_payment_type as template_soc_payment_type,t.soc_payment_month as template_soc_payment_month,t.pro_deadline as template_pro_deadline,t.pro_payment_type as template_pro_payment_type,t.pro_payment_month as template_pro_payment_month')->join('left join '.C('DB_PREFIX').'template as t on t.location=l.id')->where(array('l.id'=>$tempLocation,'l.state'=>1))->select();
							$locationResult = $location->alias('l')->field('l.id,l.name,l.level')->where(array('l.id'=>$tempLocation,'l.state'=>1))->select();
						}else {
							$tempLocation = ($value['location']/1000<<0)*1000;
							//$locationResult = $location->alias('l')->field('l.id,l.name,l.level,t.soc_deadline as template_soc_deadline,t.soc_payment_type as template_soc_payment_type,t.soc_payment_month as template_soc_payment_month,t.pro_deadline as template_pro_deadline,t.pro_payment_type as template_pro_payment_type,t.pro_payment_month as template_pro_payment_month')->join('left join '.C('DB_PREFIX').'template as t on t.location=l.id')->where(array('l.id'=>array('between',array($tempLocation+1,$tempLocation+9999)),'l.state'=>1))->select();
							$locationResult = $location->alias('l')->field('l.id,l.name,l.level')->where(array('l.id'=>array('between',array($tempLocation+1,$tempLocation+9999)),'l.state'=>1))->select();
						}
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
								/*$orderDate[1] = get_handle_month($vv['template_soc_deadline'],'-');
								$orderDate[2] = get_handle_month($vv['template_pro_deadline'],'-');
								$maxPaymentMonth[1] = 1 == $vv['template_soc_payment_type']?$orderDate[1]:date('Y-m',strtotime('+1 month', strtotime($orderDate[1])));
								$maxPaymentMonth[2] = 1 == $vv['template_pro_payment_type']?$orderDate[2]:date('Y-m',strtotime('+1 month', strtotime($orderDate[2])));
								$minPaymentMonth[1] = date('Y-m',strtotime('-'.$vv['template_soc_payment_month'].' month', strtotime($maxPaymentMonth[1])));
								$minPaymentMonth[2] = date('Y-m',strtotime('-'.$vv['template_pro_payment_month'].' month', strtotime($maxPaymentMonth[2])));
								$result['warranty_location'][$vv['id']]['deadline'] = array(1=>$vv['template_soc_deadline'],2=>$vv['template_pro_deadline']);
								$result['warranty_location'][$vv['id']]['paymentMonthNum'] = array(1=>$vv['template_soc_payment_month'],2=>$vv['template_pro_payment_month']);
								$result['warranty_location'][$vv['id']]['orderDate'] = $orderDate;
								$result['warranty_location'][$vv['id']]['maxPaymentMonth'] = $maxPaymentMonth;
								$result['warranty_location'][$vv['id']]['minPaymentMonth'] = $minPaymentMonth;*/
								
								$result['warranty_location'][$vv['id']]['warrantyLocationId'] = $value['id'];
								$result['warranty_location'][$vv['id']]['socServicePrice'] = $value['soc_service_price'];
								$result['warranty_location'][$vv['id']]['proServicePrice'] = $value['pro_service_price'];
								$result['warranty_location'][$vv['id']]['ssServicePrice'] = $value['soc_service_price'] + $value['pro_service_price'];
								$result['warranty_location'][$vv['id']]['afServicePrice'] = $value['af_service_price'];
								$result['warranty_location'][$vv['id']]['location'] = $vv['id'];
								$result['warranty_location'][$vv['id']]['locationValue'] = $isSpecLocation?$locationValue:$locationValue.'-'.$vv['name'];
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
	 * getAllEffectiveServiceProductOrder function
	 * 获取所有有效订单
	 * @param int $userId 用户ID
	 * @param boolean $isSalary 是否代发工资
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getAllEffectiveServiceProductOrder($userId = 0,$isSalary = false){
		if ($userId > 0) {
			//确认付款以及(服务中或服务结束)
			//$condition = array('spo.user_id'=>$userId,'spo.state'=>1,'spo.service_state'=>array('in','2,3'));
			$condition = array('spo.user_id'=>$userId,'spo.service_state'=>array('in','2,3'));
			if ($isSalary) {
				$condition['spo.is_salary'] = 1;
			}
			$result = $this->alias('spo')->field('spo.id,spo.product_id,spo.turn_id,spo.is_turn,sp.name as product_name,sp.company_id,ci.company_name')->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id = spo.product_id')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id = sp.company_id')->where($condition)->select();
			
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
	 * [getProOrderByPayOrderid 根据支付id获取对应的服务购买订单]
	 * @param  [type] $orderID [description]
	 * @return [type]          [description]
	 */
	public function getProOrderByPayOrderid($orderID){
        if ($orderID) {
            $result=$this->alias('spo')
		            	 ->field('spo.*,sp.product_detail,sp.name,sp.service_price,po.actual_amount,po.pay_time,po.transaction_no')
		            	 ->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id=spo.product_id')
		            	 ->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=spo.pay_order_id')
		            	 ->where(array('spo.pay_order_id'=>$orderID))
		            	 ->limit(1)
		            	 ->select();
		    return $result;
        }else{
            $this->error='参数错误';
            return false;
        }
    }

    public function getSpoidByProid($proid,$userid){
    	//return $this->field('id')->where(array('product_id'=>$proid,'user_id'=>$userid,'state'=>1,'service_state'=>2))->limit(1)->find();
    	return $this->field('id')->where(array('product_id'=>$proid,'user_id'=>$userid,'service_state'=>2))->limit(1)->find();
    }
	
}
