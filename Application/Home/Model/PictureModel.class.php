<?php
	namespace Home\Model;
	use Think\Model;
	class PictureModel extends Model{
		#获取图片路径
		public function getBanner($cache_name, $position =1){
			$picture_info = F($cache_name,'',C('WEB_SITE_PATH'));
			if(!$picture_info) return false;
			$_picture_info = array();
			foreach ($picture_info as $key => $value) {
				if($position){
					if($value['position'] == $position)
					{
						$_picture_info[] = $value;
					}	
				}			
			}

			foreach ($_picture_info as $key => $value) {
				$value['icon'] && $_picture_info[$key]['path'] = $this->where('id='.$value['icon'])->getField('path');
			}
			return $_picture_info;
		}

		#获取查询工具ico
		public function getIco($data){
			foreach ($data as $key => $value) {
				if(is_numeric($value['src']))
				$data[$key]['src'] = $this->where('id='.$value['src'])->getField('path');
			}
			return $data;
		}
	}
?>