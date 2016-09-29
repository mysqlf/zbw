<?php
	namespace Home\Model;
	use Think\Model;
	class TemplateClassifyModel extends Model
	{
		protected $tablePrefix = 'zbw_';
		/**
		 * [getClassify 获取社保/公积金分类]
		 * @Author   JieJie
		 * @DataTime 2016-07-13T14:51:48+0800
		 * @param    [int]                   $type        [1社保 2公积金]
		 * @param    [int]                   $template_id [模板id]
		 * @return   [array]                  
		 */
		public function getClassify($type,$template_id)
		{
			$map['type'] = intval($type);
			$map['template_id'] = intval($template_id);
			$map['fid'] = 0;
			$classify = $this->where($map)->field('id,name')->select();
			if(!$classify) return false;
			foreach ($classify as $key=>$value) 
			{
				$map['fid'] = $value['id'];
				$classify[$key]['classify_mixed'] = $this->where($map)->field('id,name')->select();
			}
			return $classify;
		}
	}
?>