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
 * 模板模型
 */
class TemplateModel extends RelationModel{
	protected $tablePrefix = 'zbw_';
	
	protected $_link = array(
		'TemplateClassify'=>array(
			'mapping_type'	=> self::HAS_MANY,
			'class_name'		=> 'TemplateClassify',
			//'mapping_name'	=> 'TemplateClassify',
			'foreign_key'		=> 'template_id'
		),
		'TemplateRule'=>array(
			'mapping_type'	=> self::HAS_MANY,
			'class_name'		=> 'TemplateRule',
			//'mapping_name'	=> 'TemplateRule',
			'foreign_key'		=> 'template_id'
		)
	);
	
	/**
	 * getTemplateByCondition function
	 * 根据条件获取模板
	 * param array $condition 条件数组
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getTemplateByCondition($condition){
		if (is_array($condition)) {
			$result = $this->field(true)->where($condition)->find();
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
	
	/**
	 * getTemplateByRuleId function
	 * 根据规则获取模板
	 * param int $ruleId 规则id
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getTemplateByRuleId($ruleId){
		if ($ruleId) {
			$result = $this->alias('t')->field('t.*')->join('left join '.C('DB_PREFIX').'template_rule as tr on tr.template_id = t.id')->where(array('tr.id'=>$ruleId))->find();
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
	
	
	
	
	/**
	 * getDisAndOtherTemplate function
	 * 根据城市获取残障和其他金额
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getDisAndOtherTemplate($location = null, $type = 1){
		if ($location) {
			$productTemplateResult = $this->fetchSql(false)->field('ptr.*')->alias('pt')->join('left join '.C('DB_PREFIX').'product_template_rule as ptr on pt.id = ptr.template_id')->where(array('pt.location'=>$location,'pt.type'=>$type,'ptr.type'=>array('in','3,4')))->select();
			return $productTemplateResult;
		}else {
			$this->error = '非法参数!';
			return false;
		}
	}
}
