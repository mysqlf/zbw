<?php
	namespace Home\Model;
	use Think\Model;
	class ClientAdvisoryModel extends Model
	{
		protected $tablePrefix = 'zbw_';
		
		#字段映射
		protected $_map = array(
		    'person' => 'contact_name', 
		    'phone' => 'contact_phone',
		    'company' => 'contact_company',
		    'serve-text' => 'product_info',
		);

		#自动验证
		protected $_validate = array(
			array('contact_name','_checkName','姓名不符合规范',1,'callback',1),
			array('contact_phone','_checkPhone','手机和格式不正确',1,'callback',1),
		);

		#添加联系客户
		public function addClient()
		{
			if(!$this->create()) return $this->getError();
			$this->create_time = date('Y-m-d H:i:s');
			return $this->add();
		}

		#联系客户列表
		public function clientList()
		{
			$map = '';//查询条件
			if($_GET['search']!='' && $_GET['search_field']!='')
			{
				//搜索字段下标与select value值一一对应
				$search_field = array('id','contact_company','contact_name','contact_phone');
				$key = I('search_field','','intval');
				//4为已联系 5为未联系
				/*if($key==4 || $key==5)
					$map['state'] = $key==4 ? '1' : '0';
				else*/
				$map[$search_field[$key]] = array('like','%'.I('get.search','','htmlspecialchars').'%');
			}
			//已联系，未联系
			if($_GET['state']==='1' || $_GET['state']==='0') 
			{
				$map['state'] = I('get.state','');
			}

			//搜索顺序，用于导出数据标题	
			$field = 'id,contact_company,contact_name,contact_phone,product_info,create_time,state';
			//用户搜索导出全部搜索数据
			if(is_array($map) && $_GET['csv']==1)
			{
				return $this->where($map)->field($field)->select();
			}

			$count = $this->where($map)->count();
			$Page  = new \Think\Page($count,50);
			$data['page'] = $Page->show();
			$data['list'] = $this->field($field)->where($map)->limit($Page->firstRow.','.$Page->listRows)->order('create_time DESC')->select();
			return $data;
		}

		#更新联系客户状态
		public function setState()
		{
			$id = I('get.id',0,'intval');
			return $this->where('id='.$id)->save(array('state'=>'1'));
		}

		#检测用户名
		public function _checkName($name)
		{
			$name_len = mb_strlen($name,'utf8');
			if($name_len<2 || $name_len>20) return false;
			return true;
		}

		#检测手机号
		public function _checkPhone($phone)
		{
			$reg = '/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|17[0-9]|18[0|1|2|3|4|5|6|7|8|9])\d{8}$/';
			if(preg_match($reg, $phone)) return true;
			else return false;
		}
	}
?>