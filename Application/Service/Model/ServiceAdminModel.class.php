<?php

namespace Service\Model;
#use Service\Model\AdminModel;
use Think\Model;
/**
 * 服务商管理账号模型
 * 服务商后台管理账号相关功能
 */

class ServiceAdminModel extends Model
{
   // protected $autoCheckFields = true;

    public function childAccountInfo($account,$admin)
    {
        $result = $this->alias('ua')->field('ua.*, u.username')->join('left join zbw_user u ON u.id=ua.user_id')->where("ua.id = {$account['id']} AND ua.company_id = {$admin['company_id']}")->find();
        if(!empty($result)) $result['auth'] = json_decode($result['auth']);
        if(empty($result)) return ajaxJson(-1,'帐号不存在');
        return ajaxJson(0,'',$result);
    }

    public function addChildAccount($account,$admin)
    {
        if(empty($account['id']))
        {
            $result =  M('user')->where("username = '{$account['username']}'")->find();
            if(!empty($result)) return ajaxJson(-1,'帐号已存在');

            $account['password'] =  md5($account['username'].':'.$account['password']);
            $result = $this->where("name = '{$account['name']}' AND company_id = {$admin['company_id']}")->find();
            if(!empty($result)) return ajaxJson(-1,'帐号已存在');
            unset($account['id']);
            $user['username'] = $account['username'];
            $user['password'] = $account['password'];           
            $user['create_time'] = $account['create_time'];
            $user['father_id'] = $admin['user_id'];
            $user['type'] = 2;
            
            $user_id = M('user')->add($user);
            if(empty($user_id)){
               return ajaxJson(-1,'添加帐号失败'); 
            }
            $account['user_id'] = $user_id;
            if($account['group'] == 2){//2财务 
                $account['auth'] = json_encode(C('financeAuth'));
            }elseif($account['group'] == 3){
                $account['auth'] = json_encode(C('serviceAuth'));
            }else{
                 $account['auth'] = json_encode(C('articleAuth'));
            }
            $account['type'] = 2;
            $this->token(false)->create($account);
            $id = $this->add();
            if($id)
            {
                $this->adminLog($admin['user_id'],'添加账号：'.$account['username'].' 成功');
                return ajaxJson(0,'添加帐号成功');
            }

            else
            {
                $this->adminLog($admin['user_id'],'添加账号：'.$account['username'].' 失败');
                return ajaxJson(-1,'添加帐号失败');
            }
        }
        else
        {
            $result = $this->where("id = {$account['id']} AND company_id = {$admin['company_id']}")->find();
            if(empty($result)) return ajaxJson(-1,'帐号不存在');
            if(!empty($account['password']))
            {
                $password =  md5($account['username'].':'.$account['password']);
            }
            else
            {
                unset( $account['password']);
            }

            $nowdate = $account['create_time'];
            $username = $account['name'];
            if($account['group'] == 2){//2财务 
                $account['auth'] = json_encode(C('financeAuth'));
            }else{
                $account['auth'] = json_encode(C('serviceAuth'));
            }

            $username = $account['username'];
            unset($account['create_time']);
            unset($account['username']);
            unset($account['company_id']);
            $account['update_time']  = date('Y-m-d H:i:s',time());

            $id = $this->where("id = {$account['id']}")->save($account);
            if(is_numeric($id))
            {   
                if(!empty($account['password']))
                {
                    M('user')->where(array('username'=> $username))->save(array('password'=> $password));                  
                }
                $state_user_id = S('state_user_id');
                if($account['state'] == 1){
                    $state_user_id = str_replace(','.$result['user_id'], '', $state_user_id);
                }else{
                    if(strpos($state_user_id, $result['user_id']) == false){
                        $state_user_id .= ','.$result['user_id'];
                    }
                }
                S('state_user_id', $state_user_id);
                $this->adminLog($admin['user_id'],'修改账号：'.$username.' 成功，原内容：'.json_encode($result,JSON_UNESCAPED_UNICODE).'，修改内容：'.json_encode($account,JSON_UNESCAPED_UNICODE));
                return ajaxJson(0,'修改帐号成功');
            }

            else
            {
                $this->adminLog($admin['user_id'],'修改账号：'.$username.' 失败');
                return ajaxJson(-1,'修改帐号失败');
            }
        }
    }
    
    public function delAccount($account)
    {
        $result = $this->where("id = {$account['id']} AND company_id = {$account['company_id']}")->find();
        if(empty($result)) return ajaxJson(-1,'帐号不存在');

        $uid = $result['user_id'];
        $result =  M('user')->where("id = '{$result['user_id']}'")->find();
        if(empty($result)) return ajaxJson(-1,'帐号不存在');


        $id = $this->where("id = {$account['id']}")->save(array('state'=>-9));
        $id = M('user')->where("id = {$uid}")->save(array('state'=>-9));
        if($id)
        {
            $this->adminLog($account['user_id'],'删除账号：'.$result['username'].' 成功');
            return ajaxJson(0,'删除帐号成功');
        }

        else
        {
            $this->adminLog($account['user_id'],'删除账号：'.$result['username'].' 失败');
            return ajaxJson(-1,'删除帐号失败');
        }
    }
    public function childAccountlist ($cid)
    {
        $page = I('get.p',1);
        $m = M('ServiceAdmin');
        $count = $m->alias('sa')->join('LEFT JOIN zbw_user u ON u.id=sa.user_id')->where("sa.company_id = {$cid} AND sa.type=2 AND u.state <> -9")->count();
        $result = $m->alias('sa')->field('sa.*,u.username,u.type u_type, u.state u_state')->join('LEFT JOIN zbw_user u ON u.id=sa.user_id')->where("sa.company_id = {$cid} AND sa.type=2 AND u.state <> -9")->page($page,20)->select();
        $pageshow = showpage($count,20);
        return array('page'=>$pageshow,'result'=>$result);
    }

//     public function accountInfo($account,$admin)
//     {
//         $result = $this->where("id = {$account['id']} AND company_id = {$account['company_id']}")->find();
//         if(empty($result)) return ajaxJson(-1,'帐号不存在');
//         if(IS_POST)
//         {
//             $data['name'] = $account['name'];
//             $data['qq'] = $account['qq'];
//             $data['telphone'] = $account['telphone'];
//             $data['update_time'] = date('Y-m-d H:i:s',time());
//             $id = $this->where("id = {$account['id']}")->save($data);
//             if($id)
//             {
//                 $this->adminLog($account['id'],'修改账号：'.$result.username'].'的基本信息成功，原内容：'.json_encode(array('name'=>$result['name'],'qq'=>$result['qq'],'telphone'=>$result['telphone']),JSON_UNESCAPED_UNICODE ).'，修改内容：'.json_encode(array('name'=>$account['name'],'qq'=>$account['qq'],'telphone'=>$account['telphone']),JSON_UNESCAPED_UNICODE));
//                 return ajaxJson(0,'保存成功');
//             }
//             else
//             {
//                 $this->adminLog($account['id'],'修改账号：'.$result['username'].'的基本信息失败');
//                 return ajaxJson(-1,'保存失败');
//             }
//         }
//         return $result;
//     }

    public function setPassword($account)
    {
        $result =  M('user')->where("id = '{$account['user_id']}'")->find();
        if(empty($result)) return ajaxJson(-1,'帐号不存在');

        $lastPassword = md5($result['username'].':'.$account['lastPassword']);
        if( $lastPassword != $result['password']) return ajaxJson(-1,'原密码密码错误');
        $account['password'] = md5($result['username'].':'.$account['password']);

        $id = M('user')->where("id = {$account['user_id']} AND state <> -9")->save(array('password'=>$account['password']));
        if($id)
        {
            $this->adminLog($account['id'],'修改账号：'.$result['username'].'的密码成功');
            return ajaxJson(0,'修改成功');
        }
        else
        {
            $this->adminLog($account['id'],'修改账号：'.$result['username'].'的密码失败');
            return ajaxJson(-1,'修改失败');
        }
    }

    public function bankInfo($account,$bankInfo)
    {
        $m = M('company_bank');//company_account_info
        if(IS_POST)
        {
            $bankInfo['id'] = intval($bankInfo['id']);
            $result = $m->where(" id = {$bankInfo['id']} AND company_id = {$account['company_id']}")->find();
            if(empty($result))
            {
                $bankInfo['company_id'] = $account['company_id'];
                $id = $m->add($bankInfo);
                if($id)
                {
                    $this->adminLog($account['id'],'添加银行信息成功');
                    return ajaxJson(0,'添加银行卡信息成功');
                }
                else
                {
                    $this->adminLog($account['id'],'添加银行信息失败');
                    return ajaxJson(-1,'添加银行卡信息失败');
                }
            }
            else
            {
                $bank_id = $bankInfo['id'];
                unset($bankInfo['id']);
                $id = $m->where(" id = {$bank_id} AND company_id = {$account['company_id']}")->save($bankInfo);
                if($id)
                {
                    $this->adminLog($account['id'],'修改银行信息成功，原内容：'.json_encode($result,JSON_UNESCAPED_UNICODE).'，修改内容：'.json_encode($bankInfo,JSON_UNESCAPED_UNICODE));
                    return ajaxJson(0,'修改银行卡信息成功');
                }
                else
                {
                    $this->adminLog($account['id'],'修改银行信息失败');
                    return ajaxJson(-1,'修改银行卡信息失败');
                }
            }
        }
        else
        {
            $result = $m->where("company_id = {$account['company_id']}")->find();
            return $result;
        }
    }

    public function adminLog($admin_id,$detail)
    {
        $log = M('ServiceAdminLog');
        $log->add(array('admin_id'=>$admin_id,'create_time'=>date('Y-m-d H:i:s',time()),'detail'=>$detail));
    }

//     public function balance($bill_id,$type = 0)
//     {
//         $bill = M('service_bill');
//         $result = $bill->alias('b')->field('b.id,b.price,b.state,b.order_id,b.actual_price,b.order_date,s.product_order_id,s.create_time,s.order_date as servive_order_date')->join('zbw_service_order s ON s.id = b.order_id')->where("b.id = {$bill_id} AND b.state <> -9")->find();
//         intval($result['actual_price']);
//         if(empty($result['actual_price']) || $result['actual_price'] == 0 )
//         {
//             return '';
//         }
//         $yser = substr($result['servive_order_date'],0,4);
//         $month = substr($result['servive_order_date'],4,2);
//         $order_date = $yser.'-'.$month;
//         $service = M('service_order');
//         $order_date = date('Ym',strtotime('-1 month',strtotime($order_date)));
//         $last_order = $service->where('product_order_id = '.$result['product_order_id'].' AND order_date = '.$order_date.' AND state <>-9')->find();
//         $last_result['balance_total'] = 0;

//         if(!empty($last_order))
//         {
//             $last_result = $bill->field('balance_total')->where('order_id = '.$last_order['id'])->find();
//         }
//         if($type == 1) return $last_result['balance_total'];
//         $totle = '';
//         $balance = $bill->query("
// select sum(balance) as balance from zbw_service_bill_detail where service_bill_id = {$bill_id}
// UNION ALL select sum(balance) as balance from zbw_service_bill_salary where bill_id = {$bill_id}
//         ");
//         foreach($balance as $k=>$v)
//         {
//             $totle += $v['balance'];
//         }
//         $totle_balance = $result['actual_price'] - $result['price'] + $totle +  $last_result['balance_total'];
//         $bill->where("id = {$bill_id} AND state <> -9")->save(array('balance_total'=>$totle_balance));
//     }
//     

}
