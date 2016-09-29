<?php
	namespace Home\Model;
	use Think\Model;
	use \Think\Page;
	class ServiceArticleModel extends Model{
		protected $tablePrefix = 'zbw_';
		/**
		 * [getArticleList 获取服务商文章列表]
		 * @param  [type] $category_id [分类id]
		 * @return [array]
		 */
		public function getArticleList($category_id)
		{
			$map['category_id'] = $category_id ? intval($category_id) : I('get.category_id',0,'intval');
			$map['status']=1;
			$count = $this->where($map)->count();
			$Page  = new Page($count,20);
			$data['page'] = $show = $Page->show();
			$data['list'] = $this->where($map)->field('id,title,category_id,update_time')->order('update_time DESC')->select();
			return $data;
		}

		public function getArticleInfo($cid)
		{
			$map['id'] = I('get.id',0,'intval');
			$map['company_id'] = $cid;
			return $this->where($map)->field('id,title,category_id,update_time,content')->find();
		}
		/**
		 * [getAboutCompany 获取企业介绍]
		 * @param  [type] $companyid [description]
		 * @return [type]            []
		 */
		public function getAboutCompany($companyid){
			$map['category_id']=0;
			$map['company_id']=$companyid;
			$map['status']=1;
			return $this->field(true)->where($map)->order('create_time asc')->limit(1)->select();
		}
	}