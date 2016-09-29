/**
 * 账户信息
 */

let { saveAccountInfo } = require('api/user'),
    { modifyPsw } = require('modules/user');

require('plug/validate/index');

//账号信息
//修改密码
let accountInfo = {
    init() {
        let self = this,
            $form = $('#accountInfo_form');

        self.validate($form);

        $('[data-act="change_pwd"]').click(function() {
            modifyPsw('#modifyPsw_form');
        })
    },
    validate($form) {
        $form.validate({
            submitHandler: function() {
                let dataForm = $form.serializeArray();

                saveAccountInfo(dataForm, (json) => {
                    layer.msg(json.msg, { time: 2000 })
                });

                return false;
            },
            rules: {
                telphone: {
                    isTel: true
                },
                qq: {
                    qq: true
                }
            },
            messages: {
                username: {
                    required: '请输入姓名'
                }
            }
        })
    }
}

module.exports = accountInfo;
