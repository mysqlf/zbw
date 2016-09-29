/**
 * 企业信息
 */

require('plug/validate/index');

module.exports = {
	init() {
		let self = module.exports;

		$('#J_submit-btn').click(function(){
			$(this).closest('form').submit();
		})

		self.validate();

	},
	validate(){
		let $form = $('#J_submit-btn').closest('form'),
			{ uplateArticle: saveCompanyInfo } = require('api/content');

		$form.validate({
			submitHandler(){
				let data = $form.serializeArray(),
					{ flag } = $form.data();

				if(UEeditor.getContent()) {

					if(flag) return;

					$form.data('flag', true);
					saveCompanyInfo(data, ({ msg = '保存成功' , url}) => {
						layer.msg(msg, () => {
							if(url) location.href = url;
						});
					}, ( { msg = '保存失败' } ) => {
						layer.msg(msg);
					}). complete(() => {
						$form.data('flag', false);
					});
				} else {
					layer.msg('请填写内容')
				}
			}
		})
	}
};