<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/12
 * Time: 18:39
 */

namespace Service\Model;


class PersonModel extends  ServiceAdminModel
{       
    protected $autocheckfields = false;

    public function insurancePersonList($data,$where){
        $page = I('get.p',1);

        $count = M('person_insurance')->alias('pi')
                    ->join('zbw_service_product sp ON sp.id = pi.product_id')
                   ->where('pi.payment_type = 1 AND sp.company_id = '.$data['company_id'].$where)->count();

        $resSb = M('person_insurance')->alias('pi')->field('pi.id,pi.create_time,pi.location,pi.template_location,pi.start_month,pi.end_month,pi.amount,pi.payment_type,pi.state, pi.base_id,pb.person_name,pb.card_num,pb.residence_type,sp.name,tp.name rule_name,ci.company_name')
                ->join('zbw_service_product sp ON sp.id = pi.product_id')
                ->join('zbw_person_base pb ON pb.id = pi.base_id')
                ->join('zbw_template_rule tp ON tp.id = pi.rule_id')
                ->join(' zbw_company_info ci ON ci.id = sp.company_id')
                ->where('pi.payment_type = 1 AND sp.company_id = '.$data['company_id'].$where)
                ->order(' pi.create_time desc')->page($page, 10)->select();

//         echo M('person_insurance')->getLastSql(),'<br/>';
         foreach ($resSb as $k => $val) {
                $res[$k]['person_name'] = $val['person_name'];
                $res[$k]['card_num'] = $val['card_num'];
                $res[$k]['name'] = $val['name'];  
                $res[$k]['location'] = $val['location'];
                $res[$k]['residence_type'] = $val['residence_type'];
                $res[$k]['company_name'] = $val['company_name'];
                $res[$k]['id'] = $val['id'];
                $res[$k]['base_id'] = $val['base_id'];
            
                $res[$k]['sb']['state'] = $val['state'];
                $res[$k]['sb']['template_location'] = $val['template_location'];
                $res[$k]['sb']['amount'] = $val['amount'];
                $res[$k]['sb']['start_month'] = $val['start_month'];
                $res[$k]['sb']['end_month'] = $val['end_month'];
      
         }
//         dump($res);
        $resGjj = M('person_insurance')->alias('pi')->field('pi.id,pi.create_time,pi.template_location,pi.start_month,pi.end_month,pi.amount,pi.payment_type,pi.state, pb.person_name,pb.card_num,pb.residence_type,sp.name,tp.name rule_name')
                ->join('zbw_service_product sp ON sp.id = pi.product_id')
                ->join('zbw_person_base pb ON pb.id = pi.base_id')
                ->join('zbw_template_rule tp ON tp.id = pi.rule_id')
                ->join(' zbw_company_info ci ON ci.id = sp.company_id')
                ->where('sp.company_id = '.$data['company_id'].$where)
                ->order('pi.payment_type = 2 AND pi.create_time desc')->page($page, 10)->select();

        foreach ($res as $k => $val) {
            foreach ($resGjj as $key => $value) {
                if($val['person_name'] == $value['person_name']){// && $val['pay_date'] == $value['pay_date']
                    $res[$k]['gjj']['state'] = $value['state'];
                    $res[$k]['gjj']['template_location'] = $value['template_location'];
                    $res[$k]['gjj']['amount'] = $value['amount'];
                    $res[$k]['gjj']['start_month'] = $value['start_month'];
                    $res[$k]['gjj']['end_month'] = $value['end_month'];
                }
            }
        } 


        $pageshow = showpage($count,10);
        return array('page'=>$pageshow,'result'=>$res);
    }

/*
    public function personDetail($data){
        //用户基本信息
        $baseInfo = $this->personBase($data['base_id'], 'person_name,gender,card_num,mobile,residence_location,residence_type');
     //   dump($baseInfo);
        $personInsuranceInfo = M('person_insurance_info');
        //社保
        $resSb = $personInsuranceInfo->alias('pii')->field('pii.location,pii.amount,pii.rule_id,pii.product_id,sid.insurance_detail,tr.name,sid.state')->where(array('pii.base_id'=> $data['base_id'], 'pii.payment_type'=> 1))
               ->join('zbw_service_order_insurance soi ON soi.insurance_id = pii.id')
               ->join('zbw_service_insurance_detail sid ON sid.service_order_insurance_id = soi.id')
               ->join('zbw_template_rule tr ON tr.id = pii.rule_id')
               ->order('pii.create_time desc')->find();
      //  dump($resSb);
        //公积金
        $resGjj = $personInsuranceInfo->alias('pii')->field('pii.location,pii.amount,pii.payment_info,sid.state')->where(array('pii.base_id'=> $data['base_id'], 'pii.payment_type'=> 2))
               ->join('zbw_service_order_insurance soi ON soi.insurance_id = pii.id')
               ->join('zbw_service_insurance_detail sid ON sid.service_order_insurance_id = soi.id')
//               ->join('zbw_template_rule tr ON tr.id = pii.rule_id')
               ->order('pii.create_time desc')->find();
               //echo $personInsuranceInfo->getLastSql();
        $resGjj['payment_info'] = json_decode($resGjj['payment_info'], true);
       // dump($resGjj);
        //服务费
        $productInfo = M('service_product')->field('name,member_price,service_price,service_price_state')->where(array('id'=> $resSb['product_id']))->find();
      //  dump($productInfo);
        //企业信息
        $companyInfo = M('company_info')->field('company_name')->where(array('user_id'=> $data['user_id']))->find();
        return $data = array('sb'=> $resSb, 'gjj'=> $resGjj, 'productInfo'=> $productInfo, 'companyInfo'=> $companyInfo);
    }
*/
    protected function personBase($base_id, $field='*'){
        return M('person_base')->field($field)->where(array('id'=> $base_id))->find();
    }
  
    public function personDetail($data){
        $baseInfo = $this->personBase($data['base_id'], 'person_name,gender,card_num,mobile,residence_location,residence_type');
     //   dump($baseInfo);
        $personInsuranceInfo = M('person_insurance_info');
        $sbMonth = $personInsuranceInfo->alias('pii')->field('start_month, end_month')->where(array('pii.base_id'=> $data['base_id'], 'pii.payment_type'=> 1))->find();
        $gjjMonth = $personInsuranceInfo->alias('pii')->field('start_month, end_month')->where(array('pii.base_id'=> $data['base_id'], 'pii.payment_type'=> 2))->find();
        $personInsuranceInfo = M('person_insurance_info');
        //服务费
       // $productInfo = M('service_product')->field('name,member_price,service_price,service_price_state')->where(array('id'=> $resSb['product_id']))->find();
       //规则信息
        $ruleInfo = $personInsuranceInfo->alias('pii')->field('tr.name,pb.residence_type')
                    ->join('zbw_template_rule tr ON tr.id = pii.rule_id')
                    ->join('zbw_person_base pb ON pb.id = pii.base_id')
                    ->where(array('pii.base_id'=> $data['base_id']))->find();
        //dump($ruleInfo);
        //缴纳信息 以社保月分份为准
        
        // $res = $personInsuranceInfo->alias('pii')->field('soi.id,GROUP_CONCAT(soi.pay_date) as pay_date')
        //          ->join('zbw_service_order_insurance soi ON soi.insurance_id = pii.id')
        //        //  ->join('zbw_service_insurance_detail sid ON sid.service_order_insurance_id = soi.id')
        //          ->where('pii.payment_type = 1 AND pii.base_id = '.$data['base_id'])
        //          ->order('pii.create_time desc')->page(1, 6)->select();
        //          dump($res);
                  
        // if(strpos(',', $res['pay_date'])){
        //      $payDdate = explode(',', $res['pay_date']);
        // }else{
        //      $payDdate = array($res['pay_date']);
        // }
        // foreach ($payDdate as $key => $value) {
        //    $info = M('service_insurance_detail')->alias('sid')->field('')

        //    ->where('sid.pay_date = '.$value)->find();
        // }
        $res = $personInsuranceInfo->alias('pii')->field('GROUP_CONCAT(soi.id) as id')
                 ->join('zbw_service_order_insurance soi ON soi.insurance_id = pii.id')
               //  ->join('zbw_service_insurance_detail sid ON sid.service_order_insurance_id = soi.id')
                 ->where('pii.payment_type = 1 AND pii.base_id = '.$data['base_id'])
                 ->select();
                 dump($res);
        $resSb = M('service_insurance_detail')->alias('sid')->field('')
                    ->where('service_order_insurance_id in(\''.$res[0]['id'].'\')')
                    ->order('sid.pay_date asc')->page(1, 6)->select();
        dump($resSb);
        $res = $personInsuranceInfo->alias('pii')->field('GROUP_CONCAT(soi.id) as id')
                 ->join('zbw_service_order_insurance soi ON soi.insurance_id = pii.id')
               //  ->join('zbw_service_insurance_detail sid ON sid.service_order_insurance_id = soi.id')
                 ->where('pii.payment_type = 2 AND pii.base_id = '.$data['base_id'])
                 ->select(); 
                 dump($res);
        $resGjj = M('service_insurance_detail')->alias('sid')->field('')
                    ->where('service_order_insurance_id in(\''.$res[0]['id'].'\')')
                    ->order('sid.pay_date asc')->page(1, 6)->select();
        dump($resGjj);                 
    }
}