<?php

namespace Service\Model;
use Common\Model\UserModel;

/**
 * 服务商模型
 */
class AdminModel extends UserModel
{
	protected $autoCheckFields = false;
	protected $_type = '';
	public    $_uri  = '';
	public function __construct ()
	{
		$this->_type = 2;
		//$this->_uri  = MODULE_NAME.'-'.CONTROLLER_NAME.'-'.ACTION_NAME;
		$this->_uri = CONTROLLER_NAME;
	}
	public function login ($data)
	{
		$res = $this->userAssign($data['username']);
		if ($res === false) return ajaxJson(-1,'参数错误');
		if (!$res) return ajaxJson(-1,'用户不存在');
		$newPassword = md5($data['username'].':'.$data['password']);
		if ( $newPassword != $res['password']) return ajaxJson(-1,'输入的密码错误');
		if ($res['state'] == -9) return ajaxJson(-1,'用户已被删除');
		if ($res['state'] == -1 || $res['state'] == 0) return ajaxJson(-1,'用户已被停用');
		unset ($res['password']);
		$result['user_id'] = $res['id'];
		$userid = ($res['father_id'] ? $res['father_id'] : $res['id']);
		$result['company_id'] = M('company_info')->where("user_id={$userid}")->getField('id');
		if (!$result['company_id']) return ajaxJson(-1,'用户不存在');	
		//账号信息
		$adminInfo = $this->adminInfo($result['company_id'], $result['user_id']);
		$result['group'] = $adminInfo['group'];
		$result['type'] = $adminInfo['type'];
		$result['auth'] = $adminInfo['auth'];
		session('user',$result);
		return ajaxJson(0,'登录成功');
	}
	/**
	 * 检测登录与权限
	 */
	public function isLogin ()
	{
		$account = session('user');
		if (!$account['user_id'] || !$account['company_id'])
		{
			redirect('Service-User-login');
		}
		else
		{
			if ($this->_uri === 'Service-User-login') redirect('Service-Service-index');
		}
	}
	public function getUserAuth ($cid = null , $uid= null)
	{
		$this->isLogin();
		$account = session('user');
		return M('service_admin')->where("company_id={$account['company_id']} AND user_id={$account['user_id']}")->find();
	}
	/**
	 * 检测权限
	 */
	public function userAuth ()
	{
		$admin = $this->getUserAuth();
		if (!$admin) redirect('Service-Service-index' , 3 , '您无权查看该页面');
		$state_user_id = S('state_user_id');
		if(!empty($state_user_id)){
			$state_user_id = array_filter(explode(',', $state_user_id));			
			if(in_array($admin['user_id'], $state_user_id)){
				$this->loginOut();
				redirect('Service-User-login');
			}
		}
		if ($admin['group'] != 1 && !in_array($this->_uri , array('Service-Service-index')))
		{
			$auth = json_decode($admin['auth'] , true);
			if (!in_array($this->_uri,$auth)) redirect('Service-Service-index' ,  3 , '您无权查看该页面');
		}   
	}
	//账号信息
	public function loginInfo()
	{
		$d = D('Admin');
		$d->isLogin();
		return session('user');
	}
	//账号信息
	public function adminInfo ($cid , $uid)
	{
		$m = M('service_admin');
		return $m->where("company_id={$cid} AND user_id={$uid}")->find();
	}
	//修改管理员数据
	public function updAdmin ($cid , $uid)
	{
		$m = M('service_admin');
		$data = $m->create();
		return $m->where("company_id={$cid} AND user_id={$uid}")->save($data);
	}
}
?>