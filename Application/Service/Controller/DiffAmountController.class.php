<?php
namespace Service\Controller;
/**
 * 差额管理
 */
class DiffAmountController extends ServiceBaseController {

	protected $_serviceDiff;
	protected $_type;
	protected $_item;

    protected function _initialize()
    {
        parent::_initialize();
        $this->_serviceDiff = D('ServiceDiff');
        $this->_type = array(1=>'办理失败', '规则调整', '缴费异常', '工本费');
        $this->_item = array(1=>'社保', '公积金');


    }
	public function comDiffList(){
		$order_no =  I('get.order_no', '');
		$person_name =  I('get.person_name', '');
		$company_name =  I('get.company_name', '');
		$handle_month =  I('get.handle_month', '');
		$pay_time =  I('get.pay_time', '');
		$pay_time1 =  I('get.pay_time1', '');
		$type =  I('get.type', '');
		$item =  I('get.item', '');

		$where = 'po.company_id = '.$this->_cid;
        // $blongToService = $this->blongToService();        
        // $where .= ' AND po.user_id in('.$blongToService.')';
       	
		if($order_no){
			$where .= " AND po.order_no = '{$order_no}'";
		}
		if($person_name){
			$where .= " AND pb.person_name = '{$person_name}'";
		}
		if($company_name){
			$where .= ' AND ci.company_name like \'%'.$company_name.'%\'';
		}
		if($handle_month){
			$handle_month = str_replace('/', '', substr($handle_month, 0, 7));
			$where .= " AND pii.handle_month  = '{$handle_month}'";
		}
		if($pay_time){
			$where .= " AND date_format(po.pay_time, '%Y/%m/%d') >= '{$pay_time}'";//' AND po.pay_time >= '.$order_no;
		}
		if($pay_time1){
			$where .= " AND date_format(po.pay_time, '%Y/%m/%d') <= '{$pay_time1}'";
		}
		if($type){
			$where .= ' AND sd.type = '.$type;
		}
		if($item){
			$where .= ' AND sd.item = '.$item;
		}	

//echo $where;
		$result = $this->_serviceDiff->comDiffList($where, $this->blongToService($this->_cid));
		$this->assign('result', $result)->assign('_type',$this->_type)->assign('_item', $this->_item);
		$this->display('Balance/company');
	}

	public function perDiffList(){
		//$id = 
		$this->display('Balance/person');
	}

	public function detail(){
		$id = I('get.id', '');
		$item = I('get.item', '0');
		$type = I('get.type', '0');
		$detail_id = I('get.detail_id', '0');

		$result = $this->_serviceDiff->detail(array('id'=> $id, 'item'=> $item, 'type'=> $type, 'detail_id'=> $detail_id), $this->_AccountInfo);
		if(empty($result)) $this->error('数据不存在！');
		$this->assign('_type',$this->_type)->assign('_item', $this->_item)->assign('type',$type)->assign('item', $item);
		$this->assign('result',$result);
		$this->display('Balance/details');
	}
}