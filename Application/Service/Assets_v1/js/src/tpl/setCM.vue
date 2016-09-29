<template>
	<form id="comSetService-form" class="comSetService-form" method="post">
		<div class="form-group border-bottom check-box">
			<div class="vertical-top inline-block ">
				<input id="state1" class="icheck" type="radio" name="service_state" required {{if service_state == 0}}checked{{/if}} {{if service_state == 2}}disabled{{/if}} value="0">
			</div>
            <label for="state1" class="f-bold vertical-top inline-block {{if service_state == 2}}disabled{{/if}}">
                 未签约
            </label>
	    </div>
	    <div id="state2-box" class="border-bottom clearfix">
		    <div class="form-group col-xs-12">
				<div class="vertical-top inline-block ">
					<input id="state2" class="icheck" type="radio" name="service_state" {{if service_state == 2}}checked{{/if}} required value="2">
				</div>
	            <label for="state2" class="vertical-top inline-block f-bold">
	                 服务中
	            </label>
		    </div>
		    <div class="clearfix2">
			    <div class="form-group col-xs-6">
		            <label class="label-left vertical-top w-7em text-right">
		                服务有效期：
		            </label>
		            <div class="inline-block">
		                <input id="overtime" type="text" readonly class="form-control" required name="overtime" value="{{overtime}}"> 
		            </div>
		        </div>
		        <div class="form-group col-xs-6">
		            <label class="label-left vertical-top w-7em text-right">
		                报增减截止日：
		            </label>
		            <div class="inline-block vertical-top" >
		                <select class="base-size" name="abort_add_del_date" required>
		                	<option value="">请选择</option>
		                	{{each days}}
							<option value="{{$value}}" {{if $value == abort_add_del_date}}selected{{/if}}>
								{{$value}}
							</option>
		                	{{/each}}
		                </select>
		                <div class="inline-block vertical-top">号</div>
		            </div>
		        </div>
	        </div>
		        <div class="clearfix2">
		        <div class="form-group col-xs-6">
		            <label class="label-left vertical-top w-7em text-right">
		                账单日：
		            </label>
		            <div class="inline-block vertical-top" >
		                <select class="gutter-right" name="bill_month_state" required>
		                	<option value="">请选择</option>
		                	<option value="0" {{if 0 == bill_month_state}}selected{{/if}}>当月</option>
		                	<option value="1" {{if 1 == bill_month_state}}selected{{/if}}>次月</option>
		                </select>
		            </div>
		            <div class="inline-block vertical-top" >
		                <select class="" name="create_bill_date" required>
		                	<option value="">请选择</option>
		                	{{each days}}
							<option value="{{$value}}" {{if $value == create_bill_date}}selected{{/if}}>
								{{$value}}
							</option>
		                	{{/each}}
		                </select>
		            </div>
		            <div class="inline-block vertical-top">号</div>
		            
		        </div>
		        <div class="form-group col-xs-6">
		            <label class="label-left vertical-top w-7em text-right">
		                支付截止日期：
		            </label>
		            <div class="inline-block vertical-top" >
		                <select class="gutter-right" name="payment_month_state" required>
		                	<option value="">请选择</option>
		                	<option value="0" {{if 0 == payment_month_state}}selected{{/if}}>当月</option>
		                	<option value="1" {{if 1 == payment_month_state}}selected{{/if}}>次月</option>
		                </select>
		            </div>
		            <div class="inline-block vertical-top" >
		                <select class="" name="abort_payment_date" required>
		                	<option value="">请选择</option>
		                	{{each days}}
							<option value="{{$value}}" {{if $value == abort_payment_date}}selected{{/if}}>
								{{$value}}
							</option>
		                	{{/each}}
		                </select>
		            </div>
		            号
		        </div>
		    </div>
			<div class="form-group col-xs-12 check-box">
	            <label class="label-left vertical-top w-7em text-right">
	                是否代发工资：
	            </label>
	            <div class="inline-block vertical-top gutter-x">
	                <input id="is_salary1" class="icheck" type="radio" name="is_salary" value="1" required {{if 1 == is_salary}}checked{{/if}}>
	                <label for="is_salary1" class=" ve
	                rtical-top inline-block gutter-right">
	                     是
	                </label>
	                <input id="is_salary2" class="icheck" type="radio" name="is_salary" value="0" required {{if 0 == is_salary}}checked{{/if}}>
	                <label for="is_salary2" class="vertical-top vertical-top inline-block">
	                     否
	                </label>
	            </div>
	        </div>
	        
		</div>
		<div class="form-group">
			<div class="vertical-top inline-block ">
				<input id="state3" class="icheck" type="radio" name="service_state" required value="3">
			</div>
            <label for="state3" class="f-bold vertical-top inline-block">
                 服务结束
            </label>
	    </div>
	    <input type="hidden" name="id" value="{{id}}">
	</form>
</template>