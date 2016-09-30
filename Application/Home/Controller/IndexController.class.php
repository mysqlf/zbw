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
class IndexController extends HomeController 
{

	private $_Product;
	private $_ApplicableObject;

	protected function  _initialize(){
		if(ACTION_NAME == 'serviceProduct'){
			$this->_ApplicableObject = adminState()['applicable_object'];
			$this->_Product['spl.location'] = I('get.location',0,'intval');
			$this->_Product['sp.applicable_object']  = I('get.applicable_object',0,'intval');
			//'register_fund' => I('get.register_fund',0,'floatval'),
			$this->_Product['sp.amount']  = I('get.amount','0');
			$this->_Product['sp.product_name']  = I('get.product_name','','htmlspecialchars');

		}
	}

	public function index()
	{
		// if($company_id = I('get.cid',0,'intval'))
		// {
		// 	session('cid',$company_id);
		// 	$this->banner_info = D('ServiceThumb')->getImageList(1);
		// 	$this->cooperative_client = D('ServiceThumb')->getImageList(2);
		// 	$serviceProduct = array();
		// 	$serviceProduct[1] = D('ServiceProduct')->serviceProduct(2,['company_id'=>$company_id,'state'=>1,'service_type'=>['in','1,3']]);
		// 	$serviceProduct[2] = D('ServiceProduct')->serviceProduct(1,['company_id'=>$company_id,'state'=>1,'service_type'=>['in','2,4']]);
		// 	$this->service_product = $serviceProduct;
		// 	$this->channel_list =channel_list();
		// 	$this->display('serviceIndex');
		// }
	//	else
	//	{

//			$this->service_product = D('ServiceAdmin')->getServiceInfo();
//
//			//平台文章
//			$this->article = $this->getArticleList();
//			//获取所有城市
//			//
//			$this->city = D('Document')->getLocation();
//			$LocationDemand = D('LocationDemand');
//			$this->inquire_list = $LocationDemand->getCity();
//			$this->tool_list = $LocationDemand->getToolList($this->inquire_list[0]['location']);
//			//轮播图
//			$this->banner_info = array_merge(D('Picture')->getBanner('carousel_picture'));
//
//			$this->display();
		//	}
		// $data = array(
		// 	'location' => I('get.location',0,'intval'),
		// 	'employee_number' => I('get.employee_number',0,'intval'),
		// 	'register_fund' => I('get.register_fund',0,'floatval'),
		// 	'company_name' => I('get.company_name','','htmlspecialchars'),
		// 	'product_name' => I('get.product_name','','htmlspecialchars')
		// );
		// $serviceProduct = D('ServiceAdmin')->getServiceInfo($data);
		$data['sp.state'] = 1;
		$serviceProduct = D('ServiceProduct')->productList($data, 4);
		$city = D('Document')->getLocation();
		$article = $this->getArticleList($city);
		
		//$LocationDemand = D('LocationDemand');
		//$inquireList = $LocationDemand->getCity();
		//$toolList = $LocationDemand->getToolList($inquireList[0]['location']);//查询工具列表
		$bannerInfo = D('Picture')->getBanner('carousel_picture', 1);//轮播图信息
	
		$this->assign('service_product',$serviceProduct)
			->assign('article',$article)
			->assign('city',$city)
			->assign('inquire_list',$inquireList)
			->assign('tool_list',$toolList)
			->assign('banner_info',$bannerInfo)
			->display('Index/index');
	}


	/**
	 * 查询工具页面
	 */
	public function servicePoint(){
		//$this->template_city = D('ProductTemplate')->getCityList();//城市列表
		//dump($this->template_city);die;
		$LocationDemand = D('LocationDemand');
		$inquire_list = $LocationDemand->getCity();
		$tool_list = $LocationDemand->getToolList($inquire_list[0]['location']);
		$this->assign('inquire_list',$inquire_list)
			->assign('tool_list',$tool_list)
			->display('Index/service_query');
	}

	/**
	 * 社保计算
	 */
	public function socCalculate(){

		$this->display('Index/servicePoint');
	}


	/**
	 * 服务商页面
	 * move to ServiceProviderController
	 */
	public function serviceProduct()
	{
		if($this->_Product['spl.location']) $where['spl.location'] = $this->_Product['spl.location'];
		if($this->_Product['sp.applicable_object'])$where['sp.applicable_object'] = $this->_Product['sp.applicable_object'];	
		if($this->_Product['sp.amount'])
			{
				switch ($this->_Product['sp.amount']) 
				{
					case '1':
						$where['sp.amount'] = array('elt','50');
						break;
					case '2'	:
						$where['sp.amount'] = array('between','50,60');
						break;
					case '3'	:
						$where['sp.amount'] = array('between','60,70');
						break;
					case '4'	:
						$where['sp.amount'] = array('between','70,80');
						break;
					case '5'	:
						$where['sp.amount'] = array('between','80,100');
						break;	
					case '6':
						$where['sp.amount'] = array('egt',100);
						break;
				}		
		
			}
		if($this->_Product['sp.product_name']) $where['sp.name'] = array('like', '%'.$this->_Product['sp.product_name'].'%');
		$where['sp.state'] = 1;
//dump($where);
		//获取服务商及其产品
		$serviceAdmin = D('serviceAdmin');
		$service_product = D('ServiceProduct')->productList($where);
		//dump($service_product);
		$recommend_service = $serviceAdmin->recommendService();
				//广告
		$banner_info = array_merge(D('Picture')->getBanner('carousel_picture', 2));
		$company_config = adminState();
		//有服务的城市
		$location = D('ServiceProduct')->getLocation();

		$this->assign('service_product',$service_product)
			->assign('recommend_service',$recommend_service)
			->assign('location', $location)
			->assign('banner_info',$banner_info)
			->assign('company_config',$company_config)->assign('applicable_object', $this->_ApplicableObject);
		$this->display('Index/serviceProduct');
	}
	/**
	 * 服务商详情页
	 * move to ServiceProviderController
	 */
//	public function service(){
//		$company_id = $this->_Cid;
//		$ServiceThumb = D('ServiceThumb');
//		$banner_info = $ServiceThumb->getImageList(1, $company_id);
//		$cooperative_client = $ServiceThumb->getImageList(2, $company_id);
//		$serviceProduct = D('ServiceProduct');
//		$service_product = array();
//		$service_product[1] = $serviceProduct->serviceProduct(2,['company_id'=>$company_id,'state'=>1,'service_type'=>['in','1,3']]);
//		$service_product[2] = $serviceProduct->serviceProduct(1,['company_id'=>$company_id,'state'=>1,'service_type'=>['in','2,4']]);
//		$channel_list =channel_list($this->_Cid);
//		$this->assign('Cid', $company_id)
//			->assign('banner_info',$banner_info)
//			->assign('cooperative_client',$cooperative_client)
//			->assign('service_product',$service_product)
//			->assign('channel_list',$channel_list)
//			->display('Index/serviceIndex');
//	}

	public function index_back()
	{
		//所有服务商文章
		//$this->service_list = D('ServiceArticleCategory')->serviceList();

		/*//轮播图与合作伙伴数据获取
		$Picture = D('Picture');
		//轮播图
		$this->banner_info = array_merge($Picture->getBanner('carousel_picture'));
		//合作伙伴
		$this->partenr_info = array_merge($Picture->getBanner('partner_info'));

		$map['name'] = I('get.city_code') ? I('get.city_code','','htmlspecialchars') : getAddress();
		//获取城市代码
		$city_code = M('template','zbw_')->where($map)->getField('location');
		//城市不存在默认显示合肥
		if(!$city_code) {
			$city_code = $this->heifei_code;
			$map['name'] = $this->heifei_name;
		}
		//获取服务商产品对象为企业的产品
		$condition = array(
			'state'=>1,
			'location'=>$city_code,
			'service_type'=>1
		);
		$Product = D('ServiceProduct');
		//服务商针对于企业产品列表
		$this->company_product = $Product->getProductList($condition,2,'id DESC');
		//获取服务商产品对象为个人的产品
		$condition['service_type']=2;
		//服务商针对于个人产品列表
		$this->person_product = $Product->getProductList($condition,1,'id DESC');
		//企业签订协议记录
		// $this->order_list = D('ProductOrder')->getRecord();
		//城市列表
		$this->template_city = D('ProductTemplate')->getCityList();
		$this->assign('city',$map['name']);
		$this->assign('city_code',$city_code);
		*/
		//平台文章
		$article = $this->getArticleList();
		//获取所有城市
		$city = D('Document')->getLocation();
		$LocationDemand = D('LocationDemand');
		$inquire_list = $LocationDemand->getCity();
		$tool_list = $LocationDemand->getToolList($inquire_list[0]['location']);
		$this->assign('article',$article)
			->assign('city',$city)
			->assign('inquire_list',$inquire_list)
			->assign('tool_list',$tool_list)
			->display();
	}
///////////////////////////////////////以下都为功能函数/接口
	/**
	 * [getArticleList  获取平台各个城市文章]
	 * @param [post] $location [<可选参数,城市代号>]
	 * @return [array|jsaon] [ajax请求返回json]
	 */
	public function getArticleList($city=null)
	{
		if($_POST['id'])
		{
			$where['location'] = I('post.id',0,'intval');
			$where['status'] = 1;
		}
		else
		{
			$map['status'] = 1;
			$map['location'] = $city[0]['location'];
			//$map['_string'] = 'location is null';
			//$map['_logic'] = 'or';
			//$where['_complex'] = $map;
			$where = $map;
		}
		$field = 'id,title,location,create_time';
		$data['help'] = getCateList('help',4,$where,$field);
		$data['statute'] = getCateList('statute',4,$where,$field);
		$data['notice'] = getCateList('notice',4,$where,$field);
		$data['new'] = getCateList('new',4,$where,$field);
		if(IS_POST) $this->ajaxReturn(array('state'=>0,'msg'=>'操作成功!','data'=>$data));
		else return $data;
	}
	/**
	* [getCityClass 获取城市分类]
	* @Author   JieJie
	* @DataTime 2016-07-13T15:30:11+0800
	* @return   [type]                   [description]
	*/
	public function getCityClass()
	{
		$Template = D('Template');
		$result = $Template->getCityClass();
		if(!$result)
			$this->ajaxReturn(array('state'=>1,'msg'=>$Template->getError()));
		else
			$this->ajaxReturn(array('state'=>0,'msg'=>'操作成功','data'=>$result));
	}

	/**
	 * [changeClassify 切换分类获取规则]
	 * @Author   JieJie
	 * @DataTime 2016-07-13T16:58:28+0800
	 */
	public function changeClassify()
	{
		$classify = array_filter(I('post.classify_mixed'));
		$template_id = I('post.template_id',0,'intval');
		$type = I('post.type',0,'intval');
		$result = D('TemplateRule')->getRule($type,$template_id,$classify);
		$this->ajaxReturn($result);
	}
	
	/**
	 * [calculate 社保计算器]
	 * @Author   JieJie
	 * @DataTime 2016-07-13T18:01:47+0800
	 */
	public function calculate()
	{
		$template_id = I('post.template_id',0,'intval');
		$sb_classify = I('post.classify_mixed');
		$TemplateRule = D('TemplateRule');
		$sb_rule = $TemplateRule->getRule(1,$template_id,$sb_classify);
		$Calculation = new \Common\Model\Calculate();
		//计算社保
		$sb_month = I('post.sb_month',1,'intval');
		$sb_amount = I('post.sb_amount',0,'floatval');
		$sb_json = json_encode(array('amount'=>$sb_amount,'month'=>$sb_month));
		$data['sb_result'] = json_decode($Calculation->detail($sb_rule,$sb_json,1),true);
		if(!isset($_POST['isGjj'])) $this->ajaxReturn($data);
		//计算公积金
		$gjj_rule = $TemplateRule->getRule(2,$template_id,'');
		$gjj_amount = I('post.gjj_amount',0,'floatval');
		$gjj_month = I('post.gjj_month',1,'intval');
		$person_scale = I('post.person_scale',0,'floatval');
		$company_scale = I('post.company_scale',0,'floatval');
		$Calculation = new \Common\Model\Calculate();
		$gjj_json = json_encode(array('amount'=>$gjj_amount,'month'=>$gjj_month,'personScale'=>$person_scale.'%','companyScale'=>$company_scale.'%','cardno'=>''));
		$data['gjj_result'] = json_decode($Calculation->detail($gjj_rule,$gjj_json,2),true);
		$this->ajaxReturn($data);
	}

	//查询工具切换数据
	public function changeData(){
		$id = I('post.id',0,'intval');
		$this->tool_list = D('LocationDemand')->getToolList($id);
		$info = $this->fetch();
		$this->ajaxReturn(array('state'=>0,'msg'=>'操作成功!','data'=>$info));
	}
	/**
	 * 获取分类
	 * @Author   JieJie
	 * @DataTime 2016-03-21T19:42:22+0800
	 * @param    [int]    $type 1/2 1代表社保 2代表公积金
	 * @return   [array]  
	 */
	private function getClassify($type){
		$classfiy_model = D('ProductTemplateClassify');
		$map = array(
			'c.fid'=>0,
			'c.type'=>$type,
			't.location'=>intval(I('post.location')),
			't.company_id'=>intval(I('post.company_id')),
			'c.state'=>'1',
		);
		return $classfiy_model->getClassify($map);//社保分类
	}

	/**
	 * 获取社保/公积金规则
	 * @Author   JieJie
	 * @DataTime 2016-03-21T19:54:02+0800
	 * @param    [string]       $classify   规则id,通过|分割
	 * @param    [type]         $type       $type 1/2 1代表社保 2代表公积金
	 * @return   [string] 
	 */
	private function getRules($classify,$type,$template_id){
		$template_model = D('ProductTemplateRule');
		if( isset($_POST['type']) ) $classify = I('post.type_id','0','htmlspecialchars');
		$rule_map = array(
			'template_id'=>I('post.template_id','0','intval') ? I('post.template_id','0','intval') : $template_id,
			'classify_mixed'=>rtrim($classify,'|'),
			'type'=>$type
		);
		if(!$rule_map['template_id']) return false;
		$rule_map['classify_mixed'] = $this->sortClassifyMixed($rule_map['classify_mixed']);
		if(empty($rule_map['classify_mixed'])) $rule_map = "template_id = {$rule_map['template_id']} AND type = {$type} AND classify_mixed is NULL";
		return $template_model->getRule($rule_map);
	}
   
	/**
	 * 分类id排序处理
	 * @Author   JieJie
	 * @DataTime 2016-03-28T18:02:06+0800
	 * @param    [string]   $mixed  如'5|10|'
	 * @return   [string]   如'10|5'
	 */
	private function sortClassifyMixed($mixed){
		$mixed = rtrim($mixed,'|');
		$mixed = explode('|', $mixed);
		rsort($mixed);
		return implode('|', $mixed);
	}

	#计算社保/公积金接口
	public function calculation(){
		$data['sb_result'] = json_decode($this->getResult(1));//社保计算结果
		$data['gzj_result'] = json_decode($this->getResult(2));//公积金计算结果
		$data['czj_result'] = json_decode($this->getResult(3));//残障金
		$data['else_result'] = json_decode($this->getResult(4));//其它收费
		$this->ajaxReturn($data);
	}

	/**
	 * 计算社保/公积金
	 * @Author   JieJie
	 * @DataTime 2016-03-21T20:28:00+0800
	 * @param    [int]     $type [类型 1社保 2公积金]
	 * @return   [json]    计算结果
	 */
	private function getResult($type){
		$rule_model = D('ProductTemplateRule');
		$map['template_id'] = I('post.temple_id',0,'intval');
		$map['classify_mixed'] = $type==1 ? rtrim(I('post.sb_type_id','','htmlspecialchars'),'|') : rtrim(I('post.gzj_type_id','','htmlspecialchars'),'|');
		$map['type'] = $type;
		$map['classify_mixed'] = $this->sortClassifyMixed($map['classify_mixed']);
		if(empty($map['classify_mixed'])) $map = "template_id = {$map['template_id']} AND type = {$type} AND classify_mixed is NULL";
		$temp = $rule_model->getRule($map,true); 
		$rule = $temp['rule'];
		if(!$rule) return false;
		if($type==3||$type==4) return $rule;
		$month = $type==1 ? I('post.sb_month',1,'intval') : I('post.gzj_month',1,'intval');//该参数不传递默认计算1个月
		if($type==1) $json = json_encode(array('amount'=>I('post.sb_amount'),'month'=>$month));
		else $json = json_encode(array('amount'=>I('post.gzj_amount'),'month'=>$month,'personScale'=>I('post.member').'%','companyScale'=>I('post.firme').'%','cardno'=>''));
		$calculation = new \Common\Model\Calculate();
		return $calculation->detail($rule,$json,$type);
	}
	 
	#ajax验证验证码
	public function verifyCode(){
		if( check_verify( I('post.code') ) ) {
			session('is_verify',1);
			$this->ajaxReturn(1);
		}else{
			$this->ajaxReturn(0);
		}
	}

}