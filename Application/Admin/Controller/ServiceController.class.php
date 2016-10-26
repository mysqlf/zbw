<?php
	namespace Admin\Controller;
	/**
	 * 服务商主账号管理
	 * @author JieJie
	 */
	class ServiceController extends AdminController
	{
		public function index()
		{
			$this->service_list = D('ServiceAdmin')->lists();
			$this->meta_title = '服务商列表';
			$this->display();
		}

		/**
		 * 添加服务商入口
		 * @return json 
		 */
		public function addService()
		{
			if(IS_POST)
			{
				$Admin = D('ServiceAdmin');
				$result = $Admin->addService();
				if($result) $this->success('添加成功!',U('Admin/Service/index'));
				else $this->error($Admin->getError());
			}else{
				$this->token = md5(uniqid());
				$this->industry = C('INDUSTRY');
				$this->property = C('PROPERTY');
				$this->area = get_area();
				$this->meta_title = '添加服务商';
				$this->display();
			}
		}
	
		/**
		 * 获取市级城市
		 * @return [json]
		 */
		public function getCity()
		{
			$location = I('get.location',0,'intval');
			//获取省 直辖市下的下级市
			$area = getZoning();
			$next = intval($location/1000000)*1000000+1000000;
			foreach ($area as $key => $value) 
		 	{
		 		if($key > $location && $key < $next && $key%10000 == 0)
		 		{ 
	                			$_area[$key]['name'] =  $value['name'];
	                			$_area[$key]['id'] = $key;
	            			}
		 	}
		 	$this->ajaxReturn($_area);
		}

		/**
		 * [modifyStatus 修改服务商状态]
		 * @return [json]
		 */
		public function modifyStatus()
		{
			$result = D('ServiceAdmin')->modifyStatus();
			if($result) $this->success('操作成功!',U('Admin/Service/index'));
			$this->error('操作失败!');
		}

		/**
		 * [showCompany 查看服务商服务的企业]
		 */
		public function showCompany()
		{
			$this->company_info = D('ServiceAdmin')->showCompany();
			$this->display();
		}

		/**
		 * [serviceInfo 服务商详情页]
		 * @return [type] [description]
		 */
		public function serviceInfo()
		{
			$this->service_info = D('ServiceAdmin')->serviceInfo();
			$this->meta_title = '服务商详情';
			$this->display();
		}
		public function uploadfile(){
			$company_id=intval(I('post.companyid'));
			$logo=$_FILES['service_logo'];
			$license=$_FILES['business_license'];
			if (!empty($logo)) {
				$path=getFilePath($company_id,'./Uploads/Company/','info')."service_logo.jpg";
				$file=$logo;
			}elseif(!empty($license)){
				$path=getFilePath($company_id,'./Uploads/Company/','info')."business_license.jpg";
				$file=$license;
			}else{
				$this->error('请选择文件');
			}
			$hz=end(explode('.',$file['name']));
			if (in_array($hz,array('png','jpg','jpeg','gif'))) {
				$path=ltrim($path);
				self::createPath(dirname($path));
				if (move_uploaded_file($file['tmp_name'], $path)) {
					$this->success('操作成功!');
				}else{
					$this->error('上传失败');
				}
			}else{
				$this->error('错误的文件类型');
			}
			
		}
		public function createPath($path){
		    return is_dir($path) or (self::createPath(dirname($path)) and mkdir($path,0755));
		}
		/**
		 * [upload 营业执照 服务商logo上传]
		 * @return [type] [description]
		 */
		public function upload()
		{
			$Upload = new \Think\Upload(C('IMG_UPLOAD'));
			$path = rtrim(mkFilePath(0,$Upload->rootPath,'temp'),'/');
			$path = str_replace($Upload->rootPath,'',$path);
			$Upload->subName = $path;
			$action_type = I('get.action_type');
			$prefix = $action_type == 1 ? 'business_lisence_' : 'service_logo_';
			$token = I('get.token');
			$Upload->saveName = $prefix.$token;
			$upload->saveExt = 'jpg';
			$info = $Upload->uploadOne($_FILES['file']);
			if(!$info)
				$this->ajaxReturn(array('state'=>1,'msg'=>$Upload->getError()));
			$url = $Upload->rootPath.$info['savepath'].$info['savename'];
			S($prefix.$token,$url,86400);
			$this->ajaxReturn(array('state'=>0,'msg'=>'上传成功!'));
		}

		/**
		 * [modifyPassword 修改密码]
		 * @return [type] [description]
		 */
		public function modifyPassword()
		{
			$result = D('ServiceAdmin')->modifyPassword();
			if($result) $this->ajaxReturn(array('state'=>0,'msg'=>'修改成功'));
			else $this->ajaxReturn(array('state'=>1,'msg'=>'修改失败'));
		}
	}
?>