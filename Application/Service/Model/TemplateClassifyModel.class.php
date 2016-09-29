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
 * 模板分类模型
 */
class TemplateClassifyModel extends Model{
	protected $tablePrefix = 'zbw_';
	
	/**
	 * getTemplateClassifyByCondition function
	 * 根据条件获取模板分类
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getTemplateClassifyByCondition($condition){
		if (is_array($condition)) {
			//$result = $this->field('id,name,type,template_id,fid')->where($condition)->order('fid asc')->select();
			$result = $this->field('id,name,type,fid')->where($condition)->order('fid asc')->select();
			if ($result || null === $result) {
				return $result;
			}else if (false === $result) {
				wlog($this->getDbError());
				$this->error = '系统内部错误！';
				return false;
			}else {
				$this->error = '未知错误！';
				return false;
			}
		}else {
			$this->error = '非法参数!';
			return false;
		}
	}
}
