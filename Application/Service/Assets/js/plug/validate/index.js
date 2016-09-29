require('./jquery.validate');
require('./addRule');


module.exports = {
	// 动态修改规则
	modifyRules: function(validateObj,opts){

       validateObj.settings = $.extend(true,{},validateObj.settings,opts)
    },
    // 密码规则
    password: {
    	rules: {
    		password: true,
			unPureLetter: true,
			unPureNum: true,
			rangelength:[6,20]
    	},
    	messages: {
    		required: '请输入密码'
    	}
    },
    r_password: {
    	messages: {
    		required: '请再次输入新密码',
			equalTo: '两次密码不一致'
    	}
    },
    verify_code: {
    	messages: {
    		required: '请输入验证码'
    	}
    },
    username: {
    	rules: {
			username: true
    	},
    	messages: {
    		required: '请输入用户名',
    		minlength: '用户名长度只能在6-20个字符之间',
    		maxlength: '用户名长度只能在6-20个字符之间'
    	}
    }
}