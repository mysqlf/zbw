<?php 
namespace Service\Model;
use Common\Model\InvoiceModel;

class InvoiceServiceModel extends InvoiceModel{
	protected $trueTableName = 'zbw_invoice';
	/**
	 * 发票资质审核列表
	 * @param  $[data] array state 审核状态
	 * @param  $[admin] [<当前登录信息>]
	 */
	
	public function serviceInvoiceList($data, $admin){
		$page = I('p', '1', 'intval');

		$where = "i.user_id IN (select DISTINCT(p.user_id) from zbw_service_product_order p LEFT JOIN zbw_service_product s ON p.product_id = s.id WHERE s.company_id = {$admin['company_id']}) AND i.state = {$data['state']}";
		$count = $this->alias('i')->where($where)->count();
		$result = $this->alias('i')->field('i.id, a.contact_name,a.contact_phone,i.state, c.company_name,c.audit,a.address_code')
				  ->join("zbw_company_info c ON c.user_id = i.user_id")
				  ->join("zbw_invoice_address a ON a.invoice_id = i.id")
				  ->where($where)
				  ->page($page, 20)->select();

		$pageshow = showpage($count, 20);
		return array('page'=>$pageshow,'result'=>$result);
	}


	/**
	 * 发票订单详细（开票）
	 */
	
	public function invoiceOrderDetail($data, $admin){
		$invoice_order = M('invoice_order');
		if(IS_POST){
			$where = array('id'=>$data['id'], 'company_id'=> $admin['company_id']);
			$info  = $invoice_order->field('order_no,title,invoice_state')->where($where)->find();
			if(empty($info))  return ajaxJson(-1,'订单信息不存在！');
			if($data['state'] == 1){
				$result = $invoice_order->where($where)->save(array('invoice_state'=> '1', 'modify_time'=> date('Y-m-d H:i:s', NOW_TIME)));
				if($result){
					$this->adminLog($admin['user_id'], '发票订单 '.$info['order_no'].' 订单名称 '.$info['title'].' 开票成功！');
					return ajaxJson(0,'开票成功！');
				}else{
					$this->adminLog($admin['user_id'], '发票订单 '.$info['order_no'].' 订单名称 '.$info['title'].' 开票失败！');
					return ajaxJson(-1,'开票失败！');
				}
	
			}elseif($data['state'] == 2){
				$result = $invoice_order->where($where)->save(array('invoice_state'=> '2', 'express'=> $data['express'], 'express_no'=> $data['express_no'], 'modify_time'=> date('Y-m-d H:i:s', NOW_TIME)));
				if($result){
					$this->adminLog($admin['user_id'], '发票订单 '.$info['order_no'].' 订单名称 '.$info['title'].' 发票寄出成功！');
					return ajaxJson(0,'发票寄出成功！');
				}else{
					$this->adminLog($admin['user_id'], '发票订单 '.$info['order_no'].' 订单名称 '.$info['title'].' 发票寄出失败！');
					return ajaxJson(-1,'发票寄出失败！');
				}
			}
			return ajaxJson(-1,'修改失败');
		}
	}

	/**
	 * 发票资质审核
	 */
	public function invoceAudit($where, $admin){
		if(IS_POST){
			$result = $this->wehre($where)->save(array('state'=> $data['state']));
			if($result){
				$this->adminLog($admin['user_id'], '审核企业发票资质 '.$data['id'].' 企业名称 '.$data['invoice_title'].' 审核状态为'. $data['state']);
				return ajaxJson(0,'发审核成功！');
			}else{
				$this->adminLog($admin['user_id'], '审核企业发票资质 '.$data['id'].' 企业名称 '.$data['invoice_title'].' 审核失败！');
				return ajaxJson(-1,'审核失败！');
			}
		}
	}

}
