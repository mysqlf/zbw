require('plug/selectordie/index');
require('plug/validate/validate');
require('plug/datetimepicker/datetimepicker');
let genBillList = {
    init() {
        let self = this;
        self.evenBg();
        $('.select').selectOrDie();
        self.validate();
        self.timePicker();
        self.check();
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
    },
    //时间选择
    timePicker() {
        $(".timepicker").datetimepicker({
        	format: 'yyyy-mm',
            weekStart: 1,  
         	autoclose: true,  
         	startView: 3,  
         	minView: 3,  
         	forceParse: false,  
         	language: 'zh-CN'  
        });
    },
    //复选框
    check() {
        let $showBtn = $('.check_btn');
        $showBtn.click(function() {
            let $this = $(this);
            $this.toggleClass('active');
        });
    }
}
module.exports = genBillList;
