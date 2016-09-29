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
class ServiceOrderModel extends RelationModel{
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
		/*'ServiceProduct'=>array(
			'mapping_type'		=> self::BELONGS_TO,
			'class_name'		=> 'ServiceProduct',
			//'mapping_name'	=> 'ServiceProduct',
			'foreign_key'		=> 'product_order_id'
		),*/
		'ServiceBill'=>array(
			'mapping_type'		=> self::HAS_ONE,
			'class_name'		=> 'ServiceBill',
			//'mapping_name'	=> 'ServiceBill',
			'foreign_key'		=> 'order_id'
		)
	);
	
	/**
	 * getServiceOrderByConditon function
	 * 获取报增列表
	 * @param int $companyId 企业用户ID
	 * @param string $condition 查询条件
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getServiceOrderByConditon($condition){
		if ($condition && is_array($condition)) {
			$result = $this->field(true)->where($condition)->find();
			
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
	 * getLastServiceOrderByproductId function
	 * 获取报增列表
	 * @param int $userId 用户ID
	 * @param int $productId 产品ID
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getLastServiceOrderByProductId($userId,$productId){
		if ($userId && $productId) {
			$result = $this->field(true)->where(array('user'=>$userId,'product_id'=>$productId,'payment_type'=>1))->order('order_date desc')->find();
			
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
	 * getIncreaseList function
	 * 获取报增列表
	 * @param int $companyId 企业用户ID
	 * @param string $condition 查询条件
	 * @param int $size 分页大小 默认10
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getIncreaseList($companyId = 0, $condition = ' 1 = 1 ',$size = 10){
		if ($companyId) {
			$sodCount = $this->field('count(DISTINCT pii.base_id) as count')->join('as so LEFT join '.C('DB_PREFIX').'service_insurance_detail as  sid on so.id=sid.service_order_id')->join('left join '.C('DB_PREFIX').'person_insurance_info as pii on sid.insurance_id=pii.id')->where(array('so.company_id'=>$companyId,'pii.state'=>1))->select();
			/*$sodCount  = $this->field('count(DISTINCT sod.service_order_id,sod.base_id) as count')->join('as so LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->join('RIGHT join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id')->join('LEFT join '.C('DB_PREFIX').'person_base as pb on sod.base_id = pb.id')->where($condition)->find();// 查询满足要求的总记录数*/
			$count = $sodCount['count'];
			$page = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数(10)
			//$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
			$page->setConfig('theme','<span class="fr">共%TOTAL_PAGE%页 %HEADER%</span> <div class="page fr">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%</div>');
			$page->lastSuffix=false;
			$page->rollPage=5;
			$page->setConfig('prev','上一页');
			$page->setConfig('next','下一页');
			$page->setConfig('last','末页');
			$page->setConfig('first','首页');
			$result['show'] = $page->show();// 分页显示输出
			
			//数据
			/*$serviceOrderResult  = $this->field('DISTINCT sod.service_order_id,sod.base_id,pb.user_name,pb.card_num ')->join('as so LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->join('RIGHT join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id')->join('LEFT join '.C('DB_PREFIX').'person_base as pb on sod.base_id = pb.id')->limit($page->firstRow.','.$page->listRows)->where($condition)->select();// 查询满足要求的总记录数*/

			//修改后的
			$serviceOrderResult = $this->field('pb.person_name,pb.card_num,pb.id')->join('as so LEFT join '.C('DB_PREFIX').'service_insurance_detail as  sid on so.id=sid.service_order_id')->join('left join '.C('DB_PREFIX').'person_insurance_info as pii on sid.insurance_id=pii.id')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=pii.base_id')->limit($page->firstRow.','.$page->listRows)->where(array('so.company_id'=>$companyId,'pii.state'=>1))->select();

			//$sodList = $this->field('so.*,sod.service_order_id,sod.base_id,sp.name as product_name,ci.company_name, pb.user_name, pb.card_num,sod.id as sod_id,sod.type as sod_type,sod.payment_type as sod_payment_type,sod.state as sod_state,sod.location as sod_location,sod.pay_date as sod_pay_date,sod.note as sod_note')->join('as so LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->join('LEFT join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id')->join('LEFT join '.C('DB_PREFIX').'person_base as pb on sod.base_id = pb.id')->where($condition)->order('sod.create_time asc')->limit($page->firstRow.','.$page->listRows)->select();
			//so.id pb.id 
			$sodList = $this->field('so.*,sod.service_order_id,sod.base_id,sp.name as product_name,ci.company_name, pb.user_name, pb.card_num,sod.id as sod_id,sod.type as sod_type,sod.payment_type as sod_payment_type,sod.state as sod_state,sod.location as sod_location,sod.pay_date as sod_pay_date,sod.amount as sod_amount,sod.detail_state as sod_detail_state,sod.note as sod_note,po.abort_add_del_date')->join('as so LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->join('LEFT join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id')->join('LEFT join '.C('DB_PREFIX').'person_base as pb on sod.base_id = pb.id')->where($condition)->order(' sod.payment_type asc, sod.pay_date asc')->select();
			
			foreach ($sodList as $k => $v) {
				$sodList[$k]['orderDateValue'] = $v['order_date']?substr_replace($v['order_date'],'/',4,0):'';
				$sodList[$k]['sodPayDateValue'] = $v['sod_pay_date']?substr_replace($v['sod_pay_date'],'/',4,0):'';
				
				//报增内容
				$sodList[$k]['sodPaymentTypeValue'] =get_status_value($sodList[$k]['sod_payment_type'],'ServiceOrderDetailPaymentType');
				
				//状态
				$sodList[$k]['sodStateValue'] =get_status_value($sodList[$k]['sod_state'],'ServiceOrderDetailState');
				
				//报增报减截止日期
				$sodList[$k]['abortAddDelDateValue'] = substr_replace($v['order_date'],'-',4,0).'-'.$sodList[$k]['abort_add_del_date'];
				
				//是否在报增报减截止日期之前
				$sodList[$k]['isBeforeAbortAddDelDate'] = strtotime($sodList[$k]['abortAddDelDateValue']) > time()?true:false;
				
				foreach ($serviceOrderResult as $kk => $vv) {
					if ($v['service_order_id'] == $vv['service_order_id'] && $v['base_id'] == $vv['base_id']) {
						$serviceOrderResult[$kk]['product_name'] = $v['product_name'];
						$serviceOrderResult[$kk]['company_name'] = $v['company_name'];
						$serviceOrderResult[$kk]['sod_location'] = $v['sod_location'];
						$serviceOrderResult[$kk]['sodLocationValue'] = showAreaName($v['sod_location']);
						$serviceOrderResult[$kk]['list'][] = $sodList[$k];
					}
				}
			}
			$result['serviceOrderResult'] = $serviceOrderResult;
			return $result;
		}else {
			$this->error = '缺少企业用户ID!';
			return false;
		}
	}
	
	/**
	 * getReduceList function
	 * 获取报减列表
	 * @param int $companyId 企业用户ID
	 * @param string $condition 查询条件
	 * @param int $size 分页大小 默认10
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getReduceList($companyId = 0, $condition = ' 1 = 1 ',$size = 10){
		if ($companyId) {
			//分页
			$count  = $this->field('so.*,sp.name as product_name,ci.company_name, pb.user_name, pb.card_num,sod.id as sod_id,sod.type as sod_type,sod.payment_type as sod_payment_type,sod.state as sod_state,sod.location as sod_location,sod.pay_date as sod_pay_date,sod.note as sod_note')->join('as so LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->join('LEFT join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id')->join('LEFT join '.C('DB_PREFIX').'person_base as pb on sod.base_id = pb.id')->where($condition)->order('sod.create_time asc')->count();// 查询满足要求的总记录数
			$page = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数(10)
			//$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
			$page->setConfig('theme','<span class="fr">共%TOTAL_PAGE%页 %HEADER%</span> <div class="page fr">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%</div>');
			$page->lastSuffix=false;
			$page->rollPage=5;
			$page->setConfig('prev','上一页');
			$page->setConfig('next','下一页');
			$page->setConfig('last','末页');
			$page->setConfig('first','首页');
			$result['show'] = $page->show();// 分页显示输出
			
			//数据
			$serviceOrderResult = $this->field('so.*,sp.name as product_name,ci.company_name, pb.user_name, pb.card_num,sod.base_id,sod.id as sod_id,sod.type as sod_type,sod.payment_type as sod_payment_type,sod.state as sod_state,sod.detail_state as sod_detail_state,sod.location as sod_location,sod.pay_date as sod_pay_date,sod.note as sod_note,po.abort_add_del_date')->join('as so LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->join('LEFT join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id')->join('LEFT join '.C('DB_PREFIX').'person_base as pb on sod.base_id = pb.id')->where($condition)->order('sod.create_time asc')->limit($page->firstRow.','.$page->listRows)->select();
			
			foreach ($serviceOrderResult as $key => $value) {
				$serviceOrderResult[$key]['orderDateValue'] = $value['order_date']?substr_replace($value['order_date'],'/',4,0):'';
				$serviceOrderResult[$key]['sodPayDateValue'] = $value['sod_pay_date']?substr_replace($value['sod_pay_date'],'/',4,0):'';
				
				//报减内容
				$serviceOrderResult[$key]['sodPaymentTypeValue'] =get_status_value($serviceOrderResult[$key]['sod_payment_type'],'ServiceOrderDetailPaymentType');
				
				//状态
				$serviceOrderResult[$key]['sodStateValue'] =get_status_value($serviceOrderResult[$key]['sod_state'],'ServiceOrderDetailState');
				
				//参保地
				$serviceOrderResult[$key]['sodLocationValue'] = showAreaName($value['sod_location']);
				
				//报增报减截止日期
				$serviceOrderResult[$key]['abortAddDelDateValue'] = substr_replace($value['order_date'],'-',4,0).'-'.$serviceOrderResult[$key]['abort_add_del_date'];
				
				//是否在报增报减截止日期之前
				$serviceOrderResult[$key]['isBeforeAbortAddDelDate'] = strtotime($serviceOrderResult[$key]['abortAddDelDateValue']) > time()?true:false;
			}
			
			$result['serviceOrderResult'] = $serviceOrderResult;
			return $result;
		}else {
			$this->error = '缺少企业用户ID!';
			return false;
		}
	}
	
	/**
	 * getWarrantyList function
	 * 获取在保列表
	 * @param int $companyId 企业用户ID
	 * @param string $condition 查询条件
	 * @param int $size 分页大小 默认10
	 * @param int $type 在保状态 1报增 2报减 3在保 默认1
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getWarrantyList($companyId = 0, $condition = ' 1 = 1 ', $size = 10, $type = 1){
		if ($companyId) {
			//子查询
			$serviceOrderSubSql = $this->field('so.*, sp.name as product_name, ci.company_name,sod.base_id,sod.pay_date,sod.location,po.abort_add_del_date,sod.payment_type,sod.state as sod_state')->join('as so LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->join('LEFT join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id ')->order('sod.base_id desc ,sod.pay_date asc')->where(array('po.company_id'=>$companyId,'sod.state'=>array('in','3,4,-4,-5'),'sod.type'=>$type))->select(false);
			//社保子查询
			//$socServiceOrderSubSql = $this->field('so.*, sp.name as product_name, ci.company_name,sod.base_id,sod.pay_date,sod.location')->join('as so LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->join('LEFT join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id and sod.type='.$type)->order('sod.base_id desc ,sod.pay_date asc')->where(array('po.company_id'=>$companyId,'sod.type'=>$type,'sod.payment_type'=>1))->select(false);
			
			//公积金子查询
			//$proServiceOrderSubSql = $this->field('so.*, sp.name as product_name, ci.company_name,sod.base_id,sod.pay_date,sod.location')->join('as so LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->join('LEFT join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id and sod.type='.$type)->order('sod.base_id desc ,sod.pay_date asc')->where(array('po.company_id'=>$companyId,'sod.type'=>$type,'sod.payment_type'=>2))->select(false);
			
			//分页
			$count  = $serviceOrderResult = $this->field(' pb.user_name, pb.card_num,pb.mobile,sod.*')->table($serviceOrderSubSql.' as sod')->join('LEFT join '.C('DB_PREFIX').'person_base as pb on sod.base_id = pb.id and (pb.provident_fund_state = 1 or pb.social_insurance_state = 1)')->where($condition)->count('DISTINCT(pb.id)');
			
			$page = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数(10)
			//$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
			$page->setConfig('theme','<span class="fr">共%TOTAL_PAGE%页 %HEADER%</span> <div class="page fr">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%</div>');
			$page->lastSuffix=false;
			$page->rollPage=5;
			$page->setConfig('prev','上一页');
			$page->setConfig('next','下一页');
			$page->setConfig('last','末页');
			$page->setConfig('first','首页');
			$result['show'] = $page->show();// 分页显示输出
			
			//数据
			$serviceOrderResult = $this->field(' pb.user_name, pb.card_num,pb.mobile,sod.*')->table($serviceOrderSubSql.' as sod')->join('LEFT join '.C('DB_PREFIX').'person_base as pb on sod.base_id = pb.id and (pb.provident_fund_state = 1 or pb.social_insurance_state = 1)')->where($condition)->group('pb.id')->limit($page->firstRow.','.$page->listRows)->select();
			
			$serviceOrderDetailResult = $this->query($serviceOrderSubSql);
			foreach ($serviceOrderResult as $k => $v) {
				$serviceOrderResult[$k]['locationValue'] = showAreaName($v['location']);
				//根据报增报减截止日期计算订单月份
				$tempAddDelDate = strtotime(date('Y-m').'-'.$v['abort_add_del_date'].' 00:00:00');
				$serviceOrderResult[$k]['abortAddDelDateValue'] = (time() > $tempAddDelDate) ? date('Ym',strtotime('+1 Month',time())):date('Ym');
				
				//计算社保与公积金起缴日期
				foreach ($serviceOrderDetailResult as $kk => $vv) {
					if ($v['base_id'] == $vv['base_id'] && !empty($vv['pay_date']) && 0 != $vv['pay_date']) {
						if (1 == $vv['payment_type']) {
							if (!isset($serviceOrderResult[$k]['soc_pay_date'])) {
								$serviceOrderResult[$k]['soc_pay_date'] = $vv['pay_date'];
							}else {
								if ($serviceOrderResult[$k]['soc_pay_date'] > $vv['pay_date']) {
									$serviceOrderResult[$k]['soc_pay_date'] = $vv['pay_date'];
								}
							}
						}else if(2 == $vv['payment_type']){
							if (!isset($serviceOrderResult[$k]['pro_pay_date'])) {
								$serviceOrderResult[$k]['pro_pay_date'] = $vv['pay_date'];
							}else {
								if ($serviceOrderResult[$k]['pro_pay_date'] > $vv['pay_date']) {
									$serviceOrderResult[$k]['pro_pay_date'] = $vv['pay_date'];
								}
							}
						}
					}
				}
			}
			$result['serviceOrderResult'] = $serviceOrderResult;
			return $result;
		}else {
			$this->error = '缺少企业用户ID!';
			return false;
		}
	}
	
	/**
	 * getStopList function
	 * 获取停保列表
	 * @param int $companyId 企业用户ID
	 * @param string $condition 查询条件
	 * @param int $size 分页大小 默认10
	 * @param int $type 在保状态 1报增 2报减 3在保 默认2
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getStopList($companyId = 0, $condition = ' 1 = 1 ', $size = 10, $type = 2){
		if ($companyId) {
			//子查询
			$serviceOrderSubSql = $this->field('so.*, sp.name as product_name, ci.company_name,sod.base_id,sod.pay_date,sod.location')->join('as so LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->join('LEFT join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id and sod.type='.$type)->order('sod.base_id desc ,sod.pay_date desc')->where(array('po.company_id'=>$companyId,'sod.state'=>3,'sod.type'=>$type))->select(false);
			
			//分页
			$count  = $serviceOrderResult = $this->field(' pb.user_name, pb.card_num,pb.mobile,sod.*')->table($serviceOrderSubSql.' as sod')->join('LEFT join '.C('DB_PREFIX').'person_base as pb on sod.base_id = pb.id and (pb.provident_fund_state = 0 and pb.social_insurance_state = 0)')->where($condition)->count('DISTINCT(pb.id)');
			$page = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数(10)
			//$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
			$page->setConfig('theme','<span class="fr">共%TOTAL_PAGE%页 %HEADER%</span> <div class="page fr">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%</div>');
			$page->lastSuffix=false;
			$page->rollPage=5;
			$page->setConfig('prev','上一页');
			$page->setConfig('next','下一页');
			$page->setConfig('last','末页');
			$page->setConfig('first','首页');
			$result['show'] = $page->show();// 分页显示输出
			
			//数据
			$serviceOrderResult = $this->field(' pb.user_name, pb.card_num,pb.mobile,sod.*')->table($serviceOrderSubSql.' as sod')->join('LEFT join '.C('DB_PREFIX').'person_base as pb on sod.base_id = pb.id and (pb.provident_fund_state = 0 and pb.social_insurance_state = 0)')->where($condition)->group('pb.id')->order('sod.order_date')->limit($page->firstRow.','.$page->listRows)->select();
			foreach ($serviceOrderResult as $key => $value) {
				$serviceOrderResult[$key]['locationValue'] = showAreaName($value['location']);
			}
			$result['serviceOrderResult'] = $serviceOrderResult;
			return $result;
		}else {
			$this->error = '缺少企业用户ID!';
			return false;
		}
	}
	
	/**
	 * getServiceList function
	 * 获取所有服务列表
	 * @param int $companyId 企业用户ID
	 * @param string $condition 查询条件
	 * @param int $size 分页大小 默认10
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getServiceList($companyId = 0, $condition = ' 1 = 1 ',$size = 10){
		if ($companyId) {
			$sodCount  = $this->field('count(DISTINCT sod.service_order_id,sod.base_id) as count')->join('as so LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->join('RIGHT join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id')->join('LEFT join '.C('DB_PREFIX').'person_base as pb on sod.base_id = pb.id')->where($condition)->find();// 查询满足要求的总记录数
			$count = $sodCount['count'];
			$page = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数(10)
			//$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
			$page->setConfig('theme','<span class="fr">共%TOTAL_PAGE%页 %HEADER%</span> <div class="page fr">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%</div>');
			$page->lastSuffix=false;
			$page->rollPage=5;
			$page->setConfig('prev','上一页');
			$page->setConfig('next','下一页');
			$page->setConfig('last','末页');
			$page->setConfig('first','首页');
			$result['show'] = $page->show();// 分页显示输出
			
			//数据
			$serviceOrderResult  = $this->field('DISTINCT sod.service_order_id,sod.base_id,pb.user_name,pb.card_num ')->join('as so LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->join('RIGHT join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id')->join('LEFT join '.C('DB_PREFIX').'person_base as pb on sod.base_id = pb.id')->limit($page->firstRow.','.$page->listRows)->where($condition)->select();// 查询满足要求的总记录数
			
			//$sodList = $this->field('so.*,sod.service_order_id,sod.base_id,sp.name as product_name,ci.company_name, pb.user_name, pb.card_num,sod.id as sod_id,sod.type as sod_type,sod.payment_type as sod_payment_type,sod.state as sod_state,sod.location as sod_location,sod.pay_date as sod_pay_date,sod.note as sod_note')->join('as so LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->join('LEFT join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id')->join('LEFT join '.C('DB_PREFIX').'person_base as pb on sod.base_id = pb.id')->where($condition)->order('sod.create_time asc')->limit($page->firstRow.','.$page->listRows)->select();
			$sodList = $this->field('so.*,sod.service_order_id,sod.base_id,sp.name as product_name,ci.company_name, pb.user_name, pb.card_num,sod.id as sod_id,sod.type as sod_type,sod.payment_type as sod_payment_type,sod.state as sod_state,sod.location as sod_location,sod.pay_date as sod_pay_date,sod.amount as sod_amount,sod.detail_state as sod_detail_state,sod.note as sod_note,sod.rule_id as sod_rule_id,sod.card_number as sod_card_number,sod.person_scale as sod_person_scale,sod.company_scale as sod_company_scale')->join('as so LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->join('LEFT join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id')->join('LEFT join '.C('DB_PREFIX').'person_base as pb on sod.base_id = pb.id')->where($condition)->order('sod.payment_type asc, sod.pay_date asc, sod.create_time asc')->select();
			$productTemplateRule = D('TemplateRule');
			$calculate = new \Common\Model\Calculate();
			foreach ($sodList as $k => $v) {
				$sodList[$k]['orderDateValue'] = $v['order_date']?substr_replace($v['order_date'],'/',4,0):'';
				$sodList[$k]['sodPayDateValue'] = $v['sod_pay_date']?substr_replace($v['sod_pay_date'],'/',4,0):'';
				
				//内容
				$sodList[$k]['sodPaymentTypeValue'] =get_status_value($sodList[$k]['sod_payment_type'],'ServiceOrderDetailPaymentType');
				
				//状态
				$sodList[$k]['sodStateValue'] =get_status_value($sodList[$k]['sod_state'],'ServiceOrderDetailState',$sodList[$k]['sod_detail_state']);
				
				//根据规则id计算费用
				if ($v['sod_rule_id']) {
					$calculate = new \Common\Model\Calculate();
					if (1 == $v['sod_payment_type']) {//社保
						$productTemplateRuleResult = $productTemplateRule->where(array('id'=>$v['sod_rule_id']))->find();
						$json = json_encode(array('amount' => $v['sod_amount'], 'month' => 1, 'cardno' => $v['sod_card_number']));
						$json = $calculate->detail($productTemplateRuleResult['rule'], $json, 1);
						//$json = json_decode($json);
						//$sodList[$k]['company'] = $json->data->company;
						//$sodList[$k]['person'] = $json->data->person;
						$json = json_decode($json,true);
						$sodList[$k]['company'] = $json['data']['company'];
						$sodList[$k]['person'] = $json['data']['person'];
						$sodList[$k]['total'] = $sodList[$k]['company'] + $sodList[$k]['person'];
					}else if (2 == $v['sod_payment_type']) {//公积金
						$productTemplateRuleResult = $productTemplateRule->where(array('id'=>$v['sod_rule_id']))->find();
						$json = json_encode(array('amount' => $v['sod_amount'], 'month' => 1, 'personScale' => $v['sod_person_scale'], 'companyScale' => $v['sod_company_scale'], 'cardno' => $v['sod_card_number']));
						$json = $calculate->detail($productTemplateRuleResult['rule'], $json, 2);
						$json = json_decode($json,true);
						$sodList[$k]['company'] = $json['data']['company'];
						$sodList[$k]['person'] = $json['data']['person'];
						$sodList[$k]['total'] = $sodList[$k]['company'] + $sodList[$k]['person'];
					}else if (3== $v['sod_payment_type']) {//残障金
						# code...
					}else if (4 == $v['sod_payment_type']) {//其他
						# code...
					}
				}
				
				foreach ($serviceOrderResult as $kk => $vv) {
					if ($v['service_order_id'] == $vv['service_order_id'] && $v['base_id'] == $vv['base_id']) {
						$serviceOrderResult[$kk]['product_name'] = $v['product_name'];
						$serviceOrderResult[$kk]['company_name'] = $v['company_name'];
						$serviceOrderResult[$kk]['sod_location'] = $v['sod_location'];
						$serviceOrderResult[$kk]['order_date'] = $v['order_date'];
						$serviceOrderResult[$kk]['orderDateValue'] = substr_replace($v['order_date'],'/',4,0);
						$serviceOrderResult[$kk]['sodLocationValue'] = showAreaName($v['sod_location']);
						//$serviceOrderResult[$kk]['list'][] = $v;
						$serviceOrderResult[$kk]['list'][] = $sodList[$k];
					}
				}
			}
			//$serviceOrderResult['list'] = $sodList;
			$result['serviceOrderResult'] = $serviceOrderResult;
			return $result;
		}else {
			$this->error = '缺少企业用户ID!';
			return false;
		}
	}
	
	/**
	 * getIdCardImgByBaseId function
	 * 根据个人信息ID获取身份证图片
	 * @param int $baseId 个人信息ID
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getIdCardImgByBaseId($baseId = 0){
		if ($baseId ) {
			$path = getFilePath($baseId,'./Uploads/Person/','IDCard');
			$idCardFront = $path.'idCardFront.jpg';
			$idCardBack = $path.'idCardBack.jpg';
			$result = array();
			if (file_exists($idCardFront)) {
				$result['idCardFront'] = ltrim($idCardFront,'./');
			}else {
				$result['idCardFront'] = '';//默认图片
			}
			if (file_exists($idCardBack)) {
				$result['idCardBack'] = ltrim($idCardBack,'./');
			}else {
				$result['idCardBack'] = '';//默认图片
			}
			return $result;
		}else {
			$this->error = '非法参数!';
			return false;
		}
	}
	
	

	
	/**
	 * getPayrollCreditCount function
	 * 获取代发工资人数
	 * @param int $userId 企业用户ID
	 * @param string $condition 查询条件
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getPayrollCreditCount($userId = 0, $condition = ' 1 = 1 '){
		if ($userId) {
			$count=$this->field('count(*) count')->join('as so left join '.C('DB_PREFIX').'service_order_salary as sos on so.id=sos.service_order_id ')->where(array('so.user_id'=>$userId,'sos.date'=>array('eq',date('Ym'))))->find();
			return $count['count'];
		}else{
			$this->error = '非法参数!';
			return false;
		}
	
	}
	/**
	 * [getOrderSchedule]
	 * 获取企业的订单
	 * @param  [type] $userId [企业id]
	 * @param  string $condition [查询条件]
	 * @return [type]            
	 */
	public function getOrderSchedule($userId,$condition = ' 1 = 1 '){
		if ($userId) {
			$companyOrder=$this->field('so.*,sa.qq,sp.name as product_name,spo.abort_add_del_date as aadd,spo.inc_create_bill_date as icbd,spo.inc_abort_payment_date as iapd,spo.sala_create_bill_date as scbd,spo.sala_abort_payment_date as sapd')
								->join('as so left join '.C('DB_PREFIX').'service_product_order as spo on spo.product_id=so.product_id and spo.user_id='.$userId)
								->join('left join '.C('DB_PREFIX').'service_product as sp on so.product_id = sp.id ')
								->join('left join '.C('DB_PREFIX').'service_admin as sa on spo.admin_id=sa.id ')
								->where($condition)
								->order('so.create_time desc')
								->limit(10)
								->select();
			return $companyOrder;
		}else{
			$this->error='非法参数!';
			return false;
		}
	}	

	/**
	 * getProgressServiceOrder function
	 * 获取处理中人数
	 * @param int $userId 企业用户ID
	 * @param string $condition 查询条件
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getProgressServiceOrder($userId = 0, $condition = ' 1 = 1 '){
		if ($userId) {
			$serviceOrderResult = $this->field('so.*,sp.name as product_name,po.is_salary as product_is_salary,ci.company_name')
										->join('as so LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_id = po.product_id and po.user_id = '.$userId)
										->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')
										->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')
										->where($condition)
										->order('so.create_time asc')
										->select();
			if ($serviceOrderResult) {
				#$serviceOrderDetail = M('ServiceOrderDetail');
				$serviceOrderSalary = M('ServiceOrderSalary');
				foreach ($serviceOrderResult as $k => $v) {
					#$serviceOrderDetailResult = $serviceOrderDetail->field('location')->where(array('service_order_id'=>$v['id'],'location'=>array('exp','is not null')))->select();
					#$serviceOrderSalaryResult = $serviceOrderDetail->field('location')->where(array('order_id'=>$v['id'],'location'=>array('exp','is not null')))->select();
					$locationArray = array();
					if ($serviceOrderDetailResult) {
						foreach ($serviceOrderDetailResult as $kk => $vv) {
							$locationArray['location'][] = $vv['location'];
						}
					}
					if ($serviceOrderSalaryResult) {
						foreach ($serviceOrderSalaryResult as $kk => $vv) {
							$locationArray['location'][] = $vv['location'];
						}
					}
					if ($locationArray) {
						$locationArray['location'] = array_unique($locationArray['location']);
						foreach ($locationArray['location'] as $kk => $vv) {
							$locationArray['locationValue'][] = showAreaName($vv);
						}
						//默认显示城市
						$locationArray['locationDefaultValue'] = showAreaName(reset($locationArray['location'])).(1 < count($locationArray['location'])?'+':'');
						$serviceOrderResult[$k]['location'] = $locationArray;
					}
				}
			}
			return $serviceOrderResult;
		}else {
			$this->error = '非法参数!';
			return false;
		}
	}
	
	/**
	 * getIncreaseOrderByBsaeId function
	 * 获取个人报增订单
	 * @param int $companyId 企业用户ID
	 * @param string $condition 查询条件
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getIncreaseOrderByBsaeId($companyId = 0, $condition = ' 1 = 1 '){
		if ($companyId) {
			/*$sodCount  = $this->field('count(DISTINCT sod.service_order_id,sod.base_id) as count')->join('as so LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->join('RIGHT join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id')->join('LEFT join '.C('DB_PREFIX').'person_base as pb on sod.base_id = pb.id')->where($condition)->find();// 查询满足要求的总记录数
			$count = $sodCount['count'];*/
			
			//数据
			$serviceOrderResult  = $this->field('DISTINCT sod.service_order_id,sod.pay_date,sod.base_id,pb.user_name,pb.card_num ')->join('as so LEFT join '.C('DB_PREFIX').'service_product_order as po on so.product_order_id = po.id and po.company_id = '.$companyId)->join('LEFT join '.C('DB_PREFIX').'service_product as sp on po.product_id = sp.id')->join('LEFT join '.C('DB_PREFIX').'company_user as cu on sp.company_id = cu.id')->join('LEFT join '.C('DB_PREFIX').'company_info as ci on sp.company_id = ci.company_id')->join('RIGHT join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id')->join('LEFT join '.C('DB_PREFIX').'person_base as pb on sod.base_id = pb.id')->where($condition)->order('sod.pay_date desc')->find();// 查询满足要求的总记录数
			return $serviceOrderResult;
		}
	}
	
	/**
	 * getServiceOrderLocation function
	 * 获取服务订单的所有参保地
	 * @param int $serviceOrderId 服务订单ID
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getServiceOrderLocation($serviceOrderId = 0){
		$serviceOrderLocation = $this->field('distinct location')->alias('so')->join('left join '.C('DB_PREFIX').'service_order_detail as sod on so.id = sod.service_order_id')->where(array('so.id'=>$serviceOrderId))->select();
		//dump($serviceOrderLocation);
		//计算是否多个城市
		$result = array();
		if ($serviceOrderLocation) {
			foreach ($serviceOrderLocation as $key => $value) {
				$result['location'][] = $value['location'];
			}
			if ($result['location']) {
				$result['location'] = array_unique($result['location']);
				foreach ($result['location'] as $key => $value) {
					$result['locationValue'][] = showAreaName($value);
				}
				//默认显示城市
				$result['locationDefaultValue'] = showAreaName(reset($result['location'])).(1 < count($result['location'])?'+':'');
				return $result;
			}else {
				$this->error = '不存在参保地!';
				return false;
			}
		}else {
			$this->error = '不存在参保地!';
			return false;
		}
	}
	
	
	public function getInsuranceInfo($baseId,$condition){
		$serviceOrderDetail = M('ServiceOrderDetail');
		$user = M('PersonBase');
		$result = $serviceOrderDetail->alias('d')->field("bi.balance,o.order_no,d.detail_state,d.service_order_id,d.person_scale,d.company_scale,d.base_id,d.location,d.type,d.payment_type,d.state,d.replenish,d.note,d.card_number,d.amount,d.pay_date,d.rule_id,c.company_name,c.company_id,o.order_date,sp.company_id as scompany_id,sp.name as product_name,o.product_order_id")
			->join("zbw_service_order o ON o.id = d.service_order_id")
			->join("zbw_service_product_order p ON p.id = o.product_order_id")
			->join("zbw_service_product sp ON sp.id = p.product_id")
			->join("zbw_company_info c ON c.company_id = sp.company_id")
			->join("LEFT JOIN zbw_service_bill_detail bi ON bi.order_detail_id = d.id")
			->where($condition)->select();
			//->where("d.base_id = {$order_detail['base_id']} AND d.pay_date = {$order_detail['pay_date']} AND d.service_order_id = {$order_detail['service_order_id']} AND d.type = {$order_detail['type']}")->select();
			//->where("d.base_id = {$order_detail['base_id']} AND d.pay_date = {$order_detail['pay_date']} AND d.service_order_id = {$order_detail['service_order_id']} AND o.order_no = \"{$order_detail['order_no']}\"")->select();
			//->where("d.base_id = {$order_detail['base_id']} AND d.pay_date = {$order_detail['pay_date']} AND d.service_order_id = {$order_detail['service_order_id']} ")->select();
		if(empty($result)) return ;
		$user_info = $user->field("id as base_id,card_num,user_name,mobile,brithday,gender")->where("id = {$baseId}")->find();
		foreach($result as $k=>$v){
			$user_info['order_no'] = $v['order_no'];
			$user_info['service_order_id'] = $v['service_order_id'];
			$user_info['product_order_id'] = $v['product_order_id'];
			$user_info['location'] = $v['location'];
			$user_info['company_name'] = $v['company_name'];
			$user_info['scompany_id'] = $v['scompany_id'];
			$user_info['order_date'] = $v['order_date'];
			$user_info['company_id'] = $v['company_id'];
			$user_info['product_name'] = $v['product_name'];
			$user_info['scompany_id'] = $v['scompany_id'];
			if($v['payment_type'] == 1){
				$user_info['sbalance'] = $v['balance'];
				$user_info['pay_date'] = $v['pay_date'];
				$user_info['syear'] = intval(substr($user_info['pay_date'],0,4));
				$user_info['smonth'] = intval(substr($user_info['pay_date'],4,2));
				$user_info['sdetail_state'] = $v['detail_state'];
				$user_info['stype'] = $v['type'];
				$user_info['sstate'] = $v['state'];
				$user_info['sreplenish'] = $v['replenish'];
				$user_info['snote'] = $v['note'];
				$user_info['samount'] = $v['amount'];
				$user_info['srule_id'] = $v['rule_id'];
				$user_info['scard_number'] = $v['card_number'];
				$user_info['srule'] = $this->_rule( $user_info['srule_id'],1, $user_info);
			}elseif($v['payment_type'] == 2){
				$user_info['gbalance'] = $v['balance'];
				$user_info['pay_date'] = $v['pay_date'];
				$user_info['gyear'] = intval(substr($user_info['pay_date'],0,4));
				$user_info['gmonth'] = intval(substr($user_info['pay_date'],4,2));
				$user_info['gdetail_state'] = $v['detail_state'];
				$user_info['gtype'] = $v['type'];
				$user_info['gstate'] = $v['state'];
				$user_info['greplenish'] = $v['replenish'];
				$user_info['gnote'] = $v['note'];
				$user_info['gamount'] = $v['amount'];
				$user_info['grule_id'] = $v['rule_id'];
				$user_info['person_scale'] = $v['person_scale'];
				$user_info['company_scale'] = $v['company_scale'];
				$user_info['gcard_number'] = $v['card_number'];
				$user_info['grule'] = $this->_rule( $user_info['grule_id'],2, $user_info);
			}elseif($v['payment_type'] == 3){
				$user_info['dbalance'] = $v['balance'];
				$user_info['dyear'] = intval(substr($user_info['pay_date'],0,4));
				$user_info['dmonth'] = intval(substr($user_info['pay_date'],4,2));
				$user_info['ddetail_state'] = $v['detail_state'];
				$user_info['dtype'] = $v['type'];
				$user_info['dstate'] = $v['state'];
				$user_info['dreplenish'] = $v['replenish'];
				$user_info['dnote'] = $v['note'];
				$user_info['damount'] = $v['amount'];
				$user_info['drule_id'] = $v['rule_id'];
				$user_info['drule'] = $this->_rule( $user_info['drule_id'],3);
			}elseif($v['payment_type'] == 4){
				$user_info['orule_id'] = $v['rule_id'];
				$user_info['orule'] = $this->_rule( $user_info['orule_id'],4);
			}
		}
		if(empty($user_info['srule'])){
			$ruleInfoDf = $this->_defaultRule(  $user_info['location'],1);
			$user_info['samount'] = $ruleInfoDf['rule']['min'];
			$user_info['srule_id'] = $ruleInfoDf['id'];
			$user_info['srule'] = $this->_rule( $user_info['srule_id'],1, $user_info);
		}
		if(empty($user_info['grule'])){
			$ruleInfoDf = $this->_defaultRule(  $user_info['location'],2);
			$user_info['gamount'] = $ruleInfoDf['rule']['min'];
			$user_info['grule_id'] = $ruleInfoDf['id'];
			$user_info['person_scale'] = intval($ruleInfoDf['rule']['person']);
			$user_info['company_scale'] = intval($ruleInfoDf['rule']['company']);
			$user_info['grule'] = $this->_rule( $user_info['grule_id'],2, $user_info);
		}
		$picture = get_idCardImg_by_baseId($user_info['base_id']);
		if(!empty($picture)){
			$user_info['picture'] = $picture['idCardFront'].','.$picture['idCardBack'];
		}
		$user_info['age'] = date('Y',time()) - date('Y',strtotime($user_info['brithday']));
		$user_info['warranty_location'] = $this->_location($user_info['product_order_id']);
		$user_info['company_info'] = $this->_serviceLocation($user_info['scompany_id']);
		return $user_info;
	}
	
	/***********天云锅**********/
	public function getPersonInfo($order_detail){
		/*$m = M('ServiceOrderDetail');
		$result = $m->alias('d')->field("d.detail_state,d.service_order_id,d.person_scale,d.company_scale,d.base_id,d.location,d.type,d.payment_type,d.state,d.replenish,d.note,d.card_number,d.amount,d.pay_date,d.rule_id,c.company_name,c.company_id,o.order_date,sp.company_id as scompany_id,sp.name as product_name,o.product_order_id")
			->join("zbw_service_order o ON o.id = d.service_order_id")
			->join("zbw_service_product_order p ON p.id = o.product_order_id")
			->join("zbw_service_product sp ON sp.id = p.product_id")
			->join("zbw_company_info c ON c.company_id = p.company_id")
			->where("d.base_id = {$order_detail['base_id']} AND d.pay_date = {$order_detail['pay_date']} AND d.service_order_id = {$order_detail['service_order_id']} AND d.state <> -9")->select();
		if(empty($result)) return ;
		$user = M('PersonBase');
		$user_info = $user->field("id as base_id,card_num,user_name,mobile,brithday,gender")->where("id = {$order_detail['base_id']}")->find();
		foreach($result as $k=>$v){
			$user_info['service_order_id'] = $v['service_order_id'];
			$user_info['product_order_id'] = $v['product_order_id'];
			$user_info['location'] = $v['location'];
			$user_info['pay_date'] = $v['pay_date'];
			$user_info['company_name'] = $v['company_name'];
			$user_info['order_date'] = $v['order_date'];
			$user_info['company_id'] = $v['company_id'];
			$user_info['scompany_id'] = $v['scompany_id'];
			$user_info['product_name'] = $v['product_name'];
			if($v['payment_type'] == 1){
				$user_info['sdetail_state'] = $v['detail_state'];
				$user_info['stype'] = $v['type'];
				$user_info['sstate'] = $v['state'];
				$user_info['sreplenish'] = $v['replenish'];
				$user_info['snote'] = $v['note'];
				$user_info['samount'] = $v['amount'];
				$user_info['srule_id'] = $v['rule_id'];
				$user_info['scard_number'] = $v['card_number'];
				$user_info['srule'] = $this->rule( $user_info['srule_id'],1, $user_info);
			}elseif($v['payment_type'] == 2){
				$user_info['gdetail_state'] = $v['detail_state'];
				$user_info['gtype'] = $v['type'];
				$user_info['gstate'] = $v['state'];
				$user_info['greplenish'] = $v['replenish'];
				$user_info['gnote'] = $v['note'];
				$user_info['gamount'] = $v['amount'];
				$user_info['grule_id'] = $v['rule_id'];
				$user_info['person_scale'] = $v['person_scale'];
				$user_info['company_scale'] = $v['company_scale'];
				$user_info['gcard_number'] = $v['card_number'];
				$user_info['grule'] = $this->rule( $user_info['grule_id'],2, $user_info);
			}elseif($v['payment_type'] == 3){
				$user_info['drule_id'] = $v['rule_id'];
				$user_info['drule'] = $this->rule( $user_info['drule_id'],3);
			}elseif($v['payment_type'] == 4){
				$user_info['orule_id'] = $v['rule_id'];
				$user_info['orule'] = $this->rule( $user_info['orule_id'],4);
			}
		}
		$user_info['age'] = date('Y',time()) - date('Y',strtotime($user_info['brithday']));
		$user_info['warranty_location'] = $this->location($user_info['product_order_id']);
		$user_info['company_info'] = $this->serviceLocation($user_info['scompany_id']);
		return $user_info;*/
		
		$m = M('ServiceOrderDetail');
		$user = M('PersonBase');
		$result = $m->alias('d')->field("bi.balance,o.order_no,d.detail_state,d.service_order_id,d.person_scale,d.company_scale,d.base_id,d.location,d.type,d.payment_type,d.state,d.replenish,d.note,d.card_number,d.amount,d.pay_date,d.rule_id,c.company_name,c.company_id,o.order_date,sp.company_id as scompany_id,sp.name as product_name,o.product_order_id")
			->join("zbw_service_order o ON o.id = d.service_order_id")
			->join("zbw_service_product_order p ON p.id = o.product_order_id")
			->join("zbw_service_product sp ON sp.id = p.product_id")
			->join("zbw_company_info c ON c.company_id = sp.company_id")
			->join("LEFT JOIN zbw_service_bill_detail bi ON bi.order_detail_id = d.id")
			->where("d.base_id = {$order_detail['base_id']} AND d.pay_date = {$order_detail['pay_date']} AND d.service_order_id = {$order_detail['service_order_id']} AND d.type = {$order_detail['type']}")->select();
			//->where("d.base_id = {$order_detail['base_id']} AND d.pay_date = {$order_detail['pay_date']} AND d.service_order_id = {$order_detail['service_order_id']} AND o.order_no = \"{$order_detail['order_no']}\"")->select();
		
			//->where("d.base_id = {$order_detail['base_id']} AND d.pay_date = {$order_detail['pay_date']} AND d.service_order_id = {$order_detail['service_order_id']} ")->select();
		if(empty($result)) return ;
		$user_info = $user->field("id as base_id,card_num,user_name,mobile,brithday,gender")->where("id = {$order_detail['base_id']}")->find();
		foreach($result as $k=>$v){
			$user_info['order_no'] = $v['order_no'];
			$user_info['service_order_id'] = $v['service_order_id'];
			$user_info['product_order_id'] = $v['product_order_id'];
			$user_info['location'] = $v['location'];
			$user_info['company_name'] = $v['company_name'];
			$user_info['scompany_id'] = $v['scompany_id'];
			$user_info['order_date'] = $v['order_date'];
			$user_info['company_id'] = $v['company_id'];
			$user_info['product_name'] = $v['product_name'];
			$user_info['scompany_id'] = $v['scompany_id'];
			if($v['payment_type'] == 1){
				$user_info['sbalance'] = $v['balance'];
				$user_info['pay_date'] = $v['pay_date'];
				$user_info['syear'] = intval(substr($user_info['pay_date'],0,4));
				$user_info['smonth'] = intval(substr($user_info['pay_date'],4,2));
				$user_info['sdetail_state'] = $v['detail_state'];
				$user_info['stype'] = $v['type'];
				$user_info['sstate'] = $v['state'];
				$user_info['sreplenish'] = $v['replenish'];
				$user_info['snote'] = $v['note'];
				$user_info['samount'] = $v['amount'];
				$user_info['srule_id'] = $v['rule_id'];
				$user_info['scard_number'] = $v['card_number'];
				$user_info['srule'] = $this->_rule( $user_info['srule_id'],1, $user_info);
			}elseif($v['payment_type'] == 2){
				$user_info['gbalance'] = $v['balance'];
				$user_info['pay_date'] = $v['pay_date'];
				$user_info['gyear'] = intval(substr($user_info['pay_date'],0,4));
				$user_info['gmonth'] = intval(substr($user_info['pay_date'],4,2));
				$user_info['gdetail_state'] = $v['detail_state'];
				$user_info['gtype'] = $v['type'];
				$user_info['gstate'] = $v['state'];
				$user_info['greplenish'] = $v['replenish'];
				$user_info['gnote'] = $v['note'];
				$user_info['gamount'] = $v['amount'];
				$user_info['grule_id'] = $v['rule_id'];
				$user_info['person_scale'] = $v['person_scale'];
				$user_info['company_scale'] = $v['company_scale'];
				$user_info['gcard_number'] = $v['card_number'];
				$user_info['grule'] = $this->_rule( $user_info['grule_id'],2, $user_info);
			}elseif($v['payment_type'] == 3){
				$user_info['dbalance'] = $v['balance'];
				$user_info['dyear'] = intval(substr($user_info['pay_date'],0,4));
				$user_info['dmonth'] = intval(substr($user_info['pay_date'],4,2));
				$user_info['ddetail_state'] = $v['detail_state'];
				$user_info['dtype'] = $v['type'];
				$user_info['dstate'] = $v['state'];
				$user_info['dreplenish'] = $v['replenish'];
				$user_info['dnote'] = $v['note'];
				$user_info['damount'] = $v['amount'];
				$user_info['drule_id'] = $v['rule_id'];
				$user_info['drule'] = $this->_rule( $user_info['drule_id'],3);
			}elseif($v['payment_type'] == 4){
				$user_info['orule_id'] = $v['rule_id'];
				$user_info['orule'] = $this->_rule( $user_info['orule_id'],4);
			}
		}
		if(empty($user_info['srule'])){
			$ruleInfoDf = $this->_defaultRule(  $user_info['location'],1);
			$user_info['samount'] = $ruleInfoDf['rule']['min'];
			$user_info['srule_id'] = $ruleInfoDf['id'];
			$user_info['srule'] = $this->_rule( $user_info['srule_id'],1, $user_info);
		}
		if(empty($user_info['grule'])){
			$ruleInfoDf = $this->_defaultRule(  $user_info['location'],2);
			$user_info['gamount'] = $ruleInfoDf['rule']['min'];
			$user_info['grule_id'] = $ruleInfoDf['id'];
			$user_info['person_scale'] = intval($ruleInfoDf['rule']['person']);
			$user_info['company_scale'] = intval($ruleInfoDf['rule']['company']);
			$user_info['grule'] = $this->_rule( $user_info['grule_id'],2, $user_info);
		}
		$picture = get_idCardImg_by_baseId($user_info['base_id']);
		if(!empty($picture)){
			$user_info['picture'] = $picture['idCardFront'].','.$picture['idCardBack'];
		}
		$user_info['age'] = date('Y',time()) - date('Y',strtotime($user_info['brithday']));
		$user_info['warranty_location'] = $this->_location($user_info['product_order_id']);
		$user_info['company_info'] = $this->_serviceLocation($user_info['scompany_id']);
		return $user_info;
	}
	
	private function _serviceLocation($company_id){
		$m = D("CompanyInfo");
		$result = $m->field("company_name,location")->where("company_id = {$company_id}")->find();
		$area = getZoning();
		$result['city'] = trim($area[intval($result['location']/10000)*10000]['name'],'"');
		$result['province'] = trim($area[intval($result['location']/1000000)*1000000]['name'],'"');
		return $result;
	}
	
	private function _location($service_order_id){
		$m = D('WarrantyLocation');
		$res = $m->where("service_order_id = {$service_order_id} AND state <> -9")->select();
		return $res;
	}
	
	private function _defaultRule($location,$type){
		$class = D('template_rule');
		$result = $class->field('c.*')->alias('c')->join("zbw_product_template t ON c.template_id = t.id")->where("t.location = '{$location}' AND t.company_id = 0 AND c.type = {$type}" )->find();
		$result['rule'] = json_decode($result['rule'],true);
		return $result;
	}
	
	public function _rule($id,$type,$user_info = ''){
		$rule = D('template_rule');
		$result = $rule->where("id = {$id}")->find();
		$result['classify_mixed'] = explode("|",$result['classify_mixed']);
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
		
		$class = D('template_classify');
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
	
	public function comSalaryList($data){
		$m = M('ServiceOrderSalary');
		$page = I('get.p',1);
		$count = count($m->field('id')->where('order_id='.$data['service_order_id'])->select());
		$res = $m->field(array('t1.id','t2.user_name','t2.card_num','t1.date','t1.wages','t1.deduction_social_insurance','t1.deduction_provident_fund','t1.deduction_income_tax','t1.replacement','t1.deduction_other','t1.actual_wages','t1.state'))->alias('t1')->join('left join `zbw_person_base` t2 on t1.base_id=t2.id')->where('t1.order_id='.$data['service_order_id'])->select();
		$pageshow = showpage($count,10);
		return array('page'=>$pageshow,'result'=>$res);
	}
	
	public function salaryAudit($data){
		$m = M('ServiceOrderSalary');
		$res = $m->field(array('t1.id as service_order_salary_id','t1.base_id','t2.user_name','t2.card_num','t2.bank','t2.branch','t2.account','t1.date','t1.wages','t1.deduction_social_insurance','t1.deduction_provident_fund','t1.deduction_income_tax','t1.replacement','t1.deduction_other','t1.actual_wages'))->alias('t1')->join('`zbw_person_base` t2 on t1.base_id=t2.id')->where('t1.id='.$data['service_order_salary_id'])->find();
		return $res;
	}
	
	public function salaryData($id,$salary,$base_id,$person_base,$bill=0,$data=0){
		$m = M('ServiceOrderSalary');
		$res1 = $m->where('id='.$id)->data($salary)->save();
		$d = M('person_base');
		$res2 = $d->where('id='.$base_id)->data($person_base)->save();
		$n = M('service_bill_salary');
		$res3 = $n->where('id='.$bill['id'])->data($data)->save();
		$m->startTrans();
		if($bill['id']){
			if ($res1&&$res2&&$res3){
				$this->balance($bill['bill_id']);
				$m->commit();
				return 1;
			}else{
				$m->rollback();
				return 0;
			}
		}else{
			if ($res1&&$res2){
				$m->commit();
				return 1;
			}else{
				$m->rollback();
				return 0;
			}
		}
	}
	
	public function insuredState($id){
		$m = M('ServiceOrderSalary');
		$res = $m->field(array('t2.provident_fund_state','t2.social_insurance_state'))->alias('t1')->join('`zbw_person_base` t2 on t1.base_id=t2.id')->where('t1.id='.$id)->find();
		return $res;
	}
	
	public function salaryBalance($id,$state,$salary){
		$m = M('ServiceOrderSalary');
		$res = $m->field(array('t1.wages','t1.deduction_social_insurance','t1.deduction_provident_fund','t1.location','t2.product_order_id'))->alias('t1')->join('`zbw_service_order` t2 on t1.order_id=t2.id')->where('t1.id='.$id)->find();
		$d = M('warranty_location');
		$result = $d->field('af_service_price')->where('service_order_id='.$res['product_order_id'].' and `location`='.$res['location'])->find();		
		$res['af_service_price'] = $result['af_service_price'];
		if($state==3){
			$money = $salary['wages'] - $salary['deduction_social_insurance'] - $salary['deduction_provident_fund'] + $result['af_service_price'];
		}elseif($state==2){
			$money = $salary['wages'] - $res['deduction_social_insurance'] - $salary['deduction_provident_fund'] + $result['af_service_price'];			
		}elseif($state==1){
			$money = $salary['wages'] - $salary['deduction_social_insurance'] - $res['deduction_provident_fund'] + $result['af_service_price'];						
		}else{
			$money = $salary['wages'] - $res['deduction_social_insurance'] - $res['deduction_provident_fund'] + $result['af_service_price'];		
		}		
		return $money;
	}
	
	public function billId($id){
		$m = M('ServiceBillSalary');
		$res = $m->field(array('id','bill_id'))->where('salary_id='.$id)->find();
		return $res;
	}
	
	private function _nextOrder($id,$type){
		$m = M('ServiceOrder');
		$result = $m->alias('o')->field('o.product_order_id,o.order_date,d.pay_date')->join('zbw_service_order_detail d ON d.service_order_id = o.id')->where("d.id = {$id}")->find();
		$year = intval(substr($result['order_date'],0,4));
		$month = intval(substr($result['order_date'],4,2));
		$date = $year.'-'.$month;
		$nextDate = date('Ym',strtotime('+1 month',strtotime($date)));
		$nextResult = $m->field('id')->where("product_order_id = {$result['product_order_id']} AND order_date = {$nextDate} AND (state<> -1 OR state<> 2 )")->find();
		if(empty($nextResult))	return ajaxJson(-1,'下一个月订单不存在');
		$year = intval(substr($result['pay_date'],0,4));
		$month = intval(substr($result['pay_date'],4,2));
		$date = $year.'-'.$month;
		$detail = M('ServiceOrderDetail');
		$res = $detail->where("id = {$id}")->find();
		if(!empty($res)){
			unset($res['id']);
			$res['create_time'] = date('Y-m-d H:i:s',time());
			$res['pay_date'] = date('Ym',strtotime('+1 month',strtotime($date)));
			$res['modify_time'] = date('Y-m-d H:i:s',time());
			$res['detail_state'] = 0;
			$res['service_order_id'] = $nextResult['id'];
			$state = $detail->field('id')->where("base_id = {$res['base_id']} AND service_order_id = {$res['service_order_id']} AND pay_date = {$res['pay_date']} AND payment_type = {$type}")->find();
			echo json_encode($state);

			if($state){
				$detail->where("base_id = {$res['base_id']} AND service_order_id = {$res['service_order_id']} AND pay_date = {$res['pay_date']} AND payment_type = {$type}")->save($res);
			}
			else{
				$detail->add($res);
			}
		}
	}

	private function _orderState($id){
		$m = M('ServiceOrderDetail');
		$result = $m->where("service_order_id = {$id} AND (state <> -4 AND state <> 4 AND state <> -5 )")->select();
		if(empty($result)){
			$order = M('ServiceOrder');
			$order->where("id = {$id}")->save(array('state'=>2));
		}
	}
	private function _updateUser($base_id,$data){
		$user = M('PersonBase');
		$user->where("id = {$base_id}")->save($data);
	}
}
