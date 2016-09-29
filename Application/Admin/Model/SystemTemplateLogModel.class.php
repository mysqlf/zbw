<?php
/**
 * 模板日志
 */
namespace Admin\Model;
use Think\Model;
class SystemTemplateLogModel extends Model{

		/**
		 * 模板日志
		 * @param $type 
		 * @param   $[name] [<description>]
		 */
		public function template_log($template_id, $type, $new_rules, $old_rules, $classify_mixed){
			$_old_rules = array();
			$_old_rules['old']   = $old_rules;
			$_old_rules['new']   = $new_rules;
			$data['template_id'] = $template_id;
			$data['admin_id']    = UID;
			$data['detail']      = json_encode($_old_rules, JSON_UNESCAPED_UNICODE);
			$data['create_time'] = date('Y-m-d H:i', time());
			if($this->add($data)){
				return true;
			}else{
				return false;
			}

		
		}

}