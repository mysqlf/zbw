<!-- 批量审核 -->
<template>
	<div class="layer-cnt">
		<form method="post" id="batchAudit_form" autocomplete="off">
			<div class="layer-pannel">
				<p>
					<label class="radio-label">
						<input type="radio" name="type" value="{{value}}" class="icheck" checked />
						{{text}}成功
					</label>
					{{if text != '发放'}}
					<label class="radio-label">
						<input type="radio" name="type" value="-{{value}}" class="icheck" />
						{{text}}失败
					</label>
					{{/if}}
				</p>
				<p>
					<label>备注：</label><textarea class="remark" name="remark"></textarea>
				</p>
			</div>
		</form>
	</div>
</template>