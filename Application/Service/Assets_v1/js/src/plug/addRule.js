define(function(){

jQuery.validator.addMethod("letter", function(value, element) {   
			var reg = /^[a-zA-Z]+$/;
			return this.optional(element) || (reg.test(value));
		}, "只支持字母格式，如duhaitao");
jQuery.validator.addMethod("letterNum", function(value, element) {   
			var reg = /^[a-zA-Z0-9]+$/;
			return this.optional(element) || (reg.test(value));
		}, "只支持字母、数字格式，如HT0120");	
jQuery.validator.addMethod("letterNum_g", function(value, element) {   
			var reg = /^[A-Za-z0-9_-]+$/;
			return this.optional(element) || (reg.test(value));
		}, "只支持字母、数字格式，如HT0120");
jQuery.validator.addMethod("isZipCode", function(value, element) {   
			var reg = /^[0-9]{6}$/;
			return this.optional(element) || (reg.test(value));
		}, "邮编格式不正确");

jQuery.validator.addMethod("istelephone", function(value, element) {   
			var reg = /^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|17[0-9]|18[0|1|2|3|4|5|6|7|8|9])\d{8}$/;//^0?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$
			return this.optional(element) || (reg.test(value));
		}, "手机格式不正确");

jQuery.validator.addMethod("isTel", function(value, element) {   
			var reg = /^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|17[0-9]|18[0|1|2|3|4|5|6|7|8|9])\d{8}$/;
			var reg2 = /^((\+?[0-9]{2,4}\-[0-9]{3,4}\-)|([0-9]{3,4}\-))?([0-9]{7,8})(\-[0-9]+)?$/;

			return this.optional(element) || (reg2.test(value)) || (reg.test(value));
		}, "电话格式不正确");

jQuery.validator.addMethod("ispositivenum", function(value, element) {   
			var reg = /^\d+$/;
			return this.optional(element) || (reg.test(value));
		}, "只能为正整数");

jQuery.validator.addMethod("isdate", function(value, element) {   
			var reg = /(((^((1[8-9]\d{2})|([2-9]\d{3}))([-\/\._])(10|12|0?[13578])([-\/\._])(3[01]|[12][0-9]|0?[1-9]))|(^((1[8-9]\d{2})|([2-9]\d{3}))([-\/\._])(11|0?[469])([-\/\._])(30|[12][0-9]|0?[1-9]))|(^((1[8-9]\d{2})|([2-9]\d{3}))([-\/\._])(0?2)([-\/\._])(2[0-8]|1[0-9]|0?[1-9]))|(^([2468][048]00)([-\/\._])(0?2)([-\/\._])(29))|(^([3579][26]00)([-\/\._])(0?2)([-\/\._])(29))|(^([1][89][0][48])([-\/\._])(0?2)([-\/\._])(29))|(^([2-9][0-9][0][48])([-\/\._])(0?2)([-\/\._])(29))|(^([1][89][2468][048])([-\/\._])(0?2)([-\/\._])(29))|(^([2-9][0-9][2468][048])([-\/\._])(0?2)([-\/\._])(29))|(^([1][89][13579][26])([-\/\._])(0?2)([-\/\._])(29))|(^([2-9][0-9][13579][26])([-\/\._])(0?2)([-\/\._])(29)))((\s+(0?[1-9]|1[012])(:[0-5]\d){0,2}(\s[AP]M))?$|(\s+([01]\d|2[0-3])(:[0-5]\d){0,2})?$))/;
			return this.optional(element) || (reg.test(value));
		}, "日期格式不正确");

jQuery.validator.addMethod("userName", function(value, element) {   
			var reg = /^([\u4e00-\u9fa5]{1,6}|[a-zA-Z]{1,12})$/;
			return this.optional(element) || (reg.test(value));
		}, "请输入6个以内的汉字或12个字母");
		

jQuery.validator.addMethod("noSpace", function(value, element) {   
			var reg = /(^\s+)|(\s+$)/g;
			return this.optional(element) || (reg.test(value));
		}, "不能含有空格");

jQuery.validator.addMethod("zh_cn", function(value, element) {   
			var reg = /^[\u4e00-\u9fa5]+$/i;
			return this.optional(element) || (reg.test(value));
		}, "只支持汉字");

jQuery.validator.addMethod("password", function(value, element) {   
			var reg = /^[\w\!\@\#\$\%\^\&\*\(\)\{\}\[\]\:\;\"\'\<\>\,\.\?\/\\\|\-\+\=]+$/i;
			return this.optional(element) || (reg.test(value));
		}, "只支持字母、数字或字符");

jQuery.validator.addMethod("unPureLetter", function(value, element) {   
			var reg = /^[a-zA-Z]+$/;
			return this.optional(element) || (!reg.test(value));
		}, "不能是纯字母，请重新输入");

jQuery.validator.addMethod("unPureNum", function(value, element) {   
			var reg = /^[\d]+$/;
			return this.optional(element) || (!reg.test(value));
		}, "不能是纯数字，请重新输入");

jQuery.validator.addMethod("SSN", function(value, element) {   
			var reg = /^[\w\d]+$/i;
			return this.optional(element) || (reg.test(value));
		}, "社保号格式不正确");


jQuery.validator.addMethod("qq", function(value, element) {   
			var reg=/^\d{5,10}$/i;
			return this.optional(element) || (reg.test(value));
		}, "qq格式不正确");
jQuery.validator.addMethod("account", function(value, element) {   
			var reg=/^[\w_-]{4,20}$/i;
			return this.optional(element) || (reg.test(value));
		}, "4-20字符，支持字母、数字及-_");

/*身份证校验*/	
jQuery.validator.addMethod("isIdCard",function(value, element) {
		return this.optional(element) || isIdCard(value);
	}, "身份证号码不符合国定标准，请核对！ ");

function isIdCard(person_id) {
	var person_id = person_id;
	
		//身份证的地区代码对照  
		var aCity = {
			11 : "北京",
			12 : "天津",
			13 : "河北",
			14 : "山西",
			15 : "内蒙古",
			21 : "辽宁",
			22 : "吉林",
			23 : "黑龙江",
			31 : "上海",
			32 : "江苏",
			33 : "浙江",
			34 : "安徽",
			35 : "福建",
			36 : "江西",
			37 : "山东",
			41 : "河南",
			42 : "湖北",
			43 : "湖南",
			44 : "广东",
			45 : "广西",
			46 : "海南",
			50 : "重庆",
			51 : "四川",
			52 : "贵州",
			53 : "云南",
			54 : "西藏",
			61 : "陕西",
			62 : "甘肃",
			63 : "青海",
			64 : "宁夏",
			65 : "新疆",
			71 : "台湾",
			81 : "香港",
			82 : "澳门",
			91 : "国外"
		};

		// 获取证件号码  
		// 合法性验证  
		var sum = 0;

		// 出生日期  
		var birthday;

		// 验证长度与格式规范性的正则  
		var pattern = new RegExp(/(^\d{15}$)|(^\d{17}(\d|x|X)$)/i);
		if (pattern.exec(person_id)) {

			// 验证身份证的合法性的正则  
			// pattern = new RegExp(/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/);
			pattern = new RegExp(/^([110|120|130|131]\d[1-9]\d{4}|[2-9]\d{7})((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/);
			if (pattern.exec(person_id)) {

				// 获取15位证件号中的出生日期并转为正常日期       
				birthday = "19" + person_id.substring(6, 8)
						 + "-" + person_id.substring(8, 10)
						 + "-" + person_id.substring(10, 12);
			} else {
				person_id = person_id.replace(/x|X$/i, "a");

				// 获取18位证件号中的出生日期  
				birthday = person_id.substring(6, 10) + "-"
						 + person_id.substring(10, 12) + "-"
						 + person_id.substring(12, 14);

				// 校验18位身份证号码的合法性  
				for ( var i = 17; i >= 0; i--) {
					sum += (Math.pow(2, i) % 11) * parseInt(person_id.charAt(17 - i), 11);
				}
				if (sum % 11 != 1) {

					//alert("身份证号码不符合国定标准，请核对！");                               
					return false;
				}
			}
			
			//检测证件地区的合法性                                  
			if (aCity[parseInt(person_id.substring(0, 2))] == null) {

				// alert("证件地区未知，请核对！");                             
				return false;
			}
			var dateStr = new Date(birthday.replace(/-/g, "/"));

			// alert(birthday +":"+(dateStr.getFullYear()+"-"+ Append_zore(dateStr.getMonth()+1)+"-"+ Append_zore(dateStr.getDate())))  
			if (birthday != (dateStr.getFullYear() + "-"
					     + Append_zore(dateStr.getMonth() + 1)
					     + "-" + Append_zore(dateStr.getDate()))) {

				// alert("证件出生日期非法！");                           
				return false;
			}

			
		} else {

			// alert("证件号码格式非法！");                           
			return false;
		}
	   
		return true;
}
function Append_zore(temp){  
    if(temp<10) {  
        return "0"+temp;  
    }  
    else {  
        return temp;  
    }  
} 

jQuery.validator.addMethod("rangeScale", function(value, element, rules) {   
	var arr = [],
		flag = false,
		i = len = 0;
	value -= 0;

	if(rules.indexOf(',') !== -1){
		arr = rules.split(',');

		for(len=arr.length; i < len; i++){

			if(value  === parseFloat(arr[i])){
				flag = true;
				break;
			}
		}
	} else if(rules.indexOf('-') !== -1){
		arr = rules.split('-');
		var min = parseFloat(arr[0]),
			max = parseFloat(arr[1]);

		if(value >= min && value <= max){
			flag = true;
		}
	} else{
		flag = (value+'%') === rules;
	}

	return this.optional(element) || flag;
}, "请输入正确的范围");

})


// 中文提示
$.extend($.validator.messages, {
	required: "这是必填字段",
	remote: "请修正此字段",
	email: "请输入有效的电子邮件地址",
	url: "请输入有效的网址",
	date: "请输入有效的日期",
	dateISO: "请输入有效的日期 (YYYY-MM-DD)",
	number: "请输入有效的数字",
	digits: "只能输入数字",
	creditcard: "请输入有效的信用卡号码",
	equalTo: "你的输入不相同",
	extension: "请输入有效的后缀",
	maxlength: $.validator.format("最多可以输入 {0} 个字符"),
	minlength: $.validator.format("最少要输入 {0} 个字符"),
	rangelength: $.validator.format("请输入长度在 {0} 到 {1} 之间的字符串"),
	range: $.validator.format("请输入范围在 {0} 到 {1} 之间的数值"),
	max: $.validator.format("请输入不大于 {0} 的数值"),
	min: $.validator.format("请输入不小于 {0} 的数值")
});

var pswRule = {
	password: true,
	unPureLetter: true,
	unPureNum: true,
	rangelength:[6,20]
}

//设置默认配置
$.validator.setDefaults({
	errorElement: 'span',
	errorClass:'error validator-error',
	errorPlacement: function(error,element) {
		if(element.attr('type') == 'radio' || element.attr('type') == 'checkbox'){
			error.appendTo(element.parent().parent());
		} else{
			error.appendTo(element.parent());
		}
   },
   rules:{
   		password: pswRule,
   		lastPassword: pswRule,
   		comfirmPassword: pswRule,
   		username:{
   			account: true
   		},
   		telphone:{
			isTel: true
		},
		qq: {
			qq: true
		}
   },
	messages: {
		qq: {
			required: '请输入qq'
		},
		telphone: {
			required: '请输入电话'
		},
		password: {
			required: '请输入原密码',
			maxlength: '密码长度只能在6-20个字符之间',
			minlength: '密码长度只能在6-20个字符之间'
		},
		lastPassword: {
			required: '请输入新密码',
			maxlength: '密码长度只能在6-20个字符之间',
			minlength: '密码长度只能在6-20个字符之间'
		},
		username:{
			required:'请输入用户名'
		},
		comfirmPassword: {
			required: '请再次输入新密码',
			equalTo: '两次密码不一致'
		}
	}
})
