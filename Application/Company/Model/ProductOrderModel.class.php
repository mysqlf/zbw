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
class ProductOrderModel extends RelationModel{
	protected $tablePrefix = 'zbw_';
	protected $tableName = 'service_product_order';
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
	 * [getProductIdByWhere 根据条件获取product_id]
	 * @param  [array] $where [description]
	 * @return [type]       [description]
	 */
	public function getProductIdByWhere($where){
		if($where){
			$result=$this->where($where)->group('product_id')->getField('product_id',true);
			return $this->_sql();
		}else{
			$this->error='参数错误';
			return false;
		}
	}
	/**
	 * [getMyProList 获取我购买的产品列表]
	 * @param  [int] $userId [用户ID]
	 * @return [void]         
	 */
	public function getMyProList($userId){
		if ($userId) {
			$result=$this->alias('spo')
							->field('spo.product_id,sp.name')
							->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id=spo.product_id')
							->where(array('user_id'=>intval($userId)))
							->select();
			return $result;
		}else{
			$this->error='参数错误';
			return false;
		}
	}
	/**
	 * 搜索流水账
	 * @param  [type] $where [description]
	 * @return [type]        [description]
	 */
	public function searchRuningBill($where){
		$result=$this->field('id')->where($where)->select();
        return $this->_sql();
	}
	/**
	 * [getMyCustomer 获取公司所有的客服人员]
	 * @param  [type] $userId [description]
	 * @return [type]         [description]
	 */
	public function getMyCustomer($userId){
		if($userId){
			return $this->field('sa.name,sa.id')->join('as spo left join '.C('DB_PREFIX').'service_admin as sa on spo.admin_id=sa.id')->where('spo.user_id='.$userId)->select();
		}else{
			$this->error='参数错误';
			return false;
		}
	}
	/**
	 * getProductOrder function
	 * 获取服务商产品订单
	 * @param int $companyId 企业用户ID
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getProductOrder($companyId = 0){
		$result = $this->field('po.*,sp.name as product_name,sp.location,sp.company_id as service_user_id,ci.company_name')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id and po.company_id = '.$companyId)->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id ')->where(array('po.state'=>2,'po.service_state'=>2,'po.company_id'=>$companyId,'po.overtime'=>array('gt',date('Y-m-d'))))->select();
		if ($result) {
			foreach ($result as $k => $v) {
				//根据报增报减截止日期计算订单月份
				$tempAddDelDate = strtotime(date('Y-m').'-'.$v['abort_add_del_date'].' 00:00:00');
				$result[$k]['abortAddDelDateValue'] = (time() > $tempAddDelDate) ? date('Ym',strtotime('+1 Month',time())):date('Ym');
				
				//根据账单截止日期计算账单月份
				//账单月份状态  0当月 1次月
				$tempCreateBillDate = strtotime(date('Y-m').'-'.$v['create_bill_date'].' 00:00:00');
				$result[$k]['createBillDateValue'] = (1 == $result['bill_month_state']) ? date('Ym',strtotime('+1 Month',$tempCreateBillDate)):date('Ym',$tempCreateBillDate);
				
				//根据付款截止日期计算付款截止时间
				//付款截止月份状态  0当月 1次月
				/*$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$v['abort_payment_date'].' 00:00:00');
				$result[$k]['abortPaymentDateValue'] = (time() > $tempAbortPaymentDate)?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);*/
				$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$v['abort_payment_date'].' 00:00:00');
				$result[$k]['abortPaymentDateValue'] = (1 == $v['payment_month_state'])?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);
				
				//服务地
				$result[$k]['locationValue'] = showAreaName($v['location']);
			}
			return $result;
		}else {
			$this->error = $this->getDbError;
			return false;
		}
	}
	
	/**
	 * getProductOrderByProductOrderId function
	 * 根据服务产品订单ID获取服务商产品订单
	 * @param int $companyId 企业用户ID
	 * @param int $productOrderId 服务产品订单ID
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getProductOrderByProductOrderId($companyId = 0, $productOrderId){
		$result = $this->field('po.*,sp.name as product_name,sp.location,sp.company_id as service_user_id,ci.company_name,cai.bank,cai.account_name,cai.account,cai.branch')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id and po.company_id = '.$companyId)->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id ')->join('left join '.C('DB_PREFIX').'company_account_info as cai on sp.company_id = cai.company_id ')->where(array('po.id'=>$productOrderId,'po.state'=>2,'po.service_state'=>2,'po.company_id'=>$companyId,'po.overtime'=>array('gt',date('Y-m-d'))))->find();
		if ($result) {
			//根据报增报减截止日期计算订单月份
			$tempAddDelDate = strtotime(date('Y-m').'-'.$result['abort_add_del_date'].' 00:00:00');
			$result['abortAddDelDateValue'] = (time() > $tempAddDelDate) ? date('Ym',strtotime('+1 Month',time())):date('Ym');
			
			//根据账单截止日期计算账单月份
			//账单月份状态  0当月 1次月
			$tempCreateBillDate = strtotime(date('Y-m').'-'.$result['create_bill_date'].' 00:00:00');
			$result['createBillDateValue'] = (1 == $result['bill_month_state']) ? date('Ym',strtotime('+1 Month',$tempCreateBillDate)):date('Ym',$tempCreateBillDate);
			
			//根据付款截止日期计算付款截止时间
			//付款截止月份状态  0当月 1次月
			$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$result['abort_payment_date'].' 00:00:00');
			$result['abortPaymentDateValue'] = (1 == $result['payment_month_state'])?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);
			
			//服务地
			$result['locationValue'] = showAreaName($result['location']);
			return $result;
		}else {
			$this->error = $this->getDbError;
			return false;
		}
	}
	
	
	/**
	 * getProductOrder function
	 * 获取服务商产品订单以及对应的参保地
	 * @param int $companyId 企业用户ID
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getProductOrderLocation($companyId = 0){
		//$result = $this->field('po.*,sp.name as product_name,sp.location,sp.company_id as service_user_id,ci.company_name,wl.location as warranty_location,wl.soc_service_price,wl.pro_service_price,wl.af_service_price,wl.dg_service_price')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id and po.company_id = '.$companyId)->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id ')->join('left join '.C('DB_PREFIX').'warranty_location as wl on po.id = wl.service_order_id and wl.state = 0 ')->where(array('po.state'=>2,'po.service_state'=>2,'po.company_id'=>$companyId))->select();
		
		//产品订单信息
		$result = $this->field('po.*,sp.name as product_name,sp.location,sp.company_id as service_user_id,ci.company_name')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id and po.company_id = '.$companyId)->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id ')->where(array('po.state'=>2,'po.service_state'=>2,'po.company_id'=>$companyId,'po.overtime'=>array('gt',date('Y-m-d'))))->select();
		
		//产品订单参保地信息
		$warrantyLocationResult = $this->field('wl.service_order_id as product_order_id,wl.id as warranty_id,wl.location as warranty_location,wl.soc_service_price,wl.pro_service_price,wl.af_service_price,wl.dg_service_price')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id and po.company_id = '.$companyId)->join('left join '.C('DB_PREFIX').'warranty_location as wl on po.id = wl.service_order_id and wl.state = 0')->where(array('po.state'=>2,'po.service_state'=>2,'po.company_id'=>$companyId))->select();
		
		if ($result) {
			foreach ($result as $k => $v) {
				//根据报增报减截止日期计算订单月份
				$tempAddDelDate = strtotime(date('Y-m').'-'.$v['abort_add_del_date'].' 00:00:00');
				$result[$k]['abortAddDelDateValue'] = (time() > $tempAddDelDate) ? date('Ym',strtotime('+1 Month',time())):date('Ym');
				
				//根据账单截止日期计算账单月份
				//账单月份状态  0当月 1次月
				$tempCreateBillDate = strtotime(date('Y-m').'-'.$v['create_bill_date'].' 00:00:00');
				$result[$k]['createBillDateValue'] = (1 == $result['bill_month_state']) ? date('Ym',strtotime('+1 Month',$tempCreateBillDate)):date('Ym',$tempCreateBillDate);
				
				//根据付款截止日期计算付款截止时间
				//付款截止月份状态  0当月 1次月
				/*$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$v['abort_payment_date'].' 00:00:00');
				$result[$k]['abortPaymentDateValue'] = (time() > $tempAbortPaymentDate)?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);*/
				$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$v['abort_payment_date'].' 00:00:00');
				$result[$k]['abortPaymentDateValue'] = (1 == $v['payment_month_state'])?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);
				
				//服务地
				$result[$k]['locationValue'] = showAreaName($v['location']);
				
				//参保地
				if ($warrantyLocationResult) {
					foreach ($warrantyLocationResult as $kk => $vv) {
						if ($v['id'] == $vv['product_order_id']) {
							$warrantyLocationResult[$kk]['locationValue'] = showAreaName($vv['warranty_location']);
							$result[$k]['warrantyLocationList'][] = $warrantyLocationResult[$kk];
							unset($warrantyLocationResult[$kk]);
						}
					}
				}
			}
			return $result;
		}else {
			$this->error = $this->getDbError();
			return false;
		}
	}
	
	/**
	 * getProductOrderLocationList function
	 * 根据条件获取服务商产品订单以及对应的参保地列表
	 * @param int $companyId 企业用户ID
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getProductOrderLocationList($condition = '1=1',$size = 10){
		$count  = $this->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id ')->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id ')->where($condition)->count('po.id');// 查询满足要求的总记录数
		$page = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$page->setConfig('theme','<span class="fr">共%TOTAL_PAGE%页 %HEADER%</span> <div class="page fr">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%</div>');
		$page->lastSuffix=false;
		$page->rollPage=5;
		$page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		$page->setConfig('last','末页');
		$page->setConfig('first','首页');
		$show = $page->show();// 分页显示输出
		
		//产品订单信息
		$result = $this->field('po.*,sp.name as product_name,sp.location,sp.company_id as service_user_id,sp.product_detail,ci.company_name')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id ')->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id ')->where($condition)->select();
		
		//产品订单参保地信息
		$warrantyLocationResult = $this->field('wl.service_order_id as product_order_id,wl.id as warranty_id,wl.location as warranty_location,wl.soc_service_price,wl.pro_service_price,wl.af_service_price,wl.dg_service_price')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id ')->join('left join '.C('DB_PREFIX').'warranty_location as wl on po.id = wl.service_order_id and wl.state = 0')->where($condition)->select();
		
		if ($result) {
			foreach ($result as $k => $v) {
				//根据报增报减截止日期计算订单月份
				$tempAddDelDate = strtotime(date('Y-m').'-'.$v['abort_add_del_date'].' 00:00:00');
				$result[$k]['abortAddDelDateValue'] = (time() > $tempAddDelDate) ? date('Ym',strtotime('+1 Month',time())):date('Ym');
				
				//根据账单截止日期计算账单月份
				//账单月份状态  0当月 1次月
				$tempCreateBillDate = strtotime(date('Y-m').'-'.$v['create_bill_date'].' 00:00:00');
				$result[$k]['createBillDateValue'] = (1 == $v['bill_month_state']) ? date('Ym',strtotime('+1 Month',$tempCreateBillDate)):date('Ym',$tempCreateBillDate);
				
				//根据付款截止日期计算付款截止时间
				//付款截止月份状态  0当月 1次月
				/*$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$v['abort_payment_date'].' 00:00:00');
				$result[$k]['abortPaymentDateValue'] = (time() > $tempAbortPaymentDate)?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);*/
				$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$v['abort_payment_date'].' 00:00:00');
				$result[$k]['abortPaymentDateValue'] = (1 == $v['payment_month_state'])?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);
				
				//服务地
				$result[$k]['locationValue'] = showAreaName($v['location']);
				
				//参保地
				if ($warrantyLocationResult) {
					foreach ($warrantyLocationResult as $kk => $vv) {
						if ($v['id'] == $vv['product_order_id']) {
							$warrantyLocationResult[$kk]['locationValue'] = showAreaName($vv['warranty_location']);
							$result[$k]['warrantyLocationList'][] = $warrantyLocationResult[$kk];
							unset($warrantyLocationResult[$kk]);
						}
					}
				}
			}
			return array('productOrderResult'=>$result,'page'=>$show);
		}else {
			$this->error = $this->getDbError();
		}
	}
	
	/**
	 * getProductOrderLocationByCondition function
	 * 根据条件获取服务商产品订单以及对应的参保地
	 * @param int $companyId 企业用户ID
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getProductOrderLocationByCondition($condition = '1=1'){
		//产品订单信息
		$result = $this->field('po.*,sp.product_detail,sp.name as product_name,sp.location,sp.company_id as service_user_id,sp.product_detail,ci.company_name')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id ')->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id ')->where($condition)->select();
		
		//产品订单参保地信息
		$warrantyLocationResult = $this->field('wl.service_order_id as product_order_id,wl.id as warranty_id,wl.location as warranty_location,wl.soc_service_price,wl.pro_service_price,wl.af_service_price,wl.dg_service_price')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id ')->join('left join '.C('DB_PREFIX').'warranty_location as wl on po.id = wl.service_order_id and wl.state = 0')->where($condition)->select();
		
		if ($result) {
			foreach ($result as $k => $v) {
				//根据报增报减截止日期计算订单月份
				$tempAddDelDate = strtotime(date('Y-m').'-'.$v['abort_add_del_date'].' 00:00:00');
				$result[$k]['abortAddDelDateValue'] = (time() > $tempAddDelDate) ? date('Ym',strtotime('+1 Month',time())):date('Ym');
				
				//根据账单截止日期计算账单月份
				//账单月份状态  0当月 1次月
				$tempCreateBillDate = strtotime(date('Y-m').'-'.$v['create_bill_date'].' 00:00:00');
				$result[$k]['createBillDateValue'] = (1 == $v['bill_month_state']) ? date('Ym',strtotime('+1 Month',$tempCreateBillDate)):date('Ym',$tempCreateBillDate);
				
				//根据付款截止日期计算付款截止时间
				//付款截止月份状态  0当月 1次月
				/*$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$v['abort_payment_date'].' 00:00:00');
				$result[$k]['abortPaymentDateValue'] = (time() > $tempAbortPaymentDate)?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);*/
				$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$v['abort_payment_date'].' 00:00:00');
				$result[$k]['abortPaymentDateValue'] = (1 == $v['payment_month_state'])?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);
				
				//服务地
				$result[$k]['locationValue'] = showAreaName($v['location']);
				
				//参保地
				if ($warrantyLocationResult) {
					foreach ($warrantyLocationResult as $kk => $vv) {
						if ($v['id'] == $vv['product_order_id']) {
							$warrantyLocationResult[$kk]['locationValue'] = showAreaName($vv['warranty_location']);
							$result[$k]['warrantyLocationList'][] = $warrantyLocationResult[$kk];
							unset($warrantyLocationResult[$kk]);
						}
					}
				}
			}
			return $result;
		}else {
			$this->error = $this->getDbError();
		}
	}
	
	/**
	 * getProductOrderByCondition function
	 * 根据条件获取服务商产品订单
	 * @param int $companyId 企业用户ID
	 * @param int $productOrderId 服务产品订单ID
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getProductOrderByCondition($condition = '1=1'){
		$result = $this->field('po.*,sp.name as product_name,sp.location,sp.company_id as service_user_id,sp.payment_detail ,ci.company_name,cai.bank,cai.account_name,cai.account,cai.branch')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id ')->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id ')->join('left join '.C('DB_PREFIX').'company_account_info as cai on sp.company_id = cai.company_id ')->where($condition)->find();
		if ($result) {
			//根据报增报减截止日期计算订单月份
			$tempAddDelDate = strtotime(date('Y-m').'-'.$result['abort_add_del_date'].' 00:00:00');
			$result['abortAddDelDateValue'] = (time() > $tempAddDelDate) ? date('Ym',strtotime('+1 Month',time())):date('Ym');
			
			//根据账单截止日期计算账单月份
			//账单月份状态  0当月 1次月
			$tempCreateBillDate = strtotime(date('Y-m').'-'.$result['create_bill_date'].' 00:00:00');
			$result['createBillDateValue'] = (1 == $result['bill_month_state']) ? date('Ym',strtotime('+1 Month',$tempCreateBillDate)):date('Ym',$tempCreateBillDate);
			
			//根据付款截止日期计算付款截止时间
			//付款截止月份状态  0当月 1次月
			$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$result['abort_payment_date'].' 00:00:00');
			$result['abortPaymentDateValue'] = (1 == $result['payment_month_state'])?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);
			
			//服务地
			$result['locationValue'] = showAreaName($result['location']);
			return $result;
		}else {
			$this->error = $this->getDbError();
			return false;
		}
	}
	
	/**
	 * getProductOrderLocationByProductOrderId function
	 * 根据服务产品订单ID获取服务商产品订单以及对应的参保地
	 * @param int $companyId 企业用户ID
	 * @param int $productOrderId 服务产品订单ID
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getProductOrderLocationByProductOrderId($companyId = 0, $productOrderId){
		//$result = $this->field('po.*,sp.name as product_name,sp.location,sp.company_id as service_user_id,ci.company_name,cai.bank,cai.account_name,cai.account,cai.branch,wl.location as warranty_location,wl.soc_service_price,wl.pro_service_price,wl.af_service_price,wl.dg_service_price')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id and po.company_id = '.$companyId)->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id ')->join('left join '.C('DB_PREFIX').'company_account_info as cai on sp.company_id = cai.company_id ')->join('left join '.C('DB_PREFIX').'warranty_location as wl on po.id = wl.service_order_id and wl.state = 0 ')->where(array('po.id'=>$productOrderId,'po.state'=>2,'po.service_state'=>2,'po.company_id'=>$companyId))->find();
		
		
		//产品订单信息
		$result = $this->field('po.*,sp.name as product_name,sp.location,sp.company_id as service_user_id,ci.company_name')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id and po.company_id = '.$companyId)->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id ')->where(array('po.id'=>$productOrderId,'po.state'=>2,'po.service_state'=>2,'po.company_id'=>$companyId))->find();
		
		//产品订单参保地信息
		$warrantyLocationResult = $this->field('wl.service_order_id as product_order_id,wl.id as warranty_id,wl.location as warranty_location,wl.soc_service_price,wl.pro_service_price,wl.af_service_price,wl.dg_service_price')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id and po.company_id = '.$companyId)->join('left join '.C('DB_PREFIX').'warranty_location as wl on po.id = wl.service_order_id and wl.state = 0 ')->where(array('po.id'=>$productOrderId,'po.state'=>2,'po.service_state'=>2,'po.company_id'=>$companyId,'po.overtime'=>array('gt',date('Y-m-d'))))->select();
		
		if ($result) {
			//根据报增报减截止日期计算订单月份
			$tempAddDelDate = strtotime(date('Y-m').'-'.$result['abort_add_del_date'].' 00:00:00');
			$result['abortAddDelDateValue'] = (time() > $tempAddDelDate) ? date('Ym',strtotime('+1 Month',time())):date('Ym');
			
			//根据账单截止日期计算账单月份
			//账单月份状态  0当月 1次月
			$tempCreateBillDate = strtotime(date('Y-m').'-'.$result['create_bill_date'].' 00:00:00');
			$result['createBillDateValue'] = (1 == $result['bill_month_state']) ? date('Ym',strtotime('+1 Month',$tempCreateBillDate)):date('Ym',$tempCreateBillDate);
			
			//根据付款截止日期计算付款截止时间
			//付款截止月份状态  0当月 1次月
			$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$result['abort_payment_date'].' 00:00:00');
			$result['abortPaymentDateValue'] = (1 == $result['payment_month_state'])?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);
			
			//服务地
			$result['locationValue'] = showAreaName($result['location']);
			
			//参保地
			if ($warrantyLocationResult) {
				foreach ($warrantyLocationResult as $kk => $vv) {
					if ($result['id'] == $vv['product_order_id']) {
						$warrantyLocationResult[$kk]['locationValue'] = showAreaName($vv['warranty_location']);
						$result['warrantyLocationList'][] = $warrantyLocationResult[$kk];
						unset($warrantyLocationResult[$kk]);
					}
				}
			}
			return $result;
		}else {
			$this->error = $this->getDbError();
		}
	}
	
	/**
	 * getWarrantyLocation function
	 * 获取服务商产品订单对应的参保地
	 * @param int $companyId 企业用户ID
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getWarrantyLocation($companyId = 0){
		//产品订单参保地信息
		$warrantyLocationResult = $this->field('wl.service_order_id as product_order_id,wl.id as warranty_id,wl.location as warranty_location,wl.soc_service_price,wl.pro_service_price,wl.af_service_price,wl.dg_service_price')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id and po.company_id = '.$companyId)->join('left join '.C('DB_PREFIX').'warranty_location as wl on po.id = wl.service_order_id ')->where(array('po.state'=>2,'po.company_id'=>$companyId,'wl.location'=>array('exp',' is not null ')))->select();
		$locationArray = array();
		foreach ($warrantyLocationResult as $key => $value) {
			if (in_array( $value['warranty_location'],$locationArray)) {
				unset($warrantyLocationResult[$key]);
			}else {
				$warrantyLocationResult[$key]['warrantyLocationValue'] = showAreaName($value['warranty_location']);
				$locationArray[] = $value['warranty_location'];
			}
		}
		return $warrantyLocationResult;
	}
	
	
	/**
	 * getSalaryProductOrder function
	 * 获取服务商产品代发工资订单
	 * @param int $companyId 企业用户ID
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getSalaryProductOrder($companyId = 0){
		$result = $this->field('po.*,sp.name as product_name,sp.location,sp.company_id as service_user_id,ci.company_name')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id and po.company_id = '.$companyId)->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id ')->where(array('po.state'=>2,'po.service_state'=>2,'po.company_id'=>$companyId,'po.is_salary'=>1,'po.overtime'=>array('gt',date('Y-m-d'))))->select();
		if ($result) {
			foreach ($result as $k => $v) {
				//根据报增报减截止日期计算订单月份
				$tempAddDelDate = strtotime(date('Y-m').'-'.$v['abort_add_del_date'].' 00:00:00');
				$result[$k]['abortAddDelDateValue'] = (time() > $tempAddDelDate) ? date('Ym',strtotime('+1 Month',time())):date('Ym');
				
				//根据账单截止日期计算账单月份
				//账单月份状态  0当月 1次月
				$tempCreateBillDate = strtotime(date('Y-m').'-'.$v['create_bill_date'].' 00:00:00');
				$result[$k]['createBillDateValue'] = (1 == $result['bill_month_state']) ? date('Ym',strtotime('+1 Month',$tempCreateBillDate)):date('Ym',$tempCreateBillDate);
				
				//根据付款截止日期计算付款截止时间
				//付款截止月份状态  0当月 1次月
				/*$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$v['abort_payment_date'].' 00:00:00');
				$result[$k]['abortPaymentDateValue'] = (time() > $tempAbortPaymentDate)?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);*/
				$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$v['abort_payment_date'].' 00:00:00');
				$result[$k]['abortPaymentDateValue'] = (1 == $v['payment_month_state'])?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);
				
				//服务地
				$result[$k]['locationValue'] = showAreaName($v['location']);
			}
			return $result;
		}else {
			$this->error = $this->getDbError;
			return false;
		}
	}
	
	/**
	 * getServiceOrder function
	 * 获取服务商产品订单相关的服务订单
	 * @param int $companyId 企业用户ID
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getServiceOrder($companyId = 0){
		$result = $this->field('po.*,sp.name as product_name,sp.location,sp.company_id as service_user_id,ci.company_name,so.id as so_id')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id and po.company_id = '.$companyId)->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id ')->join('left join '.C('DB_PREFIX').'service_order as so on po.id = so.product_order_id ')->where(array('po.state'=>2,'po.company_id'=>$companyId))->order('order_date desc')->select();
		if ($result) {
			foreach ($result as $k => $v) {
				//根据报增报减截止日期计算订单月份
				$tempAddDelDate = strtotime(date('Y-m').'-'.$v['abort_add_del_date'].' 00:00:00');
				$result[$k]['abortAddDelDateValue'] = (time() > $tempAddDelDate) ? date('Ym',strtotime('+1 Month',time())):date('Ym');
				
				//根据账单截止日期计算账单月份
				//账单月份状态  0当月 1次月
				$tempCreateBillDate = strtotime(date('Y-m').'-'.$v['create_bill_date'].' 00:00:00');
				$result[$k]['createBillDateValue'] = (1 == $result['bill_month_state']) ? date('Ym',strtotime('+1 Month',$tempCreateBillDate)):date('Ym',$tempCreateBillDate);
				
				//根据付款截止日期计算付款截止时间
				//付款截止月份状态  0当月 1次月
				/*$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$v['abort_payment_date'].' 00:00:00');
				$result[$k]['abortPaymentDateValue'] = (time() > $tempAbortPaymentDate)?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);*/
				$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$v['abort_payment_date'].' 00:00:00');
				$result[$k]['abortPaymentDateValue'] = (1 == $v['payment_month_state'])?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);
				
				//服务地
				$result[$k]['locationValue'] = showAreaName($v['location']);
			}
			return $result;
		}else {
			$this->error = $this->getDbError;
			return false;
		}
	}
	
	/**
	 * getLastServiceOrder function
	 * 获取服务商产品订单相关的最新服务订单
	 * @param int $companyId 企业用户ID
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getLastServiceOrder($companyId = 0){
		$result = $this->field('po.*,sp.name as product_name,sp.location,sp.company_id as service_user_id,ci.company_name,so.id as so_id')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id and po.company_id = '.$companyId)->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id ')->join('left join '.C('DB_PREFIX').'service_order as so on po.id = so.product_order_id ')->where(array('po.state'=>2,'po.company_id'=>$companyId))->order('order_date desc')->find();
		if ($result) {
			//根据报增报减截止日期计算订单月份
			$tempAddDelDate = strtotime(date('Y-m').'-'.$result['abort_add_del_date'].' 00:00:00');
			$result['abortAddDelDateValue'] = (time() > $tempAddDelDate) ? date('Ym',strtotime('+1 Month',time())):date('Ym');
			
			//根据账单截止日期计算账单月份
			//账单月份状态  0当月 1次月
			$tempCreateBillDate = strtotime(date('Y-m').'-'.$result['create_bill_date'].' 00:00:00');
			$result['createBillDateValue'] = (1 == $result['bill_month_state']) ? date('Ym',strtotime('+1 Month',$tempCreateBillDate)):date('Ym',$tempCreateBillDate);
			
			//根据付款截止日期计算付款截止时间
			//付款截止月份状态  0当月 1次月
			/*$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$result['abort_payment_date'].' 00:00:00');
			$result['abortPaymentDateValue'] = (time() > $tempAbortPaymentDate)?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);*/
			$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$result['abort_payment_date'].' 00:00:00');
			$result['abortPaymentDateValue'] = (1 == $result['payment_month_state'])?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);
			
			//服务地
			$result['locationValue'] = showAreaName($result['location']);
			
			return $result;
		}else {
			$this->error = $this->getDbError;
			return false;
		}
	}
	
	/**
	 * getLastServiceOrderByCondition function
	 * 根据条件获取服务商产品订单相关的最新服务订单
	 * @param array $condition 查询条件
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getLastServiceOrderByCondition($condition = ' 1 = 1 '){
		$result = $this->field('po.*,sp.name as product_name,sp.location,sp.company_id as service_user_id,ci.company_name,so.id as so_id,so.order_date,sod.location as sod_location,sod.rule_id as sod_rule_id,sod.amount as sod_amount,sod.card_number as sod_card_number,sod.person_scale as sod_person_scale,sod.company_scale as sod_company_scale')->join('as po left join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id ')->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id ')->join('left join '.C('DB_PREFIX').'service_order as so on po.id = so.product_order_id ')->join('left join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id ')->where($condition)->order('so.order_date desc')->find();
		if ($result) {
			//根据报增报减截止日期计算订单月份
			$tempAddDelDate = strtotime(date('Y-m').'-'.$result['abort_add_del_date'].' 00:00:00');
			$result['abortAddDelDateValue'] = (time() > $tempAddDelDate) ? date('Ym',strtotime('+1 Month',time())):date('Ym');
			
			//根据账单截止日期计算账单月份
			//账单月份状态  0当月 1次月
			$tempCreateBillDate = strtotime(date('Y-m').'-'.$result['create_bill_date'].' 00:00:00');
			$result['createBillDateValue'] = (1 == $result['bill_month_state']) ? date('Ym',strtotime('+1 Month',$tempCreateBillDate)):date('Ym',$tempCreateBillDate);
			
			//根据付款截止日期计算付款截止时间
			//付款截止月份状态  0当月 1次月
			/*$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$result['abort_payment_date'].' 00:00:00');
			$result['abortPaymentDateValue'] = (time() > $tempAbortPaymentDate)?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);*/
			$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$result['abort_payment_date'].' 00:00:00');
			$result['abortPaymentDateValue'] = (1 == $result['payment_month_state'])?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);
			
			//服务地
			$result['locationValue'] = showAreaName($result['location']);
			
			return $result;
		}else {
			$this->error = $this->getDbError;
			return false;
		}
	}
	
}
