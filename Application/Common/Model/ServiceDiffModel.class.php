<?php
namespace Common\Model;
use Think\Model;
use Common\Model\Calculate;
class ServiceDiffModel extends Model
{
    //protected $autoCheckFields = false;
    protected $trueTableName = 'zbw_service_diff';
    private $_item = 0;
    private $_type = 0;
    private $_detail = array();
    // private $_id = 0;
    private $_detail_id = 0;
    private $_current = '';
    private $_amount = 0.00;

    private $_produce = array();
    private $_cancel  = array();
    private $_rules   = array();
    public $_payData = array();
    //差额计算入库
    public function diffAmount ($item , $type , $did)
    {
        set_time_limit(0);
        $this->_item = $item;
        $this->_type = $type;
        $this->_detail_id = $did;
        $action = '';

        switch ($this->_type)
        {
            case 1:
                $action = '_manage';
            break;
            case 2:
                $action = '_ruleChange';
            break;
            case 3:
               foreach ($manage as $key=>$val)
               {
                    $this->_detail_id = $key;
                    $this->_payData = $val;
               }
               $action = '_payAnomaly';
            break;
            case 4:
                $action = '_papersCost';
            break;
            default:;
        }
        $this->$action();
        //$this->_callProc();
    }
    //差额数据入库
    private function _insertDiff ()
    {
        $insert = array();
        $insert['detail_id']   = $this->_detail_id;
        $insert['amount']      = $this->_amount;
        $insert['item']        = $this->_item;
        $insert['type']        = $this->_type;
        $insert['original']    = $this->_original;
        $insert['current']     = $this->_current;
        $insert['create_time'] = date('Y-m-d H:i:s');
        $this->add($insert , '' , true);
        return true;
    }

    //缴费规则修改
    private function _ruleChange()
    {
        $this->_detail();
        if(empty($this->_detail['rule_id'])) return false;
        //获取规则
        $m = M('template_rule' , 'zbw_');
        $rule = $m->alias('tr')->field('tr.*,(SELECT rule FROM zbw_template_rule WHERE template_id=tr.template_id AND type=3) disabled')->where("id={$this->_detail['rule_id']}")->find();
        
        $insurance_info = M('person_insurance_info' , 'zbw_')->where("id={$this->_detail['insurance_info_id']}")->find();
        $payment_info = json_decode($insurance_info['payment_info'], true);
        $payment_info['amount'] = $insurance_info['amount'];
        //$payment_info = json_decode($this->_detail['payment_info'] , true);
        //$payment_info['amount'] = $this->_detail['amount'];
        $payment_info['month']  = 1;
        //新规则明细
        $Calculate = new Calculate;
        $newDetail = $Calculate->detail($rule['rule'] , $payment_info , $this->_detail['payment_type'] , $rule['disabled'] , $this->_detail['replenish']);
        $newDetail = json_decode($newDetail , true)['data'];
        if(empty($newDetail)) return false;
        
        $original = json_decode($this->_detail['current_detail'] , true);
        // $detail = array();
        // $detail['current_detail'] = json_encode(array_merge($original , $newDetail) , JSON_UNESCAPED_UNICODE);
        
        // //不产生差额，重新计算订单金额
        // if (in_array($this->_detail['state'] , array(1,-1)))
        // {
        //     $detail['price'] = $newDetail['total'];
        //     // $locationWarranty = M('warranty_location' , 'zbw_')->where("service_product_order_id={$v['turn_id']} AND location")->find();
        //     // $detail['service'] = $this->_detail['payment_type'] == 1 ? $locationWarranty['soc_service_price'] : $locationWarranty['pro_service_price'];
        //     $detail['insurance_detail'] = json_encode($newDetail, JSON_UNESCAPED_UNICODE);
        // }
        // //产生差额
        // else if (in_array($this->_detail['state'] , array(2,3)))
        // {
            //新旧计算规则比对
            $max = max(count($newDetail['items']) , count($original['items']));
            $current = array();
            $this->_amount = 0.00;
            //匹配计算明细
            for ($i=0 ; $i<$max ; $i++)
            {
                $oitems = $original['items'][$i];
                $nitems = $newDetail['items'][$i];
                if ($oitems['total'] != $nitems['total'] && true !== $nitems['payAnomaly'] )
                {
                    $diff = array();
                    $diff['name'] = $nitems['name'].'缴费规则修改';
                    $diff['type'] = $nitems['type'];
                    $diff['amount'] = $nitems['amount'];
                    $diff['person'] = array(
                        'scale'     => $nitems['person']['scale'],
                        'scaleSum'  => $nitems['person']['scaleSum'],
                        'fixedSum'  => $nitems['person']['fixedSum'],
                        'sum'       => $nitems['person']['sum'] - $oitems['person']['sum'],
                    );
                    $diff['company'] = array(
                        'scale'    => $nitems['company']['scale'],
                        'scaleSum' => $nitems['company']['scaleSum'],
                        'fixedSum' => $nitems['company']['fixedSum'],
                        'sum'      => $nitems['company']['sum'] - $oitems['company']['sum'],
                    );
                    
                    $this->_amount += $diff['total'] = $diff['company']['sum'] + $diff['person']['sum'];
                    //array_push($current , $diff);
                }
            }
            //if(empty($current)) return false;
            $this->_current = json_encode($current , JSON_UNESCAPED_UNICODE);
            $this->_modiDetail(array('current_detail' => json_encode(array_merge(json_decode($this->_detai['current_detail'] , true) , $newDetail) , JSON_UNESCAPED_UNICODE)));
            $this->_insertDiff();
        // }
        // $this->_modiDetail($detail);
        //
        
    }
    //办理失败
    private function _manage()
    {
        $this->_detail();
        $detail = json_decode($this->_detail['current_detail'] , true);
        $current = array();
        $currentDetail = array();
        foreach ($detail['items'] as $v)
        { 
            $array = $v;
            $array['name'] .= '办理失败';
            $array['total'] = -$array['total'];
            $array['person']['sum'] = -$array['person']['sum'];
            $array['company']['sum'] = -$array['company']['sum'];
            array_push($current , $array);

            $array = $v;
            $array['total'] = 0;
            $array['person']['sum'] = 0;
            $array['company']['sum'] = 0;
            array_push($currentDetail , $array);
        }
        array_push($current, array('name'=>'服务费','total'=>-abs($this->_detail['service_price']) ));
        $this->_amount = -abs($this->_detail['price'] + (json_decode($this->_detail['current_detail'] , ture)['total']));
        $this->_current = json_encode($current , JSON_UNESCAPED_UNICODE);
        $this->_modiDetail(array('current_detail' => json_encode($currentDetail , JSON_UNESCAPED_UNICODE)));
        $this->_insertDiff();
    }
    //工本费
    private function _papersCost()
    {
        $this->_detail();
        $pro_cost = abs(json_decode($this->_detail['insurance_detail'] , true)['pro_cost']);
        $this->_amount = $pro_cost;
        $this->_current = json_encode(array(array('name'=>'工本费' , 'total'=>$pro_cost)) , JSON_UNESCAPED_UNICODE);
        $this->_insertDiff();
    }
    //缴费异常
    private function _payAnomaly()
    {
        $this->_detail();
        $json = json_decode($this->_detail['current_detail'] , true);
        $diff_amount = 0;
        //print_r($json['items']);
        $type = array('company' , 'person');
        $current = array();
        $this->_amount = '0.00';
        foreach ($this->_payData as $k=>$v)
        {
            //匹配异常记录
            if (array_search(false, $v))
            {
                //初始化异常明细
                $new = array('name' => $v['name'].'缴费异常',
                    'type' => $json['items'][$k]['type'],
                    'amount' => $json['items'][$k]['amount'],
                    'company' => array( 'scale'=>'0%','scaleSum' => 0,'fixedSum' => 0,'sum' => 0),
                    'person' => array( 'scale'=>'0%','scaleSum' => 0,'fixedSum' => 0,'sum' => 0),
                );
                foreach ($type as $val)
                {
                    //记录异常明细
                    if (false === $v[$val])
                    {
                        $this->_amount += -abs($json['items'][$k][$val]['sum']);
                        $new[$val] = array(
                            'scale'    => $json['items'][$k][$val]['scale'],//'0%',
                            'scaleSum' => $json['items'][$k][$val]['scaleSum'],//0,
                            'fixedSum' => $json['items'][$k][$val]['fixedSum'],//0,
                            'sum'      => -abs($json['items'][$k][$val]['sum']),
                        );
                        //记录异常标记
                        $json['items'][$k][$val]['payAnomaly'] = true;
                    }
                }
                array_push($current , $new);
            }
        }
        if(empty($current)) return false;      
        $this->_amount   = -abs($this->_amount);
        $this->_current  = json_encode($current,JSON_UNESCAPED_UNICODE);
        $this->_modiDetail(array('current_detail'=>json_encode($json,JSON_UNESCAPED_UNICODE)));
        $this->_insertDiff();
    }
    //修改service_insurance_detail表数据
    private function _modiDetail ($data)
    {
        if (!$data) return false;
        $m = M('service_insurance_detail' , 'zbw_');
        $m->where("id={$this->_detail_id}")->save($data);
    } 
    //detail明细
    private function _detail()
    {
        if ($this->_item == 1 || $this->_item == 2)
        {
            $m = M('service_insurance_detail' , 'zbw_');
            $this->_detail = $m->where("id={$this->_detail_id}")->find();
        }
        else if ($this->_item == 3)
        {
            $m = M('service_order_salary' , 'zbw_');
            $this->_detail = $m->where("id={$this->_detail_id}")->find();
        }
        return ((count($this->_detail)<=0) ? false : true);
    }

    //调用存储过程
    private function _callProc()
    {
        $uid = 0;
        $sid = 0;
        if ($this->_item == 1 || $this->_item == 2)
        {
            $insurance = M('person_insurance_info' , 'zbw_')->where("id={$this->_detail['insurance_info_id']}")->find();
            $uid = $insurance['user_id'];
            if ($insurance['product_id']) {
            	$sid = M('service_product' , 'zbw_')->where("id={$insurance['product_id']}")->getField('company_id');
            }
        }
        else if ($this->_item == 3)
        {
            $uid = $this->_detail['user_id'];
            $sid = M('service_product' , 'zbw_')->where("id={$this->_detail['product_id']}")->getField('company_id');
        }
        try 
        {
        	if ($uid && $sid) {
            	$this->execute("CALL proc_diff_amount({$sid} , {$uid});");
        	}
        } 
        catch (Exception $e) 
        {
            return false;
        }
        return true;
    }
}
?>