<?php
namespace Company\Model;
use Common\Model\InvoiceModel;

class InvoiceCompanyModel extends InvoiceModel{
//	protected $_auto = array();
	protected  $trueTableName = 'zbw_invoice';

	/**
	 * 填写增票资质信息
	 */
	public function invoiceAdd($data, $admin){
		$invoice['user_id'] = $invoice_order['user_id'] = $admin['id'];
		$invoice['type'] = $data['type'];
		$invoice['taxpayer'] = $data['taxpayer'];
		$invoice['invoice_title'] = $data['invoice_title'];
		$invoice['picture'] = $data['picture'];
		$invoice['address'] = $data['invoice_address'];
		$invoice['phone'] = $data['phone'];
		$invoice['create_time'] = $invoice_order['create_time'] = date('Y-m-d H:i:s', NOW_TIME);

		$invoice_order['address_code'] = $data['address_code'];
		$invoice_order['address'] = $data['invoice_order_address'];
		$invoice_order['contact_name'] = $data['contact_name'];
		$invoice_order['contact_phone'] = $data['contact_phone'];

		$this->token(false)->create($invoice);
		$result = $this->add();
		$invoice_order['invoice_id'] = $result;

		$_invoice_order = M('invoice_address');
		$_invoice_order->token(false)->create($invoice_order);
		$result = $_invoice_order->add();

		if($data['bank'] && $data['account']){
			$bank = M('company_bank');
			$info = $bank->where(array('compay_id'=> $admin['compay_id']))->find();
			if(empty($info)){
				$bank_data['bank']    = $data['bank'];
				$bank_data['account'] = $data['account'];
				$bank_data['compay_id'] = $admin['compay_id'];
				$result = M('company_bank')->add($bank_data);				
			}
		}

		mkFilePath($admin['id'], '', 'invoice');
		foreach ($invoice['picture'] as $key => $value) {
			move('.'.$value, str_replace('/temp', '/invoice', '.'.$value));
		}

		if($result){
			return true;
		}else{
			$this->error =  '添加失败！';
		}
	}


	/**
	 * 填写增票资质信息时默认信息
	 */
	public function AddDefaultInfo($admin){
		return M('company_info')->field('company_name,company_location,company_address,tel_city_code,tel_local_number,contact_name,contact_phone')->where(array('id'=> $admin['company_id']))->find();
	}	

	
	/**
	 * 修改资质信息
	 */
	public function invoiceEdit($data, $admin){
		$bank_data['bank']    = $data['bank'];
		$bank_data['account'] = $data['account'];

		$invoice['taxpayer'] = $data['taxpayer'];
		$invoice['invoice_title'] = $data['invoice_title'];
		$invoice['picture'] = $data['picture'];
		$invoice['address'] = $data['invoice_address'];
		$invoice['phone'] = $data['phone'];
	
		$result = $this->where(array('id'=> $data['id'], 'user_id'=> $data['userid']))->save($invoice);
		if(!$result){
			$this->error = '修改失败！';
			return false;
		}
		$bank = M('company_bank');
		$result = $bank->where(array('id'=> $data['bank_id'], 'compay_id'=> $data['compay_id']))->save($bank);
		if(!$result){
			$this->error = '修改失败！';
			return false;
		}

		mkFilePath($admin['id'], '', 'invoice');
		foreach ($invoice['picture'] as $key => $value) {
			move('.'.$value, str_replace('/temp', '/invoice', '.'.$value));
		}		
		return true;
	}


	/**
	 * 修改邮寄地址
	 */
	public function editInvoiceAddress($data){
		$address['address_code'] = $data['address_code'];
		$address['address'] = $data['address'];
		$address['contact_name'] = $data['contact_name'];
		$address['contact_phone'] = $data['contact_phone'];
		$result = M('invoice_address')->where(array('id'=> $data['id'], 'invoice_id'=> $data['invoice_id']))->save($address);
		if($result){
			return true;
		}else{
			$this->error = '邮寄地址修改失败';
			return false;
		}
	}

	/**
	 * 邮寄地址详细 
	 */
	public function invoiceAddressDetail($where){
		$result = M('invoice_address')->alias('a')->field('a.*')
					->join('zbw_invoice i ON i.id = a.invoice_id')
					->where($where)->find();
		if($result){
			return $result;
		}else{
			$this->error = '邮寄信息不存在！' ;
			return false;
		}
	}
}