<?php
/**
 * 模板
 */
namespace Admin\Model;
use Think\Model;
class ProductTemplateModel extends Model{

	protected $_validate = array(
	    	array('location', '0', '城市不能为空', self::EXISTS_VALIDATE, 'equal', self::MODEL_BOTH),
	);
	/**
	 *建模板
	 * @param   $[type] [<模板类型：1系统模板 2企业模板>]
	 */
	public function add($location, $payment_type, $type = 1){
		$result = $this->getFieldByLocation($location, 'id');		
		if($result){//当前城市模板已存在
			$this->error = array('status'=>1,'msg'=>'当前城市模板已存在','url'=>U('Admin/ProductTemplate/edit',array('template_id'=>$result,'location'=>$location)));
			return false;
		}else{
			$tpl_data  =  array();
			$tpl_data['admin_id'] = UID;
			$tpl_data['state'] =  1;
			$tpl_data['company_id'] = 0;
			$tpl_data['create_time'] = $tpl_data['modify_time'] = date('Y-m-d H:i',  time());//time_format	
			$tpl_data['name'] = showAreaName1($location);
			$tpl_data['type'] = 1;
			$tpl_data['payment_type'] = $payment_type;
			$tpl_data['location'] =  $location;	

			$result =  M('ProductTemplate')->add($tpl_data);
			if(false === $result){
		   		 echo $this->getDbError();
			}

			$template_id = $result;

		}
		return $template_id;
	}

}