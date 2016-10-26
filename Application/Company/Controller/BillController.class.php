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
		$billno=I('param.billno','');//单号
		$orderdate=I('param.orderdate','');//日期
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
                    if (false !==$tmp&&!empty($tmp)) {
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
	private function _billIncToExcel($objExcel,$data){
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
	private function _billSalaryToExcel($objExcel,$data){
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
    private function _billServiceToExcel($objExcel,$data){
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