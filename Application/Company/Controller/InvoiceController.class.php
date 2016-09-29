<?php
namespace Company\Controller;
use  Think\Model;

class InvoiceController extends HomeController{
	private $_Invoice;

    protected function _initialize(){
    	$this->_Invoice = D('InvoiceCompany');
    }

	/**
	 * 图片上传
	 * @param   $[picname] [Alisence BtaxRegistrationCertificate  Ccertified  DbankPermit]
	 */

	public function picUpload(){
		if(IS_POST){
			$picname = I('post.picname', '');
			$picname or $this->ajaxReturn(array('status'=> 0, 'info'=> '信息错误！'));

			$config = array(
				'savePath' => 'company/',
				'subName'  => array('mkFilePath', array($this->mCuid, '', 'temp')),//invoice
				'saveName' => $picname,
				);
			$upload = new \Think\Upload($config);
			$info = $upload->upload($_FILES);
			if($info){
				$url = str_replace('//', '/', '/Uploads/'.$info['img']['savepath'].$info['img']['savename']);
				$this->ajaxReturn(array('status'=> 1, 'info'=> '上传成功！', 'result'=> $url));
			}else{
				$this->ajaxReturn(array('status'=> 0, 'info'=> $upload->getError()));
			}
		}
	}

	/**
	 * 填写增票资质信息
	 */
	public function invoiceAdd(){		
		if(IS_POST){
			$data['type'] = I('post.type', '0', 'intval');
			$data['taxpayer'] = I('post.taxpayer', '0');
			$data['invoice_title'] = I('post.invoice_title', '');
			$data['picture'] = I('post.picture', '');
			$data['invoice_address'] = I('post.invoice_address', '');
			$data['phone'] = I('post.phone', '');

			$data['address_code'] = I('post.address_code', '');
			$data['invoice_order_address'] = I('post.invoice_order_address', '');
			$data['contact_name'] = I('post.contact_name', '');
			$data['contact_phone'] = I('post.contact_phone', '');

			$data['bank'] = I('post.bank', '0', 'intval');
			$data['account'] = I('post.account', '');
		
			$result = $this->_Invoice->invoiceAdd($data, $companyInfo);
			if($result){
				$this->ajaxReturn(array('status'=>1, 'info'=> '添加成功！'));
			}else{
				$this->ajaxReturn(array('status'=>0, 'info'=> $this->_Invoice->getError()));
			}
		}else{
			$result = $this->_Invoice->AddDefaultInfo($companyInfo);
			$this->assign('result', $result)->display();
		}
	}	

	/**
	 * 修改增票资质信息
	 */
	public function invoiceEdit(){
		if(IS_AJAX){

			$data['taxpayer'] = I('post.type', '0');
			$data['invoice_title'] = I('post.invoice_title', '');
			$data['picture'] = I('post.picture', '', 'json_decode');
			$data['invoice_address'] = I('post.invoice_address', '');
			$data['phone'] = I('post.phone', '');

			$data['bank'] = I('post.bank', '0', 'intval');
			$data['account'] = I('post.account', '');

			$result = $this->_Invoice->invoiceEdit($data, $companyInfo);
			if($result){
				$this->ajaxReturn(array('status'=>1, 'info'=> '修改成功！'));
			}else{
				$this->ajaxReturn(array('status'=>0, 'info'=> $this->_Invoice->getError()));
			}			
		}else{
			$id = I('get.id', '0', 'intval');
			$id  or $this->error('id错误！');
			$data['o.id'] = $id;
			$data['o.user_id'] = $this->Cmuid;			
			$result = $this->_Invoice->editDetail($data);
			if($result){
				$this->assign('resutl', $result)->diasplay();
			}else{
				$this->error($this->_Invoice->getError());
			}
		}
	}

	/**
	 * 修改邮寄地址
	 */
	public function editInvoiceAddress(){
		if(IS_POST){
			$address['address_code'] = I('post.address_code', '');
			$address['address'] = I('post.address', '');
			$address['contact_name'] = I('post.contact_name', '');
			$address['contact_phone'] = I('post.contact_phone', '');
			$address['id'] = I('post.id', '', 'intval');
			$address['invoice_id'] = I('post.invoice_id', '', 'intval');
			$result = $this->_Invoice->editInvoiceAddress($address);
			if($result){
				$this->ajaxReturn(array('status'=> '1', 'info'=> '修改邮寄地址成功！'));
			}else{
				$this->ajaxReturn(array('status'=>0, 'info'=> $this->_Invoice->getError()));
			}
		}else{
			$this->error('错误！');
		}
	}

	/**
	 * 邮寄地址详细
	 */
	
	public function invoiceAddressDetail(){
			$id = I('get.id', '0', 'intval');
			$id or  $this->ajaxReturn(array('status'=>0, 'info'=> '信息错误！'));
			$data['id'] = $id;
			$result = $this->_Invoice->invoiceAddressDetail($data);
			if($result){
				$this->ajaxReturn(array('status'=> '1', 'info'=> '', 'result'=> $result));
			}else{
				$this->ajaxReturn(array('status'=>0, 'info'=> $this->_Invoice->getError()));
			}			
	}

	/**
	 * 订单列表
	 */

	public function invoiceOrder(){
		$companyInfo['company_id'] = 52;
		//$data['type'] = 'company';
		$data['type'] = 'service';
		$data['invoice_state'] = '0';
		$list = $this->_Invoice->invoiceOrder($data , $companyInfo);
		dump($list);
	}


	public function  picUpCurl(){
		//$url = 'http://zbw'.U('Company/Invoice/picUpload');
		//$data = array('img'=>'@F:/item/zbw/Public/Home/images/20150914134300_93937.jpg', 'picname'=> 'DbankPermit');//'@Public\Home\images\20150914134300_93937.jpg';

		// $url = 'http://zbw'.U('Company/Invoice/invoiceAdd');
		// $data['type'] = 0;
		// $data['taxpayer'] = '201602540124522451';
		// $data['invoice_title'] = '广东集团有限公司';
		// $data['picture'] = json_encode(array('/upload/company/0/124/1457x.jpg','/upload/company/0/124/1457x2.jpg'));
		// $data['invoice_address'] = '广东B座1904';
		// $data['phone'] = '0755-2564124';

		// $data['address_code'] = '11000000' ;
		// $data['invoice_order_address'] = 'B座1904';
		// $data['contact_name'] = '陈xx';
		// $data['contact_phone'] = '15889750537';

		$url = 'http://zbw'.U('Company/Invoice/editInvoiceAddress');
		$data['address_code'] = '12000000';
		$data['address'] = '大发铺';
		$data['contact_name'] = '五';
		$data['contact_phone'] = '0755-981427';
		$data['id'] = '3';
		$data['invoice_id'] = '3';

		$ch = curl_init();
	    curl_setopt ( $ch, CURLOPT_URL, $url );
	    curl_setopt ( $ch, CURLOPT_POST, 1 );
	    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	    //curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
	    //curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		//var_dump(curl_getinfo($ch));
		$res = curl_exec($ch);		
		//dump(curl_error($ch));
		curl_close($ch);
		dump($res);

	}
}