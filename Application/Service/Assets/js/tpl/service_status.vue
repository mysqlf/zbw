<!-- 设定服务状态 -->
<template>
	<div class="layer-wra service-settings">
		<form method="post" id="service_settings">
			<input type="hidden" name="id" value="{{id}}">
			<div class="settings-tab ipt-box">
				<label><input type="radio" name="service_state" class="iradio" value="0" required />未签约</label>
				<label><input type="radio" name="service_state" class="iradio" value="2" />服务中</label>
				<label><input type="radio" name="service_state" class="iradio" value="3" />服务结束</label>
				<label for="service_state" class="error">签约状态必填</label>

			</div>
			<div class="layer-settings-cnt">
				<fieldset>
					<div class="form-items fl">
						<label for="overtime">合同到期</label>
						<div class="ipt-box">
							<input type="text" name="overtime" class="ipt date-day" readonly />
						</div>
					</div>
					<p>社保公积金</p>
					<div class="form-items">
						<label for="inc_handle_days">办理天数：</label>
						<div class="ipt-box">
							<select name="inc_handle_days" title="办理天数必填">
								<option value="">请选择</option>
								{{each daysNum as i}}
								<option value="{{i}}">{{i}}</option>
								{{/each}}
							</select>天
						</div>
					</div>
					<div class="form-items">
						<label for="is_salary">是否代发工资：</label>
						<div class="ipt-box">
							<label><input type="radio" name="is_salary" class="iradio" value="1" />是</label>
							<label><input type="radio" name="is_salary" class="iradio" value="0" />否</label>
							<label for="is_salary" class="error">代发工资必填</label>
						</div>
					</div>
				</fieldset>
			</div>
		</form>
	</div>
</template>
