<?php
namespace Company\Model;
use Think\Model;

class PayModel extends Model{
    protected $trueTableName = 'zbw_pay_order';

    // protected $_serviceInsuranceDetail;
    // protected $_personInsuranceInfo;
    // protected $_personInsurance;
    // protected $_userServiceProvider;
    // protected $_ServiceOrderSalary;
    // protected $_ServiceProductOrder;
    // protected $_serviceInfo;
        protected $_jsPayMd5Key;

    public function _initialize(){
        // $this->_serviceInsuranceDetail = M('service_insurance_detail');
        // $this->_personInsuranceInfo = M('person_insurance_info');
        // $this->_personInsurance = M('person_insurance');
        // $this->_userServiceProvider  = M('user_service_provider');
        // $this->_ServiceOrderSalary  = M('service_order_salary');
        // $this->_ServiceProductOrder = M('service_product_order');
           $this->_jsPayMd5Key  = 'job5156chinabankpayment';

    }   


    /**
     * 服务商信息
     */
    protected function serviceInfo($user_id){   
       return $this->_serviceInfo = $this->_userServiceProvider->field('company_id')->where(array('user_id'=> $user_id))->find();        

    }
    /**
     * 支付宝
     */
    public function alipay($data){
        require(LIB_PATH.'Vendor/alipay/alipay.config.php');
        import('Vendor.alipay.lib.alipay_submit');
        /**************************请求参数**************************/
        $notify_url = 'http://'.$_SERVER['SERVER_NAME']."/Company-Pay-notify_url.html";//服务器异步通知页面路径
        $return_url = 'http://'.$_SERVER['SERVER_NAME']."/Company-Pay-successCalback.html";//页面跳转同步通知页面路径
        $exter_invoke_ip = get_client_ip();//客户端的IP地址
        $anti_phishing_key = "";      

        $out_trade_no =  $data['order_no'];//商户订单号
        $subject =  $this->getSubject($data);   //订单名称
        $total_fee = $data['price'];//付款金额
        ///$total_fee =  '0.01';//元
        $body =  $this->getSubject($data);//订单描述
        /************************************************************/  
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "partner" => trim($alipay_config['partner']),
            "seller_email" => trim($alipay_config['seller_email']),
            "payment_type"  => $alipay_config['payment_type'],
            "notify_url"    => $notify_url,
            "return_url"    => $return_url,
            "out_trade_no"  => $out_trade_no,
            "subject"   => $subject,
            "total_fee" => $total_fee,
            "body"  => $body,
            "show_url"  => '',
            "anti_phishing_key" => $anti_phishing_key,
            "exter_invoke_ip"   => $exter_invoke_ip,
            "_input_charset"    => trim(strtolower($alipay_config['input_charset'])),
        );
        //建立请求
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "正在跳转中");
        return $html_text;
}
    
    /**
     * 网银在线
     */
    public function jdpay($data){
       $param = array();
      //****************************************       
       $param['v_mid'] = '22203897';// 1001是网银在线的测试商户号，商户要替换为自己的商户号。

       $param['v_url'] = 'http://'.$_SERVER['SERVER_NAME']."/Company-Pay-jdpaySuccess.html";   // 商户自定义返回接收支付结果的页面。对应Receive.php示例。
                                                            //参照"网银在线支付B2C系统商户接口文档v4.1.doc"中2.3.3.1
        
        $param['key']   = $this->_jsPayMd5Key ;                                    // 参照"网银在线支付B2C系统商户接口文档v4.1.doc"中2.4.1进行设置。

        $param['remark2'] = '[url:=http://'.$_SERVER['SERVER_NAME'].'/Company-Pay-jdpaynotify_url.html]'; //服务器异步通知的接收地址。对应AutoReceive.php示例。必须要有[url:=]格式。
                                                                    //参照"网银在线支付B2C系统商户接口文档v4.1.doc"中2.3.3.2。
    //****************************************      
    $param['v_amount'] = 0.01;//trim($data['price']);                   //支付金额                 
    $param['v_moneytype'] = "CNY";                                            //币种
    $param['v_oid'] = $data['order_no'];

    $param['text'] = $param['v_amount'].$param['v_moneytype'].$param['v_oid'].$param['v_mid'] .$param['v_url'].$param['key'];        //md5加密拼凑串,注意顺序不能变
    $param['v_md5info'] = strtoupper(md5($param['text']));                             //md5函数加密并转化成大写字母

     $param['remark1'] = '';                     //备注字段1
    $param['v_rcvname']   = ''  ;     // 收货人
    $param['v_rcvaddr']   = ''  ;     // 收货地址
    $param['v_rcvtel']    = ''   ;     // 收货人电话
    $param['v_rcvpost']   = ''  ;     // 收货人邮编
    $param['v_rcvemail']  = '' ;     // 收货人邮件
    $param['v_rcvmobile'] = '';     // 收货人手机号

    $param['v_ordername']   = ''  ; // 订货人姓名
    $param['v_orderaddr']   = ''  ; // 订货人地址
    $param['v_ordertel']    = ''  ; // 订货人电话
    $param['v_orderpost']   = '' ; // 订货人邮编
    $param['v_orderemail']  = '' ; // 订货人邮件
    $param['v_ordermobile'] = ''; // 订货人手机号 

    return array('param'=> $param);
    }

    /**
     * 获取订单名
     */
    protected function getSubject($data){
        //企业名称
        $CompnayInfo = M('company_info')->field('company_name')->where(array('user_id'=> $data['user_id']))->find();
        $pay_type = adminState()['pay_order_type'];
        return $CompnayInfo['company_name'].date('Y-m-d').$pay_type[$data['payType']];
    }

}