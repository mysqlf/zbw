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
 * 企业中心信息控制器
 * 主要获取购买服务记录和详情,信息通知等功能
 */
class InformationController extends HomeController {
	/**
	 * 跳转至信息通知页面
	 */
	public function index(){
		$this->msgList();
	}
	
	/**
	 * 获取信息通知列表
	 */
	public function msgList(){
		$userMsg = D('UserMsg');
		$result = $userMsg->getMsgByUserId($this->mCuid);
		if (false !== $result) {
			//dump($result);
			$this->assign('action','index');
			$this->assign('result',$result['data']);
			$this->assign('page',$result['page']);
			$this->display('msgList');
		}else {
			$this->error($userMsg->getError());
		}
	}
	
	/**
	 * 获取信息通知详情,修改信息状态（为已读）
	 */
	public function msgDetail(){
		if(IS_POST){
			$id = I('param.id');
			$userMsg = D('UserMsg');
			$result = $userMsg->getMsgById($this->mCuid,$id);
			if (false !== $result) {
				$this->ajaxReturn(array('status'=>1,'result'=>$result));
			}else {
				$this->ajaxReturn(array('status'=>0,'info'=>$userMsg->getError()));
			}
			/*$msgId['id'] = I('post.msgId');
			if(!empty($msgId['id'])){
				$userMsg = D('UserMsg');
				$userMsg->where($msgId)->save(array('state'=>1));
				$msgInfo = $userMsg->where($msgId)->select();
				if ($msgInfo) {
					$msgInfo['detail'] = str_replace("\n",'</br>',$msgInfo['detail']);
				}
				$this->ajaxReturn(array('status'=>1,'data'=>$msgInfo));
				//$this->success($msgInfo,'',true);
			}else{
				$this->error('非法操作！');
			}*/
		}else {
			$this->error('非法操作！');
		}
	}
	
	/**
	 * ajax获取（未读）信息条数
	 */
	public function msgCount(){
		if(IS_POST){
			$userMsg = D('UserMsg');
			$result = $userMsg->getUnreadMsgCountByUserId($this->mCuid);
			if (false !== $result) {
				$this->ajaxReturn(array('status'=>1,'result'=>$result));
			}else {
				$this->ajaxReturn(array('status'=>0,'info'=>$userMsg->getError()));
			}
		}else{
			$this->error('非法操作！');
		}
	}
	
	/**
	 * 我的会员
	 */
	public function myMember(){
		$serviceProductOrder = D('ServiceProductOrder');
		$serviceProductOrderResult = $serviceProductOrder->getMemberOrderList($this->mCuid);
		//dump($serviceProductOrderResult);
		//dump($serviceProductOrder->_sql());
		//dump($serviceProductOrder->getError());
		$this->assign('result',$serviceProductOrderResult['data']);
		$this->assign('page',$serviceProductOrderResult['page']);
		//$this->display();
	}
	
	/**
	 * 会员详情
	 */
	public function memberDetail(){
		if ($id = I('param.id',0)) {
			$serviceProductOrder = D('ServiceProductOrder');
			$serviceProductOrderResult = $serviceProductOrder->getMemberDetail($this->mCuid,$id);
			
			//dump($serviceProductOrderResult);
			//dump($serviceProductOrder->_sql());
			//dump($serviceProductOrder->getError());
			//dump($serviceProductOrder->getDbError());
			if ($serviceProductOrderResult) {
				//dump($serviceProductOrderResult);
				$this->assign('result',$serviceProductOrderResult);
				//$this->display();
			}else if(null === $serviceProductOrderResult){
				$this->error('会员不存在！');
			}else if(false === $serviceProductOrderResult){
				$this->error('系统内部错误！');
			}else {
				$this->error('未知错误！');
			}
		}else {
			$this->error('非法参数！');
		}
	}
	/**
	 * 获取购买服务记录
	 */
	public function myPackage(){
		//header('Content-Type: text/html; charset=UTF-8');
		$serviceProductOrder = D('ServiceProductOrder');
		$serviceProductOrderResult = $serviceProductOrder->getServiceProductOrderList($this->mCuid);
		if ($serviceProductOrderResult) {
			$Usp=D('UserServiceProvider');
			$Serlist=$Usp->getServiceComByUserid($this->mCuid);//服务商列表
			$CSlist=$Usp->getSaByUserid($this->mCuid);//客服列表
			$serviceProductOrderResult['data']=self::_getCSinfo($serviceProductOrderResult['data']);//获取客服相关信息
			$servicestate=array('0'=>'待签约','2'=>'服务中','服务完成');
			$showstate=array('-1'=>'停止服务','0'=>'待签约','已签约','服务中','服务完成');
			$this->assign('action','myPackage');
			$this->assign('servicestate',$servicestate);
			$this->assign('showstate',$showstate);
			$this->assign('scom',$Serlist);
			$this->assign('cs',$CSlist);
			$this->assign('prolist',$serviceProductOrderResult['data']);
			$this->assign('page',$serviceProductOrderResult['page']);
		}else{
			$serviceProduct = D('ServiceProduct');
			$result=$serviceProduct->getRecommendService();
			#var_dump($result);
		}
		$this->display();
	}
	/**
	 * [filterMypro 套餐筛选]
	 *
	 */
	public function filterMypro(){
		$com=I('param.companyId');
		$state=I('param.state');
		$service_state=I('param.service_state');
		$csid=I('param.adminId');
		if (!empty($com)) {//服务商
			$where['sp.company_id']=$com;
		}
		if ($state!=='') {//付款状态
			if ($state==3) {
				$where['spo.state']=array('lt',$state);
			}elseif($state==1){
				$where['spo.state']=array('egt',$state);
			}else{
				$where['spo.state']=$state;
			}
		}
		if ($service_state!=='') {//服务状态
			if ($service_state==4) {
				$where['spo.service_state']=array('lt',$service_state);
			}else{
				$where['spo.service_state']=$service_state;
			}
		}
		if (!empty($csid)) {#一个客户对应一个公司只有一个客服,客服对应公司
			$where['sp.company_id']=$csid;
		}
		if (!empty($where)) {//
			$where['spo.user_id']=$this->mCuid;
		}
		$servicestate=array('0'=>'待签约','2'=>'服务中','服务完成');
		$showstate=array('-1'=>'停止服务','0'=>'待签约','已签约','服务中','服务完成');
		$this->assign('servicestate',$servicestate);
		$this->assign('showstate',$showstate);
		$serviceProductOrder = D('ServiceProductOrder');
		$result=$serviceProductOrder->filterMyProOrder($where);
		$Usp=D('UserServiceProvider');
		$Serlist=$Usp->getServiceComByUserid($this->mCuid);//服务商列表
		$CSlist=$Usp->getSaByUserid($this->mCuid);//客服列表
		$result['data']=self::_getCSinfo($result['data']);//获取客服相关信息
		$this->assign('action','myPackage');
		$this->assign('scom',$Serlist);
		$this->assign('cs',$CSlist);
		$this->assign('prolist',$result['data']);
		$this->assign('page',$result['page']);
		$this->display('myPackage');
	}

	/*获取客服相关信息*/
	private function _getCSinfo($data){
		if(empty($data)) return false;
		$Usp=D('UserServiceProvider');
		foreach ($data as $key => $value) {
			$where['usp.company_id']=$value['service_company_id'];
			$where['usp.user_id']=$this->mCuid;
			$CS=$Usp->getCSByComidandUserid($where);
			if ($CS) {
				$data[$key]['csname']=$CS[0]['name'];
				$data[$key]['csqq']=$CS[0]['qq'];
			}
		}
		return $data;
	}
	/*public function serviceList(){
		$productOrder = D('ProductOrder');
		//$condition = array('po.state'=>2,'po.service_state'=>2,'po.company_id'=>$this->mCuid);
		$condition = array('po.company_id'=>$this->mCuid);
		$productOrderResult = $productOrder->getProductOrderLocationList($condition);
		if ($productOrderResult['productOrderResult']) {
			foreach ($productOrderResult['productOrderResult'] as $k => $v) {
				if (isset($v['warrantyLocationList'])) {
					$location = array();
					foreach ($v['warrantyLocationList'] as $kk => $vv) {
						$location[] = $vv['warranty_location'];
					}
					if ($location) {
						$productOrderResult['productOrderResult'][$k]['location'] = array_unique($location);
						$productOrderResult['productOrderResult'][$k]['locationCount'] = count($productOrderResult['productOrderResult'][$k]['location']);
						$productOrderResult['productOrderResult'][$k]['defaultLocationValue'] = showAreaName(reset($productOrderResult['productOrderResult'][$k]['location']));
					}
				}
			}
		}
		$this->assign('productOrderResult',$productOrderResult['productOrderResult']);
		$this->assign('page',$productOrderResult['page']);
		$this->display();
	}*/
	
	/**
	 * 获取购买服务详情
	 * 通过前端传过来的服务产品ID（orderId）查询出相应的服务产品数据和参保地数据
	 * 封装后返回给前端
	 */
	public function serviceDetail(){
		$pro_id=I('get.pro_id','0');
		$Spo=D('ServiceProductOrder');
		$spoid=$Spo->getSpoidByProid($pro_id,$this->mCuid);
		$id = I('get.id',$spoid['id']);
		if ($id>0) {
			$serviceProductOrder = D('ServiceProductOrder');
			$serviceProductOrderResult = $serviceProductOrder->getServiceProductOrderDetail($this->mCuid,$id);
			if ($serviceProductOrderResult) {
				$warrantyLocation=D('WarrantyLocation');
				$info=$warrantyLocation->getLocationByOrderid($serviceProductOrderResult['id']);
				$arr=array('1'=>'一','二','三','四','五','六','七','八','九','十');
				$serviceProductOrderResult['handle_day']=$arr[C('INSURANCE_HANDLE_DAYS')];
				$this->assign('action','myPackage');
				$this->assign('orderinfo',$serviceProductOrderResult);
				$this->assign('serviceinfo',$info);
				$this->display();
			}else if(null === $serviceProductOrderResult){
				$this->error('服务不存在！');
			}else if(false === $serviceProductOrderResult){
				$this->error('系统内部错误！');
			}else {
				$this->error('未知错误！');
			}
		}else {
			$this->error('非法参数！');
		}
	}
	/*public function serviceListDetail(){
		$productOrderId = intval(I('get.orderId',0));
		if ($productOrderId) {
			$productOrder = D('ProductOrder');
			$condition = array('po.company_id'=>$this->mCid,'po.id'=>$productOrderId);
			$productOrderResult = $productOrder->getProductOrderLocationByCondition($condition);
			$productOrderResult = reset($productOrderResult);
			if ($productOrderResult) {
				//if ($productOrderResult['warrantyLocationList']) {
				//	$location = array();
				//	foreach ($productOrderResult['warrantyLocationList'] as $kk => $vv) {
				//		$location[] = $vv['warranty_location'];
				//	}
				//	if ($location) {
				//		$productOrderResult['location'] = array_unique($location);
				//		$productOrderResult['locationCount'] = count($productOrderResult['location']);
				//		$productOrderResult['defaultLocationValue'] = showAreaName(reset($productOrderResult['location']));
				//	}
				//}
				$this->assign('productOrderResult',$productOrderResult);
				if ((1 == $productOrderResult['state'] || 2 == $productOrderResult['state']) && 0 < $productOrderResult['service_state']) {//已签约
					$this->display('paidService');
				}else if(0 == $productOrderResult['state'] || 0 == $productOrderResult['service_state']) {//未支付或未签约
					$this->display('unpaidService');
				}else {
					$this->display('unpaidService');
				}
			}else {
				$this->error('订单不存在!');
			}
		}else {
			$this->error('非法参数!');
		}
	}*/
	
	/**
	 * payInfo function
	 * 支付信息
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function payInfo(){
		if (IS_POST) {
			$id = I('param.productId');
			if ($id) {
				$serviceProductOrder = D('ServiceProductOrder');
				$productOrderResult = $serviceProductOrder->getServiceProductOrderPayInfo($this->mCuid,$id);
				if ($productOrderResult) {
					$productOrderResult['paymentDetailValue'] = html_entity_decode($productOrderResult['payment_detail']);
					$productOrderResult['openBank'] = $productOrderResult['bank'].$productOrderResult['branch'];
					$productOrderResult['discountPrice'] = $productOrderResult['modify_price']?($productOrderResult['price'] - $productOrderResult['modify_price']):0;
					$productOrderResult['discountPrice'] = $productOrderResult['discountPrice']>0?$productOrderResult['discountPrice']:0;
					$productOrderResult['beforeDiscountPrice'] = $productOrderResult['price'];
					//$productOrderResult['afterDiscountPrice'] = $productOrderResult['price'] - $productOrderResult['discountPrice'];
					$productOrderResult['afterDiscountPrice'] = $productOrderResult['modify_price'];
					//dump($productOrderResult);
					$this->ajaxReturn(array('status'=>1,'data'=>$productOrderResult));
				}else {
					$this->error('产品服务订单不存在!');
				}
			}else {
				$this->error('非法参数!');
			}
		}else {
			$this->error('非法操作!');
		}
	}
	/*public function payInfo(){
		if (IS_POST) {
			$productOrderId = I('post.productId');
			if ($productOrderId) {
				$productOrder = D('ProductOrder');
				$condition = array('po.company_id'=>$this->mCid,'po.id'=>$productOrderId);
				$productOrderResult = $productOrder->getProductOrderByCondition($condition);
				if ($productOrderResult) {
					$productOrderResult['paymentDetailValue'] = html_entity_decode($productOrderResult['payment_detail']);
					$productOrderResult['openBank'] = $productOrderResult['bank'].$productOrderResult['branch'];
					$productOrderResult['discountPrice'] = $productOrderResult['modify_price']?($productOrderResult['price'] - $productOrderResult['modify_price']):0;
					$productOrderResult['discountPrice'] = $productOrderResult['discountPrice']>0?$productOrderResult['discountPrice']:0;
					$productOrderResult['beforeDiscountPrice'] = $productOrderResult['price'];
					$productOrderResult['afterDiscountPrice'] = $productOrderResult['price'] - $productOrderResult['discountPrice'];
					$this->ajaxReturn(array('status'=>1,'data'=>$productOrderResult));
				}else {
					$this->error('产品服务订单不存在!');
				}
			}else {
				$this->error('非法参数!');
			}
		}else {
			$this->error('非法操作!');
		}
	}*/
	
	/**
	 * 撤销服务订单
	 */
	public function cancelOrder(){
		if(IS_POST) {
			$id = intval(I('post.orderId'));
			if ($id) {
				$serviceProductOrder = D('ServiceProductOrder');
				$serviceProductOrderResult = $serviceProductOrder->field(true)->where(array('id'=>$id,'user_id'=>$this->mCuid))->find();
				if ($serviceProductOrderResult) {
					if (0 == $serviceProductOrderResult['state']) {
						$serviceProductOrderResult = $serviceProductOrder->where(array('id'=>$serviceProductOrderResult['id']))->save(array('state'=>-2));
						if($serviceProductOrderResult!==false){
							$this->success('撤销成功!');
						}else{
							$this->error('撤销失败!','',true);
						}
					}else {
						$this->error('订单状态错误!');
					}
				}else {
					$this->error('订单不存在!');
				}
			}else {
				$this->error('非法参数!');
			}
		}else{
			$this->error('非法操作!');
		}
	}
	
	/**
	 * 取消撤销产品订单
	 */
	public function recoverOrder(){
		if(IS_POST) {
			$id = intval(I('post.orderId'));
			if ($id) {
				$serviceProductOrder = D('ServiceProductOrder');
				$serviceProductOrderResult = $serviceProductOrder->field(true)->where(array('id'=>$id,'user_id'=>$this->mCuid))->find();
				if ($serviceProductOrderResult) {
					if (-2 == $serviceProductOrderResult['state']) {//只有撤销状态下才能重新购买
						$serviceProductOrderResult = $serviceProductOrder->where(array('id'=>$serviceProductOrderResult['id']))->save(array('state'=>0));
						if($serviceProductOrderResult!==false){
							$this->success('重新购买成功!');
						}else{
							$this->error('重新购买失败!','',true);
						}
					}else {
						$this->error('订单状态错误!');
					}
				}else {
					$this->error('订单不存在!');
				}
			}else {
				$this->error('非法参数!');
			}
		}else{
			$this->error('非法操作!');
		}
	}

}