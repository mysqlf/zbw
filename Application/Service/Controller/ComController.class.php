<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Service\Controller;
use Think\Controller;
/**
* 
*/
class ComController extends Controller
{
    private $_AccountInfo;
    private $_Company;
    protected function _initialize()
    {
        $this->_AccountInfo = isloginstate();
        $this->_Company =  D('Company');
        $this->assign('auth', $this->_AccountInfo); 
    }

    /**
     * [ComInfo 公司详情]
     */
    public function comInfo(){
        $com['company_id']=intval(I('get.company_id'));
        $m=D('ServiceOrder');
        $cominfo=$m->comInfo($com, $this->_AccountInfo);
        $companyFile = get_companyFile_by_companyId($cominfo['company_id']);
        $this->assign('result',$cominfo)->assign('companyFile',$companyFile);
        $this->display('Bill/comInfo');
    }
    /**
     * [comFilter 筛选公司]
     * @return [type] [description]
     */
    public function comFilter(){
        $audit=intval(I('post.audit'));
    }
    /**
     * [comEmployee 公司在保员工]
     * @return [type] [description]
     */
    public function comEmployee(){
        $com['company_id']=intval(I('get.company_id'));
        $employee=$this->_Company->getComEmployee($com,$this->_AccountInfo);
        $this->assign('employee',$employee);
    }
    /**
     * [regCom 注册公司列表]
     * @return [type] [description]
     */
    public function regCom(){
        $result=$this->_Company->regcomlist($this->_AccountInfo);
        $this->assign("list",$result)->display('Service/regcom');
    }
    /**
     * [comAudit 公司资质审核提交]
     * @return [type] [description]
     */
    public function comAudit(){
        if (IS_POST) {
            $com['company_id']=intval(I('post.company_id'));
            $key=I('post.key','','htmlspecialchars');
            $value=intval(I('post.value'));
            $m=M('CompanyInfo');
            $result=$m->where($com)->setField($key,$value);
        }else{

        }
       
    }

    /**
     * [comContract 公司录入]
     * @return [type] []
     */
    public function comContractEntry(){
        if(IS_POST){
            #写入合同id合同时间以及修改后的服务价格
        }else{
            #调取显示公司信息与公司购买的产品
            $this->display();
        }
    }
}