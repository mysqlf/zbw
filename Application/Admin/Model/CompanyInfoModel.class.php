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
class CompanyInfoModel extends RelationModel{
	protected $tablePrefix = 'zbw_';
	public function getComIdByComName($where){
        $result=$this->where($where)->getField('id',true);
        return $result;
    }
    /**
     * [getLocationByUserid 获取公司所在地(location)]
     * @param  [int] $userid [用户id]
     * @return [int] 
     */
    public function getLocationByUserid($userid){
        if ($userid) {
            $location=$this->where('user_id='.$userid)->getField('location');
            return $location;
        }else{
            $this->error="参数错误";
            return false;
        }
    }
    /**
     * [getname description]获取发布人的名字
     * @param  [type] $companyid [description]
     * @return [type]            [description]
     */
    public function getname($companyid){
        return $this->where(array('id'=>$companyid))->getField('contact_name');
    }
    /**
     * [getcompanyname description]获取企业名字
     * @param  [type] $companyid [description]
     * @return [type]            [description]
     */
    public function getcompanyname($companyid){
        return $this->where(array('id'=>$companyid))->getField('company_name');
    }
    /**
     * [getcompanyidbyname 企业名字查询企业id]
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function getcompanyidbyname($where){
        return $this->where($where)->getField('id',true);
    }
}
