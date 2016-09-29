<?php
namespace Admin\Controller;
use Admin\Think;

class CompanyInfoController extends ThinkController{
	private $model = 'company_info'; /*在OneThink模型管理中查看自己模型标识（不是名称）修改此处*/
	
	/**
	 * index function
	 * 首页
	 * @param int $p 分页页码
	 * @return void
	 * property
	 **/
	public function index($p = 0){
		//$zoning = S('ptimeZoning');
		$this->lists($this->model,$p); /*系统会调用View/company_info/index.html来显示*/
	}
	
	/**
	 * lists function
	 * 列表
	 * @param string $model 模型名称
	 * @param int $p 分页页码
	 * @return void
	 * 
	 **/
	public function lists( $model = null , $p = 0){
		parent::lists( $model ,$p ); /*系统会调用View/	company_info/lists.html来显示*/
	}
	
	/**
	 * add function
	 * 新增
	 * @param string $model 模型名称
	 * @return void
	 * 
	 **/
	public function add( $model = null ){
		$model = M('Model')->getByName( $this->model ); /*通过Model名称获取Model完整信息*/
		parent::add( $model['id'] ); /*系统会调用View/	company_info/add.html来显示*/
	}
	
	/**
	 * getCompanyUserByCuid function
	 * 根据企业用户id获取企业用户以及企业信息
	 * @access protected
	 * @param int $cuid 企业用户id
	 * @param bool $isRelation 是否关联查询
	 * @return array 企业用户信息数组
	 * @author rohochan <rohochan@gmail.com>
	 **/
	protected function getCompanyUserByCuid($cuid,$isRelation= true){
		$companyUser = D('User')->relation($isRelation)->field(true)->getById($cuid);
		return $companyUser;
	}
	
	/**
	 * edit function
	 * 编辑
	 * @param string $model 模型名称
	 * @param int $id 数据id
	 * @return void
	 * 
	 **/
	public function edit( $model = null, $id = 0 ){
		if (IS_POST)
		{
			$id = intval(I('post.id' , 0));
			$m = M('company_info','zbw_');
			$data = $m->create();
			
			$res = $m->where("id={$id}")->save($data);
			//echo $m->getLastSql();
			//行为日志
			action_log('status_companyinfo', 'company_info',  $id, UID);
			if (false !== $res) {
				$bankData = array();
				$bankData['company_id'] = $id;
				$bankData['bank'] = I('post.bank');
				$bankData['account'] = I('post.account');
				$companyBank = D('CompanyBank');
				$companyBankSaveResult = $companyBank->saveCompanyBank($bankData);
				$res = $m->where("id={$id}")->find();
				if ($res['user_id']) {
					//同步数据到redis
					$companyUser = $this->getCompanyUserByCuid($res['user_id']);
					unset($companyUser['password']);
					$redis = initRedis();
					$redisKey = 'zby:com:user:'.$res['user_id'];
					//$redisres = $redis->hGetAll($redisKey);
					$redisres = $redis->hSet($redisKey,'mCuid',$res['user_id']);
					$redisres = $redis->hSet($redisKey,'mCid',$res['id']);
					$redisres = $redis->hSet($redisKey,'mCompanyUser',json_encode($companyUser));
				}
				$this->success('操作成功' , U('CompanyInfo-index'));
			}else {
				$this->error('操作失败');
			}
		}
		$id = intval(I('get.id' , ''));

		//$logo=APP_DOMAIN.getFilePath($id,'Uploads/Company/','info')."service_logo.jpg";
		//if (file_exists($logo)) {
		//	$this->assign('isServiceProvider',1);
		//}

		$m = M('company_info','zbw_');
		$rs = $m->where("id={$id}")->find();
		$rs['filedir'] = getFilePath($rs['id'] , 'Uploads/Company/' , 'info');
		$rs['companyFile'] = get_companyFile_by_companyId($id);
		$companyBank = D('CompanyBank');
		$rs['companyBank'] = $companyBank->field(true)->getByCompanyId($id);
		
		$user = D('User');
		$userResult = $user->field(true)->getById($rs['user_id']);
		if ($userResult) {
			if (2 == $userResult['type']) {
				$this->assign('isServiceProvider',1);
			}else {
				$this->assign('isServiceProvider',0);
			}
		}
		
		$this->assign('rs' , $rs)->assign('id' , $id);
		$this->meta_title = '编辑企业信息';
		$this->display();
	}
	/**
	 * del function
	 * 删除
	 * @param string $model 模型名称
	 * @param string $ids 数据ids
	 * @return void
	 * 
	 **/
	public function del( $model = null, $ids=null ){
		$model = M('Model')->getByName( $this->model ); /*通过Model名称获取Model完整信息*/
		parent::del( $model['id'], $ids ); /*没有页面，只有Ajax提示返回，不需要View/	company_info/del.html*/
	}
	
	/**
	 * 设置一条或者多条数据的状态
	 * @author huajie <banhuajie@163.com>
	 */
	public function setStatus($model='Keyword'){
		return parent::setStatus($model);
	}


	/**
	 * 省市联动
	 * @param [type] $[code] [省名称]
	 */
	 function select_area($code = ''){      
		if(IS_AJAX && is_numeric($code)){
			$area = getZoning();            
			$_area = array();
			foreach ($area as $key => $value) {
				if($key%10000 == 0){
					if( $key - $code < 1000000  && $key - $code  > 0 ){                     
						$_area[$key]['name'] =  $value['name'];
						$_area[$key]['id'] = $key;
					}
				}   
			}	        
		  
			if(!empty($_area)){
				//echo json_encode($_area, JSON_UNESCAPED_UNICODE);die();
				$this->ajaxReturn($_area);
			}else{
				$this->error('错误！');
			}
		}else{ $this->error('错误！');}
	}



}