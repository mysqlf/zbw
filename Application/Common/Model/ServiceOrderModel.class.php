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

/**
 * 分类模型
 */
class ServiceOrderModel extends Model
{
	protected $tablePrefix = 'zbw_';
	protected $autoCheckFields = false;
    static private $_yCode = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    /**
     * 返回订单号
     */
    public function orderNo ()
    {
        //return strtoupper(dechex(12%10));uniqid();
        //return date('Ymd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(10, 99));
        return create_order_sn();
    }
    /**
     * 返回账单号
     */
    public function billNo ()
    {
        //return date('Ymd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(10, 99));
        //return self::$_yCode[(date('Y')-2016)%26] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(1000, 9999));
        return create_order_sn();
    }
    /**
     * 获取所有在保人员
     * @param  [int]   $[poid] [产品订单id]
     * @param  [int]   $[type] [类型]
     * @return [array] [在保人员数组]
     */
    public function warBillLastUsers ($poid , $type = 1)
    {
        $m = M('service_order');
        $oid = $m->where("product_order_id={$poid} AND state=2")->order('id DESC')->getField('id');//获取上次订单完成的id
        $fix = '';
        switch (intval($type))
        {
            case 1:
                $fix = 'soc';
            break;
            case 2:
                $fix = 'pro';
            break;
            case 3:
                $fix = 'dis';
            break;
            default:;
        }
        $m     = M('zbw_order_per_' . $fix);
        $table = 'zbw_order_per_'   . $fix;
        //最后一次完成订单中所有在保用户，并去除报减成功用户
        return $m->query(
            "SELECT DISTINCT(base_id) base_id,amount,location,rule_id,card_number FROM {$table} WHERE  order_id={$oid} AND ((`type`=1 AND `state`=4) OR (`type`=3)) AND base_id
             NOT IN (SELECT base_id FROM {$table} WHERE order_id= {$oid} AND (`type`=2 AND `state`=3)))"
            );
    }
    /**
     * 在保人数
     * @return [type] [description]
     */
    public function warranty ()
    {
        return M('')->query(
            "SELECT base_id FROM zbw_order_per_soc WHERE order_id={$oid} AND type=3
             UNION SELECT base_id FROM zbw_order_pro WHERE order_id={$oid} AND type=3
             UNION SELECT base_id FROM zbw_order_dis WHERE order_id={$oid} AND type=3"
             );
    }

}
