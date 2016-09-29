var validateFn = require('modules/validate'),
	userAct = require('modules/actions/user'),
	userFn = require('modules/user');

//账号信息
//修改密码
var accountInfo={
	init:function(){
		var self = this,
			$form = $('#accountInfo-form');

		self.validate($form);

		$('[data-act="changePsw"]').click(function(){
			userFn.modifyPsw('#modifyPsw-form');
		})
	},
	validate: function($form){
		$form.validate({
			submitHandler: function(){
				var dataForm = $form.serializeArray();

				userAct.saveAccountInfo(dataForm, function(json){
					layer.msg(json.msg, {time:2000})
				});

				return false;
			},
			rules: {
				telphone:{
					isTel: true
				},
				qq: {
					qq: true
				}
			},
			messages: {
				username: {
					required: '请输入姓名'
				}
			}
		})
	}
}

module.exports = accountInfo;