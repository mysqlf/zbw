/**
 * 焦点图管理
 */
let WebUploader = require('plug/webuploader/webuploader')

module.exports = {
	init(){
		let self = module.exports;

		if(WebUploader.Uploader.support()) {

			$('.J_picker').each(function(){

				let formData = $(this).data(),
					uploader = WebUploader.create({

				    // swf文件路径
				    swf:  '/Application/Service/Assets/js/plug/webuploader/Uploader.swf',

				    // 文件接收服务端。
				    server: '/Service-Article-thumbUpload',

				    // 选择文件的按钮。可选。
				    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
				    auto: false,
				    duplicate: true,
				    paste: document.body,
				     // 只允许选择图片文件。
				    accept: {
				        title: 'Images',
				        extensions: 'gif,jpg,jpeg,png',
				        mimeTypes: 'image/*'
				    },
				    pick: {
				    	id: this,
				        multiple: false
				    },
				    formData,
				    fileSingleSizeLimit: 1024*1024*2
				});

				$(this).data('uploader', uploader);

				self.bindUploadEvent(this);
				self.uploadAccept(this);
			});

		}

		$('.J_picker-btn').click(function(evt) {

			if(!WebUploader.Uploader.support()) {

				layer.alert('您的浏览器暂不支持上传功能！如果你使用的是IE浏览器，请尝试升级 flash 播放器')
			}

			let $this = $(this),
				$picker = $this.closest('tr').find('.J_picker'),
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
	uploadAccept(el) {
		let $el = $(el),
		uploader = $el.data('uploader'),
		$scope = $el.closest('tr');

		uploader.on( 'uploadAccept', ( { file }, {status, url} ) => {

			let $thumb = $scope.find('.thumb-logo');

			if (status - 0 === 0){
				layer.msg('上传成功');

				// 如果存在链接返回值 直接使用 否则生成缩略图
				if(url) {
					$thumb.attr('src', url)
				} else {
					uploader.makeThumb(file, (error, ret ) => {
				    	$thumb.attr('src', ret)
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
	},
	bindUploadEvent(el){
		let $el = $(el),
			uploader = $el.data('uploader'),
			$scope = $el.closest('tr'),
			$btn = $scope.find('.J_picker-btn');

		uploader.on('fileQueued', ( { name } ) => {
			$scope.find('.webuploader-pick')
				.html(name)
				.next()
				.attr('title', name);
		});

		uploader.on( 'uploadComplete', ( fileObj, data ) => {
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
	}
}