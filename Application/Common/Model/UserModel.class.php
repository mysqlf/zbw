<?php
namespace Common\Model;
use Think\Model;
class UserModel extends Model
{
	//protected $autoCheckFields = false;
	protected $_type = '';
	public function __construct ($type = 1)
	{
		$this->_type = is_array($type) ? implode(',' , $type) : $type;
	}
	/**
	 * 用户是否存在
	 * @param  [string] $username [用户名]
	 * @return [用户数据]
	 */
	public function userAssign ($username)
	{
		if (!$username) return IS_AJAX ? ajaxJson(-1,'参数错误') : false;
		$m = M('user');
		$res = $m->where("username = '{$username}' AND `type` IN ({$this->_type})")->find();
		return $res ? $res : null;
	}
	/**
	 * 退出登录
	 */
	public function loginOut ()
	{
		session('user' , null);
	}
	public function userInfo ($uid)
	{
		$m = M('user');
		return $m->where("id={$uid}")->find();
	}
}
?>