<template>
	<table class="table table-bordered text-center">
		<thead>
    		<tr>
                <th>序号</th>
                <th>姓名</th>
                <th >身份证号码</th>
                {{if tableData[0].account}}
                <th>银行卡号</th>
                {{/if}}
              <!--   <th>会员套餐</th>
                <th>项目类型</th> -->
                <th>错误原因</th>
            </tr>
            </thead>
            <tbody class="cal-content">
                {{each tableData as item i}}
				<tr>
					<td class="">
						{{i+1}}
					</td>
					<td>
						{{item.personName}}
					</td>
					<td >
						{{item.cardNum}}
					</td>
					{{if item.account}}
					<td class="c-gray">
						{{item.account}}
					</td>
					{{/if}}
					<!-- <td class="c-gray">
						xxx
					</td>
					<td class="c-gray">
						xx
					</td>
 -->					<td class="c-red">
						{{item.info}}
					</td>
				</tr>
					{{/each}}
            </tbody>
            <tfoot>
            	
            </tfoot>
    </table>
</template>
