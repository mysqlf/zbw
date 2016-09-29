var rA=require("plug/requestAction.js");
var home={
    init:function(){
        var self=this;
        var $backTop=$('#backTop');
        rA.header.message(function(data){
            var allmsg=data.msgCount,
                message=data.msgCount;
            if(allmsg<1){
                $("#allmsg").hide();
                return;
            }
            $("#allmsg").css("display","block").html(allmsg);
            if(message > 0){
                $("#message").html("("+message+")").show();
            }else{
                $("#message").hide();
            }

        });
        $(window).scroll(function(){
            if($(window).scrollTop() == 0){
                $backTop.css('visibility','hidden');
            }else if($(window).scrollTop() > 0){
                $backTop.css('visibility','visible');
            }
        })
        $backTop.click(function(){
            self.backTop();
        })
    },
    backTop:function(){
        $('body,html').animate({scrollTop:0},100);
    }
};
module.exports=home;
