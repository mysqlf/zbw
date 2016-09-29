<?php 
namespace Common\Model;
use Think\Model;

class InvoiceModel extends Model{


	/**
	 * 订单列表
	 * array $data   type: company  service; state 审核状态; invoice_state 开票状态
	 */
	public function invoiceOrder($data, $admin){
		$page = I('p', '1', 'intval');
		$invoice_order = M('invoice_order');
		if($data['type'] == 'company'){
			$where = "o.company_id = {$admin['company_id']}";
			$count = $invoice_order->alias('o')->where($where)->count();
			$result = $invoice_order->alias('o')->field('o.*, i.invoice_title')					  
					  ->join("LEFT JOIN zbw_invoice i ON o.user_id = i.user_id")
					  ->where($where)
					  ->page($page, 20)->select();
		}elseif($data['type'] == 'service'){
			$where = "o.company_id = {$admin['company_id']} AND o.invoice_state = {$data['invoice_state']}";
			$count = $invoice_order->alias('o')->where($where)->count();
			$result = $invoice_order->alias('o')->field('o.order_no, o.title, o.invoice_state, o.amount, o.order_id, o.order_type, i.state, i.invoice_title, c.id cid')
					  ->join("LEFT JOIN zbw_invoice i ON o.user_id = i.user_id")
					  ->join("LEFT JOIN zbw_company_info c ON c.id = o.company_id")
					  ->where($where)->page($page, 20)->select();
			if($result){
				foreach ($result as $key => $value) {
					if($value['order_type'] == 1){
						$result[$key]['create_time'] = M('service_order')->getFieldById($value['order_id'], 'create_time');
					}else{
						$result[$key]['create_time'] = M('service_product_order')->getFieldById($value['order_id'], 'create_time');
					}
				}
			}
		}
		if(empty($result)) return null;
		$pageshow = showpage($count, 20);
		return array('page'=>$pageshow,'result'=>$result);
	}



	/**
	 * 修改订单详细
	 */
	public function editDetail($where){
		$invoice_order = M('invoice_order');
		$result = $invoice_order->alias('o')->field('o.id,o.order_no, o.title, o.amount, i.taxpayer, i.invoice_title,i.address,i.phone,b.bank,b.branch,b.account')
					->join('LEFT JOIN zbw_invoice i ON i.user_id = o.user_id')
					->join('LEFT JOIN zbw_invoice_address a ON a.invoice_id = i.id')
					//->join("LEFT JOIN zbw_company_info c ON c.user_id = o.user_id")
					->join("LEFT JOIN zbw_company_bank b ON b.company_id = o.id")
					->where($where)->find();//"o.id= {$data['id']} AND o.company_id = {$admin['company_id']}
		if($result){
			return $result;
		}else{
			$this->error = '信息错误';
			return true;			
		}
	}
}