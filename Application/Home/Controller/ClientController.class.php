<?php
	namespace Home\Controller;
	use Think\Controller;
	class ClientController extends Controller
	{
		#添加联系客户
		public function addClient()
		{
			$result = D('ClientAdvisory')->addClient();
			$result = is_numeric($result) ? true:false;
			$this->ajaxReturn($result);
		}
		
	}
?>