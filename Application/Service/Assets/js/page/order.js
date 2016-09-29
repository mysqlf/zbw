/*
 * 确认到款 comfirmMoney()
 */

let template = require('art-template'),
    orderAct = require('api/order'),
    dateFn = require('plug/datetimepicker/index'),
    { dateRange } = require('modules/date');

require('plug/validate/index');

let order = {
    init() {

        this.comfirmMoney();
    },
    payOrderList() {
        //企业付款流水管理初始化
        dateFn.getYearMonthDay();

        // 保证开始时间不超过结束时间
        dateRange('pay', 'create')
    },
    comfirmMoney() {

        $('[data-act="confirm_money"]').click(function() {
            let $this = $(this),
                actual = $('.amount-actual em').text().split(',').join('') - 0,
                id = $this.data('id'), // 1线上支付  2线下支付
                paytype = $this.data('paytype'),
                type = $this.data('type');

            let data = {
                due: $('.amount-due em').text() - 0,
                balance: $('.balance em').text() - 0,
                actual,
                paytype,
                type
            }

            layer.open({
                title: '确认到款',
                area: '480px',
                content: template.render(require('tpl/confirm_money.vue').template)(data),
                btn: ['确定', '取消'],
                yes: function() {
                    $('#confirm_form').submit();
                },
                success(){
                    $('#confirm_form').validate({
                        submitHandler: function() {
                            let data = {};

                            if (id === 1) {
                                data = {
                                    id: $this.data('id'),
                                    type: paytype,
                                    actual: actual
                                }

                            } else {
                                data = {
                                    id: $this.data('id'),
                                    type: paytype,
                                    actual: actual,
                                    bankNo: $('[name="bank_no"]').val()
                                }

                            }

                            orderAct.confirmMoney(data, ({ msg }) => {
                                layer.msg(msg, function() {
                                    location.href = location.href;
                                });

                            }, ({ msg }) => {
                                layer.msg(msg);

                            });

                        },
                        rules: {

                        },
                        message: {

                        }
                    });
                }
            });

        });


    }
}

module.exports = order;
