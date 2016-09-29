<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: rohochan <rohochan@gmail.com> <http://blog.rohochan.com>
// +----------------------------------------------------------------------
namespace Company\Model;
use Think\Model;

/**
 * 参保地模型
 */
class WarrantyLocationModel extends Model{
	protected $tablePrefix = 'zbw_';
	
	/**
	 * getWarrantyLocationByCondition function
	 * 根据条件获取参保地
	 * @access public
	 * @param int $condition 条件
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getWarrantyLocationByCondition($condition){
		if (isset($condition) && is_array($condition)) {
			$result = $this->field(true)->where($condition)->find();
			if ($result || null === $result) {
				return $result;
			}else if(false === $result){
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
	 * [getLocationByOrderid 获取服务订单详情]
	 * @param  [int] $orderid [订单id]
	 * @return [void]          []
	 */
	public function getLocationByOrderid($orderid){
		if ($orderid) {
			$result=$this->field('location,soc_service_price,pro_service_price,af_service_price')->where(array('service_product_order_id'=>$orderid,'state'=>array('egt',0)))->select();
			if ($result||null===$result) {
				return $result;
			}elseif (false===$result) {
				wlog($this->getDbError());
				$this->error = '系统内部错误！';
				return false;
			}else{
				$this->error = '未知错误！';
				return false;
			}
		}else{
			$this->error="参数错误";
			return false;
		}
	}
	
}
