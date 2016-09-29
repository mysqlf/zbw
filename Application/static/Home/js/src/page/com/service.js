var rA=require("plug/requestAction.js");
var service={
    unpaid:function(){
        var self=this;
        $(".pay").on("click",function(){
            rA.paytip(this,"service");
        })
    },
    list:function(){
        $('[data-btn="again"]').on("click",function(){
            var $obj=$(this),
                id=$obj.data("id");
            rA.layer.repeal({
                $obj:$obj,
                data:{orderId:id},
                url:"/Company-Information-recoverOrder",
                msg:"是否重新购买？"
            });
        });
        $('[data-btn="revoke"]').on("click",function(){
            var $obj=$(this),
                id=$obj.data("id");
            rA.layer.repeal({
                $obj:$obj,
                data:{orderId:id},
                url:"/Company-Information-cancelOrder",
                msg:"是否撤销购买？"
            });
        });
        $('[data-btn="pay"]').on("click",function(){
            var $obj=$(this),
                id=$obj.data("id");
            rA.paytip(this,"service");
        });
    },
    message:function(){
        $(".jcheck").on("click",function(){
            var $this=$(this);
            var $detail=$this.find(".detail");
            if($detail.data("show") == true){
                return;
            }
            $(".detail").fadeOut();
            rA.service.checkMessage(this,function(ret){
                if(ret.status==1){
                    var data=ret.data;
                    $(".detail").data("show",false);
                    $detail.fadeIn().html(data[0].detail);
                    $detail.data("show",true);
                }else{
                    layer.alert("请求失败，稍后再试");
                }

            });


        });
    }
};
module.exports=service;