<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: rohochan <rohochan@gmail.com> <http://blog.rohochan.com>
// +----------------------------------------------------------------------
namespace Company\Model;
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
}
