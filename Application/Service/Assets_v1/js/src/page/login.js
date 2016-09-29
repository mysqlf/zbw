var validateFn = require('modules/validate'),
	userAct = require('modules/actions/user');

var login={
	init:function(){
		this.login($("#login-form"));
	},
	login:function($loginForm){
		$loginForm.validate({
			submitHandler: function(){
				var formData = $loginForm.serializeArray();

				userAct.login(formData, function(json){
					//layer.msg(json.msg);
					location.href='/Service-Service-index'
				});
				return false;
			},
			rules:{
				username:{
					required:true,
					account: false
				},
				password:{
					password: true,
					unPureLetter: false,
					unPureNum: false,
					rangelength: false
				}
			},
			messages:{
				username:{
					required:'请输入用户名',
					rangelength:'6-20字母、数字、下划线'
				},
				password:{
					required:'请输入密码',
					rangelength:'密码格式不正确'
				}
			}
		})
	}

}
module.exports = login;