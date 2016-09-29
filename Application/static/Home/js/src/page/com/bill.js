var rA=require("plug/requestAction.js");
var bill={
    index:function(){
        $('[data-act="pay"]').on("click",function(){
            rA.paytip(this);
        })

        require("plug/datetimepicker.js");
        $('[name="orderDate"]').datetimepicker({
            lang:"ch",
            format:"Y/m",
            timepicker:false,
            todayButton:false
        });
    },
    detail:function(){
        $('[data-act="pay"]').on("click",function(){
            rA.paytip(this);
        })
    }
};
module.exports=bill;