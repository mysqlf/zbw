<?php
namespace Service\Controller;
use Common\Model\DiffCronModel;
use Common\Model\Calculate;
class RulesController extends ServiceBaseController {

	private   $_Type;
	private   $_State;
	private   $_TemplateRule;
	private   $_Rules;
	
    protected function _initialize(){
    	parent::_initialize();
		$this->_Type = array(1=>'社保', 2=>'公积金', 3=>'残障金');
		$this->_State = array(1=>'启用', 0=>'暂停', -1=>'停用', -9=>'禁用');
		$this->_TemplateRule = D('TemplateRule');
		$this->_Rules = I('post.', '');
    }
	
	/**
	 * _getTemplateClassify function
	 * 根据参保地获取模板分类
	 * @access private
	 * @param int $location 城市编号
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	private function _getTemplateClassify($location){
		if ($location) {
			$template = D('Template');
			$templateResult = $template->getTemplateByCondition(array('location'=>$location,'state'=>1));
			if ($templateResult) {
				$templateClassify = D('TemplateClassify');
				$templateClassifyResult = array();
				for ($i=1; $i <= 2; $i++) { 
					$templateClassifyResult[$i] = $templateClassify->getTemplateClassifyByCondition(array('template_id'=>$templateResult['id'],'type'=>$i,'state'=>1));
					if ($templateClassifyResult[$i]) {
						$templateClassifyResult[$i] = list_to_tree($templateClassifyResult[$i],'id','fid','_child',0);
					}
				}
				return array('template_id'=>$templateResult['id'],'result'=>$templateClassifyResult);
			}else {
				return false;
			}
		}else {
			return false;
		}
	}
	
	/**
	 * getTemplateClassify function
	 * 根据参保地获取模板分类
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getTemplateClassify(){
		if (IS_POST) {
			$location = I('post.location/d');
			if ($location) {
				$result = $this->_getTemplateClassify($location);
				if ($result) {
					$this->ajaxReturn(array('status'=>0,'result'=>$result));
				}else {
					$this->ajaxReturn(array('status'=>-1,'msg'=>'该参保地不存在模板！'));
				}
			}else {
				$this->ajaxReturn(array('status'=>-1,'msg'=>'非法参数！'));
			}
		}else {
			//$this->ajaxReturn(array('status'=>-1,'msg'=>'非法操作!'));
    		$this->error('非法操作!');
		}
	}
	
	/**
	 * _getTemplateRule function
	 * 根据参保地获取模板分类
	 * @access private
	 * @param int $ruleId 规则id
	 * @param int $type 类型 1社保 2公积金
	 * @param array $templateId 模板id
	 * @param string $classifyMixed 分类组合
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	private function _getTemplateRule($ruleId = 0, $type = 1,$templateId = 0,$companyId = 0,$classifyMixed = ''){
		if ($templateId) {
			$templateRule = D('TemplateRule');
			if ($ruleId) {
				$condition = array('id'=>$ruleId,'state'=>1);
			}else {
				if (1 == $type) {
					$classifyMixed = array_filter($classifyMixed);
					rsort($classifyMixed);
					if ($classifyMixed) {
						$classifyMixed = implode('|',$classifyMixed);
						$condition = array('template_id'=>$templateId,'company_id'=>array(0,intval($companyId),array('exp','is null'),'or'),'type'=>$type,'classify_mixed'=>$classifyMixed,'state'=>1);
					}else {
						return false;
					}
				}else if (2 == $type) {
					$condition = array('template_id'=>$templateId,'company_id'=>array(0,intval($companyId),array('exp','is null'),'or'),'type'=>$type,'state'=>1);
				}else {
					return false;
				}
			}
			$templateRuleResult = $templateRule->getTemplateRuleByCondition($condition,2);
			if ($templateRuleResult) {
				foreach ($templateRuleResult as $key => $value) {
					$rule = json_decode($value['rule'],true);
					$templateRuleResult[$key]['rule'] = $rule;
					$templateRuleResult[$key]['minAmount'] = $rule['min'];
					$templateRuleResult[$key]['maxAmount'] = $rule['max'];
					$templateRuleResult[$key]['proCost'] = $rule['pro_cost'];
					!empty($rule['company']) && $templateRuleResult[$key]['companyScale'] = $rule['company'];
					!empty($rule['person']) && $templateRuleResult[$key]['personScale'] = $rule['person'];
				}
				return $templateRuleResult;
			}else {
				return false;
			}
		}else {
			return false;
		}
	}
	
	/**
	 * getTemplateRule function
	 * 根据参保地获取模板规则
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getTemplateRule(){
		if (IS_POST) {
			$ruleId = I('post.id/d');
			$type = I('post.type/d');
			$templateId = I('post.templateId/d');
			$companyId = $this->_cid?:I('post.companyId/d');
			$classifyMixed = I('post.classifyMixed');
			if (!is_array($classifyMixed)) {
				$classifyMixed = array($classifyMixed);
			}
			if ($ruleId || ($type && $templateId)) {
				$result = $this->_getTemplateRule($ruleId,$type,$templateId,$companyId,$classifyMixed);
				if ($result) {
					$this->ajaxReturn(array('status'=>0,'result'=>$result));
				}else {
					$this->ajaxReturn(array('status'=>-1,'msg'=>'该参保地不存在模板规则！'));
				}
			}else {
				$this->ajaxReturn(array('status'=>-1,'msg'=>'非法参数！'));
			}
		}else {
			//$this->ajaxReturn(array('status'=>-1,'msg'=>'非法操作!'));
    		$this->error('非法操作!');
		}
	}
	
    /**
     * 缴费规则
     */
    public function index(){
    	$where = "tr.company_id={$this->_cid} AND tr.category =2 AND type in (1,2)";
    	$result = $this->_TemplateRule->ruleList($where, $this->_AccountInfo,9);
    	$this->assign('_Type', $this->_Type)->assign('_State', $this->_State)->assign('result', $result);
        $this->display();
    }
    
    /**
     * 保存缴费规则
     */
    public function save(){
        if (IS_AJAX)
        {
            set_time_limit(0);
            $id       = intval(I('post.id' , ''));
            $cid      = session('user.company_id');
            $location = intval(I('post.location'));
            $template_id = intval(I('post.templateId'));
            $type     = intval(I('post.type'));

            if (!($type && $location && $template_id && $cid)) $this->ajaxReturn(array('status'=>-1,'msg'=>'系统错误，请稍后重试!'));                        
            $rule = array();
            $rule['min'] = I('post.minAmount');
            $rule['max'] = I('post.maxAmount');
            $rule['pro_cost'] = I('post.pro_cost');
            $name      = I('post.name');
            $items = array();
            $other = array();
            // $disabled = array();
            // $disabled['disabled'] = I('post.disabled' , 0.00);
            if (1 == $type)
            {
                $replenish = I('post.replenish');
                $amount    = I('post.amount');
                $amountmax = I('post.amountmax');
                $comScale  = I('post.comScale');
                $comFix    = I('post.comFix');
                $perScale  = I('post.perScale');
                $perFix    = I('post.perFix');
                $repCnt = count($replenish);
                for ($i=0;$i<$repCnt;$i++)
                {
                    $items[] = array('name'=>$name[$i] , 'rules'=>array('amount'=>$amount[$i] , 'amountmax'=>$amountmax[$i] , 'company'=>$comScale[$i] .'%+'. $comFix[$i] , 'person'=> $perScale[$i] . '%+' . $perFix[$i] , 'replenish' => $replenish[$i]));   
                }
                $rule['items'] = $items;
                //if ($disabled['disabled']) $disabled['follow'] = 1;

            }
            else if (2 == $type)
            {
                $rule['intval'] = intval(I('post.intval' , 0));
                $isComType = intval(I('post.isComType' , 1));
                $isPerType = intval(I('post.isPerType' , 1));
                $comFixLow = I('post.comFixLow');
                $comFixUp  = I('post.comFixUp');
                $comScale  = I('post.comScale');
                $perFixLow = I('post.perFixLow');
                $perFixUp  = I('post.perFixUp');
                $perScale  = I('post.perScale');
                $rule['company'] = $isComType == 1 ? "{$comFixLow}%-{$comFixUp}%" :  "{$comScale}%";
                $rule['person'] = $isPerType == 1 ? "{$perFixLow}%-{$perFixUp}%" :  "{$perScale}%";
                //if ($disabled['disabled']) $disabled['follow'] = 2;
            }
            $otherName = I('post.otherName/a');
            $companyOther = I('post.companyOther');
            $personOther = I('post.personOther');
            $nameCnt = is_array($otherName)?count($otherName):0;
            for ($i=0;$i<$nameCnt;$i++)
            {
                $other[] = array('name'=>$otherName[$i],'rules'=>array('company'=>$companyOther[$i] , 'person'=> $personOther[$i]));
            }
            
            $rule['other'] = count($other) > 0 ? $other : null;
            $m = M('template_rule' , 'zbw_');
            
            $pro_cost  = I('post.pro_cost');
            //$disabled  = I('post.disabled');
            $save['rule'] = array(
                    'template_id'=> $template_id,
                    'user_id' => 0,
                    'company_id' => $cid,
                    'name'    => I('post.rule_name'),
                    'category' => 2,
                    'type' => intval(I('post.type' , 1)),
                    'classify_mixed' => intval(I('post.classifyMixed')),
                    'rule' => json_encode($rule , JSON_UNESCAPED_UNICODE),
                );
            // $save['disabled'] = array(
            //         'template_id'=> $template_id,
            //         'user_id' => 0,
            //         'company_id' => $cid,
            //         'name'    => I('post.name'),
            //         'category' => 2,
            //         'type' => 3,
            //         'classify_mixed' => '',
            //         'rule' => json_encode($disabled , JSON_UNESCAPED_UNICODE),
            //     );
            if (!$id)
            {
                $m->add($save['rule']);
                //$m->add($save['disabled']);
            }
            else
            {
                $oldRule = $m->alias('tr')->field("tr.*,(SELECT rule FROM zbw_template_rule WHERE template_id=tr.template_id AND company_id=tr.company_id AND type=3) disabled")->where("id={$id}")->find();
                $ruleDiff = array_diff_assoc($rule , json_decode($oldRule['rule'] , true));
                //$disDiff  = array_diff_assoc(json_decode($oldRule['disabled'], true) , $disabled);
                if ($ruleDiff['min'])
                {
                    M('person_insurance_info' , 'zbw_')->where("rule_id={$id} AND amount<{$ruleDiff['min']}")->save(array('amount'=>$ruleDiff['min']));
                }                

                if ($ruleDiff['max'])
                {
                    M('person_insurance_info' , 'zbw_')->where("rule_id={$id} AND amount>{$ruleDiff['max']}")->save(array('amount'=>$ruleDiff['max']));
                }

                if ($ruleDiff['company'] || $ruleDiff['person'])
                {
                    $cscale = explode('-' , str_replace('%','',$ruleDiff['company']));
                    $cmin = $cscale[0] && $cscale[1] ? min($cscale[0] , $cscale[1]) : $cscale[0];
                    $cmax = $cscale[0] && $cscale[1] ? max($cscale[0] , $cscale[1]) : $cscale[0];

                    $pscale = explode('-' , str_replace('%','',$ruleDiff['person']));
                    $pmin = $pscale[0] && $pscale[1] ? min($pscale[0] , $pscale[1]) : $pscale[0];
                    $pmax = $pscale[0] && $pscale[1] ? max($pscale[0] , $pscale[1]) : $pscale[0];

                    $res = $m->query("SELECT id,insurance_id,payment_info FROM (SELECT id,insurance_id,payment_info FROM zbw_person_insurance_info WHERE rule_id={$id} AND payment_type=2 AND state IN (1,2,3) ORDER BY id DESC) o GROUP BY insurance_id,id");
                    foreach ($res as $v)
                    {
                        $payment = json_decode(str_replace('%','',$v['payment_info']) , true);
                        if ($payment['companyScale'] < $cmin) $payment['companyScale'] = rtrim($cmin , '%');
                        if ($payment['companyScale'] > $cmax) $payment['companyScale'] = rtrim($cmax , '%');

                        if ($payment['personScale'] < $pmin) $payment['personScale'] = rtrim($pmin , '%');
                        if ($payment['personScale'] > $pmax) $payment['personScale'] = rtrim($pmax , '%');
                        M('person_insurance_info' , 'zbw_')->where("id={$v['id']}")->setField('payment_info' , json_encode($payment));
                    }
                    
                    // echo "UPDATE zbw_person_insurance_info SET JSON_REPLACE(payment_info , '$.companyScale' , {$max}) WHERE rule_id={$id} AND json_extract(payment_info , '$.companySacle') > {$max}";
                    // exit;
                    // echo M('person_insurance_info' , 'zbw_')->fetchSql(true)->query("UPDATE zbw_person_insurance_info SET JSON_REPLACE(payment_info , '$.companyScale' , {$max}) WHERE rule_id={$id} AND json_extract(payment_info , '$.companySacle') > {$max}");
                    //echo M('person_insurance_info' , 'zbw_')->getLastSql();
                    //exit;
                }
                $detail = M('service_insurance_detail' , 'zbw_');
                $res = $detail->alias('pid')->field('pid.id did,pii.payment_type payment_type,pii.payment_info payment_info,pii.amount amount,pid.replenish replenish,pid.pay_order_id pay_order_id')->join("LEFT JOIN zbw_person_insurance_info pii ON pii.id=pid.insurance_info_id")->where("pid.rule_id={$id} AND pid.state IN (0,1,-1)")->order('pid.id DESC')->select();
                $Calculate = new Calculate;
                foreach ($res as $val)
                {
                    $payment_info = json_decode($val['payment_info'], true);
                    $payment_info['amount'] = $val['amount'];
                    //$payment_info = json_decode($this->_detail['payment_info'] , true);
                    //$payment_info['amount'] = $this->_detail['amount'];
                    $payment_info['month']  = 1;
                    $newDetail = $Calculate->detail($rule , $payment_info , $val['payment_type'] , null , $val['replenish']);
                    $newDetail = json_decode($newDetail , true)['data']; 
                    $newCue = array();
                    $newCue['insurance_detail'] = json_encode($newDetail);
                    $newCue['current_detail']   = json_encode($newDetail);
                    $newCue['price']            = $newDetail['total'];
                    //更新明细
                    M('service_insurance_detail' , 'zbw_')->where("id={$val['id']}")->save(array('amount'=>$val['amount'] , 'payment_info'=>$newDetail));
                    //更新订单应付金额
                    M('pay_order' , 'zbw_')->execute("UPDATE zbw_pay_order SET amount=amount+{$newCue['price']}+{$val['service_price']} WHERE id={$val['pay_order_id']}");
                }
                if (1 == intval(I('post.synchro')))
                {
                    $d = D('DiffCron');
                    $d->_type = 2;
                    $d->_item = intval(I('post.type' , 1));
                    $d->_rule_id = $id;
                    $d->_start = str_replace('/' , '' , I('post.effective' , ''));
                    $d->diffCron();
                }
                $m->where("id={$id} AND company_id={$cid}")->save($save['rule']);
                //$m->where("template_id={$template_id} AND company_id={$cid} AND type=3")->save($save['disabled']);
                
            }
        }
        $this->ajaxReturn(array('status'=>0,'msg'=>'操作成功!'));
    }
    
    /**
     * 添加缴费规则
     */
    /*public function add(){
        $this->display();
    }*/
    
    /**
     * 修改缴费规则
     */
    public function edit(){
    	if(IS_POST){
    		
    	}else{
    		$id = I('get.id', '0', 'intval');
    		//$template_id = I('get.template_id', '0', 'intval');
    		//$type = I('get.type', '0', 'intval');
    		//if(empty($id) || empty($template_id) || empty($type)) $this->error('参数错误'); 
    		
    		$data['id'] = $id;
    		//$data['template_id'] = $template_id;
    		//$data['type'] = $type;
    		$data['company_id'] = $this->_cid;
    		$result = $this->_TemplateRule->ruleInfo($data, $type, $this->_AccountInfo);
    		//dump($result);
    		$this->assign('result',$result);
     	    $this->display('add');
     	}
    }
    
    /**
     *禁用
     */
    public function status(){
    	if(IS_POST){
	    	$id = I('post.id', '0', 'intval');
	    	if(empty($id)) $this->ajaxReturn(array('status'=>-1,'info'=>'参数错误'));
	    	$result = D('TemplateRule')->status(array('id'=>$id), $this->_AccountInfo);
       }
    }

    
}
