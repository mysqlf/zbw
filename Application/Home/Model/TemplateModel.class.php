<?php
	namespace Home\Model;
	use Think\Model;
	class TemplateModel extends Model
	{
		protected $tablePrefix = 'zbw_';
		public function getCityClass()
		{
			$location = I('post.location',0,'intval');
			if(!$location)
			{
				$this->error = '请先选择城市!';
				return false;
			}
			$condition['location'] = $location;
			$condition['state'] = 1;
			$data['template_id'] = $this->where($condition)->getField('id');
			if(!$data['template_id'])
			{
				$this->error = '数据错误！';
				return false;
			}
			$TemplateClassify = D('TemplateClassify');
			$data['sb_classify'] = $TemplateClassify->getClassify(1,$data['template_id']);
			$data['gjj_classify'] = $TemplateClassify->getClassify(2,$data['template_id']);

			$TemplateRule = D('TemplateRule');
			$data['sb_rule'] = $TemplateRule->getRule(1,$data['template_id'],'');
			$data['gjj_rule'] = $TemplateRule->getRule(2,$data['template_id'],'');
			return $data;
		}
	}
?>