<?php
	namespace Home\Controller;
	use Think\Controller;
	class ServiceArticleController extends HomeController
	{

		public function index()
		{
			$category = D('ServiceArticleCategory')->getCategory($this->_Cid);
			$this->article_list = D('ServiceArticle')->getArticleList();
			$this->assign('category',$category);
			$category_id = I('get.category_id', '0');
			if(empty($category_id))  $category_id = $category[0]['id'];
			// if (I('get.cid')) {
			// 	session('cid',I('get.cid'));
			// 	$this->redirect('ServiceArticle/index',['category_id'=>$category[0]['id']]);
			// if($this->_Cid){
			// 	$this->redirect('ServiceArticle/index',['category_id'=>$category[0]['id']]);
			// }else{
			 	$this->assign('Cid', $this->_Cid)->assign('category_id', $category_id)->display();
			// }
		}

		public function articleInfo()
		{
			$this->category = D('ServiceArticleCategory')->getCategory($this->_Cid);
			$this->article_info = D('ServiceArticle')->getArticleInfo($this->_Cid);
			$category_id = I('get.category_id', '0');
			if(empty($category_id))  $category_id = $category[0]['id'];
						
			$this->assign('category_id', $category_id)->assign('Cid', $this->_Cid)->display();
		}
	}
?>