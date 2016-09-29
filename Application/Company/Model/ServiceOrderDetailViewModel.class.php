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
class ServiceOrderDetailViewModel extends ViewModel{
	protected $tablePrefix = 'zbw_';
	
	public $viewFields = array(
//		'service_order_detail'=>array('pay_date','location','rule_id','note','amount','person_scale','state','company_scale','card_number'),
		'service_order_detail'=>array('id','base_id','pay_date'),
		'service_order'=>array('id'=>'service_order_id','order_date','state','_on'=>'service_order_detail.service_order_id=service_order.id'),
		'person_base'=>array('user_name','card_num','_on'=>'service_order_detail.base_id=person_base.id'),
		'product_order'=>array('company_id','_on'=>'service_order.product_order_id=product_order.id'),
		'service_product'=>array('location','_on'=>'product_order.product_id=service_product.id'),
//		'service_order_salary'=>array('wages','deduction_social_insurance','deduction_provident_fund','deduction_income_tax','actual_wages','state','_on'=>'service_order.id=service_order_detail.service_order_id'),
//		'product_template_rule'=>array('type','rule','_on'=>'service_order_detail.rule_id=product_template_rule.id'),

	);

}
