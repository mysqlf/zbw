<?php
namespace Service\Controller;
use Think\Controller;

class UserController extends Controller 
{

	
	/* 登录页面 */
	public function login()
	{
		if (IS_POST)
		{
			$account['username'] = I('post.username' , '');
			$account['password'] = I('post.password' , '');
			if($account['username'] && $account['password'])
			{
				$result = D('Admin')->login($account);
				$this->ajaxReturn ($result);
			}
			else
			{
				$this->ajaxReturn (array('status'=>-100001));
			}
			exit();
		}
		else
		{
			$this->display();
		}
	}
	//退出登录
	public function loginOut()
	{
		D('Admin')->loginOut();
		redirect('Service-User-login');
	}
	//账号信息
	public function accountInfo ()
	{
		$d = D('Admin');
		$account = $d->loginInfo();
		if (IS_POST)
		{
			$d->updAdmin($account['company_id'] , $account['user_id']);
			ajaxJson(0,'操作成功');
		}
		$admin = $d->adminInfo($account['company_id'] , $account['user_id']);
		$admin['username'] = $d->userInfo($account['user_id'])['username'];
		$this->assign('result' , $admin);
		$this->display('Set/account_info');
	}
	//修改密码
	public function setPassword ()
	{
		$d = D('Admin');
		$account = $d->loginInfo();
		if (IS_POST)
		{
//			$admin      = $d->userInfo($account['user_id']);
			// $o_password = I('post.lastPassword' , '');
			// if (md5("{$admin['username']}:{$o_password}") !== $admin['password']) ajaxJson(-1,'原密码错误');
			// $d->updAdmin($account['company_id'] , $account['user_id']);
			$account['lastPassword'] = I('post.lastPassword' , '');
			$account['password'] = I('post.password' , '');
			$account['comfirmPassword'] = I('post.comfirmPassword' , '');
			if($account['comfirmPassword']  != $account['password']) ajaxJson(-1,'新密码不相等');
			D('ServiceAdmin')->setPassword($account);
			ajaxJson(0,'操作成功');
		}
	}

	/**
	 * 删除
	 */
	public function delAccount(){
		$d = D('Admin');
		$account = $d->loginInfo();
		if(IS_POST){
			$account['id'] = I('post.id' , '');
			if(empty($account['id'])) ajaxJson(-1,'参数错误');
			D('ServiceAdmin')->delAccount($account);
		}
	}
}
