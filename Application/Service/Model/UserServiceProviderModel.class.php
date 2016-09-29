<?php
/**
* 
*/
namespace Service\Model;
use Think\Model;

class UserServiceProviderModel extends Model{
    protected $tablePrefix = 'zbw_';
    
	/**
	 * getUserCompany function
	 * 根据条件获取列表
	 * @param array $companyId 企业信息id
	 * @return mixed
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getUserCompany($companyId){
		if ($companyId > 0) {
			$condition = array('usp.company_id'=>$companyId);
			$result = $this->alias('usp')->field('usp.id,usp.user_id,usp.admin_id,usp.diff_amount,ci.company_name,ci.contact_name,ci.audit')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.user_id=usp.user_id')->where($condition)->order('usp.create_time desc')->select();
			
			if ($result || null === $result) {
				return $result;
			}else if (false === $result) {
				wlog($this->getDbError());
				$this->error = '系统内部错误！';
				return false;
			}else {
				$this->error = '未知错误！';
				return false;
			}
		}else {
			$this->error = '非法参数!';
			return false;
		}
	}
}
 ?>