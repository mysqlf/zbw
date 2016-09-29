/*
 * 银行信息
 */
let template = require('art-template'),
    userAct = require('api/user'),
    BIN = require('plug/bank_card_info');

require('plug/validate/index');
require('plug/selectordie');

let bankInfo = {
    init() {
        var self = this,
            data = $('#data_info').val();

        if (data) {
            data = $.parseJSON($('#data_info').val());
            self.tplData = $.extend({}, data, self.tplData);
            self.render(2);
        } else {
            self.render();
        }

        $('body').on('change', '#account', function() {
            var $bankSelect = $('#bank_select');

            BIN.getBankBin(this.value, function(err, data) {
                if (!err) {
                    $bankSelect.val(data.bankName);
                }

            })
        })

        $('body').on('click', '#J_cancle', function() {

            self.render(self.tplData.oldType);

        })
        $('body').on('click', '[data-act="modify_bankInfo"]', function() {
            self.render(1);
            self.validate($('#bankInfo_form'))
        })

    },
    tplData: {
        type: 0,
        oldType: 0,
        bankList: require('mockData/bank_data.js')
    },
    isFormChange(data) {
        let self = this;

        for (let p in data) {
            if (self.tplData[p] !== data[p]) {
                return false;
            }
        }

        return true;
    },
    render(type) {
        let self = this,
            html = '';

        // 记录旧的type
        if (self.tplData.type != 1) {
            self.tplData.oldType = self.tplData.type;
        }

        if (typeof type !== 'undefined') {
            self.tplData.type = type;
        }

        html = template.render(self.tpl)(self.tplData);

        $('#bankCardInfo_tpl').html(html);
        $("select").selectOrDie();
        self.validate($('#bankInfo_form'));
    },
    tpl: require('tpl/bank_info.vue').template,
    validate($form) {
        let self = this;

        return $form.validate({
            submitHandler: function(form) {
                let formData = $form.serializeArray(),
                    data = self.serializeObj($form);

                if (self.isFormChange(data)) {
                    $('#J_cancle').trigger('click');
                    layer.msg('修改成功');
                    return false
                }

                userAct.saveBankInfo(formData, function() {
                    $.extend(self.tplData, data);
                    self.render(2);
                    layer.msg('修改成功');
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
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.closest('.ipt-box'));
            }
        })
    },
    serializeObj($form) {
        let data = $form.serializeArray($form),
            obj = {};

        for (let i = 0, len = data.length; i < len; i++) {
            obj[data[i].name] = data[i].value;
        }

        return obj;
    }

}

module.exports = bankInfo;
