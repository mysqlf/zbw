<?php
namespace Service\Controller;
use Think\Controller;
/** 
 * 基础类
 */
class ServiceBaseController extends Controller{
	protected $_AccountInfo;
	protected $_uid = '';
	protected $_cid = '';
    protected $_amin;
    protected $_subUserId;


	protected function _initialize()
    {
         $d = D('Admin');
         $d->userAuth();
         $this->_admin = $d->loginInfo();
         $this->_uid = $this->_admin['user_id'];
         $this->_cid = $this->_admin['company_id'];
         $this->_AccountInfo = $this->_admin;
         //$this->_subUserId = $this->blongToService($this->_cid);
         publicFooter($this->_cid);
    } 

    /**
     * 权限数组
     */
    public function aouth(){
        return array(
            'User'=> '账号设置',
            'Article'=> '文章管理',
            'Product'=> '套餐管理',
            'PayOrder'=> '订单管理',
            'Bill'=> '对账单管理',
            'Link'=> '友情链接',
            'Members'=> '团队管理',
            'Manage'=> '账号管理',
            'Service'=> '首页',
            'DiffAmount'=> '差额管理',
           // 'Person'=> '参保人列表',
            //'CompanyDeclare'=> '企业报增',
            //'PersonDeclare'=> '个人',
            //'Salay'=> '代发工资',
            //'Insurance'=> '客服报增',
            'Business'=>'业务管理',
            'Rules'=>'缴纳规则',
            'Customer'=>'客户管理',

       );
    }

    /**
     * 服务商下的所有user_id
     * @return [company_id] [商务商信息id]
     * @param   $[type] [<企业 个人>] 1 3
     */
    public function blongToService($company_id = null, $type=1){
        $company_id or $company_id = $this->_cid;
        $where = array('sp.company_id'=> $company_id);
        $serviceInfo = $this->serviceInfo();
        if($serviceInfo['group'] == 3){
            $where['sp.admin_id'] = getServiceAdminId($this->_uid);
        }
        if($type == 1)
            $where['su.type'] = 1;
        else
            $where['su.type'] = 3;
        //$where['su.type'] = array('su.type'=> array('neq', 2));    
        $result = M('user_service_provider')->alias('sp')->field('group_concat(sp.user_id) user_id')
                        ->join('zbw_user su ON su.id=sp.user_id')
                        ->where($where)->select();        
     //  echo M('user_service_provider')->getLastSql();die();
        if(empty($result[0]['user_id']))
            return 0;
        else
            return $result[0]['user_id'];
    }

    /**
     *服务商所有客服
     *
     */
    public function serviceGroup($group=3){
        return M('service_admin')->field('id,name')->where(array('company_id'=> $this->_cid, 'group'=> $group, 'state'=>1))->select();
    }    

    /**
     *服务商账号信息 权限
     */
    public function serviceInfo(){
        return D('Admin')->adminInfo($this->_cid, $this->_uid );//M('service_admin')->field('group,type')->where(array('user_id'=> $this->_uid, 'company_id'=> $this->_cid))->find();

    }

    /**
     * 客服名称
     */
    public function serviceAdminName($admin_id){
        return M('service_admin')->getFieldById($admin_id,  'name');
    }

    /**
     * 差额
     */
    public function  DiffAmount($user_id){
        return M('user_service_provider')->field('diff_amount')->where(array('user_id'=> $user_id, 'company_id'=> $this->_cid))->find();
    }

    /**
     * 所有产品套餐
     */
    public function productAllList(){
           return M('service_product')->field('id,name')->where('company_id = '.$this->_cid.' AND state =1')->select();
    }

}
