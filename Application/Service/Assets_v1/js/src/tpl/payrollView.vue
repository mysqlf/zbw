<template>	
	<form id="payroll-audit-form" class="form-inline gutter clearfix">
	  <div class="form-group col-xs-6">
	  	<label class="text-right">&nbsp;&nbsp;姓名：</label>{{user_name}}
	  </div>
	  <div class="form-group col-xs-6">
	  <label class="text-right">身份证号码：</label>{{card_num}}
	  </div>
	  <div class="form-group col-xs-6">
	    <label class="text-right">银行名称：</label>
	    {{bank}}
	  </div>
	  <div class="form-group col-xs-6">
	    <label class="text-right">支行名称：</label>
	    {{branch}}
	  </div>
	  <div class="form-group col-xs-6">
	    <label class="text-right" for="branch">银行卡号：</label>
	    {{account}}
	  </div>
	  <div class="form-group col-xs-6">
	    <label class="text-right" for="date">工资年月：</label>
	    {{date}}
	  </div>
	 	<div class="form-group col-xs-6">
		    <label class="text-right">应发工资：</label>
		    {{wages}}
			 元
		  </div>
		  <div class="form-group col-xs-6">
		    <label class="text-right">扣个人所得税：</label>
		    {{deduction_income_tax}}
		     元
		</div>
	  	<div class="form-group col-xs-6">
		 	  <label class="text-right">扣个人社保：</label>
		 	  {{deduction_social_insurance}}
			 元
		 	</div>
		 	<div class="form-group col-xs-6">
		 	  <label class="text-right">扣个人公积金：</label>
		 	  {{deduction_provident_fund}}
		 	   元
		 	</div>
		<div class="form-group col-xs-6">
		    <label class="text-right">补发：</label>
		    	{{replacement}}
		     元
		  </div>
		  <div class="form-group col-xs-6">
		    <label class="text-right">其他扣除：</label>
		    {{deduction_other}}
			 元
	  	   </div>
	  <p class="text-center wages-tip divider-top col-xs-12">实发工资：<span id="wages-txt" class="c-money">{{actual_wages}}</span></p>

	</form>
</template>