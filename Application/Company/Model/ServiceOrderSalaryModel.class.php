<?php
/**
* 
*/
namespace Company\Model;
use Think\Model\RelationModel;

class ServiceOrderSalaryModel extends RelationModel{
    
    protected $tablePrefix = 'zbw_';
    /**
     * [getCountPayOrder 统计该用户的订单数]
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function getCountPayOrder($where){
        $Count=$this->alias('sos')->where($where)->count('id');
        return $Count;
    }
    /**
     * [getSalaryByPayOrderid 获取代发工资详细信息]
     * @param  [type] $orderId  [description]
     * @param  string $pageSize [description]
     * @return [type]           [description]
     */
    public function getSalaryByPayOrderid($where,$pageSize='10'){
        if ($orderId) {
            $pageCount=$this->getCountPayOrder($where);
            if ($pageCount>0) {
                $page = get_page($pageCount,$pageSize);
                $show = $page->show();// 分页显示输出
                $result=$this->alias('sos')
                             ->field('sos.location,sos.date,sos.salary,sos.actual_salary,sos.tax,sos.deduction_income_tax,sos.price,pb.card_num,pb.person_name,pb.bank,pb.account,sp.name,wl.af_service_price')
                             ->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=sos.base_id')
                             ->join('left join '.C('DB_PREFIX').'service_product as sp on sos.product_id=sp.id')
                             ->join('left join '.C('DB_PREFIX').'service_product_order as spo on sos.product_id=spo.product_id and sos.user_id=spo.user_id')
                             ->join('left join '.C('DB_PREFIX').'warranty_location as wl on wl.service_product_order_id=spo.id and wl.location=sos.location')
                             ->where($where)
                             ->limit($page->firstRow,$page->listRows)
                             ->order('sos.create_time desc')
                             ->select();
                return array('data'=>$result,'page'=>$show);
            }else{
                $this->error="没有记录";
                return false;
            }
        }else{
            $this->error='参数错误';
            return false;
        }
    }
    /**
     * [getAllSalaryByPayOrderid 获取所有的代发工资详细信息]
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public function getSalaryAllByPayOrderid($where){
        if ($where) {
            $result=$this->alias('sos')
                         ->field('sos.location,sos.date,sos.salary,sos.actual_salary,sos.tax,sos.deduction_income_tax,sos.price,pb.card_num,pb.person_name,pb.bank,pb.account,sp.name,wl.af_service_price')
                         ->join('left join '.C('DB_PREFIX').'person_base pb on pb.id=sos.base_id')
                         ->join('left join '.C('DB_PREFIX').'service_product sp on sos.product_id=sp.id')
                         ->join('left join '.C('DB_PREFIX').'service_product_order spo on sos.product_id=spo.product_id and sos.user_id=spo.user_id')
                         ->join('left join '.C('DB_PREFIX').'warranty_location wl on wl.service_product_order_id=spo.id and wl.location=sos.location')
                         ->where($where)
                         ->group('sos.base_id')
                         ->order('sos.create_time desc')
                         ->select();
            return $result;
        }else{
            $this->error='参数错误';
            return false;
        }
    }
    /**
     * [getServiceOrderSalaryCountByCondition 统计代发工资人数]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function getServiceOrderSalaryCountByCondition($data){
        if (is_array($data)) {
            $condition = array();
            if (!empty($data['user_id'])) {
                $condition['pb.user_id'] = $data['user_id'];
                //$condition['sos.user_id'] = $data['user_id'];
            }
            if ($condition['sos.state']) {
                $condition['sos.state'] = array($condition['sos.state'],array('neq',-8),array('neq',-9),'and');
            }else {
                $condition['sos.state'] = array(array('neq',-8),array('neq',-9),'and');
            }

            $Count = $this->alias('sos')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=sos.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sos.product_id=sp.id')->where($condition)->count('sos.id');
            return $Count;
        }
    }

	/**
	 * getServiceOrderSalaryListByCondition function
	 * 根据条件获取列表
	 * @param array $data 条件数组
	 * param int $pageSize 分页大小，默认10
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getServiceOrderSalaryListByCondition($data,$pageSize = 10){
		if (is_array($data)) {
			$condition = array();
			//操作状态 0待审核 1审核成功 -1审核失败 2支付成功 -2支付失败 3发放成功 -3发放失败 -9删除
			if (isset($data['type']) && null !== $data['type']) {
				if ('0' == $data['type']) {
					$condition['sos.state'] = array('eq',0);
				}else if ('1' == $data['type']) {
					$condition['sos.state'] = array('in','1,-1');
				}else if ('2' == $data['type']) {
					$condition['sos.state'] = array('eq',1);
				}else if ('3' == $data['type']) {
					$condition['sos.state'] = array('eq',2);
				}else if ('4' == $data['type']) {
					$condition['sos.state'] = array('eq',3);
					//$condition['sos.state'] = array('in','3,-3');
				}
			}
			if (!empty($data['user_id'])) {
				$condition['pb.user_id'] = $data['user_id'];
				//$condition['sos.user_id'] = $data['user_id'];
			}
			if (!empty($data['product_id'])) {
				$condition['sos.product_id'] = $data['product_id'];
			}
			if (!empty($data['date'])) {
				$condition['sos.date'] = array('eq',$data['date']);
			}
			if (!empty($data['company_id'])) {
				$condition['sp.company_id'] = array('eq',$data['company_id']);
			}
			if (!empty($data['admin_id'])) {
				$userServiceProvider = D('UserServiceProvider');
				$userServiceProviderResult = $userServiceProvider->field('company_id')->where(array('user_id'=>$data['user_id'],'admin_id'=>$data['admin_id'],'state'=>1))->select();
				$companyIds = array();
				foreach ($userServiceProviderResult as $key => $value) {
					$companyIds[] = $value['company_id'];
				}
				if ($companyIds) {
					if ($condition['sp.company_id']) {
						$condition['sp.company_id'] = array(array('in',$companyIds),$condition['sp.company_id'],'and');
					}else {
						$condition['sp.company_id'] = array('in',$companyIds);
					}
				}
			}
			if (!empty($data['person_name'])) {
				$condition['pb.person_name'] = array('like','%'.$data['person_name'].'%');
			}
			if (!empty($data['card_num'])) {
				$condition['pb.card_num'] = array('like','%'.$data['card_num'].'%');
			}
			if (isset($condition['sos.state'])) {
				$condition['sos.state'] = array($condition['sos.state'],array('neq',-8),array('neq',-9),'and');
			}else {
				$condition['sos.state'] = array(array('neq',-8),array('neq',-9),'and');
			}
			//dump($condition);
			
			$pageCount = $this->alias('sos')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=sos.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sos.product_id=sp.id')->where($condition)->count('sos.id');
			$page = get_page($pageCount,$pageSize);
			
			$result = $this->alias('sos')->field('sos.*,pb.person_name,pb.card_num,pb.bank,pb.branch,pb.account_name,pb.account,sp.name as product_name,ci.company_name,po.order_no as pay_order_no,po.transaction_no as pay_transaction_no')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=sos.base_id')->join('left join '.C('DB_PREFIX').'service_product as sp on sos.product_id=sp.id')->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id=sp.company_id')->join('left join '.C('DB_PREFIX').'pay_order as po on po.id=sos.pay_order_id')->where($condition)->limit($page->firstRow,$page->listRows)->order('sos.create_time desc')->select();
			
			if ($result || null === $result) {
				$countResult = array();
				$countResult[0] = $this->alias('sos')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=sos.base_id')->where(array('pb.user_id'=>$data['user_id'],'sos.state'=>0))->count('sos.id');
				$countResult[2] = $this->alias('sos')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=sos.base_id')->where(array('pb.user_id'=>$data['user_id'],'sos.state'=>1))->count('sos.id');
				$countResult[3] = $this->alias('sos')->join('left join '.C('DB_PREFIX').'person_base as pb on pb.id=sos.base_id')->where(array('pb.user_id'=>$data['user_id'],'sos.state'=>2))->count('sos.id');
				return array('data'=>$result,'page'=>$page->show(),'count'=>$countResult);
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
    public function getPayrollCreditCount($where){
         $result= $this->field('base_id')->where($where)
            ->group('base_id')
            ->select();
        return count($result);
    }
}
 ?>