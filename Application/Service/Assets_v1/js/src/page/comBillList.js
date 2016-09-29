
//企业账单列表
var template = require('art-template'),
	validateFn = require('modules/validate'),
	icheckFn = require('modules/icheck'),
	billAct = require('modules/actions/bill');

var comBillList={
	init:function(){
		this.billPay();

		icheckFn.init().checkAll();

		$('[data-act="export-bill"]').click(function(){
			var url = 'Service-Bill-downloadBill?',
				$checked = $('#bill-list-form').find('.single-icheck:checked'),
				idArr = [],
				noArr = [],
				len = $checked.length;

			if(!len){
				layer.alert('至少选择一个账单')
			} else{
				$checked.each(function(index){
					var $this = $(this);

					idArr.push($this.data('bill_id'))
					noArr.push($this.data('bill_no'))
				})

				location.href = url + 'bill_id=' + idArr.join(',') + '&bill_no=' + noArr.join(',');
			}

		})

	},
	billPay:function(){
		var self = this;

		$('[data-act="comBillpayment"]').click(function(){
			var dataAct = $(this).data(),
				html = template.render(require('tpl/payBill.vue').template)(dataAct);
			var $form = null;

			layer.open({
			    type: 1,
			    skin: 'bill-pay', //样式类名
			    closeBtn: 0, //不显示关闭按钮
			    shift: 5,
			    shadeClose: true, //开启遮罩关闭
			    area:['400px','auto'],
			    btn:['确定', '取消'],
			    yes:function(){

			    	$form.submit();
			    },
			    content: html
			});

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
				}
			}
		})

	}
}
module.exports = comBillList;

	