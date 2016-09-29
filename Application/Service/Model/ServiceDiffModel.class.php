<?php
namespace Service\Model;
use Think\Model;

class ServiceDiffModel extends Model{

	protected function _initialize()
    {
       
    }

	public function comDiffList($where, $subUserId){
		if(empty($subUserId)) return array();
		$page = I('get.p', '1');
		// $where .=  ' AND CASE WHEN sd.item = 3 	THEN  sos.user_id in('.$subUserId.')  ELSE pii.user_id in('.$subUserId.') END ';
		// $count = $this->alias('sd')
		// 				->join('left join zbw_service_insurance_detail sid ON sid.id = sd.detail_id')
		// 				->join('left join zbw_service_order_salary sos ON sos.id = sd.detail_id')
		// 				->join('left join zbw_person_insurance_info pii ON pii.id =  sid.insurance_info_id')
		// 				->join('zbw_pay_order po ON (sd.item=3 AND po.id=sos.pay_order_id) OR (po.id=sid.pay_order_id) ')
		// 				->join('zbw_company_info ci ON (sd.item=3 AND ci.user_id=sos.user_id) OR (ci.user_id=pii.user_id)')
		// 				->join('zbw_person_base pb ON (sd.item=3 AND pb.id=sos.base_id) OR (pb.id=pii.base_id)')	
		// 				->where($where)->count();

		// $result = $this->alias('sd')->field('sd.*,ci.company_name, pb.person_name,po.order_no,po.pay_time,CASE WHEN sd.item =3 THEN sos.date  ELSE pii.handle_month   END date ')
		// 			->join('left join zbw_service_insurance_detail sid ON sid.id = sd.detail_id')
		// 			->join('left join zbw_service_order_salary sos ON sos.id = sd.detail_id')
		// 			->join('left join zbw_person_insurance_info pii ON pii.id =  sid.insurance_info_id')
		// 			->join('zbw_pay_order po ON (sd.item=3 AND po.id=sos.pay_order_id) OR (po.id=sid.pay_order_id) ')
		// 			->join('zbw_company_info ci ON (sd.item=3 AND ci.user_id=sos.user_id) OR (ci.user_id=pii.user_id)')
		// 			->join('zbw_person_base pb ON (sd.item=3 AND pb.id=sos.base_id) OR (pb.id=pii.base_id)')
							
		// 			->where($where)->page($page, 20)->select();
		 $where .=  ' AND  pii.user_id in('.$subUserId.')';
		$count = $this->alias('sd')
						->join('left join zbw_service_insurance_detail sid ON sid.id = sd.detail_id')
						->join('left join zbw_person_insurance_info pii ON pii.id = sid.insurance_info_id')
						->join('zbw_pay_order po ON po.id=sid.pay_order_id ')
						->join('zbw_company_info ci ON ci.user_id=pii.user_id')
						->join('zbw_person_base pb ON pb.id=pii.base_id')	
						->where($where)->count();

		$result = $this->alias('sd')->field('sd.*,ci.company_name,ci.id company_id, pb.person_name,po.order_no,po.pay_time, pii.handle_month, pii.user_id,pii.base_id')
					->join('left join zbw_service_insurance_detail sid ON sid.id = sd.detail_id')
					->join('left join zbw_person_insurance_info pii ON pii.id =  sid.insurance_info_id')
					->join('zbw_pay_order po ON po.id=sid.pay_order_id')
					->join('zbw_company_info ci ON ci.user_id=pii.user_id')
					->join('zbw_person_base pb ON  pb.id=pii.base_id')
					->where($where)->page($page, 20)->select();
//echo $this->getLastSql();
 		$pageshow = showpage($count,20);
//		dump($result);
		return array('page'=>$pageshow,'result'=>$result);
	}

	public function detail($data, $admin){
		//
		// if($data['item'] == 3){
		// 	$result  = $this->alias('sd')->field('sd.*')->join('left join zbw_service_order_salary sos ON sos.id = sd.detail_id')
		// 				->join('zbw_company_info ci ON  ci.user_id=sos.user_id')
		// 				->join('zbw_person_base pb ON pb.id=sos.base_id')
		// 				->where(array('sd.id='.$data['id']))->find();			

		// }else{
//dump($data);
		$result = $this->alias('sd')->field('sd.*,ci.company_name, pb.person_name,po.order_no,po.pay_time, pii.handle_month,sid.amount soc_amount,sid.pay_date')
					->join('left join zbw_service_insurance_detail sid ON sid.id = sd.detail_id')
					->join('left join zbw_person_insurance_info pii ON pii.id =  sid.insurance_info_id')
					->join('left join zbw_pay_order po ON po.id=sid.pay_order_id')
					->join('zbw_company_info ci ON ci.user_id=pii.user_id')
					->join('zbw_person_base pb ON  pb.id=pii.base_id')
					->where(array('sd.id='.$data['id'], 'sd.type'=> $data['type'],  'sd.item'=> $data['item'], 'po.company_id'=> $admin['company_id']))->find();			

		//}
		if($result && $result['current']){
			$result['diff_info'] = json_decode($result['diff_info'], true);
			$result['current'] = json_decode($result['current'], true);
		}else{
			return false;
		}	
		//dump($result);
		return $result;

	}



    public function adminLog($admin_id,$detail)
    {
        $log = M('ServiceAdminLog');
        $log->add(array('admin_id'=>$admin_id,'create_time'=>date('Y-m-d H:i:s',time()),'detail'=>$detail));
    }
}