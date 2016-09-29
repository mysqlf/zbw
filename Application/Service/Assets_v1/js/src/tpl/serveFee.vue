<template>
	<div class="serviceFee">
		<div class="col-md-6 col-lg-5">
			<label class="label-left vertical-top" for="">服务费：</label>
	    	<div class="inline-block">
	    		<input name="service_price[]" class="form-control w-select" type="text" value="0"> 元
	    	</div>
		</div>
		<div class="col-md-6 col-lg-7">
			<label for="" class="w-5em vertical-top">期限：</label>
	  		<div class="inline-block">
	    		<select class="form-control" name="validity[]">
	            	<option value="">请选择</option>
	            	{{each months}}
	            	<option value="{{$value}}">{{$value}}</option>
	            	{{/each}}
	            </select> 个月
	        </div>
	        <a class="remove-icon" href="javascript:;" data-act="removeFee">删除</a>
	        <a class="btn btn-primary gutter-left" href="javascript:;" data-act="addServiceFee">添加</a>
		</div>
	</div>
</template>