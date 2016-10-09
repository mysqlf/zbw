let { showPsw, togglePswStronge } = require('modules/form');
let { firmRegister2 } = require('api/permit');
require('plug/validate/index');

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

				firmRegister2(formData, ({url}) => {
					layer.msg('提交成功');
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
				contact_phone:{
					isTel: true
				}
			},
			messages: {
				company_name: {
					required: '请输入企业名称'
				},
				email: {
					required: '请输入邮箱'
				},
				contact_name: {
					required: '请输入联系人姓名'
				},
				contact_phone: {
					required: '请输入联系人电话'
				}
			}
		})
	},
	verifyEmail() {
		let {  fetch } = require('api/config');

		$('#J_aginVerify').click(function(){

			let $this = $(this),
				href = $this.attr('href'),
				flag = $this.data('flag');

			if( flag ) {
				return;
			}

			$this.data('flag', true);

			fetch(href, {}, ({url, info = '发送成功'}) => {
				layer.msg(info, function(){
					if(url) {
						location.href = url;
					}
				});
				
			}, ({ info = '发送失败' }) => {
				layer.msg(info)
			}, {
				type: 'get'
			}).complete(()=>{
				$this.data('flag', true);
			})

			return false;
		})
	}
}