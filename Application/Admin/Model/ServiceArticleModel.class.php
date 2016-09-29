<?php

namespace Admin\Model;
use Think\Model;

/**
 * 行为模型
 * @author huajie <banhuajie@163.com>
 */

class ServiceArticleModel extends Model {
    protected $tablePrefix = 'zbw_';
    /**
     * [getArticleBycategory description]
     * @param  [type] $where    [description]
     * @param  string $pageSize [description]
     * @return [type]           [description]
     */
    public function getArticleBycategory($where,$pageSize='20'){
        $where['category_id']=array('neq',0);
        $pageCount=$this->where($where)->count('id');
        $page=new \Think\Page($pageCount,$pageSize);
        $result=$this->field('title,id,company_id,create_time,update_time,category_id,status')
                    ->where($where)
                    ->limit($page->firstRow,$page->listRows)
                    ->select();
        return array('data'=>$result,'page'=>$page->show());
    }
    /**
     * [getArticleinfo 通过id获取文章详情]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getArticleinfo($id){
        return $this->where('id='.$id)->find();
    }
    /**
     * [getArticlelistByWhere description]通过条件数组获取文章
     * @param  [type] $where    [description]
     * @param  string $pageSize [description]
     * @return [type]           [description]
     */
    public function getArticlelistByWhere($where,$pageSize='25'){
        $where['category_id']=array('neq',0);
        $pageCount=$this->where($where)->count('id');
        $page= new \Think\Page($pageCount,$pageSize);
        $result=$this->field('title,id,company_id,create_time,update_time,category_id,status')
                    ->where($where)
                    ->limit($page->firstRow,$page->listRows)
                    ->select();
        return array('data'=>$result,'page'=>$page->show());
    }
}
 ?>