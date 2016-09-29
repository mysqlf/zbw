// 代发工资
var template = require('art-template'),
    validateFn = require('modules/validate'),
    icheckFn = require('modules/icheck'),
    orderAct = require('modules/actions/order'),
    tool = require('lib/tool'),
    BIN = require('plug/bankcardinfo');

var payroll = {
    bankList: require('modules/bankData.js'),
    init: function() {

        var self = this,
            $payrollForm = $('#payroll-form');

        icheckFn.init().checkAll();

        // 挂起提示
        $('[data-toggle="popover"]').popover({
            container: 'body',
            placement: 'top',
            title: '提示',
            trigger: 'hover'
        });

        $(window).resize(function(event) {
            tool.fixedTableHeight();
        }).resize();

        $('body').on('change', '#account', function() {
            var $bankSelect = $('#bank-select');

            BIN.getBankBin(this.value, function(err, data) {
                if (!err) {
                    $bankSelect.val(data.bankName) /*.prop('disabled', true);*/
                }
                /* else{
                    $bankSelect.prop('disabled', false);
                }*/

            })
        })

        // 导出订单
        /*      $('[data-act="exportPayroll"]').click(function() {
                    self.exportPayroll($payrollForm.serialize())
                });*/

        //更新工资状态
        $('[data-act="updatePayrollStatus"]').click(function() {
            self.updatePayrollStatus($payrollForm.serializeArray())
        });

        //查看工资
        $('[data-act="viewPayroll"]').click(function() {
                var data = $(this).data();

                orderAct.viewPayroll(data, function(json) {

                    var html = template.render(require('tpl/payrollView.vue').template)(json.data);

                    layer.open({
                        type: 1,
                        title: '查看工资',
                        area: ['570px', 'auto'], //宽高
                        content: html
                    });
                    self.validatePayroll($('#payroll-audit-form'));
                })
            })
            // 审核通过
        $('[data-act="verifyPayroll"]').click(function() {
            var data = $(this).data();

            orderAct.viewPayroll(data, function(json) {
                var ret = json.data;

                ret.bankList = self.bankList;

                var html = template.render(require('tpl/payrollAudit.vue').template)(ret);

                layer.open({
                    type: 1,
                    title: '审核工资',
                    area: ['570px', 'auto'], //宽高
                    btn: ['确定', '取消'],
                    skin: 'layer-form',
                    content: html,
                    yes: function() {
                        var $form = $('#payroll-audit-form');
                        self.validatePayroll($form);
                        $form.submit();
                    }
                });

                self.validatePayroll($('#payroll-audit-form'));
                icheckFn.init().checkAll();
            })

        });

        // 取消审核
        $('[data-act="delPayroll"]').click(function() {
            var data = $(this).data();

            layer.confirm('确认撤销？', function() {
                orderAct.delPayroll(data, function() {
                    layer.msg('撤销成功');
                    location.reload();
                })
            })

        });


        $('body').on('change', '.J-counter', function() {
            var add = cut = 0;

            $('.J-counter-add').each(function() {
                var num = parseFloat(this.value) || 0;
                add += num;
            })

            $('.J-counter-cut').each(function() {
                var num = parseFloat(this.value) || 0;
                cut += num;
            })

            $('#wages-txt').html(add - cut)
        })
    },
    // 工资审核
    validatePayroll: function($form) {
        $form.validate({
            submitHandler: function() {
                var formData = $form.serializeArray();

                orderAct.savePayroll(formData, function() {
                    layer.msg('审核成功', {
                        end: function() {
                            location.reload();
                        }
                    });

                })

                return false;
            },
            rules: {
                wages: {
                    number: true
                },
                deduction_income_tax: {
                    number: true
                },
                deduction_social_insurance: {
                    number: true
                },
                deduction_provident_fund: {
                    number: true
                },
                replacement: {
                    number: true
                },
                deduction_order: {
                    number: true
                }
            },
            messages: {
                bank: {
                    required: '请选择银行'
                },
                branch: {
                    required: '请输入支行名称'
                },
                account: {
                    required: '请输入银行卡号'
                },
                date: {
                    required: '请输入工资年月'
                },
                wages: {
                    required: '请输入应发工资'
                },
                deduction_income_tax: {
                    required: '请输入个人所得税'
                },
                deduction_social_insurance: {
                    required: '请输入扣个人社保'
                },
                deduction_provident_fund: {
                    required: '请输入扣个人公积金'
                },
                replacement: {
                    required: '请输入补发'
                },
                deduction_order: {
                    required: '请输入其他扣除'
                },
                state: {
                    required: '请选择状态'
                }
            }
        })
    },
    // 导出工资单
    /*    exportPayroll: function(url){
            if(url === ''){
                layer.alert('请选择工资单');
            } else{
                location.href = '/Service-Service-index?'+ url;
            }
        },*/
    // 防止多次提交
    ajaxChace: {},
    /**
     * 更新工资状态
     * @param  {object}   ids id的集合
     * @param  {Function} success  成功回调
     * @param  {Function} fail 失败回调
     */
    updatePayrollStatus: function(ids, success, fail) {
        var self = this;
        var url = '/Order-comOrderDetail';

        if (!ids.length) {
            layer.alert('请选择工资单');
        } else {
            // 防止多次提交
            if (self.ajaxChace[url]) {
                return;
            }

            $.ajax({
                type: 'post',
                url: url,
                dataType: 'json',
                beforeSend: function(ajaxObj, opts) {
                    self.ajaxChace[url] = true;
                }
            }).success(function(data) {
                var status = data.status - 0;
                var msg = data.msg;

                if (status === 0) {
                    if (typeof success === 'function') {
                        success(data);
                    }
                }

                if (msg) {
                    layer.alert(msg)
                }

            }).fail(function() {
                if (typeof fail === 'function') {
                    fail();
                }
            }).complete(function() {
                self.ajaxChace[url] = false;
            })
        }
    },
    verifyPayroll: function(success) {
        var self = this;
        var url = '/Order-payrol';

        if (self.ajaxChace[url]) {
            return;
        }

        $.ajax({
            type: 'post',
            url: url,
            dataType: 'json',
            beforeSend: function(ajaxObj, opts) {
                self.ajaxChace[url] = true;
            }
        }).success(function(data) {
            var status = data.status - 0;
            var msg = data.msg;

            if (status === 0) {
                if (typeof success === 'function') {
                    success(data);
                }
            } else if (msg) {
                layer.alert(msg)
            }

        }).complete(function() {
            self.ajaxChace[url] = false;
        })
    }
}

module.exports = payroll;
