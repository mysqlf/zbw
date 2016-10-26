<?php
namespace Service\Model;
use Think\Model;

class ServiceArticleModeL extends Model{

    protected $_auto = array(
      //  array('create_time', 'getCreateTime', self::MODEL_INSERT,'callback'),
      //  array('update_time', 'getCreateTime', self::MODEL_UPDATE,'callback'),
        );

    /**
     * 列表
     */
    public function articleList($where, $admin){
        $page = I('p', '1', 'intval');
        $count = $this->where($where)->count();
        $result = $this->where($where)->field('id, title, category_id, create_time, update_time, status, company_id')->page($page, 20)->order('update_time desc')->select();
        $pageshow = showpage($count, 20);
        return array('page'=>$pageshow,'result'=>$result);
    }

    /**
     * 新增或更新
     */
    public function update($data, $admin){
        $m = M('companyInfo');
        if(empty($data['id'])){//新增数据
            $data['company_id'] = $admin['company_id'];
            $data['create_time'] = date('Y-m-d H:i:s', NOW_TIME);
            $data['update_time'] = date('Y-m-d H:i:s', NOW_TIME);
            $data['location'] = M('company_info')->getFieldById($admin['company_id'], 'location');
            if($data['company_info']) $data['status'] = 1;

            $this->token(false)->create($data);
            $result = $this->add();
            if($result){
               $this->adminLog($admin['user_id'], '添加文章 '.$data['title'].' 成功！');
               //更新企业简介
               $m->where(array('user_id'=> $admin['user_id']))->save(array('company_introduction'=> $data['description']));
                return $result;
            }else{
                $this->adminLog($admin['user_id'], '添加文章 '.$data['title'].' 失败!');
                //return ajaxJson(-1, '文章添加失败！');
                $this->error = '添加文章失败';
                return false;
            }
        }else{
            $data['update_time'] = date('Y-m-d H:i:s', NOW_TIME);
            $result = $this->where(array('id'=> $data['id'], 'company_id'=> $admin['company_id']))->save($data);
 //           echo $this->getLastSql();die();
            if($result){
                $this->adminLog($admin['user_id'], '修改文章 '.$data['title'].' 成功！');
               // return ajaxJson(0, '文章修改成功');
               //更新企业简介
               $m->where(array('user_id'=> $admin['user_id']))->save(array('company_introduction'=> $data['description']));                
                return $result;
            }else{
                $this->adminLog($admin['user_id'], '修改文章 '.$data['title'].' 失败！');
               // return ajaxJson(-1, '文章修改失败！');
                 $this->error = '修改文章失败';
                 return false;
            }
        }

    }

    protected function getCreateTime(){
        $create_time    =   I('post.create_time');
        return $create_time?strtotime($create_time):NOW_TIME;
    }


    /**
     * 修改状态
     */
    public function changeStatus($data, $admin){
        $result = $this->where(array('id'=> $data['id'], 'company_id'=> $admin['company_id']))->save(array('status'=> $data['status']));
        if($result){
            //$this->adminLog($admin['user_id'], '修改文章 '.$data['id'].' 状态为'. $data['status'] .'成功！');
            return ajaxJson(0, '修改成功');
        }else{
           // $this->adminLog($admin['user_id'], '修改文章 '.$data['id'].' 状态为'. $data['status'] .'失败！');
            return ajaxJson(-1, '修改失败！');
        }
    }


    /**
     * 详细
     */
    public function detail($data, $admin){
        $result = $this->where(array('id'=>$data['id']))->find();
        if(empty($result)){
            $this->error = '信息不存在！';
            return false;
        }else{
            return $result;
        }
    }


    public function adminLog($admin_id,$detail)
    {
        $log = M('ServiceAdminLog');
        $log->add(array('admin_id'=>$admin_id,'create_time'=>date('Y-m-d H:i:s',time()),'detail'=>$detail));
    }

    /**
     * companyInfo
     */
    public function companyInfo($admin){
        $result = $this->where(array('category_id'=>0,'company_id'=> $admin['company_id']))->order('update_time desc')->find();
        $_des = M('company_info')->field('company_introduction ')->where(array('audit'=> 1, 'user_id'=>$admin['user_id']))->find();
        $result['description'] = $_des['company_introduction'];
       
          return $result;  
    }
} 