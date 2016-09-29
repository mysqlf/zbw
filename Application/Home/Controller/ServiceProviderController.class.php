<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use OT\DataDictionary;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class ServiceProviderController extends HomeController
{
	/**
	 * 服务商首页
	 */
	public function index(){
		$company_id = $this->_Cid;
		$ServiceThumb = D('ServiceThumb');
		$banner_info = $ServiceThumb->getImageList(1, $company_id);
		$cooperative_client = $ServiceThumb->getImageList(2, $company_id);
		// $serviceProduct = D('ServiceProduct');
		// $service_product = array();
		// $service_product[1] = $serviceProduct->serviceProduct(2,['company_id'=>$company_id,'state'=>1,'service_type'=>['in','1,3']]);
		// $service_product[2] = $serviceProduct->serviceProduct(1,['company_id'=>$company_id,'state'=>1,'service_type'=>['in','2,4']]);
		$data['sp.state'] = 1;
		$data['sp.company_id'] = $this->_Cid;
		$service_product = D('ServiceProduct')->productList($data, 4);
	//	dump($serviceProduct);
		$channel_list = channel_list($this->_Cid);
		$this->assign('Cid', $company_id)
			->assign('banner_info',$banner_info)
			->assign('cooperative_client',$cooperative_client)
			->assign('service_product',$service_product)
			->assign('channel_list',$channel_list)
			->display('Index/serviceIndex');
	}

}