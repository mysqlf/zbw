<?php
	namespace Home\Model;
	use Think\Model;
	class LocationModel extends Model{
		protected $tablePrefix = 'zbw_';
		
		public function getInquireCity(){
			return $this->where('state=1')->field('id,name')->select();
		}
	}
?>