require('plug/selectordie/index'); //下拉框插件
require('plug/datetimepicker/index'); //时间选择插件
require('plug/city-picker/city-picker'); //城市选择插件

let { iCheck } = require('plug/icheck/index'), //check插件
    swiper = require('plug/swiper/index'),
    detailTable = require('tpl/detail-table.vue'),
    tpl = require('plug/artTemplate'), //模板引擎
    { detailUrl } = require('api/insurance'); //API文件

let insuranceDetail = {
    init() {
        let self = this;

        $('.select').selectOrDie();
        self.timePicker();
        self.check();
        self.showGjj();
        self.swiperTable();
    },
    check() {
        let $showBtn = $('.check_btn');

        $showBtn.click(function() {
            insuranceDetail.showGjj();
        });
    },
    showGjj() {

        let $isBuyPro = $('#isBuyPro'),
            $con_gjj = $('#con_gjj');

        if ($isBuyPro.is(':checked')) {
            $isBuyPro.parent().addClass('active');
            $con_gjj.show();
        } else {
            $isBuyPro.removeAttr('checked')
                .parent()
                .removeClass('active');
            $con_gjj.hide();
        }
    },
    timePicker() {

        $(".timepicker").datetimepicker({
            format: 'yyyy-mm',
            weekStart: 1,
            autoclose: true,
            startView: 3,
            minView: 3,
            forceParse: false,
            language: 'zh-CN'
        })
    },
    swiperTable() {
        var mySwiper = new swiper('.swiper-container', {
            loop: false,
            grabCursor: true,
            slidesPerView: 8,
            onlyExternal: true,
            slidesPerGroup: 8
        });

        //上一页下一页按钮显示
        let $swiperList = $('.swiper-slide'),
            len = $swiperList.length,
            $btnBox = $('.swiper_btn'),
            $prevBtn = $btnBox.find('.icon-prev'),
            $nextBtn = $btnBox.find('.icon-next');

        if (len < 8) {
            $btnBox.hide();
        } else {
            $btnBox.show();
        }

        if ($('.swiper-slide:first').hasClass('swiper-slide-visible')) {
            $prevBtn.hide()
        }

        if ($('.swiper-slide:last').hasClass('swiper-slide-visible')) {
            $nextBtn.hide()
        }

        //上一个下一个按钮
        $('.icon-prev, .icon-next', '.swiper_btn').on('click', function(e) {
            let index = $('.swiper-slide.active').index();

            e.preventDefault();

            if ($(this).hasClass('icon-next')) {
                mySwiper.swipeNext();
            } else {
                mySwiper.swipePrev();
            }
        });

        $(".swiper-slide").on('click', function() {
            let $this = $(this);

            $this.addClass('swiper-slide-active').siblings('.swiper-slide').removeClass('swiper-slide-active');

            if ($this.hasClass('swiper-slide-active')) {
                let payDate = $this.data('value'),
                    baseId = $('#baseId').val(),
                    userId = $('#userId').val(),
                    dataJson = {
                        payDate,
                        baseId,
                        userId
                    }

                detailUrl(dataJson, (data) => {

                    let tableData = data.result[payDate];

                    tableData.personTotal = (((tableData['1'] && tableData['1'].calculateResult && tableData['1'].calculateResult.person) || 0) +
                        ((tableData['2'] && tableData['2'].calculateResult && tableData['2'].calculateResult.person) || 0)) || 0;

                    tableData.companyTotal = (((tableData['1'] && tableData['1'].calculateResult && tableData['1'].calculateResult.company) || 0)) + ((tableData['2'] && tableData['2'].calculateResult && tableData['2'].calculateResult.company || 0)) || 0;
                    tableData.service = 0;

                    if (tableData['1']) {
                        tableData.service = tableData['1'].sid_service_price
                    }
                    if (tableData['2']) {
                        tableData.service = tableData['2'].sid_service_price
                    }
                    if (tableData['1'] && tableData['2']) {
                        tableData.service = parseFloat(tableData['1'].sid_service_price) + parseFloat(tableData['2'].sid_service_price);
                    }
                    tableData.total = (tableData.personTotal + tableData.companyTotal).toFixed(2);
                    tableData.allTotal = (tableData.personTotal + tableData.companyTotal + parseFloat(tableData.service)).toFixed(2);
                    $('#detailTable').html((tpl.render(detailTable.template)({ tableData })));

                })
            }
        }).eq(0).trigger('click');

    }
}
module.exports = insuranceDetail;
