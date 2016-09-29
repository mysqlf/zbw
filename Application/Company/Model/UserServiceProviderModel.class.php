<?php
/**
* 企业订单账单相关
* @author zl
*/
namespace Company\Model;
use Think\Model\RelationModel;

class UserServiceProviderModel extends RelationModel
{
     protected $tablePrefix = 'zbw_';
     /**
      * [getSaByUserid 获取公司所有客服]
      * @param  [type] $userID [description]
      * @return [type]         [description]
      */
    public function getSaByUserid($userID){
        if ($userID) {
            $result=$this->alias('usp')
                            ->field('usp.admin_id,sa.name,usp.company_id')
                            ->join('left join '.C('DB_PREFIX').'service_admin as sa on usp.admin_id=sa.id')
                            ->where(array('usp.user_id'=>$userID,'usp.admin_id'=>array('exp','is not null')))
                            ->select();
            return $result;
        }else{
            $this->error='参数错误';
            return false;
        }
    }
    /**
     * [getCSByComidandUserid 通过公司id和服务商id获取公司客服]
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function getCSByComidandUserid($where){
      if (count($where)==2) {
        $result= $this->alias('usp')
                  ->field('sa.name,sa.qq')
                  ->join('left join '.C('DB_PREFIX').'service_admin as sa on sa.id=usp.admin_id')
                  ->where($where)
                  ->limit(1)
                  ->select();
      }else{
        $this->error="参数错误";
        return false;
      } 
      return $result;
    }
    /**
     * [getServiceComByUserid 获取公司服务供应商]
     * @param  [int] $userID [用户id]
     * @return [void]         
     */
    public function getServiceComByUserid($userID){
        if ($userID) {
            $result=$this->alias('usp')
                        ->field('usp.company_id,ci.company_name')
                        ->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id=usp.company_id')
                        ->where('usp.user_id='.intval($userID))
                        ->select();
            return $result;
        }else{
            $this->error='参数错误';
            return false;
        }
    }
    /**
     * [getDiffAmount 获取对应公司差额]
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function getDiffAmount($where){
        return $this->where($where)->getField('diff_amount');
    }
    
     /**
      * [getSaByUserid 获取公司所有客服以及对应的服务商]
      * @param  [type] $userID [description]
      * @return [type]         [description]
      */
    public function getServiceAdminList($userID){
        if ($userID) {
            $result=$this->alias('usp')
                            ->field('usp.admin_id,usp.company_id,sa.name')
                            ->join('left join '.C('DB_PREFIX').'service_admin as sa on usp.admin_id=sa.id')
                            ->where(array('usp.user_id'=>$userID))
                            ->select();
            return $result;
        }else{
            $this->error='参数错误';
            return false;
        }
    }
}
?>