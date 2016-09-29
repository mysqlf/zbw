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
 * 服务商产品模型
 */
class ServiceProductModel extends RelationModel{
	protected $tablePrefix = 'zbw_';
    /**
     * [getRecommendService 获取推荐服务商]
     * @param  [type] $userid []
     * @return [type]         []
     */
	public function getRecommendService(){
        $result=$this->alias('sp')
                    ->field('ci.id,ci.company_name,ci.company_introduction,ci.employee_number,ci.company_address,ci.register_fund,ci.tel_city_code,ci.tel_local_number,ci.location')
                    ->join('left join '.C('DB_PREFIX').'company_info as ci on ci.id=sp.company_id')
                    ->order('sp.create_time desc')
                    ->group('sp.company_id')
                    ->limit(2)
                    ->select();
        $result=self::_getServicePro($result);
        return $result;
    }
    /**
     * [_getServicePro 获取服务商的产品]
     * @param  [type] $result [description]
     * @return [type]         [description]
     */
    private function _getServicePro($result){
        foreach ($result as $key => $value) {
                $tmp=$this->alias('sp')
                            ->field('sp.name,sp.member_price,sp.service_price,sp.service_type,sp.service_price_state,sp.location,sp.other_location')
                            ->where('sp.company_id='.$value['id'].' and sp.state=1')
                            ->limit(3)
                            ->select();
                $locationvalue=array();
                foreach ($tmp as $k => $v) {
                    $location=json_decode($v['other_location'],true);
                    foreach ($location as $va) {
                        if (!empty($va)) {
                            $locationvalue[]=showAreaName($va);
                        }
                    }
                    $locationvalue[]=showAreaName($v['location']); 
                }
                $locationvalue=array_filter($locationvalue);
                $locationvalue=array_unique($locationvalue);
                $result[$key]['locationvalue']=$locationvalue;
                $result[$key]['sp']=$tmp;
        }
        return $result;
    }
}
