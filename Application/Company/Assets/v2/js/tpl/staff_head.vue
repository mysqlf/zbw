<template>
		<div class="staff_head"><!-- monthResult = Object.keys(dataResult); -->
			<div class="swiper-container">
			  <div class="swiper-wrapper">
			  	{{if monthResult}}
			  		{{each monthResult as item i}}
			    		<div class="swiper-slide">{{item}}</div>
			    	{{/each}}
			    {{/if}}
			  </div>
			</div>
			<div class="swiper_btn">
				<i class="icon icon-next"></i>
				<i class="icon icon-prev"></i>
			</div>
		</div>
</template>
<script type="text/javascript">
	let swiper = require('plug/swiper/index'),
		tpl = require('plug/artTemplate'),
		staffTable = require('tpl/staff-table.vue');
	module.exports = {
		init(obj){

  			var mySwiper = new swiper('.swiper-container',{
    			loop:false,
    			grabCursor: true,
    			slidesPerView:8,
    			onlyExternal : true,
    			slidesPerGroup:8
  			});
  			//上一页下一页按钮显示
  			let $swiperList = $('.swiper-slide'),
  				len = $swiperList.length,
  				$btnBox = $('.swiper_btn'),
  				$prevBtn = $btnBox.find('.icon-prev'),
  				$nextBtn = $btnBox.find('.icon-next');
	  			if(len<8){
	  				$btnBox.hide();
	  			}else{
	  				$btnBox.show();
	  			}
	  			if($('.swiper-slide:first').hasClass('swiper-slide-visible')){
	  				$prevBtn.hide()
	  			}
	  			if($('.swiper-slide:last').hasClass('swiper-slide-visible')){
	  				$nextBtn.hide()
	  			}
  			//上一个下一个按钮
		  	$('.icon-prev,.icon-next','.swiper_btn').on('click', function(e){
		  		e.preventDefault();
		    	let index = $('.swiper-slide.active').index();
		    	if($(this).hasClass('icon-next')){
		    		 mySwiper.swipeNext();
		    		 $prevBtn.show()
		    	}else{
		    		mySwiper.swipePrev();
		    		if($('.swiper-slide:first').hasClass('swiper-slide-visible')){
		    			$prevBtn.hide()
		    		}
		    	}
		  });
		  	//点击当前的选项
		  	 
		   $(".swiper-slide").on('click',function(){
    			let $this = $(this),
    				$table_box = $('#table_box'),
    				calculateData = obj.calculateData,
    				text = '';
    				
    				$this.addClass('swiper-slide-active').siblings('.swiper-slide').removeClass('swiper-slide-active');
    				
    				if($this.hasClass('swiper-slide-active')){
    					text = $this.text()
    				}

    				let tableData = calculateData[text];
    					tableData.personTotal = (((tableData['1'] && tableData['1'].data.person) || 0) + 
    					((tableData['2'] && tableData['2'].data.person) || 0))||0;
    					tableData.companyTotal = (((tableData['1'] && tableData['1'].data.company) ||0)) + ((tableData['2'] && tableData['2'].data.company || 0))||0;


    				tableData.allTotal = (tableData.personTotal + tableData.companyTotal).toFixed(2);
    				tableData.personTotal = tableData.personTotal.toFixed(2);
    				tableData.companyTotal = tableData.companyTotal.toFixed(2);

    				$table_box.html((tpl.render(staffTable.template)({tableData})));
  			});
		   $(".swiper-slide").eq(0).trigger('click');

		}
	}
</script>
