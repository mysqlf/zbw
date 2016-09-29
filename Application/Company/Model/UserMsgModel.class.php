<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: rohochan <rohochan@gmail.com> <http://blog.rohochan.com>
// +----------------------------------------------------------------------
namespace Company\Model;
use Think\Model\RelationModel;

/**
 * 用户通知消息模型
 */
class UserMsgModel extends RelationModel{
	protected $tablePrefix = 'zbw_';
	
	/**
	 * getMsgByUserId function
	 * 根据用户ID获取通知信息
	 * @access public
	 * @param int $userId 用户ID
	 * @param int $pageSize 分页大小
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getMsgByUserId($userId = 0,$pageSize = 10){
		if (isset($userId) && $userId > 0) {
			$pageCount = $this->where(array('user_id'=>$userId))->count('id');
			$page = get_page($pageCount,$pageSize);
			$result = $this->field(true)->where(array('user_id'=>$userId))->limit($page->firstRow,$page->listRows)->order('create_time desc')->select();
			if ($result || null === $result) {
				return array('data'=>$result,'page'=>$page->show());
			}else if(false === $result){
				wlog($this->getDbError());
				$this->error = '系统内部错误！';
				return false;
			}else {
				$this->error = '未知错误！';
				return false;
			}
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}
	
	/**
	 * getUnreadMsgCountByUserId function
	 * 根据用户ID获取未读通知信息数量
	 * @access public
	 * @param int $userId 用户ID
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getUnreadMsgCountByUserId($userId = 0){
		if (isset($userId) && $userId > 0) {
			$result = $this->where(array('user_id'=>$userId,'state'=>0))->count('id');
			if (false !== $result) {
				return $result;
			}else if (null === $result) {
				$this->error = '没有数据！';
				return false;
			}else if(false === $result){
				wlog($this->getDbError());
				$this->error = '系统内部错误！';
				return false;
			}else {
				$this->error = '未知错误！';
				return false;
			}
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}
	
	/**
	 * getMsgById function
	 * 根据ID获取通知信息
	 * @access public
	 * @param int $userId 用户ID
	 * @param int $id ID
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getMsgById($userId = 0,$id = 0){
		if (isset($userId) && $userId > 0 && isset($id) && $id > 0) {
			$result = $this->field(true)->where(array('id'=>$id,'user_id'=>$userId,'state'=>array('neq',-9)))->find();
			if (null === $result) {
				$this->error = '通知不存在或已删除！';
				return false;
			}else if (false !== $result) {
				if (0 == $result['state']) {
					//未读标记为已读
					$updateResult = $this->setMsgToReadedById($result['id']);
					if (!$updateResult) {
						return false;
					}
				}
				return $result;
			}else if(false === $result){
				wlog($this->getDbError());
				$this->error = '系统内部错误！';
				return false;
			}else {
				$this->error = '未知错误！';
				return false;
			}
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}
	
	/**
	 * setMsgToReadedById function
	 * 根据ID标记通知信息为已读
	 * @access public
	 * @param int $id ID
	 * @return boolean
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function setMsgToReadedById($id = 0){
		if (isset($id) && $id > 0) {
			$result = $this->where(array('id'=>$id,'state'=>array('neq',-9)))->find();
			if (null === $result) {
				$this->error = '通知不存在或已删除！';
				return false;
			}else if (false !== $result) {
				$result = $this->where(array('id'=>$id,'state'=>array('neq',-9)))->save(array('state'=>1));
				if (false === $result) {
					wlog($this->getDbError());
					$this->error = '系统内部错误！';
					return false;
				}else if (0 === $result) {
					//$this->error = '请勿重复标记已读状态！';
					//return false;
					return true;
				}else {
					return true;
				}
			}else if(false === $result){
				wlog($this->getDbError());
				$this->error = '系统内部错误！';
				return false;
			}else {
				$this->error = '未知错误！';
				return false;
			}
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}

}
