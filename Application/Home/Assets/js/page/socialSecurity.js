let { payFor } = require('api/index');

module.exports = {
    init() {
        this.AddedService();
        this.buy();

        let $comDesc = document.querySelector('.com-desc'),
            $text = '';

        if ($comDesc) {
            $text = $comDesc.innerHTML;

            if ($text.length > 45) {
                $comDesc.innerHTML = $text.substring(0, 45) + '...';

            } else {
                $comDesc.innerHTML = $text + '...';

            }
        }



    },
    AddedService() {
        var Swiper = require('plug/swiper/index'),
            $swiper = $('.swiper-container'),
            mySwiper = null,
            setting = {
                loop: true,
                slidesPerView: 4,
                slidesPerGroup: 4
            },
            fx = 0,
            fxBlue = 0;

        if ($swiper.find('.swiper-slide').length <= 4) {
            setting.loop = false;
            $('.service-left,.service-right').remove();
        } else {
            //箭头滚动
            $('.service-left').on('click', function(e) {
                e.preventDefault()
                mySwiper.swipePrev()
            });

            $('.service-right').on('click', function(e) {
                e.preventDefault()
                mySwiper.swipeNext()
            });
        }

        if ($swiper[0]) {
            mySwiper = new Swiper($swiper[0], setting);
        }

        //
        $('.corporate .more-msg').on('click', function() {
            $('.corporate .more-msg-text').toggle();
        });

        $('.people-list .more-msg').on('click', function() {
            $('.people-list .more-msg-text').toggle();
        });

        $('.corporate .more-msg').on('click', function() {
            var url = '/Application/Home/Assets/img/pro-jt';

            if (fx == 0) {
                url += '-more.png';
                fx = 1;
            } else {
                url += '.gif';
                fx = 0;
            }

            $('.corporate .more-msg').css('background-image', 'url(' + url + ')');
        });

        $('.more-msg-blue').on('click', function() {
            var url = '/Application/Home/Assets/img/pro-jt';

            if (fxBlue == 0) {
                url += '-blue-more.png';
                fxBlue = 1;
            } else {
                url += '-blue.gif';
                fxBlue = 0;
            }

            $('.more-msg-blue').css('background-image', 'url(' + url + ')');
        });

        //社保产品套餐
        $('.p-btn').mouseenter(function() {
            $('.sb-ewm').show();
        });

        $('.sb-ewm').mouseleave(function() {
            $('.sb-ewm').hide();
        });
    },
    //详情页立即购买
    buy() {
        $('#buy_now').click(function() {
            let $this = $(this),
                { id } = $this.data();

            layer.confirm('是否立即购买？', {
                yes() {
                    payFor({ id }, ({ url }) => {
                        window.location.href = url;
                    }, ({ url, content }) => {

                        layer.alert(content, {
                            end() {
                                if (url) {
                                    location.href = url;
                                }
                            }
                        });
                    })
                }
            })
        })
    }
}
