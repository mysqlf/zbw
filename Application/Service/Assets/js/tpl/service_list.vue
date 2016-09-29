<!-- 设定客服 -->
<template>
	<div class="layer-wra service-list">
		<form method="post" id="service_list">
			<input type="hidden" name="id" value="{{id}}" />
			<div class="form-items">
				<label class="lb" for="service_list">客服列表</label>
				<div class="ipt-box">
					<select name="admin_id" title="必填" required>
						<option value="">请选择</option>
						{{each json.data as data key}}
						<option value="{{data.id}}">{{data.name}}</option>
						{{/each}}
					</select>
				</div>
			</div>
		</form>
	</div>
</template>
