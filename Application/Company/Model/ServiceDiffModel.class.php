<?php
/**
*   差额模型
*/
namespace Company\Model;
use Think\Model\RelationModel;
class ServiceDiffModel extends RelationModel{
    protected $tablePrefix = 'zbw_';
    /**
     * [GetAllByUser 获取用户的差额列表]
     * @param [type] $userid   [description]
     * @param string $pageSize [description]
     */
    public function GetAllByUser($userid,$pageSize='10'){
        $pageCount=$this->alias('sd')
                        ->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.id =sd.detail_id')
                        ->join('left join '.C('DB_PREFIX').'person_insurance_info as pii on pii.id=sid.insurance_info_id')
                        ->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=sid.pay_order_id')
                        ->where(array('pii.user_id'=>$userid,'po.state'=>1,'sd.current'=>array('neq','')))
                        ->count('sd.id');

        if ($pageCount>0) {
            $page=get_page($pageCount,$pageSize);
            $result=$this->alias('sd')
                        ->field('sd.*,ci.company_name,pb.person_name,po.handle_month,po.id as poid,po.order_no,po.pay_time,pii.base_id,pii.payment_type,sid.pay_date,sid.id as sid')
                        ->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.id =sd.detail_id')
                        ->join('left join '.C('DB_PREFIX').'person_insurance_info as pii on pii.id=sid.insurance_info_id')
                        ->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=sid.pay_order_id')
                        ->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=pii.base_id')
                        ->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id=po.company_id')
                        ->where(array('pii.user_id'=>$userid,'po.state'=>1,'sd.current'=>array('neq','')))
                        ->limit($page->firstRow,$page->listRows)
                        ->order('sd.modify_time desc')
                        ->select();
            return array('data'=>$result,'page'=>$page->show());
        }else{
            $this->error="没有记录";
            return false;
        }
    }
    /**
     * [getDiffOfSearch 根据筛选条件获取差额列表]
     * @param  [array] $where    [筛选条件]
     * @param  string $pageSize [分页大小]
     * @return [void]           [description]
     * @author Greedy-wolf   <1154505909@qq.com> 
     */
    public function getDiffOfSearch($where,$pageSize='10'){
        if ($where) {
            $pageCount=$this->alias('sd')
                        ->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.id =sd.detail_id')
                        ->join('left join '.C('DB_PREFIX').'person_insurance_info as pii on pii.id=sid.insurance_info_id')
                        ->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=sid.pay_order_id')
                        ->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=pii.base_id')
                        ->where($where)
                        ->count('sd.id');
            if ($pageCount>0) {
                $page=get_page($pageCount,$pageSize);
                $result=$this->alias('sd')
                            ->field('sd.*,ci.company_name,pb.person_name,po.handle_month,po.id as poid,po.order_no,po.pay_time,pii.base_id,pii.payment_type,sid.pay_date,sid.id as sid')
                            ->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.id =sd.detail_id')
                            ->join('left join '.C('DB_PREFIX').'person_insurance_info as pii on pii.id=sid.insurance_info_id')
                            ->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=sid.pay_order_id')
                            ->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=pii.base_id')
                            ->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id=po.company_id')
                            ->where($where)
                            ->limit($page->firstRow,$page->listRows)
                            ->order('sd.modify_time desc')
                            ->select();
                return array('data'=>$result,'page'=>$page->show());
            }else{
                $this->error="没有记录";
                return false;
            }
        }else{
            $this->error='参数错误';
            return false; 
        }
    }

     /**
     * [getBillDiff 获取对账单下的差额]
     * @param  [type] $billid [description]
     * @return [type]         [description]
     */
    public function getBillDiff($billid){
        if ($billid) {
            $result=$this->alias('sd')
                ->field('sd.id,sd.item,sd.type,sd.amount,sd.modify_time,sid.pay_date,pb.person_name,po.order_no,po.pay_time,po.handle_month,ci.company_name')
                ->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.id=sd.detail_id')
                ->join('left join '.C('DB_PREFIX').'person_insurance_info as pii on pii.id=sid.insurance_info_id')
                ->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=sid.pay_order_id')
                ->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=pii.base_id')
                ->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id=po.company_id')
                ->where(array('po.service_bill_id'=>$billid,'po.type'=>2,'po.state'=>1))
                ->order('po.create_time desc')
                ->select();
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
        }else{
            $this->error="参数错误";
            return false;
        }
        
    }
    /**
     * [getDiffInfoById 获取差额详情]
     * @param  [type] $userid [用户id]
     * @param  [type] $diffid [description]
     * @return [type]         [description]
     */
    public function getDiffInfoById($userid,$diffid){
        $result=$this->alias('sd')
                    ->field('sd.*,ci.company_name,ci.contact_name,pb.person_name,po.handle_month,po.id as poid,po.order_no,po.pay_time,pii.base_id,pii.payment_type,sid.pay_date,sid.amount as samount')
                    ->join('left join '.C('DB_PREFIX').'service_insurance_detail as sid on sid.id =sd.detail_id')
                    ->join('left join '.C('DB_PREFIX').'person_insurance_info as pii on pii.id=sid.insurance_info_id')
                    ->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=sid.pay_order_id')
                    ->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=pii.base_id')
                    ->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id=po.company_id')
                    ->where(array('pii.user_id'=>$userid,'sd.id'=>$diffid,'po.state'=>1))
                    ->limit('1')
                    ->find();
        return $result;
    }
}
 ?>