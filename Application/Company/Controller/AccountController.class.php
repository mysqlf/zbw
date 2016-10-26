<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: rohochan <rohochan@gmail.com> <http://blog.rohochan.com>
// +----------------------------------------------------------------------

namespace Company\Controller;
use OT\DataDictionary;

/**
 * 企业中心账号控制器
 * 主要企业信息管理与账号信息管理等功能
 */
class AccountController extends HomeController {

	/**
	 * companyInfo function
	 * 企业信息
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function companyInfoOld(){
		if (IS_POST) {
			//编辑企业信息
			$companyInfo = M('CompanyInfo');
			$companyInfoResult = $companyInfo->field(true)->where(array('id'=>$this->mCid))->find();
			$data['id'] = $this->mCid;
			$data['email'] = trim(I('post.email'));
			$data['contact_name'] = I('post.contactName');
			$data['contact_phone'] = I('post.contactPhone');
			$data['contact_sex'] = I('post.gender');
			$data['property'] = I('post.property');
			$data['location'] = I('post.location');
			$data['industry'] = I('post.industry');
			$data['employee_number'] = I('post.employeeNumber');
			//$data['company_introduction'] = I('post.companyIntroduction');
			$data['tel_city_code'] = I('post.telCityCode');
			$data['tel_local_number'] = I('post.telLocalNumber');
			$data['qq'] = I('post.qq');
			$data['company_location'] = I('post.addressVo_dist')?I('post.addressVo_dist'):(I('post.addressVo_city')?I('post.addressVo_city'):I('post.addressVo_prov'));
			$data['company_address'] = I('post.addressVo_addr');
			//$data['fax'] = I('post.fax');
			//$data['register_fund'] = intval(I('post.registerFund'));
			
			if ($companyInfoResult['email'] != $data['email']) {
				if (empty($data['email'])) {
					if (empty($companyInfoResult['email'])) {
						$this->error('邮箱不能为空!');
					}else {
						unset($data['email']);
					}
				}else {
					//emailFormat($data['email']) ||  $this->error('请输入正确的邮箱!');
				}
			}else {
				unset($data['email']);
			}
			
			if ($companyInfoResult['contact_name'] != $data['contact_name']) {
				if (empty($data['contact_name'])) {
					if (empty($companyInfoResult['contact_name'])) {
						$this->error('联系人不能为空!');
					}else {
						unset($data['contact_name']);
					}
				}
			}else {
				unset($data['contact_name']);
			}
			
			if ($companyInfoResult['contact_phone'] != $data['contact_phone']) {
				if (empty($data['contact_phone'])) {
					if (empty($companyInfoResult['contact_phone'])) {
						$this->error('联系电话不能为空!');
					}else {
						unset($data['contact_phone']);
					}
				}
			}else {
				unset($data['contact_phone']);
			}
			
			if ($companyInfoResult['contact_sex'] != $data['contact_sex']) {
				if (empty($data['contact_sex'])) {
					if (empty($companyInfoResult['contact_sex'])) {
						$this->error('联系人性别不能为空!');
					}else {
						unset($data['contact_sex']);
					}
				}
			}else {
				unset($data['contact_sex']);
			}
			
			if ($companyInfoResult['property'] != $data['property']) {
				if (empty($data['property'])) {
					unset($data['property']);
					//$this->error('企业性质不能为空!');
				}
			}else {
				unset($data['property']);
			}
			
			if ($companyInfoResult['location'] != $data['location']) {
				if (empty($data['location'])) {
					unset($data['location']);
					//$this->error('所在地不能为空!');
				}
			}else {
				unset($data['location']);
			}
			
			if ($companyInfoResult['industry'] != $data['industry']) {
				if (empty($data['industry'])) {
					unset($data['industry']);
					//$this->error('行业不能为空!');
				}
			}else {
				unset($data['industry']);
			}
			
			if ($companyInfoResult['employee_number'] != $data['employee_number']) {
				if (empty($data['employee_number'])) {
					unset($data['employee_number']);
					//$this->error('员工人数不能为空!');
				}
			}else {
				unset($data['employee_number']);
			}
			
			/*if ($companyInfoResult['companyIntroduction'] != $data['companyIntroduction']) {
				if (empty($data['companyIntroduction'])) {
					unset($data['companyIntroduction']);
					//$this->error('员工人数不能为空!');
				}
			}else {
				unset($data['companyIntroduction']);
			}*/
			
			if ($companyInfoResult['company_location'] != $data['company_location']) {
				if (empty($data['company_location'])) {
					unset($data['company_location']);
					//$this->error('所在地不能为空!');
				}
			}else {
				unset($data['company_location']);
			}
			
			if ($companyInfoResult['company_address'] != $data['company_address']) {
				if (empty($data['company_address'])) {
					unset($data['company_address']);
					//$this->error('通讯地址不能为空!');
				}
			}else {
				unset($data['company_address']);
			}
			
			if ($companyInfoResult['qq'] != $data['qq']) {
				if (empty($data['qq'])) {
					unset($data['qq']);
					//$this->error('QQ不能为空!');
				}
			}else {
				unset($data['qq']);
			}
			
			if ($companyInfoResult['tel_city_code'] != $data['tel_city_code']) {
				if (empty($data['tel_city_code'])) {
					unset($data['tel_city_code']);
					//$this->error('电话区号不能为空!');
				}
			}else {
				unset($data['tel_city_code']);
			}
			
			if ($companyInfoResult['tel_local_number'] != $data['tel_local_number']) {
				if (empty($data['tel_local_number'])) {
					unset($data['tel_local_number']);
					//$this->error('电话号码不能为空!');
				}
			}else {
				unset($data['tel_local_number']);
			}
			/*empty($data['email']) && $this->error('邮箱不能为空!');
			empty($data['contact_name']) && $this->error('联系人不能为空!');
			empty($data['contact_phone']) && $this->error('联系电话不能为空!');
			empty($data['contact_sex']) && $this->error('联系人性别不能为空!');
			emailFormat($data['email']) ||  $this->error('请输入正确的邮箱!');*/
			
			//更改邮箱,则去掉邮箱验证,重新进行验证
			if (!empty($data['email']) && $data['email'] != $companyInfoResult['email']) {
				$data['email_activation'] = 0;
				//发送验证邮件
				$this->_sendEmail($companyInfoResult['email'],$data['email'],$companyInfoResult['company_name']);
			}
			
			//如果营业执照审核状态不是已审核通过时,则可以保存新营业执照
			$certificateFileResult = true ;
			$lisenceFileResult = true;
			mkFilePath($this->mCid, C('COMPANY_UPLOAD')['rootPath'], 'info');
			//if (1 != $companyInfoResult['trading_audit'] && ($certificateFile = I('certificate',''))) {
			/*if (1 != $companyInfoResult['audit'] && ($certificateFile = I('certificate',''))) {
				$certificateFileResult = move('.'.$certificateFile,str_replace('/temp','/info','.'.$certificateFile));
			}*/
			if ($certificateFile = I('certificate','')) {
				/*if (1 == $companyInfoResult['audit']) {
					$this->error('企业信息已审批,请勿再修改营业执照!');
				}else {
					$certificateFileResult = move('.'.$certificateFile,str_replace('/temp','/info','.'.$certificateFile));
				}*/
				$certificateFileResult = move('.'.$certificateFile,str_replace('/temp','/info','.'.$certificateFile));
			}
			
			//如果授权协议审核状态不是已审核通过时,则可以保存新授权协议
			//if (1 != $companyInfoResult['lisence_audit'] && ($lisenceFile = I('lisence',''))) {
			/*if (1 != $companyInfoResult['audit'] && ($lisenceFile = I('lisence',''))) {
				$lisenceFileResult = move('.'.$lisenceFile,str_replace('/temp','/info','.'.$lisenceFile));
			}*/
			if ($lisenceFile = I('lisence','')) {
				/*if (1 == $companyInfoResult['audit']) {
					$this->error('企业信息已审批,请勿再修改营业执照!');
				}else {
					$lisenceFileResult = move('.'.$lisenceFile,str_replace('/temp','/info','.'.$lisenceFile));
				}*/
				$lisenceFileResult = move('.'.$lisenceFile,str_replace('/temp','/info','.'.$lisenceFile));
			}
			
			if (count($data) > 1) {
				$companyInfoSaveResult = $companyInfo->save($data);
				if ($companyInfoSaveResult) {
					//$this->success('保存成功!');
					//$this->redirect('Company/Account/companyInfo');
					if (isset($data['email_activation']) && 0 === $data['email_activation']) {
						$this->ajaxReturn(array('status'=>2,'info'=>'保存成功且已更换邮箱!'));
					}else {
						$this->ajaxReturn(array('status'=>1,'info'=>'保存成功!'));
					}
				}else {
					$this->error('保存失败!'.$companyInfo->getDbError());
				}
			}else if($certificateFileResult || $lisenceFileResult){
				//$this->success('保存成功!');
				//$this->redirect('Company/Account/companyInfo');
				if (isset($data['email_activation']) && 0 === $data['email_activation']) {
					$this->ajaxReturn(array('status'=>2,'info'=>'保存成功且已更换邮箱!'));
				}else {
					$this->ajaxReturn(array('status'=>1,'info'=>'保存成功!'));
				}
			}else {
				$this->error('没有修改数据!');
			}
		
		}else {
			//显示企业信息
			$companyInfo = $this->mCompanyUser['CompanyInfo'];
			$companyFile = get_companyFile_by_companyId($this->mCid);
			$isChange = I('isChange',0);
			if (($companyFile['certificateFile'] || $companyFile['lisenceFile']) && 0 == $isChange && 0 == $companyInfo['audit']) {
				$this->display('companyInfoOnAudit');
			}else {
				$this->assign('companyFile',$companyFile);
				$this->assign('companyInfo',$companyInfo);
				$this->display();
			}
		
		}
	}
	
	/**
	 * companyInfo function
	 * 企业信息
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function companyInfo(){
		if (IS_POST) {
			//编辑企业信息
			$companyInfo = M('CompanyInfo');
			$companyInfoResult = $companyInfo->field(true)->where(array('id'=>$this->mCid))->find();
			$companyBank = D('CompanyBank');
			$companyInfoResult['companyBank'] = $companyBank->field(true)->getByCompanyId($this->mCid);
			if ($companyInfoResult) {
				$data = array();
				$data['id'] = $this->mCid;
				$data['company_name'] = I('post.companyName');
				$data['full_name'] = I('post.fullName');
				$data['property'] = I('post.property');
				//$data['industry'] = I('post.industry');
				//$data['company_introduction'] = I('post.companyIntroduction');
				$data['register_fund'] = intval(I('post.registerFund'));
				$data['employee_number'] = I('post.employeeNumber');
				$data['location'] = I('post.location');
				//$data['company_location'] = I('post.addressVo_dist')?I('post.addressVo_dist'):(I('post.addressVo_city')?I('post.addressVo_city'):I('post.addressVo_prov'));
				//$data['company_address'] = I('post.addressVo_addr');
				$data['company_address'] = I('post.companyAddress');
				//$data['tel_city_code'] = I('post.telCityCode');
				$data['tel_local_number'] = I('post.telLocalNumber');
				$data['social_credit_code'] = I('post.socialCreditCode');
				$data['contact_name'] = I('post.contactName');
				$data['contact_sex'] = I('post.contactSex');
				$data['contact_phone'] = I('post.contactPhone');
				$data['email'] = trim(I('post.email'));
				$data['qq'] = I('post.qq');
				//$data['fax'] = I('post.fax');
				$data['audit'] = 0;//待审核
				$bankData = array();
				$bankData['bank'] = I('post.bank');
				$bankData['account'] = I('post.account');
				
				if (empty($data['company_name'])) {
					$this->error('企业简称不能为空!');
				}else if ($companyInfoResult['company_name'] == $data['company_name']) {
					unset($data['company_name']);
				}
				
				if (empty($data['full_name'])) {
					$this->error('企业全称不能为空!');
				}else if ($companyInfoResult['full_name'] == $data['full_name']) {
					unset($data['full_name']);
				}
				
				if (empty($data['property'])) {
					$this->error('企业性质不能为空!');
				}else if ($companyInfoResult['property'] == $data['property']) {
					unset($data['property']);
				}
				
				//if (empty($data['register_fund'])) {
				//	$this->error('注册资金不能为空!');
				//}else if ($companyInfoResult['register_fund'] == $data['register_fund']) {
				//	unset($data['register_fund']);
				//}
				
				if (empty($data['employee_number'])) {
					$this->error('员工人数不能为空!');
				}else if ($companyInfoResult['employee_number'] == $data['employee_number']) {
					unset($data['employee_number']);
				}
				
				if (empty($data['location'])) {
					$this->error('所在地区不能为空!');
				}else if ($companyInfoResult['location'] == $data['location']) {
					unset($data['location']);
				}
				
				//if (empty($data['company_location'])) {
				//	$this->error('通讯地址地区不能为空!');
				//}else if ($companyInfoResult['company_location'] == $data['company_location']) {
				//	unset($data['company_location']);
				//}
				
				if (empty($data['company_address'])) {
					$this->error('通讯地址不能为空!');
				}else if ($companyInfoResult['company_address'] == $data['company_address']) {
					unset($data['company_address']);
				}
				
				//if (empty($data['tel_city_code'])) {
				//	$this->error('固话区号不能为空!');
				//}else if ($companyInfoResult['tel_city_code'] == $data['tel_city_code']) {
				//	unset($data['tel_city_code']);
				//}
				
				if (empty($data['tel_local_number'])) {
					$this->error('固话号码不能为空!');
				}else if ($companyInfoResult['tel_local_number'] == $data['tel_local_number']) {
					unset($data['tel_local_number']);
				}
				
				if (empty($data['social_credit_code'])) {
					$this->error('纳税人识别号/社会信用代码不能为空!');
				}else if ($companyInfoResult['social_credit_code'] == $data['social_credit_code']) {
					unset($data['social_credit_code']);
				}
				
				if (empty($bankData['bank'])) {
					$this->error('开户行名称不能为空!');
				}else if ($companyInfoResult['companyBank']['bank'] == $bankData['bank']) {
					unset($bankData['bank']);
				}
				
				if (empty($bankData['account'])) {
					$this->error('开户银行账户号不能为空!');
				}else if ($companyInfoResult['companyBank']['account'] == $bankData['account']) {
					unset($bankData['account']);
				}
				
				if (empty($data['contact_name'])) {
					$this->error('联系人不能为空!');
				}else if ($companyInfoResult['contact_name'] == $data['contact_name']) {
					unset($data['contact_name']);
				}
				
				if (empty($data['contact_sex'])) {
					$this->error('联系人性别不能为空!');
				}else if ($companyInfoResult['contact_sex'] == $data['contact_sex']) {
					unset($data['contact_sex']);
				}
				
				if (empty($data['contact_phone'])) {
					$this->error('联系电话不能为空!');
				}else if ($companyInfoResult['contact_phone'] == $data['contact_phone']) {
					unset($data['contact_phone']);
				}
				
				if (empty($data['email'])) {
					$this->error('邮箱不能为空!');
				}else if ($companyInfoResult['email'] == $data['email']) {
					unset($data['email']);
				}
				
				//if (empty($data['qq'])) {
				//	$this->error('QQ不能为空!');
				//}else if ($companyInfoResult['qq'] == $data['qq']) {
				//	unset($data['qq']);
				//}
				
				/*empty($data['email']) && $this->error('邮箱不能为空!');
				empty($data['contact_name']) && $this->error('联系人不能为空!');
				empty($data['contact_phone']) && $this->error('联系电话不能为空!');
				empty($data['contact_sex']) && $this->error('联系人性别不能为空!');
				emailFormat($data['email']) ||  $this->error('请输入正确的邮箱!');*/
				
				//更改邮箱,则去掉邮箱验证,重新进行验证
				if (!empty($data['email']) && $data['email'] != $companyInfoResult['email']) {
					$data['email_activation'] = 0;
					//发送验证邮件
					$this->_sendEmail($companyInfoResult['email'],$data['email'],$companyInfoResult['company_name']);
				}
				
				//文件名数组 1营业执照 2税务登记证 3纳税人资格证 4银行开户许可证
				//$fileNameArray = array(1=>'business_license',2=>'tax_cegistration_certificate',3=>'taxpayer_qualification_certificate',4=>'account_opening_license');
				$fileNameArray = C('COMPANY_INFO_FILE_NAME');
				$fileArray = array(1=>I('businessLicense'),2=>I('taxCegistrationCertificate'),3=>I('taxpayerQualificationCertificate'),4=>I('accountOpeningLicense'));
				$fileResult = array();
				$fileResult['totalCount'] = 0;
				$fileResult['successCount'] = 0;
				//$path = mkFilePath($this->mCid, C('COMPANY_UPLOAD')['rootPath'], 'info');
				$path = mkFilePath($this->mCid, './Uploads/Company/', 'info');
				foreach ($fileArray as $key => $value) {
					if ($value) {
						$fileResult['totalCount'] ++;
						move('.'.$value,$path.$fileNameArray[$key].'.jpg') && $fileResult['successCount'] ++;
						//move('.'.$value,str_replace('/temp','/info','.'.$value)) && $fileResult['successCount'] ++;
					}
				}
				$companyInfo->startTrans();
				if (count($data) >= 4) {
					$companyInfoSaveResult = $companyInfo->save($data);
					if (false === $companyInfoSaveResult) {
						$companyInfo->rollback();
						wlog($companyInfo->getDbError());
						$this->error('保存失败!');
					}
				}
				if (count($bankData) > 0) {
					$bankData['company_id'] = $this->mCid;
					$companyBankSaveResult = $companyBank->saveCompanyBank($bankData);
					if (false === $companyBankSaveResult) {
						$companyInfo->rollback();
						wlog($companyBank->getDbError());
						$this->error('保存失败!');
					}
				}
				
				if(!($fileResult['totalCount'] > 0 && $fileResult['totalCount'] == $fileResult['successCount'])){
					$companyInfo->rollback();
					$this->error('图片保存失败!');
				}
				
				$companyInfo->commit();
				//同步数据到redis
				$this->mCompanyUser = $this->getCompanyUserByCuid($this->mCuid);
				unset($this->mCompanyUser['password']);
				$this->mCid = $this->mCompanyUser['CompanyInfo']['id'];
				$redis = initRedis();
				$redisKey = 'zby:com:user:'.$this->mCuid;
				//$redisres = $redis->hSet($redisKey,'mCuid',$this->mCuid);
				$redisres = $redis->hSet($redisKey,'mCid',$this->mCid);
				$redisres = $redis->hSet($redisKey,'mCompanyUser',json_encode($this->mCompanyUser));
				
				if (isset($data['email_activation']) && 0 === $data['email_activation']) {
					$this->ajaxReturn(array('status'=>1,'info'=>'保存成功且已更换邮箱!','isChangeEmail'=>1));
				}else {
					$this->ajaxReturn(array('status'=>1,'info'=>'保存成功!'));
				}
			}else {
				$this->error('企业信息不存在!');
			}
		}else {
			//显示企业信息
			$companyInfo = $this->mCompanyUser['CompanyInfo'];
			$companyBank = M('CompanyBank');
			$companyInfo['companyBank'] = $companyBank->field(true)->getByCompanyId($this->mCid);
			$companyFile = get_companyFile_by_companyId($this->mCid);
			//dump($companyInfo);
			//dump($companyFile);
			$this->assign('companyInfo',$companyInfo);
			$this->assign('companyFile',$companyFile);
			$this->display();
			/*$change = I('change',0);
			if ((count(array_filter($companyFile))>0) && 0 == $change && 0 == $companyInfo['audit']) {
				$this->display('companyInfoOnAudit');
			}else {
				$this->assign('companyInfo',$companyInfo);
				$this->assign('companyFile',$companyFile);
				$this->display();
			}*/
		}
	}
	
	/**
	 * companyAduit function
	 * 企业信息审核状态
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function companyAduit(){
		die;
	}
	
	/**
	 * changeEmail function
	 * 修改邮件
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function changeEmail(){
		if (IS_POST) {
			$email = I('post.email');
			if ($email) {
				if ($email != $this->mCompanyUser['CompanyInfo']['email']) {
					$companyInfo = D('CompanyInfo');
					$companyInfoSaveResult = $companyInfo->where(array('id'=>$this->mCid))->save(array('email'=>$email,'email_activation'=>0));
					if (false !== $companyInfoSaveResult) {
						//发送验证邮件
						$sendResult = $this->_sendEmail($this->mCompanyUser['CompanyInfo']['email'],$email,$this->mCompanyUser['CompanyInfo']['company_name']);
						//同步数据到redis
						$this->mCompanyUser = $this->getCompanyUserByCuid($this->mCuid);
						unset($this->mCompanyUser['password']);
						$this->mCid = $this->mCompanyUser['CompanyInfo']['id'];
						$redis = initRedis();
						$redisKey = 'zby:com:user:'.$this->mCuid;
						//$redisres = $redis->hSet($redisKey,'mCuid',$this->mCuid);
						$redisres = $redis->hSet($redisKey,'mCid',$this->mCid);
						$redisres = $redis->hSet($redisKey,'mCompanyUser',json_encode($this->mCompanyUser));
						$this->ajaxReturn(array('status'=>1,'info'=>'操作成功!'));
					}else {
						wlog($companyInfo->getDbError());
						//$this->error('操作失败!');
						$this->ajaxReturn(array('status'=>0,'info'=>'操作失败!'));
					}
				}else {
					$this->ajaxReturn(array('status'=>0,'info'=>'未修改邮箱!'));
				}
			}else {
				$this->ajaxReturn(array('status'=>0,'info'=>'非法参数!'));
			}
		}else {
			//$config = array();
			//create_verify($config);
			//$this->display();
			$this->error('非法操作!');
		}
	}
	
	/**
	 * _sendEmail function
	 * 发送验证邮件
	 * @access private
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	private function _sendEmail($oemail = '',$nemail = '',$companyName = ''){
		/*$companyName = '特斯拉';
		$oemail = '212427799@qq.com';
		$nemail = '249405537@qq.com';*/
		
		//发送验证邮件
		$expireTime = 86400;//3600*24
		$key = GUID();
		S('cid'.$this->mCid,$key,$expireTime,$expireTime);
		//接收方邮箱是否有爬虫功能,有则需要额外处理
		$isQQEmail = stripos($nemail, 'qq.com');
		$isScan = false === $isQQEmail?false:true;
		//$code = base64_encode(rsa(array('cid'=>$this->mCid,'key'=>$key,'exp'=>time()+$expireTime,'isScan'=>$isScan),3));
		//$code = base64_encode(aes(array('cid'=>$this->mCid,'key'=>$key,'exp'=>time()+$expireTime,'isScan'=>$isScan),3));
		//$code = aes(array('cid'=>$this->mCid,'key'=>$key,'exp'=>time()+$expireTime,'isScan'=>$isScan),3);
		$code = aes(array('cid'=>$this->mCid,'key'=>$key,'exp'=>time()+$expireTime,'isScan'=>$isScan),5);
		$url = APP_DOMAIN.ltrim(U('Company/Account/validateEmail',array('code'=>$code)),'/');
		$time = date('Y-m-d H:i:s',time());
		$body = "<div>
					<span style='line-height: 1.5;'>尊敬的{$companyName}</span>
				</div>
				<blockquote style='margin: 0 0 0 40px; border: none; padding: 0px;'>
					<div>
						<includetail>
							<div>
								<div>
									<span style='line-height: 1.5;'>贵公司的邮箱已经由原来的{$oemail}更改为{$nemail}。请点击以下链接进行验证并生效。</span>
								</div>
							</div>
						</includetail>
					</div>
				</blockquote>
				<div>
					<includetail>
						<div>
							<div><span style='line-height: 1.5;'>{$url}</span></div>
							<div>（系统邮件，请勿回复）</div>
							<div style='text-align: right;'>智保易</div>
							<div style='text-align: right;'>{$time}</div>
						</div><br>
					</includetail>
				</div>";
		$result = think_send_mail($nemail, $companyName, '智保易邮箱验证', $body, null);
		if (true === $result) {
			return true;
		}else {
			wlog($result);
			return false;
		}
	}
	
	/**
	 * validateEmail function
	 * 校验验证邮件
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function validateEmail(){
		$url = 'Company/Account/companyUserInfo';
		if (IS_GET) {
			$code = I('get.code');
			if ($code) {
				//$result = rsa(base64_decode($code),4);
				//$result = aes(base64_decode($code),4);
				//$result = aes($code,4);
				$result = aes($code,6);
				if (is_array($result) && $result['exp'] > time()) {
					if ($result['key'] == S('cid'.$result['cid'])) {
						if ($result['isScan']) {
							if (S($result['key'].'_isScaned')) {
								$companyInfo = M('CompanyInfo');
								$companyInfoResult = $companyInfo->where(array('id'=>$result['cid']))->save(array('email_activation'=>1));
								if ($companyInfoResult || 0 === $companyInfoResult) {
									//同步数据到redis
									$companyInfoResult = $companyInfo->field(true)->getById($result['cid']);
									$companyUserId = $companyInfoResult['user_id'];
									$this->mCompanyUser = $this->getCompanyUserByCuid($companyUserId);
									unset($this->mCompanyUser['password']);
									$this->mCid = $this->mCompanyUser['CompanyInfo']['id'];
									$redis = initRedis();
									$redisKey = 'zby:com:user:'.$companyUserId;
									//$redisres = $redis->hSet($redisKey,'mCuid',$companyUserId);
									$redisres = $redis->hSet($redisKey,'mCid',$this->mCid);
									$redisres = $redis->hSet($redisKey,'mCompanyUser',json_encode($this->mCompanyUser));
									
									S('cid'.$result['cid'],null);
									S($result['key'].'_isScaned',null);
									//$this->success('邮箱验证成功!');
									//跳转到企业中心
									header("Content-type:text/html;charset=utf-8");
									redirect(U($url),1,'<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body>邮箱验证成功!页面跳转中...</body>');
								}else {
									wlog($companyInfo->getDbError());
									$this->error('未知错误!');
								}
							}else {
								S($result['key'].'_isScaned',true,$result['exp']-time());
								header("Content-type:text/html;charset=utf-8");
								redirect(U($url),1,'<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body>邮箱验证成功!页面跳转中...</body>');
							}
						}else {
							$companyInfo = M('CompanyInfo');
							$companyInfoResult = $companyInfo->save(array('id'=>$result['cid'],'email_activation'=>1));
							if ($companyInfoResult || 0 === $companyInfoResult) {
								//同步数据到redis
								$companyInfoResult = $companyInfo->field(true)->getById($result['cid']);
								$companyUserId = $companyInfoResult['user_id'];
								$this->mCompanyUser = $this->getCompanyUserByCuid($companyUserId);
								unset($this->mCompanyUser['password']);
								$this->mCid = $this->mCompanyUser['CompanyInfo']['id'];
								$redis = initRedis();
								$redisKey = 'zby:com:user:'.$companyUserId;
								//$redisres = $redis->hSet($redisKey,'mCuid',$companyUserId);
								$redisres = $redis->hSet($redisKey,'mCid',$this->mCid);
								$redisres = $redis->hSet($redisKey,'mCompanyUser',json_encode($this->mCompanyUser));
									
								S('cid'.$result['cid'],null);
								//$this->success('邮箱验证成功!');
								//跳转到企业中心
								header("Content-type:text/html;charset=utf-8");
								redirect(U($url),1,'<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body>邮箱验证成功!页面跳转中...</body>');
							}else {
								$this->error('未知错误!'.$companyInfo->getDbError(),U($url));
							}
						}
					}else {
						//跳转到企业中心
						/*header("Content-type:text/html;charset=utf-8");
						redirect(U($url),1,'<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body>邮箱验证成功!页面跳转中...</body>');*/
						$this->error('该验证码已失效,请重新发送验证邮件!',U($url));
						//die('该验证码已失效,请重新发送验证邮件!');
					}
				}else {
					$this->error('该链接已过期,请重新发送验证邮件!',U($url));
				}
			}else {
				$this->error('非法参数!',U($url));
			}
		}else {
			$this->error('非法操作!',U($url));
		}
	}
	
	/**
	 * upload function
	 * 上传文件
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function upload(){
		if (IS_POST) {
			//企业登录/退出登录时清空temp目录下的企业对应临时文件
			//$fileNameArray = array(1=>'business_license',2=>'tax_cegistration_certificate',3=>'taxpayer_qualification_certificate',4=>'account_opening_license');
			$fileNameArray = C('COMPANY_INFO_FILE_NAME');
			$type = I('post.uploadType/d');
			if (array_key_exists($type,$fileNameArray)) {
				$upload = new \Think\Upload(C('IMG_UPLOAD'));
				$path = rtrim(mkFilePath($this->mCid,$upload->rootPath,'temp'),'/');
				$path = str_replace($upload->rootPath,'',$path);
				$upload->subName = $path;
				//$upload->autoSub = false;
				//$upload->subName = intval($this->mCid/1000).'/'.$this->mCid.'/temp';
				//$upload->saveName = $this->mCid;
				$upload->saveName = $fileNameArray[$type].'_'.GUID();
				$upload->saveExt = 'jpg';
				// 上传单个文件 
				$info = $upload->uploadOne($_FILES['file']);
				if(!$info) {// 上传错误提示错误信息
					//$this->error($upload->getError());
					$this->ajaxReturn(array('status'=>0,'info'=>$upload->getError()));
				}else{// 上传成功 获取上传文件信息
					$url = ltrim($upload->rootPath,'.').$info['savepath'].$info['savename'];
					//$this->success($url);
					$this->ajaxReturn(array('status'=>1,'info'=>$url));
				}
			}else {
				$this->error('非法参数!');
			}
		}else {
			$this->error('非法操作!');
		}
	}
	
	/**
	 * delete function
	 * 删除文件
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function delete(){
		if (IS_POST) {
			$file = I('post.file','');
			//$file = '/Uploads/Company/0/temp/1.doc';
			empty($file) && $this->error('非法参数!');
			$result = unlink ('.'.$file); 
			if($result) {
				$this->success('删除成功!');
			}else{
				$this->error('删除失败!');
			}
		}else {
			$this->error('非法操作!');
		}
	}
	
	/**
	 * download function
	 * 下载文件
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function download(){
		//$file = array('url'=>iconv('UTF-8','GB2312','./Uploads/Download/授权协议.doc'),'name'=>'智保网授权协议模板.doc','type'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document','size'=>'11849');
		//$file = array('url'=>iconv('UTF-8','GB2312','./Uploads/Download/授权委托书.doc'),'name'=>'智保网授权协议模板.doc','type'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document','size'=>'30720');
		$file = array('url'=>'./Uploads/Download/授权委托书.doc','name'=>'智保网授权协议模板.doc','type'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document','size'=>'30720');
		//dump(filesize($file['url']));
		//$file=iconv('UTF-8','GB2312',$file);
		downLocalFile($file);
	}
	
	/**
	 * companyUserInfo function
	 * 企业用户信息
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function companyUserInfo(){
		if ($this->mCompanyUser) {
			$this->assign('companyUser',$this->mCompanyUser);
			$this->display();
		}else {
			$this->error('未知错误!');
		}
	}
	
	/**
	 * changePassword function
	 * 修改密码
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function changePassword(){
		if (IS_AJAX) {
			$cuid = $this->mCuid;
			$oldPassword = I('post.oldPassword','');
			$newPassword = I('post.newPassword','');
			$comfirmPassword = I('post.comfirmPassword','');
			
			//empty($cuid) && $this->error('缺少企业用户ID!');
			empty($oldPassword) && $this->error('缺少原密码!');
			empty($newPassword) && $this->error('缺少新密码!');
			strcmp($comfirmPassword, $newPassword) && $this->error('两次新密码不一致!');
			
			$companyUser = D('User');
			$companyUserResult = $companyUser->field('id,username,password,state')->getById($cuid);
			if ($companyUserResult) {
				if (1 == $companyUserResult['state']) {
					if (md5($companyUserResult['username'].':'.$oldPassword) == $companyUserResult['password']) {
						$result = $companyUser->save(array('id'=>$cuid,'password'=>md5($companyUserResult['username'].':'.$newPassword)));
						if (false !== $result) {
							$this->success('操作成功!');
						}else {
							$this->error('修改失败!');
						}
					}else {
						$this->error('原密码错误!');
					}
				}else if (0 == $companyUserResult['state']) {
					$this->error('该企业用户已暂停!');
				}else if (-1 == $companyUserResult['state']) {
					$this->error('该企业用户已停用!');
				}else if (-9 == $companyUserResult['state']) {
					$this->error('该企业用户已删除!');
				}
			}else {
				$this->error('该企业用户不存在!');
			}
		}else {
			$this->error('非法操作!');
		}
	}
}