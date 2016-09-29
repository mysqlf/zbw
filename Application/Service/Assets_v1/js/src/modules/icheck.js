require('plug/icheck/icheck');

module.exports = {
	// 动态修改规则
	init: function(){
        $('.icheck').iCheck({
		   checkboxClass: 'icheckbox_square-blue',
		   radioClass: 'iradio_square-blue',
		   increaseArea: '16px' // optional
		});

		return this;
    },
    checkAll: function(opts){
    	var defaults = {
	        checkAll: '.icheck-all',
	        checks: '.single-icheck'
	    }

	    var setting = $.extend({}, defaults, opts);
	    var $ichecks = $(setting.checks);
	    var $checkAll = $(setting.checkAll);

	    $checkAll.on('ifChecked', function() {
	        $ichecks.iCheck('check');
	    })

	    $checkAll.on('ifUnchecked', function() {
	        var $this = $(this),
	        	len = $ichecks.filter(':checked').length;

	        if(len === $ichecks.length){
	        	$ichecks.iCheck('uncheck');
	        }

	    })

	    $ichecks.on('ifChanged', function() {
	        var len = $ichecks.filter(':checked').length;

	        if (len > 0 && len === $ichecks.length) {
	            $checkAll.iCheck('check');
	        } else if (len > 0 && $checkAll.is(':checked')) {
	            $checkAll.iCheck('uncheck');
	        }
	    })
    }
}