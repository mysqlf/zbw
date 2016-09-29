let template = require('art-template'),
    userAct = require('api/user'),
    tools = require('lib/tools'),
    icheckFn = require('plug/icheck/index');

require('plug/selectordie');
require('plug/validate/index');

//增加账号
let teamManage = {
    init() {
        let self = this;

        $('[data-act="add_team"]').click(function() {
            self.addTeam({}, '新增账号', function() {
                layer.msg('添加成功', {
                    end: function() {
                        layer.closeAll();
                        location.href = location.href;
                    }
                });
            });
        })

        $('[data-act="editTeam"]').click(function() {

            userAct.editTeam($(this).data(), (json) => {
                let data = json.data,
                    authArr = data.auth || [];
                for (let i = 0, len = authArr.length; i < len; i++) {
                    // html 属性不支持驼峰写法 所以转成小写
                    data[data.auth[i].toLowerCase()] = 'checked';
                }

                self.addTeam(data, '编辑账号', (json) => {
                    layer.msg('编辑成功', {
                        end: function() {
                            layer.closeAll();
                            location.href = location.href;
                        }
                    });

                    if (json !== 'noChange') {
                        location.href = location.href;
                    }

                });



            });

        })

        $('body').on('click', '[data-act="remove_team"]', function(data) {
            let $this = $(this)
            data = $this.data();

            layer.confirm('确定要删除该账号？', function() {
                userAct.delTeam(data, function() {
                    $this.closest('.payroll-item').remove();
                    layer.msg('删除成功');
                });
            })

        })
    },
    addTeam(data, title, success) {
        var self = this,
            $form = null,
            html = template.render(require('tpl/add_team.vue').template)(data || {});

        layer.open({
            type: 1,
            title: title || '新增账号',
            area: '600px',
            btn: ['确定', '取消'],
            yes: function() {
                $form.submit();
            },
            content: html
        });

        $("select").selectOrDie();

        $form = $('#addTeam_form');
        self.validate($form, success);
        icheckFn.init();
    },
    validate($form, success) {
        return $form.validate({
            submitHandler: function() {
                var formData = $form.serializeArray();

                if (tools.formIsDirty($form[0])) {
                    // 表单是否被修改过
                    userAct.addTeam(formData, ({ msg }) => {
                        layer.msg(msg, function() {
                            location.href = location.href;
                        });

                    }, ({ msg }) => {
                        layer.msg(msg, function() {
                            location.href = location.href;
                        });
                    });

                } else {
                    if (typeof success === 'function') {
                        success('noChange');
                    }
                }

                return false;
            },
            rules: {
                account: {
                    number: true
                },
                username: {
                    password: true,
                    rangelength: [4, 20]

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
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.closest('.ipt-box'));
            }
        })
    }
}

module.exports = teamManage;
