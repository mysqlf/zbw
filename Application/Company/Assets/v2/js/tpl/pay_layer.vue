<template>
	<div class="bank_box clearfix">
		<div class="tab_list {{if result.type == 'payinc'}}block{{/if}}">
			<label class="c-gray">账单金额：</label>
			<span>{{result.amount}}</span>元
		</div>

		{{if result.type == 'payinc'}}
		<div class="tab_list">
			<label class="c-gray">差额：</label>
			<span>{{result.diff_amount}}</span>元
		</div>
		{{/if}}
		<div class="tab_list fr">
			<label class="c-gray">实付金额：</label>
			<span class="price">{{result.actual_amount}}</span>元
		</div>
	</div>
	<!--enough不等于1时即为未结差额已够抵付当前订单 隐藏掉支付选项-->
	{{if result.enough-0 !== 1}}
	<div class="bank_choice">
		<ul class="clearfix">
			<li class="list_box">
				<a class="list_btn active list_online" data-id="1"><span>在线支付</span></a>
				<ul class="list_menu">
					<form id="listForm">
					<li class="list_pay">
						<div class="fl">
							<input type="radio" name="payType" value="1" id=""  class="radio" checked="checked">
							<i class="icon icon-alipay"></i>
						</div>
						<div class="fr">
							<p class="c-gray">支持企业、个人支付宝</p>
						</div>
					</li>
					<li class="list_pay">
						<div class="fl">
							<input type="radio" name="payType" value="2" id=""  class="radio">
							<i class="icon icon-bankpay"></i>
						</div>
						<div class="fr">
							<p class="c-gray">支持企业、个人支付宝</p>
						</div>
					</li>
					</form>
				</ul>
			</li>
			<li class="list_box">
				<a class="list_btn list_unline" data-id="2"><span>线下支付</span></a>
				<ul class="list_menu hide list_menuTwo">
					<li>
						<p>转账/汇款到以下账户</p>
						<p class="c-gray"><label>户名：</label><span id="userName"></span></p>
						<p class="c-gray"><label>账号：</label><span id="account"></span></p>
						<p class="c-gray"><label>开户行：</label><span id="bank"></span></p>
					</li>
				</ul>
			</li>
		</ul>
	</div>
	{{/if}}
</template>
<script type="text/javascript">
require('plug/icheck/icheck'); //check插件
let { getOrderBank } = require('api/Insurance');


module.exports = {
	init(obj){
		let self = this;
		self.tab(obj);
		self.radio();
	},
	tab(obj){
		
		$('.list_btn').click(function(){
			let $this = $(this);

			$('.layui-layer-btn').show();
			$('.list_btn').removeClass('active');
			$this.addClass('active');
			$('.list_menu').addClass('hide');
			$this.siblings('.list_menu').removeClass('hide');

			if($this.hasClass('list_unline')){

				getOrderBank(obj, ({ result }) => {
					let {account_name, account, branch, bank} = result;

					$('.layui-layer-btn').hide();
					$('#userName').html(account_name);
					$('#account').html(account);
					$('#bank').html(bank + branch);
				})
			}
		});
	},
	radio(){
		let $input = $('.radio');
		$input.iCheck({
			radioClass: 'check_btn'
		})
	}

}
</script>
