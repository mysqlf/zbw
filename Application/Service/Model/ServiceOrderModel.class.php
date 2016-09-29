<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/15
 * Time: 19:14
 */

namespace Service\Model;
use Think\Model;
use Common\Model\Calculate;



class ServiceOrderModel extends  ServiceAdminModel
{
    protected $tureTableName = 'zbw_service_product_order';
    public function comOrderList($data)
    {
		$o = M('service_order');
		$page = I('get.p',1);
        $state = intval(I('get.state' , -1));
        $keywords = I('get.keywords' , '');
        $payment = intval(I(''));

        $pageshow = showpage($count,10);
        return array('page'=>$pageshow,'result'=>$res);
    }
	

}