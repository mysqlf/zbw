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
                //$action = '_papersCost';
            break;
            default:;
        }
        $this->$action();
        $this->_delete();
    }
    private function _delete ()
    {
        M('diff_cron' , 'zbw_')->where("detail_id={$this->_detail_id} AND item={$this->_item} AND type={$this->_type}")->delete();
    }
    //差额数据入库
    private function _insertDiff ()
    {
        $insert = array();
        $insert['detail_id']   = $this->_detail_id;
        $insert['amount']      = $this->_amount;
        $insert['item']        = $this->_item;
        $insert['type']        = $this->_type;
        //$insert['original']    = $this->_original;
        $insert['current']     = $this->_current;
        $insert['create_time'] = date('Y-m-d H:i:s');
        $this->add($insert);
        return true;
    }

    //缴费规则修改
    private function _ruleChange()
    {
        $this->_detail();
        if(empty($this->_detail['rule_id'])) return false;
        if(-3 == $this->_detail['state'])
        {
            $this->_delete();
            return false;
        }
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
        $oldDetail = json_decode($this->_detail['current_detail'] , true);
        $new = $newDetail['items'];
        $old = $oldDetail['items'];
        //var_dump(is_array($new) , is_array($old));
        $newCnt = count($new);
        $oldCnt = count($old);
        $max = max($newCnt , $oldCnt);
        $item = array ();
        $initial =array('name' => '' , 'company'=>array('sum'=>0) , 'person'=>array('sum'=>0),'total'=>0);
        $current = array();
        $current['total'] = $current['company'] = $current['person'] = 0;
        $cdetail = $current;
        $this->_amount = 0.00;
        for ($i=0;$i<$max;$i++)
        {
            $oldItem = $old[$i];
            $newItem = $new[$i];
            if ($oldItem['name'] == $newItem['name']) 
            {
                
            }
            else if ($newCnt < $oldCnt)
            {
                $new[$i+1] = $newItem;
                $new[$i] = $initial;
                $new[$i]['name'] = $oldItem['name'];
            }
            else if ($oldCnt < $newCnt)
            {
                $old[$i+1] = $oldItem;
                $old[$i] = $initial;
                $old[$i]['name'] = $newItem['name'];
            }
            $this->_arrayDiffAssocCall($newItem , $oldItem , $current , $cdetail);
        }
        $current['total'] = $current['company'] + $current['person'];
        $cdetail['total'] = $cdetail['company'] + $cdetail['person'];
        $this->_amount = $current['total'];
        if(!$this->_amount) return false;
        //dump($current);
        //dump($cdetail);
        $this->_current = json_encode($current['items'] , JSON_UNESCAPED_UNICODE);
        $this->_modiDetail(array('current_detail' => json_encode($cdetail , JSON_UNESCAPED_UNICODE),'diff_cue'=>1));
        $this->_insertDiff();
    }
    public function _arrayDiffAssocCall ($new , $old , &$current , &$cdetail)
    {
        $diff = array();
        $type = array('person' , 'company');
        foreach ($type as $v)
        {
            //if (($new['amount'] != $old['amount'] || rtrim($new[$v]['scaleSum'],'%') != rtrim($old[$v]['scaleSum'],'%') || $new[$v]['fixedSum'] != $old[$v]['fixedSum']) && ($new['total'] != $old['total']))
            if ($new['total'] != $old['total'])
            {
                if (!$old[$v]['payAnomaly'])
                {
                    $diff['name'] = $new['name'].'修改';
                    $diff['amount'] = $new['amount'];
                    $diff[$v]     = $new[$v];

                    $diff[$v]['sum'] = $new[$v]['sum'] - $old[$v]['sum'];
                    $diff['total']  += $diff[$v]['sum'];
                }
                else
                {
                    $diff['name'] = $new['name'];
                    $diff['amount'] = $new['amount'];
                    $diff[$v] = $old[$v];
                    $diff[$v]['sum'] = 0;
                    $diff['total'] += 0;

                    $new[$v] = $diff;
                }
            }
            else
            {
                continue;
            }
            $current[$v] += $diff[$v]['sum'];
            $cdetail[$v] += $new[$v]['sum'];
        }
        $cdetail['items'][] = $new;
        if (!empty($diff)) $current['items'][] = $diff;
        
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
            $array['amount'] = $this->_detail['amount_now'];
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
        $nowPrice = $this->_sumDetailPrice(json_decode($this->_detail['current_detail'] , ture)['total']);
        $newPrice = $newPrice ? $newPrice : $this->_detail['price'];
        $this->_amount = -abs($this->_detail['service_price'] + $newPrice);
        //$this->_amount = -abs(($this->_detail['price'] + $this->_detail['service_price']));
        $this->_current = json_encode($current , JSON_UNESCAPED_UNICODE);
        $this->_modiDetail(array('current_detail' => json_encode($currentDetail , JSON_UNESCAPED_UNICODE),'diff_cue'=>1));
        $this->_insertDiff();
        //dump($this->_current);
    }
    private function _sumDetailPrice ($detail)
    {
        $price = 0;
        foreach ($detail as $v)
        {
            $price += $v['sum'];
        }
        return $price;
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
                    'amount' => $this->_detail['amount_now'],
                    'company' => array( 'scale'=>'0%','scaleSum' => 0,'fixedSum' => 0,'sum' => 0),
                    'person' => array( 'scale'=>'0%','scaleSum' => 0,'fixedSum' => 0,'sum' => 0),
                    'total' => 0
                );
                foreach ($type as $val)
                {
                    //记录异常明细
                    if (false === $v[$val])
                    {
                        $this->_amount += -abs($json['items'][$k][$val]['sum']);
                        $json['items'][$k]['total'] += $this->_amount;
                        $new[$val] = array(
                            'scale'    => $json['items'][$k][$val]['scale'],//'0%',
                            'scaleSum' => $json['items'][$k][$val]['scaleSum'],//0,
                            'fixedSum' => $json['items'][$k][$val]['fixedSum'],//0,
                            'sum'      => -abs($json['items'][$k][$val]['sum']),
                        );
                        $json['items'][$k][$val] = array(
                            'scale'    => '0%',
                            'scaleSum' => 0,
                            'fixedSum' => 0,
                            'sum'      => 0,
                            'payAnomaly' => true
                        );
                    }
                    else
                    {
                        $new[$val] = array(
                            'scale'    => '0%',
                            'scaleSum' => 0,
                            'fixedSum' => 0,
                            'sum'      => 0,
                        );
                    }
                    $new['total'] += $new[$val]['sum'];
                }
                array_push($current , $new);
            } 
            else
            {
                continue;
            }
        }
        if(empty($current)) return false;      
        $this->_amount   = -abs($this->_amount);
        $this->_current  = json_encode($current,JSON_UNESCAPED_UNICODE);
        //dump(array($this->_detail['detail_id']=>$current));
        //dump($json);
        $this->_modiDetail(array('current_detail'=>json_encode($json,JSON_UNESCAPED_UNICODE),'diff_cue'=>1));
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
            $this->_detail = $m->alias('sid')->field('sid.*,pii.amount amount_now,pii.payment_info payment_info_now,sid.state state')->join("LEFT JOIN zbw_person_insurance_info pii ON pii.id=sid.insurance_info_id")->where("sid.id={$this->_detail_id}")->find();
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