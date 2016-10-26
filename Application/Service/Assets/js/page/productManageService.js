// 增值服务

let { delService, editProductService, addProductService } = require('api/package'),
	template = require('art-template'),
	validateFn = require('plug/validate/index'),
	icheckFn = require('plug/icheck/index'),
	WebUploader = require('plug/webuploader/webuploader');

require('plug/city-picker/city-picker');

let productManageService = {
	init(){
		this.bindEvent();
	},
	tpl: require('tpl/addProductService.vue').template,
	bindEvent(){
		let self = this;
		// 删除增值服务
		$('[data-act="del"]').click(function(){
			let data = $(this).data();

			layer.confirm('是否删除该产品？', {
			  btn: ['确定删除','取消'] //按钮
			}, function(){
				delService(data,() => {
					layer.msg('删除成功',function(){
						location.href = location.href;
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
			let data = $(this).data();
			let $form = null;

			editProductService(data, ( json ) => {
				let jData = json.data;

				self.addOrEditProduct($.extend(jData,data))
			})
		})
	},
	addOrEditProduct( tplData ){
		let self = this,
			$form = null,
			html = template.render(self.tpl)(tplData);

		layer.open({
			title:'添加/编辑产品',
			type: 10,
			skin: 'layui-layer-dialog',
			closeBtn: 1, //不显示关闭按钮
			shadeClose: false, //开启遮罩关闭
			content: html,
			btn:['确定','取消'],
			yes(){
				$form.submit();
			},
			success(){
            	$('.city-picker-input').cityPicker({
            		end: 'city'
            	});
			},
			area:'600px'
		})

		$form = $('#productService');

		self.uploadImg();
		self.productValidate($form);
		icheckFn.init();
	},
	uploadImg(){    //广告缩略图上传
		let $picker = $('#picker');

		let uploader = WebUploader.create({
		    swf: '/Application/Service/Assets/js/plug/webuploader/Uploader.swf',
		    server: '/Service-Product-updateImg',
		    pick: '#picker',
		    resize: false,
		    accept: {
		        title: 'Images',
		        extensions: 'jpg,gif,png,jpeg',
		        mimeTypes: 'image/*'
	    	},
	    	fileSingleSizeLimit: 1024*1024*2
		})

		$picker.data('uploader', uploader);

		// this.bindUploadEvent($picker);

		$('.J_picker-btn').click(function(evt) {
			let $this = $(this),
				$picker = $this.closest('.horizontal').find('.J_picker'),
				uploader = $picker.data('uploader'),
				flag = $this.data('flag');

			if(!uploader.getFiles().length) {
				layer.msg('请选择图片');
				return
			}

			if(flag){
				return;
			}

			$this.html('上传中...');
			$this.data('flag', true);
			uploader.upload();
		})
	},
	productValidate($form){   //表单验证

		$form.validate({
			submitHandler(){
				let formData = $form.serializeArray(),
					serviceId = $form.find(({name}) => {
						return name == 'service_id';
					}),
					msgFixed = serviceId.value == '' ? '添加' : '编辑';

				addProductService(formData,({ msg = msgFixed + '成功' }) => {
					layer.closeAll();
					layer.msg(msg, () => {
						location.href = location.href;
					});
				}, ({ msg = msgFixed + '失败' }) => {
					layer.closeAll();
					layer.msg(msg);
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
					required:true,
					qq: true
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
				},
				advertising_url: {
					required: '请上传广告图'
				},
				content: {
					required: '请输入产品内容'
				}
			}
		})
	},
	bindUploadEvent(el){
		let $el = $(el),
			uploader = $el.data('uploader'),
			$scope = $el.closest('.horizontal'),
			$btn = $scope.find('.J_picker-btn');

		uploader.on('fileQueued', ( {name} ) => {
			$scope.find('.webuploader-pick')
				.html(name)
				.next()
				.attr('title', name);
		});

		uploader.on( 'uploadComplete', ( { file }, data ) => {
			$btn.html('上传');
			$btn.data('flag',false);
		});

		uploader.on( 'error', ( type) => {
			let tip = '上传失败';

			switch (type) {
				case 'F_EXCEED_SIZE':
					tip = '上传图片不能超过2M'
					break;
				case 'Q_TYPE_DENIED':
					tip = '只允许上传gif,jpg,jpeg,png格式的图片'
					break;
				default:

					break;
			}

			layer.msg(tip);
		});

		uploader.on( 'uploadAccept', ( { file }, {status, data: { url }} ) => {

			let $thumb = $('#uploader-view img');

			if (status - 0 === 0){
				layer.msg('上传成功');

				// 如果存在链接返回值 直接使用 否则生成缩略图
				if(url) {
					$thumb.attr('src', url).show();
					$('#img-url').val(url);
				} else {
					uploader.makeThumb(file, (error, ret ) => {
				    	$thumb.attr('src', ret).show();
				    },1,1);
				}
			} else {
				layer.msg('上传失败');
			}

		    uploader.reset();
		    $scope.find('.webuploader-pick')
		    	.html('选择图片')
		    	.next()
		    	.attr('title', '选择图片');
		})
	}
}
module.exports = productManageService;
