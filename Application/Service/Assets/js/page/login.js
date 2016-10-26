let { login: loginAct } = require('api/user');

require('plug/validate/index');

let login = {
    init() {
        this.login($("#login-form"));

    },
    login($loginForm) {

        $loginForm.validate({
            submitHandler() {
                let formData = $loginForm.serializeArray();

                loginAct(formData, (json) => {
                    // layer.msg(json.msg);
                    // location.href = '/Service-Service-index'
                    window.location.replace('/Service-Service-index');
                }, ({ msg }) => {

                    if (msg) {
                        layer.msg(msg);
                        $('.verifyimg').trigger('click');
                        $('#verify').val('');
                    }

                });
                return false;
            },
            rules: {
                username: {
                    required: true,
                    account: false
                },
                password: {
                    password: true,
                    unPureLetter: false,
                    unPureNum: false,
                    rangelength: false
                }
            },
            messages: {
                username: {
                    required: '请输入用户名',
                    rangelength: '6-20字母、数字、下划线'
                },
                password: {
                    required: '请输入密码',
                    rangelength: '密码格式不正确'
                },
                verify: {
                    required: '请输入验证码'
                }
            }
        })
    }

}
module.exports = login;
