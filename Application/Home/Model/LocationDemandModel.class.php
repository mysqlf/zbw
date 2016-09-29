<?php
	namespace Home\Model;
	use Think\Model;
	class LocationDemandModel extends Model
	{
		protected $tablePrefix = 'zbw_';
		
		public function getToolList($location)
		{
			$map['location'] = $location;
			$map['state'] = 1;
			$result = $this->where($map)->select();
			$result && $result = D('Picture')->getIco($result);
			return $result;
		}

		public function getCity()
		{
			return $this->query('SELECT DISTINCT location FROM zbw_location_demand WHERE location IS NOT NULL');
		}

	}
?>