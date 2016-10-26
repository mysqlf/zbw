<?php
	namespace Admin\Model;
	use Think\Model;
	/**
	 * 服务商管理
	 * @author  JieJie <heihei_yh@163.com>
	 */
	class ServiceAdminModel extends Model
	{	
		protected $tablePrefix = 'zbw_';
		/**
		 * [addService 添加服务商入口]
		 * @return boolean 
		 */
		public function addService()
		{
			$this->startTrans();
			//添加企业用户
			$user_result = $this->_addUser();
			//添加企业信息

			$info_result = $this->_addCompanyInfo($user_result);
			//添加服务商
			$service_result = $this->_addService($info_result,$user_result);
			if($user_result && $info_result && $service_result)
			{
				//添加服务商默认焦点图
				$img_result = $this->_defaultImg($info_result,$service_result);
				//添加默认频道
				$category_result = $this->_defaultCategory($info_result);
				if(!$img_result || !$category_result)
				{
					$this->rollback();
					return false;
				}
				$this->commit();
				//移动上传图片到相应目录
				$this->_moveImgs($info_result);
				return true;
			}
			return false;
			$this->rollback();
		}

		/**
		 * [_defaultCategory 添加默认频道]
		 * @param  [type] $company_id [企业id]
		 * @return [int]             [最先添加id]
		 */
		private function _defaultCategory($company_id)
		{
			$Category = M('service_article_category','zbw_');
			$company_id = intval($company_id);
			$title = array('行业资讯','公司资讯','社保资讯');
			for($i=0;$i<3;$i++)
			{
				$data[$i]['company_id'] = $company_id;
				$data[$i]['title'] = $title[$i];
				$data[$i]['create_time'] = time();
				$data[$i]['update_time'] = time();
				$data[$i]['status'] = 1;
			}
			return $Category->addAll($data);
		}
		/**
		 * [_defaultImg 添加默认焦点图]
		 * @param  [type] $company_id    [企业信息id]
		 * @param  [type] $service_admin [服务商主账号id]
		 * @return [type]                [description]
		 */
		private function _defaultImg($company_id,$service_admin)
		{
			$Thumb = M('service_thumb','zbw_');
			$banner_len = 3;
			//首页banner图
			for($i=0;$i<$banner_len;$i++)
			{
				$data[$i]['company_id'] = intval($company_id);
				$data[$i]['url'] = '/Application/Home/Assets/img/bg.jpg';// $_SERVER['HTTP_HOST'].
				$data[$i]['place'] = 1;
				$data[$i]['admin_id'] = intval($service_admin);
				$data[$i]['create_time'] = date('Y-m-d H:i:s');
			}
			//合作客户默认图
			$data[$i]['company_id'] = intval($company_id);
			$data[$i]['url'] = '/Application/Home/Assets/img/hzkh.png';
			$data[$i]['place'] = 2;
			$data[$i]['admin_id'] = intval($service_admin);
			$data[$i]['create_time'] = date('Y-m-d H:i:s');
			//社保大厅焦点图
			$i+=1;
			$data[$i]['company_id'] = intval($company_id);
			$data[$i]['url'] = '/Application/Home/Assets/img/sbdt_banner.jpg';
			$data[$i]['place'] = 3;
			$data[$i]['admin_id'] = intval($service_admin);
			$data[$i]['create_time'] = date('Y-m-d H:i:s');
			return $Thumb->addAll($data);
		}
		/**
		 * [_addUser 添加企业用户]
		 * @return  boolean 
		 */
		private function _addUser()
		{
			$User = M('User','zbw_');
			$data = $User->create();
			$pattern = '/^[(\w)|(\d)|(\-)|(_)]{6,20}$/';
			if(!preg_match($pattern, $data['username'])) 
			{
				$this->error = '主账号格式不正确!';
				return false;
			}
			$pword_len = strlen($data['password']);
			if($pword_len<6 || $pword_len>20)
			{
				$this->error ='密码格式错误!';
				return false;
			}
			$r_password = I('post.r_password');
			if($r_password != $data['password'])
			{
				$this->error = '两次密码不一致!';
				return false;
			}
			$result = $User->where(array('username'=>$data['username']))->count();
			if($result>0)
			{
				$this->error = '该账号已被注册,请更换主账号名称';
				return false;
			}
			$data['password'] = md5($data['username'].':'.$data['password']);
			$data['type'] = 2;
			$data['create_time'] = date('Y-m-d H:i:s');
			return $User->add($data);
		}

		/**
		 * [_addCompanyInfo 添加企业信息]
		 * @param [int] $company_user_id [企业用户id]
		 * @return int [企业信息id]
		 */
		private function _addCompanyInfo($company_user_id)
		{
			//if(!$company_user_id) return false;
			$CompanyInfo = M('company_info','zbw_');
			$data = $CompanyInfo->create();
			if(!isTel($data['contact_phone']))
			{
				$this->error = '手机号格式不正确！';
				return false;
			}
			if(!emailFormat($data['email']))
			{
				$this->error = '联系人邮箱格式不正确！';
				return false;
			}
			// $company_name_len = strlen($data['company_name']);
			// if($company_name_len<4 || $company_name_len>10)
			// {
			// 	$this->error = '企业简称格式不正确！';
			// 	return false;
			// }
			// $full_name_len = strlen($data['full_name']);
			// if($full_name_len<4 || $full_name_len>100)
			// {
			// 	$this->error = '企业全称格式不正确！';
			// 	return false;
			// }
			// $contact_name_len = strlen($data['contact_name']);
			// if($contact_name_len<2 || $contact_name_len>20)
			// {
			// 	$this->error = '联系人名称格式不正确！';
			// }
			if(isset($_POST['location1']))
			{
				$data['company_address'] = showAreaName(I('post.location')).showAreaName(I('post.location1')).$data['company_address'];
				$data['location'] = I('post.location1','','intval');
			}else{
				$data['company_address'] = showAreaName1(I('post.location')).$data['company_address'];
			}
			$data['user_id'] = $company_user_id;
			return $CompanyInfo->add($data);
		}

		/**
		 * 添加服务商
		 * @param int 企业信息id
		 * @param int 企业用户id
		 * @return int  新增服务商id
		 */
		private function _addService($company_id,$user_id)
		{
			$data['company_id'] = $company_id;
			$data['user_id'] = $user_id;
			$data['name'] = I('post.username','');
			$data['full_name']=I('post.full_name');
			$data['telphone'] = I('post.contact_phone');
			$data['group'] = 1;
			$data['type'] = 1;
			$data['create_time'] = date('Y-m-d H:i:s');
			return $this->add($data);
		}

		/**
		 * [lists 获取服务商主账号列表]
		 * @return [array] 
		 */
		public function lists()
		{
			$condition['s.type'] = 1;
			$condition['s.state'] = array('neq',-9);
			$count   = $this->alias('s')->where($condition)->count();
			$Page = new \Think\Page($count,25);
			$data['page'] = $Page->show();
			$data['list'] = $this->alias('s')
				->join('zbw_company_info as c ON s.company_id = c.id')
				->join('zbw_user u ON u.id=s.user_id')
				->field('s.id,s.company_id,s.user_id,s.name,s.telphone,c.contact_name,u.state')
				->where($condition)
				->order('s.id DESC')
				->limit($Page->firstRow.','.$Page->listRows)
				->select();
			$Service_product = M('service_product','zbw_');
			$map['p.state'] = 1;
			foreach ($data['list'] as $key => $value) 
			{
				//团队账号数目
				$data['list'][$key]['account_num'] = $this->where('company_id='.$value['company_id'])->count();
				//服务企业数
				$map['p.company_id'] = $value['company_id'];
				$data['list'][$key]['service_num'] = $Service_product->alias('p')
					->join('zbw_service_product_order as o ON p.id = o.product_id')
					->where($map)
					->count('distinct o.user_id');
			}
			return $data;
		}

		/**
		 * [modifyStatus 修改服务商状态]
		 * @return [boolean] 
		 */
		public function modifyStatus()
		{
			$ids = I('post.id');
			$state = I('get.state',1,'intval');
			switch ($state) 
			{
				case '2':
					$state = -1;
					break;
				case '3':
					$state = -9;
					break;
				case '1':
				default:
					$state = 1;
					break;
			}
			$user = M('user', $this->tablePrefix);
			$_user_id = S('state_user_id');
			if(empty($_user_id)) $_user_id = '';
			for($i=0;$i<count($ids);$i++){
					$info = $this->field('company_id,user_id')->find($ids[$i]);					
					$where = "id={$info['user_id']} or father_id={$info['user_id']}";
					$this->where(array('id'=>$ids[$i]))->save(array('state'=>$state));#主表禁用 
					$user->where($where)->save(array('state'=>$state));
					$user_id = $user->field('id')->where($where)->select();//group_concat(id) 
					foreach ($user_id as $key => $value) {
						if($state == 1){
							$_user_id = str_replace(','.$value['id'], '', $_user_id);
						}else{
							if(strpos($_user_id, ','.$value['id']) == false){
								$_user_id .= ','.$value['id'];
							}
						}
					}
					if($state == -9 ) $state = -1;
					$result = M('CompanyInfo', 'zbw_')->where("id={$info['company_id']}")->save(array('audit'=>$state));
			}
			if(empty($_user_id)) $_user_id = null;
			S('state_user_id', $_user_id);
			return $result!==false ? true : false;
		}

		/**
		 * [showCompany 获取服务商服务企业信息]
		 * @return [array] [description]
		 */
		public function showCompany()
		{
			$company_id = I('get.company_id',0,'intval');
			if(!$company_id)
			{
				$this->error = '数据错误!';
				return false;
			}
			$map['p.state'] = 1;
			$map['p.company_id'] = $company_id;

			//获取服务企业用户id
			$user_id_arr = M('service_product as p','zbw_')->join('zbw_service_product_order as o ON p.id = o.product_id')->where($map)->field('distinct o.user_id')->select();
			$user_id = array();
			foreach ($user_id_arr as $value) 
			{
				$user_id[] = $value['user_id'];
			}
			$condition['user_id'] = array('in',$user_id);
			$CompanyInfo = M('company_info','zbw_');
			$count = $CompanyInfo->where($condition)->count();
			$Page = new \Think\Page($count,25);
			$data['page'] = $Page->show();
			$data['list'] = $CompanyInfo->where($condition)->field('*')->limit($Page->firstRow.','.$Page->listRows)->select();
			//数据处理
			$property = C('PROPERTY');
			$industry = C('INDUSTRY');
			$people_num = array(1=>'1~100','101~200','201~500','501~1000','1001~2000','2001~');
			foreach ($data['list'] as $key=>$value) 
			{
				$data['list'][$key]['contact_sex'] = $value['contact_sex'] == 1 ? '男' : '女';
				$data['list'][$key]['property'] = $property[$value['property']];
				$data['list'][$key]['location'] = rtrim(showAreaName1($value['location']),'-');
				$data['list'][$key]['company_location'] = rtrim(showAreaName1($value['company_location']),'-');
				$data['list'][$key]['industry'] = $industry[$value['industry']];
				$data['list'][$key]['employee_number'] = $people_num[$value['employee_number']];
			}
			return $data;
		}

		/**
		 * [serviceInfo 服务商详情]
		 * @return [array] [description]
		 */
		public function serviceInfo()
		{
			$id = I('get.id',0,'intval');
			$map['s.id'] = $id;
			$data = $this->alias('s')->join('zbw_company_info as c ON s.company_id = c.id')->where($map)->find();
			if(!$data) return false;
			$property = C('PROPERTY');
			$industry = C('INDUSTRY');
			$people_num = array(1=>'1~100','101~200','201~500','501~1000','1001~2000','2001~');
			$data['property'] = $property[$data['property']];
			$data['industry'] = $industry[$data['industry']];
			$data['employee_number'] = $people_num[$data['employee_number']];
			return $data;
		}

		/**
		 * [_moveImgs 上传成功移动文件到正式目录]
		 * @param  [int] $company_id 企业信息id
		 */
		private function _moveImgs($company_id)
		{
			$token = I('post.token');
			//营业执照路径
			$lisence_path = S('business_lisence_'.$token);
			//服务商logo路径
			$service_logo_path = S('service_logo_'.$token);
			if($lisence_path || $service_logo_path)
			{
				$path = mkFilePath($company_id,'./Uploads/Company/','info');
			}
			$lisence_path && rename($lisence_path, $path.'business_license.jpg');
			$service_logo_path && rename($service_logo_path, $path.'service_logo.jpg');
		}

		/**
		 * [modifyPassword 修改密码]
		 * @return [type] [description]
		 */
		public function modifyPassword()
		{
			$password = I('post.password');
			$re_password = I('post.re_password');
			$user_id = I('post.user_id','','intval');
			if($password != $re_password)
			{
				$this->error = '输入的密码两次不一致';
				return false;
			}
			$password_len = strlen($password);
			if($password_len<6 || $password_len>20)
			{
				$this->error = '密码格式错误';
				return false;
			}
			$User = M('user','zbw_');
			$username = $User->where('id='.$user_id)->getField('username');
			$save_data['password'] = md5($username.':'.$password);
			return $User->where('id='.$user_id)->save($save_data);
		}
	}
?>