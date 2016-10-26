<?php
/**
 * 社保公积金订单数据模型
 */
namespace Company\Model;
use Think\Model\RelationModel;
 class ServiceInsuranceDetailModel extends RelationModel
 {
    protected $tablePrefix = 'zbw_';
    //service_order_insurance
   
    /**
     * [getSGinfoByOrderid 根据订单id获取人员信息列表]
     * @param  [type] $orderId  
     * @param  string $pageSize 
     * @return [type]           
     */
    public function getSGinfoByOrderid($userid,$orderId,$pageSize='10'){
        if (!$orderId) {
            $this->error='参数错误';
            return false;
        }
        $pageCount=self::_getCountByOrderidAndPtype($userid,$orderId,1);
        #$pageCount2=self::_getCountByOrderidAndPtype($userid,$orderId,2);
        if ($pageCount>0) {
             $page=get_page($pageCount,$pageSize);
            /*if ($pageCount1>=$pageCount2) {
               
                $type=1;
            }else{
                $page=get_page($pageCount2,$pageSize);
                $type=2;
            }*/
            $show = $page->show();// 分页显示输出
            //数据部分
            $result=$this->alias('sid')
                        ->field('sid.pay_date,sid.type,sid.price,sid.service_price,pii.base_id,pii.location,pb.person_name,pb.card_num')
                        ->join('left join '.C('DB_PREFIX').'person_insurance_info as pii on sid.insurance_info_id=pii.id')
                        ->join('left join '.C('DB_PREFIX').'person_base as pb on pii.base_id=pb.id')
                        ->where(array('pii.user_id'=>$userid,'sid.pay_order_id'=>$orderId))
                        ->limit($page->firstRow,$page->listRows)
                        ->select();
            $result=self::_getIncInfo($result,$orderId);//获取详细缴费数据
            return array('data'=>$result,'page'=>$show);
        }else{
            $this->error="没有记录";
            return false;
        }
        
    }
   


    /**
     * [getSGAllinfoByOrderid 获取社保公积金详情]
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public function getSGAllByOrderid($userid,$orderId){
        if (!$orderId) {
            $this->error="参数错误";
            return false;
        }

        $resSb = $this->alias('sid')->field('sid.pay_date,pii.base_id,pii.location,pb.person_name,pb.card_num,sp.name,sid.current_detail')
                            ->join('left join zbw_person_insurance_info pii ON pii.id = sid.insurance_info_id')
                            ->join('left join zbw_person_base pb ON pb.id = pii.base_id')
                            ->join('left join zbw_service_product sp ON sp.id = pii.product_id')
                            ->where("sid.pay_order_id ={$orderId} AND pii.user_id={$userid} AND pii.payment_type=1 AND sid.state NOT IN(0,-1)")
                            ->order('sid.id asc')->select();

        $resGjj = $this->alias('sid')->field('sid.pay_date,pii.base_id,pii.location,pb.person_name,pb.card_num,sp.name,sid.current_detail')
                            ->join('left join zbw_person_insurance_info pii ON pii.id = sid.insurance_info_id')
                            ->join('left join zbw_person_base pb ON pb.id = pii.base_id')
                            ->join('left join zbw_service_product sp ON sp.id = pii.product_id')
                            ->where("sid.pay_order_id ={$orderId} AND pii.user_id={$userid} AND pii.payment_type=2 AND sid.state NOT IN(0,-1)")
                            ->order('sid.id asc')->select();

        foreach ($resSb as $key => $value) {
            foreach ($resGjj as $k => $val) {
                if($value['base_id'] == $val['base_id'] && $value['pay_date'] == $val['pay_date'] && $value['card_num'] == $val['card_num']) {
                    if(!empty($val['current_detail'])){
                        unset($resGjj[$k]);
                        break;
                    }
                }
            }
        }

        if(count($resGjj) > 0){
            $resSb = array_merge($resSb, $resGjj);
        }
        $result=self::_getIncInfo($resSb,$orderId);//获取详细缴费数据
        return $result;
/*
        return false;*/

    }
    /**
     * [SIDgetSidDid 根据用户id查询所有的详细信息id]
     * @param [type] $where [description]
     */
    public function SIDgetSidDid($where){
        if ($where) {
            return $this->alias('sid')
                ->field('sid.id')
                ->join('left join '.C('DB_PREFIX').'person_insurance_info as pii on sid.insurance_info_id=pii.id')
                ->where($where)
                ->select();
        }else{
            $this->error='参数错误';
            return false;
        }
        
    }
    /*内部方法*/
    /**
     * [_getCountByOrderidAndPtype 获取订单内公积金或社保条数]
     * @param  [int] $orderId [支付订单id]
     * @param  [int] $type    [类型1社保,2公积金]
     * @return [int]          [条数]
     */
    private function _getCountByOrderidAndPtype($userid,$orderId){
        $count=$this->alias('sid')
                    ->join('left join '.C('DB_PREFIX').'person_insurance_info as pii on sid.insurance_info_id=pii.id')
                    ->where(array('pii.user_id'=>$userid,'sid.pay_order_id'=>$orderId))
                    ->group('pii.base_id,sid.pay_date')
                    ->count('sid.id');
        return $count;
    }
    /**
     * [_getIncInfo 获取社保公积金缴费数据详情]
     * @param  [type] $result [description]
     * @return [type]         [description]
     */
    private function _getIncInfo($result,$orderId){
        foreach ($result as $key => $value) {
            $inc=$this->alias('sid')
                ->field('sid.insurance_detail,sid.type,sid.price,sid.service_price,sid.replenish,pii.payment_type')
                ->join('left join '.C('DB_PREFIX').'person_insurance_info as pii on sid.insurance_info_id=pii.id')
                ->where(array('sid.pay_date'=>$value['pay_date'],'sid.pay_order_id'=>$orderId,'pii.base_id'=>$value['base_id']))
                ->select();
            unset($result[$key]['current_detail']);
            $result[$key]['inc']=$inc;
        }
        return $result;
    }
 }
 ?>