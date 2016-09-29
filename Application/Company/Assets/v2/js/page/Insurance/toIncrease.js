require('plug/selectordie/index'); //下拉框插件
require('plug/datetimepicker/datetimepicker'); //时间选择插件
require('plug/city-picker/city-picker'); //城市选择插件
require('plug/icheck/icheck'); //check插件

let { modifyRules } = require('plug/validate/validate'); //验证插件
let tpl = require('plug/artTemplate'), //模板引擎插件
    staffTable = require('tpl/staff-table.vue'), //表单模板
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
        editFrom
    } = require('api/Insurance'); //API文件

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
        self.proProject();
    },
    //单选框
    radio() {
        $(".inp_radio input").click(function() {
            let $this = $(this);
            $this.parent().addClass('active');
            $this.parent().siblings('.inp_radio').removeClass('active');
        });
    },

    //验证
    validate() {
        $("#submitBtn").click(function() {
            $('#toIncreaseForm').submit();
        });

        this.validatorObj = $('#toIncreaseForm').validate({
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
            errorPlacement(error, element) {
                if (element.closest('.inp_radio').length) {
                    error.appendTo(element.closest('.inp_radio').parent());
                } else {
                    error.appendTo(element.parent());
                }

            },
            submitHandler(form) {
                let dataJson = $(form).serializeArray();
                $("#submitBtn").attr('disabled', 'disable');
                if ($('#templateRuleResult').length > 0) {
                    editFrom(dataJson, ({ info }) => {

                        layer.alert(info, {
                            yes() {
                                window.location.href = '/Company-Insurance-insuranceOrderList-type-0-state-1.html';
                            }
                        });

                    }, ({ info }) => {
                        layer.msg(info)

                    }).complete(()=>{
                        $("#submitBtn").removeAttr('disabled');
                    })
                } else {
                    submitFrom(dataJson, ({ info }) => {

                        layer.alert(info, {
                            yes() {
                                window.location.href = '/Company-Insurance-insuranceOrderList-type-0-state-1.html';
                            }
                        });

                    }, ({ info }) => {
                        layer.msg(info)
                    }).complete(()=>{
                        $("#submitBtn").removeAttr('disabled');
                    })
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
    //下拉框
    selectChange() {
        $('.select').change(function() {
            let $this = $(this),
                val = $this.val();
            if (val !== "") {
                $this.removeClass('error').siblings('label.error').remove();
            }
        });
    },
    //会员套餐选择参保地
    vipSelect() {
        $("#vip_select").change(function() {
            // 表格相关联的值需清空
            $('[name="location"] option').removeAttr('selected');
            $('[name="location"]').selectOrDie('update');

            $('[name="socialType"] option').removeAttr('selected');
            $('[name="socialType"]').selectOrDie('update');

            $('[name="project"] option').removeAttr('selected');
            $('[name="project"]').selectOrDie('update');

            $('[name="proProject"] option').removeAttr('selected');
            $('[name="proProject"]').selectOrDie('update');

            $('#proAmount').val('');
            $('#proCompanyScale').val('');
            $('#proPersonScale').val('');

            let $this = $(this),
                productId = $this.val(),
                overtime = $this.find('option:selected').data('overtime');
            if ($this.val() !== "") {
                $this.closest('.inp_box').find('.max_text').html('服务有效期至' + overtime + '');
                vipUrl({ productId: productId }, (data) => {

                    let location = data.result.warranty_location,
                        html = '<option value="">请选择</option>';
                    for (let i in location) {
                        html += '<option value="' + location[i].location + '" data-minpro="' + location[i].minPaymentMonth['2'] + '" data-maxpro="' + location[i].maxPaymentMonth['2'] + '" data-date="' + location[i].deadline['1'] + '"data-min="' + location[i].minPaymentMonth['1'] + '" data-max="' + location[i].maxPaymentMonth['1'] + '" data-month="' + location[i].paymentMonthNum + '" data-servicePrice="' + location[i].ssServicePrice + '" data-locationid="' + location[i].warrantyLocationId + '" data-prodate="' + location[i].deadline['2'] + '">' + location[i].locationValue + '</option>'
                    }
                    $('#location').html(html).selectOrDie('update');

                })
            } else {
                $this.closest('.inp_box').find('.max_text').html('');
            }
        });
    },
    //参保地请求
    locationSelect() {
        let self = this;

        $("#location").change(function() {
            $('[name="project"] option').removeAttr('selected');
            $('[name="project"]').selectOrDie('update');

            $('[name="proProject"] option').removeAttr('selected');
            $('[name="proProject"]').selectOrDie('update');

            $('#proAmount').val('');
            $('#proCompanyScale').val('');
            $('#proPersonScale').val('');

            let $this = $(this),
                val1 = $("#vip_select").val(),
                val2 = $this.val(),
                deadline = $this.find('option:selected').data('date'),
                deadline2 = $this.find('option:selected').data('prodate');
            if (val1 !== "" && val2 !== "") {
                locationUrl({ location: val2 }, (data) => {

                    $("#staff_msg").show();
                    let result1 = data.result,
                        result2 = result1.result[1];
                    $('#templateId').val(result1.template_id);
                    let html = "";

                    self.requeiredGjj();

                    if (result2 !== null) {
                        for (let i = 0, len = result2.length; i < len; i++) {
                            let optionHtml = "";

                            for (let j = 0, len2 = result2[i]._child.length; j < len2; j++) {
                                optionHtml += `<option value="${result2[i]._child[j].id}">${result2[i]._child[j].name}</option>`
                            }

                            html += `<div class="inp_box select_box">
                                        <label class="inp_tit">${result2[i].name}：<i class="inp_tip">*</i></label>
                                        <select class="select select_socialType" name="socialType" required>
                                        <option value="">请选择</option>
                                        ${optionHtml}
                                        </select>
                                        </div>
                                        `
                        }
                        $('#socialType').html(html);
                        $('.select_socialType').selectOrDie();
                        $('#socPayDate').val('');
                        let startDate = new Date($('#location').find('option:selected').data('min')),
                            endDate = new Date($('#location').find('option:selected').data('max')),
                            proStartDate = new Date($('#location').find('option:selected').data('minpro')),
                            proEndDate = new Date($('#location').find('option:selected').data('maxpro')),
                            month = $('#location').find('option:selected').data('month');
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
                        }).datetimepicker('setStartDate', startDate).datetimepicker('setEndDate', endDate).siblings('.max_text').html('该地区报增截止日为每月' + deadline + '号');

                        $('#proPayDate').datetimepicker({
                            format: 'yyyy-mm',
                            weekStart: 1,
                            autoclose: true,
                            startView: 3,
                            minView: 3,
                            forceParse: false,
                            language: 'zh-CN',
                            startDate: proStartDate,
                            endDate: proEndDate
                        }).datetimepicker('setStartDate', proStartDate).datetimepicker('setEndDate', proEndDate).siblings('.max_text').html('该地区报增截止日为每月' + deadline2 + '号');

                    }

                }, ({ info }) => {
                    if (info) {
                        layer.alert(info);
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
    //社保类型请求
    serviceSelect() {
        $('#socialType').on('change', '.select_socialType', function() {
            $('#project option').removeAttr('selected');
            $('#project').selectOrDie('update');
            $('#socAmount').val('');
            $('#proProject option').removeAttr('selected');
            $('#proProject').selectOrDie('update');
            $('#proAmount').val('');
            $('#proCompanyScale').val('');
            $('#proPersonScale').val('');
            let val = $(this).val(),
                templateId = $('#templateId').val(),
                companyId = $('#vip_select').find('option:selected').data('companyid');
            if (val !== "") {
                let dataJson = {
                    'type': 1,
                    'templateId': templateId,
                    'companyId': companyId,
                    'classifyMixed[]': val
                }
                socUrl(dataJson, (data) => {

                    let result = data.result,
                        html = '<option value="">请选择</option>';

                    for (let i = 0, len = result.length; i < len; i++) {
                        html += `<option value="${result[i].id}" data-minamount="${result[i].minAmount}" data-maxamount="${result[i].maxAmount}">${result[i].name}</option>`
                    }

                    $('#project').html(html).selectOrDie('update');

                }, ({ info }) => {
                    if (info) {
                        layer.alert(info, function(index) {
                            layer.close(index);
                            $('[name="socialType"] option').removeAttr('selected');
                            $('[name="socialType"]').selectOrDie('update');
                        });

                    }

                })
            }
        });
    },
    //项目类型显示社保基数
    projectSelect() {
        $('#project').change(function() {

            $('[name="proProject"] option').removeAttr('selected');
            $('[name="proProject"]').selectOrDie('update');

            $('#proAmount').val('');
            $('#proCompanyScale').val('');
            $('#proPersonScale').val('');

            let $this = $(this),
                socRuleId = $this.find('option:selected').val(),
                minAmount = $this.find('option:selected').data('minamount'),
                maxAmount = $this.find('option:selected').data('maxamount');
            if ($this.val() !== "") {
                $('#socAmount').next('.max_text').html('基数范围 ' + minAmount + ' 到 ' + maxAmount + '');

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
                $('#socAmount').removeAttr('readonly');
                $('#socRuleId').val(socRuleId);
            } else {
                $('#socAmount').next('.max_text').html('');
                $('#socAmount').attr('readonly', 'readonly');
            }
        });
    },
    //公积金栏目
    showGjj() {
        let $showBtn = $('#isBuyPro');
        $showBtn.iCheck({
            checkboxClass: 'check_btn'
        }).on('ifChanged', (event) => {
            let $con_gjj = $('#con_gjj'),
                $ignore = $con_gjj.find('.text,.select');

            $con_gjj.toggle();

            if ($('#isBuyPro').is(':checked')) {
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
        let templateId = $('#templateId').val(),
            companyId = $('#vip_select').find('option:selected').data('companyid'),
            dataJson = {
                'type': 2,
                'templateId': templateId,
                'companyId': companyId
            };
        proUrl(dataJson, (data) => {

            let result = data.result,
                html = '<option value="">请选择</option>';
            for (let i = 0, len = result.length; i < len; i++) {
                html += `<option data-minamount="${result[i].minAmount}" data-maxamount="${result[i].maxAmount}" data-id="${result[i].id}" data-personscale="${result[i].personScale}" data-companyscale="${result[i].companyScale}" >
                        ${result[i].name}
                        </option>`
            }

            $('#proProject').html(html).selectOrDie('update');
            /*toIncrease.proProject();*/

        }, ({ info }) => {
            if (info) {
                layer.alert(info);
            }

        });
        if ($('#con_gjj').css("display") == "block") {
            $('#con_gjj').find('.text,.select').removeClass('ignore');
            // 批量报增（#batchForm）不请求缴费信息
            if ($('form').attr('id') == 'batchForm') return;
            toIncrease.payMsg();
        } else {
            $('#con_gjj').find('.text,.select').addClass('ignore');
            // 批量报增（#batchForm）不请求缴费信息
            if ($('form').attr('id') == 'batchForm') return;
            toIncrease.payMsg();
        }
    },
    //公积金类型
    proProject() {
        $('body').on('change', '#proProject', function() {
            $('#proAmount').val('');
            $('#proCompanyScale').val('');
            $('#proPersonScale').val('');

            let $this = $(this),
                $select = $this.find('option:selected'),
                minAmount = $select.data('minamount'),
                maxAmount = $select.data('maxamount'),
                {
                    companyscale: companyScale,
                    personscale: personScale,
                    id: proRuleId
                } = $select.data();

            $this.find('option:selected')
                .attr('selected', 'selected')
                .siblings('option')
                .removeAttr('selected');

            if ($this.val() !== "") {
                $('#proAmount').next('.max_text').html('基数范围' + minAmount + '到 ' + maxAmount + '');
                $('#proCompanyScale').siblings('.max_text').html('比例为' + companyScale + '');
                $('#proPersonScale').siblings('.max_text').html('比例为' + personScale + '');
                $('#proRuleId').val(proRuleId);

                // 批量报增（#batchForm）没有规则校验
                if ($('form').attr('id') == 'batchForm') return;

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
            } else {
                $('#proAmount').next('.max_text').html('');
                $('#proCompanyScale').siblings('.max_text').html('');
                $('#proPersonScale').siblings('.max_text').html('');
            }

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
    //判断缴费信息是否出现
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
                dataJson = {
                    'type': 1,
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
                    proAmount,
                    'proMonthNum': 1,
                    proPayMonth,
                    proPersonScale,
                    proCompanyScale
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

            }, ({ info }) => {
                if (info) {
                    layer.alert(info);
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
                let cardNum = $(this).val();
                idCard({ cardNum: cardNum }, (data) => {

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
                            $this.attr('checked', 'checked').parent().addClass('active').siblings().removeClass('active').find('.residenceType').removeAttr('checked');
                        }
                    });
                    $("#residenceLocation").val(result.residence_location);
                    $cityPicker.cityPicker('update');
                }, ({ info }) => {
                    if (info) {
                        layer.alert(info);
                    }
                })
            } else {
                return;
            }

        });
    },
    //图片上传
    imgLoad() {
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
            };
        let frontUpload = uploader.uploadCreate(opts),
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
        var self = this;
        let $templateRuleResult = $('#templateRuleResult');
        if ($('#templateRuleResult').length > 0) {
            let overTime = $('#vip_select').find('option:selected').data('overtime'),
                $location = $('#location').find('option:selected'),
                $project = $('#project ').find('option:selected'),
                {
                    date: maxDate,
                    datepro: maxProDate,

                } = $location.data(),
                {
                    minamount: minAmount,
                    maxamount: maxAmount
                } = $project.data(),

                startDate = new Date($location.data('min')),
                endDate = new Date($location.data('max')),
                proStartDate = new Date($location.data('minpro')),
                proEndDate = new Date($location.data('maxpro')),
                month = $location.data('month');

            if (overTime && overTime !== "") {
                $('#vip_select').closest('.select_box').find('.max_text').html('服务有效期至' + overTime + '');
            }
            if (maxDate && maxDate !== "") {
                $('#socPayDate').closest('.inp_box').find('.max_text').html('该地区报增截止日为每月' + maxDate + '号');;
            }


            if (minAmount && maxAmount) {
                $('#socAmount').closest('.inp_box').find('.max_text').html('基数范围 ' + minAmount + ' 到' + maxAmount + '');
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
                }).datetimepicker('setStartDate', startDate).datetimepicker('setEndDate', endDate);
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
                }).datetimepicker('setStartDate', proStartDate).datetimepicker('setEndDate', proEndDate).siblings('.max_text').html('该地区报增截止日为每月' + maxProDate + '号');;
            }
            if ($('#isBuyPro').is(':checked')) {
                $('#con_gjj').show().find('.text,.select').removeClass('ignore');
                let self = this,
                    $proProject = $('#proProject').find('option:selected'),
                    {
                        minamount: minAmount,
                        maxamount: maxAmount,
                        companyscale: CompanyScale,
                        personscale: PersonScale,
                        id: proRuleId
                    } = $proProject.data();
                $('#proAmount').closest('.inp_box').find('.max_text').html('基数范围' + minAmount + '到 ' + maxAmount + '');
                $('#proCompanyScale').closest('.inp_box').find('.max_text').html('比例为' + CompanyScale + '');
                $('#proPersonScale').closest('.inp_box').find('.max_text').html('比例为' + PersonScale + '');
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
                idCardBackFile = $('#idCardBackFile').val();

            if (idCardFrontFile !== "") {
                $('#filePicker').addClass('new_btn').find('.icon-addImg').hide().parent().append('<img src="' + idCardFrontFile + '">');
            }
            if (idCardBackFile !== "") {
                $('#filePicker2').addClass('new_btn').find('.icon-addImg').hide().parent().append('<img src="' + idCardBackFile + '">');
            }
        }

    }
}
module.exports = toIncrease;
