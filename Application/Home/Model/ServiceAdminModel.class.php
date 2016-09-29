<?php
	namespace Home\Model;
	use Think\Model;
	use Think\Page;
	class ServiceAdminModel extends Model
	{
		protected $tablePrefix = 'zbw_'; 
		/**
		 * [getServiceInfo 获取服务商及产品信息]
		 * @param array $data location,employee_number,register_fund,company_name,product_name
		 * @return mixed 返回服务商信息
		 */
		public function getServiceInfo($data=array())
		{
			$map = array();
			if($location = $data['location'])
				$map['c.location'] = $location;
			if($employee_number = $data['employee_number'])
				$map['c.employee_number'] = $employee_number;
			if($register_fund = $data['register_fund'])
			{
				switch ($register_fund) 
				{
					case '1':
						$map['c.register_fund'] = array('between','1,100');
						break;
					case '2'	:
						$map['c.register_fund'] = array('between','101,200');
						break;
					case '3'	:
						$map['c.register_fund'] = array('between','201,500');
						break;
					case '4'	:
						$map['c.register_fund'] = array('between','501,1000');
						break;
					case '5'	:
						$map['c.register_fund'] = array('between','1001,2000');
						break;	
					case '6':
						$map['c.register_fund'] = array('gt',2000);
						break;
				}
			}
				
			if($company_name = $data['company_name']){
				$map['c.company_name'] = array('like','%'.$company_name.'%');
			}
			if($product_name = $data['product_name']){
				$serviceProductResult = D('ServiceProduct')->getCompanyId($product_name);
				if ($serviceProductResult) {
					$map['c.id'] = array('in',$serviceProductResult);
				}else {
					$map['c.id'] = -1;
				}
			}
			//$map['c.id'] = 1;
			return $this->_getData(true,'','',$map);
		}

		/**
		 * [recommendService 推荐服务商]
		 * @return [type] [description]
		 */
		public function recommendService($service_id,$limit)
		{
			//推荐服务商id ,后期推荐服务商变动只需获取要服务商id传入即可
			$service_id = isset($service_id) ? $service_id : '1,33';
			$map['s.id'] = array('in',$service_id);
			return $this->_getData(false,'',$limit,$map);
		}

		/**
		 * [_getData 获取服务商信息及产品信息]
		 * @param  boolean $page  [是否需要分页]
		 * @param  [string]  $field [需要查找的字段]
		 * @param  [type]  $limit [需要显示的条数 ]
		 * @param  [type]  $map   [需要查找的条件]
		 * @return [array]         [description]
		 */
		private function _getData($page=true,$field,$limit,$map)
		{
			if ($map['c.location']) {
				$location = $map['c.location'];
				unset($map['c.location']);
				$companyId = 0;
				$serviceProductLocation = M('ServiceProductLocation','zbw_');
				$serviceProductLocationResult = $serviceProductLocation->field('service_product_id')->where(['location'=>$location])->select();
				if ($serviceProductLocationResult) {
					$productIdArray = array();
					foreach ($serviceProductLocationResult as $key => $value) {
						$productIdArray[$value['service_product_id']] = $value['service_product_id'];
					}
					if ($productIdArray) {
						$serviceProduct = D('ServiceProduct');
						$serviceProductResult = $serviceProduct->field('company_id')->where(['id'=>['in',$productIdArray],'state'=>1])->select();
						if ($serviceProductResult) {
							$companyIdArray = array();
							foreach ($serviceProductResult as $key => $value) {
								$companyIdArray[$value['company_id']] = $value['company_id'];
							}
							if ($companyIdArray) {
								$companyId = ['in',$companyIdArray];
							}else {
								$companyId = 0;
							}
						}else {
							$companyId = 0;
						}
					}else {
						$companyId = 0;
					}
				}else {
					$companyId = 0;
				}
				if ($map['c.id']) {
					$map['c.id'] = [$map['c.id'],$companyId];
				}else {
					$map['c.id'] = $companyId;
				}
			}
			$map['s.type'] = $map['s.state'] = $map['c.audit'] = 1;
			$field or $field = array('s.id'=>'s_id','c.id'=>'c_id','c.company_name','c.location','c.property','c.employee_number','c.register_fund','c.company_introduction');
			if($page)
			{
				$count = $this->alias('s')
					->join('zbw_company_info AS c ON s.company_id = c.id')
					->where($map)->count();
				$Page = new Page($count,6);
				$data['page'] = $Page->show();
			}
			
			$limit or $limit = $Page->firstRow.','.$Page->listRows;

			$data['list'] = $this->alias('s')
				->join('zbw_company_info AS c ON s.company_id = c.id')
				->field($field)
				->where($map)
				->order('s.id ASC')
				->limit($limit)
				->select();
			//获取企业信息配置项	
			$company_param = adminState();
			foreach ($data['list'] as $key => $value) 
			{
				$data['list'][$key]['employee_number'] = $company_param['employee_number'][$value['employee_number']];
				$data['list'][$key]['industry'] = $company_param['industry'][$value['industry']];
				$data['list'][$key]['property'] = $company_param['property'][$value['property']];
				if ($productIdArray) {
					$data['list'][$key]['product_id'] = $productIdArray;
				}
				
			}
			//获取产品列表
			$data['list'] = D('ServiceProduct')->getProductList($data['list']);
			return $data;
		}
	}
?>