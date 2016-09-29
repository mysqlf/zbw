<?php
	namespace Home\Model;
	use Think\Model;
	class TemplateRuleModel extends Model
	{
		protected $tablePrefix = 'zbw_';

		/**
		 * [getRule 获取规则]
		 * @Author   JieJie
		 * @DataTime 2016-07-13T15:24:50+0800
		 * @param    [int]                $type        [类型]
		 * @param    [int]                $template_id [模板id]
		 * @param    [array]            $classify    [分类]
		 * @return     [array]                            
		 */
		public function getRule($type,$template_id,$classify)
		{
			$condition['type'] = intval($type);
			$condition['template_id'] = intval($template_id);
			$condition['classify_mixed'] = $this->_classifySort($classify);
			$result=$this->where($condition)->getField('rule');
	

			return json_decode($result,true);
		}

		/**
		 * [_classifySort 分类数据排序处理]
		 * @Author   JieJie
		 * @DataTime 2016-07-04T18:54:22+0800
		 * @param    [array]        $classify [分类数组]
		 * @return   [string]       		  [组合的分类规则]
		 */
		private function _classifySort($classify)
		{
			$classify = array_filter($classify);
			if(empty($classify)) return '';
			if($classify == '') return $classify;
			if(!is_array($classify))
			{
				$classify = explode(',', $classify);
			}

		 	if(count($classify) > 1)
		 	{
		 		rsort($classify);
		 		$classify = implode('|', $classify);
		 	}else{
		 		$classify = $classify[0];
		 	}
	 		return $classify;		
		}
	}
?>