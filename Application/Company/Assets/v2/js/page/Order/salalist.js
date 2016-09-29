
let icheck = require('plug/icheck/index');
require('plug/selectordie/index');
require('plug/validate/validate');
require('plug/datetimepicker/datetimepicker'); //时间选择插件
require('plug/selectordie/index');

let salalist = {
    init() {
        let self = this;
        self.evenBg();
        self.validate();
        $('.select').selectOrDie();
        icheck.init().checkAll();

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

        $('.select').selectOrDie();


    },
    //奇偶行背景色
    evenBg(){
    	$('.table').find('tbody tr:odd').addClass('even_bg');
    },
    //验证导入
    validate(){
    	$("#submitBtn").click(function() {
            $('#listForm').submit();
        });
    }
}
module.exports = salalist;

