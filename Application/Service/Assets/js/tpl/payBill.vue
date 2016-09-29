<template>	
	<form id="pay-form" class="pay-form-layer" action="">
		<div class="total-pay">
			<label class="label-left vertical-top">账单总额：</label>
			<span class="c-money f-bold">{{pay}}元</span>
		</div>
		<div class="form-group">
			<label class="label-left vertical-top text-right">
	        	实付：
	        </label>
	        <div class="inline-block">
	        	<input type="text" class="form-control " required name="actual_price" placeholder="" value="">
	        </div>
	    </div>
	    <div class="form-group">
			<label class="label-left vertical-top text-right">
	        	备注：
	        </label>
	        <div class="inline-block">
	        	<input type="text" class="form-control " name="note" placeholder="备注" value="">
	        </div>
	    </div>
	    <input type="hidden" name="id" value="{{id}}">
	    <input type="hidden" name="bill_no" value="{{bill_no}}">
    </form>
</template>