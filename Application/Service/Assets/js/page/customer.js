/*
 * 客服管理模块
 */
let template = require('art-template'),
    customerAct = require('api/customer'),
    dateFn = require('plug/datetimepicker/index'),
    checkFn = require('plug/icheck/index');

require('plug/selectordie');
require('plug/validate/index');
require('plug/city-picker/city-picker');

let customer = {
    init() {
        let self = this;

        self.setCustomerService();
        self.setServiceStatus();
        self.addServiceCity();
        self.modifyServiceList();
        self.getContract();
        self.addContract();
        self.validateContract();
        self.export();
        self.deleteServiceList();
        self.submitServiceDetail();

        dateFn.getYearMonthDay();
        $('.set-startDate').datetimepicker('setStartDate', new Date());
        checkFn.init();

        $('.setting-lb .icheck').on('ifChecked', function() {
            let $this = $(this),
                $settings_cnt = $this.closest('.setting-lb').siblings();

            if ($settings_cnt.hasClass('settings-cnt')) {
                $settings_cnt.show();
                $('.date-day').removeClass('ignore');

            } else {
                $('.settings-cnt').hide();
                $('.date-day').addClass('ignore');
            }
        });

        $('[name="is_salary"]').on('ifChecked',function(){
            $('.payroll-fee').show();
            $('[name="af_service_price"]').removeClass('ignore');
        });

        $('[name="is_salary"]').on('ifUnchecked',function(){
            $('.payroll-fee').hide();
            $('[name="af_service_price"]').addClass('ignore');
        });
    },
    // 添加切换合同操作按钮的不同状态
    state: '',
    // 设定客服
    setCustomerService() {
        let self = this;

        $('[data-act="set_service"]').click(function() {
            let $this = $(this);

            customerAct.getServiceList().success(function(json) {
                let $cnt = '';

                $cnt = template.render(require('tpl/service.vue').template)(json || {});

                layer.open({
                    title: false,
                    area: '400px',
                    type: 11,
                    content: $cnt,
                    scrollbar: false,
                    btn: ['确定', '取消'],
                    closeBtn: false,
                    yes: function() {
                        $('#set_service').submit();

                    },
                    success: function() {
                        $('select').selectOrDie();
                    }
                });

                self.validate($('#set_service'), $this);

            });


        });

    },
    // 设置服务状态
    setServiceStatus() {
        let self = this;


        $('[data-act="set_status"]').click(function() {
            let $this = $(this);

            self.state = $this.data('act');

            if ($this.data('name') == 'contract') { //添加切换合同
                $('#add_contract').submit();

            } else {

                self.getServiceStatusCommon($this);

            }

        });

    },
    // 服务状态公用模板
    getServiceStatusCommon(obj) {
        let self = this,
            days = [];

        for (let i = 1; i <= 31; i++) {
            days.push(i);
        }

        let [templateData] = [{
            daysNum: days,
            id: $('[name="ipt_hidden"]').val()
        }];

        layer.open({
            title: false,
            area: '520px',
            content: template.render(require('tpl/service_status.vue').template)(templateData),
            scrollbar: false,
            btn: ['确定', '取消'],
            yes: function() {
                $('#service_settings').submit();

            }
        });

        $("select").selectOrDie();
        self.validate($('#service_settings'), obj);

    },
    // 添加服务城市
    addServiceCity() {
        let self = this;

        $('[data-act="set_city"]').click(function() {
            let $this = $(this);
            self.state = $this.data('act');
            if ($this.data('name') == 'contract') { //添加切换合同
                $('#add_contract').submit();
            } else {
                self.addServiceCityCommon($this);
            }


        });

    },
    // 添加服务城市公用模板
    addServiceCityCommon(obj) {
        let [self, templateData, tblData] = [this, '', {}];

        if ((obj.prop('tagName')).toLowerCase() == 'a') {
            tblData = {
                location_id: obj.closest('tr').find('.location-id').text(),
                location: obj.closest('tr').find('.location').data('location'),
                soc_service_price: obj.closest('tr').find('.soc-service-price').text(),
                pro_service_price: obj.closest('tr').find('.pro-service-price').text(),
                af_service_price: obj.closest('tr').find('.af-service-price').text() || $('[name="af_service_price"]').val(),
            }

        } else {
            tblData = {}
        }

        templateData = {
            id: $('[name="ipt_hidden"]').val() || $('[name="hidden_city"]').val(),
            tblData: tblData
        }

        layer.open({
            title: '添加服务城市',
            area: '520px',
            type: 11,
            content: template.render(require('tpl/service_city.vue').template)(templateData),
            scrollbar: false,
            btn: ['确定', '取消'],
            yes: function() {
                $('#service_city').submit();
            }

        });
        $('.city-picker-input').cityPicker({
            end: 'city'
        });

        $("select").selectOrDie();
        self.validate($('#service_city'), obj);

    },
    // 企业客服详情（修改客服）
    modifyServiceList() {
        let self = this;

        $('[data-act="handle-modify"]').click(function(event) {
            customerAct.getServiceList().success(function(json) {
                let templateData = {
                    id: $('[name="ipt_hidden"]').val(),
                    json: json || {}
                }

                layer.open({
                    title: '修改客服',
                    area: '400px',
                    type: 11,
                    content: template.render(require('tpl/service_list.vue').template)(templateData),
                    btn: ['确定', '取消'],
                    yes: function() {
                        $('#service_list').submit();
                    }
                });

                $('select').selectOrDie();

                self.validate($('#service_list'), null);

            });

        });

    },
    // 企业服务详情（删除）
    deleteServiceList() {
        $('[data-act="delete"]').click(function() {
            let $this = $(this);

            layer.open({
                title: '删除操作',
                content: '确定要删除吗？',
                btn: ['确定', '取消'],
                yes: function() {
                    layer.closeAll();
                    let data = {
                        id: $('[name="ipt_hidden"]').val(),
                        location_id: $this.closest('tr').find('.location-id').text()
                    }

                    customerAct.deleteLocation(data, ({ msg }) => {
                        layer.msg(msg, function() {
                            location.href = location.href;

                        });

                    }, ({ msg }) => {
                        layer.msg(msg);
                    });
                }
            });


        });

    },
    //企业服务详情（提交）
    submitServiceDetail() {
        let self = this;

        $('[data-act="service_submit"]').click(function() {
            self.validate($('form'), $(this));

        });

    },
    // 企业合同根据企业id动态加载
    getContract() {
        let self = this;

        $('[name="user_id"]').change(function() {
            let $this = $(this);

            if ($('.contract-oldId').css('display') == 'block') {
                $('[name="old_id"] option').each(function() {
                    if ($(this).val()) {
                        $(this).remove();
                        $('[name="old_id"]').removeAttr('required');
                        $('select').selectOrDie('update');
                    }

                });

            }

            let data = {
                user_id: $this.find('option:selected').val()
            }

            customerAct.selectProduct(data).success((json) => {
                let status = json.status - 0,
                    data = json.data;

                if (data === null) return false;

                if (status === 0) {
                    let html = '';

                    for (let i = 0; i < data.length; i++) {
                        html += `<option value="${data[i].id}">${data[i].company_name}-${ data[i].name}(合同号：${data[i].id})</option>`;
                    }

                    $('.contract-oldId').show();

                    $('[name="old_id"]').append(html)
                        .attr('required', 'required');

                    $('select').selectOrDie('update');

                    self.getContractId();
                }
            });
        });
    },
    // 根据合同ID判断合同是否过期
    getContractId() {
        $('[name="old_id"]').change(function() {
            let $this = $(this),
                [data] = [{ id: $this.find('option:selected').val() }];

            customerAct.contractIsTurn(data, ({ data }) => {
                $('[name="overtime"]').datetimepicker('setStartDate', data);

            }, ({ msg }) => {
                layer.msg(msg);
                $this.children('option').removeAttr('selected');
                $('select').selectOrDie('update');

            });

        });

    },
    // 添加切换合同——提交表单
    addContract() {
        let self = this;

        $('[data-act="contract_submit"]').click(function() {
            let $this = $(this);

            self.state = $this.data('act');

            $('#add_contract').submit();

        });
    },
    // 合同服务状态设定验证
    validateContract() {

        let self = this;

        $('#add_contract').validate({
            submitHandler: function() {
                let state = self.state;

                if (state == 'set_status') {
                    self.getServiceStatusCommon($('[data-act="set_status"]'));
                }

                if (state == 'set_city') {
                    self.addServiceCityCommon($('[data-act="set_city"]'))
                }

                if (state == 'contract_submit') {
                    $('[data-act="contract_submit"]').attr('disabled', 'disabled');

                    customerAct.addContractChange($('#add_contract').serialize(), ({ msg, data }) => {
                        layer.msg(msg, function() {
                            window.location.replace(data);
                        });

                    }, ({ msg }) => {
                        layer.msg(msg, function() {
                            $('[data-act="contract_submit"]').removeAttr('disabled');
                        });

                    });

                }

            },
            rules: {
                price: {
                    number: true,
                    min: 0,
                    max: 999999999.99
                }
            },
            messages: {
                serviceLayerForm: {
                    required: '服务状态需设定'
                },
                price: {
                    number: '套餐费必须是大于0的数字',
                    min: '套餐费必须是大于0的数字',
                    max: '请填写有效套餐费金额'
                }
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.closest('.ipt-box'));
            }
        });
    },
    validate(form, obj) {
        form.validate({
            submitHandler: function() {
                let $form = form.attr('id');

                // 根据表单id加载
                switch ($form) {
                    case 'set_service':
                        let [$option, $cus_company, $cnt] = [];

                        $option = $('[name="service_list"]').find("option:selected");
                        $cus_company = obj.closest('tr').find('.company-name').text();
                        $cnt = `确定指定${$option.text()}为${$cus_company}的客服吗？`;

                        layer.open({
                            title: false,
                            content: `<div class="layer-wra" style="padding-bottom: 0;">${$cnt}</div>`,
                            btn: ['确定', '取消'],
                            closeBtn: false,
                            yes: function() {
                                layer.closeAll();

                                let [data] = [{
                                    admin_id: $option.val(),
                                    id: obj.closest('tr').find('[type="hidden"]').val()
                                }];

                                customerAct.setService(data, ({ msg }) => {
                                    layer.msg(msg, function() {
                                        location.href = location.href;
                                    });

                                }, ({ msg }) => {
                                    layer.msg(msg);

                                });

                            }

                        });

                        break;
                    case 'service_city':
                        let data = $('form').serialize();

                        customerAct.comAddLocation(data, ({ msg }) => {
                            layer.closeAll();
                            layer.msg(msg, function() {
                                location.href = location.href;
                            });

                        }, ({ msg }) => {
                            layer.msg(msg, function() {
                                $('[data-act="contract_submit"]').removeAttr('disabled');
                            });

                        });

                        break;
                    case 'service_settings':
                        if (obj.data('name') == 'contract') { // 切换合同添加

                            let $layrData = $('#service_settings').serialize();

                            $('[name="serviceLayerForm"]').val($layrData)
                                .attr('required', 'required');

                            $('[data-act="contract_submit"]').removeAttr('disabled');

                            layer.closeAll();


                        } else {
                            customerAct.comSetService($('#service_settings').serialize(), ({ msg }) => {
                                layer.msg(msg);

                            });

                        }

                        break;
                    case 'service_list':
                        customerAct.setService($('form').serialize(), ({ msg }) => {
                            layer.closeAll();
                            layer.msg(msg, function() {
                                location.href = location.href;
                            });

                        }, ({ msg }) => {
                            layer.msg(msg);

                        });

                        break;
                    case 'service_detail':
                        obj.val('保存中...');
                        customerAct.editProductOrder($('form').serialize(), ({ msg }) => {
                            layer.msg(msg, function() {
                                window.location.replace('/Service-Customer-productList');

                            });

                        }, ({ msg }) => {
                            layer.msg(msg);
                            obj.val('保存');

                        });

                        break;
                    default:
                        // statements_def
                        break;
                }

            },
            rules: {
                soc_service_price: {
                    number: true,
                    min: 0,
                    max: 999999999.99
                },
                pro_service_price: {
                    number: true,
                    min: 0,
                    max: 999999999.99
                },
                af_service_price: {
                    number: true,
                    min: 0,
                    max: 999999999.99
                }
            },
            messages: {
                service_list: {
                    required: '客服列表必填'
                },
                soc_service_price: {
                    required: '服务费必填',
                    number: '服务费必须是大于0的数字',
                    min: '服务费必须是大于0的数字',
                    max: '请填写有效服务费金额'
                },
                pro_service_price: {
                    required: '服务费必填',
                    number: '服务费必须是大于0的数字',
                    min: '服务费必须是大于0的数字',
                    max: '请填写有效服务费金额'
                },
                af_service_price: {
                    required: '服务费必填',
                    number: '服务费必须是大于0的数字',
                    min: '服务费必须是大于0的数字',
                    max: '请填写有效服务费金额'
                },
                service_state: {
                    required: '签约状态必选'
                },
                overtime: {
                    required: '合同到期必填'
                },
                is_salary: {
                    required: '代发工资必选'
                },
                admin_id: {
                    required: '客服设定列表必选'
                }
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.closest('.ipt-box'));
            }
        });
    },
    // 企业客户导出
    export () {
        $('body').on('click', '[data-act="export"]', function() {
            $('#company_query_form').append('<input type="hidden" name="export" value="1" />');
            $('#company_query_form').submit();
            $('input[name="export"]').remove();

        });
    },

}

module.exports = customer;
