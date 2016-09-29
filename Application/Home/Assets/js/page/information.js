
let { getInformationList } = require('api/helpCenter');
require('plug/limitHeight');


module.exports = {
	init() {
		let self = this;

		$('.info-city>.right').limitHeight();

		// 加载更多
		$('#J_loading-more').click(function(event) {
			let $this = $(this),
				pn = $this.data('pn') - 0  || 2,
				dataForm = [];

			if($this.hasClass('disabled') || $this.hasClass('no-more')){
				return;
			}

			$this.addClass('disabled loading');

			self.getInformationList(0, ({ msg }) => {

				$this.data('pn', ++pn);
			}, ({ msg = '数据获取失败' }) => {

			})

		});

		// 切换城市城市
		$('a', self.$els.locationList).click(function () {
			let $this = $(this),
				{ location } = $this.data(),
				val = self.$els.location.val(),
				$scope = self.$els.locationList;

			$scope.find('a').removeClass('active');

			$this.addClass('active');

			if(location + '' !== val) {
				self.$els.location.val(location);

				self.getInformationList(1);
			}

		});

		self.$els.inforForm.on('submit', function(){
			self.getInformationList(1);
			return false
		})
	},
	/**
	 * 
	 * @param  {[type]} type    1为搜索 其他为加载更多
	 * @param  {[type]} success [description]
	 * @param  {[type]} fail    [description]
	 * @return {[type]}         [description]
	 */
	getInformationList(type,success, fail){
		let self= this,
			$loadingMmore = self.$els.loadingMmore,
			pn = type == 1 ? 1 : $loadingMmore.data('pn'),
			dataForm = self.$els.inforForm.serializeArray();

		dataForm.push({
			name: 'p',
			value: pn
		})

		return getInformationList(dataForm, ( ret ) => {

					let html = self.renderList(ret.data);

					if(type == 1) {
						self.$els.infoList.html(html);
						$loadingMmore.removeClass('disabled loading no-more')
							.data('pn', 2);
					} else {
						self.$els.infoList.append(html);
					}

					if(typeof success == 'function') {
						success(ret);
					}

					if(ret.pagecount <= pn) {
						$loadingMmore.addClass('disabled no-more');
					} else {
						$loadingMmore.removeClass('disabled no-more');
					}


				}, (ret) => {

					$loadingMmore.removeClass('disabled');

					if(typeof fail == 'function') {
						fail(ret);
					}

				},{
					type: 'get'
				}).complete(()=>{
					$loadingMmore.removeClass('loading');
				})
	},
	$els: {
		loadingMmore: $('#J_loading-more'),
		location: $('#J_location'),
		locationList: $('#J_location-list'),
		infoList: $('#J_info-list'),
		inforForm: $('#J_infor-search-form')
	},
	// 渲染资讯列表
	renderList(data){
		let html = '',
			self = this;

		data.forEach((item) => {
			let date = self.transformDate(item.create_time);

			html += `<li class="info-item horizontal">
					<div class="left info-time">
						<span class="month">${date.m}<span class="line">/</span>${date.d}</span>
						<span class="year">${date.y}</span>
					</div>
					<div class="right info-content">
						<h3 class="info-title text-overflow">
							<a href="/Article-detail-id-${item.id}" title="${item.title}">${item.title}</a>
						</h3>
						<p class="clearfix">
							${item.description}
							<a href="/Article-detail-id-${item.id}" class="more fr">MORE</a>
						</p>
					</div>
				</li>`
		})

		return html
	},
	transformDate(date){

		let dateObj = new Date(date),
			m = dateObj.getMonth() + 1,
			y = dateObj.getFullYear(),
			d = dateObj.getDate();

		m = m < 10 ? '0' + m : m;
		d = d < 10 ? '0' + d : d;

		return {
			y ,
			m ,
			d
		}
	},
	indexInit(){
		this.banner();
		this.inforTab();
	},
	banner() {
		let Swiper = require('plug/swiper/index');

        new Swiper('#J_banner-swiper', {
            autoplay: 5000, //可选选项，自动滑动
            loop: true, //可选选项，开启循环
            // grabCursor: true, //手型
            pagination: '.proService-pagination',
            calculateHeight: true,
            paginationClickable: true
        });
    },
    inforTab(){
    	require('plug/tab/index');

    	$('#J_infor-tab').tab();
    }
}