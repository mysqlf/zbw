<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: rohochan <rohochan@gmail.com> <http://blog.rohochan.com>
// +----------------------------------------------------------------------
namespace Service\Model;
use Think\Model;

/**
 * 个人信息模型
 */
class PersonBaseModel extends Model{
	protected $tablePrefix = 'zbw_';
	
	/**
	 * savePersonBase function
	 * 保存个人信息
	 * @access public
	 * @param array $data 数据
	 * @return boolean
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function savePersonBase($data){
		if (is_array($data) && !empty($data['card_num'])) {
			$nowtime = date('Y-m-d H:i:s');
			$data['birthday'] = get_birthday_by_idCard($data['card_num']);
			$data['gender'] = get_gender_by_idCard($data['card_num']);
			$data['audit'] = $data['audit']?$data['audit']:0;
			$data['residence_location'] = $data['residence_location']?$data['residence_location']:0;
			$data['residence_type'] = $data['residence_type']?$data['residence_type']:0;
			
			//保存个人基本信息表
			//$result = $this->field('id')->getByCardNum($data['card_num']);
			$result = $this->field('id,audit')->where(array('user_id'=>$data['user_id'],'card_num'=>$data['card_num']))->find();
			if ($result && 1 == $result['audit'] && 1 != $data['audit']) {
				if ($data['bank'] || $data['branch'] || $data['account_name'] || $data['account']) {
					$tempdata = ['bank'=>$data['bank'],'branch'=>$data['branch'],'account_name'=>$data['account_name'],'account'=>$data['account']];
					$saveResult = $this->where(['id'=>$result['id']])->save($tempdata);
					if ($saveResult || 0 === $saveResult) {
						return $result['id'];
					}else if (false === $saveResult) {
						$this->error = '系统内部错误！';
						wlog($this->getDbError());
						return false;
					}else {
						$this->error = '未知错误！';
						return false;
					}
				}else {
					return $result['id'];
				}
			}
			if ($data['id']) {
				if ($data['id'] == $result['id']) {
					//更新数据
					$condition = $data['id']?array('id'=>$data['id']):array('user_id'=>$data['user_id'],'card_num'=>$data['card_num']);
					$saveResult = $this->where($condition)->save($data);
					if ($saveResult || 0 === $saveResult) {
						return $data['id']?:$result['id'];
					}else if (false === $saveResult) {
						$this->error = '系统内部错误！';
						wlog($this->getDbError());
						return false;
					}else {
						$this->error = '未知错误！';
						return false;
					}
				}else {
					$this->error = '该身份证已存在！';
					return false;
				}
			}else if ($result) {
				//更新数据
				$condition = $data['id']?array('id'=>$data['id']):array('user_id'=>$data['user_id'],'card_num'=>$data['card_num']);
				$saveResult = $this->where($condition)->save($data);
				if ($saveResult || 0 === $saveResult) {
					return $data['id']?:$result['id'];
				}else if (false === $saveResult) {
					$this->error = '系统内部错误！';
					wlog($this->getDbError());
					return false;
				}else {
					$this->error = '未知错误！';
					return false;
				}
			}else {
				//新增数据
				$data['create_time'] = $nowtime;
				$saveResult = $this->add($data);
				if ($saveResult) {
					return $saveResult;
				}else if (false === $saveResult) {
					$this->error = '系统内部错误！';
					wlog($this->getDbError());
					return false;
				}else {
					$this->error = '未知错误！';
					return false;
				}
			}
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}

}
