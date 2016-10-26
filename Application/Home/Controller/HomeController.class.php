<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeController extends Controller {
    protected $heifei_code = 24010000;
    protected $heifei_name = '合肥';
    public $kf;
    protected $_Cid;
    protected $_CompanyName;

	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
	}

   
    protected function _initialize(){
       /* if(!session('?company_info')){
            //自动登录
           //D('CompanyUser')->checkAutoLogin();
        }else{
            if(strtolower(CONTROLLER_NAME)=='member' && (strtolower(ACTION_NAME)=='firmregister' || strtolower(ACTION_NAME)=='firmlogin')){
                $this->redirect('Index/index');
            }
        }*/
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置
        if(!C('WEB_SITE_CLOSE')){
            $this->error('站点已经关闭，请稍后访问');
        }
        $this->_Cid = I('get.cid', '0');
      //  $cid = I('get.cid')?:session('cid');
        if ($this->_Cid) {
        	//session('cid',$cid);
        	$companyInfo = D('companyInfo');
        	$serviceCompanyInfoResult = $companyInfo->getById($this->_Cid);
        	if ($serviceCompanyInfoResult) {
        		//获取服务商信息
        		$path = getFilePath($serviceCompanyInfoResult['id'],'./Uploads/Company/','info');
        		$serviceCompanyInfoResult['service_logo'] = $path.'service_logo.jpg';
                $this->_CompanyName = $serviceCompanyInfoResult['company_name'];
        		$this->assign('serviceCompanyInfoResult',$serviceCompanyInfoResult);
        		//获取服务商客服列表
        		$serviceAdmin = D('ServiceAdmin');
        		$serviceAdminResult = $serviceAdmin->field('id,company_id,user_id,name,type,telphone,qq')->where(['company_id'=>$this->_Cid,'state'=>1,'group'=>3])->select();
        		
        		if ($serviceAdminResult) {
			        //随机获取一个元素的key
			        $oneServiceAdminResult = array_rand($serviceAdminResult);
        		}
        		//全部客服
		        $this->assign('serviceAdminResult',$serviceAdminResult);
		        //一条客服记录
		        $this->assign('oneServiceAdminResult',$serviceAdminResult[$oneServiceAdminResult]);
        	}

            $info = S('home'.$this->_Cid);
            if(empty($info)){
                 $result = M('company_info', 'zbw_')->alias('ci')->field('company_address,tel_city_code,tel_local_number, contact_phone')->where(array('id'=> $this->_Cid))->find();

                 S('home'.$this->_Cid, json_encode($result), 24*3600);
            }else{
                $this->assign('publicFooter',json_decode($info, true));
            }     
      
        }

        /*
        //获取客服
        $qq_kf = F('qqkf','',C('WEB_SITE_PATH'));
        //随机获取一个元素的key
        $one_qq_kf = array_rand($qq_kf,1);
        //全部客服
        $this->assign('qq_kf',$qq_kf);
        //一条客服记录
        $this->assign('one_kf_key',$one_qq_kf);
		*/
    }

	/* 用户登录检测 */
	protected function login(){
		/* 用户登录检测 */
		is_login() || $this->error('您还没有登录，请先登录！', U('User/login'));
	}

	
}
