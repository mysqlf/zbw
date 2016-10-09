<?php
namespace Service\Controller;
use Think\Controller;

class ArticleController extends ServiceBaseController{

	private   $_ArticleData;
	protected $_status;
	protected $_category;

    protected function _initialize(){
    	parent::_initialize();
		$this->_Article = D('ServiceArticle');
		$this->_status = array('0'=> '待审核','1'=>'审核通过', '-1'=>'审核不通过');//, '-2'=>'删除')
		$this->_category = $this->category();

		 $this->_ArticleData['id'] = I('id', '0', 'intval');
		 $this->_ArticleData['user_id'] = $this->_AccountInfo['user_id'];
		 if(IS_GET){
			 $this->_ArticleData['title'] = I('title', '');
			 $this->_ArticleData['category_id'] = I('category_id', '0');
			 $this->_ArticleData['status'] = I('status', '');
			 $this->_ArticleData['create_time'] = I('create_time', '');
			 $this->_ArticleData['create_time1'] = I('create_time1', '');
		}
		if(IS_POST){
		 	$this->_ArticleData['title'] = I('post.title', '企业介绍');
		 	$this->_ArticleData['category_id'] = I('post.category_id', '0', 'intval');
		 	$this->_ArticleData['description'] = I('post.description', '');
		 	$this->_ArticleData['company_info'] = I('post.company_info', '0', 'intval');
		 	$this->_ArticleData['keyword'] = I('post.keyword', '');
		 	$this->_ArticleData['content'] = I('post.content', '');
		    $this->_ArticleData['id'] = I('post.id', '0', 'intval');
		 }
	}

	/**
	 * 
	 */
	public function index(){
		redirect(U('Article/articleList'));

	}
    /**
     * 列表
     */
    public function articleList(){
    	$where = "company_id = {$this->_AccountInfo['company_id']} AND status != -2 AND category_id >0";
    	if($this->_AccountInfo['group'] == 4){

    	}
    	if($this->_ArticleData['title']){
    		$where .= ' AND title like \'%'.$this->_ArticleData['title'].'%\'';
    	}
    	if($this->_ArticleData['category_id']){
    		$where .= ' AND category_id ='.$this->_ArticleData['category_id'];
    	}
    	if(is_numeric($this->_ArticleData['status']) ){
    		$where .= ' AND status ='.$this->_ArticleData['status'];
    	}
    	if($this->_ArticleData['create_time']){//" AND date_format(po.create_time, '%Y/%m/%d') >= '{$create_time}'";
		
    		$where .= " AND date_format(create_time, '%Y/%m/%d')  >= '{$this->_ArticleData['create_time']}'";//' AND create_time >'.$this->_ArticleData['create_time'];
    	}
    	if($this->_ArticleData['create_time1']){
    		$where .=  " AND date_format(create_time, '%Y/%m/%d')  <= '{$this->_ArticleData['create_time1']}'";
    	}	    	    	    	
	//	echo $where;
    	$result = $this->_Article->articleList($where, $this->_AccountInfo);

    	$this->assign('result', $result)->assign('_status', $this->_status)->assign('_category', $this->_category)->display();
    }

	/**
	 * update
	 */
	public function update(){
		if(IS_POST){		//dump($this->_ArticleData);	die();
			if(!$this->_ArticleData['company_info'] &&  empty($this->_ArticleData['category_id'])){
				$this->ajaxReturn(array('status'=> -1, 'msg'=>'分类必选！'));
			}
			$result = $this->_Article->update($this->_ArticleData, $this->_admin);

			if(empty($result)){
				$this->ajaxReturn(array('status'=> -1, 'msg'=> $this->_Article->getError()));
			}else{
				if(empty($this->_ArticleData['id'])){
					if($this->_ArticleData['company_info'])
					{
						//$this->success('添加成功', U('Article/companyInfo'));
						$this->ajaxReturn(array('status'=> 0, 'msg'=>'添加成功', 'url'=> U('Article/companyInfo') ));
					}else{
						$this->ajaxReturn(array('status'=> 0, 'msg'=>'添加成功', 'url'=> U('Article/articleList') ));
					}
				}else{
					if($this->_ArticleData['company_info'])
					{
						$this->ajaxReturn(array('status'=> 0, 'msg'=>'修改成功', 'url'=> U('Article/companyInfo') ));
					}else{
						$this->ajaxReturn(array('status'=> 0, 'msg'=>'修改成功', 'url'=> U('Article/articleList') ));
						}
				}
			}
		}else{
			if($this->_ArticleData['id']){
				$result = $this->_Article->detail($this->_ArticleData, $this->_AccountInfo);
				
			}
			$this->assign('_category', $this->_category)->assign('result', $result)->display();			
		}

	}

	/**
	 * 修改状态
	 */
	public function changeStatus(){
		if(IS_POST){
			$this->_ArticleData['status']= '-2';
			$result = $this->_Article->changeStatus($this->_ArticleData, $this->_AccountInfo);
			return $result;
		}
	}

	/**
	 * 分类信息
	 */
	public function category(){
		$result =  M('service_article_category')->field('id,title')->where("status=1 AND company_id = {$this->_AccountInfo['company_id']}")->select();// or company_id = 0
		foreach ($result as $key => $value) {
			$_result[$value['id']] = $value;
		}
		unset($result);
		return $_result;
	}



	
	/**
	 * 焦点图管理
	 */
	public function thumbList(){
		$result = M('service_thumb')->where(array('company_id'=> $this->_cid))->order('create_time desc')->select();
		foreach ($result as $key => $value) {
			if($value['place'] == 1){
				$_result['place1'][] = $value;
				unset($result[$key]);
		    }elseif($value['place'] == 2){
		    	$_result['place2'] = $value;
		    	unset($result[$key]);
		    }else{
				$_result['place3'] = $value;
				unset($result[$key]);
		    }
		} 
		//dump($_result);
		$this->assign('result', $_result);
		$this->display('Content/ad');
	}

	/**
	 * 焦点图上传
	 */
	public function thumbUpload(){
		if(IS_POST){
			$id = I('post.ids', '1');
			$id or  $this->ajaxReturn(array('status'=>0, 'msg'=> '参数错误！'));
			$path = mkFilePath($this->_uid, 'Service/');
			$config = array(			   
				'rootPath'   =>    './Uploads/',
			    'savePath'   =>    $path,
			    'subName'	=> '',
			    'saveExt'   => 'png',
			    'maxSize'   => '1024000',
				);
			$upload = new \Think\Upload($config);// 实例化上传类
		   // 上传单个文件 
		    $info   =   $upload->uploadOne($_FILES['file']);
		    if(!$info) {// 上传错误提示错误信息
		       // $this->error($upload->getError());
		         $this->ajaxReturn(array('status'=>0, 'msg'=> $upload->getError()));
		    }else{// 上传成功 获取上传文件信息
		    
		    	$url = $config['rootPath'] . $info['savepath'] . $info['savename'];
		    	$thumb_url =  $config['rootPath'] . $info['savepath'] . str_replace('.png', '_thumb.png', $info['savename']);//.$info['ext'];
		    	copy($url, $thumb_url);
		    	//生成小图	
		    	$img  = new \Think\Image();
		    	$img->open($thumb_url);
		    	$img->thumb('150', '150')->save($thumb_url);		    	
		    	//修改记录
				$url = ltrim($config['rootPath'] . $info['savepath'] . $info['savename'],'.');						
				$result = M('service_thumb')->where(array('id'=> $id))->save(array('url'=> $url));
				if(empty($result)){					
					$this->ajaxReturn(array('status'=>-1, 'msg'=>'数据保存失败！'));
				}
					$thumb_url = ltrim($thumb_url,'.');			
		            $this->ajaxReturn(array('status'=>0, 'msg'=>'', 'url'=> $thumb_url));
		    }
		}

	}



	/**
	 * 编辑器图片上传
	 */
	public function UeditUpload(){
		if(IS_POST){
			$path = mkFilePath($this->_uid, 'Service/');
			$config = array(			   
				'rootPath'   =>    './Uploads/',
			    'savePath'   =>    $path,
			    'subName'	=> '',
			);
			$upload = new \Think\Upload($config);// 实例化上传类
		   
		    $info   =   $upload->uploadOne($_FILES['pic']);
		    if(!$info) {// 上传错误提示错误信息
		       // $this->error($upload->getError());
		         $this->ajaxReturn(array('status'=>0, 'msg'=> $upload->getError()));
		    }else{// 上传成功 获取上传文件信息
		    //{"state":"SUCCESS","url":"\/ueditor\/php\/upload\/image\/20160812\/1470981008118061.gif","title":"1470981008118061.gif","original":"201402041393927982380295860867.gif","type":".gif","size":60405}
		    	$url = $config['rootPath'] . $info['savepath'] . $info['savename'];
	        	$this->ajaxReturn(array('state'=>'SUCCESS','url'=> $url));
		    }
		}

	}	


	/**
	 * 企业信息
	 */
	public function companyInfo(){
		$result = $this->_Article->companyInfo($this->_AccountInfo);
		$this->assign('result', $result)->display('Content/company_info');	//update
	}
}