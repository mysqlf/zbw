<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Cron\Controller;
use Think\Controller;
use Common\Model\ServiceOrderModel;
use Common\Model\ServiceBillModel;
use Common\Model\Calculate;
use Common\Model\ServiceDiffModel;
use Common\Model\DiffCronModel;
/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class CronController extends Controller
{
    public function __construct ()
    {
        set_time_limit(0);
    }
    public function index()
    {
        header("HTTP/1.0 404 Not Found");
        exit();
    }
    
    public function diff ()
    {
        $old  = array();
        $data = array();
        //$diff = 
        echo min(max(5000 , 12000) , 15000);
        exit;
        //echo max(min(13000 , 12573) , 2577);
        //$d = D('DiffCron');
        // 办理
        // $d->_type = 1;
        // $d->_item = 1;
        // $d->_sign = array('insurance_info_id'=>'585,586'); //办理失败的
        // $d->_unsign = array('insrance_info_id'=>'585,586'); //办理成功的
        
        // 规则修改
        // $d->_type = 2;
        // $d->_item = 1;
        // $d->_rule_id = 121; //办理失败的
        // $d->_start = 201601 //办理成功的

        // 缴费异常
        // $d->_type = 3;
        // $d->_item = 1;
        // $d->_message_body = array(
        //  array('name'=>'养老保险' , 'company'=>true , 'person'=>true),
        //  array('name'=>'医疗保险' , 'company'=>true , 'person'=>true),
        //  array('name'=>'失业保险' , 'company'=>true , 'person'=>true),
        //  array('name'=>'工伤保险' , 'company'=>true , 'person'=>true),
        //  array('name'=>'生育保险' , 'company'=>true , 'person'=>false) 
        //)
        // $d->_sign = array('detail_id'=>1611) //办理成功的

        // $d->_type = 4;
        // $d->_item = 1;
        // $d->_sign = array('detail_id'=>'1611,1611'); //办理社保卡的
        // $d->_unsign = array('detail_id'=>'1611,1611'); //不办理社保卡的
        //$d->diffCron();
    }
    // public function cue ()
    // {
    //     $rule = M('template_rule' , 'zbw_')->where("id=54")->getField('rule');
    //     $disable = M('template_rule', 'zbw_')->where("id=110")->getField('rule');
    //     print_r($rule);
    //     echo "<br />";
    //     $json = json_encode(array('amount'=>100.00,'month'=>1));
    //     $SocInsure = new Calculate();
    //     $json = $SocInsure->detail($rule , $json , 1 , $disable , 1);
    //     print_r($json);
    // }
    // public function mklocation ()
    // {
    //     $m = M('location' , 'zbw_');
    //     $level = $m->field('DISTINCT(level)')->where("level<>'' AND id%10000=0 AND id%1000000<>0")->select();
    //     $location = $m->where("id%100=0 AND id%10000=0 AND id%1000000<>0")->select();
    //     foreach ($location as $v)
    //     {
    //         $name = '';
    //         if ($v['level'] == '地区')
    //         {
    //             $name = $v['level'];
    //         }
    //         else
    //         {
    //             $name = $v['level'].'级';
    //         }
    //         $array = array();
    //         $array['name'] = $name;
    //         $array['id'] = $v['id']+100;
    //         $array['state'] = 1;
    //         $m->add($array);
    //         //$array['id'] = M('location')->where("(id > ({$v['id']}%100000) AND id < ({$v['id']}%100000+1))")->find();
    //     }
    //     echo "ok";
    // }
    
    /**
     * 计算日期差
     * @param  [date] $datetime1 [日期1]
     * @param  [date] $datetime2 [日期2]
     * @param  string $format    [返回格式(天数等)]
     * @return [int]             [按格式返回数量]
     */
    private function _datediff ($datetime1 , $datetime2 , $format = '%R%a')
    {
        $datetime1 = date_create($datetime1);
        $datetime2 = date_create($datetime2);
        $diff = date_diff($datetime1 , $datetime2);
        return $diff->format($format);
    }
    
    /*
     * 处理过期产品订单
     */
    public function dealOrder()
    {
        $over = date('Y-m-d',time());
        $page = 1;
        $limit = 10000;
        $current = 0;
        $m = M('service_product_order' , 'zbw_');
        $sid = M('service_insurance_detail' , 'zbw_');
        $po = M('pay_order' , 'zbw_');
        $sp = M('service_product' , 'zbw_');
        $wl = M('warranty_location' , 'zbw_');
        $orderModel = D('ServiceOrder');
        $pii = D('PersonInsuranceInfo');
        $cnt = $m->where("service_state = 2 AND overtime <= '{$over}'")->count('id');
        $saveResult = $m->where("service_state = 2 AND overtime <= '{$over}'")->save(['service_state'=>3]);
        $ceil = ceil($cnt/$limit);
        $calculate = new Calculate;
        while ($page <= $ceil)
        {
            $current = ($page-1)*$limit;
            $page++;
            $list = $m
            ->field('*')
            ->where("service_state = 2 AND overtime <= '{$over}'")
            ->order('id DESC')
            ->limit($current , $limit)
            ->select();
            foreach ($list as $v)
            {
                if (!$v['turn_id'])
                {
                    $warranty = $pii->execute("SELECT c.* FROM (SELECT * FROM zbw_person_insurance_info WHERE user_id={$v['user_id']} AND product_id={$v['product_id']} AND ((state = 1 AND operate_state = 3) OR state = 2) ORDER BY id DESC) c GROUP BY c.base_id");
                    foreach ($warranty as $v)
                    {
                    	$insert = array();
                        //$v['product_id'] = $tproduct;
                        //$v['pay_order_id'] = null;
                        $v['handle_month'] = $this->_datediff($v['handle_month'] , 1);
                        $v['start_month']  = $this->_datediff($v['start_month'] , 1);
                        $v['pay_date']     = $this->_datediff($v['pay_date'] , 1);
                        $v['state']        = 3;
                        $v['operate_state'] = 2;
                        $v['creaete_time'] = $v['modify_time'] = date('Y-m-d H:i:s');
                        $insert = $v;
                    	$detail = array();
                    	$pii->startTrans();
                        //$detail['insurance_info_id'] =  $pii->add($insert , '' , true);
                        $detail['insurance_info_id'] =  $pii->addPersonInsuranceInfo($insert);
                        if (!$detail['insurance_info_id']) {
                        	$pii->rollback();
                        	continue;
                        }
                        $detail['pay_order_id'] = null;
                        $detail['type'] = $v['state'];
                        $detail['amount'] = $v['amount'];
                        $detail['pay_date'] = $v['pay_date'];
                        $detail['handle_month'] = $v['handle_month'];
                        $detail['rule_id'] = $v['rule_id'];
                        $detail['state'] = $v['operate_state'];
                        $detail['payment_type'] = $v['payment_type'];
                        $detail['creaete_time'] = $detail['modify_time'] = date('Y-m-d H:i:s');

                        $person = json_decode($v['payment_info']  , true);
                        $person['amount'] = $v['amount'];
                        $person['month']  = 1;
                        $rule = $m->query("SELECT tr.*,(SELECT rule FROM zbw_template_rule tr1 WHERE tr1.template_id=tr.template_id AND tr1.type=3) disabled FROM zbw_template_rule tr WHERE id={$v['rule_id']}");
                        $cue = json_decode($calculate->detail($v['rule'] , $person , $v['payment_type'] , $rule['disabled']) , true)['data'];
                        //$detail['current_detail'] = $detail['insurance_detail'] = json_encode($cue , JSON_UNESCAPED_UNICODE);
                        $detail['price'] = ($detail['type'] == 3 ? 0 : $cue['total']);

                        $detail['service_price'] = 0.00;
                        if ($sid->add($detail , '' , true)) {
                        	$pii->commit();
                        }else {
                        	$pii->rollback();
                        }
                    }
                }
                else
                {
                    $tproduct = $m->where("id={$v['turn_id']}")->getField('product_id');
                    $companyId = $sp->where("id={$tproduct}")->getField('company_id');
                    $warranty = $pii->execute("SELECT c.* FROM (SELECT * FROM zbw_person_insurance_info WHERE user_id={$v['user_id']} AND product_id={$v['product_id']} AND state IN (1,2) ORDER BY id DESC) c GROUP BY c.base_id");
                    $date = date('Ym');
                    foreach ($warranty as $v)
                    {
                    	$pii->startTrans();
	                    $oid = $po->where("user_id={$v['user_id']} AND company_id={$companyId} AND location={$v['location']} AND handle_month={$this->_monthcal ($date , 1)} AND `type`=2 AND `state`=0")->getField('id');
	                    //订单入库
	                    if (!$oid)
	                    {
	                        $oid = $po->add(
	                            array(
	                                'order_no' => $orderModel->orderNo(),
	                                'user_id'  => $v['user_id'],
	                                'company_id' => $companyId,
	                                'location' => $v['location'],
	                                'handle_month' => $this->_monthcal ($date , 1),
	                                'amount' => 0.00,
	                                'diff_amount' => 0.00,
	                                'actual_amount' => 0.00,
	                                'state' => 0,
	                                'type'  => 2,
	                                'pay_deadline' => date('Y-m-d H:i:s',mktime(0,0,0,substr($this->_monthcal($date,1), 4 , 2),date('d'),substr($this->_monthcal($date,1), 0 , 4))),
	                                'create_time' => date('Y-m-d H:i:s')
	                            )
	                        );
	                    }
	                    
                    	$insert = array();
                        $v['product_id'] = $tproduct;
                        //$v['pay_order_id'] = $oid;
                        $v['handle_month'] = $this->_datediff($v['handle_month'] , 1);
                        $v['start_month']  = $this->_datediff($v['start_month'] , 1);
                        $v['pay_date']     = $this->_datediff($v['pay_date'] , 1);
                        $v['state']        = 2;
                        $v['operate_state'] = 1;
                        $v['creaete_time'] = $v['modify_time'] = date('Y-m-d H:i:s');
                        $insert = $v;
                        $detail = array();
                        //$detail['insurance_info_id'] =  $pii->add($insert , '' , true);
                        $detail['insurance_info_id'] =  $pii->addPersonInsuranceInfo($insert);
                        if (!$detail['insurance_info_id']) {
                        	$pii->rollback();
                        	continue;
                        }
                        $detail['pay_order_id'] = $oid;
                        $detail['type'] = $v['state'];
                        $detail['amount'] = $v['amount'];
                        $detail['pay_date'] = $v['pay_date'];
                        $detail['handle_month'] = $v['handle_month'];
                        $detail['rule_id'] = $v['rule_id'];
                        $detail['state'] = $v['operate_state'];
                        $detail['payment_type'] = $v['payment_type'];
                        $detail['creaete_time'] = $detail['modify_time'] = date('Y-m-d H:i:s');

                        $person = json_decode($v['payment_info']  , true);
                        $person['amount'] = $v['amount'];
                        $person['month']  = 1;
                        $rule = $m->query("SELECT tr.*,(SELECT rule FROM zbw_template_rule tr1 WHERE tr1.template_id=tr.template_id AND tr1.type=3) disabled FROM zbw_template_rule tr WHERE id={$v['rule_id']}");
                        $cue = json_decode($calculate->detail($v['rule'] , $person , $v['payment_type'] , $rule['disabled']) , true)['data'];
                        $detail['current_detail'] = $detail['insurance_detail'] = json_encode($cue , JSON_UNESCAPED_UNICODE);
                        $detail['price'] = ($detail['type'] == 3 ? 0 : $cue['total']);

                        $locationWarranty = $wl->where("service_product_order_id={$v['turn_id']} AND location")->find();
                        $detail['service_price'] = $v['payment_type'] == 1 ? $locationWarranty['soc_service_price'] : $locationWarranty['pro_service_price'];
                        if ($sid->add($detail , '' , true)) {
                        	$pii->commit();
                        }else {
                        	$pii->rollback();
                        }
                    }
                }
                // $pii->execute("SELECT * FROM zbw_person_insurance_info WHERE product_id IN ({$v['id']})");
            }
        }
        //$m->where("overtime = '{$overtime}'")->save(array('service_state'=>3,'update_date'=>date('Y-m-d H:i:s',time())));
    }
    
    /**
     * 购买产品30天过期通知
     * @return [null]
     */
    public function overdueMsg ()
    {
        $title = '产品过期通知';
        $msgTemplate = "尊敬的#公司#：<p>您购买的#名称#，将于30天后过期,如有需要，请及时与服务商联系。</p>";
        $over = mktime(0 , 0 , 0 , date('m') , date('d') , date('Y')) + (24*3600*30);//计算30天过期时间戳
        $over = date('Y-m-d H:i:s',$over);
        $page = 1;
        $limit = 10000;
        $current = 0;
        $m = M('service_product_order' , 'zbw_');
        $cnt = $m->where("service_state = 2 AND overtime = '{$over}'")->count('id');
        $ceil = ceil($cnt/$limit);
        while ($page <= $ceil)
        {
            $current = ($page-1)*$limit;
            $page++;
            $m = M('service_product_order' , 'zbw_');
            $companys = $m->alias('o')
               ->field('o.id,o.user_id,ci.company_name,sp.name,o.product_id')
               ->join('LEFT JOIN zbw_service_product sp ON sp.id=o.product_id')
               ->join('LEFT JOIN zbw_company_info ci ON ci.user_id=o.user_id')
               ->where("service_state = 2 AND overtime = '{$over}'")
               ->order('o.id DESC')
               ->limit($current , $limit)
               ->select();
            $insert = array ();
            $nowTime = date('Y-m-d H:i:s');
            foreach ($companys as $k=>$v)
            {
                $msg = $msgTemplate.'<a href="/Company-Information-serviceDetail-id-'.$v['id'].'.html" target="_blank">点击查看</a></p>';
                $detail = str_replace('#名称#' , $v['name'] , str_replace('#公司#' , $v['company_name'] , $msg));
                array_push($insert , array('title' => $title , 'detail' => $detail , 'user_id' => $v['user_id'] , 'create_time' => $nowTime));
            }
            $m = M('user_msg','zbw_');
            $m->addAll($insert);
        }
    }
    
    /**
     * 订单截止时间前5天通知
     * @return [type] [description]
     */
    public function increaseMsg ()
    {
        $title = '订单付款通知';
        $over = date('Y-m-d H:i:s',mktime(0 , 0 , 0 , date('m') , date('d') , date('Y')) + (24*3600*5));//计算5天过期时间戳,取当天零点
        $page = 1;
        $limit = 10000;
        $current = 0;
        $m = M('pay_order' , 'zbw_');
        $cnt = $m->where("state = 0 AND pay_deadline = '{$over}'")->count('id');
        $ceil = ceil($cnt/$limit);
        $msgTemplate = "尊敬的#公司#：<p>您有订单将在5天后过期，请及时处理，逾期将无法办理，感谢您的配合。</p>";
        while ($page <= $ceil)
        {
            $current = ($page-1)*$limit;
            $page++;
            //$m =  M('pay_order' , 'zbw_');
            $companys = $m->alias('o')
                ->field('o.user_id,o.type,o.company_id,o.id,ci.company_name')
                ->join('LEFT JOIN zbw_company_info ci ON ci.user_id=o.user_id')
                ->where("state = 0 AND pay_deadline = '{$over}'")
                ->order('o.id DESC')
                ->limit($current , $limit)
                ->select();
            $insert = array ();
            $nowTime = date('Y-m-d H:i:s');
            foreach ($companys as $v)
            {
                if ($v['type']==1) {
                    $msg = $msgTemplate.'<a href="/Company-Order-payservice-orderId-'.$v['id'].'.html" target="_blank">点击查看</a></p>';
                }elseif($v['type']==2){
                    $msg = $msgTemplate.'<a href="/Company-Order-payinc-orderId-'.$v['id'].'.html" target="_blank">点击查看</a></p>';
                }elseif($v['type']==3){
                    $msg = $msgTemplate.'<a href="/Company-Order-paysalary-orderId-'.$v['id'].'.html" target="_blank">点击查看</a></p>';
                }else {
                	$msg = $msgTemplate;
                }
                $detail = str_replace('#公司#' , $v['company_name'] , $msg);
                array_push($insert , array('title' => $title , 'detail' => $detail , 'user_id' => $v['user_id'] , 'create_time' => $nowTime));
            }
            $m = M('user_msg','zbw_');
            $m->addAll($insert);
        }
    }
    
    //根据失败数据更改状态
    /**
     * undocumented function
     * @param int $operateState -1审核失败 -2支付失败 -3办理失败
     * @return void
     * @author rohochan
     **/
    private function changePersonInsuranceState($operateState){
    	$nowTime = date('Y-m-d H:i:s');
    	$personInsuranceInfo = M('PersonInsuranceInfo','zbw_');
    	
    	if (-3 == $operateState) {
	    	$handleTime = time();
	    	$yearMonth = date('Ym',$handleTime);
	    	$day = date('d',$handleTime);
    		//$personInsuranceInfoResult = $personInsuranceInfo->alias('pii')->field('pii.id,pii.insurance_id,pii.state,pii.payment_type,pii.operate_state,sid.id as sid_id, sid.type as sid_type, sid.payment_type as sid_payment_type')->join('left join zbw_template_rule as tr on tr.id = pii.rule_id')->join('left join zbw_template as t on t.id = tr.template_id')->join('left join zbw_service_insurance_detail as sid on sid.insurance_info_id = pii.id')->where(['pii.operate_state'=>['in',[2,3]],'pii.handle_month'=>$yearMonth,'t.soc_deadline'=>$day,'sid.state'=>-3,'sid.replenish'=>0])->select();
    		//社保公积金独立处理
    		//$personInsuranceInfoResult = $personInsuranceInfo->alias('pii')->field('pii.id,pii.insurance_id,pii.state,pii.payment_type,pii.operate_state,sid.id as sid_id, sid.type as sid_type, sid.payment_type as sid_payment_type')->join('left join zbw_template_rule as tr on tr.id = pii.rule_id')->join('left join zbw_service_insurance_detail as sid on sid.insurance_info_id = pii.id')->where(['pii.operate_state'=>['in',[2,3]],'pii.handle_month'=>$yearMonth,'tr.deadline'=>$day,'sid.state'=>-3,'sid.replenish'=>0])->select();
    		//社保公积金合并处理
    		$personInsuranceInfoResult = $personInsuranceInfo->alias('pii')->field('pii.id,pii.insurance_id,pii.state,pii.payment_type,pii.operate_state,sid.id as sid_id, sid.type as sid_type, sid.payment_type as sid_payment_type')->join('left join zbw_template_rule as tr on tr.id = pii.rule_id')->join('left join zbw_service_insurance_detail as sid on sid.insurance_info_id = pii.id')->where(['pii.operate_state'=>['in',[2,3]],'pii.handle_month'=>$yearMonth,'tr.deadline'=>$day,'sid.state'=>-3,'sid.replenish'=>0,'pii.payment_type'=>1])->select();
    		
    		$proPersonInsuranceInfoResult = $personInsuranceInfo->alias('propii')->field('propii.id,propii.insurance_id,propii.state,propii.payment_type,propii.operate_state,sid.id as sid_id, sid.type as sid_type, sid.payment_type as sid_payment_type')->join('left join zbw_person_insurance_info as socpii on socpii.payment_type = 1 and propii.payment_type = 2 and socpii.user_id = propii.user_id and socpii.base_id = propii.base_id and socpii.handle_month = propii.handle_month')->join('left join zbw_template_rule as tr on tr.id = socpii.rule_id')->join('left join zbw_service_insurance_detail as sid on sid.insurance_info_id = propii.id')->where(['propii.operate_state'=>['in',[2,3]],'propii.handle_month'=>$yearMonth,'tr.deadline'=>$day,'sid.state'=>-3,'sid.replenish'=>0,'propii.payment_type'=>2])->select();
    		
	    	if ($proPersonInsuranceInfoResult) {
	    		if ($personInsuranceInfoResult) {
		    		foreach ($proPersonInsuranceInfoResult as $key => $value) {
		    			array_push($personInsuranceInfoResult,$value);
		    		}
	    		}else {
	    			$personInsuranceInfoResult = $proPersonInsuranceInfoResult;
	    		}
	    	}
	    	
	    	if ($personInsuranceInfoResult) {
	    		$piIdArray = array();
	    		foreach ($personInsuranceInfoResult as $key => $value) {
	    			$piIdArray['all'][$value['insurance_id']] = $value['insurance_id'];
	    			if (1 == $value['state'] || 2 == $value['state']) {
	    				$piIdArray['increase'][$value['insurance_id']] = $value['insurance_id'];
	    			}else if (3 == $value['state']) {
	    				$piIdArray['reduce'][$value['insurance_id']] = $value['insurance_id'];
	    			}
	    		}
	    		$piIds['all'] = implode(',',$piIdArray['all']);
	    		$piIds['increase'] = implode(',',$piIdArray['increase']);
	    		$piIds['reduce'] = implode(',',$piIdArray['reduce']);
	    		$personInsuranceSaveResult = array('increase'=>true,'reduce'=>true);
	    		if ($piIds['all']) {
	    			$personInsurance = M('PersonInsurance','zbw_');
	    			$personInsurance->startTrans();
			    	if ($piIds['increase']) {
			    		$personInsuranceSaveResult['increase'] = $personInsurance->where(['id'=>['in',$piIds['increase']]])->save(['state'=>0,'modify_time'=>$nowTime]);
			    	}
			    	if ($piIds['reduce']) {
			    		$personInsuranceSaveResult['reduce'] = $personInsurance->where(['id'=>['in',$piIds['reduce']]])->save(['state'=>2,'modify_time'=>$nowTime]);
			    	}
			    	if (false !== $personInsuranceSaveResult['increase'] && false !== $personInsuranceSaveResult['reduce']) {
			    		$personInsurance->commit();
			    	}else {
			    		$personInsurance->rollback();
			    	}
	    		}
	    	}
    	}else {
	    	$handleTime = strtotime('+ '.C('INSURANCE_HANDLE_DAYS').' day');
	    	$yearMonth = date('Ym',$handleTime);
	    	$day = date('d',$handleTime);
    		//$personInsuranceInfoResult = $personInsuranceInfo->alias('pii')->field('pii.id,pii.insurance_id')->join('left join zbw_template_rule as tr on tr.id = pii.rule_id')->join('left join zbw_template as t on t.id = tr.template_id')->where(['pii.state'=>['in','1,2'],'pii.operate_state'=>$operateState,'pii.handle_month'=>$yearMonth,'t.soc_deadline'=>$day])->select();
    		//社保公积金独立处理
    		//$personInsuranceInfoResult = $personInsuranceInfo->alias('pii')->field('pii.id,pii.insurance_id')->join('left join zbw_template_rule as tr on tr.id = pii.rule_id')->where(['pii.state'=>['in','1,2'],'pii.operate_state'=>$operateState,'pii.handle_month'=>$yearMonth,'tr.deadline'=>$day])->select();
    		
	    	//社保公积金合并处理
	    	$personInsuranceInfoResult = $personInsuranceInfo->alias('pii')->field('pii.id,pii.insurance_id')->join('left join zbw_template_rule as tr on tr.id = pii.rule_id')->where(['pii.state'=>['in','1,2'],'pii.operate_state'=>$operateState,'pii.handle_month'=>$yearMonth,'tr.deadline'=>$day,'pii.payment_type'=>1])->select();
	    	$proPersonInsuranceInfoResult = $personInsuranceInfo->field('propii.id,propii.insurance_id')->alias('propii')->join('left join zbw_person_insurance_info as socpii on socpii.payment_type = 1 and socpii.user_id = propii.user_id and socpii.base_id = propii.base_id and socpii.handle_month = propii.handle_month')->join('left join zbw_template_rule as tr on tr.id = socpii.rule_id')->where(['propii.state'=>['in','1,2'],'propii.operate_state'=>$operateState,'propii.handle_month'=>$yearMonth,'tr.deadline'=>$day,'propii.payment_type'=>2])->select();
	    	if ($proPersonInsuranceInfoResult) {
	    		if ($personInsuranceInfoResult) {
		    		foreach ($proPersonInsuranceInfoResult as $key => $value) {
		    			array_push($personInsuranceInfoResult,$value);
		    		}
	    		}else {
	    			$personInsuranceInfoResult = $proPersonInsuranceInfoResult;
	    		}
	    	}
	    	
	    	if ($personInsuranceInfoResult) {
	    		$piIdArray = array();
	    		foreach ($personInsuranceInfoResult as $key => $value) {
	    			$piIdArray[$value['insurance_id']] = $value['insurance_id'];
	    		}
	    		$piIds = implode(',',$piIdArray);
	    		if ($piIds) {
	    			$personInsurance = M('PersonInsurance','zbw_');
	    			$personInsurance->startTrans();
			    	$personInsuranceSaveResult = $personInsurance->where(['id'=>['in',$piIds]])->save(['state'=>0,'modify_time'=>$nowTime]);
			    	if (false !== $personInsuranceSaveResult) {
			    		$personInsurance->commit();
			    	}else {
			    		$personInsurance->rollback();
			    	}
	    		}
	    	}
    	}
    }
    
    //审核过期
    public function approveOverDay(){
    	$nowTime = date('Y-m-d H:i:s');
    	$handleTime = strtotime('+ '.C('INSURANCE_HANDLE_DAYS').' day');
    	$yearMonth = date('Ym',$handleTime);
    	$day = date('d',$handleTime);
    	$remark = '到达付款截止日,自动处理审核过期数据';
    	$personInsuranceInfo = M('PersonInsuranceInfo','zbw_');
    	//处理未审核数据
    	//$personInsuranceInfoResult = $personInsuranceInfo->field('pii.id')->alias('pii')->join('left join zbw_template_rule as tr on tr.id = pii.rule_id')->join('left join zbw_template as t on t.id = tr.template_id')->where(['pii.operate_state'=>0,'pii.handle_month'=>$yearMonth,'t.soc_deadline'=>$day])->select();
    	//社保公积金独立处理
    	//$personInsuranceInfoResult = $personInsuranceInfo->field('pii.id')->alias('pii')->join('left join zbw_template_rule as tr on tr.id = pii.rule_id')->where(['pii.operate_state'=>0,'pii.handle_month'=>$yearMonth,'tr.deadline'=>$day])->select();
    	//社保公积金合并处理
    	$personInsuranceInfoResult = $personInsuranceInfo->field('pii.id')->alias('pii')->join('left join zbw_template_rule as tr on tr.id = pii.rule_id')->where(['pii.operate_state'=>0,'pii.handle_month'=>$yearMonth,'tr.deadline'=>$day,'pii.payment_type'=>1])->select();
    	$proPersonInsuranceInfoResult = $personInsuranceInfo->field('propii.id')->alias('propii')->join('left join zbw_person_insurance_info as socpii on socpii.payment_type = 1 and socpii.user_id = propii.user_id and socpii.base_id = propii.base_id and socpii.handle_month = propii.handle_month')->join('left join zbw_template_rule as tr on tr.id = socpii.rule_id')->where(['propii.operate_state'=>0,'propii.handle_month'=>$yearMonth,'tr.deadline'=>$day,'propii.payment_type'=>2])->select();
    	if ($proPersonInsuranceInfoResult) {
    		if ($personInsuranceInfoResult) {
				foreach ($proPersonInsuranceInfoResult as $key => $value) {
					array_push($personInsuranceInfoResult,$value);
				}
    		}else {
    			$personInsuranceInfoResult = $proPersonInsuranceInfoResult;
    		}
    	}
    	
    	if ($personInsuranceInfoResult) {
    		$piiIdArray = array();
    		foreach ($personInsuranceInfoResult as $key => $value) {
    			$piiIdArray[$value['id']] = $value['id'];
    		}
    		$piiIds = implode(',',$piiIdArray);
    		if ($piiIds) {
    			$personInsuranceInfo->startTrans();
		    	$personInsuranceInfoSaveResult = $personInsuranceInfo->where(['id'=>['in',$piiIds]])->save(['operate_state'=>-1,'remark'=>$remark,'modify_time'=>$nowTime]);
		    	$serviceInsuranceDetail = M('ServiceInsuranceDetail','zbw_');
		    	$serviceInsuranceDetailSaveResult = $serviceInsuranceDetail->where(['insurance_info_id'=>['in',$piiIds]])->save(['state'=>-1,'note'=>$remark,'modify_time'=>$nowTime]);
		    	if (false !== $personInsuranceInfoSaveResult && false !== $serviceInsuranceDetailSaveResult) {
		    		$personInsuranceInfo->commit();
		    	}else {
		    		$personInsuranceInfo->rollback();
		    	}
    		}
    	}
    	
    	//处理报增审核失败数据
    	$this->changePersonInsuranceState(-1);
    }
    
    //支付过期
    public function payOverDay()
    {
        $dtime = date('Y-m-d H:i:s' , time());
        $over = mktime(0 , 0 , 0 , date('m') , date('d') , date('Y'));
    	$remark = '到达付款截止日,自动处理支付过期数据';
        $payOrder = M('pay_order' , 'zbw_');
        $USP=M('user_service_provider','zbw_');
        $personInsuranceInfo = M('person_insurance_info' , 'zbw_');
        $insuranceDetail = M('service_insurance_detail' , 'zbw_');
        //$personInsuranceInfo->where("pay_order_id IN (SELECT id FROM zbw_pay_order WHERE pay_deadline <= '{$dtime}' AND `state` = 0)")->save(array('operate_state'=>-2,'modify_time'=>$dtime));
        //$insuranceDetail->where("insurance_info_id IN (SELECT id FROM zbw_person_insurance_info WHERE pay_order_id IN (SELECT id FROM zbw_pay_order WHERE pay_deadline <= '{$dtime}' AND `state` = 0))")->save(array('state'=>-2,'modify_time'=>$dtime));
        $personInsuranceInfo->where("id IN (SELECT DISTINCT insurance_info_id FROM zbw_service_insurance_detail WHERE pay_order_id IN (SELECT id FROM zbw_pay_order WHERE pay_deadline <= '{$dtime}' AND `state` = 0))")->save(array('operate_state'=>-2,'remark'=>$remark,'modify_time'=>$dtime));
        $insuranceDetail->where("pay_order_id IN (SELECT id FROM zbw_pay_order WHERE pay_deadline <= '{$dtime}' AND `state` = 0)")->save(array('state'=>-2,'note'=>$remark,'modify_time'=>$dtime));
        $payOrder->where("pay_deadline <= '{$dtime}' AND `state`=0")->save(array('state'=>-1));
        $diff_amount=$payOrder->field('diff_amount,user_id,company_id,id')->where("pay_deadline <= '{$dtime}' AND `diff_amount` != 0 AND `state` = -1")->select();
        foreach ($diff_amount as $key => $value) {
            #差额不为0的过期订单,将差额退回对应服务商
            if ($value['diff_amount']!=0) {
                $payOrder->startTrans();
                $where=array('user_id'=>$value['user_id'],'company_id'=>$value['company_id'],'id'=>$value['id']);
                $uspwhere=array('user_id'=>$value['user_id'],'company_id'=>$value['company_id']);
                $nowdiff_amount=$USP->where($uspwhere)->getField('diff_amount');#查询当前差额
                $data=array('diff_amount'=>($value['diff_amount']+$nowdiff_amount));
                $result1=$payOrder->where($where)->save(array('diff_amount'=>0));#订单内差额改为0
                $result2=$USP->where($uspwhere)->save($data);#将差额退回
                if ($result1 && $result2) {
                    $payOrder->commit();
                }else{
                    $payOrder->rollback();
                }
            }
        }
        
    	//处理报增支付失败数据
    	$this->changePersonInsuranceState(-2);
    }
    
    //办理过期
	public function handleOverDayOld(){
    	$nowTime = date('Y-m-d H:i:s');
    	$personInsuranceInfo = M('PersonInsuranceInfo','zbw_');
    	$personInsuranceInfoResult = $personInsuranceInfo->field('pii.id,pii.state,pii.payment_type')->alias('pii')->join('left join zbw_template_rule as tr on tr.id = pii.rule_id')->join('left join zbw_template as t on t.id = tr.template_id')->where(['pii.operate_state'=>2,'pii.handle_month'=>date('Ym'),'t.soc_deadline'=>date('d')])->select();
    	//dump($personInsuranceInfoResult);
    	if ($personInsuranceInfoResult) {
    		$piiIdArray = array();
    		foreach ($personInsuranceInfoResult as $key => $value) {
    			$piiIdArray['all'][$value['id']] = $value['id'];
    			if (1 == $value['state'] || 2 == $value['state']) {
    				$piiIdArray['increase'][0][$value['id']] = $value['id'];
    				$piiIdArray['increase'][$value['payment_type']][$value['id']] = $value['id'];
    			}elseif (3 == $value['state']) {
    				$piiIdArray['reduce'][0][$value['id']] = $value['id'];
    				$piiIdArray['reduce'][$value['payment_type']][$value['id']] = $value['id'];
    			}
    		}
    		$piiIds = array();
    		$piiIds['all'] = implode(',',$piiIdArray['all']);
    		$piiIds['increase'] = implode(',',$piiIdArray['increase'][0]);
    		$piiIds['reduce'] = implode(',',$piiIdArray['reduce'][0]);
    		if ($piiIds['all']) {
    			$personInsuranceInfo->startTrans();
    			$remark = '到达报增减截止日,自动处理办理过期数据';
		    	$personInsuranceInfoSaveResult = $personInsuranceInfo->where(['id'=>['in',$piiIds['all']]])->save(['operate_state'=>3,'remark'=>$remark,'modify_time'=>$nowTime]);
		    	$serviceInsuranceDetail = M('ServiceInsuranceDetail','zbw_');
		    	$serviceInsuranceDetailSaveResult = [1=>true,2=>true];
		    	if ($piiIds['increase']) {
		    		$serviceInsuranceDetailSaveResult[1] = $serviceInsuranceDetail->where(['insurance_info_id'=>['in',$piiIds['increase']]])->save(array('state'=>-3,'note'=>$remark,'modify_time'=>date('Y-m-d H:i:s')));
		    	}
		    	
		    	if ($piiIds['reduce']) {
		    		$serviceInsuranceDetailSaveResult[2] = $serviceInsuranceDetail->where(['insurance_info_id'=>['in',$piiIds['reduce']]])->save(array('state'=>3,'note'=>$remark,'modify_time'=>date('Y-m-d H:i:s')));
		    	}
		    	
		    	if (false !== $personInsuranceInfoSaveResult && false !== $serviceInsuranceDetailSaveResult[1] && false !== $serviceInsuranceDetailSaveResult[2]) {
					$diffCron = D('DiffCron');
					$diffCron->_type = 1;
					for ($i=1; $i <= 2; $i++) {
						$diffCron->_item = $i;
			        	if ($piiIdArray['increase'][$i]) {
			        		$diffCron->_sign = array('insurance_info_id'=>implode(',',$piiIdArray['increase'][$i]));
			        	}
			        	if ($piiIdArray['reduce'][$i]) {
					    	$diffCron->_unsign = array('insurance_info_id'=>implode(',',$piiIdArray['reduce'][$i]));
			        	}
			        	$diffCron->diffCron();
					}
		    		$personInsuranceInfo->commit();
		    	}else {
		    		$personInsuranceInfo->rollback();
		    	}
    		}
    	}
    	
    	//处理报增办理失败数据
    	$this->changePersonInsuranceState(-3);
	}
	
    //办理过期
	public function handleOverDay(){
    	$nowTime = date('Y-m-d H:i:s');
    	$personInsuranceInfo = M('PersonInsuranceInfo','zbw_');
    	
    	//处理没有全部办理完成的数据
    	//$personInsuranceInfoResult = $personInsuranceInfo->alias('pii')->field('pii.id,pii.state,pii.payment_type,pii.operate_state,sid.id as sid_id, sid.type as sid_type, sid.payment_type as sid_payment_type')->join('left join zbw_template_rule as tr on tr.id = pii.rule_id')->join('left join zbw_template as t on t.id = tr.template_id')->join('left join zbw_service_insurance_detail as sid on sid.insurance_info_id = pii.id')->where(['pii.operate_state'=>['in',[2,3]],'pii.handle_month'=>date('Ym'),'t.soc_deadline'=>date('d'),'sid.state'=>2])->select();
    	//社保公积金独立处理
    	//$personInsuranceInfoResult = $personInsuranceInfo->alias('pii')->field('pii.id,pii.state,pii.payment_type,pii.operate_state,sid.id as sid_id, sid.type as sid_type, sid.payment_type as sid_payment_type')->join('left join zbw_template_rule as tr on tr.id = pii.rule_id')->join('left join zbw_service_insurance_detail as sid on sid.insurance_info_id = pii.id')->where(['pii.operate_state'=>['in',[2,3]],'pii.handle_month'=>date('Ym'),'tr.deadline'=>date('d'),'sid.state'=>2])->select();
    	
    	//社保公积金合并处理
    	$personInsuranceInfoResult = $personInsuranceInfo->alias('pii')->field('pii.id,pii.state,pii.payment_type,pii.operate_state,sid.id as sid_id, sid.type as sid_type, sid.payment_type as sid_payment_type')->join('left join zbw_template_rule as tr on tr.id = pii.rule_id')->join('left join zbw_service_insurance_detail as sid on sid.insurance_info_id = pii.id')->where(['pii.operate_state'=>['in',[2,3]],'pii.handle_month'=>date('Ym'),'tr.deadline'=>date('d'),'sid.state'=>2,'pii.payment_type'=>1])->select();
    	
    	$proPersonInsuranceInfoResult = $personInsuranceInfo->alias('propii')->field('propii.id,propii.state,propii.payment_type,propii.operate_state,sid.id as sid_id, sid.type as sid_type, sid.payment_type as sid_payment_type')->join('left join zbw_person_insurance_info as socpii on socpii.payment_type = 1 and propii.payment_type = 2 and socpii.user_id = propii.user_id and socpii.base_id = propii.base_id and socpii.handle_month = propii.handle_month')->join('left join zbw_template_rule as tr on tr.id = socpii.rule_id')->join('left join zbw_service_insurance_detail as sid on sid.insurance_info_id = propii.id')->where(['propii.operate_state'=>['in',[2,3]],'propii.handle_month'=>date('Ym'),'tr.deadline'=>date('d'),'sid.state'=>2,'propii.payment_type'=>2])->select();
    	
    	if ($proPersonInsuranceInfoResult) {
    		if ($personInsuranceInfoResult) {
				foreach ($proPersonInsuranceInfoResult as $key => $value) {
					array_push($personInsuranceInfoResult,$value);
				}
    		}else {
    			$personInsuranceInfoResult = $proPersonInsuranceInfoResult;
    		}
    	}
    	
    	if ($personInsuranceInfoResult) {
    		$piiIdArray = array();
    		$sidIdArray = array();
    		foreach ($personInsuranceInfoResult as $key => $value) {
    			$piiIdArray['all'][$value['id']] = $value['id'];
    			
    			//if (2 == $value['operate_state']) {
    			//	$piiIdArray['handle_over_day'][$value['id']] = $value['id'];
    			//}
    			//if (1 == $value['state']) {
    			//	$piiIdArray['increase'][0][$value['id']] = $value['id'];
    			//	$piiIdArray['increase'][$value['payment_type']][$value['id']] = $value['id'];
    			//}elseif (3 == $value['state']) {
    			//	$piiIdArray['reduce'][0][$value['id']] = $value['id'];
    			//	$piiIdArray['reduce'][$value['payment_type']][$value['id']] = $value['id'];
    			//}
    			
    			$sidIdArray['all'][$value['sid_id']] = $value['sid_id'];
    			if (1 == $value['sid_type'] || 2 == $value['sid_type']) {
    				$sidIdArray['increase'][0][$value['sid_id']] = $value['sid_id'];
    				$sidIdArray['increase'][$value['sid_payment_type']][$value['sid_id']] = $value['sid_id'];
    			}elseif (3 == $value['sid_type']) {
    				$sidIdArray['reduce'][0][$value['sid_id']] = $value['sid_id'];
    				$sidIdArray['reduce'][$value['sid_payment_type']][$value['sid_id']] = $value['sid_id'];
    			}
    		}
    		$piiIds = array();
    		$piiIds['all'] = implode(',',$piiIdArray['all']);
    		//$piiIds['handle_over_day'] = implode(',',$piiIdArray['handle_over_day']);
    		//$piiIds['increase'] = implode(',',$piiIdArray['increase'][0]);
    		//$piiIds['reduce'] = implode(',',$piiIdArray['reduce'][0]);
    		
    		$sidIds = array();
    		$sidIds['all'] = implode(',',$sidIdArray['all']);
    		$sidIds['increase'] = implode(',',$sidIdArray['increase'][0]);
    		$sidIds['reduce'] = implode(',',$sidIdArray['reduce'][0]);
    		//dump($piiIdArray);
    		//dump($piiIds);
    		//dump($sidIdArray);
    		//dump($sidIds);
    		if ($piiIds['all']) {
    			$personInsuranceInfo->startTrans();
    			$remark = '到达报增减截止日,自动处理办理过期数据';
		    	$personInsuranceInfoSaveResult = $personInsuranceInfo->where(['id'=>['in',$piiIds['all']]])->save(['operate_state'=>3,'remark'=>$remark,'modify_time'=>$nowTime]);
		    	$serviceInsuranceDetail = M('ServiceInsuranceDetail','zbw_');
		    	$serviceInsuranceDetailSaveResult = [1=>true,2=>true];
		    	if ($sidIds['increase']) {
		    		$serviceInsuranceDetailSaveResult[1] = $serviceInsuranceDetail->where(['id'=>['in',$sidIds['increase']]])->save(array('state'=>-3,'note'=>$remark,'modify_time'=>date('Y-m-d H:i:s')));
		    	}
		    	
		    	if ($sidIds['reduce']) {
		    		$serviceInsuranceDetailSaveResult[2] = $serviceInsuranceDetail->where(['id'=>['in',$sidIds['reduce']]])->save(array('state'=>3,'note'=>$remark,'modify_time'=>date('Y-m-d H:i:s')));
		    	}
		    	if (false !== $personInsuranceInfoSaveResult && false !== $serviceInsuranceDetailSaveResult[1] && false !== $serviceInsuranceDetailSaveResult[2]) {
					$diffCron = D('DiffCron');
					$diffCron->_type = 1;
					for ($i=1; $i <= 2; $i++) {
						$diffCron->_item = $i;
			        	if ($sidIdArray['increase'][$i]) {
			        		//$diffCron->_sign = array('insurance_info_id'=>implode(',',$piiIdArray['increase'][$i]));
			        		$diffCron->_sign = array('detail_id'=>implode(',',$sidIdArray['increase'][$i]));
			        	}
			        	if ($sidIdArray['reduce'][$i]) {
					    	//$diffCron->_unsign = array('insurance_info_id'=>implode(',',$piiIdArray['reduce'][$i]));
					    	$diffCron->_unsign = array('detail_id'=>implode(',',$sidIdArray['reduce'][$i]));
			        	}
			        	$diffCron->diffCron();
					}
		    		$personInsuranceInfo->commit();
		    	}else {
		    		$personInsuranceInfo->rollback();
		    	}
    		}
    	}
    	
    	//处理报增办理失败数据
    	$this->changePersonInsuranceState(-3);
	}
    
    /**
     * 自动生成在保订单
     * @return null
     */
    public function warranty()
    {
        $month = date('m');
        $day   = date('d');
        $date  = date('Ym');
        $remark = '到达报增减截止日,自动处理在保数据';
        $calculate = new Calculate;
        $m = M('template_rule' , 'zbw_');
        
        //$info = M('person_insurance_info' , 'zbw_');
        $info = D('PersonInsuranceInfo');
        $pi = M('person_insurance' , 'zbw_');
        $order = M('pay_order' , 'zbw_');
        $product = M('service_product' , 'zbw_');
        $detailModel = M('service_insurance_detail','zbw_');
        $orderModel = D('ServiceOrder');
        $wl = M('warranty_location' , 'zbw_');
        $rule = $m->query("SELECT tr.* FROM zbw_template_rule tr LEFT JOIN zbw_template zt ON zt.id=tr.template_id WHERE deadline={$day} AND tr.state=1");
        //查找报增减截止日是当天的规则
        //$rule = $m->query("SELECT tr.*,t.soc_deadline,t.soc_payment_type,t.pro_deadline,t.pro_payment_type,(SELECT rule FROM zbw_template_rule tr1 WHERE tr1.template_id=tr.template_id AND tr1.type=3) disabled FROM zbw_template_rule tr LEFT JOIN zbw_template t ON t.id=tr.template_id WHERE t.soc_deadline={$day} AND tr.state=1 AND tr.type IN (1,2) ORDER BY tr.id DESC");
        //循环规则
        foreach ($rule as $val)
        {
            $page = 1;
            $limit = 10000;
            $current = 0;
            //应用规则的记录
            //$cnt = $pi->where("rule_id={$val['id']} AND `state` IN (1,2)")->count();
            $soccnt = $pi->where("rule_id={$val['id']} AND `state` IN (1,2) and payment_type = 1")->count();
            $procnt = $pi->alias('propi')->join('LEFT JOIN zbw_person_insurance as socpi on socpi.payment_type = 1 and propi.payment_type=2 and socpi.user_id = propi.user_id and socpi.base_id = propi.base_id')->where("socpi.rule_id={$val['id']} AND propi.state IN (1,2) and propi.payment_type = 2")->order('propi.id DESC')->limit($current,$tail)->count('propi.id');
            $cnt = $soccnt + $procnt;
            $ceil = ceil($cnt/$limit);
            while ($page <= $ceil)
            {
                $current = ($page-1)*$limit;
                $tail = $page*$limit;
                $page++;
                //应用规则的在保记录
	    		//社保公积金独立处理
	    		//$list = $pi->where("rule_id={$val['id']} AND state IN (1,2)")->order('id DESC')->limit($current,$tail)->select();
	    		
		    	//社保公积金合并处理
                $list = $pi->where("rule_id={$val['id']} AND state IN (1,2) and payment_type = 1")->order('id DESC')->limit($current,$tail)->select();
                
                $prolist = $pi->field('propi.*,tr.rule')->alias('propi')->join('LEFT JOIN zbw_person_insurance as socpi on socpi.payment_type = 1 and propi.payment_type=2 and socpi.user_id = propi.user_id and socpi.base_id = propi.base_id')->join('LEFT JOIN zbw_template_rule as tr on propi.rule_id = tr.id')->where("socpi.rule_id={$val['id']} AND propi.state IN (1,2) and propi.payment_type = 2")->order('propi.id DESC')->limit($current,$tail)->select();
                if ($prolist) {
                	if ($list) {
	                	foreach ($prolist as $key => $value) {
	                		array_push($list,$value);
	                	}
                	}else {
                		$list = $prolist;
                	}
                }
                
                $insertInfo = array();
                $handleMonth = $this->_monthcal ($date , 1);
                $payDeadline = date('Y-m-d H:i:s',mktime(0,0,0,substr($handleMonth, 4 , 2),date('d'),substr($handleMonth, 0 , 4)));
                $payDeadline = date('Y-m-d',strtotime('-'.C('INSURANCE_HANDLE_DAYS').' day',strtotime($payDeadline)));
                foreach ($list as $k=>$v)
                {
                	if (1 == $v['state'] || 2 == $v['state']) {
                		if (1 == $v['state']) {
                			//$exitInfo = $info->field('id')->where(array('insurance_id'=>$v['id'],'handle_month'=>$handleMonth,'payment_type'=>$v['payment_type'],'state'=>$v['state'],'operate_state'=>array('egt',0)))->find();
                			$exitInfo = $info->field('id')->where(array('insurance_id'=>$v['id'],'handle_month'=>$handleMonth,'payment_type'=>$v['payment_type']))->find();
                			if ($exitInfo) {
                				continue;
                			}
                		}
	                    $insertInfo = $v;
	                    unset($insertInfo['id']);
	                    //unset($insertInfo['start_month']);
	                    unset($insertInfo['end_month']);
	                    unset($insertInfo['effect']);
	                    if(2 == $v['payment_type']){
	                    	unset($insertInfo['rule']);
	                    	$rule = $v['rule'];
	                    }else {
	                    	$rule = $val['rule'];
	                    }
	                    $rule = 2 == $v['payment_type']?$v['rule']:$val['rule'];
	                    $productOrder = $product->alias('p')->field('p.company_id,po.id')->join('LEFT JOIN zbw_service_product_order po ON po.product_id=p.id')->join('LEFT JOIN zbw_company_info ci ON po.user_id=ci.user_id')->where("p.id={$v['product_id']} and po.user_id={$v['user_id']} and service_state = 2")->find();
	                    $oid = $order->where("user_id={$v['user_id']} AND company_id={$productOrder['company_id']} AND location={$v['location']} AND handle_month={$handleMonth} AND `type`=2 AND `state`=0")->getField('id');
	                    $info->startTrans();
	                    //订单入库
	                    if (!$oid)
	                    {
	                        $oid = $order->add(
	                            array(
	                                'order_no' => $orderModel->orderNo(),
	                                'user_id'  => $v['user_id'],
	                                'company_id' => $productOrder['company_id'],
	                                'location' => $v['location'],
	                                'handle_month' => $handleMonth,
	                                'amount' => 0.00,
	                                'diff_amount' => 0.00,
	                                'actual_amount' => 0.00,
	                                'state' => 0,
	                                'type'  => 2,
	                                'pay_deadline' => $payDeadline,
	                                'create_time' => date('Y-m-d H:i:s')
	                            )
	                        );
	                    }
	                    
	                    //$insertInfo['pay_order_id'] = $oid;
	                    $insertInfo['insurance_id'] = $v['id'];
	                    $insertInfo['handle_month'] = $handleMonth;
	                    $insertInfo['pay_date']     = (1 == $val['payment_type'] ? $insertInfo['handle_month'] : $this->_monthcal($insertInfo['handle_month'] , 1));
	                    $insertInfo['remark'] = $remark;
	                    //$insertInfo['state'] = $v['state'];
	                    $insertInfo['operate_state'] = 1;
	                    $insertInfo['create_time'] = $insertInfo['modify_time'] = date('Y-m-d H:i:s' , time());
	                    $insertDetail = array();
	                    //$insertDetail['insurance_info_id'] = $info->add($insertInfo , '' ,true);
                        $insertDetail['insurance_info_id'] =  $info->addPersonInsuranceInfo($insertInfo);
                        if (!$insertDetail['insurance_info_id']) {
                        	$info->rollback();
                        	continue;
                        }
	                    $insertDetail['pay_order_id'] = $oid;
	                    $insertDetail['type'] = $insertInfo['state'];
	                    $insertDetail['amount'] = $insertInfo['amount'];
	                    $insertDetail['pay_date'] = $insertInfo['pay_date'];
	                    $insertDetail['rule_id'] = $insertInfo['rule_id'];
	                    $insertDetail['payment_type'] = $insertInfo['payment_type'];
	                    $insertDetail['handle_month'] = $insertInfo['handle_month'];

	                    if ($insertDetail['payment_type'] == 1)
	                    {
	                        $insertDetail['service_price'] = $wl->where("service_product_order_id={$productOrder['id']}")->getField('soc_service_price');
	                    }
	                    else if ($insertDetail['payment_type'] == 2)
	                    {
	                        $insertDetail['service_price'] = $wl->where("service_product_order_id={$productOrder['id']}")->getField('pro_service_price');
	                    }else {
	                    	$insertDetail['service_price'] = 0;
	                    }
	                    
	                    $insertDetail['replenish'] = 0;
	                    
	                    $insertDetail['payment_info'] = $insertInfo['payment_info'];
	                    $person = array();
	                    $person['amount'] = $insertInfo['amount'];
	                    $person['month']  = 1;
	                    //$insertDetail['payment_type'] = $insertInfo['payment_type'];
	                    if ($insertDetail['payment_type'] == 2)
	                    {
	                        $person['personScale']  = json_decode($insertInfo['payment_info'] , true)['personScale'];
	                        $person['companyScale'] = json_decode($insertInfo['payment_info'], true)['companyScale'];
	                    }
	                    $cue = $calculate->detail($rule , $person , $insertDetail['payment_type'] , $val['disabled'] , $insertDetail['replenish']);
	                    $cue = json_decode($cue , true)['data'];
	                    //var_dump($rule , $person , $insertDetail['payment_type'] , $val['disabled'] , $insertDetail['replenish'] , $cue);
	                    $insertDetail['insurance_detail'] = $insertDetail['current_detail'] = json_encode($cue , JSON_UNESCAPED_UNICODE);
	                    $insertDetail['price'] = $cue['company']+$cue['person'];
	                    $insertDetail['state'] = 1;
	                    
	                    $insertDetail['create_time']  = $insertDetail['modify_time'] = date('Y-m-d H:i:s' , time());
                        if ($detailModel->add($insertDetail)) {
                        	$info->commit();
                        }else {
                        	$info->rollback();
                        }
                    }/*else {
                    	//person_insurance_info写入空白数据
	                    $insertInfo = $v;
	                    $insertInfo['state'] = 0;
	                    unset($insertInfo['id']);
	                    //unset($insertInfo['start_month']);
	                    unset($insertInfo['end_month']);
	                    
	                    $insertInfo['insurance_id'] = $v['id'];
	                    $insertInfo['handle_month'] = $handleMonth;
	                    $insertInfo['remark'] = $remark;
	                    $insertInfo['operate_state'] = 0;
	                    $insertInfo['create_time'] = $insertInfo['modify_time'] = date('Y-m-d H:i:s' , time());
	                    $insertDetail['insurance_info_id'] = $info->add($insertInfo , '' ,true);
                    }*/
                }
            }
        }
    }
    
    //生成月对账单
    public function mkBill()
    {
        if (1 == date('d'))
        {
            $apended = $this->_monthcal (date('Ym') , -1);
            $payOrder = M('pay_order' , 'zbw_');
            $cnt = $payOrder->field("DISTINCT(user_id) user_id")->where("date_format(pay_time, '%Y%m')={$apended} AND `state`=1")->count();

            $page = 1;
            $limit = 10000;
            $current = 0;
            $ceil = ceil($cnt/$limit);
            $bill = M('service_bill' , 'zbw_');
            $orderModel = D('ServiceOrder');
            $insert = array();

            while ($page <= $ceil)
            {
                $current = ($page-1)*$limit;
                $page++;
                $users = $payOrder->field("DISTINCT(user_id) user_id,company_id")->where("date_format(pay_time, '%Y%m')={$apended} AND `state`=1")->order('id DESC')->limit($current , $limit)->select();

                foreach ($users as $v)
                {
                    $res = $payOrder->alias('po')->field('sum(po.amount) amount,sum(po.diff_amount) diff_amount,ci1.company_name user_name,ci2.company_name service_name,ci2.id service_company_id')->join("LEFT JOIN zbw_company_info ci1 ON ci1.user_id=po.user_id")->join("LEFT JOIN zbw_company_info ci2 ON ci2.id=po.company_id")->where("po.user_id={$v['user_id']} AND date_format(po.pay_time, '%Y%m')={$apended}  AND po.state=1")->group('po.company_id')->find();
                    $bill->add(array (
                            'bill_no' => $orderModel->orderNo(),
                            'bill_date' => $apended,
                            //'bill_name' => "{$res['user_name']}{$apended}{$res['service_name']}",
                            'bill_name' => "{$res['user_name']}{$apended}对账单\n({$res['service_name']})",
                            'user_id' => $v['user_id'],
                            //'company_id' => $v['company_id'],
                            'company_id' => $res['service_company_id'],
                            'handle_month' => $apended,
                            'price' => $res['amount'],
                            'diff_amount' => $res['diff_amount'],
                            'create_time' => date('Y-m-d H:i:s')
                        )
                   );
                }
            }
        }
    }
    public function diffNow ()
    {
        $dids = '54378,54379,54380,54381';
        $m = M('diff_cron' , 'zbw_');
        $cnt = $m->where("detail_id IN ({$dids})")->count();
        //$cnt = $m->where("detail_id IN (SELECT id FROM zbw_service_insurance_detail WHERE handle_month={$prevMonth})")->count();
        $page = 1;
        $limit = 1000;
        $current = 0;
        $ceil = ceil($cnt/$limit);
        while ($page <= $ceil)
        {
            $current = ($page-1)*$limit;
            $page++;
            $m = M('diff_cron' , 'zbw_');
            $diff = $m
            ->where("detail_id IN ({$dids})")
            //->where("detail_id IN (SELECT id FROM zbw_service_insurance_detail WHERE handle_month={$prevMonth})")
            ->order("field(type,3,1,2)")
            ->limit($current , $limit)
            ->select();
            foreach ($diff as $v)
            {
                if ($v['detail_id']) {
                    switch ($v['type'])
                    {
                        //办理失败
                        case 1:
                            $this->_manageFail($v);
                        break;
                        //规则修改
                        case 2:
                            $this->_changeRule($v);
                        break;
                        //缴费异常
                        case 3:
                            $this->_payAnomaly($v);
                        break;
                        //工本费
                        case 4:
                            //$this->_papersCost($v);
                        break;
                        default:;
                    }
                }
            }
        }
    }
    public function diffAmount()
    {
        $day = date('d');
        if (28 == $day)
        {
            $prevMonth = date('Ym' , strtotime('-1 month'));
            $appended = date('Y-m-d' , strtotime('-1 month'));
            $nowDate = date('Y-m-d' , time());
            $start = substr($appended ,0 ,7) . '-01 00:00:00';
            $end   = substr($nowDate ,0 ,7) . '-01 00:00:00';
            $m = M('diff_cron' , 'zbw_');
            $cnt = $m->where("modify_time >= '{$start}' AND modify_time < '{$end}'")->count();
            //$cnt = $m->where("detail_id IN (SELECT id FROM zbw_service_insurance_detail WHERE handle_month={$prevMonth})")->count();
            $page = 1;
            $limit = 1000;
            $current = 0;
            $ceil = ceil($cnt/$limit);
            while ($page <= $ceil)
            {
                $current = ($page-1)*$limit;
                $page++;
                $m = M('diff_cron' , 'zbw_');
                $diff = $m
                ->where("modify_time >= '{$start}' AND modify_time < '{$end}'")
                //->where("detail_id IN (SELECT id FROM zbw_service_insurance_detail WHERE handle_month={$prevMonth})")
                ->order("field(type,3,1,2)")
                ->limit($current , $limit)
                ->select();
                foreach ($diff as $v)
                {
                	if ($v['detail_id']) {
	                    switch ($v['type'])
	                    {
	                        //办理失败
	                        case 1:
	                            $this->_manageFail($v);
	                        break;
	                        //规则修改
	                        case 2:
	                            $this->_changeRule($v);
	                        break;
	                        //缴费异常
	                        case 3:
	                            $this->_payAnomaly($v);
	                        break;
	                        //工本费
	                        case 4:
	                           //$this->_papersCost($v);
	                        break;
	                        default:;
	                    }
                	}
                }
            }
        }
    }
    
    private function _manageFail($data)
    {
        $sd = D('ServiceDiff');
        $sd->diffAmount($data['item'] , $data['type'] , $data['detail_id']);
    }
    
    private function _changeRule($data)
    {
        $sd = D('ServiceDiff');
        $sd->diffAmount($data['item'] , $data['type'] , $data['detail_id']);
    }
    
    private function _payAnomaly($data)
    {
        $sd = D('ServiceDiff');
        $sd->_payData = json_decode($data['message_body'] , ture);
        $sd->diffAmount($data['item'] , $data['type'] , $data['detail_id']);
    }
    
    private function _papersCost($data)
    {
        $sd = D('ServiceDiff');
        $sd->diffAmount($data['item'] , $data['type'] , $data['detail_id']);
    }
    
    /**
     * 报增减截止日生成在保订单
     * @return [type] [description]
     
    public function mkOrder ()
    {
        $page = 1;
        $limit = 10000;
        $current = 0;
        $day = date('d');
        $date = date('Ym');
        $adate = date('Ym' , strtotime('+1 month'));
        //生效且报增减截止日为当天的产品订单
        $pomWhere = "service_state = 2 AND abort_add_del_date = {$day}";
        $pom = M('product_order');
        $cnt = $pom->where($pomWhere)->count('id');
        //$orderBill = new OrderBill;
        $orderModel = D('ServiceOrder');
        $ceil = ceil($cnt/$limit);
        while ($page <= $ceil)
        {
            $current = ($page-1)*$limit;
            $page++;

            //查询所有报增截止日是今天的产品订单
            $porder = $pom
                ->field('id,product_id,company_id,abort_add_del_date,create_bill_date')
                ->where($pomWhere)
                ->order('id DESC')
                ->limit($current , $limit)
                ->select();
            foreach ($porder as $val)
            {
                $m = M('service_order');
                $oid = $m->where("product_order_id = {$val['id']} AND order_date = {$adate}")->getField('id');//下月订单是否生成
                if (!$oid)
                {
                    //入库下月空订单
                    $insert = array ();
                    $insert['order_no'] = $orderModel->orderNo();
                    $insert['product_order_id'] = $val['id'];
                    $insert['order_date'] = $adate;
                    $insert['create_time'] = date('Y-m-d H:i:s');
                    $m = M('service_order');
                    $m->add($insert);
                }
                //本期在保人员 = 本期报增+(上期在保-本期报减)
                $wdate = date('Ym' , strtotime("-1 month" , time()));
                //$m = M('service_order');
                $m = M('');
                //在保记录
                $warrent = $m->query(
                    "SELECT * FROM zbw_service_order_detail WHERE service_order_id=(SELECT id FROM zbw_service_order WHERE product_order_id={$val['id']} AND order_date={$wdate}) AND (`type`=3 OR (`type` = 1 AND `state` in (3,-4,-5,4))) AND base_id NOT IN (SELECT base_id FROM zbw_service_order_detail WHERE service_order_id=(SELECT id FROM zbw_service_order WHERE product_order_id={$val['id']} AND order_date={$date}) AND (`type` = 2 AND `state` >= 3)) GROUP BY base_id,payment_type"
                    );
                //当期订单id
                $noid = M('service_order')->where("product_order_id = {$val['id']} AND order_date = {$date}")->getField('id');
                $array = array ();
                foreach ($warrent as $v)
                {
                    array_push ($array ,
                        array(
                            'base_id' => $v['base_id'] ? $v['base_id'] : '',
                            'rule_id' => $v['rule_id'] ? $v['rule_id'] : '',
                            'service_order_id' => $noid ? $noid : '',
                            'location' => $v['location'] ? $v['location'] : '',
                            'type' => 3,
                            'payment_type' => $v['payment_type'] ? $v['payment_type'] : '',
                            'amount' => $v['amount'] ? $v['amount'] : '',
                            'pay_date' => $this->_monthcal($v['pay_date'] , 1),
                            'card_number' => $v['card_number'] ? $v['card_number'] : '',
                            'note' => $v['note'] ? $v['note'] : '',
                            'replenish' => 0,
                            'state' => 3,
                            'company_scale' => $v['company_scale'] ? $v['company_scale'] : '',
                            'person_scale'  => $v['person_scale'] ? $v['person_scale'] : '',
                            'modify_time' => date('Y-m-d H:i:s'),
                            'create_time' => date('Y-m-d H:i:s')
                        )
                    );
                }
                $m = M('service_order_detail');
                $m->addAll($array,$options=array(),$replace=true);
                $warrent_num = $m->field('id')->where("service_order_id = {$noid} AND type = 3")->group('base_id')->select();
                //在保人数更新
                $m = M('service_order');
                $m->where("id={$noid}")->setField('warranty_num' ,count($warrent_num));
            }
        }
    }
    */
    /**
     * 生成当日账单
     * @return [type] [description]
     
    public function mkBill ()
    {
        $page = 1;
        $limit = 10000;
        $current = 0;
        $day = date('d');
        $date = date('Ym');
        $pdate = date('Ym' , strtotime('-1 month'));
        $title = '账单付款通知';
        $msg = "尊敬的#公司#：<p>您本期的缴费账单已生成，请于合同约定日期前完成确认及付款，逾期可能会产生滞纳金或造成员工停保，感谢您的配合！</p>";

        $serviceBill = D('ServiceBill');
        $Calculate = new Calculate;
        $pomWhere = "service_state = 2 AND create_bill_date = {$day}";
        $pom = M('product_order');
        $cnt = $pom->where($pomWhere)->count('id');
        $ceil = ceil($cnt/$limit);
        while ($page <= $ceil)
        {
            $current = ($page-1)*$limit;
            $page++;
            //查询所有报增截止日是今天的产品订单
            $porder = $pom->alias('po')
                ->field('po.id,po.product_id,po.company_id,po.abort_add_del_date,po.create_bill_date,po.bill_month_state,po.payment_month_state,co.company_name')
                ->join("LEFT JOIN zbw_company_info co ON po.company_id=co.company_id")
                ->where("service_state = 2 AND create_bill_date = {$day}")
                ->order('id DESC')
                ->limit($current , $limit)
                ->select();
            foreach ($porder as $val)
            {
                //查找上月订单
                if ($val['bill_month_state'] == 1)
                {
                    $m = M('service_order');
                    $order = $m->where("product_order_id={$val['id']} AND order_date={$pdate} AND bill_make_date is null")->find();
                }
                //查找当月订单
                else
                {
                    $m = M('service_order');
                    $order = $m->where("product_order_id={$val['id']} AND order_date={$date} AND bill_make_date is null")->find();
                }
                if(empty($order)) continue;

                //获取对应订单的账单
                $m = M('service_bill');

                $bid = $m->where("order_id={$order['id']}")->getField('id');

                //没有则创建
                if (!$bid)
                {
                    $insert = array();
                    $insert['bill_no'] = $serviceBill->billNo();
                    $insert['order_id'] = $order['id'];
                    $insert['order_date'] = $order['order_date'];
                    $insert['actual_price'] = 0.00;
                    $insert['price'] = 0.00;
                    $insert['state'] = 0;
                    $insert['pay_time'] = '';
                    $insert['balance_total'] = 0.00;
                    $insert['note'] = '';
                    $insert['create_time'] = date('Y-m-d H:i:s');
                    $m = M('service_bill');
                    $bid = $m->add($insert);
                    M('service_order')->where("id={$order['id']}")->setField('bill_make_date' , date('Y-m-d H:i:s'));
                }
                //查找同时购买社保和公积金的订单记录
                $bothData = $m->query("
                    SELECT id FROM zbw_service_order_detail WHERE service_order_id={$order['id']} AND payment_type = 2 AND (`type` IN (3,1) AND `state` >= 3) AND base_id IN (SELECT base_id FROM zbw_service_order_detail WHERE service_order_id={$order['id']} AND payment_type=1 AND (`type` IN (3,1) AND `state` >= 3))
                    ");
                $ids = array ();
                foreach($bothData as $k => $v)
                {
                    $ids[] = $v['id'];
                }
                //当前订单入账的数据
                $m = M('service_order_detail');
                $exportData = $m->where("service_order_id={$order['id']} AND `state` >= 3 AND `type` IN (1,3)")->order('id DESC')->select();
                $array = array ();//保存入库数据
                $spdo = array ();//保存社保、公积金、残障金数据
                foreach ($exportData as $v)
                {
                    $m = M('product_template_rule');
                    $rule = $m->where("id={$v['rule_id']}")->getField('rule');
                    $rule = json_decode($rule , true);
                    if(isset($rule['material'])) unset($rule['material']);//删除文本文档数据
                    $insert = array ();
                    $insert = $serviceBill->orderCue($rule , $v);
                    $insert['rule'] = json_encode($rule , JSON_UNESCAPED_UNICODE);
                    $insert['order_detail_id'] = $v['id'];
                    $insert['pay_date'] = $v['pay_date'];
                    $insert['service_bill_id'] = $bid;
                    $insert['payment_type'] = $v['payment_type'];
                    $insert['balance'] = '0.00';
                    $insert['service_price'] = '0.00';
                    $insert['total'] = ($insert['company'] + $insert['person'] + $insert['pro_post']) ? ($insert['company'] + $insert['person'] + $insert['pro_post']) : '0.00';
                    $insert['create_time'] = date('Y-m-d H:i:s');
                    //计算服务费
                    if (!in_array($v['id'] , $ids) && $v['payment_type'] != 4)
                    {
                        $insert['service_price'] = $serviceBill->servicePrice ($val['id'] , $v['location'] , $v['payment_type']);
                    }
                    //保存个人社保、公积金赋值，便于工资记录查找
                    if (in_array($v['payment_type'] , array(1,2)))
                    {
                        $spdo["{$bid}-{$v['base_id']}-{$v['payment_type']}"] = $insert['person'];
                    }
                    array_push($array , $insert);
                }
                $m = M('service_bill_detail');
                $m->addAll($array);
                //审核成功的工资入账
                $m = M('service_order_salary');
                $salary = $m->where("order_id={$order['id']} AND state >= 1")->order('id DESC')->select();
                $array = array ();
                foreach ($salary as $v)
                {
                    $insert = array();
                    $insert['salary_id'] = $v['id'];
                    $insert['bill_id']   = $bid;
                    $insert['service_price'] = '0.00';
                    $insert['salary']   = $v['wages'];
                    $insert['balance'] = '0.00';
                    $insert['deduction_social_insurance'] = $spdo["{$bid}-{$v['base_id']}-1"] ? $spdo["{$bid}-{$v['base_id']}-1"] : ($v['deduction_social_insurance'] ? $v['deduction_social_insurance'] : '0.00');
                    $insert['deduction_provident_fund'] = $spdo["{$bid}-{$v['base_id']}-2"] ? $spdo["{$bid}-{$v['base_id']}-2"] : ($v['deduction_social_insurance'] ? $v['deduction_social_insurance'] : '0.00');
                    $insert['deduction_income_tax'] = '0.00';
                    $insert['replacement'] = '0.00';
                    $insert['deduction_other'] = '0.00';
                    $insert['tex'] = $serviceBill->salaryTax($v['wages'] - $insert['deduction_social_insurance'] -  $insert['deduction_provident_fund']);
                    $insert['actual_salary'] = $insert['salary'] - $insert['deduction_social_insurance'] -  $insert['deduction_provident_fund'] -  $insert['tex'];
                    $insert['create_time'] = date('Y-m-d H:i:s',time());
                    //发放成功
                    $insert['service_price'] = $serviceBill->servicePrice ($val['id'] , $v['location'] , 5);
                        //$insert['actual_salary'] = $insert['actual_salary'] - $insert['tex'];

                    array_push($array , $insert);
                }
                $m = M('service_bill_salary');
                $m->addAll($array);
                $m->execute("CALL zbw_bill_price({$bid})");
                //赋值账单消息数据
                $msgData = array ();
                $detail = str_replace('#公司#' , $val['company_name'] , $msg);
                array_push($msgData , array('title' => $title , 'detail' => $detail , 'company_id' => $val['company_id'] , 'create_time' => date('Y-m-d H:i:s')));
            }
            $m = M('company_msg');
            $m->addAll($msgData);
        }
    }
*/
    /**
     * 处理过期账单
     * @return [type] [description]
     
    public function overBillPay ()
    {
        $m = M('service_order');
        $day = date('d');
        $date = date('Ym');
        $pdate = date('Ym' , strtotime('-1 month' , strtotime($date)));
        //生效且报付款截止日为当天的产品订单
        $pomWhere = "service_state = 2 AND abort_payment_date = {$day}";
        $m->execute("UPDATE SET state=-1 WHERE order_id IN (SELECT id FROM zbw_service_order WHERE product_order_id IN (SELECT id FROM zbw_product_order WHERE service_state = 2 AND payment_month_state=0 AND abort_payment_date = {$date}))");
        $m->execute("UPDATE SET state=-1 WHERE order_id IN (SELECT id FROM zbw_service_order WHERE product_order_id IN (SELECT id FROM zbw_product_order WHERE service_state = 2 AND payment_month_state=1 AND abort_payment_date = {$pdate}))");

    }
*/

    /**
     * intdata转为date('Y-m')格式
     * @param  [int] $intDate [intdate]
     * @return [string]       [date('Y-m')]
     */
    private function _dateSplit ($intDate)
    {
        $str = substr($intDate , 0 , 4) . '-' . substr($intDate , 4 , 2);
        return strlen($intDate) < 8 ? $str : $str . '-' . substr($intDate , 6 , 2);
    }
    
    /**
     * 月份计算
     * @param  [mixed] $date  [日期]
     * @param  [int]   $month [年月整数]
     * @return [int]       [年月]
     */
    private function _monthcal($date , $month=1)
    {
        if (is_int($date))
        {
            return date('Ym' , strtotime(intval($month).' month' , $date));
        }
        else
        {
            return date('Ym' , strtotime(intval($month).' month' , strtotime($this->_dateSplit($date))));
        }
    }
}