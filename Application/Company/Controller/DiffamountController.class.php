<?php
/**
* 差额控制器
*/
namespace Company\Controller;
use OT\DataDictionary;
class DiffamountController extends HomeController{
    /**
     * [index 首页]
     * @return [type] [description]
     */
    public function index(){
        $ServiceDiff=D('ServiceDiff');
        $result=$ServiceDiff->GetAllByUser($this->mCuid);
        $Usp=D('UserServiceProvider');
        $Serlist=$Usp->getServiceComByUserid($this->mCuid);//服务商列表
        $typearr=array('1'=>'办理失败','规则调整','缴费异常');
        $this->assign('types',$typearr);
        $this->assign('scom',$Serlist);
        $this->assign('list',$result['data']);
        $this->assign('page',$result['page']);
        $this->display();
       
        
    }
    /**
     * [diffInfo 详情页]
     * @return [type] [description]
     */
    public function diffInfo(){
        $diffid=intval(I('param.diffid'));
        if (!$diffid){
            $this->error("错误的对账单");

        }else{
            $ServiceDiff=D('ServiceDiff');
            $result=$ServiceDiff->getDiffInfoById($this->mCuid,$diffid);
            $result['pay_date']=substr($result['pay_date'],0,4).'/'.substr($result['pay_date'],4,2);
            $current=json_decode($result['current'],true);
            foreach ($current as $key => $value) {
                $current[$key]['sum']=$value['company']['sum']+$value['person']['sum'];
                if (isset($value['total'])) {
                    $current[$key]['sum']=$value['total'];
                }
            }
            $menu=array('1'=>'社保','2'=>'公积金');
            $typearr=array('1'=>'办理失败','规则调整','缴费异常');
            $this->assign('menu',$menu);
            $this->assign('types',$typearr);
            $this->assign('diff',$result);
            $this->assign('list',$current);
            $this->display();
        }
    }
    public function Billdiff(){
        $billid=intval(I('get.billid'));
        if (!$billid) {
            $this->error('错误的对账单');
        }else{
            $PayOrder=D('ServiceDiff');
            $result=$PayOrder->getBillDiff($billid);
            $Usp=D('UserServiceProvider');
            $Serlist=$Usp->getServiceComByUserid($this->mCuid);//服务商列表
            $this->assign('scom',$Serlist);
            $typearr=array('1'=>'办理失败','规则调整','缴费异常');
            $this->assign('types',$typearr);
            $this->assign('list',$result);
            $this->display('index');
        }

    }
    /**
     * [searchDiff 筛选]
     * @return [type] [description]
     */
    public function searchDiff(){
        $order=I('param.orderNo');
        $name=I('param.name');
        $companyId=I('param.companyId');
        $paydate=I('param.paydate');
        $paystart=I('param.paystart');
        $payend=I('param.payend');
        $item=I('param.item');
        $type=I('param.type');
       
        if (!empty($paydate)) {
            $where['sid.pay_date']=str_replace('-','',$paydate);
        }
        if (!empty($paystart)||!empty($payend)) {
            $where['po.pat_time']=self::_makeTimeWhere($paystart,$payend);
        }
        if (!empty($name)) {
            $where['pb.person_name']=array('like',"%$name%");
        }
        if (!empty($type)) {
            if ($type==5) {
                $where['sd.type']=array('lt',$type);
            }else{
                $where['sd.type']=$type;
            }
            
        }
        if (!empty($item)) {
            if ($item==4) {
                $where['sd.item']=array('lt',$item);
            }else{
                $where['sd.item']=$item;
            }
            
        }
        if (!empty($companyId)) {
            $where['po.company_id']=$companyId;
        }
        if (!empty($where)) {
            $where['pii.user_id']=$this->mCuid;
            $where['po.state']=1;
            $where['sd.current']=array('neq','');
            $ServiceDiff=D('ServiceDiff');
            $result=$ServiceDiff->getDiffOfSearch($where);
        }
        $typearr=array('1'=>'办理失败','规则调整','缴费异常');
        $this->assign('list',$result['data']);
        $this->assign('page',$result['page']);
        $this->assign('types',$typearr);
        $Usp=D('UserServiceProvider');
        $Serlist=$Usp->getServiceComByUserid($this->mCuid);//服务商列表
        $this->assign('scom',$Serlist);
        $this->display('index');
        
    }
     /**
     * [_makeTimeWhere 组合时间条件]
     * @param  string $start [开始时间]
     * @param  string $end  [结束时间]
     * @return array       
     */
    private function _makeTimeWhere($start='',$end=''){
        if (!empty($end)) {
            $end=$end.'-31 23:59:59';
            if (!empty($start)) {
                $start=$start.'-01 00:00:00';
                return array('between',array($start,$end));
            }else{
                return array('lt',$end);
            }
        }else{
            if (!empty($start)) {
                $start=$start.'-01 00:00:00';
                return array('gt',$start);
            }else{
                return array('between',array('2016-01-01 00:00:00',date('Y-m-d H:i:s')));
            }
        }
    }
}
 ?>