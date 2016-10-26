<?php
	namespace Admin\Model;
	use Think\Model;
	class TemplateRuleModel extends Model
	{
		protected $tablePrefix = 'zbw_';
		private $_clear_rule = array();
		/**
		 * [snalySb 社保规则解析入口]
		 * @Author   JieJie
		 * @DataTime 2016-07-04T16:05:24+0800
		 * @return   [array]         [解析后的数据]
		 */
		public function snalySb()
		{
			$sb_rule = I('post.sb','');
			if(empty($sb_rule) || !is_array($sb_rule))
			{
				$this->error = '社保数据错误！';
				return false;
			}

			//检测企业社保比例
			$sb_rule['company'] = $this->_checkSbData($sb_rule['company']);
			if(!$sb_rule['company']) 
			{
				$this->error = '社保单位比例不正确！';
				return false;
			}

			//检测个人社保比例
			$sb_rule['person'] = $this->_checkSbData($sb_rule['person']);
			if(!$sb_rule['person'])
			{
				$this->error = '社保个人比例不正确！';
				return false;
			}

			//企业或个人缴纳比例变个姿势
			$company_rule = $this->_appendRule($sb_rule['company'],'');
			$person_rule = $this->_appendRule('',$sb_rule['person']);
			$merge_rule = $this->_appendRule($company_rule,$person_rule);
			
			//社保数据处理
			$merge_rule = $this->_dataHandle($merge_rule,$sb_rule);
			if($merge_rule) return $merge_rule;
			$this->error = '社保数据错误!';
			return false;
			
		}

		/**
		 * [_checkSbData 检测个人或单位比例]
		 * @Author   JieJie
		 * @DataTime 2016-07-01T17:51:03+0800
		 * @param    [array]         &$rule_data [比例数据]
		 * @return   [array]      
		 */
		private function _checkSbData(&$rule_data)
		{
			$keys = array_keys($rule_data,'');
			if(count($keys) == count($rule_data)) return false;
			foreach ($keys as $value) 
			{
				$rule_data[$value] = 0;
			}
			return $rule_data;
		}

		/**
		 * [_appendRule 拼接缴纳比例规则]
		 * @Author   JieJie
		 * @DataTime 2016-07-01T19:14:16+0800
		 * @param    [array]        $compay_rule [企业比例数据]
		 * @param    [array]        $person_rule [个人比例数据]
		 * @return    [array]   
		 */
		private function _appendRule($company_rule,$person_rule)
		{
			//两个玩意合体
			if($person_rule && $company_rule)
			{
				for($i=0;$i<count($company_rule);$i++) 
				{
					//保存不缴纳险种的下标
					if($company_rule[$i]['company']=='0%+0' && $person_rule[$i]['person']=='0%+0')
					{
						$this->_clear_rule[] = $i;
					}
					$company_rule[$i]['person'] = $person_rule[$i]['person'];
				}
				return $company_rule;
			}

			//一个玩意换姿势
			$rule_arr = $person_rule;
			$keys = 'person';
			if($company_rule)
			{
				$rule_arr = $company_rule;
				$keys = 'company';
			}	

			$data_len = count($rule_arr);
			for ($i=0; $i < $data_len; $i+=2) 
			{ 
				$temp = array_slice($rule_arr,$i,2);
				$rules[] = array($keys=>$temp[0].'%+'.$temp[1]);
			}
			return $rules;
		}

		/**
		 * [_dataHandle 社保数据处理]
		 * @Author   JieJie
		 * @DataTime 2016-07-04T14:00:55+0800
		 * @param    [array]          $merge_rule [拼接比例规则数据]
		 * @param    [array]          $sb_rule    [社保post参数]
		 * @return   [array]                      [处理完的数据]
		 */
		private function _dataHandle($merge_rule,$sb_rule)
		{
			$merge_len = count($merge_rule);
			$bujiao = I('post.bujiao');
			for ($i=0; $i < $merge_len; $i++) 
			{ 
				$sb_rule['amount'][$i] < $sb_rule['min'] && $sb_rule['amount'][$i] = $sb_rule['min'];
				$sb_rule['amount'][$i] > $sb_rule['max'] && $sb_rule['amount'][$i] = $sb_rule['max'];
				$merge_rule[$i]['amount'] = $sb_rule['amount'][$i];
				$merge_rule[$i]['amountmax'] = $sb_rule['amountmax'][$i];
			}

			$items = $_POST['sb']['items'];
			if(!$items) return false;

			foreach ($items as $key => $value) 
			{
				$_sb['items'][$key] = array('name'=>$value);
				$_sb['items'][$key]['rules'] = $merge_rule[$key];
				$temp = in_array($key, $bujiao) ? 1 : 0;
				$_sb['items'][$key]['rules']['replenish'] = $temp;
			}

			$_sb['min'] = floatval($sb_rule['min']);
			$_sb['max'] = floatval($sb_rule['max']);
			$_sb['pro_cost'] = floatval($sb_rule['pro_cost']);
			$_sb['material'] = htmlspecialchars($sb_rule['material']);

			//社保其它收费
			$other_rule = I('post.sb_other');
			if(empty($other_rule))
			{
				$_sb['other'] = '';
				return $_sb;
			}
			$sb_other = $this->_otherCharge($other_rule);
			$_sb['other'] = $sb_other;

			//残障金
			/*if(isset($_POST['czj']) && $_POST['follow']=='1')
			$_sb['disabled'] = I('post.czj',0,'floatval');*/

			//清除不缴纳的险种
			foreach ($this->_clear_rule as $value) 
			{
				unset($_sb['items'][$value]);
			}
			return $_sb;
		}

		/**
		 * [snalyGjj 公积金规则解析入口]
		 * @Author   JieJie
		 * @DataTime 2016-07-04T16:03:32+0800
		 * @return   [array]           [解析后的数据]
		 */
		public function snalyGjj()
		{
			$gjj_rule = I('post.gjj');
			$other_rule = I('post.gjj_other');
			if(!is_array($gjj_rule) || empty($gjj_rule) || (intval($gjj_rule['min'])==0 && intval($gjj_rule['max'])==0))
			{
				$this->error = '公积金数据错误！';
				return false;
			}

			//处理比例数据
			$gjj_rule['company'] = $this->_gjjRatio($gjj_rule['company']);
			$gjj_rule['person'] = $this->_gjjRatio($gjj_rule['person']);
			//工本费
			$gjj_rule['pro_cost'] = floatval($gjj_rule['pro_cost']);
			//是否取整
			$gjj_rule['intval'] = isset($gjj_rule['intval']) ? intval($gjj_rule['intval']) : 0;
			//残障金
			/*if(isset($_POST['czj']) && $_POST['follow']=='2')
			$gjj_rule['disabled '] = I('post.czj',0,'floatval');*/

			if(empty($other_rule))
			{
				$gjj_rule['other'] = '';
				return $gjj_rule;
			}
			$gjj_order = $this->_otherCharge($other_rule);
			$gjj_rule['other'] = $gjj_order;
			return $gjj_rule;
		}

		/**
		 * [_gjjRatio 处理公积金比例]
		 * @Author   JieJie
		 * @DataTime 2016-07-04T15:38:48+0800
		 * @param    [array/string]           $ratio  [比例字符串或数组]
		 * @return   [string]                         [解析后的字符串]
		 */
		private function _gjjRatio($ratio)
		{
			$ratio_str = '';
			if(is_array($ratio))
			{
				foreach ($ratio as $key => $value) 
				{
					$ratio_str .= '-'.floatval($value).'%';
				}
				return ltrim($ratio_str,'-');
			}
			$ratio = explode(',', $ratio);
			foreach ($ratio as $key => $value) 
			{
				$ratio_str .= ','.floatval($value).'%';
			}
			return ltrim($ratio_str,',');
		}

		/**
		 * [createTemplateRule 添加模板规则]
		 * @Author   JieJie
		 * @DataTime 2016-07-04T18:51:30+0800
		 * @param    [int]             $type        [分类1社保、2公积金、3残障金]
		 * @param    [array]           $classify    [模板分类]
		 * @param    [int]             $template_id [模板id]
		 * @param    [array]           $rule        [模板规则]
		 * @return   [boolean]
		 */
		public function createTemplateRule($type,$classify,$template_id,$rule)
		{
			$rule_data['user_id'] = 0;
			$rule_data['name']='标准规则';
			$rule_data['type'] = $type;
			$rule_data['classify_mixed'] = $this->_classifySort($classify);
			$rule_data['template_id'] = $template_id;
			$rule_data['state'] = 1;
			$rule_data['create_time'] = date('Y-m-d H:i:s');
			$rule_data['rule'] = json_encode($rule,JSON_UNESCAPED_UNICODE);
			return $this->add($rule_data);
		}

		/**
		 * [updateRule 修改社保规则]
		 * @Author   JieJie
		 * @DataTime 2016-07-13T11:07:46+0800
		 * @param    [array]              $rule        [规则]
		 * @param    [int]                  $template_id [模板id]
		 * @param    [int]                  $rule_id [模板规则id]
		 * @param    array                 $classify    [分类]
		 * @return   [boolean]  
		 */
		public function updateRule($rule,$template_id,$rule_id,$classify='')
		{
			$classify = $classify ? $classify : I('post.sb_category_sub');
			$template_id = $template_id ? $template_id : I('post.template_id',0,'intval');
			$rule_id = $rule_id ? $rule_id : I('post.rule_id',0,'intval');
			$company_id = I('post.company_id',0,'intval');
			$condition = array('id'=>$rule_id,'template_id'=>$template_id,'company_id'=>$company_id,'classify_mixed'=>$this->_classifySort($classify),'type'=>1);
			$old_rule  = $this->where($condition)->field('rule,id')->find();
			if(!empty($old_rule))
			{
				$result = $this->where($condition)->save(array('rule'=>json_encode($rule,JSON_UNESCAPED_UNICODE)));
				//写日志
				if($result!==false) 
				{	
					$log_data['template_id'] = $template_id;
					$log_data['old_rule'] = $old_rule['rule'];
					$log_data['detail'] = '修改社保规则，模板规则id:'.$old_rule['id'].'模板分类:'.$this->_classifySort($classify);
					add_template_log($log_data);
					return true;
				}
				return false;
			}
			return $this->createTemplateRule(1,$classify,$template_id,$rule);
		}

		/**
		 * [_classifySort 分类数据排序处理]
		 * @Author   JieJie
		 * @DataTime 2016-07-04T18:54:22+0800
		 * @param    [array]        $classify [分类数组]
		 * @return   [string]       		  [组合的分类规则]
		 */
		private function _classifySort($classify)
		{
			$classify = array_filter($classify);
			if(empty($classify)) return '';
			//if(empty(array_filter($classify))) return '';
			if($classify == '') return $classify;
			if(!is_array($classify))
			{
				$classify = explode(',', $classify);
			}

		 	if(count($classify) > 1)
		 	{
		 		rsort($classify);
		 		$classify = implode('|', $classify);
		 	}else{
		 		$classify = $classify[0];
		 	}
	 		return $classify;		
		}

		/**
		 * [_otherCharge 社保或公积金其它费用处理]
		 * @Author   JieJie
		 * @DataTime 2016-07-06T14:38:32+0800
		 * @param    [array]                   $other_rule [费用比例数据]
		 * @return   [array]
		 */
		private function _otherCharge($other_rule)
		{
			foreach ($other_rule['name'] as $key => $value) 
			{
				if(!$value && !$other_rule['company'][$key] && !$other_rule['person'][$key]) 
				{
					unset($other_rule['name'][$key],$other_rule['company'][$key],$other_rule['person'][$key]);
					continue;
				}
				$other_charge[$key] = array('name'=> $value);
				$other_charge[$key]['rules'] = array('company'=>floatval($other_rule['company'][$key]),'person'=>floatval($other_rule['person'][$key]));
			}
			return $other_charge;
		}

		/**
		 * [getRules 获取规则]
		 * @Author   JieJie
		 * @DataTime 2016-07-07T19:17:17+0800
		 * @param    [int]                   $type           [分类类型]
		 * @param    [int]                   $template_id    [模板id]
		 * @param    [string]              $classify_mixed [分类规则]
		 * @return    [array]  
		 */
		public function getRules($type,$template_id,$classify_mixed='')
		{
			$condition['type'] = intval($type);
			$condition['template_id'] = intval($template_id);
			$condition['company_id'] = 0;
			$condition['classify_mixed'] = $this->_classifySort($classify_mixed);
			//return $this->where($condition)->getField('rule');
			return $this->field('id,rule')->where($condition)->find();
		}

		/**
		 * [analysisRule 解析公积金、社保规则返回前台数据]
		 * @Author   JieJie
		 * @DataTime 2016-07-08T15:37:58+0800
		 * @param    [type]                   $type [1社保 2公积金]
		 * @param    [type]                   $rule [规则]
		 * @return   [type]                         [description]
		 */
		public function analysisRule($type,$rule)
		{
			$rule = json_decode($rule,true);
			if(empty($rule)) return false;
			if($type == 1)
			{
				foreach ($rule['items'] as $key => $value) 
				{
					foreach ($value['rules'] as $k => $v) 
					{
						if($k == 'company' || $k == 'person')
						$rule['items'][$key]['rules'][$k] = explode('+', str_replace('%','',$v));
					}
				}
			}
			if($type == 2)
			{
				if(strpos($rule['company'], '-') )
				{
					$rule['company'] = explode('-', $rule['company']);
				}

				if(strpos($rule['person'], '-') )
				{
					$rule['person'] = explode('-', $rule['person']);
				}	
				$rule = array_map(create_function('&$n', 'return str_replace(\'%\', \'\', $n);'), $rule);
			}
			return $rule;
		}

		/**
		 * [modifyGjj 修改公积金规则]
		 * @return  boolean
		 */
		public function modifyGjj()
		{
			$template_id = I('post.template_id','','intval');
			$map['template_id'] = $template_id;
			$map['company_id'] = I('post.company_id',0,'intval');
			$map['type'] = 2;
			//解析公积金规则
			$gjj_rule = $this ->snalyGjj();
			$save_data['rule'] = json_encode($gjj_rule,JSON_UNESCAPED_UNICODE);
			$old_rule = $this->where($map)->getField('rule');
			if ($old_rule) {
				$result = $this->where($map)->save($save_data);
			}else {
				$save_data['template_id'] = $map['template_id'];
				$save_data['user_id'] = 0;
				$save_data['company_id'] = 0;
				$save_data['type'] = $map['type'];
				$save_data['name'] = '标准规则';
				$save_data['classify_mixed'] = '';
				$save_data['category'] = 1;
				$save_data['state'] = 1;
				$result = $this->add($save_data);
			}
			if($result)
			{
				$Template=M('template','zbw_');
				$Template->data(array('modify_time'=>date('Y-m-d H:i:s')))->where(array('id'=>$template_id))->save();
				//写日志
				$log_data['template_id'] = $template_id;
				$log_data['old_rule'] = $old_rule;
				$log_data['detail'] = '修改公积金规则';
				add_template_log($log_data);
				return true;
			}
			return false;
		}


		/**
		 * [modifyCzj 修改残障金]
		 * @return [type] [description]
		 */
		public function modifyCzj()
		{
			$data['rule'] = json_encode(I('post.czj'),JSON_UNESCAPED_UNICODE);
			$map['template_id'] = I('post.template_id',0,'intval');
			$map['company_id'] = I('post.company_id',0,'intval');
			$map['type'] = 3;
			$result = $this->where($map)->save($data);
			return $result ? true : false;
		}
		/**
		 * [modifyCzj 修改残障金]
		 * @return [boolean] [description]
		 */
		/*public function modifyCzj()
		{
			//旧规则中残障金跟随代码 1社保 2公积金 3之前没缴纳
			$old_follow = I('post.old_follow','','intval');
			//模板id
			$template_id = I('post.template_id','','intval');
			//修改后的跟随代码 1社保 2公积金
			$follow = I('post.follow','','intval');
			//修改后的残障金金额
			$new_amount = I('post.czj','','floatval');
			//社保分类id
			$classify_id = I('post.classify_id','','intval');
			//获取规则查询条件
			$map['template_id'] = $template_id;
			$map['type'] = $old_follow;
			switch ($old_follow) 
			{
				case 1:
					$old_rule = json_decode($this->getRules(1,$template_id,$classify_id),true);
					$map['classify_mixed'] = $classify_id;
					if($old_follow==$follow)
					{
						$old_rule['disabled'] = $new_amount;
						$result->where($map)->save(array('rule'=>json_encode($old_rule,JSON_UNESCAPED_UNICODE)));
						return $result ? true : false;
					}
					else
					{
						unset($old_rule['disabled']);
						$save_result = $this->_saveCzjRule(2);
						return $save_result && $result = $this->where($map)->save(array('rule'=>json_encode($old_rule,JSON_UNESCAPED_UNICODE)));
					}
					break;
				case 2:
					$old_rule = json_decode($this->getRules(2,$template_id),true);
					if($old_follow==$follow) 
					{
						$old_rule['disabled'] = $new_amount;
						$result = $this->where(array('template_id'=>$template_id,'type'=>2))->save(array('rule'=>json_encode($old_rule,JSON_UNESCAPED_UNICODE)));
						return $result ? true : false;
					}
					else
					{
						unset($old_rule['disabled']);
						$save_result = $this->_saveCzjRule(1);
						return $save_result &&$result = $this->where(array('template_id'=>$template_id,'type'=>2))->save(array('rule'=>json_encode($old_rule,JSON_UNESCAPED_UNICODE)));
					}

					break;	
				case 3:
					$result = $this->_saveCzjRule($follow);
					return $result ? true : false;
					break;
				default:
					$this->error = '数据错误!';
					return false;
					break;
			}
		}*/

		/**
		 * [_saveCzjRule 保存修改残障金数据]
		 * @param  [type] $type [规则类型 1社保 2公积金]
		 * @return [boolean]       [description]
		 */
		/*private  function _saveCzjRule($type)
		{
			$map['template_id'] = I('post.template_id',0,'intval');
			$map['type'] = $type;
			if($type==1)
			{
				$map['classify_mixed'] = I('post.classify_id',0,'intval');
				$sb_rule = json_decode($this->where($map)->getField('rule'),true);
				$sb_rule['disabled'] = I('post.czj',0,'floatval');
				$result = $this->where($map)->save(array('rule'=>json_encode($sb_rule,JSON_UNESCAPED_UNICODE)));
				return $result ? true : false;
			}

			$gjj_rule = json_decode($this->where($map)->getField('rule'),rule);
			$gjj_rule['disabled'] = I('post.czj',0,'floatval');
			$result = $this->where($map)->save(array('rule'=>json_encode($gjj_rule,JSON_UNESCAPED_UNICODE)));
			return $result ? true : false;
		}*/
	}
?>