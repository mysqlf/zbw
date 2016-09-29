<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: rohochan <rohochan@gmail.com> <http://blog.rohochan.com>
// +----------------------------------------------------------------------

namespace Company\Controller;
use OT\DataDictionary;

/**
 * 企业中心账单控制器
 * 主要获取账单数据以及查看账单详情
 */
class BillController extends HomeController {
	/**
	 * [index 发票首页]
	 * @return [type] [description]
     * 
	 * @date 2016-08-05 
	 */
	public function index(){
		$ServiceBill=D('ServiceBill');
		$where['sb.user_id']=$this->mCuid;
		$Billlist=$ServiceBill->getMyBillByWhere($where);//getMyBillByWhere
		$Usp=D('UserServiceProvider');
		$Serlist=$Usp->getServiceComByUserid($this->mCuid);//服务商列表
		$this->assign('com',$Serlist);
		$this->assign('list',$Billlist['data']);
		$this->assign('page',$Billlist['page']);
		$this->display();
	}
	/**
	 * [searchBill 对账单筛选]
	 * @author Greedy-wolf 
	 * @date 2016-08-05 
	 */
	public function searchBill(){
		$comid=intval(I('param.com'));//服务商
		#$pro=I('param.pro');//产品
		$billno=I('param.billno');//单号
		$orderdate=I('param.orderdate');//日期
		$mailtype=intval(I('param.mailtype'));//邮寄
		if (!empty($comid)) {//服务商
			$where['sb.company_id']=$comid;
		}
		if (!empty($billno)) {//订单号
			$where['sb.bill_no']=array('like',"%$billno%");
		}
		if (!empty($mailtype)) {//是否邮寄
			if ($mailtype==1) {
				$where['sb.invoice_express_company']=array('neq','');
			}else{
				$where['sb.invoice_express_company']=array('exp','is null');
			}
		}
        #var_dump($where);die;
		if (!empty($orderdate)) {//时间
			$where['sb.bill_date']=str_replace('-','',$orderdate);
		}
        $where['sb.user_id']=$this->mCuid;
        $ServiceBill=D('ServiceBill');
		$Billlist=$ServiceBill->getMyBillByWhere($where);
		$this->assign('list',$Billlist['data']);
		$this->assign('page',$Billlist['page']);
		//筛选条件
		$Usp=D('UserServiceProvider');
		$Serlist=$Usp->getServiceComByUserid($this->mCuid);//服务商列表
		$this->assign('com',$Serlist);
		$this->display('index');
	}
	/**
	 * [billInfo 对账单详情]
	 */
	public function billInfo(){
		$billid=intval(I('param.bill'));
        if (!$billid) {
            $this->error("错误的账单号");
            exit();
        }
        $act=I('param.act','inc');
        $this->assign('act',$act);
		$Bill=D('ServiceBill');
		$overview=$Bill->getBillInfobyId($billid);//获取订单总览
        $overview['0']['bill_date']=substr($overview['0']['bill_date'],0,4).'/'.substr($overview['0']['bill_date'],4,2);
        $overview['0']['payprice']=sprintf("%01.2f",($overview[0]['price']+$overview[0]['diff_amount']));
		$this->assign('overview',$overview['0']);
		$result=self::_getBillInfo($billid);
        $this->assign('billservice',$result['billservice']);
        $this->assign('billinc',$result['billinc']);
        $this->assign('billsalary',$result['billsalary']);
        $this->display();
        
		
	}
	/**
	 * 导出对账单
	 */
	public function exportBilltoExcel(){
		$billid=intval(I('param.bill'));
        if (!$billid) {
            $this->error("错误的账单号");
            exit();
        }
		$result=self::_getBillInfo($billid);
        if (false == $result) {
            $this->error('没有数据');
            exit();
        }
		//导出到excel--
		$this->objExcel=setExcelHead();
		$this->objExcel=self::_billIncToExcel($this->objExcel,$result['billinc']);
		$this->objExcel=self::_billSalaryToExcel($this->objExcel,$result['billsalary']);
		$this->objExcel=self::_billServiceToExcel($this->objExcel,$result['billservice']);
		vendor('PHPExcel.PHPExcel.Reader.Excel5');
        $objWriter = new \PHPExcel_Writer_Excel5($this->objExcel);
        $ua = $_SERVER["HTTP_USER_AGENT"];
        $fileName = '导出发票_'.date('YmdHis').'.xls';
        set_filename_header($ua,$fileName);
        $objWriter->save('php://output');
	}

    /**
     * [_getBillInfo 获取对账单详情]
     * @param  [int] $billid [对账单id]
     * @return [type]         [description]
     */
    private function _getBillInfo($billid){
        $PayOrder=D('PayOrder');
        $paylist=$PayOrder->getPayOrderlistByBillid($billid);
        if(empty($paylist)){
            return false;
        }
        $billservice=array();
        $billinc=array();
        $billsalary=array();
        foreach ($paylist as $ks => $vs) {
            switch ($vs['type']) {
                case '1'://产品
                    $ServiceProductOrder=D('ServiceProductOrder');
                    $tmp=$ServiceProductOrder->getProOrderByPayOrderid($vs['id']);
                    if (false !==$tmp) {
                        $billservice=array_merge($billservice,$tmp);
                    }
                    break;
                case '2'://社保公积金
                    $ServiceInsuranceDetail=D('ServiceInsuranceDetail');
                    $tmp=$ServiceInsuranceDetail->getSGAllByOrderid($this->mCuid,$vs['id']);
                    if (false !==$tmp) {
                        $billinc=array_merge($billinc,$tmp);
                    }
                    break;
                case '3'://代发工资
                    $serviceOrderSalary=D('ServiceOrderSalary');
                    $where=array('sos.pay_order_id'=>$vs['id'],'sos.user_id'=>$this->mCuid);
                    $tmp=$serviceOrderSalary->getSalaryAllByPayOrderid($where);
                    foreach ($tmp as $key => $value) {
                        $tmp[$key]['price']=sprintf("%01.2f",($value['price']+$value['af_service_price']));
                    }
                    if (false !==$tmp) {
                        $billsalary=array_merge($billsalary,$tmp);
                    }
                    break;
            }
        }
        //对billinc进行处理
        if(false!==$billinc&&!empty($billinc)){
            $billinc=deal_inc($billinc);
        }
        $result=array(
            'billinc'=>$billinc,
            'billsalary'=>$billsalary,
            'billservice'=>$billservice,
            );
        return $result;
    }

	/**
	 * [_billIncToExcel 社保公积金]
	 * @param  [type] $objExcel [description]
	 * @param  [type] $data     [description]
	 * @return [type]           [description]
	 */
	public function _billIncToExcel($objExcel,$data){
        vendor('PHPExcel.PHPExcel');
        $objExcel->getActiveSheet('0')->setTitle('社保公积金');
        //开启自动换行
        $objExcel -> getDefaultStyle()->getAlignment()->setWrapText(true);
        //设置全局水平垂直居中
        $objExcel -> getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER) -> setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $arr=array('A1'=>'姓名','B1'=>'身份证号码','C1'=>'服务套餐','D1'=>'参保地','E1'=>'服务类型','F1'=>'缴纳年月','G2'=>'单位','H2'=>'个人','I2'=>'单位','J2'=>'个人','K1'=>'服务费','L1'=>'合计','G1'=>'社保','I1'=>'公积金');//
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
             /*   ->setCellValue('E1',$arr['E1'])
                ->mergeCells('F1:F2')*/
                ->setCellValue('E1',$arr['F1'])
                ->mergeCells('F1:G1')
                ->setCellValue('F1',$arr['G1'])
                ->setCellValue('F2',$arr['G2'])
                ->setCellValue('G2',$arr['H2'])
                ->mergeCells('H1:I1')
                ->setCellValue('H1',$arr['I1'])
                ->setCellValue('H2',$arr['I2'])
                ->setCellValue('I2',$arr['J2'])
                ->mergeCells('J1:J2')
                ->setCellValue('J1',$arr['K1']);
                /*->mergeCells('L1:L2')
                ->setCellValue('L1',$arr['L1']);//---*/
        //表头加粗
        $objExcel=setExcelTextFont('A1:M2',$objExcel);
        //设置宽度
        $width=array('B'=>23,'C'=>10,'D'=>10,'E'=>10,'F'=>10,);
        $objExcel=setExcelWidth($width,$objExcel);
        if (empty($data)) {
            return $objExcel;
        }
        //设置单元格属性为文本,防止将数字转科学计数法
        foreach ($data as $k => $v) {
                $type=array('1'=>'报增','2'=>'在保','3'=>'报减');
                $num=$k+3;
		        $objExcel->getactivesheet()
	                    ->setCellValue('A'.$num,$v['person_name'])   
	                    ->setCellValueExplicit('B'.$num,(string)$v['card_num'],\PHPExcel_Cell_DataType::TYPE_STRING)//防止科学计数法
	                    ->setCellValue('C'.$num,$v['name'])
	                    ->setCellValue('D'.$num,$v['location'])//参保地
	                    // ->setCellValue('E'.$num,$v['type'])
	                    ->setCellValue('E'.$num,$v['pay_date'])
	                    ->setCellValue('F'.$num,$v['soc_company'])
	                    ->setCellValue('G'.$num,$v['soc_person'])
	                    ->setCellValue('H'.$num,$v['pro_company'])
	                    ->setCellValue('I'.$num,$v['pro_person'])
	                    ->setCellValue('J'.$num,empty($v['service_price'])?'/':$v['service_price'])//残障金
	                    ->setCellValue('K'.$num,$v['price']);//合计
		}
        
        return $objExcel;
	}
	/**
	 * [_billSalaryToExcel 工资]
	 * @param  [type] $objExcel [description]
	 * @param  [type] $data     [description]
	 * @return [type]           [description]
	 */
	public function _billSalaryToExcel($objExcel,$data){
		//设置表头--合并表格---遍历写入
        vendor('PHPExcel.PHPExcel');
        $msgWorkSheet = new \PHPExcel_Worksheet($objExcel,'代发工资'); //创建一个工作表
        $objExcel->addSheet($msgWorkSheet); //插入工作表
        $objExcel->setactivesheetindex();
        //开启自动换行
        $objExcel -> getDefaultStyle()->getAlignment()->setWrapText(true);
        //设置全局水平垂直居中
        $objExcel -> getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER) -> setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objExcel->setActiveSheetIndex('1')
                ->mergeCells('A1:A2')//合并单元格
                ->setCellValue('A1','姓名')
                ->mergeCells('B1:B2')
                ->setCellValue('B1','身份证号码')
                ->mergeCells('C1:C2')
                ->setCellValue('C1','服务套餐')
                ->mergeCells('D1:D2')
                ->setCellValue('D1','银行')
                ->mergeCells('E1:E2')
                ->setCellValue('E1','卡号')
                ->mergeCells('F1:F2')
                ->setCellValue('F1','工资年月')
                ->mergeCells('G1:G2')
                ->setCellValue('G1','实发工资')
                ->mergeCells('H1:H2')
                ->setCellValue('H1','个人所得税')
                ->mergeCells('I1:I2')
                ->setCellValue('I1','服务费')
               	->mergeCells('J1:J2')
                ->setCellValue('J1','合计');
        #表头加粗
        $objExcel=setExcelTextFont('A1:J1',$objExcel);
         #设置宽度
        $width=array('B'=>23,'C'=>12,'D'=>10,'E'=>23,'F'=>10,'G'=>10,'H'=>13);
        $objExcel=setExcelWidth($width,$objExcel);
        if (empty($data)) {
            return $objExcel;
        }
        //设置单元格属性为文本,防止将数字转科学计数法
        $objExcel->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        foreach ($data as $k => $v) {
            $num=$k+3;
            $objExcel->getactivesheet()
                    ->setCellValue('A'.$num,$v['person_name'])   
                    ->setCellValueExplicit('B'.$num,(string)$v['card_num'],\PHPExcel_Cell_DataType::TYPE_STRING)//防止科学计数法setCellValue('B'.$num,$v['card_num'])
                    ->setCellValue('C'.$num,$v['name'])
                    ->setCellValue('D'.$num,$v['bank'])
                    ->setCellValueExplicit('E'.$num,(string)$v['account'],\PHPExcel_Cell_DataType::TYPE_STRING)//防止科学计数法
                    ->setCellValue('F'.$num,$v['date'])
                    ->setCellValue('G'.$num,$v['actual_salary'])
                    ->setCellValue('H'.$num,$v['deduction_income_tax'])
                    ->setCellValue('I'.$num,$v['af_service_price'])
                    ->setCellValue('j'.$num,$v['price']);
            }
        return $objExcel;
	}
    /**
     * [_billServiceToExcel 导出购买服务]
     * @param  [type] $objExcel [description]
     * @param  [type] $data     [description]
     * @return [type]           [description]
     */
    public function _billServiceToExcel($objExcel,$data){
        vendor('PHPExcel.PHPExcel');
        $msgWorkSheet = new \PHPExcel_Worksheet($objExcel,'服务套餐'); //创建一个工作表
        $objExcel->addSheet($msgWorkSheet); //插入工作表
        $objExcel->setactivesheetindex('2');
        $objExcel->getactivesheet()
                ->setCellValue('A1','合同号')
                ->setCellValue('B1','服务套餐')
                ->setCellValue('C1','套餐费')
                ->setCellValue('D1','总额');//---
        #表头加粗
        $objExcel=setExcelTextFont('A1:D1',$objExcel);
        #设置宽度
        $objExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        if (empty($data)) {
            return $objExcel;
        }
        foreach ($data as $k => $v) {
            $num=$k+2;
            $objExcel->getactivesheet()
                    ->setCellValue('A'.$num,$v['id'])
                    ->setCellValue('B'.$num,$v['name'])
                    ->setCellValue('C'.$num,$v['price'])
                    ->setCellValue('D'.$num,$v['actual_amount']);//---
        }
        return $objExcel;
    }
	




	/***旧代码↓***/
	/**
	 * index function
	 * 账单首页列表
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function index1(){
		$companyName = I('param.companyName','');
		$orderDate = intval(str_replace('/','',I('param.orderDate',0)));
		$state = I('param.state','');
		
		//拼接sql条件语句
		//$condition = 'po.company_id = '.$this->mCid.' and sb.state <> -9';
		$condition = 'po.company_id = '.$this->mCid;
		$condition .= $companyName?" and ci.company_name like '%{$companyName}%' ":'';
		$condition .= $orderDate?" and sb.order_date = {$orderDate}":'';
		$condition .= intval($state) || '0' == $state?" and sb.state = {$state} ":'';
		
		$serviceBill = D('ServiceBill');
		$serviceBillResult = $serviceBill->getBillList($this->mCid,$condition);
		$this->assign('serviceBillResult',$serviceBillResult['serviceBillResult']);// 赋值数据集
		$this->assign('page',$serviceBillResult['show']);// 赋值分页输出
		$this->display();
	}
	
	/**
	 * detail function
	 * 账单详情
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function detail(){
		($billNo = I('param.billNo','')) || $this->error('缺少订单号!');
		($id = I('param.id','')) || $this->error('缺少参数!');
		//$billNo = 1;
		//获取账单
		$serviceBill = D('ServiceBill');
		//$serviceBillResult = $serviceBill->comBillDetail($this->mCid,$billNo);
		$serviceBillResult = $serviceBill->comBillDetailDT($id,$billNo);
		if ($serviceBillResult) {
			$this->assign('serviceBillResult',$serviceBillResult['serviceBillResult']);// 赋值数据集
			$this->assign('serviceBillDetail',$serviceBillResult['serviceBillDetail']);// 赋值数据集
			$this->assign('page',$serviceBillResult['show']);// 赋值分页输出
			$this->display();
		}else {
			$this->error($serviceBill->getError());
		}
	}
	
	/**
	 * payInfo function
	 * 付款信息
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function payInfo(){
		//if (IS_AJAX) {
		if (IS_POST) {
			$billId = intval(I('post.billId',0));
			$billNo = I('post.billNo','');
			if ($billId && $billNo) {
				$serviceBillResult = $this->_payInfo($billId,$billNo);
				if ($serviceBillResult) {
					//$this->success(json_encode($serviceBillResult));
					//$this->success($serviceBillResult);
					$this->ajaxReturn(array('status'=>1,'data'=>$serviceBillResult));
				}else {
					$this->error('该账单不存在!');
				}
			}else {
				$this->error('非法参数!');
			}
		}else {
			$this->error('非法操作!');
		}
	}
	
		
	/**
	 * _payInfo function
	 * 付款信息
	 * @access private
	 * @param int $billId 账单ID
	 * @param int $billNo 账单号
	 * @return array 账单信息数组
	 * @author rohochan <rohochan@gmail.com>
	 **/
	private function _payInfo($billId = 0,$billNo = ''){
		if ($billId && $billNo) {
			$serviceBill = D('ServiceBill');
			$serviceBillResult = $serviceBill->field('sb.*,so.product_order_id as product_order_id,so.order_date as so_order_date')->join('as sb left join '.C('DB_PREFIX').'service_order as so on sb.order_id = so.id ')->where(array('sb.id'=>$billId,'bill_no'=>$billNo))->find();
			if ($serviceBillResult) {
				//获取上期账单结余
				//$servicePrevBillResult = $serviceBill->getPrevBill($this->mCid,$serviceBillResult['order_date']);
				$servicePrevBillResult = $serviceBill->getPrevBill($serviceBillResult['product_order_id'],$serviceBillResult['so_order_date']);
				if ($servicePrevBillResult) {
					$serviceBillResult['prevBalanceTotal'] = $servicePrevBillResult['balance_total'];
					$serviceBillResult['prevBalanceTotalValue'] = $servicePrevBillResult['balance_total']>0?'+'.$servicePrevBillResult['balance_total']:$servicePrevBillResult['balance_total'];
				}else {
					$serviceBillResult['prevBalanceTotal'] = 0;
					$serviceBillResult['prevBalanceTotalValue'] = 0;
				}
				$serviceBillResult['totalPrice'] = $serviceBillResult['price'] - $serviceBillResult['prevBalanceTotal'];
				$serviceBillResult['totalPrice'] = $serviceBillResult['totalPrice']>0 ?$serviceBillResult['totalPrice'] : 0;
				
				
				$serviceBillResult['orderDateValue'] = substr_replace($serviceBillResult['order_date'],'/',4,0);
				
				//查询参保城市
				$serviceOrder = D('ServiceOrder');
				$serviceBillResult['city'] = $serviceOrder->getServiceOrderLocation($serviceBillResult['order_id']);
				//$serviceBillResult['city'] = $serviceOrder->getServiceOrderLocation(3);
				
				//获取服务订单信息
				$serviceBillResult['productOrder'] = D('ProductOrder')->getProductOrderByProductOrderId($this->mCid,$serviceBillResult['product_order_id']);
				if ($serviceBillResult['productOrder']) {
					$serviceBillResult['billName'] = $serviceBillResult['orderDateValue'].$serviceBillResult['productOrder']['product_name'].'('.$serviceBillResult['productOrder']['company_name'].')';
					$serviceBillResult['openBank'] = $serviceBillResult['productOrder']['bank'].$serviceBillResult['productOrder']['branch'];
				}
				return $serviceBillResult;
			}else {
				return false;
			}
		}else {
			return false;
		}
	}
	
	/**
	 * detail function
	 * 账单详情
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function detailOld(){
		//获取账单
		$ServiceBill = M('ServiceBill as sb');
		$ServiceBillResult = $ServiceBill->field('sb.*,so.order_date')->join('left join '.C('DB_PREFIX').'service_order as so on sb.order_id = so.id ')->join('LEFT join '.C('DB_PREFIX').'product_order as po on so.product_order_id = po.id and po.company_id = '.$this->mCid)->where(array('bill_no'=>$billNo))->find();
		if ($ServiceBillResult) {
			$billDetail['companyName'] = $this->mCompanyUser['CompanyInfo']['company_name'];
			$billDetail['orderDateValue'] = substr($ServiceBillResult['order_date'] ,0,4).'年'.substr($ServiceBillResult['order_date'] ,-2,2).'月';
			$ServiceBillResult['orderDateValue'] = substr($ServiceBillResult['order_date'] ,0,4).'/'.substr($ServiceBillResult['order_date'] ,-2,2);
			
			/*$orderPerDetail = M('orderPerDetail as opd');
			$orderPerDetailResult = $orderPerDetail->field('opd.*,pb.account_name,pb.card_num,ss.wages')->join('left join '.C('DB_PREFIX').'person_base as pb on opd.base_id = pb.id ')->join('left join '.C('DB_PREFIX').'service_salary as ss on opd.base_id = ss.base_id and date = '.$ServiceBillResult['order_date'])->where(array('opd.order_id'=>$ServiceBillResult['order_id']))->select();
			echo $orderPerDetail->_sql().'</br>';
			$ServiceBillResult['list'] = $orderPerDetailResult;*/
			
			$serviceBillDetail = M('ServiceBillDetail');
			$serviceBillDetailResult = $serviceBillDetail->field(true)->where(array('bill_id'=>$ServiceBillResult['id']))->select();
			$ServiceBillResult['list'] = $serviceBillDetailResult;
			$billDetail['data'] = $ServiceBillResult;
			$billDetail['payInfo'] = $this->_payInfo($ServiceBillResult['id']);
		}else {
			$this->error('该账单不存在!');
		}
		//$this->display();
	}
	
	/*
	 * 下载账单
	 */
	public function downloadBill(){
		$id = I('param.id',0);
		if(empty($id)) $id=0;
		$billNo = I('param.billNo',0);
		if(empty($billNo)) $billNo=0;
		$serviceBill =  D('ServiceBill');
		$serviceBillResult = $serviceBill->field(true)->where(array('id'=>$id,'bill_no'=>$billNo))->find();
		$data = $serviceBill->downData($id,$billNo);
		
		$xlsTitle = iconv('utf-8', 'gb2312', time());//文件名称
		//$fileName = $this->mCompanyUser['CompanyInfo']['company_name'].date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
		$fileName = $this->mCompanyUser['CompanyInfo']['company_name'].'_'.$serviceBillResult['order_date'].'_'.time();//or $xlsTitle 文件名称可根据自己情况设定
		Vendor('PHPExcel.PHPExcel');
		$objPHPExcel = new \PHPExcel();
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:AB1');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1',"账单明细");
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('A2:C2');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2',"单位编号：".$this->mCompanyUser['id']);
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('D2:G2');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2',"单位名称：".$this->mCompanyUser['CompanyInfo']['company_name']);
		$objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('A3:C3');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3',"账单年月：".substr_replace($serviceBillResult['order_date'],'年',4,0).'月');
		$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setName('宋体');
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D3',"状态：".get_status_value($serviceBillResult['state'],'ServiceBillState'));
		$objPHPExcel->getActiveSheet()->getStyle('D3')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('D3')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('K3:N3');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K3',"打印日期：".date('Y-m-d H:i:s'));
		$objPHPExcel->getActiveSheet()->getStyle('K3')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('K3')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('E4:H4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E4',"应收金额");
		$objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('I4:K4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I4',"养老保险");
		$objPHPExcel->getActiveSheet()->getStyle('I4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('I4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('I4')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('L4:N4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L4',"医疗保险");
		$objPHPExcel->getActiveSheet()->getStyle('L4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('L4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('L4')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('O4:Q4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O4',"工伤保险");
		$objPHPExcel->getActiveSheet()->getStyle('O4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('O4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('O4')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('R4:T4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R4',"失业保险");
		$objPHPExcel->getActiveSheet()->getStyle('R4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('R4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('R4')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('U4:W4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U4',"生育保险");
		$objPHPExcel->getActiveSheet()->getStyle('U4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('U4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('U4')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('X4:Z4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X4',"公积金");
		$objPHPExcel->getActiveSheet()->getStyle('X4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('X4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('X4')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('AA4:AC4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA4',"其他保险");
		$objPHPExcel->getActiveSheet()->getStyle('AA4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('AA4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('AA4')->getFont()->setName('宋体');
		
		//$objPHPExcel->getActiveSheet()->mergeCells('AD4:AD4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD4',"残障金/其他");
		$objPHPExcel->getActiveSheet()->getStyle('AD4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('AD4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('AD4')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('AE4:AG4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE4',"代发工资");
		$objPHPExcel->getActiveSheet()->getStyle('AE4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('AE4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('AE4')->getFont()->setName('宋体');
		
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('AE4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('AD4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('AA4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('X4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('U4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('R4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('O4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('L4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('I4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('E4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$array = array('A'=>'序号','B'=>'社保号','C'=>'姓名','D'=>'身份证号','E'=>'应收合计','F'=>'个人合计','G'=>'单位合计','H'=>'应发工资','I'=>'缴费基数','J'=>'个人交','K'=>'单位交','L'=>'缴费基数',
			'M'=>'个人交','N'=>'单位交','O'=>'缴费基数','P'=>'个人交','Q'=>'单位交','R'=>'缴费基数',
			'S'=>'个人交','T'=>'单位交','U'=>'缴费基数','V'=>'个人交','W'=>'单位交','X'=>'缴费基数','Y'=>'个人交','Z'=>'单位交','AA'=>'缴费基数','AB'=>'个人交','AC'=>'单位交','AD'=>'残障金/其他','AE'=>'实发工资','AF'=>'个人所得税','AG'=>'服务费');
		foreach($array as $k=>$v){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($k.'5',$v);
			$objPHPExcel->getActiveSheet()->getStyle($k.'5')->getFont()->setSize(9);
			$objPHPExcel->getActiveSheet()->getStyle($k.'5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle($k.'5')->getFont()->setName('宋体');
		}
		//$data = $this->downData();
		foreach($data as $k=>$v){
			$total = $v['soc_company']+$v['tex']+$v['soc_person']+$v['pro_company']+$v['pro_person']+$v['disabled']+$v['other']+$v['actual_salary']+$v['soc_service']+$v['pro_service']+$v['dis_service']+$v['salary_service']+$v['soc_post_price']+$v['pro_post_price'];
			$person = $v['soc_person'] + $v['pro_person'] + $v['tex'];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($k+6),($k+1));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($k+6),$v['scard_number']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($k+6),$v['user_name']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.($k+6),$v['card_num'],\PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($k+6))->getNumberFormat()->setFormatCode("@");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($k+6),$total);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($k+6),$person);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.($k+6),($total - $person));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.($k+6),$v['salary']);
			if(!empty($v['srule']['rule']['items'] )){
				foreach($v['srule']['rule']['items'] as $k1=>$v1){
					if(strstr($v1['name'],'养老')){
						//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.($k+6),$v['samount']?$v['samount']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.($k+6),$v1['rules']['amount']?$v1['rules']['amount']:$v['samount']?$v['samount']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.($k+6),$v1['rules']['personSum']?$v1['rules']['personSum']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.($k+6),$v1['rules']['companySum']?$v1['rules']['companySum']:0);
					}elseif(strstr($v1['name'],'医疗保险')){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.($k+6),$v1['rules']['amount']?$v1['rules']['amount']:$v['samount']?$v['samount']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.($k+6),$v1['rules']['personSum']?$v1['rules']['personSum']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.($k+6),$v1['rules']['companySum']?$v1['rules']['companySum']:0);
					}elseif(strstr($v1['name'],'工伤')){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.($k+6),$v1['rules']['amount']?$v1['rules']['amount']:$v['samount']?$v['samount']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.($k+6),$v1['rules']['personSum']?$v1['rules']['personSum']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q'.($k+6),$v1['rules']['companySum']?$v1['rules']['companySum']:0);
					}elseif(strstr($v1['name'],'失业')){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R'.($k+6),$v1['rules']['amount']?$v1['rules']['amount']:$v['samount']?$v['samount']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S'.($k+6),$v1['rules']['personSum']?$v1['rules']['personSum']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T'.($k+6),$v1['rules']['companySum']?$v1['rules']['companySum']:0);
					}elseif(strstr($v1['name'],'生育')){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U'.($k+6),$v1['rules']['amount']?$v1['rules']['amount']:$v['samount']?$v['samount']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V'.($k+6),$v1['rules']['personSum']?$v1['rules']['personSum']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W'.($k+6),$v1['rules']['companySum']?$v1['rules']['companySum']:0);
					}else {
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA'.($k+6),$v1['rules']['amount']?$v1['rules']['amount']:$v['samount']?$v['samount']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB'.($k+6),$objPHPExcel->getActiveSheet()->getCell('AB'.($k+6))->getValue()+($v1['rules']['personSum']?$v1['rules']['personSum']:0));
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC'.($k+6),$objPHPExcel->getActiveSheet()->getCell('AC'.($k+6))->getValue()+($v1['rules']['companySum']?$v1['rules']['companySum']:0));
					}
				}
			}else{
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC'.($k+6),0);
			}
			
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X'.($k+6),$v['gamount']?$v['gamount']:0);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y'.($k+6),$v['pro_person']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z'.($k+6),$v['pro_company']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD'.($k+6),($v['disabled'] + $v['other']+$v['soc_post_price']+$v['pro_post_price']));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE'.($k+6),$v['actual_salary']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF'.($k+6),$v['tex']);
			//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AG'.($k+6),($v['salary_service'] + ($v['soc_service']>0?$v['soc_service']:$v['pro_service'])));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AG'.($k+6),($v['salary_service'] + $v['soc_service']+$v['pro_service']));
		}
		
		//exit(dump($data));
		header('pragma:public');
		header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
		header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
	
	public function downloadBill1(){
		$id = I('param.id',0);
		if(empty($id)) $id=0;
		$billNo = I('param.billNo',0);
		if(empty($billNo)) $billNo=0;
		$serviceBill =  D('ServiceBill');
		$serviceBillResult = $serviceBill->field(true)->where(array('id'=>$id,'bill_no'=>$billNo))->find();
		$data = $serviceBill->downData($id,$billNo);
		
		$xlsTitle = iconv('utf-8', 'gb2312', time());//文件名称
		//$fileName = $this->mCompanyUser['CompanyInfo']['company_name'].date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
		$fileName = $this->mCompanyUser['CompanyInfo']['company_name'].'_'.$serviceBillResult['order_date'].'_'.time();//or $xlsTitle 文件名称可根据自己情况设定
		Vendor('PHPExcel.PHPExcel');
		$objPHPExcel = new \PHPExcel();
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:AB1');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1',"账单明细");
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('A2:C2');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2',"单位编号：".$this->mCompanyUser['id']);
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('D2:G2');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2',"单位名称：".$this->mCompanyUser['CompanyInfo']['company_name']);
		$objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('A3:C3');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3',"账单年月：".substr_replace($serviceBillResult['order_date'],'年',4,0).'月');
		$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setName('宋体');
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D3',"状态：".get_status_value($serviceBillResult['state'],'ServiceBillState'));
		$objPHPExcel->getActiveSheet()->getStyle('D3')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('D3')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('K3:N3');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K3',"打印日期：".date('Y-m-d H:i:s'));
		$objPHPExcel->getActiveSheet()->getStyle('K3')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('K3')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('E4:H4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E4',"应收金额");
		$objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('I4:K4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I4',"养老保险");
		$objPHPExcel->getActiveSheet()->getStyle('I4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('I4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('I4')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('L4:N4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L4',"医疗保险");
		$objPHPExcel->getActiveSheet()->getStyle('L4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('L4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('L4')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('O4:P4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O4',"工伤保险");
		$objPHPExcel->getActiveSheet()->getStyle('O4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('O4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('O4')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('Q4:S4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q4',"失业保险");
		$objPHPExcel->getActiveSheet()->getStyle('Q4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('Q4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('Q4')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('T4:U4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T4',"生育保险");
		$objPHPExcel->getActiveSheet()->getStyle('T4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('T4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('T4')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('V4:X4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V4',"公积金");
		$objPHPExcel->getActiveSheet()->getStyle('V4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('V4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('V4')->getFont()->setName('宋体');
		
		//$objPHPExcel->getActiveSheet()->mergeCells('Y4:Y4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y4',"残障金/其他");
		$objPHPExcel->getActiveSheet()->getStyle('Y4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('Y4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('Y4')->getFont()->setName('宋体');
		
		$objPHPExcel->getActiveSheet()->mergeCells('Z4:AB4');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z4',"代发工资");
		$objPHPExcel->getActiveSheet()->getStyle('Z4')->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('Z4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('Z4')->getFont()->setName('宋体');
		
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('Y4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('V4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('T4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('Q4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('O4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('L4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('I4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('E4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$array = array('A'=>'序号','B'=>'社保号','C'=>'姓名','D'=>'身份证号','E'=>'应收合计','F'=>'个人合计','G'=>'单位合计','H'=>'应发工资','I'=>'缴费基数','J'=>'个人交','K'=>'单位交','L'=>'缴费基数',
			'M'=>'个人交','N'=>'单位交','O'=>'缴费基数','P'=>'单位交','Q'=>'缴费基数',
			'R'=>'个人交','S'=>'单位交','T'=>'缴费基数','U'=>'单位交','V'=>'缴费基数','W'=>'个人交','X'=>'单位交','Y'=>'残障金/其他','Z'=>'实发工资','AA'=>'个人所得税','AB'=>'服务费');
		foreach($array as $k=>$v){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($k.'5',$v);
			$objPHPExcel->getActiveSheet()->getStyle($k.'5')->getFont()->setSize(9);
			$objPHPExcel->getActiveSheet()->getStyle($k.'5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle($k.'5')->getFont()->setName('宋体');
		}
		//$data = $this->downData();
		foreach($data as $k=>$v){
			$total = $v['soc_company']+$v['tex']+$v['soc_person']+$v['pro_company']+$v['pro_person']+$v['disabled']+$v['other']+$v['actual_salary']+$v['soc_service']+$v['pro_service']+$v['dis_service']+$v['salary_service']+$v['soc_post_price']+$v['pro_post_price'];
			$person = $v['soc_person'] + $v['pro_person'] + $v['tex'];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($k+6),($k+1));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($k+6),$v['scard_number']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.($k+6),$v['user_name']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.($k+6),$v['card_num'],\PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($k+6))->getNumberFormat()->setFormatCode("@");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.($k+6),$total);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($k+6),$person);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.($k+6),($total - $person));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.($k+6),$v['salary']);
			$otherSocCost = 0;
			if(!empty($v['srule']['rule']['items'] )){
				foreach($v['srule']['rule']['items'] as $k1=>$v1){
					if(strstr($v1['name'],'养老')){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.($k+6),$v['samount']?$v['samount']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.($k+6),$v1['rules']['personSum']?$v1['rules']['personSum']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.($k+6),$v1['rules']['companySum']?$v1['rules']['companySum']:0);
					}elseif(strstr($v1['name'],'医疗保险')){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.($k+6),$v['samount']?$v['samount']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.($k+6),$v1['rules']['personSum']?$v1['rules']['personSum']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.($k+6),$v1['rules']['companySum']?$v1['rules']['companySum']:0);
					}elseif(strstr($v1['name'],'工伤')){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.($k+6),$v['samount']?$v['samount']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.($k+6),$v1['rules']['companySum']?$v1['rules']['companySum']:0);
					}elseif(strstr($v1['name'],'失业')){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q'.($k+6),$v['samount']?$v['samount']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R'.($k+6),$v1['rules']['personSum']?$v1['rules']['personSum']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S'.($k+6),$v1['rules']['companySum']?$v1['rules']['companySum']:0);
					}elseif(strstr($v1['name'],'生育')){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T'.($k+6),$v['samount']?$v['samount']:0);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U'.($k+6),$v1['rules']['companySum']?$v1['rules']['companySum']:0);
					}else {
						$otherSocCost += ($v1['rules']['personSum'] + $v1['rules']['companySum']);//dump( $otherSocCost);
					}
				}
			}else{
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T'.($k+6),0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U'.($k+6),0);
			}
			
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V'.($k+6),$v['gamount']?$v['gamount']:0);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W'.($k+6),$v['pro_person']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X'.($k+6),$v['pro_company']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y'.($k+6),($v['disabled'] + $v['other']+$v['soc_post_price']+$v['pro_post_price']+$otherSocCost));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z'.($k+6),$v['actual_salary']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA'.($k+6),$v['tex']);
			//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB'.($k+6),($v['salary_service'] + ($v['soc_service']>0?$v['soc_service']:$v['pro_service'])));
			//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB'.($k+6),($v['salary_service'] + $v['soc_service']+$v['pro_service']));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB'.($k+6),$v['salary_service'] + ($v['soc_service']?$v['salary_service'] + $v['soc_service']:$v['soc_services']));
		}
		
		header('pragma:public');
		header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
		header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
	
	public function downloadBill2(){
		$id = I('param.id',0);
		if(empty($id)) $id=0;
		$billNo = I('param.billNo',0);
		if(empty($billNo)) $billNo=0;
		$serviceBill =  D('ServiceBill');
		$serviceBillResult = $serviceBill->field(true)->where(array('id'=>$id,'bill_no'=>$billNo))->find();
		$data = $serviceBill->downData($id,$billNo);
		
		$xlsTitle = iconv('utf-8', 'gb2312', time());//文件名称
		//$fileName = $this->mCompanyUser['CompanyInfo']['company_name'].date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
		$fileName = $this->mCompanyUser['CompanyInfo']['company_name'].'_'.$serviceBillResult['order_date'].'_'.time();//or $xlsTitle 文件名称可根据自己情况设定
		Vendor('PHPExcel.PHPExcel');
		$objPHPExcel = new \PHPExcel();
		$start = 0;
		$array =  array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
		$dataType =  array('序号','社保号','姓名','身份证号','1应收合计','1个人合计','1单位合计','1应发工资','2缴费基数','2个人交','2单位交','3缴费基数', '3个人交','3单位交','4缴费基数','4单位交','5缴费基数','5个人交','5单位交','6缴费基数','6单位交','7缴费基数','7个人交','7单位交','残障金/其他','8实发工资','8个人所得税','8服务费');
		$tableType = array(array('0-'.count($dataType)=>'#账单明细'),array('0-3'=>'单位编号：@','3-4'=>'单位名称：#'),array('0-3'=>'账单年月：%','3-1'=>'状态：#','10-4'=>'打印日期：@'),array(1=>'应收金额','养老保险','医疗保险','工伤保险','失业保险','生育保险','公积金','代发工资'));
		$temp_array = array();
		foreach( $dataType as $k => $v ){
			foreach( $array as $key => $val){
				if($k == $key){
					$temp_array[$val] = $v;
				}
			}
		}
		foreach($tableType as $k => $v){
			foreach($v as $key => $val){
				$temp = explode('-',$key);
				if(!empty($temp[1])){
					if($temp[1] == 1){
						$tableType[$k][$array[$temp[0]].($k+1+$start)] = $val;
					}else{
						$tableType[$k][$array[$temp[0]].($k+1+$start).':'.$array[$temp[1] + $temp[0] - 1].($k+1+$start)] = $val;
					}
					unset($tableType[$k][$key]);
				}else{
					$temp_data = array();
					foreach( $temp_array as $k1 => $v1){
						if($key == intval($v1)){
							$temp_data[] = $k1;
							$temp_array[$k1] = str_replace(array(1,2,3,4,5,6,7,8,9,0),'',$v1);
						}
					}
					unset($tableType[$k][$key]);
					$tableType[$k][$temp_data[0].($k+1+$start).':'.array_pop($temp_data).($k+1+$start)] = $val;
				}
			}
		}
		$temp_count = count($tableType) + 1 + $start;
		foreach($temp_array as $k =>$v){
			$temp_array[$k.$temp_count] = $v;
			unset( $temp_array[$k]);
		}
		
		$tableType[]  = $temp_array;
		unset($temp_array);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		foreach($tableType as $k=>$v){
			foreach($v as $key => $val){
				$pos = explode(':',$key);
				
				if(!empty($pos[1])){
					$objPHPExcel->getActiveSheet()->mergeCells($key);
				}
				$objPHPExcel->setActiveSheetIndex(0)->getStyle($pos[0])->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($pos[0],$val);
				$objPHPExcel->getActiveSheet()->getStyle($key)->getFont()->setSize(13-$k);
				$objPHPExcel->getActiveSheet()->getStyle($key)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle($key)->getFont()->setName('宋体');
			}
		}
		
		header('pragma:public');
		header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
		header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
	
	private function downData(){
		$id = I('param.id',0);
		if(empty($id)) $id=0;
		$bill_no = I('param.billNo',0);
		if(empty($bill_no)) $bill_no=0;
		
		$serviceBill =  D('ServiceBill');
		$result = $serviceBill->downData($id,$bill_no);
		return $result;
	}
}