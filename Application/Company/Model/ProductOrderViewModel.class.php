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
class ProductOrderViewModel extends ViewModel{
	protected $tablePrefix = 'zbw_';
	
	public $viewFields = array(
		'product_order'=>array('id','order_no','company_id','price','modify_price','state','service_state','is_salary','create_time'
			,'pay_type','pay_time','abort_add_del_date','create_bill_date','bill_month_state','payment_month_state','abort_payment_date','_type'=>'LEFT'),
		'company_account_info'=>array('account_name','branch','account','_on'=>'product_order.company_id=company_account_info.company_id','_type'=>'LEFT'),
		'service_product'=>array('name'=>'product_name','product_detail','validity','_on'=>'product_order.product_id=service_product.id','_type'=>'LEFT'),
		//服务商（卖方）信息
		'seller_company_info'=>array('_table'=>'zbw_company_info','company_name'=>'seller_company_name','_on'=>'service_product.company_id=seller_company_info.id','_type'=>'LEFT'),
	);

}
