<?php
namespace Service\Model;
//use Think\Model;

class ArticleCategoryModel extends ServiceModel{
    protected $trueTableName = 'zbw_service_article_category';

    
    /**
     * 新增或更新
     */
    public function update($admin){
        $data = $this->create();
        if(!$data){ //数据对象创建错误
            return false;
        }

        /* 添加或更新数据 */
        if(empty($data['id'])){
            $res = $this->add();
            if($res){
               $this->adminLog($admin['user_id'], '添加分类 '.$data['title'].' 成功！');
                return ajaxJson(0, '添加分类成功！');
            }else{
                $this->adminLog($admin['user_id'], '添加分类 '.$data['title'].' 失败!');
                return ajaxJson(-1, '添加分类失败！');
            }            
        }else{
            $res = $this->save();
            if($result){
                $this->adminLog($admin['user_id'], '更新分类 '.$data['title'].' 成功！');
                return ajaxJson(0, '更新分类成功');
            }else{
                $this->adminLog($admin['user_id'], '更新分类 '.$data['title'].' 失败！');
                return ajaxJson(-1, '更新分类失败！');
            }            
        }
    }



    /**
     * 详细
     */
    public function info($id, $field = true){
        /* 获取分类信息 */
        $map = array();
        if(is_numeric($id)){ //通过ID查询
            $map['id'] = $id;
        } else { //通过标识查询
            $map['name'] = $id;
        }
        return $this->field($field)->where($map)->find();
    }
 

    public function getTree($id = 0, $field = true){
        /* 获取当前分类信息 */
        if($id){
            $info = $this->info($id);
            $id   = $info['id'];
        }

        /* 获取所有分类 */
        $map  = array('status' => array('gt', -1));
        $list = $this->field($field)->where($map)->order('sort')->select();
        $list = list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_', $root = $id);

        /* 获取返回数据 */
        if(isset($info)){ //指定分类则返回当前分类极其子分类
            $info['_'] = $list;
        } else { //否则返回所有分类
            $info = $list;
        }

        return $info;
    }
}