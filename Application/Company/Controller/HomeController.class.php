<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: rohochan <rohochan@gmail.com> <http://blog.rohochan.com>
// +----------------------------------------------------------------------

namespace Company\Controller;
use Think\Controller;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeController extends Controller {
	protected $mCuid = 0;//企业用户id
	protected $mCid = 0;//企业id
	protected $mCompanyUser = array();//企业用户信息
	protected $mMemberStatus = -1;//是否会员 -1不是 0过期 1:社保会员 2:公积金会员 4:代发工资会员 3:1社保会员+2公积金会员 5:1社保会员+4代发工资会员 6:2公积金会员+4代发工资会员 7:1社保会员+2公积金会员+4代发工资会员
	protected $mMemberStatusArray = array();//会员状态数组
	
	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
		/*header("HTTP/1.0 404 Not Found");//使HTTP返回404状态码
		//header("status: 404 Not Found");
		//header( $_SERVER['SERVER_PROTOCOL']." 404 Not Found", true );
		//header( $_ENV['SERVER_PROTOCOL']." 404 Not Found", true );
		$this->display("Public:404"); */
	}


	protected function _initialize(){
		/* 读取站点配置 */
		$config = api('Config/lists');
		C($config); //添加配置
		
		//session('company_user',array('user_id'=>55,'company_id'=>55,'username'=>'小霸王','company_name'=>'大霸王'));
		//dump(session('company_user'));
		if(!session('?company_user')){
			//自动登录
			//$companyUser = new \Home\Model\CompanyUserModel();
			//$companyUser->checkAutoLogin();
		}else{
			if(strtolower(CONTROLLER_NAME)=='member' && (strtolower(ACTION_NAME)=='firmregister' || strtolower(ACTION_NAME)=='firmlogin')){
				$this->redirect('Index/index');
			}
		}
		if(!C('WEB_SITE_CLOSE')){
			$this->error('站点已经关闭，请稍后访问~');
		}
		
		if(('Company' == MODULE_NAME && 'Pay' == CONTROLLER_NAME && 'jdpaynotify_url' == ACTION_NAME) || ('Company' == MODULE_NAME && 'Pay' == CONTROLLER_NAME && 'notify_url' == ACTION_NAME)){
			return;
		}

		if (!('Company' == MODULE_NAME && 'Account' == CONTROLLER_NAME && 'validateEmail' == ACTION_NAME)) {
		//验证邮箱不需要校验登录状态
			set_redirect_url(json_encode(array('referer'=>__SELF__,'controller'=>CONTROLLER_NAME)));
			//dump(get_redirect_url());
			$user = $this->login();
			$this->mCuid = $user['user_id'];
			$redis = initRedis();
			$redisKey = 'zby:com:user:'.$this->mCuid;
        	$comUser = $redis->hGetAll($redisKey);
			//echo '<br/><br/><br/><br/><br/>';
			//dump($comUser);
			if ($comUser) {
				$this->mCompanyUser = json_decode($comUser['mCompanyUser'],true);
				$this->mCid = $comUser['mCid'];
				//$this->mMemberStatus = $comUser['mMemberStatus'];
				//$this->mMemberStatusArray = json_decode($comUser['mMemberStatusArray'],true);
				$this->getMemberStatus($this->mCuid);
				
				/*$redis->hDel($redisKey,'mCuid');
				$redis->hDel($redisKey,'mCid');
				$redis->hDel($redisKey,'mCompanyUser');
				$redis->hDel($redisKey,'mMemberStatus');
				$redis->hDel($redisKey,'mMemberStatusArray');*/
			}else {
				$this->mCompanyUser = $this->getCompanyUserByCuid($this->mCuid);
				unset($this->mCompanyUser['password']);
				$this->mCid = $this->mCompanyUser['CompanyInfo']['id'];
				$this->getMemberStatus($this->mCuid);
				
				//TODO:修改企业信息同步到redis上,是否每次都查询会员状态
				$redisres = $redis->hSet($redisKey,'mCuid',$this->mCuid);
				$redisres = $redis->hSet($redisKey,'mCid',$this->mCid);
				$redisres = $redis->hSet($redisKey,'mCompanyUser',json_encode($this->mCompanyUser));
				//$redisres = $redis->hSet($redisKey,'mMemberStatus',json_encode($this->mMemberStatus));
				//$redisres = $redis->hSet($redisKey,'mMemberStatusArray',json_encode($this->mMemberStatusArray));
			}
			
			/*dump($this->mCuid);
			dump($this->mCid);
			dump($this->mCompanyUser);
			dump($this->mMemberStatus);
			dump($this->mMemberStatusArray);*/
			
			//如果协议没有审核则只能访问企业信息页面
			if (0 == $this->mCompanyUser['CompanyInfo']['audit']||-1 == $this->mCompanyUser['CompanyInfo']['audit']) {
				if (!(('Company' == MODULE_NAME && 'Account' == CONTROLLER_NAME ) || 'upload' == ACTION_NAME || ('Company' == MODULE_NAME && 'Information' == CONTROLLER_NAME  && 'msgCount' == ACTION_NAME))) {
					redirect(U('Company/Account/companyInfo'));
				}
			}
			$this->assign('companyUser',$this->mCompanyUser);
			$this->assign('memberStatus',$this->mMemberStatus);
			$this->assign('mMemberStatusArray',$this->mMemberStatusArray);
		}
	}

	/* 用户登录检测 */
	protected function login(){
		/* 用户登录检测 */
		//is_company_login() || $this->error('您还没有登录，请先登录！', U('Home/Member/firmLogin'));
		($companyInfo = is_company_login()) || $this->redirect('Home/Member/firmLogin');
		return $companyInfo;
	}
	
	/**
	 * getCompanyInfoByCuid function
	 * 根据企业用户id获取企业
	 * @access protected
	 * @param int $cuid 企业用户id
	 * @return array 企业信息数组
	 * @author rohochan <rohochan@gmail.com>
	 **/
	protected function getCompanyInfoByCuid($cuid){
		$companyInfo = D('CompanyInfo')->field(true)->getByCompanyId($cuid);
		return $companyInfo;
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
	 * getMemberStatus function
	 * 检测是否会员
	 * @access protected
	 * @param int $cuid 企业用户id
	 * @return int 是否会员 -1不是 0过期 1是
	 * @author rohochan <rohochan@gmail.com>
	 **/
	protected function getMemberStatus($cuid){
		$companyUser = D('User');
		$this->mMemberStatusArray = $companyUser->isMember($cuid);
		$this->mMemberStatus = $this->mMemberStatusArray['status'];
		return $this->mMemberStatus;
	}

}
