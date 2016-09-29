<?php
namespace Service\Controller;
/**
 * 参保人员记录
 */

class PersonController extends ServiceBaseController 
{

    protected function _initialize()
    {
        parent::_initialize();

    }
    /**
     * 参保人员记录
     */
    
    public function insurancePersonList(){
     
        $data['company_id'] = $this->_AccountInfo['company_id'];
        if(!$data['company_id']){
            echo '参数不全~';exit;
        }
        $person_name =  I('post.person_name', '');
        $card_num = I('post.card_num', '');
        $location = I('post.location', '');
        $state = I('post.state', '0');

        $where = '';
        if($person_name) $where = 'pb.person_name = '.$person_name;
        if($card_num) $where = 'pb.card_num = '.$card_num;
        if($location) $where = 'pi.location = '.$location;
        if($state) $where = 'pi.state = '.$state;        

        $product = D('Person');
        $res =  $product->insurancePersonList($data, $where);
        $this->assign('res', $res);
//      echo "<pre>";var_dump($res);exit;
        $this->display();
    }


    /**
     * 参保人信息详细
     */
    public function personDetail(){
        $data['base_id'] = I('get.base_id', '152');
        $data['user_id'] = I('get.user_id', '55');
        $data['company_id'] = I('get.company_id', '0');
        $product = D('Person');
        $res = $product->personDetail($data);
        dump($res);
       // $this->display();
    }

    
}
