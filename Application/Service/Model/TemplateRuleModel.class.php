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
				$result = $this->field('id,template_id,company_id,name,category,type,classify_mixed,rule,deadline,payment_type,payment_month')->where($condition)->order('company_id asc')->find();
			}else {
				$result = $this->field('id,template_id,company_id,name,category,type,classify_mixed,rule,deadline,payment_type,payment_month')->where($condition)->order('company_id asc')->select();
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

	/**
	 * 规则列表
	 */
	public function ruleList($where, $admin,$pageSize = 10){
		$page = I('get.p', '1');
		$count = $this->alias('tr')->join('zbw_template t ON t.id = tr.template_id')->where($where)->count();
		$result = $this->alias('tr')->field('tr.id,tr.name,tr.state,type,t.location')
					->join('zbw_template t ON t.id = tr.template_id')
					->where($where)->order('tr.id desc')->page($page, $pageSize)->select();

		$pageshow = showpage($count, $pageSize);
		return array('page'=>$pageshow,'result'=>$result);
	}

	public function status($data, $admin){
		$info = $this->where(array('id'=> $data['id'], 'company_id'=> $admin['company_id']))->find();
		if(empty($info)) return ajaxJson(-1, '规则不存在'); 
		if($info['state'] == -9){
			$result = $this->where(array('id'=> $data['id'], 'company_id'=> $admin['company_id']))->save(array('state'=> 1));
		}else{
			$result = $this->where(array('id'=> $data['id'], 'company_id'=> $admin['company_id']))->save(array('state'=> -9));
		}
		if(empty($result)){
 			return ajaxJson(-1, '设置失败'); 
		}else{
			 return ajaxJson(0, '设置成功'); 
		}
	}

	/**
	 * 修改
	 */
	public function ruleEdit($rules, $admin){
		//处理规则
		if($rules['type'] == 2){
			$rules = $this->gjjHandleRule($rules);
		}
	}

	/**
	 * 修改-详细
	 */
	public function ruleInfo($where, $admin){
		$where['state'] = array('neq', -9);
		$res = $this->field(true)->where($where)->find();
		$where['type'] = 3 ;
		unset($where['id']);
		$disabled = $this->field(true)->where($where)->find();
		$dis_rule = json_decode($disabled['rule'], true);
		if($dis_rule['follow'] == $res['type']){
			$res['disabled'] = $dis_rule['disabled'];
		}
		$template = D('Template');
		$res['template'] = $template->field(true)->where(array('id'=>$res['template_id']))->find();
		$res['rule'] = json_decode($res['rule'], true);
		return $res;

	}

	/**
	 * 处理规则
	 */
	protected function gjjHandleRule($data){
		

	}
}
