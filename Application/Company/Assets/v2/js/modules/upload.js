let uploader = require('plug/webuploader/webuploader');
let upload = {
	init(){
		let self = this;
		self.uploadCreate();
	},
//初始化图片上传
	uploadCreate(opts){
		var defaults = {
			// 选完文件后，是否自动上传。
			auto: true,
			// swf文件路径
			swf: '/Application/Company/Assets/v2/js/plug/webuploader/Uploader.swf',
			// 文件接收服务端。
			server: '/Company-Insurance-upload',
			// 内部根据当前运行是创建，可能是input元素，也可能是flash.
			pick: {
				id: '#filePicker',
				multiple: false
			},
			//允许重复上传
			duplicate :true,
			// 只允许选择图片文件。
			accept: {
				title: 'Images',
				extensions: 'gif,jpg,jpeg,bmp,png',
				mimeTypes: 'image/*'
			},
			fileSingleSizeLimit:2*1024*1024,
			compress: false,
			thumb: {
				width: 405,
				height: 300,
				quality: 100,
				allowMagnify: false,
				crop: false
			}
		}
		let setting = $.extend(true, defaults, opts);
	
		return uploader.create(setting);
	},
	//错误验证
	uploadError(uploader){

		uploader.on('error', function(error) {
            switch (error) {
                case 'F_EXCEED_SIZE':
                	let type = /^image\//.test(uploader.options.accept[0].mimeTypes) ? '图片' : '文件';

                    layer.alert(type + "大小不能超过2M", {
                        title: false
                    })
                    break;
                case 'Q_TYPE_DENIED':
                    layer.alert("格式不正确", {
                        title: false
                    })
                    break;
                default:
                    layer.alert("上传错误", {
                        title: false
                })
            }
        });
	},
	//上传成功后
	uploadSuccess(uploader,opts,el){
		uploader.on('uploadSuccess', function(file, response){
            if(response.status === 1){
                let url = response.info,
                	$parent = $(uploader.options.pick.id),
                	$webPick = $parent.find('.webuploader-pick'),
                	$addImg = $parent.find('.icon-addImg');

                	$addImg.hide();
                	if($parent.hasClass('new_btn')){
                		$parent.find('img').attr('src',url);
                	}else{
                		$parent.addClass('new_btn');
                		$webPick.append('<img src='+url+'>');

                	}
                	el.val(url);
                	$('#upload_msg').addClass('msg_text');
                	
            }else{
            	layer.alert(response.info);
            }
        })
	}
}
module.exports = upload;