<?php
namespace Service\Model;
#use Think\Model;
/**
 * 客户列表
 */
class MemberModel extends ServiceAdminModel{

	protected $trueTableName ='zbw_user_service_provider';

	public function MembersList($where){
        $page = I('get.p', '1');          
        $company = M('company_info');
        $count = $company->alias('ci')
                    ->join('left join zbw_user_service_provider usp ON usp.user_id=ci.user_id')
                    ->join('left join zbw_user u ON u.id = ci.user_id')
                    ->where($where)->count();

        $result = $company->alias('ci')->field('ci.id company_id,ci.contact_phone,ci.tel_city_code,ci.tel_local_number,ci.company_name,ci.location,ci.contact_name,ci.industry,ci.employee_number,ci.register_fund,usp.diff_amount,usp.admin_id,usp.id,usp.price')
                    ->join('left join zbw_user_service_provider usp ON usp.user_id=ci.user_id')
                    ->join('left join zbw_user u ON u.id = ci.user_id')
                    ->where($where)->order('u.create_time desc')->page($page, 20)->select();

        $pageshow = showpage($count, 20);
        return array('page'=>$pageshow,'result'=>$result);
	}

    public function perMembersList($where){
        $page = I('get.p', '1');
        $count = $this->alias('usp')
                    ->join('left join zbw_user u ON u.id = usp.user_id')
                    ->join('left join zbw_person_base pb ON pb.user_id = usp.user_id')     
                    ->where($where)->count();
        $result  = $this->alias('usp')->field('usp.id,pb.person_name,pb.card_num,pb.residence_location,pb.residence_type,pb.mobile,usp.admin_id,usp.price,usp.diff_amount')
                    ->join('left join zbw_user u ON u.id = usp.user_id')
                    ->join('left join zbw_person_base pb ON pb.user_id = usp.user_id ')
                    ->where($where)->page($page, 20)->select();
        $pageshow = showpage($count, 20);
        return array('page'=>$pageshow,'result'=>$result);
    }
    /**
     * 设置客服
     */
    public function setService($members,$admin)
    {
        $result = $this->where("id = {$members['id']} AND company_id = {$admin['company_id']}")->find();
        if($result['admin_id'] == $members['admin_id']) return ajaxJson(-1,' 请确认要分配的新客服');
        $m = M('service_admin');
        $res = $m->where("id = {$members['admin_id']} AND `group` = 3 AND state = 1")->find();
        if(empty($res)) return ajaxJson(-1,'客服不存在');
        $state = $this->where("id = {$members['id']}")->save(array('admin_id'=>$members['admin_id']));
        if($state)
        {
            $this->adminLog($admin['user_id'],'分配会员：'.$result['user_id'].'，给客服：'.($res['name']?$res['name']:$members['admin_id']).'，成功');
            return ajaxJson(0,'分配成功');
        }
        else
        {
            $this->adminLog($admin['user_id'],'分配会员：'.$result['user_id'].'，给客服：'.($res['name']?$res['name']:$members['admin_id']).'，失败');
            return ajaxJson(-1,'分配失败');
        }
    }
}