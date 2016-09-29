let { banner } = require('modules/index'),
	swiper = require('plug/swiper/index'),
	{ changeData,getArticleList } = require('api/index');

	require('plug/city-picker/city-picker'); //城市选择插件

module.exports = {
	init() {
		let self = this;
		banner();
		require('plug/selectordie/index');
		$('select').selectOrDie();
		self.changeData();//查询工具
		self.getArticleList();//最新资讯
		self.choiceCity(); //所在地选择
	},
	sWeiper(evet,pare){
		var dataSwiper = new swiper(pare,{
				loop : false,//可选选项，开启循环
    			slidesPerView:15,
    			onlyExternal : true,
    			slidesPerGroup:15
  			});
		let $list = /*$('#city_list')*/evet,
			$btnNext = $list.find('.common-next'),
			$btnPrev = $list.find('.common-prev'),
			$swiperBtn = $list.find('.swiper_btn'),
			len = $list.find('.swiper-slide ').length;

			if(len<15){
				$swiperBtn.hide();
			}
			if($list.find('.swiper-slide:first').hasClass('swiper-slide-visible')){
				$btnPrev.hide()
			}
			if($list.find('.swiper-slide:last').hasClass('swiper-slide-visible')){
				$btnNext.hide()
			}
			//上一个下一个按钮
		  	$('.swiper_btn',$list).on('click', function(e){
		  		e.preventDefault();
		    	if($(this).hasClass('common-next')){
		    		 dataSwiper.swipeNext();
		    		 $btnPrev.show();
		    		 if($list.find('.swiper-slide:last').hasClass('swiper-slide-visible')){
						$btnNext.hide();
					}
		    	}else{
		    		dataSwiper.swipePrev();
		    		if(len>15){
						$btnNext.show();
					}
		    		if($list.find('.swiper-slide:first').hasClass('swiper-slide-visible')){
						$btnPrev.hide()
					}
		    	}
		  });
	},
	changeData(){
		this.sWeiper($('#city_list'),'.swiper_container');

		$('#city_list .item').click(function(){
			let $this = $(this),
				cityId = $this.val();

				$this.addClass('now').siblings('.item').removeClass('now');
				changeData({id:cityId},(msg)=>{
					$('#city_box').html(msg.data);
				})
			})
	},
	getArticleList(){
		this.sWeiper($('#city_tit'),'.swiper_content');

		$('#city_tit .item').click(function(){
			let $this = $(this),
				cityId = $this.val();

				$this.addClass('now').siblings('.item').removeClass('now');
				getArticleList({id:cityId},(msg)=>{
					let data = msg.data,
						help = data.help,
						newData = data.new,
						notice = data.notice,
						statute =data.statute;
				
				addHtml(help,$('#help_box'));
				addHtml(newData,$('#new_box'));
				addHtml(notice,$('#notice_box'));
				addHtml(statute,$('#statute_box'));

				function addHtml(el,event){
					if(el&&el !==null ){
						let html = '';
						for(let i = 0,len = el.length; i<len;i++){
							html +=`<dd>
				    				<a title="${el[i].title}" href="/Article-detail-id-${el[i].id}">
				    					<span class="text-overflow col-8"><i></i>${el[i].title}</span>
				    					<span class="text-overflow col-4 text-right">${el[i].create_time}</span>
				    				</a>
				   				</dd>`
						}
						event.html(html);
					}
					if(el == null){
						event.html('<div class="error_text">暂无数据...</div>');
					}
				}

				})
		});
	},
	 //所在地选择
    choiceCity() {
        let $cityPicker = $('#J_city-picker-query');
        
        $cityPicker.cityPicker({
            end: 'city'
        });
    },
}
