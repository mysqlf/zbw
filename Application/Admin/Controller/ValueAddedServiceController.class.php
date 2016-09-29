<?php
namespace Admin\Controller;
use Admin\Think;

class ValueAddedServiceController extends ThinkController{
	private $model = 'value_added_service'; /*在OneThink模型管理中查看自己模型标识（不是名称）修改此处*/
	
	/**
	 * index function
	 * 首页
	 * @param int $p 分页页码
	 * @return void
	 * @author rohochan<rohochan@gmail.com>
	 **/
	public function index($p = 0){
		$this->lists($this->model,$p); /*系统会调用View/ValueAddedService/index.html来显示*/
	}
	
	/**
	 * lists function
	 * 列表
	 * @param string $model 模型名称
	 * @param int $p 分页页码
	 * @return void
	 * @author rohochan<rohochan@gmail.com>
	 **/
	public function lists( $model = null , $p = 0){
		parent::lists( $model ,$p ); /*系统会调用View/ValueAddedService/lists.html来显示*/
	}
	
	/**
	 * add function
	 * 新增
	 * @param string $model 模型名称
	 * @return void
	 * @author rohochan<rohochan@gmail.com>
	 **/
	public function add( $model = null ){
		$model = M('Model')->getByName( $this->model ); /*通过Model名称获取Model完整信息*/
		$_POST['update_date'] = date('Y-m-d H:i',  time());	
		parent::add( $model['id'] ); /*系统会调用View/ValueAddedService/add.html来显示*/
	}
	
	/**
	 * edit function
	 * 编辑
	 * @param string $model 模型名称
	 * @param int $id 数据id
	 * @return void
	 * @author rohochan<rohochan@gmail.com>
	 **/
	public function edit( $model = null, $id = 0 ){
		$id || $this->error('请选择要编辑的数据！');
		$model = M('Model')->getByName( $this->model ); /*通过Model名称获取Model完整信息*/	

	//	parent::edit( $model['id'], $id ); /*系统会调用View/ValueAddedService/edit.html来显示*/
	        //获取模型信息
       // $model = M('Model')->find($this->model );
    
        $model || $this->error('模型不存在！');

        if(IS_POST){
        	$_POST['update_date'] = date('Y-m-d H:i',  time());	
            $Model  =   D(parse_name(get_table_name($model['id']),1));
            // 获取模型的字段信息
            $Model  =   $this->checkAttr($Model,$model['id']);
            if($Model->create() && $Model->save()){
                $this->success('保存'.$model['title'].'成功！', U('lists?model='.$model['name']));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields     = get_model_attribute($model['id']);

            //获取数据
            $data       = M(get_table_name($model['id']))->find($id);
            $data || $this->error('数据不存在！');

       
			$data['province'] = trim(intval($data['location']/1000000)*1000000);			
            $this->assign('model', $model);
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->meta_title = '编辑'.$model['title'];
            $this->display($model['template_edit']?$model['template_edit']:'');
        }	
	}
	
	/**
	 * del function
	 * 删除
	 * @param string $model 模型名称
	 * @param string $ids 数据ids
	 * @return void
	 * @author rohochan<rohochan@gmail.com>
	 **/
	public function del( $model = null, $ids=null ){
		$model = M('Model')->getByName( $this->model ); /*通过Model名称获取Model完整信息*/
		parent::del( $model['id'], $ids ); /*没有页面，只有Ajax提示返回，不需要View/ValueAddedService/del.html*/
	}
	
	/**
	 * 设置一条或者多条数据的状态
	 * @author huajie <banhuajie@163.com>
	 */
	public function setStatus($model='Keyword'){
		return parent::setStatus($model);
	}

}