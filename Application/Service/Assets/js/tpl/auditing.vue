<template>
	<form id="J_auditing-layer-form">
		<input type="hidden" name="socPiiId" value="{{socPiiId}}">
		{{if isBuyPro}}
		<input type="hidden" name="proPiiId" value="{{proPiiId}}">
		{{/if}}
		<input type="hidden" name="type" value="1">
		<div class="auditing-layer-box">

			<span class="auditing-layer-title">
				<!-- 社保 -->
			</span>

			<label class="icheck-label">
				<input type="radio" class="icheck" name="socOperateState" value="1" checked> 审核通过
			</label>
			<label class="icheck-label">
				<input type="radio" class="icheck" name="socOperateState" value="-1"> 审核失败
			</label>
			<div class="remark-layer">
				备注
				<input class="ipt" type="text" name="socRemark">
			</div>
		</div>
<!-- 		{{if isBuyPro}}
<div class="auditing-layer-box">
	<span class="auditing-layer-title">
		公积金
	</span>
	
	<label class="icheck-label">
		<input type="radio" class="icheck" name="proOperateState" value="1" checked> 审核通过
	</label>
	<label class="icheck-label">
		<input type="radio" class="icheck" name="proOperateState" value="-1"> 审核失败
	</label>
	<div class="remark-layer">
		备注
		<input class="ipt" type="text" name="proRemark">
	</div>
</div>
{{/if}} -->
	</form>
</template>

