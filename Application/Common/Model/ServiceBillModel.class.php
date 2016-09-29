<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Common\Model;
use Think\Model;
use Common\Model\Calculate;
/**
 * 分类模型
 */
class ServiceBillModel extends Model
{
	protected $tablePrefix = 'zbw_';

    static private $_yCode = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    /**
     * 返回账单号
     */
    public function billNo ()
    {
        return date('Ymd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(10, 99));
        //return self::$_yCode[(date('Y')-2016)%26] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(1000, 9999));
    }
    /**
     * 生成账单明细数组
     * @param  [array] $order   [订单记录]
     * @return [type]           [description]
     */
    public function orderCue ($rule , $order)
    {
        $bill = array();
        $Calculate = new Calculate;
        if (!is_array($rule)) $rule = json_decode($rule , true);
        switch ($order['payment_type'])
        {
            case 1:
                $personArray = array ('amount'=>$order['amount'],'month'=>1,'cardno'=>$order['card_number']);
                $cue = $Calculate->detail($rule , $personArray , 1);
                $cue = json_decode($cue , true);
                $cue = $cue['data'];
                $bill['company']    = $cue['company'];
                $bill['person']     = $cue['person'];
                $bill['post_price'] = $cue['pro_cost'];
            break;
            case 2:
                $personArray = array ('amount'=>$order['amount'],'month'=>1,'personScale'=>$order['person_scale'],'companyScale'=>$order['company_scale'],'cardno'=>$order['card_number']);
                $cue = $Calculate->detail($rule , $personArray , 2);
                $cue = json_decode($cue , true);
                $cue = $cue['data'];
                $bill['company']    = $cue['company'];
                $bill['person']     = $cue['person'];
                $bill['post_price'] = $cue['pro_cost'];
            break;
            case 3:
                $personArray = array ('month'=>1);
                $cue = $Calculate->detail($rule , $personArray , 3);
                $cue = json_decode($cue , true);
                $cue = $cue['data'];
                $bill['company']    = $cue['sum'];
                $bill['person']     = 0;
                $bill['post_price'] = 0;
            break;
            case 4:
                $personArray = array ('month'=>1);
                $cue = $Calculate->detail($rule , $personArray , 5);
                $cue = json_decode($cue , true);
                $cue = $cue['data'];
                $bill['company']    = $cue['actual'];
                $bill['person']     = 0;
                $bill['post_price'] = 0;
            break;
            default:;
        }
        $bill['company']    = $bill['company'] ? $bill['company'] : '0.00';
        $bill['person']     = $bill['person'] ? $bill['person'] : '0.00';
        $bill['post_price'] = $bill['post_price'] ? $bill['post_price'] : '0.00';
        return $bill;
    }
    /**
     * [salaryTax description]
     * @return [type] [description]
     */
    public function salaryTax ($salary)
    {
        $Calculate = new Calculate;
        $person = array('amount'=>$salary , 'month'=>1);
        $json = $Calculate->detail(array() , $person , 4);
        $json = json_decode($json , true);
        return $json['data']['tax'] ? $json['data']['tax'] : '0.00';

    }
    /**
     * 返回服务费
     * @param  [int] $poid          [产品订单id]
     * @param  [int] $location      [地点id]
     * @param  [int] $payment_type  [类型 1社保 2公积金 3残障金 5工资]
     * @return [type]               [description]
     */
    public function servicePrice ($pid , $location , $payment_type)
    {
        $m = M('warranty_location');
        $sData = $m->field('ss_service_price,af_service_price,dg_service_price')->where("`service_order_id` = {$pid} AND `location` = {$location}")->find();
        $price = 0.00;
        switch ($payment_type)
        {
            case 1:
                $price = $sData['ss_service_price'];
            break;
            case 2:
                $price = $sData['ss_service_price'];
            break;
            case 3:
                $price = $sData['dg_service_price'];
            break;
            case 5:
                $price = $sData['af_service_price'];
            break;
            default:;
        }
        return $price ? $price : '0.00';
    }
}
