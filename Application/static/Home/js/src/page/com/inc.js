var rA=require("plug/requestAction.js"),
    template = require('art-template');
var inc={
    increase:function(){
        $('[data-act="repeal"]').on("click",function(){
            rA.insurance.repeal(this,"/Company-Insurance-cancelToIncrease",'<div class="center">是否撤销此人的报增？</div>');
        });
    },
    reduce:function(){
        $('[data-act="repeal"]').on("click",function(){
            rA.insurance.repeal(this,"/Company-Insurance-cancelToReduce",'<div class="center">是否撤销此人的社保报减？<br/>其他参保仍报减？</div>');
        });
    },
    warranty:function() {
        var self=this;
        $('[data-act="reduc"]').on("click",function() {
            self.reducetip(this);
        });
    },
    stop:function(){
        require("plug/datetimepicker.js");

        var $startTime=$('#startTime');
        var $endTime=$('#endTime');
        $startTime.datetimepicker({
            lang:"ch",           //语言选择中文
            format:"Y/m",      //格式化日期
            timepicker:false,    //关闭时间选项
            todayButton:false,    //关闭选择今天按钮
            onShow:function( ct ) {
                /*this.setOptions({
                    maxDate: $endTime.val() ? $endTime.val() : false
                })*/
            }
        });
        $endTime.datetimepicker({
            lang:"ch",           //语言选择中文
            format:"Y/m",      //格式化日期
            timepicker:false,    //关闭时间选项
            todayButton:false,    //关闭选择今天按钮
            onShow:function( ct ) {
                /*this.setOptions({
                    minDate: $startTime.val() ? $startTime.val() : false
                })*/
            }
        });
    },
    reducetip:function(obj){
        var $obj=$(obj);
        var id=$obj.data("id");
        rA.insurance.reducetip(obj,function(json){
            if(json.status == 0){
                layer.alert(json.info);
                $obj.data('flag',false);
                return false;
            }
            var html = template.render(require('tpl/reducetip.vue').template)({baseId:id,status:json.status});
            var i=layer.open({
                title: "报减",
                area: "520px",
                btn: [],
                content: html,
                end:function(){
                    $obj.data('flag',false);
                }
            });
            var $reduceTip = $("#reduceTip"),
                $socIsCancel=$('[name="socIsCancel"]'),
                $socNote=$('[name="socNote"]'),
                $proIsCancel=$('[name="proIsCancel"]'),
                $proNote=$('[name="proNote"]');
            $socIsCancel.on("change",function(){
                if($(this).is(":checked")){
                    $socNote.attr("disabled",false);
                }else{
                    $socNote.attr("disabled",true);
                }
            });
            $proIsCancel.on("change",function(){
                if($(this).is(":checked")){
                    $proNote.attr("disabled",false);
                }else{
                    $proNote.attr("disabled",true);
                }
            });
            $reduceTip.find(".btnB").on("click",function(){
                layer.close(i);
                $obj.data('flag',false);
                return false;
            });
            $reduceTip.find(".btnO").on("click",function(){
                if($('[name="socNote"]:enabled').length>0 && $('[name="socNote"]:enabled').val()==""){
                    layer.msg("停保原因不能为空!");
                    return false;
                }
                if($('[name="proNote"]:enabled').length>0 && $('[name="proNote"]:enabled').val()==""){
                    layer.msg("停保原因不能为空!");
                    return false;
                }
                rA.insurance.reduce(this,$reduceTip,function(data){
                    if(data.status==1){
                        layer.close(i);
                        layer.msg("报减成功！",function(){window.location.reload()});
                        $obj.data('flag',false);
                    }else{
                        layer.alert(data.info);
                    }
                });
                return false;
            });
        });
    }
};
module.exports=inc;