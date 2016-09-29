// 基本动作方法

$.ajaxSetup({
	type:'post',
	dataType: 'json',
	timeout: 30000,
	error: function(){
		layer.alert('服务器繁忙')
	}
})
module.exports = {

	ajax: function(opts, success){
		var defaults = {
			dataType: 'json',
			async: false,
			success: function(json){
				/*console.log(json.msg)
				if(typeof(json) == "undefined")
				{ 
					alert('请升级您的浏览器，为了获得更佳的体验效果，本站暂时不支持IE9以下版本的浏览器');
				}*/
				// if(json.status == null || json.status == '') return;

				if(json === null) return;

				var data = json.data,
					status = json.status - 0,
					msg = json.msg;
				if(status === 0){

					if(typeof success === 'function'){
						success(json);
					}
				} else if(status === -100001){
					layer.msg('非法操作', {time:2000})
				}else if(msg){
					layer.alert(msg);
				}
			}
		};

		var setting = $.extend({},opts,defaults);

	 	return $.ajax(setting);

	}
}