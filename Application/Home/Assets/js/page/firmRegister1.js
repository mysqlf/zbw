let { showPsw, togglePswStronge } = require('modules/form');
let { firmRegister } = require('api/permit');
let { password, r_password, verify_code, username } = require('plug/validate/index');

module.exports = {
	init() {
		togglePswStronge();

		$('.J_btn-submit').click(function(){
			$(this).closest('form').submit();
		})

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

				firmRegister(formData, () => {
					layer.msg('提交成功');
					location.href = '/Member-firmRegister2.html';
				}, ({msg}) => {
					if(msg){
						layer.msg(msg);
					}
					$form.find('.verifyimg').click();
				}).complete(() => {
					$submit.data('flag', false).val(txt);
				})

				return false
			},
			rules: {
				password: password.rules,
				r_password: {
					equalTo: '#J_pswStronge'
				},
				username: $.extend(username.rules, {
					remote: {
						url: '/Member-repeatUserName',
						type: 'post',
						dataType: 'json',
						dataFilter(json){
							try {
								let data = $.parseJSON(json);

								if(data.status - 0 === 0){
									return true
								} else {
									return false
								}

							} catch(err) {
								return true
							}
						}
					}
				})
			},
			messages: {
				password: password.messages,
				r_password: r_password.messages,
				username: $.extend(username.messages, {
					remote: '该用户名已存在，立刻<a href="/Member-firmLogin" title="登录"> 登录 </a>或更换用户名',
				}),
				verify_code: verify_code.messages
			}
		})
	}
}