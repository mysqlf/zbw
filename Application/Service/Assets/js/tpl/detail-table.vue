<template>
          		{{if tableData['1']}}
                <div class="table_tip fl pro_tip">
                	<div class="tip_head clearfix soc_head">
                		<div class="head_list fl">
                			<label>社保</label>
                			
                		</div>
                		<div class="head_list fr">
                			<label>订单编号：</label>
                			<span>{{tableData['1'].po_order_no}}</span>
                		</div>
                		<!--<a class="btn btn_white">撤销</a>-->
                	</div>
                	<div class="tip_con">
                		<div class="tip_con_list even_bg">
                			<div class="head_list">
                				<label>申报类型：</label>
                				<span>{{tableData['1'].sidTypeValue}}</span>
                			</div>
                			<div class="head_list">
                				{{if tableData['1'].sid_state > 0}}
	                				<span class="success">
	                					<i class="icon icon-success"></i>
	                					{{tableData['1'].sidStateValue}}
	                				</span>
                				{{else if tableData['1'].sid_state < 0}}
                					<span class="fail">
                						<span class="c-red">{{tableData['1'].sidStateValue}}：</span>
                						<span>{{tableData['1'].sid_note}}</span>
                					</span>
                				{{else}}
                					<span>{{tableData['1'].sidStateValue}}</span>
                				{{/if}}
                			</div>
                		</div>
                		<div class="tip_con_list">
                			<div class="head_list audit_list">
	                			<label>缴费规则：</label>
	                			<span>{{tableData['1'].tr_name}}</span>
                			</div>
                			<div class="head_list audit_list">
	                			<label>申报时间：</label>
	                			<span>{{tableData['1'].sidCreateTimeValue}}</span>
                			</div>
                		</div>
                		<div class="tip_con_list even_bg">
                			<div class="head_list audit_list">
	                			<label>基数：</label>
	                			<span>{{tableData['1'].sid_amount}}</span>
                			</div>
                			<div class="head_list audit_list">
	                			<label>办理时间：</label>
	                			<span>{{tableData['1'].piiHandleMonthValue}}</span>
                			</div>
                		</div>
                		<div class="tip_con_list">
	                		<div class="head_list audit_list">
	                			<label>参保地：</label>
		                		<span>{{tableData['1'].piiLocationValue}}</span>
		                	</div>
                		</div>
                	</div>
                </div>
                {{/if}}
                {{if tableData['2']}}
                <div class="table_tip fl">
                	<div class="tip_head clearfix pro_head">
                		<div class="head_list fl">
                			<label>公积金</label>
                			
                		</div>
                		<div class="head_list fr">
                			
                		</div>
                		<!--<a class="btn btn_white">撤销</a>-->
                	</div>
                	<div class="tip_con">
                		<div class="tip_con_list even_bg">
                			<div class="head_list">
                				<label>申报类型：</label>
                				<span>{{tableData['2'].sidTypeValue}}</span>
                			</div>
                			<div class="head_list">
                				{{if tableData['2'].sid_state > 0}}
	                				<span class="success">
	                					<i class="icon icon-success"></i>
	                					{{tableData['2'].sidStateValue}}
	                				</span>
                				{{else if tableData['2'].sid_state < 0}}
                					<span class="fail">
                						<span class="c-red">{{tableData['2'].sidStateValue}}：</span>
                						<span>{{tableData['2'].sid_note}}</span>
                					</span>
                				{{else}}
                					<span>{{tableData['2'].sidStateValue}}</span>
                				{{/if}}
                			</div>
                		</div>
                		<div class="tip_con_list">
                			<div class="head_list audit_list">
	                			<label>缴费规则：</label>
	                			<span>{{tableData['2'].tr_name}}</span>
                			</div>
                			<div class="head_list audit_list">
	                			<label>申报时间：</label>
	                			<span>{{tableData['2'].sidCreateTimeValue}}</span>
                			</div>
                		</div>
                		<div class="tip_con_list even_bg">
                			<div class="head_list audit_list">
	                			<label>基数：</label>
	                			<span>{{tableData['2'].sid_amount}}</span>
                			</div>
                			<div class="head_list audit_list">
	                			<label>办理时间：</label>
	                			<span>{{tableData['2'].piiHandleMonthValue}}</span>
                			</div>
                		</div>
                		<div class="tip_con_list">
	                		<div class="head_list audit_list">
	                			<label>参保地：</label>
		                		<span>{{tableData['2'].piiLocationValue}}</span>
		                	</div>
		                	<div class="head_list audit_list">
	                			<label>缴纳比例：</label>
		                		<span>个人{{tableData['2'].sid_payment_info.personScale | split:'%','0'}}%</span>
		                		<span>企业{{tableData['2'].sid_payment_info.companyScale | split:'%','0'}}%</span>
		                	</div>
                		</div>
                	</div>
                </div>
                {{/if}}
                <div id="table_box" class="clear">
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
				            {{if tableData['1'] && tableData['1'].calculateResult}}
				            	{{each tableData['1'].calculateResult.items as item i}}
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
									<td>
										{{item.person.sum | replaceEmpty}}
									</td>
									<td class="c-gray">
										{{item.company.scale}}
										{{if item.company.fixedSum}}
											+{{item.company.fixedSum}}
										{{/if}}
									</td>
									<td>
										{{item.company.sum | replaceEmpty}}
									</td>
									<td>
										{{item.total | replaceEmpty}}
									</td>
								</tr>
								{{/each}}
							{{/if}}
							{{if tableData['2'] && tableData['2'].calculateResult}}
								{{each tableData['2'].calculateResult.items as item i}}
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
									<td>
										{{item.person.sum | replaceEmpty}}
									</td>
									<td class="c-gray">
										{{item.company.scale | replaceEmpty}}
										{{if item.company.fixedSum}}
											+{{item.company.fixedSum}}
										{{/if}}
									</td>
									<td>
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
				            		<td colspan="2" class="c-gray">个人缴纳：<span class="price">{{tableData.personTotal | toFixed:2}}元</span></td>
				            		<td colspan="2" class="c-gray">企业共缴纳：<span class="price">{{tableData.companyTotal | toFixed:2}}元</span></td>
				            		<td colspan="1" class="c-gray">合计：<span class="price">{{tableData.total | toFixed:2}}元</span></td>
				            	</tr>
				            {{/if}}
				            </tfoot>
				    </table>
                </div>
            <div id="page_foot">
                <div class="staff_box">
                {{if tableData['1'] || tableData['2']}}
                    <div class="staff_text">
                        企业缴纳：
                        <span class="price">{{tableData.companyTotal | toFixed:2}}</span>元
                    </div>
                    <div class="staff_text">
                        个人缴纳：
                        <span class="price">{{tableData.personTotal | toFixed:2}}</span>元
                    </div>
                    <div class="staff_text">
                        服务费：
                        <span class="price">{{tableData.service | toFixed:2}}</span>元
                    </div>
                    <div class="staff_text">
                        总缴纳：
                        <span class="total_price">{{tableData.allTotal | toFixed:2}}</span>元
                    </div>
                   {{/if}}
                </div>
            </div>
</template>
