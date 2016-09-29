<?php
	namespace Home\Model;
	use Think\Model;
	class ProductOrderModel extends Model{
		protected $tablePrefix = 'zbw_';
		protected $tableName = 'service_product_order';
		//protected $trueTableName = 'zbw_service_product_order';
		
		public function getRecord(){
			$map['p.service_state'] = array('in','1,2,3');//已签约 服务中 服务完成

			$model = D('ProductOrder');
			return $model->alias('p')->join('zbw_'.'company_info as c ON p.company_id=c.company_id')
			->where($map)->field('c.company_name,p.create_time')->order('p.create_time DESC')->limit(50)->select();
		}

		#用户购买产品处理
		public function buyHandle(){
			$companyUser = session('company_user');
			$product_id = I('param.id/d',0);//产品id
			$product_info = D('ServiceProduct')->productInfo($product_id);//产品信息
			if(empty($product_info)) return '数据错误';
			$product_info['servicePrice'] = reset(json_decode($product_info['service_price'],true));
			$product_info['validity'] = $product_info['servicePrice']['validity'];
			if($product_info['service_type']==2 || $product_info['service_type']==4) return '暂时不能购买个人产品';
			$result = $this->checkBuy($product_id,$product_info['company_id']);
			if(!is_bool($result)) return ['url'=>U('Company/Account/companyInfo'),'info'=>$result];
			$data = array(
				//'order_no' => date('Ymd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(10, 99)),//订单号
				//'company_id' => $companyUser['company_id'],//购买企业信息id
				'user_id' => $companyUser['user_id'],//购买企业用户id
				'product_id' => $product_id,//产品订单id
				'price'	=> $product_info['member_price'] ,//会员费
				'create_time' => date('Y-m-d H:i:s',time()),//入库时间
				//'service_com_id' => $product_info['company_id'],//服务商id
				'overtime' => date('Y-m-d',strtotime("+{$product_info['validity']} month",time())),
				'inc_handle_days' => C('INSURANCE_HANDLE_DAYS')
			);
			$data['price'] == 0 && $data['state'] = 1;
			$this->startTrans();
			$productOrderSaveResult = $this->add($data);
			if ($productOrderSaveResult) {
				$payOrder = D('PayOrder');
				$payOrderData = array();
				$payOrderData['order_no'] = create_order_sn();
				$payOrderData['user_id'] = $companyUser['user_id'];
				$payOrderData['company_id'] = $product_info['company_id'];
				$payOrderData['location'] = 0;
				$payOrderData['handle_month'] = date('Ym');
				$payOrderData['type'] = 1;//服务产品订单
				$payOrderData['amount'] = $data['price'];
				$payOrderData['create_time'] = date('Y-m-d H:i:s');
				if ($payOrderData['amount'] == 0){
					$payOrderData['state'] = 1;
					$payOrderData['transaction_no'] = $payOrderData['order_no'];
					$payOrderData['pay_type'] = 1;
					$payOrderData['pay_time'] = $payOrderData['create_time'];
				};
				$payOrderSaveResult = $payOrder->add($payOrderData);
				
				/*$serviceProductLocation = M('ServiceProductLocation','zbw_');
				$serviceProductLocationResult = $serviceProductLocation->field('location')->where(['service_product_id'=>$product_id])->select();
				if ($serviceProductLocationResult) {
					$serviceProductLocationArray = array();
					foreach ($serviceProductLocationResult as $key => $value) {
						$serviceProductLocationArray[$value['location']] = $value['location'];
					}
					if ($serviceProductLocationArray) {
						$warrantyLocationData = array();
						$nowTime = date('Y-m-d H:i:s');
						foreach ($serviceProductLocationArray as $key => $value) {
							$warrantyLocationData[$value]['service_product_order_id'] = $productOrderSaveResult;
							$warrantyLocationData[$value]['location'] = $value;
							$warrantyLocationData[$value]['soc_service_price'] = $product_info['service_price'];
							$warrantyLocationData[$value]['pro_service_price'] = $product_info['service_price'];
							$warrantyLocationData[$value]['af_service_price'] = 0;
							$warrantyLocationData[$value]['state'] = 0;
							$warrantyLocationData[$value]['create_date'] = $nowTime;
							$warrantyLocationData[$value]['update_date'] = $nowTime;
						}
					}
				}*/
				
				if ($payOrderSaveResult) {
					$result = $this->where(array('id'=>$productOrderSaveResult))->save(array('pay_order_id'=>$payOrderSaveResult));
					if (false !== $result) {
						$result = $productOrderSaveResult;
						$this->commit();
					}else {
						$result = false;
						$this->rollback();
					}
				}else {
					$result = false;
					$this->rollback();
				}
			}else {
				$result = false;
				$this->rollback();
			}
			return $result ? $result : '操作失败，请重试或联系客服';
		}

		/**
		 * 检测企业是否已经购买过当前服务
		 * @Author   JieJie
		 * @DataTime 2016-03-24T15:18:45+0800
		 * @param    int     $product_id [产品id]
		 * @return   int
		 */
		private function checkBuy($product_id,$service_com_id){
			$companyUser = session('company_user');
			if($service_com_id == $companyUser['company_id']) return '不能购买自己发布的产品';
			$state = D('CompanyInfo')->where('id='.$companyUser['company_id'])->getField('audit');
			if($state!=1) return '你的资料暂未通过审核，请先通过审核后购买服务';
			$map = array(
				//'company_id' => $companyUser['company_id'],
				'user_id' => $companyUser['user_id'],
				'product_id' => $product_id,
				'state' => ['not in','-2,-9'],
				'overtime' => ['gt',date('Y-m-d H:i:s')],
				'create_time' => ['lt',date('Y-m-d H:i:s')]
			);
			$result = $this->where($map)->count();
			if($result==='0') return true;
			return '你已购买过当前服务，请勿重复购买';
		}
	}