<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Service\Controller;
use Think\Controller;

/**
 * 对账单管理
 */
class BillController extends ServiceBaseController
{
    private $_bill;
    private $_billInfo;
    private $_payment_type;
    private $invoice_state;

    protected function _initialize()
    {
        parent::_initialize();
        $this->_bill =  D('ServiceBill');
    }

    /**
     * 企业账单
     * @return [type] [description]
     */
    public function comBillList()
    {
   
        $type = I('get.type', '1');//企业1 3个人" AND date_format(po.pay_time, '%Y/%m/%d') >= '{$pay_time}'";
        $company_name = I('get.company_name', '0');
        $bill_no = I('get.bill_no', '');
        $bill_date = I('get.bill_date', '');
        $payment_type = I('get.payment_type', '');
        $invoice_state = I('get.invoice_state', '999');


        $where = 'u.type = '.$type.' AND sb.company_id ='.$this->_AccountInfo['company_id'];
        if($company_name) $where.= ' AND ci.company_name like \'%'.$company_name.'%\'';
        if($bill_no) $where.= ' AND sb.bill_no= \''.$bill_no.'\'';
        if($bill_date){
            $bill_date = str_replace('/', '', substr($bill_date, 0, 7));
            $where.= " AND sb.bill_date = '{$bill_date}'";
        }

        if($invoice_state != '999') $where.= ' AND sb.invoice_state='.$invoice_state;

//echo $where;
        $result = $this->_bill->comBillList($this->_AccountInfo, $where);

        $this->assign('result',$result)->assign('product_list', $this->productAllList())->assign('type', $type);
        $this->display('Bill/company');
    }

    public function comBillDetail()
    {
        $where['type'] = I('get.type', '1');//企业1 3个人
        $where['id'] = I('get.id', '0');
        $where['export'] = I('get.export', '0');

        $result = $this->_bill->comBillDetail($this->_AccountInfo, $where);
        //权限
        $aouth = false;
        $adminInfo = D('Admin')->adminInfo($this->_AccountInfo['company_id'], $this->_AccountInfo['user_id']);
        if(in_array($adminInfo['group'], array('1,2'))){
            $aouth = true;
        }
        
        // foreach ($result['resSb'] as $key => $value) {
        //     if(!empty($value['insurance_detail'])){
        //         $sb = json_decode($value['insurance_detail'], true);
        //         $result['resSb'][$key]['sb_per'] = $sb['person'];
        //         $result['resSb'][$key]['sb_com'] = $sb['company'];
        //         $result['resSb'][$key]['service_price'] = $sb['service_price'];

        //         foreach ($sb['items'] as $k => $val) {
        //             if($val['name'] == '残障金'){
        //                 $result['resSb'][$key]['disable'] = $val['total'];
        //                 continue;
        //             }
        //         }                
        //         unset($result['resSb'][$key]['insurance_detail']);
        //     }    
        //     if(!empty($value['gjj'])){
        //         $gjj = json_decode($value['gjj'], true);
        //         $result['resSb'][$key]['gjj_per'] = $gjj['person'];
        //         $result['resSb'][$key]['gjj_com'] = $gjj['company'];
        //         $result['resSb'][$key]['service_price'] = $gjj['service_price'];
        //         foreach ($gjj['items'] as $k => $val) {
        //             if($val['name'] == '残障金'){
        //                 $result['resSb'][$key]['disable'] = $val['total'];
        //                 continue;
        //             }
        //         }                     
        //         unset($result['resSb'][$key]['gjj']);
        //     } 

        // }

        if($where['export']){
            $this->exportBill($result);exit;
        }
        $this->assign('result',$result)->assign('type',$where['type'])->assign('id',$where['id'])->assign('aouth', $aouth);
        $this->display('details');
    }
  
    /**
     * 个人账单
     * @return [type] [description]
     */
    public function perBillList()
    {
        $type = I('get.type', '3');//企业1 3个人
        $person_name = I('get.person_name', '');
        $product_id = I('get.product_id', '0');
        $bill_no = I('get.bill_no', '');
        $bill_date = I('get.bill_date', '');
        $payment_type = I('get.payment_type', '');
        $invoice_state = I('get.invoice_state', '');


        $where = 'u.type = '.$type.' AND sb.company_id ='.$this->_AccountInfo['company_id'];
        if($person_name) $where.= 'pb.id='.$person_name;
        if($product_id) $where.= 'sp.id='.$product_id;
        if($bill_no) $where.= 'sb.bill_no='.$bill_no;
        if($bill_date) $where.= 'sb.bill_date='.$bill_date;
        if($payment_type) $where.= 'sb.payment_type='.$payment_type;
        if($invoice_state) $where.= 'sb.invoice_state='.$invoice_state;

        $this->assign('result',$result)->assign('product_list', $this->productAllList())->assign('type', $type);
        $this->display('Bill/person');
    }

    public function perBillDetail()
    {
        $where['type'] = I('get.type', '1');//企业1 3个人
        $where['id'] = I('get.id', '0');
        $result = $this->_bill->perBillDetail($this->_AccountInfo, $where);
        //权限
        $aouth = false;
        $adminInfo = D('Admin')->adminInfo($this->_AccountInfo['company_id'], $this->_AccountInfo['user_id']);
        if(in_array($adminInfo['group'], array('1,2'))){
            $aouth = true;
        }

        $this->assign('result',$result)->assign('id',$id)->assign('aouth', $aouth);
        $this->display('details');
    }
  

    /**
     * 服务套餐
     */
    // protected function productAllList(){
    //     return  $result = M('service_product')->field('id,name')->where(array('company_id'=> $this->_AccountInfo['company_id']))->select();
    // }

    /**
     * 开票
     */
    public function invoice(){
        $where['id'] = I('post.id', '');
        $data['invoice_state'] = 1;
        $data['invoice_amount'] = I('post.invoice_amount', '0.00');
        $data['invoice_express_company'] = I('post.invoice_express_company', '');
        $data['invoice_express_no'] = I('post.invoice_express_no', '');
        $data['invoice_consignee'] = I('post.invoice_consignee', '');
        $data['invoice_consignee_phone'] = I('post.invoice_consignee_phone', '');
        $result =   $this->_bill->invoice($data, $where);
        if($result){
            $this->ajaxReturn(array('status'=>0, 'msg'=> '开票成功'));
        }else{
            $this->ajaxReturn(array('status'=>-1, 'msg'=> '开票失败'));
        }
    }

    /**
     * 开票默认信息
     */
    public function invoiceDefault(){
        if(IS_POST){
             $data['id'] = I('post.id', '');
             $data['type'] = I('post.type', '');
             $result =   $this->_bill->invoice($data);
             $this->ajaxReturn(array('status'=>1, 'msg'=> '', 'result'=> $result));
         }
    }

    /**
     * 导出
     */

    protected function exportBill($result=null, $type=1){
        vendor('PHPExcel.PHPExcel');
        vendor('PHPExcel.Writer.Excel5');
        $objPHPExcel = new \PHPExcel();        
        $objPHPExcel->getProperties()->setCreator("对账单");
        //设置当前的sheet
        $objPHPExcel->setActiveSheetIndex(0);
        //设置名称
      //   $title = array(array('A1', '姓名'), array('B1', '身份证号码'), array('C1', '服务套餐'), array('D1', '参保地'), array('E1', '服务类型'), array('F1', '缴纳年月'), array('G1', '社保'), array('H1', '公积金'), array('I1', '残障金'), array('J1', '服务费'), array('K1', '合计'));
   
      //   foreach ($title as $key => $value) {
      //      $objPHPExcel->getActiveSheet()->setCellValue($value[0], $value[1]);
      //   }
      //   //合并单元格
      //   // $objPHPExcel->getActiveSheet(0)->mergeCells('G1:H1');
      //   // $objPHPExcel->getActiveSheet(0)->mergeCells('I1:J1');
      //   // $objPHPExcel->getActiveSheet(0)->getStyle('G1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      //   // $objPHPExcel->getActiveSheet(0)->getStyle('I1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);    

      //   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G2', '单位');
      //   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H2', '个人');
      //   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I2', '单位');
      //   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J2', '个人');

      //   //输出数据      $result['resSb'] 
        $i = 0;
        if($result['resSb']){//'F1'=>'单位','F2'=>'个人','H2'=>'单位','H2'=>'个人', 'F1'=>'社保','H1'=>'公积金',
            //设置sheet的name
            $objPHPExcel->getActiveSheet()->setTitle('社保公积金');            
              $arr=array('A1'=>'姓名','B1'=>'身份证号码','C1'=>'服务套餐','D1'=>'参保地','E1'=>'缴纳年月','F2'=>'单位','G2'=>'个人','H2'=>'社保类型','I2'=>'单位','J2'=>'个人','K2'=>'公积金类型','L1'=>'服务费','M1'=>'合计','F1'=>'社保','I1'=>'公积金');
            $objPHPExcel->getactivesheet()
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
                    // ->mergeCells('F1:F2')
                    // ->setCellValue('F1',$arr['F1'])
                    ->mergeCells('F1:H1')
                    ->setCellValue('F1',$arr['F1'])
                    ->setCellValue('F2',$arr['F2'])
                    ->setCellValue('G2',$arr['G2'])
                    ->setCellValue('H2',$arr['H2'])
                    ->mergeCells('I1:K1')
                    ->setCellValue('I1',$arr['I1'])
                    ->setCellValue('I2',$arr['I2'])
                    ->setCellValue('J2',$arr['J2'])
                    ->setCellValue('K2',$arr['K2'])  
                    ->mergeCells('L1:L2')
                    ->setCellValue('L1',$arr['L1'])
                    ->mergeCells('M1:M2')
                    ->setCellValue('M1',$arr['M1']);
                    //->mergeCells('M1:M2')
                   // ->setCellValue('M1',$arr['M1']);//---
            $objPHPExcel->getactivesheet($i)->getStyle('F1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getactivesheet()->getStyle('I1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $adminState = adminState();
            foreach($result['resSb'] as $key=>$val){  
                $objPHPExcel->getActiveSheet()->setCellValue(chr(65) . ($key+3), $val['person_name']);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr(66) . ($key+3), $val['card_num'], 's');
                $objPHPExcel->getActiveSheet()->setCellValue(chr(67) . ($key+3), $this->convertUTF8($val['product_name']));
                $objPHPExcel->getActiveSheet()->setCellValue(chr(68) . ($key+3), $this->convertUTF8(showAreaName1($val['template_location'], 2)));
                $objPHPExcel->getActiveSheet()->setCellValue(chr(69) . ($key+3), $val['pay_date']);          
                $objPHPExcel->getActiveSheet()->setCellValue(chr(70) . ($key+3), $val['sb_com']);
                $objPHPExcel->getActiveSheet()->setCellValue(chr(71) . ($key+3), $val['sb_per']);
                $objPHPExcel->getActiveSheet()->setCellValue(chr(72) . ($key+3), $adminState['warranty'][$val['sb_type']]);
                $objPHPExcel->getActiveSheet()->setCellValue(chr(73) . ($key+3), $val['gjj_com']);
                $objPHPExcel->getActiveSheet()->setCellValue(chr(74) . ($key+3), $val['gjj_per']);
               $objPHPExcel->getActiveSheet()->setCellValue(chr(75) . ($key+3), $adminState['warranty'][$val['gjj_type']]);
                $objPHPExcel->getActiveSheet()->setCellValue(chr(76) . ($key+3), $val['service_price']);
                $objPHPExcel->getActiveSheet()->setCellValue(chr(77) . ($key+3), $val['sb_com']+$val['sb_per']+$val['gjj_com']+$val['gjj_per']+$val['service_price']); 
          }   
         $i++;
       }
        if($result['resSalary']){       
            $msgWorkSheet = new \PHPExcel_Worksheet($objPHPExcel,'代发工资'); //创建一个工作表
            $objPHPExcel->addSheet($msgWorkSheet); //插入工作表
            $objPHPExcel->setactivesheetindex();
            //objPHPExcel
            $objPHPExcel -> getDefaultStyle()->getAlignment()->setWrapText(true);
            //设置全局水平垂直居中
            $objPHPExcel -> getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER) -> setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->setActiveSheetIndex($i)
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

            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(23);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(23);
            //设置单元格属性为文本,防止将数字转科学计数法
            $objPHPExcel->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            foreach ($result['resSalary'] as $k => $v) {
                $num=$k+3;
                $objPHPExcel->getactivesheet()
                        ->setCellValue('A'.$num,$v['person_name'])   
                        ->setCellValueExplicit('B'.$num,(string)$v['card_num'],\PHPExcel_Cell_DataType::TYPE_STRING)//防止科学计数法setCellValue('B'.$num,$v['card_num'])
                        ->setCellValue('C'.$num,$v['product_name'])
                        ->setCellValue('D'.$num,$v['bank'])
                        ->setCellValueExplicit('E'.$num,(string)$v['account'],\PHPExcel_Cell_DataType::TYPE_STRING)//防止科学计数法
                        ->setCellValue('F'.$num,$v['date'])
                        ->setCellValue('G'.$num,$v['actual_salary'])
                        ->setCellValue('H'.$num,$v['tax'])
                        ->setCellValue('I'.$num,$v['service_price'])
                        ->setCellValue('j'.$num,$v['actual_salary']+$v['tax']+$v['service_price']);
                }
                $i++;
        }

        if($result['product']){   
            $msgWorkSheet = new \PHPExcel_Worksheet($objPHPExcel,'服务套餐'); //创建一个工作表
            $objPHPExcel->addSheet($msgWorkSheet); //插入工作表
            $objPHPExcel->setactivesheetindex($i);
            $objPHPExcel->getactivesheet()
                    ->setCellValue('A1','合同号')
                    ->setCellValue('B1','服务套餐')
                    ->setCellValue('C1','套餐费')
                    ->setCellValue('D1','总额');//---
            #表头加粗
             $styleArray = array(
                'font' => array(
                    'bold' => true
                )
                );
            $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray($styleArray);
            #
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            if (empty($result['product'])) {
                return $objPHPExcel;
            }
            foreach ($result['product'] as $k => $v) {
                $num=$k+2;
                $objPHPExcel->getactivesheet()
                        ->setCellValue('A'.$num,$v['id'])
                        ->setCellValue('B'.$num,$v['product_name'])
                        ->setCellValue('C'.$num,$v['price'])
                        ->setCellValue('D'.$num,$v['price']);//---
            }
        }
        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        // excel头参数 
        $ua = $_SERVER["HTTP_USER_AGENT"];
        $fileName = $type ==1 ? '企业' : '个人';
        $fileName = $fileName.$data['billInfo']['bill_date'].'_对账单_'.date('YmdHis').'.xls';
        set_filename_header($ua,$fileName);
        $objWriter->save('php://output');  
    }

  protected  function convertUTF8($str)
    {
        return $str;
       if(empty($str)) return '';
       return  iconv('gb2312', 'utf-8', $str);
    }    
}
    
    
