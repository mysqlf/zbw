module.exports = {
    init() {
        this.backTop();
        this.sideShow();
        this.asideCalculator();
        this.showList();
        this.showList({
            box: '.pub-wechat',
            innerBox: '.pQcode'
        });
        this.showList({
            box: '.company-users',
            innerBox: '.tool-menu-bd'
        });

    },
    backTop() {
        //返回顶部
        $('.sidebar-back_top').on('click', function() {
            $("html,body").animate({ scrollTop: 0 }, 800);
        });
    },
    sideShow() {
        //侧边悬浮显示
        $(window).scroll(function() {
            let side = $(window).scrollTop();

            $(".side-float").show();
            if (side >= 200) {
                $(".sidebar-back_top").show();
            } else {
                $(".sidebar-back_top").hide();
            }
        }).trigger('scroll');
    },
    asideCalculator() {
        let calTpl = require('tpl/calculator.vue');

        new calTpl.calTpl({
            contrainer: '#J_aside-calculator'
        }).init();
    },
    showList(opts) {

        let defaults = {
            box: '.sidebar-Qcode',
            innerBox: '.Qcode'
        }

        let settings = $.extend(true, defaults, opts);

        let $box = $(settings.box),
            $Qcode = $(settings.innerBox);

        $box.hover(() => {
            $($Qcode, $($box)).show();
        }, () => {
            $($Qcode, $($box)).hide();
        });

    }
}
