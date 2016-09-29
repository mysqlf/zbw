let tpl = require('plug/artTemplate'),
    { isFn } = require('modules/util'),
    { getTemplateClassify, getTemplateRule, saveRules } = require('api/rules'),
    { serializeEl } = require('modules/util'),
    dateFn = require('plug/datetimepicker/index'),
    sbTpl = require('tpl/sb_rules.vue'),
    icheckFn = require('plug/icheck/index');

require('plug/city-picker/city-picker');
require('plug/validate/index');

let rulesAdd = {
    init() {
        let self = this;

        self.eventBind();
        self.validate();

    },
    eventBind() {
        let self = this;

        $('.city-picker-input').cityPicker();

        let $J_location = $('#J_location');

        // 规则类型
        $J_location.change(function(){
            let location = $('#J_location').val() - 0;

            self.resetVal();

            getTemplateClassify({
                location
            },( {result} ) => {
                let paymentType = $('#J_paymentType').val() - 0;

                $('#J_templateId').val(result.template_id);

                self.sbClass = {
                    items: result.result['1'] || []
                }

                self.emptyTpl();
                self.ispaymentType(paymentType);

            }, ( { msg = '该参保地不存在模板！' } ) => {
                layer.msg(msg);
                self.emptyTpl();
                self.resetVal();
            })
        });

        if($J_location.val()){
            $J_location.trigger('change')
        }

        $('#J_paymentType').change(function(){
            let val = $(this).val() - 0;

            self.emptyTpl();
            self.ispaymentType(val);
        })

        // 公积金 切换固定比例和比例范围
        $('#J_sb-rules-tpl').on('ifChanged', '.J_scale-select', function(evt) {
            let $this = $(this),
                val = $this.val() - 0,// 1 社保 2 公积金
                $scope = $this.closest('.J_scale-scope'),
                $scalebox = $scope.find('.scale-box'),
                $fixedRatio = $scope.find('.fixed-ratio');

            if(val === 1) {
                $scalebox.show().find('input').removeClass('ignore');
                $fixedRatio.hide().addClass('ignore');
            } else {
                $scalebox.hide().find('input').addClass('ignore');
                $fixedRatio.show().removeClass('ignore');
            }

            $scope.find('span.validator-error').remove();
            $scope.find('.validator-error').removeClass('error validator-error');
        });

        // 选择社保类型
        $('#J_add-form').on('change', '.J_sb-type', function(event) {
            self.getGjjClass();
        });

        // 提交表单
        $('#J_submit-btn').click(function(){
            $(this).closest('form').submit();
        })

        // 更改规则 去掉对应的提示
        $('body').on('blur', '#amount-min,#amount-max', function(){
            let id = this.id == 'amount-min' ? '#amount-max' : '#amount-min';
            $('#J_add-form').data('validator').element(id);
        })

    },
    validate() {
        var self = this;

        // 同级对比 处理都过同名无法比较
        $.validator.addMethod("lttSub", function(value, element, param) {
            var $el = $(element),
                classes = param.split(','),
                $scope = $el.closest(classes[0]),
                $el2 = $scope.find(classes[1]),
                val = $el.val(),
                val2 = $el2.val(),
                id = $el2.attr('id') || $el2.attr('name'),
                flag = val2 === '' || val === '' || val - 0 <= val2 - 0;

            if (flag) {
                $el2.removeClass(this.settings.errorClass);

                $('#' + id + '-error').remove();
            }

            return this.optional( element ) || flag;
        }, "范围有误");

        $.validator.addMethod("lgtSub", function(value, element, param) {
            var $el = $(element),
                classes = param.split(','),
                $scope = $el.closest(classes[0]),
                $el2 = $scope.find(classes[1]),
                val = $el.val(),
                val2 = $el2.val(),
                id = $el2.attr('id') || $el2.attr('name'),
                flag = val2 === '' || val === '' || val - 0 >= val2 - 0;

            if (flag) {
                $el2.removeClass(this.settings.errorClass);

                $('#' + id  + '-error').remove();
            };

             return this.optional( element ) || flag;
        }, "范围有误");

        // 表单校验
        $('#J_add-form').validate({
            submitHandler(form){
                let $form = $(form),
                    data = $form.serializeArray(),
                    pageType = $('#J_pageType').val() - 0,
                    $J_rulesSblist = $('#J_rules-sb-list');

                    // 如果存在模板 
                    if($.trim($('#J_sb-rules-tpl').html()) === '') {
                        layer.msg('模板规则不存在,请重新选择');
                        return;
                    }
                    if($J_rulesSblist[0] && !$J_rulesSblist.find('.J_rules-item').length) {
                        layer.msg('至少保留一种险种');
                        return;
                    }
                    // 编辑
                    if(pageType === 1){
                        layer.open({
                            content: `<form>
                                        <div class="title">是否同步修改数据？</div>
                                        <div class="icheck-label">
                                        <input class="icheck" type="checkbox" name="synchro" checked id="J_synchro" value="1"/>
                                        同时修改应用当前规则的参保人数据
                                        <a href="/Article-helpCenter-category-common_question" class="icon icon-ask" title="什么是修改应用到当前规则的参保人数据?" target="_blank"></a>
                                        </div>

                                        <div id="J_datePicker-box">
                                            <div class="inline-block align-top">生效月份：</div>
                                            <div class="inline-block ipt-box ipt-pos">
                                                <input id="J_datePicker-layer" type="text" name="effective" required class="ipt date" readonly> 
                                                <i class="icon icon-date"></i>
                                            </div>
                                        </div>
                                    </form>
                                     `,
                            btn: ['提交', '取消'],
                            skin: 'rules-save-layer',
                            scrollbar: false,
                            yes(){
                                $('.rules-save-layer form').submit();
                            },
                            success(){
                                let startDate = new Date(),
                                    endDate = new Date();

                                startDate.setFullYear(endDate.getFullYear()-1);

                                icheckFn.iCheck();

                                dateFn.getYearMonth({
                                    el: '#J_datePicker-layer',
                                    startDate,
                                    endDate
                                });

                                // 同步数据 交互
                                $('#J_synchro').on('ifChanged', function(){
                                    let $J_datePicker = $('#J_datePicker-layer'),
                                        $box = $('#J_datePicker-box');

                                    if($(this).is(':checked')) {
                                        $J_datePicker.removeClass('ignore');
                                        $box.show();
                                    } else {
                                        $J_datePicker.addClass('ignore');
                                        $box.hide();
                                    }
                                })

                                $('.rules-save-layer form').validate({
                                    submitHandler(formInner){
                                        let $formInner = $(formInner),
                                            data2 = $formInner.serializeArray(),
                                            { flag } = $formInner.data();

                                        if( flag ){
                                            return;
                                        }

                                        $formInner.data('flag', true);

                                        self.saveRules(data.concat(data2)).complete(() => {
                                            $formInner.data('flag', false);
                                        })
                                    },
                                    rules: {
                                        effective: {
                                            'date': false // ie 2013/09格式会判断错误
                                        }
                                    }
                                });
                            }
                        });
                    } else {
                        self.saveRules(data, () => {
                            layer.open({
                                content: '创建模板成功！',
                                btn: ['继续创建', '返回列表'],
                                yes(){
                                    location.href = location.href;
                                },
                                btn2 (){
                                    location.href = '/Service-Rules-index'
                                }
                            });
                        })
                    }
            },
            messages: {
                minAmount: {
                    ltt: '最低基数不能大于最高基数'
                },
                maxAmount: {
                    lgt: '最高基数不能小于最低基数'
                }
            },
            rules: {
                minAmount: {
                    min: 0,
                    ltt: '#amount-max'
                },
                maxAmount: {
                    min: 0,
                    lgt: '#amount-min'
                },
                'amount[]': {
                    min: 0,
                    lttSub: '.J_rules-item,[name = "amountmax[]"]'
                },
                'amountmax[]': {
                    min: 0,
                    lgtSub: '.J_rules-item,[name = "amount[]"]'
                },
                'comScale[]': {
                    min: 0
                },
                'comFix[]': {
                    min: 0
                },
                'perScale[]': {
                    min: 0
                },
                'perFix[]': {
                    min: 0
                },
                'companyOther[]': {
                    min: 0
                },
                'personOther[]': {
                    min: 0
                },
                'pro_cost': {
                    min: 0
                },
                'disabled': {
                    min: 0
                },
                'comFixLow': {
                    min: 0
                },
                'comFixUp': {
                    min: 0
                },
                'perFixLow': {
                    min: 0
                },
                'perFixUp': {
                    min: 0
                },
                'comScale': {
                    scale: true
                },
                'perScale': {
                    scale: true
                }
            }
        });
    },
    // 保存规则
    saveRules(data, success){
        return saveRules(data, ({msg = '保存成功'}) => {
            if(isFn(success)) {
                success();
            } else {
                layer.msg(msg);
            }

        }, ({ msg = "保存失败" }) => {
            layer.msg(msg)
        })
    },
    ispaymentType(val){
        let self = this,
            location = $('#J_location').val();

        if (location === '' || $('#J_templateId').val() === '') {
            return
        }

        if(val === 1) {
            self.getSbClass();
        } else if (val === 2 ){
            self.getGjjClass();
        }
    },
    /**
     * 公积金开关  进入页面 调用getTemplateRule接口  
     * 如果是编辑页 需要发id
     */
    getGjjClassFlag: true,
    // 获取公积金数据并渲染
    getGjjClass(){
        let paymentType = $('#J_paymentType').val() - 0,
            self = this,
            elStr = '[name="classifyMixed"],[name="type"],[name="templateId"]',
            data = null;

            if(self.getGjjClassFlag) {
                elStr += ',[name="id"]'
            }
            self.getGjjClassFlag = false;
            data = serializeEl(elStr);

        getTemplateRule(data,( { result } ) => {
            let rule = paymentType === 1 ? result[0].rule : result[0].rule;

            sbTpl.init('#J_sb-rules-tpl', rule);

        }, ({ msg = '模板规则不存在' }) => {
            $('#J_sb-rules-tpl').html('');
            layer.msg( msg );
        })
    },
    // 获取社保数据并渲染
    getSbClass(){
        let self = this,
            html = '',
            $classify_mixed = $('#classify_mixed'),
            classify_mixed = $classify_mixed.val(),
            isclassify_mixed = classify_mixed !== '' || typeof classify_mixed !== 'undefined';//第一次需要 获取默认值 再次选择不需要

        if(!self.sbClass.items.length){
            return;
        }

        html = tpl.render(require('tpl/sb_class.vue').template)(isclassify_mixed ? $.extend({},self.sbClass,{classify_mixed}) : self.sbClass);

        $('#J_sb-type-box').html(html).show();
        $('.J_sb-type').selectOrDie();

        if(isclassify_mixed) {
            $('.J_sb-type').eq(0).trigger('change');
            $classify_mixed.val('');
        }

    },
    emptyTpl(){
        $('#J_sb-rules-tpl').html('');
        $('#J_sb-type-box').html('').hide();
    },
    // 重置
    resetVal(){
        $('#J_templateId').val('');
        this.sbClass = {
            items: []
        }
    },
    // 社保类型
    sbClass: {
        items: []
    }
}

module.exports = rulesAdd;

