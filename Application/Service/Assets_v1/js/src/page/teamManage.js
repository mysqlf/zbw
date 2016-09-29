var template = require('art-template'),
	validateFn = require('modules/validate'),
	userAct = require('modules/actions/user'),
	userFn = require('modules/user'),
	tool = require('lib/tool'),
	icheckFn = require('modules/icheck');

//增加账号
var teamManage={
	init:function(){
		var self = this;

		$('[data-act="addTeam"]').click(function(){
			self.addTeam({},'新添账号', function(){
				layer.msg('添加成功',{
            		end: function(){
            			layer.closeAll();
            			location.reload();
            		}
            	});
			});
		})

		$('[data-act="editTeam"]').click(function(){

			userAct.editTeam($(this).data(), function(json){
				var data = json.data
					authArr = data.auth || [];
				for(var i = 0, len = authArr.length; i < len; i++){
					data[data.auth[i]] = 'checked';
				}

				self.addTeam(data, '编辑账号',function(json){
					layer.msg('编辑成功',{
                		end: function(){
                			layer.closeAll();
                			location.reload();
                		}
                	});

					if(json !=='noChange'){
						location.reload()
					}
                	
                });
			});

		})

		$('body').on('click', '[data-act="changePsw"]',function(){
			userFn.modifyPsw('#modifyPsw-form');
		})

		$('body').on('click', '[data-act="removeTeam"]',function(){
			var $this = $(this) 
				data = $this.data();

			layer.confirm('确定要删除该账号？',function(){
				userAct.delTeam(data,function(){
					$this.closest('.payroll-item').remove();
					layer.msg('删除成功');
				});
			})
			
		})


	},
	addTeam:function(data, title, success){
		var self = this,
			$form = null,
			html = template.render(require('tpl/addTeam.vue').template)(data || {});

		layer.open({
		    type: 1,
		    title: title || '新添账号',
		    skin:'layer-form',
		    shadeClose: true, //开启遮罩关闭
		    area:['630px','auto'],
		    btn:['确定', '取消'],
		    yes:function(){
				$form.submit();
		    },
		    content: html
		});
		$form = $('#addTeam-form');
		
		self.validate($form, success);
		icheckFn.init();
	},
	validate: function($form, success){
		var self = this;

		return $form.validate({
            submitHandler:function(){
                var formData = $form.serializeArray();

                if(tool.formIsDirty($form[0])){
                	// 表单是否被修改过
                	userAct.addTeam(formData,function(json){
	                	if(typeof  success === 'function'){
	                		success(json)
	                	}
	                });
                } else{
                	if(typeof success === 'function'){
	                		success('noChange');
	                }
                }

                return false;
            },
            rules: {
               account: {
               		number: true
               }
            },
            messages: {
            	group: {
            		required: '请选择角色'
                },
                username: {
                	required: '请输入账号'
                },
                password: {
                	required: '请输入密码'
                },
                name: {
                	required: '请输入姓名'
                },
                telphone: {
                	require: '请输入电话'
                },
                state: {
                	required: '请选择账号状态'
                },
                'auth[]': {
                	required: '至少选择一种权限'
                }
            }
        })
	}
}

module.exports = teamManage;