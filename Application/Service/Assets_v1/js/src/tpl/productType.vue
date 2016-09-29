
<template>
	
	{{if type == 1}}
	<div class="form-group  col-xs-12">
		<div class="col-md-6 col-lg-5">
			<label class="label-left vertical-top" for="">会员费：</label>
	    	<div class="inline-block">
	    		<input name="member_price" class="form-control w-select" type="text" value="0"> 元
				<label class="gutter-left">
					<input class="icheck" name="no_member_price" type="radio" value="1">
					免会员费
				</label>
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
		</div>
		
	</div>
	<div class="form-group  col-xs-12">
		<div class="col-md-6 col-lg-5">
			<label class="label-left vertical-top">服务费：</label>
			<input name="service_price[]" class="form-control w-select" type="text" value="0">
			元/月.人
		</div>
		
	</div>
	{{else if type == 2}}
	<div class="J_server-box">
		<div class="form-group  col-xs-12">
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
		</div>
	</div>
	{{else if type == 3}}
	<div class="form-group  col-xs-12">
		<div class="col-md-6 col-lg-5">
			<label class="label-left vertical-top" for="">会员费：</label>
	    	<div class="inline-block">
				免服务费
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
		</div>
	</div>
	{{/if}}

</template>
