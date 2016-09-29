<!-- 设定客服 -->
<template>
	<div class="layer-wra service-list">
		<form method="post" id="set_service">
			<p class="layer-title">请指定一个客服</p>
			<div class="form-items">
				<label class="lb" for="service_list">客服列表</label>
				<div class="ipt-box">
					<select name="service_list" title="必填" required>
						<option value="">请选择</option>
						{{each data}}
						<option value="{{data[$index].id}}">{{data[$index].name}}</option>
						{{/each}}
					</select>
				</div>
			</div>
		</form>
	</div>
</template>