<?php
	namespace Home\Controller;
	use Think\Controller;
	class MemberController extends HomeController 
	{

		public function _initialize()
		{
			parent::_initialize();
			$register = array('firmRegister','firmRegister2','firmRegister3','regSuccess');
			$find_p = array('getBackPass','getBackPass2','getBackPass4','getBackPass3');
			$login = array('firmLogin');
			if(in_array(ACTION_NAME,$register))
				$this->action_name = '企业注册';
			else if(in_array(ACTION_NAME,$find_p))
				$this->action_name = '找回密码';
			else if(in_array(ACTION_NAME,$login))
				$this->action_name = '企业登录';
		}
		/**
		 * [firmRegister 注册第一步]
		 * @Author   JieJie
		 * @DataTime 2016-07-15T16:24:34+0800
		 * @return   json                   [注册结果]
		 */
		public function firmRegister()
		{
			if(IS_AJAX)
			{
				if(!check_verify(I('post.verify_code')))
				{
					$this->ajaxReturn(array('status'=>1,'msg'=>'验证码错误！'));
				}
				$User = D('User');
				$result = $User->registerHandle();
				if($result) $this->ajaxReturn(array('status'=>0,'msg'=>'操作成功！'));
				else $this->ajaxReturn($User->getError());
			}
			$this->display();
		}
		/**
		 * [firmRegister2 注册第二步]
		 * @Author   JieJie
		 * @DataTime 2016-07-15T17:34:20+0800
		 * @return   [json]                   [注册结果]
		 */
		public function firmRegister2()
		{
			if(IS_AJAX)
			{
				$User = D('User');
				$result = $User->registerStep2();
				if($result)
				{	
					$user_info = session('company_user');
					$email = I('post.email');
					$this->sendEmail($user_info['company_name'],$user_info['company_id'],$user_info['username'],$email);
					$this->ajaxReturn(array('status'=>0,'msg'=>'操作成功！','url'=>'/Member-firmRegister3-email-'.$email));
				}
				$this->ajaxReturn($User->getError());
			}
			$this->display();
		}
		/**
		 * [firmRegister3 用户注册第三步]
		 * @Author   JieJie
		 * @DataTime 2016-07-15T18:35:13+0800
		 * @return   [type]                   [description]
		 */
		public function firmRegister3()
		{
			$verify_email = explode('@',I('get.email'));
			$usually_email = C('USUALLY_EMAIL');
			$this->email_login_url = $usually_email[$verify_email[1]];
			$this->display();
		}

		/**
		 * [repeatUserName 检测用户名是否被注册]
		 * @Author   JieJie
		 * @DataTime 2016-07-15T18:36:57+0800
		 * @return   [json]                   [结果]
		 */
		public function repeatUserName()
		{
			$result = D('User')->repeatUserName();
			if($result) $this->ajaxReturn(array('status'=>0,'msg'=>'用户名可用'));
			else $this->ajaxReturn(array('status'=>1,'msg'=>'用户名已被注册'));
		}

		#验证码
		public function verifyCode()
		{
			$config = array(
				'length' => 4,
				'reset' => false,
				'fontSize' => 30
			);
			$Verify = new \Think\Verify($config);
			$Verify->entry();
		}

		#ajax验证验证码
		public function checkCode()
		{
			if(!check_verify(I('post.verify_code'))) 
	           			$this->ajaxReturn(array('status'=>0,'msg'=>'验证通过'));
	        		else
	            		$this->ajaxReturn(array('status'=>1,'msg'=>'验证码不正确！'));
		}

		/**
		 * [firmRegisterHandle 企业注册处理]
		 * @Author   JieJie
		 * @DataTime 2016-06-17T14:43:38+0800
		 * @return   [json]   [正确/错误信息]
		 */
		/*public function firmRegisterHandle()
		{
			if(check_verify(I('post.verify_code','')))
			{
				$this->ajaxReturn(array('state'=>false,'info'=>'验证码错误'));
			}

			$result = D('CompanyUser')->firmRegisterHandle();
			is_array($result) && $this->ajaxReturn($result);

			//注册成功发送验证邮件
			$company_info = session('company_info'); 
			session('tmp_user_email',I('post.contact_email'));
			$this->sendEmail($company_info['company_name'],$company_info['company_id'],$company_info['user_name'],session('tmp_user_email'));
			$this->ajaxReturn(array('state'=>true,'info'=>'注册成功','url'=>'/Member/regSuccess'));
		}*/

		/**
		 * 发送注册邮件
		 * @Author   JieJie
		 * @DataTime 2016-03-11T11:52:43+0800
		 * @param    string      $firm_name  企业名
		 * @param    int     	 $firm_id    企业id
		 * @param    string      $user_name  用户名
		 * @param    string 	 $user_email 企业联系人邮箱
		 * @return   boolean
		 */
		public function sendEmail($firm_name,$firm_id,$user_name,$user_email)
		{
			//邮箱验证数组
			$verify_info['verify_code'] = md5(mt_rand(100000,999999).$user_email);
			$verify_info['email'] = $user_email;
			//存储验证规则 24小时有效
			S('verify_email_info'.$firm_id,$verify_info,86400);
			//验证链接
			$this->verify_url = 'http://'.$_SERVER['SERVER_NAME'].U('Member/verifyEmail').'?'.urlencode("code={$verify_info['verify_code']}&firm_id={$firm_id}");
			//分配变量
			$this->firm_name = $firm_name;
			//获取邮件内容
			$email_content = $this->fetch('Member:sendEmail');
			//发送邮件
			return think_send_mail($user_email,$user_name,'欢迎注册智保易，请点击链接激活邮箱',$email_content);
		}

		#注册成功页面
		public function regSuccess()
		{
			$verify_email = I('get.email') ?  explode('@',I('get.email')) : explode('@',session('tmp_user_email'));
			$usually_email = C('USUALLY_EMAIL');
			$this->email_login_url = $usually_email[$verify_email[1]];
			$this->display();
		}

		#邮箱验证
		public function verifyEmail()
		{
			//获取url参数
			$param = explode('&',urldecode($_SERVER['QUERY_STRING']));
			//获取验证规则字符串
			$verify_code = explode('=',$param[0]);
			//获取用户id
			$verify_id = explode('=', $param[1]);
			//获取用户验证规则
			$verify_info = S('verify_email_info'.$verify_id[1]);
			if ($verify_info) {
				//获取企业用户信息
				$Model = D('CompanyInfo');
				$condition = array('id'=>$verify_id[1],'email'=>$verify_info['email']);
				$field = 'email_activation';
				$firm_info = $Model->getFirmInfo($condition,$field);
				//用户规则跟缓存规则一致
				if($verify_info['verify_code']==$verify_code[1])
				{
					//修改用户状态
					$result = $Model->modifyState($condition);
					if($result)
					{
						$this->verify_status = true;
					}else{
						$this->company_id = $verify_id[1];
						$this->verify_status = false;
					}
				}else{
					$this->company_id = $verify_id[1];
					$this->verify_status = false;
				}
				$this->display();
			}else {
				$this->error('数据错误!');
			}
		}

		#验证失败重新发送
		public function aginVerify()
		{
			//获取企业信息
			$firm_info = D('CompanyInfo')->getFirmInfo(array('id'=>I('get.firm_id')));
			//企业邮箱通过验证
			$firm_info['email_activation']==1 && $this->redirect('Index/index','',3, '您的邮箱已经验证成功，请勿重复验证...');
			//发送验证邮件
			$result = $this->sendEmail($firm_info['company_name'],I('get.firm_id'),$firm_info['company_name'],$firm_info['email']);
			if($result) 
				$this->ajaxReturn(array('state'=>true,'info'=>'邮件发送成功','url'=>'/Member-regSuccess-email-'.$firm_info['email']));
			else 
				$this->ajaxReturn(array('state'=>false,'info'=>'邮件发送失败'));
		}

		#企业登录
		public function firmLogin()
		{
			if(IS_AJAX)
			{
				if(!check_verify(I('post.verify_code','')))
				{
					$this->ajaxReturn(array('status'=>1,'msg'=>'验证码错误！'));
				}
				$result = D('User')->loginHandle();
				if($result){
					$companyUser = session('company_user');
					if ($companyUser) {
						clean_temp_by_companyId($companyUser['company_id']);
					}
					$this->ajaxReturn(array('status'=>0,'msg'=>'登录成功！'));
				}else{
					$this->ajaxReturn(array('status'=>2,'msg'=>'用户名或密码不正确！'));
				}
			}
			$this->display();
		}


		#企业注销登录
		public function logout()
		{
			$companyUser = session('company_user');
			if ($companyUser) {
				clean_temp_by_companyId($companyUser['company_id']);
			}
			session('company_user',null);
			cookie('auto_login',null);
			$this->redirect('Index/index');
		}

		/**
		 * [getBackPass 找回密码第一步]
		 * @Author   JieJie
		 * @DataTime 2016-07-15T19:31:39+0800
		 * @return   [json]
		 */
		public function getBackPass()
		{
			if(IS_AJAX)
			{
				if(!check_verify(I('post.verify_code')))
				{
					$this->ajaxReturn(array('status'=>1,'msg'=>'验证码错误！'));
				}
				//获取用户名
				$username = I('post.user_name');
				//获取账号关联邮箱
				$condition['a.username'] = $username;
				$result = M('user as a','zbw_')->join('zbw_company_info as b ON a.id=b.user_id')->where($condition)->field('b.email,a.id,b.company_name')->find();
				//保存用户寻找密码数据	
				session('look_of_pass',$result);
				if($result)
				{
					$url = '/Member-getBackPass2-email-'.$result['email'];
					$this->ajaxReturn(array('status'=>0,'msg'=>'操作成功','url'=>$url));
				}
				$this->ajaxReturn(array('status'=>2,'msg'=>'该用户不存在'));
			}
			$this->display();
		}
		#找回密码页面2
		public function getBackPass2()
		{
			if(IS_AJAX)
			{	
				$look_of_pass = session('look_of_pass');
				if(!$look_of_pass) $this->ajaxReturn(array('status'=>1,'msg'=>'数据错误!'));

				//生成验证代码
				$code = md5(mt_rand(100000,999999));
				//发送给用户的验证链接
				$this->reset_url = 'http://'.$_SERVER['HTTP_HOST'].U('getBackPass3').'?'.urlencode('code='.$code.'&id='.$look_of_pass['id'].'&email='.$look_of_pass['email']);
				$this->firm_name = $look_of_pass['company_name'];
				//存储验证规则
				S($look_of_pass['email'].':'.$look_of_pass['id'],$code,86400);
				//获取发送邮件内容
				$email_info = $this->fetch("Member:sendEmailPassword");
				$email_title = '您正在智保易申请找回密码';
				$send_state = think_send_mail($look_of_pass['email'],$this->firm_name,$email_title,$email_info);
				if($send_state===true)
				{
					session('look_of_pass',null);
					$this->ajaxReturn(array('status'=>0,'msg'=>'发送成功'));
				}else{
					$this->ajaxReturn(array('status'=>2,'msg'=>'发送失败'));
				}
			}
			$this->display();
		}	
		#找回密码 4
		public function getBackPass4()
		{
			$this->display();
		}

		/*#找回密码处理
		public function getPassHandle()
		{
			//验证验证码
			if(check_verify(I('post.verify_code')))
			{
				$this->ajaxReturn(array('state'=>false,'info'=>'验证码错误'));
			}
			//获取用户名
			$username = I('post.user_name');
			//获取账号关联邮箱
			$condition['a.username'] = $username;
			$result = M('company_user as a','zbw_')->join('zbw_company_info as b ON a.id=b.company_id')
					->where($condition)->field('b.email,a.id,b.company_name')->find();

			//保存用户寻找密码数据	
			session('look_of_pass',$result);
			if($result)
			{
				$url = '/Member-passwordEmail-email-'.$result['email'].'-company_id-'.$result['id'];
				$this->ajaxReturn(array('state'=>true,'info'=>'验证成功','url'=>$url));
			}
			$this->ajaxReturn(array('state'=>false,'info'=>'该用户不存在'));
		}*/

		#发送找回密码页面
	/*	public function passwordEmail()
		{
			$this->display();
		}*/

		#发送找回密码邮件
		/*public function sendEmailPassword()
		{
			//企业id
			$company_id = I('get.company_id');
			//获取企业邮箱
			$company_email = I('get.company_email');
			//企业找回密码信息
			$look_of_pass = session('look_of_pass');
			//验证企业邮箱及id
			if($company_id!=$look_of_pass['id'] || $company_email!=$look_of_pass['email'])
				$this->ajaxReturn(array('state'=>false,'info'=>'数据错误'));

			//生成验证代码
			$code = md5(mt_rand(100000,999999));
			//发送给用户的验证链接
			$this->reset_url = 'http://'.$_SERVER['HTTP_HOST'].U('resetPassword').'?'.urlencode('code='.$code.'&id='.$company_id.'&email='.$company_email);
			$this->firm_name = $look_of_pass['company_name'];
			//存储验证规则
			S($company_email.':'.$company_id,$code,86400);
			//获取发送邮件内容
			$email_info = $this->fetch();
			$email_title = '您正在智保易申请找回密码';
			$send_state = think_send_mail($company_email,$this->firm_name,$email_title,$email_info);
			if($send_state===true)
			{
				session('look_of_pass',null);
				$this->ajaxReturn(array('state'=>true,'info'=>'发送成功'));
			}else{
				$this->ajaxReturn(array('state'=>false,'info'=>'发送失败'));
			}
		}*/

		#验证找回密码链接
		public function getBackPass3()
		{
			//获取url参数
			$param = explode('&',urldecode($_SERVER['QUERY_STRING']));
			//重组参数
			foreach ($param as $value) 
			{
				$tmp_arr = explode('=', $value);
				$new_param[$tmp_arr[0]] = $tmp_arr[1];
			}
			//获取验证规则
			$rule = S($new_param['email'].':'.$new_param['id']);
			//验证码不正确
			if($rule!=$new_param['code']) 
			{
				$this->redirect('Index/index','',3,'该验证链接已失效，请重新发送。页面跳转中...');
			}
			//保存修改密码的账号id
			session('modify_password',intval($new_param['id']));
			//S($new_param['email'].':'.$new_param['id'],null);
			$this->display();
		}

		/**
		 * [modifyPassword 修改找回密码]
		 * @Author   JieJie
		 * @DataTime 2016-07-15T20:19:42+0800
		 * @return   [json]                   [description]
		 */
		public function modifyPassword()
		{
			$User = D('user');
			$result = $User->modifyPassword();
			if($result)
				$this->ajaxReturn(array('status'=>0,'msg'=>'修改成功','url'=>'/Member-getBackPass4'));
			$this->ajaxReturn($User->getError());
		}
	}
?>