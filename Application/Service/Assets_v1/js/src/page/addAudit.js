// 代发工资
var template = require('art-template'),
    idCard =require('modules/idCard'),
    dateObj =require('modules/date'),
    validateFn = require('modules/validate'),
    icheckFn = require('modules/icheck');


module.exports  = {
    init: function() {
    	var self = this;

        // 复选框和单选框
        icheckFn.init();

        self.validate();

        self.bindEvent();

        self.computeAll();

    },
    bindEvent: function(){
        var self = this,
            $card_num = $('#card_num');

         // 获取年龄
        $card_num.change(function(){
            var val = this.value,
                begin = 0,
                end = 0;

            if(self.validateObj.element('#card_num')){
                begin = idCard.getBirthday(val),
                end = $.now();
                $('#age').html(dateObj.getDiffYear(begin, end));
            }
        });

        if($card_num.val() !==''){
            $card_num.trigger('change');
        }

        // 社保地址
        $('body').on('change', '#location', function(){
            var data = {},
                val = this.value;

            if(val === ''){
                //$('#socialSecurity-tel').html(self.initTpl);
               // icheckFn.init();
                return;
            }

            data.location = val;
            self.getDataByArea(data,function(){

                self.requiredObj.sb = $('[required]','.form-box[data-type="1"]');
                self.requiredObj.ss = $('[required]','.form-box[data-type="2"]');

                $('.buy-server').trigger('ifChanged');

                self.counterValidate('.security-change');
                self.counterValidate('.gzj-change');

                // 单选多选 样式
                icheckFn.init();

            });
        })

        //查看图片
        $('body').on('click', '#view-pic', function(){
            var hrefs = $(this).data('href').split(','),
                html = '',
                i = 0,
                len = hrefs.length;
            if(!len){
                return
            }
            while(i < len){
                html += '<a href="'+ hrefs[i] +'" target="_blank" title="点击查看原图"><img src="'+ hrefs[i] +'" style="cursor: zoom-in;max-width:300px;max-height:450px; margin: 10px;"></a>';
                i++;
            }

            layer.open({
                title: '查看图片',
                area:'700px',
                btn:[],
                content:html
            })

            return false;
        })


        // 挂单
        $('body').on('ifChecked','.panel-audit .guadan .icheck', function(){
            var $this = $(this),
                $closest = $this.closest('.panel-audit'),
                $success = $('.states-success',$closest);

            $success.iCheck('check');
        })

        // 审核状态改变
        $('body').on('ifChecked', '.panel-audit .states:not(.states-success)', function(){
            var $this = $(this)
                $closest = $this.closest('.panel-audit'),
                $guadan = $('.guadan .icheck',$closest);

                $guadan.iCheck('uncheck');
        })

        // 填写社保时候 社保卡必填
        $('body').on('ifChanged','.ss-card', function(){
            var $this = $(this),
                $cardNum = $('#ss_card_number'),
                $has = $('#has-ss-card');

             if($has.is(':checked') && $('#security-checkbox').is(':checked')){
                $cardNum.prop('required', true);
             } else{
                $cardNum.prop('required', false).removeClass('error');

                $('#' + $cardNum.attr('id') + '-error.validator-error').remove();
             }
             self.computeSbTotal();
             self.computeAll();
        })

        // 填写公积金卡时候 公积金卡必填
        $('body').on('ifChanged','.gzj-card', function(){
            var $this = $(this),
                $cardNum = $('#gzj_card_number'),
                $has = $('#has-gzj-card');

             if($has.is(':checked') && $('#gzj-checkbox').is(':checked')){
                $cardNum.prop('required', true);
             } else{
                $cardNum.prop('required', false).removeClass('error');

                $('#' + $cardNum.attr('id') + '-error.validator-error').remove();
             }

             self.computeGzjTotal();
             self.computeAll();
        })

        // 所需材料
        $('body').on('click', '.material', function(){
            var msg = $(this).data('material') || '不需要材料';

            layer.open({
                title:'所需材料',
                content: msg
            })
        })

        // 计算社保 并渲染
        $('body').on('change', '.security-change', function(){
            self.counterValidate('.security-change');
        }).trigger('change');

        $('body').on('change', '.gzj-change', function(){
             self.counterValidate('.gzj-change');
        }).trigger('change');

        // 处理必填增删
        $('body').on('ifChanged','.buy-server', function(){
            var $this = $(this),
                type = $this.closest('.form-box').data('type');

            if($this.is(':checked')){
                self.addRequired(type);
            } else{
                self.removeRequired(type);
            }
            $('.ss-card,.gzj-card').trigger('ifChanged');

        })

        $('.buy-server').trigger('ifChanged');

        /*$('body').on('change','#sb_year', function(){
            var defaultHtml = self.createOptions('','请选择'),
                index = $(this).find('option:selected').index(),
                $sb_month = $('#sb_month'),
                months = self.dates.sb.months;

            if(this.value === ''){
                $sb_month.html(defaultHtml);
            } else{
                $sb_month.html(defaultHtml + self.createOptionsByArr(months[index-1]))
            }

        })*/

    },
    // 起缴时间
    dates:{
        sb:{},
        gzj:{}
    },
    // 默认起缴时间
    defaultDate:{
        sb_year: $('#sb_year').val(),
        sb_month: $('#sb_month').val(),
        gzj_year: $('#gzj_year').val(),
        gzj_month: $('#gzj_month').val()
    },
    createOptions: function(value, txt){
        return '<option value="'+ value +'">'+ txt +'</option>';
    },
    createOptionsByArr: function(arr){
        var self = this,
            html = '',
            i = 0,
            len = arr.length;

        if(typeof arr[0] === 'object'){
            for(;i<len;i++){

                html += self.createOptions(arr[i].value,arr[i].text);
            }
        } else{
            for(;i<len;i++){

                html += self.createOptions(arr[i],arr[i]);
            }
        }

        return html;
    },
    initTpl: $('#socialSecurity-tel').html(),
    counter:{
        sb:{
            sum: parseFloat($('#sb-total').text()) || 0,//小计
            total: parseFloat($('#total').text()) || 0,//总额
            pro_cost:parseFloat($('#sb-pro_cost').text()) || 0// 工本费
        },
        ss: {
            sum: parseFloat($('#gzj-total').text()),
            total: (function(){
                var total = parseFloat($('#gzj-total').text()) || 0;

                if($('#no-gzj-card').is(':checked')){
                    total -= parseFloat($('#gzj-pro_cost').text()) || 0;
                }

                return total;
            })(),
            pro_cost: parseFloat($('#gzj-pro_cost').text()) || 0
        }
    },
    ajaxCache:{},
    /**
     * 计算总额前校验
     * @param  {string|jquery} className 选择器
     * @return {boolen}           返回结果 true 或者 false
     */
    counterValidate: function(className){
        var self = this,
            formArr = self.serializeArray('.security-change,.gzj-change'),
            $el = $(className),
            flag = true,
            i = 0,
            len = formArr.length,
            data = {},
            sb_type_id_arr = [],
            gzj_type_id_arr = [];

        for(; i < len; i++){
            /*if(formArr[i].value === ''){
                return false;
            }*/
            // 参数矫正
            switch (formArr[i].name) {
                case 'sb_name[]':

                    sb_type_id_arr.push(formArr[i].value);

                    break;
                case 'pro_cost':
                    data.sb_amount =  formArr[i].value;
                    break;
                case 'gzj_name[]':
                    gzj_type_id_arr.push(formArr[i].value);
                    break;
                case 'gzj_pro_cost':
                    data.gzj_amount =  formArr[i].value;
                    break;
                case 'gzj_pro_cost_com':
                    data.firme =  formArr[i].value;
                    break;
                case 'gzj_pro_cost_per':
                    data.member =  formArr[i].value;
                    break;
                default:
                    break;
            }

        }
        // 必须要由大到小排序
        data.sb_type_id = sb_type_id_arr.sort(function(a,b){return a<b?1:-1}).join('|');
        data.gzj_type_id = gzj_type_id_arr.sort(function(a,b){return a<b?1:-1}).join('|');

        data.temple_id = $('#template_id').val();
        data.sb_amount=$('#sb_amount').val();
        data.gzj_amount=$('#gzj_amount').val();
        data.sb_month=self.monthDiffer($('#sb_month').val());
        data.gzj_month=self.monthDiffer($('#gzj_month').val());
        data.member=$('#member').val();
        data.firme=$('#firme').val();

        // 是否通过验证
        $el.each(function(){
            return flag = self.validateObj.element(this);
        })

        if(!flag) return;

        data.temple_id = $('#template_id').val();

        if($el.hasClass('gzj-change')){
            self.computeGzj(data);
        } else{
            self.computeSecurity(data);
        }
    },
    /**
     * 添加必填
     * @param {string ｜ number} type 1为社保 2为公积金
     */
    addRequired: function(type){
        var self = this;

        if((type-0) === 1){
            self.requiredObj.sb.prop('required', true);
        } else{
            self.requiredObj.ss.prop('required', true);
        }
    },
    /**
     * 删除必填
     * @param {string ｜ number} type 1为社保 2为公积金
     */
    removeRequired: function(type){
        var self = this;

        if((type-0) === 1){
            self.requiredObj.sb.prop('required', false).removeClass('error');
        } else{
            self.requiredObj.ss.prop('required', false).removeClass('error');
        }

        $('span.error.validator-error','.form-box[data-type="' + type+'"]').remove();

    },
    // 序列化 单个元素表单
    serializeArray: function(inputs){
        var arr = [];

        $(inputs).each(function(){
            var name = this.name,
                val = this.value;

            arr.push({
                name: name,
                value: val
            })
        })

        return arr;
    },
    // 序列化 单个元素表单 转化成对象形式
    serializeObj:function(inputs){
        var obj = {};

        $(inputs).each(function(){
            var name = this.name,
                val = this.value;
            if(name.indexOf('[]') !== -1){
                obj[name] ? obj[name].push(val) : obj[name] = [val];
            } else{
                obj[name] = val
            }
            
        })

        return obj;
    },
    requiredObj: {
        sb: $('[required]','.form-box[data-type="1"]'),//社保
        ss:  $('[required]','.form-box[data-type="2"]')// 公积金
    },
    getYears: function(time){

        var date = new Date(time),
            year = date.getFullYear(),
            month = date.getMonth() + 1,
            years = [],
            months = [],
            step = 5,
            down = month - step

        if(down < 0){
            var arr = [],
                arr2 = [],
                j = down;

            years = [year-1,year];

            while(j !== 0){
                j++;
                arr.push(12+j);
            }

            for(var i = 1; i <= month; i++){

                arr2.push(i)
            }

            months = [arr,arr2]
        }else{
            years = [year];
            for(var i = 1; i <= down; i++){
                months.push(i)
            }
        }

        return {
            years: years,
            months: months
        };
    },
    // 根据地区获取数据
    getDataByArea: function(data, cb){
        var self = this,
            url = '/Index-getCityClass';
        // 取消之前的ajax
        self.abort(url);
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: data,
            url: url,
            success: function(json){

                var data = json,
                    status = 0,
                    html = '';

                //data.sb_rule.years = self.getYears($.now());
                if(!data.sb_rule || !data.gzj_rule){
                    return
                }

                /*data.sb_rule.dates = self.dates.sb = self.getYears($.now());
                data.gzj_rule.dates = self.dates.gzj = self.getYears(new Date('2012-10-2'));*/

                data = $.extend(self.defaultDate,data,self.serializeObj('[type="hidden"]'));
     
                html = template.render(require('tpl/security.vue').template)(data);

                if(status !== 0){
                    layer.alert(json.msg);
                    return;
                }

                $('#socialSecurity-tel').html(html);

                $('#template_id').val(data.sb_classify.classify[0].template_id);

                //select 加上默认值
                self.changeVal(data);

                self.computeScaleBoth();
                // 重置计数
                self.resetCounter();

                // 记录工本费
                self.counter.sb.pro_cost = data.sb_rule.pro_cost - 0;
                self.counter.ss.pro_cost = data.gzj_rule.pro_cost - 0;

                if(typeof cb === 'function'){
                    cb();
                }

                validateFn.modifyRules(self.validateObj, {
                    rules: {
                        gzj_pro_cost_com: {
                            rangeScale: data.gzj_rule.company
                        },
                        gzj_pro_cost_per: {
                            rangeScale: data.gzj_rule.person
                        }
                    },
                    messages: {
                        gzj_pro_cost_com: {
                            rangeScale: '可输入范围为' + data.gzj_rule.company
                        },
                        gzj_pro_cost_per: {
                            rangeScale: '可输入范围为' + data.gzj_rule.person
                        }
                    }
                })
            },
            error:function() {
                //默认取消弹服务繁忙
            }
        }).complete(function(){
            self.resetAjaxCache(url)
        })
    },
    validateObj: null,
    sendAudit: function(url,data, cb){
        $.ajax({
            type: 'post',
            data: data,
            url: url,
            dataType: 'json',
            success: function(json){
                var data = json.data,
                    stauts = json.status - 0;
                if(stauts === 0){

                    if(typeof cb === 'function'){
                        cb()
                    }
                } else{
                    layer.alert(json.msg)
                }
            },
            error:function() {
                //默认取消弹服务繁忙
            }

        })
    },
    validate: function(){
        var self = this;

        self.validateObj = $('#add-audit-form').validate({
            submitHandler: function(form){

                if($('.buy-server:checked').length){

                    // 如果购买社保被勾选并且 返回的sb规则为null
                    if(!self.retComputeData.sb_result && $('#security-checkbox').is(':checked')){
                        layer.alert('选择社保模板不存在')
                        return false;
                    }

                    // 如果购买公积金被勾选并且 返回的gzj规则为null
                    if(!self.retComputeData.gzj_result && $('#fund-checkbox').is(':checked')){
                        layer.alert('选择公积金模板不存在')
                        return false;
                    }

                    var $form = $(form),
                        url = 'Service-Order-audit',
                        data = $form.serializeArray();

                    self.sendAudit(url, data,function(){
                        layer.msg('审核成功', {
                            end: function(){
                                location.reload();
                            }
                        })
                    });
                } else{
                    layer.alert('购买公积金和购买社保至少选一项')
                }
            return false
            },
            rules: {
                card_num:{
                    
                    isIdCard: true
                },
                mobile: {
                    istelephone: true
                },
                ss_card_number:{
                    SSN: true
                },
                gzj_card_number:{
                    SSN: true
                },
                pro_cost:{
                    number: true
                },
                gzj_pro_cost: {
                    number: true
                },
                gzj_pro_cost_com: {
                    number: true
                },
                gzj_pro_cost_per: {
                    number: true
                },
                gzj_pro_cost_com: {
                    rangeScale: $('#comp-scale').text()
                },
                gzj_pro_cost_per: {
                    rangeScale: $('#per-scale').text()
                }

            },

            messages:{
                gzj_pro_cost_com: {
                    rangeScale: '可输入范围为' + $('#comp-scale').text()
                },
                gzj_pro_cost_per: {
                    rangeScale: '可输入范围为' + $('#per-scale').text()
                },
                card_num: {
                    required: '请输入身份证号'
                },
                user_name: {
                    required: '请输入姓名'
                },
                sex: {
                    required: '请选择性别'
                },
                mobile: {
                    required: '请输入手机号码'
                },
                location: {
                    required: '请选择参保地'
                },
                ss_card_number:{
                    required: '请输入卡号'
                },
                gzj_card_number: {
                    required: '请输入卡号'
                },
                pro_cost: {
                    required: '请输入社保基数'
                },
                sb_year: {
                    required: '请选择起缴年份'
                },
                sb_month: {
                    required: '请选择起缴月份'
                },
                sb_state: {
                    required: '请选择审核状态'
                },
                gzj_pro_cost: {
                    required: '请输入公积金基数'
                },
                gzj_year: {
                    required: '请选择起缴年份'
                },
                gzj_month: {
                    required: '请选择起缴月份'
                },
                gzj_pro_cost_com: {
                    required: '请输入单位缴纳比例'
                },
                gzj_pro_cost_per: {
                    required: '请输入个人缴纳比例'
                },
                gzj_state: {
                    required: '请选择审核状态'
                }
            }
        })
    },
    /* 计算时返回的数据 
     * 用于处理 规则为null的时候 
     * 如果勾选购买保险 阻止提交
     * 第一次可以被提交
     **/
    retComputeData: {
        sb_result: {},
        gzj_result: {}
    } ,
    //发送计算请求 （公积金）
    sendCompute: function(data,type){
        var self = this,
            url = '/Index-calculation',
            urlType = url+type;
        self.abort(urlType);

        return $.ajax({
            type: 'post',
            dataType: 'json',
            data: data,
            url: url,
            beforeSend: function(ajax, opts){
                self.ajaxCache[urlType] = ajax;
            },
            success: function(ret){
                self.retComputeData = ret;
            },
            error: function() {
                //默认取消弹服务繁忙
            }
        }).complete(function(){
            self.resetAjaxCache(urlType);
        })
    },
    // 计算社保总额
    computeSecurity: function(data){
        var self = this;

        self.sendCompute(data,1)
            .success(function(json){
                var $sb_table=$("#sb_table");
                if(!json.sb_result) {
                    $sb_table.hide();

                    $('#sb-pro_cost').html(0);
                    self.resetSbCounter();
                    self.computeSbTotal();
                    self.computeAll();
                    return;
                }

                var data = json.sb_result.data,
                    items = data.items,
                    totalCompany = totalPerson = 0,
                    $sumCompany = $('.sum-company'),
                    $sumPerson = $('.sum-person'),
                    $sumCompScale = $('.sum-company-scale'),
                    $sumPerScale = $('.sum-person-scale'),
                    $sum = $('.sum'),
                    status = json.sb_result.state - 0,
                    allTotal = 0;

                if(status !== 0){
                    return false;
                }

                var compTotalScale = perTotalScale = 0;

                self.securityTable(data);

               /* $(items).each(function(index, el) {
                    var perData = items[index],
                        comSum = perData.company.sum,
                        perSum = perData.person.sum;

                    $sumCompScale.eq(index).html(perData.company.scale + '+' + perData.company.fixedSum);
                    $sumPerScale.eq(index).html(perData.person.scale + '+' + perData.person.fixedSum);
                    $sumCompany.eq(index).html(comSum);
                    $sumPerson.eq(index).html(perSum);
                    $sum.eq(index).html(perData.total);
                })*/


                allTotal = (data.company - 0) + (data.person - 0)

                self.counter.sb.pro_cost = data.pro_cost - 0 || 0;

                $('#total-sum-company').html(data.company);
                $('#total-sum-person').html(data.person);
                $('#total').html(allTotal.toFixed(2));
                $('#sb-pro_cost').html(self.counter.sb.pro_cost);

                $('#czj-total').html(((json.czj_result && json.czj_result.price) || 0) + '元/月')

                self.computeScaleBoth();
                // 记录社保总额
                self.counter.sb.total = allTotal;
                

                self.computeSbTotal();
                self.computeAll();

                self.computeScale($('.sum-company-scale'));

                $sb_table.show();

            })
    },
    computeScaleBoth: function(){
        var self = this;

        $('#total-sum-company-scale').html(self.computeScale($('.sum-company-scale')));
        $('#total-sum-person-scale').html(self.computeScale($('.sum-person-scale')));
    },
    computeScale: function($el){
        var scaleSum = scaleFixedSum = 0;

        $el.each(function(){
            var $this = $(this),
                val = $this.text(),
                arr = val.split('+');

            for(var i = 0, len = arr.length; i < len; i++){
                if(arr[i].indexOf('%') !== -1){
                    scaleSum += (arr[i].replace('%', '') - 0)
                } else{
                    scaleFixedSum += (arr[i]-0)
                }
            }

        })
        return scaleSum.toFixed(2) + '%+' + scaleFixedSum.toFixed(2)
    },
    // 取消ajax
    abort: function(url){

        var cacheObj = this.ajaxCache[url];

        if(cacheObj){
            cacheObj.abort();
        }
    },
    resetAjaxCache: function (url) {
        this.ajaxCache[url] = false;
    },
    // 计算公积金总额
    computeGzj: function(data){
        var self = this;


        self.sendCompute(data,2)
        .success(function(json){
            if(!json.gzj_result) {

                $('#gzj-pro_cost').html(0);
                self.resetGzjCounter();
                self.computeGzjTotal();
                self.computeAll();
                return;
            }

            var data = json.gzj_result.data,
                status = json.gzj_result.state - 0;

            if(status === 0){
                self.counter.ss.total = data.person + data.company;

                self.counter.ss.pro_cost = data.pro_cost - 0 || 0;

                $('#gzj-pro_cost').html(self.counter.ss.pro_cost);

                self.computeGzjTotal();
                self.computeAll();
            }
        })

    },
    resetSbCounter: function(){
        this.counter.sb = {
            sum: 0,//小计
            total: 0,//总额
            pro_cost:0// 工本费
        }
    }, 
    resetGzjCounter: function(){
        this.counter.ss = {
            sum: 0,//小计
            total: 0,
            pro_cost:0,
            czj: 0
        }
    },
    resetCounter: function(){
        this.resetSbCounter();
        this.resetGzjCounter();
    },
    // 计算社保小计
    computeSbTotal: function(){
        var total = this.counter.sb.total;

        if($('#no-ss-card').is(':checked')){
            total += this.counter.sb.pro_cost;
        }

        this.counter.sb.sum = total;

        $('#sb-total').html(total.toFixed(2) + '元/月')
    },
    computeGzjTotal: function(){
        var total = this.counter.ss.total;

        if($('#no-gzj-card').is(':checked')){

            total += this.counter.ss.pro_cost;
        }

        this.counter.ss.sum = total;

        $('#gzj-total').html(total.toFixed(2) + '元/月')
    },
    computeAll: function(){
        var total = 0;

        if($('#security-checkbox').is(':checked')){
            total += this.counter.sb.sum;
        }

        if($('#fund-checkbox').is(':checked')){
            total += this.counter.ss.sum ;
        }

        total += (parseFloat($('#czj-total').text() ) || 0);

        $('#all-total').html(total.toFixed(2) + '元/月');
    },
    //select 加上默认值
    changeVal:function(data){
        var sb_change_valArr=[],
            gzj_change_valArr=[],
            sb_classify=data.sb_classify.classify_mixed,
            gzj_classify=data.gzj_classify.classify_mixed;
        //社保
        if(sb_classify != null){
            sb_change_valArr=sb_classify.split("|");
        }
        //公职金
        if(gzj_classify != null){
            gzj_change_valArr=gzj_classify.split("|");
        }

        for(var i=0;sb_change_valArr.length > i;i++){
            $(".security-change").find('[value="'+sb_change_valArr[i]+'"]').attr("selected",true);
        }

        for(var i=0;gzj_change_valArr.length > i;i++){
            $(".gzj-change").find('[value="'+gzj_change_valArr[i]+'"]').attr("selected",true);
        }
    },
    securityTable:function(data){
        var html = template.render(require('tpl/securityTable.vue').template)(data);
        $("#sb_table").html(html);
    },
    monthDiffer:function(){
        /*var month=this.orderDate;
        if(month == ""){
            return 1;
        }
        return (orderDate-0)-(month-0)+1;*/
        return 1;
    }

}


