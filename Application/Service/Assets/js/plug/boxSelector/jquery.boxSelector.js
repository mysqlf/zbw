require('./jquery.boxSelector.css');

let datajson = require('./area')

/**
 * 主要城市JSON数据
 * */
datajson.mainCity = [
    { "id": 11000000, "name": "北京", "en": "beijing" },
    { "id": 10000000, "name": "上海", "en": "shanghai" },
    { "id": 14030000, "name": "广州", "en": "guangzhou" },
    { "id": 14020000, "name": "深圳", "en": "shenzhen" },
    { "id": 14010000, "name": "东莞", "en": "dongguan" },
    { "id": 14090000, "name": "佛山", "en": "foshan" },
    { "id": 14040000, "name": "中山", "en": "zhongshan" },
    { "id": 14080000, "name": "江门", "en": "jiangmen" },
    { "id": 14070000, "name": "惠州", "en": "huizhou" },
    { "id": 23010000, "name": "西安", "en": "xian" },
    { "id": 28010000, "name": "长沙", "en": "changsha" },
    { "id": 28020000, "name": "湘潭", "en": "xiangtan" },
    { "id": 29010000, "name": "南昌", "en": "nanchang" },
    { "id": 13000000, "name": "重庆", "en": "chongqing" },
    { "id": 27010000, "name": "武汉", "en": "wuhan" },
    { "id": 12000000, "name": "天津", "en": "tianjin" },
    { "id": 32010000, "name": "成都", "en": "chengdu" },
    { "id": 16020000, "name": "苏州", "en": "suzhou" },
    { "id": 15020000, "name": "宁波", "en": "ningbo" },
    { "id": 16010000, "name": "南京", "en": "nanjing" },
    { "id": 17030000, "name": "泉州", "en": "quanzhou" },
    { "id": 24010000, "name": "合肥", "en": "hefei" },
    { "id": 28070000, "name": "郴州", "en": "chenzhou" },
    { "id": 28060000, "name": "衡阳", "en": "hengyang" },
    { "id": 27040000, "name": "荆州", "en": "jingzhou" },
    { "id": 14050000, "name": "珠海", "en": "zhuhai" },
    { "id": 46000000, "name": "三沙", "en": "Sansha" },
    { "id": 47000000, "name": "钓鱼岛", "en": "Diaoyudao" }
];;
(function($) {
    var BoxSelector = function(element, options) {
        var defaults = {
            key: "city",
            allowWrite: false,
            boxId: "selectBox",
            boxClass: "selBox",
            max: 3,
            selectType: "checkbox",
            selectParent: true,
            setHeight: 400,
            setWidth: 806,
            maskModal: true, //遮罩
            boxPos: "left",
            arrowXY: "", //static,
            offsetX: 0,
            offsetY: 0,
            draggable: true,
            closed: null,
            showed: $.noop
        };
        this.name = "name";
        this.enClass = "";
        if (typeof resumeLang === "string") {
            this.name = resumeLang == "en" ? "en" : "name";
            this.enClass = resumeLang == "en" ? " en" : "";
        }
        this.setting = $.extend({}, defaults, options);
        /*配置*/
        this.config = {
            "city": {
                "jsonData": datajson.City,
                "townData": datajson.town,
                "template": '<div class="titBar">' +
                    '<h2>请选择地区</h2>' +
                    '<span class="fl">(最多选择{#max}个)</span>' +
                    '</div>' +
                    '<div class="toolBar">' +
                    '<div id="js_selectedBox" class="selectedCont clearfix"><div class="tip">已选地区</div>' +
                    '<div class="selectedA"></div></div><div class="btnArea">' +
                    '<a data-act="del" href="#" class="btnGray" title="清除">' +
                    '<span>清除</span>' +
                    '</a>' +
                    '</div>' +
                    '</div>' +
                    '<div class="clearer"></div>' +
                    '<div class="hotCI' + this.enClass + '">' +
                    '<h3>热门地区：</h3>' +
                    '{#hotCI}' +
                    '</div>' +
                    '<div class="list' + this.enClass + '" id="js_List">' +
                    '<h3>省/市/直辖市：</h3>' +
                    '<dl class="odd clearfix">' +
                    '<dt>A.B.C.F</dt>' +
                    '<dd>{#Item_ABCF}</dd>' +
                    '</dl>' +
                    '<dl class="even clearfix">' +
                    '<dt>G</dt>' +
                    '<dd>{#Item_G}</dd>' +
                    '</dl>' +
                    '<dl class="odd clearfix">' +
                    '<dt>H</dt>' +
                    '<dd>{#Item_H}</dd>' +
                    '</dl>' +
                    '<dl class="even clearfix">' +
                    '<dt>J.L.N.Q</dt>' +
                    '<dd>{#Item_JLNQ}</dd>' +
                    '</dl>' +
                    '<dl class="odd clearfix">' +
                    '<dt>S</dt>' +
                    '<dd>{#Item_S}</dd>' +
                    '</dl>' +
                    '<dl class="even clearfix">' +
                    '<dt>T.X.Y.Z</dt>' +
                    '<dd>{#Item_TXYZ}</dd>' +
                    '</dl>' +
                    '</div>'
            },
            "town": {
                "jsonData": datajson.town
            },
            "post": {
                "jsonData": datajson.jobFun,
                "template": '<div class="titBar">' +
                    '<h2>请选择职位类别</h2>' +
                    '<span class="fl">(最多选择{#max}个)</span>' +
                    '</div>' +
                    '<div class="toolBar clearfix">' +

                    '</div>' +
                    '<div id="js_selectedBox" class="selectedCont clearfix">' +
                    '<div class="tip">已选职位</div>' +
                    '<div class="selectedA"></div>' +
                    '<div class="btnArea">' +
                    '<a data-act="del" href="#" class="btnGray" title="清除">' +
                    '<span>清除</span>' +
                    '</a>' +
                    '</div>' +
                    '</div>' +
                    '<div class="clearer"></div>' +
                    '<div class="list" id="js_List">{#ItemList}</div>'
            },
            "industry": {
                "jsonData": datajson.industry,
                "template": '<div class="titBar">' +
                    '<h2>请选行业类别</h2>' +
                    '<span class="fl">(最多选择{#max}个)</span>' +
                    '</div>' +
                    '<div class="toolBar">' +
                    '<div id="js_selectedBox" class="selectedCont clearfix">' +
                    '<div class="tip">已选行业</div>' +
                    '<div class="selectedA"></div>' +
                    '<div class="btnArea">' +
                    '<a data-act="del" href="#" class="btnGray" title="清除">' +
                    '<span>清除</span>' +
                    '</a>' +
                    '</div>' +
                    '</div>' +

                    '<div class="clearer"></div>' +
                    '</div>' +
                    '<div class="list" id="js_List">{#ItemList}</div>'
            },
            "cert": {
                "jsonData": datajson.cert,
                "template": '<div class="titBar">' +
                    '<h2>请选择证书名称</h2>' +
                    '<span class="fl">(最多选择{#max}个)</span>' +
                    '</div>' +
                    '<div class="toolBar">' +
                    '<div id="js_selectedBox" class="selectedCont clearfix">' +
                    '<div class="tip">已选证书名称</div>' +
                    '<div class="selectedA"></div>' +
                    '</div>' +
                    '<div class="btnArea">' +
                    '<a data-act="del" href="#" class="btnGray" title="清除">' +
                    '<span>清除</span>' +
                    '</a>' +
                    '</div>' +
                    '<div class="clearer"></div>' +
                    '</div>' +
                    '<div class="list certCat" id="js_List">{#ItemList}</div>'
            },
            "tagJobs": {
                "jsonData": datajson.tagJobs,
                "template": '<div class="titBar">' +
                    '<h2>请选淘标签</h2>' +
                    '<span class="fl">(最多选择{#max}个)</span>' +
                    '</div>' +
                    '<div class="toolBar">' +
                    '<div id="js_selectedBox" class="selectedCont clearfix">' +
                    '<div class="tip">已选淘标签</div>' +
                    '<div class="selectedA"></div>' +
                    '</div>' +
                    '<div class="btnArea">' +
                    '<a data-act="del" href="#" class="btnGray" title="清除">' +
                    '<span>清除</span>' +
                    '</a>' +
                    '</div>' +
                    '<div class="clearer"></div>' +
                    '</div>' +
                    '<div class="list tagJobCat" id="js_List">{#ItemList}</div>'
            },
            "mainCity": {
                "jsonData": datajson.mainCity,
                "template": '<div class="titBar">' +
                    '<h2>请选择地区</h2>' +
                    '<span class="fl">(最多选择{#max}个)</span>' +
                    '</div>' +
                    '<div class="toolBar">' +
                    '<div id="js_selectedBox" class="selectedCont clearfix">' +
                    '<div class="tip">已选地区</div>' +
                    '<div class="selectedA"></div>' +
                    '</div>' +
                    '<div class="btnArea">' +
                    '<a data-act="del" href="#" class="btnGray" title="清除">' +
                    '<span>清除</span>' +
                    '</a>' +
                    '</div>' +
                    '</div>' +
                    '<div class="clearer"></div>' +
                    '<div class="list" id="js_List">{#ItemList}</div>'
            }
        };

        this.initialize(element, options);
    };

    BoxSelector.prototype = {
        initialize: function(element, options) {
            var that = this;
            this.$element = $(element);
            var key = that.setting.key.split("-")[0];
            var jsonData = that.config[key].jsonData;
            var idSuffix = this.$element.attr("id").replace("btn_", "");
            var $namesObj = $("#name_" + idSuffix);
            var $idsObj = $("#id_" + idSuffix);

            if ($namesObj.val() != "") {
                this.$element.find("span").hide();
            }
            if ($idsObj.length) {
                //初始值
                if ($idsObj.val() != "") {
                    var strIds = $idsObj.val();
                    this.$element.find("span").hide();
                    if (that.setting.selectType == "radio") {
                        //4位地区直接变为8位地区
                        strIds = (key == "city" && strIds.length == 4) ? strIds + "0000" : strIds;
                        $idsObj.val(strIds);
                        $namesObj.val(that.getNamesByIds([strIds], jsonData)).focus().blur();
                    }
                    if (that.setting.selectType == "checkbox") {
                        var arrIds = strIds.split(",");
                        var aNames = [];
                        var arrIds2 = [];
                        var l = arrIds.length;
                        for (var i = 0; i < l; i++) {
                            var Aid = arrIds[i];
                            if (/^[0-9]+$/.test(Aid)) {
                                //4位地区直接变为8位地区
                                if (key == "city" && Aid.length == 4) {
                                    Aid = Aid + "0000";
                                }
                                arrIds2.push(Aid);
                                aNames.push(that.getNamesByIds([Aid], jsonData));
                            }
                        }
                        $idsObj.val(arrIds2);
                        $namesObj.val(aNames).focus().blur();
                    }
                }
            }
            if (that.setting.allowWrite && that.setting.key == "city") {
                //暂时只对地区支持
                //common.inputTip({obj:$namesObj});
                if (that.setting.selectType == "checkbox") {

                } else {

                }
                if (that.setting.selectParent) {
                    //TODO：数据限制
                    //var data=datajson.City;
                } else {
                    //var data=datajson.City;
                }
                $namesObj.autocomplete(datajson.City, {
                    scroll: false,
                    selectFirst: false,
                    multiple: that.setting.selectType == "checkbox" ? true : false,
                    multipleSeparator: that.setting.selectType == "checkbox" ? "," : "",
                    formatItem: function(row, i, max) {
                        return row.name;
                    },
                    formatResult: function(row) {
                        return row.name;
                    },
                    reasultSearch: function(row, v) //本场数据自定义查询语法 注意这是我自己新加的事件
                        {
                            //自定义在code或spell中匹配
                            if (row.data.f.toLowerCase().indexOf(v) == 0 || row.data.en.toLowerCase().indexOf(v) == 0 || row.data.name.toLowerCase().indexOf(v) != -1) {
                                return row;
                            } else {
                                return false;
                            }
                        }
                }).result(function(event, data, formatted) {
                    //去重复
                    var v = $namesObj.val();
                    var arrV = v.split(",");
                    var arrNames = common.arr.unique(arrV);
                    //if(arrNames.length>that.setting.max){
                    //alert("对不起，最多只能选择"+that.setting.max+"个");
                    //return false;
                    //}
                    if (that.setting.selectType == "checkbox") {
                        $namesObj.val(arrNames + ",");
                    }
                    var arrIds = that.getIdsByNames(arrNames, jsonData);
                    $idsObj.val(arrIds);

                }).blur(function() {

                }).keyup(function() {
                    /*中文逗号转换*/
                    if ($(this).val().indexOf("，") != -1) {
                        $(this).val($(this).val().replace(/，/g, ","));
                    }
                    if ($(this).val().indexOf(",") != -1) {
                        var arrNames = $namesObj.val().split(",");
                        //var temV=$(this).val();
                        if (arrNames.length > that.setting.max) {
                            //$(this).val(temV);
                            alert("对不起，最多只能选择" + that.setting.max + "个");
                            return false;
                        }
                        $(this).val(arrNames.toString().replace(",,", ','));

                        var arrIds = that.getIdsByNames(arrNames, jsonData);
                        $idsObj.val(arrIds);
                    } else {
                        var nameArr = [];
                        nameArr.push($(this).val());
                        $idsObj.val(that.getIdsByNames(nameArr, jsonData));
                    }
                });
            }
            this.$element.click(function() {
                var $this = $(this);
                var objToId = "#js_List";
                var $objBox = $("#" + that.setting.boxId);
                if ($objBox.length) {
                    $objBox.remove();
                    return;
                };
                /*弹出选择框*/
                that.box({ boxId: that.setting.boxId, boxClass: that.setting.boxClass, draggable: that.setting.draggable, setHeight: that.setting.setHeight, setWidth: that.setting.setWidth }, function() {
                    var $objBox = $("#" + that.setting.boxId);
                    if (!that.setting.maskModal) {
                        var setTop = $this.offset().top + $this.outerHeight() + that.setting.offsetY;
                        var setLeft = that.setOffsetLeft($objBox);
                        /*弹出框坐标定位*/
                        $objBox.css({ top: setTop, left: setLeft });
                        /*箭头定位*/
                        if (that.setting.arrowXY == "") {
                            var arrowX = setLeft - $this.offset().left;
                            $objBox.find("i.arrow").css({ left: 10 - arrowX });
                        } else if (that.setting.arrowXY == "static") {
                            $objBox.find("i.arrow").css({ left: 10 });
                        } else {
                            $objBox.find("i.arrow").css();
                        }

                        /*窗口变化时重新定位*/
                        $(window).resize(function() {
                            var setLeft = that.setOffsetLeft($objBox);
                            $objBox.css("left", setLeft);
                        });
                    }

                    /*数据载入*/
                    $objBox.append(that.loadData());
                    /*可拖动鼠标状态*/
                    if (that.setting.draggable == true) {
                        $objBox.find(".titBar").css({ "cursor": "move" });

                    }
                    /*是否可以进行父选中*/
                    if (!that.setting.selectParent) {
                        $("#js_List").find(".hasSub input").attr("disabled", true).css({ visibility: "hidden" });
                    }

                    /**/
                    that.attachParentClickEvent($(objToId));
                    /*搜索条*/
                    if (key === "post") {
                        // that.searcher($objBox);
                    }

                    /*单选时，选中后直接关闭窗口*/
                    if (that.setting.selectType == "radio") {

                        $objBox.find(".titBar span").remove();
                        //$objBox.find(".btnOk").remove();
                    } else {

                        $objBox.append('<div class="sel-footer"><a href="#" class="btnOk" data-act="close" title="点击确定">确定</a></div>')
                            //$objBox.find(".btnClose").remove();
                    }

                    $objBox.find('[data-act="close"]').click(function() {
                        that.closeBox();
                        return false;
                    });

                    /*证书名称选择tab选项卡*/
                    if (key === "cert") {
                        var $tabItem = $objBox.find(".tab a");
                        var $tabItemCont = $objBox.find(".tabItemCont");
                        $tabItem.eq(0).addClass("selected");
                        $tabItemCont.eq(0).show();
                        $tabItem.on("click", function() {
                            var index = $(this).index();
                            $tabItem.removeClass("selected").eq(index).addClass("selected");
                            $tabItemCont.hide().eq(index).show();
                            return false;
                        });
                    }
                    if ($idsObj.length) {
                        //初始选项状态
                        var strIds = $idsObj.val();
                        var strNames = $namesObj.val();
                        if (strNames != "" && (strIds == "" || strIds == null) && that.setting.selectType == "checkbox") {
                            $namesObj.val("");
                            var arrNames = strNames.split(",");
                            var arrIds = that.getIdsByNames(arrNames, jsonData);
                            strIds = arrIds;
                        }
                        if (strIds != "" && that.setting.selectType == "checkbox") {
                            $idsObj.val("");
                            if (strIds instanceof Array) {
                                var arrIds = strIds;
                            } else {
                                var arrIds = strIds.split(",");
                            }
                            for (var i = 0, len = arrIds.length; i < len; i++) {
                                that.appendItemToA(arrIds[i]);
                            }
                        }
                        if (strIds != "" && that.setting.selectType == "radio") {
                            that.appendItemToA(strIds);
                        }
                    } else {
                        if (that.setting.selectType == "radio") {
                            var strNames = $namesObj.val();
                            $objBox.find(".item").each(function(index, element) {
                                if ($(this).attr("title") === strNames) {
                                    $(this).addClass("selected").find("input").prop("checked", true);
                                }
                            });
                        }
                    }
                    if (key === "city") {
                        //热门城市
                        if (that.setting.key.split("-")[1] === "town") {
                            that.attachParentClickEvent($objBox.find(".hotCI"));
                        } else {
                            that.attachChildClickEvent($objBox.find(".hotCI"));
                        }
                    }
                    /*清空选项*/
                    $objBox.find('[data-act="del"]').click(function() {
                        that.removeItemAll();
                        if (that.setting.selectType == "radio") {
                            //                            that.closeBox();
                        }
                        return false;
                    });

                    that.setting.showed($objBox);

                });
                return false;
            });


        },
        //弹出框
        box: function(options, fn) {
            var that = this;
            var defaults = {
                initWidth: 0,
                initHeight: 0,
                boxId: 'styleBox',
                boxClass: "styleBox",
                appendTo: $('body'), //创建位置
                setHeight: 400,
                setWidth: 620,
                maskModal: true, //遮罩
                setTop: 0,
                setLeft: 0,
                draggable: true
            };
            options = $.extend({}, defaults, options || {});
            var element = function(elm, obj) {
                //创建节点
                if ($("#" + obj).length) return;
                var $tag = $(elm);
                $tag.attr('id', obj);
                return $tag;
            };
            var mask = function() {
                //创建遮罩层节点
                if ($('#maskLayer').length) return;
                var $mask = element('<div></div>', 'maskLayer');
                $('body').append($mask);
                $($mask).css({ opacity: 0.3, width: '100%', height: $(document).height(), background: '#000', position: 'absolute', left: 0, top: 0, cursor: 'pointer', "z-index": 9000 });
                $mask.bgiframe();
            };
            var createBox = function() {
                //创建Cbox节点
                if ($('#' + options.boxId).length) return;
                var $objBox = element('<div class="' + options.boxClass + '"></div>', options.boxId);
                var strHtml = '<a href="#" class="btnClose" data-act="close" title="点击关闭">关闭</a>';

                $objBox.html(strHtml);


                if (options.maskModal) {
                    $objBox.find("i.arrow").remove();
                    var initPosLeft = parseInt(($(window).width() - options.initWidth) / 2);
                    var initPosTop = parseInt(($(window).height() - options.initHeight) / 2);
                    var winTop = 0;
                    $objBox.css({ position: 'fixed' });
                    $objBox.css({ width: options.initWidth, height: options.initHeight, top: initPosTop, left: initPosLeft });
                    //$objBox.css({left:initPosLeft,top:initPosTop,"z-index":500});
                    var cBoxLeft = parseInt(($(window).width() - options.setWidth) / 2);
                    var cBoxTop = parseInt(($(window).height() - options.setHeight) / 2 + winTop);

                    $objBox.animate({
                        width: options.setWidth + 'px',
                        height: options.setHeight + 'px',
                        left: cBoxLeft + 'px',
                        top: cBoxTop + 'px'
                    }, 400, function() {
                        $objBox.css({ height: 'auto', overflow: "visible" });
                    });
                    $(window).resize(function() {
                        var cBoxLeft = parseInt(($(window).width() - options.setWidth) / 2);
                        var cBoxTop = parseInt(($(window).height() - options.setHeight) / 2);
                        $objBox.css({ left: cBoxLeft + 'px', top: cBoxTop + 'px' });
                        $('#maskLayer').css({ width: $(document).width(), height: $(document).height() });
                    });
                } else {
                    $objBox.bgiframe();
                    $objBox.css({ left: options.setLeft, top: options.setTop });
                }
                if (options.draggable) {
                    $objBox.on("mousedown", ".titBar", function(event) {
                        var h = this;
                        var o = document;
                        var ox = parseInt($objBox.css("left"), 10);
                        var oy = parseInt($objBox.css("top"), 10);
                        var mx = event.clientX;
                        var my = event.clientY;
                        var w = $(window).width();
                        var wb = $objBox.width();
                        var h = $(window).height();
                        var hb = $objBox.height();

                        if (h.setCapture) h.setCapture();
                        var mousemove = function(event) {
                            if (window.getSelection) {
                                window.getSelection().removeAllRanges();
                            } else {
                                document.selection.empty();
                            }
                            var left = Math.max(ox + event.clientX - mx, 0);
                            var top = Math.max(oy + event.clientY - my, 0);
                            if (left >= w - wb && w - wb > 0) {
                                left = w - wb;
                            }
                            $objBox.css({ left: left, top: top });
                        };
                        var mouseup = function() {
                            if (h.releaseCapture) h.releaseCapture();
                            $(document).unbind('mousemove', mousemove);
                            $(document).unbind('mouseup', mouseup);
                        };
                        $(document).mousemove(mousemove).mouseup(mouseup);
                    });
                }

                options.appendTo.append($objBox);

                fn();
            };
            if (options.maskModal == true) {
                mask();
                createBox();
            }
        },
        /*关闭窗口*/
        closeBox: function() {
            $('#' + this.setting.boxId).remove();
            if (this.setting.maskModal) {
                $('#maskLayer').remove();
            }
            if (typeof this.setting.closed == "function") this.setting.closed();
        },
        /*设置弹出框的Left坐标值*/
        setOffsetLeft: function(obj) {
            var that = this;
            var $boxTarget = this.$element;
            var $box = obj;
            if (that.setting.boxPos == "left") {
                var setLeft = $boxTarget.offset().left + that.setting.offsetX;
            } else if (that.setting.boxPos == "right") {
                if (common.isIE()) {
                    var setLeft = $boxTarget.offset().left - ($box.outerWidth() - $boxTarget.outerWidth()) + that.setting.offsetX - 4;
                } else {
                    var setLeft = $boxTarget.offset().left - ($box.outerWidth() - $boxTarget.outerWidth()) + that.setting.offsetX;
                }
            }

            return setLeft;
        },

        /*判断是否有子项*/
        hasChild: function(idValue, jsonData) {
            var k = idValue.toString().substr(0, 2);
            var flg = false;
            for (var i = 0, mLen = jsonData.length; i < mLen; i++) {
                if (jsonData[i].id.toString().substr(0, 2) == k && jsonData[i].id != idValue) {
                    flg = true;
                    break; //提高性能，一但有就跳出
                }
            }
            return flg;
        },

        /*判断是否有town数据*/
        hasTownData: function(idValue) {
            var jsonData = datajson.town;
            var k = idValue.toString();
            var flg = false;
            for (var i = 0, mLen = jsonData.length; i < mLen; i++) {
                if (jsonData[i].id.toString().substr(0, 4) + "0000" == k) {
                    flg = true;
                    break; //提高性能，一但有就跳出
                }
            }
            return flg;
        },

        /*装载列表*/
        loadDataList: function(type, jsonData, id) {
            var that = this;
            var html = '';
            var idValue = id || "";
            switch (type) {
                case "city":
                    var hasSub;
                    var html = [];
                    var str = '';
                    var html_ABCF = '',
                        html_G = '',
                        html_H = '',
                        html_JLNQ = '',
                        html_S = '',
                        html_TXYZ = '';
                    for (var i = 0, mLen = jsonData.length; i < mLen; i++) {
                        if (jsonData[i].id.toString().substr(2, 2) == "00") {
                            if (that.hasChild(jsonData[i].id, jsonData)) {
                                hasSub = " hasSub";
                            } else {
                                hasSub = "";
                            }
                            str = '<a class="item' + hasSub + '" href="javascript:;" data-id="' + jsonData[i].id + '" title="' + jsonData[i][that.name] + '"><input type="' + that.setting.selectType + '" value="' + jsonData[i].id + '" /><label>' + jsonData[i][that.name] + '</label></a>';

                            if (jsonData[i].f.toString().substr(0, 1) == "A" || jsonData[i].f.toString().substr(0, 1) == "B" || jsonData[i].f.toString().substr(0, 1) == "C" || jsonData[i].f.toString().substr(0, 1) == "F") {
                                html_ABCF += str;
                            }
                            if (jsonData[i].f.substr(0, 1) == "G") {
                                html_G += str;
                            }
                            if (jsonData[i].f.substr(0, 1) == "H") {
                                html_H += str;
                            }
                            if (jsonData[i].f.substr(0, 1) == "J" || jsonData[i].f.substr(0, 1) == "L" || jsonData[i].f.substr(0, 1) == "N" || jsonData[i].f.substr(0, 1) == "Q") {
                                html_JLNQ += str;
                            }
                            if (jsonData[i].f.substr(0, 1) == "S") {
                                html_S += str;
                            }
                            if (jsonData[i].f.substr(0, 1) == "T" || jsonData[i].f.substr(0, 1) == "X" || jsonData[i].f.substr(0, 1) == "Y" || jsonData[i].f.substr(0, 1) == "Z") {
                                html_TXYZ += str;
                            }
                        }
                    }
                    html.push(html_ABCF);
                    html.push(html_G);
                    html.push(html_H);
                    html.push(html_JLNQ);
                    html.push(html_S);
                    html.push(html_TXYZ);
                    break;
                case "hotCI":
                    for (var i = 0, mLen = jsonData.length; i < mLen; i++) {
                        if (that.hasTownData(jsonData[i].id) && that.setting.key.split("-")[1] == "town") {
                            hasSub = " hasSub";
                        } else {
                            hasSub = "";
                        }
                        html += '<a class="item' + hasSub + '" href="javascript:;" data-id="' + jsonData[i].id + '" title="' + jsonData[i][that.name] + '"><input type="' + that.setting.selectType + '" value="' + jsonData[i].id + '" /><label>' + jsonData[i][that.name] + '</label></a>';
                    }
                    break;
                case "post":
                    for (var i = 0, mLen = jsonData.length; i < mLen; i++) {
                        if (jsonData[i].id.toString().substr(2, 4) == "00") {
                            html += '<a class="item itemPost hasSub" href="javascript:;" data-id="' + jsonData[i].id + '" title="' + jsonData[i][that.name] + '"><input type="' + that.setting.selectType + '" value="' + jsonData[i].id + '" /><label>' + jsonData[i][that.name] + '</label></a>';
                        }
                    }
                    break;
                case "industry":
                    for (var i = 0, mLen = jsonData.length; i < mLen; i++) {
                        html += '<a class="item itemInds" href="javascript:;" data-id="' + jsonData[i].id + '" title="' + jsonData[i][that.name] + '"><input type="' + that.setting.selectType + '" value="' + jsonData[i].id + '" />' + jsonData[i][that.name] + '</a>';
                    }
                    break;
                case "getDataById":
                    //子类筛选
                    for (var i = 0, mLen = jsonData.length; i < mLen; i++) {
                        if (jsonData[i].id.toString().substr(0, 2) == idValue.substr(0, 2) && jsonData[i].id != idValue) {
                            html += '<a class="item" href="javascript:;" data-id="' + jsonData[i].id + '" title="' + jsonData[i][that.name] + '"><input type="' + that.setting.selectType + '" value="' + jsonData[i].id + '" /><label>' + jsonData[i][that.name] + '</label></a>';
                        }
                    }
                    break;
                case "town":
                    //子类筛选
                    for (var i = 0, mLen = jsonData.length; i < mLen; i++) {
                        if (jsonData[i].id.toString().substr(0, 4) + "0000" == idValue && jsonData[i].id != idValue) {
                            html += '<a class="item" href="javascript:;" data-id="' + jsonData[i].id + '" title="' + jsonData[i][that.name] + '"><input type="' + that.setting.selectType + '" value="' + jsonData[i].id + '" /><label>' + jsonData[i][that.name] + '</label></a>';
                        }
                    }
                    break;
                case "cert":
                    var trow = 0;
                    var tabHtml = '',
                        contHtml = '';
                    for (var i = 0, mLen = jsonData.length; i < mLen; i++) {
                        if (jsonData[i].id.toString().substr(2, 2) == "00") {
                            tabHtml += '<a href="#">' + jsonData[i][that.name] + '</a>';
                            contHtml += '<div class="tabItemCont">'
                            for (var j = 0, mLen = jsonData.length; j < mLen; j++) {
                                if (jsonData[j].id.toString().substr(0, 2) == jsonData[i].id.toString().substr(0, 2) && jsonData[i].id != jsonData[j].id) {
                                    contHtml += '<a class="item" href="javascript:;" data-id="' + jsonData[j].id + '" title="' + jsonData[j][that.name] + '"><input type="' + that.setting.selectType + '" value="' + jsonData[j].id + '" /><label>' + jsonData[j][that.name] + '</label></a>';
                                }
                            }
                            contHtml += '</div>';
                            //html += '<dl class="'+trowClass+' clearfix"><dt>'+jsonData[i][that.name]+'</dt><dd>'+list+'</dd></dl>';
                        }
                    }
                    html += '<div class="tab">' + tabHtml + '</div>';
                    html += '<div class="tabContent">' + contHtml + '</div>';
                    break;
                case "tagJobs":
                    var trow = 0;
                    var trowClass = "odd";

                    for (var i = 0, mLen = jsonData.length; i < mLen; i++) {
                        if (jsonData[i].id.toString().substr(2, 2) == "00") {
                            var list = '';
                            for (var j = 0, mLen = jsonData.length; j < mLen; j++) {
                                if (jsonData[j].id.toString().substr(0, 2) == jsonData[i].id.toString().substr(0, 2) && jsonData[i].id != jsonData[j].id) {
                                    list += '<a class="item" href="javascript:;" data-id="' + jsonData[j].id + '" title="' + jsonData[j][that.name] + '"><input type="' + that.setting.selectType + '" value="' + jsonData[j].id + '" /><label>' + jsonData[j][that.name] + '</label></a>';
                                }
                            }
                            if (trow % 2 == 0) {
                                trowClass = "odd";
                            } else {
                                trowClass = "even";
                            }
                            trow++;
                            html += '<dl class="' + trowClass + ' clearfix"><dt>' + jsonData[i][that.name] + '</dt><dd>' + list + '</dd></dl>';
                        }
                        //html += '<a class="item itemInds" href="javascript:;" data-id="'+jsonData[i].id+'" title="'+jsonData[i][that.name]+'"><input type="'+that.setting.selectType+'" value="'+jsonData[i].id+'" />'+jsonData[i][that.name]+'</a>';
                    }
                    break;
                default:
                    for (var i = 0, mLen = jsonData.length; i < mLen; i++) {
                        html += '<a class="item" href="javascript:;" data-id="' + jsonData[i].id + '" title="' + jsonData[i][that.name] + '"><input type="' + that.setting.selectType + '" value="' + jsonData[i].id + '" />' + jsonData[i][that.name] + '</a>';
                    }
            }
            return html;
        },
        /*载入数据*/
        loadData: function() {
            var that = this;
            var key = that.setting.key.split("-")[0];
            var html = that.config[key].template;

            if(that.setting.max == Infinity){
                html = html.replace(/\(最多选择{#max}个\)/g,'');
            } else {
                html = html.replace(/{#max}/g, that.setting.max);
            }

            if (key === "city") {
                var jsonData_hotCI = datajson.mainCity;
                var arrHtml = that.loadDataList('city', that.config[key].jsonData);
                html = html.replace(/{#hotCI}/g, that.loadDataList("hotCI", jsonData_hotCI));
                html = html.replace(/{#Item_ABCF}/g, arrHtml[0]);
                html = html.replace(/{#Item_G}/g, arrHtml[1]);
                html = html.replace(/{#Item_H}/g, arrHtml[2]);
                html = html.replace(/{#Item_JLNQ}/g, arrHtml[3]);
                html = html.replace(/{#Item_S}/g, arrHtml[4]);
                html = html.replace(/{#Item_TXYZ}/g, arrHtml[5]);
            } else {
                var itemList = that.loadDataList(key, that.config[key].jsonData);
                html = html.replace(/{#ItemList}/g, itemList);
            }
            return html;
        },
        searcher: function(obj) {
            var that = this;
            var key = that.setting.key;
            var $obj = obj;
            var jsonData = datajson.jobFun;
            var $List = $("#js_List");
            var $searchBar = $('<div class="sBar"><p class="sel-search"><span class="icon">搜索：</span><input type="text" class="inpt" placeholder="请输入关键字"/></p><em>没找到！</em></div>');
            var $searchPanel = $('<div class="searchPanel"></div>');
            var $tipSearchResult = $searchBar.find("em");
            $searchPanel.appendTo($obj);
            $searchBar.appendTo($obj.find(".titBar")).find("input").keyup(function(event) {
                var $self = $(this);
                var keyword = $.trim($self.val());
                if (keyword === '') {
                    $List.show();
                    $searchPanel.hide();
                    $tipSearchResult.fadeOut();
                } else {
                    var count = 0;
                    var thtml = '';
                    var childItem;
                    var parentItem;
                    for (var i = 0, m = jsonData.length; i < m; i++) {
                        if (jsonData[i].id.toString().substr(2, 4) === "00") {
                            childItem = "";
                            var pid = jsonData[i].id;
                            for (var j = 0, n = jsonData.length; j < m; j++) {
                                if (pid.toString().substr(0, 2) === jsonData[j].id.toString().substr(0, 2) && jsonData[j].id !== pid && jsonData[j][that.name].indexOf(keyword) != -1) {
                                    childItem += '<a class="item" href="javascript:;" data-id="' + jsonData[j].id + '" title="' + jsonData[j][that.name] + '"><input type="' + that.setting.selectType + '" value="' + jsonData[j].id + '" /><label>' + jsonData[j][that.name] + '</label></a>';
                                }
                            }
                            if (childItem != "" || jsonData[i][that.name].indexOf(keyword) != -1) {
                                if (!that.setting.selectParent && childItem == "") {
                                    thtml = "";
                                } else {
                                    count++;
                                    thtml += '<dl';
                                    if (count % 2 === 0) {
                                        thtml += ' class="clearfix even"';
                                    } else {
                                        thtml += ' class="clearfix odd"';
                                    }
                                    thtml += '><dt style="margin-right:10px;">';
                                    //父项不可选
                                    if (that.setting.selectParent) {
                                        thtml += '<a class="item hasSub" href="javascript:;" data-id="' + jsonData[i].id + '" title="' + jsonData[i][that.name] + '"><input type="' + that.setting.selectType + '" value="' + jsonData[i].id + '" /><label>' + jsonData[i][that.name] + '</label></a>';
                                    } else {
                                        thtml += jsonData[i][that.name];
                                    }
                                    thtml += '</dt><dd>' + childItem + '</dd></dl>';
                                }
                            }
                        }
                    }
                    if (thtml !== "") {
                        $List.hide();
                        $tipSearchResult.fadeOut();
                        $searchPanel.html(thtml);
                        $searchPanel.show();
                        that.attachChildClickEvent($searchPanel);
                        var arrIds = that.getSelectedIds();
                        var $sedIemt;
                        for (var i = 0, m = arrIds.length; i < m; i++) {
                            $sedIemt = $searchPanel.find('[data-id="' + arrIds[i] + '"]');
                            if ($sedIemt.length) {
                                //已选状态
                                $sedIemt.addClass("selected").find("input").prop("checked", true);
                            }
                        }
                    } else {
                        $searchPanel.hide();
                        $List.show();
                        $tipSearchResult.fadeIn();
                    }
                }

            });
            // ie placeholder
            if (typeof placeHolderInit === 'function') {
                placeHolderInit();
            }
        },
        attachParentClickEvent: function(obj) {
            var that = this;
            var key = that.setting.key;
            var $obj = obj;
            $obj.find(".item").click(function(event) {
                event.stopPropagation();
                var $self = $(this);
                var id = $self.attr("data-id");
                var $objChild = $("#" + id);
                if (event.target.nodeName.toUpperCase() == "INPUT") {
                    that.selectItmeCheckEvent($self, $objChild);
                } else {
                    if ($self.hasClass("hasSub")) {
                        $(this).toggleClass("on");
                        that.displayChildBox($obj, $self);
                    } else {
                        that.selectItmeCheckEvent($self, $objChild);
                    }
                    return false;
                }
            });
        },
        displayChildBox: function(obj, parentItem) {
            var that = this;
            var key = that.setting.key.split("-")[0];
            var $obj = obj;
            var $parentItem = parentItem;
            var id = $parentItem.attr("data-id");
            var subBoxClassName;
            var jsonData;
            switch (key) {
                case "city":
                    jsonData = datajson.City;
                    subBoxClassName = "subCI";
                    break;
                case "post":
                    jsonData = datajson.jobFun;
                    subBoxClassName = "subPost";
                    break;
                case "industry":
                    jsonData = datajson.industry;
                    break;
                default:
                    return;
            }
            if ($parentItem.parent().hasClass("hotCI")) {
                var childBoxId = "town_" + id;
                var typeKey = "town";
                var jsonData = datajson.town;
            } else {
                var childBoxId = "subCat_" + id;
                var typeKey = "getDataById";
            }
            var $objChild = $("#" + childBoxId);

            if (!$objChild.length) {

                $objChild = $('<div id="' + childBoxId + '" class="' + subBoxClassName + '"></div>');
                var childData = that.loadDataList(typeKey, jsonData, id);
                $objChild.append(childData);
                $objChild.appendTo($obj);

                that.delayChildHideEvent($parentItem, $objChild);
                that.attachChildClickEvent($objChild);
                if (that.setting.selectType == "checkbox") {
                    var arrIds = that.getSelectedIds();
                    var $sedIemt;
                    for (var i = 0, m = arrIds.length; i < m; i++) {
                        $sedIemt = $objChild.find('[data-id="' + arrIds[i] + '"]');
                        if ($sedIemt.length) {
                            //已选状态
                            $sedIemt.addClass("selected").find("input").prop("checked", true);
                        }
                    }
                } else {
                    var idSuffix = this.$element.attr("id").replace("btn_", "");
                    var $idsObj = $("#id_" + idSuffix);
                    $sedIemt = $objChild.find('[data-id="' + $idsObj.val() + '"]');
                    if ($sedIemt.length) {
                        //已选状态
                        $sedIemt.addClass("selected").find("input").prop("checked", true);
                    }
                }

            }
            var t;
            var l;
            var parentPosition = $parentItem.position();
            var parentOffset = $obj.offset();
            t = parentOffset.top + parentPosition.top + $parentItem.height() + $objChild.height() > $(window).height() + $(document).scrollTop() ? parentPosition.top - $objChild.height() + 18 : parentPosition.top;

            //开始位置超过范围
            if (t < -(parentOffset.top - $(document).scrollTop())) {
                t = -(parentOffset.top - $(document).scrollTop());

            }

            if (parentOffset.top - $objChild.outerHeight() < 0) {
                t = -(parentOffset.top / 2);
            }

            //弹出框的高度大于浏览器窗口
            if ($objChild.height() > $(window).height()) {
                t = -(parentOffset.top - $(document).scrollTop());
            }

            if (parentOffset.left + parentPosition.left + $parentItem.width() + $objChild.width() > $(window).width() + $(document).scrollLeft()) {
                l = parentPosition.left - $objChild.width() - 11;

                $parentItem.removeClass('dir-left');
                $parentItem.addClass('dir-right');
            } else {
                l = parentPosition.left + $parentItem.width();
                $parentItem.removeClass('dir-right');
                $parentItem.addClass('dir-left');
            }

            $objChild.css({ "top": t, "left": l }).toggle();
        },
        attachChildClickEvent: function(obj) {
            var that = this;
            //var key=that.setting.key;
            var $obj = obj;
            $obj.find(".item").click(function(event) {
                var $self = $(this);
                that.selectItmeCheckEvent($self, null);
                if (event.target.nodeName.toUpperCase() !== "INPUT") {
                    return false;
                }
            });
        },
        //单项选择
        radioSelected: function(id) {
            var that = this;
            that.appendItemToA(id);
        },
        //将选项变为选中状态
        itemSelected: function(itemObj, childId) {
            var that = this;
            var key = that.setting.key.split("-")[0];
            var $itemObj = itemObj;
            var $selectPar = $("#selectBox");
            if ($itemObj) {
                var id = $itemObj.attr("data-id");
                var arrIds = that.getSelectedIds();
                var $sedIemt;

                //父项（一级）
                if ($itemObj.hasClass("hasSub")) {
                    //判断子项是否被选，有则删除
                    var n = parseInt(id.toString().substr(2, 2), 10) > 0 ? 4 : 2;
                    for (var i = 0, m = arrIds.length; i < m; i++) {
                        if (arrIds[i].toString().substr(0, n) == id.toString().substr(0, n)) {
                            that.removeItemInA(arrIds[i]);
                        }
                    }
                    arrIds = that.getSelectedIds();
                }

                //子项：（二级、三级）
                if (parseInt(id.toString().substr(2, 4), 10) > 0) {
                    //判断父项是否被选，有则删除
                    for (var i = 0, m = arrIds.length; i < m; i++) {
                        if (parseInt(id.toString().substr(0, 2) + (id.length == 4 ? "00" : "000000"), 10) == arrIds[i] || parseInt(id.toString().substr(0, 4) + "0000", 10) == arrIds[i]) {
                            that.removeItemInA(arrIds[i]);
                            break;
                        }
                    }
                    arrIds = that.getSelectedIds();
                }

                if (arrIds.length + 1 > that.setting.max) {
                    $selectPar.find('[data-id="' + id + '"]').find("input").prop("checked", false);
                    //$.jBox.tip("对不起，最多只能选择"+that.setting.max+"个")
                    // alert("对不起，最多只能选择"+that.setting.max+"个");
                    return;
                }
                that.appendItemToA(id);
            }
        },
        //去掉选中状态
        itemUnselected: function(itemObj, childId) {
            var that = this;
            var $itemObj = itemObj;
            if ($itemObj) {
                var id = $itemObj.attr("data-id");
                //$itemObj.filter("[data-id='"+id+"']").removeClass("selected");//去掉高亮
                $itemObj.find("input").prop("checked", false); //去掉选中状态
                that.removeItemInA(id);
            }
        },
        //添加选项到容器
        appendItemToA: function(id) {
            var that = this;
            var $obj = $("#js_selectedBox");
            var $selectPar = $("#selectBox");
            var jsonData;
            var name;
            var key = that.setting.key.split("-")[0];
            //var jsonData=that.getJsonData();

            var jsonData = that.config[key].jsonData;
            name = that.getNamesByIds([id], jsonData);
            var temp = '';
            var $obj_stItme = $('<div id="ST_' + id.toString().replace(".", "_") + '" class="st" title="' + name + '"></div>');
            temp += '<b>' + name + '</b>';
            temp += '<a class="btn_del" href="#" title="删除此选项">×</a>';
            temp += '</div>';

            var idSuffix = this.$element.attr("id").replace("btn_", "");
            var $namesObj = $("#name_" + idSuffix);
            var $idsObj = $("#id_" + idSuffix);

            if ($obj.find(".selectedA").text() == "") {
                $namesObj.val("").css("color", "#333");
                //$obj.find(".tip").hide();
                //显示提示语
                that.$element.find("span").hide();
            }
            //直接附加到文本框里
            var vName = $namesObj.val();
            var vId = $idsObj.val();
            if (that.setting.selectType == "checkbox") {
                var valNames, valIds;
                if (vId == "") {
                    valIds = id;
                } else {
                    valIds = vId + "," + id;
                }
                if (vName == "") {
                    valNames = name;
                } else {
                    valNames = vName + "," + name;
                }
                $namesObj.val(valNames);
                $idsObj.val(valIds);
            } else {
                $namesObj.val(name);
                $idsObj.val(id);
            }
            $namesObj.focus().blur().change();
            $obj_stItme.html(temp);

            $obj_stItme.appendTo($obj.find(".selectedA")).click(function(event) {
                var $self = $(this);
                if (event.target.nodeName.toUpperCase() == "A") {
                    that.removeItemInA(id);
                }
                return false;
            });

            $selectPar.find('[data-id="' + id + '"]').addClass("selected").find("input").prop("checked", true);
            if (parseInt(id.toString().substr(4, 2), 10) > 0) {
                //镇区
                $selectPar.find('[data-id="' + id.toString().substr(0, 4) + '0000"]').addClass("selectedSub");
            } else {
                $selectPar.find('[data-id="' + id.toString().substr(0, 2) + (id.length == 4 ? "00" : "000000") + '"]').addClass("selectedSub");
            }
        },
        removeItemInA: function(id) {
            var that = this;
            var key = that.setting.key.split("-")[0];
            var $obj = $("#js_selectedBox");
            var $selectPar = $("#selectBox");
            var jsonData = that.config[key].jsonData;
            var name = that.getNamesByIds([id], jsonData);
            var idSuffix = this.$element.attr("id").replace("btn_", "");
            var $namesObj = $("#name_" + idSuffix);
            var $idsObj = $("#id_" + idSuffix);

            var vName = $namesObj.val();
            var vId = $idsObj.val();
            if (that.setting.selectType == "checkbox") {
                if (vName.indexOf(",") != -1) {
                    $namesObj.val(vName.replace(name, ",").replace(",,", ""));
                } else {
                    $namesObj.val("");
                }
                if (vId.indexOf(",") != -1) {
                    $idsObj.val(vId.replace(id, ",").replace(",,", ""));
                } else {
                    $idsObj.val("");
                }

            } else {
                $namesObj.val("");
                $idsObj.val("");
            }
            $namesObj.change();
            $("#ST_" + id.toString().replace(".", "_")).remove();
            $selectPar.find('[data-id="' + id + '"]').removeClass("selected").find("input").prop("checked", false);
            if (parseInt(id.toString().substr(4, 2), 10) > 0) {
                if (!$("#town_" + id.toString().substr(0, 4) + "0000").find(".selected").length == 1) {
                    $selectPar.find('[data-id="' + id.toString().substr(0, 4) + '0000"]').removeClass("selectedSub");
                }
            } else {
                if (!$("#subCat_" + id.toString().substr(0, 2) + (id.length == 4 ? "00" : "000000")).find(".selected").length == 1) {
                    $selectPar.find('[data-id="' + id.toString().substr(0, 2) + (id.length == 4 ? "00" : "000000") + '"]').removeClass("selectedSub");
                }
            }
            if (that.getSelectedIds().length == 0) {
                $obj.find(".tip").show();
                //显法提示语
                that.$element.find("span").show();
            }
        },
        //删除所有选项
        removeItemAll: function() {
            var that = this;
            var arrIds = that.getSelectedIds();
            for (var i = 0, m = arrIds.length; i < m; i++) {
                that.removeItemInA(arrIds[i]);
            }
        },
        //获取已被选项ID
        getSelectedIds: function() {
            var arrSelectedIds = [];
            var $obj = $("#js_selectedBox").find(".selectedA").children("div");
            var id = "";
            for (var i = 0, m = $obj.length; i < m; i++) {
                id = $obj.eq(i).attr("id").replace("ST_", "").replace("_", ".");
                arrSelectedIds.push(id);
            }
            return arrSelectedIds;
        },
        //获取已被选项名称
        getSelectedNames: function() {
            var arrSelectedNames = [];
            var $obj = $("#js_selectedBox").find(".selectedA").children("div");
            var str = "";
            for (var i = 0, m = $obj.length; i < m; i++) {
                str = $obj.eq(i).attr("title");
                arrSelectedNames.push(str);
            }
            return arrSelectedNames;
        },
        //名称Id返回
        getIdsByNames: function(names, jsonData) {
            var that = this;
            var arrIds = [];
            var jsonDataTown = datajson.town;

            for (var i = 0, m = names.length; i < m; i++) {
                if (names[i].toString().indexOf("-") != -1) {
                    var cityName = names[i].toString().split("-")[0];
                    var cityId = jsonData[common.json.getIndexByFile(jsonData, that.name, cityName)].id;
                    var townName = names[i].toString().split("-")[1];
                    for (var j = 0, n = jsonDataTown.length; j < n; j++) {
                        if (townName == jsonDataTown[j][that.name] && townName != "" && (jsonDataTown[j].id).toString().substr(0, 4) == cityId.toString().substr(0, 4)) {
                            arrIds.push(jsonDataTown[j].id);
                            //alert(arrIds);
                            break;
                        }
                    }

                } else {
                    for (var j = 0, n = jsonData.length; j < n; j++) {
                        if (names[i] == jsonData[j][that.name] && names[i] != "") {
                            arrIds.push(jsonData[j].id);
                            break;
                        }
                    }
                }
            }

            return arrIds;
        },
        //Id返回名称
        getNamesByIds: function(ids, jsonData) {
            var that = this;
            var key = that.setting.key.split("-")[0];
            var arrNames = [];
            for (var i = 0, m = ids.length; i < m; i++) {
                var Aid = ids[i].toString();
                //地区旧数据兼容
                if (key == "city") {
                    if (Aid.length == 4) {
                        Aid = Aid + "0000";
                    }
                    if (Aid.indexOf(".") != -1) {
                        Aid = Aid.replace(".0", "") + "00";
                    }
                }
                //common.log(Aid);
                if (parseInt(Aid.substr(4, 2), 10) > 0 && parseInt(Aid.substr(6, 2), 10) == 0) {
                    var cityId = parseInt(Aid.substr(0, 4) + "0000", 10);
                    Aid = parseInt(Aid, 10);
                    var cityName;
                    for (var j = 0, n = jsonData.length; j < n; j++) {
                        if (cityId == jsonData[j].id) {
                            cityName = jsonData[j][that.name];
                            break;
                        }
                    }
                    for (var j = 0, n = datajson.town.length; j < n; j++) {
                        if (Aid == datajson.town[j].id) {
                            arrNames.push(cityName + "-" + datajson.town[j][that.name]);
                            break;
                        }
                    }
                } else {
                    Aid = parseInt(Aid, 10);
                    for (var j = 0, n = jsonData.length; j < n; j++) {
                        if (Aid == jsonData[j].id) {
                            arrNames.push(jsonData[j][that.name]);
                            break;
                        }
                    }
                }
            }
            return arrNames;
        },
        //选中操作
        selectItmeCheckEvent: function(itemObj, childId) {
            var that = this;
            var $selectPar = $("#selectBox");
           // var key = that.setting.key.split("-")[0];
            var $itemObj = itemObj;
            if (that.setting.selectType == "radio") {
                var id = $itemObj.attr("data-id");
                that.itemSelected($itemObj);
                that.closeBox();
            } else {
                if ($itemObj.is(".selected")) {
                    that.itemUnselected($itemObj, childId);
                } else {
                    that.itemSelected($itemObj, childId);
                }
            }

        },
        //控制子层的显示与隐藏(鼠标延时，目标内显示，目标外消失)
        delayChildHideEvent: function(parentObj, childObj) {
            var $obj = parentObj;
            var $objChild = childObj;
            $obj.mouseenter(function() {
                clearTimeout($obj.data("hideTimeOut"));
            }).mouseleave(function() {
                clearTimeout($obj.data("hideTimeOut"));
                $(this).data("hideTimeOut", setTimeout(function() {
                    $obj.removeClass("on");
                    $objChild.hide();
                }, 200));
            });
            $objChild.mouseenter(function() {
                clearTimeout($obj.data("hideTimeOut"));
                $obj.addClass("on");
            }).mouseleave(function() {
                clearTimeout($obj.data("hideTimeOut"));
                $obj.data("hideTimeOut", setTimeout(function() {
                    $obj.removeClass("on");
                    $objChild.hide();
                }, 100));
            });
        }
    };
    $.fn.bgiframe = function() {
        return this;
    };
    $.fn.boxSelector = function(options) {
        return this.each(function(key, value) {
            var element = $(this);
            // Return early if this element already has a plugin instance
            if (element.data('boxSelector')) return element.data('boxSelector');
            // Pass options to plugin constructor
            var boxSelector = new BoxSelector(this, options);
            // Store plugin object in this element's data
            element.data('boxSelector', boxSelector);
        });
    };
})(jQuery);
