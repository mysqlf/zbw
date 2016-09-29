<?php
/**
* 企业订单账单相关
* @author zl
*/
namespace Home\Model;
use Think\Model;

class PayOrderModel extends Model{
    protected $tablePrefix = 'zbw_';
    /**
     * [searchRuningBill 付款单筛选]
     * @param  array  $where    [筛选条件]
     * @param  string $pageSize [分页大小]
     * @return void        
     */
    public function getOrderListOfSearch($where,$pageSize='10'){
        if (!$where) {
            $this->error='参数错误';
            return false;
        }else{
            //分页部分
            $pageCount=$this->alias('po')->where($where)->count('po.id');
            if ($pageCount>0) {
                $page=get_page($pageCount,$pageSize);
                $show = $page->show();// 分页显示输出
                //数据查询部分
                $result=$this->alias('po')
                                ->field('po.*,ci.company_name,sa.name')
                                ->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id=po.company_id')
                                ->join('left join '.C('DB_PREFIX').'user_service_provider as usp on po.company_id=po.company_id')
                                ->join('left join '.C('DB_PREFIX').'service_admin as sa on sa.id=usp.admin_id')
                                ->where($where)
                                ->limit($page->firstRow,$page->listRows)
                                ->group('po.id')
                                ->order('po.create_time desc')
                                ->select();
                foreach ($result as $k => $v) {
                    $result[$k]['location']=empty($v['location'])?'/':showAreaName($v['location']);
                    //$tmp=self::_getOrderProName($v['id'],$v['type']);
                    //$result[$k]['proname']=$tmp['name'];
                    //$result[$k]['proid']=$tmp['id'];
                }
                return array('data'=>$result,'page'=>$show);
            }else{
                $this->error="没有记录";
                return false;
            }
        }
    }
   
    /**
     * [getBanknoByOrderid 通过订单id获取订单号]
     * @param  [int] $orderid [订单id]
     * @param  [int] $userid  [用户id]
     * @return [string]          [银行流水号]
     */
    public function getOrderBankByOrderid($orderid,$userid){
        if ($orderid) {
            $orderno=$this->alias('po')
                        ->field('cb.bank,cb.account,cb.account_name,cb.branch')
                        ->join('left join '.C('DB_PREFIX').'company_bank as cb on cb.company_id=po.company_id')
                        ->where(array('po.user_id'=>$userid,'po.id'=>$orderid))
                        ->find();
            if ($orderno!=false&&$orderno!=null) {
                return $orderno;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    /**
     * [getPriceByUserandOrder 获取一个订单的金额]
     * @param  [type] $orderid [订单id]
     * @param  [type] $userid  [用户id]
     * @return [array]         订单金额,差额,实付金额
     */
    public function getPriceByUserandOrder($orderid='0',$userid='0'){
        if ($orderid) {
            $result=$this->field('amount,diff_amount,actual_amount')
                        ->where(array('user_id'=>$userid,'id'=>$orderid,'state'=>'0'))
                        ->find();
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
        }else{
            $this->error='参数错误';
            return false;
        }
    }
    /**
     * [getPayOrderPriceByOrderid 通过支付订单id获取支付订单详情]
     * @param  [int] $orderid 
     * @return [void]          
     */
    public function getPayOrderPriceByOrderid($userid,$orderid){
        if ($orderid) {
            $result= $this->alias('po')
                        ->field('po.*,ci.company_name')
                        ->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id=po.company_id')
                        ->where(array('po.id'=>$orderid,'po.user_id'=>$userid))
                        ->limit(1)
                        ->select();
            if ($result) {
                return $result;
            }else if (null === $result) {
                return $result;
            }else if (false === $result) {
                wlog($this->getDbError());
                $this->error = $this->getDbError();
                return false;
            }else {
                $this->error = '未知错误！';
                return false;
            }
        }else{
            $this->error='参数错误';
            return false;
        }
       
    }
    /**
     * [getPayOrderlistByBillid 获取对账单下的支付订单]
     * @param  [int] $billid [对账单id]
     * @return [type]         [description]
     */
    public function getPayOrderlistByBillid($billid){
        if ($billid) {
            $result=$this->alias('po')
                    ->field('po.id,po.type')
                    ->where(array('po.service_bill_id'=>intval($billid)))
                    ->select();
            if ($result) {
                return $result;
            }else if (null === $result) {
                return $result;
            }else if (false === $result) {
                wlog($this->getDbError());
                $this->error = $this->getDbError();
                return false;
            }else {
                $this->error = '未知错误！';
                return false;
            }
        }else{
            $this->error='参数错误';
            return false;
        }
        
    }
    
    /**
     * [_getOrderProName 获取订单所属产品名]
     * @param  [int] $orderid [订单ID]
     * @param  [int] $type    [订单类型]
     * @return [str]          
     */
    private function _getOrderProName($orderid,$type){
        switch ($type) {
            case '1':
                $spo=M('service_product_order');
                $proname=$spo->alias('spo')
                            ->field('sp.name,sp.id')   
                            ->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id=spo.product_id')
                            ->where(array('spo.pay_order_id'=>$orderid))
                            ->limit(1)
                            ->find();
                break;
            case '2':
                $pii=M('person_insurance_info');
                $proname=$pii->alias('pii')
                            ->field('sp.name,sp.id') 
                            ->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id=pii.product_id')
                            ->where(array('pii.pay_order_id'=>$orderid))
                            ->limit(1)
                            ->find();
                break;
            case '3':
                $sos=M('service_order_salary');
                $proname=$sos->alias('sos')
                            ->field('sp.name,sp.id') 
                            ->join('left join '.C('DB_PREFIX').'service_product as sp on sp.id=sos.product_id')
                            ->where(array('sos.pay_order_id'=>$orderid))
                            ->limit(1)
                            ->find();
                break;
        }
        return $proname;
    }
    
	/**
	 * savePayOrder function
	 * 保存支付订单
	 * @access public
	 * @param array $data 数据
	 * @return boolean
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function savePayOrder($data){
		if (is_array($data) && !empty($data['user_id']) && !empty($data['company_id']) && !empty($data['type']) && !empty($data['location']) && !empty($data['handle_month'])) {
			$condition = $data;
			$condition['state'] = 0;
			unset($condition['amount']);
			$payOrderResult = $this->field(true)->where($condition)->order('create_time desc')->find();
			if ($payOrderResult) {
				$result =  $payOrderResult['id'];
				if ($data['amount']) {
					//$payOrderSaveResult = $this->where(array('id'=>$payOrderResult['id']))->save(array('amount'=>array('exp','amount + '.$data['amount'])));
					$payOrderSaveResult = $this->where(array('id'=>$payOrderResult['id']))->setInc('amount',$data['amount']);
					if (false === $payOrderSaveResult) {
						$this->error = '更新支付订单数据失败!';
						$result = false;
					}
				}
			}else {
				$data['order_no'] =create_order_sn();
				//$data['amount'] = 0;
				$data['diff_amount'] = 0;
				$data['actual_amount'] = 0;
				$data['state'] = 0;
				$data['pay_deadline'] = 0;
				$data['create_time'] = date('Y-m-d H:i:s');
				$result = $this->add($data);
				if (false === $result) {
					wlog($this->getDbError());
					$this->error = '系统内部错误!';
				}
			}
			return $result;
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}
}
?>