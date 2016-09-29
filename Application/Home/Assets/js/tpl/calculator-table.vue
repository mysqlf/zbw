<template>

	{{if sb_result}}
		{{each sb_result.data.items as item i}}
	        <tr>
				<td class="calculator-bg calculator-bg{{item.type == 2 ? 2 : 1}}">
					{{item.name}}
				</td>
				<td>
					{{item.amount | replaceEmpty}}
				</td>
				<td class="c-gray">
					{{item.person.scale | replaceEmpty}}
					{{if item.person.fixedSum}}
						+ {{item.person.fixedSum}}
					{{/if}}
				</td>
				<td>
					{{item.person.sum | replaceEmpty}}
				</td>
				<td class="c-gray">
					{{item.company.scale | replaceEmpty}} 
					{{if item.company.fixedSum}}
						+ {{item.company.fixedSum}}
					{{/if}}
				</td>
				<td>
					{{item.company.sum | replaceEmpty}}
				</td>
			</tr>
	    {{/each}}
	{{/if}}

	{{if gjj_result}}
		{{each gjj_result.data.items as item i}}
	        <tr>
				<td class="calculator-bg calculator-bg{{item.type == 2 ? 2 : 3}}">
					{{item.name}}
				</td>
				<td>
					{{item.amount | replaceEmpty}}
				</td>
				<td class="c-gray">
					{{item.person.scale | replaceEmpty}}

					{{if item.person.fixedSum}}
						+ {{item.person.fixedSum}}
					{{/if}}
				</td>
				<td>
					{{item.person.sum | replaceEmpty}}
				</td>
				<td class="c-gray">
					{{item.company.scale | replaceEmpty}} 
					{{if item.company.fixedSum}}
						+ {{item.company.fixedSum}}
					{{/if}}
					
				</td>
				<td>
					{{item.company.sum | replaceEmpty}}
				</td>
			</tr>
	    {{/each}}
	{{/if}}
	
	{{if sb_result || gjj_result}}
	    <tr class="subtotal">
			<td colspan="2"></td>
			<td colspan="2">
				个人共缴纳：<span class="c-money">
				{{ perTotal }} 元
				</span>
			</td>
			<td colspan="2">
				企业共缴纳：<span class="c-money">
					{{ compTotal}} 元
				</span>
			</td>
		</tr>
		<tr>
			<td class="c-money total-money" colspan="6">总缴纳：
				<span class="c-money">{{ $helpers.toFixed(perTotal + compTotal, 2)}} 元</span>
			</td>
		</tr>
	{{else}}
		<tr>
			<td colspan="6">
				<div class="no-data-td c-gray">请选择城市，点击计算后才能生成明细哦~</div>
			</td>
		</tr>
	{{/if}}

</template>
