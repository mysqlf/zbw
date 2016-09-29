<?php
namespace Service\Controller;
use Think\Controller;

class CategoryController extends ServiceBaseController{
	private $_IsLogin;

	private $_ArticleData;
	private $_Category;
	private $_CategoryDate;

	protected function _initialize(){
		parent::_initialize();
		 $this->_Category = D('ArticleCategory');
	
		 $this->_CategoryDate['user_id'] = $this->_AccountInfo['user_id'];
		 if(IS_POST){
		 	$this->_CategoryDate['title'] = I('post.title', '');
		 	$this->_CategoryDate['pid'] = I('post.pid', '', 'intval');
		 	$this->_CategoryDate['id'] = I('post.id', '0', 'intval');
		 }
	}	 

	public function index(){
		$result = $this->_Category->getTree(0,'id,name,title,sort,pid,allow_publish,status');
		$this->assign('result', $result)->display();
	}


    /**
     * 新增或更新
     */
    public function update(){
		if(IS_POST){
			$result = $this->_Category->update($this->_ArticleData);
			return $result;
		}else{
			$this->_CategoryDate['id'] = I('id', '0', 'intval');
			if($this->_ArticleData['id']){
				$result = $this->_Category->info($this->_ArticleData, $this->_AccountInfo);
				$this->assign('result', $result);
			}
			$this->display();
		}

    }

}