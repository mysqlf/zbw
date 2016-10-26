<?php
namespace Admin\Controller;

class ProductTemplateClassifyController extends AdminController
{
	private $classify_error = '';
	public function index()
	{
		$this->display();
	}

	/**
	 * [add 添加分类]
	 * @Author   JieJie
	 * @DataTime 2016-07-06T15:29:03+0800
	 */
	public function add()
	{
		if(I('post.modify')=='1')
		{
			$template_id = I('post.template_id','','intval');
		}else{
			$template_id = $this->_getTemplateId();
			if(!$template_id) $this->error($this->classify_error);
		}
		$Classify = D('TemplateClassify');
		$result = $Classify->addClassify($template_id);
		if(!$result)
		{
			$this->error($Classify->getError());
		}
		$this->ajaxReturn($result);
	}
	
	/**
	 * [_getTemplateId 添加分类时获取模板id]
	 * @Author   JieJie
	 * @DataTime 2016-07-14T14:53:49+0800
	 * @return   [int]                   [模板id]
	 */
	private function _getTemplateId()
	{
		$_POST['location'] or $this->classify_error = '请选择城市，且选中市级！';
	// 	$_POST['soc_payment_type'] or $this->classify_error = '社保支付方式必填！';
	// 	//$_POST['pro_payment_type'] or $this->classify_error = '公积金支付方式必填！';
	// 	$_POST['soc_deadline'] or $this->classify_error = '社保报增截止时间不能为空!';
	// //	$_POST['pro_deadline'] or $this->classify_error = '公积金报增截止时间不能为空!';
	// 	$_POST['soc_payment_month'] or $this->classify_error = '社保最大补缴月不能为空!';
		//$_POST['pro_payment_month'] or $this->classify_error = '公积金最大补缴月不能为空!';
		$category = I('post.category','');
		$category or $this->classify_error = '分类名必填！';
		if($this->classify_error!='') return false;
		$Template = D('Template');
		$template_id = $Template->createTemplate();
		if(!$template_id)
		{
			$this->classify_error = $Template->getError();
			return false;
		}
		return $template_id;
	}
	/**
	 * 分类删除  
	 * @param [type] $[type] [//1社保 2 公积金 3 残障金] 
	 */
	public function classify_del()
	{
		if(!IS_AJAX) $this->error('错误!');

		//接收参数
		$type = I('post.type','','intval');
		$classify_id = I('post.classify_id','','intval');
		if(!$type || !$classify_id) $this->error('数据错误!');

		//删除分类
		$Classify = D('TemplateClassify');
		$result = $Classify->classifyDel($classify_id,$type);
		if($result) $this->success('删除成功!');
		else $this->error($Classify->getError());
	}
	
	/**
	 * [get_classify_fid 返回指定分类数据]
	 * @Author   JieJie
	 * @DataTime 2016-06-30T17:27:14+0800
	 * @return   [array]      [指定分类数组]
	 */
	public function get_classify_fid()
	{
		if(!IS_AJAX) $this->error('错误！');
		$fid  = I('post.fid','','intval');
		$type = I('post.type','','intval');
		if(!$fid || !$type) $this->error('分类错误!');
		$list = D('TemplateClassify')->get_classify_fid();
		$this->ajaxReturn($list);
	}

	/**
	 * 返回指定模板下所有的规则名组合
	 */
				
	public function  show_rules_list($template_id, $type , $payment_type)
	{
		if(IS_AJAX){
			$template_id = I('post.template_id', '1');
			$type 		 = I('post.type', '');
			$payment_type = I('post.type', '');
			$template_id   or $this->error('模板ID必填！');
			$type          or $this->error('分类ID必填！');
			$payment_type  or $this->error('支付方式必填！');
			$list =  D('ProductTemplateRule')->show_rules_list($template_id, $type, $payment_type);
			$this->ajaxReturn($list);
		}else{
			$this->error('擦！');
		}
	}

	/**
	 * [modifyClassify 修改分类]
	 * @return [type] [description]
	 */
	public function modifyClassify()
	{
		$TemplateClassify = D('TemplateClassify');
		if(IS_POST)
		{
			$result = $TemplateClassify->modifyClassifyHandle();
			if(is_array($result)) $this->ajaxReturn(array('status'=>0,'msg'=>$result));
			else $this->ajaxReturn($TemplateClassify->getError());
			//if($result) $this->ajaxReturn(array('status'=>0,'msg'=>'修改成功'));
			//else $this->ajaxReturn($TemplateClassify->getError());
		}else{
			$fid = I('get.fid','','intval');
			$this->template_id = I('get.template_id','','intval');
			//获取模板分类
			$this->classify = $TemplateClassify->get_classify_fid($fid,1);
			$this->display();
		}
	}

	/**
	 * [delCalssify 删除分类]
	 * @return [type] [description]
	 */
	public function delCalssify()
	{
		$TemplateClassify = D('TemplateClassify');
		$id = I('post.id',0,'intval');
		$result = $TemplateClassify->where('id='.$id)->save(array('state'=>-9));
		if($result) $this->ajaxReturn(array('status'=>0,'msg'=>'删除成功!'));
		$this->ajaxReturn(array('status'=>1,'msg'=>'删除失败，请重试'));
	}
}