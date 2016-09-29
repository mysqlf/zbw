<?php
namespace Common\Model;
use Think\Model;
use Common\Model\Calculate;
class DiffCronModel extends Model
{
    //protected $autoCheckFields = false;
    protected $trueTableName = 'zbw_diff_cron';
    public $_sign = '';
    public $_unsign = '';
    public $_type = 1;//1办理 2规则调整 3缴费异常 4工本费
    public $_item = 1;//1社保 2公积金
    public $_messageBody = array();
    public $_rule_id = array();
    public $_start   = '';

    //计划任务表入口
    public function diffCron ()
    {
        $this->_refactor($this->_sign);
        $this->_refactor($this->_unsign);
        $this->_unsign = $this->_unsign['detail_id'];
        $this->_dispose();
    }
    private function _validata()
    {
        if (!$this->_type || !$this->_item) die('参数错误');
        if ($this->_type == 2 && (!$this->_rule_id || !intval($this->_start))) die('参数错误');
        if ($this->_type == 3 && (!$this->_messageBody || !$this->_sign['detail_id'])) die('参数错误');
    }
    private function _dispose()
    {
        $this->_validata();
        switch ($this->_type)
        {
            case 1:
                $this->_delete();
            break;
            case 2:
                $this->_sign = M('service_insurance_detail' , 'zbw_')->field('GROUP_CONCAT(id) id')->where("rule_id={$this->_rule_id} AND pay_date >= {$this->_start}")->select();
                if(!empty($this->_sign)) $this->_sign['detail_id'] = $this->_sign[0]['id'];
            break;
            case 3:
            break;
            case 4:
                $this->_delete();
            break;
            default:;
        }
        $this->_insert();
        
    }

    private function _refactor (&$data)
    {
        if ($data['insurance_info_id']&&!$data['detail_id'])
        {
            if (is_array($data['insurance_info_id'])) $data['insurance_info_id'] = implode(',', $data['insurance_info_id']);
            $data['detail_id'] = M('service_insurance_detail' , 'zbw_')->where("insurance_info_id IN ({$data['insurance_info_id']})")->getField('GROUP_CONCAT(id) id');
            unset($data['insurance_info_id']);
        }
    }

    private function _delete ()
    {
    	if ($this->_unsign) {
        	$this->where("detail_id IN ($this->_unsign) AND item={$this->_item} AND type={$this->_type}")->delete();
    	}
    }
    private function _insert ()
    {
    	if (!$this->_sign || !$this->_sign['detail_id']) {
    		return false;
    	}
        $data = explode(',' , $this->_sign['detail_id']);
        $insert = array ();
        foreach ($data as $v)
        {
            array_push($insert , array(
                'detail_id' => $v,
                'type'      => $this->_type,
                'item'      => $this->_item,
                'message_body' => json_encode($this->_messageBody , JSON_UNESCAPED_UNICODE),
                'modify_time' => date('Y-m-d H:i:s')
            ));
        }
        $this->addAll($insert , '' , true);
    }
}
?>