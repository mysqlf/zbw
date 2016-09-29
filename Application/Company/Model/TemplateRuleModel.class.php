<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: rohochan <rohochan@gmail.com> <http://blog.rohochan.com>
// +----------------------------------------------------------------------
namespace Company\Model;
use Think\Model;

/**
 * 模板规则模型
 */
class TemplateRuleModel extends Model{
	protected $tablePrefix = 'zbw_';
	
	/**
	 * getTemplateRuleByCondition function
	 * 根据条件获取模板规则
	 * @param array $condition 条件数组
	 * @param int $selectType 查询类型 1:find 2:select
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getTemplateRuleByCondition($condition,$selectType = 1){
		if (is_array($condition)) {
			if (1 == $selectType) {
				$result = $this->field('id,template_id,company_id,name,category,type,classify_mixed,rule')->where($condition)->order('company_id asc')->find();
			}else {
				$result = $this->field('id,template_id,company_id,name,category,type,classify_mixed,rule')->where($condition)->order('company_id asc')->select();
			}
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
