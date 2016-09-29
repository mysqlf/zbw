
let { dateRange, countDownFn } = require('modules/date');

require('plug/validate/validate');
require('plug/datetimepicker/datetimepicker'); //时间选择插件
require('plug/selectordie/index');


let index = {
    init() {
        let self = this;
        self.evenBg();
        self.validate();
        $('.select').selectOrDie();

        $(".timepicker").datetimepicker({
            format: 'yyyy-mm',
            weekStart: 1,
            autoclose: true,
            startView: 3,
            minView: 3,
            forceParse: false,
            language: 'zh-CN',
          /*  startDate: startDate,*/
            endDate: new Date()
        })

        dateRange('create', 'pay');

        countDownFn();

    },

    //奇偶行背景色
    evenBg(){
    	$('.table').find('tbody tr:odd').addClass('even_bg');
    },
    //验证导入
    validate(){
    	$("#submitBtn").click(function() {
            $('#listForm').submit();
            $(this).attr('disabled','disabled');
        });
    }
}
module.exports = index;
