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
        $orderid=I('param.orderId');
        $orderNo=I('param.orderNo');//订单号
        $companyId=intval(I('param.companyId'));//公司ID
        $state=intval(I('param.state'));//付款状态0全部1已支付2未支付
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

    /**-------旧代码↓--------**/
   
    /**
     * [batchBilltoExcel 批量导出]
     * @return [type] [description]
     */
    public function batchBilltoExcel(){
        header('Content-Type: text/html; charset=UTF-8');
        //$orderId=I('post.orderid');
        //var_dump($orderId);die;
        $orderId=array( 
                    array('orderid' => 30),
                    array('orderid' => 41)
                    );
        if (empty($orderId)) {
            $this->error('参数错误');
            return 'false';
        }
        $this->objExcel=setExcelHead();
        $ServiceBillDetailCollect=D('ServiceBillDetailCollect');
        $SocialOrder=D('SocialOrder');
        $sheet=0;
        foreach ($orderId as $v) {
            $result=$ServiceBillDetailCollect->getBillInfoById($v['orderid'],1000);
           
            if (!empty($result['data'])) {
                 //订单相关信息
                $orderinfo =$SocialOrder->getOrderInfoById($this->mCuid,$v['orderid']);
                 if ($orderinfo['payment_type']==1) {
                    //社保
                    $this->objExcel=self::outSociInfoToExcel($result['data'],$this->objExcel,$orderinfo,$sheet);
                }else{
                    //工资
                    $this->objExcel=self::outSalaInfotoExcel($result['data'],$this->objExcel,$orderinfo,$sheet);
                }
                $sheet++;
            }
           
        }
        vendor('PHPExcel.PHPExcel.Reader.Excel5');
        $objWriter = new \PHPExcel_Writer_Excel5($this->objExcel);
        $ua = $_SERVER["HTTP_USER_AGENT"];       
        $fileName = '导出报表_'.date('YmdHis').'.xls';
        set_filename_header($ua,$fileName);
        $objWriter->save('php://output');
    }
    /**
     * [outSociInfoToExcel 导出社保账单详情]
     * @param  [array]  $data      [数据]
     * @param  [object] $objExcel  [phpexcel对象]
     * @param  [array]  $orderinfo [账单主要信息]
     * @param  integer  $sheet     [description]
     * @return [object]            [填充了新数据excel对象]
     */
    public function outSociInfoToExcel($data,$objExcel,$orderinfo,$sheet=0){
        //设置表头--合并表格---遍历写入
        if($sheet>0){
            vendor('PHPExcel.PHPExcel');
            $msgWorkSheet = new \PHPExcel_Worksheet($objExcel,$orderinfo['order_date'].'社保'); //创建一个工作表
            $objExcel->addSheet($msgWorkSheet); //插入工作表
            $objExcel->setactivesheetindex($sheet);
        }else{
            $objExcel->getActiveSheet()->setTitle($orderinfo['order_date'].'社保');
        }
        if (empty($data)) {
            return $objExcel;
        }
        //开启自动换行
        $objExcel -> getDefaultStyle()->getAlignment()->setWrapText(true);
        //设置全局水平垂直居中
        $objExcel -> getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER) -> setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // getactivesheet
        // setActiveSheetIndex
        $objExcel->getactivesheet()
                ->mergeCells('A1:A2')//合并单元格
                ->setCellValue('A1','姓名')
                ->mergeCells('B1:B2')
                ->setCellValue('B1','身份证号码')
                ->mergeCells('C1:C2')
                ->setCellValue('C1','会员产品')
                ->mergeCells('D1:D2')
                ->setCellValue('D1','参保地')
                ->mergeCells('E1:E2')
                ->setCellValue('E1','状态')
                ->mergeCells('F1:F2')
                ->setCellValue('F1','缴纳年月')
                ->mergeCells('G1:H1')
                ->setCellValue('G1','社保')
                ->setCellValue('G2','单位')
                ->setCellValue('H2','个人')
                ->mergeCells('I1:J1')
                ->setCellValue('I1','公积金')
                ->setCellValue('I2','单位')
                ->setCellValue('J2','个人')
                ->mergeCells('K1:K2')
                ->setCellValue('K1','残障金/其他')
                ->mergeCells('L1:L2')
                ->setCellValue('L1','差额')
                ->mergeCells('M1:M2')
                ->setCellValue('M1','服务费')
                ->mergeCells('N1:N2')
                ->setCellValue('N1','合计');//---未计算
         //设置宽度
        $objExcel->getActiveSheet()->getColumnDimension('B')->setWidth(23);
        //设置单元格属性为文本,防止将数字转科学计数法
        $objExcel->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        foreach ($data as $k => $v) {
            $num=$k+3;
            $count=$v['soc_post_price']+$v['pro_post_price']+$v['soc_service']+$v['pro_service']+$v['dis_service']+$v['salary_service'];//计算服务费
            $sum=$v['soc_company']+$v['soc_person']+$v['pro_company']+$v['pro_person']+$v['disabled']+$count;
            $objExcel->getactivesheet()
                        ->setCellValue('A'.$num,$v['person_name'])   
                        ->setCellValueExplicit('B'.$num,(string)$v['card_num'],\PHPExcel_Cell_DataType::TYPE_STRING)//防止科学计数法setCellValue('B'.$num,$v['card_num'])
                        ->setCellValue('C'.$num,$orderinfo['name'])//
                        ->setCellValue('D'.$num,showAreaName($v['template_location']))//参保地
                        ->setCellValue('E'.$num,'在保')
                        ->setCellValue('F'.$num,$v['pay_date'])
                        ->setCellValue('G'.$num,$v['soc_company'])
                        ->setCellValue('H'.$num,$v['soc_person'])
                        ->setCellValue('I'.$num,$v['pro_company'])
                        ->setCellValue('J'.$num,$v['pro_person'])
                        ->setCellValue('K'.$num,$v['disabled'])//残障金+其他
                        ->setCellValue('L'.$num,$v['state'])//差额---未完成
                        ->setCellValue('M'.$num,$count)//未计算---所有的项目相加?
                        ->setCellValue('N'.$num,$sum);//合计---未完成
        }
        return $objExcel;
    }
    /**
     * [outSalaInfotoExcel 导出工资账单]
     * @param  [array]  $data      [数据]
     * @param  [object] $objExcel  [phpexcel对象]
     * @param  [array]  $orderinfo [账单主要信息]
     * @param  integer  $sheet     [description]
     * @return [object]            [填充了数据excel对象]
     */
    public function outSalaInfotoExcel($data,$objExcel,$orderinfo,$sheet=0){
        //设置表头--合并表格---遍历写入
        if($sheet>0){
            vendor('PHPExcel.PHPExcel');
            $msgWorkSheet = new \PHPExcel_Worksheet($objExcel,$orderinfo['order_date'].'工资'); //创建一个工作表
            $objExcel->addSheet($msgWorkSheet); //插入工作表
            $objExcel->setactivesheetindex($sheet);
        }else{
            $objExcel->getActiveSheet()->setTitle($orderinfo['order_date'].'工资');
        }
        if (empty($data)) {
            return $objExcel;
        }
        //开启自动换行
        $objExcel -> getDefaultStyle()->getAlignment()->setWrapText(true);
        //设置全局水平垂直居中
        $objExcel -> getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER) -> setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // getactivesheet
        // setActiveSheetIndex
        $objExcel->setActiveSheetIndex($sheet)
                ->mergeCells('A1:A2')//合并单元格
                ->setCellValue('A1','姓名')
                ->mergeCells('B1:B2')
                ->setCellValue('B1','身份证号码')
                ->mergeCells('C1:C2')
                ->setCellValue('C1','会员产品')
                ->mergeCells('D1:D2')
                ->setCellValue('D1','应发工资')
                ->mergeCells('E1:E2')
                ->setCellValue('E1','实发工资')
                ->mergeCells('F1:F2')
                ->setCellValue('F1','发放年月')
                ->mergeCells('G1:J1')
                ->setCellValue('G1','扣款')
                ->setCellValue('G2','五险')
                ->setCellValue('H2','公积金')
                ->setCellValue('I2','残障金/其他')
                ->setCellValue('J2','个人所得税');
        $objExcel->getActiveSheet()->getColumnDimension('B')->setWidth(23);
        //设置单元格属性为文本,防止将数字转科学计数法
        $objExcel->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        foreach ($data as $k => $v) {
            $num=$k+3;
            $count=$v['soc_post_price']+$v['pro_post_price']+$v['soc_service']+$v['pro_service']+$v['dis_service']+$v['salary_service'];//计算服务费
            $sum=$v['soc_company']+$v['soc_person']+$v['pro_company']+$v['pro_person']+$v['disabled']+$v['other']+$count;
            $objExcel->getactivesheet()
                    ->setCellValue('A'.$num,$v['person_name'])   
                    ->setCellValueExplicit('B'.$num,(string)$v['card_num'],\PHPExcel_Cell_DataType::TYPE_STRING)//防止科学计数法setCellValue('B'.$num,$v['card_num'])
                    ->setCellValue('C'.$num,$orderinfo['name'])//
                    ->setCellValue('D'.$num,$v['salary'])//参保地
                    ->setCellValue('E'.$num,$v['actual_salary'])
                    ->setCellValue('F'.$num,$v['pay_date'])
                    ->setCellValue('G'.$num,$v['deduction_social_insurance'])
                    ->setCellValue('H'.$num,$v['deduction_provident_fund'])
                    ->setCellValue('I'.$num,$v['pro_company'])
                    ->setCellValue('J'.$num,$v['deduction_income_tax']);
            }
        return $objExcel;
    }
    /**
     * [orderstate 订单状态]
     * @param  [int] $state 状态标记
     * @return [string]        
     */
    public function orderstate($state){
        switch ($state) {
                case '-1':
                   $orderstate='审核失败';
                    break;
                case '-3':
                    $orderstate='支付失败';
                    break;
                case '-4':
                    $orderstate='办理失败';
                    break;
                case '0':
                    $orderstate='未审核';
                    break;
                case '1':
                    $orderstate='审核通过';
                    break;
                case '2':
                    $orderstate='待支付';
                    break;
                case '3':
                    $orderstate='已支付';
                    break;
                case '4':
                    $orderstate='已办理';
                    break;
                case '5':
                    $orderstate='完成';
                    break;
            }
        return $orderstate;
    }
     /**
     * [outBillToExcel 导出账单主列表]
     * @return [type] [description]
     */
    public function outBillToExcel(){
        $socialOrder=D('SocialOrder');
        $where=array(
                'user_id'=>$this->mCuid,
                'state'=>array('egt',2)
            );
        $result=$socialOrder->getBillList($where,1000);
        if (empty($result)) {
            $this->error('内部错误');
            return false;
        }
        $objExcel=setExcelHead();
        vendor('PHPExcel.PHPExcel.Reader.Excel5');
        $objWriter = new \PHPExcel_Writer_Excel5($objExcel);  
        /*设置表头*/
        //开启自动换行
        $objExcel -> getDefaultStyle() -> getAlignment()->setWrapText(true);
        //设置全局水平垂直居中
        $objExcel -> getDefaultStyle() -> getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER) -> setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objExcel->setActiveSheetIndex(0)
                ->setCellValue('A1','账单编号')
                ->setCellValue('B1','账单年月')
                ->setCellValue('C1','账单名称')
                ->setCellValue('D1','办理人数')
                ->setCellValue('E1','实付金额')
                ->setCellValue('F1','付款截止时间')
                ->setCellValue('G1','账单状态')
                ->setCellValue('H1','付款时间')
                ->setCellValue('I1','差额');
        $objExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        foreach ($result['data'] as $k => $v) {
            $num=$k+2;
            $objExcel->setActiveSheetIndex(0)
                        ->setCellValueExplicit('A'.$num,(string)$v['order_no'],\PHPExcel_Cell_DataType::TYPE_STRING)//防止科学计数法
                        ->setCellValue('B'.$num,$v['order_date'])
                        ->setCellValue('C'.$num,$v['ordername'])
                        ->setCellValue('D'.$num,$v['count'])
                        ->setCellValue('E'.$num,$v['price'])
                        ->setCellValue('F'.$num,$v['incpaydate'])
                        ->setCellValue('G'.$num,$v['paystate'])
                        ->setCellValue('H'.$num,$v['pay_time'])
                        ->setCellValue('I'.$num,$v['diff_amount']);
        }
        $ua = $_SERVER["HTTP_USER_AGENT"];
        $fileName=$_SESSION['onethink_home']['company_info']['company_name'].'_导出账单_'.date('YmdHis').'.xls';
        set_filename_header($ua,$fileName);
        $objWriter->save('php://output');
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