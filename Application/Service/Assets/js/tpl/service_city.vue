<!-- 设定服务城市 -->
<template>
	<div class="layer-wra service-city">
		<form method="post" id="service_city" autocomplete="off">
			<div class="form-items">
				<label class="lb" for="location">服务城市</label>
				<input type="hidden" name="id" value="{{id}}" />
				<input type="hidden" name="location_id" value="{{tblData.location_id}}" />
				<div class="ipt-box">
					<p class="city-picker-input ipt-selected">
						<span class="picker-txt">请选择</span>
						<input type="hidden" name="location" class="hidden-txt" value="{{tblData.location}}" required />
					</p>
				</div>
			</div>
			<div class="form-items">
				<label class="lb" for="soc_service_price">社保服务费</label>
				<div class="ipt-box">
					<input type="text" name="soc_service_price" class="ipt w-normal" value="{{tblData.soc_service_price}}" required />元/人/月
				</div>
			</div>
			<div class="form-items">
				<label class="lb" for="pro_service_price">公积金服务费</label>
				<div class="ipt-box">
					<input type="text" name="pro_service_price" class="ipt w-normal" value="{{tblData.pro_service_price}}" required />元/人/月
				</div>
			</div>
			{{if tblData.af_service_price}}
			<div class="form-items">
				<label class="lb" for="af_service_price">代发工资服务费</label>
				<div class="ipt-box af-service-price">
					<span>{{tblData.af_service_price}}</span>元/人/月
				</div>
			</div>
			{{/if}}
		</form>
	</div>
	
</template>