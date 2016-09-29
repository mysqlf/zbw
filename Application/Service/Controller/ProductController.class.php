<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Service\Controller;
use Think\Controller;


/**
 * 产品订单
 */
class ProductController extends ServiceBaseController
{
    protected $trueTableName = 'zbw_service_product';
    private $_ServiceProduct;
    private $_product;
    private $_service;
    private $_service_type;//1企业 2个人 3企业体验 4个人体验
    //-9删除 0下架 1上架 -1停用
    private $_state;
    private $_person_service_price;



    protected function _initialize()
    {
        parent::_initialize();
        $this->_ServiceProduct =  D('ServiceProduct');
        $this->_service_type = array(1=> '企业','个人','企业体验','个人体验');
        $this->_state = array('0'=>'下架', '1'=> '上架');//array( '-9'=>'删除', '0'=>'下架', '1'=> '上架', '-1'=>'停用');
        $this->_person_service_price = array('3', '6', '12');

        if(IS_POST)
        {
            $this->_product['id'] = intval(I('post.product_id',0));
            $this->_product['name'] = I('post.name','');
            $this->_product['service_type'] = intval(I('post.service_type',1));
            $this->_product['member_price'] = I('post.member_price',0);
            $this->_product['other_location'] = I('post.other_location','');
            $this->_product['location'] = I('post.location','');
            $this->_product['service_price'] = I('post.service_price','');
            $this->_product['product_detail'] = I('post.product_detail','');
            $this->_product['payment_detail'] = I('post.payment_detail','');
            $this->_product['state'] = intval(I('post.state',1));
            $this->_product['company_id'] = $this->_AccountInfo['company_id'];
            $this->_product['service_price_state'] = I('post.service_price_state',0);
            $this->_product['validity'] = I('post.validity','');
            $this->_product['service_type'] = intval(I('post.service_type',1));
            $this->_product['applicable_object'] = intval(I('post.applicable_object','0'));


            $this->_service['id'] = intval(I('post.service_id',0));
            $this->_service['product_name'] = I('post.product_name','');
            $this->_service['qq'] = I('post.qq','');
            $this->_service['state'] = intval(I('post.service_state',1));
            $this->_service['location'] = I('post.location','');
            $this->_service['advertising_url'] = I('post.advertising_url','');
            $this->_service['company_id'] = $this->_AccountInfo['company_id'];
            $this->_service['content'] = I('post.content',''); 
         }
    }

    /**
     * 企业套餐管理
     */
    public function productList()
    {
        $product['service_type'] = I('get.service_type','1'); // 1企业 2个人 3企业体验 4个人体验
        
        $result = $this->_ServiceProduct->productList($this->_product, $this->_AccountInfo);
        //dump($result);
        $this->assign('result',$result)->assign('_service_type', $this->_service_type)->assign('_state', $this->_state)->display('Product/product_manage');
    }

    /**
     * 服务套餐详情
     */
    public function productDetail()
    {
        if(IS_POST)
        {
            $result =  $this->_ServiceProduct->productDetail( $this->_product,$this->_AccountInfo, $this->_person_service_price);
            $this->ajaxReturn ($result);
        }
        else
        {
            $product['id'] = intval(I('get.id',''));
            if(!empty($product['id']))
            {
                $result = $this->_ServiceProduct->productDetail($product,$this->_AccountInfo);
                $result['service_price'] = json_decode( $result['service_price'],true);
                $result['other_location'] = json_decode( $result['other_location'],true);

                $this->assign('result',$result);
            }
            $applicable_object = adminState()['applicable_object'];

            $this->assign('_state', $this->_state)->assign('applicable_object', $applicable_object)->display('Product/com_product_detail');
        }
    }




    // public function selectLocation()
    // {
    //     $city = I('post.city','合肥');
    //     if(!empty($city))
    //     {
    //         if(IS_AJAX)
    //         {
    //             $m = M('location');
    //             $result = $m->field('id,name')->where("name LIKE '%{$city}%'")->select();
    //             $this->ajaxReturn ($result);
    //         }
    //     }
    // }

    public function delProduct()
    {
        if(IS_POST)
        {
            $result =  $this->_ServiceProduct->delProduct( $this->_product,$this->_AccountInfo);
            $this->ajaxReturn ($result);
        }
    }

    public function serviceList()
    {
        $result = $this->_ServiceProduct->serviceList($this->_AccountInfo);
       // dump($result);
        $this->assign('result',$result)->display('Product/product_manage_service');
    }
    public function serviceDetail()
    {
        if(IS_POST)
        {
            $result =  $this->_ServiceProduct->serviceDetail( $this->_service,$this->_AccountInfo);
            $this->ajaxReturn ($result);
        }
        else
        {
            $product['id'] = I('get.service_id','');
            $result = $this->_ServiceProduct->serviceDetail($product,$this->_AccountInfo);
            $this->assign('result',$result);
        }
    }



    public function delService()
    {
        if(IS_POST)
        {
            $result =  $this->_ServiceProduct->delService( $this->_service,$this->_AccountInfo);
            $this->ajaxReturn ($result);
        }
    }

    public function serviceInformation()
    {
        $m = M('value_added_service');
        $result = $m->where("id = {$this->_service['id']} AND company_id = {$this->_AccountInfo['company_id']}")->find();
        if(!empty($result['location'])) $result['city'] = showAreaName($result['location']);
        if(empty($result))  $this->ajaxReturn (array('status'=>-1,'msg'=>'未找增值服务','data'=>''));
        $this->ajaxReturn (array('status'=>0,'msg'=>'','data'=>$result));
    }

    public function updateImg()
    {
        $path = mkFilePath($this->_AccountInfo['company_id'],'Company/','advertising');
        $microtime = microtime();
        $comps = explode(' ', $microtime);
        $filename = sprintf('%d%03d', $comps[1], $comps[0] * 1000);
        set_time_limit(0);
        $upload = new \Think\Upload();
        $upload->maxSize   =     1024000 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->savePath  =     $path; // 设置附件上传根目录
        $upload->replace  =     true; // 设置附件上传（子）目录
        $upload->autoSub =       false;
        $upload->saveName =     $filename;
        //$upload->saveExt =      'jpg';
        $upload->saveExt =      'png';
        if(!$upload->upload())
        {
            $this->ajaxReturn(array('msg'=>$upload->getError(),'status'=>-1,'data'=>'') , 'json');
        }
        else
        {
            //$this->ajaxReturn(array('msg'=>'成功','status'=>0,'data'=>array('url'=>'Uploads/'.$path.$filename.'.jpg')) , 'json');
            $this->ajaxReturn(array('msg'=>'成功','status'=>0,'data'=>array('url'=>'Uploads/'.$path.$filename.'.png')) , 'json');
        }
    }

 
}
