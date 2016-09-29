let { getBackPass2 } = require('api/permit');

module.exports = {
	init() {
		var self = this;

		$('.J_send-email').click(function(){
			self.sendEmail($(this));
		})
	},
	sendEmail($submit){
		var flag = $submit.data('flag'),
			txt = $submit.text();

		if(flag){
			return;
		}

		$submit.data('flag',true);
		$submit.text('发送中...').addClass('disabled');

		getBackPass2({},() => {
			layer.msg('发送成功,请登录邮箱验证');
		}, () => {
			layer.msg('发送失败，请重新发送');
		}).complete(() => {
			$submit.text(txt).removeClass('disabled');
			$submit.data('flag',false);
		})
	}
}