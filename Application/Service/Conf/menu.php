<?php
defined('__APP__') or define('__APP__', '');
return array(
		'leftMenu' => array(
			'Service'=>array('name'=>'企业首页','tag'=>'icon-home','link'=>'/Service-Service-index','route'=>array(
				'Service-Service',
				),
			),
			'Rules'=>array('name'=>'缴纳规则管理','tag'=>'icon-rules','link'=> '' ,'route'=>array(
				'Rules',
			),'children'=>array(
					'index'=>array('name'=>'我的缴费规则','link'=> U('Rules/index'),'route'=>array(
						'Rules-index','Rules-edit',
					)),
					'add'=>array('name'=>'创建缴纳规则','link'=> U('Rules/add?n=1'),'route'=>array(
						'Rules-add',
					)),
				)
			),
			'Product'=>array('name'=>'产品管理','tag'=>'icon-serpackages','link'=> '' ,'route'=>array(
				'Product',
			),'children'=>array(
					'index'=>array('name'=>'服务产品管理','link'=> U('Product/productList'),'route'=>array(
						'Product-productList','Product-productDetail',
					)),
					'add'=>array('name'=>'增值服务管理','link'=> U('Product/serviceList'),'route'=>array(
						'Product-serviceList',
					)),
				)
			),
			'Members'=>array('name'=>'客户管理','tag'=>'icon-customer','link'=> '' ,'route'=>array(
				'Members',
			),'children'=>array(
					'comMembersList'=>array('name'=>'企业客户','link'=> U('Members/comMembersList'),'route'=>array(
						'Members-comMembersList','Members-companyDetail',
					)),
			//		'perMembersList'=>array('name'=>'个人客户','link'=> U('Members/perMembersList'),'route'=>array(
			//			'perMembersList',
			//		)),
				)
			),
			'Business'=>array('name'=>'业务管理','tag'=>'icon-business','link'=> '' ,'route'=>array(
				'Business','Customer',
			),'children'=>array(
					'Customer'=>array('name'=>'企业套餐管理','link'=> U('Customer/productList'),'route'=>array(
						'Customer-productList','Customer-productOrderDetail',
					)),				
					'comMembersList'=>array('name'=>'参保人列表','link'=> U('Business/personList'),'route'=>array(
						'Business-index','Business-personList','Business-insuranceDetail','Business-editInsurance',
					)),
					'companyOrder'=>array('name'=>'企业申报','link'=> U('Business/companyOrder'),'route'=>array(
						'Business-companyOrder',
						'Business-insuranceInfoDetail',
					)),
					'salaryOrder'=>array('name'=>'代发工资','link'=> U('Business/salaryOrder'),'route'=>array(
						'Business-salaryOrder',
						'Business-importSalary',
					)),	
				)
			),
			'PayOrder'=>array('name'=>'订单管理','tag'=>'icon-order','link'=> '' ,'route'=>array(
				'PayOrder',
			),'children'=>array(
					'comMembersList'=>array('name'=>'企业订单','link'=> U('PayOrder/comPayOrderList'),'route'=>array(
						'PayOrder-comPayOrderList','PayOrder-payOrderDetail',
					)),
					// 'companyOrder'=>array('name'=>'个人订单','link'=> U('PayOrder/perPayOrderList'),'route'=>array(
					// 	'perPayOrderList',
					// )),
				)
			),
			'DiffAmount'=>array('name'=>'差额管理','tag'=>'icon-balance','link'=> '' ,'route'=>array(
				'DiffAmount',
			),'children'=>array(
					'comDiffList'=>array('name'=>'企业差额管理','link'=> U('DiffAmount/comDiffList'),'route'=>array(
						'DiffAmount-comDiffList','DiffAmount-detail',
					)),
					// 'perDiffList'=>array('name'=>'个人差额管理','link'=> U('DiffAmount/perDiffList'),'route'=>array(
					// 	'perDiffList',
					// )),
				)
			),
			'Bill'=>array('name'=>'对账单管理','tag'=>'icon-invoice','link'=> '' ,'route'=>array(
				'Bill',
			),'children'=>array(
					'comDiffList'=>array('name'=>'企业对账单','link'=> U('Bill/comBillList'),'route'=>array(
						'Bill-comBillList','Bill-comBillDetail',
					)),
					// 'perDiffList'=>array('name'=>'个人对账单','link'=> U('Bill/perBillList'),'route'=>array(
					// 	'perBillList',
					// )),
				)
			),
			'Article'=>array('name'=>'内容管理','tag'=>'icon-content','link'=> '' ,'route'=>array(
				'Article',
			),'children'=>array(
					'articleList'=>array('name'=>'文章管理','link'=> U('Article/articleList'),'route'=>array(
						'Article-articleList','Article-update',
					)),
					'thumbList'=>array('name'=>'焦点图管理','link'=> U('Article/thumbList'),'route'=>array(
						'Article-thumbList',
					)),
					'companyInfo'=>array('name'=>'企业介绍','link'=> U('Article/companyInfo'),'route'=>array(
						'Article-companyInfo',
					)),
				)
			),
		// 	'User'=>array('name'=>'设置','tag'=>'icon-setting','link'=> '' ,'route'=>array(
		// 		'User','Manage',
		// 	),'children'=>array(
		// 			'accountInfo'=>array('name'=>'账号信息','link'=> U('User/accountInfo'),'route'=>array(
		// 				'accountInfo',
		// 			)),
		// 			'adminList'=>array('name'=>'团队管理','link'=> U('Manage/adminList'),'route'=>array(
		// 				'adminList',
		// 			)),
		// 			'bankInfo'=>array('name'=>'银行信息','link'=> U('Manage/bankInfo'),'route'=>array(
		// 				'bankInfo',
		// 			)),
		// 		)
		// 	),

		 ),
	//业务 差额
		'serviceAuth'=> array('DiffAmount','Business','Customer','Service','Members','User',

			),
		//订单，对账
		'financeAuth'=> array('PayOrder','Bill','Service','User',

			),
		'articleAuth'=> array('Service', 'Article'),
	)	
;