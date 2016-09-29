<?php 
namespace Service\Controller;
use Think\Controller;
/**
 * 个人申报
 */
class PersonDeclareController extends ServiceBaseController{

    protected function _initialize()
    {
        parent::_initialize();

    }

	public function index(){

		$this->display('index');
	}

}

