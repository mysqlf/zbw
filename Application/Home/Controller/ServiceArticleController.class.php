<?php
	namespace Home\Controller;
	use Think\Controller;
	class ServiceArticleController extends HomeController
	{

		public function index()
		{
			$category = D('ServiceArticleCategory')->getCategory($this->_Cid);
			$this->article_list = D('ServiceArticle')->getArticleList($this->_Cid);
			$this->assign('category',$category);
			$category_id = I('get.category_id', '0');
		//	if(empty($category_id))  $category_id = $category[0]['id'];
			// if (I('get.cid')) {
			// 	session('cid',I('get.cid'));
			// 	$this->redirect('ServiceArticle/index',['category_id'=>$category[0]['id']]);
			// if($this->_Cid){
			// 	$this->redirect('ServiceArticle/index',['category_id'=>$category[0]['id']]);
			// }else{
			// 
			$this->keywords = '最新资讯';
			$this->description = '最新资讯';
			if(empty($category_id))
				$title = '最新资讯';
			else
				$title= D('ServiceArticleCategory')->getFieldById($category_id, 'title');
			$this->title = $title.'-'.$this->_CompanyName.'-'.C('WEB_SITE_TITLE');

			 	$this->assign('Cid', $this->_Cid)->assign('category_id', $category_id)->display();
			// }
		}

		public function articleInfo()
		{
			$this->category = D('ServiceArticleCategory')->getCategory($this->_Cid);
			$this->article_info = D('ServiceArticle')->getArticleInfo($this->_Cid);
			$category_id = I('get.category_id', '0');
			if(empty($category_id))  $category_id = $category[0]['id'];

			$this->keywords = $this->article_info['keyword'];
			$this->description = $this->article_info['description'];
			$this->title = $this->article_info['title'].'-'.$this->_CompanyName.'-'.C('WEB_SITE_TITLE');

			$this->assign('category_id', $category_id)->assign('Cid', $this->_Cid)->display();
		}
	}
?>