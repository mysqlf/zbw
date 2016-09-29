<!-- 确认到款 -->
<template>
	<div class="layer-confirm-money">
		<form method="post" id="confirm_form" autocomplete="off">
			<div class="layer-pannel">
				<p class="layer-amountDue">应付金额：<em>{{ due }}</em>元</p>
				{{if type == 2}}
					<p class="layer-balance">差&emsp;&emsp;额：<em>{{ balance }}</em>元</p>
				{{/if}}
				<p class="layer-amountActual">实付金额：<em>{{ actual }}</em><i>元</i></p>
				{{if paytype === 2}}
				<p class="bank-no">银行流水号：<input type="text" name="bank_no" class="ipt" required /></p>
				{{/if}}
			</div>
		</form>
	</div>
</template>