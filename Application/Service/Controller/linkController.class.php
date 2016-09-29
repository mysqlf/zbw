<?php
namespace Service\Controller;
use Think\Controller;

class LinkController extends ServiceBaseController{
	private $_linkDate;
	private $_Link;

	protected function _initialize(){
		 parent::_initialize();
		 $this->_Link = M('service_link');
		// 
		 $this->_linkDate['user_id'] = $this->_AccountInfo['user_id'];
		 if(IS_POST){
		 	$this->_linkDate['name'] = I('post.name', '');
		 	$this->_linkDate['url'] = I('post.url', '');
		 	$this->_linkDate['id'] = I('post.id', '0', 'intval');
		 }
		 $page = I('get.p', '1', 'intval');
	}	 


	public function index(){
		// $where = "user_id = {$this->_AccountInfo['user_id']}";
		// $count =  $this->_Link->where($where)->count();
		// $result = $this->_Link->field('id,name,url')->where($where)->page($page, 20)->select();
		// $pages = showpage($count, 20);
		$this->assign('pages', $pages)->assign('result', $result)->display();	
	}

	public function update(){
		if(IS_POST){
			if(isset($this->_linkDate['id'])){
				$result =  $this->_Link->where(array('id'=> $this->_linkDate['id'], 'user_id'=> $this->_linkDate['user_id']))->save($this->_linkDate);
				if($result){
					$this->ajaxReturn(array('status'=> 1, 'info'=> '修改成功'));
				}else{
					$this->ajaxReturn(array('status'=> 0, 'info'=> '修改失败'));
				}
			}else{
				$this->_Link->create($this->_linkDate);
				$result = $this->_Link->add();
				if($result){
					$this->ajaxReturn(array('status'=> 1, 'info'=> '添加成功！'));
				}else{
					$this->ajaxReturn(array('status'=> 0, 'info'=> '添加失败！'));
				}
			}
		}else{
			$id = I('get.id', '0', 'intval');
			if($id){
				$info = $this->_Link->where(array('id'=> $id, 'user_id'=> $this->_linkDate['user_id']))->find();
				if(empty($info)) $this->error('信息不存在！');
				$this->assign('result', $result)->display();
			}
		}

	}
}