<?php
/**
*账单明细
*/
namespace Company\Model;
use Think\Model\RelationModel;
class ServiceBillDetailCollectModel extends RelationModel
{
    /**
     * [getBillInfoById 获取账单详情]
     * @param  [type] $orderId  [description]
     * @param  string $pageSize [description]
     * @return [type]           [description]
     */
    public function getBillInfoById($BillId,$pageSize='10'){
        if ($BillId) {
           $pageCount=$this->where(array('order_id'=>$BillId))->count('id');
           if (empty($pageCount)) {
                $this->error='没有记录';
                return false;
           }
           $page=get_page($pageCount,$pageSize);
           $result=$this->field('sbdc.*,pb.person_name,pb.card_num')
                        ->join('as sbdc left join '.C('DB_PREFIX').'person_base as pb on pb.id=sbdc.base_id')
                        ->where(array('sbdc.order_id'=>$BillId))
                        ->limit($page->firstRow,$page->listRows)
                        ->order('sbdc.base_id desc')
                        ->select();
            return array('data'=>$result,'page'=>$page->show());
        }else{
            $this->error='非法参数';
            return false;    
        }
    }
}
 ?>