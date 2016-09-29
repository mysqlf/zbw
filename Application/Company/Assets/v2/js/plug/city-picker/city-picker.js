var areaFn = require('./areaFn');
require('plug/tab/index');

$.fn.cityPicker = function(method) {
    var defaults = {
            placeholder: '请选择',
            end: 'district'
        }

    var _private = {
        init: function(options) {
            return this.each(function() {
                var $this = $(this);
                var setting = $.extend({}, defaults, $this.data(), options);
                var provinceArr = areaFn.getSubItem();


                $this.append(_private.createTpl(setting)).data('setting',setting);
                $('.province-content', $this ).html(areaFn.render(provinceArr));

                $('.city-picker', this).tab();

                _private.bindEvents($this);

                _private.bindToggle($this);

                _private.initVal($this);
            })

        },
        bindEvents($this){
            var setting = $this.data('setting');

            if(setting.disabled){
                $this.addClass('disabled');
                return;
            }

            $this.on('click', function(evt) {
                var $target = $(evt.target);

                if (evt.target == this || $target.hasClass('picker-txt')) {
                    $this.toggleClass('open');

                    if($this.hasClass('open')){
                        _private.showed($this);
                    }
                }
            })

            $(document).on('click.'+ new Date().getTime(), function(evt){
                var $target = $(evt.target),
                    $picker = $target.closest('.city-picker-input');

                if(!$picker[0] && $this[0] !== $target[0]){
                    _private.close($this);
                }
            })
        },
        initVal: function($this){
            var hiddenVal = $this.find('.hidden-txt').val();
            var initCityArr = [];

            if(hiddenVal !== '') {
                var curCityItem = areaFn.getById(hiddenVal);

                if(curCityItem){
                    initCityArr = areaFn.getParentsById(hiddenVal);
                    initCityArr.push(curCityItem);

                    var isOutProvince = areaFn.isCityWithoutProvince(initCityArr[0].id);

                     $('.province-content .area-toggle[data-value="'+ initCityArr[0].id +'"]', $this )
                        .trigger('click');

                    if(isOutProvince){
                        $('.city-content .area-toggle[data-value="'+ initCityArr[0].id +'"]', $this )
                        .trigger('click');

                        if(initCityArr.length > 1){
                            $('.district-content .area-toggle[data-value="'+ initCityArr[1].id +'"]', $this )
                            .trigger('click');
                        }
                    } else if(initCityArr.length > 1){
                        $('.city-content .area-toggle[data-value="'+ initCityArr[1].id +'"]', $this )
                        .trigger('click');
                    }
                }
            }
        },
        createTpl: function(setting){

            var tpl = '<div class="tab city-picker">' +
                '<ul class="tab-toggle clearfix">' +
                '<li><a class="active" href="javascript:;" data-target=".province-content">省份</a></li>' +
                '<li><a href="javascript:;" data-target=".city-content">城市</a></li>' +
                (setting.end == 'city' ? '' : '<li><a class="last" href="javascript:;" data-target=".district-content">区县</a></li>') +
                '</ul>' +
                '<div class="tab-content">' +
                '<div class="item province-content active"></div>' +
                '<div class="item city-content"></div>' +
                (setting.end == 'city' ? '' : '<div class="item district-content"></div>') +
                '</div>' +
                '</div>';

            return tpl;
        },
        bindToggle: function($obj) {

            $obj.on('click', '.area-toggle', function(evt, isWithoutChange) {
                var $this = $(this),
                    id = $this.data('value'),
                    $content = $this.closest('.item'),
                    $next = $content.next('.item'),
                    nextIndex = $next.index(),
                    $tab = $this.closest('.tab'),
                    cityArr = areaFn.getSubItem(id),
                    keys = [];

                if(nextIndex === -1 ){
                    //addActive必须在前面 因为下面获取值依赖他
                    addActive();
                    if(!isWithoutChange) {
                        _private.close($obj, true);
                    }

                } else{
                    cityArr = _private.getNextDataById(id,nextIndex,cityArr);

                    addActive(true);

                    keys = Object.keys(cityArr);

                    if(nextIndex === 1 && !keys.length) {
                        addActive();
                         if(!isWithoutChange) {
                            _private.close($obj, true);
                        }
                        return;
                    }

                    // 最后一个只有一个的时候 自动选上
                    if (nextIndex === 2 && keys.length < 2 && cityArr[keys[0]].length < 2) {
                        $('.area-toggle', $next).trigger('click', isWithoutChange);
                    }

                    $tab.trigger("change.tab", $('.tab-toggle a', $tab).eq(nextIndex).data('target'));
                }

                function addActive(addcontent){
                    if (!$this.hasClass('active')) {
                        $content.find('.area-toggle').removeClass('active');
                        $this.addClass('active');

                        if(addcontent){
                            $next.html(areaFn.render(cityArr)).next('.item').html('').next('.item').html('');
                        }
                    }
                }
            })
        },
        // 根据id 获取子集内容获取
        getNextDataById: function(id, nextIndex,arr ){
            var cityArr = arr || areaFn.getSubItem(id);

            if (areaFn.isEmptyObj(cityArr) && nextIndex === 2) {
                        // 没有县级
                cityArr[0] = [{
                    group: "",
                    id: id,
                    active: true,
                    name: "市级"
                }]
            } else if (areaFn.isCityWithoutProvince(id) && nextIndex === 1) {
                // 如果是一级市
                var item = areaFn.getById(id);

                cityArr = {};
                cityArr[item.group] = [item];
            }

            return cityArr;
        },
        close: function($obj,setVal){
            var setting = $obj.data('setting');

            $obj.removeClass('open');

            if(setVal){
                _private.setVal($obj);
            }

            if(setting && typeof setting.close === 'function'){
                setting.close($obj);
            }
        },
        showed: function($obj){
            var valObj = $obj.data('values');

            if (valObj){

                $(['province', 'city', 'district']).each(function(index,item){
                    judgment(item);
                })

            } else {
                //$('.area-toggle.active', $obj).removeClass('active');
            }

            $obj.find('.tab').trigger("change.tab", '.province-content');

            function judgment(type){
                var val = valObj[type],
                    /*index = type === 'province' ? 1 : 2,*/
                    classname = '.'+ type +'-content',
                    $active = $( classname + ' .area-toggle.active', $obj);

                if ( $active.data('value') - 0  !== val){
                    //$active.removeClass('active');
                    //$(classname +' .area-toggle[data-value="'+ val +'"]', $obj).addClass('active');
                    $(classname +' .area-toggle[data-value="'+ val +'"]', $obj).trigger('click', true)
                }
            }

        },
        getVal: function($obj){
            var txt = '',
                $active = $('.area-toggle.active', $obj),
                val = $active.eq(-1).data('value'),
                setting = $obj.data('setting');

            $active.each(function(){
                txt += $(this).text() + ' ';
            })

            return {
                txt: val == '' || val == null ? setting.placeholder : txt ,
                province: $active.eq(0).data('value'),
                city: $active.eq(1).data('value'),
                district: val || $active.eq(2).data('value')
            }
        },
        setVal: function($obj){
            var valObj = _private.getVal($obj),
                district = valObj.district,
                $hidden = $obj.find('.hidden-txt'),
                setting = $obj.data('setting'),
                $pickerTxt = $obj.find('.picker-txt');

            if(setting.end == 'city'){
                district = valObj.city || valObj.province;
                
                if(areaFn.isCityWithoutProvince(valObj.province)){
                    valObj.txt = valObj.txt.split(' ')[0];
                }
            }

            if(((district !== null || district !== '') && $hidden.val() !== district + '' ) || !$obj.data('values')){

                $pickerTxt.html(valObj.txt);
                $hidden.val(district).trigger('change');

                $obj.data('values', valObj);

                if(typeof setting.change === 'function'){
                    setting.change($obj, valObj);
                }
            }
        }
    }

    var methods = {

        reset: function(){
            return this.each(function () {
                var $this = $(this);
                var setting = $this.data('setting');

                $this.data('values','');
                $('.picker-txt', $this).html(setting.placeholder);
                $('.hidden-txt', $this).val('');

            })
        },
        update: function() {

            return this.each(function () {
                var $this = $(this);
                _private.initVal($this);
            })
            
        }
    }

    if ( methods[method] ) {
        return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    } else if ( typeof method === "object" || !method ) {
        return _private.init.apply(this, arguments);
    }

}
