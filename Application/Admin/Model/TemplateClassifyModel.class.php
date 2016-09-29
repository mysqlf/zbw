<?php
	namespace Admin\Model;
	use Think\Model;
	class TemplateClassifyModel extends Model
	{
		protected $tablePrefix = 'zbw_';
		//是否开启无限分类
		public $is_Infinite = false;
		/**
		 * [add 添加模板分类]
		 * @Author   JieJie
		 * @DataTime 2016-06-29T16:21:32+0800
		 * @param    [int]  $template_id [模板id]
		 * @return   [mixed] 			 [失败返回false 成功返回新增id数组]
		 */
		public function addClassify($template_id)
		{
			if(!$this->is_Infinite)
			{
				$map['template_id'] = $template_id;
				$map['name'] = array('neq','');
				$result = $this->where($map)->find();
				if($result)
				{
					$this->error = '分类已经存在，不允许添加分类。';
					return false;
				}
			}
			if(I('post.category')=='')
			{
				$this->error = '分类名不能为空!';
				return false;
			}
			//组装新增数据
			$add_data = array(
				'name' => I('post.category','trim'),
				'type' => I('post.type',1,'intval'),
				'template_id' => intval($template_id),
				'fid' => 0,
				'create_time' => date('Y-m-d H:i:s'),
			);
			$category_sub = array_filter(I('post.category_sub'));
			//检测该分类是否唯一
			$parent_id = $this->_checkAdd($add_data);
			if(!$parent_id) 
				$parent_id = $this->add($add_data);
			if(!$parent_id)
			{
				$this->error = '数据错误!';
				return false;
			}
			//存放子记录
			$child_data = array();
			foreach ($category_sub as $value) 
			{
				$add_data['name'] = $value;
				$add_data['fid'] = $parent_id;
				//检测是否有重复记录
				$check_result = $this->_checkAdd($add_data);
				$check_result or $child_data[] = $add_data;
			}
			//批量插入，获取第一条数据id
			$return_id = $this->addAll($child_data);
			//返回添加的分类信息
			return $this->get_classify_fid($parent_id,$add_data['type']);
		}

		/**
		 * [_checkAdd 检测记录是否存在]
		 * @Author   JieJie
		 * @DataTime 2016-06-29T14:43:53+0800
		 * @param    [array]     $conditon [需添加的记录数组]
		 * @return   [mixed]               [存在记录的id，不存在返回false]
		 */
		private function _checkAdd($conditon)
		{
			unset($conditon['create_time']);
			return $this->where($conditon)->getField('id');
		}

		/**
		 * [get_classify_fid 返回指定pid的分类]
		 * @Author   JieJie
		 * @DataTime 2016-06-30T14:46:11+0800
		 * @return   [array]     [返回指定id分类数组]
		 */
		public function get_classify_fid($fid,$type)
		{
			$fid or $fid = I('post.fid','','intval');
			$type or $type = I('post.type','','intval');
			$map['_complex'] = array('fid' => $fid, '_logic'=> 'OR', 'id'=>$fid);
			$map['type'] = $type;
			$map['state'] = 1;
			$classify_info = $this->where($map)->field('*')->order('id ASC')->select();
			$list = array_shift($classify_info);
			$list['category_sub'] = $classify_info;
			return $list;
		}

		/**
		 * [classifyDel 删除分类]
		 * @Author   JieJie
		 * @DataTime 2016-06-30T16:54:35+0800
		 * @param    [int]       $id   [分类id]
		 * @param    [int]       $type [类型]
		 * @return   [boolean] 
		 */
		public function classifyDel($id,$type)
		{
			//判断当前id有没有父类
			$conditon['fid'] = $id;
			$conditon['type'] = $type;
			$parent_count = $this->where($conditon)->count();
			if($parent_count>=1)
			{
				$this->error = '请先删除子类!';
				return false;
			}

			//删除当前分类
			unset($conditon['fid']);
			$conditon['id'] = $id;
			$template_id = $this->where($conditon)->getField('template_id');
			if($template_id)
			{
				//查找相应的规则
				$Rule = M('template_rule','zbw_');
				$rule_map['template_id'] = $template_id;
				$rule_map['type'] = $type;
				$rule_map['classify_mixed'] = array('like','%'.$id.'%');
				//删除相应的规则
				$del_rule = $Rule->where($rule_map)->save(array('state'=>-9));
				//删除相应的分类
				$del_rule!==false && $del_classify = $this->where($conditon)->save(array('state'=>-9));
				if($del_rule!==false && $del_classify!==false) return true;
			}

			$this->error = '删除失败，请重试';
			return false;
		}

		/**
		 * [cleanClassify 清理id为0的分类]
		 * @Author   JieJie
		 * @DataTime 2016-07-01T17:07:29+0800
		 * @param    [array]      $classify_info [分类数组]
		 * @return   [mixed]      [清理后的分类]
		 */
		public function cleanClassify($classify_info)
		{
			if(is_array($classify_info))
			{
				$keys = array_keys($classify_info,'0');
				foreach ($variable as $value) 
				{
					unset($classify_info[$value]);
				}
				if(count($classify_info) == 0) return '';
				return array_values($classify_info);
			}
			return '';
		}

		/**
		 * [modifyClassifyHandle 修改社保分类处理]
		 * @return [boolean] 
		 */
		public function modifyClassifyHandle()
		{
			$template_id = I('post.template_id','',0,'intval');
			$category = I('post.category');
			$fid = I('post.category_id','','intval');
			if(!$category)
			{
				$this->error = array('status'=>1,'msg'=>'分类名必须填写');
				return false;
			}
			$map['category_id'] = $fid;
			$map['template_id'] = $template_id;
			$result = $this->where($map)->save(array('name'=>$category));
			if($result!==false)
			{
				$category_sub = I('post.category_sub');
				$category_sub_id = I('post.category_sub_id');
				foreach ($category_sub as $key => $value)
				{
					if(!$value) continue;
					$classify_id = $category_sub_id[$key];
					//修改时新添加的分类id为0
					if($classify_id==0)
					{
						$data['name'] = $value;
						$data['type'] = 1;
						$data['template_id'] = $template_id;
						$data['fid'] = $fid;
						$data['create_time'] = date('Y-m-d H:i:s');
						$this->add($data);
					}else{
						$this->where('id='.intval($classify_id))->save(array('name'=>$value));
					}
				}

				return $this->get_classify_fid($fid,1);
				//return true;
			}
			$this->error =array('status'=>1,'msg'=>'修改失败，请重试!');
			return false;
		}
	}
?>