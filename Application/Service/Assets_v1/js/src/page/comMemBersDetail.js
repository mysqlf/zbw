var template = require('art-template'),
    validateFn = require('modules/validate'),
    CMAct = require('modules/actions/userManagement.js'),
    dateObj = require('modules/date'),
    icheckFn = require('modules/icheck');

require('plug/datetimepicker');

module.exports = {
	init: function(){
		var self = this;

		$('[data-act="comSetService"]').click(function(){
			var $form = null,
				data = $(this).data().json;
				data.days = dateObj.createNumArr(1,28);

			layer.open({
				title:'设定服务状态',
				area: ['700px','auto'],
				btn: ['确认','取消'],
				skin: 'layer-form',
				content: template.render(require('tpl/setCM.vue').template)(data),
				yes: function(){
					$form.submit();
				}
			});

			$form = $('#comSetService-form');
			icheckFn.init();
			self.setValidate($form);

			self.requiredEls = $('#state2-box').find('[required]');

			$('[name="service_state"]:checked').trigger('ifChecked');

			$('#overtime').datetimepicker({
				timepicker:false,
				minDate: $.now(),
				format:'Y-m-d',
				formatDate:'Y-m-d'
			})
		});

		$('body').on('ifChecked','[name="service_state"]',function(){
			var $state2Box = $('#state2-box')

			if(this.value == 2){
				self.requiredEls.prop('required', true);
			} else{
				self.requiredEls.prop('required', false);
				self.requiredEls.removeClass('error');
				$state2Box.find('span.error').remove();
			}
		})


		$('[ data-act="addLocation"]').click(function(){
			self.addOrModifyLocation(this);
		})

		$('[ data-act="modifyLocation"]').click(function(){
			self.addOrModifyLocation(this,1);
		})

		$('[ data-act="comPayment"]').click(function(){
			var data = $(this).data();

			layer.confirm('确认支付？',function(){
				CMAct.comPayment(data,function(){
					layer.msg('确认支付成功',{
						end: function(){
							location.reload();
						}
					});
				})
			})
		})


	},
	requiredEls: {},
	/**
	 * 修改和添加参保地
	 * @param {[type]} el   点击元素
	 * @param {[type]} type 1为修改 其他未新建
	 */
	addOrModifyLocation: function(el,type){
		var self = this,
			$form = null,
			data = $(el).data(),
			title = type ? '修改' : '添加';

			CMAct.wLocation(data,function(json){
				var retData = data;

				retData.locationArr = json.data

				layer.open({
					title: title + '参保地',
					area: ['470px','auto'],
					btn: ['确认','取消'],
					skin: 'layer-form',
					content: template.render(require('tpl/addLocation.vue').template)(retData),
					yes: function(){
						$form.submit();
					}
				});

				$form = $('#addLocation-form');
				self.addValidate($form,type);
			})
	},
	setValidate: function($form){
		return $form.validate({
			submitHandler:function(){
				var data = $form.serializeArray();

				CMAct.setService(data, function(){
					layer.msg('设置成功',{
						end: function(){
							location.reload();
						}
					});

				});

			},
			rules: {
				overtime:{

				}
			},
			messages:{
				overtime:{
					required: '请选择服务有效期'
				},
				abort_add_del_date:{
					required: '请选择报增减截止日'
				},
				create_bill_date:{
					required: '请选择账单日'
				},
				abort_payment_date:{
					required: '请选择支付截止日期'
				}
				
			}
		});
	},
	addValidate: function($form,type){
		var msg = type ? '修改' : '添加';

		return $form.validate({
			submitHandler:function(){
				var data = $form.serializeArray();

				CMAct.addLocation(data, function(){
					layer.msg(msg+'成功',{
						end: function(){
							location.reload();
						}
					});

				});

			},
			rules: {
				af_service_price:{
					number: true
				},
				ss_service_price:{
					number: true
				}
			},
			messages:{
				location:{
					required: '请选择参保地'
				},
				af_service_price:{
					required: '请输入代发工资服务费'
				},
				ss_service_price:{
					required: '请输入社保/公积金服务费'
				}
			}
		});
	}
}