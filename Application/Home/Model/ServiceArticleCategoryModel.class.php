<?php
	namespace Home\Model;
	use Think\Model;
	class ServiceArticleCategoryModel extends Model
	{
		protected $tablePrefix = 'zbw_';
		
		/**
		 * [getCategory 获取服务商文章分类]
		 * @param  [type] $company_id [服务商企业信息id]
		 * @return [array]       
		 */
		public function getCategory($company_id)
		{
			$map['company_id'] = $company_id ? $company_id : session('cid');
			$map['status'] = 1;
			$data = $this->field(true)->where($map)->select();
			return $data;
		}	
	}
?>