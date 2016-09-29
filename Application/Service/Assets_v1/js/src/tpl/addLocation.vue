<template>
	<form id="addLocation-form" class="addLocation-form">
		<div class="form-group col-xs-12">
	        <label class="label-left vertical-top w-10em text-right">
	            参保地：
	        </label>
	        <div class="inline-block vertical-top" >
	            <select class="gutter-right base-size" name="location" required>
	            	<option value="">请选择</option>
	            	{{each locationArr}}
	            	<option value="{{$value.location}}" {{if location == $value.location}}selected{{/if}}>{{$value.name}}</option>
	            	{{/each}}
	            </select>
	        </div>
	        
	    </div>
		<div class="form-group col-xs-12">
	        <label class="label-left vertical-top w-10em text-right">
	            社保/公积金服务费：
	        </label>
	        <div class="inline-block">
	            <input id="ss_service_price" type="text" class="form-control" required name="ss_service_price" value="{{ss_service_price}}"> 
	        </div>
	        <div class="inline-block">元/人/月</div>
	    </div>
	    <div class="form-group col-xs-12">
	        <label class="label-left vertical-top w-10em text-right">
	            代发工资服务费：
	        </label>
	        <div class="inline-block">
	            <input id="af_service_price" type="text" class="form-control" required name="af_service_price" value="{{af_service_price}}"> 
	        </div>
	        <div class="inline-block">元/人/月</div>
	    </div>
	    <input type="hidden" name="id" value="{{id}}">
	    <input type="hidden" name="location_id" value="{{location_id}}">
	    
	</form>
</template>