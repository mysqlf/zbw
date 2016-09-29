<?php
/**
* 
*/
namespace Company\Model;
use Think\Model\RelationModel;
class ServiceBillSalaryModel extends RelationModel
{
    /**
     * [getSalaryInfoByOrderId 获取工资订单详情]
     * @param  [type] $orderId  [订单Id]
     * @param  string $pageSize [description]
     * @return [array]           [description]
     */
    public function getSalaryInfoByOrderId($orderId,$pageSize='10'){
        $pageCount=$this->where(array('service_order_id'=>$orderId))->count('id');
        if ($pageCount==0) {
            return false;
        }
        $page=get_page($pageCount,$pageSize);
        $result=$this->field('sbs.service_price,sbs.actual_salary,sbs.pay_date,sbs.deduction_income_tax,sos.state,sos.date,pb.person_name,pb.card_num,pb.bank,pb.account')
                     ->join('as sbs left join '.C('DB_PREFIX').'service_order_salary as sos on sos.service_order_id=sbs.id')
                     ->join('left join '.C('DB_PREFIX').'person_base as pb on sbs.base_id=pb.id')
                     ->where(array('sbs.service_order_id'=>$orderId))
                     ->order('sbs.create_time desc')
                     ->limit($page->firstRow,$page->listRows)
                     ->select();
        if ($result) {
            return array('data'=>$result,'page'=>$page->show(),'count'=>$pageCount);
        }else{
            return false;
        }     
    }    
}
 ?>