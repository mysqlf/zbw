<?php
namespace Service\Model;
#use Think\Model;
/**
 * 支付订单
 */
class PayOrderModel extends ServiceAdminModel{

	public function payOrderList($where, $admin){
		$page = I('get.p', '1');
		$count = $this->alias('po')
					->join('zbw_company_info ci ON ci.user_id=po.user_id')
					->join('zbw_user_service_provider usp ON usp.user_id = po.user_id')
					->where($where)->count();
		
		$result = $this->alias('po')->field('po.id,po.order_no,po.pay_type,po.type,po.state,po.amount,po.actual_amount,po.create_time,po.pay_time,ci.company_name,ci.id company_id,usp.admin_id')
					->join('zbw_company_info ci ON ci.user_id=po.user_id')
					->join('zbw_user_service_provider usp ON usp.user_id = po.user_id')
				->where($where)->order('create_time desc')->page($page, 20)->select();//pay_time desc,

	//	dump($result);die();
        $pageshow = showpage($count, 20);
        return array('page'=>$pageshow,'result'=>$result);
	}

	/**
	 * 订单信息
	 */
	public function payOrderInfo($data, $admin){
        $result = $this->alias('po')->field('po.*,ci.company_name')
                        ->join('left join zbw_company_info ci ON ci.user_id = po.user_id')                 
                        ->where(array('po.id'=> $data['id'], 'po.company_id'=> $admin['company_id']))->find();

        if($result['state'] != 1){
            $res = M('user_service_provider')->field('diff_amount')->where(array('user_id'=> $result['user_id'], 'company_id'=> $admin['company_id']))->find();
            if(!empty($res)){
                $result['diff_amount']  = $res['diff_amount'];        
            }            
        }
        return $result;
	}
	
	
	/**
	 * 社保公积金明细
	 */
	public function sbGjjDetail($data, $admin){
        //$page = I('get.p', '1');
        $resSb = M('service_insurance_detail')->alias('sid')->field('sid.id,sid.current_detail,sid.pay_date,sid.type,pb.person_name,pii.base_id,pii.location,pb.card_num,pb.user_id,sp.name product_name,sid.service_price,pii.template_location')
                            ->join('zbw_person_insurance_info pii ON pii.id = sid.insurance_info_id')
                            ->join('zbw_person_base pb ON pb.id = pii.base_id')
                            ->join('left join zbw_service_product sp ON sp.id = pii.product_id')
                            ->join('left join zbw_pay_order po ON po.id = sid.pay_order_id ')                         
                            ->where("sid.pay_order_id ={$data['id']} AND pii.payment_type=1 AND po.company_id={$admin['company_id']} AND sid.state NOT IN(0,-1)")->order('sid.id asc')->select();
 //dump($resSb);
        $resGjj = M('service_insurance_detail')->alias('sid')->field('sid.id,sid.current_detail,sid.pay_date,sid.type,pb.person_name,pii.base_id,pii.location,pb.card_num,pb.user_id,sp.name product_name,pii.template_location,sid.service_price')
                            ->join('zbw_person_insurance_info pii ON pii.id = sid.insurance_info_id')
                            ->join('zbw_person_base pb ON pb.id = pii.base_id')
                            ->join('left join zbw_service_product sp ON sp.id = pii.product_id')
                            ->join('left join zbw_pay_order po ON po.id = sid.pay_order_id ')
                            ->where("sid.pay_order_id ={$data['id']} AND pii.payment_type=2 AND po.company_id={$admin['company_id']} AND sid.state NOT IN(0,-1)")->order('sid.id asc')->select(); 
                          //  dump($resGjj);
        foreach ($resSb as $key => $value) {//echo $key,'/';
                foreach ($resGjj as $k => $val) {
                    if($value['base_id'] == $val['base_id'] && $value['pay_date'] == $val['pay_date'] && $value['card_num'] == $val['card_num']) {
                       	if(!empty($val['current_detail'])){//echo $k,'<br/>';
	                        $resSb[$key]['gjj'] = $val['current_detail'];
                            $resSb[$key]['service_price'] = $val['service_price'] + $value['service_price'];
	                        unset($resGjj[$k]);
	                        break;
                  	 	}
                    }
         		}
        } 
                            //dump($resGjj);
                           
        if(count($resGjj) > 0){
            foreach ($resGjj as $key => $value) {
                $resGjj[$key]['gjj'] = $val['current_detail'];
                unset($resGjj[$key]['current_detail']);
            }
        	$resSb = array_merge($resSb, $resGjj);
        }
   //dump($resGjj);//
        foreach ($resSb as $key => $value) {
            if(!empty($value['current_detail'])){
                $sb = json_decode($value['current_detail'], true);
                $resSb[$key]['sb_per'] = $sb['person'];
                $resSb[$key]['sb_com'] = $sb['company'];
                $resSb[$key]['sb_type'] = $value['type'];
                foreach ($sb['items'] as $k => $val) {
                    if($val['name'] == '残障金'){
                        $resSb[$key]['disable'] = $val['total'];
                        continue;
                    }
                }                
                unset($resSb[$key]['current_detail']);
            }    
            if(!empty($value['gjj'])){
                $gjj = json_decode($value['gjj'], true);
                $resSb[$key]['gjj_per'] = $gjj['person'];
                $resSb[$key]['gjj_com'] = $gjj['company'];
                $resSb[$key]['gjj_type'] = $value['type'];
                  foreach ($gjj['items'] as $k => $val) {
                    if($val['name'] == '残障金'){
                        $resSb[$key]['disable'] = $val['total'];
                        continue;
                    }
                }                     
                unset($resSb[$key]['gjj']);
            } 

        }        
     //   dump($resSb);die();
		return $resSb;
	}


	/**
	 * 工资
	 */
	public function salaryDetail($data){
      return  M('service_order_salary')->alias('sos')->field('sos.*,pb.person_name,pb.card_num,pb.bank,pb.account,sp.name product_name')
                      ->join('zbw_person_base pb ON pb.id = sos.base_id')
                      ->join('left join zbw_service_product sp ON sp.id = sos.product_id')
                      ->where("sos.pay_order_id ={$data['id']}")->select();
	}

	/**
	 * 套餐
	 */
	public function productDetail($data, $admin){
       $result = M('service_product_order')->alias('spo')->field('spo.id,pay_order_id, price,modify_price,sp.name product_name,sp.member_price,spo.overtime,po.*,sp.product_detail')
                    ->join('left join zbw_service_product sp ON sp.id = spo.product_id')
                    ->join('left join zbw_pay_order po ON po.id = spo.pay_order_id')
                   // ->join('zbw_company_info ci ON ci.id = po.company_id')  
                    ->where("spo.pay_order_id ={$data['id']}  AND po.company_id = {$admin['company_id']}")->find();	
      
        if($result['state'] == 0){
            $res = M('user_service_provider')->field('diff_amount')->where(array('user_id'=> $result['user_id'], 'company_id'=> $admin['company_id']))->find()
            ;
            if(!empty($res)){
                $result['diff_amount']  = $res['diff_amount'];        
            }
        }                    
        return $result;
	}

	/**
	 * savePayOrder function
	 * 保存支付订单
	 * @access public
	 * @param array $data 数据
	 * @return boolean
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function savePayOrder($data){
	 	if (is_array($data) && !empty($data['user_id']) && !empty($data['company_id']) && !empty($data['type']) && !empty($data['location']) && !empty($data['handle_month'])) {
	 		$condition = $data;
	 		$condition['state'] = 0;
	 		unset($condition['amount']);
	 		//unset($condition['pay_deadline']);
	 		$payOrderResult = $this->field(true)->where($condition)->order('create_time desc')->find();
	 		if ($payOrderResult) {
	 			$result = $payOrderResult['id'];
	 			if ($data['amount']) {
	 				//$payOrderSaveResult = $this->where(array('id'=>$payOrderResult['id']))->save(array('amount'=>array('exp','amount + '.$data['amount'])));
	 				$payOrderSaveResult = $this->where(array('id'=>$payOrderResult['id']))->setInc('amount',$data['amount']);
	 				if (false === $payOrderSaveResult) {
	 					$this->error = '更新支付订单数据失败!';
	 					$result = false;
	 				}
	 			}
	 		}else {
	 			$data['order_no'] =create_order_sn();
	 			//$data['amount'] = 0;
	 			$data['diff_amount'] = 0;
	 			$data['actual_amount'] = 0;
	 			$data['state'] = 0;
	 			//$data['pay_deadline'] = 0;
	 			$data['create_time'] = date('Y-m-d H:i:s');
	 			$result = $this->add($data);
	 			if (false === $result) {
	 				wlog($this->getDbError());
	 				$this->error = '系统内部错误!';
	 			}
	 		}
	 		return $result;
	 	}else {
	 		$this->error = '非法参数！';
	 		return false;
	 	}
	}

    /**
     * 确认付款  只有线下付款才有
     * @param  [type] $members [description]
     * @param  [type] $admin   [description]
     * @return [type]          [description]
     */
    public function comPayment($members,$admin, $serviceInfo)
    {       
        if(is_array($serviceInfo['group'], array(3,4))) return ajaxJson(-1,'权限错误！');

        $result = $this->where("id = {$members['id']}")->find();
        if(empty($result))  return ajaxJson(-1,'订单不存在');
        // if($result['type'] == 1){//服务订单
        //      $order = M('service_product_order');
        //     if($result['state'] == 2 ) return ajaxJson(-1,'已确认支付');
        //     $state = $order->where("pay_order_id = {$members['id']}")->save(array('state'=>2));
        // }

        $data = array('transaction_no'=> $members['transaction_no'], 'pay_type'=>2, 'state'=> 1, 'pay_time'=>date('Y-m-d H:i:s',time()));        
        if($result['actual_amount'] == 0.00){
            $data['actual_amount'] = $members['actual_amount'];
        }
       
        $state = $this->where(array('id'=> $members['id'], 'company_id'=> $admin['company_id']))->save($data);

        if($state)
        {
            $this->adminLog($admin['user_id'],'确认产品订单：'.$result['order_no'].'已付款，成功');
            return ajaxJson(0,'确认付款成功');
        }
        else
        {
            $this->adminLog($admin['user_id'],'确认产品订单：'.$result['order_no'].'已付款，失败');
            return ajaxJson(-1,'确认付款失败');
        }
    }
	
}