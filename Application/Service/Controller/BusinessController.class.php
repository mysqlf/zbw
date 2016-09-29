<?php 
namespace Service\Controller;

/**
 * BusinessController class
 * 业务管理
 * @package Service\Controller
 * @author rohochan
 **/
class BusinessController extends ServiceBaseController{
	public function index(){
		$this->personList();
	}
	
    /**
     * personList function
     * 参保人列表
     * @return void
     * @author rohochan
     **/
    public function personList(){
		$type = I('param.type');
		$location = I('param.location');
		$personName = I('param.personName');
		$cardNum = I('param.cardNum');
		$condition = array();
		$condition['service_company_id'] = $this->_cid;
        $condition['account_info'] = $this->_AccountInfo;
		$location && $condition['location'] = $location;
		$personName && $condition['person_name'] = $personName;
		$cardNum && $condition['card_num'] = $cardNum;
		
        $personInsurance = D('PersonInsurance');
        $personInsuranceResult = $personInsurance->getPersonList($condition,$type);
		if (false !== $personInsuranceResult) {
			//dump($personInsuranceResult);
			$serviceProduct = D('ServiceProduct');
			$serviceProductResult = $serviceProduct->getAllEffectiveServiceProductOrderLocation($this->_cid);
			$this->assign('warrantyLocation',$serviceProductResult);
			$this->assign('result',$personInsuranceResult['data']);
			$this->assign('page',$personInsuranceResult['page']);
        	$this->display('person_list');
		}else {
			$this->error($personInsurance->getError());
		}
    }
    
	/**
	 * insuranceDetail function
	 * 参保详情
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function insuranceDetail(){
		$userId = I('param.userId/d');
		$baseId = I('param.baseId/d');
		$condition = array();
		$condition['user_id'] = $userId;
		$condition['base_id'] = $baseId;
		if (!empty($baseId) && !empty($userId)) {
			$personInsurance = D('PersonInsurance');
			$personInsuranceResult = $personInsurance->getPersonInsuranceByCondition($condition);
			if (false !== $personInsuranceResult) {
				$personInsuranceResult['propiPaymentInfoValue'] = json_decode($personInsuranceResult['propi_payment_info'],true);
				//获取身份证图片
				$personInsuranceResult['idCardImg'] = get_idCardImg_by_baseId($personInsuranceResult['base_id']);
				//dump($personInsuranceResult);
				$this->assign('result',$personInsuranceResult);
				$this->display('insurance_detail');
			}else {
				$this->error($personInsurance->getError());
			}
		}else {
			$this->error('非法参数！');
		}
	}
	
	/**
	 * insurancePayDateDetail function
	 * 根据缴纳月份获取参保详情
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function insurancePayDateDetail(){
		if (IS_POST) {
			$userId = I('param.userId/d');
			$baseId = I('param.baseId/d');
			$payDate = I('param.payDate/d');
			if (!empty($userId) && !empty($baseId) && !empty($payDate)) {
				$condition = array();
				$condition['user_id'] = $userId;
				$condition['base_id'] = $baseId;
				$condition['pay_date'] = $payDate;
				$personInsuranceInfo = D('PersonInsuranceInfo');
				$personInsuranceInfoResult = $personInsuranceInfo->getInsurancePayDateDetailByCondition($condition);
				if (false !== $personInsuranceInfoResult) {
					$this->ajaxReturn(array('status'=>0,'result'=>$personInsuranceInfoResult));
				}else {
					$this->ajaxReturn(array('status'=>-1,'msg'=>$personInsuranceInfo->getError()));
				}
			}else {
				$this->ajaxReturn(array('status'=>-1,'msg'=>'非法参数！'));
			}
		}else {
			$this->error('非法操作！');
		}
	}
    
    /**
     * companyOrder function
     * 企业申报
     * @return void
     * @author rohochan
     **/
    public function companyOrder(){
    	$type = I('param.type');
		$location = I('param.location');
		$personName = I('param.personName');
		$companyName = I('param.companyName');
		$cardNum = I('param.cardNum');
		$productId = I('param.productId');
		$companyId = I('param.companyId');
		$adminId = I('param.adminId');
		$state = I('param.state');
		$handleMonth = I('param.handleMonth');
		//$startTime = I('param.startTime');
		//$endTime = I('param.endTime');
		
		
		$condition = array();
		//$condition['user_id'] = $this->mCuid;
		$condition['service_company_id'] = $this->_cid;
        $condition['account_info'] = $this->_AccountInfo;
		$condition['user_type'] = 1;//企业用户
		$location && $condition['location'] = $location;
		$personName && $condition['person_name'] = $personName;
		$companyName && $condition['company_name'] = $companyName;
		$cardNum && $condition['card_num'] = $cardNum;
		$productId && $condition['product_id'] = $productId;
		$companyId && $condition['company_id'] = $companyId;
		$adminId && $condition['admin_id'] = $adminId;
		$state !== '' && $condition['state'] = $state;
		$handleMonth && $condition['handle_month'] = string_to_number($handleMonth);
		
		//dump($condition);
		
		$personInsuranceInfo = D('PersonInsuranceInfo');
		if (in_array($type,[0,1])) {
			$personInsuranceInfoResult = $personInsuranceInfo->getInsuranceOrderListByCondition($condition,$type,10);
		}else {
			$personInsuranceInfoResult = $personInsuranceInfo->getInsuranceDetailListByCondition($condition,$type,10);
		}
		if (false !== $personInsuranceInfoResult) {
			//$userServiceProvider = D('UserServiceProvider');
			//$userServiceProviderResult = $userServiceProvider->getUserCompany($this->_cid);
			//$this->assign('userServiceProviderResult',$userServiceProviderResult);
			$this->assign('result',$personInsuranceInfoResult['data']);
			$this->assign('page',$personInsuranceInfoResult['page']);
			$this->assign('count',$personInsuranceInfoResult['count']);
     	   $this->display('company_order');
		}else {
			$this->error($personInsuranceInfo->getError());
		}
    }
    
    /**
     * _exportInsurance function
     * 导出申报
     * @return void
     * @author rohochan
     **/
	public function _exportInsuranceOld($objExcel,$data){
        vendor('PHPExcel.PHPExcel');
        $objExcel->getActiveSheet('0')->setTitle('社保公积金');
        //开启自动换行
        $objExcel -> getDefaultStyle()->getAlignment()->setWrapText(true);
        //设置全局水平垂直居中
        $objExcel -> getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER) -> setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $arr=array('A1'=>'姓名','B1'=>'身份证号码','C1'=>'服务套餐','D1'=>'参保地','E1'=>'服务类型','F1'=>'缴纳年月','G2'=>'单位','H2'=>'个人','I2'=>'单位','J2'=>'个人','K1'=>'残障金','L1'=>'服务费','M1'=>'合计','G1'=>'社保','I1'=>'公积金');
        $objExcel->getactivesheet()
                ->mergeCells('A1:A2')//合并单元格
                ->setCellValue('A1',$arr['A1'])
                ->mergeCells('B1:B2')
                ->setCellValue('B1',$arr['B1'])
                ->mergeCells('C1:C2')
                ->setCellValue('C1',$arr['C1'])
                ->mergeCells('D1:D2')
                ->setCellValue('D1',$arr['D1'])
                ->mergeCells('E1:E2')
                ->setCellValue('E1',$arr['E1'])
                ->mergeCells('F1:F2')
                ->setCellValue('F1',$arr['F1'])
                ->mergeCells('G1:H1')
                ->setCellValue('G1',$arr['G1'])
                ->setCellValue('G2',$arr['G2'])
                ->setCellValue('H2',$arr['H2'])
                ->mergeCells('I1:J1')
                ->setCellValue('I1',$arr['I1'])
                ->setCellValue('I2',$arr['I2'])
                ->setCellValue('J2',$arr['J2'])
                ->mergeCells('K1:K2')
                ->setCellValue('K1',$arr['K1'])
                ->mergeCells('L1:L2')
                ->setCellValue('L1',$arr['L1'])
                ->mergeCells('M1:M2')
                ->setCellValue('M1',$arr['M1']);
        if (empty($data)) {
            return $objExcel;
        }
         //设置宽度
        $objExcel->getActiveSheet()->getColumnDimension('B')->setWidth(23);
        $objExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objExcel->getActiveSheet()->getColumnDimension('D')->setWidth(9);
        $objExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $objExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        //设置单元格属性为文本,防止将数字转科学计数法
        #$objExcel->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        
        foreach ($data as $k => $v) {
                $type=array('1'=>'报增','2'=>'在保','3'=>'报减');
                $num=$k+3;
		        $objExcel->getactivesheet()
	                    ->setCellValue('A'.$num,$v['person_name'])   
	                    ->setCellValueExplicit('B'.$num,(string)$v['card_num'],\PHPExcel_Cell_DataType::TYPE_STRING)//防止科学计数法setCellValue('B'.$num,$v['card_num'])
	                    ->setCellValue('C'.$num,$v['product_name'])
	                    ->setCellValue('D'.$num,$v['locationValue'])//参保地
	                    ->setCellValue('E'.$num,$type[$v['type']])
	                    ->setCellValue('F'.$num,$v['pay_date'])
	                    ->setCellValue('G'.$num,$v['soc_company'])
	                    ->setCellValue('H'.$num,$v['soc_person'])
	                    ->setCellValue('I'.$num,$v['pro_company'])
	                    ->setCellValue('J'.$num,$v['pro_person'])
	                    ->setCellValue('K'.$num,empty($v['disabled'])?'/':$v['disabled'])//残障金
	                    ->setCellValue('L'.$num,$v['service_price'])//
	                    ->setCellValue('M'.$num,$v['price']);//合计
		}
        return $objExcel;
	}
	
    /**
     * _exportInsurance function
     * 导出申报
     * @return void
     * @author rohochan
     **/
	public function _exportInsurance($objExcel,$data){
        vendor('PHPExcel.PHPExcel');
        $objExcel->getActiveSheet('0')->setTitle('社保公积金');
        //开启自动换行
        $objExcel -> getDefaultStyle()->getAlignment()->setWrapText(true);
        //设置全局水平垂直居中
        $objExcel -> getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER) -> setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $arr=array('A1'=>'姓名','B1'=>'身份证号码','C1'=>'服务套餐','D1'=>'联系电话','E1'=>'参保地','F1'=>'服务类型','F2'=>'社保','G2'=>'公积金','H1'=>'缴纳年月','H2'=>'社保','I2'=>'公积金','J1'=>'基数','J2'=>'社保','K2'=>'公积金','L1'=>'公积金比例','L2'=>'单位','M2'=>'个人');
        $objExcel->getactivesheet()
                ->mergeCells('A1:A2')//合并单元格
                ->setCellValue('A1',$arr['A1'])
                ->mergeCells('B1:B2')
                ->setCellValue('B1',$arr['B1'])
                ->mergeCells('C1:C2')
                ->setCellValue('C1',$arr['C1'])
                ->mergeCells('D1:D2')
                ->setCellValue('D1',$arr['D1'])
                ->mergeCells('E1:E2')
                ->setCellValue('E1',$arr['E1'])
                ->mergeCells('F1:G1')
                ->setCellValue('F1',$arr['F1'])
                ->setCellValue('F2',$arr['F2'])
                ->setCellValue('G2',$arr['G2'])
                ->mergeCells('H1:I1')
                ->setCellValue('H1',$arr['H1'])
                ->setCellValue('H2',$arr['H2'])
                ->setCellValue('I2',$arr['I2'])
                ->mergeCells('J1:K1')
                ->setCellValue('J1',$arr['J1'])
                ->setCellValue('J2',$arr['J2'])
                ->setCellValue('K2',$arr['K2'])
                ->mergeCells('L1:M1')
                ->setCellValue('L1',$arr['L1'])
                ->setCellValue('L2',$arr['L2'])
                ->setCellValue('M2',$arr['M2']);
        //表头加粗
        $objExcel->getActiveSheet()->getStyle('A1:M2')->applyFromArray(array('font'=>array('bold' => true)));
        if (empty($data)) {
            return $objExcel;
        }
         //设置宽度
        $objExcel->getActiveSheet()->getColumnDimension('B')->setWidth(23);
        $objExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        //$objExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        //设置单元格属性为文本,防止将数字转科学计数法
        #$objExcel->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        
        foreach ($data as $k => $v) {
                $type=array('1'=>'报增','2'=>'在保','3'=>'报减');
                $num=$k+3;
                $propiiPaymentInfo = json_decode($v['propii_payment_info'],true);
		        $objExcel->getactivesheet()
	                    ->setCellValue('A'.$num,$v['person_name'])   
	                    ->setCellValueExplicit('B'.$num,(string)$v['card_num'],\PHPExcel_Cell_DataType::TYPE_STRING)//防止科学计数法setCellValue('B'.$num,$v['card_num'])
	                    ->setCellValue('C'.$num,$v['product_name'])
	                    ->setCellValue('D'.$num,$v['mobile'])//联系电话
	                    ->setCellValue('E'.$num,$v['locationValue'])//参保地
	                    ->setCellValue('F'.$num,get_code_value($v['socpii_state'],'PersonInsuranceState'))
	                    ->setCellValue('G'.$num,get_code_value($v['propii_state'],'PersonInsuranceState'))
	                    ->setCellValue('H'.$num,$v['socpii_pay_date'])
	                    ->setCellValue('I'.$num,$v['propii_pay_date'])
	                    ->setCellValue('J'.$num,$v['socpii_amount'])
	                    ->setCellValue('K'.$num,$v['propii_amount'])
	                    ->setCellValue('L'.$num,trim($propiiPaymentInfo['companyScale'],'%'))
	                    ->setCellValue('M'.$num,trim($propiiPaymentInfo['personScale'],'%'));
		}
        return $objExcel;
	}
	
    /**
     * exportInsurance function
     * 导出申报
     * @return void
     * @author rohochan
     **/
    public function exportInsurance(){
    	$type = I('param.type');
		$location = I('param.location');
		$personName = I('param.personName');
		$companyName = I('param.companyName');
		$cardNum = I('param.cardNum');
		$productId = I('param.productId');
		$companyId = I('param.companyId');
		$adminId = I('param.adminId');
		$state = I('param.state');
		$handleMonth = I('param.handleMonth');
		//$startTime = I('param.startTime');
		//$endTime = I('param.endTime');
		//dump('Content-Disposition: attachment; filename*="utf8\'\'' . 'asdada' . '.xls"');die;
		$condition = array();
		//$condition['user_id'] = $this->mCuid;
		$condition['service_company_id'] = $this->_cid;
		$condition['user_type'] = 1;//企业用户
		$location && $condition['location'] = $location;
		$personName && $condition['person_name'] = $personName;
		$companyName && $condition['company_name'] = $data['companyName'];
		$cardNum && $condition['card_num'] = $cardNum;
		$productId && $condition['product_id'] = $productId;
		$companyId && $condition['company_id'] = $companyId;
		$adminId && $condition['admin_id'] = $adminId;
		$state !== '' && $condition['state'] = $state;
		$handleMonth && $condition['handle_month'] = string_to_number($handleMonth);
		
		//dump($condition);
		
		$personInsuranceInfo = D('PersonInsuranceInfo');
		$personInsuranceInfoResult = $personInsuranceInfo->getInsuranceOrderListByCondition($condition,$type,100000);
		if (false !== $personInsuranceInfoResult) {
			//$this->assign('result',$personInsuranceInfoResult['data']);
			//$this->assign('page',$personInsuranceInfoResult['page']);
			//$this->assign('count',$personInsuranceInfoResult['count']);
			$result=$personInsuranceInfoResult['data'];
			//dump($personInsuranceInfoResult);
			//导出到excel
			$this->objExcel=setExcelHead(array('creator'=>'智保易','lastModifiedBy'=>'智保易','title'=>'导出申报_'.date('YmdHis')));
			$this->objExcel=$this->_exportInsurance($this->objExcel,$result);
			vendor('PHPExcel.PHPExcel.Reader.Excel5');
	        $objWriter = new \PHPExcel_Writer_Excel5($this->objExcel);
	        $ua = $_SERVER["HTTP_USER_AGENT"];
	        $fileName='导出申报_'.date('YmdHis').'.xls';
        	set_filename_header($ua,$fileName);
	        $objWriter->save('php://output');
		}else {
			$this->error($personInsuranceInfo->getError());
		}
    }
    
    /**
     * personOrder function
     * 个人申报
     * @return void
     * @author rohochan
     **/
    public function personOrder(){
    	$type = I('param.type');
		$location = I('param.location');
		$personName = I('param.personName');
		$companyName = I('param.companyName');
		$cardNum = I('param.cardNum');
		$productId = I('param.productId');
		$companyId = I('param.companyId');
		$adminId = I('param.adminId');
		$state = I('param.state');
		$handleMonth = I('param.handleMonth');
		//$startTime = I('param.startTime');
		//$endTime = I('param.endTime');
		
		
		$condition = array();
		//$condition['user_id'] = $this->mCuid;
		$condition['service_company_id'] = $this->_cid;
		$condition['user_type'] = 3;//个人用户
		$location && $condition['location'] = $location;
		$personName && $condition['person_name'] = $personName;
		$companyName && $condition['company_name'] = $data['companyName'];
		$cardNum && $condition['card_num'] = $cardNum;
		$productId && $condition['product_id'] = $productId;
		$companyId && $condition['company_id'] = $companyId;
		$adminId && $condition['admin_id'] = $adminId;
		$state !== '' && $condition['state'] = $state;
		$handleMonth && $condition['handle_month'] = string_to_number($handleMonth);
		
		$personInsuranceInfo = D('PersonInsuranceInfo');
		if (in_array($type,[0,1])) {
			$personInsuranceInfoResult = $personInsuranceInfo->getInsuranceOrderListByCondition($condition,$type,10);
		}else {
			$personInsuranceInfoResult = $personInsuranceInfo->getInsuranceDetailListByCondition($condition,$type,10);
		}
		if (false !== $personInsuranceInfoResult) {
			//$userServiceProvider = D('UserServiceProvider');
			//$userServiceProviderResult = $userServiceProvider->getUserCompany($this->_cid);
			//$this->assign('userServiceProviderResult',$userServiceProviderResult);
			$this->assign('result',$personInsuranceInfoResult['data']);
			$this->assign('page',$personInsuranceInfoResult['page']);
			$this->assign('count',$personInsuranceInfoResult['count']);
     	   $this->display('person_order');
		}else {
			$this->error($personInsuranceInfo->getError());
		}
    }
    
	/**
	 * getPersonBaseByIdCard function
	 * 通过身份证号获取个人信息
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getPersonBaseByCardNum(){
		if (IS_POST) {
			$userId = I('post.userId/d');
			$cardNum = I('post.cardNum','');
			empty($cardNum) && $this->ajaxReturn(array('status'=>-1,'msg'=>'非法参数!'));
			$personBase = D('PersonBase');
			$personBaseResult = $personBase->field(true)->getByCardNum($cardNum);
			if($personBaseResult) {
				$personInsurance = D('PersonInsurance');
				$personInsuranceResult = $personInsurance->getInsuranceStatus($userId,$personBaseResult['id']);
				$personInsuranceResult['idCardImg'] = get_idCardImg_by_baseId($personInsuranceResult['base_id']);
				if ($personInsuranceResult['increase']) {
					$this->ajaxReturn(array('status'=>0,'result'=>$personBaseResult));
				}else {
					$this->ajaxReturn(array('status'=>-1,'msg'=>'参保状态错误！'));
				}
			}else{
				$this->ajaxReturn(array('status'=>0,'result'=>null));
			}
		}else {
			//$this->ajaxReturn(array('status'=>-1,'msg'=>'非法操作!'));
    		$this->error('非法操作!');
		}
	}
	
	/**
	 * getLocation function
	 * 根据产品ID获取参保地
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getLocation(){
		if (IS_POST) {
			//根据产品ID获取参保地
			$userId = I('post.userId/d');
			$productId = I('post.productId/d');
			if ($userId && $productId) {
				$serviceProductOrder = D('ServiceProductOrder');
				$serviceProductOrderResult = $serviceProductOrder->getEffectiveServiceProductOrderLocationByProductId($userId,$productId);
				$this->ajaxReturn(array('status'=>0,'result'=>$serviceProductOrderResult));
			}else {
				$this->ajaxReturn(array('status'=>-1,'msg'=>'非法参数！'));
			}
		}else {
			//$this->ajaxReturn(array('status'=>-1,'msg'=>'非法操作!'));
    		$this->error('非法操作!');
		}
	}
	
	/**
	 * _getTemplateClassify function
	 * 根据参保地获取模板分类
	 * @access private
	 * @param int $location 城市编号
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	private function _getTemplateClassify($location){
		if ($location) {
			$template = D('Template');
			$templateResult = $template->getTemplateByCondition(array('location'=>$location,'state'=>1));
			if ($templateResult) {
				$templateClassify = D('TemplateClassify');
				$templateClassifyResult = array();
				for ($i=1; $i <= 2; $i++) { 
					$templateClassifyResult[$i] = $templateClassify->getTemplateClassifyByCondition(array('template_id'=>$templateResult['id'],'type'=>$i,'state'=>1));
					if ($templateClassifyResult[$i]) {
						$templateClassifyResult[$i] = list_to_tree($templateClassifyResult[$i],'id','fid','_child',0);
					}
				}
				return array('template_id'=>$templateResult['id'],'result'=>$templateClassifyResult);
			}else {
				return false;
			}
		}else {
			return false;
		}
	}
	
	/**
	 * getTemplateClassify function
	 * 根据参保地获取模板分类
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getTemplateClassify(){
		if (IS_POST) {
			$location = I('post.location/d');
			if ($location) {
				$result = $this->_getTemplateClassify($location);
				if ($result) {
					$this->ajaxReturn(array('status'=>0,'result'=>$result));
				}else {
					$this->ajaxReturn(array('status'=>-1,'msg'=>'该参保地不存在模板！'));
				}
			}else {
				$this->ajaxReturn(array('status'=>-1,'msg'=>'非法参数！'));
			}
		}else {
			//$this->ajaxReturn(array('status'=>-1,'msg'=>'非法操作!'));
    		$this->error('非法操作!');
		}
	}
	
	/**
	 * _getTemplateRule function
	 * 根据参保地获取模板分类
	 * @access private
	 * @param int $ruleId 规则id
	 * @param int $type 类型 1社保 2公积金
	 * @param array $templateId 模板id
	 * @param string $classifyMixed 分类组合
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	private function _getTemplateRule($ruleId = 0, $type = 1,$templateId = 0,$companyId = 0,$classifyMixed = ''){
		if ($templateId) {
			$templateRule = D('TemplateRule');
			if ($ruleId) {
				$condition = array('id'=>$ruleId,'state'=>1);
			}else {
				if (1 == $type) {
					$classifyMixed = array_filter($classifyMixed);
					rsort($classifyMixed);
					if ($classifyMixed) {
						$classifyMixed = implode('|',$classifyMixed);
						$condition = array('template_id'=>$templateId,'company_id'=>array(0,intval($companyId),array('exp','is null'),'or'),'type'=>$type,'classify_mixed'=>$classifyMixed,'state'=>1);
					}else {
						return false;
					}
				}else if (2 == $type) {
					$condition = array('template_id'=>$templateId,'company_id'=>array(0,intval($companyId),array('exp','is null'),'or'),'type'=>$type,'state'=>1);
				}else {
					return false;
				}
			}
			$templateRuleResult = $templateRule->getTemplateRuleByCondition($condition,2);
			if ($templateRuleResult) {
				foreach ($templateRuleResult as $key => $value) {
					$rule = json_decode($value['rule'],true);
					$templateRuleResult[$key]['rule'] = $rule;
					$templateRuleResult[$key]['minAmount'] = $rule['min'];
					$templateRuleResult[$key]['maxAmount'] = $rule['max'];
					$templateRuleResult[$key]['proCost'] = $rule['pro_cost'];
					!empty($rule['company']) && $templateRuleResult[$key]['companyScale'] = $rule['company'];
					!empty($rule['person']) && $templateRuleResult[$key]['personScale'] = $rule['person'];
				}
				return $templateRuleResult;
			}else {
				return false;
			}
		}else {
			return false;
		}
	}
	
	/**
	 * getTemplateRule function
	 * 根据参保地获取模板规则
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getTemplateRule(){
		if (IS_POST) {
			//$userId = I('post.userId/d');
			$ruleId = I('post.id/d');
			$type = I('post.type/d');
			$templateId = I('post.templateId/d');
			$companyId = I('post.companyId/d',$this->_cid);
			$classifyMixed = I('post.classifyMixed');
			if (!is_array($classifyMixed)) {
				$classifyMixed = array($classifyMixed);
			}
			if ($ruleId || ($type && $templateId)) {
				$result = $this->_getTemplateRule($ruleId,$type,$templateId,$companyId,$classifyMixed);
				if ($result) {
					$this->ajaxReturn(array('status'=>0,'result'=>$result));
				}else {
					$this->ajaxReturn(array('status'=>-1,'msg'=>'该参保地不存在模板规则！'));
				}
			}else {
				$this->ajaxReturn(array('status'=>-1,'msg'=>'非法参数！'));
			}
		}else {
			//$this->ajaxReturn(array('status'=>-1,'msg'=>'非法操作!'));
    		$this->error('非法操作!');
		}
	}
	
	/**
	 * _calculateCost function
	 * 计算社保公积金费用
	 * @param array $data 数据
	 * @access private
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	private function _calculateCost($data){
		$template = D('Template');
		$templateResult = $template->getTemplateByCondition(array('id'=>$data['templateId'],'state'=>1));
		if ($templateResult) {
			$templateResult['deadline'] = array(1=>$templateResult['soc_deadline'],2=>$templateResult['pro_deadline']);
			$templateResult['payment_type'] = array(1=>$templateResult['soc_payment_type'],2=>$templateResult['pro_payment_type']);
			$templateResult['payment_month'] = array(1=>$templateResult['soc_payment_month'],2=>$templateResult['pro_payment_month']);
			
			//$orderDate[1] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($templateResult['deadline'][1],2,'0',STR_PAD_LEFT)))))?date('Ym',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Ym',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
			//$orderDate[2] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($templateResult['deadline'][2],2,'0',STR_PAD_LEFT)))))?date('Ym',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Ym',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
			$orderDate[1] = get_handle_month($templateResult['deadline'][1]);
			$orderDate[2] = get_handle_month($templateResult['deadline'][2]);
			$orderDateStr[1] = substr_replace($orderDate[1],'-',4,0);
			$orderDateStr[2] = substr_replace($orderDate[2],'-',4,0);
			
			$maxPaymentMonth[1] = 1 == $templateResult['payment_type'][1]?$orderDate[1]:date('Ym',strtotime('+1 month', strtotime($orderDateStr[1])));
			$maxPaymentMonth[2] = 1 == $templateResult['payment_type'][2]?$orderDate[2]:date('Ym',strtotime('+1 month', strtotime($orderDateStr[2])));
			$minPaymentMonth[1] = date('Ym',strtotime('-'.$templateResult['payment_month'][1].' month', strtotime($maxPaymentMonth[1])));
			$minPaymentMonth[2] = date('Ym',strtotime('-'.$templateResult['payment_month'][2].' month', strtotime($maxPaymentMonth[2])));
			//$minPaymentMonth[1] = date('Ym',strtotime('-'.$templateResult['payment_month'][1].' month', strtotime($orderDateStr[1])));
			//$minPaymentMonth[2] = date('Ym',strtotime('-'.$templateResult['payment_month'][2].' month', strtotime($orderDateStr[2])));
			
			if (!empty($data['socPayMonth']) && string_to_number($data['socPayMonth'])<$minPaymentMonth[1]) {
				return array('status'=>-1,'msg'=>'社保起缴月份错误！');
			}
			
			if (!empty($data['proPayMonth']) && string_to_number($data['proPayMonth'])<$minPaymentMonth[2]) {
				return array('status'=>-1,'msg'=>'公积金起缴月份错误！');
			}
			
			$calculateData = array();
			$templateRule = D('TemplateRule');
			if ($data['socRuleId'] && $data['socAmount']) {
				$socRuleResult = $templateRule->getTemplateRuleByCondition(array('id'=>$data['socRuleId'],'state'=>1));
				$disRuleResult = $templateRule->getTemplateRuleByCondition(array('template_id'=>$data['templateId'],'company_id'=>$socRuleResult['company_id'],'type'=>3,'state'=>1));
				if ($socRuleResult) {
					$calculateData[1]['rule_id'] = $socRuleResult['id'];
					$calculateData[1]['disRule'] = $disRuleResult['rule'];
					$calculateData[1]['payMonth'] = string_to_number($data['socPayMonth']);
					$calculateData[1]['rule'] = $socRuleResult['rule'];
					$calculateData[1]['json'] = json_encode(array('amount'=>$data['socAmount'],'month'=>$data['socMonthNum'],'cardno'=>$data['socCcardno']));
				}
			}
			
			if ($data['proRuleId'] && $data['proAmount'] && $data['proPersonScale'] && $data['proCompanyScale']) {
				//$proRuleResult = $templateRule->getTemplateRuleByCondition(array('template_id'=>$data['templateId'],'type'=>2,'state'=>1));
				$proRuleResult = $templateRule->getTemplateRuleByCondition(array('id'=>$data['proRuleId'],'state'=>1));
				$disRuleResult = $templateRule->getTemplateRuleByCondition(array('template_id'=>$data['templateId'],'company_id'=>$proRuleResult['company_id'],'type'=>3,'state'=>1));
				if($proRuleResult){
					$calculateData[2]['rule_id'] = $proRuleResult['id'];
					$calculateData[2]['disRule'] = $disRuleResult['rule'];
					$calculateData[2]['payMonth'] = string_to_number($data['proPayMonth']);
					$calculateData[2]['rule'] = $proRuleResult['rule'];
					$calculateData[2]['json'] = json_encode(array('amount'=>$data['proAmount'],'month'=>$data['proMonthNum'],'personScale'=>$data['proPersonScale'],'companyScale'=>$data['proCompanyScale'],'cardno'=>$data['proCcardno']));
				}
			}
			
			if ($calculateData) {
				$calculate = new \Common\Model\Calculate();
				$warrantyLocation = D('WarrantyLocation');
				$warrantyLocationResult = $warrantyLocation->getWarrantyLocationByCondition(array('id'=>$data['warrantyLocationId'],'state'=>0));
				
				$result = array();
				//先按月份顺序插入数组
				$socMonthNum = get_different_by_month($calculateData[1]['payMonth'],$maxPaymentMonth[1]);
				$proMonthNum = get_different_by_month($calculateData[2]['payMonth'],$maxPaymentMonth[2]);
				$monthNum = $socMonthNum>$proMonthNum?$socMonthNum:$proMonthNum;
				$payMonth = strtotime(int_to_date(($maxPaymentMonth[1]>$maxPaymentMonth[2]?$maxPaymentMonth[1]:$maxPaymentMonth[2]),'-'));
				for ($i=$monthNum-1; $i >= 0; $i--) { 
					$month = date('Y-m',strtotime('-'.$i.' Month',$payMonth));
					$result['data'][$month] = array();
				}
				$servicePrice = array();
				$servicePrice[1] = $warrantyLocationResult['soc_service_price'];
				$servicePrice[2] = $warrantyLocationResult['pro_service_price'];
				//$result['servicePrice'] = $warrantyLocationResult['soc_service_price']+$warrantyLocationResult['pro_service_price'];
				$result['servicePrice'] = 0;
				$result['companyCost'] = 0;
				$result['personCost'] = 0;
				$result['totalCost'] = 0;
				foreach ($calculateData as $key => $value) {
					$monthNum = get_different_by_month($value['payMonth'],$maxPaymentMonth[$key]);
					$payMonth = strtotime(int_to_date($value['payMonth'],'-'));
					//$payMonth = strtotime(int_to_date($maxPaymentMonth[$key],'-'));
					$result['rule_id'][$key] = $value['rule_id'];
					for ($i=0; $i < $monthNum; $i++) { 
						$month = date('Y-m',strtotime('+'.$i.' Month',$payMonth));
						//$result['data'][$month][$key]['replenish'] = string_to_number($month) < $orderDate[$key]?1:0;
						$replenish = string_to_number($month) < $maxPaymentMonth[$key]?1:0;
						$calculateResult = json_decode($calculate->detail($value['rule'],$value['json'],$key,$value['disRule'],$replenish),true);
						//$calculateResult = json_decode($calculate->detail($value['rule'],$value['json'],$key),true);
						$result['data'][$month][$key] = $calculateResult;
						$result['data'][$month][$key]['servicePrice'] = $servicePrice[$key];
						$result['data'][$month][$key]['replenish'] = $replenish;
						if (0 == $calculateResult['state']) {
							$companyCost = $calculateResult['data']['company'];
							$personCost = $calculateResult['data']['person'];
							$proCost = $calculateResult['data']['pro_cost'];
						}else {
							$companyCost = 0;
							$personCost = 0;
							$proCost = 0;
						}
						$result['companyCost'] += $companyCost;
						$result['personCost'] += $personCost;
						$result['servicePrice'] += $servicePrice[$key];
					}
				}
				$result['totalCost'] = $result['servicePrice'] + $result['companyCost'] + $result['personCost'];
				$result['monthNum'] = count($result['data']);
				return array('status'=>0,'result'=>$result);
			}else {
				return array('status'=>-1,'msg'=>'非法参数！');
			}
		}else {
			return array('status'=>-1,'msg'=>'非法参数！');
		}
	}
	
	/**
	 * _calculateCostByPiiId function
	 * 根据insurance_info_id获取参保数据
	 * @param array $data 数据
	 * @access private
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function _calculateCostByPiiId($data){
		//dump($data);
		$userId = $data['userId'];
		$baseId = $data['baseId'];
		$piiId = array('in',array($data['socPiiId'],$data['proPiiId']));
		if (!empty($userId) && !empty($baseId) && !empty($piiId)) {
			$condition = array();
			$condition['user_id'] = $userId;
			$condition['base_id'] = $baseId;
			$condition['pii_id'] = $piiId;
			$personInsuranceInfo = D('PersonInsuranceInfo');
			$personInsuranceInfoResult = $personInsuranceInfo->getInsurancePayDateDetailByPiiId($condition);
			if (false !== $personInsuranceInfoResult) {
				$this->ajaxReturn(array('status'=>0,'result'=>$personInsuranceInfoResult));
			}else {
				$this->ajaxReturn(array('status'=>-1,'msg'=>$personInsuranceInfo->getError()));
			}
		}else {
			$this->ajaxReturn(array('status'=>-1,'msg'=>'非法参数！'));
		}
	}
	
	/**
	 * calculateCost function
	 * 计算社保公积金费用
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function calculateCost(){
		if (IS_POST) {
			$data = I('param.');
			$data['proPayMonth'] = empty($data['proPayMonth'])?$data['socPayMonth']:$data['proPayMonth'];
			
			//$data['baseId'] = 277;
			//$data['socPiiId'] = 1030;
			//$data['proPiiId'] = 1031;
			//dump($data);
			if ($data['templateId']) {
				if ($data['socPiiId'] || $data['proPiiId']) {
					$this->ajaxReturn($this->_calculateCostByPiiId($data));
				}else {
					$this->ajaxReturn($this->_calculateCost($data));
				}
			}else {
				$this->ajaxReturn(array('status'=>-1,'msg'=>'非法参数！'));
			}
		}else {
			//$this->ajaxReturn(array('status'=>-1,'msg'=>'非法操作!'));
    		$this->error('非法操作!');
		}
	}
    
    /**
     * editInsurance function
     * 编辑参保
     * @return void
     * @author rohochan
     **/
    public function editInsurance(){
    	if (IS_POST) {
			//构造测试数据start
			$data = array();
			$data['userId'] = '55';
			$data['baseId'] = '279';
			$data['personName'] = '袁量儒';
			$data['cardNum'] = '440301198706155110';
			$data['mobile'] = '13445613211';
			$data['residenceLocation'] = '18090000';
			$data['residenceType'] = '1';
			$data['handleMonth'];
			
			$data['productId'] = '25';
			$data['location'] = '14020100';
			
			$data['isBuySoc'] = '1';
			$data['socPiiId'] = '1034';
			$data['socRuleId'] = '117';
			//$data['socPayDate'] = '2016-09';
			$data['socAmount'] = '9999';
			//$data['socCardNum'] = 'zby';
			$data['isBuyPro'] = '1';
			$data['proPiiId'] = '1035';
			$data['proRuleId'] = '118';
			//$data['proPayDate'] = '2016-09';
			$data['proAmount'] = '9999';
			//$data['proCardNum'] = 'zby';
			$data['proPersonScale'] = '11%';
			$data['proCompanyScale'] = '11%';
			//构造测试数据end
			
			$data = I('param.');
			//dump($data);
			//$data['socCardNum'] = 'zby';
			//$data['proCardNum'] = 'zby';
			//if ($data['payDate']) {
			//	$data['socPayDate'] = $data['payDate'];
			//	$data['proPayDate'] = $data['payDate'];
			//}
			
			//$data['proPayDate'] = empty($data['proPayDate'])?$data['socPayDate']:$data['proPayDate'];
			
			$data['templateLocation'] = $data['location'];
			$data['location'] = ($data['location']/1000<<0)*1000;
			
			$personBaseData = array();
			$personBaseData['id'] = $data['baseId'];
			$personBaseData['user_id'] = $data['userId'];
			$personBaseData['person_name'] = $data['personName'];
			$personBaseData['card_num'] = $data['cardNum'];
			$personBaseData['mobile'] = $data['mobile'];
			$personBaseData['residence_location'] = $data['residenceLocation'];
			$personBaseData['residence_type'] = $data['residenceType'];
			$personBaseData['audit'] = 1;
			$personBase = D('PersonBase');
			$personBase->startTrans();
			$personBaseResult = $personBase->savePersonBase($personBaseData);
			if ($personBaseResult) {
				$personBaseId = $personBaseResult;
				$personInsurance = D('PersonInsurance');
				$personInsuranceResult = $personInsurance->getInsuranceStatus($data['userId'],$personBaseId);
				if ($personInsuranceResult) {
					if ($personInsuranceResult['editIncrease'] || $personInsuranceResult['editInsurance']) {
						//保存参保信息
						$personInsuranceInfo = D('PersonInsuranceInfo');
						$personInsuranceInfoData = array();
						$personInsuranceInfoData['user_id'] = $data['userId'];
						$personInsuranceInfoData['base_id'] = $personBaseId;
						$personInsuranceInfoData['product_id'] = $data['productId'];
						$personInsuranceInfoData['location'] = $data['location'];
						$personInsuranceInfoData['template_location'] = $data['templateLocation'];
						$personInsuranceInfoData['modify_time'] = date('Y-m-d H:i:s');
						
						$personInsuranceInfoArray = array();
						$personInsuranceInfoOriginArray = array();
						if (1 == $data['isBuySoc']) {
							//$personInsuranceInfoResult = $personInsuranceInfo->field('id,state,operate_state')->getById($data['socPiiId']);
							$personInsuranceInfoOriginArray[1] = $personInsuranceInfoResult = $personInsuranceInfo->field(true)->getById($data['socPiiId']);
							//if (in_array($personInsuranceInfoResult['operate_state'],array(0,-1,-9))) {
							if ($personInsuranceInfoResult) {
								//dump($personInsuranceInfoResult);
								$personInsuranceInfoArray[1] = $personInsuranceInfoData;
								$personInsuranceInfoArray[1]['id'] = $data['socPiiId'];
								$personInsuranceInfoArray[1]['rule_id'] = $data['socRuleId'];
								//$personInsuranceInfoArray[1]['start_month'] = string_to_number($data['socPayDate']);
								//$personInsuranceInfoArray[1]['handle_month'] = $data['handleMonth'];
								$personInsuranceInfoArray[1]['amount'] = $data['socAmount'];
								$personInsuranceInfoArray[1]['payment_type'] = 1;
								$personInsuranceInfoArray[1]['payment_info'] = json_encode(array('cardno'=>$data['socCardNum']));
								$personInsuranceInfoArray[1]['state'] = (0 == $personInsuranceInfoResult['state']?1:$personInsuranceInfoResult['state']);
								//$personInsuranceInfoArray[1]['operate_state'] = 0;//未审核
								$personInsuranceInfoArray[1]['operate_state'] = 1;//审核通过
							}
						}
						if (1 == $data['isBuyPro']) {
							//$personInsuranceInfoResult = $personInsuranceInfo->field('id,state,operate_state')->getById($data['proPiiId']);
							$personInsuranceInfoOriginArray[2] = $personInsuranceInfoResult = $personInsuranceInfo->field(true)->getById($data['proPiiId']);
							//if (in_array($personInsuranceInfoResult['operate_state'],array(0,-1,-9))) {
							if ($personInsuranceInfoResult) {
								//dump($personInsuranceInfoResult);
								$personInsuranceInfoArray[2] = $personInsuranceInfoData;
								$personInsuranceInfoArray[2]['id'] = $data['proPiiId'];
								$personInsuranceInfoArray[2]['rule_id'] = $data['proRuleId'];
								//$personInsuranceInfoArray[2]['start_month'] = string_to_number($data['proPayDate']);
								//$personInsuranceInfoArray[2]['handle_month'] = $data['handleMonth'];
								$personInsuranceInfoArray[2]['amount'] = $data['proAmount'];
								$personInsuranceInfoArray[2]['payment_type'] = 2;
								$personInsuranceInfoArray[2]['payment_info'] = json_encode(array('companyScale'=>trim($data['proCompanyScale'],'%').'%','personScale'=>trim($data['proPersonScale'],'%').'%','cardno'=>$data['proCardNum']));
								$personInsuranceInfoArray[2]['state'] = (0 == $personInsuranceInfoResult['state']?1:$personInsuranceInfoResult['state']);
								//$personInsuranceInfoArray[2]['operate_state'] = 0;//未审核
								$personInsuranceInfoArray[1]['operate_state'] = 1;//审核通过
							}
						}else {
							$personInsuranceInfoOriginArray[2] = $personInsuranceInfoResult = $personInsuranceInfo->field(true)->getById($data['proPiiId']);
							//$personInsuranceInfoArray[2] = $personInsuranceInfoData;
							$personInsuranceInfoArray[2]['id'] = $data['proPiiId'];
							$personInsuranceInfoArray[2]['state'] = 3;//报减
							//$personInsuranceInfoArray[2]['operate_state'] = 0;//未审核
							$personInsuranceInfoArray[1]['operate_state'] = 1;//审核通过
						}
						if ($personInsuranceInfoArray) {
							/*foreach ($personInsuranceInfoArray as $key => $value) {
								$personInsuranceInfoSaveResult = $personInsuranceInfo->where(array('id'=>$value['id']))->save($value);
							}*/
							//dump($personInsuranceInfoArray);
							$personInsuranceInfoResult = array();
							$templateRule = D('TemplateRule');
							$personInsuranceInfoLog = D('PersonInsuranceInfoLog');
							foreach ($personInsuranceInfoArray as $key => $value) {
								if (1 == $value['state']) {
									//报增状态
									//$personInsuranceInfoResult[$key] = $personInsuranceInfo->savePersonInsurance($value);
									$personInsuranceInfoResult['rule'][$key] = $templateRule->getById($value['rule_id']);
									if ($personInsuranceInfoResult['rule'][$key]) {
										$personInsuranceInfoResult['id'][$key] = $value['id'];
										$personInsuranceInfoSaveResult = $personInsuranceInfo->where(array('id'=>$value['id']))->save($value);
										$personInsuranceInfoResult['successCount'] += false !== $personInsuranceInfoSaveResult?1:0;
									}else {
										$personBase->rollback();
										$this->ajaxReturn(array('status'=>-1,'msg'=>'规则参数错误！'));
									}
								}else {
									$personInsuranceInfoResult['id'][$key] = $value['id'];
									$personInsuranceInfoSaveResult = $personInsuranceInfo->save($value);
									$personInsuranceInfoResult['successCount'] += false !== $personInsuranceInfoSaveResult?1:0;
								}
								$personInsuranceInfoLog->add(['insurance_id'=>$personInsuranceInfoOriginArray[$key]['insurance_id'],'user_id'=>$personInsuranceInfoOriginArray[$key]['user_id'],'data'=>json_encode(['origin'=>$personInsuranceInfoOriginArray[$key],'current'=>$value]),'create_time'=>date('Y-m-d H:i:s')]);
							}
							if ($personInsuranceInfoResult['successCount'] == count($personInsuranceInfoArray)) {
								$personBase->commit();
								$this->ajaxReturn(array('status'=>0,'msg'=>'操作成功！'));
							}else {
								$personBase->rollback();
								$this->ajaxReturn(array('status'=>-1,'msg'=>'操作失败！'));
							}
						}else {
							$personBase->rollback();
							$this->ajaxReturn(array('status'=>-1,'msg'=>'系统内部错误！'));
						}
					}else {
						$personBase->rollback();
						$this->ajaxReturn(array('status'=>0,'msg'=>'参保状态错误！'));
					}
				}else {
					$personBase->rollback();
					$this->ajaxReturn(array('status'=>0,'msg'=>'系统内部错误！'));
				}
			}else {
				$personBase->rollback();
				$this->ajaxReturn(array('status'=>0,'msg'=>$personBase->getError()));
				//$this->error($personBase->getError());
			}
    	}else {
			$userId = I('get.userId/d');
			$baseId = I('get.baseId/d');
			$handleMonth = I('get.handleMonth/d');
			if ($baseId >0) {
				//获取个人信息
				$personBase = D('PersonBase');
				$personBaseResult = $personBase->field(true)->getById($baseId);
				//$personBaseResult['readonly'] = 1 == $personBaseResult['audit']?' readonly="readonly" ':'';
				//$personBaseResult['disabled'] = 1 == $personBaseResult['audit']?' disabled="disabled" ':'';
				$personBaseResult['readonly'] = '';
				$personBaseResult['disabled'] = '';
				//获取身份证图片
				$personBaseResult['idCardImg'] = get_idCardImg_by_baseId($baseId);
				//获取参保信息
				$personInsuranceInfo = D('personInsuranceInfo');
				//$personInsuranceInfoResult = $personInsuranceInfo->getServiceOrderDetailByCondition(array('user_id'=>$userId,'base_id'=>$baseId),array('in','0,1'),false);
				$personInsuranceInfoResult = $personInsuranceInfo->getPersonInsuranceInfoByHandleMonth(array('user_id'=>$userId,'base_id'=>$baseId,'handle_month'=>$handleMonth));
				
				if ($personInsuranceInfoResult) {
					$productId = $personInsuranceInfoResult[1]['product_id']?:$personInsuranceInfoResult[2]['product_id'];
					$location = $personInsuranceInfoResult[1]['location']?:$personInsuranceInfoResult[2]['location'];
					$templateLocation = $personInsuranceInfoResult[1]['template_location']?:$personInsuranceInfoResult[2]['template_location'];
					
					$serviceProductOrder = D('ServiceProductOrder');
					$serviceProduct = D('ServiceProduct');
					$templateRule = D('TemplateRule');
					$template = D('Template');
					
					//获取购买的产品信息
					$serviceProductResult = $serviceProduct->alias('sp')->field('sp.company_id,sp.name,ci.company_name')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id = sp.company_id')->where(['sp.id'=>$productId])->find();
					
					//获取购买的产品订单信息
					$serviceProductOrderResult['list'] = $serviceProductOrder->getEffectiveServiceProductOrder($userId);
					$serviceProductOrderResult['condition'] = array('product_id'=>$productId,'product_name'=>$serviceProductResult['name'],'company_name'=>$serviceProductResult['company_name']);
					
					$serviceProductOrderLocationResult['list'] = $serviceProductOrder->getEffectiveServiceProductOrderLocationByProductId($userId,$productId);
					$serviceProductOrderLocationResult['condition'] = array('location'=>$templateLocation);
					
					foreach ($personInsuranceInfoResult as $key => $value) {
						//$personInsuranceInfoResult[$key]['serviceProductOrderResult'] = $serviceProductOrder->getEffectiveServiceProductOrderLocationByProductId($userId,$value['product_id']);
						$personInsuranceInfoResult[$key]['paymentInfoValue'] = json_decode($value['payment_info'],true);
						$personInsuranceInfoResult[$key]['templateRuleResult'] = $templateRule->getTemplateRuleByCondition(array('id'=>$value['rule_id']));
						//$personInsuranceInfoResult[$key]['templateResult'] = $template->getTemplateByCondition(array('id'=>$personInsuranceInfoResult[$key]['templateRuleResult']['template_id']));
						
						$templateClassifyResult[$key]['list'] = $this->_getTemplateClassify($templateLocation);
						$templateClassifyResult[$key]['condition'] = array('classify_mixed'=>array_filter(explode('|',$personInsuranceInfoResult[$key]['templateRuleResult']['classify_mixed'])));
						
						$templateRuleResult[$key]['list'] = $this->_getTemplateRule($value['rule_id'],$key,$personInsuranceInfoResult[$key]['templateRuleResult']['template_id']?:$personInsuranceInfoResult[3-$key]['templateRuleResult']['template_id'],$serviceProductResult['company_id'],explode('|',$personInsuranceInfoResult[$key]['templateRuleResult']['classify_mixed']));
						
						$templateRuleResult[$key]['condition'] = array('rule_id'=>$value['rule_id'],'amount'=>$value['amount'],'start_month'=>int_to_date($value['start_month'],'-'),'companyScale'=>$personInsuranceInfoResult[$key]['paymentInfoValue']['companyScale'],'personScale'=>$personInsuranceInfoResult[$key]['paymentInfoValue']['personScale']);
						
					}
					//dump($serviceProductOrderResult);
					//dump($serviceProductOrderLocationResult);
					//dump($templateClassifyResult);
					//dump($templateRuleResult);
					//dump($personInsuranceInfoResult);
				}else {
					//$this->ajaxReturn(array('status'=>-1,'msg'=>'参保状态错误！'));
					$this->error('参保状态错误！');
				}
				$this->assign('personBaseResult',$personBaseResult);
				$this->assign('personInsuranceInfoResult',$personInsuranceInfoResult);
				$this->assign('serviceProductOrderResult',$serviceProductOrderResult);
				$this->assign('serviceProductOrderLocationResult',$serviceProductOrderLocationResult);
				$this->assign('templateClassifyResult',$templateClassifyResult);
				$this->assign('templateRuleResult',$templateRuleResult);
				$this->display('edit_insurance');
			}else {
				$this->error('非法参数！');
			}
    	}
    }
	
	/**
	 * insuranceInfoDetail function
	 * 参保信息详情
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function insuranceInfoDetail(){
		if (IS_POST) {
			$data = I('param.');
			//$data['socCardNum'] = 'zby';
			//$data['proCardNum'] = 'zby';
			//if ($data['payDate']) {
			//	$data['socPayDate'] = $data['payDate'];
			//	$data['proPayDate'] = $data['payDate'];
			//}
			$data['proPayDate'] = empty($data['proPayDate'])?$data['socPayDate']:$data['proPayDate'];
			
			$data['templateLocation'] = $data['location'];
			$data['location'] = ($data['location']/1000<<0)*1000;
			
			//获取参保信息
			$personInsuranceInfo = D('personInsuranceInfo');
			$personInsuranceInfoOriginResult = $personInsuranceInfo->getPersonInsuranceInfoByHandleMonth(array('user_id'=>$data['userId'],'base_id'=>$data['baseId'],'handle_month'=>$data['handleMonth']));
			if (!$personInsuranceInfoOriginResult) {
				$this->ajaxReturn(array('status'=>-1,'msg'=>'参数错误！'));
			}
			$personBaseData = array();
			$personBaseData['id'] = $data['baseId'];
			$personBaseData['user_id'] = $data['userId'];
			$personBaseData['person_name'] = $data['personName'];
			$personBaseData['card_num'] = $data['cardNum'];
			$personBaseData['mobile'] = $data['mobile'];
			$personBaseData['residence_location'] = $data['residenceLocation'];
			$personBaseData['residence_type'] = $data['residenceType'];
			$personBase = D('PersonBase');
			$personBase->startTrans();
			$personBaseResult = $personBase->savePersonBase($personBaseData);
			if ($personBaseResult) {
				$personBaseId = $personBaseResult;
				$personInsurance = D('PersonInsurance');
				$personInsuranceResult = $personInsurance->getInsuranceStatus($data['userId'],$personBaseId);
				if ($personInsuranceResult) {
					if ($personInsuranceResult['editIncrease'] || $personInsuranceResult['editInsurance']) {
						//计算订单月份
						$template = D('Template');
						//$templateResult = $template->getTemplateByCondition(array('location'=>$data['location'],'state'=>1));
						$templateResult = $template->getTemplateByCondition(array('location'=>$data['templateLocation'],'state'=>1));
						if ($templateResult) {
							$templateResult['deadline'] = array(1=>$templateResult['soc_deadline'],2=>$templateResult['pro_deadline']);
							$templateResult['payment_type'] = array(1=>$templateResult['soc_payment_type'],2=>$templateResult['pro_payment_type']);
							$templateResult['payment_month'] = array(1=>$templateResult['soc_payment_month'],2=>$templateResult['pro_payment_month']);
							
							//$orderDate[1] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($templateResult['deadline'][1],2,'0',STR_PAD_LEFT)))))?date('Y-m',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Ym',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
							//$orderDate[2] = date('Ymd')>=intval(date('Ymd',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime(date('Y-m-',time()+(C('INSURANCE_HANDLE_DAYS')*86400)).str_pad($templateResult['deadline'][2],2,'0',STR_PAD_LEFT)))))?date('Y-m',strtotime('+1 month '.date('Y-m',strtotime(' + '.C('INSURANCE_HANDLE_DAYS').' day')))):date('Ym',time()+(C('INSURANCE_HANDLE_DAYS')*86400));
							$orderDate[1] = get_handle_month($templateResult['deadline'][1]);
							$orderDate[2] = get_handle_month($templateResult['deadline'][2]);
							$orderDateStr[1] = substr_replace($orderDate[1],'-',4,0);
							$orderDateStr[2] = substr_replace($orderDate[2],'-',4,0);
							
							$personInsuranceInfo = D('PersonInsuranceInfo');
							$personInsuranceInfoData = array();
							$personInsuranceInfoData['user_id'] = $data['userId'];
							$personInsuranceInfoData['base_id'] = $personBaseId;
							$personInsuranceInfoData['product_id'] = $data['productId'];
							$personInsuranceInfoData['location'] = $data['location'];
							$personInsuranceInfoData['template_location'] = $data['templateLocation'];
							//$personInsuranceInfoData['end_month'] = 0;
							//$personInsuranceInfoData['audit'] = 0;//未审核
							//$personInsuranceInfoData['state'] = 1;//报增
							//$personInsuranceInfoData['create_time'] = date('Y-m-d H:i:s');
							$personInsuranceInfoData['modify_time'] = date('Y-m-d H:i:s');
							$personInsuranceInfoArray = array();
							if (1 == $data['isBuySoc'] && !in_array($personInsuranceInfoOriginResult[1]['operate_state'],array(2,3,-3))) {
								$personInsuranceInfoResult = $personInsuranceInfo->field('id,state,operate_state')->getById($data['socPiiId']);
								if (in_array($personInsuranceInfoResult['operate_state'],array(0,1,-1,-9))) {
									$personInsuranceInfoArray[1] = $personInsuranceInfoData;
									$personInsuranceInfoArray[1]['id'] = $data['socPiiId'];
									//$personInsuranceInfoArray[1]['pay_order_id'] = 0;
									$personInsuranceInfoArray[1]['rule_id'] = $data['socRuleId'];
									$personInsuranceInfoArray[1]['start_month'] = string_to_number($data['socPayDate']);
									//$personInsuranceInfoArray[1]['handle_month'] = $orderDate[1];
									$personInsuranceInfoArray[1]['handle_month'] = $data['handleMonth'];
									$personInsuranceInfoArray[1]['amount'] = $data['socAmount'];
									$personInsuranceInfoArray[1]['payment_type'] = 1;
									$personInsuranceInfoArray[1]['payment_info'] = json_encode(array('cardno'=>$data['socCardNum']));
									$personInsuranceInfoArray[1]['state'] = (0 == $personInsuranceInfoResult['state']?1:$personInsuranceInfoResult['state']);
									//if ($personInsuranceInfoResult['operate_state'] < 0) {
										$personInsuranceInfoArray[1]['operate_state'] = 0;//未审核
									//}
								}
							}
							if (1 == $data['isBuyPro'] && !in_array($personInsuranceInfoOriginResult[2]['operate_state'],array(2,3,-3))) {
								$personInsuranceInfoResult = $personInsuranceInfo->field('id,state,operate_state')->getById($data['proPiiId']);
								if (in_array($personInsuranceInfoResult['operate_state'],array(0,1,-1,-9))) {
									$personInsuranceInfoArray[2] = $personInsuranceInfoData;
									$personInsuranceInfoArray[2]['id'] = $data['proPiiId'];
									//$personInsuranceInfoArray[2]['pay_order_id'] = 0;
									$personInsuranceInfoArray[2]['rule_id'] = $data['proRuleId'];
									$personInsuranceInfoArray[2]['start_month'] = string_to_number($data['proPayDate']);
									//$personInsuranceInfoArray[2]['handle_month'] = $orderDate[2];
									$personInsuranceInfoArray[2]['handle_month'] = $data['handleMonth'];
									$personInsuranceInfoArray[2]['amount'] = $data['proAmount'];
									$personInsuranceInfoArray[2]['payment_type'] = 2;
									$personInsuranceInfoArray[2]['payment_info'] = json_encode(array('companyScale'=>trim($data['proCompanyScale'],'%').'%','personScale'=>trim($data['proPersonScale'],'%').'%','cardno'=>$data['proCardNum']));
									$personInsuranceInfoArray[2]['state'] = (0 == $personInsuranceInfoResult['state']?1:$personInsuranceInfoResult['state']);
									//if ($personInsuranceInfoResult['operate_state'] < 0) {
										$personInsuranceInfoArray[2]['operate_state'] = 0;//未审核
									//}
								}
							}else if(0 == $data['isBuyPro'] && 0 != $personInsuranceInfoOriginResult[2]['state']) {
								$personInsuranceInfoArray[2]['id'] = $data['proPiiId'];
								//$personInsuranceInfoArray[2]['pay_order_id'] = 0;
								$personInsuranceInfoArray[2]['pay_date'] = '';
								$personInsuranceInfoArray[2]['operate_state'] = -9;//撤销
								$personInsuranceInfoResult = $personInsuranceInfo->getLastPersonInsuranceInfo(array('id'=>$data['proPiiId'],'user_id'=>$data['userId'],'base_id'=>$personBaseId,'payment_type'=>2));
								if ($personInsuranceInfoResult) {
									$personInsuranceInfoArray[2]['state'] = (1 == $personInsuranceInfoResult['state']?0:$personInsuranceInfoResult['state']);
								}else {
									$personInsuranceInfoArray[2]['state'] = 0;//未参保
								}
							}
							//dump($personInsuranceInfoArray);
							if ($personInsuranceInfoArray) {
								$personInsuranceInfoResult = array();
								$templateRule = D('TemplateRule');
								foreach ($personInsuranceInfoArray as $key => $value) {
									if (1 == $value['state'] || 2 == $value['state']) {
										//报增状态或在保状态
										$personInsuranceInfoResult['rule'][$key] = $templateRule->getById($value['rule_id']);
										if ($personInsuranceInfoResult['rule'][$key]) {
											$personInsuranceInfoResult['id'][$key] = $value['id'];
											$tempPersonInsuranceInfoResult = $personInsuranceInfo->save($value);
											$personInsuranceInfoResult['successCount'] += false !== $tempPersonInsuranceInfoResult?1:0;
										}else {
											$personBase->rollback();
											$this->ajaxReturn(array('status'=>-1,'msg'=>'规则参数错误！'));
										}
									}else {
										$personInsuranceInfoResult['id'][$key] = $value['id'];
										//$personInsuranceInfoResult['state'][$key] = $value['state'];
										$tempPersonInsuranceInfoResult = $personInsuranceInfo->save($value);
										$personInsuranceInfoResult['successCount'] += false !== $tempPersonInsuranceInfoResult?1:0;
									}
								}
								if ($personInsuranceInfoResult['successCount'] == count($personInsuranceInfoArray)) {
									//计算订单月份
									$serviceProductOrder = D('ServiceProductOrder');
									$serviceProductOrderResult = $serviceProductOrder->getEffectiveServiceProductOrderByProductId($data['userId'],$data['productId']);
									if ($serviceProductOrderResult) {
											//计算缴纳月份，补缴月份
											$serviceInsuranceDetailBaseData = array();
											//$serviceInsuranceDetailBaseData['pay_order_id'] = 0;//无支付订单
											//$serviceInsuranceDetailBaseData['type'] = 1;//报增
											$serviceInsuranceDetailBaseData['state'] = 0;//待审核
											$serviceInsuranceDetailBaseData['create_time'] = date('Y-m-d H:i:s');
											$serviceInsuranceDetailBaseData['modify_time'] = $serviceInsuranceDetailBaseData['create_time'];
											$warrantyLocation = D('WarrantyLocation');
											//计算服务费
											$warrantyLocationResult = $warrantyLocation->getWarrantyLocationByCondition(array('service_product_order_id'=>$serviceProductOrderResult['id'],'location'=>$data['location']));
											$servicePrice = array();
											$servicePrice[1] = $warrantyLocationResult['soc_service_price'];
											$servicePrice[2] = $warrantyLocationResult['pro_service_price'];
											$calculate = new \Common\Model\Calculate();
											foreach ($personInsuranceInfoResult['id'] as $key => $value) {
												//勾选报增
												if (1 == $personInsuranceInfoArray[$key]['state'] || 2 == $personInsuranceInfoArray[$key]['state']) {
													//报增状态或在保状态
													$endMonth = 1 == $templateResult['payment_type'][$key]?$orderDate[$key]:date('Ym',strtotime('+1 month', strtotime($orderDateStr[$key])));//1缴当月 2缴次月
													$monthNum = get_different_by_month($personInsuranceInfoArray[$key]['start_month'],$endMonth);
													$replenishMonthNum = get_different_by_month($personInsuranceInfoArray[$key]['start_month'],$orderDate[$key])-1;
													if ($monthNum > 0) {
														//计算缴纳费用
														$productTemplateRuleResult = $templateRule->getById($personInsuranceInfoArray[$key]['rule_id']);
														$disRuleResult = $templateRule->getTemplateRuleByCondition(array('template_id'=>$templateResult['id'],'company_id'=>$productTemplateRuleResult['company_id'],'type'=>3,'state'=>1));
														//$servicePrice = 1 == $key?$warrantyLocationResult['ss_service_price']:0;
														$json = json_decode($personInsuranceInfoArray[$key]['payment_info'],true);
														$json['amount'] = $personInsuranceInfoArray[$key]['amount'];
														$json['month'] = 1;
														$json = json_encode($json);
														//最大补缴月份
														if ($replenishMonthNum <= $templateResult['payment_month'][$key]) {
															//缴纳年月数组
															$paymentMonthArray = array();
															for ($i=0; $i < $monthNum; $i++) {
																$paymentMonthArray[] = date('Ym',strtotime("+{$i} month",strtotime(substr_replace($personInsuranceInfoArray[$key]['start_month'],'-',4,0))));
															}
															//添加参保订单表数据
															$serviceInsuranceDetail = D('ServiceInsuranceDetail');
															
															//$serviceInsuranceDetailDeleteResult = $serviceInsuranceDetail->where(array('insurance_info_id'=>$value,'state'=>0))->delete();
															$serviceInsuranceDetailDeleteResult = $serviceInsuranceDetail->where(array('insurance_info_id'=>$value))->delete();
															//更新数据
															$personInsuranceInfoUpdateResult = $personInsuranceInfo->where(array('id'=>$value))->save(array('pay_date'=>implode(',',$paymentMonthArray)));
															
															$serviceInsuranceDetailResult = array();
															$serviceInsuranceDetailResult['monthNum'] += $monthNum;
															for ($i=0; $i < $monthNum; $i++) {
																$payDate = date('Ym',strtotime("+{$i} month",strtotime(substr_replace($personInsuranceInfoArray[$key]['start_month'],'-',4,0))));
																$replenish = $payDate < $endMonth?1:0;//是否补缴
																$calculateResult = json_decode($calculate->detail($productTemplateRuleResult['rule'], $json, $key, $disRuleResult['rule'] ,$replenish ),true);
																if (0 == $calculateResult['state']) {
																	//$price = $calculateResult['data']['company']+$calculateResult['data']['person']+$calculateResult['data']['pro_cost'];
																	$price = $calculateResult['data']['company']+$calculateResult['data']['person'];
																	//$calculateResult['data']['service_price'] = $servicePrice[$key];
																	$serviceInsuranceDetailData = $serviceInsuranceDetailBaseData;
																	$serviceInsuranceDetailData['type'] = $personInsuranceInfoArray[$key]['state'];
																	$serviceInsuranceDetailData['payment_type'] = $key;//参保类型
																	$serviceInsuranceDetailData['insurance_info_id'] = $value;
																	$serviceInsuranceDetailData['price'] = $price;
																	$serviceInsuranceDetailData['service_price'] = $servicePrice[$key];
																	$serviceInsuranceDetailData['amount'] = $personInsuranceInfoArray[$key]['amount'];
																	$serviceInsuranceDetailData['pay_date'] = $payDate;
																	$serviceInsuranceDetailData['replenish'] = $replenish;
																	$serviceInsuranceDetailData['rule_id'] = $personInsuranceInfoArray[$key]['rule_id'];
																	//$serviceInsuranceDetailData['rule_detail'] = $productTemplateRuleResult['rule'];
																	$serviceInsuranceDetailData['payment_info'] = $personInsuranceInfoArray[$key]['payment_info'];
																	$serviceInsuranceDetailData['insurance_detail'] = json_encode($calculateResult['data'],JSON_UNESCAPED_UNICODE);//计算结果
																	$serviceInsuranceDetailData['current_detail'] = $serviceInsuranceDetailData['insurance_detail'];
																	$serviceInsuranceDetailResult['id'][$key][$serviceInsuranceDetailData['pay_date']] = $serviceInsuranceDetail->add($serviceInsuranceDetailData);
																	$serviceInsuranceDetailResult['successCount'] += $serviceInsuranceDetailResult['id'][$key][$serviceInsuranceDetailData['pay_date']]?1:0;
																}else {
																	$personBase->rollback();
																	$this->ajaxReturn(array('status'=>-1,'msg'=>'参保数据计算错误！'));
																}
															}
														}else {
															$personBase->rollback();
															$this->ajaxReturn(array('status'=>-1,'msg'=>'超出最大补缴月份！'));
														}
													}else {
														$personBase->rollback();
														$this->ajaxReturn(array('status'=>-1,'msg'=>'起缴月份错误！'));
													}
												}else {
													//未勾选购买
													$serviceInsuranceDetail = D('ServiceInsuranceDetail');
													$condition = array();
													$condition['insurance_info_id'] = $value;
													$condition['state'] = ['in',[0,1,-1]];
													$serviceInsuranceDetailData = array();
													$serviceInsuranceDetailData['pay_order_id'] = 0;//无支付订单
													$serviceInsuranceDetailData['state'] = -9;
													$serviceInsuranceDetailData['modify_time'] = date('Y-m-d H:i:s');
													$serviceInsuranceDetailSaveResult = $serviceInsuranceDetail->where($condition)->save($serviceInsuranceDetailData);
													if (false === $serviceInsuranceDetailSaveResult) {
														$personBase->rollback();
														$this->ajaxReturn(array('status'=>-1,'msg'=>'系统内部错误！'));
													}
												}
											}
											if ($serviceInsuranceDetailResult['successCount'] == $serviceInsuranceDetailResult['monthNum']) {
												$personBase->commit();
												//保存身份证
												$path = mkFilePath($personBaseId,'./Uploads/Person/','IDCard');
												//保存身份证正面照片
												if ($idCardFrontFile = I('idCardFrontFile','')) {
													$idCardFrontFile = reset(explode('?',$idCardFrontFile));
													if ('/Application/Company/Assets/v2/images/idcard1.png' != $idCardFrontFile) {
														$idCardFrontFileResult = move('.'.$idCardFrontFile,$path.'idCardFront.jpg');
													}
												}
												//保存身份证反面照片
												if ($idCardBackFile = I('idCardBackFile','')) {
													$idCardBackFile = reset(explode('?',$idCardBackFile));
													if ('/Application/Company/Assets/v2/images/idcard2.png' != $idCardBackFile) {
														$idCardBackFileResult = move('.'.$idCardBackFile,$path.'idCardBack.jpg');
													}
												}
												$this->ajaxReturn(array('status'=>0,'msg'=>'操作成功！'));
											}else {
												$personBase->rollback();
												$this->ajaxReturn(array('status'=>-1,'msg'=>'操作失败！'));
											}
									}else {
										$personBase->rollback();
										$this->ajaxReturn(array('status'=>-1,'msg'=>'产品订单错误！'));
									}
								}else {
									$personBase->rollback();
									$this->ajaxReturn(array('status'=>-1,'msg'=>'系统内部错误！'));
								}
							}else {
								$personBase->rollback();
								//$this->ajaxReturn(array('status'=>-1,'msg'=>'请选择要参保的项目！'));
								$this->ajaxReturn(array('status'=>0,'msg'=>'操作成功！'));
							}
						}else {
							$personBase->rollback();
							$this->ajaxReturn(array('status'=>-1,'msg'=>'系统缴费模板错误！'));
						}
					}else {
						$personBase->rollback();
						$this->ajaxReturn(array('status'=>-1,'msg'=>'参保状态错误！'));
					}
				}else {
					$personBase->rollback();
					$this->ajaxReturn(array('status'=>-1,'msg'=>'系统内部错误！'));
				}
			}else {
				$personBase->rollback();
				$this->ajaxReturn(array('status'=>-1,'msg'=>$personBase->getError()));
			}
		}else {
			$userId = I('get.userId/d');
			$baseId = I('get.baseId/d');
			$handleMonth = I('get.handleMonth/d');
			if ($baseId >0 && $handleMonth>0) {
				//获取个人信息
				$personBase = D('PersonBase');
				$personBaseResult = $personBase->field(true)->getById($baseId);
				//$personBaseResult['readonly'] = 1 == $personBaseResult['audit']?' readonly="readonly" ':'';
				//$personBaseResult['disabled'] = 1 == $personBaseResult['audit']?' disabled="disabled" ':'';
				$personBaseResult['readonly'] = '';
				$personBaseResult['disabled'] = '';
				//获取身份证图片
				$personBaseResult['idCardImg'] = get_idCardImg_by_baseId($baseId);
				//获取参保信息
				$personInsuranceInfo = D('personInsuranceInfo');
				//$personInsuranceInfoResult = $personInsuranceInfo->getServiceOrderDetailByCondition(array('user_id'=>$userId,'base_id'=>$baseId),array('in','0,1'),false);
				$personInsuranceInfoResult = $personInsuranceInfo->getPersonInsuranceInfoByHandleMonth(array('user_id'=>$userId,'base_id'=>$baseId,'handle_month'=>$handleMonth));
				//dump($personInsuranceInfoResult);
				if ($personInsuranceInfoResult) {
					$personBaseResult['editable'] = ($personInsuranceInfoResult[1]['operate_state'] <= 0 && $personInsuranceInfoResult[2]['operate_state'] <= 0);
					$personBaseResult['isPaid'] = ($personInsuranceInfoResult[1]['operate_state'] >= 2 || $personInsuranceInfoResult[2]['operate_state'] >= 2);
					$personBaseResult['whetherToOperate'] = $personInsuranceInfoResult[1]['whetherToOperate'] && $personInsuranceInfoResult[2]['whetherToOperate'];
					$payDate = str_unique($personInsuranceInfoResult[1]['pay_date'],$personInsuranceInfoResult[2]['pay_date']);
					$productId = $personInsuranceInfoResult[1]['product_id']?:$personInsuranceInfoResult[2]['product_id'];
					$location = $personInsuranceInfoResult[1]['location']?:$personInsuranceInfoResult[2]['location'];
					$templateLocation = $personInsuranceInfoResult[1]['template_location']?:$personInsuranceInfoResult[2]['template_location'];
					
					$serviceProductOrder = D('ServiceProductOrder');
					$serviceProduct = D('ServiceProduct');
					$templateRule = D('TemplateRule');
					$template = D('Template');
					
					//获取购买的产品信息
					$serviceProductResult = $serviceProduct->alias('sp')->field('sp.company_id,sp.name,ci.company_name')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id = sp.company_id')->where(['sp.id'=>$productId])->find();
					
					//获取购买的产品订单信息
					$serviceProductOrderResult['list'] = $serviceProductOrder->getEffectiveServiceProductOrder($userId);
					$serviceProductOrderResult['condition'] = array('product_id'=>$productId,'product_name'=>$serviceProductResult['name'],'company_name'=>$serviceProductResult['company_name']);
					
					$serviceProductOrderLocationResult['list'] = $serviceProductOrder->getEffectiveServiceProductOrderLocationByProductId($userId,$productId);
					$serviceProductOrderLocationResult['condition'] = array('location'=>$templateLocation,'locationValue'=>get_location_value($templateLocation));
					
					foreach ($personInsuranceInfoResult as $key => $value) {
						//$personInsuranceInfoResult[$key]['serviceProductOrderResult'] = $serviceProductOrder->getEffectiveServiceProductOrderLocationByProductId($userId,$value['product_id']);
						$personInsuranceInfoResult[$key]['paymentInfoValue'] = json_decode($value['payment_info'],true);
						$personInsuranceInfoResult[$key]['templateRuleResult'] = $templateRule->getTemplateRuleByCondition(array('id'=>$value['rule_id']));
						//$personInsuranceInfoResult[$key]['templateResult'] = $template->getTemplateByCondition(array('id'=>$personInsuranceInfoResult[$key]['templateRuleResult']['template_id']));
						
						$templateClassifyResult[$key]['list'] = $this->_getTemplateClassify($templateLocation);
						$templateClassifyResult[$key]['condition'] = array('classify_mixed'=>array_filter(explode('|',$personInsuranceInfoResult[$key]['templateRuleResult']['classify_mixed'])));
						
						//$templateRuleResult[$key]['list'] = $this->_getTemplateRule($value['rule_id'],$key,$personInsuranceInfoResult[$key]['templateRuleResult']['template_id']?:$personInsuranceInfoResult[3-$key]['templateRuleResult']['template_id'],$serviceProductResult['company_id'],explode('|',$personInsuranceInfoResult[$key]['templateRuleResult']['classify_mixed']));
						$templateRuleResult[$key]['list'] = $this->_getTemplateRule(0,$key,$personInsuranceInfoResult[$key]['templateRuleResult']['template_id']?:$personInsuranceInfoResult[3-$key]['templateRuleResult']['template_id'],$serviceProductResult['company_id'],explode('|',$personInsuranceInfoResult[$key]['templateRuleResult']['classify_mixed']));
						$templateRuleResult[$key]['condition'] = array('rule_id'=>$value['rule_id'],'amount'=>$value['amount'],'start_month'=>int_to_date($value['start_month'],'-'),'companyScale'=>$personInsuranceInfoResult[$key]['paymentInfoValue']['companyScale'],'personScale'=>$personInsuranceInfoResult[$key]['paymentInfoValue']['personScale']);
					}
					//dump($serviceProductOrderResult);
					//dump($serviceProductOrderLocationResult);
					//dump($templateClassifyResult);
					//dump($templateRuleResult);
					//dump($personInsuranceInfoResult);
				}else {
					//$this->ajaxReturn(array('status'=>-1,'msg'=>'参保状态错误！'));
					$this->error('参保状态错误！');
				}
				$this->assign('payDate',$payDate);
				$this->assign('personBaseResult',$personBaseResult);
				$this->assign('personInsuranceInfoResult',$personInsuranceInfoResult);
				$this->assign('serviceProductOrderResult',$serviceProductOrderResult);
				$this->assign('serviceProductOrderLocationResult',$serviceProductOrderLocationResult);
				$this->assign('templateClassifyResult',$templateClassifyResult);
				$this->assign('templateRuleResult',$templateRuleResult);
				$this->display('insurance_info_detail');
			}else {
				$this->error('非法参数！');
			}
		}
	}
	
    /**
     * getServiceInsuranceDetail function
     * 获取参保明细
     * @return void
     * @author rohochan
     **/
    public function getServiceInsuranceDetail(){
    	if (IS_POST) {
    		$data[1] = I('socPiiId');
    		$data[2] = I('proPiiId');
    		if ($data) {
    			$result = array();
    			$serviceInsuranceDetail = D('ServiceInsuranceDetail');
    			$diffCron = D('DiffCron');
    			foreach ($data as $key => $value) {
	    			$result[$key] = $serviceInsuranceDetail->field('id,pay_date,type')->where(array('insurance_info_id'=>$value,'payment_type'=>$key,'state'=>array('in',array(2,3,-3))))->select();
	    			if ($result[$key]) {
	    				foreach ($result[$key] as $kk => $vv) {
		    				$result[$key][$kk]['handle_result'] = $diffCron->field(true)->where(['detail_id'=>$vv['id'],'type'=>1])->find();
		    				$result[$key][$kk]['handle_result'] = is_array($result[$key][$kk]['handle_result'])?false:true;
	    				}
	    			}
 	    		}
 	    		if ($result) {
    				$this->ajaxReturn(array('status'=>0,'msg'=>'操作成功！','result'=>$result));
 	    		}else {
 	    			$this->ajaxReturn(array('status'=>-1,'msg'=>'数据错误！'));
 	    		}
    		}else {
    			$this->ajaxReturn(array('status'=>-1,'msg'=>'非法参数！'));
    		}
    	}else {
    		$this->error('非法操作!');
    	}
    }
	
    /**
     * operateInsuranceOrder function
     * 处理参保订单
     * @return void
     * @author rohochan
     **/
    public function operateInsuranceOrder(){
    	if (IS_POST) {
    		$type = I('post.type/d');//1审批成功 -1审批失败 3办理成功 -3办理失败 -4缴费异常
    		//$type = -4;
    		if (in_array($type,array(1,-1))) {
    			//审批
    			//dump(I('post.'));
    			//构造测试数据start
				$data = array();
    			$data["socPiiId"] = "1034";
				$data["proPiiId"] = "1035";
				$data["socOperateState"] = "-1";
				$data["proOperateState"] = "-1";
				$data["proRemark"] = "test2";
				$data["socRemark"] = "test1";
    			//构造测试数据end
    			
				$data = array();
				//$data['id'] = explode(',',implode(',',I('post.id')));
				$data['service_company_id'] = $this->_cid;
				$data['data'][1]['id'] = I('post.socPiiId');
				$data['data'][1]['operate_state'] = I('post.socOperateState');
				$data['data'][1]['remark'] = I('post.socRemark');
				$data['data'][2]['id'] = I('post.proPiiId');
				$data['data'][2]['operate_state'] = empty($data['data'][2]['id'])?'':I('post.proOperateState',$data['data'][1]['operate_state']);
				$data['data'][2]['remark'] = empty($data['data'][2]['id'])?'':I('post.proRemark',$data['data'][1]['remark']);
				$personInsuranceInfo = D('PersonInsuranceInfo');
				$personInsuranceInfoResult = $personInsuranceInfo->approvePersonInsuranceInfo($data);
				//dump($personInsuranceInfoResult);
				if ($personInsuranceInfoResult) {
					$this->ajaxReturn(array('status'=>0,'msg'=>'操作成功!'));
				}else {
					$this->ajaxReturn(array('status'=>-1,'msg'=>$personInsuranceInfo->getError()));
				}
			}else if (in_array($type,array(3,-3))) {
				//办理
    			//构造测试数据start
    			$data = array();
    			$data['service_company_id'] = $this->_cid;
    			$data['data'][1]["id"] = "1034";
				$data['data'][1]["buyCard"] = "1";
				$data['data'][2]["id"] = "1035";
				$data['data'][2]["buyCard"] = "0";
				$data['data'][1]["data"]['201610'] = array("id"=>1741,"operate_state" => "3","is_hang_up" => "0","remark" => "kkkkkkk");
				$data['data'][1]["data"]['201609'] = array("id"=>1740,"operate_state" => "3","is_hang_up" => "0","remark" => "kkkkkkk");
				$data['data'][2]["data"]['201609'] = array("id"=>1742,"operate_state" => "-3","is_hang_up" => "0","remark" => "kkkkkkk");
				$data['data'][2]["data"]['201610'] = array("id"=>1743,"operate_state" => "-3","is_hang_up" => "0","remark" => "kkkkkkk");
    			//构造测试数据end
    			
    			$data = array();
    			$data['service_company_id'] = $this->_cid;
    			$data['data'][1]['id'] = I('socPiiId');
				$data['data'][1]['buyCard'] = I('socBuyCard',0);
    			$data['data'][2]['id'] = I('proPiiId');
				$data['data'][2]['buyCard'] = I('proBuyCard',0);
				$tempData = I('post.data');
				foreach ($tempData as $k => $v) {
					foreach ($v as $kk => $vv) {
						$data['data'][$k]['data'][$vv['pay_date']]['id'] = $kk;
						$data['data'][$k]['data'][$vv['pay_date']]['operate_state'] = $vv['operate_state'];
						$data['data'][$k]['data'][$vv['pay_date']]['is_hang_up'] = $vv['is_hang_up']?1:0;
						$data['data'][$k]['data'][$vv['pay_date']]['remark'] = $vv['remark'];
					}
				}
    			//dump(I('post.'));
    			//dump($data);
    			//die;
				$personInsuranceInfo = D('PersonInsuranceInfo');
				$personInsuranceInfoResult = $personInsuranceInfo->handlePersonInsuranceInfo($data);
				if ($personInsuranceInfoResult) {
					$this->ajaxReturn(array('status'=>0,'msg'=>'操作成功!'));
				}else {
					$this->ajaxReturn(array('status'=>-1,'msg'=>$personInsuranceInfo->getError()));
				}
    			
			}else if (in_array($type,array(-4))) {
				//缴费异常
    			//构造测试数据start
				$data = array();
    			$data['service_company_id'] = $this->_cid;
				//$data['data'][1] = array("id"=>1740,'pay_date'=>'201609',"data" =>array('养老保险'=>array('company'=>1,'person'=>0),'工商保险'=>array('company'=>1,'person'=>0),'失业保险'=>array('company'=>1,'person'=>0),'生育保险'=>array('company'=>1,'person'=>0),'重大医疗保险'=>array('company'=>1,'person'=>0),'其他险种'=>array('company'=>1,'person'=>0)));
				//$data['data'][1] = array("id"=>1741,'pay_date'=>'201610',"data" =>array('养老保险'=>array('company'=>1,'person'=>0),'工商保险'=>array('company'=>1,'person'=>0),'失业保险'=>array('company'=>1,'person'=>0),'生育保险'=>array('company'=>1,'person'=>0),'重大医疗保险'=>array('company'=>1,'person'=>0),'其他险种'=>array('company'=>1,'person'=>0)));
				//$data['data'][2] = array("id"=>1742,'pay_date'=>'201609',"data" =>array('company'=>1,'person'=>0));
				//$data['data'][2] = array("id"=>1743,'pay_date'=>'201610',"data" =>array('company'=>1,'person'=>0));
				$data['data'][1] = array("id"=>1740,'insurance_info_id'=>1034,'pay_date'=>'201609',"data" =>array(0=>array('name'=>'养老保险','company'=>1,'person'=>0),1=>array('name'=>'医疗保险','company'=>1,'person'=>0),2=>array('name'=>'失业保险','company'=>1,'person'=>0),3=>array('name'=>'工伤保险','company'=>1,'person'=>0),4=>array('name'=>'生育保险','company'=>1,'person'=>0),5=>array('name'=>'补充医疗保险','company'=>1,'person'=>0),6=>array('name'=>'残障金','company'=>1,'person'=>0)));
				//$data['data'][1] = array("id"=>1741,'insurance_info_id'=>1034,'pay_date'=>'201610',"data" =>array(0=>array('name'=>'养老保险','company'=>1,'person'=>0),1=>array('name'=>'医疗保险','company'=>1,'person'=>0),2=>array('name'=>'失业保险','company'=>1,'person'=>0),3=>array('name'=>'工伤保险','company'=>1,'person'=>0),4=>array('name'=>'生育保险','company'=>1,'person'=>0),5=>array('name'=>'补充医疗保险','company'=>1,'person'=>0),6=>array('name'=>'残障金','company'=>1,'person'=>0)));
				$data['data'][2] = array("id"=>1742,'insurance_info_id'=>1035,'pay_date'=>'201609',"data" =>array(0=>array('name'=>'公积金','company'=>0,'person'=>1)));
				//$data['data'][2] = array("id"=>1743,'insurance_info_id'=>1035,'pay_date'=>'201610',"data" =>array(0=>array('name'=>'公积金','company'=>0,'person'=>1)));
				//构造测试数据end
				
				$data = array();
    			$data['service_company_id'] = $this->_cid;
				$data['data'] = I('post.data');
				$personInsuranceInfo = D('PersonInsuranceInfo');
				$personInsuranceInfoResult = $personInsuranceInfo->paymentExceptionPersonInsuranceInfo($data);
				if ($personInsuranceInfoResult) {
					$this->ajaxReturn(array('status'=>0,'msg'=>'操作成功!'));
				}else {
					$this->ajaxReturn(array('status'=>-1,'msg'=>$personInsuranceInfo->getError()));
				}
			}else {
    			$this->ajaxReturn(array('status'=>-1,'msg'=>'非法参数!'));
    		}
    	}else {
			//$this->ajaxReturn(array('status'=>-1,'msg'=>'非法操作!'));
    		$this->error('非法操作!');
    	}
    }
    
    /**
     * updateInsuranceOrder function
     * 更新参保订单
     * @return void
     * @author rohochan
     **/
    public function updateInsuranceOrder(){
    	if (IS_POST) {
    		$type = I('post.type/d');//1审批成功 -1审批失败 3办理成功 -3办理失败
    		if (in_array($type,array(1,-1,3,-3))) {
				$personInsuranceInfo = D('PersonInsuranceInfo');
				$condition = array();
				$condition['id'] = explode(',',implode(',',I('post.id')));
				$condition['service_company_id'] = $this->_cid;
				$condition['remark'] = I('post.remark');
				if (in_array($type,array(1,-1))) {
					$personInsuranceInfoResult = $personInsuranceInfo->updatePersonInsuranceInfoByCondition($condition,$type);
				}else {
					$personInsuranceInfoResult = $personInsuranceInfo->updatePersonInsuranceInfoByConditionByDetailId($condition,$type);
				}
				if ($personInsuranceInfoResult) {
					$this->ajaxReturn(array('status'=>0,'msg'=>'操作成功!'));
				}else {
					$this->ajaxReturn(array('status'=>-1,'msg'=>$personInsuranceInfo->getError()));
				}
    		}else {
    			$this->ajaxReturn(array('status'=>-1,'msg'=>'非法参数!'));
    		}
    	}else {
			//$this->ajaxReturn(array('status'=>-1,'msg'=>'非法操作!'));
    		$this->error('非法操作!');
    	}
    }
	
    /**
     * toIncrease function
     * 客服报增
     * @return void
     * @author rohochan
     **/
    public function toIncrease(){
        $this->display('to_increase');
    }
	
    /**
     * salaryOrder function
     * 代发工资
     * @return void
     * @author rohochan
     **/
    public function salaryOrder(){
		$data = I('get.');
		$condition = array();
		$condition['service_company_id'] = $this->_cid;
        $condition['account_info'] = $this->_AccountInfo;
		'' !== $data['type'] && $condition['type'] = $data['type'];
		$data['companyId'] && $condition['company_id'] = $data['companyId'];
		$data['productId'] && $condition['product_id'] = $data['productId'];
		$data['personName'] && $condition['person_name'] = $data['personName'];
		$data['companyName'] && $condition['company_name'] = $data['companyName'];
		$data['date'] && $condition['date'] = string_to_number($data['date']);
		//$data['startTime'] && $condition['start_time'] = date('Y-m-d H:i:s',strtotime(int_to_date(string_to_number($data['startTime']),'-')));
		//$data['endTime'] && $condition['end_time'] = date('Y-m-d H:i:s',strtotime('+1 month -1 second',strtotime(int_to_date(string_to_number($data['endTime']),'-'))));
		$data['startTime'] && $condition['start_time'] = date('Y-m-d H:i:s',strtotime($data['startTime']));
		$data['endTime'] && $condition['end_time'] = date('Y-m-d H:i:s',strtotime('+1 day -1 second',strtotime(($data['endTime']))));
		$serviceOrderSalary = D('ServiceOrderSalary');
		$serviceOrderSalaryResult = $serviceOrderSalary->getServiceOrderSalaryListByCondition($condition);
		
		if (false !== $serviceOrderSalaryResult) {
			//$userServiceProvider = D('UserServiceProvider');
			//$userServiceProviderResult = $userServiceProvider->getUserCompany($this->_cid);
			//dump($userServiceProviderResult);
			//dump($serviceOrderSalaryResult);
			//$this->assign('userServiceProviderResult',$userServiceProviderResult);
			$this->assign('result',$serviceOrderSalaryResult['data']);
			$this->assign('page',$serviceOrderSalaryResult['page']);
			$this->assign('count',$serviceOrderSalaryResult['count']);
	        $this->display('salary_order');
		}else {
			$this->error($serviceOrderSalary->getError());
		}
    }
    
    /**
     * deleteSalaryOrder function
     * 删除代发工资
     * @return void
     * @author rohochan
     **/
    public function deleteSalaryOrder(){
    	if (IS_POST) {
			$serviceOrderSalary = D('ServiceOrderSalary');
			$condition = array();
			$condition['id'] = I('post.id/d');
			$condition['user_id'] = I('post.userId/d');
			$serviceOrderSalaryResult = $serviceOrderSalary->deleteSalaryOrderByCondition($condition);
			if ($serviceOrderSalaryResult ) {
				$this->ajaxReturn(array('status'=>0,'msg'=>'操作成功!'));
			}else {
				$this->ajaxReturn(array('status'=>-1,'msg'=>$serviceOrderSalary->getError()));
			}
    	}else {
			//$this->ajaxReturn(array('status'=>-1,'msg'=>'非法操作!'));
    		$this->error('非法操作!');
    	}
    }
    
    /**
     * updateSalaryOrder function
     * 更新代发工资
     * @return void
     * @author rohochan
     **/
    public function updateSalaryOrder(){
    	if (IS_POST) {
    		$type = I('post.type/d');//1审批成功 -1审批失败 3发放成功 -3发放失败
    		if (in_array($type,array(1,-1,3,-3))) {
				$serviceOrderSalary = D('ServiceOrderSalary');
				$condition = array();
				$condition['id'] = I('post.id');
				$condition['service_company_id'] = $this->_cid;
				$condition['remark'] = I('post.remark');
				$serviceOrderSalaryResult = $serviceOrderSalary->updateSalaryOrderByCondition($condition,$type);
				if ($serviceOrderSalaryResult ) {
					$this->ajaxReturn(array('status'=>0,'msg'=>'操作成功!'));
				}else {
					$this->ajaxReturn(array('status'=>-1,'msg'=>$serviceOrderSalary->getError()));
				}
    		}else {
    			$this->ajaxReturn(array('status'=>-1,'msg'=>'非法参数!'));
    		}
    	}else {
			//$this->ajaxReturn(array('status'=>-1,'msg'=>'非法操作!'));
    		$this->error('非法操作!');
    	}
    }
    
	/**
	 * downloadTemplateFile function
	 * 下载模板文件
	 * @access public
	 * @return file
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function downloadTemplateFile(){
		$category = I('get.category',1);
		$type = I('get.type','xls');
		if($category && $type) {
			if ($type == 'xls' || $type == 'xlsx') {
				if (1 == $category) {
					$fileName = $type=='xls'?'批量报增模板.xls':'批量报增模板.xlsx';
					$fileSize = $type=='xls'?50176:19421;
				}else {
					$fileName = $type=='xls'?'导入工资模板.xls':'导入工资模板.xlsx';
					$fileSize = $type=='xls'?23040:8644;
				}
				$file = array('url'=>'./Uploads/Download/'.$fileName,'name'=>$fileName,'type'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document','size'=>$fileSize);
				downLocalFile($file);
			}else{
				$this->error('未知的文件类型!');
			}
		}else {
			$this->error('非法操作!');
		}
	}
	
	/**
	 * uploadTemplateFile function
	 * 上传文件
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function uploadTemplateFile(){
		if (IS_POST) {
			$category = I('post.category',1);
			if (1 == $category) {
				$saveName = 'batchIncrease_'.GUID();
			}else if (2 == $category){
				$saveName = 'batchSalary_'.GUID();
			}else {
				$this->ajaxReturn(array('status'=>-1,'msg'=>'非法参数!'));
			}
			//企业登录/退出登录时清空temp目录下的企业对应临时文件
			$upload = new \Think\Upload(C('EXCEL_UPLOAD'));
			$path = rtrim(mkFilePath($this->_cid,$upload->rootPath,'temp'),'/');
			$path = str_replace($upload->rootPath,'',$path);
			$upload->subName = $path;
			$upload->saveName = $saveName;
			// 上传单个文件 
			$info = $upload->uploadOne($_FILES['file']);
			if(!$info) {// 上传错误提示错误信息
				$this->ajaxReturn(array('status'=>-1,'msg'=>$upload->getError()));
			}else{// 上传成功 获取上传文件信息
				$url = ltrim($upload->rootPath,'.').$info['savepath'].$info['savename'];
				$this->ajaxReturn(array('status'=>0,'msg'=>$url));
			}
		}else {
			$this->error('非法操作!');
		}
	}
	
	
	/**
	 * getSalaryServiceProductOrder function
	 * 获取生效的代发工资产品订单列表
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getSalaryServiceProductOrder(){
		if (IS_POST) {
			$companyUserId = I('companyUserId');
			if ($companyUserId) {
				$serviceProductOrder = D('ServiceProductOrder');
				$serviceProductOrderResult = $serviceProductOrder->getEffectiveServiceProductOrder($companyUserId,true);
				if ($serviceProductOrderResult) {
					$this->ajaxReturn(array('status'=>0,'msg'=>'操作成功','result'=>$serviceProductOrderResult));
				}else {
					$this->ajaxReturn(array('status'=>-1,'msg'=>'没有代发工资套餐'));
				}
			}else {
				$this->ajaxReturn(array('status'=>-1,'msg'=>'非法参数'));
			}
			//获取购买的产品订单信息
		}else {
			$this->error('非法操作!');
		}
	}
	
	/**
	 * _handleExcel function
	 * 处理excel
	 * @access private
	 * @param string $filePath 文件路径
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	private function _handleExcel($filePath){
		Vendor('PHPExcel.PHPExcel');
		$extend=pathinfo($filePath);
		$extend = strtolower($extend['extension']);//获取后缀名并转为小写
		$extend=='xlsx'?$reader_type='Excel2007':$reader_type='Excel5';//获取excel处理类型
		if (file_exists($filePath)) {
			$phpReader = \PHPExcel_IOFactory::createReader($reader_type);
			if (!$phpReader) {
				return array('status'=>0,'msg'=>'抱歉！Excel文件不兼容。');
			}
		}else {
			return array('status'=>0,'msg'=>'抱歉！Excel文件不存在。');
		}
		$phpExcel = $phpReader->load($filePath);
		$currentSheet = $phpExcel->getSheet();//默认获取第一个表
		$allColumn = $currentSheet->getHighestColumn();////取得一共有多少列
		$allRow = $currentSheet->getHighestRow();//取得一共有多少行
		$excelData = array();
		for($currentRow = 2;$currentRow<=$allRow;$currentRow++){
			for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
				//dump($currentColumn.$currentRow);
				$excelData[$currentRow][$currentColumn] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$currentSheet->getCell($currentColumn.$currentRow)->getValue());
				//$excelData[$currentRow][($currentColumn++)] = '222';
				if (empty($excelData[$currentRow][$currentColumn])) {
					unset($excelData[$currentRow][$currentColumn]);
				}
			}
			if (empty($excelData[$currentRow])) {
				unset($excelData[$currentRow]);
			}
		}
		return array('status'=>1,'result'=>$excelData);
	}
	
	/**
	 * importSalary function
	 * 处理excel
	 * @access public
	 * @param string $filePath 文件路径
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function importSalary(){
		if (IS_POST) {
			//构造测试数据start
			$data['companyUserId'] = '55';
			$data['filePath'] = '/Uploads/Company/0/55/temp/55_20160718134512.xls';
			$data['productId'] = '24';
			$data['location'] = '14020000';
			//构造测试数据end
			
			$data = I('post.');//服务产品id、参保地
			
			$data['templateLocation'] = $data['location'];
			$data['location'] = ($data['location']/1000<<0)*1000;
			$filePath = '.'.$data['filePath'];
			unset($data['fileName']);
			unset($data['filePath']);
			if (is_file($filePath)) {
				$excelResult = $this->_handleExcel($filePath);//获取个人的工资信息
				if (1 == $excelResult['status']) {
					$excelData = $excelResult['result'];
					
					$personBase = D('PersonBase');
					$serviceProductOrder = D('ServiceProductOrder');
					$warrantyLocation = D('WarrantyLocation');
					$serviceOrderSalary = D('ServiceOrderSalary');
					$serviceProductOrderResult = $serviceProductOrder->getEffectiveServiceProductOrderByProductId($data['companyUserId'],$data['productId']);
					if ($serviceProductOrderResult) {
						//计算服务费
						$warrantyLocationResult = $warrantyLocation->getWarrantyLocationByCondition(array('service_product_order_id'=>$serviceProductOrderResult['id'],'location'=>$data['location']));
						if ($warrantyLocationResult) {
							$servicePrice = $warrantyLocationResult['af_service_price'];
							$batchResult = array();
							$batchResult['totalCount'] = 0;
							$batchResult['successCount'] = 0;
							//dump($excelData);
							foreach ($excelData as $rowNum => $rowData) {
								$rowNum --;
								$batchResult['totalCount'] ++;
								$batchResult['data'][$rowNum]['personName'] = $rowData['A'];
								$batchResult['data'][$rowNum]['cardNum'] = $rowData['B'];
								$batchResult['data'][$rowNum]['bank'] = $rowData['C'];
								$batchResult['data'][$rowNum]['branch'] = $rowData['D'];
								$batchResult['data'][$rowNum]['account_name'] = $batchResult['data'][$rowNum]['personName'];
								$batchResult['data'][$rowNum]['account'] = $rowData['E'];
								$batchResult['data'][$rowNum]['date'] = $rowData['F'];
								$batchResult['data'][$rowNum]['actual_salary'] = $rowData['G'];
								$batchResult['data'][$rowNum]['tax'] = $rowData['H'];
								$batchResult['data'][$rowNum]['salary'] = $batchResult['data'][$rowNum]['actual_salary'] + $batchResult['data'][$rowNum]['tax'];
								$batchResult['data'][$rowNum]['deduction_income_tax'] = $batchResult['data'][$rowNum]['tax'];
								$batchResult['data'][$rowNum]['price'] = $batchResult['data'][$rowNum]['salary'];
								$batchResult['data'][$rowNum]['service_price'] = $servicePrice;
								
								if (validateIDCard($batchResult['data'][$rowNum]['cardNum'])) {
									$personBaseData = array();
									$personBaseData['user_id'] = $data['companyUserId'];
									$personBaseData['person_name'] = $batchResult['data'][$rowNum]['personName'];
									$personBaseData['card_num'] = $batchResult['data'][$rowNum]['cardNum'];
									$personBaseData['bank'] = $batchResult['data'][$rowNum]['bank'];
									$personBaseData['branch'] = $batchResult['data'][$rowNum]['branch'];
									$personBaseData['account_name'] = $batchResult['data'][$rowNum]['account_name'];
									$personBaseData['account'] = $batchResult['data'][$rowNum]['account'];
									$personBaseData['birthday'] = get_birthday_by_idCard($batchResult['data'][$rowNum]['cardNum']);
									$personBaseData['residence_location'] = '0';//未设置
									$personBaseData['residence_type'] = '0';//未设置
									//dump($personBaseData);
									$personBase->startTrans();
									$personBaseResult = $personBase->savePersonBase($personBaseData);
									if ($personBaseResult) {
										$personBaseId = $personBaseResult;
										
										//$salaryData = $batchResult['data'][$rowNum];
										$salaryData = array();
										$salaryData['user_id'] = $data['companyUserId'];
										$salaryData['base_id'] = $personBaseId;
										$salaryData['product_id'] = $data['productId'];
										$salaryData['location'] = $data['location'];
										$salaryData['date'] = $batchResult['data'][$rowNum]['date'];
										$salaryData['salary'] = $batchResult['data'][$rowNum]['salary'];
										$salaryData['price'] = $batchResult['data'][$rowNum]['price'];
										$salaryData['actual_salary'] = $batchResult['data'][$rowNum]['actual_salary'];
										$salaryData['tax'] = $batchResult['data'][$rowNum]['tax'];
										$salaryData['deduction_income_tax'] = $batchResult['data'][$rowNum]['deduction_income_tax'];
										$salaryData['price'] = $batchResult['data'][$rowNum]['price'];
										$salaryData['service_price'] = $batchResult['data'][$rowNum]['service_price'];
										$salaryData['state'] = 0;//待审核
										$salaryData['create_time'] = date('Y-m-d H:i:s');
										
										//查询是否已存在记录，如果存在则判断是否未审核，未审核和审核失败则更新
										$serviceOrderSalaryResult = $serviceOrderSalary->field(true)->where(array('user_id'=>$data['companyUserId'],'base_id'=>$personBaseId,'date'=>$batchResult['data'][$rowNum]['date']))->find();
										if ($serviceOrderSalaryResult) {
											if (in_array($serviceOrderSalaryResult['state'],array(0,-1))) {
												$serviceOrderSalaryResult = $serviceOrderSalary->where(array('id'=>$serviceOrderSalaryResult['id']))->save($salaryData);
												if (false !== $serviceOrderSalaryResult) {
													$personBase->commit();
													$batchResult['successCount'] ++;
												}else {
													$personBase->rollback();
													$batchResult['data'][$rowNum]['msg'] = '系统内部错误！';
												}
											}else {
												$personBase->rollback();
												$batchResult['data'][$rowNum]['msg'] = '已存在审核通过数据！';
											}
										}else {
											$serviceOrderSalaryResult = $serviceOrderSalary->add($salaryData);
											if ($serviceOrderSalaryResult) {
												$personBase->commit();
												$batchResult['successCount'] ++;
											}else {
												$personBase->rollback();
												$batchResult['data'][$rowNum]['msg'] = '系统内部错误！';
											}
										}
									}else {
										$personBase->rollback();
										$batchResult['data'][$rowNum]['msg'] = $personBase->getError();
									}
								}else {
									$batchResult['data'][$rowNum]['msg'] = '身份证错误！';
								}
							}
							$this->ajaxReturn(array('status'=>0,'result'=>$batchResult));
						}else {
							$this->ajaxReturn(array('status'=>-1,'msg'=>'参保地错误！'));
						}
					}else {
						$this->ajaxReturn(array('status'=>-1,'msg'=>'产品订单错误！'));
						//$this->error('产品订单错误');
					}
				}else {
					$this->ajaxReturn(array('status'=>-1,'msg'=>$this->error($excelResult['msg'])));
				}
			}else {
				$this->ajaxReturn(array('status'=>-1,'msg'=>'文件路径错误！'));
			}
		}else {
			//获取购买的产品订单信息
			$serviceProductOrder = D('ServiceProductOrder');
			$serviceProductOrderResult = $serviceProductOrder->getEffectiveServiceProductOrderUser($this->_cid,true);
			$this->assign('serviceProductOrderResult',$serviceProductOrderResult);
			$this->display('import_salary');
		}
	}
}