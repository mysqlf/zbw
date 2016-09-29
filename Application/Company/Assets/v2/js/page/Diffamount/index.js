let { dateRange } = require('modules/date');
require('plug/selectordie/index');
require('plug/validate/validate');
require('plug/datetimepicker/datetimepicker'); //时间选择插件

let index = {
	init(){
		let self = this;
		$('.select').selectOrDie();
		self.validate();
		self.timePick();
	},
	validate(){
		let $form = $("#listForm");

		$("#submitBtn").click(function(){
			$form .submit();
		});

	},
	timePick(){
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


        dateRange('pay');
	}
}
module.exports =index 