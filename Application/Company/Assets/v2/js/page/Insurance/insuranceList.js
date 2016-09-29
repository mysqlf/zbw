require('plug/selectordie/index');
require('plug/validate/validate');
let icheck = require('plug/icheck/index'),
    { payFrom, cancelUrl, subUrl, itemUrl } = require('api/Insurance'),
    { dateRange, countDown } = require('modules/date');
let insuranceList = {
    init() {
        let self = this;
        self.validate();
        self.payNow(); //立刻支付
        self.cancel();
        $('.select').selectOrDie();
        icheck.init().checkAll();
        $(".timepicker").datetimepicker({
            format: 'yyyy-mm',
            weekStart: 1,
            autoclose: true,
            startView: 3,
            minView: 3,
            forceParse: false,
            language: 'zh-CN',
            /*  startDate: startDate,*/
            // endDate: new Date()
        })

        $('#timepicker-begin').on('changeDate', function(ev) {
            $('#timepicker-end').datetimepicker('setStartDate', ev.date);
        });
        $('#timepicker-end').on('changeDate', function(ev) {
            $('#timepicker-begin').datetimepicker('setEndDate', ev.date);
        });
        self.sub();
        self.timeOver();
    },
    validate() {
        let $form = $("#listForm");

        $("#submitBtn").click(function() {
            $form.submit();
        });

        $form.validate({
            rules: {
                personName: {
                    maxlength: 20,
                    letterCn: true
                },
                cardNum: {
                    isIdCard: true
                }
            },
            messages: {
                personName: {
                    maxlength: "长度只能在1-20个字符之间",
                    letterCn: "联系人姓名只能由中文和英文组成",
                },
                cardNum: {
                    isIdCard: '请输入正确的身份证格式'
                }
            }
        })
    },
    payNow() {
        $('#J_btn-pay').click(function() {
            let dataJson = $('#payList').serialize();
            if (dataJson !== "") {
                payFrom(dataJson, ({ info, url }) => {
                    layer.alert(info, {
                        yes() {
                            if (url) {
                                window.location.href = url
                            }
                        }
                    });
                }, ({ info }) => {
                    if (info) {
                        layer.alert(info);
                    }
                })
            }
        });
    },
    //社保公积金撤销
    cancel() {
        $('.cancel_btn').click(function() {
            let $this = $(this),
                baseId = $this.data('baseid'),
                id = $this.data('id');
            layer.confirm("是否撤销？", {
                yes() {
                    cancelUrl({ baseId, id }, (data) => {
                        location.href = location.href;
                    }, ({ info }) => {
                        if (info) {
                            layer.alert(info);
                        }
                    })
                },
                cancel() {
                    layer.closeAll();
                }
            })

        })
    },
    //我的参保人报减
    sub() {
        $('.sub_btn').click(function() {
            let $this = $(this),
                baseId = $this.data('baseid');

            itemUrl({ baseId: baseId }, (data) => {
                    let result = data.result,
                        socIsCancel = 0,
                        proIsCancel = 0;
                    if (result['1']) {
                        socIsCancel = 1
                    }
                    if (result['2']) {
                        proIsCancel = 1
                    }
                    layer.confirm("是否报减", {
                        yes() {
                            subUrl({ socIsCancel: socIsCancel, proIsCancel: proIsCancel, baseId: baseId }, (data) => {
                                location.href = location.href;
                            }, ({ info }) => {
                                if (info) {
                                    layer.alert(info);
                                }
                            })
                        },
                        cancel() {
                            layer.closeAll();
                        }
                    })
                }, ({ info }) => {
                    if (info) {
                        layer.alert(info);
                    }
                })
                /**/
        });
    },
    timeOver() {
        dateRange('create', 'pay');

        $('.pay-deadline').each(function() {
            let $this = $(this),
                value = $this.text().trim(),
                end = new Date(value),
                text = '',
                { timer } = $this.data();

            if (value == '/') {
                $this.html(`/`);

            } else {
                if (end.getTime() < new Date().getTime()) {
                    $this.html(`已截止`);

                } else {
                    timer = setInterval(function() {
                        let ret = countDown(end),
                            {
                                dd,
                                hh,
                                mm,
                                ss
                            } = ret;


                        if (!ret || end.getTime() < new Date().getTime()) {
                            clearInterval(timer);
                        }

                        $this.html(`${dd}天${hh}小时${mm}分${ss}秒`)
                    }, 1000)

                    $this.data('timer', timer);

                }

            }

        });
    }

}
module.exports = insuranceList;
