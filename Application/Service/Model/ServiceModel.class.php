<?php

namespace Service\Model;
use Think\Model;

/**
 * 服务商模型
 */
class ServiceModel extends Model
{
    protected $autoCheckFields = false;
    public function incTotal ($sid, $product_id)
    {
        $res['audit']     = $this->insUnAudit($sid, $product_id);
        $res['conduct']   = $this->insUnConduct($sid, $product_id);
        $res['insuranceNumber'] = $this->insuranceNumber($sid, $product_id);
        $res['baoJianNumber']   = $this->baoJianNumber($sid, $product_id);
        $res['zaiBaoNumber']    = $this->zaiBaoNumber($sid, $product_id);
        return $res;
    }
    /**
     * 统计社保待审核人数
     * @param  [int] $sid [服务商id]
     * @return [int]      [待审核人数]
     */ 
    public function insUnAudit ($sid, $product_id)
    {
        $m = M('person_insurance_info');
        return $m->where("operate_state=0 AND state <> 0 AND user_id IN({$sid})  AND product_id in ({$product_id})")->count('DISTINCT(base_id)');
    }
    /**
     * 统计社保待办理人数
     * @param  [int] $sid [服务商id]
     * @return [int]      [待操作人数]
     */
    public function insUnConduct ($sid, $product_id)
    {
        $m = M('person_insurance_info');
        //$result =   $m->alias('pii')->where("pii.operate_state=2 AND pii.user_id IN({$sid})  AND pii.product_id in ({$product_id}) ")
               // ->join('left join zbw_service_insurance_detail sid ON sid.insurance_info_id=pii.id')
               // ->count('DISTINCT pii.base_id, pii.handle_month,sid.pay_date');
        //$sql = "SELECT pii.id FROM zbw_person_insurance_info pii left join zbw_service_insurance_detail sid ON sid.insurance_info_id=pii.id WHERE ( pii.operate_state=2 AND pii.user_id IN({$sid}) AND pii.product_id in ({$product_id}) ) GROUP BY pii.base_id, pii.handle_month,sid.pay_date";
        $sql = "SELECT count(pii.id) as piiid_count FROM zbw_person_insurance_info pii left join zbw_service_insurance_detail sid ON sid.insurance_info_id=pii.id WHERE ( pii.operate_state=2 AND pii.user_id IN({$sid}) AND pii.product_id in ({$product_id}) ) GROUP BY pii.base_id, pii.handle_month,sid.pay_date";
        $result = $m->query($sql);
		return count($result);
		//dump($result);echo $m->getLastSql();
    }

    /**
     * 报增人数
     */
    protected function insuranceNumber($sid, $product_id){
        $m = M('person_insurance');
        return $m->where("state=1 AND user_id IN({$sid})  AND product_id in ({$product_id}) ")->count('DISTINCT(base_id)');
    }
    

    /**
     * 报减人数
     */
    protected function baoJianNumber($sid, $product_id){
        $m = M('person_insurance');
        return $m->where("state=3 AND user_id IN({$sid})  AND product_id in ({$product_id}) ")->count('DISTINCT(base_id)');
    }


    /**
     * 在保人数
     */
    protected function zaiBaoNumber($sid, $product_id){
        $m = M('person_insurance');
        return $m->where("state=2 AND user_id IN({$sid})  AND product_id in ({$product_id}) ")->count('DISTINCT(base_id)');
    }


    /**
     * 待发放工资人数
     * @param  [int] $sid [服务商id]
     * @return [int]      [待发放人数]
     */
    // public function salaUnConduct ($sid)
    // {
    //     $m = M('service_order_salary');
    //     return $m->where("state=1 AND user_id IN({$sid}) ")->count();
    // }
    /**
     * 待设定服务数
     * @param  [int] $sid [服务商id]
     * @return [int]      [服务数目]
     */
    // public function servTotal ($sid)
    // {
    //     $m = M('service_product_order');
    //     return $m->where("state=1 AND service_state=1 AND user_id IN ({$sid})")->count();
    // }
    
}
?>