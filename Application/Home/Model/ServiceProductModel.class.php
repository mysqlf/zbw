<?php
	namespace Home\Model;
	use Think\Model;
	class ServiceProductModel extends Model
	{
		protected $tablePrefix = 'zbw_';
		
		/**
		 * [getProductList 获取服务商产品列表]
		 * @param  [array]  $service_list [服务商信息]
		 * @param  integer $limit        [条数]
		 * @return [array]                [description]
		 */
		public function getProductList($service_list,$limit=3)
		{
			//$Order = M('ServiceProductOrder','zbw_');
			$map['state'] = 1;
			$condition['state'] = array('in','1,2');
			$condition['state'] = array('in','1,2,3');

			foreach ($service_list as $key => $value) 
			{
				$map['company_id'] = $value['c_id'];
				if ($value['product_id']) {
					$map['id'] = ['in',$value['product_id']];
				}
				$service_list[$key]['product'] = $this->where($map)->limit($limit)->order('id DESC')->select();
				/*if($service_list[$key]['product'])
				{
					foreach ($service_list[$key]['product'] as $k => $v) 
					{
						$condition['product_id'] = $v['id'];
						$service_list[$key]['product'][$k]['service_num'] = $Order->where($condition)->count();
					}
				}*/
			}

			return $service_list;
		}

		/**
		 * [getCompanyId 获取产品服务商id]
		 * @param  [type] $product_name [产品名]
		 * @return [type]               [description]
		 */
		public function getCompanyId($product_name)
		{
			$map['name'] = array('like','%'.$product_name.'%');
			$map['state'] = 1;
			$data = $this->where($map)->field('company_id')->select();
			$temp_id = array();
			foreach ($data as $value) 
			{
				$temp_id[] = $value['company_id'];
			}
			return $temp_id;
		}
		/**
		 * 获取地区服务商产品
		 * @Author   JieJie
		 * @DataTime 2016-03-17T17:05:30+0800
		 * @param    int   $limit 查询记录条数
		 * @param    int     $company_id 服务商企业信息id
		 * @param    string  $order 排序规则
		 * @return   array
		 */
		public function serviceProduct($limit=3,$map,$order='')
		{
			$map['state'] = 1;
			$order = $order ? $order :'id DESC';
			return $this->where($map)->field(true)->order($order)->limit($limit)->select();
		}
		
		#获取单条产品信息
		public function productInfo($id)
		{
			return $this->where(array('id'=>$id,'state'=>1))->field('*')->find();
		}

		public function productList($where, $pageSize=20){
			$page = I('get.p', '1');
			$m = M('service_product_location', 'zbw_');
			$count = $this->alias('sp')->join('LEFT JOIN zbw_service_product_location spl ON spl.service_product_id=sp.id')
						->where($where)->count();
			$result = $this->alias('sp')->field('ci.id cid,sp.name,sp.applicable_object,sp.service_price,sp.id,sp.service_type,sp.service_price_state,ci.company_name')
						->join('LEFT JOIN zbw_service_product_location spl ON spl.service_product_id=sp.id')
						->join('LEFT JOIN zbw_company_info ci ON ci.id=sp.company_id')
						->where($where)->page($page, $pageSize)->order('create_time asc')->select();
			
			if($result){
				foreach ($result as $key => $value) {
					$result[$key]['service_price'] = json_decode($value['service_price'], true);
				}
			}
			$pageshow = showpage($count, 20);
			return array('page'=>$pageshow,'result'=>$result);
		}

		/**
		 * 添加过产品的城市
		 * @return [type] [description]
		 */
		public function getLocation(){
			$result = M('service_product_location', 'zbw_')->field('location')->where(array('state'=> 1, 'location'=> array('neq', 0), 'service_product_id'=> array('neq', 0)))->group('location')->select();
			$m = M('location', 'zbw_');
			foreach ($result as $key => $value) {
				$result[$key]['name'] = $m->getFieldById($value['location'], 'name');
			}
			return $result;
		}

	}