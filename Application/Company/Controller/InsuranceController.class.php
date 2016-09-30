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
use Service\Model\ServiceOrderModel;
use Common\Model\Calculate;
/**
 * 企业中心参保状态控制器
 * 主要获取所有的参保状态数据以及进行报增报减操作
 */
class InsuranceController extends HomeController {
	
	/**
	 * insuranceList function
	 * 参保列表
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function insuranceList(){
		$type = I('param.type');
		$location = I('param.location');
		$personName = I('param.personName');
		$cardNum = I('param.cardNum');
		$condition = array();
		//$condition['company_id'] = $this->mCid;
		$condition['user_id'] = $this->mCuid;
		$location && $condition['location'] = $location;
		$personName && $condition['person_name'] = $personName;
		$cardNum && $condition['card_num'] = $cardNum;
		
		//dump($condition);
		
		if ('' !== $type) {
			/*$personInsuranceInfo = D('PersonInsuranceInfo');
			$personInsuranceInfoResult = $personInsuranceInfo->getInsuranceListByCondition($condition,$type);
			
			if (false !== $personInsuranceInfoResult) {
				dump($personInsuranceInfoResult);
				$this->assign('result',$personInsuranceInfoResult['data']);
				$this->assign('page',$personInsuranceInfoResult['page']);
			}else {
				$this->error($personInsuranceInfo->getError());
			}*/
			$personInsurance = D('PersonInsurance');
			$personInsuranceResult = $personInsurance->getInsuranceListByCondition($condition,$type,10);
			if (false !== $personInsuranceResult) {
				//dump($personInsuranceResult);
				$serviceProductOrder = D('ServiceProductOrder');
				$serviceProductOrderResult = $serviceProductOrder->getAllEffectiveServiceProductOrderLocation($this->mCuid);
				$this->assign('warrantyLocation',$serviceProductOrderResult);
				$this->assign('result',$personInsuranceResult['data']);
				$this->assign('page',$personInsuranceResult['page']);
				$this->display();
			}else {
				$this->error($personInsurance->getError());
			}
		}else {
			$this->error('非法参数！');
		}
	}
	
	/**
	 * insurancePersonList function
	 * 参保人员列表
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function insurancePersonList(){
		$type = I('param.type','-1');
		$location = I('param.location');
		$personName = I('param.personName');
		$cardNum = I('param.cardNum');
		$condition = array();
		$condition['user_id'] = $this->mCuid;
		$location && $condition['location'] = $location;
		$personName && $condition['person_name'] = $personName;
		$cardNum && $condition['card_num'] = $cardNum;
		
		//dump($condition);
		//dump($type);
		
		/*$personInsuranceInfo = D('PersonInsuranceInfo');
		$personInsuranceInfoResult = $personInsuranceInfo->getInsuranceListByCondition($condition,$type);
		
		if (false !== $personInsuranceInfoResult) {
			dump($personInsuranceInfoResult);
			$this->assign('result',$personInsuranceInfoResult['data']);
			$this->assign('page',$personInsuranceInfoResult['page']);
		}else {
			$this->error($personInsuranceInfo->getError());
		}*/
		$personInsurance = D('PersonInsurance');
		$personInsuranceResult = $personInsurance->getInsuranceListByCondition($condition,$type,10);
		if (false !== $personInsuranceResult) {
			//dump($personInsuranceResult);
			$serviceProductOrder = D('ServiceProductOrder');
			$serviceProductOrderResult = $serviceProductOrder->getAllEffectiveServiceProductOrderLocation($this->mCuid);
			$this->assign('warrantyLocation',$serviceProductOrderResult);
			$this->assign('result',$personInsuranceResult['data']);
			$this->assign('page',$personInsuranceResult['page']);
			$this->display();
		}else {
			$this->error($personInsurance->getError());
		}
	}
	
	/**
	 * insuranceOrderList function
	 * 参保列表
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function insuranceOrderList(){
		$type = I('param.type');
		$location = I('param.location');
		$personName = I('param.personName');
		$cardNum = I('param.cardNum');
		$productId = I('param.productId');
		$companyId = I('param.companyId');
		$adminId = I('param.adminId');
		$state = I('param.state');
		$handleMonth = I('param.handleMonth');
		//$startTime = I('param.startTime');
		//$endTime = I('param.endTime');
		
		
		$condition = array();
		$condition['user_id'] = $this->mCuid;
		$location && $condition['location'] = $location;
		$personName && $condition['person_name'] = $personName;
		$cardNum && $condition['card_num'] = $cardNum;
		$productId && $condition['product_id'] = $productId;
		$companyId && $condition['company_id'] = $companyId;
		$adminId && $condition['admin_id'] = $adminId;
		$state !== '' && $condition['state'] = $state;
		$handleMonth && $condition['handle_month'] = string_to_number($handleMonth);
		//$startTime && $condition['start_time'] = $startTime;
		//$endTime && $condition['end_time'] = $endTime;
		
		//dump($condition);
		
		//$personInsurance = D('PersonInsurance');
		//$personInsuranceResult = $personInsurance->getInsuranceOrderListByCondition($condition,$type,10);
		$personInsuranceInfo = D('PersonInsuranceInfo');
		if (in_array($type,[0,1])) {
			$personInsuranceInfoResult = $personInsuranceInfo->getInsuranceOrderListByCondition($condition,$type,10);
		}else {
			$personInsuranceInfoResult = $personInsuranceInfo->getInsuranceDetailListByCondition($condition,$type,10);
		}
		if (false !== $personInsuranceInfoResult) {
			//dump($personInsuranceInfoResult);
			$serviceProductOrder = D('ServiceProductOrder');
			$serviceProductOrderResult = $serviceProductOrder->getAllEffectiveServiceProductOrder($this->mCuid);
			$serviceProviderResult = array();
			if ($serviceProductOrderResult) {
				foreach ($serviceProductOrderResult as $key => $value) {
					$serviceProviderResult[$value['company_id']] = $value['company_name'];
				}
			}
			$userServiceProvider = D('UserServiceProvider');
			$userServiceProviderResult = $userServiceProvider->getServiceAdminList($this->mCuid);
			
			$this->assign('serviceProductOrderResult',$serviceProductOrderResult);
			$this->assign('serviceProviderResult',$serviceProviderResult);
			$this->assign('userServiceProviderResult',$userServiceProviderResult);
			$this->assign('result',$personInsuranceInfoResult['data']);
			$this->assign('page',$personInsuranceInfoResult['page']);
			$this->assign('count',$personInsuranceInfoResult['count']);
			$this->display();
		}else {
			$this->error($personInsuranceInfo->getError());
		}
	}
	
	/**
	 * insuranceDetail function
	 * 参保详情
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function insuranceDetailOld(){
		$baseId = I('param.baseId/d');
		//$payDate = I('param.payDate/d');
		$condition = array();
		//$condition['company_id'] = $this->mCid;
		$condition['user_id'] = $this->mCuid;
		$condition['base_id'] = $baseId;
		//$condition['pay_date'] = 201606;
		
		dump($condition);
		
		if (!empty($baseId)) {
			/*$personInsuranceInfo = D('PersonInsuranceInfo');
			$personInsuranceInfoResult = $personInsuranceInfo->getInsuranceDetailByCondition($condition);
			if (false !== $personInsuranceInfoResult) {
				dump($personInsuranceInfoResult);
				$this->assign('result',$personInsuranceInfoResult['data']);
				$this->assign('page',$personInsuranceInfoResult['page']);
			}else {
				$this->error($personInsuranceInfo->getError());
			}*/
			
			$personInsurance = D('PersonInsurance');
			$personInsuranceResult = $personInsurance->getInsuranceDetailByCondition($condition);
			if (false !== $personInsuranceResult) {
				dump($personInsuranceResult);
				$this->assign('result',$personInsuranceResult['data']);
				$this->assign('page',$personInsuranceResult['page']);
			}else {
				$this->error($personInsurance->getError());
			}
		}else {
			$this->error('非法参数！');
		}
	}
	
	/**
	 * insuranceDetail function
	 * 参保详情
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function insuranceDetail(){
		$baseId = I('param.baseId/d');
		$condition = array();
		$condition['user_id'] = $this->mCuid;
		$condition['base_id'] = $baseId;
		if (!empty($baseId)) {
			$personInsurance = D('PersonInsurance');
			$personInsuranceResult = $personInsurance->getPersonInsuranceByCondition($condition);
			//$condition['pay_date'] = 201608;
			//$personInsuranceResult = $personInsurance->getInsurancePayDateDetailByCondition($condition);
			if (false !== $personInsuranceResult) {
				$personInsuranceResult['propiPaymentInfoValue'] = json_decode($personInsuranceResult['propi_payment_info'],true);
				//获取身份证图片
				$personInsuranceResult['idCardImg'] = get_idCardImg_by_baseId($personInsuranceResult['base_id']);
				$this->assign('result',$personInsuranceResult);
				$this->display();
			}else {
				$this->error($personInsurance->getError());
			}
		}else {
			$this->error('非法参数！');
		}
	}
	
	/**
	 * insurancePayDateDetail function
	 * 根据缴纳月份获取参保详情
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function insurancePayDateDetail(){
		if (IS_POST) {
			$baseId = I('param.baseId/d');
			$payDate = I('param.payDate/d');
			if (!empty($baseId) && !empty($payDate)) {
				$condition = array();
				$condition['user_id'] = $this->mCuid;
				$condition['base_id'] = $baseId;
				$condition['pay_date'] = $payDate;
				$personInsuranceInfo = D('PersonInsuranceInfo');
				$personInsuranceInfoResult = $personInsuranceInfo->getInsurancePayDateDetailByCondition($condition);
				if (false !== $personInsuranceInfoResult) {
					//dump($personInsuranceInfoResult);
					$this->ajaxReturn(array('status'=>1,'result'=>$personInsuranceInfoResult));
				}else {
					$this->ajaxReturn(array('status'=>0,'info'=>$personInsuranceInfo->getError()));
					$this->error($personInsuranceInfo->getError());
				}
			}else {
				$this->error('非法参数！');
			}
		}else {
			$this->error('非法操作！');
		}
	}
	
	/**
	 * insuranceInfoDetail function
	 * 参保信息详情
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function insuranceInfoDetail(){
		if (IS_POST) {
			//构造测试数据start
			$data = array();
			$data['baseId'] = '147';
			$data['personName'] = '和介辉';
			$data['cardNum'] = '340522197602272018';
			$data['baseId'] = '149';
			$data['personName'] = '熊自鸣';
			$data['cardNum'] = '150302199103145193';
			$data['mobile'] = '15920288888';
			$data['residenceLocation'] = '14010000';
			$data['residenceType'] = '1';
			$data['handleMonth'];
			
			$data['productId'] = '24';
			$data['location'] = '14020100';
			
			$data['isBuySoc'] = '1';
			$data['socPiiId'] = '396';
			$data['socRuleId'] = '89';
			//$data['socPayYear'] = '2016';
			//$data['socPayMonth'] = '6';
			$data['socPayDate'] = '2016-06';
			$data['socAmount'] = '2300';
			$data['socCardNum'] = 'zby';
			$data['isBuyPro'] = '0';
			$data['proPiiId'] = '397';
			$data['proRuleId'] = '67';
			//$data['proPayYear'] = '2016';
			//$data['proPayMonth'] = '5';
			$data['proPayDate'] = '2016-05';
			$data['proAmount'] = '2500';
			$data['proCardNum'] = 'zby';
			$data['proPersonScale'] = '11%';
			$data['proCompanyScale'] = '11%';
			//构造测试数据end
			
			$data = I('param.');
			//$data['socCardNum'] = 'zby';
			//$data['proCardNum'] = 'zby';
			//if ($data['payDate']) {
			//	$data['socPayDate'] = $data['payDate'];
			//	$data['proPayDate'] = $data['payDate'];
			//}
			$data['proPayDate'] = empty($data['proPayDate'])?$data['socPayDate']:$data['proPayDate'];
			
			$data['templateLocation'] = $data['location'];
			$data['location'] = ($data['location']/1000<<0)*1000;
			
			$personBaseData = array();
			$personBaseData['id'] = $data['baseId'];
			$personBaseData['user_id'] = $this->mCuid;
			$personBaseData['person_name'] = $data['personName'];
			$personBaseData['card_num'] = $data['cardNum'];
			$personBaseData['mobile'] = $data['mobile'];
			$personBaseData['residence_location'] = $data['residenceLocation'];
			$personBaseData['residence_type'] = $data['residenceType'];
			$personBase = D('PersonBase');
			$personBase->startTrans();
			$personBaseResult = $personBase->savePersonBase($personBaseData);
			if ($personBaseResult) {
				$personBaseId = $personBaseResult;
				$personInsurance = D('PersonInsurance');
				$personInsuranceResult = $personInsurance->getInsuranceStatus($this->mCuid,$personBaseId);
				if ($personInsuranceResult) {
					if ($personInsuranceResult['editIncrease']) {
						
						//计算订单月份
						$template = D('Template');
						//$templateResult = $template->getTemplateByCondition(array('location'=>$data['location'],'state'=>1));
						$templateResult = $template->getTemplateByCondition(array('location'=>$data['templateLocation'],'state'=>1));
						if ($templateResult) {
							$templateResult['deadline'] = array(1=>$templateResult['soc_deadline'],2=>$templateResult['pro_deadline']);
							$templateResult['payment_type'] = array(1=>$templateResult['soc_payment_type'],2=>$templateResult['pro_payment_type']);
							$templateResult['payment_month'] = array(1=>$templateResult['soc_payment_month'],2=>$templateResult['pro_payment_month']);
							
							//$orderDate[1] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($templateResult['deadline'][1],2,'0',STR_PAD_LEFT)))))?date('Ym',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Ym',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
							//$orderDate[2] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($templateResult['deadline'][2],2,'0',STR_PAD_LEFT)))))?date('Ym',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Ym',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
							$orderDate[1] = get_handle_month($templateResult['deadline'][1]);
							$orderDate[2] = get_handle_month($templateResult['deadline'][2]);
							$orderDateStr[1] = substr_replace($orderDate[1],'-',4,0);
							$orderDateStr[2] = substr_replace($orderDate[2],'-',4,0);
							
							$personInsuranceInfo = D('PersonInsuranceInfo');
							$personInsuranceInfoOriginResult = $personInsuranceInfo->getPersonInsuranceInfoByHandleMonth(array('user_id'=>$this->mCuid,'base_id'=>$personBaseResult,'handle_month'=>$data['handleMonth']));
							$personInsuranceInfoData = array();
							$personInsuranceInfoData['user_id'] = $this->mCuid;
							$personInsuranceInfoData['base_id'] = $personBaseId;
							$personInsuranceInfoData['product_id'] = $data['productId'];
							$personInsuranceInfoData['location'] = $data['location'];
							$personInsuranceInfoData['template_location'] = $data['templateLocation'];
							//$personInsuranceInfoData['end_month'] = 0;
							//$personInsuranceInfoData['audit'] = 0;//未审核
							//$personInsuranceInfoData['state'] = 1;//报增
							//$personInsuranceInfoData['create_time'] = date('Y-m-d H:i:s');
							$personInsuranceInfoData['modify_time'] = date('Y-m-d H:i:s');
							
							$personInsuranceInfoArray = array();
							if (1 == $data['isBuySoc']) {
								$personInsuranceInfoResult = $personInsuranceInfo->field('id,state,operate_state')->getById($data['socPiiId']);
								if (in_array($personInsuranceInfoResult['operate_state'],array(0,-1,-9))) {
									$personInsuranceInfoArray[1] = $personInsuranceInfoData;
									$personInsuranceInfoArray[1]['id'] = $data['socPiiId'];
									//$personInsuranceInfoArray[1]['pay_order_id'] = 0;
									$personInsuranceInfoArray[1]['rule_id'] = $data['socRuleId'];
									$personInsuranceInfoArray[1]['start_month'] = string_to_number($data['socPayDate']);
									//$personInsuranceInfoArray[1]['handle_month'] = $orderDate[1];
									$personInsuranceInfoArray[1]['handle_month'] = $data['handleMonth'];
									$personInsuranceInfoArray[1]['amount'] = $data['socAmount'];
									$personInsuranceInfoArray[1]['payment_type'] = 1;
									$personInsuranceInfoArray[1]['payment_info'] = json_encode(array('cardno'=>$data['socCardNum']));
									$personInsuranceInfoArray[1]['state'] = (0 == $personInsuranceInfoResult['state']?1:$personInsuranceInfoResult['state']);
									$personInsuranceInfoArray[1]['operate_state'] = 0;//未审核
								}
							}
							if (1 == $data['isBuyPro']) {
								$personInsuranceInfoResult = $personInsuranceInfo->field('id,state,operate_state')->getById($data['proPiiId']);
								if (in_array($personInsuranceInfoResult['operate_state'],array(0,-1,-9))) {
									$personInsuranceInfoArray[2] = $personInsuranceInfoData;
									$personInsuranceInfoArray[2]['id'] = $data['proPiiId'];
									//$personInsuranceInfoArray[2]['pay_order_id'] = 0;
									$personInsuranceInfoArray[2]['rule_id'] = $data['proRuleId'];
									$personInsuranceInfoArray[2]['start_month'] = string_to_number($data['proPayDate']);
									//$personInsuranceInfoArray[2]['handle_month'] = $orderDate[2];
									$personInsuranceInfoArray[2]['handle_month'] = $data['handleMonth'];
									$personInsuranceInfoArray[2]['amount'] = $data['proAmount'];
									$personInsuranceInfoArray[2]['payment_type'] = 2;
									$personInsuranceInfoArray[2]['payment_info'] = json_encode(array('companyScale'=>trim($data['proCompanyScale'],'%').'%','personScale'=>trim($data['proPersonScale'],'%').'%','cardno'=>$data['proCardNum']));
									$personInsuranceInfoArray[2]['state'] = (0 == $personInsuranceInfoResult['state']?1:$personInsuranceInfoResult['state']);
									$personInsuranceInfoArray[2]['operate_state'] = 0;//未审核
								}
							}else if(0 == $data['isBuyPro'] && 0 != $personInsuranceInfoOriginResult[2]['state']) {
								$personInsuranceInfoArray[2]['id'] = $data['proPiiId'];
								//$personInsuranceInfoArray[2]['pay_order_id'] = 0;
								$personInsuranceInfoArray[2]['pay_date'] = '';
								$personInsuranceInfoArray[2]['operate_state'] = -9;//撤销
								$personInsuranceInfoResult = $personInsuranceInfo->getLastPersonInsuranceInfo(array('id'=>$data['proPiiId'],'user_id'=>$this->mCuid,'base_id'=>$personBaseId,'payment_type'=>2));
								//dump($personInsuranceInfoResult);
								if ($personInsuranceInfoResult) {
									$personInsuranceInfoArray[2]['state'] = (1 == $personInsuranceInfoResult['state']?0:$personInsuranceInfoResult['state']);
								}else {
									$personInsuranceInfoArray[2]['state'] = 0;//未参保
								}
							}
							//dump($personInsuranceInfoArray);
							if ($personInsuranceInfoArray) {
								$personInsuranceInfoResult = array();
								$templateRule = D('TemplateRule');
								foreach ($personInsuranceInfoArray as $key => $value) {
									if (1 == $value['state']) {
										//报增状态
										//$personInsuranceInfoResult[$key] = $personInsuranceInfo->savePersonInsurance($value);
										$personInsuranceInfoResult['rule'][$key] = $templateRule->getById($value['rule_id']);
										if ($personInsuranceInfoResult['rule'][$key]) {
											$personInsuranceInfoResult['id'][$key] = $value['id'];
											$tempPersonInsuranceInfoResult = $personInsuranceInfo->save($value);
											$personInsuranceInfoResult['successCount'] += false !== $tempPersonInsuranceInfoResult?1:0;
										}else {
											$personBase->rollback();
											$this->ajaxReturn(array('status'=>0,'info'=>'规则参数错误！'));
										}
									}else {
										$personInsuranceInfoResult['id'][$key] = $value['id'];
										$tempPersonInsuranceInfoResult = $personInsuranceInfo->save($value);
										$personInsuranceInfoResult['successCount'] += false !== $tempPersonInsuranceInfoResult?1:0;
									}
								}
								if ($personInsuranceInfoResult['successCount'] == count($personInsuranceInfoArray)) {
									//计算订单月份
									$serviceProductOrder = D('ServiceProductOrder');
									$serviceProductOrderResult = $serviceProductOrder->getEffectiveServiceProductOrderByProductId($this->mCuid,$data['productId']);
									if ($serviceProductOrderResult) {
											//计算缴纳月份，补缴月份
											$serviceInsuranceDetailBaseData = array();
											$serviceInsuranceDetailBaseData['pay_order_id'] = 0;//无支付订单
											$serviceInsuranceDetailBaseData['type'] = 1;//报增
											$serviceInsuranceDetailBaseData['state'] = 0;//待审核
											$serviceInsuranceDetailBaseData['create_time'] = date('Y-m-d H:i:s');
											$serviceInsuranceDetailBaseData['modify_time'] = $serviceInsuranceDetailBaseData['create_time'];
											$warrantyLocation = D('WarrantyLocation');
											//计算服务费
											$warrantyLocationResult = $warrantyLocation->getWarrantyLocationByCondition(array('service_product_order_id'=>$serviceProductOrderResult['id'],'location'=>$data['location']));
											$servicePrice = array();
											$servicePrice[1] = $warrantyLocationResult['soc_service_price'];
											$servicePrice[2] = $warrantyLocationResult['pro_service_price'];
											$calculate = new \Common\Model\Calculate();
											foreach ($personInsuranceInfoResult['id'] as $key => $value) {
												//勾选报增
												if (1 == $personInsuranceInfoArray[$key]['state']) {
													//报增状态
													$endMonth = 1 == $templateResult['payment_type'][$key]?$orderDate[$key]:date('Ym',strtotime('+1 month', strtotime($orderDateStr[$key])));//1缴当月 2缴次月
													$monthNum = get_different_by_month($personInsuranceInfoArray[$key]['start_month'],$endMonth);
													$replenishMonthNum = get_different_by_month($personInsuranceInfoArray[$key]['start_month'],$orderDate[$key])-1;
													if ($monthNum > 0) {
														//计算缴纳费用
														$productTemplateRuleResult = $templateRule->getById($personInsuranceInfoArray[$key]['rule_id']);
														$disRuleResult = $templateRule->getTemplateRuleByCondition(array('template_id'=>$templateResult['id'],'company_id'=>$productTemplateRuleResult['company_id'],'type'=>3,'state'=>1));
														//$servicePrice = 1 == $key?$warrantyLocationResult['ss_service_price']:0;
														$json = json_decode($personInsuranceInfoArray[$key]['payment_info'],true);
														$json['amount'] = $personInsuranceInfoArray[$key]['amount'];
														$json['month'] = 1;
														$json = json_encode($json);
														//最大补缴月份
														if ($replenishMonthNum <= $templateResult['payment_month'][$key]) {
															//缴纳年月数组
															$paymentMonthArray = array();
															for ($i=0; $i < $monthNum; $i++) {
																$paymentMonthArray[] = date('Ym',strtotime("+{$i} month",strtotime(substr_replace($personInsuranceInfoArray[$key]['start_month'],'-',4,0))));
															}
															//添加参保订单表数据
															$serviceInsuranceDetail = D('ServiceInsuranceDetail');
															
															//$serviceInsuranceDetailDeleteResult = $serviceInsuranceDetail->where(array('insurance_info_id'=>$value,'state'=>0))->delete();
															$serviceInsuranceDetailDeleteResult = $serviceInsuranceDetail->where(array('insurance_info_id'=>$value))->delete();
															//更新数据
															$personInsuranceInfoUpdateResult = $personInsuranceInfo->where(array('id'=>$value))->save(array('pay_date'=>implode(',',$paymentMonthArray)));
															
															$serviceInsuranceDetailResult = array();
															$serviceInsuranceDetailResult['monthNum'] += $monthNum;
															for ($i=0; $i < $monthNum; $i++) {
																$payDate = date('Ym',strtotime("+{$i} month",strtotime(substr_replace($personInsuranceInfoArray[$key]['start_month'],'-',4,0))));
																$replenish = $payDate < $endMonth?1:0;//是否补缴
																$calculateResult = json_decode($calculate->detail($productTemplateRuleResult['rule'], $json, $key, $disRuleResult['rule'] ,$replenish ),true);
																if (0 == $calculateResult['state']) {
																	//$price = $calculateResult['data']['company']+$calculateResult['data']['person']+$calculateResult['data']['pro_cost'];
																	$price = $calculateResult['data']['company']+$calculateResult['data']['person'];
																	//$calculateResult['data']['service_price'] = $servicePrice[$key];
																	$serviceInsuranceDetailData = $serviceInsuranceDetailBaseData;
																	$serviceInsuranceDetailData['payment_type'] = $key;//参保类型
																	$serviceInsuranceDetailData['insurance_info_id'] = $value;
																	//$serviceInsuranceDetailData['service_order_insurance_id'] = $serviceOrderInsuranceId;
																	$serviceInsuranceDetailData['price'] = $price;
																	$serviceInsuranceDetailData['service_price'] = $servicePrice[$key];
																	$serviceInsuranceDetailData['amount'] = $personInsuranceInfoArray[$key]['amount'];
																	$serviceInsuranceDetailData['pay_date'] = $payDate;
																	$serviceInsuranceDetailData['handle_month'] = $personInsuranceInfoArray[$key]['handle_month'];
																	$serviceInsuranceDetailData['replenish'] = $replenish;
																	$serviceInsuranceDetailData['rule_id'] = $personInsuranceInfoArray[$key]['rule_id'];
																	//$serviceInsuranceDetailData['rule_detail'] = $productTemplateRuleResult['rule'];
																	$serviceInsuranceDetailData['payment_info'] = $personInsuranceInfoArray[$key]['payment_info'];
																	$serviceInsuranceDetailData['insurance_detail'] = json_encode($calculateResult['data'],JSON_UNESCAPED_UNICODE);//计算结果
																	$serviceInsuranceDetailData['current_detail'] = $serviceInsuranceDetailData['insurance_detail'];
																	$serviceInsuranceDetailResult['id'][$key][$serviceInsuranceDetailData['pay_date']] = $serviceInsuranceDetail->add($serviceInsuranceDetailData);
																	$serviceInsuranceDetailResult['successCount'] += $serviceInsuranceDetailResult['id'][$key][$serviceInsuranceDetailData['pay_date']]?1:0;
																}else {
																	$personBase->rollback();
																	$this->ajaxReturn(array('status'=>0,'info'=>'参保数据计算错误！'));
																}
															}
														}else {
															$personBase->rollback();
															$this->ajaxReturn(array('status'=>0,'info'=>'超出最大补缴月份！'));
														}
													}else {
														$personBase->rollback();
														$this->ajaxReturn(array('status'=>0,'info'=>'起缴月份错误！'));
													}
												}else {
													//未勾选购买
													/*$serviceOrderInsurance = D('ServiceOrderInsurance');
													$condition = array();
													$condition['insurance_id'] = $value;
													$condition['type'] = 1;
													$condition['state'] = 0;
													$serviceOrderInsuranceResult = $serviceOrderInsurance->getServiceOrderInsuranceByCondition($condition);
													if ($serviceOrderInsuranceResult) {
														//有老订单则撤销购买
														$serviceOrderInsuranceData = array();
														$serviceOrderInsuranceData['state'] = -9;
														$serviceOrderInsuranceData['modify_time'] = date('Y-m-d H:i:s');
														$serviceOrderInsuranceSaveResult = $serviceOrderInsurance->where(array('id'=>$serviceOrderInsuranceResult['id']))->save($serviceOrderInsuranceData);
														if (false !== $serviceOrderInsuranceSaveResult) {*/
															$serviceInsuranceDetail = D('ServiceInsuranceDetail');
															$condition = array();
															//$condition['service_order_insurance_id'] = $serviceOrderInsuranceResult['id'];
															$condition['insurance_info_id'] = $value;
															$condition['state'] = ['in',[0,-1]];
															$serviceInsuranceDetailData = array();
															$serviceInsuranceDetailData['pay_order_id'] = 0;//无支付订单
															$serviceInsuranceDetailData['state'] = -9;
															$serviceInsuranceDetailData['modify_time'] = date('Y-m-d H:i:s');
															$serviceInsuranceDetailSaveResult = $serviceInsuranceDetail->where($condition)->save($serviceInsuranceDetailData);
															if (false === $serviceInsuranceDetailSaveResult) {
																$personBase->rollback();
																$this->ajaxReturn(array('status'=>0,'info'=>'系统内部错误！'));
															}
														/*}else {
															$personBase->rollback();
															$this->ajaxReturn(array('status'=>0,'info'=>'系统内部错误！'));
														}
													}*/
												}
											}
											
											//if ($serviceInsuranceDetailResult && $serviceInsuranceDetailResult['successCount'] == $serviceInsuranceDetailResult['monthNum']) {
											if ($serviceInsuranceDetailResult['successCount'] == $serviceInsuranceDetailResult['monthNum']) {
												$personBase->commit();
												//保存身份证
												$path = mkFilePath($personBaseId,'./Uploads/Person/','IDCard');
												//保存身份证正面照片
												if ($idCardFrontFile = I('idCardFrontFile','')) {
													$idCardFrontFile = reset(explode('?',$idCardFrontFile));
													if ('/Application/Company/Assets/v2/images/idcard1.png' != $idCardFrontFile) {
														$idCardFrontFileResult = move('.'.$idCardFrontFile,$path.'idCardFront.jpg');
													}
												}
												//保存身份证反面照片
												if ($idCardBackFile = I('idCardBackFile','')) {
													$idCardBackFile = reset(explode('?',$idCardBackFile));
													if ('/Application/Company/Assets/v2/images/idcard2.png' != $idCardBackFile) {
														$idCardBackFileResult = move('.'.$idCardBackFile,$path.'idCardBack.jpg');
													}
												}
												$this->ajaxReturn(array('status'=>1,'info'=>'编辑成功！'));
											}else {
												$personBase->rollback();
												$this->ajaxReturn(array('status'=>0,'info'=>'编辑失败！'));
											}
									}else {
										$personBase->rollback();
										$this->ajaxReturn(array('status'=>0,'info'=>'产品订单错误！'));
									}
								}else {
									$personBase->rollback();
									$this->ajaxReturn(array('status'=>0,'info'=>'系统内部错误！'));
								}
							}else {
								$personBase->rollback();
								$this->ajaxReturn(array('status'=>0,'info'=>'请选择要参保的项目！'));
							}
						}else {
							$personBase->rollback();
							$this->ajaxReturn(array('status'=>0,'info'=>'系统缴费模板错误！'));
						}
					}else {
						$personBase->rollback();
						$this->ajaxReturn(array('status'=>0,'info'=>'参保状态错误！'));
					}
				}else {
					$personBase->rollback();
					$this->ajaxReturn(array('status'=>0,'info'=>'系统内部错误！'));
				}
			}else {
				$personBase->rollback();
				$this->ajaxReturn(array('status'=>0,'info'=>$personBase->getError()));
				//$this->error($personBase->getError());
			}
		}else {
			$baseId = I('get.baseId/d');
			$handleMonth = I('get.handleMonth/d');
			if ($baseId >0 && $handleMonth>0) {
				//获取个人信息
				$personBase = D('PersonBase');
				$personBaseResult = $personBase->field(true)->getById($baseId);
				$personBaseResult['readonly'] = 1 == $personBaseResult['audit']?' readonly="readonly" ':'';
				$personBaseResult['disabled'] = 1 == $personBaseResult['audit']?' disabled="disabled" ':'';
				//获取身份证图片
				$personBaseResult['idCardImg'] = get_idCardImg_by_baseId($baseId);
				
				//获取参保信息
				$personInsuranceInfo = D('personInsuranceInfo');
				//$personInsuranceInfoResult = $personInsuranceInfo->getServiceOrderDetailByCondition(array('user_id'=>$this->mCuid,'base_id'=>$baseId),array('in','0,1'),false);
				$personInsuranceInfoResult = $personInsuranceInfo->getPersonInsuranceInfoByHandleMonth(array('user_id'=>$this->mCuid,'base_id'=>$baseId,'handle_month'=>$handleMonth));
				
				if ($personInsuranceInfoResult) {
					$personBaseResult['editable'] = ($personInsuranceInfoResult[1]['operate_state'] <= 1 && $personInsuranceInfoResult[2]['operate_state'] <= 1);
					$personBaseResult['isPaid'] = ($personInsuranceInfoResult[1]['operate_state'] >= 2 || $personInsuranceInfoResult[2]['operate_state'] >= 2);
					$personBaseResult['whetherToOperate'] = $personInsuranceInfoResult[1]['whetherToOperate'] && $personInsuranceInfoResult[2]['whetherToOperate'];
					$productId = $personInsuranceInfoResult[1]['product_id']?:$personInsuranceInfoResult[2]['product_id'];
					$location = $personInsuranceInfoResult[1]['location']?:$personInsuranceInfoResult[2]['location'];
					$templateLocation = $personInsuranceInfoResult[1]['template_location']?:$personInsuranceInfoResult[2]['template_location'];
					
					$serviceProductOrder = D('ServiceProductOrder');
					$serviceProduct = D('ServiceProduct');
					$templateRule = D('TemplateRule');
					$template = D('Template');
					
					//获取购买的产品信息
					$serviceProductResult = $serviceProduct->alias('sp')->field('sp.company_id,sp.name,ci.company_name')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id = sp.company_id')->where(['sp.id'=>$productId])->find();
					
					//获取购买的产品订单信息
					$serviceProductOrderResult['list'] = $serviceProductOrder->getEffectiveServiceProductOrder($this->mCuid);
					$serviceProductOrderResult['condition'] = array('product_id'=>$productId,'product_name'=>$serviceProductResult['name'],'company_name'=>$serviceProductResult['company_name']);
					
					$serviceProductOrderLocationResult['list'] = $serviceProductOrder->getEffectiveServiceProductOrderLocationByProductId($this->mCuid,$productId);
					$serviceProductOrderLocationResult['condition'] = array('location'=>$templateLocation,'locationValue'=>get_location_value($templateLocation));
					
					foreach ($personInsuranceInfoResult as $key => $value) {
						//$personInsuranceInfoResult[$key]['serviceProductOrderResult'] = $serviceProductOrder->getEffectiveServiceProductOrderLocationByProductId($this->mCuid,$value['product_id']);
						$personInsuranceInfoResult[$key]['paymentInfoValue'] = json_decode($value['payment_info'],true);
						$personInsuranceInfoResult[$key]['templateRuleResult'] = $templateRule->getTemplateRuleByCondition(array('id'=>$value['rule_id']));
						//$personInsuranceInfoResult[$key]['templateResult'] = $template->getTemplateByCondition(array('id'=>$personInsuranceInfoResult[$key]['templateRuleResult']['template_id']));
						
						$templateClassifyResult[$key]['list'] = $this->_getTemplateClassify($templateLocation);
						$templateClassifyResult[$key]['condition'] = array('classify_mixed'=>array_filter(explode('|',$personInsuranceInfoResult[$key]['templateRuleResult']['classify_mixed'])));
						
						$templateRuleResult[$key]['list'] = $this->_getTemplateRule($key,$personInsuranceInfoResult[$key]['templateRuleResult']['template_id']?:$personInsuranceInfoResult[3-$key]['templateRuleResult']['template_id'],$serviceProductResult['company_id'],explode('|',$personInsuranceInfoResult[$key]['templateRuleResult']['classify_mixed']));
						
						$templateRuleResult[$key]['condition'] = array('rule_id'=>$value['rule_id'],'amount'=>$value['amount'],'start_month'=>int_to_date($value['start_month'],'-'));
						isset($personInsuranceInfoResult[$key]['paymentInfoValue']['companyScale']) && $templateRuleResult[$key]['condition']['companyScale'] = $personInsuranceInfoResult[$key]['paymentInfoValue']['companyScale'];
						isset($personInsuranceInfoResult[$key]['paymentInfoValue']['personScale']) && $templateRuleResult[$key]['condition']['personScale'] = $personInsuranceInfoResult[$key]['paymentInfoValue']['personScale'];
					}
					//dump($serviceProductOrderResult);
					//dump($serviceProductOrderLocationResult);
					//dump($templateClassifyResult);
					//dump($templateRuleResult);
					//dump($personInsuranceInfoResult);
				}else {
					//$this->ajaxReturn(array('status'=>0,'info'=>'参保状态错误！'));
					$this->error('参保状态错误！');
				}
				
				$this->assign('personBaseResult',$personBaseResult);
				$this->assign('personInsuranceInfoResult',$personInsuranceInfoResult);
				$this->assign('serviceProductOrderResult',$serviceProductOrderResult);
				$this->assign('serviceProductOrderLocationResult',$serviceProductOrderLocationResult);
				$this->assign('templateClassifyResult',$templateClassifyResult);
				$this->assign('templateRuleResult',$templateRuleResult);
				$this->display();
			}else {
				$this->error('非法参数！');
			}
		}
	}
	
	/**
	 * toIncrease function
	 * 报增
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function toIncrease(){
		if (IS_POST) {
			//构造测试数据start
			$data = array();
			$data['personName'] = '郎烽凌';
			$data['cardNum'] = '360735199106216856';
			$data['personName'] = '章可文';
			$data['cardNum'] = '340304198803024150';
			$data['mobile'] = '15920288889';
			$data['residenceLocation'] = '14010000';
			$data['residenceType'] = '1';
			
			$data['productId'] = '24';
			$data['location'] = '14020000';
			
			$data['isBuySoc'] = '1';
			$data['socRuleId'] = '54';
			//$data['socPayYear'] = '2016';
			//$data['socPayMonth'] = '6';
			$data['socPayDate'] = '2016-06';
			$data['socAmount'] = '2300';
			$data['socCardNum'] = 'zby';
			$data['isBuyPro'] = '1';
			$data['proRuleId'] = '55';
			//$data['proPayYear'] = '2016';
			//$data['proPayMonth'] = '5';
			$data['proPayDate'] = '2016-05';
			$data['proAmount'] = '2500';
			$data['proCardNum'] = 'zby';
			$data['proPersonScale'] = '11%';
			$data['proCompanyScale'] = '11%';
			//构造测试数据end
			
			$data = I('param.');
			//$data['socCardNum'] = 'zby';
			//$data['proCardNum'] = 'zby';
			//if ($data['payDate']) {
			//	$data['socPayDate'] = $data['payDate'];
			//	$data['proPayDate'] = $data['payDate'];
			//}
			$data['proPayDate'] = empty($data['proPayDate'])?$data['socPayDate']:$data['proPayDate'];
			
			//$data = I('param.');
			//$data['socPayDate'] = $data['socPayYear'].str_pad($data['socPayMonth'],2,'0',STR_PAD_LEFT);
			//$data['proPayDate'] = $data['proPayYear'].str_pad($data['proPayMonth'],2,'0',STR_PAD_LEFT);
			$data['templateLocation'] = $data['location'];
			$data['location'] = ($data['location']/1000<<0)*1000;
			
			$personBaseData = array();
			$personBaseData['user_id'] = $this->mCuid;
			$personBaseData['person_name'] = $data['personName'];
			$personBaseData['card_num'] = $data['cardNum'];
			$personBaseData['mobile'] = $data['mobile'];
			$personBaseData['residence_location'] = $data['residenceLocation'];
			$personBaseData['residence_type'] = $data['residenceType'];
			$personBaseData['birthday'] = get_birthday_by_idCard($data['cardNum']);
			//dump($personBaseData);
			$personBase = D('PersonBase');
			$personBase->startTrans();
			$personBaseResult = $personBase->savePersonBase($personBaseData);
			if ($personBaseResult) {
				$personBaseId = $personBaseResult;
				//dump($personBaseId);
				$personInsurance = D('PersonInsurance');
				$personInsuranceResult = $personInsurance->getInsuranceStatus($this->mCuid,$personBaseId);
				if ($personInsuranceResult) {
					if ($personInsuranceResult['increase']) {
						//计算订单月份
						$template = D('Template');
						//$templateResult = $template->getTemplateByCondition(array('location'=>$data['location'],'state'=>1));
						$templateResult = $template->getTemplateByCondition(array('location'=>$data['templateLocation'],'state'=>1));
						if ($templateResult) {
							$templateResult['deadline'] = array(1=>$templateResult['soc_deadline'],2=>$templateResult['pro_deadline']);
							$templateResult['payment_type'] = array(1=>$templateResult['soc_payment_type'],2=>$templateResult['pro_payment_type']);
							$templateResult['payment_month'] = array(1=>$templateResult['soc_payment_month'],2=>$templateResult['pro_payment_month']);
							
							//$orderDate[1] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($templateResult['deadline'][1],2,'0',STR_PAD_LEFT)))))?date('Ym',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Ym',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
							//$orderDate[2] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($templateResult['deadline'][2],2,'0',STR_PAD_LEFT)))))?date('Ym',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Ym',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
							$orderDate[1] = get_handle_month($templateResult['deadline'][1]);
							$orderDate[2] = get_handle_month($templateResult['deadline'][2]);
							$orderDateStr[1] = substr_replace($orderDate[1],'-',4,0);
							$orderDateStr[2] = substr_replace($orderDate[2],'-',4,0);
							
							//dump($personInsuranceResult);
							$personInsuranceInfo = D('PersonInsuranceInfo');
							$personInsuranceInfoData = array();
							$personInsuranceInfoData['user_id'] = $this->mCuid;
							$personInsuranceInfoData['base_id'] = $personBaseId;
							$personInsuranceInfoData['product_id'] = $data['productId'];
							$personInsuranceInfoData['location'] = $data['location'];
							$personInsuranceInfoData['template_location'] = $data['templateLocation'];
							//$personInsuranceInfoData['end_month'] = 0;
							//$personInsuranceInfoData['audit'] = 0;//未审核
							$personInsuranceInfoData['state'] = 1;//报增
							$personInsuranceInfoData['create_time'] = date('Y-m-d H:i:s');
							$personInsuranceInfoData['modify_time'] = date('Y-m-d H:i:s');
							
							$personInsuranceInfoArray = array();
							if (1 == $data['isBuySoc']) {
								$personInsuranceInfoArray[1] = $personInsuranceInfoData;
								$personInsuranceInfoArray[1]['rule_id'] = $data['socRuleId'];
								$personInsuranceInfoArray[1]['start_month'] = string_to_number($data['socPayDate']);
								$personInsuranceInfoArray[1]['handle_month'] = $orderDate[1];
								$personInsuranceInfoArray[1]['amount'] = $data['socAmount'];
								$personInsuranceInfoArray[1]['payment_type'] = 1;
								$personInsuranceInfoArray[1]['payment_info'] = json_encode(array('cardno'=>$data['socCardNum']));
							}
							if (1 == $data['isBuyPro']) {
								$personInsuranceInfoArray[2] = $personInsuranceInfoData;
								$personInsuranceInfoArray[2]['rule_id'] = $data['proRuleId'];
								$personInsuranceInfoArray[2]['start_month'] = string_to_number($data['proPayDate']);
								$personInsuranceInfoArray[2]['handle_month'] = $orderDate[2];
								$personInsuranceInfoArray[2]['amount'] = $data['proAmount'];
								$personInsuranceInfoArray[2]['payment_type'] = 2;
								$personInsuranceInfoArray[2]['payment_info'] = json_encode(array('companyScale'=>trim($data['proCompanyScale'],'%').'%','personScale'=>trim($data['proPersonScale'],'%').'%','cardno'=>$data['proCardNum']));
							}else {
								$tempPersonInsuranceInfoData = $personInsuranceInfoData;
								$tempPersonInsuranceInfoData['state'] = 0;//未参保
								//$tempPersonInsuranceInfoData['product_id'] = $data['productId'];
								$tempPersonInsuranceInfoData['location'] = 0;
								$tempPersonInsuranceInfoData['template_location'] = 0;
								$tempPersonInsuranceInfoData['rule_id'] = 0;
								$tempPersonInsuranceInfoData['start_month'] = 0;
								$tempPersonInsuranceInfoData['handle_month'] = $orderDate[2];
								$tempPersonInsuranceInfoData['amount'] = 0;
								$tempPersonInsuranceInfoData['payment_type'] = 2;
								$tempPersonInsuranceInfoData['payment_info'] = json_encode(array('companyScale'=>null,'personScale'=>null,'cardno'=>null));
								//if (!$personInsuranceInfo->add($tempPersonInsuranceInfoData)){
								if (!$personInsuranceInfo->addPersonInsurance($tempPersonInsuranceInfoData)){
									$personBase->rollback();
									$this->ajaxReturn(array('status'=>0,'info'=>'系统内部错误！'));
								}
							}
							//dump($personInsuranceInfoArray);
							if ($personInsuranceInfoArray) {
								$personInsuranceInfoResult = array();
								$templateRule = D('TemplateRule');
								foreach ($personInsuranceInfoArray as $key => $value) {
									$personInsuranceInfoResult['rule'][$key] = $templateRule->getById($value['rule_id']);
									//dump($value['rule_id']);
									//dump($personInsuranceInfoResult['rule'][$key]);
									if ($personInsuranceInfoResult['rule'][$key]) {
										//$personInsuranceInfoResult['id'][$key] = $personInsuranceInfo->add($value);
										$personInsuranceInfoResult['id'][$key] = $personInsuranceInfo->addPersonInsurance($value);
										$personInsuranceInfoResult['successCount'] += $personInsuranceInfoResult['id'][$key]?1:0;
									}else {
										$personBase->rollback();
										$this->ajaxReturn(array('status'=>0,'info'=>'规则参数错误！'));
									}
								}
								if ($personInsuranceInfoResult['successCount'] == count($personInsuranceInfoArray)) {
									//计算订单月份
									$serviceProductOrder = D('ServiceProductOrder');
									$serviceProductOrderResult = $serviceProductOrder->getEffectiveServiceProductOrderByProductId($this->mCuid,$data['productId']);
									if ($serviceProductOrderResult) {
										//判断当前月是否存在订单，没有则新增
										/*$serviceOrder = D('ServiceOrder');
										$serviceOrderResult = $serviceOrder->getServiceOrderByConditon(array('user_id'=>$this->mCuid,'product_id'=>$data['productId'],'order_date'=>$orderDate,'payment_type'=>1));
										if ($serviceOrderResult) {
											$serviceOrderId = $serviceOrderResult['id'];
										}else {
											$serviceOrderId = $serviceOrder->add(array('order_no'=>create_order_sn(),'user_id'=>$this->mCuid,'company_id'=>$serviceProductOrderResult['company_id'],'product_id'=>$data['productId'],'order_date'=>$orderDate,'price'=>0,'payment_type'=>1,'state'=>0,'diff_amount'=>0,'create_time'=>date('Y-m-d H:i:s')));
										}*/
										
										//计算缴纳月份，补缴月份
										$serviceInsuranceDetailBaseData = array();
										$serviceInsuranceDetailBaseData['type'] = 1;//报增
										$serviceInsuranceDetailBaseData['state'] = 0;//待支付
										$serviceInsuranceDetailBaseData['create_time'] = date('Y-m-d H:i:s');
										$serviceInsuranceDetailBaseData['modify_time'] = $serviceInsuranceDetailBaseData['create_time'];
										$warrantyLocation = D('WarrantyLocation');
										//计算服务费
										$warrantyLocationResult = $warrantyLocation->getWarrantyLocationByCondition(array('service_product_order_id'=>$serviceProductOrderResult['id'],'location'=>$data['location']));
										$servicePrice = array();
										$servicePrice[1] = $warrantyLocationResult['soc_service_price'];
										$servicePrice[2] = $warrantyLocationResult['pro_service_price'];
										
										$calculate = new \Common\Model\Calculate();
										foreach ($personInsuranceInfoResult['id'] as $key => $value) {
											$endMonth = 1 == $templateResult['payment_type'][$key]?$orderDate[$key]:date('Ym',strtotime('+1 month', strtotime($orderDateStr[$key])));//1缴当月 2缴次月
											$monthNum = get_different_by_month($personInsuranceInfoArray[$key]['start_month'],$endMonth);
											$replenishMonthNum = get_different_by_month($personInsuranceInfoArray[$key]['start_month'],$orderDate[$key])-1;
											if ($monthNum > 0) {
												//计算缴纳费用
												//$servicePrice = 1 == $key?$warrantyLocationResult['ss_service_price']:0;
												$productTemplateRuleResult = $templateRule->getById($personInsuranceInfoArray[$key]['rule_id']);
												$disRuleResult = $templateRule->getTemplateRuleByCondition(array('template_id'=>$templateResult['id'],'company_id'=>$productTemplateRuleResult['company_id'],'type'=>3,'state'=>1));
												$json = json_decode($personInsuranceInfoArray[$key]['payment_info'],true);
												$json['amount'] = $personInsuranceInfoArray[$key]['amount'];
												$json['month'] = 1;
												$json = json_encode($json);
												
												//最大补缴月份
												if ($replenishMonthNum <= $templateResult['payment_month'][$key]) {
													//缴纳年月数组
													$paymentMonthArray = array();
													for ($i=0; $i < $monthNum; $i++) {
														$paymentMonthArray[] = date('Ym',strtotime("+{$i} month",strtotime(substr_replace($personInsuranceInfoArray[$key]['start_month'],'-',4,0))));
													}
													
													//添加参保订单表数据
													//$serviceOrderInsurance = D('ServiceOrderInsurance');
													//$serviceOrderInsuranceData = $serviceInsuranceDetailBaseData;
													//$serviceOrderInsuranceData['service_order_id'] = $serviceOrderId;
													//$serviceOrderInsuranceData['insurance_id'] = $value;
													//$serviceOrderInsuranceData['amount'] = $personInsuranceInfoArray[$key]['amount'];
													//$serviceOrderInsuranceData['pay_date'] = implode(',',$paymentMonthArray);
													//$serviceOrderInsuranceResult = $serviceOrderInsurance->add($serviceOrderInsuranceData);
													$serviceInsuranceDetail = D('ServiceInsuranceDetail');
													$serviceInsuranceDetailDeleteResult = $serviceInsuranceDetail->where(array('insurance_info_id'=>$value))->delete();
													$serviceInsuranceDetailResult = array();
													$serviceInsuranceDetailResult['monthNum'] += $monthNum;
													//更新数据
													$personInsuranceInfoUpdateResult = $personInsuranceInfo->where(array('id'=>$value))->save(array('pay_date'=>implode(',',$paymentMonthArray)));
													//if ($serviceOrderInsuranceResult) {
													if (false !== $personInsuranceInfoUpdateResult) {
														for ($i=0; $i < $monthNum; $i++) { 
															$payDate = date('Ym',strtotime("+{$i} month",strtotime(substr_replace($personInsuranceInfoArray[$key]['start_month'],'-',4,0))));
															$replenish = $payDate < $endMonth?1:0;//是否补缴
															$calculateResult = json_decode($calculate->detail($productTemplateRuleResult['rule'], $json, $key,$disRuleResult['rule'],$replenish),true);
															if (0 == $calculateResult['state']) {
																//$price = $calculateResult['data']['company']+$calculateResult['data']['person']+$calculateResult['data']['pro_cost'];
																$price = $calculateResult['data']['company']+$calculateResult['data']['person'];
																//$calculateResult['data']['service_price'] = $servicePrice[$key];
																$serviceInsuranceDetailData = $serviceInsuranceDetailBaseData;
																$serviceInsuranceDetailData['payment_type'] = $key;//参保类型
																$serviceInsuranceDetailData['insurance_info_id'] = $value;
																//$serviceInsuranceDetailData['service_order_insurance_id'] = $serviceOrderInsuranceResult;
																$serviceInsuranceDetailData['price'] = $price;
																$serviceInsuranceDetailData['service_price'] = $servicePrice[$key];
																$serviceInsuranceDetailData['amount'] = $personInsuranceInfoArray[$key]['amount'];
																$serviceInsuranceDetailData['pay_date'] = $payDate;
																$serviceInsuranceDetailData['handle_month'] = $personInsuranceInfoArray[$key]['handle_month'];
																$serviceInsuranceDetailData['replenish'] = $replenish;
																$serviceInsuranceDetailData['rule_id'] = $personInsuranceInfoArray[$key]['rule_id'];
																//$serviceInsuranceDetailData['rule_detail'] = $productTemplateRuleResult['rule'];
																$serviceInsuranceDetailData['payment_info'] = $personInsuranceInfoArray[$key]['payment_info'];
																$serviceInsuranceDetailData['insurance_detail'] = json_encode($calculateResult['data'],JSON_UNESCAPED_UNICODE);//计算结果
																$serviceInsuranceDetailData['current_detail'] = $serviceInsuranceDetailData['insurance_detail'];
																$serviceInsuranceDetailResult['id'][$key][$serviceInsuranceDetailData['pay_date']] = $serviceInsuranceDetail->add($serviceInsuranceDetailData);
																$serviceInsuranceDetailResult['successCount'] += $serviceInsuranceDetailResult['id'][$key][$serviceInsuranceDetailData['pay_date']]?1:0;
															}else {
																$personBase->rollback();
																$this->ajaxReturn(array('status'=>0,'info'=>'参保数据计算错误！'));
															}
														}
													}else {
														$personBase->rollback();
														$this->ajaxReturn(array('status'=>0,'info'=>'报增失败！'));
													}
												}else {
													$personBase->rollback();
													$this->ajaxReturn(array('status'=>0,'info'=>'超出最大补缴月份！'));
												}
											}else {
												$personBase->rollback();
												$this->ajaxReturn(array('status'=>0,'info'=>'起缴月份错误！'));
											}
										}
										
										if ($serviceInsuranceDetailResult && $serviceInsuranceDetailResult['successCount'] == $serviceInsuranceDetailResult['monthNum']) {
											//统计服务人次
											$serviceProduct = D('ServiceProduct');
											$serviceProductSaveResult = $serviceProduct->where(array('id'=>$data['productId']))->setInc('service_num',1);
											$personBase->commit();
											//保存身份证
											$path = mkFilePath($personBaseId,'./Uploads/Person/','IDCard');
											//保存身份证正面照片
											if ($idCardFrontFile = I('idCardFrontFile','')) {
												$idCardFrontFile = reset(explode('?',$idCardFrontFile));
												if ('/Application/Company/Assets/v2/images/idcard1.png' != $idCardFrontFile) {
													$idCardFrontFileResult = move('.'.$idCardFrontFile,$path.'idCardFront.jpg');
												}
											}
											//保存身份证反面照片
											if ($idCardBackFile = I('idCardBackFile','')) {
												$idCardBackFile = reset(explode('?',$idCardBackFile));
												if ('/Application/Company/Assets/v2/images/idcard2.png' != $idCardBackFile) {
													$idCardBackFileResult = move('.'.$idCardBackFile,$path.'idCardBack.jpg');
												}
											}
											$this->ajaxReturn(array('status'=>1,'info'=>'报增成功！'));
										}else {
											$personBase->rollback();
											$this->ajaxReturn(array('status'=>0,'info'=>'报增失败！'));
										}
									}else {
										$personBase->rollback();
										$this->ajaxReturn(array('status'=>0,'info'=>'产品订单错误！'));
									}
								}else {
									$personBase->rollback();
									$this->ajaxReturn(array('status'=>0,'info'=>'系统内部错误！'));
								}
							}else {
								$personBase->rollback();
								$this->ajaxReturn(array('status'=>0,'info'=>'请选择要参保的项目！'));
								//$this->error('请选择要参保的项目！');
							}
						}else {
							$personBase->rollback();
							$this->ajaxReturn(array('status'=>0,'info'=>'系统缴费模板错误！'));
						}
					}else {
						$personBase->rollback();
						$this->ajaxReturn(array('status'=>0,'info'=>'参保状态错误！'));
					}
				}else {
					$personBase->rollback();
					$this->ajaxReturn(array('status'=>0,'info'=>'系统内部错误！'));
				}
			}else {
				$personBase->rollback();
				$this->ajaxReturn(array('status'=>0,'info'=>$personBase->getError()));
				//$this->error($personBase->getError());
			}
		}else {
			//是否带有baseId，有则查询个人信息
			$baseId = I('get.baseId/d');
			if ($baseId >0 ) {
				$personBase = D('PersonBase');
				$personBaseResult = $personBase->field(true)->getById($baseId);
				$personBaseResult['readonly'] = 1 == $personBaseResult['audit']?' readonly="readonly" ':'';
				$personBaseResult['disabled'] = 1 == $personBaseResult['audit']?' disabled="disabled" ':'';
				$this->assign('personBaseResult',$personBaseResult);
			}
			
			//获取购买的产品订单信息
			$serviceProductOrder = D('ServiceProductOrder');
			$serviceProductOrderResult = $serviceProductOrder->getEffectiveServiceProductOrder($this->mCuid);
			if ($serviceProductOrderResult) {
				//dump($serviceProductOrderResult);
				//dump($this->mMemberStatusArray);
				$this->assign('serviceProductOrderResult',$serviceProductOrderResult);
				$this->display();
			}else {
				$this->redirect('Company/Information/myPackage');
			}
		}
	}
	
	/**
	 * getPersonBaseByIdCard function
	 * 通过身份证号获取个人信息
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getPersonBaseByCardNum(){
		if (IS_POST) {
			$cardNum = I('post.cardNum','');
			empty($cardNum) && $this->error('非法参数!');
			$personBase = D('PersonBase');
			//$personBaseResult = $personBase->field(true)->getByCardNum($cardNum);
			$personBaseResult = $personBase->field(true)->where(['card_num'=>$cardNum,'user_id'=>$this->mCuid])->find();
			if($personBaseResult) {
				$personInsurance = D('PersonInsurance');
				$personInsuranceResult = $personInsurance->getInsuranceStatus($this->mCuid,$personBaseResult['id']);
				$personInsuranceResult['idCardImg'] = get_idCardImg_by_baseId($personInsuranceResult['base_id']);
				if ($personInsuranceResult['increase']) {
					$this->ajaxReturn(array('status'=>1,'result'=>$personBaseResult));
				}else {
					$this->ajaxReturn(array('status'=>0,'info'=>'参保状态错误！'));
				}
			}else{
				//$this->error('该个人信息不存在!');
				$this->ajaxReturn(array('status'=>1,'result'=>null));
			}
		}else {
			$this->error('非法操作!');
		}
	}
	
	/**
	 * getLocation function
	 * 根据产品ID获取参保地
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getLocation(){
		if (IS_POST) {
			//根据产品ID获取参保地
			$productId = I('post.productId/d');
			if ($productId) {
				$serviceProductOrder = D('ServiceProductOrder');
				//$start = microtime(true);
				$serviceProductOrderResult = $serviceProductOrder->getEffectiveServiceProductOrderLocationByProductId($this->mCuid,$productId);
				//$end = microtime(true);
				//dump($end-$start);
				$this->ajaxReturn(array('status'=>1,'result'=>$serviceProductOrderResult));
			}else {
				$this->ajaxReturn(array('status'=>0,'info'=>'非法参数！'));
			}
		}else {
			$this->error('非法操作！');
		}
	}
	
	/**
	 * _getTemplateClassify function
	 * 根据参保地获取模板分类
	 * @access private
	 * @param int $location 城市编号
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	private function _getTemplateClassify($location){
		if ($location) {
			$template = D('Template');
			$templateResult = $template->getTemplateByCondition(array('location'=>$location,'state'=>1));
			if ($templateResult) {
				$templateClassify = D('TemplateClassify');
				$templateClassifyResult = array();
				for ($i=1; $i <= 2; $i++) { 
					$templateClassifyResult[$i] = $templateClassify->getTemplateClassifyByCondition(array('template_id'=>$templateResult['id'],'type'=>$i,'state'=>1));
					if ($templateClassifyResult[$i]) {
						$templateClassifyResult[$i] = list_to_tree($templateClassifyResult[$i],'id','fid','_child',0);
					}
				}
				return array('template_id'=>$templateResult['id'],'result'=>$templateClassifyResult);
			}else {
				return false;
			}
		}else {
			return false;
		}
	}
	
	/**
	 * getTemplateClassify function
	 * 根据参保地获取模板分类
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getTemplateClassify(){
		if (IS_POST) {
			$location = I('post.location/d');
			if ($location) {
				//$start = microtime(true);
				$result = $this->_getTemplateClassify($location);
				//$end = microtime(true);
				//dump($result);
				//dump($end-$start);
				if ($result) {
					$this->ajaxReturn(array('status'=>1,'result'=>$result));
				}else {
					$this->ajaxReturn(array('status'=>0,'info'=>'该参保地不存在模板！'));
				}
			}else {
				$this->ajaxReturn(array('status'=>0,'info'=>'非法参数！'));
			}
		}else {
			$this->error('非法操作！');
		}
	}
	
	/**
	 * _getTemplateRule function
	 * 根据参保地获取模板分类
	 * @access private
	 * @param int $type 类型 1社保 2公积金
	 * @param array $templateId 模板id
	 * @param string $classifyMixed 分类组合
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	private function _getTemplateRule($type = 1,$templateId = 0,$companyId = 0,$classifyMixed = ''){
		if ($templateId) {
			$templateRule = D('TemplateRule');
			if (1 == $type) {
				$classifyMixed = array_filter($classifyMixed);
				rsort($classifyMixed);
				if ($classifyMixed) {
					$classifyMixed = implode('|',$classifyMixed);
					$condition = array('template_id'=>$templateId,'company_id'=>array(0,intval($companyId),array('exp','is null'),'or'),'type'=>$type,'classify_mixed'=>$classifyMixed,'state'=>1);
				}else {
					return false;
				}
			}else if (2 == $type) {
				$condition = array('template_id'=>$templateId,'company_id'=>array(0,intval($companyId),array('exp','is null'),'or'),'type'=>$type,'state'=>1);
			}else {
				return false;
			}
			$templateRuleResult = $templateRule->getTemplateRuleByCondition($condition,2);
			if ($templateRuleResult) {
				foreach ($templateRuleResult as $key => $value) {
					$rule = json_decode($value['rule'],true);
					$templateRuleResult[$key]['rule'] = $rule;
					$templateRuleResult[$key]['minAmount'] = $rule['min'];
					$templateRuleResult[$key]['maxAmount'] = $rule['max'];
					$templateRuleResult[$key]['proCost'] = $rule['pro_cost'];
					!empty($rule['company']) && $templateRuleResult[$key]['companyScale'] = $rule['company'];
					!empty($rule['person']) && $templateRuleResult[$key]['personScale'] = $rule['person'];
				}
				return $templateRuleResult;
			}else {
				return false;
			}
		}else {
			return false;
		}
	}
	
	/**
	 * getTemplateRule function
	 * 根据参保地获取模板规则
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getTemplateRule(){
		if (IS_POST) {
			//dump(I('param.'));
			$type = I('post.type/d');
			$templateId = I('post.templateId/d');
			$companyId = I('post.companyId/d');
			$classifyMixed = I('post.classifyMixed');
			if ($type && $templateId) {
				//$start = microtime(true);
				$result = $this->_getTemplateRule($type,$templateId,$companyId,$classifyMixed);
				//$end = microtime(true);
				//dump($result);
				//dump($end-$start);
				if ($result) {
					$this->ajaxReturn(array('status'=>1,'result'=>$result));
				}else {
					$this->ajaxReturn(array('status'=>0,'info'=>'该参保地不存在模板规则！'));
				}
			}else {
				$this->ajaxReturn(array('status'=>0,'info'=>'非法参数！'));
			}
		}else {
			$this->error('非法操作！');
		}
	}
	
	/**
	 * 调用实例
	 * $rule = 查询数据库结果
	 * 社保实例
	 * $json = json_encode(array('amount'=>100.00,'month'=>3));
	 * $SocInsure = new Calculate();
	 * $json = $SocInsure->detail($rule , $json , 1);
	 * 公积金实例
	 * $json = json_encode(array('amount'=>2000.00,'month'=>3 , 'personScale'=>'5%' , 'companyScale'=>'5%' 'cardno'=>''));
	 * $SocInsure = new Calculate();
	 * $json = $SocInsure->detail($rule , $json , 2);
	 */
	 
	/**
	 * _calculateCost function
	 * 计算社保公积金费用
	 * @param array $data 数据
	 * @access private
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	private function _calculateCost($data){
		$template = D('Template');
		$templateResult = $template->getTemplateByCondition(array('id'=>$data['templateId'],'state'=>1));
		if ($templateResult) {
			$templateResult['deadline'] = array(1=>$templateResult['soc_deadline'],2=>$templateResult['pro_deadline']);
			$templateResult['payment_type'] = array(1=>$templateResult['soc_payment_type'],2=>$templateResult['pro_payment_type']);
			$templateResult['payment_month'] = array(1=>$templateResult['soc_payment_month'],2=>$templateResult['pro_payment_month']);
			
			//$orderDate[1] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($templateResult['deadline'][1],2,'0',STR_PAD_LEFT)))))?date('Ym',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Ym',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
			//$orderDate[2] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($templateResult['deadline'][2],2,'0',STR_PAD_LEFT)))))?date('Ym',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Ym',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
			$orderDate[1] = get_handle_month($templateResult['deadline'][1]);
			$orderDate[2] = get_handle_month($templateResult['deadline'][2]);
			$orderDateStr[1] = substr_replace($orderDate[1],'-',4,0);
			$orderDateStr[2] = substr_replace($orderDate[2],'-',4,0);
			
			//$maxPaymentMonth[1] = $orderDate[1];
			//$maxPaymentMonth[2] = $orderDate[2];
			$maxPaymentMonth[1] = 1 == $templateResult['payment_type'][1]?$orderDate[1]:date('Ym',strtotime('+1 month', strtotime($orderDateStr[1])));
			$maxPaymentMonth[2] = 1 == $templateResult['payment_type'][2]?$orderDate[2]:date('Ym',strtotime('+1 month', strtotime($orderDateStr[2])));
			$minPaymentMonth[1] = date('Ym',strtotime('-'.$templateResult['payment_month'][1].' month', strtotime($maxPaymentMonth[1])));
			$minPaymentMonth[2] = date('Ym',strtotime('-'.$templateResult['payment_month'][2].' month', strtotime($maxPaymentMonth[2])));
			//$minPaymentMonth[1] = date('Ym',strtotime('-'.$templateResult['payment_month'][1].' month', strtotime($orderDateStr[1])));
			//$minPaymentMonth[2] = date('Ym',strtotime('-'.$templateResult['payment_month'][2].' month', strtotime($orderDateStr[2])));
			
			if (!empty($data['socPayMonth']) && string_to_number($data['socPayMonth'])<$minPaymentMonth[1]) {
				return array('status'=>0,'info'=>'社保起缴月份错误！');
			}
			
			if (!empty($data['proPayMonth']) && string_to_number($data['proPayMonth'])<$minPaymentMonth[2]) {
				return array('status'=>0,'info'=>'公积金起缴月份错误！');
			}
			//date_to_int($data['proPayMonth']);
			
			$calculateData = array();
			$templateRule = D('TemplateRule');
			if ($data['socRuleId'] && $data['socAmount']) {
				$socRuleResult = $templateRule->getTemplateRuleByCondition(array('id'=>$data['socRuleId'],'state'=>1));
				$disRuleResult = $templateRule->getTemplateRuleByCondition(array('template_id'=>$data['templateId'],'company_id'=>$socRuleResult['company_id'],'type'=>3,'state'=>1));
				//dump($socRuleResult['company_id']);
				//dump($disRuleResult);
				if ($socRuleResult) {
					$calculateData[1]['rule_id'] = $socRuleResult['id'];
					$calculateData[1]['disRule'] = $disRuleResult['rule'];
					$calculateData[1]['payMonth'] = string_to_number($data['socPayMonth']);
					$calculateData[1]['rule'] = $socRuleResult['rule'];
					$calculateData[1]['json'] = json_encode(array('amount'=>$data['socAmount'],'month'=>$data['socMonthNum'],'cardno'=>$data['socCcardno']));
				}
			}
			
			if ($data['proRuleId'] && $data['proAmount'] && $data['proPersonScale'] && $data['proCompanyScale']) {
				//$proRuleResult = $templateRule->getTemplateRuleByCondition(array('template_id'=>$data['templateId'],'type'=>2,'state'=>1));
				$proRuleResult = $templateRule->getTemplateRuleByCondition(array('id'=>$data['proRuleId'],'state'=>1));
				$disRuleResult = $templateRule->getTemplateRuleByCondition(array('template_id'=>$data['templateId'],'company_id'=>$proRuleResult['company_id'],'type'=>3,'state'=>1));
				if($proRuleResult){
					$calculateData[2]['rule_id'] = $proRuleResult['id'];
					$calculateData[2]['disRule'] = $disRuleResult['rule'];
					$calculateData[2]['payMonth'] = string_to_number($data['proPayMonth']);
					$calculateData[2]['rule'] = $proRuleResult['rule'];
					$calculateData[2]['json'] = json_encode(array('amount'=>$data['proAmount'],'month'=>$data['proMonthNum'],'personScale'=>$data['proPersonScale'],'companyScale'=>$data['proCompanyScale'],'cardno'=>$data['proCcardno']));
				}
			}
			
			if ($calculateData) {
				$calculate = new \Common\Model\Calculate();
				$warrantyLocation = D('WarrantyLocation');
				$warrantyLocationResult = $warrantyLocation->getWarrantyLocationByCondition(array('id'=>$data['warrantyLocationId'],'state'=>0));
				
				$result = array();
				//先按月份顺序插入数组
				$socMonthNum = get_different_by_month($calculateData[1]['payMonth'],$maxPaymentMonth[1]);
				$proMonthNum = get_different_by_month($calculateData[2]['payMonth'],$maxPaymentMonth[2]);
				$monthNum = $socMonthNum>$proMonthNum?$socMonthNum:$proMonthNum;
				$payMonth = strtotime(int_to_date(($maxPaymentMonth[1]>$maxPaymentMonth[2]?$maxPaymentMonth[1]:$maxPaymentMonth[2]),'-'));
				//dump($maxPaymentMonth);
				//dump($monthNum);
				//dump(date('Y-m',$payMonth));
				for ($i=$monthNum-1; $i >= 0; $i--) { 
					$month = date('Y-m',strtotime('-'.$i.' Month',$payMonth));
					$result['data'][$month] = array();
				}
				$servicePrice = array();
				$servicePrice[1] = $warrantyLocationResult['soc_service_price'];
				$servicePrice[2] = $warrantyLocationResult['pro_service_price'];
				//$result['servicePrice'] = $warrantyLocationResult['soc_service_price']+$warrantyLocationResult['pro_service_price'];
				$result['servicePrice'] = 0;
				$result['companyCost'] = 0;
				$result['personCost'] = 0;
				$result['totalCost'] = 0;
				foreach ($calculateData as $key => $value) {
					$monthNum = get_different_by_month($value['payMonth'],$maxPaymentMonth[$key]);
					$payMonth = strtotime(int_to_date($value['payMonth'],'-'));
					//$payMonth = strtotime(int_to_date($maxPaymentMonth[$key],'-'));
					$result['rule_id'][$key] = $value['rule_id'];
					for ($i=0; $i < $monthNum; $i++) { 
						$month = date('Y-m',strtotime('+'.$i.' Month',$payMonth));
						//dump($month);
						//$result['data'][$month][$key]['replenish'] = string_to_number($month) < $orderDate[$key]?1:0;
						$replenish = string_to_number($month) < $maxPaymentMonth[$key]?1:0;
						$calculateResult = json_decode($calculate->detail($value['rule'],$value['json'],$key,$value['disRule'],$replenish),true);
						//$calculateResult = json_decode($calculate->detail($value['rule'],$value['json'],$key),true);
						$result['data'][$month][$key] = $calculateResult;
						$result['data'][$month][$key]['servicePrice'] = $servicePrice[$key];
						$result['data'][$month][$key]['replenish'] = $replenish;
						//dump($calculateResult);
						//dump($value['disRule']);
						//dump($result['data'][$month][$key]['replenish']);
						if (0 == $calculateResult['state']) {
							$companyCost = $calculateResult['data']['company'];
							$personCost = $calculateResult['data']['person'];
							$proCost = $calculateResult['data']['pro_cost'];
						}else {
							$companyCost = 0;
							$personCost = 0;
							$proCost = 0;
						}
						$result['companyCost'] += $companyCost;
						$result['personCost'] += $personCost;
						$result['servicePrice'] += $servicePrice[$key];
					}
				}
				$result['totalCost'] = $result['servicePrice'] + $result['companyCost'] + $result['personCost'];
				$result['monthNum'] = count($result['data']);
				//dump($result);
				return array('status'=>1,'result'=>$result);
			}else {
				return array('status'=>0,'info'=>'非法参数！');
			}
		}else {
			return array('status'=>0,'info'=>'非法参数！');
		}
	}
	
	/**
	 * _calculateCostByPiiId function
	 * 根据insurance_info_id获取参保数据
	 * @param array $data 数据
	 * @access private
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function _calculateCostByPiiId($data){
		//dump($data);
		$baseId = $data['baseId'];
		$piiId = array('in',array($data['socPiiId'],$data['proPiiId']));
		if (!empty($baseId) && !empty($piiId)) {
			$condition = array();
			$condition['user_id'] = $this->mCuid;
			$condition['base_id'] = $baseId;
			$condition['pii_id'] = $piiId;
			$personInsuranceInfo = D('PersonInsuranceInfo');
			$personInsuranceInfoResult = $personInsuranceInfo->getInsurancePayDateDetailByPiiId($condition);
			if (false !== $personInsuranceInfoResult) {
				$this->ajaxReturn(array('status'=>1,'result'=>$personInsuranceInfoResult));
			}else {
				$this->ajaxReturn(array('status'=>0,'info'=>$personInsuranceInfo->getError()));
			}
		}else {
			$this->ajaxReturn(array('status'=>0,'info'=>'非法参数！'));
		}
	}
	 
	/**
	 * calculateCost function
	 * 计算社保公积金费用
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function calculateCost(){
		if (IS_POST) {
			
			//构造测试数据start
			$data = array();
			$data['templateId'] = '39';
			$data['warrantyLocationId'] = '19';
			$data['socRuleId'] = '90';
			$data['socAmount'] = '2300';
			$data['socMonthNum'] = '1';
			$data['socPayMonth'] = '2016-06';
			$data['socCcardno'] = '123456789123456789';
			$data['proRuleId'] = '55';
			$data['proAmount'] = '2300';
			$data['proMonthNum'] = '1';
			$data['proPayMonth'] = '2016-05';
			$data['proPersonScale'] = '11%';
			$data['proCompanyScale'] = '11%';
			$data['proCcardno'] = '123456789123456789';
			//$data['baseId'] = 277;
			//$data['socPiiId'] = 1030;
			//$data['proPiiId'] = 1031;
			//构造测试数据end
			
			$data = I('param.');
			$data['proPayMonth'] = empty($data['proPayMonth'])?$data['socPayMonth']:$data['proPayMonth'];
			
			if ($data['templateId']) {
				if ($data['socPiiId'] || $data['proPiiId']) {
					$this->ajaxReturn($this->_calculateCostByPiiId($data));
				}else {
					$this->ajaxReturn($this->_calculateCost($data));
				}
			}else {
				$this->ajaxReturn(array('status'=>0,'info'=>'非法参数！'));
			}
		}else {
			$this->error('非法操作！');
		}
	}
	
	/**
	 * editIncrease function
	 * 编辑报增
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function editIncrease(){
		if (IS_POST) {
			//构造测试数据start
			$data = array();
			$data['baseId'] = '147';
			$data['personName'] = '和介辉';
			$data['cardNum'] = '340522197602272018';
			$data['baseId'] = '149';
			$data['personName'] = '熊自鸣';
			$data['cardNum'] = '150302199103145193';
			$data['mobile'] = '15920288888';
			$data['residenceLocation'] = '14010000';
			$data['residenceType'] = '1';
			
			$data['productId'] = '24';
			$data['location'] = '14020100';
			
			$data['isBuySoc'] = '1';
			$data['socPiiId'] = '396';
			$data['socRuleId'] = '89';
			//$data['socPayYear'] = '2016';
			//$data['socPayMonth'] = '6';
			$data['socPayDate'] = '2016-06';
			$data['socAmount'] = '2300';
			$data['socCardNum'] = 'zby';
			$data['isBuyPro'] = '0';
			$data['proPiiId'] = '397';
			$data['proRuleId'] = '67';
			//$data['proPayYear'] = '2016';
			//$data['proPayMonth'] = '5';
			$data['proPayDate'] = '2016-05';
			$data['proAmount'] = '2500';
			$data['proCardNum'] = 'zby';
			$data['proPersonScale'] = '11%';
			$data['proCompanyScale'] = '11%';
			//构造测试数据end
			
			$data = I('param.');
			//$data['socCardNum'] = 'zby';
			//$data['proCardNum'] = 'zby';
			//if ($data['payDate']) {
			//	$data['socPayDate'] = $data['payDate'];
			//	$data['proPayDate'] = $data['payDate'];
			//}
			$data['proPayDate'] = empty($data['proPayDate'])?$data['socPayDate']:$data['proPayDate'];
			
			//$data['socPayDate'] = $data['socPayYear'].str_pad($data['socPayMonth'],2,'0',STR_PAD_LEFT);
			//$data['proPayDate'] = $data['proPayYear'].str_pad($data['proPayMonth'],2,'0',STR_PAD_LEFT);
			$data['templateLocation'] = $data['location'];
			$data['location'] = ($data['location']/1000<<0)*1000;
			
			$personBaseData = array();
			$personBaseData['id'] = $data['baseId'];
			$personBaseData['user_id'] = $this->mCuid;
			$personBaseData['person_name'] = $data['personName'];
			$personBaseData['card_num'] = $data['cardNum'];
			$personBaseData['mobile'] = $data['mobile'];
			$personBaseData['residence_location'] = $data['residenceLocation'];
			$personBaseData['residence_type'] = $data['residenceType'];
			$personBase = D('PersonBase');
			$personBase->startTrans();
			$personBaseResult = $personBase->savePersonBase($personBaseData);
			if ($personBaseResult) {
				$personBaseId = $personBaseResult;
				$personInsurance = D('PersonInsurance');
				$personInsuranceResult = $personInsurance->getInsuranceStatus($this->mCuid,$personBaseId);
				if ($personInsuranceResult) {
					if ($personInsuranceResult['editIncrease']) {
						
						//计算订单月份
						$template = D('Template');
						//$templateResult = $template->getTemplateByCondition(array('location'=>$data['location'],'state'=>1));
						$templateResult = $template->getTemplateByCondition(array('location'=>$data['templateLocation'],'state'=>1));
						if ($templateResult) {
							$templateResult['deadline'] = array(1=>$templateResult['soc_deadline'],2=>$templateResult['pro_deadline']);
							$templateResult['payment_type'] = array(1=>$templateResult['soc_payment_type'],2=>$templateResult['pro_payment_type']);
							$templateResult['payment_month'] = array(1=>$templateResult['soc_payment_month'],2=>$templateResult['pro_payment_month']);
							
							//$orderDate[1] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($templateResult['deadline'][1],2,'0',STR_PAD_LEFT)))))?date('Ym',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Ym',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
							//$orderDate[2] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($templateResult['deadline'][2],2,'0',STR_PAD_LEFT)))))?date('Ym',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Ym',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
							$orderDate[1] = get_handle_month($templateResult['deadline'][1]);
							$orderDate[2] = get_handle_month($templateResult['deadline'][2]);
							$orderDateStr[1] = substr_replace($orderDate[1],'-',4,0);
							$orderDateStr[2] = substr_replace($orderDate[2],'-',4,0);
							
							$personInsuranceInfo = D('PersonInsuranceInfo');
							$personInsuranceInfoOriginResult = $personInsuranceInfo->getPersonInsuranceInfoByHandleMonth(array('user_id'=>$this->mCuid,'base_id'=>$personBaseResult,'handle_month'=>$data['handleMonth']));
							$personInsuranceInfoData = array();
							$personInsuranceInfoData['user_id'] = $this->mCuid;
							$personInsuranceInfoData['base_id'] = $personBaseId;
							$personInsuranceInfoData['product_id'] = $data['productId'];
							$personInsuranceInfoData['location'] = $data['location'];
							$personInsuranceInfoData['template_location'] = $data['templateLocation'];
							//$personInsuranceInfoData['end_month'] = 0;
							//$personInsuranceInfoData['audit'] = 0;//未审核
							//$personInsuranceInfoData['state'] = 1;//报增
							//$personInsuranceInfoData['create_time'] = date('Y-m-d H:i:s');
							$personInsuranceInfoData['modify_time'] = date('Y-m-d H:i:s');
							
							$personInsuranceInfoArray = array();
							if (1 == $data['isBuySoc']) {
								$personInsuranceInfoResult = $personInsuranceInfo->field('id,state,operate_state')->getById($data['socPiiId']);
								if (in_array($personInsuranceInfoResult['operate_state'],array(0,-1,-9))) {
									$personInsuranceInfoArray[1] = $personInsuranceInfoData;
									$personInsuranceInfoArray[1]['id'] = $data['socPiiId'];
									//$personInsuranceInfoArray[1]['pay_order_id'] = 0;
									$personInsuranceInfoArray[1]['rule_id'] = $data['socRuleId'];
									$personInsuranceInfoArray[1]['start_month'] = string_to_number($data['socPayDate']);
									//$personInsuranceInfoArray[1]['handle_month'] = $orderDate[1];
									$personInsuranceInfoArray[1]['handle_month'] = $data['handleMonth'];
									$personInsuranceInfoArray[1]['amount'] = $data['socAmount'];
									$personInsuranceInfoArray[1]['payment_type'] = 1;
									$personInsuranceInfoArray[1]['payment_info'] = json_encode(array('cardno'=>$data['socCardNum']));
									$personInsuranceInfoArray[1]['state'] = (0 == $personInsuranceInfoResult['state']?1:$personInsuranceInfoResult['state']);
									$personInsuranceInfoArray[1]['operate_state'] = 0;//未审核
								}
							}
							if (1 == $data['isBuyPro']) {
								$personInsuranceInfoResult = $personInsuranceInfo->field('id,state,operate_state')->getById($data['proPiiId']);
								if (in_array($personInsuranceInfoResult['operate_state'],array(0,-1,-9))) {
									$personInsuranceInfoArray[2] = $personInsuranceInfoData;
									$personInsuranceInfoArray[2]['id'] = $data['proPiiId'];
									//$personInsuranceInfoArray[2]['pay_order_id'] = 0;
									$personInsuranceInfoArray[2]['rule_id'] = $data['proRuleId'];
									$personInsuranceInfoArray[2]['start_month'] = string_to_number($data['proPayDate']);
									//$personInsuranceInfoArray[2]['handle_month'] = $orderDate[2];
									$personInsuranceInfoArray[2]['handle_month'] = $data['handleMonth'];
									$personInsuranceInfoArray[2]['amount'] = $data['proAmount'];
									$personInsuranceInfoArray[2]['payment_type'] = 2;
									$personInsuranceInfoArray[2]['payment_info'] = json_encode(array('companyScale'=>trim($data['proCompanyScale'],'%').'%','personScale'=>trim($data['proPersonScale'],'%').'%','cardno'=>$data['proCardNum']));
									$personInsuranceInfoArray[2]['state'] = (0 == $personInsuranceInfoResult['state']?1:$personInsuranceInfoResult['state']);
									$personInsuranceInfoArray[2]['operate_state'] = 0;//未审核
								}
							}else if(0 == $data['isBuyPro'] && 0 != $personInsuranceInfoOriginResult[2]['state']) {
								$personInsuranceInfoArray[2]['id'] = $data['proPiiId'];
								//$personInsuranceInfoArray[2]['pay_order_id'] = 0;
								$personInsuranceInfoArray[2]['pay_date'] = '';
								$personInsuranceInfoArray[2]['operate_state'] = -9;//撤销
								$personInsuranceInfoResult = $personInsuranceInfo->getLastPersonInsuranceInfo(array('id'=>$data['proPiiId'],'user_id'=>$this->mCuid,'base_id'=>$personBaseId,'payment_type'=>2));
								if ($personInsuranceInfoResult) {
									$personInsuranceInfoArray[2]['state'] = (1 == $personInsuranceInfoResult['state']?0:$personInsuranceInfoResult['state']);
								}else {
									$personInsuranceInfoArray[2]['state'] = 0;//未参保
								}
							}
							if ($personInsuranceInfoArray) {
								$personInsuranceInfoResult = array();
								$templateRule = D('TemplateRule');
								foreach ($personInsuranceInfoArray as $key => $value) {
									if (1 == $value['state']) {
										//报增状态
										//$personInsuranceInfoResult[$key] = $personInsuranceInfo->savePersonInsurance($value);
										$personInsuranceInfoResult['rule'][$key] = $templateRule->getById($value['rule_id']);
										if ($personInsuranceInfoResult['rule'][$key]) {
											$personInsuranceInfoResult['id'][$key] = $value['id'];
											$tempPersonInsuranceInfoResult = $personInsuranceInfo->save($value);
											$personInsuranceInfoResult['successCount'] += false !== $tempPersonInsuranceInfoResult?1:0;
										}else {
											$personBase->rollback();
											$this->ajaxReturn(array('status'=>0,'info'=>'规则参数错误！'));
										}
									}else {
										$personInsuranceInfoResult['id'][$key] = $value['id'];
										$tempPersonInsuranceInfoResult = $personInsuranceInfo->save($value);
										$personInsuranceInfoResult['successCount'] += false !== $tempPersonInsuranceInfoResult?1:0;
									}
								}
								if ($personInsuranceInfoResult['successCount'] == count($personInsuranceInfoArray)) {
									//计算订单月份
									$serviceProductOrder = D('ServiceProductOrder');
									$serviceProductOrderResult = $serviceProductOrder->getEffectiveServiceProductOrderByProductId($this->mCuid,$data['productId']);
									if ($serviceProductOrderResult) {
											//计算缴纳月份，补缴月份
											$serviceInsuranceDetailBaseData = array();
											$serviceInsuranceDetailBaseData['pay_order_id'] = 0;//无支付订单
											$serviceInsuranceDetailBaseData['type'] = 1;//报增
											$serviceInsuranceDetailBaseData['state'] = 0;//待审核
											$serviceInsuranceDetailBaseData['create_time'] = date('Y-m-d H:i:s');
											$serviceInsuranceDetailBaseData['modify_time'] = $serviceInsuranceDetailBaseData['create_time'];
											$warrantyLocation = D('WarrantyLocation');
											//计算服务费
											$warrantyLocationResult = $warrantyLocation->getWarrantyLocationByCondition(array('service_product_order_id'=>$serviceProductOrderResult['id'],'location'=>$data['location']));
											$servicePrice = array();
											$servicePrice[1] = $warrantyLocationResult['soc_service_price'];
											$servicePrice[2] = $warrantyLocationResult['pro_service_price'];
											$calculate = new \Common\Model\Calculate();
											foreach ($personInsuranceInfoResult['id'] as $key => $value) {
												//勾选报增
												if (1 == $personInsuranceInfoArray[$key]['state']) {
													//报增状态
													$endMonth = 1 == $templateResult['payment_type'][$key]?$orderDate[$key]:date('Ym',strtotime('+1 month', strtotime($orderDateStr[$key])));//1缴当月 2缴次月
													$monthNum = get_different_by_month($personInsuranceInfoArray[$key]['start_month'],$endMonth);
													$replenishMonthNum = get_different_by_month($personInsuranceInfoArray[$key]['start_month'],$orderDate[$key])-1;
													if ($monthNum > 0) {
														//计算缴纳费用
														$productTemplateRuleResult = $templateRule->getById($personInsuranceInfoArray[$key]['rule_id']);
														$disRuleResult = $templateRule->getTemplateRuleByCondition(array('template_id'=>$templateResult['id'],'company_id'=>$productTemplateRuleResult['company_id'],'type'=>3,'state'=>1));
														//$servicePrice = 1 == $key?$warrantyLocationResult['ss_service_price']:0;
														$json = json_decode($personInsuranceInfoArray[$key]['payment_info'],true);
														$json['amount'] = $personInsuranceInfoArray[$key]['amount'];
														$json['month'] = 1;
														$json = json_encode($json);
														//最大补缴月份
														if ($replenishMonthNum <= $templateResult['payment_month'][$key]) {
															//缴纳年月数组
															$paymentMonthArray = array();
															for ($i=0; $i < $monthNum; $i++) {
																$paymentMonthArray[] = date('Ym',strtotime("+{$i} month",strtotime(substr_replace($personInsuranceInfoArray[$key]['start_month'],'-',4,0))));
															}
															//添加参保订单表数据
															$serviceInsuranceDetail = D('ServiceInsuranceDetail');
															
															//$serviceInsuranceDetailDeleteResult = $serviceInsuranceDetail->where(array('insurance_info_id'=>$value,'state'=>0))->delete();
															$serviceInsuranceDetailDeleteResult = $serviceInsuranceDetail->where(array('insurance_info_id'=>$value))->delete();
															//更新数据
															$personInsuranceInfoUpdateResult = $personInsuranceInfo->where(array('id'=>$value))->save(array('pay_date'=>implode(',',$paymentMonthArray)));
															
															$serviceInsuranceDetailResult = array();
															$serviceInsuranceDetailResult['monthNum'] += $monthNum;
															for ($i=0; $i < $monthNum; $i++) {
																$payDate = date('Ym',strtotime("+{$i} month",strtotime(substr_replace($personInsuranceInfoArray[$key]['start_month'],'-',4,0))));
																$replenish = $payDate < $endMonth?1:0;//是否补缴
																$calculateResult = json_decode($calculate->detail($productTemplateRuleResult['rule'], $json, $key, $disRuleResult['rule'] ,$replenish ),true);
																if (0 == $calculateResult['state']) {
																	//$price = $calculateResult['data']['company']+$calculateResult['data']['person']+$calculateResult['data']['pro_cost'];
																	$price = $calculateResult['data']['company']+$calculateResult['data']['person'];
																	//$calculateResult['data']['service_price'] = $servicePrice[$key];
																	$serviceInsuranceDetailData = $serviceInsuranceDetailBaseData;
																	$serviceInsuranceDetailData['payment_type'] = $key;//参保类型
																	$serviceInsuranceDetailData['insurance_info_id'] = $value;
																	//$serviceInsuranceDetailData['service_order_insurance_id'] = $serviceOrderInsuranceId;
																	$serviceInsuranceDetailData['price'] = $price;
																	$serviceInsuranceDetailData['service_price'] = $servicePrice[$key];
																	$serviceInsuranceDetailData['amount'] = $personInsuranceInfoArray[$key]['amount'];
																	$serviceInsuranceDetailData['pay_date'] = $payDate;
																	$serviceInsuranceDetailData['handle_month'] = $personInsuranceInfoArray[$key]['handle_month'];
																	$serviceInsuranceDetailData['replenish'] = $replenish;
																	$serviceInsuranceDetailData['rule_id'] = $personInsuranceInfoArray[$key]['rule_id'];
																	//$serviceInsuranceDetailData['rule_detail'] = $productTemplateRuleResult['rule'];
																	$serviceInsuranceDetailData['payment_info'] = $personInsuranceInfoArray[$key]['payment_info'];
																	$serviceInsuranceDetailData['insurance_detail'] = json_encode($calculateResult['data'],JSON_UNESCAPED_UNICODE);//计算结果
																	$serviceInsuranceDetailData['current_detail'] = $serviceInsuranceDetailData['insurance_detail'];
																	$serviceInsuranceDetailResult['id'][$key][$serviceInsuranceDetailData['pay_date']] = $serviceInsuranceDetail->add($serviceInsuranceDetailData);
																	$serviceInsuranceDetailResult['successCount'] += $serviceInsuranceDetailResult['id'][$key][$serviceInsuranceDetailData['pay_date']]?1:0;
																}else {
																	$personBase->rollback();
																	$this->ajaxReturn(array('status'=>0,'info'=>'参保数据计算错误！'));
																}
															}
														}else {
															$personBase->rollback();
															$this->ajaxReturn(array('status'=>0,'info'=>'超出最大补缴月份！'));
														}
													}else {
														$personBase->rollback();
														$this->ajaxReturn(array('status'=>0,'info'=>'起缴月份错误！'));
													}
												}else {
													//未勾选购买
													/*$serviceOrderInsurance = D('ServiceOrderInsurance');
													$condition = array();
													$condition['insurance_id'] = $value;
													$condition['type'] = 1;
													$condition['state'] = 0;
													$serviceOrderInsuranceResult = $serviceOrderInsurance->getServiceOrderInsuranceByCondition($condition);
													if ($serviceOrderInsuranceResult) {
														//有老订单则撤销购买
														$serviceOrderInsuranceData = array();
														$serviceOrderInsuranceData['state'] = -9;
														$serviceOrderInsuranceData['modify_time'] = date('Y-m-d H:i:s');
														$serviceOrderInsuranceSaveResult = $serviceOrderInsurance->where(array('id'=>$serviceOrderInsuranceResult['id']))->save($serviceOrderInsuranceData);
														if (false !== $serviceOrderInsuranceSaveResult) {*/
															$serviceInsuranceDetail = D('ServiceInsuranceDetail');
															$condition = array();
															//$condition['service_order_insurance_id'] = $serviceOrderInsuranceResult['id'];
															$condition['insurance_info_id'] = $value;
															$condition['state'] = ['in',[0,-1]];
															$serviceInsuranceDetailData = array();
															$serviceInsuranceDetailData['pay_order_id'] = 0;//无支付订单
															$serviceInsuranceDetailData['state'] = -9;
															$serviceInsuranceDetailData['modify_time'] = date('Y-m-d H:i:s');
															$serviceInsuranceDetailSaveResult = $serviceInsuranceDetail->where($condition)->save($serviceInsuranceDetailData);
															if (false === $serviceInsuranceDetailSaveResult) {
																$personBase->rollback();
																$this->ajaxReturn(array('status'=>0,'info'=>'系统内部错误！'));
															}
														/*}else {
															$personBase->rollback();
															$this->ajaxReturn(array('status'=>0,'info'=>'系统内部错误！'));
														}
													}*/
												}
											}
											//if ($serviceInsuranceDetailResult && $serviceInsuranceDetailResult['successCount'] == $serviceInsuranceDetailResult['monthNum']) {
											if ($serviceInsuranceDetailResult['successCount'] == $serviceInsuranceDetailResult['monthNum']) {
												$personBase->commit();
												//保存身份证
												$path = mkFilePath($personBaseId,'./Uploads/Person/','IDCard');
												//保存身份证正面照片
												if ($idCardFrontFile = I('idCardFrontFile','')) {
													$idCardFrontFile = reset(explode('?',$idCardFrontFile));
													if ('/Application/Company/Assets/v2/images/idcard1.png' != $idCardFrontFile) {
														$idCardFrontFileResult = move('.'.$idCardFrontFile,$path.'idCardFront.jpg');
													}
												}
												//保存身份证反面照片
												if ($idCardBackFile = I('idCardBackFile','')) {
													$idCardBackFile = reset(explode('?',$idCardBackFile));
													if ('/Application/Company/Assets/v2/images/idcard2.png' != $idCardBackFile) {
														$idCardBackFileResult = move('.'.$idCardBackFile,$path.'idCardBack.jpg');
													}
												}
												$this->ajaxReturn(array('status'=>1,'info'=>'编辑成功！'));
											}else {
												$personBase->rollback();
												$this->ajaxReturn(array('status'=>0,'info'=>'编辑失败！'));
											}
									}else {
										$personBase->rollback();
										$this->ajaxReturn(array('status'=>0,'info'=>'产品订单错误！'));
									}
								}else {
									$personBase->rollback();
									$this->ajaxReturn(array('status'=>0,'info'=>'系统内部错误！'));
								}
							}else {
								$personBase->rollback();
								$this->ajaxReturn(array('status'=>0,'info'=>'请选择要参保的项目！'));
							}
						}else {
							$personBase->rollback();
							$this->ajaxReturn(array('status'=>0,'info'=>'系统缴费模板错误！'));
						}
					}else {
						$personBase->rollback();
						$this->ajaxReturn(array('status'=>0,'info'=>'参保状态错误！'));
					}
				}else {
					$personBase->rollback();
					$this->ajaxReturn(array('status'=>0,'info'=>'系统内部错误！'));
				}
			}else {
				$personBase->rollback();
				$this->ajaxReturn(array('status'=>0,'info'=>$personBase->getError()));
				//$this->error($personBase->getError());
			}
		}else {
			//是否带有baseId，有则查询个人信息
			$baseId = I('get.baseId/d');
			if ($baseId >0 ) {
				//获取个人信息
				$personBase = D('PersonBase');
				$personBaseResult = $personBase->field(true)->getById($baseId);
				$personBaseResult['readonly'] = 1 == $personBaseResult['audit']?' readonly="readonly" ':'';
				$personBaseResult['disabled'] = 1 == $personBaseResult['audit']?' disabled="disabled" ':'';
				//获取身份证图片
				$personBaseResult['idCardImg'] = get_idCardImg_by_baseId($baseId);
				
				/*$condition = array();
				$condition['user_id'] = $this->mCuid;
				$condition['base_id'] = $baseId;
				$personInsurance = D('personInsurance');
				$personInsuranceResult = $personInsurance->getPersonInsuranceByCondition($condition,false);*/
				
				//获取参保信息
				$personInsuranceInfo = D('personInsuranceInfo');
				$personInsuranceInfoResult = $personInsuranceInfo->getServiceOrderDetailByCondition(array('user_id'=>$this->mCuid,'base_id'=>$baseId),array('in','0,1'),false);
				if ($personInsuranceInfoResult) {
					$productId = $personInsuranceInfoResult[1]['product_id']?:$personInsuranceInfoResult[2]['product_id'];
					$location = $personInsuranceInfoResult[1]['location']?:$personInsuranceInfoResult[2]['location'];
					$templateLocation = $personInsuranceInfoResult[1]['template_location']?:$personInsuranceInfoResult[2]['template_location'];
					
					$serviceProductOrder = D('ServiceProductOrder');
					$serviceProduct = D('ServiceProduct');
					$templateRule = D('TemplateRule');
					$template = D('Template');
					
					//获取购买的产品信息
					$serviceProductResult = $serviceProduct->alias('sp')->field('sp.company_id,sp.name,ci.company_name')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id = sp.company_id')->where(['sp.id'=>$productId])->find();
					
					//获取购买的产品订单信息
					$serviceProductOrderResult['list'] = $serviceProductOrder->getEffectiveServiceProductOrder($this->mCuid);
					$serviceProductOrderResult['condition'] = array('product_id'=>$productId,'product_name'=>$serviceProductResult['name'],'company_name'=>$serviceProductResult['company_name']);
					
					$serviceProductOrderLocationResult['list'] = $serviceProductOrder->getEffectiveServiceProductOrderLocationByProductId($this->mCuid,$productId);
					$serviceProductOrderLocationResult['condition'] = array('location'=>$templateLocation);
					
					foreach ($personInsuranceInfoResult as $key => $value) {
						//$personInsuranceInfoResult[$key]['serviceProductOrderResult'] = $serviceProductOrder->getEffectiveServiceProductOrderLocationByProductId($this->mCuid,$value['product_id']);
						$personInsuranceInfoResult[$key]['paymentInfoValue'] = json_decode($value['payment_info'],true);
						$personInsuranceInfoResult[$key]['templateRuleResult'] = $templateRule->getTemplateRuleByCondition(array('id'=>$value['rule_id']));
						//$personInsuranceInfoResult[$key]['templateResult'] = $template->getTemplateByCondition(array('id'=>$personInsuranceInfoResult[$key]['templateRuleResult']['template_id']));
						
						$templateClassifyResult[$key]['list'] = $this->_getTemplateClassify($templateLocation);
						$templateClassifyResult[$key]['condition'] = array('classify_mixed'=>array_filter(explode('|',$personInsuranceInfoResult[$key]['templateRuleResult']['classify_mixed'])));
						
						$templateRuleResult[$key]['list'] = $this->_getTemplateRule($key,$personInsuranceInfoResult[$key]['templateRuleResult']['template_id']?:$personInsuranceInfoResult[3-$key]['templateRuleResult']['template_id'],$serviceProductResult['company_id'],explode('|',$personInsuranceInfoResult[$key]['templateRuleResult']['classify_mixed']));
						
						$templateRuleResult[$key]['condition'] = array('rule_id'=>$value['rule_id'],'amount'=>$value['amount'],'start_month'=>int_to_date($value['start_month'],'-'),'companyScale'=>$personInsuranceInfoResult[$key]['paymentInfoValue']['companyScale'],'personScale'=>$personInsuranceInfoResult[$key]['paymentInfoValue']['personScale']);
						
					}
					//dump($serviceProductOrderResult);
					//dump($serviceProductOrderLocationResult);
					//dump($templateClassifyResult);
					//dump($templateRuleResult);
					//dump($personInsuranceInfoResult);
					/*foreach ($personInsuranceInfoResult as $key => $value) {
						$personInsuranceInfoResult[$key]['serviceProductOrderResult'] = $serviceProductOrder->getEffectiveServiceProductOrderLocationByProductId($this->mCuid,$value['product_id']);
						$personInsuranceInfoResult[$key]['serviceProductResult'] = $serviceProduct->field('company_id,name')->getById($value['product_id']);
						$personInsuranceInfoResult[$key]['templateRuleResult'] = $templateRule->getTemplateRuleByCondition(array('id'=>$value['rule_id']));
						$personInsuranceInfoResult[$key]['templateResult'] = $template->getTemplateByCondition(array('id'=>$personInsuranceInfoResult[$key]['templateRuleResult']['template_id']));
						$personInsuranceInfoResult[$key]['templateClassifyList'] = $this->_getTemplateClassify($personInsuranceInfoResult[$key]['templateResult']['location']);
						$personInsuranceInfoResult[$key]['templateRuleList'] = $this->_getTemplateRule($key,$personInsuranceInfoResult[$key]['templateRuleResult']['template_id'],$personInsuranceInfoResult[$key]['serviceProductResult']['company_id'],explode('|',$personInsuranceInfoResult[$key]['templateRuleResult']['classify_mixed']));
					}*/
				}else {
					//$this->ajaxReturn(array('status'=>0,'info'=>'参保状态错误！'));
					$this->error('参保状态错误！');
				}
				
				$this->assign('personBaseResult',$personBaseResult);
				$this->assign('personInsuranceInfoResult',$personInsuranceInfoResult);
				$this->assign('serviceProductOrderResult',$serviceProductOrderResult);
				$this->assign('serviceProductOrderLocationResult',$serviceProductOrderLocationResult);
				$this->assign('templateClassifyResult',$templateClassifyResult);
				$this->assign('templateRuleResult',$templateRuleResult);
				$this->display();
			}else {
				$this->error('非法参数！');
			}
		}
	}
	
	/**
	 * downloadTemplateFile function
	 * 下载模板文件
	 * @access public
	 * @return file
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function downloadTemplateFile(){
		$type = I('get.type','xls');
		if($type) {
			if ($type == 'xls' || $type == 'xlsx') {
				$fileName = $type=='xls'?'批量报增模板.xls':'批量报增模板.xlsx';
				$fileSize = $type=='xls'?50176:19421;
				$file = array('url'=>'./Uploads/Download/'.$fileName,'name'=>$fileName,'type'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document','size'=>$fileSize);
				downLocalFile($file);
			}else{
				$this->error('未知的文件类型!');
			}
		}else {
			$this->error('非法操作!');
		}
	}
	
	/**
	 * uploadTemplateFile function
	 * 上传文件
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function uploadTemplateFile(){
		if (IS_POST) {
			//企业登录/退出登录时清空temp目录下的企业对应临时文件
			$upload = new \Think\Upload(C('EXCEL_UPLOAD'));
			$path = rtrim(mkFilePath($this->mCid,$upload->rootPath,'temp'),'/');
			$path = str_replace($upload->rootPath,'',$path);
			$upload->subName = $path;
			$upload->saveName = 'batchIncrease_'.GUID();
			// 上传单个文件 
			$info = $upload->uploadOne($_FILES['file']);
			if(!$info) {// 上传错误提示错误信息
				$this->ajaxReturn(array('status'=>0,'info'=>$upload->getError()));
			}else{// 上传成功 获取上传文件信息
				$url = ltrim($upload->rootPath,'.').$info['savepath'].$info['savename'];
				$this->ajaxReturn(array('status'=>1,'info'=>$url));
			}
		}else {
			$this->error('非法操作!');
		}
	}
	
	/**
	 * _handleExcel function
	 * 处理excel
	 * @access private
	 * @param string $filePath 文件路径
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	private function _handleExcel($filePath){
		Vendor('PHPExcel.PHPExcel');
		$extend=pathinfo($filePath);
		$extend = strtolower($extend['extension']);//获取后缀名并转为小写
		$extend=='xlsx'?$reader_type='Excel2007':$reader_type='Excel5';//获取excel处理类型
		if (file_exists($filePath)) {
			$phpReader = \PHPExcel_IOFactory::createReader($reader_type);
			if (!$phpReader) {
				return array('status'=>0,'info'=>'抱歉！Excel文件不兼容。');
			}
		}else {
			return array('status'=>0,'info'=>'抱歉！Excel文件不存在。');
		}
		$phpExcel = $phpReader->load($filePath);
		$currentSheet = $phpExcel->getSheet();//默认获取第一个表
		$allColumn = $currentSheet->getHighestColumn();////取得一共有多少列
		$allRow = $currentSheet->getHighestRow();//取得一共有多少行
		$excelData = array();
		for($currentRow = 2;$currentRow<=$allRow;$currentRow++){
			for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
				$excelData[$currentRow][$currentColumn] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$currentSheet->getCell($currentColumn.$currentRow)->getValue());
				if (empty($excelData[$currentRow][$currentColumn])) {
					unset($excelData[$currentRow][$currentColumn]);
				}
			}
			if (empty($excelData[$currentRow])) {
				unset($excelData[$currentRow]);
			}
		}
		return array('status'=>1,'result'=>$excelData);
	}
	
	/**
	 * toIncreaseBatch function
	 * 批量导入报增
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function toIncreaseBatch(){
		if (IS_POST) {
			//构造测试数据start
			/*$baseData['filePath'] = '/Uploads/Company/0/55/temp/batchIncrease_9E044EF1-4759-4A65-A797-023E4D9BF23D.xls';
			//$baseData['templateId'] = 25;
			$baseData['productId'] = '24';
			$baseData['location'] = '14020100';
			$baseData['socRuleId'] = 54;
			$baseData['proRuleId'] = 55;*/
			//构造测试数据end
			
			$baseData = I('param.');
			
			$baseData['templateLocation'] = $baseData['location'];
			$baseData['location'] = ($baseData['location']/1000<<0)*1000;
			$filePath = '.'.$baseData['filePath'];
			unset($baseData['fileName']);
			unset($baseData['filePath']);
			
			$excelResult = $this->_handleExcel($filePath);//获取个人的工资信息
			//dump(get_excel_column_name(26));
			//dump(get_excel_column_index(get_excel_column_name(1323)));
			//dump(IntToChr(26));
			
			if (1 == $excelResult['status']) {
				$excelData = $excelResult['result'];
				$personBase = D('PersonBase');
				$personInsurance = D('PersonInsurance');
				$personInsuranceInfo = D('PersonInsuranceInfo');
				$templateRule = D('TemplateRule');
				$warrantyLocation = D('WarrantyLocation');
				$calculate = new \Common\Model\Calculate();
				//$serviceOrderInsurance = D('ServiceOrderInsurance');
				$batchResult = array();
				$batchResult['totalCount'] = 0;
				$batchResult['successCount'] = 0;
				//dump($excelData);
				foreach ($excelData as $rowNum => $rowData) {
					$rowNum --;
					$batchResult['totalCount'] ++;
					$batchResult['data'][$rowNum]['personName'] = $rowData['A'];
					$batchResult['data'][$rowNum]['cardNum'] = $rowData['B'];
					$batchResult['data'][$rowNum]['mobile'] = $rowData['C'];
					$batchResult['data'][$rowNum]['socPayDate'] = $rowData['D'];
					$batchResult['data'][$rowNum]['socAmount'] = $rowData['E'];
					$batchResult['data'][$rowNum]['proPayDate'] = $rowData['D'];
					$batchResult['data'][$rowNum]['proAmount'] = $rowData['F'];
					$batchResult['data'][$rowNum]['proPersonScale'] = $rowData['G'].'%';
					$batchResult['data'][$rowNum]['proCompanyScale'] = $rowData['H'].'%';
					
					$data = $baseData;
					$data['personName'] = $batchResult['data'][$rowNum]['personName'];
					$data['cardNum'] = $batchResult['data'][$rowNum]['cardNum'];
					$data['mobile'] = $batchResult['data'][$rowNum]['mobile'];
					$data['residenceLocation'] = '0';//未设置
					$data['residenceType'] = '0';//未设置
					
					$data['isBuySoc'] = ($batchResult['data'][$rowNum]['socAmount'] && $batchResult['data'][$rowNum]['socPayDate'])?'1':'0';
					//$data['socRuleId'] = '54';
					$data['socPayDate'] = $batchResult['data'][$rowNum]['socPayDate'];
					$data['socAmount'] = $batchResult['data'][$rowNum]['socAmount'];
					//$data['socCardNum'] = 'zby';
					$data['isBuyPro'] = ($batchResult['data'][$rowNum]['proAmount'] && $batchResult['data'][$rowNum]['proPayDate'])?'1':'0';
					//$data['proRuleId'] = '55';
					$data['proPayDate'] = $batchResult['data'][$rowNum]['proPayDate'];
					$data['proAmount'] = $batchResult['data'][$rowNum]['proAmount'];
					//$data['proCardNum'] = 'zby';
					$data['proCompanyScale'] = $batchResult['data'][$rowNum]['proPersonScale'];
					$data['proPersonScale'] = $batchResult['data'][$rowNum]['proCompanyScale'];
					//dump($data);die;
					
					if (validateIDCard($data['cardNum'])) {
						$personBaseData = array();
						$personBaseData['user_id'] = $this->mCuid;
						$personBaseData['person_name'] = $data['personName'];
						$personBaseData['card_num'] = $data['cardNum'];
						$personBaseData['mobile'] = $data['mobile'];
						$personBaseData['residence_location'] = $data['residenceLocation'];
						$personBaseData['residence_type'] = $data['residenceType'];
						$personBaseData['birthday'] = get_birthday_by_idCard($data['cardNum']);
						//dump($personBaseData);
						$personBase->startTrans();
						$personBaseResult = $personBase->savePersonBase($personBaseData);
						if ($personBaseResult) {
							$personBaseId = $personBaseResult;
							//dump($personBaseId);
							$personInsuranceResult = $personInsurance->getInsuranceStatus($this->mCuid,$personBaseId);
							if ($personInsuranceResult) {
								if ($personInsuranceResult['increase']) {
									//计算订单月份
									$template = D('Template');
									//$templateResult = $template->getTemplateByCondition(array('location'=>$data['location'],'state'=>1));
									$templateResult = $template->getTemplateByCondition(array('location'=>$data['templateLocation'],'state'=>1));
									if ($templateResult) {
										$templateResult['deadline'] = array(1=>$templateResult['soc_deadline'],2=>$templateResult['pro_deadline']);
										$templateResult['payment_type'] = array(1=>$templateResult['soc_payment_type'],2=>$templateResult['pro_payment_type']);
										$templateResult['payment_month'] = array(1=>$templateResult['soc_payment_month'],2=>$templateResult['pro_payment_month']);
										
										//$orderDate[1] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($templateResult['deadline'][1],2,'0',STR_PAD_LEFT)))))?date('Ym',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Ym',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
										//$orderDate[2] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($templateResult['deadline'][2],2,'0',STR_PAD_LEFT)))))?date('Ym',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Ym',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
										$orderDate[1] = get_handle_month($templateResult['deadline'][1]);
										$orderDate[2] = get_handle_month($templateResult['deadline'][2]);
										$orderDateStr[1] = substr_replace($orderDate[1],'-',4,0);
										$orderDateStr[2] = substr_replace($orderDate[2],'-',4,0);
										//dump($personInsuranceResult);
										$personInsuranceInfoData = array();
										$personInsuranceInfoData['user_id'] = $this->mCuid;
										$personInsuranceInfoData['base_id'] = $personBaseId;
										$personInsuranceInfoData['product_id'] = $data['productId'];
										$personInsuranceInfoData['location'] = $data['location'];
										$personInsuranceInfoData['template_location'] = $data['templateLocation'];
										//$personInsuranceInfoData['end_month'] = 0;
										//$personInsuranceInfoData['audit'] = 0;//未审核
										$personInsuranceInfoData['state'] = 1;//报增
										$personInsuranceInfoData['create_time'] = date('Y-m-d H:i:s');
										$personInsuranceInfoData['modify_time'] = date('Y-m-d H:i:s');
										
										$personInsuranceInfoArray = array();
										if (1 == $data['isBuySoc']) {
											$personInsuranceInfoArray[1] = $personInsuranceInfoData;
											$personInsuranceInfoArray[1]['rule_id'] = $data['socRuleId'];
											$personInsuranceInfoArray[1]['start_month'] = string_to_number($data['socPayDate']);
											$personInsuranceInfoArray[1]['handle_month'] = $orderDate[1];
											$personInsuranceInfoArray[1]['amount'] = $data['socAmount'];
											$personInsuranceInfoArray[1]['payment_type'] = 1;
											$personInsuranceInfoArray[1]['payment_info'] = json_encode(array('cardno'=>$data['socCardNum']));
										}else {
											$personBase->rollback();
											$batchResult['data'][$rowNum]['info'] = '社保数据必填！';
											continue;
										}
										if (1 == $data['isBuyPro']) {
											$personInsuranceInfoArray[2] = $personInsuranceInfoData;
											$personInsuranceInfoArray[2]['rule_id'] = $data['proRuleId'];
											$personInsuranceInfoArray[2]['start_month'] = string_to_number($data['proPayDate']);
											$personInsuranceInfoArray[2]['handle_month'] = $orderDate[2];
											$personInsuranceInfoArray[2]['amount'] = $data['proAmount'];
											$personInsuranceInfoArray[2]['payment_type'] = 2;
											$personInsuranceInfoArray[2]['payment_info'] = json_encode(array('companyScale'=>$data['proCompanyScale'],'personScale'=>$data['proPersonScale'],'cardno'=>$data['proCardNum']));
										}else {
											$tempPersonInsuranceInfoData = $personInsuranceInfoData;
											$tempPersonInsuranceInfoData['state'] = 0;//未参保
											//$tempPersonInsuranceInfoData['product_id'] = $data['productId'];
											$tempPersonInsuranceInfoData['location'] = 0;
											$tempPersonInsuranceInfoData['template_location'] = 0;
											$tempPersonInsuranceInfoData['rule_id'] = 0;
											$tempPersonInsuranceInfoData['start_month'] = 0;
											$tempPersonInsuranceInfoData['handle_month'] = $orderDate[2];
											$tempPersonInsuranceInfoData['amount'] = 0;
											$tempPersonInsuranceInfoData['payment_type'] = 2;
											$tempPersonInsuranceInfoData['payment_info'] = json_encode(array('companyScale'=>null,'personScale'=>null,'cardno'=>null));
											//if (!$personInsuranceInfo->add($tempPersonInsuranceInfoData)){
											if (!$personInsuranceInfo->addPersonInsurance($tempPersonInsuranceInfoData)){
												$personBase->rollback();
												$batchResult['data'][$rowNum]['info'] = '系统内部错误！';
												//$this->ajaxReturn(array('status'=>0,'info'=>'系统内部错误！'));
											}
										}
										//dump($personInsuranceInfoArray);
										if ($personInsuranceInfoArray) {
											$personInsuranceInfoResult = array();
											foreach ($personInsuranceInfoArray as $key => $value) {
												$personInsuranceInfoResult['rule'][$key] = $templateRule->getById($value['rule_id']);
												//dump($value['rule_id']);
												//dump($personInsuranceInfoResult['rule'][$key]);
												if ($personInsuranceInfoResult['rule'][$key]) {
													//$personInsuranceInfoResult['id'][$key] = $personInsuranceInfo->add($value);
													$personInsuranceInfoResult['id'][$key] = $personInsuranceInfo->addPersonInsurance($value);
													$personInsuranceInfoResult['successCount'] += $personInsuranceInfoResult['id'][$key]?1:0;
												}else {
													$personBase->rollback();
													$batchResult['data'][$rowNum]['info'] = '规则参数错误！';
													//$this->ajaxReturn(array('status'=>0,'info'=>'规则参数错误！'));
												}
											}
											if ($personInsuranceInfoResult['successCount'] == count($personInsuranceInfoArray)) {
												//计算订单月份
												$serviceProductOrder = D('ServiceProductOrder');
												$serviceProductOrderResult = $serviceProductOrder->getEffectiveServiceProductOrderByProductId($this->mCuid,$data['productId']);
												if ($serviceProductOrderResult) {
													//dump($serviceProductOrderResult);
														//判断当前月是否存在订单，没有则新增
														/*$serviceOrder = D('ServiceOrder');
														$serviceOrderResult = $serviceOrder->getServiceOrderByConditon(array('user_id'=>$this->mCuid,'product_id'=>$data['productId'],'order_date'=>$orderDate,'payment_type'=>1));
														if ($serviceOrderResult) {
															$serviceOrderId = $serviceOrderResult['id'];
														}else {
															$serviceOrderId = $serviceOrder->add(array('order_no'=>create_order_sn(),'user_id'=>$this->mCuid,'company_id'=>$serviceProductOrderResult['company_id'],'product_id'=>$data['productId'],'order_date'=>$orderDate,'price'=>0,'payment_type'=>1,'state'=>0,'diff_amount'=>0,'create_time'=>date('Y-m-d H:i:s')));
														}*/
														
														//计算缴纳月份，补缴月份
														$serviceInsuranceDetailBaseData = array();
														$serviceInsuranceDetailBaseData['type'] = 1;//报增
														$serviceInsuranceDetailBaseData['state'] = 0;//待支付
														$serviceInsuranceDetailBaseData['create_time'] = date('Y-m-d H:i:s');
														$serviceInsuranceDetailBaseData['modify_time'] = $serviceInsuranceDetailBaseData['create_time'];
														//计算服务费
														$warrantyLocationResult = $warrantyLocation->getWarrantyLocationByCondition(array('service_product_order_id'=>$serviceProductOrderResult['id'],'location'=>$data['location']));
														$servicePrice = array();
														$servicePrice[1] = $warrantyLocationResult['soc_service_price'];
														$servicePrice[2] = $warrantyLocationResult['pro_service_price'];
														foreach ($personInsuranceInfoResult['id'] as $key => $value) {
															$endMonth = 1 == $templateResult['payment_type'][$key]?$orderDate[$key]:date('Ym',strtotime('+1 month', strtotime($orderDateStr[$key])));//1缴当月 2缴次月
															$monthNum = get_different_by_month($personInsuranceInfoArray[$key]['start_month'],$endMonth);
															$replenishMonthNum = get_different_by_month($personInsuranceInfoArray[$key]['start_month'],$orderDate[$key])-1;
															if ($monthNum > 0) {
																//计算缴纳费用
																$productTemplateRuleResult = $templateRule->getById($personInsuranceInfoArray[$key]['rule_id']);
																$disRuleResult = $templateRule->getTemplateRuleByCondition(array('template_id'=>$templateResult['id'],'company_id'=>$productTemplateRuleResult['company_id'],'type'=>3,'state'=>1));
																//判断基数
																$rule = json_decode($productTemplateRuleResult['rule'],true);
																//dump($rule);
																if ($personInsuranceInfoArray[$key]['amount'] >= $rule['min'] && $personInsuranceInfoArray[$key]['amount'] <= $rule['max']) {
																	$scaleErrorInfo = '';
																	if (2 == $key) {
																		$scaleErrorArray = array();
																		if (!check_scale(trim($data['proCompanyScale'],'%'),$rule['company'])) {
																			$scaleErrorArray['type'][] = '单位';
																			$scaleErrorArray['rule'][] = '单位比例范围为'.$rule['company'];
																		}
																		if (!check_scale(trim($data['proPersonScale'],'%'),$rule['person'])) {
																			$scaleErrorArray['type'][] .= '个人';
																			$scaleErrorArray['rule'][] = '个人比例范围为'.$rule['person'];
																		}
																		if ($scaleErrorArray) {
																			$scaleErrorInfo .= '公积金'.implode('与',$scaleErrorArray['type']).'缴纳比例错误！'.'('.implode(',',$scaleErrorArray['rule']).')';
																		}
																		//dump($scaleErrorInfo);
																	}
																	
																	if (!$scaleErrorInfo) {
																		//$servicePrice = 1 == $key?$warrantyLocationResult['ss_service_price']:0;
																		$json = json_decode($personInsuranceInfoArray[$key]['payment_info'],true);
																		$json['amount'] = $personInsuranceInfoArray[$key]['amount'];
																		$json['month'] = 1;
																		$json = json_encode($json);
																		//最大补缴月份
																		if ($replenishMonthNum <= $templateResult['payment_month'][$key]) {
																			//缴纳年月数组
																			$paymentMonthArray = array();
																			for ($i=0; $i < $monthNum; $i++) {
																				$paymentMonthArray[] = date('Ym',strtotime("+{$i} month",strtotime(substr_replace($personInsuranceInfoArray[$key]['start_month'],'-',4,0))));
																			}
																			//添加参保订单表数据
																			//$serviceOrderInsuranceData = $serviceInsuranceDetailBaseData;
																			//$serviceOrderInsuranceData['service_order_id'] = $serviceOrderId;
																			//$serviceOrderInsuranceData['insurance_id'] = $value;
																			//$serviceOrderInsuranceData['amount'] = $personInsuranceInfoArray[$key]['amount'];
																			//$serviceOrderInsuranceData['pay_date'] = implode(',',$paymentMonthArray);
																			//$serviceOrderInsuranceResult = $serviceOrderInsurance->add($serviceOrderInsuranceData);
																			$serviceInsuranceDetail = D('ServiceInsuranceDetail');
																			$serviceInsuranceDetailResult = array();
																			$serviceInsuranceDetailResult['monthNum'] += $monthNum;
																			//更新数据
																			$personInsuranceInfoUpdateResult = $personInsuranceInfo->where(array('id'=>$value))->save(array('pay_date'=>implode(',',$paymentMonthArray)));
																			//if ($serviceOrderInsuranceResult) {
																			if (false !== $personInsuranceInfoUpdateResult) {
																				for ($i=0; $i < $monthNum; $i++) { 
																					$payDate = date('Ym',strtotime("+{$i} month",strtotime(substr_replace($personInsuranceInfoArray[$key]['start_month'],'-',4,0))));
																					$replenish = $payDate < $endMonth?1:0;//是否补缴
																					$calculateResult = json_decode($calculate->detail($productTemplateRuleResult['rule'], $json, $key,$disRuleResult['rule'],$replenish),true);
																					if (0 == $calculateResult['state']) {
																						//$price = $calculateResult['data']['company']+$calculateResult['data']['person']+$calculateResult['data']['pro_cost'];
																						$price = $calculateResult['data']['company']+$calculateResult['data']['person'];
																						//$calculateResult['data']['service_price'] = $servicePrice[$key];
																						$serviceInsuranceDetailData = $serviceInsuranceDetailBaseData;
																						$serviceInsuranceDetailData['payment_type'] = $key;//参保类型
																						$serviceInsuranceDetailData['insurance_info_id'] = $value;
																						//$serviceInsuranceDetailData['service_order_insurance_id'] = $serviceOrderInsuranceResult;
																						$serviceInsuranceDetailData['price'] = $price;
																						$serviceInsuranceDetailData['service_price'] = $servicePrice[$key];
																						$serviceInsuranceDetailData['amount'] = $personInsuranceInfoArray[$key]['amount'];
																						$serviceInsuranceDetailData['pay_date'] = $payDate;
																						$serviceInsuranceDetailData['handle_month'] = $personInsuranceInfoArray[$key]['handle_month'];
																						$serviceInsuranceDetailData['replenish'] = $replenish;
																						$serviceInsuranceDetailData['rule_id'] = $personInsuranceInfoArray[$key]['rule_id'];
																						//$serviceInsuranceDetailData['rule_detail'] = $productTemplateRuleResult['rule'];
																						$serviceInsuranceDetailData['payment_info'] = $personInsuranceInfoArray[$key]['payment_info'];
																						$serviceInsuranceDetailData['insurance_detail'] = json_encode($calculateResult['data'],JSON_UNESCAPED_UNICODE);//计算结果
																						$serviceInsuranceDetailData['current_detail'] = $serviceInsuranceDetailData['insurance_detail'];
																						$serviceInsuranceDetailResult['id'][$key][$serviceInsuranceDetailData['pay_date']] = $serviceInsuranceDetail->add($serviceInsuranceDetailData);
																						$serviceInsuranceDetailResult['successCount'] += $serviceInsuranceDetailResult['id'][$key][$serviceInsuranceDetailData['pay_date']]?1:0;
																					}else {
																						$personBase->rollback();
																						$batchResult['data'][$rowNum]['info'] = '参保数据计算错误！';
																					}
																				}
																			}else {
																				$personBase->rollback();
																				$batchResult['data'][$rowNum]['info'] = '报增失败！';
																				//$this->ajaxReturn(array('status'=>0,'info'=>'报增失败！'));
																			}
																		}else {
																			$personBase->rollback();
																			$batchResult['data'][$rowNum]['info'] = '超出最大补缴月份！';
																			//$this->ajaxReturn(array('status'=>0,'info'=>'超出最大补缴月份！'));
																		}
																	}else {
																		$personBase->rollback();
																		$batchResult['data'][$rowNum]['info'] .= $scaleErrorInfo;
																	}
																}else {
																	$personBase->rollback();
																	$batchResult['data'][$rowNum]['info'] .= get_code_value($key,'InsuranceType').'超出基数范围('.$rule['min'].'到'.$rule['max'].')！';
																}
															}else {
																$personBase->rollback();
																$batchResult['data'][$rowNum]['info'] .= '起缴月份错误（大于'.$endMonth.'）！';
															}
														}
														//dump($serviceInsuranceDetailResult);
														if ($serviceInsuranceDetailResult &&  $serviceInsuranceDetailResult['successCount'] == $serviceInsuranceDetailResult['monthNum']) {
															$personBase->commit();
															/*
															//保存身份证
															$path = mkFilePath($personBaseId,'./Uploads/Person/','IDCard');
															//保存身份证正面照片
															if ($idCardFrontFile = I('idCardFrontFile','')) {
																$idCardFrontFile = reset(explode('?',$idCardFrontFile));
																if ('/Application/Company/Assets/v2/images/idcard1.png' != $idCardFrontFile) {
																	$idCardFrontFileResult = move('.'.$idCardFrontFile,$path.'idCardFront.jpg');
																}
															}
															//保存身份证反面照片
															if ($idCardBackFile = I('idCardBackFile','')) {
																$idCardBackFile = reset(explode('?',$idCardBackFile));
																if ('/Application/Company/Assets/v2/images/idcard2.png' != $idCardBackFile) {
																	$idCardBackFileResult = move('.'.$idCardBackFile,$path.'idCardBack.jpg');
																}
															}
															*/
															$batchResult['successCount'] ++;
															//$batchResult['data'][$rowNum]['info'] = '';
															//$this->ajaxReturn(array('status'=>1,'info'=>'报增成功！'));
														}else if ($batchResult['data'][$rowNum]['info']) {
															//不处理
														}else {
															$personBase->rollback();
															$batchResult['data'][$rowNum]['info'] = '报增失败！';
															//$this->ajaxReturn(array('status'=>0,'info'=>'报增失败！'));
														}
												}else {
													$personBase->rollback();
													$batchResult['data'][$rowNum]['info'] = '产品订单错误！';
													//$this->ajaxReturn(array('status'=>0,'info'=>'产品订单错误！'));
												}
											}else {
												$personBase->rollback();
												$batchResult['data'][$rowNum]['info'] = '系统内部错误！';
												//$this->ajaxReturn(array('status'=>0,'info'=>'系统内部错误！'));
											}
										}else {
											$personBase->rollback();
											$batchResult['data'][$rowNum]['info'] = '请填写要参保的项目！';
											//$this->ajaxReturn(array('status'=>0,'info'=>'请选择要参保的项目！'));
										}
									}else {
										$personBase->rollback();
										$batchResult['data'][$rowNum]['info'] = '系统缴费模板错误！';
										//$this->ajaxReturn(array('status'=>0,'info'=>'系统缴费模板错误！'));
									}
								}else {
									$personBase->rollback();
									$batchResult['data'][$rowNum]['info'] = '参保状态错误！';
									//$this->ajaxReturn(array('status'=>0,'info'=>'参保状态错误！'));
								}
							}else {
								$personBase->rollback();
								$batchResult['data'][$rowNum]['info'] = '系统内部错误！';
								//$this->ajaxReturn(array('status'=>0,'info'=>'系统内部错误！'));
							}
						}else {
							$personBase->rollback();
							$batchResult['data'][$rowNum]['info'] = $personBase->getError();
							//$this->ajaxReturn(array('status'=>0,'info'=>$personBase->getError()));
						}
					}else {
						$batchResult['data'][$rowNum]['info'] = '身份证错误！';
					}
				}
				//dump($batchResult);
				//unlink($filePath);//删除文件
				if ($batchResult['data']) {
					//统计服务人次
					$serviceProduct = D('ServiceProduct');
					$serviceProductSaveResult = $serviceProduct->where(array('id'=>$data['productId']))->setInc('service_num',$batchResult['successCount']);
					$this->ajaxReturn(array('status'=>1,'result'=>$batchResult));
				}else {
					$this->ajaxReturn(array('status'=>0,'info'=>'Excel数据为空！'));
				}
			}else {
				$this->error($excelResult['info']);
			}
		}else {
			//获取购买的产品订单信息
			$serviceProductOrder = D('ServiceProductOrder');
			$serviceProductOrderResult = $serviceProductOrder->getEffectiveServiceProductOrder($this->mCuid);
			$this->assign('serviceProductOrderResult',$serviceProductOrderResult);
			$this->display();
		}
	}
	
	/**
	 * cancel function
	 * 撤销
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function cancel(){
		if (IS_POST) {
			//构造测试数据start
			//$data['id'] = '621,622';
			//构造测试数据end
			
			$data = I('post.');
			if ($data) {
				$data['id'] = explode(',',$data['id']);
				$data['user_id'] = $this->mCuid;
				$personInsuranceInfo = D('PersonInsuranceInfo');
				$personInsuranceInfoResult = $personInsuranceInfo->cancel($data);
				//dump($personInsuranceInfo);
				//dump($personInsuranceInfoResult);
				if (false !== $personInsuranceInfoResult) {
					$this->ajaxReturn(array('status'=>1,'info'=>$personInsuranceInfoResult['info']));
				}else {
					$this->ajaxReturn(array('status'=>0,'info'=>$personInsuranceInfo->getError()));
				}
			}else {
				$this->error('非法参数！');
			}
		}else {
			$this->error('非法操作！');
		}
	}
	
	/**
	 * createPayOrder function
	 * 创建支付订单
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function createPayOrder(){
		if (IS_POST) {
			//构造测试数据start
			$data['id'] = array('628,627','626,625');
			//构造测试数据end
			
			$data = I('post.');
			if ($data) {
				$data['id'] = implode(',',$data['id']);
				$data['user_id'] = $this->mCuid;
				$data['type'] = 2;//社保公积金订单
				$payOrder = D('PayOrder');
				$payOrderResult = $payOrder->createPayOrder($data);
				//dump($payOrder);
				//dump($payOrderResult);
				if (false !== $payOrderResult) {
					$this->ajaxReturn(array('status'=>1,'info'=>$payOrderResult['info'],'url'=>$payOrderResult['url']));
				}else {
					$this->ajaxReturn(array('status'=>0,'info'=>$payOrder->getError()));
				}
			}else {
				$this->error('非法参数！');
			}
		}else {
			$this->error('非法操作！');
		}
	}
	
	
	
	
	
	/**
	 * getReduceItem function
	 * 获取个人报减项目
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getReduceItem(){
		if (IS_POST) {
			$baseId = I('baseId/d');
			if ($baseId > 0) {
				$condition = array();
				$condition['user_id'] = $this->mCuid;
				$condition['base_id'] = $baseId;
				$personInsurance = D('personInsurance');
				$personInsuranceResult = $personInsurance->getPersonInsuranceByCondition($condition,false);
					//dump($condition);
					//dump($personInsuranceResult);
				if ($personInsuranceResult) {
					//dump($personInsuranceResult);
					$result = array();
					$result[1] = 2 == $personInsuranceResult['socpi_state']?true:false;
					$result[2] = 2 == $personInsuranceResult['propi_state']?true:false;
					$this->ajaxReturn(array('status'=>1,'result'=>$result));
				}else {
					$this->error($personInsurance->getError());
					$this->error('参保数据异常！');
				}
			}else {
				$this->error('参数错误！');
			}
		}else {
			$this->error('非法操作！');
		}
	}
	
	/**
	 * toReduce function
	 * 报减
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function toReduce(){
		if (IS_POST) {
			$baseId = I('post.baseId/d',0);
			$reduceData = array();
			$reduceData[1]['isCancel'] = (1 == I('post.socIsCancel',0))?true:false;
			$reduceData[1]['remark'] = I('post.socNote');
			$reduceData[2]['isCancel'] = (1 == I('post.proIsCancel',0))?true:false;
			$reduceData[2]['remark'] = I('post.proNote');
			
			if ($baseId > 0) {
				$condition = array();
				$condition['user_id'] = $this->mCuid;
				$condition['base_id'] = $baseId;
				$personInsurance = D('personInsurance');
				$payOrder = D('PayOrder');
				$personInsuranceResult = $personInsurance->getPersonInsuranceByCondition($condition,false);
				if ($personInsuranceResult) {
					//dump($personInsuranceResult);
					$reduceData[1]['state'] = 2 == $personInsuranceResult['socpi_state']?true:false;
					$reduceData[2]['state'] = 2 == $personInsuranceResult['propi_state']?true:false;
					
					//dump($reduceData);
					if (empty($reduceData[1]['isCancel']) && empty($reduceData[2]['isCancel'])) {
						$this->error('请选择报减服务！');
					}else if($reduceData[1]['isCancel'] && !$reduceData[1]['state']){
						$this->error('该参保人社保未在保！');
					}else if($reduceData[2]['isCancel'] && !$reduceData[2]['state']){
						$this->error('该参保人公积金未在保！');
					}else {
						if ($reduceData[1]['state'] || $reduceData[2]['state']) {
							//获取参保信息
							$personInsuranceInfo = D('PersonInsuranceInfo');
							//$personInsuranceInfoResult = $personInsuranceInfo->getServiceOrderDetailByCondition(array('user_id'=>$this->mCuid,'base_id'=>$baseId),2);
							$personInsuranceInfoResult = $personInsuranceInfo->getWarrantyServiceOrderDetail(array('user_id'=>$this->mCuid,'base_id'=>$baseId));
							
							//dump($personInsuranceInfoResult);
							//die;
							$template = D('Template');
							$serviceInsuranceDetail = D('ServiceInsuranceDetail');
							$result = array();
							$result['totalCount'] = 0;
							$result['successCount'] = 0;
							$nowTime = date('Y-m-d H:i:s');
							$personInsuranceInfo->startTrans();
							foreach ($reduceData as $key => $value) {
								//报减勾选的项目
								if ($value['isCancel'] && $value['state']) {
									$result['totalCount']++;//自增总数
									if ($personInsuranceInfoResult[$key]) {
										$templateResult = $template->getTemplateByRuleId($personInsuranceInfoResult[$key]['rule_id']);
										if ($templateResult) {
											$templateResult['deadline'] = array(1=>$templateResult['soc_deadline'],2=>$templateResult['pro_deadline']);
											$templateResult['payment_type'] = array(1=>$templateResult['soc_payment_type'],2=>$templateResult['pro_payment_type']);
											$templateResult['payment_month'] = array(1=>$templateResult['soc_payment_month'],2=>$templateResult['pro_payment_month']);
											
											//$orderDate[1] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($templateResult['deadline'][1],2,'0',STR_PAD_LEFT)))))?date('Ym',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Ym',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
											//$orderDate[2] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($templateResult['deadline'][1],2,'0',STR_PAD_LEFT)))))?date('Ym',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Ym',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
											$orderDate[1] = get_handle_month($templateResult['deadline'][1]);
											$orderDate[2] = get_handle_month($templateResult['deadline'][2]);
											$orderDateStr[1] = substr_replace($orderDate[1],'-',4,0);
											$orderDateStr[2] = substr_replace($orderDate[2],'-',4,0);
											//获取当月缴还是次月缴状态并计算报减月份
											$paymentMonth = 1 == $templateResult['payment_type'][1]?$orderDate[$key]:date('Ym',strtotime('+1 month', strtotime(int_to_date($orderDate[$key],'-'))));//1缴当月 2缴次月
											//$orderDate[1] = '201610';
											//$orderDate[2] = '201610';
											
											//支付订单数据
											$payOrderData = array();
											$payOrderData['user_id'] = $this->mCuid;
											$payOrderData['company_id'] = $personInsuranceInfoResult[$key]['company_id'];
											$payOrderData['type'] = 2;
											$payOrderData['location'] = ($personInsuranceInfoResult[$key]['location']/1000<<0)*1000;
											$payOrderData['amount'] = 0;
											$personInsuranceInfoExistResult = $personInsuranceInfo->field('id,state,operate_state')->where(array('insurance_id'=>$personInsuranceInfoResult[$key]['insurance_id'],'handle_month'=>$orderDate[$key],'payment_type'=>$personInsuranceInfoResult[$key]['payment_type']))->order('create_time desc')->find();
											if (!$personInsuranceInfoExistResult || ($personInsuranceInfoExistResult && 2 == $personInsuranceInfoExistResult['state'] && in_array($personInsuranceInfoExistResult['operate_state'],array(2,3)))) {
												if($personInsuranceInfoExistResult && 3 == $personInsuranceInfoExistResult['state']){
													$personInsuranceInfo->rollback();
													$this->error('请勿重复报减！');
												}else if($personInsuranceInfoExistResult && 2 == $personInsuranceInfoExistResult['state'] && in_array($personInsuranceInfoExistResult['operate_state'],array(2,3))) {
													$handleMonth = date('Ym',strtotime('+1 month',strtotime(int_to_date($orderDate[$key],'-'))));
													$paymentMonth = date('Ym',strtotime('+1 month',strtotime(int_to_date($paymentMonth,'-'))));;
													
													$nextPersonInsuranceInfoExistResult = $personInsuranceInfo->field('id,state,operate_state')->where(array('insurance_id'=>$personInsuranceInfoResult[$key]['insurance_id'],'handle_month'=>$handleMonth,'payment_type'=>$personInsuranceInfoResult[$key]['payment_type']))->find();
													if ($nextPersonInsuranceInfoExistResult) {
														if (3 == $nextPersonInsuranceInfoExistResult['state']) {
															$personInsuranceInfo->rollback();
															$this->error('请勿重复报减！');
														}else{
															$personInsuranceInfo->rollback();
															$this->error('数据错误!');
														}
													}
												}else {
													$handleMonth = $orderDate[$key];
												}
												$payDeadline = date('Y-m-d',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(int_to_date($handleMonth,'-').'-'.sprintf('%02d',$templateResult['soc_deadline']))));
												//写入支付订单数据
												$payOrderData['handle_month'] = $handleMonth;
												$payOrderData['pay_deadline'] = $payDeadline;
												//$payOrderId = $payOrder->savePayOrder($payOrderData);
												$payOrderId = 0;
												
												//$personInsuranceInfoData = $personInsuranceInfoResult[$key];
												//unset($personInsuranceInfoData['id']);
												//unset($personInsuranceInfoData['pay_order_id']);
												$personInsuranceInfoData = array();
												$personInsuranceInfoData['insurance_id'] = $personInsuranceInfoResult[$key]['insurance_id'];
												$personInsuranceInfoData['user_id'] = $personInsuranceInfoResult[$key]['user_id'];
												$personInsuranceInfoData['product_id'] = $personInsuranceInfoResult[$key]['product_id'];
												$personInsuranceInfoData['base_id'] = $personInsuranceInfoResult[$key]['base_id'];
												$personInsuranceInfoData['rule_id'] = $personInsuranceInfoResult[$key]['rule_id'];
												//$personInsuranceInfoData['pay_order_id'] = $payOrderId;
												$personInsuranceInfoData['location'] = $personInsuranceInfoResult[$key]['location'];
												$personInsuranceInfoData['template_location'] = $personInsuranceInfoResult[$key]['template_location'];
												$personInsuranceInfoData['amount'] = $personInsuranceInfoResult[$key]['amount'];
												$personInsuranceInfoData['payment_info'] = $personInsuranceInfoResult[$key]['payment_info'];
												$personInsuranceInfoData['payment_type'] = $personInsuranceInfoResult[$key]['payment_type'];
												
												$personInsuranceInfoData['start_month'] = $paymentMonth;
												$personInsuranceInfoData['handle_month'] = $handleMonth;
												$personInsuranceInfoData['pay_date'] = $paymentMonth;
												$personInsuranceInfoData['remark'] = $value['remark'];
												$personInsuranceInfoData['state'] = 3;//报减
												$personInsuranceInfoData['operate_state'] = 2;//已付款,待办理
												$personInsuranceInfoData['create_time'] = $nowTime;
												$personInsuranceInfoData['modify_time'] = $nowTime;
												$personInsuranceInfoAddResult = $personInsuranceInfo->add($personInsuranceInfoData);
												if ($personInsuranceInfoAddResult) {
													$piiId = $personInsuranceInfoAddResult;
													$serviceInsuranceDetailData = array();
													$serviceInsuranceDetailData['insurance_info_id'] = $piiId;
													$serviceInsuranceDetailData['pay_order_id'] = 0;
													$serviceInsuranceDetailData['rule_id'] = $personInsuranceInfoResult[$key]['rule_id'];
													$serviceInsuranceDetailData['price'] = 0;
													$serviceInsuranceDetailData['service_price'] = 0;
													$serviceInsuranceDetailData['payment_type'] = $personInsuranceInfoResult[$key]['payment_type'];
													$serviceInsuranceDetailData['payment_info'] = $personInsuranceInfoResult[$key]['payment_info'];
													//$serviceInsuranceDetailData['insurance_detail'] = reset($personInsuranceInfoResult[$key]['data'])['insurance_detail'];
													//$serviceInsuranceDetailData['current_detail'] = $serviceInsuranceDetailData['insurance_detail'];
													$serviceInsuranceDetailData['insurance_detail'] = '';
													$serviceInsuranceDetailData['current_detail'] = '';
													$serviceInsuranceDetailData['type'] = 3;//报减
													$serviceInsuranceDetailData['amount'] = $personInsuranceInfoResult[$key]['amount'];
													$serviceInsuranceDetailData['pay_date'] = $paymentMonth;
													$serviceInsuranceDetailData['handle_month'] = $handleMonth;
													$serviceInsuranceDetailData['note'] = $value['remark'];
													$serviceInsuranceDetailData['state'] = 2;//已付款,待办理
													$serviceInsuranceDetailData['create_time'] = $nowTime;
													$serviceInsuranceDetailData['modify_time'] = $nowTime;
													$serviceInsuranceDetailData['replenish'] = 0;//非补缴
													$serviceInsuranceDetailData['is_hang_up'] = 0;//非挂起
													$serviceInsuranceDetailResult = $serviceInsuranceDetail->add($serviceInsuranceDetailData);
													if ($serviceInsuranceDetailResult) {
														$result['successCount']++;//自增成功数
													}
												}
											}else if ($personInsuranceInfoExistResult && 2 == $personInsuranceInfoExistResult['state'] && !in_array($personInsuranceInfoExistResult['operate_state'],array(2,3))){
												//如果有未支付的在保数据,则更新数据
												$piiId = $personInsuranceInfoExistResult['id'];
												$handleMonth = $orderDate[$key];
												$payDeadline = date('Y-m-d',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(int_to_date($handleMonth,'-').'-'.sprintf('%02d',$templateResult['soc_deadline']))));
												//写入支付订单数据
												$payOrderData['handle_month'] = $handleMonth;
												$payOrderData['pay_deadline'] = $payDeadline;
												//$payOrderId = $payOrder->savePayOrder($payOrderData);
												$payOrderId = 0;
												
												$personInsuranceInfoData = array();
												//$personInsuranceInfoData['pay_order_id'] = $payOrderId;
												$personInsuranceInfoData['start_month'] = $paymentMonth;
												//$personInsuranceInfoData['handle_month'] = $handleMonth;
												$personInsuranceInfoData['pay_date'] = $paymentMonth;
												$personInsuranceInfoData['remark'] = $value['remark'];
												$personInsuranceInfoData['state'] = 3;//报减
												$personInsuranceInfoData['operate_state'] = 2;//已付款,待办理
												$personInsuranceInfoData['modify_time'] = $nowTime;
												$personInsuranceInfoSaveResult = $personInsuranceInfo->where(array('id'=>$piiId))->save($personInsuranceInfoData);
												if (false !== $personInsuranceInfoSaveResult) {
													//查找pay_order_id
													$serviceInsuranceDetailResult = $serviceInsuranceDetail->field('pay_order_id')->where(array('insurance_info_id'=>$piiId,'pay_order_id'=>array('gt',0)))->find();
													$payOrderId = $serviceInsuranceDetailResult['pay_order_id'];
													
													$serviceInsuranceDetailData = array();
													//$serviceInsuranceDetailData['insurance_info_id'] = $piiId;
													$serviceInsuranceDetailData['pay_order_id'] = 0;
													$serviceInsuranceDetailData['price'] = 0;
													$serviceInsuranceDetailData['service_price'] = 0;
													$serviceInsuranceDetailData['type'] = 3;//报减
													$serviceInsuranceDetailData['insurance_detail'] = '';
													$serviceInsuranceDetailData['current_detail'] = '';
													$serviceInsuranceDetailData['note'] = $value['remark'];
													$serviceInsuranceDetailData['state'] = 2;//已付款,待办理
													$serviceInsuranceDetailData['modify_time'] = $nowTime;
													$serviceInsuranceDetailData['replenish'] = 0;//非补缴
													$serviceInsuranceDetailData['is_hang_up'] = 0;//非挂起
													$serviceInsuranceDetailSaveResult = $serviceInsuranceDetail->where(array('insurance_info_id'=>$piiId))->save($serviceInsuranceDetailData);
													if (false !== $serviceInsuranceDetailSaveResult) {
														//$amount = $personInsuranceInfo->alias('pii')->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.insurance_info_id=pii.id')->where(array('pii.pay_order_id'=>$payOrderId,'sid.state'=>array('gt',0)))->sum('sid.price + sid.service_price');
														$amount = $serviceInsuranceDetail->where(array('pay_order_id'=>$payOrderId,'state'=>array('gt',0)))->sum('price + service_price');
														$payOrderSaveResult = $payOrder->where(array('id'=>$payOrderId))->save(array('amount'=>$amount));
														if (false !== $payOrderSaveResult) {
															$result['successCount']++;//自增成功数
														}
													}
												}
											}else if ($personInsuranceInfoExistResult && 3 == $personInsuranceInfoExistResult['state'] && !in_array($personInsuranceInfoExistResult['operate_state'],array(-8,-9))){
												$personInsuranceInfo->rollback();
												$this->error('请勿重复报减！');
											}else {
												$personInsuranceInfo->rollback();
												$this->error('当前时间不能报减！');
											}
										}else {
											$personInsuranceInfo->rollback();
											$this->error('参保数据错误！');
										}
									}else {
										$personInsuranceInfo->rollback();
										$this->error('参保数据错误！');
									}
								}
							}
							if ($result['totalCount'] > 0 && $result['totalCount'] == $result['successCount']) {
								$personInsuranceInfo->commit();
								$this->success('报减成功！');
							}else {
								$personInsuranceInfo->rollback();
								$this->error('报减失败！');
							}
						}else {
							$this->error('参保状态错误！');
						}
					}
				}else {
					$this->error('参保数据异常！');
				}
			}else {
				$this->error('参数错误！');
			}
		}else {
			$this->error('非法操作!');
		}
	}
	
	
	
	
	
	
	
	
	
	/**
	 * 调用实例
	 * $rule = 查询数据库结果
	 * 社保实例
	 * $json = json_encode(array('amount'=>100.00,'month'=>3));
	 * $SocInsure = new Calculate();
	 * $json = $SocInsure->detail($rule , $json , 1);
	 * 公积金实例
	 * $json = json_encode(array('amount'=>2000.00,'month'=>3 , 'personScale'=>'5%' , 'companyScale'=>'5%' 'cardno'=>''));
	 * $SocInsure = new Calculate();
	 * $json = $SocInsure->detail($rule , $json , 2);
	 */
	 
	/**
	 * calculate function
	 * 计算社保公积金费用
	 * @access private
	 * @param string $type 1社保 2公积金
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	private function calculate($rule, $json, $type = null){
		$calculate = new \Common\Model\Calculate();
		$calculateResult = $calculate->detail($rule, $json,$type);
		return $calculateResult;
	}
	
	/**
	 * getPersonBaseByIdCard function
	 * 通过身份证号获取个人信息
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getPersonBaseByIdCard(){
		if (IS_POST) {
			$idCard = I('post.idCard','');
			empty($idCard) && $this->error('非法参数!');
			$personBase = M('PersonBase');
			$personBaseResult = $personBase->field(true)->getByCardNum($idCard);
			if($personBaseResult) {
				if (1 == $personBaseResult['provident_fund_state'] || 1 == $personBaseResult['social_insurance_state'] || 1 == $personBaseResult['disable_state']) {
					//正在报增跳到“编辑报增”
					//根据个人ID和企业ID获取报增记录
					$serviceOrder = D('ServiceOrder');
					$condition = ' po.company_id = '.$this->mCid.' and sod.id is not null and sod.type = 1 and sod.state <> -9 and sod.base_id = '.$personBaseResult['id'];
					$serviceOrderResult = $serviceOrder->getIncreaseOrderByBsaeId($this->mCid,$condition);
					if ($serviceOrderResult) {
						$personBaseResult['status'] = 1;
						//$personBaseResult['url'] = U('Company/Insurance/editInsurance',array('baseId'=>$personBaseResult['id'],'serviceOrderId'=>$serviceOrderResult['service_order_id'],'payDate'=>$serviceOrderResult['pay_date'],'type'=>1));
						$personBaseResult['url'] = U('Company/Insurance/personInfo',array('baseId'=>$personBaseResult['id'],'serviceOrderId'=>$serviceOrderResult['service_order_id'],'payDate'=>$serviceOrderResult['pay_date'],'type'=>1));
					}else {
						$personBaseResult['status'] = -1;
					}
				}elseif (2 == $personBaseResult['provident_fund_state'] || 2 == $personBaseResult['social_insurance_state'] || 2 == $personBaseResult['disable_state']) {
					$serviceOrder = D('ServiceOrder');
					$condition = ' po.company_id = '.$this->mCid.' and sod.id is not null and sod.type in (1,3) and sod.state <> -9 and sod.base_id = '.$personBaseResult['id'];
					$serviceOrderResult = $serviceOrder->getIncreaseOrderByBsaeId($this->mCid,$condition);
					if ($serviceOrderResult) {
						//在保跳到“参保人信息”
						$personBaseResult['status'] = 2;
						//$personBaseResult['url'] = U('Company/Insurance/personInfo',array('base_id'=>$personBaseResult['id'],'service_order_id'=>1,'pay_date'=>201603));
						$personBaseResult['url'] = U('Company/Insurance/warrantyList');
					}else {
						$personBaseResult['status'] = -2;
					}
				}else {
					$personBaseResult['status'] = 0;
				}
				$this->ajaxReturn(array('status'=>1,'data'=>$personBaseResult));
			}else{
				//$this->error('该个人信息不存在!');
				$this->ajaxReturn(array('status'=>2,'info'=>'该个人信息不存在'));
			}
		}else {
			$this->error('非法操作!');
		}
	}
	
	/**
	 * upload function
	 * 上传文件
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function upload(){
		if (IS_POST) {
			//企业登录/退出登录时清空temp目录下的企业对应临时文件
			$fileNameArray = array(1=>'idCardFront',2=>'idCardBack');
			$type = I('uploadType/d');
			if (array_key_exists($type,$fileNameArray)) {
				$upload = new \Think\Upload(C('IMG_UPLOAD'));
				$path = rtrim(mkFilePath($this->mCid,$upload->rootPath,'temp'),'/');
				$path = str_replace($upload->rootPath,'',$path);
				$upload->subName = $path;
				//$upload->autoSub = false;
				//$upload->subName = intval($this->mCid/1000).'/'.$this->mCid.'/temp';
				//$upload->saveName = $this->mCid.'_'.$type;
				$upload->saveName = $fileNameArray[$type].'_'.GUID();
				$upload->saveExt = 'jpg';
				// 上传单个文件 
				$info = $upload->uploadOne($_FILES['file']);
				if(!$info) {// 上传错误提示错误信息
					//$this->error($upload->getError());
					$this->ajaxReturn(array('status'=>0,'info'=>$upload->getError()));
				}else{// 上传成功 获取上传文件信息
					$url = ltrim($upload->rootPath,'.').$info['savepath'].$info['savename'];
					//$this->success($url);
					$this->ajaxReturn(array('status'=>1,'info'=>$url));
				}
			}else {
				$this->error('非法参数!');
			}
		}else {
			$this->error('非法操作!');
		}
	}
	
	/**
	 * delete function
	 * 删除文件
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function delete(){
		if (IS_POST) {
			$file = I('post.idCardFrontFile','')? I('post.idCardFrontFile','') : I('post.idCardBackFile','');
			$file = reset(explode('?',$file));
			//$file = '/Uploads/Company/0/temp/1.doc';
			empty($file) && $this->error('非法参数!');
			$result = unlink ('.'.$file); 
			if($result) {
				$this->success('删除成功!');
			}else{
				$this->error('删除失败!');
			}
		}else {
			$this->error('非法操作!');
		}
	}
	
	/**
	 * 地域选择（三级选择）
	 * @return [type] [description]
	 */
	public function areaSelect(){
		$ptimearea = getZoning();
		$code = I('post.code');
		
		if(S('ptimearea')){
			$ptimearea = S('ptimearea');
		}else{
			ksort($ptimearea);
			while (1) {
				$cur = current($ptimearea);
				$ck = key($ptimearea);
				
				if(!$cur){
					break;
				} 
				
				$next = next($ptimearea);
				$nk = key($ptimearea);
				if($ck%1000000 == 0 && $nk%1000000 != 0 && $nk%10000 == 0){
					$ptimearea[$ck]['hasChild'] = 1;
				}
				if($ck%10000 == 0 && $nk%10000 != 0 && $nk%100 == 0){
					$ptimearea[$ck]['hasChild'] = 1;
				}
			}
			
			S('ptimearea',$ptimearea,0);
		}
		
		if($code){
			if($code%1000000 == 0){
				$i = 0;
				foreach ($ptimearea as $key => $value) {
					if($code == 46000000 || $code == 11000000 || $code == 10000000 || $code == 12000000 || $code == 13000000){//三沙
						if($key%100 == 0 && $key > $code && $key < ($code+1000000)){
							$arr[$i]['code'] = $key;
							$arr[$i]['name'] = trim($value['name'],'"');
							$arr[$i]['hasChild'] = isset($value['hasChild']) ? $value['hasChild'] : 0;
							$i++;
						}
					}else{
						if($key%1000000 && $key%10000 == 0 && ($key > $code) && ($key < ($code+1000000))){
							$arr[$i]['code'] = $key;
							$arr[$i]['name'] = trim($value['name'],'"');
							$arr[$i]['hasChild'] = isset($value['hasChild']) ? $value['hasChild'] : 0;
							$i++;
						}
					}
				}
				
				$result = json_encode(array('items'=>$arr,"ret"=>1));
				echo $result;
				exit();
			}else if($code%10000 == 0){
				$i = 0;
				foreach ($ptimearea as $key => $value) {
					if($key%100 == 0 && $key > $code && $key < $code + 10000){
						$arr[$i]['code'] = $key;
						$arr[$i]['name'] = trim($value['name'],'"');
						$arr[$i]['hasChild'] = isset($value['hasChild']) ? $value['hasChild'] : 0;
						$i++;
					}
				}
				$result = json_encode(array('items'=>$arr,"ret"=>1));
				echo $result;
				exit();
			}
		}else{
			$i = 0;
			foreach ($ptimearea as $key => $value) {
				if($key%1000000 == 0){
					$arr[$i]['code'] = $key;
					$arr[$i]['name'] = trim($value['name'],'"');
					$arr[$i]['hasChild'] = 1;
					if($key == 47000000 || ($key > 40000000 && $key < 46000000)){//钓鱼岛、香港、澳门、台湾、国外、其他
						$arr[$i]['hasChild'] = 0;
					}
					$i++;
				}
			}
			$result = json_encode(array('items'=>$arr,"ret"=>1));
			echo $result;
		} 
	}
	
}