<?php
	namespace Common\Model;
	use Think\Model;
	class ProductTemplateModel extends Model{
		protected $tablePrefix = 'zbw_';
		protected $tableName = 'template';
		
		#获取城市列表
		public function getCityList(){
	    		return $result = $this->field('id,name,location')->where('state=1')->select();
		}
	}
?>