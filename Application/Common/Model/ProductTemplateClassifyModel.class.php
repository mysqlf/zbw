<?php
	namespace Common\Model;
	use Think\Model;
	class ProductTemplateClassifyModel extends Model{
		protected $tablePrefix = 'zbw_';
		
		//获取分类以及子分类
		public function getClassify($map){
			$data['classify'] = $this->alias('c')->where($map)->join('zbw_product_template t ON t.id = c.template_id')->field('c.*,t.payment_type')->select();

			if(!$data['classify']) return false;

	    	foreach ($data['classify'] as $key => $value) {
	    		$data['classify'][$key]['child'] = $this->where('fid='.$value['id'])->select();
	    		$data['classify_mixed'] .= $data['classify'][$key]['child'][0]['id'].'|';
	    	}
	    	return $data;
		}
	}

?>