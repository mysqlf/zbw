<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/13
 * Time: 16:27
 */

/**
 * 团队管理 
 */
namespace Service\Controller;
use Think\Controller;


class MembersController extends ServiceBaseController
{

    private $_ProductOrder;
    private $_Members;
    private $_Location;
    private $_MembersDate;
    private $_product;  

    protected function _initialize()
    {
        parent::_initialize(); 
        $this->_ProductOrder =  D('ProductOrder');
        $this->_Members = D('Member');
        if(IS_POST){
            $this->_MembersDate['admin_id'] = I('post.admin_id', '0');
            $this->_MembersDate['id'] = I('post.id', '0');

            $this->_product['id'] = I('post.id', '0');
            $this->_product['user_id'] = I('post.user_id', '0');
            $this->_product['product_id'] = I('post.product_id', '0');
            $this->_product['pay_order_id'] = I('post.pay_order_id', '0');
            $this->_product['overtime'] = I('post.overtime', '');
            $this->_product['inc_handle_days'] = I('post.inc_handle_days', '0');
            $this->_product['is_salary'] = I('post.is_salary', '0');
            $this->_product['user_id'] = I('post.user_id', '0');
            $this->_product['type'] = I('post.type',1); 

            $this->_Location['id'] = intval(I('post.location_id',0));
            $this->_Location['location'] = I('post.location',0);
            $this->_Location['soc_service_price'] = doubleval(I('post.soc_service_price',0));
            $this->_Location['af_service_price'] = doubleval(I('post.af_service_price',0));
            $this->_Location['pro_service_price'] = doubleval(I('post.pro_service_price',0));
            $this->_Location['type'] = I('post.type',1); 
        }
    }

    /**
     * 服务订单
     * @return [type] [description]
     */
    // public function comMembersOrderList()
    // {
    //     $where = "p.state <> -9 AND (s.service_type = 1 OR s.service_type = 3) AND s.company_id = ".$this->_AccountInfo['company_id'];
    //     if($this->_AccountInfo['group'] == 3)
    //     {
    //         $where = "p.state <> -9 AND (s.service_type = 1 OR s.service_type = 3)  AND s.company_id = ".$this->_AccountInfo['company_id'].' AND p.admin_id = '.$this->_AccountInfo['id'];            
    //     }
    //     if($company_name)
    //     {
    //         $where .=  "  AND c.company_name like '%{$company_name}%'";
    //     }
    //     if(is_numeric($service_state))
    //     {
    //         $where .=  "  AND p.service_state = {$service_state}";
    //     }
    //     if(is_numeric($state))
    //     {
    //         $where .=  "  AND p.state = {$state}";
    //     }  

    //    // $result =  $this->_ProductOrder->comMembersOrderList($where);
    //     $this->assign('result',$result)->display('Service/comBuyServiceOrder');
    // }

    /**
     * 客户列表 
     * @return [type] [description]
     */
    public function comMembersList()
    {
        $type = I('get.type', '1'); 
        $company_name = I('get.company_name', ''); 
        $admin_state = I('get.admin_state', '0');
        $admin_id = I('get.admin_id', '0');
        $export = I('get.export', '0');


        if($type == 1)
             $where = 'u.type=1';
        else
             $where = 'u.type=3';

        $blongToService = $this->blongToService();
        $where .= " AND ci.user_id in({$blongToService}) AND usp.company_id={$this->_cid}";// AND ci.user_id in({$blongToService})  

        $serviceInfo = $this->serviceInfo();
        if($serviceInfo['group'] == 3){
            $where .= ' AND usp.admin_id ='.getServiceAdminId($this->_uid);
        }

        if($company_name) $where .= ' AND ci.company_name like \'%'.$company_name.'%\'';

        if($admin_id){
            if($admin_id == -1){
                $where .= ' AND usp.admin_id IS NULL';
            }else{
                $where .= ' AND usp.admin_id ='.$admin_id;
           }
        }

        if($export ==1){
            $this->exportMembers($where);exit;
        }        
//echo $where;
        $result =  $this->_Members->MembersList($where);
       // dump($result);
        $serviceGroup = $this->serviceGroup();
        $this->assign('result',$result)->assign('serviceGroup', $serviceGroup);
        $this->display('Customer/company');


    }
    /**
     * 个人客户列表 
     * @return [type] [description]
     */
    public function perMembersList()
    {
        $cord_num = I('get.cord_num', '0');
        $person_name = I('get.person_name', '');
        $location = I('get.location', '');
        $export = I('get.export', '0');

        $where = 'u.type=3';
        $serviceInfo = $this->serviceInfo();
        if($serviceInfo['group'] == 3){
            $where = ' AND usp.admin_id ='.$this->_uid;
        }

        $where .= " AND usp.company_id={$this->_cid} AND usp.state <>-9";

        if($company_name) $where .= ' AND pb.person_name like \''.$person_name.'%\'';
        if($person_name) $where .= ' AND pb.cord_num ='.$cord_num;
        if($location)
        {
            $m = M('location');
            $location = $m->field('id')->where(array('name'=> array('like', '%'.$location.'%')))->find();
            if($location) $where .= ' AND pb.residence_location ='.$location['id'];
        }
        if($export ==1){
            $this->exportMembers($where);exit;
        }          
//echo $where;
        $result =  $this->_Members->perMembersList($where);
        $residenceType = array('未设置', '农村', '城镇');
        $this->assign('result',$result)->assign('residenceType', $residenceType);
        $this->display('Customer/person');
    }
      

    /**
     * 客服列表
     * @return [type] [description]
     */
    public function cServideList()
    {        
        if(IS_AJAX)
        {
            $serviceGroup = $this->serviceGroup();
            $this->ajaxReturn( ajaxJson(0,'',$serviceGroup));
        }
    }

    /**
     * 设置客服
     */
    public function setService()
    {
        if(IS_POST)
        {
            if(empty($this->_MembersDate['admin_id']) || empty($this->_MembersDate['id']) ) $this->ajaxReturn (array('status'=>-100001));//|| ($this->_AccountInfo['group'] != 1))

            $result = $this->_Members->setService($this->_MembersDate,$this->_AccountInfo);
            $this->ajaxReturn ($result);
        }
    }

    // public function comSetPrice()
    // {
    //     if(IS_POST)
    //     {
    //         $result = $this->_ProductOrder->comSetPrice($this->_Members,$this->_AccountInfo);
    //         $this->ajaxReturn ($result);
    //     }
    // }


    // public function comPayment()
    // {
    //     if(IS_POST)
    //     {
    //         if($this->_AccountInfo['group'] == 3) $this->ajaxReturn (array('status'=>-1,'msg'=>'暂无权限','data'=>''));
    //         $result = $this->_ProductOrder->comPayment($this->_Members,$this->_AccountInfo);
    //         $this->ajaxReturn ($result);
    //     }
    // }

    /**
     * 服务状态设定
     */
    // public function comSetService()
    // {
    //     if(IS_POST)
    //     {
    //         $result = $this->_ProductOrder->comSetService($this->_product,$this->_AccountInfo);
    //         $this->ajaxReturn ($result);
    //     }
    // }
    /**
     * 添加及修改服务城市
     */
    public function comAddLocation()
    {
        if(IS_POST)
        {
            if(empty($this->_MembersDate['id']))  $this->error('id错误');
            $result = $this->_ProductOrder->comAddLocation($this->_MembersDate,$this->_Location,$this->_AccountInfo);
            //$this->ajaxReturn ($result);
        }
    }

    public function wLocation()
    {
        $service_product_order_id = I('post.id', '0');
        $m = M('template');
        $result = $m->field('name,location')->where(array('state'=> 1))->select();
        if(empty($result)) $this->ajaxReturn (array('status'=>-1,'msg'=>'未找到参保地','data'=>''));
        foreach ($result as $key => $value) {
            $result[$key]['name'] = showAreaName($value['location']);
        }
        $this->ajaxReturn (array('status'=>1,'msg'=>'','data'=>$result));
    }

    
    /**
     * 企业客户详情
     */
    
    public function companyDetail(){
        $id = I('get.id', '0');
        $id or $this->error('id错误');
        $result = M('company_info')->alias('ci')->field('ci.*, usp.id usp_id,usp.admin_id,cb.bank,cb.branch,cb.account,cb.company_id')
                    ->join('left join zbw_user_service_provider usp ON usp.user_id=ci.user_id')
                    ->join('left join zbw_company_bank cb ON cb.company_id=ci.id')
                    ->where(array('ci.id'=> $id, 'usp.company_id'=> $this->_cid))->find();

    //    dump($result);
        if(empty($result)){
            $this->error('客户不存在!');
        }
        $this->assign('result', $result)->assign('_adminState', adminState())->display('Customer/customer_detail');
    }

    /**
     *企业会员导出
     */
    protected function exportMembers($where=null, $type=1){
        $title = array(array('A1', '企业名称'), array('B1', '所在地'), array('C1', '联系人'), array('D1', '电话'), array('E1', '所属行业'), array('F1', '公司规模'), array('G1', '注册资金'), array('H1', '消费总额'), array('I1', '差额总计'), array('J1', '客服'));
        vendor('PHPExcel.PHPExcel');
        vendor('PHPExcel.Writer.Excel5');
        $objPHPExcel = new \PHPExcel();        
        $objPHPExcel->getProperties()->setCreator("企业名称");
        //设置当前的sheet
        $objPHPExcel->setActiveSheetIndex(0);
        //设置sheet的name
        $objPHPExcel->getActiveSheet()->setTitle('客户列表');
        //设置名称
        foreach ($title as $key => $value) {
           $objPHPExcel->getActiveSheet()->setCellValue($value[0], $value[1]);
        }

       // $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

        $adminState = adminState();

        $result  = M('user_service_provider')->alias('usp')->field('ci.id company_id,ci.contact_phone,ci.tel_city_code,ci.tel_local_number,ci.company_name,ci.location,ci.contact_name,ci.contact_phone,ci.industry,ci.employee_number,ci.register_fund,usp.diff_amount,usp.admin_id,usp.id,usp.price')
                    ->join('left join zbw_user u ON u.id = usp.user_id')
                    ->join('zbw_company_info ci ON ci.user_id = u.id')
                    ->where($where)->select();
        foreach($result as $key=>$val){  
            $objPHPExcel->getActiveSheet()->setCellValue(chr(65) . ($key+2), $val['company_name']);
            $objPHPExcel->getActiveSheet()->setCellValue(chr(66) . ($key+2), $this->convertUTF8(showAreaName1($val['location'])));
            $objPHPExcel->getActiveSheet()->setCellValue(chr(67) . ($key+2), $this->convertUTF8($val['contact_name']));
            $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr(68) . ($key+2), $val['tel_local_number'], 's');
            $objPHPExcel->getActiveSheet()->setCellValue(chr(69) . ($key+2), $adminState['industry'][$val['industry']]);
            $objPHPExcel->getActiveSheet()->setCellValue(chr(70) . ($key+2), $adminState['employee_number'][$val['employee_number']].'人');          
            $objPHPExcel->getActiveSheet()->setCellValue(chr(71) . ($key+2), $val['register_fund'].'万');
            $objPHPExcel->getActiveSheet()->setCellValue(chr(72) . ($key+2), $val['price']);
            $objPHPExcel->getActiveSheet()->setCellValue(chr(73) . ($key+2), $val['diff_amount']);
            $objPHPExcel->getActiveSheet()->setCellValue(chr(74) . ($key+2), serviceAdminName($val['admin_id']));        }   
        
        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        // excel头参数 
        $ua = $_SERVER["HTTP_USER_AGENT"];
        $fileName = $type ==1 ? '企业' : '个人';
        $fileName = $fileName.'会员信息'.date('Y-m-d').'.xls';
        set_filename_header($ua,$fileName);
        $objWriter->save('php://output');  
    }

  protected  function convertUTF8($str)
    {return $str;
       if(empty($str)) return '';
      return  iconv('gb2312', 'utf-8', $str);
    }

    /**
     *个人会员导出
     */
    protected function exportPersonMembers($where=null, $type=3){
        $title = array(array('A1', '姓名'), array('B1', '身份证号'), array('C1', '所在地'), array('D1', '户口性质'), array('E1', '手机号码'), array('F1', '消费金额'), array('G1', '差额总计'), array('H1', '客服'));
        vendor('PHPExcel.PHPExcel');
        vendor('PHPExcel.Writer.Excel5');
        $objPHPExcel = new \PHPExcel();        
        $objPHPExcel->getProperties()->setCreator("企业名称");
        //设置当前的sheet
        $objPHPExcel->setActiveSheetIndex(0);
        //设置sheet的name
        $objPHPExcel->getActiveSheet()->setTitle('客户列表');
        //设置名称
        foreach ($title as $key => $value) {
           $objPHPExcel->getActiveSheet()->setCellValue($value[0], $value[1]);
        }

       // $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $residenceType = array('未设置', '农村', '城镇');
        $adminState = adminState();
        $result  = $this->alias('usp')->field('usp.id,pb.person_name,pb.card_num,pb.residence_location,pb.residence_type,ci.mobile,usp.admin_id,usp.price,usp.diff_amount')
                    ->join('left join zbw_user u ON u.id = usp.user_id')
                    ->join('left join zbw_person_base pb ON pb.user_id = usp.user_id ')
                    ->where($where)->select();
        foreach($result as $key=>$val){  
            $objPHPExcel->getActiveSheet()->setCellValue(chr(65) . ($key+2), $val['person_name']);
            $objPHPExcel->getActiveSheet()->setCellValue(chr(66) . ($key+2), $val['card_num']);
            $objPHPExcel->getActiveSheet()->setCellValue(chr(67) . ($key+2), $this->convertUTF8(showAreaName1($val['residence_location'])));
            $objPHPExcel->getActiveSheet()->setCellValue(chr(68) . ($key+2), $residenceType[$val['residence_type']]);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr(69) . ($key+2), $val['mobile'], 's');     
            $objPHPExcel->getActiveSheet()->setCellValue(chr(70) . ($key+2), $val['price']);
            $objPHPExcel->getActiveSheet()->setCellValue(chr(71) . ($key+2), $val['diff_amount']);
            $objPHPExcel->getActiveSheet()->setCellValue(chr(72) . ($key+2), serviceAdminName($val['admin_id']));}   
        
        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        // excel头参数 
        $ua = $_SERVER["HTTP_USER_AGENT"];
        $fileName = $type ==1 ? '企业' : '个人';
        $fileName = $fileName.'会员信息'.date('Y-m-d').'.xls';
        set_filename_header($ua,$fileName);
        $objWriter->save('php://output');  
    }



}