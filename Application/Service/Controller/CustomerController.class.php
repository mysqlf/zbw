<?php
namespace Service\Controller;

/** 
 * 产品订单
 */
class 	CustomerController extends ServiceBaseController{
    private $_ProductOrder;
    protected $_state;
    protected $service_state;
    protected $_PayType;
    private $_product;

    protected function _initialize()
    {
        parent::_initialize();
        $this->_ProductOrder =  D('ProductOrder');
       // $this->_state = array(0=>'未支付', 1=>'已支付', -1=>'支付失败', -9=>'删除', -2=>'撤销', 2=>'确认付款');
        $this->_state = array(0=>'待支付', 1=>'支付成功', -1=>'支付失败');
        $this->service_state = array(0=>'未签约', 1=>'已签约', -1=>'停止服务', 3=>'服务完成', 2=>'服务中');
        $this->_PayType = array(1=>'线上支付', 2=>'线下支付');
    }  


	/**
     * 企业套餐管理
     */
    public function productList()
    {
        $id = I('post.product_id', '0');
        $company_name = I('post.company_name', '');
        $state = I('post.state', '');
        $service_state = I('post.service_state', '');

        $blongToService = $this->blongToService();        
        $where = 'p.user_id in('.$blongToService.') AND usp.company_id='.$this->_cid.' AND s.company_id='.$this->_cid;
        

        $serviceInfo = $this->serviceInfo();
        if($serviceInfo['group']==3){
            $where .= ' AND usp.admin_id ='.getServiceAdminId($this->_uid);
        }

        if($id){
            $where .=' AND p.id='.$id;
        }
        if($company_name){
            $where .=' AND c.company_name LIKE \'%'.$company_name.'\'';
        }

        if(is_numeric($state)){
            $where .=' AND p.state='.$state;
        }
        if(is_numeric($service_state)){
            $where .=' AND p.service_state='.$service_state;
        }

        $result = $this->_ProductOrder->comMembersOrderList($where);
        //dump($result);
        $this->assign('result', $result)->assign('_state', $this->_state)->assign('service_state', $this->service_state)->assign('_PayType', $this->_PayType);
    	$this->display('Customer/package_manage');
    }


    /**
     * 服务详情
     * @return [type] [description]
     */
    public function productOrderDetail()
    {
        $id = intval(I('get.id',1));
        $where = "p.state <> -9 AND service_type <>-1 AND p.id = ".$id." AND s.company_id  = ".$this->_AccountInfo['company_id'] ;
        $result = $this->_ProductOrder->comMemBersDetail($where);
        if($result['turn_id']){
          $productInfo =  $this->_ProductOrder->productInfo($result['turn_id']);

          $result['return_pro_id'] = $productInfo['id'];
          $result['return_pro_name'] = $productInfo['name'];
        }

//dump($result);
        $this->assign('result',$result)->assign('_state', $this->_state)->assign('service_state', $this->service_state)->display('Customer/service_detail');
    }


    /**
     * 切换合同-当前服务商下企业
     */
    protected function selectCompany(){
            $blongToService = $this->blongToService();
            if(empty($blongToService)) return;
            $result = M('company_info')->alias('ci')->field('id,company_name,user_id')->where('user_id in('.$blongToService.')')->select();
            return $result;
        //    dump($result);
//        $result = M('user_service_provider')->field()->where()->select();
    }

    /**
     * 切换合同-企业购买过的服务
     * 
     */
    public function selectProduct(){
        if(IS_POST){
            $user_id = I('post.user_id', '0');
            $company_id = I('get.company_id', '0'); 
            $result = $this->_ProductOrder->alias('po')->field('ci.company_name,sp.name, po.id')
                            ->join('zbw_company_info ci ON ci.user_id = po.user_id')
                            ->join('zbw_service_product sp ON sp.id = po.product_id')
                            ->where(array('po.user_id'=> $user_id, 'po.service_state'=> 2, 'sp.state'=> array('neq', -9), 'sp.company_id'=> $this->_cid))->select();
                   
            $this->ajaxReturn(array('status'=>0,'msg'=>'', 'data'=> $result));
        }
    }

    /**
     * 切换合同-设定服务
     */
    // public function setService(){
    //     if(IS_POST){
    //        // $_product['id'] = I('post.id', '0');
    //         $_product['user_id'] = I('post.user_id', '0');
    //         $_product['product_id'] = I('post.product_id', '0');
    //       //  $_product['overtime'] = I('post.overtime', '');
    //       //  $_product['inc_handle_days'] = I('post.inc_handle_days', '0');
    //        // $_product['is_salary'] = I('post.is_salary', '0');
    //         $_product['price'] = I('post.price', '0');
    //         $_product['type'] = I('post.type', '1');
    //         $hidden_city = I('post.serviceLayerForm','', 'htmlspecialchars_decode');        
    //         parse_str($serviceLayerForm, $_serviceLayerForm);
    //         $_product['is_salary'] = $_serviceLayerForm ['is_salary'];          
    //         $_product['service_state'] = $_serviceLayerForm ['service_state'];
    //         $_product['overtime'] = $_serviceLayerForm ['overtime'];
    //         $_product['inc_handle_days'] = $_serviceLayerForm ['inc_handle_days'];
    //         $result = $this->_ProductOrder->createProductOrder($_product, $admin);

    //     }
    // }


    /**
     * 添加切换合同
     */
    public function addContractChange(){
         if(IS_POST){
            // $this->_product['id'] = I('post.id', '83');
            // $this->_product['user_id'] = I('post.user_id', '55');
            // $this->_product['product_id'] = I('post.product_id', '24');
            // $this->_product['old_id'] = I('post.old_id', '74');
            // $this->_product['price'] = I('post.price', '2300'); 
            // $_product['id'] = I('post.id', '0');
            $_product['user_id'] = I('post.user_id', '0');
            $_product['product_id'] = I('post.product_id', '0');
            $_product['price'] = I('post.price', '0');
            $_product['type'] = I('post.type', '1');
            $_product['old_id'] = I('post.old_id', '0');

          //  $serviceLayerForm = I('post.serviceLayerForm','', 'htmlspecialchars_decode');        
         //   parse_str($serviceLayerForm, $_serviceLayerForm);
            $_product['is_salary'] = I('post.is_salary', '0');
            $_product['service_state'] = I('post.service_state', '0');
            $_product['overtime'] =I('post.overtime', '');
            $_product['overtime']  = str_replace('/', '-', $_product['overtime']);
            $_product['inc_handle_days'] = 3;
            $_product['af_service_price'] =I('post.af_service_price', '0');
            //id=&service_state=2&overtime=2016%2F08%2F25&inc_handle_days=5&is_salary=1
            //dump($_product);die();
            $result = $this->_ProductOrder->addContractChange($_product, $this->_AccountInfo);
        }else{
            $selectCompany = $this->selectCompany();
            $productAllList = $this->productAllList();
            $this->assign('_selectCompany', $selectCompany)->assign('_productAllList', $productAllList)->display('Customer/add_switch_contract');
      }
    }  

    /**
     * 删除服务城市列表
     */
    public function deleteLocation(){
        if(IS_POST){
            $location_id= I('post.location_id', '0');
            $service_product_order_id= I('post.id', '0');
            if(empty($location_id) || empty($service_product_order_id)) ajaxJson(-1, '参数错误！');
            $result = $this->_ProductOrder->deleteLocation(array('location_id'=>$location_id, 'service_product_order_id'=> $service_product_order_id), $this->_AccountInfo);
        }

    }

    /**
     * 服务详情->服务设定
     */
    public function   editProductOrder(){
        if(IS_POST){
            $this->_product['id'] = I('post.ipt_hidden', '0');
         //   $this->_product['user_id'] = I('post.user_id', '0');
          //  $this->_product['product_id'] = I('post.product_id', '0');
            $this->_product['service_state'] = I('post.service_state', '0');
            $this->_product['overtime'] = I('post.overtime', '');
            $this->_product['overtime']  = str_replace('/', '-', $this->_product['overtime']);
            $this->_product['inc_handle_days'] = I('post.inc_handle_days', '3');
            $this->_product['is_salary'] = I('post.is_salary', '0');
            $this->_product['is_salary'] = I('post.is_salary', '0');
            $this->_product['type'] = I('post.type',1);
            $this->_product['af_service_price'] = I('post.af_service_price','0');            
            $result = $this->_ProductOrder->comSetService($this->_product, $this->_AccountInfo);
            $this->ajaxReturn ($result);

        }
    }

    /**
     * 切换合同-是否已添加过切换套餐及过期时间
     */
    public function isTurnID(){
        if(IS_POST){
            $id = I('post.id', '0');
            if(empty($id))  $this->ajaxReturn(array('status'=>-1,'msg'=>'参数错误！'));
            $result = $this->_ProductOrder->isTurnID($id);
        }
    }
}