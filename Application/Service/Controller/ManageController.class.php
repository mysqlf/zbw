<?php

namespace Service\Controller;

class ManageController extends ServiceBaseController
{
    protected $_serviceInfo;
    protected $_ServiceAdmin;
    protected $_account;
    protected $_bank;    

    protected function _initialize()
    {
        parent::_initialize(); 
        $this->_serviceInfo = D('Admin')->adminInfo($this->_cid, $this->_uid );
        $this->_ServiceAdmin = D('ServiceAdmin');
        if(IS_POST){
            $this->_account['id'] = I('post.id', '0');
            $this->_account['username'] = I('post.username', '');
            $this->_account['password'] = I('post.password', '');
            $this->_account['name'] = I('post.name', '');
            $this->_account['telphone'] = I('post.telphone', '');
            $this->_account['qq'] = I('post.qq', '');
            $this->_account['state'] = I('post.state', '0', 'intval');
            $this->_account['auth'] = I('post.auth', '');
            $this->_account['group'] = I('post.group', '', 'intval');

            $this->_bank['id'] = I('post.id', '0');
            $this->_bank['bank'] = I('post.bank', '');
            $this->_bank['account_name'] = I('post.account_name', '');
            $this->_bank['branch'] = I('post.branch', '');
            $this->_bank['account'] = I('post.account', '');
        }
    }

    public function adminList()
    {
  
        $d = D('ServiceAdmin');
        $result = $d->childAccountlist($this->_cid);
        $this->assign('result' , $result);
        $this->display('Set/team_manage');
    }

    /*
     * 返回子账号信息
     */
    public function childAccountInfo()
    {
        if(IS_POST)
        {
            if( $this->_serviceInfo['type'] != 1 || empty($this->_serviceInfo['id']))  $this->ajaxReturn (array('status'=>-100001));//
            $result =  $this->_ServiceAdmin->childAccountInfo( $this->_account,$this->_AccountInfo);
            $this->ajaxReturn ($result);
        }
    }

    /*
     * 添加子账号
     */
    public function addChildAccount()
    {
        if(IS_POST) 
        {
            if( $this->_serviceInfo['type'] != 1)  $this->ajaxReturn (array('status'=>-100001));
            $this->_account['type'] = 2;
            $this->_account['company_id'] = $this->_AccountInfo['company_id'];
            $this->_account['create_time'] = date('Y-m-d H:i:s',time());
            if(preg_match("/^[\u4e00-\u9fa5]+$/",$this->_account['username']))  $this->ajaxReturn(-1, '账号不能包含中文');
            $result =  $this->_ServiceAdmin->addChildAccount( $this->_account, $this->_AccountInfo);
            $this->ajaxReturn ($result);
        }
    }

    /* 删除帐号 */
    public function delAccount()
    {
        if(IS_POST)
        {
            if( $this->_serviceInfo['type'] != 1 || empty($this->_account['id']))  $this->ajaxReturn (array('status'=>-100001));
            $result =  $this->_ServiceAdmin->delAccount( $this->_account, $this->_AccountInfo);
            $this->ajaxReturn ($result);
        }
    }

    /*
     * 银行卡信息
     */
    public function bankInfo()
    {
        $result = $this->_ServiceAdmin->bankInfo($this->_AccountInfo, $this->_bank);
        $this->assign('result', $result)->display('Set/bank_info');
        
    }
}