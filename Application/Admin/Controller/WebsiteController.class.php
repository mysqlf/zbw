<?php
	namespace Admin\Controller;
	class WebsiteController extends AdminController {
		#轮播图列表
		public function index(){
			$this->carousel_list = F('carousel_picture','',C('WEB_SITE_PATH'));
			$this->meta_title = '切屏广告列表';
			$this->display();
		}

		#添加轮播图
		public function add(){
			$this->meta_title = '添加切屏广告';
			$this->display();
		}

		#合作商家管理
		public function merchant(){
			$this->partner_list = F('partner_info','',C('WEB_SITE_PATH'));
			$this->meta_title = '合作商家管理';
			$this->display();
		}

		#添加合作伙伴
		public function addPartner(){
			$this->meta_title = '新增合作商家';
			$this->display();
		}
		
		#添加/编辑,轮播图/合作商家处理
		public function addHandle(){
			//$key设置为不恒等于false的目的是排除key为0也是false的情况
			$picture_id = I('post.icon',0,'intval');
			$key = I('post.key');//用于判断是添加还是修改
			$cache_name = I('post.cache_name');//用于判断是轮播图还是合作商家
			unset($_POST['key']);//去除判断项
			unset($_POST['cache_name']);
			if($key!==false && $picture_id){//修改过图片
				$_POST['icon'] = $_POST['icon'][0];
				unset($_POST['icon_old']);
			}elseif($key!==false){//未修改过图片
				$_POST['icon'] = $_POST['icon_old'];
			}
			!$picture_id && $key===false && $this->error('请上传图片');//不是添加也不是删除则报错
			!$key && $_POST['icon'] = $picture_id[0];//不是编辑则获取图片id
			$picture_info = F($cache_name,'',C('WEB_SITE_PATH')) ? F($cache_name,'',C('WEB_SITE_PATH')) : array();//有缓存则读取缓存否则创建空数组
			//$key!==false ? $picture_info[$key] = $_POST : array_push($picture_info, $_POST);//是编辑则直接赋值否则入栈
			if($key!=false){
				$picture_info[$key] = $_POST;
			}else{
				array_push($picture_info, $_POST);
			}
			F($cache_name,$picture_info,C('WEB_SITE_PATH'));
			$jump_path = $cache_name == 'carousel_picture' ? U('index') : U('merchant');
			$this->success('操作成功',$jump_path);
		}

		#编辑或浏览
		public function edit(){
			$cache_name = I('get.name');
			if($cache_name!='carousel_picture' && $cache_name!='partner_info') $this->error('参数错误');
			$picture_info = F($cache_name,'',C('WEB_SITE_PATH'));
			$this->info = $picture_info[I('get.key',0,'intval')];
			$this->info['icon'] or $this->error('数据错误');
			$this->pic_path = M('picture')->where('id='.$this->info['icon'])->getField('path');
			$this->meta_title = '编辑切屏广告';
			$this->display();
		}

		#删除处理
		public function delHandle(){
			$cache_name = I('get.name');
			if($cache_name!='carousel_picture' && $cache_name!='partner_info') $this->error('参数错误');
			$key = I('get.key','');
			$picture_info = F($cache_name,'',C('WEB_SITE_PATH'));
			unset($picture_info[$key]);
			F($cache_name,$picture_info,C('WEB_SITE_PATH'));
			$this->success('删除成功');
		}

		#客服QQ管理
		public function kfqq(){
			$this->kf_list = F('qqkf','',C('WEB_SITE_PATH'));
			$this->meta_title = '客服QQ管理';
			$this->display();
		}

		#新增客服处理
		public function addKf(){
			$kf = F('qqkf','',C('WEB_SITE_PATH'));
			$kf = $kf ? $kf : array();
			$param = I('post.','','htmlspecialchars');
			array_push($kf, $param);
			F('qqkf',null,C('WEB_SITE_PATH'));
			F('qqkf',$kf,C('WEB_SITE_PATH'));
			$this->success('新增客服操作成功');
		}
		#删除客服
		public function delKf(){
			$key = I('get.key','','intval');
			$kf = F('qqkf','',C('WEB_SITE_PATH'));
			unset($kf[$key]);
			F('qqkf',$kf,C('WEB_SITE_PATH'));
			$this->success('删除客服操作成功');
		}
	}
?>