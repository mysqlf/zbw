
module.exports = {
	getList(url, page, max_page) {

		let disable = false,
			flag = false;// 是否加载中

		page = page - 0;
		max_page = max_page - 0;

		$(window).scroll(function() {
	    	if(!disable){
		        if ($(document).scrollTop() >= $(document).height() - $(window).height()) {

		        	if(flag) {
		        		return;
		        	}

		        	flag = true;

		        	if(page > parseInt(max_page)) {
						$('.no-news').append('<div class="no-data">没有更多数据了</div>');
						disable = true;
						return false;
					}

		        	$.get(url,{p:page},function(msg){
		        		if(msg.status)
		        		{
		        			page++;	
		        			$('#J_news-inner-box').append(msg.result);
		        		}
		        	},'json').complete(()=>{
		        		flag = false;
		        	});
		        }
		    }	
	    }).trigger('scroll');
	},
	collapse() {

		$('.collapse-box .collapse-title').click(function(){
			let $this = $(this),
				$scope = $this.closest('.collapse-box'),
				$item = $this.closest('.collapse-item'),
				$items = $scope.find('.collapse-item').not($item),
				$content = $item.find('.collapse-content'),
				$contents = $items.find('.collapse-content');

				$contents.stop().slideUp(300);
				$content.stop().slideToggle(300);
				$item.addClass('active');
				$items.removeClass('active');
		});
	}
}