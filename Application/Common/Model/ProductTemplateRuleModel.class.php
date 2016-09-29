<?php
namespace Common\Model;
use Think\Model;
class ProductTemplateRuleModel extends Model
{
	protected $tablePrefix = 'zbw_';
	
    public function getRule($map,$is_null = false)
    {   
        $result =  $this->where($map)->find();
        if($is_null) return $result;
        if($result){
            $data['rule'] = $result['rule'];
            $data['classify_mixed'] = $result['classify_mixed'];
            return $data;
        }
        unset($map['classify_mixed']);
        $result = $this->where($map)->find();
        $data['rule'] = $result['rule'];
        $data['classify_mixed'] = $result['classify_mixed'];
        return $data;
    }
    /**
     * 修改规则后同步数据
     * @param  [mixed] $orgrule [原始的规则]
     * @param  [mixed] $newrule [修改的规则]
     * @param  [array] $modify  [修改的方式] $modify = array('warranty'=>1,'rules'=>array());
     * @return [boolean]
     */
    public function ruleSync ($orgrule , $newrule , $modify)
    {

        if (!is_array($orgrule)) $orgrule = json_decode($orgrule , true);
        if (!is_array($newrule)) $newrule = json_decode($newrule , true);
        if (empty($orgrule) || empty($newrule) || empty($modify)) return false;
        unset($orgrule['material']);
        unset($newrule['material']);
        $diff = array_diff_assoc($newrule,$orgrule);
        $rid = implode(',' , $modify['rules']);
        $where = "service_order_id IN (SELECT id FROM zbw_service_order WHERE state=0 AND bill_make_date<>'') AND rule_id IN ({$rid}) AND type IN(1,3)";
        if (array_key_exists('min' , $diff) || array_key_exists('max', $diff))
        {
            $this->_amountMsg($rid);
            $updWhere = '';
            $m = M('service_order_detail');
            if ($newrule['min'] > $orgrule['min'])
            {
                $updWhere = $where . " AND amount < {$newrule['min']}";
                $this->_amountMsg ($updWhere);
                $m->execute("UPDATE zbw_service_order_detail SET amount = {$newrule['min']} WHERE id IN (SELECT id FROM (SELECT * FROM zbw_service_order_detail WHERE {$updWhere} ORDER BY id) od GROUP BY base_id)");
            }
            $updWhere = '';
            if ($newrule['max'] < $orgrule['max'])
            {
                $updWhere = $where . " AND amount > {$newrule['max']}";
                $m->execute("UPDATE zbw_service_order_detail SET amount = {$newrule['max']} WHERE id IN (SELECT id FROM (SELECT * FROM zbw_service_order_detail WHERE {$updWhere}) od GROUP BY base_id)");
            }
        }
        return true;
    }
    private function _amountMsg ($rules)
    {
        if (empty($rules)) return false;
        if (strpos($rules , ',') !== false && is_array($rules)) $rules = implode(',' , $rules);
        $title = '参保基数调整';
        $msg = "尊敬的#公司#：<p>您参保的城市#参保地#，社保局政策已调整，请通过以下链接查看社保局政策。</p>";
        $msg .= "#参保地#\n#跳转链接#";
        $m = M('product_template_rule');
        $location = $m->query("SELECT type,url,location FROM zbw_location_demand WHERE location=(SELECT location FROM zbw_product_template WHERE id=(SELECT template_id FROM zbw_product_template_rule WHERE id IN ({$rules}) LIMIT 1)) LIMIT 1");
        $m = M('service_order_detail');
        $company = $m->query("SELECT id,company_name FROM zbw_company_info WHERE company_id IN (SELECT company_id FROM zbw_product_order WHERE id IN (SELECT product_order_id FROM zbw_service_order WHERE id IN (SELECT service_order_id FROM zbw_service_order_detail WHERE location={$location[0]['location']} AND rule_id IN ({$rules}) AND type IN (1,3) AND payment_type IN (1,2)) AND state=0 AND bill_make_date='') GROUP BY company_id)");
        $msg = array();
        $zoning = getZoning();
        foreach ($company as $v)
        {
            $insert = array();
            $insert['company_id'] = $v['company_id'];
            $insert['title']  = $title;
            $insert['detail'] = str_replace('#公司#' , $v['company_name'] , str_replace('#参保地#' , $zoning , str_replace('#跳转链接#' , $location[0]['url'])));
            $insert['create_time'] = date('Y-m-d H:i:s');
        }
        $m = M('company_msg');
        $m->addAll($insert);
    }
    /**
     * 修改模板向企业发消息
     * @param  [mixed] $rules [规则id]
     * @return [boolean]      [执行结果]
     */
    public function modRuleMsg ($rules)
    {

        if (empty($rules)) return false;
        if (strpos($rules , ',') !== false && is_array($rules)) $rules = implode(',' , $rules);
        $title = '参保基数调整';
        $msg = "尊敬的#公司#：\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;您参保的城市#参保地#，月份:#月份#社保已调整，请通过以下链接进行查看。\n";
        $msg .= "#社保局政策链接#";
    }
    /**
     * 查找在保及报增人
     * @param  [mixed] $tpid [description]
     * @return [type]       [description]
     */
    private function _warranty ($rid)
    {

    }
    public function rules ()
    {

    }
}
?>