var icheckFn = require('modules/icheck'),
	validateFn = require('modules/validate'),
	productAct = require('modules/actions/product'),
	template = require('art-template');
/*  	require('editor/ueditor.config.js');
	require('editor/ueditor.all.min.js');*/
	

var comProductDetail = {
	init:function(){
		var self = this;

		
		UE.getEditor('editor');
	//	UE.getEditor('editor1');
		UE.getEditor('editor2');
		self.validateObj = self.addProduct($("#comProductDetail"));

		self.cityList();


		icheckFn.init();

		$('body').on('click', '[data-act="addCity"]',function(){
			var $this = $(this),
				$scope = $this.closest('.J_other-city'),
				len = $scope.find('.cityList').length,
				lastVal = $('[name="other_location[]"]').last().val();

			var html = '<div class="cityList">' +
	  				   '<input type="text" class="J_city-txt form-control" name="other_city[]" value="">' +
					   '<input class="J_location" type="hidden" name="other_location[]" >' +
					   '<a class="remove-icon" href="javascript:;" data-act="removeCity"> 删除</a>'+
	  			       '</div>';

	  		if(len > 8){
	  			layer.alert('最多只能添加10个城市');
	  			$scope.find('[data-act="addCity"]').hide();
	  		} else if (len > 9) {
	  			return ;
	  		}

	  		if(lastVal === ''){
	  			layer.msg('请输入城市名称');
	  			return ;
	  		}

			$scope.find('.cityList').eq(-1).after(html);

			$scope.find('[data-act="removeCity"]').show();

		})

		$('body').on('click', '[data-act="removeCity"]',function(){
			var $this = $(this),
				$box = $this.closest('.J_other-city'),
				$scope = $this.closest('.cityList'),
				len = $box.find('.cityList').length;

			if(len < 3){
				$box.find('[data-act="removeCity"]').hide();
			}
			if( len < 2){
				return ;
			}

			$box.find('[data-act="addCity"]').show()

			$scope.remove();
		})

		$('body').on('click', '[data-act="addServiceFee"]',function(){
			var $this = $(this),
				$box = $this.closest('.J_server-box'),
				$scope = $this.closest('.serviceFee'),
				len = $box.find('.serviceFee').length,
				lastVal = $('.serviceFee').last().find('[name="validity[]"]').val(),
				lastVal1 = $('.serviceFee').last().find('[name="service_price[]"]').val();

	  		var data = {
				months:self.months
			}
			var ServeHtml = template.render(require('tpl/serveFee.vue').template)(data);

			if(lastVal === '' || lastVal1 === ''){
				layer.msg('请输入服务金额与期限');
	  			return ;
	  		}

			if(len > 9){
				layer.alert('最多只能添加10种');
				$box.find('[data-act="addServiceFee"]').hide();
				return ;
			}

			$(this).hide();

			$box.find('.serviceFee').eq(-1).after(ServeHtml);

			$box.find('[data-act="removeFee"]').show();

		})

		$('body').on('click', '[data-act="removeFee"]',function(){
			var $this = $(this),
				$box = $this.closest('.J_server-box'),
				$scope = $this.closest('.serviceFee'),
				len = $box.find('.serviceFee').length;

			$scope.remove();
			$box.find('.serviceFee').last().find('[data-act="addServiceFee"]').show();
			if(len < 3){
				$box.find('[data-act="removeFee"]').hide();
			}
		})

		$('body').on('change','#J_service-type',function(){
			var val = this.value;

			if(val){
				self.renderType(val)
			}
		})

	},
	months:[1,2,3,4,5,6,7,8,9,10,11,12],
	renderType: function(type,data){
		var self = this,
			typeData = {
				months:self.months,
				type: type
			};

			typeData = $.extend({},typeData,data)

		var productHtml = template.render(require('tpl/productType.vue').template)(typeData);

		$('#J-product-type').html(productHtml);
	},
	validateObj: null,
	addProduct:function($proForm){
		return $proForm.validate({
			submitHandler:function(){
				var formData = $proForm.serializeArray();
				productAct.addComProduct(formData,function(json){
					layer.msg(json.msg,function(){
						window.location='/Service-Product-productList';
					});
				})
				return false;
			},
			rules:{
				name:{
					required:true
				},
				service_type:{
					required:true
				},
				member_price:{
					required:true
				},
				validity:{
					required:true
				},
				city:{
					required:true
				},
				state:{
					required:true
				}
			},
			messages:{
				name:{
					required:"请输入产品名称"
				},
				service_type:{
					required:"请选择产品对象"
				},
				member_price:{
					required:"请输入会员费"
				},
				validity:{
					required:"请选择服务有效期限"
				},
				city:{
					required:"请输入城市名称"
				},
				state:{
					required:"请选择状态"
				},
				'other_city[]': {
					required:"请输入覆盖服务城市"
				}
			}
		})
	},
	cityList:function(){

		var timer = null,
			self = this;
		var aCity = [];

		$('body').on('keyup blur','.J_city-txt', function(evt){
			var $this = $(this),
				$cityList = $this.closest('.cityList'),
				$list = $('.cityUl',$cityList),
				val = $this.val();

			if(evt.type === 'keyup'){
				if(timer){
					clearTimeout(timer);
				}

				timer = setTimeout(function(){
					if(self.validateObj.element($this[0])){
						productAct.getCityLocaltion({
							city: val
						}).success(function(data){
							var node = '';
							if(data){
								if(!$list.length){
									$list = $('<ul class="cityUl"></ul>').appendTo($cityList)
								}
								$.each(data,function(index,val){
									node+='<li data-id="'+ val.id +'" data-name="'+ val.name +'"> '+ val.name +'</li>';
								})
							}
							$list.html(node).show();
						})
					}
				},500)
			} else{
				setTimeout(function(){
					$list.hide();
				},500)
			}
		})


		$('body').on('click','.cityUl li', function(e){
			var $this = $(this),
				cityName =  $this.data('name'),
				cityId = $this.data('id'),
				$scope =  $this.closest('.cityList');

			$scope.find('.J_city-txt').val(cityName);
			$scope.find('.J_location').val(cityId);

			$scope.find('.cityUl').hide();

			aCity.push(cityId);

			var newArr = aCity.sort();

			for(var i=0;i<newArr.length;i++){
				if(newArr[i] == newArr[i+1]){
					layer.msg('该城市已存在，请重新输入',function(){
						$scope.find('input.J_city-txt,input.J_location').val('');
					})
				}
			}

		})
	}

}

module.exports = comProductDetail;