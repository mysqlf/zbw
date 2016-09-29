<?php 
namespace Service\Controller;

/**
 * 代发工资
 */
class SalaryController extends ServiceBaseController{

    protected function _initialize()
    {
        parent::_initialize();

    }

	public function index(){

		$this->display('index');
	}

}

