<template>

	<dl class="horizontal horizontal-ipt horizontal-5em fl">
			<dt class="left">缴纳方式：</dt>
			<dd class="right size2">
				<select name="payment_type" required>
					<option value="1"  {{if payment_type - 0 == 1}}selected{{/if}}>缴当月</option>
					<option value="2"  {{if payment_type - 0 == 2}}selected{{/if}}>缴次月</option>
				</select>

			</dd>
		</dl>
		<dl class="horizontal horizontal-ipt horizontal-6em fl">
			<dt class="left">报增截止日：</dt>
			<dd class="right size0">
				<select name="deadline" required>
					<option value="">请选择</option>
					{{each dayList as item i}}
					<option value="{{item}}" {{if deadline - 0 == item}}selected{{/if}}>{{item}}</option>
					{{/each}}
				</select>
			</dd>
		</dl>
		<dl class="horizontal horizontal-ipt horizontal-7em fl">
			<dt class="left">最大补缴月份：</dt>
			<dd class="right size0">
				<select name="payment_month" required>
					<option value="">请选择</option>
					{{each monthList as item i}}
					<option value="{{item}}" {{if payment_month - 0 == item}}selected{{/if}}>{{item}}</option>
					{{/each}}
				</select>
			</dd>
		</dl>
</template>

<script>
	let tpl = require('art-template');

	require('plug/selectordie');

	let listObj = {
		init(box, data){
			let self = listObj,
				html = tpl.render(self.template)(data);

			self.initEvents();

			$(box).html(html);

		},
		initEvents(){
			$(".J_rules-item select").selectOrDie();

		}
	}

	module.exports = listObj;
</script>
