
var template = require('art-template'),
    validateFn = require('modules/validate'),
    CMAct = require('modules/actions/userManagement.js'),
    icheckFn = require('modules/icheck');

var comBuyService={
	init:function(){
		var self = this;

		$('[data-act="assign"]').click(function(){
			var data = $(this).data();
			self.assign(data);
		})

		//修改价格
		$('[data-act="changePrice"]').click(function(){
			var data = $(this).data();

			self.modifyPrice(data);
		})

		
	},
	modifyPrice: function(data){
		var self = this,
			$form = null;

		layer.open({
		    title: '修改价格',
		    skin: 'layer-form', //样式类名
		    area:['400px','auto'],
		    btn:['确定', '取消'],
		    yes:function(){

		    	$form.submit();
		    },
		    content: template.render(require('tpl/modifyPrice.vue').template)(data)
		});

		$form = $('#modifyPrice-form');
		self.modifyPvalidate($form);
	},
	modifyPvalidate: function($form){
		return $form.validate({
			submitHandler:function(){
				var data = $form.serializeArray();

				CMAct.setPrice(data, function(){
					layer.msg('修改成功',{
						end: function(){
							location.reload();
						}
					});
					
				});
			},
			rules: {
				modify_price:{
					number: true
				}
			},
			messages:{
				modify_price:{
					required: '请输入优惠价'
				}
			}
		});
	},
	//分配
	assign:function(data){
		var self = this;

		CMAct.getCService(data,function(json){
			var retData = json,
				$form = null;

			retData.id = data.id;

			layer.open({
			    title: '分配',
			    skin: 'layer-form', //样式类名
			    area:['350px','auto'],
			    btn:['确定分配', '取消'],
			    yes:function(){
			    	$form.submit();
			    },
			    content: template.render(require('tpl/assign.vue').template)(retData)
			});

			$form = $('#assign-form');
			self.assignValidate($form);
		})
	},
	assignValidate: function($form){
		return $form.validate({
			submitHandler:function(){
				var data = $form.serializeArray();

				CMAct.saveCService(data, function(){
					layer.msg('分配成功',{
						end: function(){
							layer.closeAll();
							location.reload();
						}
					});
					
				});
			},
			messages:{
				admin_id:{
					required: '请选择客服'
				}
			}
		});
	}
}
module.exports = comBuyService;