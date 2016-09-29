<?php
	namespace Common\Model;
	use Think\Model;
	class ValueAddedServiceModel extends Model
	{
		protected $tablePrefix = 'zbw_';
		
		#获取增值服务列表
		public function getAddedList($map,$order)
		{
			return $this->where($map)->field(true)->order($order)->select();
		}
	}
?>