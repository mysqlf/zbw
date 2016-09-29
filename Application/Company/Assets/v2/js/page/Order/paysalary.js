let tpl = require('plug/artTemplate'),//模板引擎插件
{ payUrl } = require('api/Insurance');

let paysalary={
	init(){
		let self = this;
		self.pay();
	},
	pay(){
		let self = this,
			$submitBtn = $('#submitBtn');

		$submitBtn.click(function(evt, handle){
			let orderId = $('#orderId').val(),
				orderType = $('#orderType').val(),
				dataObj = { orderId },
				{ type } = $(this).data();// 'payinc' 社保公积金订单 需要差额 其他不需要

			if(handle) {// 有未结算的差额 需要弹出询问框 额外传一个handle参数
				dataObj.handle = handle;
			}
			payUrl(dataObj, ({ result, confrim, info}) => {

				if(result) {
					result.type = type;
				}

				if(confrim === 1){
					if($('#orderdiff').val() === 0){
						self.payLayer(result);
					}else{
						layer.confirm(info, {
							title: '',
							btn: ['是', '否'],
							yes(){
								$submitBtn.trigger('click',1)

							},
							btn2(){
								$submitBtn.trigger('click',2)
							}
						})
					}
					
				} else {
					self.payLayer(result);
				}

			},({info}) => {
				layer.alert(info);
			})
		});
	},
	payLayer(result){
		let payLayer = require('tpl/pay_layer.vue'),
			orderType = $('#orderType').val(),
			orderId = $('#orderId').val();
		layer.open({
			title:'支付',
			content:tpl.render(payLayer.template)({result}),
			btn: result.enough - 0 === 1 ? ['确定'] : ['支付','取消'],
			area:'480px',
			success(){
				payLayer.init({orderId});

			},
			yes(){
				if(result.enough - 0 === 1 ){
					location.href = location.href;
				}
				
				if($('.list_online').hasClass('active')){
					let payType = $('.radio:checked').val();
					layer.alert('支付已完成',{
						yes(){
							location.href = location.href;
						},
						cancel(){
							location.href = location.href;
						}
					});
					window.open('/Company-Pay-payOrder?orderId='+orderId+'&orderType='+orderType+'&payType='+payType+'');
				}else{
					layer.closeAll();
				}

			},
			cancel(){
				layer.closeAll();
			}
		})
	}
}
module.exports = paysalary