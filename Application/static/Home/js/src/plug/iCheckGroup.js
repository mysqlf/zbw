/*!
 * iCheckGroup
 * 模拟单选按钮组
 */
;(function($){
    $.fn.iCheckGroup = function(options){
        var defaults = {
            data:null,
            type:"checkbox",
            name:null,
            emptyVal:"",
            defVal:'',
            required:true,
            change:null
        };
        var params = $.extend({}, defaults, options || {});
        $(this).each(function(index, element){
            var $element=$(this);
            var type = params.type;
            var required = params.required;
            var defVal = params.defVal || '';
            var emptyVal = params.emptyVal || '';
            if($element.find("ul")){
                type=$element.find("ul").is(".radio_list")?"radio":"checkbox";
            }
            if($element.find("input")){
                defVal=$element.find("input").val();
                //为扩展checkbox字段不能这空时，给定一个值的设定值
                emptyVal= $element.find("input").attr("data-empty");
            }


            //初始选中状态
            if(defVal!=""){
                var defValArr=defVal.split(",");
                var len=defValArr.length;
                for(var i= 0;i<len;i++){
                    $element.find('[data-val="'+defValArr[i]+'"]').addClass("selected");
                }
            }else{
                if(type=="checkbox"){
                    defVal=$element.find("input").val(emptyVal);
                }
            }

            var getArrIndex = function(arr,val) {
                for (var i = 0; i < arr.length; i++) {
                    if (arr[i] == val) return i;
                }
                return -1;
            };
            var delArrItem = function(arr,val){
                var index = getArrIndex(arr,val);
                if (index > -1) {
                    arr.splice(index, 1);
                }
            };

            var getSelectFn=function(t,arr,v){
                var tempArr =[];
                tempArr = arr!="" ? arr : arr=[];
                if(t=="add"){
                    var flag=true;
                    for(var i=0;i<tempArr.length;i++){
                        if(tempArr[i]==v){
                            flag=false;
                        }
                    }
                    if(flag){
                        tempArr.push(v);
                    }
                }else if(t=="del"){
                    delArrItem(tempArr,v);
                }
                return tempArr;
            };

            $element.find("li").off("click").on("click",function(){
                var $this=$(this);
                if($(this).is(".disabled")){
                    return;
                }
                var defVal=$element.find("input").val();

                var v=$(this).data("val");
                var defValArr=defVal.split(",");
                var tempVal="";
                var name=$element.find("input")[0].name;
                var errorId='';
                if(name){
                    errorId=name;
                }
                if($(this).is(".selected")){
                    //去选
                    if(type=="radio"){
                        return;
                    }else if(type=="checkbox"){
                        tempVal=getSelectFn("del",defValArr,v).join(",");
                        tempVal = tempVal =="" ? emptyVal : tempVal;
                    }
                    $(this).removeClass("selected");

                }else{
                    //选中
                    //单选
                    if(type=="radio"){
                        $element.find(".selected").removeClass("selected");
                        tempVal=v;
                    }else if(type=="checkbox"){
                        defValArr = defVal == emptyVal ? "" : defValArr;
                        tempVal= getSelectFn("add",defValArr,v).join(",");
                    }
                    $(this).addClass("selected");
                }
                $element.find("input").val(tempVal);
                //验证标签显示
                if(required){
                    if(errorId!="" && $('[for="'+errorId+'"]')){
                        if(tempVal == emptyVal){
                            $element.find("input").addClass("error");
                            $('[for="'+errorId+'"]').show();
                        }else{
                            $element.find("input").removeClass("error");
                            $('[for="'+errorId+'"]').hide();
                        }
                    }
                }
                if(typeof params.change == "function"){
                    params.change($element,$this);
                }
            });
        });
        return $(this);
    };
})(jQuery);