<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: rohochan <rohochan@gmail.com> <http://blog.rohochan.com>
// +----------------------------------------------------------------------

namespace Company\Controller;
use OT\DataDictionary;

/**
 * 企业中心首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends HomeController {
	//系统首页
	public function index(){
		if (-1 != $this->mMemberStatus && (check_position($this->mMemberStatus,1) || check_position($this->mMemberStatus,2) || check_position($this->mMemberStatus,4))) {
			//获取服务订单进度数据与参保一览数据
			//根据报增报减截止日来判断当前月的订单
			$companyResult = array();
			//获取产品中的报增报减截止日期以便查找当期订单
			#$serviceOrder = D('ServiceOrder');
			$productOrder = D('ServiceProductOrder');
			$PersonInsuranceInfo=D('PersonInsuranceInfo');
			#getInsuranceListByCondition
			$PersonInsurance=D('PersonInsurance');
			/*$productOrderResult = $productOrder->where(array('user_id'=>$this->mCuid,'state'=>array('in','1,2'),'service_state'=>array('in','1,2')))->select();
			if ($productOrderResult) {
				
			}*/
		/*参保地一览*/
		$companyResult['insurance']['locationValue']=$productOrder->getAllEffectiveServiceProductOrderLocation($this->mCuid);
		//数据统计
		$where['user_id']=$this->mCuid;
		//报增
		$companyResult['insurance']['add_num'] = $PersonInsurance->getInsuranceCountByCondition($where,1);
		//报减
		$companyResult['insurance']['del_num'] = $PersonInsurance->getInsuranceCountByCondition($where,3);
		//在保
		$companyResult['insurance']['warranty_num'] = $PersonInsurance->getInsuranceCountByCondition($where,2);
		//代发工资
		$Sos=D('ServiceOrderSalary');
		$where=array('user_id'=>$this->mCuid);
		$companyResult['insurance']['payroll_credit_num'] = $Sos->getServiceOrderSalaryCountByCondition($where);

		}
		$PayOrder=D('PayOrder');
		$where=array('po.user_id'=>$this->mCuid,'po.type'=>array('neq',''));
		$orderlist=$PayOrder->getOrderListOfSearch($where);
		$companyResult['order']=$orderlist['data'];
		$this->assign('companyResult',$companyResult);

		$this->assign('cuid',$this->mCuid);
		$this->assign('cid',$this->mCid);
		//$this->assign('companyUser',$this->mCompanyUser);
		$this->assign('memberStatus',$this->mMemberStatus);
		$this->display();
	}
	
	//系统首页
	public function index2(){
		//dump($this->mCuid);
		//dump($this->mCompanyInfo);
		//dump($this->mCid);
		dump($this->mMemberStatus);
		if (-1 != $this->mMemberStatus && 0 != $this->mMemberStatus && (check_position($this->mMemberStatus,1) || check_position($this->mMemberStatus,2) || check_position($this->mMemberStatus,4))) {
			//获取服务订单进度数据与参保一览数据
			//根据账单生成日来判断当前月的订单
			$companyResult = array();
			//$createBillDate = date('Ym');
			//获取产品中的报增报减截止日期以便查找当期订单
			$serviceOrder = D('ServiceOrder');
			$productOrder = D('ProductOrder');
			$productOrderResult = $productOrder->relation(true)->where(array('company_id'=>$this->mCid,'state'=>array('in','1,2'),'service_state'=>array('in','1,2')))->select();
			if ($productOrderResult) {
				foreach ($productOrderResult as $key => $value) {
					//产品城市编号和产品城市名称
					$companyResult['insurance']['city'][] = array('location'=>$value['ServiceProduct']['location'],'value'=>showAreaName($value['ServiceProduct']['location']));
					
					//计算当前账单月
					//$createBillDate = date('d') > $value['create_bill_date'] ? date('Ym',strtotime('-1 month')):date('Ym');
					
					//根据报增报减截止日期计算订单月份
					$tempAddDelDate = strtotime(date('Y-m').'-'.$value['abort_add_del_date'].' 00:00:00');
					$dateInfo['abortAddDelDateValue'] = (time() > $tempAbortPaymentDate) ? date('Ym',strtotime('+1 Month',time())):date('Ym');
					
					//根据账单截止日期计算账单月份
					//账单月份状态  0当月 1次月
					$tempCreateBillDate = strtotime(date('Y-m').'-'.$value['create_bill_date'].' 00:00:00');
					$dateInfo['createBillDateValue'] = (1 == $value['bill_month_state']) ? date('Ym',strtotime('+1 Month',$tempCreateBillDate)):date('Ym',$tempCreateBillDate);
					
					//根据付款截止日期计算付款截止时间
					//付款截止月份状态  0当月 1次月
					$tempAbortPaymentDate = strtotime(date('Y-m').'-'.$value['abort_payment_date'].' 00:00:00');
					$dateInfo['abortPaymentDateValue'] = (1 == $value['payment_month_state'])?date('Y-m-d H:i:s',strtotime('+1 Month',$tempAbortPaymentDate)):date('Y-m-d H:i:s',$tempAbortPaymentDate);
					
					//dump($dateInfo['abortAddDelDateValue']);
					//$dateInfo['abortAddDelDateValue'] = 201603;
					$isSalary = $value['is_salary'];//是否代发工资产品
					
					//start:订单进度
					//$serviceOrderResult = $serviceOrder->field(true)->relation(true)->where(array('company_id'=>$this->mCid,'product_order_id'=>$value['id'],'order_date'=>$dateInfo['abortAddDelDateValue']))->select();
					//未完成订单
					$serviceOrderResult = $serviceOrder->field(true)->relation(true)->where(array('company_id'=>$this->mCid,'product_order_id'=>$value['id'],'state'=>array('in','0,1')))->select();
					if ($serviceOrderResult) {
						foreach ($serviceOrderResult as $k => $v) {
							//TODO:当归属某一服务商的上月订单已结束，自动显示下一订单的进度(去掉)
							$companyInfo = D('CompanyInfo')->field('company_name')->getByCompanyId($v['ServiceProduct']['company_id']);
							$serviceOrderResult[$k]['company_name'] = $companyInfo['company_name'];
							//$serviceOrderResult[$k]['is_salary'] = $isSalary;//是否代发工资订单
							$orderState = 0;
							$orderStateInfo[0] = array('value'=>$isSalary?'代发工资':'报增减','time'=>date('Y/m/d H:i',strtotime($v['create_time'])));
							$orderStateInfo[1] = array('value'=>'生成账单','time'=>'');
							$orderStateInfo[2] = array('value'=>'待支付','time'=>'');
							$orderStateInfo[3] = array('value'=>'办理中','time'=>'');
							$orderStateInfo[4] = array('value'=>'缴纳完成','time'=>'');
							//是否存在账单
							$serviceBill = D('ServiceBill');
							if ($serviceBillResult = $serviceBill->getByOrderId($v['id'])) {
								$orderState = 1;
								$orderStateInfo[1] = array('value'=>'生成账单','time'=>date('Y/m/d H:i',strtotime($serviceBillResult['create_time'])));
								if (1 == $serviceBillResult['state']) {
									$orderState = 3;
									$orderStateInfo[2] = array('value'=>'支付完成','time'=>date('Y/m/d H:i',strtotime($serviceBillResult['pay_time'])));
									$orderStateInfo[3] = array('value'=>'办理中','time'=>date('Y/m/d H:i',strtotime($serviceBillResult['pay_time'])));
								}
								if (2 == $v['state']) {
									$orderState = 4;
									$orderStateInfo[4] = array('value'=>'缴纳完成','time'=>date('Y/m/d H:i',strtotime($v['order_complete_date'])));
								}
							}
							
							$serviceOrderResult[$k]['order_state'] = $orderState;//订单状态
							$serviceOrderResult[$k]['order_state_info'] = $orderStateInfo;//订单状态
							$companyResult['order'][] = $serviceOrderResult[$k];
						}
					}//end:订单进度
				}
			}
			
			$insuranceResult = $serviceOrder->field('max(warranty_num) as warranty_num ,sum(add_num)as add_num, sum(del_num) as del_num,sum(payroll_credit_num) as payroll_credit_num')->where(array('company_id'=>$this->mCid,'order_date'=>$createBillDate))->find();
			//echo $serviceOrder->_sql();
			$companyResult['insurance']['data'] = $insuranceResult;
			dump($companyResult);
			/*$serviceOrder = D('ServiceOrder');
			$serviceSalary =  D('ServiceSalary');
			$companyResult['insurance_review']['warranty_num'] = 0;
			$companyResult['insurance_review']['add_num'] = 0;
			$companyResult['insurance_review']['del_num'] = 0;
			$companyResult['insurance_review']['payroll_credit_num'] = 0;
			$serviceOrderResult = $serviceOrder->field(true)->relation(true)->where(array('company_id'=>$this->mCid,'order_date'=>$orderDate))->select();
			if ($serviceOrderResult) {
				foreach ($serviceOrderResult as $key => $value) {
					$companyResult['insurance_review']['warranty_num'] = $value['warranty_num'];
					$companyResult['insurance_review']['add_num'] += $value['add_num'];
					$companyResult['insurance_review']['del_num'] += $value['del_num'];
					
					$serviceSalaryCount = $serviceSalary->field(true)->where(array('order_id'=>$value['id'],'date'=>$orderDate))->count('pase_base_id');
					if ($serviceSalaryCount) {
							$companyResult['insurance_review']['payroll_credit_num'] += $serviceSalaryCount;
					}
				}
			}*/
			$this->assign('companyResult',$companyResult);
		}
		$this->assign('cuid',$this->mCuid);
		$this->assign('companyUser',$this->mCompanyUser);
		$this->assign('cid',$this->mCid);
		$this->assign('memberStatus',$this->mMemberStatus);
		$this->display();
	}

}