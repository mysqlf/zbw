<?php
/**
* 社保公积金订单控制器
*/
namespace Company\Controller;
use OT\DataDictionary;

class OrderController extends HomeController
{
    public function index(){
        $payOrder=D('PayOrder');
        $where=array('po.user_id'=>$this->mCuid,'po.type'=>array('neq',''));
        $result=$payOrder->getOrderListOfSearch($where);
        $Pro=D('ProductOrder');
        $prolist=$Pro->getMyProList($this->mCuid);#获取购买的产品
        $userServiceProvider=D('UserServiceProvider');
        $Scom=$userServiceProvider->getServiceComByUserid($this->mCuid);#获取服务商
        $this->assign('scom',$Scom);
        $this->assign('serviceProductOrderResult',$prolist);
        $this->assign('list',$result['data']);
        $this->assign('page',$result['page']);
        $this->display();
    }
    /**
     * [getPriceByOrderid 获取订单金额]
     * @return [type] [description]
     */
    public function getPriceByOrderid(){
        $orderId=intval(I('param.orderId'));
        $handle = intval(I('post.handle' , ''));
        $ordertmp=D('PayOrder')->getPayOrderPriceByOrderid($this->mCuid,$orderId);
        $order=$ordertmp[0];
        if ($orderId) {
            $m = M('pay_order' , 'zbw_');
            $unsettle = M('user_service_provider' , 'zbw_')->where("user_id={$order['user_id']} AND company_id={$order['company_id']}")->getField('diff_amount');
            $result = array(
                        'amount'        => $order['amount'],
                        'diff_amount'   => 0.00,
                        'actual_amount' => $order['amount']
                    );
            if ($order)
            {
                if (2 == $order['type'])
                {
                    
                    if (2 == $handle)
                    {
                        $this->ajaxReturn(array('status'=>1,'result'=>array('actual_amount'=>($order['amount']+$order['diff_amount']),'amount'=>$order['amount'],'diff_amount'=>$order['diff_amount'])));
                        exit();
                    }
                    $settle = $order['amount']+$unsettle;
                    if($order['diff_amount']!=0){
                        $this->ajaxReturn(array('status'=>1,'result'=>array('actual_amount'=>($order['amount']+$order['diff_amount']),'amount'=>$order['amount'],'diff_amount'=>$order['diff_amount'])));
                        exit();
                    }
                    if ((int)$unsettle==0||!$unsettle)
                    {
                        $this->ajaxReturn(array('status'=>1,'result'=>$result));
                    }
                    else if ($settle <= 0)
                    {
                        $msg = '您的未结差额已够抵付当前订单，立即结算？';
                        $type=1;
                    }
                    else if ($settle > 0)
                    {
                        $msg = "您有未结差额{$unsettle}元，立即结算？";
                        $type=0;
                    }
                    if (!$handle)
                    {
                        $this->ajaxReturn(array('status'=>1,'info'=>$msg,'enough'=>$type,'confrim'=>1));
                    }
                    else if (1 == $handle)
                    {
                        if ($settle <= 0)
                        {
                            M('pay_order' , 'zbw_')->where("id={$orderId} AND user_id={$this->mCuid}")->save(array('diff_amount' => -$order['amount'],'state'=>1,'pay_time'=>date('Y-m-d H:i:s'),'pay_type'=>1));

                            M('user_service_provider' , 'zbw_')->where("user_id={$order['user_id']} AND company_id={$order['company_id']}")->save(array('diff_amount' => $settle));

                            $this->ajaxReturn(array('status'=>1,'result'=>array('enough'=>$type,'actual_amount'=>0,'amount'=>$order['amount'],'diff_amount'=>$order['amount'])));
                        }
                        else
                        {
                            M('pay_order' , 'zbw_')->where("id={$orderId} AND user_id={$this->mCuid}")->save(array('diff_amount' => $unsettle));

                            M('user_service_provider' , 'zbw_')->where("user_id={$order['user_id']} AND company_id={$order['company_id']}")->save(array('diff_amount' => 0));

                            $this->ajaxReturn(array('status'=>1,'result'=>array('enough'=>$type,'actual_amount'=>$settle,'amount'=>$order['amount'],'diff_amount'=>$unsettle)));
                        }
                    }
                    /*else if (2 == $handle)
                    {
                        $this->ajaxReturn(array('status'=>1,'result'=>array('actual_amount'=>($order['amount']+$order['diff_amount']),'amount'=>$order['amount'],'diff_amount'=>$order['diff_amount'])));
                    }*/
                }
                else
                {
                    $this->ajaxReturn(array('status'=>1,'result'=>array('actual_amount'=>$order['actual_amount'],'amount'=>$order['amount'],'diff_amount'=>$order['diff_amount'])));
                }
            }
            else
            {
                $this->ajaxReturn(array('status'=>0,'info'=>'系统错误，请稍后重试。'));
            }
            // $payOrder=D('PayOrder');
            // $price=$payOrder->getPriceByUserandOrder($orderId,$this->mCuid);

            // if (false !== $price) {
            //     $this->ajaxReturn(array('status'=>1,'result'=>$price));
            // }else{
            //     $this->ajaxReturn(array('status'=>0,'info'=>$payOrder->getError()));
            // }
        }else{
            $this->ajaxReturn(array('status'=>0,'result'=>'错误订单号'));
        }
    }

    /**
     * [searchRuningBill 账单筛选]
     * @return [type] [description]
     */
    public function searchPayOrder(){
        $orderid=intval(I('param.orderId'));
        $orderNo=I('param.orderNo');//订单号
        $companyId=intval(I('param.companyId'));//公司ID
        $state=intval(I('param.state',''));//付款状态0全部1已支付2未支付
        $type=intval(I('param.type'));//类型0全部1社保2工资3服务
        $paystart=I('param.paystart');//支付
        $payend=I('param.payend');
        /*$creatstart=I('param.creatstart');//创建
        $creatend=I('param.creatend');*/
        if (!empty($orderid)) {
           $where['po.id']=$orderid;
        }
        if (!empty($orderNo)) {//订单号
            $where['po.order_no']=array('like',"%$orderNo%");
        }
        if (!empty($companyId)) {//公司名字
            $where['po.company_id']=$companyId;
        }
        if (!empty($type)) {//单据类型
            $where['po.type']=$type;
        }
        if(!empty($paystart)||!empty($payend)){//支付时间    
            $where['po.pay_time']=self::_makeTimeWhere($paystart,$payend);
        }
        /*if (!empty($creatstart)||!empty($creatend)) {//创建时间
            $where['po.create_time']=self::_makeTimeWhere($creatstart,$creatend);
        }*/

        if($state!==''){//支付状态
            if ($state==2) {
                $where['po.state']=array('lt',$state);   
            }else{
                $where['po.state']=array('eq',$state);    
            }
        }


        if (!empty($where)) {
            $where['po.user_id']=$this->mCuid;
            isset($where['po.type'])?'':$where['po.type']=array('neq','');
            $PayOrder=D('PayOrder');
            $result=$PayOrder->getOrderListOfSearch($where);
        }
        

        $Pro=D('ProductOrder');
        $prolist=$Pro->getMyProList($this->mCuid);#获取购买的产品
        $userServiceProvider=D('UserServiceProvider');
        $Scom=$userServiceProvider->getServiceComByUserid($this->mCuid);#获取服务商
        $this->assign('scom',$Scom);
        $this->assign('serviceProductOrderResult',$prolist);
        $this->assign('list',$result['data']);
        $this->assign('page',$result['page']); 
        $this->assign('data',$data);
        $this->display('index');
    }
    /**
     * [salary 代发工资支付账单详情]
     * @return [type] [description]
     */
    public function paysalary(){
        header('Content-Type: text/html; charset=UTF-8');
        $orderId=intval(I('param.orderId'));
        if (!$orderId) {
            return array('status'=>0,'info'=>'错误的订单号');#$this->error='错误订单';
            exit();
        }
        $serviceOrderSalary=D('ServiceOrderSalary');
        $where=array('sos.pay_order_id'=>array('eq',$orderId),'sos.user_id'=>$this->mCuid);
        $salarylist=$serviceOrderSalary->getSalaryAllByPayOrderid($where);
        foreach ($salarylist as $key => $value) {
            $salarylist[$key]['price']=sprintf("%01.2f",($value['price']+$value['af_service_price']));
        }
        $payOrder=D('PayOrder');
        $payOrderinfo=$payOrder->getPayOrderPriceByOrderid($this->mCuid,$orderId);
        $this->assign('payorder',$payOrderinfo[0]);
        $this->assign('list',$salarylist);
        $this->display();
        
        
    }
    /**
     * [inc 社保公积金支付账单详情]
     * @return [type] [description]
     */
    public function payinc(){
        header('Content-Type: text/html; charset=UTF-8');
        $orderId=intval(I('param.orderId'));
        if (!$orderId) {
            return array('status'=>0,'info'=>'错误的订单号');#$this->error='错误订单';
            exit();
        }
        $ServiceInsuranceDetail=D('ServiceInsuranceDetail');
        $result=$ServiceInsuranceDetail->getSGAllByOrderid($this->mCuid,$orderId);
        
        $result=deal_inc($result);
        $payOrder=D('PayOrder');
        $payOrderinfo=$payOrder->getPayOrderPriceByOrderid($this->mCuid,$orderId);
        if ($payOrderinfo[0]['state']!=1 && $payOrderinfo[0]['state']!= -1) {
            if ($payOrderinfo[0]['diff_amount']==0) {
                $userServiceProvider=D('UserServiceProvider');
                $where=array('user_id'=>$this->mCuid,'company_id'=>$payOrderinfo[0]['company_id']);
                $payOrderinfo[0]['diff_amount']=$userServiceProvider->getDiffAmount($where);
                $this->assign('payorderdiff',1);
            }else{
               /* $userServiceProvider=D('UserServiceProvider');
                $where=array('user_id'=>$this->mCuid,'company_id'=>$payOrderinfo[0]['company_id']);
                $payOrderinfo[0]['diff_amount']=$userServiceProvider->getDiffAmount($where);*/
                $this->assign('payorderdiff',0);
            }
        }
        if (strtotime($payOrderinfo[0]['pay_deadline'])>time()) {
            $this->assign('canpay',1);
        }else{
            $this->assign('canpay',2);
        }

        $this->assign('payorder',$payOrderinfo[0]);
        $this->assign('list',$result);
        $this->display();
    }
    /**
     * [service 服务支付账单详情]
     * @return [type] [description]
     */
    public function payservice(){
        header('Content-Type: text/html; charset=UTF-8');
        $orderId=intval(I('param.orderId'));
        if (!$orderId) {
            return array('status'=>0,'info'=>'错误订单');#$this->error='错误订单';
            exit();
        }
        $ServiceProductOrder=D('ServiceProductOrder');
        $tmp=$ServiceProductOrder->getProOrderByPayOrderid($orderId);//产品信息
        $proinfo=$tmp[0];
        $proinfo['product_detail']=htmlspecialchars_decode($tmp[0]['product_detail']);
        $payOrder=D('PayOrder');
        $payOrderinfo=$payOrder->getPayOrderPriceByOrderid($this->mCuid,$orderId);//订单信息
        $this->assign('payorder',$payOrderinfo[0]);
        #var_dump($payOrderinfo);die;
        $this->assign('proinfo',$proinfo);
        $this->display();
    }
    /**
     * [getOrderNo 通过订单id获取银行信息]
     * @return [type] [description]
     */
    public function getOrderBank(){
        $orderid=intval(I('param.orderId'));
        if ($orderid) {
            $PayOrder=D('PayOrder');
            $bank=$PayOrder->getOrderBankByOrderid($orderid,$this->mCuid);
            if (!empty($bank['account'])) {
                $this->ajaxReturn(array('status'=>1,'result'=>$bank));
            }else{
                $this->ajaxReturn(array('status'=>0,'info'=>$PayOrder->getError()));
            }
        }else{
            $this->ajaxReturn(array('status'=>0,'result'=>'错误订单'));
        }

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