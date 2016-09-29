let backTop={
	init(){
		let self=this;
		self.goUp();
	},
//跳到页面头部按钮
	goUp(){
		var $upBtn=$("#back_top");
		$(window).scroll(function(){
			var scrollTop = $(this).scrollTop();
			if(scrollTop>400){
				$upBtn.show();
			}else{
				$upBtn.hide();
			}
		});
		$upBtn.click(function(){
			$('html, body').animate({scrollTop:0}, '1000');  
		})
	}
}
module.exports = backTop;