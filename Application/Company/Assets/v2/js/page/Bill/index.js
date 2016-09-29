require('plug/selectordie/index');
require('plug/validate/validate');
require('plug/datetimepicker/datetimepicker');
let index = {
	init(){
		let self = this;
		$('.select').selectOrDie();
		self.timePicker();
		self.validate()
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
         	language: 'zh-CN',
         	endDate:new Date() 
        });
    },
    validate() {
    	let $form = $("#listForm");

		$("#submitBtn").click(function(){
			$form .submit();
			
		});
    }
}
module.exports = index