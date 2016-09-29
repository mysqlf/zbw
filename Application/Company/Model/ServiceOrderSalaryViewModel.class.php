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
class ServiceOrderSalaryViewModel extends ViewModel{
	protected $tablePrefix = 'zbw_';
	
	public $viewFields = array(
		'service_order_salary'=>array('id','product_id','location','date','actual_salary','deduction_income_tax','distribute_time','create_time','state','remark','_type'=>'LEFT'),
		'person_base'=>array('id'=>'person_id','person_name','card_num','bank','account','_on'=>'service_order_salary.base_id=person_base.id','_type'=>'LEFT'),
		'company_info'=>array('id'=>'company_id','company_name','_on'=>'service_order_salary.user_id=company_info.user_id','_type'=>'LEFT'),
		'service_product'=>array('name'=>'product_name','company_id'=>'service_company_id','_on'=>'service_order_salary.product_id=service_product.id','_type'=>'LEFT'),
	);
	
}
