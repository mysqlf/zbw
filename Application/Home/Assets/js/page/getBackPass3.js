let { showPsw, togglePswStronge } = require('modules/form');
let { modifyPassword } = require('api/permit');

let { password, r_password } = require('plug/validate/index');

module.exports = {
	init() {

		$('.J_btn-submit').click(function(){
			$(this).closest('form').submit();
		})

		togglePswStronge();

		this.validate();
		showPsw();
	},
	validate(){
		var $form = $('#J_reg-form'),
			$submit = $form.find('.J_btn-submit');

		return $form.validate({
			submitHandler(form){
				let formData = $form.serializeArray(),
					flag = $submit.data('flag'),
					txt = $submit.val();

				if(flag){
					return;
				}

				$submit.data('flag', true).val('提交中...');

				modifyPassword(formData, ({url}) => {
					layer.msg('修改成功');
					location.href = url;
				}, ({msg}) => {
					if(msg){
						layer.msg(msg);
					}
				}).complete(() => {
					$submit.data('flag', false).val(txt);
				})

				return false
			},
			rules: {
				password: password.rules,
				r_password: {
					equalTo: '#J_pswStronge'
				}
			},
			messages: {
				password: $.extend(password.messages, {
					required: '请输入新密码'
				}),
				r_password: r_password.messages
			}
		})
	}
}