
let { msgCount } = require('api/common');

//侧边栏
let asideNav =  {
	init(){
		let self = this;
		self.accordion();
		self.sendMsg();
		self.msgNum()
	},
	accordion(){
		$(".menu_body").each(function(){
			let $this = $(this);
			if($this.find('a').hasClass('active')){
				$this.addClass('block');
				$this.siblings('.menu_head').addClass('current');
			}
		});

		$(".menu_head").click(function(){
		 	let $this = $(this),
		 		$tree = $this.closest('.down_tree'),
		 		$scope = $this.closest('.menu_list');
		 	$this.toggleClass("current");
		 	$scope.find('.menu_body').stop().slideUp("slow");
		 	$tree.find('.menu_body').stop().slideToggle(300);
		 	$scope.find('.menu_head').not(this).removeClass("current");
		}).each(function(index, el) {
			let $this = $(this);
			if($this.hasClass('current')){
				$this.closest('.down_tree').find('.menu_body').addClass('block');
			}
		});;
	},
	//头部消息数量
	sendMsg(){
		let $msg_tip = $('.msg_tit .msg_tip');

		msgCount({},({ result }) => {

			if(result - 0) {
				$msg_tip.show().html(result);
			} else {
				$msg_tip.hide();
			}
		})

	},
	msgNum(){
		setInterval(() => {
			this.sendMsg();
		},60000);
	}

}

module.exports = asideNav;
