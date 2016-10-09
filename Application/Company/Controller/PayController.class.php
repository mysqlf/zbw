<?php
namespace  Company\Controller;
/**
 * 订单支付
 */

class PayController extends HomeController{

	protected $_serviceInsurancedetail;
	protected $_personInsuranceInfo;
	protected $_personInsurance;
	protected $_payOrder;
	protected $_ServiceProductOrder;

	public function _initialize(){
		parent::_initialize();
		$this->_payOrder = new \Company\Model\PayModel();

	}

	/**
	 * 支付
	 * @param  $[order_id]  [<订单id >]
	 */
	public function payOrder(){
		$order_id = I('get.orderId', '0');
		$type = I('get.orderType', '0', 'intval');//订单类型
		$payType = I('get.payType', '1', 'intval');//支付方式
		if(empty($order_id) || empty($type) || empty($payType)){
			$this->error('信息不完整');
		}

		$this->orderState($order_id);
		$orerInfo = $this->_payOrder->field('order_no,amount,diff_amount')->where(array('id'=> $order_id))->find();
		$data['user_id'] = $this->mCuid;
		$data['type'] = $type;
		$data['payType'] = $payType;
		$data['price'] = $orerInfo['amount'] + $orerInfo['diff_amount'];
		$data['order_no'] = $orerInfo['order_no'];
		if($payType == 1){
			$result = $this->_payOrder->alipay($data);
			header('Content-Type: text/html; charset=utf-8');
			echo $result;exit;
		}elseif($payType == 2){
			$result = $this->_payOrder->jdpay($data);
	        $this->assign('param',$result['param']);
	        $this->display('Jdpay/paySubmit');
		}
	}

	/**
	 *订单信息状态
	 */
	protected function orderState($order_id){
		$orderInfo = $this->_payOrder->field('id,state,amount,order_no')->where(array('id'=> $order_id))->find();
		if(empty($orderInfo))
		{
			$this->error( '订单不存在！');
		}
		if(empty($orderInfo['amount']) || empty($orderInfo['order_no']))
		{
			$this->error( '订单信息不完整！');
			//$this->ajaxReturn(array('status'=> 0, 'msg'=> '订单信息不完整！'));
		}		
		if($orderInfo['state'] == 1)
		{
			$this->error( '订单已支付！请不要重复支付。');
		}
	}	
	


	/**
	 * 支付宝 即时到账  同步跳转
	 */
	public function  successCalback(){
		IS_GET or $this->error('非法操作!');
		require(LIB_PATH.'Vendor/alipay/alipay.config.php');
		import("Vendor.alipay.lib.alipay_notify");
		//计算得出通知验证结果
		$alipayNotify = new \AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result) {//验证成功			
			//获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
			//商户订单号
			$out_trade_no = I('get.out_trade_no');
			//支付宝交易号 流水号
			$trade_no = I('get.trade_no');
			//交易状态
			$trade_status = I('get.trade_status');
			//通知时间
			$notify_time = I('get.notify_time');
			//交易金额
			$total_fee = I('get.total_fee');			
			if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
					//修改订单状态
						$orderInfo = $this->_payOrder->field(true)->where(array('order_no'=>$out_trade_no))->find();
						if($orderInfo){
							if($orderInfo['status'] == 1 ){
								//$this->error('订单'.$param['tradeNum'].'已支付，请勿重复支付！');														
								redirect(U('Order/index') ,2, '支付失败！订单'.$param['tradeNum'].'已支付，请勿重复支付！');
								paylog(date('Y-m-d H:i:s', time()).' 用户'.$this->mCuid.' 使用支付宝支付了订单。订单号为'.$out_trade_no.'支付失败！原因：重复支付');	
							}elseif($orderInfo['status'] == 0){
								$ods = array();
								$ods['state'] = 1;
								$ods['pay_type'] = 1;
								$ods['pay_time'] = $notify_time;
								$ods['transaction_no'] = $trade_no;
								$ods['actual_amount'] = $total_fee;
								if($this->_payOrder->where( array('order_no'=> $out_trade_no))->save($ods)){
									//更新总额
									$this->providerPrice($orderInfo, $ods['actual_amount']);

									paylog(date('Y-m-d H:i:s', time()).' 用户'.$this->mCuid.' 使用支付宝支付了订单。订单号为'.$out_trade_no.'。支付成功');	
									redirect(U('Order/index') ,2, '支付成功');
								}
							}
						}else{
							redirect(U('Order/index') ,2, '支付失败！');
						}
			}else {
		   	  redirect(U('Order/index') ,2, '支付失败！');
			}
		}
		else {
			//验证失败
			redirect(U('Order/index') ,2, '验证失败');
		}
	}
	
	/**
	 * 支付宝 即时到账 异步通知
	 */
	public function notify_url(){
		IS_GET or $this->error('非法操作!');
		require(LIB_PATH.'Vendor/alipay/alipay.config.php');
		import("Vendor.alipay.lib.alipay_notify");
		//计算得出通知验证结果
		$alipayNotify = new \AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		if($verify_result) {//验证成功	
			$out_trade_no = I('post.out_trade_no');//商户订单号
			$trade_no = I('post.trade_no');//支付宝交易号
			$trade_status = I('post.trade_status');//交易状态
			$notify_time = I('post.notify_time');
			if($_POST['trade_status'] == 'TRADE_FINISHED') {//TRADE_FINISHED请求后，这笔订单就结束了， (即时到账普通版)
				//注意：
				//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知			
				$orderInfo = $this->_payOrder->field(true)->where(array('order_no'=>$out_trade_no))->find();
				if($orderInfo && $orderInfo['order_no']){
					$this->_payOrder->where(array('id'=> $orderInfo['id']))->save(array('state'=> 1));

					paylog(date('Y-m-d H:i:s', time()).' 异步接口返回，订单'.$out_trade_no.'。支付成功，确认收货成功');

					echo "success";//请不要修改或删除
					exit;
				}
			}else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {//收到TRADE_SUCCESS请求后，后续一定还有至少一条通知记录，即TRADE_FINISHED (即时到账高级版)
			}
		}
		else {
			//验证失败
			echo "fail";
		}
	}




	/**
	 * jdpayWebSuccess function
	 * 京东支付成功跳转页面
	 * @return void
	 * @author rohochan<rohochan@gmail.com>
	 **/
	public function jdpaySuccess(){
			//****************************************	//MD5密钥要跟订单提交页相同，如Send.asp里的 key = "test" ,修改""号内 test 为您的密钥
														//如果您还没有设置MD5密钥请登陆我们为您提供商户后台，地址：https://merchant3.chinabank.com.cn/
				$key= $this->_payOrder->_jsPayMd5Key();					//登陆后在上面的导航栏里可能找到“B2C”，在二级导航栏里有“MD5密钥设置”
														//建议您设置一个16位以上的密钥或更高，密钥最多64位，但设置16位已经足够了
			//****************************************
				
			$v_oid     =trim($_POST['v_oid']);       // 商户发送的v_oid定单编号   
			$v_pmode   =trim($_POST['v_pmode']);    // 支付方式（字符串）   
			$v_pstatus =trim($_POST['v_pstatus']);   //  支付状态 ：20（支付成功）；30（支付失败）
			$v_pstring =trim($_POST['v_pstring']);   // 支付结果信息 ： 支付完成（当v_pstatus=20时）；失败原因（当v_pstatus=30时,字符串）； 
			$v_amount  =trim($_POST['v_amount']);     // 订单实际支付金额
			$v_moneytype  =trim($_POST['v_moneytype']); //订单实际支付币种    
			$remark1   =trim($_POST['remark1' ]);      //备注字段1
			$remark2   =trim($_POST['remark2' ]);     //备注字段2
			$v_md5str  =trim($_POST['v_md5str' ]);   //拼凑后的MD5校验值 
			/**
			 * 重新计算md5的值
			 */			                           
			$md5string=strtoupper(md5($v_oid.$v_pstatus.$v_amount.$v_moneytype.$key));			 
			if ($v_md5str==$md5string)
			{
				if($v_pstatus=="20")
				{
					//支付成功，可进行逻辑处理！
					//商户系统的逻辑处理（例如判断金额，判断支付状态，更新订单状态等等）......
					//修改订单状态
						$orderInfo = $this->_payOrder->field(true)->where(array('order_no'=>$v_oid))->find();
						if($orderInfo){
							if($orderInfo['status'] == 1 ){
								redirect(U('Order/index') ,2, '支付失败！订单'.$v_oid.'已支付，请勿重复支付！');
								paylog(date('Y-m-d H:i:s', time()).' 用户'.$this->mCuid.' 使用支付宝支付了订单。订单号为'.$v_oid.'支付失败！原因：重复支付');
							}elseif($orderInfo['status'] == 0){
								$ods = array();
								$ods['state'] = 1;
								$ods['pay_type'] = 1;
								$ods['pay_time'] = $notify_time;
							//	$ods['transaction_no'] = $trade_no;
								$ods['actual_amount'] = $v_amount;				
								if($this->_payOrder->where( array('order_no'=> $v_oid))->save($ods)){
									//更新总额
									$this->providerPrice($orderInfo, $ods['actual_amount']);
									paylog(date('Y-m-d H:i:s', time()).' 用户'.$this->mCuid.' 使用网银支付了订单。订单号为'.$v_oid.'。支付成功');
									redirect(U('Order/index') ,2, '支付成功');
								}
							}
						}else{
							redirect(U('Order/index') ,2, '支付失败！');
						}
				}else{
					if($v_pstatus == '30'){
						paylog(date('Y-m-d H:i:s', time()).' 用户'.$this->mCuid.' 使用网银支付了订单。订单号为'.$v_oid.'。支付失败,原因:'.$v_pstring);		
					}
					redirect(U('Order/index') ,2, '支付失败！');
				}

			}else{
				redirect(U('Order/index') ,2, '支付失败！数据校验失败');
			}
		
	}
	/**
	 * jdpayWebAsynNotificationCtrl function
	 * 京东支付状态异步通知
	 * @return void
	 * @author rohochan<rohochan@gmail.com>
	 **/
	public function jdpaynotify_url(){
		//****************************************	//MD5密钥要跟订单提交页相同，如Send.asp里的 key = "test" ,修改""号内 test 为您的密钥
													//如果您还没有设置MD5密钥请登陆我们为您提供商户后台，地址：https://merchant3.chinabank.com.cn/
			$key= $this->_payOrder->_jsPayMd5Key;						//登陆后在上面的导航栏里可能找到“B2C”，在二级导航栏里有“MD5密钥设置”
													//建议您设置一个16位以上的密钥或更高，密钥最多64位，但设置16位已经足够了
		//****************************************

		$v_oid     =trim($_POST['v_oid']);      
		$v_pmode   =trim($_POST['v_pmode']);      
		$v_pstatus =trim($_POST['v_pstatus']);      
		$v_pstring =trim($_POST['v_pstring']);      
		$v_amount  =trim($_POST['v_amount']);     
		$v_moneytype  =trim($_POST['v_moneytype']);
		$remark1   =trim($_POST['remark1' ]);     
		$remark2   =trim($_POST['remark2' ]);     
		$v_md5str  =trim($_POST['v_md5str' ]);     
		/**
		 * 重新计算md5的值
		 */
		                           
		$md5string=strtoupper(md5($v_oid.$v_pstatus.$v_amount.$v_moneytype.$key)); //拼凑加密串
		if ($v_md5str==$md5string)
		{
			
		   if($v_pstatus=="20")
			{
			   //支付成功
				//商户系统的逻辑处理（例如判断金额，判断支付状态(20成功,30失败),更新订单状态等等）......
				$orderInfo = $this->_payOrder->field(true)->where(array('order_no'=>$v_oid))->find();
				if($orderInfo && $orderInfo['order_no']){
					$this->_payOrder->where(array('id'=> $orderInfo['id']))->save(array('state'=> 1));
					paylog(date('Y-m-d H:i:s', time()).' 异步接口返回，订单'.$v_oid.'。支付成功，确认收货成功');					
				}
				
			}
		  echo "ok";exit;
			
		}else{
			echo "error";exit;
		}		
	}
	
	// private function diff_amount($data){
	// 	$diff_amount = M('user_service_provider')->field('diff_amount')->where(array('user_id'=> $data['user_id'], 'company_id'=> $this->mCid))->find();
	// 	$this->_payOrder->where( array('order_no'=> $data['out_trade_no']))->save(array('diff_amount'=> $diff_amount['diff_amount']));
	// }
	
	protected function providerPrice($orderInfo, $actual_amount){
		$m = M('user_service_provider', 'zbw_');
		$m->where("company_id={$orderInfo['company_id']} AND user_id={$orderInfo['user_id']}")->save("price = price+{$actual_amount}");
		return true;
	}
}