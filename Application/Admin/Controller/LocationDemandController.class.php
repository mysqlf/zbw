<?php
namespace Admin\Controller;
use Admin\Think;

class LocationDemandController extends ThinkController{
	private $model = 'location_demand'; /*在OneThink模型管理中查看自己模型标识（不是名称）修改此处*/
	
	/**
	 * index function
	 * 首页
	 * @param int $p 分页页码
	 * @return void
	 * 
	 **/
	public function index($p = 0){
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
		$model = M('Model')->getByName( $this->model ); /*通过Model名称获取Model完整信息*/
		parent::add( $model['id'] ); /*系统会调用View/LocationDemand/add.html来显示*/
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
		$id || $this->error('请选择要编辑的数据！');
		$model = M('Model')->getByName( $this->model ); /*通过Model名称获取Model完整信息*/
		parent::edit( $model['id'], $id ); /*系统会调用View/LocationDemand/edit.html来显示*/

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
		parent::del( $model['id'], $ids ); /*没有页面，只有Ajax提示返回，不需要View/LocationDemand/del.html*/
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
	        $next = intval($code/1000000)*1000000+1000000;
	        foreach ($area as $key => $value) {
	            if($key > $code && $key < $next && $key%10000 == 0){ 
                    $_area[$key]['name'] =  $value['name'];
                    $_area[$key]['id'] = $key;
	            }   
	        }  	      	        
      
	        if(!empty($_area)){

	        	$this->ajaxReturn($_area);
	        }else{
	        	$this->error('无下级分类!');
	        }
	    }else{ $this->error('错误！');}
	}
}