require('plug/selectordie/index'); //下拉框插件
require('plug/datetimepicker/index'); //时间选择插件
require('plug/city-picker/city-picker'); //城市选择插件

let { iCheck } = require('plug/icheck/index'); //check插件

let { modifyRules } = require('plug/validate/index'); //验证插件
let tpl = require('plug/artTemplate'), //模板引擎插件
    staffHead = require('tpl/staff_head.vue'), //表单头部模板
    uploader = require('modules/upload'), //图片上传插件
    {
        submitFrom,
        vipUrl,
        locationUrl,
        socUrl,
        proUrl,
        costUrl,
        idCard,
        editFrom,
        getServiceInsuranceDetail,
        operateInsuranceOrder
    } = require('api/insurance'); //API文件

let { isFormChanged } = require('modules/util')

let toIncrease = {
    init() {
        let self = this;

        self.validate(); //验证表单
        self.radio(); //单选框
        self.edit(); //编辑页面的初始化
        self.showGjj(); //公积金栏目显示
        self.payMsg(true);
        self.selectChange(); //下拉框去除错误提示
        self.vipSelect(); //会员套餐选择参保地
        self.locationSelect(); //参保地请求
        self.serviceSelect(); //社保类型请求
        self.projectSelect(); //项目类型显示社保基数
        self.showTable(); //缴费信息table
        self.idCard(); //输入身份证号码带入信息
        self.choiceCity(); //户口所在地选择
        $('.select').selectOrDie(); //下拉框美化
        self.eachInput(); //遍历input
        self.imgLoad(); //图片上传

        self.handleLayer();
        self.proProject();

        if($('#project').val()) {
            // 有数据时候 更新规则
            self.updateRules();
        }
    },
    handleLayer() {
        let feedbackTel = require('tpl/handle_feedback.vue'),
            self = this;

        $('[data-act="handle-layer"]').click(function(event) {
            let $this = $(this),
                { flag } = $this.data(),
                $form = null;

            if (flag) return;

            $this.data('flag', true);

            let socPiiId = $('#socPiiId').val(),
                proPiiId = $('#proPiiId').val();

            getServiceInsuranceDetail({
                socPiiId,
                proPiiId
            }, (data) => {

                data.socPiiId = socPiiId;
                data.proPiiId = proPiiId;

                layer.open({
                    title: '办理反馈',
                    btn: ['确定', '取消'],
                    area: '686px',
                    content: tpl.render(feedbackTel.template)(data),
                    yes() {
                        $form.submit();
                    },
                    success() {

                        iCheck();
                        feedbackTel.init();

                        $form = $('#J_handle-layer-form');

                        $form.validate({
                            submitHandler(form) {
                                let data = $form.serialize();

                                operateInsuranceOrder(data, ({ msg = '办理成功' }) => {
                                    layer.msg(msg, () => {
                                        location.href = location.href;
                                    })
                                }, ({ msg = '办理失败' }) => {
                                    layer.msg(msg)
                                })
                            }
                        });

                        let result1 = data.result[1],
                            result2 = data.result[2];

                        self.handleReduce(result1, $('[name="socBuyCard"]'));
                        self.handleReduce(result2, $('[name="proBuyCard"]'));

                        let $len = $('.agency-opts').children().length;

                        if ($len === 0) {
                            $('.handle-layer-title').eq(2).remove();

                        }


                    }
                })
            }, ({ msg = '获取数据失败' }) => {
                layer.msg(msg);
            }).complete(() => {
                $this.data('flag', false);
            });

        });
    },
    userId: $('#userId').val(),
    // 办理反馈代办项动态加载（报减没有代办理项）
    handleReduce(arr, obj) {

        if(!$.isArray(arr)) return;
        for (let i = 0; i < arr.length; i++) {
            let type = arr[i].type - 0;

            if (type === 3) { // type = 3为报减状态
                obj.closest('label').remove();
            }
        }
    },
    //单选框
    radio() {
        $(".inp_radio input").click(function() {
            let $this = $(this);

            $this.parent()
                .addClass('active')
                .siblings('.inp_radio')
                .removeClass('active');
        });
    },
    //验证
    validate() {
        let self = this;

        self.validatorObj = $('#toIncreaseForm').validate({
            // debug: true
            rules: {
                personName: {
                    minlength: 2,
                    maxlength: 20,
                    letterCn: true
                },
                mobile: {
                    istelephone: true
                },
                cardNum: {
                    isIdCard: true
                },
                socAmount: {
                    number: true
                },
                proAmount: {
                    number: true
                },
                proCompanyScale: {
                    number: true
                },
                proPersonScale: {
                    number: true
                }
            },
            messages: {
                personName: {
                    minlength: "长度只能在2-20个字符之间",
                    maxlength: "长度只能在2-20个字符之间",
                    letterCn: "联系人姓名只能由中文和英文组成",
                    required: "请输入参保人姓名"
                },
                mobile: {
                    istelephone: "格式错误，请重新输入",
                    required: "请输入您的手机号码"
                },
                cardNum: {
                    required: "请输入身份证号码",
                    isIdCard: "请输入正确的身份证号码"
                }
            },
            submitHandler(form) {

                let $form = $(form),
                    dataJson = $(form).serializeArray(),
                    $submitBtn = $("#submitBtn"),
                    url = $form.attr('action'),
                    opts = url ? { url } : {};

                $submitBtn.attr('disabled', 'disable');

                if ($('#templateRuleResult').length > 0) {

                    editFrom(dataJson, ({ msg }) => {
                        // 审核操作
                        if ($submitBtn.data('acttype') == 'auditing') {
                            self.auditingLayer();
                        } else {
                            layer.alert(msg, {
                                yes: _yes,
                                cancel: _cancel
                            });
                        }

                    }, ({ msg = '提交失败' }) => {
                        layer.msg(msg)
                    }, opts).complete(() => {
                        $submitBtn.removeAttr('disabled');
                    })
                } else {
                    submitFrom(dataJson, ({ msg }) => {
                        layer.alert(msg, {
                            yes: _yes,
                            cancel: _cancel
                        });
                    }, ({ msg = '提交失败' }) => {
                        layer.msg(msg)
                    }, opts).complete(() => {
                        $submitBtn.removeAttr('disabled');
                    })
                }

                // 点击确定
                function _yes() {

                    $submitBtn.removeAttr('disabled');
                    layer.closeAll();
                    window.location.href = "/Service-Business-personList.html";
                }

                // 点击取消
                function _cancel() {
                    $submitBtn.removeAttr('disabled');
                }

            }
        });

        //空字符提示语
        $(".inp_box .text").focus(function() {
            let $this = $(this),
                val = $this.val(),
                $parent = $this.parent(),
                $error = $parent.find('.error_text');

            if (val == "") {
                $error.show()
            }
        }).change(function() {
            let $this = $(this),
                val = $this.val(),
                $parent = $this.parent(),
                $error = $parent.find('.error_text');

            if (val !== "") {
                $error.hide()
            }
        });
    },
    // 审核弹层
    auditingLayer() {
        let auditingTpl = require('tpl/auditing.vue'),
            isBuyPro = $('#isBuyPro').is(':checked'),
            socPiiId = $('#socPiiId').val(),
            proPiiId = $('#proPiiId').val(),
            $form;

        layer.open({
            title: '审核',
            content: tpl.render(auditingTpl.template)({
                isBuyPro,
                socPiiId,
                proPiiId
            }),
            area: '400px',
            btn: ['确定', '取消'],
            yes() {
                $form.submit();
            },
            success() {
                iCheck();

                $form = $('#J_auditing-layer-form');

                $form.validate({
                    submitHandler() {
                        let data = $form.serializeArray(),
                            { flag } = $form.data();

                        if (flag) {
                            return
                        }

                        $form.data('flag', true);

                        operateInsuranceOrder(data, ({ msg = '审核成功' }) => {
                            layer.msg(msg, () => {
                                location.href = location.href;

                            });
                        }, ({ msg = '审核失败' }) => {
                            layer.msg(msg);
                        }).complete(() => {
                            $form.data('flag', false);
                        })
                    }
                });
            }
        })
    },
    //下拉框
    selectChange() {
        $('.select').change(function() {
            let $this = $(this),
                val = $this.val();

            if (val !== "") {
                $this.siblings('label.error').remove();
            }
        });
    },
    //会员套餐选择参保地
    vipSelect() {
        $("#vip_select").change(function() {
            // 相关联数据清空
            $('#location option:not(:first)').remove();
            $('#location').selectOrDie('update');

            $('[name="socialType"] option:not(:first)').remove();
            $('[name="socialType"]').selectOrDie('update');

            $('[name="socialType"] option:not(:first)').remove();
            $('#project').selectOrDie('update');

            $('#proProject option').removeAttr('selected');
            $('#proProject').selectOrDie('update');

            $('#socAmount').val('');
            $('#socAmount').val('').siblings('.max_text').text('');
            $('#proAmount').val('');
            $('#proAmount').val('').siblings('.max_text').text('');
            $('#proCompanyScale').val('');
            $('#proCompanyScale').val('').siblings('.max_text').text('');
            $('#proPersonScale').val('');
            $('#proPersonScale').val('').siblings('.max_text').text('');

            let $this = $(this),
                userId = $('#userId').val(), // 导入订单 需要实时获取id
                productId = $this.val(),
                overtime = $this.find('option:selected').data('overtime');

            if ($this.val() !== "") {

                $this.closest('.inp_box')
                    .find('.max_text')
                    .html('服务有效期至' + overtime + '');

                vipUrl({ productId, userId }, (data) => {

                    let location = data.result.warranty_location,
                        html = '<option value="">请选择</option>';

                    for (let i in location) {
                        html += '<option value="' + 
                                location[i].location + 
                                '"  data-month="' + 
                                location[i].paymentMonthNum + 
                                '" data-servicePrice="' + 
                                location[i].ssServicePrice + 
                                '" data-locationid="' + 
                                location[i].warrantyLocationId + 
                                '" >' + 
                                location[i].locationValue + 
                                '</option>'
                    }

                    $('#location').html(html)
                        .selectOrDie('update');
                })
            } else {
                $this.closest('.inp_box')
                    .find('.max_text')
                    .html(' ');
            }
        });
    },
    //参保地请求
    locationSelect() {
        let self = this;

        $("#location").change(function() {
            // 相关联数据清空
            $('[name="socialType"] option:not(:first)').remove();
            $('[name="socialType"]').selectOrDie('update');

            $('#project option:not(:first)').remove();
            $('#project').selectOrDie('update');

            $('#proProject option').removeAttr('selected');
            $('#proProject').selectOrDie('update');

            $('#socAmount').val('');
            $('#socAmount').val('').siblings('.max_text').text('');
            $('#proAmount').val('');
            $('#proAmount').val('').siblings('.max_text').text('');
            $('#proCompanyScale').val('');
            $('#proCompanyScale').val('').siblings('.max_text').text('');
            $('#proPersonScale').val('');
            $('#proPersonScale').val('').siblings('.max_text').text('');

            let $this = $(this),
                $selected = $this.find('option:selected'),
                val1 = $("#vip_select").val(),
                val2 = $this.val(),
                userId = toIncrease.userId;

            if (val1 !== "" && val2 !== "") {
                locationUrl({ location: val2, userId }, (data) => {
                    let result1 = data.result,
                        result2 = result1.result[1],
                        html = "";

                    $("#staff_msg").show();
                    $('#templateId').val(result1.template_id);

                    self.requeiredGjj()

                    if (result2 !== null) {
                        for (let i = 0, len = result2.length; i < len; i++) {
                            let optionHtml = "";

                            for (let j = 0, len2 = result2[i]._child.length; j < len2; j++ ) {
                                optionHtml += `<option value="${result2[i]._child[j].id}">${result2[i]._child[j].name}</option>`
                            }

                            html += `<div class="inp_box select_box">
                                        <label class="inp_tit">${result2[i].name}：<i class="inp_tip">*</i></label>
                                        <div class="inline-block ipt-box">
                                            <select class="select select_socialType" name="socialType" required>
                                                <option value="">请选择</option>
                                                ${optionHtml}
                                            </select>
                                        </div>
                                    </div>
                                    `;
                        }

                        $('#socialType').html(html);
                        $('.select_socialType').selectOrDie();

                    }

                    $('#socialType').trigger('change');
                    $('.select_socialType').trigger('change');

                }, ({ msg }) => {
                    if (msg) {
                        layer.msg(msg);
                    }

                });
            } else {
                $("#staff_msg").hide();
            }
            if (val2 == "") {
                $this.closest('.inp_box').find('.max_text').html('');
            }

        });
    },
    // 当选择规则时候 修改对应的规则
    updateRules(){
         let self = this,
            $this = $('#project'),
            $selected = $this.find('option:selected'),
            socRuleId = $selected.val(),
            $socAmount = $('#socAmount'),
            $socPayDate = $('#socPayDate'),
            $proPayDate = $('#proPayDate'),
            {
                minamount: minAmount,
                maxamount: maxAmount
            } = $selected.data();

        if ($this.val() !== "") {
            $socAmount
                .next('.max_text')
                .html('基数范围 ' + minAmount + ' 到 ' + maxAmount + '');

            modifyRules(toIncrease.validatorObj, {
                rules: {
                    socAmount: {
                        number: true,
                        min: minAmount,
                        max: maxAmount
                    }
                },
                messages: {
                    socAmount: {
                        min: '基数范围' + minAmount + '到 ' + maxAmount + '',
                        max: '基数范围' + minAmount + '到 ' + maxAmount + ''
                    }
                }
            });
            $socAmount.removeAttr('readonly');
            $('#socRuleId').val(socRuleId);
            self.updateDateRules();
        } else {
            $socAmount
                .next('.max_text')
                .html('')
                .attr('readonly', 'readonly');
            $socPayDate.prop('disabled', true).siblings('.max_text').text('');
            $proPayDate.prop('disabled', true).siblings('.max_text').text('');
        }
    },
    // 更新日期
    updateDateRules(){
        let $locationSelected = $('#project').find('option:selected'),
            startDate = new Date($locationSelected.data('min')),
            endDate = new Date($locationSelected.data('max')),
            proStartDate = new Date($locationSelected.data('minpro')),
            proEndDate = new Date($locationSelected.data('maxpro')),
            month = $locationSelected.data('month'),
            deadline = $locationSelected.data('date'),
            deadline2 = $locationSelected.data('prodate'),
            $socPayDate = $('#socPayDate'),
            $proPayDate = $('#proPayDate');

        // 服务商在保状态  不能修改起缴日期
        if(!$socPayDate.data('disabled')) {
            $socPayDate.prop('disabled', false)
                .siblings('.max_text')
                .html('该地区报增截止日为每月' + deadline + '号');
        }

        if(!$proPayDate.data('disabled')) {
            $proPayDate.prop('disabled', false)
                .siblings('.max_text')
                .html('该地区报增截止日为每月' + deadline2 + '号');
        }

        if( $socPayDate.data('datetimepicker')) {
            $socPayDate.datetimepicker('setStartDate', startDate)
            .datetimepicker('setEndDate', endDate);
        } else {
            $socPayDate.datetimepicker({
                format: 'yyyy-mm',
                weekStart: 1,
                autoclose: true,
                startView: 3,
                minView: 3,
                forceParse: false,
                language: 'zh-CN',
                startDate: startDate,
                endDate: endDate
            })
        }

        if( $proPayDate.data('datetimepicker')) {
            $proPayDate.datetimepicker('setStartDate', proStartDate)
            .datetimepicker('setEndDate', proEndDate);
        } else {
            $proPayDate.datetimepicker({
                format: 'yyyy-mm',
                weekStart: 1,
                autoclose: true,
                startView: 3,
                minView: 3,
                forceParse: false,
                language: 'zh-CN',
                startDate: proStartDate,
                endDate: proEndDate
            })
        }
    },
    //社保类型请求
    serviceSelect() {
        $('#socialType').on('change', '.select_socialType', function() {
            // 相关联数据清空
            $('#project option:not(:first)').remove();
            $('#project').selectOrDie('update').trigger('change');

            $('#proProject option').removeAttr('selected');
            $('#proProject').selectOrDie('update');

            $('#socAmount').val('').siblings('.max_text').text('');

            $('#proAmount').val('').siblings('.max_text').text('');

            $('#proCompanyScale').val('').siblings('.max_text').text('');

            $('#proPersonScale').val('').siblings('.max_text').text('');

            let val = $(this).val(),
                userId = toIncrease.userId,
                templateId = $('#templateId').val(),
                companyId = $('#vip_select').find('option:selected').data('companyid');

            if (val !== "") {
                let dataJson = {
                    'type': 1,
                    'templateId': templateId,
                    'companyId': companyId,
                    'classifyMixed[]': val,
                    userId
                }

                socUrl(dataJson, (data) => {
                    let result = data.result,
                        html = '<option value="">请选择</option>';

                    for (let i = 0, len = result.length; i < len; i++ ) {
                        let perRet = result[i];

                        html += `<option value="${result[i].id}" 
                        data-date="${perRet.deadlineArray['1']}" 
                        data-prodate="${perRet.deadlineArray['2']}" 
                        data-minamount="${perRet.minAmount}" 
                        data-maxamount="${perRet.maxAmount}" 
                        data-minpro="${perRet.minPaymentMonth['2']}" 
                        data-maxpro="${perRet.maxPaymentMonth['2']}" 
                        data-min="${perRet.minPaymentMonth['1']}" 
                        data-max="${perRet.maxPaymentMonth['1']}" 
                        data-month="${perRet.paymentMonthNum}" 
                        >${perRet.name}</option>`
                    }
                    $('#project').html(html).selectOrDie('update');
                }, ({ msg }) => {
                    let html = '<option value="">请选择</option>';

                    $('#project').html(html).selectOrDie('update');

                    if (msg) {
                        layer.msg(msg);
                    }

                })
            }
        });
    },
    //项目类型显示社保基数
    projectSelect() {
        let self = this,
            $socPayDate = $('#socPayDate'),
            $proPayDate = $('#proPayDate');

        $('#project').change(function() {
            // 相关联数据清空
            $('#proProject option').removeAttr('selected');
            $('#proProject').selectOrDie('update');

            $('#socAmount').val('').siblings('.max_text').text('');
            $('#proAmount').val('').siblings('.max_text').text('');
            $('#proCompanyScale').val('').siblings('.max_text').text('');
            $('#proPersonScale').val('').siblings('.max_text').text('');

            // 服务商在保状态  不能修改起缴日期
            if($socPayDate.data('disabled')) {
                $socPayDate.prop('disabled', true)
            } else {
                $socPayDate.val('');
            }

            if($proPayDate.data('disabled')) {
                $proPayDate.prop('disabled', true)
            } else {
                $proPayDate.val('');
            }



            self.updateRules();
        });
    },
    //公积金栏目
    showGjj() {
        let $showBtn = $('#isBuyPro');

        iCheck({
            el: $showBtn
        });

        $showBtn.on('ifChanged', (event) => {
            let $con_gjj = $('#con_gjj'),
                $ignore = $con_gjj.find('.text,.select');

            $con_gjj.toggle();

            if($('#isBuyPro').is(':checked')){
                $ignore.removeClass('ignore');
            } else {
                $ignore.addClass('ignore');
            }
            this.payMsg();
            $('.bottom_btn').removeClass('hasTable').html('缴费详情');
        });
    },
    //公积金添加验证规则
    requeiredGjj() {
        let $con_gjj = $('#con_gjj'),
            $ignore = $con_gjj.find('.text,.select');

        // if ($con_gjj.is(':visible')) {

            let templateId = $('#templateId').val(),
                userId = toIncrease.userId,
                companyId = $('#vip_select option:selected').data('companyid'),
                dataJson = {
                    'type': 2,
                    'templateId': templateId,
                    userId,
                    'companyId': companyId
                };

            proUrl(dataJson, (data) => {

                let result = data.result,
                    html = '<option value="">请选择</option>';

                for (let i = 0, len = result.length; i < len; i++) {
                    html += `<option data-minamount="${result[i].minAmount}" 
                                     data-maxamount="${result[i].maxAmount}" 
                                     data-id="${result[i].id}" 
                                     data-personscale="${result[i].personScale}" 
                                     data-companyscale="${result[i].companyScale}" >
                            ${result[i].name}
                            </option>`
                }

                $('#proProject').html(html).selectOrDie('update');

            }, ({ msg }) => {
                if (msg) {
                    layer.msg(msg);
                }

            });

        //     $ignore.removeClass('ignore');
        // } else {
        //     $ignore.addClass('ignore');
        // }

        toIncrease.payMsg();
    },
    //公积金类型
    proProject() {
        $('body').on('change', '#proProject', function() {
            // 相关联数据清空
            $('#proAmount').val('');
            $('#proAmount').val('').siblings('.max_text').text('');
            $('#proCompanyScale').val('');
            $('#proCompanyScale').val('').siblings('.max_text').text('');
            $('#proPersonScale').val('');
            $('#proPersonScale').val('').siblings('.max_text').text('');
            let $this = $(this),
                $selected = $this.find('option:selected'),
                minAmount = parseInt($selected.data('minamount')),
                maxAmount = parseInt($selected.data('maxamount')),
                {
                    companyscale: companyScale,
                    personscale: personScale,
                    id: proRuleId

                } = $selected.data();


            $this.find('option:selected')
                .attr('selected', 'selected')
                .siblings('option')
                .removeAttr('selected');

            let proAmountHtml = '',
                proCompanyScaleHtml = '',
                proPersonScaleHtml = '',
                proRuleIdHtml = '';

            if ($this.val() !== "") {
                proAmountHtml = '基数范围' + minAmount + '到 ' + maxAmount + '';
                proCompanyScaleHtml = '比例为' + companyScale + '';
                proPersonScaleHtml = '比例为' + personScale + '';
                proRuleIdHtml = proRuleId;

                modifyRules(toIncrease.validatorObj, {
                    rules: {
                        proCompanyScale: {
                            rangeScale: companyScale,
                            number: true
                        },
                        proPersonScale: {
                            rangeScale: personScale,
                            number: true
                        },
                        proAmount: {
                            number: true,
                            min: minAmount,
                            max: maxAmount
                        }

                    },
                    messages: {
                        proAmount: {
                            min: '基数范围' + minAmount + '到 ' + maxAmount + '',
                            max: '基数范围' + minAmount + '到 ' + maxAmount + ''
                        }
                    }
                })
            }

            $('#proAmount').next('.max_text').html(proAmountHtml);
            $('#proCompanyScale').siblings('.max_text').html(proCompanyScaleHtml);
            $('#proPersonScale').siblings('.max_text').html(proPersonScaleHtml);
            $('#proRuleId').val(proRuleIdHtml);
        });
    },

    //遍历显示缴费信息
    eachInput() {
        let $form = $("#required_msg");

        $form.find('.text,.select').change(function() {
            // 延迟处理  否则出现未验证完就执行
            setTimeout(()=>{
                toIncrease.payMsg();
            })
        });
    },

    /**
     * 判断缴费信息是否出现
     * @param  {Boolean} isFirst 页面加载调用
     * @return {[type]}          [description]
     */
    payMsg(isFirst) {
        let flag = false,
            self = this,
            $form = $("#required_msg"),
            socPiiId = $('#socPiiId').val(),
            proPiiId = $('#proPiiId').val();

        // 如果非编辑页和新增页 直接发送请求
        if (isFirst && (socPiiId || proPiiId)) {
            flag = false;
        } else {
            $form.find('.text,.select').not('.ignore').each(function(index, el) {
                let $this = $(this),
                    val = $this.val();

                if (val == "" || $this.hasClass('error')) {
                    flag = true;
                    return false;
                }
            });
        }

        if (!flag) {
            let templateId = $('#templateId').val(),
                socRuleId = $('#socRuleId').val(),
                proRuleId = $('#proRuleId').val(),
                socAmount = $('#socAmount').val(),
                //socCardno = $('#socCardno').val(),
                ////proCardno = $('#proCardno').val(),
                proAmount = $('#proAmount').val(),
                proPersonScale = $('#proPersonScale').val(),
                proCompanyScale = $('#proCompanyScale').val(),
                socPayMonth = $('#socPayDate').val(),
                warrantyLocationId = $('#location').find('option:selected').data('locationid'),
                proPayMonth = $('#proPayDate').val(),
                userId = toIncrease.userId,
                dataJson = {
                    'type': 1,
                    userId,
                    templateId,
                    warrantyLocationId,
                    socRuleId,
                    socAmount,
                    'socMonthNum': 1,
                    socPayMonth
                };

            // 第一次进入页面 数据是用户以前填写的数据 非计算出来的数据 需要添加额外的参数 
            if (isFirst) {
                ['baseId', 'socPiiId', 'proPiiId'].forEach((item, index) => {
                    dataJson[item] = $('#' + item).val();
                });
            }

            if ($('#isBuyPro').is(':checked')) {
                $.extend(dataJson, {
                    proRuleId,
                    //'socCardno':socCardno,
                    proAmount,
                    'proMonthNum': 1,
                    proPayMonth,
                    proPersonScale,
                    proCompanyScale
                    //'proCardno':proCardno
                })
            }

            costUrl(dataJson, (data) => {
                let result = data.result,
                    companyPrice = parseFloat(result.companyCost).toFixed(2),
                    personPrice = parseFloat(result.personCost).toFixed(2),
                    servicePrice = parseFloat(result.servicePrice).toFixed(2),
                    totalPrice = parseFloat(result.totalCost).toFixed(2),
                    $table_box = $('#table_box'),
                    $staffdate = $('#staffdate'),
                    dataResult = result.data || [],
                    monthResult = Object.keys(dataResult);

                $("#msg_staff_box").hide();
                $('#companyPrice').html(companyPrice);
                $('#personPrice').html(personPrice);
                $('#servicePrice').html(servicePrice);
                $('#totalPrice').html(totalPrice);
                $("#msg_staff").show();

                $staffdate.html((tpl.render(staffHead.template)({ monthResult })));
                $table_box.find('tbody tr:odd').find('td').addClass('even_bg');
                self.calculateData = data.result.data;
                staffHead.init(self);

                for (let i in dataResult) {
                    for (let j in dataResult[i]) {
                        if (typeof dataResult[i][j] === 'object' && dataResult[i][j].isHandleException === false) {
                            $('#J_abnormal').remove();

                        }
                    }
                }

            }, ({ msg }) => {
                if (msg) {
                    layer.msg(msg);
                }
            });
        } else {
            $("#msg_staff").hide();
        }
    },
    //缴费信息table
    showTable() {

        $('.bottom_btn').click(function() {
            let $this = $(this),
                $msg_staff_box = $("#msg_staff_box");

            if ($this.hasClass('hasTable')) {
                $this.html('缴费详情').removeClass('hasTable');
                $msg_staff_box.hide();
            } else {
                $this.html('收起详情').addClass('hasTable');
                $msg_staff_box.show();
            }
        });
    },
    //户口所在地选择
    choiceCity() {
        let $cityPicker = $('.city-picker-input'),
            $cityInput = $cityPicker.find('input');

        $cityPicker.cityPicker({
            end: 'city',
            change: function(el, vals) {
                el.find('label.error').remove()
            }
        });
    },
    //输入身份证号码带入信息
    idCard() {
        let self = this;

        $("#cardNum").change(function() {
            if (self.validatorObj.element(this)) {

                let cardNum = $(this).val(),
                    userId = toIncrease.userId;

                idCard({ cardNum, userId }, (data) => {

                    let $cityPicker = $('.city-picker-input'),
                        result = data.result;
                    if (result == null) {
                        return;
                    }

                    $("#useName").val(result.person_name);
                    $("#mobile").val(result.mobile);

                    $(".residenceType").each(function() {
                        let $this = $(this),
                            val = $this.val();
                        if (val == result.residence_type) {
                            $this.attr('checked', 'checked')
                                .parent()
                                .addClass('active')
                                .siblings()
                                .removeClass('active')
                                .find('.residenceType')
                                .removeAttr('checked');
                        }
                    });

                    $("#residenceLocation").val(result.residence_location);
                    $cityPicker.cityPicker('update');
                })
            }

        });
    },
    //图片上传
    imgLoad() {

        if (!uploader.webuploader.Uploader.support()) {

            $('.upload_btn.new_btn').click(function() {
                layer.alert('您的浏览器暂不支持上传功能！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
            })

            return;
        }

        //初始化图片上传
        let opts = {
                //传入的值
                formData: { uploadType: 1 }
            },
            optsTwo = {
                // 内部根据当前运行是创建，可能是input元素，也可能是flash.
                pick: {
                    id: '#filePicker2',
                },
                //传入的值
                formData: { uploadType: 2 }
            },
            frontUpload = uploader.uploadCreate(opts),
            reverseUplod = uploader.uploadCreate(optsTwo);

        //错误验证
        uploader.uploadError(frontUpload);
        uploader.uploadError(reverseUplod);
        //上传成功后
        uploader.uploadSuccess(frontUpload, opts, $('#idCardFrontFile'));
        uploader.uploadSuccess(reverseUplod, optsTwo, $('#idCardBackFile'));
    },
    //编辑页面初始化
    edit() {
        let self = this;

        if ($('#templateRuleResult').length > 0) {
            let overTime = $('#vip_select').find('option:selected').data('overtime'),
                {
                    date: maxDate,
                    datepro: maxProDate,
                    min: startDate,
                    max: endDate,
                    minpro: proStartDate,
                    maxpro: proEndDate,
                    month
                } = $('#location').find('option:selected').data(),
                {
                    minamount: minAmount,
                    maxamount: maxAmount
                } = $('#project').find('option:selected').data();

            startDate = new Date(startDate);
            endDate = new Date(endDate);
            proStartDate = new Date(proStartDate);
            proEndDate = new Date(proEndDate);


            if (overTime && overTime !== "") {
                $('#vip_select')
                    .closest('.select_box')
                    .find('.max_text')
                    .html('服务有效期至' + overTime + '');
            }

            if (maxDate && maxDate !== "") {
                $('#socPayDate')
                    .closest('.inp_box')
                    .find('.max_text')
                    .html('该地区报增截止日为每月' + maxDate + '号');
            }


            if (minAmount && maxAmount) {
                $('#socAmount')
                    .closest('.inp_box')
                    .find('.max_text')
                    .html('基数范围 ' + minAmount + ' 到' + maxAmount + '');
            }

            modifyRules(toIncrease.validatorObj, {
                rules: {
                    socAmount: {
                        number: true,
                        min: minAmount,
                        max: maxAmount
                    }
                },
                messages: {
                    socAmount: {
                        min: '基数范围' + minAmount + '到 ' + maxAmount + '',
                        max: '基数范围' + minAmount + '到 ' + maxAmount + ''
                    }
                }
            });

            if (startDate && endDate) {

                $("#socPayDate").datetimepicker({
                        format: 'yyyy-mm',
                        weekStart: 1,
                        autoclose: true,
                        startView: 3,
                        minView: 3,
                        forceParse: false,
                        language: 'zh-CN',
                        startDate: startDate,
                        endDate: endDate
                    }).datetimepicker('setStartDate', startDate)
                    .datetimepicker('setEndDate', endDate);
            }

            if (proStartDate && proEndDate) {

                $("#proPayDate").datetimepicker({
                        format: 'yyyy-mm',
                        weekStart: 1,
                        autoclose: true,
                        startView: 3,
                        minView: 3,
                        forceParse: false,
                        language: 'zh-CN',
                        startDate: proStartDate,
                        endDate: proEndDate
                    }).datetimepicker('setStartDate', proStartDate)
                    .datetimepicker('setEndDate', proEndDate)
                    .siblings('.max_text')
                    .html('该地区报增截止日为每月' + maxProDate + '号');
            }
            if ($('#isBuyPro').is(':checked')) {

                $('#con_gjj').show()
                    .find('.text,.select')
                    .removeClass('ignore');

                let {
                    minamount: minAmount,
                    maxamount: maxAmount,
                    companyscale: CompanyScale,
                    personscale: PersonScale,
                    id: proRuleId
                } = $('#proProject').find('option:selected').data();


                $('#proAmount')
                    .closest('.inp_box')
                    .find('.max_text')
                    .html('基数范围' + minAmount + '到 ' + maxAmount + '');

                $('#proCompanyScale')
                    .closest('.inp_box')
                    .find('.max_text')
                    .html('比例为' + CompanyScale + '');

                $('#proPersonScale')
                    .closest('.inp_box')
                    .find('.max_text')
                    .html('比例为' + PersonScale + '');

                $('#proRuleId').val(proRuleId);

                modifyRules(toIncrease.validatorObj, {

                    rules: {
                        proAmount: {
                            number: true,
                            min: minAmount,
                            max: maxAmount
                        },
                        proCompanyScale: {
                            rangeScale: CompanyScale,
                            number: true
                        },
                        proPersonScale: {
                            rangeScale: PersonScale,
                            number: true
                        }
                    },
                    messages: {
                        proAmount: {
                            min: '基数范围' + minAmount + '到 ' + maxAmount + '',
                            max: '基数范围' + minAmount + '到 ' + maxAmount + ''
                        }
                    }
                });

                /*toIncrease.proProject();*/
            } else {
                $('#con_gjj').hide();
            }
            //身份证图片初始化
            let idCardFrontFile = $('#idCardFrontFile').val(),
                idCardBackFile = $('#idCardBackFile').val(),
                domId = '';

            if (idCardFrontFile !== "") {

                $('#filePicker')
                    .addClass('new_btn')
                    .find('.icon-addImg')
                    .hide()
                    .parent()
                    .append('<img src="' + idCardFrontFile + '">');
            }
            if (idCardBackFile !== "") {

                $('#filePicker2')
                    .addClass('new_btn')
                    .find('.icon-addImg')
                    .hide()
                    .parent()
                    .append('<img src="' + idCardBackFile + '">');
            }
        }

    }
}

module.exports = toIncrease;
