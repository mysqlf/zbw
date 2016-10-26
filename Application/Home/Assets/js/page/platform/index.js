let swiper = require('plug/swiper/index'),
    { validate: firmLogin } = require('page/login'),
    { login: serviceLogin } = require('page/service_login'),
    { getArticleList } = require('api/index');

require('plug/limitHeight');

module.exports = {
    init() {
        $('.search-terms-city dd').limitHeight({
            margin: 3
        });

        this.banner(); // 平台首页banner
        this.showFixNav(); // 平台浮动导航
        this.loginTab(); // 首页企业用户和服务商登录切换
        firmLogin(); // 企业登录
        serviceLogin($("#login-form")); // 服务商登录
        this.proService(); // 平台产品服务交互
        this.getNewInfoData(); // 平台首页最新资讯数据请求与加载
        // this.getServiceProductQuery(); // 服务机构按条件搜索查询

        $('.icheck').iCheck({
            checkboxClass: 'icheckbox icheckbox_minimal-orange',
            increaseArea: '20%' // optional
        });

    },
    banner() {
        let $len = $('.swiper-wrapper').children().length;

        if ($len > 1) {
            new swiper('.swiper-container', {
                autoplay: 5000, //可选选项，自动滑动
                loop: true, //可选选项，开启循环
                // grabCursor: true, //手型
                pagination: '.banner-pagination',
                calculateHeight: true,
                paginationClickable: true
            });

        }

    },
    showFixNav() {
        getScrollTop();

        $(window).scroll(function() {
            getScrollTop();

        });

        function getScrollTop() {
            let len = $(window).scrollTop();

            if (len > 0) {
                $('.ind-header').addClass('fixed');

            } else {
                $('.ind-header').removeClass('fixed');
            }
        }

    },
    loginTab() {
        $('.login-tabs li').each(function() {
            let $this = $(this);

            $this.click(function() {
                $this.addClass('active')
                    .siblings()
                    .removeClass('active');
                $('.login-item').eq($this.index()).show()
                    .siblings()
                    .hide();
            });
        });

    },
    proService() {
        let mySwiper = new swiper('.pro-service', {
            loop: true, //可选选项，开启循环
            pagination: '.proService-pagination',
            calculateHeight: true,
            paginationClickable: true
        });

        $('.sec3-tabs li').each(function(index, el) {
            let $this = $(this);
            $this.click(function() {
                $this.addClass('active').siblings().removeClass('active');
                $('.proService-pagination span').eq($this.index()).trigger('click');
            });
        });

        setInterval(function() {
            let $pIndex = $('.proService-pagination span.swiper-active-switch').index(),
                $tabIndex = $('.sec3-tabs li.active').index();

            $('.sec3-tabs li').eq($pIndex).addClass('active').siblings().removeClass('active');

        }, 100);

        $('.proService-pagination span').each(function() {
            let $this = $(this);

            $this.click(function(event) {
                $('.sec3-tabs li').eq($this.index()).addClass('active').siblings().removeClass('active');
            });

        });

    },
    getNewInfoData() {
        let self = this;
        // 根据城市切换
        $('.sec6-tabs li').each(function() {
            let $this = $(this);
            $this.click(function() {

                // 获取当前状态的index()
                $this.addClass('active').siblings().removeClass('active');

                // 获取城市数据
                self.changeNewInfoData($this.val());

            });
        });

        let $height = $('.sec6-tabs').height(),
            $liHeight = $('.sec6-tabs li').outerHeight(true);

        if ($height > $liHeight) {
            $('.sec6-tabs').css({ 'textAlign': 'left' });

        } else {
            $('.sec6-tabs').css({ 'textAlign': 'center' });

        }

    },
    changeNewInfoData(opts) {
        getArticleList({ id: opts }, (msg) => {
            let data = msg.data,
                help = data.help,
                newData = data.new,
                notice = data.notice,
                statute = data.statute;

            addHtml(help, $('#help'));
            addHtml(statute, $('#statute'));
            addHtml(newData, $('#new'));
            addHtml(notice, $('#notice'));

            function addHtml(el, event) {

                if (el && el !== null) {
                    let html = '';
                    for (let i = 0, len = el.length; i < len; i++) {
                        html += `<p>
                                    <a href="/Article-detail-id-${el[i].id}" title="${el[i].title}">${el[i].title}</a>
                                </p>`
                    }
                    event.html(html);

                }
                if (el == null) {
                    event.html(`<p class="no-data">暂无数据...</p>`);
                }
            }


        });
    },
    getServiceProductQuery() {

        $('.search-terms-tm').each(function() {
            let $this = $(this);
            $this.click(function(event) {
                event.preventDefault();
                $this.addClass('active').siblings('a').removeClass('active');

                function getVal(obj) {

                    return $(obj).find('a.active').data('value');
                }

                let data = {
                    product_name: $('[name="product_name"]').val(),
                    location: getVal('[name="location"]'),
                    applicable_object: getVal('[name="applicable_object"]'),
                    amount: getVal('[name="amount"]')
                }

                let product_name = data.product_name ? '-product_name-' + data.product_name : '',
                    location = data.location ? '-location-' + data.location : '',
                    applicable_object = data.applicable_object ? '-applicable_object-' + data.applicable_object : '',
                    amount = data.amount ? '-amount-' + data.amount : '';

                let $url = '/Index-serviceProduct' + product_name + location + applicable_object + amount;
                window.location.assign($url);

            });
        });

    }
}
