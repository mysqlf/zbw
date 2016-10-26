<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/13
 * Time: 16:36
 */

namespace Service\Model;
use Think\Model;


class ProductOrderModel extends ServiceAdminModel
{
    protected $trueTableName = 'zbw_service_product_order';
    public function comMembersOrderList($where)
    {
        $page = I('get.p',1);
        $count = $this->alias('p')
                    ->join('zbw_user_service_provider usp ON usp.user_id=p.user_id')
                    ->join('zbw_service_product s ON p.product_id = s.id')
                    ->join('zbw_company_info c ON c.user_id = p.user_id')
                    ->join('zbw_pay_order po ON po.id=p.pay_order_id')
                ->where($where)->count();
        $result = $this->alias('p')->field('p.service_state,p.state,p.id,po.order_no,po.pay_type,po.state payState,po.transaction_no,po.amount,po.id pay_order_id,usp.diff_amount, s.member_price,p.state,p.service_state,p.price,p.modify_price,p.is_salary,p.overtime,p.create_time,p.is_turn,s.name as product_name,c.id company_id,c.company_name,usp.admin_id,usp.price total_price')
            ->join('zbw_user_service_provider usp ON usp.user_id=p.user_id')
            ->join('zbw_service_product s ON p.product_id = s.id')
            ->join('zbw_company_info c ON c.user_id = p.user_id')
            ->join('zbw_pay_order po ON po.id=p.pay_order_id')
            ->where($where)->order('p.create_time desc')->page($page,20)->select();     
         foreach ($result as $key => $value) {
                if($value['is_turn'] == 1){
                    $validity = $this->getFieldByTurn_id($value['id'], 'overtime');
                    $result[$key]['validity'] = round((strtotime($value['overtime'])-strtotime($validity))/3600/24)+1;
                }else{
                    $result[$key]['validity'] = round((strtotime($value['overtime'])-strtotime($value['create_time']))/3600/24)+1;
                }
            }   
        $pageshow = showpage($count,20);
        return array('page'=>$pageshow,'result'=>$result);
    }
    // public function comDisAdmin($members,$admin)
    // {
    //     $result = $this->where("id = {$members['id']} AND state <> -9 AND service_com_id = {$admin['user_id']}")->find();
    //     $orderState = $this->_comOrderState($result);
    //     if(!empty($orderState)) return $orderState;
    //     //if(!empty($result['admin_id'])) return ajaxJson(-1,' 订单已分配过客服');
    //     $m = M('service_admin');
    //     $res = $m->where("id = {$members['admin_id']} AND `group` = 3 AND state = 1")->find();
    //     if(empty($res)) return ajaxJson(-1,'客服不存在');
    //     $state = $this->where("id = {$members['id']} AND state <> -9")->save(array('admin_id'=>$members['admin_id'],'update_date'=>date('Y-m-d H:i:s',time())));
    //     if($state)
    //     {
    //         $this->adminLog($admin['user_id'],'分配产品订单：'.$result['order_no'].'，给客服：'.($res['name']?$res['name']:$members['admin_id']).'，成功');
    //         return ajaxJson(0,'分配成功');
    //     }
    //     else
    //     {
    //         $this->adminLog($admin['user_id'],'分配产品订单：'.$result['order_no'].'，给客服：'.($res['name']?$res['name']:$members['admin_id']).'，失败');
    //         return ajaxJson(-1,'分配失败');
    //     }
    // }

    // public function comSetPrice($members,$admin)
    // {
    //     $result = $this->where("id = {$members['id']} AND state <> -9 AND service_com_id = {$admin['user_id']}")->find();
    //     $orderState = $this->_comOrderState($result);
    //     if(!empty($orderState)) return $orderState;
    //     if($result['state'] == 1 || $result['state'] == 2) return ajaxJson(-1,'服务已确认付款，无法修改');
    //     //if(!empty($result['modify_price'])) return ajaxJson(-1,'服务已调整过价格');
    //     $state = $this->where("id = {$members['id']} AND state <> -9")->save(array('modify_price'=>$members['modify_price'],'update_date'=>date('Y-m-d H:i:s',time())));
    //     if($state)
    //     {
    //         $this->adminLog($admin['user_id'],'修改产品订单：'.$result['order_no'].'的价格，成功');
    //         return ajaxJson(0,'修改价格成功');
    //     }
    //     else
    //     {
    //         $this->adminLog($admin['user_id'],'修改产品订单：'.$result['order_no'].'的价格，失败');
    //         return ajaxJson(-1,'修改价格失败');
    //     }
    // }
    /**
     * 订单详细
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function comMemBersDetail($where)
    {
        $result = $this->alias('p')->field('c.company_name,po.transaction_no,po.actual_amount,po.state pay_state,s.name product_name,p.turn_id,p.overtime,p.inc_handle_days,p.is_salary,p.id,p.service_state,p.af_service_price,p.af_service_price')
            //->join('LEFT JOIN zbw_service_admin a ON a.user_id = p.user_id')
            ->join('zbw_service_product s ON p.product_id = s.id')
            ->join('zbw_company_info c ON c.user_id = p.user_id')
            ->join('zbw_pay_order po ON po.id= p.pay_order_id')
            ->where($where)->order('p.create_time desc')->find();
          //  echo $this->getLastSql();die();
            //dump($result);
        if(!empty($result))
        {
            $m = M('warranty_location');
            $res = $m->where("service_product_order_id = {$result['id']} AND state = 0")->select();
            if(!empty($res))
            {
                $result['warranty_location'] = $res;
            }
        }

        return $result;
    }


    public function comSetService($members,$admin)
    {     
        $where = "state <> -9  AND id = ".$members['id'];
        $result = $this->where($where)->find();
        $data = array();    
       
       // $orderState = $this->_comOrderState($result);
       // if(!empty($orderState)) return $orderState;

        if($members['service_state'] == 0)   $data['service_state'] = 0;
        
        if( $members['service_state'] == 2)
        {
            $data['service_state'] = 2;
            if(empty($members['overtime'])) return ajaxJson(-1,'过期时间不能为空');
            $data['overtime'] = $members['overtime'];
            $data['inc_handle_days']  = $members['inc_handle_days'] ? $members['inc_handle_days'] : 0;
            $data['is_salary'] = $members['is_salary'];
            $data['af_service_price'] = $members['af_service_price'];
        }     
        if($members['service_state'] == 3)   $data['service_state'] = 3;

        $state = $this->where($where)->save($data);
        if(is_numeric($state))
        {
            //更新服务城市工资服务费
            if($members['service_state'] == 2 && $data['is_salary'] && is_numeric($data['af_service_price'])){
                $m = M('warranty_location');
                $m->where(array('service_product_order_id'=> $members['id'] ))->save(array('af_service_price'=> $data['af_service_price']));
            }

            $this->adminLog($admin['user_id'],'设定产品订单：'.$result['order_no'].'的服务状态，成功');
            return ajaxJson(0,'设定成功');
        }
        else
        {
            $this->adminLog($admin['user_id'],'设定产品订单：'.$result['order_no'].'的服务状态，失败');
            return ajaxJson(-1,'设定失败');
        }

    }

    /**
     * 参保地 服务城
     * @param  [type] $members  [description]
     * @param  [type] $location [description]
     * @param  [type] $admin    [description]
     * @return [type]           [description]
     */
    public function comAddLocation($members,$location,$admin)
    {
        if(empty($location)) return ajaxJson(-1,'数据不能为空');
        if(empty($location['location'])) return ajaxJson(-1,'参保地不能为空');
        $where = "state <> -9  AND id = ".$members['id'];
        if($admin['group'] == 3)
        {
            $where = "state <> -9 AND id = ".$members['id'];
        }
        $result = $this->where($where)->find();
        $orderState = $this->_comOrderState($result);
        if(!empty($orderState)) return $orderState;
        $product = M('service_product');
        $res = $product->field('service_price_state,service_type')->where("id = {$result['product_id']}")->find();
        if($res['service_price_state'] == 0 && ($res['service_type'] == 3|| $res['service_type'] == 4) )
        {
            $location['soc_service_price'] = 0;
            $location['pro_service_price'] = 0;
            $location['af_service_price'] = 0;
        }
        $m = M('warranty_location');
        $res = $m->field('id')->where("id = {$location['id']} AND state = 0")->find();
        if(empty($res))
        {
            $locationRes = $m->where("service_product_order_id = ".$members['id']." AND location = ".$location['location'].' AND state = 0')->find();
            if(!empty($locationRes)) return ajaxJson(-1,'参保地已存在，请勿重复创建');
            $state = $m->add(array('service_product_order_id'=>$members['id'],'location'=>$location['location'],'soc_service_price'=>$location['soc_service_price'],
            'pro_service_price'=>$location['pro_service_price'],
            'af_service_price'=>$location['af_service_price'],'create_date'=>date('Y-m-d H:i:s',time()),'update_date'=>date('Y-m-d H:i:s',time())));
            if($state)//->where("id = {$location['id']}")
            {   

                $this->adminLog($admin['user_id'],'添加产品订单：'.$result['order_no'].'的参保地，成功');
                $_result = M('warranty_location')->field('*')->where(array('id'=> $state))->find();
                $_result['location'] = showAreaName($_result['location']);
                return ajaxJson(0,'添加参保地成功', $_result);
            }
            else
            {
                $this->adminLog($admin['user_id'],'添加产品订单：'.$result['order_no'].'的参保地，失败');
                return ajaxJson(-1,'添加参保地失败');
            }
        }
        else
        {            
            $locationRes = $m->field('id')->where("service_product_order_id = ".$members['id']." AND location = ".$location['location'].' AND state = 0 AND id <> '.$location['id'])->find();
           // if(!empty($locationRes)) return ajaxJson(-1,'参保地已存在，请勿重复创建');
            $state = $m->where("id = {$location['id']} AND state = 0")->save(array('service_product_order_id'=>$members['id'],'location'=>$location['location'],'soc_service_price'=>$location['soc_service_price'],
                'pro_service_price'=>$location['pro_service_price'],'af_service_price'=>$location['af_service_price'],'update_date'=>date('Y-m-d H:i:s',time())));
            if($state)
            {
                $this->adminLog($admin['user_id'],'修改产品订单：'.$result['order_no'].'的参保地，成功');
                return ajaxJson(0,'修改参保地成功');
            }
            else
            {
                $this->adminLog($admin['user_id'],'修改产品订单：'.$result['order_no'].'的参保地，失败');
                return ajaxJson(-1,'修改参保地失败');
            }
        }
    }

    /**
     * 参保地删除
     */
    public function deleteLocation($where, $admin){
           $m = M('warranty_location');
           $info = $m->field('location')->where(array('id'=>$where['location_id'], 'service_product_order_id'=> $where['service_product_order_id']))->find();
           $m->startTrans();
           $result = $m->where(array('id'=>$where['location_id'], 'service_product_order_id'=> $where['service_product_order_id']))->save(array('state'=>-9));
            if($result)
            {

                $m->commit();
                $this->adminLog($admin['user_id'],'删除产品订单：'.$where['service_product_order_id'].'的参保地'.$where['id'].'，成功');
                return ajaxJson(0,'删除参保地成功');
            }
            else
            {
                $m->rollback();
                $this->adminLog($admin['user_id'],'删除产品订单'.$where['service_product_order_id'].'的参保地'.$where['id'].'，失败');
                return ajaxJson(-1,'删除参保地失败');
            }
    }


    /**
     * 产品订单状态
     * @param  [type] $result [description]
     * @return [type]         [description]
     */
    private function _comOrderState($result, $type)
    {
        if(empty($result))  return ajaxJson(-1,'订单不存在');
        // $product = M('service_product');
        // $res = $product->where("id = {$result['product_id']}")->find();     
        // if($result['is_turn'] == 0 && ($result['service_state'] == 3 || $result['service_state'] == -1 ||  $result['state'] == -2))  return ajaxJson(-1,'该服务已完成');
    }

  //  public function get 1企业 2服务商 3个人'
    /**
     * 产品信息
     */
    public function productInfo($id){
        return $this->alias('spo')->field('sp.name,spo.id')
                ->join('left join zbw_service_product sp ON sp.id = spo.product_id ')
                 ->where(array('spo.id'=> $id))->find();
    }


    /**
     * 生成支付订单-切换套餐用
     */
    protected function createPayOrder($members, $admin){
        $data['order_no']  = date('ymd').sprintf('%05d',time()-strtotime(date("Y-m-d"))).substr(microtime(),2,6).sprintf('%03d',rand(0,999));
        $data['user_id'] = $members['user_id'];
        $data['product_id'] = $members['product_id'];
        $data['state'] = 0;
        $data['type'] = 1;
        $data['handle_month'] =date("Ym");
        $data['amount'] = $members['price'];
        $data['diff_amount'] = 0;
        $data['actual_amount']  = 0;
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $data['company_id'] = $admin['company_id'];
        $payOrder = M('pay_order');
        $payOrder->token(false)->create($data);
        $result = $payOrder->add();
        if(empty($result)){
            $this->where(array('id'=> $members['order_id']))->delete();
            return ajaxJson(-1,'1添加失败');
        }else{
            //更新差额
           return $result;
        }
    } 

    /**
     * 生成订单及设定服务-切换套餐用
     */
    protected function createProductOrder($members, $admin){
       
       // $data['user_id'] = $this->getFieldById($members['product_id', 'user_id');
        $data['user_id'] = $members['user_id'];
        $data['product_id'] =  $members['product_id'];
        $data['state'] = 0;
        $data['service_state'] = 0;
        $data['is_turn'] = 1;
        $data['create_time'] = date('Y-m-d H:i:s',time());
       // $data['diff_amount'] = M('user_service_provider')->getFieldByUser_id($members['user_id'], 'diff_amount'); 
        $data['price'] = $members['price'];
        $data['handle_month'] = date('Ym');

        if( $members['service_state'] == 2)
        {
            $data['service_state'] = 2;
            if(empty($members['overtime'])) return ajaxJson(-1,'过期时间不能为空');
            $data['overtime'] = $members['overtime'];
            $data['inc_handle_days']  = $members['inc_handle_days'] ? $members['inc_handle_days'] : 0;
            $data['is_salary'] = $members['is_salary'];
            $data['af_service_price'] = $members['af_service_price'];
        }
        
        if($members['service_state'] == 3)   $data['service_state'] = 3;
        $this->token(false)->create($data);
        $state = $this->add();
        if($state)
        {
            //S('createProductOrder'.$data['user_id'], '1', 60);
          //  $this->adminLog($admin['user_id'],'设定产品订单：'.$result['order_no'].'的服务状态，成功');
            //return ajaxJson(0,'设定成功', $state);
            return $state;
        }
        else
        {
          //  $this->adminLog($admin['user_id'],'设定产品订单：'.$result['order_no'].'的服务状态，失败');
            return ajaxJson(-1,'2设定失败');
        }
    }

    /**
     * 保存-切换套餐用
     */
    public function addContractChange($members, $admin){
            $order_id = $this->createProductOrder($members, $admin);
            //生成支付订单
            $members['order_id'] = $order_id;
           $pay_order_id =  $this->createPayOrder($members,$admin);
           $result = $this->where(array('id'=> $order_id))->save(array('pay_order_id'=> $pay_order_id));

           //修改原订单turn_id
           $result = $this->where(array('id'=> $members['old_id']))->save(array('turn_id'=> $order_id));
          if($result)
            {
                $this->adminLog($admin['user_id'],'添加切换套餐：'.$result['order_no'].'成功');
                return ajaxJson(0,'添加成功', U('Customer/productOrderDetail?type=1&id='.$order_id));                
            }
            else
            {
                $this->adminLog($admin['user_id'],'添加切换套餐：'.$result['order_no'].'失败');
                return ajaxJson(-1,'添加失败');
            }
    }


    /**
     * 切换合同-是否已添加过切换套餐及过期时间
     */
    public function isTurnID($id){
        $info = $this->field('turn_id,is_turn,overtime')->find($id);
        if(empty($info)) return ajaxJson(-1,'合同不存在！');
        if(empty($info['turn_id'])){
            return ajaxJson(0,'未添加过切换套餐', $info['overtime']); 
        }else{
            return ajaxJson(-1,'已添加过切换套餐'); 
        }
    }
}