<?php
/**
* 
*/
namespace Service\Model;
use Think\Model;

class ServiceOrderSalaryModel extends Model{
    protected $tablePrefix = 'zbw_';
    
	/**
	 * getServiceOrderSalaryListByCondition function
	 * 根据条件获取列表
	 * @param array $data 条件数组
	 * param int $pageSize 分页大小，默认10
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getServiceOrderSalaryListByCondition($data,$pageSize = 10){
		if (is_array($data)) {
			$condition = array();
			//操作状态 0待审核 1审核成功 -1审核失败 2支付成功 -2支付失败 3发放成功 -3发放失败 -9删除
			if (null !== $data['type']) {
				if ('0' == $data['type']) {
					$condition['sos.state'] = array('eq',0);
				}else if ('1' == $data['type']) {
					$condition['sos.state'] = array('in','1,-1');
				}else if ('2' == $data['type']) {
					$condition['sos.state'] = array('eq',1);
				}else if ('3' == $data['type']) {
					$condition['sos.state'] = array('eq',2);
				}else if ('4' == $data['type']) {
					//$condition['sos.state'] = array('eq',3);
					$condition['sos.state'] = array('in','3,-3');
				}
			}
			if (!empty($data['user_id'])) {
				$condition['pb.user_id'] = $data['user_id'];
				//$condition['sos.user_id'] = $data['user_id'];
			}
			if (!empty($data['product_id'])) {
				$condition['sos.product_id'] = $data['product_id'];
			}
			if (!empty($data['start_time'])) {
				$condition['sos.create_time'][] = array('egt',$data['start_time']);
			}
			if (!empty($data['end_time'])) {
				$condition['sos.create_time'][] = array('elt',$data['end_time']);
			}
			if (!empty($data['date'])) {
				$condition['sos.date'] = array('eq',$data['date']);
			}
			if (!empty($data['company_id'])) {
				$condition['sp.company_id'] = array('eq',$data['company_id']);
			}
			/*if (!empty($data['admin_id'])) {
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
			}*/
			if (!empty($data['person_name'])) {
				$condition['pb.person_name'] = array('like','%'.$data['person_name'].'%');
			}
			if (!empty($data['card_num'])) {
				$condition['pb.card_num'] = array('like','%'.$data['card_num'].'%');
			}
			if (!empty($data['company_name'])) {
				$condition['ci.company_name'] = array('like','%'.$data['company_name'].'%');
			}
			if ($condition['sos.state']) {
				$condition['sos.state'] = array($condition['sos.state'],array('neq',-8),array('neq',-9),'and');
			}else {
				$condition['sos.state'] = array(array('neq',-8),array('neq',-9),'and');
			}
			
			//客服只能看当前用户信息
			if (!empty($data['account_info'])) {
				if (3 == $data['account_info']['group']) {
					$adminId = getServiceAdminId($data['account_info']['user_id']);
					if ($adminId) {
						$userServiceProvider = D('UserServiceProvider');
						$userServiceProviderResultl = $userServiceProvider->field('GROUP_CONCAT(distinct user_id) as user_id')->where(['company_id'=>$data['account_info']['company_id'],'admin_id'=>$adminId,'state'=>1,'user_id'=>['gt',0]])->find();
						if ($userServiceProviderResultl['user_id']) {
							$condition['sos.user_id'] = ['in',$userServiceProviderResultl['user_id']];
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
				if ($condition['sos.product_id']) {
					$condition['sos.product_id'] = array($condition['sos.product_id'],array('in',$productIdArray));
				}else {
					$condition['sos.product_id'] = array('in',$productIdArray);
				}
				
				$pageCount = $this->alias('sos')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=sos.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sos.product_id=sp.id')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.user_id=sos.user_id')->where($condition)->count('sos.id');
				$page = get_page($pageCount,$pageSize);
				
				$result = $this->alias('sos')->field('sos.*,pb.person_name,pb.card_num,pb.bank,pb.branch,pb.account_name,pb.account,sp.name as product_name,ci.id as company_id,ci.company_name,po.order_no as pay_order_no,po.transaction_no as pay_transaction_no')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=sos.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sos.product_id=sp.id')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.user_id=sos.user_id')->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=sos.pay_order_id')->where($condition)->limit($page->firstRow,$page->listRows)->order('sos.create_time desc')->select();
				
				if ($result || null === $result) {
					$countResult = array();
					$countResult[0] = $this->alias('sos')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=sos.base_id')->where(array('sos.product_id'=>array('in',$productIdArray),'sos.state'=>0,'sos.user_id'=>($condition['sos.user_id']?$condition['sos.user_id']:array('gt',0))))->count('sos.id');
					$countResult[2] = $this->alias('sos')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=sos.base_id')->where(array('sos.product_id'=>array('in',$productIdArray),'sos.state'=>1,'sos.user_id'=>($condition['sos.user_id']?$condition['sos.user_id']:array('gt',0))))->count('sos.id');
					$countResult[3] = $this->alias('sos')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=sos.base_id')->where(array('sos.product_id'=>array('in',$productIdArray),'sos.state'=>2,'sos.user_id'=>($condition['sos.user_id']?$condition['sos.user_id']:array('gt',0))))->count('sos.id');
					return array('data'=>$result,'page'=>$page->show(),'count'=>$countResult);
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
	 * deleteSalaryOrderByCondition function
	 * 根据条件删除代发工资订单
	 * @param array $condition 条件数组
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function deleteSalaryOrderByCondition($condition){
		if (is_array($condition)) {
			//$condition['state'] || $condition['state'] = array('in',array(0,-1,-9));
			$salaryOrderResult = $this->field(true)->where($condition)->find();
			if ($salaryOrderResult) {
				if (in_array($salaryOrderResult['state'],array(0,-1,-9))) {
					$result = $this->where($condition)->save(array('state'=>-8));
					if (false !== $result) {
						return true;
					}else {
						$this->error = '删除失败!';
						return false;
					}
				}else {
					$this->error = '订单状态错误!';
					return false;
				}
			}else {
				$this->error = '此订单不存在!';
				return false;
			}
		}else {
			$this->error = '非法参数!';
			return false;
		}
	}
	
	/**
	 * updateSalaryOrderByCondition function
	 * 根据条件更新代发工资订单
	 * @param array $data 条件数组
	 * @param array $type 类型 1审批成功 -1审批失败 (2付款成功 -2付款失败) 3发放成功 -3发放失败
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function updateSalaryOrderByCondition($data,$type){
		if (is_array($data) && is_array($data['id']) && in_array($type,array(1,-1,3,-3))) {
			$serviceProduct = M('ServiceProduct');
			$serviceProductResult = $serviceProduct->field(true)->where(array('company_id'=>$data['service_company_id']))->select();
			$productIdArray = array();
			if ($serviceProductResult) {
				foreach ($serviceProductResult as $key => $value) {
					$productIdArray[] = $value['id'];
				}
			}
			if ($productIdArray) {
				$stateArray = array(-1=>array('in','0,-1,1'), 1=>array('in','0,-1,1'), -3=>2, 3=>2);
				$condition = array('id'=>array('in',$data['id']),'product_id'=>array('in',$productIdArray),'state'=>$stateArray[$type]);
				$salaryOrderSalaryResult = $this->field(true)->where($condition)->select();
				//if (count($data['id']) == count($salaryOrderSalaryResult)) {
					$this->startTrans();
					$saveResult = $this->where($condition)->save(array('state'=>$type,'remark'=>$data['remark']));
					if (false !== $saveResult) {
						$payOrder = D('PayOrder');
						switch ($type) {
							case -1:
								$salaryOrderSalaryArray = array();
								foreach ($salaryOrderSalaryResult as $key => $value) {
									$salaryOrderSalaryArray['baseId'][$value['base_id']] = $value['base_id'];
									if ($value['pay_order_id']) {
										$salaryOrderSalaryArray['id'][$value['id']] = $value['id'];
										$salaryOrderSalaryArray['pay_order_id'][$value['pay_order_id']] = $value['pay_order_id'];
										//$salaryOrderSalaryArray['pay_order_id_sosid'][$value['pay_order_id']][] = $value['id'];
									}
								}
								if ($salaryOrderSalaryArray['id']) {
									$salaryOrderSalarySaveResult = $this->where(array('id'=>array('in',$salaryOrderSalaryArray['id'])))->save(array('pay_order_id'=>0));
									if (false !== $salaryOrderSalarySaveResult) {
										$result = array();
										$result['totalCount'] = 0;
										$result['successCount'] = 0;
										foreach ($salaryOrderSalaryArray['pay_order_id'] as $key => $value) {
											$result['totalCount'] ++;
											$amount = $this->where(array('pay_order_id'=>$value,'state'=>array('gt',0)))->sum('price + service_price');
											$payOrderSaveResult = $payOrder->where(array('id'=>$value))->save(array('amount'=>$amount));
											if (false !== $payOrderSaveResult) {
												$result['successCount'] ++;
											}
										}
										if ($result['totalCount'] == $result['successCount']) {
											$this->commit();
											//$result = true;
										}else {
											$this->rollback();
											$this->error = '操作失败!';
											$result = false;
										}
									}else {
										$this->rollback();
										$this->error = '操作失败!';
										$result = false;
									}
								}else {
									$this->commit();
									$result = true;
								}
								break;
							case 1:
								$result = array();
								$result['totalCount'] = 0;
								$result['successCount'] = 0;
								foreach ($salaryOrderSalaryResult as $key => $value) {
									$result['totalCount'] ++;
									$handleMonth = date('Ym',strtotime($value['create_time']));
									//写入支付订单数据
									$payOrderData = array();
									$payOrderData['user_id'] = $value['user_id'];
									$payOrderData['company_id'] = $data['service_company_id'];
									$payOrderData['type'] = 3;
									$payOrderData['location'] = ($value['location']/1000<<0)*1000;
									$payOrderData['handle_month'] = $handleMonth;
									$payOrderData['amount'] = $value['price'] + $value['service_price'];
									//dump($payOrderData);
									$payOrderResult = $payOrder->savePayOrder($payOrderData);
									if ($payOrderResult) {
										$payOrderId = $payOrderResult;
										$salaryOrderSaveResult = $this->where(array('id'=>$value['id']))->save(array('pay_order_id'=>$payOrderId));
										if (false !== $salaryOrderSaveResult) {
											$amount = $this->where(array('pay_order_id'=>$payOrderId,'state'=>array('gt',0)))->sum('price + service_price');
											$payOrderSaveResult = $payOrder->where(array('id'=>$payOrderId))->save(array('amount'=>$amount));
											if (false !== $payOrderSaveResult) {
												$result['successCount'] ++;
											}
										}
									}else {
										$this->rollback();
										$this->error = $payOrder->getError();
										return false;
									}
								}
								//if (0 != $result['totalCount'] && $result['totalCount'] == $result['successCount']) {
								if ($result['totalCount'] == $result['successCount']) {
									$this->commit();
									//$result = true;
								}else {
									$this->rollback();
									$this->error = '操作失败!';
									$result = false;
								}
								break;
							case -3:
								//TODO:写入差额
								$this->commit();
								$result = true;
								break;
							case 3:
								//TODO:判断是否存在差额,存在则删除差额
								$this->commit();
								$result = true;
								break;
							default:
								$this->rollback();
								$this->error = '类型错误';
								$resul = false;
								break;
						}
						return $result;
					}else {
						$this->rollback();
						$this->error = '操作失败!';
						return false;
					}
				//}else {
				//	$this->error = '所选数据状态没有变化!';
				//	return false;
				//}
			}else {
				$this->error = '产品数据错误！';
				return false;
			}
		}else {
			$this->error = '非法参数!';
			return false;
		}
	}
	
	/**
	 * updateSalaryOrderByCondition function
	 * 根据条件更新代发工资订单
	 * @param array $data 条件数组
	 * @param array $type 类型 1审批成功 -1审批失败 (2付款成功 -2付款失败) 3发放成功 -3发放失败
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function updateSalaryOrderByConditionOld($data,$type){
		if (is_array($data) && is_array($data['id']) && in_array($type,array(1,-1,3,-3))) {
			$serviceProduct = M('ServiceProduct');
			$serviceProductResult = $serviceProduct->field(true)->where(array('company_id'=>$data['service_company_id']))->select();
			$productIdArray = array();
			if ($serviceProductResult) {
				foreach ($serviceProductResult as $key => $value) {
					$productIdArray[] = $value['id'];
				}
			}
			if ($productIdArray) {
				$stateArray = array(-1=>array('in','0,-1,1'), 1=>array('in','0,-1'), -3=>2, 3=>2);
				$condition = array('id'=>array('in',$data['id']),'product_id'=>array('in',$productIdArray),'state'=>$stateArray[$type]);
				$salaryOrderResult = $this->field(true)->where($condition)->select();
				//if (count($data['id']) == count($salaryOrderResult)) {
					$this->startTrans();
					$saveResult = $this->where($condition)->save(array('state'=>$type,'note'=>$data['note']));
					if (false !== $saveResult) {
						$payOrder = D('PayOrder');
						switch ($type) {
							case -1:
								$result = array();
								$result['totalCount'] = 0;
								$result['successCount'] = 0;
								foreach ($salaryOrderResult as $key => $value) {
									$result['totalCount'] ++;
									//dump($value['pay_order_id']);
									if ($value['pay_order_id']) {
										$payOrderSaveResult = $payOrder->where(array('id'=>$value['pay_order_id']))->setDec('amount',$value['price']+$value['service_price']);
										$salaryOrderSaveResult = $this->where(array('id'=>$value['id']))->save(array('pay_order_id'=>0));
										if (false !== $payOrderSaveResult && false !== $personInsuranceInfoSaveResult) {
											$result['successCount'] ++;
										}
									}else {
										$result['successCount'] ++;
									}
								}
								//if (0 != $result['totalCount'] && $result['totalCount'] == $result['successCount']) {
								if ($result['totalCount'] == $result['successCount']) {
									$this->commit();
									//$result = true;
								}else {
									$this->rollback();
									$this->error = '操作失败!';
									$result = false;
								}
								break;
							case 1:
								$result = array();
								$result['totalCount'] = 0;
								$result['successCount'] = 0;
								foreach ($salaryOrderResult as $key => $value) {
									$result['totalCount'] ++;
									$handleMonth = date('Ym',strtotime($value['create_time']));
									//写入支付订单数据
									$payOrderData = array();
									$payOrderData['user_id'] = $value['user_id'];
									$payOrderData['company_id'] = $data['service_company_id'];
									$payOrderData['type'] = 3;
									$payOrderData['location'] = ($value['location']/1000<<0)*1000;
									$payOrderData['handle_month'] = $handleMonth;
									$payOrderData['amount'] = $value['price'] + $value['service_price'];
									//dump($payOrderData);
									$payOrderResult = $payOrder->savePayOrder($payOrderData);
									if ($payOrderResult) {
										$payOrderId = $payOrderResult;
										$salaryOrderSaveResult = $this->where(array('id'=>$value['id']))->save(array('pay_order_id'=>$payOrderId));
										if (false !== $salaryOrderSaveResult) {
											$result['successCount'] ++;
										}
									}else {
										$this->rollback();
										$this->error = $payOrder->getError();
										return false;
									}
								}
								//if (0 != $result['totalCount'] && $result['totalCount'] == $result['successCount']) {
								if ($result['totalCount'] == $result['successCount']) {
									$this->commit();
									//$result = true;
								}else {
									$this->rollback();
									$this->error = '操作失败!';
									$result = false;
								}
								break;
							case -3:
								//TODO:写入差额
								$this->commit();
								$result = true;
								break;
							case 3:
								//TODO:判断是否存在差额,存在则删除差额
								$this->commit();
								$result = true;
								break;
							default:
								$this->rollback();
								$this->error = '类型错误';
								$resul = false;
								break;
						}
						return $result;
					}else {
						$this->rollback();
						$this->error = '操作失败!';
						return false;
					}
				//}else {
				//	$this->error = '所选数据状态没有变化!';
				//	return false;
				//}
			}else {
				$this->error = '产品数据错误！';
				return false;
			}
		}else {
			$this->error = '非法参数!';
			return false;
		}
	}
	
}
 ?>