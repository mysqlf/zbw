var rA=require("plug/requestAction.js");
require('plug/addRule.js');
var extra={
    init:function(){
        var self=this;
        //头部信息
        //rA.header.message();
        $("#changePassword").on("click",function(){
            self.layer=layer.open({
                title:"修改密码",
                area:"500px",
                btn:[],
                content: self.pdFromHtml()
            });
            var $passwordFrom=$("#pdFrom");
            self.validate($passwordFrom);
            $('[data-act="cancel"]').on("click",function(){
                layer.close(self.layer);
            });
            $('[data-act="submit"]').on("click",function(){
                $passwordFrom.submit();
            });
            $passwordFrom.find("input").keypress(function(e) {
                if(e.which == 13) {
                    $passwordFrom.submit();
                }
            });
        });
    },
    layer:"",
    pdFromHtml:function(){
        return '<form class="changePassword" id="pdFrom">'+
            '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formTable">'+
            '<tr><td class="name">原密码：</td>'+
            '<td><input class="zb_inpt" type="password" name="oldPassword" placeholder="请输入原密码" value="" required /></td></tr>'+
            '<tr><td class="name">新密码：</td>'+
            '<td><input class="zb_inpt" type="password" name="newPassword" id="newPassword" placeholder="请输入新密码" value="" required /></td></tr>'+
            '<tr><td class="name">密码确认：</td>'+
            '<td><input class="zb_inpt" type="password" name="comfirmPassword" placeholder="请再次输入新密码" value="" required /></td></tr></table>'+
            '<div class="btnC"><a href="javascript:;" class="blueBtn" data-act="cancel">取消</a>' +
            '<a href="javascript:;" class="orangeBtn" data-act="submit">确认</a></div></form>';
    },
    validate:function(form){
        form.validate({
            submitHandler:function(from){
                $.ajax({
                    url:"/Company-Account-changePassword",
                    type:"POST",
                    data:$(from).serialize(),
                    success:function(data){
                        if(data.status == 1){
                            layer.msg(data.info);
                        }else{
                            layer.alert(data.info);
                        }
                    }
                })
            },
            rules:{
                comfirmPassword:{
                    equalTo:"#newPassword"
                }
            }
        });
    }


};
module.exports=extra;