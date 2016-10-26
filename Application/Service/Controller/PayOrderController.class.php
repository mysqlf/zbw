<?php
namespace Service\Controller;
/**
 * 订单管理
 */
class PayOrderController extends ServiceBaseController 
{
    private $_payOrder;
    private $_data;
    private $_state;
    private $_orderState;
    private $_pay_type;
    private $_cbtype;
    private $_Payment;

    protected function _initialize()
    {
        parent::_initialize(); 
        $this->_payOrder = D('PayOrder');
        $this->_state = array(0=>'待支付', 1=>'支付成功', -1=>'支付失败');
        $this->_orderState = array(1=>'服务订单', 2=>'社保公积金订单', 3=>'代发工资订单');
        $this->_pay_type = array(1=>'线上支付', 2=>'线下支付');
        $this->_cbtype = array(1=>'报增', 2=>'在保', 3=>'报减');

        if(IS_POST){
            $this->_Payment['id'] = I('post.id', '0');
            $this->_Payment['transaction_no'] = I('post.bankNo', ''); 
            $this->_Payment['actual_amount'] = I('post.actual', '0');
        }

    }

    /**
     * 企业
     */
    
    public function comPayOrderList(){
        $order_no = I('get.order_no', '');
        $buy_name = I('get.company_name', '');
        $state = I('get.state', '');
        $type = I('get.type', '');
        $admin_id = I('get.admin_id', '0');
        $create_time = I('get.create_time', '');
        $create_time1 = I('get.create_time1', '');
        $pay_time = I('get.pay_time', '');
        $pay_time1 = I('get.pay_time1', '');

        $where = 'po.company_id ='.$this->_cid.'  AND usp.company_id='.$this->_cid;

        $serviceInfo = $this->serviceInfo();
        if($serviceInfo['group']==3){
            $where .= ' AND usp.admin_id ='.$this->_uid;
        }

        if($order_no) $where .= " AND po.order_no = '{$order_no}'";
        if($buy_name) $where .= ' AND ci.company_name like \'%'.$buy_name.'%\'';
        if(is_numeric($state)) $where .= ' AND po.state = '.$state;
        if($type) $where .= '  AND po.type = '.$type;
        if($admin_id) $where .= '  AND usp.admin_id = '.$admin_id;
        if($create_time) $where .= " AND date_format(po.create_time, '%Y/%m/%d') >= '{$create_time}'";
        if($create_time1) $where .= " AND date_format(po.create_time, '%Y/%m/%d') <= '{$create_time1}'";
        if($pay_time) $where .= " AND date_format(po.pay_time, '%Y/%m/%d') >= '{$pay_time}'";
        if($pay_time1) $where .= " AND date_format(po.pay_time, '%Y/%m/%d') <= '{$pay_time1}'";
//echo $where;
        $result = $this->_payOrder->payOrderList($where, $this->_AccountInfo);
        $this->assign('_state', $this->_state);
        $this->assign('serviceGroup', $this->serviceGroup());
        $this->assign('_pay_type', $this->_pay_type)->assign('_orderState', $this->_orderState)->assign('result', $result)->display('Order/company');
    }

    /**
     * 个人
     */
    public function perPayOrderList(){

        $this->display('Order/person');
    }

    /**
     * 企业明细
     */
    public function payOrderDetail(){
        $data['type'] = I('get.type', '2', 'intval');
        $data['id'] = I('get.id', '441');
        if(empty($data['type']) || empty($data['id'])) $this->ajaxReturn(array('status'=> -1, 'msg'=> '参数不完整'));
        switch ($data['type'] ) {
            case '1':
                $this->productDetail($data);
                break;
            case '2':
                 $this->sbGjjDetail($data);
                break;
            case '3':
                $this->salaryDetail($data);
                break;
        }

    }



    /**
     * 明细
     */
    protected function sbGjjDetail($data){
        $result = $this->_payOrder->sbGjjDetail($data, $this->_AccountInfo);        
        $orderInfo = $this->_payOrder->payOrderInfo($data, $this->_AccountInfo);
        //dump($result);
         $this->assign('_cbtype', $this->_cbtype);
        $this->assign('_state', $this->_state)->assign('_pay_type', $this->_pay_type)->assign('result', $result)->assign('orderInfo', $orderInfo)->display('Order/sbGjj_details');
    }
    /**
     * 工资明细
     */
    protected function salaryDetail($data){
        $result = $this->_payOrder->salaryDetail($data, $this->_AccountInfo);
        $orderInfo = $this->_payOrder->payOrderInfo($data, $this->_AccountInfo);
         // dump($orderInfo);
         $this->assign('_cbtype', $this->_cbtype);
        $this->assign('result', $result)->assign('_state', $this->_state)->assign('_pay_type', $this->_pay_type)->assign('orderInfo', $orderInfo)->display('Order/salary_details');
    } 
    /**
     * 服务套餐明细
     */
    protected function productDetail($data){
         $result = $this->_payOrder->productDetail($data, $this->_AccountInfo);
          $orderInfo = $this->_payOrder->payOrderInfo($data, $this->_AccountInfo);
        $this->assign('result', $result)->assign('_state', $this->_state)->assign('orderInfo', $orderInfo)->assign('_pay_type', $this->_pay_type)->display('Order/product_details');
    }  

    /**
     * 确认付款
     */
    public function confirmPayment(){
        if(IS_POST){
            $serviceInfo = $this->serviceInfo();
            $result = $this->_payOrder->comPayment($this->_Payment, $this->_AccountInfo, $serviceInfo);
        }
    }
}
