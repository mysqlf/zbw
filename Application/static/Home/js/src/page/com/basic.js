var rA=require("plug/requestAction.js"),
    common=require("modules/common.js"),
    tool=require("lib/tool.js");
require('plug/addRule.js');
var basic={
    init:function(){
        var self=this;
        //头部信息
        //rA.header.message();
        this.widget();
    },
    widget:function(){
        var self=this;
        require("plug/iCheckGroup.js");
        require('publics/data/otherData.js');
        require('publics/data/area.js');
        require('plug/jquery.boxSelector.js');


        /*//页面编辑离开提示
        $(window).on("beforeunload",function(){
            if(tool.formIsDirty($("#comInfo")[0])){
                var tip="报增编辑还没保存，是否继续？";
                return tip;
            }
        });*/

        //公司性质
        var $property=$("#property"),
            property=$property.data("value");
        $property.append(common.json.getOptions(datajson.comType,property));
        //行业
        var $property=$("#industry"),
            industry=$property.data("value");
        $("#industry").append(common.json.getOptions(datajson.industry,industry));
        //员工人数
        $("#employeeNumberSel").iCheckGroup();
        //所在地区
        $("#btn_location").boxSelector({
            key:"city-town",
            selectType:"radio",
            selectParent:false
        });
        //通讯地址
        var location=$("#areaBox").data("location");
        common.area(location);
        //证件上传
        rA.upload({
            id:"#uploadID",
            uploader:'[data-act="uploadID"]',
            url:"/Company-Account-upload",
            delUrl:"/Company-Account-delete",
            data:{uploadType:"certificate"},
            fileVal:"certificate"
        });
        rA.upload({
            id:"#treaty",
            uploader:'[data-act="protocol"]',
            url:"/Company-Account-upload",
            delUrl:"/Company-Account-delete",
            data:{uploadType:"license"},
            fileVal:"license"
        });
        $('[data-act="preview"]').on("click",function(){
            var src=$(this).data("src") + '?t=' + $.now();
            layer.open({
                title:"预览",
                btn:[],
                area: ['800px','500px'],
                content:'<a href="'+ src +'" target="_blank" title="点击查看原图"><img src="'+src+'"  style="max-height:350px;max-width:750px;" /></a>'
            });
            return false;
        });

        $(".change").on("click",function(){
            var $parent=$(this).parent(".hold");
            $parent.hide().next().show();
            return false;
        });

        $(".cancel").on("click",function(){
            var $parent=$(this).parent(".holdFrom");
            $parent.hide().prev().show();
            $parent.find("input").val("");
            return false;
        });

        /*$("#Cemail").off().on("click",function(){
            var email=$(this).data("val");
            layer.confirm("将修改链接发送至（"+email+"），请登录该邮箱进行修改",function(index){
                layer.close(index);
            });
            return false;
        });*/
        self.validate();

    },
    validate:function() {
        $("#comInfo").validate({
            ignore: ":hidden",
            rules: {
                telCityCode:{
                    isAreaCode:true
                },
                telLocalNumber:{
                    isPhoneA:true
                },
                name:{
                    required: true
                },
                gender: {
                    required: true
                },
                contactName:{
                    required: true,
                    account: true
                },
                contactPhone:{
                    required: true,
                    isTel:true
                },
                email:{
                    required: true,
                    email:true
                },
                qq:{
                    qq:true
                }
            },
            submitHandler:function(form){
                $.ajax({
                    url:"/Company-Account-companyInfo",
                    type:"post",
                    data:$(form).serialize(),
                    success:function(data){
                        var status=data.status;
                        var msg=data.info;
                        switch (status){
                            case 0:
                                layer.alert(msg);
                                break;
                            case 1:
                                layer.msg(msg,{},function(){window.location.reload()});
                                break;
                            case 2:
                                layer.confirm("将验证链接发送至（"+$("#email").val()+"），请登录该邮箱进行修改",function(index){
                                    layer.close(index);
                                    window.location.reload()
                                });
                                break;
                        }
                    }
                })
                return false;
            }
        });
    }
};
module.exports=basic;