var productAct = require('modules/actions/product');

var productManage={
	init:function(){

		this.bindEvent();

	},
	bindEvent: function(){
		$('[data-act="del"]').click(function(){
			var data = $(this).data();
			layer.confirm('是否删除该产品？', {
			  btn: ['确定删除','取消'] //按钮
			}, function(){
				productAct.delProduct(data,function(){
					layer.msg('删除成功');
					location.reload();
				})
			})
		})
	}
}
module.exports = productManage;