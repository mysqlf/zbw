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
 * 服务订单模型
 */
class ServiceBillModel extends RelationModel{
	protected $tablePrefix = 'zbw_';
	/**
	 * [getMyBillByCid description]
	 * @param  [type] $where    [description]
	 * @param  string $pageSize [description]
	 * @return [type]           [description]
	 */
	public function getMyBillByWhere($where,$pageSize='10'){
		if ($where) {
			//分页部分
			$pageCount=$this->alias('sb')->where($where)->count('sb.id');
			if ($pageCount>0) {
				$page=get_page($pageCount,$pageSize);
	            $show = $page->show();// 分页显示输出
	            //数据部分
				$result=$this->alias('sb')
						->field('sb.id,sb.bill_no,sb.company_id,sb.bill_date,sb.bill_name,sb.price,sb.invoice_state,sb.invoice_express_company,sb.invoice_express_no,sb.invoice_consignee,sb.invoice_consignee_phone,ci.company_name')
						->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id=sb.company_id')
						->where($where)
						->limit($page->firstRow,$page->listRows)
						->order('sb.create_time desc')
						->select();
				if ($result) {
					return array('data'=>$result,'page'=>$show);
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
					
			}else{
				$this->error="没有记录";
				return false;
			}
		}else{
			$this->error='参数错误';
			return false;
		}
	}
	
	/**
	 * [getBillInfobyId 获取对账单详细信息]
	 * @param  [int] $billid [对账单ID]
	 * @return [void]         
	 */
	public function getBillInfobyId($billid){
		if ($billid) {
			$result=$this->alias('sb')
					 ->field('sb.id,sb.diff_amount,sb.bill_no,sb.company_id,sb.bill_date,sb.bill_name,sb.price,sb.invoice_state,sb.invoice_express_company,sb.invoice_express_no,sb.invoice_consignee,sb.invoice_consignee_phone,ci.company_name')
					 ->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id=sb.company_id')
					 ->where(array('sb.id'=>$billid))
					 ->limit(1)
					 ->select();
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
		}else{
			$this->error='参数错误';
			return false;
		}
	}
	/**旧代码↓**/
	/* 用户模型自动完成 */
	/*protected $_auto = array(
		array('login', 0, self::MODEL_INSERT),
		array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
		array('reg_time', NOW_TIME, self::MODEL_INSERT),
		array('last_login_ip', 0, self::MODEL_INSERT),
		array('last_login_time', 0, self::MODEL_INSERT),
		array('status', 1, self::MODEL_INSERT),
	);*/

	/*protected $_link = array(
		'ServiceProduct'=>array(
			'mapping_type'		=> self::BELONGS_TO,
			'class_name'		=> 'ServiceProduct',
			//'mapping_name'	=> 'ServiceProduct',
			'foreign_key'		=> 'product_id'
		),
		'ServiceBill'=>array(
			'mapping_type'		=> self::HAS_ONE,
			'class_name'		=> 'ServiceBill',
			//'mapping_name'	=> 'ServiceBill',
			'foreign_key'		=> 'order_id'
		)
	);*/
	
	/**
	 * getPrevBill function
	 * 获取上一期账单
	 * @param int $companyId 企业用户ID
	 * @param int $billMonth 账单月份
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getPrevBillOld($companyId = 0, $billMonth = 0){
		if ($billMonth && 6 == strlen($billMonth)) {
			/*$year = substr($billMonth,0,4);
			$month = substr($billMonth,-2,2);
			$prevBillMonth = date('Ym',strtotime('-1 Month',strtotime($year.'-'.$month)));*/
			$prevBillMonth = date('Ym',strtotime('-1 Month',strtotime(substr_replace($billMonth,'-',4,0))));
			$result = $this->field('sb.*')->join('as sb left join '.C('DB_PREFIX').'service_order as so on sb.order_id = so.id ')->join('left join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->where(array('sb.order_date'=>$prevBillMonth,'po.company_id'=>$companyId))->find();
			return $result;
		}else {
			$this->error = '账单月份错误!';
			return false;
		}
	}
	
	/**
	 * getPrevBill function
	 * 获取上一期账单
	 * @param int $productOrderId 企业产品服务订单ID
	 * @param int $billMonth 账单月份
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getPrevBill($productOrderId = 0, $serviceOrderMonth = 0){
		if ($serviceOrderMonth && 6 == strlen($serviceOrderMonth)) {
			/*$year = substr($serviceOrderMonth,0,4);
			$month = substr($serviceOrderMonth,-2,2);
			$prevServiceOrderMonth = date('Ym',strtotime('-1 Month',strtotime($year.'-'.$month)));*/
			$prevServiceOrderMonth = date('Ym',strtotime('-1 Month',strtotime(substr_replace($serviceOrderMonth,'-',4,0))));
			//$result = $this->field('sb.*')->join('as sb left join '.C('DB_PREFIX').'service_order as so on sb.order_id = so.id ')->join('left join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id')->where(array('sb.order_date'=>$prevServiceOrderMonth,'po.company_id'=>$companyId))->find();
			$result = $this->field('sb.*')->join('as sb left join '.C('DB_PREFIX').'service_order as so on sb.order_id = so.id ')->join('left join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id')->where(array('sb.order_date'=>$prevServiceOrderMonth,'so.product_order_id'=>$productOrderId))->find();
			return $result;
		}else {
			$this->error = '账单月份错误!';
			return false;
		}
	}
	
	/**
	 * getBillList function
	 * 获取账单列表
	 * @param int $companyId 企业用户ID
	 * @param int $condition 条件
	 * @param int $size 分页大小(默认10)
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getBillList($companyId = 0, $condition = ' 1=1 ', $size = 10){
		if ($companyId) {
			//分页
			$count  = $this->field('sb.*,so.order_date,po.abort_payment_date,ci.company_name')->join(' as sb LEFT join '.C('DB_PREFIX').'service_order as so on sb.order_id = so.id')->join('LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id ')->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->where($condition)->count();// 查询满足要求的总记录数
			$page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
			//$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
			$page->setConfig('theme','<span class="fr">共%TOTAL_PAGE%页 %HEADER%</span> <div class="page fr">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%</div>');
			$page->lastSuffix=false;
			$page->rollPage=5;
			$page->setConfig('prev','上一页');
			$page->setConfig('next','下一页');
			$page->setConfig('last','末页');
			$page->setConfig('first','首页');
			$show = $page->show();// 分页显示输出
			
			//数据
			$serviceBillResult = $this->field('sb.*,so.order_date,po.abort_payment_date,po.payment_month_state,ci.company_name
	')->join(' as sb LEFT join '.C('DB_PREFIX').'service_order as so on sb.order_id = so.id')->join('LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id ')->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->where($condition)->order('create_time')->limit($page->firstRow.','.$page->listRows)->select();
			foreach ($serviceBillResult as $key => $value) {
				/*$year = substr($value['order_date'],0,4);
				$month = substr($value['order_date'],-2,2);
				$day = $value['abort_payment_date'];
				$serviceBillResult[$key]['abort_payment_date_value'] = date("Y-m-d H:i:s",mktime(23,59,59,$month,$day,$year));*/
				
				//根据付款截止日期计算付款截止时间
				//付款截止月份状态  0当月 1次月
				if ($value['abort_payment_date']) {
					$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$value['abort_payment_date'].' 00:00:00');
					$serviceBillResult[$key]['abortPaymentDateValue'] = (1 == $value['payment_month_state'])?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);
				}
			}
			$result = array('serviceBillResult'=>$serviceBillResult,'show'=>$show);
			return $result;
		}else {
			$this->error = '缺少企业用户ID!';
			return false;
		}
	}
	
	/**
	 * getBillList function
	 * 获取账单详情列表
	 * @param int $companyId 企业用户ID
	 * @param int $billNo 账单号
	 * @param int $size 分页大小(默认10)
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function comBillDetail($companyId = 0, $billNo = '', $size = 10){
		if ($companyId) {
			//分页
			$count  = $this->field('sb.*,so.order_date,po.abort_payment_date,ci.company_name')->join(' as sb LEFT join '.C('DB_PREFIX').'service_order as so on sb.order_id = so.id')->join('LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->where($condition)->count();// 查询满足要求的总记录数
			$page = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数(10)
			$page->setConfig('theme','<span class="fr">共%TOTAL_PAGE%页 %HEADER%</span> <div class="page fr">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%</div>');
			$page->lastSuffix=false;
			$page->rollPage=5;
			$page->setConfig('prev','上一页');
			$page->setConfig('next','下一页');
			$page->setConfig('last','末页');
			$page->setConfig('first','首页');
			$show = $page->show();// 分页显示输出
			
			//数据
			$serviceBillResult = $this->field('sb.*,so.order_date,po.abort_payment_date,ci.company_name
	')->join(' as sb LEFT join '.C('DB_PREFIX').'service_order as so on sb.order_id = so.id')->join('LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on cu.id = ci.company_id')->where($condition)->order('create_time')->limit($page->firstRow.','.$page->listRows)->select();
			
			$result = array('serviceBillResult'=>$serviceBillResult,'show'=>$show);
			return $result;
		}else {
			$this->error = '缺少企业用户ID!';
			return false;
		}
	}
	
	
	
	/**********天云锅**********/
	
	public function comBillDetailDT($id,$bno,$size = 10){
		/*$id = intval(I('get.id' , 0));
		$bno = I('get.bno' , '');
		$page = intval(I('get.p' , 1));*/
		$result = array ();
		$serviceBillDetailCollect = M('service_bill_detail_collect');
		$cnt = $serviceBillDetailCollect->where("bill_id={$id}")->count('id');
		//$pageshow = showpage($cnt,20);
		$page = new \Think\Page($cnt,$size);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$page->setConfig('theme','<span class="fr">共%TOTAL_PAGE%页 %HEADER%</span> <div class="page fr">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%</div>');
		$page->lastSuffix=false;
		$page->rollPage=5;
		$page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		$page->setConfig('last','末页');
		$page->setConfig('first','首页');
		$pageshow = $page->show();// 分页显示输出
		
		$m = M('service_bill');
		$bill = $m->alias('sb')->field('sb.*,sb.id id,so.id oid,so.product_order_id')->join('LEFT JOIN zbw_service_order so ON so.id=sb.order_id')->where("sb.id={$id} AND sb.bill_no='{$bno}'")->find();
		//if (!$bill) return ajaxJson(-9,'参数错误');
		//if (!$bill) die(json_encode(array('status'=>-9,'info'=>'参数错误')));
		if (!$bill){$this->error = '参数错误!';return false;}
		$company_name = $m->query("SELECT company_name FROM zbw_company_info WHERE company_id=(SELECT company_id FROM zbw_service_product_order WHERE id={$bill['product_order_id']})");
		$bill['company_name'] = $company_name[0]['company_name'];
		$yser = substr($bill['order_date'],0,4);
		$month = substr($bill['order_date'],4,2);
		$order_date = $yser.'-'.$month;
		$pdate = date('Ym',strtotime('-1 month' , strtotime($order_date)));
		$balance_total = $m->query("SELECT balance_total FROM zbw_service_bill WHERE order_id=(SELECT id FROM zbw_service_order WHERE product_order_id={$bill['product_order_id']} AND order_date={$pdate})");
		$bill['per_balance_total'] = $balance_total[0]['balance_total'];
		$list = $serviceBillDetailCollect->alias('bdc')->field('bdc.*,pb.user_name,pb.card_num,pb.provident_fund_state,pb.social_insurance_state')->join("LEFT JOIN zbw_person_base pb ON pb.id=bdc.base_id")->where("bdc.bill_id={$bill['id']}")->limit($page->firstRow.','.$page->listRows)->order('bdc.id DESC')->select();
		foreach ($list as $k=>$v)
		{
			$list[$k]['location'] = M('service_order_detail')->where("base_id={$v['base_id']} AND pay_date={$v['pay_date']}")->getField('location');
		}
		return array('show'=>$pageshow,'serviceBillResult'=>$bill , 'serviceBillDetail'=>$list);
	}
	
	public function comBillpayment($billInfo,$accountInfo){
		if(empty($accountInfo['id'])) return ajaxJson(-100001,'非法操作');
		$result = $this->field('id,price,state,bill_no,order_id,pay_time,actual_price,create_time')->where("id = {$billInfo['id']} AND state <> -9 AND bill_no = {$billInfo['bill_no']}")->find();
		if(empty($result)) return ajaxJson(-1,'账单不存在');
		if($result['state'] == 2) return ajaxJson(-1,'账单已确认支付过，请勿重复操作');
		$state = $this->where("id = {$billInfo['id']}")->save(array('state'=>2,'pay_time'=>date('Y-m-d H:i:s',time()),'actual_price'=>$billInfo['actual_price'],'note'=>$billInfo['note']));
		if($state)
		{
			$this->balance($billInfo['id']);
			$this->adminPayLog($accountInfo['id'],'操作：付款，状态：成功，付款金额：'.$billInfo['actual_price'].'，原账单总额：'.$result['price'].'，操作人：'.$accountInfo['name'],$billInfo['id']);
			return ajaxJson(0,'付款操作成功');
		}
		return ajaxJson(-1,'付款操作失败');
	}
	
	public function comBillList($companyId){
		$page = I('get.p',1);
		$count = $this->alias('b')->where("b.state <> -9 AND p.service_com_id = {$companyId}")->join('zbw_service_order s ON s.id = b.order_id')->join('zbw_service_product_order p ON p.id = s.product_order_id')->count();
		$result = $this->alias('b')->field('b.id,b.order_date,b.price,b.state,b.bill_no,b.order_id,b.pay_time,b.actual_price,b.create_time,p.abort_payment_date,p.abort_add_del_date,p.payment_month_state')
			->join('zbw_service_order s ON s.id = b.order_id')->join('zbw_service_product_order p ON p.id = s.product_order_id')
			->order('b.create_time desc')->where("b.state <> -9 AND p.service_com_id = {$companyId}")->page($page,20)->select();
		$pageshow = showpage($count,20);
		return array('page'=>$pageshow,'result'=>$result);
	}
	
	
	public function downData($id,$bill_no){
		$m = M('service_bill');
		/*$bill = $m->alias('sb')->field('sbd.*,so.id as order_id,pb.user_name,pb.card_num')
			->join('zbw_service_order so ON so.id=sb.order_id')
			->join('zbw_service_bill_detail_collect sbd ON sbd.bill_id = sb.id')
			->join("LEFT JOIN zbw_person_base pb ON pb.id=sbd.base_id")
			->where("sb.id in({$id}) AND sb.bill_no in({$bill_no})")->select();*/
		$bill = $m->alias('sb')->field('sbd.*,sb.order_id as order_id,pb.user_name,pb.card_num')
			->join('zbw_service_bill_detail_collect sbd ON sbd.bill_id = sb.id')
			->join("LEFT JOIN zbw_person_base pb ON pb.id=sbd.base_id")
			->where("sb.id in({$id}) AND sb.bill_no in({$bill_no})")->order('sbd.id desc')->select();
		if(empty($bill)) return '';
		$order_id = '';
		$temp_array = array();
		foreach($bill as $k=>$v){
			if(!in_array($v['order_id'],$temp_array))
			{
				$order_id .=','.$v['order_id'];
				$temp_array[] = $v['order_id'];
			}
		}
		$order_id = trim($order_id,',');
		//$order = M('service_order_detail');
		//$result = $order->field('')->where("service_order_id in ({$order_id})")->select();
		$serviceBillDetail = M('ServiceBillDetail');
		$result = $serviceBillDetail->alias('sbd')->field('sbd.rule as sbd_rule,sod.*')->join('left join '.C('DB_PREFIX').'service_order_detail as sod on sbd.order_detail_id = sod.id')->where("sbd.service_bill_id in({$id})")->select();
		foreach($bill as $k=>$v){
			foreach($result as $key=>$val){
				if($v['base_id'] == $val['base_id'] && $v['pay_date'] == $val['pay_date']){
					$orderModel = D('ServiceOrder');
					switch($val['payment_type']){
						case 1:
							$val['scard_number'] =  $val['card_number'];
							$val['samount'] =  $val['amount'];
							$bill[$k]['srule'] = $this->_rule( $val['sbd_rule'],1, $val);
							$bill[$k]['scard_number'] =  $val['card_number'];
							$bill[$k]['samount'] =  $val['amount'];
							break;
						case 2:
							$val['gcard_number'] =  $val['card_number'];
							$val['gamount'] =  $val['amount'];
							$bill[$k]['grule'] = $this->_rule( $val['sbd_rule'],2, $val);
							$bill[$k]['gcard_number'] = $val['card_number'];
							$bill[$k]['gamount'] =  $val['amount'];
							break;
						case 3:
							$bill[$k]['drule'] = $this->_rule( $val['sbd_rule'],3, $val);
							break;
						case 4:
							$bill[$k]['orule'] = $this->_rule( $val['sbd_rule'],4, $val);
							break;
					}
				}
			}
		}
		return $bill;
	}
	
	
	public function _rule($rule,$type,$user_info = ''){
		/*$rule = M('product_template_rule');
		$result = $rule->where("id = {$id}")->find();
		$result['classify_mixed'] = explode("|",$result['classify_mixed']);*/
		$result['rule'] = $rule;
		if($type == 1){
			$json = json_encode(array('amount'=>$user_info['samount'],'month'=>1,'cardno'=>$user_info['scard_number'] ));
		}elseif($type == 2){
			$json = json_encode(array('amount'=>$user_info['gamount'],'month'=>1 , 'personScale'=>$user_info['person_scale'] , 'companyScale'=>$user_info['company_scale'],'cardno'=>$user_info['gcard_number'] ));
		}
		$calcuate = new \Common\Model\Calculate();
		$ruleinfo = $calcuate->detail( $result['rule'],$json,$type);
		$ruleinfo = json_decode($ruleinfo,true);
		if($type == 1){
			if(!empty($ruleinfo['data']))  $result['ruleInfo'] = $ruleinfo['data']['items'];
		}
		if($type == 2){
			if(!empty($ruleinfo['data']))  $result['ruleInfo'] = $ruleinfo['data'];
		}
		$result['rule'] = json_decode($result['rule'],true);
		
		$class = M('product_template_classify');
		$result['template'] = $class->where("template_id = '{$result['template_id']} ' AND type = {$type}  AND state = 1")->select();
		foreach($result['classify_mixed'] as $k=>$v){
			foreach($result['template'] as $key=>$val){
				if($v == $val['id']){
					$result['template'][$key]['selected'] = 1;
					break;
				}
			}
		}

		if($type == 1){
			$result['rule']['total'] = array('name'=>"合计");
			$personscale = '';
			$companyscale = '';
			$personfixedSum = '';
			$companyfixedSum = '';
			foreach($result['rule']['items'] as $k=>$v){
				foreach($result['ruleInfo'] as $key =>$val){
					if($v['name'] == $val['name']){
						$result['rule']['items'][$k]['rules']['amount'] = $val['amount'];
						$result['rule']['items'][$k]['rules']['companySum'] = $val['company']['sum'];
						$result['rule']['items'][$k]['rules']['personSum'] = $val['person']['sum'];
						$result['rule']['items'][$k]['rules']['total'] = $val['total'];
						$personscale += $val['person']['scale'];
						$companyscale += $val['company']['scale'];
						$personfixedSum += $val['person']['fixedSum'];
						$companyfixedSum += $val['company']['fixedSum'];
						break;
					}
				}
			}
			$result['rule']['total']['rules']['company'] = $companyscale.'%+'.$companyfixedSum;
			$result['rule']['total']['rules']['person'] = $personscale.'%+'.$personfixedSum;
			$result['rule']['total']['rules']['companySum'] =  $ruleinfo['data']['company'];
			$result['rule']['total']['rules']['personSum'] =   $ruleinfo['data']['person'];
			$result['rule']['total']['rules']['total'] =  $ruleinfo['data']['company']+  $ruleinfo['data']['person'];
			$result['rule']['pro_costs'] = $result['rule']['pro_cost'];
			unset($result['ruleInfo']);
		}
		if($type == 2){
			$result['rule']['personSum'] = $result['ruleInfo']['person'];
			$result['rule']['companySum'] = $result['ruleInfo']['company'];
			$result['rule']['pro_costs'] = $result['ruleInfo']['pro_cost'];
			unset($result['ruleInfo']);
		}
		return $result;
	}
	
}
