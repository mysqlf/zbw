<?php
	namespace Home\Controller;
	use Think\Controller;
	class SocialSecurityController extends HomeController
	{
		#社保大厅
		public function index()
		{
			$map['name'] = I('get.city_code') ? I('get.city_code','','htmlspecialchars') : getAddress();
			//获取城市代码
			$city_code = M('template','zbw_')->where($map)->getField('location');
			if(!$city_code)
			{//城市不存在默认显示合肥
				$city_code = $this->heifei_code;
				$map['name'] = $this->heifei_name;
			}
			//获取所有城市
			$this->template_city = D('ProductTemplate')->getCityList();
			
			$cid = $this->_Cid;//I('get.cid');
			//session('cid',$cid);
			//获取服务商产品对象为企业的产品
			$condition = array(
				'state'=>1,
				'service_type'=>array('in',array(1,2)),
				'company_id'=>$cid,
			);
			//$Product = D('ServiceProduct');
			// //服务商针对于企业产品列表
			// $company_product = $Product->serviceProduct(2,$condition,'id DESC');
			// foreach ($company_product as $key => $value) {
			// 	$company_product[$key]['product_detail']=htmlspecialchars_decode($value['product_detail']);
			// 	$tmp=json_decode($value['service_price'],true);
			// 	$company_product[$key]['service_price']=$tmp[0];
			// }
			// //获取服务商产品对象为个人的产品
			// $condition['service_type']=array('in',array(2,4));
			// //服务商针对于个人产品列表
			// $person_product = $Product->serviceProduct(1,$condition,'id DESC');
			// foreach ($person_product as $key => $value) {
			// 	$person_product[$key]['product_detail']=htmlspecialchars_decode($value['product_detail']);
			// 	$tmp=json_decode($value['service_price'],true);
			// 	$person_product[$key]['service_price']=$tmp;
			// }
			//增值服务列表
			$add_ser_list = D('ValueAddedService')->getAddedList(array('state'=>1,'company_id'=>$cid,'state'=>1),'create_date DESC');
			//增值服务列表外层循环次数 配合前端轮播特效
			$this->loop_num = ceil(count($add_ser_list)/4);
			 //社保资讯
			$this->sb_advisory = getCateList('questions');
			//行业资讯
			$this->industry_advisory = getCateList('social_policy');
			$this->channel_list =channel_list($this->_Cid);
			//公司资讯
			$this->company_advisory = getCateList('xz_help');
			$service_product = D('ServiceProduct')->productList($condition);
			$banner_info = D('ServiceThumb')->getImageList(3, $this->Cid);
			$this->assign('city',$map['name']);
			$this->assign('service_product',$service_product);
			//$this->assign('person_product',$person_product);
			$this->assign('add_ser_list',$add_ser_list)->assign('applicable_object', adminState()['applicable_object'])->assign('banner_info', $banner_info);
			$this->assign('Cid', $this->_Cid)->display();
		}
		public function getArticleList($cid)
		{
			if($_POST['id'])
			{
				$where['location'] = I('post.id',0,'intval');
			}
			else
			{
				$map['uid'] = $cid;
				#$map['_string'] = 'location is null';
				$map['_logic'] = 'or';
				$where['_complex'] = $map;
			}

			$field = 'id,title,location,create_time';
			$data['help'] = getCateList('help',6,$where,$field);
			$data['statute'] = getCateList('statute',6,$where,$field);
			$data['notice'] = getCateList('notice',6,$where,$field);
			$data['new'] = getCateList('new',6,$where,$field);
			if(IS_POST) $this->ajaxReturn(array('state'=>0,'msg'=>'操作成功!','data'=>$data));
			else return $data;
		}
		#产品购买处理
		public function buyHandle()
		{
			if(!session('?company_user')) $this->ajaxReturn(array('status'=>2,'content'=>'请先登录','url'=>'/Member-firmLogin'));
			$result = D('ProductOrder')->buyHandle();
			if(is_numeric($result)){
				$this->ajaxReturn(array('status'=>0,'content'=>'购买成功','url'=>U('Company/Information/serviceDetail',['id'=>$result])));
			}else if (is_array($result)) {
				$this->ajaxReturn(array('status'=>1,'content'=>$result['info'],'url'=>$result['url']));
			}else{
				$this->ajaxReturn(array('status'=>1,'content'=>$result));
			}
		}
		
		/**
		 * productDetail function
		 * 产品详情
		 * @return void
		 * @author rohochan
		 **/
		public function productDetail(){
			if (IS_POST) {
				$this->buyHandle();
			}else {
				$id = I('get.id');
				if ($id) {
					$serviceProduct = D('ServiceProduct');
					$serviceProductResult = $serviceProduct->where(array('id'=>$id,'state'=>1))->find();
					if ($serviceProductResult) {
						$serviceProductResult['servicePriceValue'] = json_decode($serviceProductResult['service_price'],true);
						//$serviceProductResult['serviceLocation'] = array_unique(merge_array([$serviceProductResult['location']],array_filter(explode(',',trim($serviceProductResult['other_location'],'"')))));
						$serviceProductResult['serviceLocation'] = array_unique(merge_array([$serviceProductResult['location']],array_filter(json_decode($serviceProductResult['other_location'],true))));
						if ($serviceProductResult['serviceLocation']) {
							foreach ($serviceProductResult['serviceLocation'] as $key => $value) {
								$serviceProductResult['serviceLocationValue'][$key] = showAreaName($value);
							}
						}
						$companyInfo = D('CompanyInfo');
						$serviceArticle = D('ServiceArticle');
						$companyInfoResult = $companyInfo->field(true)->getById($serviceProductResult['company_id']);
						if ($companyInfoResult) {
							$path = getFilePath($serviceProductResult['company_id'],'./Uploads/Company/','info');
							$companyInfoResult['service_logo'] = $path.'service_logo.jpg';
						}
						$serviceArticleResult = $serviceArticle->field('id,title,category_id')->where(['company_id'=>$serviceProductResult['company_id'],'status'=>1, 'category_id'=>array('neq', 0)])->order('update_time desc')->limit(4)->select();
						$result = ['serviceProductResult'=>$serviceProductResult,'companyInfoResult'=>$companyInfoResult,'serviceArticleResult'=>$serviceArticleResult];
						//dump($serviceProductResult);
						$this->assign('Cid', $this->_Cid)->assign('result',$result);
						$this->display('SocialSecurity/product_detail');
					}else {
						$this->error('该产品不存在或已下架!','Index/index');
					}
				}else {
					$this->error('非法参数!','Index/index');
				}
			}
		}
	}
?>