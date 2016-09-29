<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: rohochan <rohochan@gmail.com> <http://blog.rohochan.com>
// +----------------------------------------------------------------------
namespace Company\Model;
use Think\Model\ViewModel;
/**
 * 产品订单模型
 */
class ServiceOrderViewModel extends ViewModel{
	protected $tablePrefix = 'zbw_';
	
	public $viewFields = array(
		'service_order'=>array('id','order_no'=>'service_order_no','order_date','state'=>'service_order_state','create_time'=>'order_createtime','order_complete_date'),
		'product_order'=>array('company_id','service_state'=>'product_service_state','price'=>'product_order_price','abort_add_del_date','create_bill_date','abort_payment_date','modify_price','is_salary','_on'=>'service_order.product_order_id=product_order.id'),
		'company_account_info'=>array('account_name','branch','account','_on'=>'product_order.id=company_account_info.company_id'),
		'service_product'=>array('name'=>'product_name','validity','_on'=>'product_order.product_id=service_product.id'),
		//服务商（卖方）信息
		'seller_company_info'=>array('_table'=>'zbw_company_info','company_name'=>'seller_company_name','_on'=>'service_product.company_id=seller_company_info.id'),
	);

}
