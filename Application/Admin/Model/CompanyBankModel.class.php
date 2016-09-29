<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: rohochan <rohochan@gmail.com> <http://blog.rohochan.com>
// +----------------------------------------------------------------------
namespace Admin\Model;
use Think\Model\RelationModel;

/**
 * 企业用户模型
 */
class CompanyBankModel extends RelationModel{
	protected $tablePrefix = 'zbw_';
	
	/**
	 * saveCompanyBank function
	 * 保存企业银行信息
	 * param array $data 数据
	 * @return mixed
	 * @author rohochan
	 **/
	public function saveCompanyBank($data){
		if (is_array($data) and $data['company_id'] > 0) {
	        $companyBankResult = $this->field(true)->where(array('company_id'=>$data['company_id']))->find();
	        if ($companyBankResult) {
	        	$saveResult = $this->where(array('company_id'=>$data['company_id']))->save($data);
	        	if (false !== $saveResult) {
	        		$result = $companyBankResult['id'];
	        	}else {
	        		$this->error = '保存失败!';
	        		$result = false;
	        	}
	        }else {
	        	$result = $this->add($data);
	        	if (false === $result) {
					wlog($this->getDbError());
					$this->error = '系统内部错误！';
				}
	        }
	        return $result;
		}else {
			$this->error = '非法参数!';
			return false;
		}
    }
}
