<?php
/**
 * 模板
 */
namespace Admin\Controller;
class ProductTemplateController extends AdminController
{
	protected  $_state = array(
		'1'  => '启用',
		'0'  => '暂停',
		'-1' => '停用'	
	);

	public function index()
	{
		$Template = D('Template');
		//获取模板信息
		$this->template_info = $Template -> getTemplateInfo();
		$location1 = I('location1/d');
		$location2 = I('location2/d');
		$location = $location2 ? $location2 : $location1;
		//获取城市信息
		$this->AREA = select_location($location);
		$this->meta_title = '社保模板管理';
		$this->display();
	}

	/**
	 *添加
	 */
	public function add()
	{ 
		if(IS_POST)
		{
			//数据验证
			$location = I('post.location') or $this->error('请选择城市，并将城市选中到市级！');
			$_POST['soc_payment_type'] or $this->error( '社保支付方式必填！');
			$_POST['pro_payment_type'] or $this->error('公积金支付方式必填！');
			$_POST['soc_deadline'] or $this->error( '社保报增截止时间不能为空!');
			$_POST['pro_deadline'] or $this->error( '公积金报增截止时间不能为空!');
			$_POST['soc_payment_month'] or $this->error('社保最大补缴月不能为空!');
			$_POST['pro_payment_month'] or$this->error( '公积金最大补缴月不能为空!');

			$amount=$_POST['sb'];
			$max=$amount['max'];
			$min=$amount['min'];
			$minlist=$amount['amount'];
			$maxlist=$amount['amountmax'];
			$count=count($minlist);
			for ($i=0; $i <$count ; $i++) {
				if ($maxlist[$i]>0&&$minlist[$i]>0) {
					if ($minlist[$i]<$min||$maxlist[$i]>$max) {
						$this->error('基数范围错误');exit;
					}
				}
			}
			if(!$_POST['sb_category_sub'][0]) 
				$this->error('请先社保选择分类!');
			$czj = I('post.czj','','floatval');
			$follow = I('post.follow','','intval');
			//清理分类为0的分类
			$sb_classify = I('post.sb_category_sub');
			//$gjj_classify = I('post.gjj_category_sub');
			$Classify = D('TemplateClassify');
			$sb_classify = $Classify->cleanClassify($sb_classify);
			//$gjj_classify = $Classify->cleanClassify($gjj_classify);

			//解析社保、公积金规则
			$Rule = D('TemplateRule');
			$sb_rule = $Rule->snalySb();
			if(!$sb_rule) $this->error($Rule->getError());
			$gjj_rule = $Rule->snalyGjj();
			if(!$gjj_rule) $this->error($Rule->getError());
			$czj_rule = I('post.czj');
			//残障金全部跟随社保
			$czj_rule['follow'] = 1;
			//建模板表
			$template_id = D('Template')->createTemplate(true);
			if(!$template_id) $this->error('城市模板添加失败！');
			//添加模板规则
			$sb_result = $Rule->createTemplateRule(1,$sb_classify,$template_id,$sb_rule);
			$gjj_result = $Rule->createTemplateRule(2,'',$template_id,$gjj_rule);
			
			//$czj_result = $Rule->createTemplateRule(3,'',$template_id,$czj_rule);
			$czj_result = true;
			if($sb_result && $gjj_result && $czj_result)
				$this->success('添加成功！', U('edit?template_id='.$template_id.'&location='.$location));
			else 
				$this->error('添加失败！');
		}else{
			$this->_area = get_area(); 
			$this->meta_title = '社保模板管理';
			$this->display();
		}
     
  	  }

	/**
	 * 模板状态
	 */
	public function get_tpl_state($state)
	{
		return $this->_state[$state];
	}


	/**
	 * [_getTplArea 有模板的直辖市删除该城市]
	 * @Author   JieJie
	 * @DataTime 2016-07-06T17:28:04+0800
	 * @return   [type]                   [description]
	 */
	private function _getTplArea()
	{	

		$area = get_area();	
		$direct = array('11000000', '12000000', '10000000', '13000000');//'北京', '天津', '上海', '重庆'
		$Template = M('template','zbw_');
		$conditon['location'] = array('in',$direct);
		$city_code = $Template->where($conditon)->field('location')->select();
	   	if(!empty($city_code))
	   	{
		    	foreach ($city_code as $key => $value) 
		    	{
		    		unset($area[$value['location']]);
		    	}
	    	}
		return $area;
	}

	/**
	 * 省市联动
	 * @param [type] $[code] [省名称]
	 */
	public function select_area($code='',$path='')
	 {      
	 	if(!IS_AJAX && !is_numeric($code)) $this->error('错误！');
		$area = getZoning();
	 	$_area = array();
	 	$next = intval($code/1000000)*1000000+1000000;
	 	$proportion = 10000;
	 	if($path==3)
	 	{
	 		$next = intval($code/100)*100+1000;
	 		//$next = intval($code/10000)*10000+10000;
	 		$proportion = 100;
	 	}
	 	foreach ($area as $key => $value) 
	 	{
	 		if($key > $code && $key < $next && $key%$proportion == 0) 
	 		{ 
                			$_area[$key]['name'] =  $value['name'];
                			$_area[$key]['id'] = $key;
            			}   
	 	}
	 	//删除已有模板城市
		/*$Template = M('template','zbw_');
		foreach ($_area as $key => $value) 
		{
			$location[] = $value['id'];
		}

	    	!empty($location) or $this->error('无下级分类！');

	    	$conditon['location'] = array('in',$location);
	    	$city_code = $Template->where($conditon)->field('location')->select();
	    	if(!empty($city_code))
	    	{
	    		foreach ($city_code as $key => $value) 
	    		{
	    			unset($_area[$value['location']]);
	    		}
	    	}*/

	    	!empty($_area) or $this->error('无下级分类！');
	    	$this->ajaxReturn($_area);
	}
	
	/**
	 * [edit 模板修改]
	 * @Author   JieJie	
	 * @DataTime 2016-07-13T13:36:21+0800
	 */
	public function edit()
	{
		$template_id = I('get.template_id', 0,'intval');
		$location    = I('get.location',0,'intval');
		$template_id or $this->error('无效模板ID!');
		$location  or $this->error('无效城市ID!');
		$this->info = M('Template','zbw_')->where(array('template_id'=> $template_id, 'location'=> $location))->find();
		$this->_area = showAreaName1($location);
		$this->classfiy_sb = $this->get_location_classify($template_id,  1);
		// $this->classfiy_gjj = $this->get_location_classify($template_id, 2);
		$TemplateRule = D('TemplateRule');
		$sb_rule = $TemplateRule->getRules(1,$template_id);
		$this->sb_rule_id = $sb_rule['id'];
		$this->sb_rule = $TemplateRule->analysisRule(1,$sb_rule['rule']);
		$gjj_rule = $TemplateRule->getRules(2,$template_id);
		$this->gjj_rule_id = $gjj_rule['id'];
		$this->gjj_rule = $TemplateRule->analysisRule(2,$gjj_rule['rule']);
		$czj_rule = $TemplateRule->getRules(3,$template_id);
		$this->czj_rule_id = $czj_rule['id'];
		$this->czj_rule = json_decode($czj_rule['rule'],true);
		$this->assign('location', $location);
		$this->assign('template_id', $template_id);
		$this->meta_title = '社保模板管理';
		$this->display();
	}	

	/**
	 * [templatePrice 修改缴费标准]
	 * @Author   JieJie
	 * @DataTime 2016-07-08T18:58:25+0800
	 * @return   [type]                   [description]
	 */
	public function templatePrice()
	{
		$Template = D('Template');
		if(IS_POST){
			$result = $Template->modifyTemplate();
			if(!$result) $this->error('修改失败！');
			$this->success('修改成功！');
		}else{
			$id = I('get.template_id','','intval');
			$this->template = $Template->where('id='.$id)->find();
			$this->display();
		}
	}

	/**
	 * [templatePrice 修改缴费标准]
	 * @Author   JieJie
	 * @DataTime 2016-07-08T18:58:25+0800
	 * @return   [type]                   [description]
	 */
	public function templategjjPrice()
	{
		$Template = D('Template');
		if(IS_POST){
			$result = $Template->modifyTemplate();
			if(!$result) $this->error('修改失败！');
			$this->success('修改成功！');
		}else{
			$id = I('get.template_id','','intval');
			$this->template = $Template->where('id='.$id)->find();
			$this->display();
		}
	}
	/**
	 * [modifySb 社保修改弹窗页面]
	 * @Author   JieJie
	 * @DataTime 2016-07-13T11:18:16+0800
	 * @return   [html]
	 */
	public function modifySb()
	{
		$this->template_id = I('post.template_id',0,'intval');
		$classify_mixed = I('post.sb_category_sub'); 
		$classify_mixed = D('TemplateClassify')->cleanClassify($classify_mixed);
		$this->classify = $classify_mixed;
		$TemplateRule = D('TemplateRule');
		$sb_rule = $TemplateRule->getRules(1,$this->template_id,$classify_mixed);
		$this->rule_id = $sb_rule['id'];
		$this->sb_rule = $TemplateRule->analysisRule(1,$sb_rule['rule']);
		//if(I('post.get_rule')) $this->ajaxReturn($this->sb_rule);
		if(I('post.get_rule')) $this->ajaxReturn(['rule_id'=>$this->rule_id,'rule'=>$this->sb_rule]);
		$this->classfiy_sb = $this->get_location_classify($this->template_id,  1);
		$this->display();
	}

	/**
	 * [modifySbHandle 社保修改处理]
	 * @Author   JieJie
	 * @DataTime 2016-07-13T11:18:50+0800
	 */
	public function modifySbHandle()
	{
		$template_id = I('post.template_id',0,'intval');
		$template_id or $this->error('数据错误!');
		$rule_id = I('post.rule_id',0,'intval');
		$sb_classify = I('post.sb_category_sub');
		$sb_classify[0] or $this->error('请先选择分类!');
		$Classify = D('TemplateClassify');
		$sb_classify = $Classify->cleanClassify($sb_classify);
		$Rule = D('TemplateRule');
		$sb_rule = $Rule->snalySb();
		$result = $Rule->updateRule($sb_rule,$template_id,$rule_id,$sb_classify);
		if(!$result) {
			$this->error('修改失败！');
		}else{
			$Template=M('template','zbw_');
			$Template->data(array('modify_time'=>date('Y-m-d H:i:s')))->where(array('id'=>$template_id))->save();
			$this->success('修改成功！');
		}
	}
	/**
	 * [modifyGjj 修改公积金]
	 * @return [type] [description]
	 */
	public function modifyGjj()
	{
		$TemplateRule = D('TemplateRule');
		if(IS_POST)
		{
			$result = $TemplateRule->modifyGjj($gjj_rule);
			if($result) $this->success('操作成功!');
			$this->error('操作失败，请重试!');
		}else{
			$template_id = I('get.template_id','','intval');
			$gjj_rule = $TemplateRule->getRules(2,$template_id);
			$this->rule_id = $gjj_rule['id'];
			$this->gjj_rule = $TemplateRule->analysisRule(2,$gjj_rule['rule']);
			$this->display();
		}
	}

	/**
	 * 显示分类及其规则  有的返回规则在前端显示 没的前端生成空表单 ajax
	 */
	public function show_classify_rules(){
		 if(IS_AJAX){
			$classify = I('get.category_sub', '0');//为0时查找 没有分类下的规则 classify_mixed
			$type 	  = I('get.type');
			$template_id = I('get.template_id');

			$template_id or $this->error('无效模板ID!');
			$type or $this->error('无效类型ID!');
			//$classify or $this->error('无效分类ID!');
			if(is_array($classify)){
				if(count($classify) > 1){
					rsort($classify);
					$classify_mixed = $classify;						
					$classify_mixed = implode('|', $classify_mixed);							
				}else{
					$classify_mixed = $classify[0];
				}
			}
			$map = array('template_id'=> $template_id, 'type'=> $type);
			if($classify_mixed){
				$map['classify_mixed'] = $classify_mixed;
			}else{
				$map['classify_mixed'] = array('exp', 'IS NULL');
			}
			$data = M('product_template_rule')->where($map)->find();	
			if($data){
				$rule = json_decode($data['rule'], true);
			}else{
				$rule = '';
			}	
			$content = '';
			if($type == 1){
				if(is_array($rule)){
					foreach ($rule['items'] as $key => $value) {
						foreach ($value['rules'] as $k => $v) {
							 $rule['items'][$key]['rules'][$k] = explode('+', str_replace('%', '', $v));
						}
					}
				}
				
				$this->assign('sb_rule', $rule);															
				$content = $this->fetch('sb_edit');
			}elseif($type ==2 ){
					if(strpos($rule['company'], '-') ){
					$rule['company'] = explode('-', $rule['company']);
				}
				if(strpos($rule['person'], '-') ){
					$rule['person'] = explode('-', $rule['person']);
				}						
				$rule = array_map(create_function('&$n', 'return str_replace(\'%\', \'\', $n);'), $rule);	
				$this->assign('gjj_rule', $rule);
				$content = $this->fetch('gjj_edit');

			}
			echo $content;die();
		 }else{
		 	$this->error('非法操作！');
		 }

	}

	

	/**
	 * 其他收费 删除
	 */
	/*public function other_del(){
		$type = I('get.type', '4');
		$template_id = I('get.template_id', 0);
		$name        = I('get.name', '');
		$template_id or $this->error('模块id错误！');
		$name or $this->error('费用名称不能为空！');

		$product_template_rule = M('product_template_rule');
		$other_rule = $product_template_rule->where(array('template_id'=> $template_id, 'type'=> $type))->find();	
		$rules = json_decode($other_rule['rule'], true);
		foreach ($rules as $key=>$value){
			if($name[0] == $key){
				unset($rules[$key]);
			}
			if($name[1] == $key){
				unset($rules[$key]);
			}
		}
		
		$result = M('product_template_rule')->where(array('template_id'=> $template_id, 'type'=> $type))->setField('rule', json_encode($rules, JSON_UNESCAPED_UNICODE));
		$this->success('删除成功！');
	}*/

	/**
	 * [modifyCzj 修改残障金]
	 * @return [type] [description]
	 */
	public function modifyCzj()
	{
		$TemplateRule = D('TemplateRule');
		$result = $TemplateRule->modifyCzj();
		if($result) 
			$this->success('操作成功!');
		else
			$this->error('操作失败，请重试!');
	}

	/**
	 * 返回某个城市下所有分类
	 * @param [type] $type [//1社保 2 公积金 3 残障金]
	 */
	public function get_location_classify($template_id, $type){
		$TemplateClassify = M('template_classify','zbw_');
		$data = $TemplateClassify->field('id, name')->where(array( 'template_id'=> $template_id,'type'=> $type, 'fid'=>0, 'state'=>'1'))->order('id asc')->select();
		$classify = array();
		if($data){
			foreach ($data as $key => $value) {
				$classify[$key]['id'] =  $value['id'];
				$classify[$key]['name'] =  $value['name'];
				if($value['id']){
					$data_sub = $TemplateClassify->field('id, name')->where(array('type'=> $type, 'template_id'=> $template_id, 'fid'=> $value['id'], 'state'=>'1'))->order('id asc')->select();
					    if($data_sub){
							$classify[$key]['category_sub'] = $data_sub;
					}
				}
			}
			return $classify;
		}	
	}

		

	/**
	 * 状态设置
	 */
	public function status()
	{
		$Template = M('template','zbw_');
		if(IS_POST)
		{
			$map['id'] = I('post.template_id',0,'intval');
			$map['location'] = array('eq',I('post.location',0,'intval'));
			$state = I('post.state',0,'intval');
			$result = $Template->where($map)->setField(array('state'=>$state));
			if($result)
				$this->success('操作成功!',U('index'));
			else
				$this->error('操作失败，请重试!');
		}
		else
		{
			$this->template_id = I('get.template_id',0,'intval');
			$this->location = I('get.location',0,'intval');
			$this->_area = showAreaName1($this->location);
			$this->info = $Template->find($this->template_id);
			$this->display();
		}
	}
}		
