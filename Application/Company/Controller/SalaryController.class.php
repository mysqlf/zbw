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
 * 企业中心工资发放控制器
 * 主要获取工资发放数据以及导入工资
 */
class SalaryController extends HomeController {

	public function index(){
		$this->salaryOrderList();
	}
	
	/**
	 * salaryOrderList function
	 * 列表
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function salaryOrderList(){
		$data = I('get.');
		$condition = array();
		$condition['user_id'] = $this->mCuid;
		isset($data['type']) && '' !== $data['type'] && $condition['type'] = $data['type'];
		!empty($data['companyId']) && $condition['company_id'] = $data['companyId'];
		!empty($data['productId']) && $condition['product_id'] = $data['productId'];
		!empty($data['personName']) && $condition['person_name'] = $data['personName'];
		!empty($data['date']) && $condition['date'] = string_to_number($data['date']);
		
		$serviceOrderSalary = D('ServiceOrderSalary');
		$serviceOrderSalaryResult = $serviceOrderSalary->getServiceOrderSalaryListByCondition($condition);
		
		$serviceProductOrder = D('ServiceProductOrder');
		$serviceProductOrderResult = $serviceProductOrder->getAllEffectiveServiceProductOrder($this->mCuid,true);
		$serviceProviderResult = array();
		if ($serviceProductOrderResult) {
			foreach ($serviceProductOrderResult as $key => $value) {
				$serviceProviderResult[$value['company_id']] = $value['company_name'];
			}
		}
		//dump($serviceProductOrderResult);
		//dump($serviceOrderSalaryResult);
		$this->assign('serviceProductOrderResult',$serviceProductOrderResult);
		$this->assign('serviceProviderResult',$serviceProviderResult);
		$this->assign('result',$serviceOrderSalaryResult['data']);
		$this->assign('page',$serviceOrderSalaryResult['page']);
		$this->assign('count',$serviceOrderSalaryResult['count']);
		$this->display('salaryOrderList');
	}
	
	/**
	 * 上传工资单
	 **/
	public function upload(){
		if (IS_POST) {
			if (check_position($this->mMemberStatus,4)) {
				$upload = new \Think\Upload(C('COMPANY_UPLOAD'));
				$path = rtrim(mkFilePath($this->mCid,$upload->rootPath,'temp'),'/');
				$path = str_replace($upload->rootPath,'',$path);
				$upload->subName = $path;
				$upload->saveName = $this->mCid.date('_YmdHis');
				$upload->exts = array('xls','xlsx');
				$upload->uploadReplace = false;
				$info   =   $upload->uploadOne($_FILES['file']);
				if(!$info) {// 上传错误
					//$this->error($upload->getError());
					$this->ajaxReturn(array('status'=>0,'info'=>$upload->getError()));
				}else{// 上传成功
					$filePath = C('COMPANY_UPLOAD.rootPath') . $info['savepath'] . $info['savename'];
//					return $filePath;
					$this->ajaxReturn(array('status'=>1,'info'=>ltrim($filePath,'.')));
				}
			}else {
				$this->ajaxReturn(array('status'=>0,'info'=>'没有权限!'));
			}
		}else {
			$this->ajaxReturn(array('status'=>0,'info'=>'非法操作!'));
		}
	}
	
	/**
	 * downloadTemplateFile function
	 * 下载模板文件
	 * @access public
	 * @return file
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function downloadTemplateFile(){
		$type = I('get.type','xls');
		if($type) {
			if ($type == 'xls' || $type == 'xlsx') {
				$fileName = $type=='xls'?'导入工资模板.xls':'导入工资模板.xlsx';
				$fileSize = $type=='xls'?23040:8644;
				$file = array('url'=>'./Uploads/Download/'.$fileName,'name'=>$fileName,'type'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document','size'=>$fileSize);
				downLocalFile($file);
			}else{
				$this->error('未知的文件类型!');
			}
		}else {
			$this->error('非法操作!');
		}
	}
	
	/**
	 * uploadTemplateFile function
	 * 上传文件
	 * @access public
	 * @return json
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function uploadTemplateFile(){
		if (IS_POST) {
			if (check_position($this->mMemberStatus,4)) {
				//企业登录/退出登录时清空temp目录下的企业对应临时文件
				$upload = new \Think\Upload(C('EXCEL_UPLOAD'));
				$path = rtrim(mkFilePath($this->mCid,$upload->rootPath,'temp'),'/');
				$path = str_replace($upload->rootPath,'',$path);
				$upload->subName = $path;
				$upload->saveName = 'batchSalary_'.GUID();
				// 上传单个文件 
				$info = $upload->uploadOne($_FILES['file']);
				if(!$info) {// 上传错误提示错误信息
					$this->ajaxReturn(array('status'=>0,'info'=>$upload->getError()));
				}else{// 上传成功 获取上传文件信息
					$url = ltrim($upload->rootPath,'.').$info['savepath'].$info['savename'];
					$this->ajaxReturn(array('status'=>1,'info'=>$url));
				}
			}else {
				$this->ajaxReturn(array('status'=>0,'info'=>'没有代发工资套餐!'));
			}
		}else {
			$this->error('非法操作!');
		}
	}
	
	/**
	 * _handleExcel function
	 * 处理excel
	 * @access private
	 * @param string $filePath 文件路径
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	private function _handleExcel($filePath){
		Vendor('PHPExcel.PHPExcel');
		$extend=pathinfo($filePath);
		$extend = strtolower($extend['extension']);//获取后缀名并转为小写
		$extend=='xlsx'?$reader_type='Excel2007':$reader_type='Excel5';//获取excel处理类型
		if (file_exists($filePath)) {
			$phpReader = \PHPExcel_IOFactory::createReader($reader_type);
			if (!$phpReader) {
				return array('status'=>0,'info'=>'抱歉！Excel文件不兼容。');
			}
		}else {
			return array('status'=>0,'info'=>'抱歉！Excel文件不存在。');
		}
		$phpExcel = $phpReader->load($filePath);
		$currentSheet = $phpExcel->getSheet();//默认获取第一个表
		$allColumn = $currentSheet->getHighestColumn();////取得一共有多少列
		$allRow = $currentSheet->getHighestRow();//取得一共有多少行
		$excelData = array();
		for($currentRow = 2;$currentRow<=$allRow;$currentRow++){
			for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
				//dump($currentColumn.$currentRow);
				$excelData[$currentRow][$currentColumn] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$currentSheet->getCell($currentColumn.$currentRow)->getValue());
				//$excelData[$currentRow][($currentColumn++)] = '222';
				if (empty($excelData[$currentRow][$currentColumn])) {
					unset($excelData[$currentRow][$currentColumn]);
				}
			}
			if (empty($excelData[$currentRow])) {
				unset($excelData[$currentRow]);
			}
		}
		return array('status'=>1,'result'=>$excelData);
	}
	
	/**
	 * importSalary function
	 * 处理excel
	 * @access public
	 * @param string $filePath 文件路径
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function importSalary(){
		if (IS_POST) {
			//构造测试数据start
			$data['filePath'] = '/Uploads/Company/0/55/temp/55_20160718134512.xls';
			$data['productId'] = '24';
			$data['location'] = '14020000';
			//构造测试数据end
			
			$data = I('post.');//服务产品id、参保地
			
			$data['templateLocation'] = $data['location'];
			$data['location'] = ($data['location']/1000<<0)*1000;
			$filePath = '.'.$data['filePath'];
			unset($data['fileName']);
			unset($data['filePath']);
			if (is_file($filePath)) {
				$excelResult = $this->_handleExcel($filePath);//获取个人的工资信息
				if (1 == $excelResult['status']) {
					$excelData = $excelResult['result'];
					
					$personBase = D('PersonBase');
					$serviceProductOrder = D('ServiceProductOrder');
					$warrantyLocation = D('WarrantyLocation');
					$serviceOrderSalary = D('ServiceOrderSalary');
					$serviceProductOrderResult = $serviceProductOrder->getEffectiveServiceProductOrderByProductId($this->mCuid,$data['productId']);
					if ($serviceProductOrderResult) {
						//计算服务费
						$warrantyLocationResult = $warrantyLocation->getWarrantyLocationByCondition(array('service_product_order_id'=>$serviceProductOrderResult['id'],'location'=>$data['location']));
						if ($warrantyLocationResult) {
							$servicePrice = $warrantyLocationResult['af_service_price'];
							$batchResult = array();
							$batchResult['totalCount'] = 0;
							$batchResult['successCount'] = 0;
							//dump($excelData);
							foreach ($excelData as $rowNum => $rowData) {
								$rowNum --;
								$batchResult['totalCount'] ++;
								$batchResult['data'][$rowNum]['personName'] = $rowData['A'];
								$batchResult['data'][$rowNum]['cardNum'] = $rowData['B'];
								$batchResult['data'][$rowNum]['bank'] = $rowData['C'];
								$batchResult['data'][$rowNum]['branch'] = $rowData['D'];
								$batchResult['data'][$rowNum]['account_name'] = $batchResult['data'][$rowNum]['personName'];
								$batchResult['data'][$rowNum]['account'] = $rowData['E'];
								$batchResult['data'][$rowNum]['date'] = $rowData['F'];
								$batchResult['data'][$rowNum]['actual_salary'] = $rowData['G'];
								$batchResult['data'][$rowNum]['tax'] = $rowData['H'];
								$batchResult['data'][$rowNum]['salary'] = $batchResult['data'][$rowNum]['actual_salary'] + $batchResult['data'][$rowNum]['tax'];
								$batchResult['data'][$rowNum]['deduction_income_tax'] = $batchResult['data'][$rowNum]['tax'];
								$batchResult['data'][$rowNum]['price'] = $batchResult['data'][$rowNum]['salary'];
								$batchResult['data'][$rowNum]['service_price'] = $servicePrice;
								
								if ($batchResult['data'][$rowNum]['personName']) {
									if (!validatePersonName($batchResult['data'][$rowNum]['personName'])) {
										$batchResult['data'][$rowNum]['info'] = '请输入正确的姓名！';
										continue;
									}
								}else {
									$batchResult['data'][$rowNum]['info'] = '姓名必填！';
									continue;
								}
								if (!$batchResult['data'][$rowNum]['bank']) {
									$batchResult['data'][$rowNum]['info'] = '银行名称必填！';
									continue;
								}
								if (!$batchResult['data'][$rowNum]['branch']) {
									$batchResult['data'][$rowNum]['info'] = '支行名称必填！';
									continue;
								}
								if (!$batchResult['data'][$rowNum]['account']) {
									$batchResult['data'][$rowNum]['info'] = '银行账号必填！';
									continue;
								}
								if (!$batchResult['data'][$rowNum]['date']) {
									$batchResult['data'][$rowNum]['info'] = '工资年月必填！';
									continue;
								}
								if (!$batchResult['data'][$rowNum]['actual_salary']) {
									$batchResult['data'][$rowNum]['info'] = '实发工资必填！';
									continue;
								}
								if (!$batchResult['data'][$rowNum]['tax']) {
									$batchResult['data'][$rowNum]['info'] = '个人所得税必填！';
									continue;
								}
								
								if (validateIDCard($batchResult['data'][$rowNum]['cardNum'])) {
									$personBaseData = array();
									$personBaseData['user_id'] = $this->mCuid;
									$personBaseData['person_name'] = $batchResult['data'][$rowNum]['personName'];
									$personBaseData['card_num'] = $batchResult['data'][$rowNum]['cardNum'];
									$personBaseData['bank'] = $batchResult['data'][$rowNum]['bank'];
									$personBaseData['branch'] = $batchResult['data'][$rowNum]['branch'];
									$personBaseData['account_name'] = $batchResult['data'][$rowNum]['account_name'];
									$personBaseData['account'] = $batchResult['data'][$rowNum]['account'];
									$personBaseData['birthday'] = get_birthday_by_idCard($batchResult['data'][$rowNum]['cardNum']);
									$personBaseData['residence_location'] = '0';//未设置
									$personBaseData['residence_type'] = '0';//未设置
									//dump($personBaseData);
									$personBase->startTrans();
									$personBaseResult = $personBase->savePersonBase($personBaseData);
									if ($personBaseResult) {
										$personBaseId = $personBaseResult;
										
										//$salaryData = $batchResult['data'][$rowNum];
										$salaryData = array();
										$salaryData['user_id'] = $this->mCuid;
										$salaryData['base_id'] = $personBaseId;
										$salaryData['product_id'] = $data['productId'];
										$salaryData['location'] = $data['location'];
										$salaryData['date'] = $batchResult['data'][$rowNum]['date'];
										$salaryData['salary'] = $batchResult['data'][$rowNum]['salary'];
										$salaryData['price'] = $batchResult['data'][$rowNum]['price'];
										$salaryData['actual_salary'] = $batchResult['data'][$rowNum]['actual_salary'];
										$salaryData['tax'] = $batchResult['data'][$rowNum]['tax'];
										$salaryData['deduction_income_tax'] = $batchResult['data'][$rowNum]['deduction_income_tax'];
										$salaryData['price'] = $batchResult['data'][$rowNum]['price'];
										$salaryData['service_price'] = $batchResult['data'][$rowNum]['service_price'];
										$salaryData['state'] = 0;//待审核
										$salaryData['create_time'] = date('Y-m-d H:i:s');
										
										//查询是否已存在记录，如果存在则判断是否未审核，未审核和审核失败则更新
										$serviceOrderSalaryResult = $serviceOrderSalary->field(true)->where(array('user_id'=>$this->mCuid,'base_id'=>$personBaseId,'date'=>$batchResult['data'][$rowNum]['date']))->find();
										if ($serviceOrderSalaryResult) {
											if (in_array($serviceOrderSalaryResult['state'],array(0,-1))) {
												$serviceOrderSalaryResult = $serviceOrderSalary->where(array('id'=>$serviceOrderSalaryResult['id']))->save($salaryData);
												if (false !== $serviceOrderSalaryResult) {
													$personBase->commit();
													$batchResult['successCount'] ++;
												}else {
													$personBase->rollback();
													$batchResult['data'][$rowNum]['info'] = '系统内部错误！';
												}
											}else {
												$personBase->rollback();
												$batchResult['data'][$rowNum]['info'] = '已存在审核通过数据！';
											}
										}else {
											$serviceOrderSalaryResult = $serviceOrderSalary->add($salaryData);
											if ($serviceOrderSalaryResult) {
												$personBase->commit();
												$batchResult['successCount'] ++;
											}else {
												$personBase->rollback();
												$batchResult['data'][$rowNum]['info'] = '系统内部错误！';
											}
										}
									}else {
										$personBase->rollback();
										$batchResult['data'][$rowNum]['info'] = $personBase->getError();
									}
								}else {
									$batchResult['data'][$rowNum]['info'] = '身份证错误！';
								}
							}
							$this->ajaxReturn(array('status'=>1,'result'=>$batchResult));
						}else {
							$this->ajaxReturn(array('status'=>0,'info'=>'参保地错误！'));
						}
					}else {
						$this->ajaxReturn(array('status'=>0,'info'=>'产品订单错误！'));
						//$this->error('产品订单错误');
					}
				}else {
					$this->error($excelResult['info']);
				}
			}else {
				$this->ajaxReturn(array('status'=>0,'info'=>'文件路径错误！'));
			}
		}else {
			//获取购买的产品订单信息
			$serviceProductOrder = D('ServiceProductOrder');
			$serviceProductOrderResult = $serviceProductOrder->getEffectiveServiceProductOrder($this->mCuid,true);
			$this->assign('serviceProductOrderResult',$serviceProductOrderResult);
			$this->display();
		}
	}
	
	/**
	 * createPayOrder function
	 * 创建支付订单
	 * @access public
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function createPayOrder(){
		if (IS_POST) {
			//构造测试数据start
			$data['id'] = array('163,164');
			//构造测试数据end
			
			$data = I('post.');
			if ($data) {
				$data['id'] = implode(',',$data['id']);
				$data['user_id'] = $this->mCuid;
				$data['type'] = 3;//代发工资订单
				$payOrder = D('PayOrder');
				$payOrderResult = $payOrder->createPayOrder($data);
				if ($payOrderResult) {
					$this->ajaxReturn(array('status'=>1,'info'=>$payOrderResult['info'],'url'=>$payOrderResult['url']));
				}else {
					$this->ajaxReturn(array('status'=>0,'info'=>$payOrder->getError()));
				}
			}else {
				$this->error('非法参数！');
			}
		}else {
			$this->error('非法操作！');
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * _handleExcel function
	 * 处理excel
	 * @access private
	 * @param string $filePath 文件路径
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function _handleExcelOld($filePath){
		Vendor('PHPExcel.PHPExcel');
		$extend=pathinfo($filePath);
		$extend = strtolower($extend["extension"]);//获取后缀名并转为小写
		$extend=='xlsx'?$reader_type='Excel2007':$reader_type='Excel5';//获取excel处理类型
		if (file_exists($filePath)) {
			$phpReader = \PHPExcel_IOFactory::createReader($reader_type);
			if (!$phpReader) {
				$this->error("抱歉！excel文件不兼容。");
			}
		}else {
			$this->error("抱歉！excel文件不存在。");
		}
		$phpExcel = $phpReader->load($filePath);
		$currentSheet = $phpExcel->getSheet();//默认获取第一个表
		$allColumn = $currentSheet->getHighestColumn();////取得一共有多少列
		$allRow = $currentSheet->getHighestRow();//取得一共有多少行
		$excelData = array();
		for($currentRow = 2;$currentRow<=$allRow;$currentRow++){
			for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
				$excelData[$currentRow][$currentColumn] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$currentSheet->getCell($currentColumn.$currentRow)->getValue());
				if (empty($excelData[$currentRow][$currentColumn])) {
					unset($excelData[$currentRow][$currentColumn]);
				}
			}
			if (empty($excelData[$currentRow])) {
				unset($excelData[$currentRow]);
			}
		}
		return $excelData;
	}
	
	/**
	 * 将excel表数据处理后导入至数据库相应的表中
	 */
	public function getExcel(){
		$data = I('post.');//服务产品id、参保地
		//调试数据，注意工资表绑定的组合索引
		$data = array(
			'filePath' => "./Uploads/Company/0/55/temp/55_20160718134512.xls",
			'location' => "14010100",
			'orderDate' => "201606",
			'companyId' => "1",
			'productId' => "25",
			'userId' => 66
			);
		if ($data['filePath']) {
			$excelInfo = $this->_handleExcel($data['filePath']);//获取导入工资信息
			$salaryOrder = M('ServiceOrderSalary');
			$person = M('PersonBase');
			foreach ($excelInfo as $k => $v) {
				//先通过身份证号码检查是否存在个人信息
				$cardCondition['card_num'] = $v['B'];
				$personInfo = $person->where($cardCondition)->find();
				if(!$personInfo){//不存在个人信息，则创建
					$personInfo = array(
						'person_name' => $v['A'],
						'card_num' => $v['B'],
						'birthday' => get_birthday_by_idCard($v['B']),
						'bank' => $v['C'],
						'branch' => $v['D'],
						'account' => $v['E'],
						'audit' => 0,
						'create_time' => date('Y-m-d H:i:s'),
					);
					$personId = $person->add($personInfo);
					if($personId){
						$this->error('个人信息添加失败!请重新导入!');
					}
				}else{//如果存在，则更新，因为有可能出现更改工资卡的情况
					if($personInfo['account'] != $v['E']){
						$personInfo['bank'] = $v['C'];
						$personInfo['branch'] = $v['D'];
						$personInfo['account'] = $v['E'];
						$savePerson = $person->save($personInfo);
						if(!$savePerson){
							$this->error('个人信息更新失败!请重新导入!');
						}
					}
					$personId = $personInfo['id'];
				}
				// dump($personInfo);
				$salaryCondition['user_id'] = $data['userId'];
				$salaryCondition['base_id'] = $personId;
				$salaryCondition['date'] = $v['F'];
				// dump($salaryCondition);
				$salaryOrderInfo = $salaryOrder->where($salaryCondition)->find();
				if(!$salaryOrderInfo){//如果工资信息不存在则新增
					$salaryOrderInfo = array(
						'user_id' => $data['userId'],
						'base_id' => $personId,
						'product_id' => $data['productId'],
						'location' => $data['location'],
						'date' => $v['F'],
						// 'salary' => 0,
						'actual_salary' => $v['G'],
						'tax' => $v['H'],
						// 'deduction_income_tax' => detailsaly($v['G']),
						// 'price' => $v['G'],
						'state' => 0,
						'create_time' => date('Y-m-d H:i:s'),
					);
					$salaryOrderInfo['deduction_income_tax'] = detailsaly($v['G']);
					$salaryOrderInfo['salary'] = $salaryOrderInfo['price'] = $salaryOrderInfo['actual_salary'] + $salaryOrderInfo['deduction_income_tax'];
					$salaryId = $salaryOrder->add($salaryOrderInfo);
					// dump('add:'.$salaryId);
					if(!$salaryId){
						$this->error('个人工资添加失败!请重新导入!');
					}
				}else{//否则更新工资信息
					if($salaryOrderInfo['state']==0){//只有待审核的工资才能覆盖修改
						$salaryToUpdate = false;
						if($salaryOrderInfo['date'] != $v['F']){
							$salaryOrderInfo['date'] = $v['F'];
							$salaryToUpdate = true;
						}
						if($salaryOrderInfo['actual_salary'] != $v['G']){
							$salaryOrderInfo['actual_salary'] = $v['G'];
							$salaryOrderInfo['deduction_income_tax'] = detailsaly($v['G']);
							$salaryOrderInfo['salary'] = $salaryOrderInfo['price'] = $salaryOrderInfo['actual_salary'] + $salaryOrderInfo['deduction_income_tax'];
							$salaryToUpdate = true;
						}
						if($salaryOrderInfo['tax'] != $v['H']){
							$salaryOrderInfo['tax'] = $v['H'];
							$salaryToUpdate = true;
						}
						if($salaryOrderInfo['price'] != $v['G']){
							$salaryOrderInfo['price'] = $v['G'];
							$salaryToUpdate = true;
						}
						if($salaryToUpdate){
							$salaryId = $salaryOrder->save($salaryOrderInfo);
							if(!$salaryId){
								$this->error('个人工资修改失败!请勿修改非待审核的工资信息!'.$personInfo['person_name'].$salaryOrderInfo['date']);
							}
							// dump('update:'.$salaryOrderInfo['id']);
						}
					}
				}
			}
		}else {
			$this->error('非法参数!');
		}
	}
	
	/**
	 * 查询/条件查询工资信息
	 * 条件：姓名、银行卡号、工资年月、发放情况
	 */
	public function getSalaryRecord(){
		$memberStatus = $this->mMemberStatusArray;
		if($memberStatus['isSalMember']) {
			$stateArray = array(
				array('value'=>-9,'name'=>'撤销'),
				array('value'=>-2,'name'=>'发放失败'),
				array('value'=>-1,'name'=>'审核失败'),
				array('value'=>0,'name'=>'审核中'),
				array('value'=>1,'name'=>'审核通过'),
				array('value'=>2,'name'=>'发放成功')
			);
			$data = I('get.');
			//测试数据
			//$data['state'] = '4';
			//$data['companyName'] = 'last';
			//$data['productId'] = 25;
			//$data['personName'] = '泽洋';
			//$data['date'] = '2016/03';
			
			$condition = array(
				'company_id' => 65,//$this->mCid,//当前用户公司id
				'person_name' => array('like',empty($data['personName'])?'%%':'%'.$data['personName'].'%'),
				'date' => array('like',empty($data['date'])?'%%':'%'.str_replace('/','',$data['date']).'%'),
				'state' => array('like',(!isset($data['state']) || ''==$data['state'])?'%%':$stateArray[$data['state']]['value'])
			);
			$data['companyId']&&$condition['service_company_id'] = $data['companyId'];//服务商公司id
			$data['productId']&&$condition['product_id'] = $data['productId'];//服务产品id
			//dump($condition);
			$salary = D('ServiceOrderSalaryView');
			$page = getPage($salary,$condition,10);
			$show = $page->show();
			$salaryData = $salary->where($condition)->limit($page->firstRow.','.$page->listRows)->select();
			$spo = M('service_product_order');
			foreach($salaryData as $k => $v){
				if ($v['date']) {
					$salaryData[$k]['salaryDate'] = substr_replace($v['date'],'/',4,0);
				}else {
					$salaryData[$k]['salaryDate'] = '';
				}
				unset($salaryData[$k]['date'],$salaryData[$k]['state'],$salaryData[$k]['location']);
				$salaryData[$k]['salaryState'] = $this->getSalaryState($v['state']);
				//测试数据，查出service_product_order_id
				$spoCondition['user_id'] = 66;
				$spoCondition['product_id'] = $v['product_id'];
				$spoCondition['state'] = 2;
				$spoCondition['service_state'] = 2;
				unset($salaryData[$k]['product_id']);
				//再查出对应的服务费
				$servicePriceCondition['service_product_order_id'] = $spo->where($spoCondition)->getField('id');
				$servicePriceCondition['location'] = $v['location'];
				$salaryData[$k]['servicePrice'] = M('warrantyLocation')->where($servicePriceCondition)->getField('af_service_price');
			}
			//dump($stateArray);
			//dump($salaryData);
			//dump($show);
			$this->assign('stateArray',$stateArray);
			$this->assign('list',$salaryData);
			$this->assign('page',$show);
			$this->display('index');
		}
	}
	
	/**
	 * 查询工资信息详情
	 */
	public function getSalaryRecordDetail(){
		if (IS_POST) {
			$salaryId = 97;//I('post.salaryid');
			if($salaryId){//查看/编辑工资详情时通过post方式获取相应的工资信息
				$condition = array(
					//'company_id' =>$this->mCid,
					'id' => $salaryId,
					'state' => array('neq',-9),
				);
				$salary = D('ServiceOrderSalaryView');
				$salaryData = $salary->where($condition)->find();
				if ($salaryData) {
					$this->ajaxReturn(array('status'=>1,'data'=>$salaryData));
				}else {
					$this->error('数据不存在!');
				}
			}else {
				$this->error('非法参数!');
			}
		}else {
			$this->error('非法操作!');
		}
	}



	/**
	 * 返回工资的发放状态
	 * @param $state 工资的发放状态(int)
	 * @return string 工资的发放状态(string)
	 */
	protected function getSalaryState($state){
		switch($state){
			case -9:$stateString = '撤销';break;
			case -2:$stateString = '发放失败';break;
			case -1:$stateString = '审核失败';break;
			case 0:$stateString = '待审核';break;
			case 1:$stateString = '审核完成';break;
			case 2:$stateString = '发放成功';break;
		}
		return $stateString;
	}

	/**
	 * 提交编辑工资/员工信息
	 */
	public function editSalary(){
		$data = I('post.');
		///*测试数据
		$data['salaryId'] = 97;
		///*
		$data['salary'] = 7000;
		$data['tax'] = 200;
		$data['date'] = '201605';
		$data['person_name'] = '罗嘉义';
		$data['card_num'] = '511702198504283657';
		$data['bank'] = '建设银行';
		$data['branch'] = '东莞分行';
		$data['account'] = '987654321987654320';
		//*/
		/*源数据
		$data['salary'] = 6000;
		$data['tax'] = 145;
		$data['date'] = '201604';
		$data['person_name'] = '罗嘉懿';
		$data['card_num'] = '511702198504283656';
		$data['bank'] = '农业银行';
		$data['branch'] = '东莞分行';
		$data['account'] = '987654321987654321';
		*/		
		$memberStatus = $this->mMemberStatusArray;
		if($memberStatus['isSalMember']) {
			$salaryId['id'] = $data['salaryId'];
			$salary = M('service_order_salary');
			$salaryIsUpdate = false;//如果跟数据库中数据都不相同才更新
			$perIsUpdate = false;
			$salaryUpdateData = $salary->where($salaryId)->find();
			$detailCondition['service_order_id'] = $salaryUpdateData['order_id'];
			if ($salaryUpdateData) {
				if (!empty($data['salary']) && $data['salary'] != $salaryUpdateData['salary']) {
					$salaryUpdateData['salary'] = $data['salary'];
					$salaryUpdateData['price'] = $data['price'];
					$salaryIsUpdate = true;
				}
				if (!empty($data['date']) && $data['date'] != $salaryUpdateData['date']) {
					$salaryUpdateData['date'] = $data['date'];
					$salaryIsUpdate = true;
				}
				if (!empty($data['tax']) && $data['tax'] != $salaryUpdateData['tax']) {
					$salaryUpdateData['tax'] = $data['tax'];
					$salaryIsUpdate = true;
				}
				//dump('是否要更新工资信息:');
				//dump($salaryIsUpdate ? '是' : '否');
				if ($salaryIsUpdate) {//表单工资数据跟数据库数据不同则进行更新
					if(-1 != $salaryUpdateData['state'] || 0 != $salaryUpdateData['state']) {//只有审核失败跟发放失败的才能修改
						//同时不为发放成功或者审核通过的状态
						$salaryUpdateData['deduction_income_tax'] = detailsaly($salaryUpdateData['salary']);
						$salaryUpdateData['actual_salary'] = $salaryUpdateData['salary'] - $salaryUpdateData['deduction_income_tax'];
						if (-1 == $salaryUpdateData['state']) {
							//审核失败后变为审核中
							$salaryUpdateData['state'] = 0;
						}
						$salaryUpdateResult = $salary->save($salaryUpdateData);
					}else{
						//dump($this->getSalaryState($salaryUpdateData['state']).'的工资不能修改。');
						$this->error($this->getSalaryState($salaryUpdateData['state']).'的工资不能修改。');
					}
				}
				if (!$salaryIsUpdate || $salaryUpdateResult !== false) {//如果不需要更新或者更新成功
				//if ($salaryUpdateResult !== false) {//如果不需要更新或者更新成功
					//dump('工资信息不需要更新或者更新成功');
					$person = M('person_base');
					$personId['id'] = $salaryUpdateData['base_id'];
					$personUpdateData = $person->where($personId)->find();
					//如果数据不为空并且社保跟公积金都不在参保状态则进行数据更新
					if ($personUpdateData) {
						/*if (!empty($data['personName']) && $data['personName'] != $personUpdateData['person_name']) {
							$personUpdateData['person_name'] = $data['personName'];
							$perIsUpdate = true;
						}*/
						/*if (!empty($data['cardNum']) && $data['cardNum'] != $personUpdateData['card_num']) {
							$personUpdateData['card_num'] = $data['cardNum'];
							$perIsUpdate = true;
						}*/
						if (!empty($data['bank']) && $data['bank'] != $personUpdateData['bank']) {
							$personUpdateData['bank'] = $data['bank'];
							$perIsUpdate = true;
						}
						if (!empty($data['account']) && $data['account'] != $personUpdateData['account']) {
							$personUpdateData['account'] = $data['account'];
							$perIsUpdate = true;
						}
						if (!empty($data['branch']) && $data['branch'] != $personUpdateData['branch']) {
							$personUpdateData['branch'] = $data['branch'];
							$perIsUpdate = true;
						}
						//dump('是否要更新个人信息:');
						//dump($perIsUpdate ? '是' : '否');
						
						if (!$salaryIsUpdate && !$perIsUpdate) {
							//dump('不需要更新或者更新成功!');
							$this->error('没有更改数据!');
						}else {
							if ($perIsUpdate) {//确认表单个人信息数据跟数据库数据不同则进行更新
								$perUpdateResult = $person->save($personUpdateData);
								if ($perUpdateResult !== false) {
									$this->success('更新成功!');
								}else {
									$this->error('个人信息更新失败!');
								}
							}else if($salaryUpdateResult !== false){
								$this->success('更新成功!');
							}
						}
					}else{
						//dump('该人员为参保状态，个人信息不可改变!');
						$this->error('个人信息不存在!');
					}
				} else {
					//dump('工资信息更新失败!');
					$this->error('工资信息更新失败!');
				}
			} else {
				//dump('工资信息不存在!');
				$this->error('工资信息不存在!');
			}
		}
	}


	/**
	 * 撤销工资
	 */
	public function cancelSalary(){
		$data = I('post.');
		$memberStatus = $this->mMemberStatus;
		if(1||$memberStatus===0||$memberStatus===1) {
			$salaryData = array(
				'id' => $data['salaryid'],
				'state' => -9,
			);
			$salary = M('service_order_salary');
			$cancelResult = $salary->save($salaryData);
			if ($cancelResult !== false) {
				$this->success('撤销成功!');
			} else {
				$this->error('撤销失败!');

			}
		}
	}

	/**
	 * 下载工资单模版
	 */
	public function downloadSalaryTemplate(){
		$type = I('get.type');
		if($type) {
			if ($type == 'xls' || $type == 'xlsx') {
				$fileName = $type=='xls'?'导入工资模板.xls':'导入工资模板.xlsx';
				$file = './Uploads/Download/'. $fileName;
				//$file=iconv('UTF-8','GB2312',$file);
				if(file_exists($file))
					$this->downloadFile($file);
				else{
					$this->error('文件已被移除!');
				}
			}else{
				$this->error('未知的文件类型!');
			}
		}else {
			$this->error('非法操作!');
		}
	}

	/**
	 * @param $file 文件名
	 */
	function downloadFile($file){
		if(is_file($file)){
			$length = filesize($file);
			$type = pathinfo($file, PATHINFO_EXTENSION);
			$showname =  ltrim(strrchr($file,'/'),'/');
			header("Content-Description: File Transfer");
			header('Content-type: ' . $type);
			header('Content-Length:' . $length);
			if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
				header('Content-Disposition: attachment; filename="' . rawurlencode($showname) . '"');
			} else {
				header('Content-Disposition: attachment; filename="' . $showname . '"');
			}
			readfile($file);
			exit;
		} else {
			exit('文件已被删除！');
		}
	}

	/**
	 * getProductList function
	 * 获取产品列表
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getProductList(){
		if (IS_POST) {
			//获取产品(服务商)信息,并计算各个产品的对应订单月份
			$productOrder = D('ProductOrder');
			$productOrderResult = $productOrder->getSalaryProductOrder($this->mCid);
			if ($productOrderResult) {
				$this->ajaxReturn(array('status'=>1,'data'=>$productOrderResult));
			}else {
				$this->error('缺少有效的服务订单!');
			}
		}else {
			$this->error('非法操作!');
		}
	}
	
	
	/**
	 * getCityByProductOrderId function
	 * 获取根据产品订单ID获取参保地
	 * @return void
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getCityByProductOrderId(){
		if (IS_POST) {
			$productOrderId = I('post.productOrderId');
			if ($productOrderId) {
				$productOrder = D('ProductOrder');
				$productOrderResult = $productOrder->getProductOrderLocationByProductOrderId($this->mCid,$productOrderId);
				$this->ajaxReturn(array('status'=>1,'data'=>$productOrderResult['warrantyLocationList']));
			}else {
				$this->error('非法参数!');
			}
		}else {
			$this->error('非法操作!');
		}
	}

}