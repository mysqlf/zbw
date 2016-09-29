<?php
/**
 * 社保公积金订单数据模型
 */
namespace Company\Model;
use Think\Model\RelationModel;
 class ServiceOrderInsuranceModel extends RelationModel
 {
    protected $tablePrefix = 'zbw_';
    //service_order_insurance
    /**
     * [getHaviorcount 统计订单内报增减的数据]
     * @param  int $orderId [订单Id]
     * @param  int $type    [行为标记字段1报增3报减2在保]
     * @return [void]           [操作数]
     */
    public function getHaviorcount($orderId='',$type=1){
        if ($orderId) {
            $count=$this->field('soi.id')
                        ->join('as soi left join '.C('DB_PREFIX').'person_insurance_info as pii on soi.insurance_id=pii.id')
                        ->where(array('soi.service_order_id'=>$orderId,'soi.type'=>$type))
                        ->group('pii.base_id')
                        ->select();
            return count($count);
        }else{
            $this->error = '非法参数!';
            return false; 
        }
    }
    
	/**
	 * getServiceOrderInsuranceByCondition function
	 * 根据条件获取
	 * @param array $condition 条件数组
	 * @param int $selectType 查询类型 1:find 2:select
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getServiceOrderInsuranceByCondition($condition,$selectType = 1){
		if (is_array($condition)) {
			if (1 == $selectType) {
				$result = $this->field(true)->where($condition)->order('create_time desc')->find();
			}else {
				$result = $this->field(true)->where($condition)->order('create_time desc')->select();
			}
			if ($result || null === $result) {
				return $result;
			}else if (false === $result) {
				wlog($this->getDbError());
				$this->error = '系统内部错误！';
				return false;
			}else {
				$this->error = '未知错误！';
				return false;
			}
		}else {
			$this->error = '非法参数!';
			return false;
		}
	}
 }
 ?>