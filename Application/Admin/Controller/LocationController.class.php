<?php
namespace Admin\Controller;
use Admin\Think;

class LocationController extends ThinkController{
	private $model = 'location'; /*在OneThink模型管理中查看自己模型标识（不是名称）修改此处*/
	
	/**
	 * index function
	 * 首页
	 * @param int $p 分页页码
	 * @return void
	 * 
	 **/
	public function index($p = 0){	
		//$zoning = S('ptimeZoning');
		$this->lists($this->model,$p); /*系统会调用View/LocationDemand/index.html来显示*/
	}
	
	/**
	 * lists function
	 * 列表
	 * @param string $model 模型名称
	 * @param int $p 分页页码
	 * @return void
	 * 
	 **/
	public function lists( $model = null , $p = 0){
		parent::lists( $model ,$p ); /*系统会调用View/LocationDemand/lists.html来显示*/
	}
	
	/**
	 * add function
	 * 新增
	 * @param string $model 模型名称
	 * @return void
	 * 
	 **/
	public function add( $model = null ){
		/*$model = M('Model')->getByName( $this->model ); //通过Model名称获取Model完整信息
		parent::add( $model['id'] ); //系统会调用View/LocationDemand/add.html来显示*/
		if(IS_POST){
			$city_name = I('post.city_name');
			$city_name or $this->error('请输入城市名!');
			$location = empty($_POST['location1']) ? I('post.location','','intval') : I('post.location1','','intval');
			$Location = M('location','zbw_');
			$max_code = intval($location/100)*100+1000;
			$min_code = intval($location/100)*100+100;
			$map['id'] = array(array('between',array($min_code,$max_code)));
			$max_id = $Location->where($map)->max('id');
			$data['id'] = $max_id+100;
			$data['state'] = 1;
			$data['name'] = $city_name;
			if($Location->add($data)) $this->success('添加成功!');
			else $this->error('添加失败!');
		}else{
			$this->area = get_area();
			$this->display();
		}
	}

	 /**
	 * 省市联动
	 * @param [type] $[code] [省名称]
	 */
	 public function selectArea($code = '')
	 {      
	 	if(!IS_AJAX && !is_numeric($code)) $this->error('错误！');
		$area = getZoning();
	 	$_area = array();
	 	$next = intval($code/1000000)*1000000+1000000;
	 	foreach ($area as $key => $value) 
	 	{
	 		if($key > $code && $key < $next && $key%10000 == 0)
	 		{ 
                			$_area[$key]['name'] =  $value['name'];
                			$_area[$key]['id'] = $key;
            			}   
	 	}
	    	!empty($_area) or $this->error('无下级分类！');
	    	$this->ajaxReturn($_area);
	}
	
	/**
	 * edit function
	 * 编辑
	 * @param string $model 模型名称
	 * @param int $id 数据id
	 * @return void
	 * 
	 **/
	public function edit( $model = null, $id = 0 ){
		$model = M('Model')->find($model);
		$model || $this->error('模型不存在！');
		if(IS_POST){
			$state = I('post.state');        	
			$location = M('location','zbw_');
			if($location->create()){
				$location->save();
				$pre  = intval($id/1000000)*1000000;	
				if($id%1000000 == 0){//修改省一级 同时修改其下级	               
					$next = intval($id/1000000)*1000000+1000000;
					if($pre && $next){
						$location->where(array('id'=> array('between', array($pre, $next))))->save(array('state'=> $state));	
					}
				}
				//同时修改其省级 				
				if($pre){
					$location->where(array('id'=> $pre))->save(array('state'=> $state));	
				}
				S('ptimeZoning', null);
				S('area', null);
				$data = $location->where('1')->order('id asc')->select();
				foreach ($data as $key => $value) {
					$_data[$value['id']] = $value;
				}				
				S('ptimeZoning', $_data);   
				$this->success('保存成功！', U('Location/index'));     		
			} else {
				$this->error($Model->getError());
			}
		} else {        	
			$fields     = get_model_attribute($model['id']);
			//获取数据
			$data       = M(get_table_name($model['id']),'zbw_')->find($id);
			$data || $this->error('数据不存在！');
			$this->assign('data', $data);
			$this->assign('model', $model);
			$this->assign('fields', $fields);
			$this->assign('model', $model);
			$this->meta_title = '编辑'.$model['title'];
			$this->edit_title = $data['name'];
			$this->display($model['template_edit']?$model['template_edit']:'');
		}
	}
	
	/**
	 * del function
	 * 删除
	 * @param string $model 模型名称
	 * @param string $ids 数据ids
	 * @return void
	 * 
	 **/
	public function del( $model = null, $ids=null ){
		$model = M('Model')->getByName( $this->model ); /*通过Model名称获取Model完整信息*/
		parent::del( $model['id'], $ids ); 

		/*没有页面，只有Ajax提示返回，不需要View/LocationDemand/del.html*/
	}
	
	/**
	 * 设置一条或者多条数据的状态
	 * @author huajie <banhuajie@163.com>
	 */
	public function setStatus($model='Keyword'){
		return parent::setStatus($model);
	}


	/**
	 * 省市联动
	 * @param [type] $[code] [省名称]
	 */
	 function select_area($code = ''){      
		if(IS_AJAX && is_numeric($code)){
			$area = getZoning();            
			$_area = array();
			foreach ($area as $key => $value) {
				if($key%10000 == 0){
					if( $key - $code < 1000000  && $key - $code  > 0 ){                     
						$_area[$key]['name'] =  $value['name'];
						$_area[$key]['id'] = $key;
					}
				}   
			}	        
		  
			if(!empty($_area)){
				//echo json_encode($_area, JSON_UNESCAPED_UNICODE);die();
				$this->ajaxReturn($_area);
			}else{
				$this->error('错误！');
			}
		}else{ $this->error('错误！');}
	}



}