var WebUploader=require("plug/webuploader/Webuploader.js");
var uploader={
    init:function(options){
        var defaults={
            id:null,
            url:null,
            data:{},
            fileVal:'file',
            isImg:true,
            progress:null,
            uploadAccept:null,
            success:null,
            error:null,
            complete:null,
            auto:true,
            duplicate: true

        };
        var opts=$.extend({},defaults ,options);

        var id=opts.id,
            url=opts.url,
            data=opts.data,
            fileVal=opts.fileVal,
            progress=opts.progress,
            uploadAccept=opts.uploadAccept,
            success=opts.success,
            error=opts.error,
            complete=opts.complete,
            auto=opts.auto;
        var accept={};
        if(opts.isImg){
            accept={
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/*'
            }
        }
        var uploader = WebUploader.create({

            // 选完文件后，是否自动上传。
            auto: auto,

            // swf文件路径
            swf: '/Public/static/webuploader/Uploader.swf',

            // 文件接收服务端。
            server: url,

            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: id,
            fileVal:fileVal,
            formData:data,

            // 只允许选择图片文件。
            accept: accept,
            duplicate: opts.duplicate,
            fileSingleSizeLimit : 2097152
        });

        uploader.on('fileQueued', function (file,data) {
            switch( file.statusText ) {
                case 'exceed_size':
                    layer.alert('文件太大，请控制在2M以内');
                    break;

                case 'interrupt':
                    layer.alert('上传暂停');
                    break;

            }

        });

        uploader.on('error', function (type) {
            switch(type) {
                case 'Q_TYPE_DENIED':
                    layer.alert('请选择正确的文件类型');
                    break;

                case 'F_EXCEED_SIZE':
                    layer.alert('文件太大，请控制在2M以内');
                    break;

            }

        });

        // 文件上传过程中创建进度条实时显示。
        if(typeof progress =="function"){
            uploader.on( 'uploadProgress', function( file, percentage ) {
                progress(file, percentage);
                /*var $li = $( '#'+file.id ),
                    $percent = $li.find('.progress span');

                // 避免重复创建
                if ( !$percent.length ) {
                    $percent = $('<p class="progress"><span></span></p>')
                        .appendTo( $li )
                        .find('span');
                }

                $percent.css( 'width', percentage * 100 + '%' );*/
            });
        }


        // 文件上传成功，给item添加成功class, 用样式标记上传成功。
        if(typeof success =="function") {
            uploader.on('uploadSuccess', function (file,data) {
                success(file,data);
            });
        }

        // 文件上传成功，给item添加成功class, 用样式标记上传成功。
        if(typeof uploadAccept =="function") {
            uploader.on('uploadAccept', function (object,ret) {
                uploadAccept(object,ret);
            });
        }

        // 文件上传失败，显示上传出错。
        if(typeof error =="function") {
            uploader.on('uploadError', function (file) {
                error(file);
                /*var $li = $('#' + file.id),
                    $error = $li.find('div.error');

                // 避免重复创建
                if (!$error.length) {
                    $error = $('<div class="error"></div>').appendTo($li);
                }

                $error.text('上传失败');*/
            });
        }

        // 完成上传完了，成功或者失败，先删除进度条。
        if(typeof complete =="function") {
            uploader.on('uploadComplete', function (file) {
                complete(file);
            });
        }
        return uploader;
    }
};
module.exports = uploader;