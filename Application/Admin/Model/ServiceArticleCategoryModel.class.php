<?php

namespace Admin\Model;
use Think\Model;

/**
 * 行为模型
 * @author huajie <banhuajie@163.com>
 */

class ServiceArticleCategoryModel extends Model {
    protected $tablePrefix = 'zbw_';
    /**
     * [getcatename description]获取cate名称
     * @param  [type] $cateid []
     * @return [type]         []
     */
    public function getcatename($cateid){
        return $this->where('id='.$cateid)->getField('title');
    }
}
 ?>