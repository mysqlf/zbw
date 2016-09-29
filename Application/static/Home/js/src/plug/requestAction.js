var uploader=require("plug/uploader.js"),
    template = require('art-template');
var rA={
    /*鼠标多次点击开关处理*/
    sendAjax:function(options){
        var defaults = {
            $obj:null,      //点击的对象
            url:null,		//请求URL
            data:null,
            type:"post",
            success:null,		//完成动作后的回调，参数为返回JOSN
            erorr:null,
            complete:null
        };
        var settings = $.extend({}, defaults, options);
        var $obj=settings.$obj;
        if($obj!=null){
            var flag = $obj.data('flag');
            if(flag) {
                return;
            }
            $obj.data('flag',true);
        }
        var ajaxJson={
            url:settings.url,
            type:settings.type,
            data:settings.data,
            success:settings.success
        };
        if(typeof settings.erorr === "function"){
            ajaxJson.erorr=settings.erorr;
        }else{
            ajaxJson.erorr=function(){
                layer.alert("请求失败，请稍后再试");
                if($obj!=null){
                    $obj.data('flag',false);
                }
            };
        }
        if(typeof settings.complete === "function"){
            ajaxJson.complete=settings.complete;
        }
        if(typeof settings.success === "function"){
            ajaxJson.success=function(data){
                settings.success(data);
                if($obj!=null){
                    $obj.data('flag',false);
                }
            };
        }

        return $.ajax(ajaxJson);

    },
    /*头部*/
    header:{
        /*头部信息*/
        message:function(cb){
            var self=this;
            var success=function(data){
                if(typeof cb == "function"){
                    cb(data);
                }
            };
            var ajaxFn=function(){
                rA.sendAjax({
                    url:"/Company-Information-msgCount?t="+new Date().getTime(),
                    success:success,
                    timeout: 30000
                });
            };
            var messages=function(){
                if(!self.messageTimer){
                    ajaxFn();
                    self.messageTimer = setInterval(ajaxFn,60000);
                }
            };
            messages();
        }
    },

    layer:{
        repeal:function(options){
            var defaults={
                $obj:null,
                data:null,
                url:null,
                msg:''
            };
            var opts=$.extend(defaults ,options);
            layer.confirm(opts.msg,function(){
                rA.sendAjax({
                    $obj:opts.$obj,
                    url:opts.url+"?t="+new Date().getTime(),
                    data:opts.data,
                    success:function(data){
                        if(data.status==1){
                            layer.msg("操作成功！",{},function(){window.location.reload()});
                        }else{
                            layer.alert(data.info);
                        }
                    }
                });
            })
        }
    },
    //社保状态
    insurance:{
        repeal:function(obj,url,msg){
            var $obj=$(obj);
            var id=$obj.data("id");
            rA.layer.repeal({
                $obj:$obj,
                data:{serviceOrderDetailIds:id},
                url:url,
                msg:msg
            });
        },
        reduce:function(obj,$form,cb){
            var $obj=$(obj);
            var id=$obj.data("id");
            rA.sendAjax({
                $obj:$obj,
                url:"/Company-Insurance-toReduce?t="+new Date().getTime(),
                data:$form.serialize(),
                success:function(data){
                    if(typeof cb == "function"){
                        cb(data);
                    }
                }
            });
        },
        reducetip:function(obj,cb){
            var $obj=$(obj);
            var id=$obj.data("id");
            rA.sendAjax({
                $obj:$obj,
                url:"/Company-Insurance-reduceStatus?t="+new Date().getTime(),
                data:{id:id},
                success:function(data){
                    if(typeof cb == "function"){
                        cb(data);
                    }
                }
            });
        },
        getPersonBaseByIdCard:function(val){
            rA.sendAjax({
                url:"/Company-Insurance-getPersonBaseByIdCard?t="+new Date().getTime(),
                data:{idCard:val},
                success:function(ret){
                    var data=ret.data;
                    if(ret.status == 1){
                        switch (ret.data.status){
                            case -2:
                                layer.alert('该员已在其他企业在保！',{end:function(){$("#card_num").val("");}});
                                break;
                            case -1:
                                layer.alert('该员已在其他企业报增！',{end:function(){$("#card_num").val("");}});
                                break;
                            case 1:
                                layer.open({

                                    btn:['重新填写','查看'],
                                    content: "该员已报增！",
                                    btn1: function(i){
                                        layer.close(i);
                                    },
                                    end: function(){
                                        $("#card_num").val("");
                                    },
                                    btn2: function(i){
                                        $("#card_num").val("");
                                        window.location.href=data.url;
                                    }
                                })
                                break;
                            case 2:
                                layer.open({

                                    btn:['重新填写','查看'],
                                    content: "该员已在保！",
                                    btn1: function(i){
                                        layer.close(i);
                                    },
                                    btn2: function(i){
                                        $("#card_num").val("");
                                        window.location.href=data.url;
                                    },
                                    end: function(){
                                        $("#card_num").val("");
                                    }
                                })
                                break;
                            case 0:

                                /*if(typeof data.gender != "undefined"){
                                 var sex = data.gender;
                                 $('#user_name').val(data.user_name);
                                 $('#sex'+sex).prop('checked',true);
                                 $('#mobile').val(data.mobile)
                                 }*/
                                break;
                        }
                    }
                }
            });
        }

    },
    upload:function(options){
        var defaults={
            $obj:null,
            url:null,
            data:null,
            uploader:null,
            isImg:true,
            id:null,
            fileVal:'file',
            auto:true,
            success:null
        };
        var opts=$.extend(defaults ,options);
        var $obj=$(opts.id),
            isImg=opts.isImg;
        var $upload=$obj.find(".upload");
        var $jupload=$upload.find(".jUpload");
        var $toloadBtn=$upload.find(".toloadBtn");
        var $uploaded=$obj.find(".uploaded");
        var $uploading=$obj.find(".uploading");
        var $delBtn=$obj.find(".del");
        var $input=$obj.find('[data-act="upload"]');
        var certificate=$uploaded.find('.jSrc').data("src");
        var webUpload={};
        webUpload.uploaded=function(){
            $uploaded.show();
            $delBtn.show();
            $toloadBtn.show();
            $jupload.hide();
            $uploading.hide();
        };
        webUpload.uploading=function(){
            $uploading.show();
            $toloadBtn.show();
            $jupload.hide();
            $uploaded.hide();
            $delBtn.hide();
        };
        webUpload.upload=function(){
            $jupload.show();
            $uploading.hide();
            $toloadBtn.hide();
            $uploaded.hide();
            $delBtn.hide();
        };
        if(certificate){
            webUpload.uploaded();
        }
        webUpload.uploader=uploader.init({
            id:opts.uploader,
            url:opts.url+"?t="+new Date().getTime(),
            delUrl:opts.delUrl+"?t="+new Date().getTime(),
            data:opts.data,
            delData:opts.delData,
            fileVal:opts.fileVal,
            isImg:isImg,
            auto:opts.auto,
            progress:function(file, percentage){
                webUpload.uploading();
                $uploading.find("span").css( 'width', percentage * 100 + '%' );
            },
            success:function(file,data){
                if(data.status ==1){
                    webUpload.uploaded();
                    if(typeof opts.success == "function"){
                        opts.success(file,data,webUpload);
                        return;
                    }
                    if(!isImg){
                        $('.jProtocol').attr("href",data.info);
                    }else{
                        $('[data-act="preview"]').data("src",data.info);
                    }
                    $input.val(data.info);
                }else{
                    layer.alert(data.info);
                }
            },
            error:function(){
                layer.msg('上传失败，请稍后再试！', {icon: 5});
                webUpload.upload();
            }
        });

        //删除
        $delBtn.find(".delBtn").on("click",function(){
            layer.confirm("是否删除已上传的文件！",function(){
                rA.sendAjax({
                    $obj:$(this),
                    url:opts.delUrl,
                    data:{file:$input.val()},
                    success:function(data){
                        if(data.status==1){
                            webUpload.upload();
                            $('[data-act="preview"]').data("src",data);
                            $input.val("");
                            layer.msg("删除成功！");
                        }else{
                            layer.alert(data.info);
                        }
                    }

                })
            });

        });
        return webUpload;
    },
    /**
     * 编辑实发工资
     */
    editSalary: function(data){
       var opts = {};

       opts.url = "Company-Salary-getSalaryRecordDetail";
       opts.data = data;

        return rA.sendAjax(opts);
    },
    /**
     * 保存实发工资
     */
    saveSalary: function(data){
       var opts = {};

       opts.url = "Company-Salary-editSalary";
       opts.data = data;

        return rA.sendAjax(opts);
    },
    //撤销实发工资
    delSalary: function(data){
        var opts = {};

       opts.url = "Company-Salary-cancelSalary";
       opts.data = data;

        return rA.sendAjax(opts);
    },
    service:{
        checkMessage:function(obj,cb){
            var $obj=$(obj),
                id=$obj.data("id");
            rA.sendAjax({
                $obj:$obj,
                url:"/Company-Information-msgDetail?t="+new Date().getTime(),
                data:{msgId:id},
                success:function(data){
                    if(typeof cb == "function"){
                        cb(data);
                    }
                }

            })
        }
    },
    // 获取产品列表 实发工资 导入订单
    getProductList: function(data){
        var opts = {};

        opts.url = "Company-Salary-getProductList";
        opts.data = data;

        return rA.sendAjax(opts);
    },
    // 获取城市列表 实发工资 导入订单
    getCityByProduct: function(data){
        var opts = {};

        opts.url = "Company-Salary-getCityByProductOrderId";
        opts.data = data;

        return rA.sendAjax(opts);
    },
    uploadSalary: function(data){
        var opts = {};

        opts.url = "Company-Salary-getExcel";
        opts.data = data;

        return rA.sendAjax(opts);
    },
    paytip:function(obj,type){
        var $this=$(obj),
            No=$this.data("no"),
            id=$this.data("id");

        if(!No){
            No=$("#billNo").val();
        }
        if(!id){
            id=$("#billId").val();
        }
        var data={billId:id,billNo:No};

        var url="/Company-Bill-payInfo";

        if(type == "service"){
            url="/Company-Information-payInfo";
            data = {productId: id};
        }

        var success=function(ret){
            if(ret.status === 0){
                layer.alert(ret.info);
                return;
            }
            var html = "";
            if(type == "service"){
                html = template.render(require("tpl/servicepay.vue").template)(ret.data);
            }else{
                html = template.render(require("tpl/paytip.vue").template)(ret.data);
            }

            var i=layer.open({
                title:"付款",
                area:"520px",
                btn:[],
                content:html
            });
            var $payTip=$("#payTip"),
                $li=$payTip.find(".tit li"),
                $item=$payTip.find(".cont .item");
            $li.on("click",function(){
                var $this=$(this);
                var index=$this.index();
                $li.removeClass("active");
                $this.addClass("active");
                $item.hide();
                $item.eq(index).show();
            });
            $item.find(".arrowsL").on("click",function(){
                $li.eq(0).trigger("click");
            });
            $item.find(".blueB").on("click",function(){
                layer.close(i);
            });

        };

        rA.sendAjax({
            $obj:$this,
            url:url,
            data:data,
            type:"post",
            success:success
        });
    }

};

module.exports = rA;