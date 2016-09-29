var rA=require("plug/requestAction.js"),
    template = require('art-template'),
    BIN = require('plug/bankcardinfo');

require('plug/addRule.js');

var salary={
    salary:function(){

        var self=this;

        $('[data-act="tolead"]').on("click",function(){
            self.toleadtip();
        });

        $('body').on('change', '#account', function(){
            var $bankSelect = $('#bank-select');

            BIN.getBankBin(this.value, function(err,data){
                if(!err){
                    $bankSelect.val(data.bankName)
                }

            })
        })

        $('body').on('change', '#productOrderId', function(){
            var val = this.value,
                $location = $('#location'),
                optionDefault = '<option value="">请选择</option>';

            if(val===''){
                $location.html(optionDefault)
                return
            }
            var time=$(this).find('[value="'+val+'"]').data("time")+"";
            $("#abortAddDelDate").html(time.replace(/(.{4})/g,'$1\/'));
            rA.getCityByProduct({productOrderId:val})
            .success(function (json) {

                var data = json.data,
                    optionHtml = optionDefault;

                $(data).each(function(index){
                    optionHtml += '<option value="' + data[index].warranty_location+'">'+ data[index].locationValue +'</option>'
                })
                $location.html(optionHtml)
            })
        })
         // 查看工资单
        $('[data-act="viewSalary"]').on("click",function(){
        
            var data = $(this).data();

            self.getSalaryInfo(data,function(retData){
                var $form = null;

                layer.open({
                    title:'查看工资单',
                    area: "520px",
                    skin: 'layer-form',
                    btn: [],
                    content: template.render(require('tpl/viewSalary.vue').template)(retData)
                })

            })

            return false
        })

         // 撤销工资单
        $('[data-act="delSalary"]').on("click",function(){
        
            var data = $(this).data();

            layer.confirm('确定撤销？', function(){
                rA.delSalary(data)
                    .success(function(json){
                        var status = json.status - 0;
                            if(status === 1){
                                layer.msg('撤销成功',{
                                    time: 2000,
                                    end: function(){
                                        location.reload();
                                    }
                                });
                            } else{
                                layer.alert(json.info);
                            }
                    });
            })

            return false
        })


        // 编辑工资单
        $('[data-act="editSalary"]').on("click",function(){
        
            var data = $(this).data();

            self.getSalaryInfo(data,function(retData){
                var $form = null;

                layer.open({
                    title:'编辑工资单',
                    area: "520px",
                    skin: 'layer-form',
                    btn: ['保存','取消'],
                    yes:function(){
                        $form.submit();
                    },
                    content: template.render(require('tpl/editSalary.vue').template)(retData)
                })

                $form = $('#payroll-audit-form');
                self.validateEditSalary($form);
            })

            return false
        })

        $('body').on('change','.J-counter', function(){
            var add = cut = 0;

            $('.J-counter-add').each(function(){
                var num = parseFloat(this.value) || 0;
                add += num;
            })

            $('.J-counter-cut').each(function(){
                var num = parseFloat(this.value) || 0;
                cut += num;
            })

            $('#wages-txt').html((add - cut).toFixed(2))
        })

        require("plug/datetimepicker.js");
        $('[name="date"]').datetimepicker({
            lang:"ch",
            format:"Y/m",
            timepicker:false,
            todayButton:false
        });
    },
    bankList: require('modules/bankData.js'),
    validateEditSalary:function($form){
        $form.validate({
            submitHandler:function(){
                var formData = $form.serializeArray();

                rA.saveSalary(formData)
                    .success(function(json){
                        var status = json.status - 0;

                        if(status === 1){
                            layer.msg('更新成功',{
                                time: 2000,
                                end: function(){
                                    location.reload();
                                }
                            });
                        } else{
                            layer.alert(json.info);
                        }
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
                bank:{
                    required: '请选择银行'
                },
                branch:{
                    required: '请输入支行名称'
                },
                account:{
                    required: '请输入银行卡号'
                },
                date:{
                    required: '请输入工资年月'
                },
                wages:{
                    required: '请输入应发工资'
                },
                deduction_income_tax:{
                    required: '请输入个人所得税'
                },
                deduction_social_insurance:{
                    required: '请输入扣个人社保'
                },
                deduction_provident_fund:{
                    required: '请输入扣个人公积金'
                },
                replacement:{
                    required: '请输入补发'
                },
                deduction_order:{
                    required: '请输入其他扣除'
                },
                state: {
                    required: '请选择状态'
                }
            }
        })
    },
    getSalaryInfo: function(data, success){
        var self = this;

        rA.editSalary(data)
          .success(function(json){
            var status = json.status - 0,
                retData = json.data;

            retData.salaryid = data.salaryid;
            retData.bankList = self.bankList;

            if(status === 1){
                if(typeof success === 'function'){
                    success(retData)
                }
                
            } else{
                layer.alert(json.info);
            }

            
        })
    },
    toleadtip:function(){
        var self = this;

        rA.getProductList()
        .success(function(json){
            var upload="";
            var retData = {},
                html = '',
                status = json.status - 0,
                $reduceTip = null,
                $form = null;

            if(status !== 1){
                layer.alert(json.info);
                return;
            }
            retData.products = json.data;
            html = template.render(require('tpl/tolead.vue').template)(retData);
            layer.open({
                title: "导入工资单",
                skin: 'layer-form',
                area: "660px",
                btn:['确定'],
                content: html,
                yes: function(){
                    $form.submit();
                }
            });

            $reduceTip = $("#reduceTip");
            $form = $('#tolead-form');
                $("#abortAddDelDate").html($("#abortAddDelDate").html().replace(/(.{4})/g,'$1\/'));
            upload=rA.upload({
                id:"#treaty",
                uploader:'[data-act="protocol"]',
                url:"/Company-Salary-upload",
                isImg:false,
                success:function(file,ret,webUpload){
                    if(ret.status == 1){
                        $form.find('[data-act="upload"]').val(ret.info);
                    }else{
                        webUpload.upload();
                        layer.alert(ret.info);
                    }
                }
            });

            self.uploadValidate($form);
        })

    },
    uploadValidate: function($form){
        $form.validate({
            submitHandler: function(){
                var data = $form.serializeArray();

                rA.uploadSalary(data)
                .success(function(json){
                    if(json.status == 1){
                        layer.msg("导入成功！");
                    }else{
                        layer.alert(json.info);
                    }
                })
            },
            rules:{
               /* certificate:{
                    required: true
                }*/
            },
            messages: {
                productOrderId: {
                    required: '请选择服务产品'
                },
                location: {
                    required: '请选择城市'
                },
                file: {
                    required: '请选择上传文件'
                }
            }
        })
    },
    progress:function(){
        require("plug/datetimepicker.js");
        $('[name="orderDate"]').datetimepicker({
            lang:"ch",
            format:"Y/m",
            timepicker:false,
            todayButton:false
        });
    }


};
module.exports=salary;