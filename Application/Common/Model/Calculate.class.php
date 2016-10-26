<?php
namespace Common\Model;
/**
 * 调用实例
 * $rule = 查询数据库结果
 * 社保实例
 * $json = json_encode(array('amount'=>100.00,'month'=>3));
 * $SocInsure = new Calculate();
 * $json = $SocInsure->detail($rule , $json , 1);
 * 公积金实例
 * $json = json_encode(array('amount'=>2000.00,'month'=>3 , 'personScale'=>'5%' , 'companyScale'=>'5%' 'cardno'=>''));
 * $SocInsure = new Calculate();
 * $json = $SocInsure->detail($rule , $json , 2);
 */
class Calculate
{
    private $_fixed = '';//规则分类修饰字串
    private $_rule = array ();//社保模板规则
    private $_person = array ();//缴纳的参数
    private $_msg = array (
                0     =>'操作成功',
                100001=>'参数错误',
                200001=>'基数范围有误',
                200002=>'数据有误',
                200003=>'基数错误',
                200004=>'月数错误',
                200005=>'缴费比例有误',
                999999=>'系统错误,请稍后重试'
            );//返回的数据 
    private $_detail = array ();//计算出的数据
    /**
     * 构造类校验
     * @param [mixed] $rule   [社保模板规则]
     * @param [mixed] $person [用户缴纳参数]
     * @param [int]   $type   [类型 1社保 2公积金 3残障金 4工资 5其他收费]
     */
    public function __construct()
    {
    }
    /**
     * 返回数据
     * @param  [type] $state [description]
     * @return [type]        [description]
     */
    private function _response ($state)
    {
        return json_encode(array('state' => $state , 'msg' => $this->_msg[$state] , 'data' => $this->_detail) , JSON_UNESCAPED_UNICODE);
    }
    /**
     * 社保校验
     * @return [type] [description]
     */
    private function _verifysoc ()
    {
        if (!$this->_person['amount']) return 200003;
        if (!intval($this->_person['month'])) return 200004;
        //if ($this->_person['amount'] > $this->_rule['max'] || $this->_person['amount'] < $this->_rule['min']) return 200001;
    }
    /**
     * 个税校验
     * @return [type] [description]
     */
    private function _verifyslay ()
    {
        
    }
    /**
     * 残障金校验
     * @return [type] [description]
     */
    private function _verifydis ()
    {
        
    }
    /**
     * 残障金校验
     * @return [type] [description]
     */
    private function _verifyothers ()
    {
        
    }
    /**
     * 公积金校验
     * @return [type] [description]
     */
    private function _verifypro ()
    {
        //if ($this->_person['amount'] > $this->_rule['max'] || $this->_person['amount'] < $this->_rule['min']) return 200001;
        $verify = $this->_verifyProSale($this->_rule['company'] , $this->_person['companyScale']);
        if($verify) return $verify;
        $verify = $this->_verifyProSale($this->_rule['person'] , $this->_person['personScale']);
        if($verify) return $verify;
    }
    /**
     * [_verifyProSale description]
     * @param  [type] $array [description]
     * @param  [type] $scale [description]
     * @return [type]        [description]
     */
    private function _verifyProSale ($str , $scale)
    {
        $str = str_replace('%' , '' , $str);
        $scale = str_replace('%' , '' , $scale);
        if (false !== strpos($str , ','))
        {
            $array = explode(',' , $str);
            if (!in_array($scale , $array))
            {
                return 200005;
            }
        }
        else if (false !== strpos($str , '-'))
        {
            $array = explode('-' , $str);
            if ($scale > max($array) || $scale < min($array))
            return 200005;
        }
        else
        {
            if ($str != $scale) return 200005;
        }
    }
    /**
     * 校验工资
     * @return [type] [description]
     */
    private function _verifysaly () {}
    /**
     * 计算费用
     * @return [type] [description]
     */
    public function detail ($rule , $person , $type , $disable = '' , $replenish = 0)
    {
        if (!is_array($rule))   $rule = json_decode($rule , true);
        if (!is_array($person)) $person = json_decode($person , true);
        if (!is_array($disable)) $disable = json_decode($disable , true);
        $this->_rule = $rule;
        $this->_person = $person;
        $this->_disable = $disable;
        $this->_replenish = $replenish ? 1 : 0;
        switch ($type)
        {
            case 1:
                $this->_fixed = 'soc';
            break;
            case 2:
                 $this->_fixed = 'pro';
            break;
            case 3:
                 $this->_fixed = 'dis';
            break;
            case 4:
                $this->_fixed = 'saly';
            break;
            case 5:
                $this->_fixed = 'others';
            break;
            default:;
        }
        $vaction = '_verify'.$this->_fixed;
        $result = array();
        $result = $this->$vaction();
        if ($result) return $this->_response($result);
        $action = __FUNCTION__.$this->_fixed;
    	$this->_detail = array();
        return $this->$action();
    }
    /**
     * 社保费用计算
     * @return json
     */
    private function detailsoc ()
    {
        //print_r($this->_rule);
        foreach ($this->_rule['items'] as $k=>$v)
        {
            if (($this->_replenish == 1 && $this->_replenish == $v['rules']['replenish']) || !$this->_replenish) 
            {
                $p_exp   = explode('+' , $v['rules']['person']);
                $c_exp   = explode('+' , $v['rules']['company']);
                $p_scale = $p_exp[0];
                $c_scale = $c_exp[0];
                $p_value = $p_exp[1]*intval($this->_person['month']);
                $c_value = $c_exp[1]*intval($this->_person['month']);
                $cardinality = $v['rules']['amountmax'] ? min(max($this->_person['amount'] , $v['rules']['amount']) , $v['rules']['amountmax']) : max($this->_person['amount'] , $v['rules']['amount']);
                $ps_sum  = round($p_scale*$cardinality/100*intval($this->_person['month']) , 2);
                $cs_sum  = round($c_scale*$cardinality/100*intval($this->_person['month']), 2);
                $this->_detail['items'][$k] = array(
                    'name'   => $v['name'],
                    'type'   => 1,
                    'amount' => $cardinality,
                    'person' => array('scale'=>$p_scale , 'scaleSum'=>$ps_sum,'fixedSum'=>$p_value ,'sum'=>($ps_sum+$p_value)),
                    'company'=> array('scale'=>$c_scale , 'scaleSum'=>$cs_sum,'fixedSum'=>$c_value ,'sum'=>($cs_sum+$c_value))
                );
                $this->_detail['company'] += $this->_detail['items'][$k]['company']['sum'];
                $this->_detail['person']  += $this->_detail['items'][$k]['person']['sum'];
                $this->_detail['items'][$k]['total'] = $this->_detail['items'][$k]['person']['sum'] + $this->_detail['items'][$k]['company']['sum'];
            }  
        }
        sort($this->_detail['items']);
        //其他收费
        foreach ($this->_rule['other'] as $v)
        {
            $this->_detail['items'][] = $this->_otherData($v);
            $this->_detail['company'] += $v['rules']['company'];
            $this->_detail['person']  += $v['rules']['person'];
        }
        //残障金
        if (1 == $this->_disable['follow'])
        {
            $this->_detail['items'][] = $this->_disableData();
            $this->_detail['company'] += $this->_disable['disabled'];
        }
        $this->_detail['pro_cost'] = $this->_postCost();
        // foreach ($this->_rule['disabled'] as $v)
        // {
        //     $this->_detail['items'][] = $this->_disableData($v);
        //     $this->_detail['company'] += $v['disabled'];
            
        // }
        // $this->_detail['pro_cost'] = $this->_postCost();
        //$this->_detail['person'] += $this->_detail['pro_cost'];
        return $this->_response(0);
    }
    /**
     * [计算公积金]
     * @return [type] [description]
     */
    private function detailpro ()
    {
        $intval = $this->_rule['intval'] ? 0 : 2;
        $person = array();
        $company = array();
        $person['scale'] = rtrim($this->_person['personScale'],'%') . '%';
        $person['sum'] = round($this->_person['personScale']*$this->_person['amount']/100*intval($this->_person['month']) , $intval);
        $person['scaleSum'] = $person['sum'];
        $person['fixedSum'] = 0;
        $company['sum'] = round($this->_person['companyScale']*$this->_person['amount']/100*intval($this->_person['month']) , $intval);
        $company['scale'] = rtrim($this->_person['companyScale'],'%') . '%';
        $company['scaleSum'] = $company['sum'];
        $company['fixedSum'] = 0;

        $this->_detail['items'][] = array('name'=>'公积金' , 'amount'=>$this->_person['amount'] , 'type'=>1 ,  'person'=>$person , 'company'=>$company , 'total'=>$company['sum']+ $person['sum']);
        
        $this->_detail['company'] = $company['sum'];
        $this->_detail['person']  = $person['sum'] + $this->_detail['pro_cost'];
        //其他收费
        foreach ($this->_rule['other'] as $v)
        {
            $this->_detail['items'][] = $this->_otherData($v);
            $this->_detail['company'] += $v['rules']['company'];
            $this->_detail['person']  += $v['rules']['person'];
        }

        //残障金
        if (2 == $this->_disable['follow'])
        {
            $this->_detail['items'][] = $this->_disableData();
            $this->_detail['company'] += $this->_disable['disabled'];
        }
        // foreach ($this->_rule['disabled'] as $v)
        // {
        //     $this->_detail['items'][] = $this->_disableData($v);
        //     $this->_detail['company'] += $v['disabled'];
            
        // }
        $this->_detail['pro_cost' ] = $this->_postCost();
        //$this->_detail['person'] += $this->_detail['pro_cost'];
        return $this->_response(0);
    }
    private function _postCost()
    {
        return $this->_person['cardno'] ? 0 : $this->_rule['pro_cost'];
    }
    /**
     * 残障金结构数据
     * @param  [array] $data [残障金规则数组]
     * @return [array]       [残障金返回结构]
     */
    private function _disableData()
    {
         return array('name'=>'残障金' , 'type'=>2 , 'amount'=>0 , 'person'=>array('scale'=>'0%' , 'scaleSum'=>0 , 'scaleSum'=>0 , 'fixedSum'=>0 , 'sum'=>0) , 'company'=>array('scale'=>'0%' , 'scaleSum'=>0 , 'scaleSum'=>0 , 'fixedSum'=>0 , 'sum'=>$this->_disable['disabled']) , 'total'=>$this->_disable['disabled']);
    }
    /**
     * 其他收费结构数据
     * @param  [array] $data [其他收费规则数组]
     * @return [array]       [其他收费返回结构]
     */
    private function _otherData($data)
    {
        return array('name'=>$data['name'] , 'type'=>3 , 'amount'=>0 , 'person'=>array('scale'=>'0%' , 'scaleSum'=>0 , 'scaleSum'=>0 , 'fixedSum'=>0 , 'sum'=>$data['rules']['person']) , 'company'=>array('scale'=>'0%' , 'scaleSum'=>0 , 'scaleSum'=>0 , 'fixedSum'=>0 , 'sum'=>$data['rules']['company']) , 'total'=>$data['rules']['company'] + $data['rules']['person']);
    }
    /**
     * [计算残障金]
     * @return [type] [description]
     */
    private function detaildis ()
    {
        $sum = $this->_rule['price'] ? $this->_rule['price'] : 0;
        $this->_detail = array('sum'=>$this->_rule['price']*$this->_person['month']);
        return $this->_response(0);
    }
    /**
     * [计算工资]
     * @return [type] [description]
     */
    private function detailsaly ()
    {
        $start = 3500;
        $amount = $this->_person['amount'];
        $month  = $this->_person['month'];
        $beyond = $amount - $start;
        $tax = 0.00;
        if ($beyond < 0) return $tax;
        $beyond = abs($beyond);
        if ($beyond > 0 && $beyond <= 1500)
        {
            $tax = round($beyond*3/100*$month , 2);
        }
        else if ($beyond > 1500 && $beyond <= 4500)
        {
            $tax = round(($beyond*10/100-105)*$month , 2);
        }
        else if ($beyond > 4500 && $beyond <= 9000)
        {
            $tax = round(($beyond*20/100-555)*$month , 2);
        }
        else if ($beyond > 9000  && $beyond <= 35000)
        {
            $tax = round(($beyond*25/100-1005)*$month , 2);
        }
        else if ($beyond > 35000 && $beyond <= 55000)
        {
            $tax = round(($beyond*30/100-2755)*$month , 2);
        }
        else if ($beyond > 55000 && $beyond <= 80000)
        {
            $tax = round(($beyond*35/100-5505)*$month , 2);
        }
        else
        {
            $tax = round(($beyond*45/100-13505)*$month , 2);
        }
        $this->_detail['tax'] = $tax;
        $this->_detail['actual'] = $amount-$tax;
        return $this->_response(0);
    }
    /**
     * [计算其他费用]
     * @return [type] [json]
     */
    private function detailothers ()
    {
        $total = 0.00;
        foreach ($this->_rule as $k=>$v)
        {
            $total += $v['price'];
        }
        $this->_detail['actual'] = $total*$this->_person['month'];
        return $this->_response(0);
    }
}
?>