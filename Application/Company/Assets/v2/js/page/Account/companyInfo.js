require('plug/selectordie/index'); //下拉框插件
require('plug/city-picker/city-picker'); //城市选择插件
require('plug/validate/validate');
let { modifyRules } = require('plug/validate/validate'), //验证插件
    uploader = require('modules/upload'), //图片上传插件
    { companyForm ,changePassWord,bindEmail} = require('api/Insurance'),//api接口
    tpl = require('plug/artTemplate'),//模板引擎
    passWordTpl = require('tpl/change-passWord.vue'); //表单模板

let companyInfo = {
    init() {
        let self = this;
        $('.select').selectOrDie(); //下拉框美化
        self.radio(); //员工人数
        self.choiceCity(); //户口所在地选择
        self.validate(); //验证提交
        self.imgLoad() ;//图片上传
        self.changePassWord();//修改密码
        self. bindEmail()//绑定邮箱
    },
    //单选框
    radio() {
        $(".inp_radio input").each(function() {
            let $this = $(this);
            if ($this.is(':checked')) {
                $this.parent().addClass('active');
            }
        });
        $(".inp_radio input").click(function() {
            let $this = $(this);
            $this.parent().addClass('active');
            $this.parent().siblings('.inp_radio').removeClass('active');
        });
    },
    //所在地区
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
    //验证提交
    validate() {
        $("#submitBtn").click(function() {
            $('#companyForm').submit();
        });
        $('#companyForm').validate({
            rules: {
                email: {
                    email: true
                },
                contactPhone: {
                    istelephone: true
                },
                telLocalNumber: {
                    isPhone: true
                },
                socialCreditCode: {
                    digits: true
                },
                account: {
                    digits: true
                }
            },
            messages: {

            },
            submitHandler(form) {
                let dataJson = $(form).serializeArray(),
                    $submitBtn = $("#submitBtn");

                $submitBtn.attr('disabled', 'disable');

                companyForm(dataJson, ({ info }) => {

                    layer.alert(info, {
                        yes() {
                            layer.closeAll();
                            window.location.href = "/Company-Account-companyInfo";
                        }
                    });

                }, ({ info = '保存失败' }) => {
                    layer.msg(info);
                }).complete(()=>{
                    $submitBtn.removeAttr('disabled');
                });
            }
        });
    },
    //图片上传
    imgLoad() {
        //初始化图片上传
        let opts = {
                server: '/Company-Account-upload',
                //传入的值
                formData: { uploadType: 1 }
            },
            optsTwo = {
                server: '/Company-Account-upload',
                pick: {
                    id: '#filePicker2',
                },
                //传入的值
                formData: { uploadType: 2 }
            },
            optsThree = {
                server: '/Company-Account-upload',
                pick: {
                    id: '#filePicker3',
                },
                //传入的值
                formData: { uploadType: 3 }
            },
            optsFour = {
                server: '/Company-Account-upload',
                pick: {
                    id: '#filePicker4',
                },
                //传入的值
                formData: { uploadType: 4 }
            };
        let businessLicense = uploader.uploadCreate(opts),
            taxCegistrationCertificate = uploader.uploadCreate(optsTwo),
            taxpayerQualificationCertificate = uploader.uploadCreate(optsThree),
            accountOpeningLicense = uploader.uploadCreate(optsFour);
        //错误验证
        uploader.uploadError(businessLicense);
        uploader.uploadError(taxCegistrationCertificate);
        uploader.uploadError(taxpayerQualificationCertificate);
        uploader.uploadError(accountOpeningLicense);

        //上传成功后
        uploader.uploadSuccess(businessLicense, opts, $('#businessLicense'));
        uploader.uploadSuccess(taxCegistrationCertificate, optsTwo, $('#taxCegistrationCertificate'));
        uploader.uploadSuccess(taxpayerQualificationCertificate, optsThree, $('#taxpayerQualificationCertificate'));
        uploader.uploadSuccess(accountOpeningLicense, optsFour, $('#accountOpeningLicense'));
        //图片存在
        hasImg($('#businessLicense'));
        hasImg($('#taxCegistrationCertificate'));
        hasImg($('#taxpayerQualificationCertificate'));
        hasImg($('#accountOpeningLicense'));

        function hasImg(el) {
            if (el.val() !== "") {
                let val = el.val();
                el.siblings('.upload_btn').addClass('new_btn').find('.icon-addImg').hide();
                el.siblings('.upload_btn').find('.webuploader-pick').append('<img src="' + val + '">');
            }
        }


    },
    //账号设置修改密码
    changePassWord(){
        //密码验证规则
        let pswRule = {
            password: true,
            unPureLetter: true,
            unPureNum: true,
            rangelength:[6,20]
        }

        $('#changePassword').click(function(){
            layer.open({
                title:'修改密码',
                content:tpl.render(passWordTpl.template)(),
                btn:['确定','取消'],
                success(){
                    $('#listForm').validate({
                        rules:{
                            newPassword:pswRule,
                            comfirmPassword:{
                                equalTo:'#J_pswStronge'
                            }
                        },
                        submitHandler(form){
                            let dataJson = $(form).serializeArray();
                            changePassWord(dataJson,(data) => {
                                layer.alert(data.info)
                            },({info})=>{
                                layer.alert(info);
                            });

                        },
                    });
                    passWordTpl.init()
                },
                yes(){
                    $('#listForm').submit();
                }
            })
        })
    },
    bindEmail(){
        $('#bind_email').click(function(){
            let $this = $(this),
            email = $this.data('email');
           var html = `<div class="passWord_tpl">
                        <form id="listForm">
                            <div class="inp_box block mb">
                                <label>邮箱</label>
                                <input type="text" class="text" value="${email}" name="email" required>
                            </div>
                        </form>
                    </div>`
            layer.open({
                title:'绑定邮箱',
                btn:['确定','取消'],
                area:['400px','auto'],
                content:html,
                success(){
                     $('#listForm').validate({
                        rules:{
                            email:{
                                email:true
                            }
                        },
                        submitHandler(form){
                            let dataJson = $(form).serializeArray();
                            bindEmail(dataJson,(data)=>{
                                layer.alert(data.info,{
                                    yes(){
                                        location.href = location.href;
                                    },
                                    cancel(){
                                        location.href = location.href;
                                    }
                                });

                            },({info})=>{
                                layer.alert(info)
                            })
                        }
                     })
                },
                yes(){
                    $('#listForm').submit();
                }
            })
        })
    }
}
module.exports = companyInfo
