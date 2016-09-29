var common={
    json:{
        /*!
         * 功能：json数据生成下拉列表项
         * 参数：
         * data--json数据,格式：id:"",name="";
         * defaultValue--选中值
         * fv--value的字段，可设置为name字段作为表单值即"name"， 默认为id，不省略
         */
        getOptions:function(data,defaultValue,fv){
            var title="name";
            var id="id";
            if(typeof resumeLang === "string"){
                title=resumeLang=="cn"?"name":"en";
            }
            if(fv=="name"){
                id=title;
            }
            return common.json.toOptions(data,id,title,defaultValue);
        },
        /*!
         * 功能：json数据生成下拉列表项
         * 参数：
         * data--json数据,字段id,title
         * defaultValue--选中值
         */
        toOptions:function(data,id,title,defaultValue){
            var html=[],v,t;
            var len=data.length;
            html.push('<option value="">请选择</option>');
            for(var i=0;i<len;i++){
                v=data[i][id];
                t=data[i][title];
                html.push('<option value="'+v+'"');
                if(defaultValue==v && defaultValue!='' && defaultValue!=null){html.push(' selected')};
                html.push(">");
                html.push(t);
                html.push('</options>');
            }
            return html.join('');
        },
        /*!
         * 功能：根据字段值获取对应的索引值
         */
        getIndexByFile:function(data,file,value){
            var len=data.length;
            for(var key=0;key<len;key++){
                if(data[key][file]==value){
                    return key;
                }
            }
            return -1;
        },
        /*!
         * 功能：地区联动
         * boxId:省市数据josn文件路径
         * prov:默认省份
         * city:默认城市
         * dist:默认地区（县）
         * nodata:无数据状态
         * required:必选项
         */
        area:function(options){
            var defaults={
                boxId:"areaBox",
                prov:null,
                city:null,
                dist:null,
                address:null,
                nodata:null,
                hasSuffix:true,
                fv:"id",
                change:null,
                load:null,
                required:true
            };
            var settings = $.extend({}, defaults, options);
            var box_obj=$("#"+settings.boxId);
            var prov_obj=box_obj.find(".prov");
            var city_obj=box_obj.find(".city");
            var dist_obj=box_obj.find(".dist");
            var title="name";
            var defText="请选择";
            if(typeof resumeLang === "string" && resumeLang=="en"){
                title="en";
                defText="select"
            }

            var select_prehtml=(settings.required) ? "" : "<option value=''>"+defText+"</option>";
            var dataCity=datajson.City;
            var dist=datajson.town;


            var suffix="";
            var suffixV="";

            if(settings.fv=="name"){
                settings.fv=title;
            }else{
                settings.fv="id";
            }

            var getSuffix=function(s){
                var suffix="";
                if(settings.hasSuffix){
                    suffix=s;
                    if(typeof resumeLang === "string"){
                        suffix=resumeLang=="cn"?s:"";
                    }
                }
                return suffix;
            };

            //获取地区全称，主要是兼容新旧数据处理
            var getAllName = function(v,arrSuff){
                var hasSuff=false;
                var allName=v;
                if(v==null || v==""){
                    return "";
                }
                //判断是否有后缀
                for(var i= 0,len=arrSuff.length;i<len;i++){
                    if(v.indexOf(arrSuff[i])!=-1){
                        hasSuff=true;
                        break;
                    }
                }
                //无后缀时找出其后缀
                if(!hasSuff){
                    for(var key= 0,len=dataCity.length;key<len;key++){
                        if(dataCity[key].name==v){
                            allName=v+dataCity[key].s;
                            break;
                        }
                    }
                }
                return allName;
            };

            var init=function(){
                // 遍历赋值省份下拉列表
                temp_html=select_prehtml;
                for(var key= 0,len=dataCity.length;key<len;key++){
                    if(dataCity[key].id.toString().substr(2,2) == "00" && dataCity[key].s!=""){
                        temp_html+='<option value="'+dataCity[key][settings.fv]+'" data-id="'+dataCity[key].id+'">'+dataCity[key][title]+'</option>';
                    }
                }
                prov_obj.html(temp_html);

                // 若有传入省份与市级的值，则选中。（setTimeout为兼容IE6而设置）
                t && clearTimeout(t);
                var t=setTimeout(function(){
                    if(settings.prov!=null){
                        prov_obj.val(settings.prov);
                        cityStart();
                        setTimeout(function(){
                            if(settings.city!=null){
                                var defCity=getAllName(settings.city,["市","地区","州"]);
                                city_obj.val(defCity);
                                distStart();
                                setTimeout(function(){
                                    if(settings.dist!=null){
                                        dist_obj.val(settings.dist);
                                    };
                                    if(typeof settings.load =="function" ){
                                        settings.load();
                                    }
                                },1);
                            }else{
                                if(typeof settings.load =="function" ){
                                    settings.load();
                                }
                            }
                        },1);
                    }else{
                        if(typeof settings.load =="function" ){
                            settings.load();
                        }
                    }
                },1);

                // 选择省份时发生事件
                prov_obj.off("change").on("change",function(){
                    cityStart();
                    if(typeof settings.change =="function" ){
                        settings.change(0,$(this));
                    }
                });

                // 选择市级时发生事件
                city_obj.off("change").on("change",function(){
                    distStart();
                    if(typeof settings.change =="function" ){
                        settings.change(1,$(this));
                    }
                });
                dist_obj.off("change").on("change",function(){
                    if(typeof settings.change =="function" ){
                        settings.change(2,$(this));
                    }
                });
            };



            // 赋值市级函数
            var cityStart=function(){
                var prov_id=common.getSelectedAttr(prov_obj,"data-id") || 0;
                city_obj.empty().attr("disabled",true);
                dist_obj.empty().attr("disabled",true);
                if(prov_id <= 0){
                    if(settings.nodata=="none"){
                        city_obj.css("display","none");
                        dist_obj.css("display","none");
                    }else if(settings.nodata=="hidden"){
                        city_obj.css("visibility","hidden");
                        dist_obj.css("visibility","hidden");
                    };
                    return;
                }
                //common.log(prov_id)
                // 遍历赋值市级下拉列表
                temp_html=select_prehtml;
                if( parseInt(prov_id.substr(0,4),10) >=4100 || parseInt(prov_id.substr(0,4),10) <=1300){
                    for(var key= 0,len=dataCity.length;key<len;key++){
                        if(dataCity[key].id ==prov_id && dataCity[key].s!=""){
                            temp_html+='<option value="'+dataCity[key][settings.fv]+getSuffix(dataCity[key].s)+'" data-id="'+dataCity[key].id+'">'+dataCity[key][title]+getSuffix(dataCity[key].s)+'</option>';
                        }
                    }
                }else{
                    var i=0;
                    for(var key= 0,len=dataCity.length;key<len;key++){
                        if(dataCity[key].id.toString().substr(0,2) == prov_id.toString().substr(0,2) && dataCity[key].id !=prov_id && dataCity[key].s!=""){
                            temp_html+='<option value="'+dataCity[key][settings.fv]+getSuffix(dataCity[key].s)+'" data-id="'+dataCity[key].id+'">'+dataCity[key][title]+getSuffix(dataCity[key].s)+'</option>';
                            i++;
                        }
                    }
                    if(i==0){
                        if(settings.nodata=="none"){
                            city_obj.css("display","none");
                            dist_obj.css("display","none");
                        }else if(settings.nodata=="hidden"){
                            city_obj.css("visibility","hidden");
                            dist_obj.css("visibility","hidden");
                        };
                    }
                }

                city_obj.html(temp_html).attr("disabled",false).css({"display":"","visibility":""});
                distStart();
            };
            // 赋值地区（县）函数
            var distStart=function(){
                var prov_id=common.getSelectedAttr(prov_obj,"data-id");
                var city_id=common.getSelectedAttr(city_obj,"data-id");

                dist_obj.empty().attr("disabled",true);

                if(!prov_id>0||!city_id>0){
                    if(settings.nodata=="none"){
                        dist_obj.css("display","none");
                    }else if(settings.nodata=="hidden"){
                        dist_obj.css("visibility","hidden");
                    };
                    return;
                };

                // 遍历赋值市级下拉列表
                temp_html=select_prehtml;
                var i=0;
                for(var key= 0,len=dist.length;key<len;key++){
                    if(dist[key].id.toString().substr(0,4) == city_id.toString().substr(0,4)){
                        temp_html+='<option value="'+dist[key][settings.fv]+'" data-id="'+dist[key].id+'">'+dist[key][title]+'</option>';
                        i++;
                    }
                }
                if(i==0){
                    if(settings.nodata=="none"){
                        dist_obj.css("display","none");
                    }else if(settings.nodata=="hidden"){
                        dist_obj.css("visibility","hidden");
                    };
                    return;
                }
                dist_obj.html(temp_html).attr("disabled",false).css({"display":"","visibility":""});
            };
            init();
        }

    },
    /*!
     * 功能：数组去重,并移除空值
     * 2013-01-24
     */
    arr:{
        unique : function(arr){
            var res=[],hash={};
            var len=arr.length;
            for(var i=0;i<len;i++) {
                if(!hash[arr[i]]){
                    if(arr[i]!=""){
                        res.push(arr[i]);
                    }
                    hash[arr[i]] = true;
                }
            }
            return res;
        }
    },

    /*！
     * 功能：IE判断系列，IE6及以上
     * 用法：无参数为IE，IE--common.isIE();参数为整数，为IE版本，IE6--common.isIE(6) ...
     * 2013-01-23
     */
    isIE:function(n){
        //jQ1.9后去除$.browser，所以这里不再用jQ去判断
        var browser=/(msie) ([\w.]+)/.exec((navigator.userAgent).toLowerCase());
        if(!browser){
            return false;
        }else{
            var version=browser[2];
            var isIE6 = parseFloat(version) < 7;
            if(n==6){
                return isIE6;
            }else if(n>6){
                return parseFloat(version)==n;
            }else{
                return true;
            }
        }
    },
    /*
     * 功能：获select下拉单的选中属性值
     * $oselect下拉单对象，attrName属性名称，可以是自定义的属性。
     */
    getSelectedAttr : function($o,attrName){
        var id=null;
        $o.find('option').each(function(){
            if($(this).prop("selected")){
                id=$(this).attr(attrName);
            }
        });
        return id;
    },
    area:function(location){
        var self=this,
            prov="",
            city="",
            dist="";
        if(location){
            var type=self.getAreaType(location);
            location=location+"";
            switch(type){
                case 1:
                    prov=location;
                    break;
                case 2:
                    prov=location.replace(/\d{6}$/g,'000000');
                    city=location;
                    break;
                case 3:
                    prov=location.replace(/\d{6}$/g,'000000');
                    city=location.replace(/\d{4}$/g,'0000');
                    dist=location;
                    break;
            }

        }
        self.json.area({
            boxId:"areaBox",
            prov:prov,
            city:city,
            dist:dist,
            hasSuffix:false,
            required:false,
            nodata:"none"
        });
    },
    //地区ID类型判断
    //0为非法；1为省；2为市；3为区；4为点
    getAreaType : function(id){
        if(id==null || id=="" || id==0){
            return 0;
        }
        var id=id.toString();
        //省
        if(parseInt(id.substr(2),10)==0){
            return 1;
        }
        //市
        if(id.length==4){
            return 2;
        }
        if(parseInt(id.substr(4),10)==0){
            return 2;
        }

        //区
        if(parseInt(id.substr(6),10)==0){
            return 3;
        }

        //点
        if(parseInt(id.substr(6),10)> 0){
            return 4;
        }
    }
};



module.exports=common;