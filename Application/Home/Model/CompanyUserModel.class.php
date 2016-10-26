<?php
	namespace Home\Model;
	use Think\Model;
	class CompanyUserModel extends Model
	{
		protected $tablePrefix = 'zbw_';
		protected $_map = array(
			'user_name' => 'username', 
			'user_password' => 'password',
		);
		
		protected $_validate = array(
			array('verify_code','require','验证码必须！'), 
			array('username','','帐号名称已经存在！',0,'unique',1), 
			array('username','checkUname','用户名格式不正确!',0,'callback'), 
			array('contact_name','2,20','联系人姓名格式不正确!',0,'length',1), 
			array('password','6,20','密码长度只能在6-20个字符之间!',0,'length',1), 
			array('password','checkPwd','密码格式不正确',0,'callback'),
			array('confirm_password','password','确认密码不正确!',0,'confirm'), 
			array('contact_email','email','邮箱格式不正确！',0,'regex',1),
			array('contact_phone','isTel','联系人电话格式不正确!',0,'function',1),
			array('firm_name','4,40','企业名称格式不正确!',0,'length',1), 
		);
		
		#检测密码	
		protected function checkPwd($password)
		{
			$preg = '/^[\w\W]+$/';
			if(preg_match($preg,$password)) return true;
			return false;
		}

		#检测用户名
		protected function checkUname($username)
		{
			$preg = '/^[a-z0-9-_]{6,20}+$/i';
			if(preg_match($preg, $username)) return true;
			return false;
		}

		#企业注册
		public function firmRegisterHandle()
		{

			if(!$data=$this->create())
			{
				return array('state'=>false,$this->getError());
			}
			$data['create_time'] = date('Y-m-d H:i:s',time());
			$data['password'] = md5(trim(I('post.user_password','')).':'.trim(I('post.user_name','')));
			$this->startTrans();
			$return_id = $this->add($data);
			$info_data = array(
				'company_id' => $return_id,
				'company_name' => I('post.firm_name'),
				'email' => I('post.contact_email'),
				'contact_name' => I('post.contact_name'),
				'contact_phone' => I('post.contact_phone'),
			);
			$company_info_id = D('CompanyInfo')->insetInfo($info_data);
			if($return_id && $company_info_id)
			{
				$this->commit();
				//session信息
				$session_data = array(
					'company_info_id'=>$company_info_id,
					'company_id'=>$company_id,
					'user_name'=>I('post.user_name'),
					'company_name'=>I('post.firm_name'),
				);
				return $this->_saveSession($session_data);
			}

			$this->rollback();
			return false;
		}

		/**
		 * [_saveSession 存储session/cookie]
		 * @Author   JieJie
		 * @DataTime 2016-06-16T20:18:05+0800
		 * @param    [int]   $company_info_id [企业信息id]
		 * @param    [int]   $company_id      [企业id]
		 * @return   [boolean]  
		 */
		private function _saveSession($data)	
		{
			session('company_info',$data);
			//自动登录
			if(I('post.auto_login','')=='1')
			{
				$cookie = array(
					'company_id'=>$data['company_id'],
					'company_pass'=>md5(I('post.user_password').':'.I('post.user_name')),
				);
				cookie('company_info',$cookie,86400*30);
			}
			clean_temp_by_companyId($data['company_info_id']);
			return session('?company_info');
		}

		#企业登录
		public function firmLoginHandle()
		{
			//用户名密码
			$condition['u.username'] = I('post.user_name');
			$condition['u.password'] = md5(I('post.user_password').':'.$condition['u.user_name']);
			//获取用户信息
			$company_info = $this->alias('u')->join('zbw_company_info as c ON u.id=c.company_id')
			->where($condition)->field('u.id as company_id,u.username as user_name,c.id as company_info_id,c.company_name')->find();
			if($company_info)
			{
				//保存用户session信息
				return $this->_saveSession($company_info);
			}
			return false;
		}

		#修改密码
		public function modifyPassword()
		{
			if(!session('?modify_password')) return array('state'=>false,'info'=>'数据错误');
			$password = I('post.user_password');
			$rpassword = I('post.confirm_password');
			if($password != $rpassword) return array('state'=>false,'info'=>'两次密码不一致');
			if($this->checkPwd($password)) return array('state'=>false,'info'=>'密码不符合规则');
			$user_name = $this->where('id='.session('modify_password'))->getField('username');
			$user_name && $new_password = md5($password.':'.$username);
			if($new_password)
			{
				$save_data['password'] = $new_password;
				$service_num = M('service_admin','zbw_')->where('company_id='.session('modify_password'))->count();
				if($service_num)
				{
					$this->startTrans();
					$company_user = $this->where('id='.session('modify_password'))->save($save_data);
					$service_user = M('service_admin','zbw_')->where('company_id='.session('modify_password'))->save($save_data);
					if($company_user && $service_user)
					{
						$this->commit();
						return true;
					}else{
						$this->rollback();
						return false;
					}
				}
				
				$company_user = $this->where('id='.session('modify_password'))->save($save_data);
				return $company_user;
			}

		}

		#检测自动登录
		public function checkAutoLogin()
		{
			$company_info = cookie('company_info');
			if($company_info)
			{
				$condition['u.id'] = $company_info['id'];
				$condition['u.password'] = $company_info['password'];
				$company_info = $this->alias('u')->join('zbw_company_info as c ON u.id=c.company_id')
								->where($condition)->field('u.id as company_id,u.username as user_name,c.id as company_info_id,c.company_name')->find();
				$company_info && $this->_saveSession($company_info);
			}
		}
		
		#检测用户名是否注册
		public function checkUserName()
		{
			$username = I('get.username','');
			return $this->where(array('username'=>$username))->count();
		}
	}
?>