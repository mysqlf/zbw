let { firmLogin } = require('api/permit');
let { verify_code, username, password } = require('plug/validate/index');
require('plug/icheck/icheck');

module.exports = {
	init() {

		$('.icheck').iCheck({
		    checkboxClass: 'icheckbox icheckbox_minimal-orange',
		    radioClass: 'iradio iradio_minimal-orange',
		    increaseArea: '20%' // optional
		})

		$('.J_btn-submit').click(function(){
			$(this).closest('form').submit();
		})

		this.validate();

	},
	validate(){
		var	$form = $('#J_login-form'),
			$submit = $form.find('.J_btn-submit');

		return $form.validate({
			submitHandler(form){
				let formData = $form.serializeArray(),
					flag = $submit.data('flag'),
					txt = $submit.val();

				if(flag){
					return;
				}

				$submit.data('flag', true).val('登录中...');

				firmLogin(formData, ({url = '/Company-Index'}) => {
					layer.msg('登录成功');
					location.href = url;
				}, ({msg}) => {
					if(msg){
						layer.msg(msg);
					}
					$('[name="verify_code"]').val('');
					$('.verifyimg', $form).click();
				}).complete(() => {
					$submit.data('flag', false).val(txt);
				})

				return false
			},
			messages: {
				verify_code: verify_code.messages,
				username: username.messages,
				password: password.messages
			}
		})
	}
}