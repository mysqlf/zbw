var template = require('art-template'),
    validateFn = require('modules/validate'),
    userAct = require('modules/actions/user'),
    BIN = require('plug/bankcardinfo');

module.exports = {
	init: function(){
		var self = this,
			data = $.parseJSON($('#dataInfo').val());

		if(data){
			self.tplData = $.extend({}, data, self.tplData);
			self.render(2);
		} else {
			self.render();
		}

		$('body').on('change', '#account', function(){
            var $bankSelect = $('#bank-select');

            BIN.getBankBin(this.value, function(err,data){
                if(!err){
                    $bankSelect.val(data.bankName);
                }

            })
        })

		$('body').on('click', '#J-cancle', function(){

 			self.render(self.tplData.oldType);

        })
        $('body').on('click', '[data-act="modifyBankInfo"]', function(){
 			self.render(1);
 			self.validate($('#bankInfo-form'))
        })
	},
	tplData:{
		type:0,
		oldType: 0,
		bankList: require('modules/bankData.js')
	},
	isFormChange: function(data){
		var self = this;

		for(var p in data){
			if (self.tplData[p] !== data[p]){
				return false;
			}
		}

		return true;
	},
	render: function(type){
		var self = this,
			html = '';

		// 记录旧的type
		if(self.tplData.type != 1){
			self.tplData.oldType = self.tplData.type;
		}

		if(typeof type !== 'undefined'){
			self.tplData.type = type;
		}

		html = template.render(self.tpl)(self.tplData);

		$('#bankCardInfo-tpl').html(html);
		self.validate($('#bankInfo-form'));
	},
	tpl: require('tpl/bankInfo.vue').template,
	validate: function($form){
		var self = this;

		return $form.validate({
            submitHandler:function(form){
                var formData = $form.serializeArray(),
                	data = self.serializeObj($form);

                if(self.isFormChange(data)){
                	$('#J-cancle').trigger('click');
                	layer.msg('修改成功')
                	return false
                }

                userAct.saveBankInfo(formData,function(){
                	$.extend(self.tplData, data);
                	self.render(2);
                	layer.msg('修改成功')
                });

                return false;
            },
            rules: {
               account: {
               		number: true
               }
            },
            messages: {
            	account_name: {
            		required: '请输入开户名'
                },
                account: {
                	required: '请输入账号',
            		number: '账号格式不正确'
                },
                branch: {
                	required: '请输入支行名称'
                },
                bank: {
                	required: '请输入银行名称'
                }
            }
        })
	},
	serializeObj: function($form){
		var data = $form.serializeArray($form),
			obj = {};

		for(var i=0,len=data.length; i < len; i++){
			obj[data[i].name] = data[i].value;
		}

		return obj;
	}
}