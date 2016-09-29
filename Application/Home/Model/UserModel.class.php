<?php
	namespace Home\Model;
	use Think\Model\RelationModel;
	class UserModel extends RelationModel
	{
		protected $tablePrefix = 'zbw_';
		
		protected $_link = array(
			'CompanyInfo'=>array(
				'mapping_type'		=> self::HAS_ONE,
				'class_name'		=> 'CompanyInfo',
				//'mapping_name'	=> 'CompanyInfo',
				'foreign_key'		=> 'user_id'
			)
		);
		
		/**
		 * [firmRegister 用户注册第一步]
		 * @Author   JieJie
		 * @DataTime 2016-07-15T15:30:41+0800
		 * @return   [type]                   [description]
		 */
		public function registerHandle()
		{
			$data = $this->create();
			$r_password = I('r_password','');
			$pattern = '/^[(\w)|(\d)|(\-)|(_)]{6,20}$/';
			if(!preg_match($pattern, $data['username']))
			{
				$this->error = array('status'=>2,'msg'=>'用户名格式错误！');
				return false;
			}
			$pword_len = strlen($data['password']);
			if($pword_len<6 || $pword_len>20)
			{
				$this->error = array('status'=>3,'msg'=>'密码格式错误！');
				return false;
			}
			if($r_password != $data['password'])
			{
				$this->error = array('status'=>4,'msg'=>'两次密码不一致！');
				return false;
			}
			$data['create_time'] = date('Y-m-d H:i:s');
			$data['password'] = md5($data['username'].':'.$data['password']);
			session('register_step1',$data);
			return true;
		}

		/**
		 * [registerStep2 用户注册第二步添加企业信息]
		 * @Author   JieJie
		 * @DataTime 2016-07-15T17:21:56+0800
		 * @return   [type]                   [description]
		 */
		public function registerStep2()
		{
			$company_info['company_name'] = I('post.company_name');
			//$company_info['full_name'] = I('post.full_name');
			$company_info['email'] = I('post.email');
			$company_info['contact_name'] = I('post.contact_name');
			$company_info['contact_phone'] = I('post.contact_phone');
			if(!isTel($company_info['contact_phone']))
			{
				$this->error = array('status'=>1,'msg'=>'手机号格式不正确！');
				return false;
			}
			if(!emailFormat($company_info['email']))
			{
				$this->error = array('status'=>2,'msg'=>'联系人邮箱格式不正确！');
				return false;
			}
			$company_name_len = mb_strlen($company_info['company_name'],'utf-8');
			if($company_name_len<4 || $company_name_len>10)
			{
				$this->error = array('status'=>3,'msg'=>'企业简称格式不正确！长度为4~10');
				return false;
			}
			//$full_name_len = mb_strlen($company_info['full_name'],'utf-8');
			//if($full_name_len<4 || $full_name_len>40)
			//{
			//	$this->error = array('status'=>3,'msg'=>'企业全称格式不正确！长度为4~40');
			//	return false;
			//}
			$contact_name_len = mb_strlen($company_info['contact_name'],'utf-8');
			if($contact_name_len<2 || $contact_name_len>20)
			{
				$this->error = array('status'=>4,'msg'=>'联系人名称格式不正确！长度为2~20');
			}
			$this->startTrans();
			$user_info = session('register_step1');
			$company_user_id = $this->add($user_info);
			$company_info['user_id'] = $company_user_id;
			$company_info_id = M('company_info','zbw_')->add($company_info);
			if($company_info_id and $company_user_id)
			{
				$this->commit();
				$session_info['user_id'] = $company_user_id;
				$session_info['company_id'] = $company_info_id;
				$session_info['username'] = $user_info['username'];
				$session_info['company_name'] = $company_info['company_name'];
				session('company_user',$session_info);
				session('register_step1',null);
				//同步数据到redis
				//$companyUser = $this->getCompanyUserByCuid($company_user_id);
				$companyUser = D('User')->relation(true)->field(true)->getById($company_user_id);
				unset($companyUser['password']);
				$redis = initRedis();
				$redisKey = 'zby:com:user:'.$company_user_id;
				//$redisres = $redis->hGetAll($redisKey);
				$redisres = $redis->hSet($redisKey,'mCuid',$company_user_id);
				$redisres = $redis->hSet($redisKey,'mCid',$company_info_id);
				$redisres = $redis->hSet($redisKey,'mCompanyUser',json_encode($companyUser));
				return true;
			}
			session('register_step1',null);
			$this->error = array('status'=>5,'msg'=>'数据错误！');
			$this->rollback();
			return false;
		}

		/**
		 * [repeatUserName 检测用户名是否重复]
		 * @Author   JieJie
		 * @DataTime 2016-07-15T18:34:30+0800
		 * @return   [int]                   
		 */
		public function repeatUserName()
		{
			$user_name = I('post.username','');
			$result =  $this->where(array('username'=>$user_name))->getField('id');
			if(!$result) return true;
			return false;
		}

		/**
		 * [loginHandle 用户登录]
		 * @Author   JieJie
		 * @DataTime 2016-07-15T18:56:25+0800
		 * @return   [boolean] 
		 */
		public function loginHandle()
		{
			$map['u.username'] = I('post.username');
			$map['u.password'] = md5($map['u.username'].':'.I('post.password'));
			$map['u.type'] = 1;
			$user_info = $this->alias('u')->join('zbw_company_info as c ON u.id=c.user_id')->where($map)->field('u.id as user_id,c.id as company_id,u.username,c.company_name')->find();
			if($user_info) session('company_user',$user_info);
			if(isset($_POST['auto_login']))
			{
				cookie('auto_login',$user_info);
			}
			return session('?company_user');
		}

		/**
		 * [modifyPassword 修改用户密码]
		 * @Author   JieJie
		 * @DataTime 2016-07-15T20:12:45+0800
		 * @return   [boolean]  
		 */
		public function modifyPassword()
		{
			if(!session('?modify_password'))
			{
				$this->error = array('status'=>1,'msg'=>'数据错误!');
				return false;	
			}
			$password = I('post.password');
			$r_password = I('post.r_password');
			if($password!=$r_password)
			{
				$this->error = array('status'=>2,'msg'=>'两次输入的密码不一致');
				return false;
			}
			$pword_len = strlen($password);
			if($pword_len<6 || $pword_len>20)
			{
				$this->error = array('status'=>3,'msg'=>'密码格式错误！');
				return false;
			}
			$user_name = $this->where('id='.session('modify_password'))->getField('username');
			if($user_name)
			{
				$data['password'] = md5($user_name.':'.$password);

				$result = $this->where('id='.session('modify_password'))->save($data);
				if(false !== $result) return true;
			}
			$this->error = array('status'=>4,'msg'=>'修改失败');
			return false;
		}
	}
?>