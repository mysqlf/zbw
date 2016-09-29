<template>

	{{each items as item i}}
		<tr class="J_rules-item">
			<td>
				 {{if item.rules.replenish - 0 == 1}}
					<i class="icon icon-yes"></i>
				 {{/if}}
				 <input type="hidden" name="replenish[]" value="{{item.rules.replenish}}">
			</td>
			<td>
				{{item.name}}
				<input id="inputName{{i}}" type="hidden" name="name[]" value="{{item.name}}">
			</td>
			<td>

				<div class="inline-block">
					<input id="inputAmount{{i}}" class="ipt size0" type="number" name="amount[]" value="{{item.rules.amount}}" required placeholder="最低缴纳基数"> 
				</div>
				<span class="line">
				~
				</span>
				<div class="inline-block">
					<input id="inputAmountmax{{i}}" class="ipt size0" type="number" name="amountmax[]" value="{{item.rules.amountmax}}" required placeholder="最高缴纳基数"> 
				</div>
				元

			</td>
			<td>
				<div class="inline-block">
					<input id="inputComScale{{i}}" class="ipt size0" type="number" name="comScale[]" value="{{item.rules.company | split:'+','0' | parseFloat}}" required> %
				</div>
				<span class="line">
					+
				</span>
				<div class="inline-block">
					<input id="inputComFix{{i}}" class="ipt size0" type="number" name="comFix[]" value="{{item.rules.company | split:'+','1' | parseFloat}}"  required> 元 
				</div>
			</td>
			<td>
				<div class="inline-block">
					<input id="inputPerScale{{i}}" class="ipt size0" type="number" name="perScale[]" value="{{item.rules.person | split:'+','0' | parseFloat}}" required> % 
				</div>
				<span class="line">
					+
				</span>
				<div class="inline-block">
					<input id="inputPerFix{{i}}" class="ipt size0" type="number" name="perFix[]" value="{{item.rules.person | split:'+','1' | parseFloat}}" required> 元 
				</div>
			</td>
			<td class="opt-td">
				<a data-act="removeSbRules" data-key="{{item.name}}" href="javascript:;">
					<i class="icon icon-del"></i>
				</a> 
			</td>
		</tr>
	{{/each}}
</template>

<script>
	let tpl = require('art-template'),
		icheckFn = require('plug/icheck/index');

	require('plug/selectordie');

	let listObj = {
		init(box, data){
			let self = listObj,
				html = tpl.render(self.template)(data);

			if(data.type == 'add') {
				$(box).append(html);
			} else {
				$(box).html(html);
			}

			self.initEvents();

		},
		initEvents(){
			$(".J_rules-item select").selectOrDie();
			icheckFn.init();
		}
	}

	module.exports = listObj;
</script>
