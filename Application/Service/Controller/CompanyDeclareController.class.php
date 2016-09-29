<?php 
namespace Service\Controller;
use Think\Controller;
/**
 * 企业申报
 */
class CompanyDeclareController extends ServiceBaseController{

    protected function _initialize()
    {
        parent::_initialize();

    }

	public function index(){

		$this->display('index');
	}

}

