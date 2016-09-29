<template>
	<form id="J_abnormal-layer-form">
		<input type="hidden" value="-4" name="type">
		<ul class="layer-table-list">
			<li class="clearfix list-head">
				<div class="col-2">
					&nbsp;
				</div>
				<div class="col-4">
					<span class="list-tag">
						企业缴纳
					</span>
					
				</div>
				<div class="col-4">
					<span class="list-tag">
						个人缴纳
					</span>
				</div>
				<div class="col-2">
					<span class="list-tag">
						产生差额(元)
					</span>
				</div>

			</li>
			{{if tableData['1'] && tableData['1'].sid_state == 3}}
				<input type="hidden" name="data[1][id]" value="{{tableData['1'].sid_id}}">
				<input type="hidden" name="data[1][insurance_info_id]" value="{{socPiiId}}">
				<input type="hidden" name="data[1][pay_date]" value="{{tableData.pay_date}}">
				{{each tableData['1'].data.items as item i}}
					<li class="clearfix layer-list-item" data-person="{{item.person.sum | replaceEmpty}}" data-company="{{item.company.sum | replaceEmpty}}">
						<div class="col-2 text-center">
							{{item.name}}
							<input type="hidden" name="data[1][data][{{i}}][name]" value="{{item.name}}">
						</div>
						<div class="col-4">
							<label class="icheck-label">
								<input type="radio" {{if item.company.handle_result}}checked{{/if}} class="icheck J_company-icheck" name="data[1][data][{{i}}][company]"  value="1">
								缴费成功
							</label>
							<label class="icheck-label">
								<input type="radio"  {{if !item.company.handle_result}}checked{{/if}} class="icheck J_company-icheck" name="data[1][data][{{i}}][company]"  value="0">
								缴费失败
							</label>
						</div>
						<div class="col-4">
							<label class="icheck-label">
								<input type="radio"  {{if item.person.handle_result}}checked{{/if}} class="icheck J_person-icheck" name="data[1][data][{{i}}][person]" value="1">
								缴费成功
							</label>
							<label class="icheck-label">
								<input type="radio"  {{if !item.person.handle_result}}checked{{/if}} class="icheck J_person-icheck" name="data[1][data][{{i}}][person]" value="0">
								缴费失败
							</label>
						</div>
						<div class="J_difference col-2 text-center">
							0.00
						</div>
					</li>
				{{/each}}
			{{/if}}
			{{if tableData['2'] && tableData['2'].sid_state == 3}}

				<input type="hidden" name="data[2][id]" value="{{tableData['2'].sid_id}}">
				<input type="hidden" name="data[2][insurance_info_id]" value="{{proPiiId}}">
				<input type="hidden" name="data[2][pay_date]" value="{{tableData.pay_date}}">
				{{each tableData['2'].data.items as item i}}
					<li class="clearfix layer-list-item" data-person="{{item.person.sum | replaceEmpty}}" data-company="{{item.company.sum | replaceEmpty}}">
						<div class="col-2 text-center">
							{{item.name}}
							<input type="hidden" name="data[2][data][{{i}}][name]" value="{{item.name}}">
						</div>
						<div class="col-4">
							<label class="icheck-label">
								<input type="radio" {{if item.company.handle_result}}checked{{/if}}  class="icheck J_company-icheck" name="data[2][data][{{i}}][company]"  value="1">
								缴费成功
							</label>
							<label class="icheck-label">
								<input type="radio" {{if !item.company.handle_result}}checked{{/if}}  class="icheck J_company-icheck" name="data[2][data][{{i}}][company]"  value="0">
								缴费失败
							</label>
						</div>
						<div class="col-4">
							<label class="icheck-label">
								<input type="radio" {{if item.person.handle_result}}checked{{/if}}  class="icheck J_person-icheck" name="data[2][data][{{i}}][person]" value="1">
								缴费成功
							</label>
							<label class="icheck-label">
								<input type="radio" {{if !item.person.handle_result}}checked{{/if}} class="icheck J_person-icheck" name="data[2][data][{{i}}][person]" value="0">
								缴费失败
							</label>
						</div>
						<div class="J_difference col-2 text-center">
							0.00
						</div>
					</li>
				{{/each}}
			{{/if}}
			
		</ul>
	</form>
</template>

