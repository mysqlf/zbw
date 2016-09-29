require('jquery-validation');
require('plug/addRule');


module.exports = {
	// 动态修改规则
	modifyRules: function(validateObj,opts){

       validateObj.settings = $.extend(true,{},validateObj.settings,opts)
    }
}