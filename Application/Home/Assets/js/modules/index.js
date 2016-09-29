/**
 * 首页公用模块
 */

module.exports = {
	banner() {
		let Swiper = require('plug/swiper/index'),
			$swiperContainer = $('.swiper-container'),
			$arrowLeft = $('.arrow-left', $swiperContainer),
			$arrowRight = $('.arrow-right', $swiperContainer);

		//banner
		var mySwiper = new Swiper('.swiper-container', {
			autoplay : 5000,//可选选项，自动滑动
			loop : true,//可选选项，开启循环
			grabCursor : true,//手型
			pagination : '.pagination',
			calculateHeight: true
		});

		$swiperContainer.removeClass('swiper-loading');

		$swiperContainer.hover(() => {
			mySwiper.stopAutoplay();
		},() => {
			mySwiper.startAutoplay();
		});

		$swiperContainer.on('click', '.swiper-pagination-switch', function(){
			let index = $(this).index();

			mySwiper.swipeTo(index);
		})

		//箭头滚动
		$arrowLeft.on('click', (e) => {
			e.preventDefault()
			mySwiper.swipePrev()
		});

		$arrowRight.on('click', (e) => {
			e.preventDefault()
			mySwiper.swipeNext()
		});

		$swiperContainer.hover((e) => {
			$arrowLeft.show();
			$arrowRight.show();
		}, () => {
			$arrowLeft.hide();
			$arrowRight.hide();
		});
	}
}