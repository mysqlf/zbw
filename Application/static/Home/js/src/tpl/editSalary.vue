<template>	
	<form id="payroll-audit-form" class="form-inline gutter clearfix" method="post" action="">

	  <div class="form-group col-xs-12">
	    <label class="text-right w-9em">姓名：</label>
	    <div class="inline-block text-blue f-bold">
	    	{{user_name}}
	    </div>
	  </div>
	  <div class="form-group col-xs-12">
	    <label class="text-right w-9em">身份证号码：</label>
	    <div class="inline-block text-blue f-bold">
	    	{{card_num}}
	    </div>
	  </div>
	  <div class="form-group col-xs-12">
	    <label class="text-right w-9em">工资年月：</label>
	    <div class="inline-block text-blue f-bold">
	    	{{date}}
	    </div>
	  </div>
	  <div class="form-group col-xs-12">
	    <label class="text-right w-9em" for="bank">银行名称：</label>
	    <div class="inline-block">
		    <select id="bank-select" class="base-size zb_inpt" name="bank" required>
		    	<option value="">请选择</option>

		    	{{each bankList}}
		    		<option value="{{$value.bank}}" {{if $value.bank == bank}}selected{{/if}}>{{$value.bank}}</option>
		    	{{/each}}
		    </select>
	    </div>
	  </div>
	  <div class="form-group col-xs-12" >
	    <label class="text-right w-9em" for="branch">支行名称：</label>
	    <div class="inline-block">
	    	<input type="text" class="form-control zb_inpt" required id="branch" name="branch" placeholder="" value="{{branch}}">
	    </div>
	  </div>
	  <div class="form-group col-xs-12">
	    <label class="text-right w-9em" for="branch">银行卡号：</label>
	    <div class="inline-block">
	   		<input type="text" class="form-control zb_inpt" required id="account" name="account" placeholder="" value="{{account}}">
	   	</div>
	  </div>

 		<div class="form-group col-xs-12">
		    <label class="text-right w-9em" for="wages">应发工资：</label>
		    <div class="inline-block">
		    	<input type="text" class="form-control zb_inpt form-control zb_inpt-small J-counter J-counter-add" required id="wages" name="wages" placeholder="" value="{{wages}}">
		    </div>
			 元
		 </div>
		  <div class="form-group col-xs-12">
		    <label class="text-right w-9em" for="deduction_income_tax">扣个人所得税：</label>
		    <div class="inline-block">
		    	<input type="text" class="form-control zb_inpt form-control zb_inpt-small J-counter J-counter-cut" required id="deduction_income_tax" name="deduction_income_tax" placeholder="" value="{{deduction_income_tax}}">
		    </div>
		     元
		</div>
	 	<div class="form-group col-xs-12">
	 	    <label class="text-right w-9em" for="deduction_social_insurance">扣个人社保：</label>
	 	    <div class="inline-block">
	 	  		<input type="text" class="form-control zb_inpt form-control zb_inpt-small J-counter J-counter-cut" required id="deduction_social_insurance" name="deduction_social_insurance" placeholder="" value="{{deduction_social_insurance}}">
	 	    </div>
		 	元
	 	</div>
	 	<div class="form-group col-xs-12">
	 	    <label class="text-right w-9em" for="deduction_provident_fund">扣个人公积金：</label>
	 	    <div class="inline-block">
	 	  		<input type="text" class="form-control zb_inpt form-control zb_inpt-small J-counter J-counter-cut" required id="deduction_provident_fund" name="deduction_provident_fund" placeholder="" value="{{deduction_provident_fund}}">
	 	  	</div>
	 	   元
		</div>
		<div class="form-group col-xs-12">
		  <label class="text-right w-9em" for="replacement">补发：</label>
		  <div class="inline-block">
		  	<input type="text" class="form-control zb_inpt form-control zb_inpt-small" required id="replacement" name="replacement" placeholder="" value="{{replacement}}">
		  </div>
		   元
		</div>
		<div class="form-group col-xs-12">
		  <label class="text-right w-9em" for="deduction_other">扣除：</label>
		  <div class="inline-block">
		  	<input type="text" class="form-control zb_inpt form-control zb_inpt-small" required id="deduction_other" name="deduction_other" placeholder="" value="{{deduction_other}}">
		  </div>
		 元
	  	</div>

	  	<div class="wages-tip form-group  col-xs-12 f-bold text-gray2">
	  		<span class="text-right inline-block w-9em">实发工资：</span>
	  		<div class="inline-block text-money">
	  			<span id="wages-txt">{{actual_wages}}</span> 元
	  		</div>
	  	</div>

		 <input type="hidden" name="base_id" value="{{base_id}}">
		 <input type="hidden" name="salaryId" value="{{salaryid}}">

	</form>
</template>