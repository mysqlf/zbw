let { getBackPass1 } = require('api/permit');

let { verify_code, username } = require('plug/validate/index');

module.exports = {
	init() {

		$('.J_btn-submit').click(function(){
			$(this).closest('form').submit();
		})
		this.validate();
	},
	validate() {
		let	$form = $('#findPsw-form');

		return $form.validate({
			submitHandler(form) {
				let $form = $(form),
					formData = $form.serializeArray(),
					$submit = $form.find('.J_btn-submit'),
					flag = $submit.data('flag'),
					txt = $submit.val();

				if(flag){
					return ;
				}

				$submit.data('flag',true);
				$submit.val('提交中...').addClass('disabled');

				getBackPass1(formData, ({url = '/Member-getBackPass2'}) => {

					location.href = url;

				}, ({ msg }) => {
					if(msg){
						layer.alert(msg);
					}

					$form.find('.verifyimg').click();
				}).complete(() => {
					$submit.val(txt).removeClass('disabled');
					$submit.data('flag',false);
				})

				return false
			},
			rules: {
				user_name: username.rules
			},
			messages: {
				user_name: username.messages,
				verify_code: verify_code.messages
			}
		})
	}
}