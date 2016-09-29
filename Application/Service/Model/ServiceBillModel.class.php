<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Service\Model;
use Think\Model;

/**
 * 文件模型
 * 负责文件的下载和上传
 */

class ServiceBillModel extends ServiceAdminModel
{
    protected $trueTableName = 'service_bill';
    protected $db;

    public function __Construct(){
      $this->db = M('service_bill');
    }

    public function comBillList($admin, $where)
    {
        $page = I('get.p', '1');
        $count = $this->db->alias('sb')->where($where)->join('zbw_user u ON u.id= sb.user_id')->join('zbw_company_info ci ON ci.id=sb.company_id')->count();
        $result = $this->db->alias('sb')->field('sb.*,ci.company_name,ci.id company_id')
                    ->join('zbw_user u ON u.id= sb.user_id')
                    ->join('zbw_company_info ci ON ci.user_id=sb.user_id')
                   // ->join('zbw_service_product sp ON sp.id=sb.product_id')
                    ->where($where)->page($page, 20)->order('sb.create_time desc')->select();
      //  dump($result);
        $pageshow = showpage($count,20);
        return array('page'=>$pageshow,'result'=>$result);
    }

    public function comBillDetail($admin, $data)
    {
        $billInfo =  $this->db->alias('sb')->field('sb.*,ci.company_name,ci.contact_name,ci.contact_phone')
                        ->join('zbw_company_info ci ON ci.user_id = sb.user_id')
                        ->where(array('sb.id'=> $data['id']))->find();//GROUP_CONCAT
        //$billInfo['service_name'] = $this->serviceInfo($billInfo['user_id']);
        $payOrderId = M('pay_order')->field('group_concat(id) id')->where(array('type'=> 2,'service_bill_id'=> $data['id']))->select(); 

        if(!empty($payOrderId[0]['id'])){
//dump($payOrderId);die();
          // $resSb = M('service_insurance_detail')->alias('sid')->field('sid.*,pb.person_name,pii.base_id,pb.card_num,sp.name product_name,pii.template_location,pii.handle_month')
          //                     ->join('zbw_person_insurance_info pii ON pii.id = sid.insurance_info_id')
          //                     ->join('zbw_person_base pb ON pb.id = pii.base_id')
          //                     ->join('left join zbw_service_product sp ON sp.id = pii.product_id')
          //                     ->where('sid.pay_order_id in('.$payOrderId[0]['id'].') AND pii.payment_type=1')->select();

          // $resGjj = M('service_insurance_detail')->alias('sid')->field('sid.insurance_detail,pb.person_name,pii.base_id,sid.pay_date,pii.handle_month')
          //                     ->join('zbw_person_insurance_info pii ON pii.id = sid.insurance_info_id')
          //                     ->join('zbw_person_base pb ON pb.id = pii.base_id')
          //                     ->where('sid.pay_order_id in('.$payOrderId[0]['id'].') AND pii.payment_type=2')->select(); 
          // foreach ($resSb as $key => $value) {
          //         foreach ($resGjj as $k => $val) {
          //             if($value['base_id'] = $val['base_id'] && $value['pay_date'] = $val['pay_date']) {
          //                 $resSb[$key]['gjj'] = $val['insurance_detail'];
          //                 continue;
          //             }
          //         }
          //     }

        $resSb = M('service_insurance_detail')->alias('sid')->field('sid.id,sid.insurance_detail,sid.pay_date,sid.type,pb.person_name,pii.base_id,pii.location,pii.handle_month,pb.card_num,pb.user_id,sp.name product_name,sid.service_price,pii.template_location')
                            ->join('zbw_person_insurance_info pii ON pii.id = sid.insurance_info_id')
                            ->join('zbw_person_base pb ON pb.id = pii.base_id')
                            ->join('left join zbw_service_product sp ON sp.id = pii.product_id')
                            ->join('left join zbw_pay_order po ON po.id = sid.pay_order_id ')                         
                            ->where("sid.pay_order_id in({$payOrderId[0]['id']}) AND pii.payment_type=1 AND po.company_id={$admin['company_id']} AND sid.state NOT IN(0,-1) AND po.state=1")->order('sid.id asc')->select();
 //dump($resSb);
        $resGjj = M('service_insurance_detail')->alias('sid')->field('sid.id,sid.insurance_detail,sid.pay_date,sid.type,pb.person_name,pii.base_id,pii.location,pii.handle_month,pb.card_num,pb.user_id,sp.name product_name,pii.template_location,sid.service_price')
                            ->join('zbw_person_insurance_info pii ON pii.id = sid.insurance_info_id')
                            ->join('zbw_person_base pb ON pb.id = pii.base_id')
                            ->join('left join zbw_service_product sp ON sp.id = pii.product_id')
                            ->join('left join zbw_pay_order po ON po.id = sid.pay_order_id ')
                            ->where("sid.pay_order_id in ({$payOrderId[0]['id']}) AND pii.payment_type=2 AND po.company_id={$admin['company_id']} AND sid.state NOT IN(0,-1) AND po.state=1")->order('sid.id asc')->select(); 
                          //  dump($resGjj);
        foreach ($resSb as $key => $value) {//echo $key,'/';
                foreach ($resGjj as $k => $val) {
                    if($value['base_id'] == $val['base_id'] && $value['pay_date'] == $val['pay_date'] && $value['card_num'] == $val['card_num']) {
                        if(!empty($val['insurance_detail'])){//echo $k,'<br/>';
                          $resSb[$key]['gjj'] = $val['insurance_detail'];
                            $resSb[$key]['service_price'] = $val['service_price'] + $value['service_price'];
                          unset($resGjj[$k]);
                          break;
                      }
                    }
            }
        } 
        //dump($resGjj);
                           
        if(count($resGjj) > 0){
            foreach ($resGjj as $key => $value) {
                $resGjj[$key]['gjj'] = $val['insurance_detail'];
                unset($resGjj[$key]['insurance_detail']);
            }
          $resSb = array_merge($resSb, $resGjj);
        }
   //dump($resGjj);//
        foreach ($resSb as $key => $value) {
            if(!empty($value['insurance_detail'])){
                $sb = json_decode($value['insurance_detail'], true);
                $resSb[$key]['sb_per'] = $sb['person'];
                $resSb[$key]['sb_com'] = $sb['company'];
                $resSb[$key]['sb_type'] = $value['type'];
                // foreach ($sb['items'] as $k => $val) {
                //     if($val['name'] == '残障金'){
                //         $resSb[$key]['disable'] = $val['total'];
                //         continue;
                //     }
                // }                
                unset($resSb[$key]['insurance_detail']);
            }    
            if(!empty($value['gjj'])){
                $gjj = json_decode($value['gjj'], true);
                $resSb[$key]['gjj_per'] = $gjj['person'];
                $resSb[$key]['gjj_com'] = $gjj['company'];
                $resSb[$key]['gjj_type'] = $value['type'];
                //   foreach ($gjj['items'] as $k => $val) {
                //     if($val['name'] == '残障金'){
                //         $resSb[$key]['disable'] = $val['total'];
                //         continue;
                //     }
                // }                     
                unset($resSb[$key]['gjj']);
            } 

        }    


        }    
//            dump($resSb);die();
        //工资
       $payOrderId = M('pay_order')->field('group_concat(id) id')->where(array('type'=> 3,'service_bill_id'=> $data['id']))->select(); 
   
       if(!empty($payOrderId[0]['id']))
       {
           $resSalary = M('service_order_salary')->alias('sos')->field('sos.*,pb.person_name,pb.card_num,pb.bank,pb.account,sp.name product_name')
                      ->join('zbw_person_base pb ON pb.id = sos.base_id')
                      ->join('left join zbw_service_product sp ON sp.id = sos.product_id')
                      ->join('left join zbw_pay_order po ON po.id = sos.pay_order_id')
                      ->where('sos.pay_order_id in('.$payOrderId[0]['id'].')   AND po.state=1')->select(); 
       }

       //服务套餐
      $payOrderId = M('pay_order')->field('group_concat(id) id')->where(array('type'=> 1,'service_bill_id'=> $data['id']))->select();
      if(!empty($payOrderId[0]['id']))
      {
        $product =  M('service_product_order')->alias('spo')->field('spo.id,pay_order_id, price,modify_price,sp.name product_name')
                    ->join('left join zbw_service_product sp ON sp.id = spo.product_id')
                    ->join('left join zbw_pay_order po ON po.id = spo.pay_order_id')
                    ->where('spo.pay_order_id in('.$payOrderId[0]['id'].')  AND po.state=1')->select();//spo.pay_order_id IS NULL AND 
      }

      return array('billInfo'=> $billInfo, 'resSb'=> $resSb, 'resSalary'=> $resSalary, 'product'=> $product);
    }

    /**
     * 个人
     */
    public function perBillList($admin, $where)
    {
        $page = I('get.p', '1');
        $count = $this->db->alias('sb')->where($where)->join('zbw_user u ON u.id= sb.user_id')->count();
        $result = $this->db->alias('sb')->field('sb.*,sp.name,pb.person_name')
                    ->join('zbw_user u ON u.id= sb.user_id')
                    ->join('LEFT JOIN zbw_person_base pb ON pb.user_id=sb.user_id')
                    ->join('zbw_service_product sp ON sp.id=sb.product_id')
                    ->where($where)->page($page, 20)->order('sb.create_time desc')->select();
        //dump($result);
        $pageshow = showpage($count,20);
        return array('page'=>$pageshow,'result'=>$result);
    }

    public function perBillDetail($admin, $data)
    {
        $billInfo =  $this->db->alias('sb')->field('sb.*,ci.company_name')
                        ->join('zbw_company_info ci ON ci.id = sb.company_id')
                        ->where(array('sb.id'=> $data['id']))->find();//GROUP_CONCAT

        $payOrderId = M('pay_order')->field('group_concat(id) id')->where(array('type'=> 2,'service_bill_id'=> $data['id']))->select();
        if(!empty($payOrderId[0]['id'])){
            $resSb = M('service_insurance_detail')->alias('sid')->field('sid.*,pb.person_name,pii.base_id')
                                ->join('zbw_person_insurance_info pii ON pii.id = sid.insurance_info_id')
                                ->join('zbw_person_base pb ON pb.id = pii.base_id')
                                ->where('sid.pay_order_id in('.$payOrderId[0]['id'].') AND pii.payment_type=1')->select();
            $resGjj = M('service_insurance_detail')->alias('sid')->field('sid.insurance_detail,pb.person_name,pii.base_id,sid.pay_date')
                                ->join('zbw_person_insurance_info pii ON pii.id = sid.insurance_info_id')
                                ->join('zbw_person_base pb ON pb.id = pii.base_id')
                                ->where('sid.pay_order_id in('.$payOrderId[0]['id'].') AND pii.payment_type=2')->select(); 
            foreach ($resSb as $key => $value) {
                    foreach ($resGjj as $k => $val) {
                        if($value['base_id'] = $val['base_id'] && $value['pay_date'] = $val['pay_date']) {
                            $resSb[$key]['gjj'] = $val['insurance_detail'];
                            continue;
                        }
                    }
                }
        }      

        //    dump($resSb);
        //工资
       $payOrderId = M('pay_order')->field('group_concat(id) id')->where(array('type'=> 3,'service_bill_id'=> $data['id']))->select(); 
       if(!empty($payOrderId[0]['id']))
       {
           $resSalary = M('service_order_salary')->alias('sos')->field('sos.*,pb.person_name,pb.card_num,pb.bank,pb.account,sp.name product_name')
                      ->join('zbw_person_base pb ON pb.id = sos.base_id')
                      ->join('left join zbw_service_product sp ON sp.id = sos.product_id')           
                    ->where('sos.pay_order_id in('.$payOrderId[0]['id'].')')->select(); 
       }      

       //服务套餐
      $payOrderId = M('pay_order')->field('group_concat(id) id')->where(array('type'=> 1,'service_bill_id'=> $data['id']))->select();
      if(!empty($payOrderId[0]['id']))
      {
        $product =  M('service_product_order')->alias('spo')->field('spo.id,pay_order_id, price,modify_price,sp.name product_name')
                    ->join('left join zbw_service_product sp ON sp.id = spo.product_id')
                    ->join('left join zbw_pay_order po ON po.id = spo.pay_order_id')
                    ->where('spo.pay_order_id in('.$payOrderId[0]['id'].') AND po.state=1')->select();
      }

    return array('billInfo'=> $billInfo, 'resSb'=> $resSb, 'resSalary'=> $resSalary, 'product'=> $product);
    }

    /**
     * 开票
     */
    public function invoice($data, $where){
        $result = $this->db->where(array('id'=> $where['id']))->save($data);      
        if(empty($result)){
            $this->adminLog($admin['user_id'], '对账单 '.$info['order_no'].' 对账单名称 '.$info['title'].' 开票失败');
            $this->error = '开票失败';
            return false;
        }else{
            $this->adminLog($admin['user_id'], '对账单 '.$info['order_no'].' 对账单名称 '.$info['title'].' 开票成功！');
            return true;
        }
    }

    /**
     * 开票默认信息
     */
    public function invoiceDefault($data){
        if($data['type'] == 1){
             $billInfo =  $this->db->alias('sb')->field('sb.price,sb.diff_amount,ci.company_name,ci.contact_name,ci.contact_phone')
                        ->join('zbw_company_info ci ON ci.user_id = sb.user_id')
                        ->where(array('sb.id'=> $data['id']))->find();
        }elseif($data['type'] == 3){
              $billInfo =  $this->db->alias('sb')->field('sb.price,sb.diff_amount,pb.person_name,pb.mobile')
                        ->join('zbw_person_base pb ON pb.id = sb.user_id')
                        ->where(array('sb.id'=> $data['id']))->find();
        }

        return $billInfo;
    }
  /**
   * 服务商信息
   */
  public function serviceInfo($user_id){
    $companyId =  M('user_service_provider')->field('company_id')->where(array('user_id'=> $user_id))->find();
    $res =  M('company_info')->alias('ci')->field('ci.company_name')->where(array('id'=> $companyId['company_id']))->find();
    return $res['company_name'];
  }
}
