var productAct = require('modules/actions/product'),
	template = require('art-template'),
	validateFn = require('modules/validate'),
	icheckFn = require('modules/icheck'),
	WebUploader = require('plug/webuploader/webuploader.js');

var productManageService={
	init:function(){
		this.bindEvent();
	},
	tpl: require('tpl/addProductService.vue').template,
	bindEvent: function(){
		var self = this;
		// 删除增值服务
		$('[data-act="del"]').click(function(){
			var data = $(this).data();
			layer.confirm('是否删除该产品？', {
			  btn: ['确定删除','取消'] //按钮
			}, function(){
				productAct.delService(data,function(){
					layer.msg('删除成功',function(){
						location.reload();
					});
				})
			})
		})
		// 添加增值服务
		$('[data-act="add_product"]').click(function(){

			self.addOrEditProduct({})

		})

		// 编辑增值服务
		$('[data-act="edit"]').click(function(){
			var data = $(this).data();
			var $form = null;

			productAct.editProductService(data,function(json){
				var jData = json.data;

				self.addOrEditProduct($.extend(jData,data))
			})
		})
	},
	addOrEditProduct: function(tplData){
		var self = this,
			$form = null,
			html = template.render(self.tpl)(tplData);

		layer.open({
			type: 1,
			closeBtn: 1, //不显示关闭按钮
			shift: 2,
			shadeClose: false, //开启遮罩关闭
			content: html,
			btn:['确定','取消'],
			yes:function(){
				$form.submit();
			},
			area:'470px'
		})

		$form = $('#productService');
		self.cityList();
		self.uploadImg();
		self.productValidate($form);
		icheckFn.init();
	},
	uploadImg:function(){    //广告缩略图上传
		var uploader = WebUploader.create({
			auto: true,
		    swf: '/Application/Service/Assets/js/src/plug/webuploader/Uploader.swf',
		    server: '/Service-Product-updateImg',
		    pick: '#picker',
		    resize: false,
		    accept: {
		        title: 'Images',
		        extensions: 'jpg,gif,png,jpeg',
		        mimeTypes: 'image/*'
	    	},
	    	fileSingleSizeLimit: 204800

		}).on('uploadSuccess',function(file,res){

			if(res.status-0 === 0){
				var smallImg='<img style="width:100px;" src="'+res.data.url+'">'
				$('#uploader-view').html(smallImg);
				$('#img-url').val(res.data.url);
			}
		}).on('error',function(file,res){
			layer.msg('图片过大（上传图片必须小于200KB）')
		})
	},
	productValidate: function($form){   //表单验证
		$form.validate({
			submitHandler: function(){
				var formData = $form.serializeArray();
				productAct.addProductService(formData,function(json){
					if(json.status-0 === 0){
						layer.msg(json.msg,function(){
							location.reload();
						});
					}else{
						layer.msg("添加失败");
					}
				})
			},
			rules:{
				city:{
					required:true
				},
				product_name:{
					required:true
				},
				qq:{
					required:true
				},
				service_state:{
					required:true
				}
			},
			messages:{
				city:{
					required:'请输入城市名称'
				},
				product_name:{
					required:'请输入产品名称'
				},
				status:{
					required:'请选择状态'
				}
			}
		})
	},
	cityList:function(){
		var $city = $('#add_city'),
			$location = $('#location'),
			html = '<ul id="cityList"></ul>',
			onOff = true;
		$city.after(html);
		var	$list = $('#cityList');

		// 城市名联想功能
		$city.keyup(function(){
			var city = $city.val();
			var $self = $(this);

			if(city === ''){
				$list.hide().html('');
				return;
			}

			// ajax获取城市名
			$.ajax({
				type: 'POST',
				url: '/Service-Product-selectLocation',
				data: {
					city: city
				},
				dataType:'json',
				success:function(data){
					var node = '';
					if(data){
						$list.show();
						$.each(data,function(index,val){
							node+='<li data-id="'+ val.id +'" data-name="'+ val.name +'"> '+ val.name +'</li>';
						})
					}
					$list.html(node);
				},
				error: function(){

				}

			})
		})
		// 选择城市
		$city.blur(function(){
			var defaultVal = this.defaultValue;
			$list.find('li').click(function(){
				var cityName = $(this).attr('data-name');
				var cityId = $(this).attr('data-id');
				$city.val(cityName);
				$("#location").attr('value',cityId);
				onOff=false;
				$list.hide();
			})

			if(onOff){
				$(this).val(defaultVal);
			}

			$list.fadeOut(200);
		})
	}
}
module.exports = productManageService;