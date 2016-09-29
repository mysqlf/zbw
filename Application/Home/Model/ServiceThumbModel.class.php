<?php
	namespace Home\Model;
	use Think\Model;
	class ServiceThumbModel extends Model
	{
		protected  $tablePrefix = 'zbw_';

		/**
		 * [getImageList 获取服务商焦点图信息]
		 * @param  [type] $place      [显示位置]
		 * @param  [type] $company_id [服务商企业信息id]
		 * @return [type]             [description]
		 */
		public function getImageList($place,$company_id)
		{
			$map['company_id'] = $company_id ? $company_id : I('get.cid',0,'intval');
			$map['place'] = $place;
			$map['state'] = 1;
			return $this->where($map)->field('*')->select();
		}
	}
?>