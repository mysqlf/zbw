<?php
	namespace Home\Model;
	use Think\Model;
	class CompanyInfoModel extends Model{
		protected $tablePrefix = 'zbw_';
		
		public function insetInfo($data){
			return $this->add($data);
		}

		/**
		 * getCompanyUserByCuid function
		 * 根据企业用户id获取企业用户以及企业信息
		 * @access protected
		 * @param int $cuid 企业用户id
		 * @param bool $isRelation 是否关联查询
		 * @return array 企业用户信息数组
		 * @author rohochan <rohochan@gmail.com>
		 **/
		protected function getCompanyUserByCuid($cuid,$isRelation= true){
			$companyUser = D('User')->relation($isRelation)->field(true)->getById($cuid);
			return $companyUser;
		}
		
		//修改邮箱状态
		public function modifyState($where){
			$result = $this->where($where)->save(array('email_activation'=>1));
			if (false !== $result) {
				$res = $this->where($where)->find();
				if ($res['user_id']) {
					//同步数据到redis
					$companyUser = $this->getCompanyUserByCuid($res['user_id']);
					unset($companyUser['password']);
					$redis = initRedis();
					$redisKey = 'zby:com:user:'.$res['user_id'];
					//$redisres = $redis->hGetAll($redisKey);
					$redisres = $redis->hSet($redisKey,'mCuid',$res['user_id']);
					$redisres = $redis->hSet($redisKey,'mCid',$res['id']);
					$redisres = $redis->hSet($redisKey,'mCompanyUser',json_encode($companyUser));
				}
			}
			return  $result!==false ? true : false;
		}

		//获取单个企业详情
		public function getFirmInfo($map){
			return $this->where($map)->field('*')->find();
		}
		//检测企业用户名是否已经被注册
		public function checkCompanyName(){
			$username = I('get.company_name','','htmlspecialchars');
			return $this->where(array('company_name'=>$username))->count();
		}
	}
?>