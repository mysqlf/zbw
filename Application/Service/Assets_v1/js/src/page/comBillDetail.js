
//企业账单列表
var validateFn = require('modules/validate'),
	icheckFn = require('modules/icheck'),
	billAct = require('modules/actions/bill');

var comBillList={
	init:function(){
		this.billPay();
		icheckFn.init();


	},
	billPay:function(){
		var self = this;

		$('[data-act="comBillpayment"]').click(function(){

			$form = $('#pay-form');
			self.validateObj = self.validate($form);
		})
	},
	validateObj: null,
	validate: function($form){
		var self = this;

		return $form.validate({
			submitHandler: function(){
				layer.confirm('确定保存？', {
				    btn: ['确定','取消'] //按钮
				}, function(){

					var data = $form.serializeArray();

					var loadIndex = layer.msg('提交中..');

					billAct.comBillpayment(data, function(json){
						layer.msg(json.msg, {time:2000},function(){
							layer.closeAll();
							location.reload();
						});
					}).complete(function(){
						layer.close(loadIndex);
					});

				});

				return false;
			},
			rules: {
				actual_price:{
					number: true
				}
			},
			messages: {
				actual_price: {
					required: '请输入实付金额'
				},
				has_pay: {
					required: '请选择确认付款'
				}
			}
		})

	}
}
module.exports = comBillList;

	