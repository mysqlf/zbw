<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

/**
 * 文档模型控制器
 * 文档模型列表和详情
 */
class ArticleController extends HomeController {

    /* 文档模型频道页 */
	public function index(){
		//热门资讯
		$new_array = array(
				'help'=> array('办事指南', '5'),
				'statute'=> array('政策法规', '6'),
				'new'=> array('新闻动态', '11'),
			);
		$news = array();
		$m = M('Document');
		$field = 'id,title,create_time';
		foreach ($new_array as $key => $value) {
			$news[$key] = $m->field($field)->where(array('category_id'=> $value[1], 'status'=> 1))->order('update_time desc')->limit(8)->select();
		}
		$this->assign('hotNews', $news)->assign('news_cate', $new_array);
		//公示公告
		$notice = array();
		$notice['hot'] = $m->field($field.',cover_id')->where(array('category_id'=> 2, 'status'=> 1, 'cover_id'=> array('neq', '0') , 'position'=> 3))->order('update_time desc')->find();
		$notice['list'] = $m->field($field)->where(array('category_id'=> 2, 'status'=> 1))->order('update_time desc')->limit(5)->select();
		$this->assign('notice', $notice);
				//广告
		$banner_info = array_merge(D('Picture')->getBanner('carousel_picture', 3));

		$this->keywords = '平台资讯';
		$this->description = '平台资讯';
		$this->title = '平台资讯-'.C('WEB_SITE_TITLE');
		#文章地区
		$location=D('Document')->getLocation();//getArticleLocation();
		$this->assign('location',$location);
		$this->assign('banner_info',$banner_info);
		$this->display();

	}
	//帮助中心
	public function helpCenter()
	{
		$category = $this->category();
		$Document = D('Document');
		$map['d.display'] = '1';
		$map['status'] = array('neq','-1');
		$map['d.category_id'] = $category['id'];
		$this->list = $Document->alias('d')->join('onethink_document_article as a ON d.id=a.id')->where($map)->order('d.level DESC')->select();
		$this->display();
	}
	/* 文档模型列表页 */
	public function lists(){
		$keyword= urldecode(I('get.keyword',''));#关键字
		$location=I('get.location/d','');#地区
		$p = I('get.p/d',1);
		$p = $p?$p:1;
		$category = $_category =I('get.category','');
		if (!empty($category)) {
			$category=filter_value($category);
			$where['name']=$category;
			$map['category_id']=D('category')->getIDByName($where);
		}else{
			$map['category_id'] = array('in', '2,5,6,11');
		}
		/* 分类信息 */
		#$category = $this->category();
		/* 获取当前分类列表 */
		/*$map['category_id'] = $category['id'];*/
		$map['status'] = 1;
		$map['display'] = '1';

		if(!empty($location)){
			$map['location']=$location;
		}
		if (!empty($keyword)) {
			$keyword=filter_value($keyword);
			$map['title']=array("like","%$keyword%");
		}

		$Document = D('Document');
		$count = $Document->getCountByMap($map);
		/*var_dump($count);
		$this->count = ceil($count/$category['list_row']);*/
		$pagecount=ceil($count/10);
		$list = $Document->getListByMap($map,$p);//page($p, $category['list_row'])->lists($category['id']);

		if(IS_AJAX){
			if($list){
				/*foreach ($list as $key => $value) {
					$list[$key]['create_time']=date('Y-m-d H:i:s',$value['create_time']);
				}*/
				$this->ajaxReturn(array('status'=>0,'data'=>$list,'pagecount'=>$pagecount));
			}else {
				$this->ajaxReturn(array('status'=>0,'data'=>array(),'pagecount'=>$pagecount));
			}
			exit;
		}

		if(false === $list){
			$this->error('获取列表数据失败！');
		}
		#文章地区
		$locations=$Document->getLocation();//getArticleLocation();
		$categorys = $this->getLists(5);
		$this->keywords = '资讯';
		$this->description = '资讯';
		$pageshow = showpage($count, 10);
		$this->assign('page',$pageshow);
		if(empty($_category))
			$title = '最新资讯';
		else
			$title= M('category')->getFieldById($map['category_id'], 'title');
		$this->title = $title.'-'.C('WEB_SITE_TITLE');	
		#var_dump($category);
		/* 模板赋值并渲染模板 */
		$this->assign('pagecount',$pagecount);
		$this->assign('location',$location);
		$this->assign('keyword',$keyword);
		$this->assign('category', $category);
		$this->assign('locations',$locations);
		$this->assign('categorys', $categorys);
		$this->assign('list', $list);
		$this->display();
	}
	/**
	 * [_returnHtml 瀑布流返回html]
	 * @Author   JieJie
	 * @DataTime 2016-05-26T17:47:59+0800
	 * @param    [array]    $data [查询后的数组]
	 * @return   [string]     
	 */
	private function _returnHtml($data){
		$str = '';
		foreach ($data as $key => $value) {
			$day = date('d',$value['create_time']);
			$year = date('Y-m',$value['create_time']);
			$str.=<<<HTML
			<div class="news-list-box">
				<div class="news-time fl">
					<em>{$day}</em>
					<p>{$year}</p>
				</div>
				<dl class="fr">
					<dt><a href="/Article-detail-id-{$value['id']}">{$value['title']}</a></dt>
					<dd>{$value['description']}</dd>
				</dl>
			</div>
HTML;
		}
		return $str;
	}
	/*关于我们*/
	public function aboutUs(){
		$company_id= $this->_Cid;//I('get.cid');
		$this->keywords = '关于我们';
		$this->description = '关于我们';
		$this->title = '关于我们-'.C('WEB_SITE_TITLE');
		if ($company_id) {
			$ServiceArticle=D('ServiceArticle');

			$info=$ServiceArticle->getAboutCompany($company_id);
			$info[0]['content']=htmlspecialchars_decode($info[0]['content']);
			$this->assign('info',$info[0]);
			$this->assign('Cid',$this->_Cid)->display('serviceAboutUs');
		}else{
			$this->assign('Cid',$this->_Cid)->display();
		}
	}
	/**
	 * [servicedetail description]
	 * @return [type] [description]
	 */
	public function servicedetail(){
		$id=I('get.id');
		$company_id=I('get.company_id');
		$ServiceArticleCategory=D('ServiceArticleCategory');
		$ServiceArticle=D('ServiceArticle');
		$category=$ServiceArticleCategory->getCategory($company_id);
		$info=$ServiceArticle->getArticleInfo();
		$this->assign('category',$category);
		$this->assign('info', $info);
		$this->display('detail');
	}
	/* 文档模型详情页 */
	public function detail(){
		$id = I('get.id/d',0);
		 // 标识正确性检测 
		if(!$id){
			$this->redirect('Index/index','',3, '文章不存在或已被删除，页面跳转中...');
		}
		 // 获取详细信息 
		$Document = D('Document');
		$info = $Document->detail($id);
		if(!$info){
			$this->redirect('Index/index','',3, '文章不存在或已被删除，页面跳转中...');
			//$this->redirect('Index/index','',3, $Document->getError());
		}
		 // 分类信息
		$category = $this->category($info['category_id']);
		$category = $this->getLists($category['id']);

		//  更新浏览数 
		$map = array('id' => $id);
		$Document->where($map)->setInc('view');
		$this->keywords = $info['keyword'];
		$this->description = $info['description'];
		$this->title = $info['title'].'-'.C('WEB_SITE_TITLE');

		 // 模板赋值并渲染模板 
		$this->assign('category', $category);
		$this->assign('info', $info);
		$this->display();
	}
	/* 文档分类检测 */
	private function category($id = 0){
		/* 标识正确性检测 */
		$id = $id ? $id : I('get.category', 0);
		if(empty($id)){
			$this->error('没有指定文档分类！');
		}

		/* 获取分类信息 */
		$category = D('Category')->info($id);
		if($category && 1 == $category['status']){
			switch ($category['display']) {
				case 0:
					$this->error('该分类禁止显示！');
					break;
				//TODO: 更多分类显示状态判断
				default:
					return $category;
			}
		} else {
			$this->error('分类不存在或被禁用！');
		}
	}

	public function getLists($cate, $child = false){
		$field = 'id,name,pid,title,link_id';
		if($child){
			$category = D('Category')->getTree($cate, $field);
			$category = $category['_'];
		} else {
			$category = D('Category')->getSameLevel($cate, $field);
		}
		return $category;
	}
}
