var template = require('art-template'),
	validateFn = require('modules/validate'),
	userAct = require('modules/actions/user');

module.exports = {
	modifyPsw: function(form){
		var self = this;
		var $modifyPswForm = null;
		var $doc = $(document);
		layer.open({
			area: '400px',
			btn:['确认','取消'],
			skin: 'layer-form error-right',
			yes: function(){

				$modifyPswForm.submit();
			},
			end: function(){
				$doc.off('keydown.submit');
			},
			content: template.render(require('tpl/modifyPsw.vue').template)()
		});

		var $modifyPswForm = $(form);
		self.changePswValidate($modifyPswForm);

		$doc.on('keydown.submit',function(evt){
			if(evt.keyCode === 13){
				$modifyPswForm.submit();
			}
		})
	},
	changePswValidate: function($form){
		$form.validate({
			submitHandler: function(){
				userAct.modifyPsw($form.serializeArray(),function(json){
					layer.msg(json.msg,function(){
						layer.closeAll();
					});
				})
			},
			rules: {
				comfirmPassword: {
					equalTo: '#password'
				}
			},
			messages: {
				comfirmPassword: {
					required: '请再次输入新密码',
					equalTo: '两次密码不一致'
				}
			}
		})
	}
}