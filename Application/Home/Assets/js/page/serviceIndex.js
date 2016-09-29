let { banner } = require('modules/index');

module.exports = {
	init() {
		banner();
		this.sbtc();
	},
	sbtc() {
		//社保产品套餐
		$('.p-btn').mouseenter(() => {
			$('.people-list').hide();
			$('.people-ewm').show();
		});
		$('.people-ewm').mouseleave(() => {
			$('.people-list').show();
			$('.people-ewm').hide();
		});
		
	}
}
