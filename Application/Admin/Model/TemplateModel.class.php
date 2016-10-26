<?php
	namespace Admin\Model;
	use Think\Model;
	class TemplateModel extends Model
	{
		protected $tablePrefix = 'zbw_';
		/**
		 * [createTemplate 创建模板]
		 * @Author   JieJie
		 * @DataTime 2016-07-06T19:07:01+0800
		 * @param    integer          $type [模板类型 默认系统模板]
		 * @return   [int]                	[最后插入的id]
		 */
		public function createTemplate($get_id = false)
		{
			//城市模板存在
			$city_id = $this->getFieldByLocation(I('post.location','','intval'),'id');
			if($city_id)  
			{
				if($get_id) return $city_id;
				$this->error = '当前城市模板已存在！请返回修改模板。';
				return false;
			}
			$add_data = array(
				'admin_id' => UID,
				'state' => 1,
				//'type' => $type,
				'location' => I('post.location','','intval'),

				// 'soc_payment_type' => I('post.soc_payment_type','','intval'),
				// //'pro_payment_type' => I('post.pro_payment_type','','intval'),
				// 'pro_payment_type' => I('post.soc_payment_type','','intval'),
				// 'soc_deadline' => I('post.soc_deadline','','intval'),
				// //'pro_deadline' => I('post.pro_deadline','','intval'),
				// 'pro_deadline' => I('post.soc_deadline','','intval'),
				// 'soc_payment_month' => I('post.soc_payment_month','','intval'),
				// //'pro_payment_month' => I('post.pro_payment_month','','intval'),
				// 'pro_payment_month' => I('post.soc_payment_month','','intval'),

				'create_time' => date('Y-m-d H:i:s'),
				'modify_time' => date('Y-m-d H:i:s'),
				'company_id' => 0,
				'name' => showAreaName1(I('post.location','','intval')),
			);

			return $this->add($add_data);
		}

		/**
		 * [getTemplateInfo 获取模板列表]
		 * @Author   JieJie
		 * @DataTime 2016-07-06T19:08:19+0800
		 * @return   [type]                   [description]
		 */
		public function getTemplateInfo()
		{
			$condition['state'] = array('neq',-9);
			//isset($_GET['location1']) and $condition['location'] = intval($_GET['location1']);
			//isset($_GET['location2']) and $condition['location'] = intval($_GET['location2']);
			if ($_GET['location2']) {
				$condition['location'] = ['between',[$_GET['location2'],$_GET['location2']+1000]];
			}else if($_GET['location1']){
				$condition['location'] = ['between',[$_GET['location1'],$_GET['location1']+1000000]];
			}
			//intval($_GET['payment_type'])!=0 and $condition['payment_type'] = intval($_GET['payment_type']);
			//intval($_GET['soc_payment_type'])!=0 and $condition['soc_payment_type'] = intval($_GET['soc_payment_type']);
			isset($_GET['type']) and  intval($condition['type']);
			
			$count = $this->where($condition)->count();
			$Page  = new \Think\Page($count,25);
			$data['page'] = $Page->show();
			//$data['list'] = $this->field('id,name,location,soc_payment_type,modify_time,admin_id')
			$data['list'] = $this->field('id,name,location,modify_time,admin_id')
					->where($condition)
					->limit($Page->firstRow.','.$Page->listRows)
					->order('id DESC')
					->select();
			return $data;
		}

		/**
		 * [modifyTemplate 修改模板缴费标准]
		 * @Author   JieJie
		 * @DataTime 2016-07-08T18:34:49+0800
		 * @return   [boolean] 
		 */
		public function modifyTemplate()
		{
			$template_id = I('post.template_id','','intval');
			$data = $this->create();
			/*
			empty($data['pro_payment_type']) && $data['pro_payment_type'] = $data['soc_payment_type'];
			empty($data['pro_deadline']) && $data['pro_deadline'] = $data['soc_deadline'];
			empty($data['pro_payment_month']) && $data['pro_payment_month'] = $data['soc_payment_month'];
			*/
			$result = $this->where('id='.$template_id)->save($data);
			if(false === $result) return false;

			$log_data['template_id'] = $template_id;
			$log_data['old_rule'] = json_encode($data);
			$log_data['detail'] = '缴费标准修改';
			return add_template_log($log_data);
			
		}
		/**
		 * [modifyTemplate 修改模板缴费标准]
		 * @Author   JieJie
		 * @DataTime 2016-07-08T18:34:49+0800
		 * @return   [boolean] 
		 */
		/*public function modifygjjTemplate()
		{
			$template_id = I('post.template_id','','intval');
			$data = $this->create();
			empty($data['pro_payment_type']) && $data['pro_payment_type'] = $data['pro_payment_type'];
			empty($data['pro_deadline']) && $data['pro_deadline'] = $data['pro_deadline'];
			empty($data['pro_payment_month']) && $data['pro_payment_month'] = $data['pro_payment_month'];
			$result = $this->where('id='.$template_id)->save($data);
			if(false === $result) return false;

			$log_data['template_id'] = $template_id;
			$log_data['old_rule'] = json_encode($data);
			$log_data['detail'] = '缴费标准修改';
			return add_template_log($log_data);
		}*/
	}
?>