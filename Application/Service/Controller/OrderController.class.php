<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Service\Controller;

/**
 * 订单
 */
class OrderController extends ServiceBaseController
{

    private $_ServiceOrder;
    private $_ServiceOrderDetail;
    private $_Order;
    private $_Audit;
	private $_Salary;
    private $_State;
    private $_Company;

        
    protected function _initialize()
    {
        parent::_initialize(); 
        $this->_Order = D('ServiceOrder');

    }




    /*
     * 公司订单列表，有分页
     */

    public function comOrderList()
    {
		$data['company_id'] = $this->_AccountInfo['company_id'];
		if(!$data['company_id']){
			echo '参数不全~';exit;
		}
		$product = D('serviceOrder');
		$res = 	$product->comOrderList($data);
		$this->assign('res', $res);
//		echo "<pre>";var_dump($res);exit;
		$this->display('Order/comOrderList');
		
    }
    /*
     * 参保人信息及审核 
     */
    public function comOrderDetail()
    {
        $data['user_id'] = I('get.user_id', '0');
        $data['base_id'] = I('get.base_id', '0');
		if(!$data['user_id']||!$data['base_id']){
			echo '参数不全~';exit;
		}
		// $product = D('serviceOrder');
		// $res = $product->comOrderDetail($data);
		// $this->assign('res', $res)->assign('url', $this->_ServiceOrderDetail);
		//echo "<pre>";var_dump($res);exit;
		$this->display('Order/comOrderDetail');
    }

    /*
     * 代发工资
     */
    public function comSalaryList()
    {
		$data['service_order_id'] = $this->_ServiceOrderDetail['service_order_id'];
		$data['order_no'] = $this->_Salary['order_no'];
		if(!$data['service_order_id']||!$data['order_no']){
			echo '参数不全~';exit;
		}
		$service = D('serviceOrder');
		$res = $service->comSalaryList($data);
		$this->assign('res', $res)->assign('url', $this->_ServiceOrderDetail);
		//echo "<pre>";var_dump($res);exit;
		$this->display('Order/payroll');
    }
    /*
     * 审核
     * 1在保，2报增，3报减
     */
    public function audit()
    {
        if(IS_POST)
        {
            $result = $this->_Order->audit( $this->_Audit,$this->_AccountInfo);
            $this->ajaxReturn ($result);
        }
        else
        {
            $result =  $this->_Order->audit($this->_ServiceOrderDetail,$this->_AccountInfo);
//            if(empty($result)) echo "404";
            $this->assign('result',$result)->assign('state',$this->_State);
            //dump($result);
            $this->display('Order/addAudit');

        }
    }
    public function viewDetail()
    {
        $result =  $this->_Order->audit($this->_ServiceOrderDetail,$this->_AccountInfo);
        $this->assign('result',$result)->assign('state',$this->_State);
        $this->display('Order/auditDetail');
    }

    /*
     * 工资审核展示操作
     */
    public function salaryAudit()
    {
		$data['service_order_salary_id'] = $this->_Salary['service_order_salary_id'];
		//$state = $this -> insuredState($data['service_order_salary_id']);
		if(!$data['service_order_salary_id']){
			die(json_encode(array('status'=>1,'msg'=>'参数不全','data'=>'')));
		}
		$service = D('serviceOrder');
		$res = $service->salaryAudit($data);
		die(json_encode(array('status'=>0,'msg'=>'','data'=>$res)));
    }

	 /*
     * 工资审核数据入库
     */
	public function salaryData(){
		$salary['state'] = $this->_Salary['state'];
		$salary['remark']= $this->_Salary['remark'];
		$salary['wages'] = $this->_Salary['wages'];
		$salary['deduction_income_tax'] = $this->_Salary['deduction_income_tax'];
		$salary['replacement'] = $this->_Salary['replacement'];
		$salary['deduction_other'] = $this->_Salary['deduction_other'];
		$person_base['bank'] = $this->_Salary['bank'];
		$person_base['branch'] = $this->_Salary['branch'];
		$person_base['account'] = $this->_Salary['account'];
		$id  = $this->_Salary['service_order_salary_id'];
		$base_id = $this->_Salary['base_id'];
        $m = M('service_order_salary');
        $res = $m->field('deduction_social_insurance,deduction_provident_fund,state,order_id')->where("id = {$id}")->find();
        $order_id = $res['order_id'];
        unset($res['order_id']);
        if($res['state'] == 2) die(json_encode(array('status'=>1,'msg'=>'已发放成功，请勿重复操作~','data'=>array())));
        $salary['deduction_social_insurance']= $res['deduction_social_insurance'];
        $salary['deduction_provident_fund']= $res['deduction_provident_fund'];
		$state = $this ->insuredState($id);
		if($state == 1){
			$salary['deduction_social_insurance']= $this->_Salary['deduction_social_insurance'];
		}elseif($state == 2){
			$salary['deduction_provident_fund']= $this->_Salary['deduction_provident_fund'];
		}elseif($state == 3){
			$salary['deduction_social_insurance']= $this->_Salary['deduction_social_insurance'];
			$salary['deduction_provident_fund']= $this->_Salary['deduction_provident_fund'];
		}
        $salary['actual_wages'] = $salary['wages'] - $salary['deduction_income_tax'] - $salary['deduction_social_insurance'] - $salary['deduction_provident_fund'];
		if(!$id||!$base_id){
			die(json_encode(array('status'=>1,'msg'=>'参数不全~','data'=>array())));
		}
		$service = D('serviceOrder');
        $bill = $service->billId($id);
		if($salary['state'] == -2 || $salary['state'] == 2){
			if($bill['id']){
                if($salary['state'] == -2 )
                {
                    $data['balance'] = $this->salaryBalance($id,$state,$salary);
                }
                if($salary['state'] == 2 )
                {
                    $data['balance'] = 0;
                }
				$res = $service->salaryData($id,$salary,$base_id,$person_base,$bill,$data,$order_id,$this->_AccountInfo);
			}else{
				die(json_encode(array('status'=>1,'msg'=>'查无此账单','data'=>array())));
			}
		}
        else{
            $service_bill = M('service_bill');
            $bill = $service_bill->alias('b')->field('b.id')->join('LEFT JOIN zbw_service_order_salary s ON s.order_id = b.order_id')->find();
            if($bill['id'])
            {
                if($salary['state'] == 1)
                {
                    die(json_encode(array('status'=>1,'msg'=>'账单已生成，无法审核','data'=>array())));
                }
            }
			$res = $service->salaryData($id,$salary,$base_id,$person_base,'','',$order_id,$this->_AccountInfo);
		}
		die(json_encode(array('status'=>0,'msg'=>'操作成功','data'=>array())));		
	}
	/*
     * 代发工资工资页面撤销操作
     */
	public function salaryRevoke(){
		$id  = $this->_Salary['service_order_salary_id'];
		if(!$id){
			die(json_encode(array('status'=>1,'msg'=>'参数不全~','data'=>array())));
		}
		$service = D('serviceOrder');
		$res = $service->salaryRevoke($id);
		die(json_encode(array('status'=>0,'msg'=>'操作成功','data'=>array())));		
	}
	/*
     * 查询社保公积金在保状态
     */
	public function insuredState($id){
		$service = D('serviceOrder');
		$res = $service->insuredState($id);
		if($res['provident_fund_state']== 0 && $res['social_insurance_state']==0){
			return 3;
		}elseif($res['provident_fund_state'] == 0){//公积金状态
			return 2;
		}elseif($res['social_insurance_state'] == 0){//社保状态状态
			return 1;
		}
	}
	/*
     * 计算结余
     */
	public function salaryBalance($id,$state,$salary){
		$service = D('serviceOrder');
		$res = $service->salaryBalance($id,$state,$salary);
		return $res;
	}
    /*
     * 公司信息
     */
    public function comInfo()
    {
        $result =  $this->_Order->comInfo($this->_Company,$this->_AccountInfo);
        $companyFile = get_companyFile_by_companyId($result['company_id']);
        $this->assign('result',$result)->assign('companyFile',$companyFile);
        $this->display('Bill/comInfo');
    }
}
