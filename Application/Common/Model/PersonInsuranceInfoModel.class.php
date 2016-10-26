<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: rohochan <rohochan@gmail.com> <http://blog.rohochan.com>
// +----------------------------------------------------------------------
namespace Common\Model;
use Think\Model\RelationModel;

/**
 * 参保信息模型
 */
class PersonInsuranceInfoModel extends RelationModel{
    protected $tablePrefix = 'zbw_';
    //protected $tableName = 'person_insurance_info';
	
	/**
	 * addPersonInsuranceInfo function
	 * 添加参保详情
	 * param array $data 数据
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function addPersonInsuranceInfo($data){
		if (is_array($data) && $data['insurance_id'] > 0) {
			$result = $this->field('id,state,operate_state')->where(array('insurance_id'=>$data['insurance_id'],'payment_type'=>$data['payment_type'],'handle_month'=>$data['handle_month']))->find();
			if ($result) {
				if (2 > $result['operate_state']) {
					//更新数据
					unset($data['create_time']);
					$saveResult = $this->where(array('id'=>$result['id']))->save($data);
					//清楚原来的明细记录
        			$serviceInsuranceDetail = M('service_insurance_detail' , 'zbw_');
        			$serviceInsuranceDetailDeleteResult = $serviceInsuranceDetail->where(array('insurance_info_id'=>$result['id']))->delete();
					if (($saveResult || 0 === $saveResult) && false !== $serviceInsuranceDetailDeleteResult) {
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
					$this->error = '当前月已存在办理成功数据,暂时不能更新数据！';
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
			$personInsurance = M('PersonInsurance','zbw_');
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
			$personInsurance = M('PersonInsurance','zbw_');
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
	
}