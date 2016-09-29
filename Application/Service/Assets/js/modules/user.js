let tpl = require('art-template'),
    passWordTpl = require('tpl/modify_pwd.vue'),
    { modifyPsw } = require('api/user');

require('plug/validate/index')

module.exports = {
    modifyPsw: function(form) {
        let self = module.exports,
            $modifyPswForm = null,
            $doc = $(document);

        layer.open({
            title: '修改密码',
            area: '500px',
            btn: ['确认', '取消'],
            skin: 'layer-form error-right',
            success: function() {
                passWordTpl.init();
            },
            yes: function() {

                $modifyPswForm.submit();
            },
            end: function() {
                $doc.off('keydown.submit');
            },
            content: tpl.render(require('tpl/modify_pwd.vue').template)()
        });

        $modifyPswForm = $(form);

        self.changePwdValidate($modifyPswForm);

        $doc.on('keydown.submit', function(evt) {
            if (evt.keyCode === 13) {
                $modifyPswForm.submit();
            }
        })
    },
    changePwdValidate: function($form) {
        $form.validate({
            submitHandler: function() {
                modifyPsw($form.serializeArray(), ({ msg }) => {

                    layer.msg(msg);

                }, ({ msg }) => {
                    layer.msg(msg);
                });
            },
            rules: {
                lastPassword: {
                    rangelength: [6, 20]
                },
                password: {
                    rangelength: [6, 20]
                },
                comfirmPassword: {
                    equalTo: '#password'
                }
            },
            messages: {
                lastPassword: {
                    required: '原密码必须填写',
                    rangelength: '密码长度为6~20位',
                    password: '密码由数字、字母或符号组成'
                },
                password: {
                    required: '新密码必须填写',
                    rangelength: '密码长度为6~20位',
                    password: '密码由数字、字母或符号组成'
                },
                comfirmPassword: {
                    required: '请再次输入新密码',
                    equalTo: '两次密码不一致'
                }
            }
        })
    }
}
