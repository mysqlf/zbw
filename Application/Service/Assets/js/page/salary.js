/*
 * 上传模板 upload()
 */

let checkFn = require('plug/icheck/index'),
    WebUploader = require('plug/webuploader/webuploader'),
    { deleteSalaryOrder } = require('api/business'),
    dateFn = require('plug/datetimepicker/index'),
    { dateRange } = require('modules/date'),
    { businessHandler } = require('modules/business');// 代发工资和申报操作

require('plug/validate/index');


let salary = {
    init() {
        var self = this;

        checkFn.init();
        checkFn.checkAll();
        businessHandler();
        self.upload();
        dateFn.getYearMonth();
        dateFn.getYearMonthDay();
        dateRange('declare');

        // 删除代发工资
        $('[data-act="del-salary"]').click(function(){
            let $this = $(this),
                {
                    id,
                    userid: userId
                } = $this.data();

            layer.open({
                title: '删除',
                content: '是否删除？',
                btn: ['删除', '取消'],
                yes(){
                    deleteSalaryOrder({
                        id,
                        userId
                    }, ({ msg = '删除成功' }) => {

                        layer.msg(msg);
                        $this.closest('tr').remove();

                    }, ({ msg = '删除失败' }) => {
                        layer.msg(msg);
                    })
                }
            });
        })

    },
    // 上传模板
    upload() {
        let uploader = WebUploader.create({
            swf: '/Application/Service/Assets/js/plug/webuploader/Uploader.swf', // swf文件路径
            server: '', // 文件接收服务端

            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            auto: false,
            prepareNextFile: true,
            chunked: true,
            duplicate: true, //去重， 根据文件名字、文件大小和最后修改时间来生成hash Key.
            // 只允许选择图片文件。
            accept: {
                title: 'file',
                extensions: '',
                mimeTypes: ''
            },
            pick: {
                id: '.ipt-file',
                multiple: true
            }
        });

        uploader.on('uploadAccept', function(obj, ret) {

        });

        uploader.on('uploadFinished', function() {

        });

        $('[data-act="upload"]').click(function() {
            uploader.upload(); //从初始状态开始上传
        });
    }

}


module.exports = salary;
