// 显示
exports.showPsw = () => {
	$('.J_show-psw').click(function() {
		let $this = $(this);
		let $input = $this.parent().find('input');

		if ($input.attr('type') === 'password') {
			$this.addClass('active');
			$input.attr('type', 'text');
		} else {
			$this.removeClass('active');
			$input.attr('type', 'password');
		}
	})
}

// 密码强度
exports.pswStronge = (val) => {
	let strongRegex = /^(?=.{6,})(((?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]))|((?=.*[A-Z])(?=.*[a-z])(?=.*\W))|((?=.*[A-Z])(?=.*[0-9])(?=.*\W))|((?=.*[a-z])(?=.*[0-9])(?=.*\W))).*$/, //强
    	mediumRegex = /^(?=.{6,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))|((?=.*[a-z])(?=.*\W))|((?=.*[A-Z])(?=.*\W))|((?=.*[0-9])(?=.*\W))).*$/, //中
    	enoughRegex = /^(?=.{6,}).*/; //弱

    if(strongRegex.test(val)){
    	return 3;
    } else if(mediumRegex.test(val)){
    	return 2
    } else {
    	return 1;
    }

}

// 切换密码强度
exports.togglePswStronge = ($obj = $('#password')) => {
	let pswStronge = exports.pswStronge;

	$obj.on('keyup keydwon afterpaste change',function(){
		let val = this.value,
			level = 1,
			$curItem = null,
			$levelTxt = $('.level-txt');

		if(val){
			level = pswStronge(val);
			$curItem = $('.level-item'+ level);

			$curItem
				.addClass('active')
				.nextAll('.level-item')
				.removeClass('active');
			$curItem
			.prevAll('.level-item')
			.addClass('active')
		} else {
			$('.level-item').removeClass('active');
		}

		switch (level) {
			case 3:
				$levelTxt.text('高');
				break;
			case 2:
				$levelTxt.text('中');
				break;
			default:
				$levelTxt.text('低');
				break;
		}
	});
}
