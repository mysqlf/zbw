<!-- 批量审核 -->
<template>
	<div class="layer-cnt">
		<form method="post" id="batchGrant_form" autocomplete="off">
			<div class="layer-pannel">
				<p>
					<label><input type="radio" name="type" class="icheck" value="3" checked />发放成功</label>
					<label><input type="radio" name="audit" class="icheck" value="-3" />发放失败</label>
				</p>
				<p>
					<label>备注：</label><textarea></textarea>
				</p>
			</div>
		</form>
	</div>
</template>