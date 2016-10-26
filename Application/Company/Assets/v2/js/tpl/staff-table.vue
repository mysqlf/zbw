<template>
	<table class="table table-bordered text-center">
		<thead>
    		<tr>
                <th rowspan="2">缴纳项目</th>
                <th rowspan="2">社保基数</th>
                <th colspan="2">个人缴纳</th>
                <th colspan="2">企业缴纳</th>
                <th rowspan="2">合计金额</th>
            </tr>
            <tr>
                <th>缴纳比例</th>
                <th>缴纳金额（元）</th>
                <th>缴纳比例</th>
                <th>缴纳金额（元）</th>
            </tr>
            </thead>
            <tbody class="cal-content">
			{{if tableData['1']}}
				{{each tableData['1'].data.items as item i}}
				<tr>
					<td class="td_bg td_bg1">
						{{item.name}}
					</td>
					<td>
						{{item.amount | replaceEmpty}}
					</td>
					<td class="c-gray">
						{{item.person.scale | replaceEmpty}}
						{{if item.person.fixedSum}}
							+{{item.person.fixedSum}}
						{{/if}}
					</td>
					<td class="{{if !item.person.handle_result}}c-red{{/if}}">
						{{item.person.sum | replaceEmpty}}
					</td>
					<td class="c-gray">
						{{item.company.scale}}
						{{if item.company.fixedSum}}
							+{{item.company.fixedSum}}
						{{/if}}
					</td>
					<td class="{{if !item.company.handle_result}}c-red{{/if}}">
						{{item.company.sum | replaceEmpty}}
					</td>
					<td>
						{{item.total | replaceEmpty}}
					</td>
				</tr>
				{{/each}}
			{{/if}}
			{{if tableData['2']}}
				{{each tableData['2'].data.items as item i}}
				<tr>
					<td class="td_bg td_bg3">
						{{item.name}}
					</td>
					<td>
						{{item.amount | replaceEmpty}}
					</td>
					<td class="c-gray">
						{{item.person.scale | replaceEmpty}}
						{{if item.person.fixedSum}}
							+{{item.person.fixedSum}}
						{{/if}}
					</td>
					<td class="{{if !item.person.handle_result}}c-red{{/if}}">
						{{item.person.sum | replaceEmpty}}
					</td>
					<td class="c-gray">
						{{item.company.scale | replaceEmpty}}
						{{if item.company.fixedSum}}
							+{{item.company.fixedSum}}
						{{/if}}
					</td>
					<td class="{{if !item.company.handle_result}}c-red{{/if}}">
						{{item.company.sum | replaceEmpty}}
					</td>
					<td>
						{{item.total | replaceEmpty}}
					</td>
				</tr>
				{{/each}}
			{{/if}}
            </tbody>
            <tfoot>
            {{if tableData['1'] || tableData['2']}}
            	<tr>
            		<td colspan="2"></td>
            		<td colspan="2" class="c-gray">个人共缴纳：<span class="price">{{tableData.personTotal}}元</span></td>
            		<td colspan="2" class="c-gray">企业共缴纳：<span class="price">{{tableData.companyTotal}}元</span></td>
            		<td colspan="1" class="c-gray">合计：<span class="price">{{tableData.allTotal}}元</span></td>
            	</tr>
            </tfoot>
            {{/if}}
    </table>

    {{if tableData['1'] && tableData['1'].sidStateValue}}
		<div class="sid-state inline-block">
		 	社保状态：<i class="icon icon-{{if tableData['1'].sid_state > 0}}success{{else}}fail{{/if}}"></i>
		 	<span class="c-{{if tableData['1'].sid_state > 0}}success{{else}}fail{{/if}}">{{tableData['1'].sidStateValue}}</span>
	 	</div>
 	{{/if}}
	
 	{{if tableData['2'] && tableData['2'].sidStateValue}}
 		<div class="sid-state inline-block">
	 		公积金状态：<i class="icon icon-{{if tableData['2'].sid_state > 0}}success{{else}}fail{{/if}}"></i>
	 		<span class="c-{{if tableData['2'].sid_state > 0}}success{{else}}fail{{/if}}">{{tableData['2'].sidStateValue}}</span>
	 	</div>
 	{{/if}}

 	{{if tableData.isAbnormal}}
    	<a id="J_abnormal" class="btn btn-border" href="javascript:;" data-socstate="{{tableData['1'] && tableData['1'].sid_state}}" data-prostate="{{tableData['2'] && tableData['2'].sid_state}}">缴纳异常</a>
    {{/if}}
</template>
