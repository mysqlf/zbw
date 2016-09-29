<!-- 开具发票 -->
<template>
	<div class="layer-cnt">
		<form id="J_invoice-form-layer" autocomplete="off" method="post">
			<input type="hidden" name="id" value="{{ id }}" />
			<div class="layer-pannel clearfix" >
				<p class="layer-title col-12">企业信息</p>
				<p class="layer-comName col-8 ellipsis">公司名称：{{company}}</p>
				<p class="layer-amount col-4 text-right">金额：<em class="c-money f-bold">{{ amount }}</em> 元</p>
			</div>
			<div class="layer-pannel">
				<p class="layer-title">填写快递信息</p>
				<dl class="horizontal horizontal-6em">
		            <dt class="left text-right">收件人名称：</dt>
		            <dd class="right">
		                <input type="text" name="invoice_consignee" class="ipt size3" value="{{contact_name}}" required />
		            </dd>
		        </dl>
		        <dl class="horizontal horizontal-6em">
		            <dt class="left text-right">联系电话：</dt>
		            <dd class="right">
		                <input type="text" name="invoice_consignee_phone" class="ipt size3" value="{{contact_phone}}" required />
		            </dd>
		        </dl>
		        <dl class="horizontal horizontal-6em">
		            <dt class="left text-right">快递公司：</dt>
		            <dd class="right ">
		                <input type="text" name="invoice_express_company" class="ipt size3" required />
		            </dd>
		        </dl>
		        <dl class="horizontal horizontal-6em">
		            <dt class="left text-right">快递号：</dt>
		            <dd class="right">
		                <input type="text" name="invoice_express_no" class="ipt size3" required />
		            </dd>
		        </dl>
			</div>
		</form>
	</div>
</template>