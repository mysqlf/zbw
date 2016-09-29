<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/15
 * Time: 14:57
 */
namespace Service\Controller;
/**
 * 首页
 */
class ServiceController extends ServiceBaseController
{


	protected function _initialize()
    {
        parent::_initialize();
    }
    public function index()
    {   
        $productAllList = $this->productAllList();
        foreach ($productAllList as $key => $value) {
           $product_id .= $value['id'].',';
        }
        $product_id = rtrim($product_id, ',');
        $blongToService = $this->blongToService();
        if(!empty($blongToService)){
            $res = D('Service')->incTotal($blongToService, $product_id);
         }

        $userInfo = session('user'); 
        $auth = array_unique(json_decode($userInfo['auth'], true)); 

        $where = "u.type=1 AND ci.user_id in ({$blongToService}) AND usp.company_id={$this->_cid}";
        if($this->_AccountInfo['group'] == 3){
            $where .= " AND usp.admin_id={$this->_uid}";
        }
        $result  = M('user_service_provider')->alias('usp')->field('ci.id company_id,ci.contact_phone,ci.tel_city_code,ci.tel_local_number,ci.company_name,ci.location,ci.contact_name,ci.industry,ci.employee_number,ci.register_fund,usp.diff_amount,usp.admin_id,usp.id,usp.price')
            ->join('left join zbw_user u ON u.id = usp.user_id')
            ->join('zbw_company_info ci ON ci.user_id = u.id')
            ->where($where)->limit(6)->order('u.create_time desc')->select();
		$this->assign('res', $res)->assign('_auth', $auth)->assign('result', $result)->display('Index/index');		
    }
    public function personManage ()
    {
        
    }
    public function salaryManage ()
    {

    }
    public function calculate ()
    {
        $m = M('template_rule');
        $rule = $m->where("id=54")->getField('rule');
        $calculate = new \Common\Model\Calculate;
        $person = array();
        $person['amount'] = 2000;
        $person['month'] = 1;
        $res = $calculate->detail($rule , $person , 1);
        print_r($res);
        $rule = $m->where("id=55")->getField('rule');
        $calculate = new \Common\Model\Calculate;
        $person = array();
        $person['amount'] = 2000;
        $person['month'] = 1;
        $person['personScale'] = '11%';
        $person['companyScale'] = '11%';
        $res = $calculate->detail($rule , $person , 2);
        echo "<br />";
        print_r($res);
    }
}