
module.exports = {
	// 验证身份证的合法性的正则
	pattern: new RegExp(/(^\d{15}$)|(^\d{17}(\d|x|X)$)/i),
	// 验证长度与格式规范性的正则
	pattern2: new RegExp(/^([110|120|130|131]\d[1-9]\d{4}|[2-9]\d{7})((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/),
	//身份证的地区代码对照  
	city:{
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
	},
	getBirthday: function(person_id){
		var self = this,
			birthday;

		if (self.pattern.exec(person_id)) {

			if(self.pattern2.exec(person_id)){
				// 获取15位证件号中的出生日期并转为正常日期       
				birthday = "19" + person_id.substring(6, 8)
						 + "-" + person_id.substring(8, 10)
						 + "-" + person_id.substring(10, 12);
			} else{
				person_id = person_id.replace(/x|X$/i, "a");

				// 获取18位证件号中的出生日期  
				birthday = person_id.substring(6, 10) + "-"
						 + person_id.substring(10, 12) + "-"
						 + person_id.substring(12, 14);
			}
		}

		return birthday;
	},
	//检测证件地区的合法性
	isCity: function(person_id){
		if (this.city[parseInt(person_id.substring(0, 2))] == null) {                            
			return false;
		}
		return true;
	},
	// 校验18位身份证号码的合法性
	isLength: function(person_id){
		var sum = 0;

		for ( var i = 17; i >= 0; i--) {
			sum += (Math.pow(2, i) % 11) * parseInt(person_id.charAt(17 - i), 11);
		}
		if (sum % 11 != 1) {
			return false;
		}
	},
	// 生日合法性
	isBirthday: function(person_id){
		var self = this,
			birthday = this.getBirthday(person_id),
			dateStr = new Date(birthday.replace(/-/g, "/"));

		if (birthday != (dateStr.getFullYear() + "-"
			+ self.appendZore(dateStr.getMonth() + 1)
			+ "-" + self.appendZore(dateStr.getDate()))) {
			return false;
		}
	},
	appendZore:function(temp){
		if(temp<10) {  
	        return "0"+temp;  
	    }  
	    else {  
	        return temp;  
	    }  
	}
}
