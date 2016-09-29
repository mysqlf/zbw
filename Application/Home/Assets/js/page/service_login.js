let { serviceLogin: serviceLogin } = require('api/permit');

require('plug/validate/index');

let login = {
    login($loginForm) {

        $loginForm.validate({
            submitHandler() {
                let formData = $loginForm.serializeArray();

                serviceLogin(formData, (json) => {
                    // layer.msg(json.msg);
                    // location.href = '/Service-Service-index'
                    document.location.replace('/Service-Service-index');
                }, ({ msg }) => {

                    if (msg) {
                        layer.msg(msg);
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
                }
            }
        })
    }

}
module.exports = login;
