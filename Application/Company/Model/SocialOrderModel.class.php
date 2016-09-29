<?php
/**
* 企业订单账单相关
* @author zl
*/
namespace Company\Model;
use Think\Model\RelationModel;

class SocialOrderModel extends RelationModel
{
    protected $tablePrefix = 'zbw_';
    protected $tableName = 'service_order';
   
    /**
     * [getRunningBill 获取流水] 数据库改变--删
     * @param  [type] $userId   [description]
     * @param  string $pageSize [description]
     * @return [type]           [description]
     */
/*    public function getRunningBill($userId,$pageSize='10'){
        if ($userId) {
            $pageCount=$this->where(array('user_id'=>$userId))->count('id');
            $prefix=C('DB_PREFIX');
            $page=get_page($pageCount,$pageSize);
            $result=$this->alias('so')
                         ->field('so.id,so.order_no,so.company_id,so.product_id,so.price,so.state,so.payment_type,so.create_time,so.pay_time,ci.company_name,sp.name as spname,sa.name as saname')
                         ->join('left join '.$prefix.'service_product_order as spo on spo.product_id=so.product_id and spo.user_id='.$userId.' and spo.service_state in (2,3)')
                         ->join('left join '.$prefix.'service_product as sp on so.product_id = sp.id')
                         ->join('left join '.$prefix.'service_admin as sa on spo.admin_id=sa.id ')
                         ->join('left join '.$prefix.'company_info as ci on so.company_id=ci.id')
                         ->where(array('so.user_id'=>$userId))
                         ->order('so.create_time desc')
                         ->limit($page->firstRow,$page->listRows)
                         ->select();
            return array('data'=>$result,'page'=>$page->show());
        }else{
            $this->error='参数错误';
            return false;
        }
    }*/

    /**
     * [getAllBill 获取所有的订单] ---数据库改变--删
     * @param  [type] $userId   [用户id]
     * @param  string $pageSize [分页大小]
     * @return [type]           [description]
     */
 /*   public function getAllBill($userId,$pageSize='10'){
        $spoModel=M('service_product_order');
        $prefix=C('DB_PREFIX');
         #使用账单子查询
        $sosql=$this->alias('so')
                    ->field('so.id,so.user_id,so.create_time,so.pay_time,so.product_id,so.state,so.order_no,so.price,ci.company_name,sp.name as spname ,sa.name as saname')
                    ->join('left join '.$prefix.'service_product_order as spo on spo.product_id=so.product_id and spo.user_id='.$userId.' and spo.service_state in (2,3)')
                    ->join('left join '.$prefix.'service_product as sp on so.product_id = sp.id')
                    ->join('left join '.$prefix.'service_admin as sa on spo.admin_id=sa.id ')
                    ->join('left join '.$prefix.'company_info as ci on so.company_id=ci.id')
                    ->where(array('so.user_id'=>$userId))
                    ->order('so.create_time desc')
                    ->select(false);

        #服务账单子查询
        $sposql=$spoModel->alias('spo')
                        ->field('spo.id as id,spo.user_id,spo.create_time,spo.pay_time,spo.product_id,spo.state,spo.order_no,spo.price,ci.company_name,sp.name as spname ,sa.name as saname')
                        ->join('left join '.$prefix.'service_product as sp on spo.product_id=sp.id')
                        ->join('left join '.$prefix.'service_admin as sa on spo.admin_id=sa.id ')
                        ->join('left join '.$prefix.'company_info as ci on sp.company_id=ci.id')
                        ->where(array('spo.user_id'=>$userId))
                        ->order('spo.create_time desc')
                        ->select(false);
        $sql='select count(sospo.id) as count from ('.$sosql.' union all '.$sposql.') as sospo';
        $count=$this->query($sql);

        $page=get_page($count['count'],$pageSize);
        $sql='select c.id,c.user_id,c.create_time,c.pay_time,c.product_id,c.state,c.order_no,c.price,c.company_name,c.spname,c.saname from ('.$sosql.' union all '.$sposql.') as c order by create_time desc limit '.$page->firstRow.', '.$page->listRows;
        $result=$this->query($sql);
        return $result;
    }*/
    /**
     * [getMySerCom 获取我的服务商]
     * @param  [type] $userId [description]
     * @return [type]         [description]
     */
    public function getMySerCom($userId){
        if ($userId) {
            $prefix=C('DB_PREFIX');
            $result=$this->alias('so')
                         ->field('so.company_id,ci.company_name')
                         ->join('LEFT JOIN '.$prefix.'company_info as ci on so.company_id=ci.id')
                         ->where(array('so.user_id'=>$userId))
                         ->group('so.company_id')
                         ->select();
            return $result;
        }else{
            $this->error='参数错误';
            return false;
        }
    }
    /**
     * [getOrderInfoById 获取订单详情]
     * @param  [int] $userId  [用户id]
     * @param  [int] $orderId [订单id]
     * @return [void]          
     */
    public function getOrderInfoById($userId,$orderId){
        if ($userId&&$orderId) {
           $orderinfo=$this->field('so.*,sp.name,sp.location,ci.company_name,spo.modify_price')
                            ->join('as so left join '.C('DB_PREFIX').'service_product_order as spo on so.product_id=spo.product_id')
                            ->join('left join '.C('DB_PREFIX').'service_product as sp on so.product_id=sp.id')
                            ->join('left join '.C('DB_PREFIX').'company_info as ci on sp.company_id=ci.id')
                            ->where(array('so.id'=>$orderId,'so.user_id'=>$userId,'spo.user_id'=>$userId))
                            ->find();
            return $orderinfo;
        }else{
            $this->error='非法参数';
            return false;
        }
        
    }
    /**
     * [getOrderList 获取公司的订单记录]
     * @param  [int]  $userId [id]
     * @param  int $type      [订单类型 1社保公积金2工资订单]
     * @param  int $pageSize  [分页大小]
     * @return [void]             
     */
    public function getOrderList($userId,$type=1,$pageSize=10){
        if ($userId) {
            $pageCount=$this->where(array('user_id'=>$userId,'payment_type'=>$type))->order('create_time desc')->count('id');

            $page = get_page($pageCount,$pageSize);
            $orderlist=$this->field('so.*,sp.name')
                            ->join('as so left join '.C('DB_PREFIX').'service_product as sp on so.product_id=sp.id')
                            ->where(array('so.user_id'=>$userId,'so.payment_type'=>$type))
                            ->order('so.create_time desc')
                            ->limit($page->firstRow,$page->listRows)
                            ->select();
            if (empty($orderlist)) {
                $this->error='内部错误';
                return false;
            }
            return array('data'=>$orderlist,'page'=>$page->show());
        }else{
            $this->error = '非法参数!';
            return false; 
        }
        
    }

    /**
     * [getOrderByOrderID 订单号搜索订单]
     * @param  [int]  $userId  [用户id]
     * @param  [str]  $orderNo [订单号]
     * @param  [int]  $type    [订单分类]
     * @return [void]           []
     */
    public function getOrderByOrderNo($userId,$orderNo,$type=1){
        if ($userId&&$orderNo){
            $pageCount=$this->where(array('user_id'=>$userId,'payment_type'=>$type,'order_no'=>array('like',"%{$orderNo}%")))->count('id');
            $page=get_page($pageCount,10);
            $result=$this->field('so.*,sp.name')
                        ->join('as so left join '.C('DB_PREFIX').'service_product as sp on so.product_id=sp.id')
                        ->where(array('so.user_id'=>$userId,'so.payment_type'=>$type,'so.order_no'=>array('like',"%{$orderNo}%")))
                        ->order('so.create_time desc')
                        ->limit($page->firstRow,$page->listRows)
                        ->select();
            if ($result) {
                return array('data'=>$result,'page'=>$page->show());
            }else{
                $this->error='订单号不存在';
                return false;
            }
        }else{
            $this->error = '非法参数!';
            return false; 
        }
    }
    /**
     * [seacrchBillList 账单列表]
     * @param  [array]  $where    [查询条件]
     * @param  integer $pageSize [分页大小]
     * @return [void]            
     */
    public function getBillList($where,$pageSize=10){
        if (empty($where)) {
            $this->error='查询条件不能为空';
            return false;
        }else{
            $pageCount=$this->where($where)->count('id');
            if (!$pageCount) {
                $this->error='没有记录';
                return false;
            }
            $page=get_page($pageCount,$pageSize);
            $result=$this->field(true)->where($where)->limit($page->firstRow,$page->listRows)->select();
            foreach ($result as $k => $v) {
                $spo=M('service_product_order');
                $tmp=$spo->field('sp.name,spo.inc_abort_payment_date,spo.inc_create_bill_date,spo.sala_create_bill_date,spo.sala_abort_payment_date')
                            ->join('as spo left join '.C('DB_PREFIX').'service_product as sp on sp.id=spo.product_id')
                            ->where(array('sp.id'=>$v['product_id'],'spo.user_id'=>$v['user_id']))
                            ->find();

                if (!empty($tmp)) {
                    $result[$k]=array_merge_recursive($result[$k],$tmp);
                }

                 //统计服务人数
                $tmpTime=strtotime($v['create_time']);
                $result[$k]['orderdate']=substr($v['order_date'],0,4).'/'.substr($v['order_date'],4,2);
                if ($v['payment_type']==2) {
                    $sos=M('service_order_salary');
                    $result[$k]['salcreadate']=date('Y/m',$tmpTime).'/'.$tmp['sala_create_bill_date'];
                    $result[$k]['salpaydate']=date('Y/m',$tmpTime).'/'.$tmp['sala_abort_payment_date'];
                    $count=$sos->where(array('service_order_id'=>$v['id'],'state'=>array('egt',0)))->group('base_id')->count('id');
                    $result[$k]['count']=$count;
                    $name="工资";
                }else{
                    $soi=M('service_order_insurance');
                    $result[$k]['inccreadate']=date('Y/m',$tmpTime).'/'.$tmp['inc_create_bill_date'];
                    $result[$k]['incpaydate']=date('Y/m',$tmpTime).'/'.$tmp['inc_abort_payment_date'];
                    $count=$soi->where(array('service_order_id'=>$v['id'],'state'=>array('egt',0)))->count('id');
                    $result[$k]['count']=$count;
                    $name='社保公积金';
                }
                
                $result[$k]['ordername']=$v['order_date'].' '.$name;
                if (!empty($v['pay_time'])) {
                    $tmpTime=strtotime($v['pay_time']);
                    $result[$k]['payday']=date('Y/m/d',$tmpTime);
                    $result[$k]['payhs']=date('H:i',$tmpTime);
                    $result[$k]['paystate']='已支付';
                }else{
                    $result[$k]['paystate']='未支付'; 
                }
            
                switch ($v['state']) {
                    case '0':
                        $result[$k]['paystate']='待审核';
                        break;
                    case '1':
                        $result[$k]['paystate']='审核通过';
                        break;
                    case '-1':
                        $result[$k]['paystate']='审核失败';
                        break;
                
                }    
            }
            return array('data'=>$result,'page'=>$page->show());
        }
    }

    /**
     * [getOrderDate 获取企业的账单月份]
     * @param  [int] $userId [企业id]
     * @return [void]         
     */
    public function getOrderDate($where){
        if ($where) {
            $result=$this->field('order_date')->where($where)->group('order_date')->select();
            return $result;
        }else{
            $this->error='非法参数';
            return false;
        }
        
    }
}
?>