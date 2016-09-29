<template>	
	<form id="payroll-audit-form" class="form-inline gutter clearfix" method="post" action="">
	  <div class="form-group col-xs-12">
	  <p class="col-xs-4">&nbsp;&nbsp;姓名：{{user_name}}</p>
	  <p class="col-xs-8">身份证号码：{{card_num}}</p>
	  </div>
	  <div class="form-group col-xs-12">
	    <label class="text-right" for="bank">银行名称：</label>
	    <div class="inline-block">
		    <select id="bank-select" class="base-size" name="bank" required>
		    	<option value="">请选择</option>

		    	{{each bankList}}
		    		<option value="{{$value.bank}}" {{if $value.bank == bank}}selected{{/if}}>{{$value.bank}}</option>
		    	{{/each}}
		    </select>
	    </div>
	  </div>
	  <div class="form-group col-xs-12" >
	    <label class="text-right" for="branch">支行名称：</label>
	    <div class="inline-block">
	    	<input type="text" class="form-control" required id="branch" name="branch" placeholder="" value="{{branch}}">
	    </div>
	    
	  </div>
	  <div class="form-group col-xs-12">
	    <label class="text-right" for="branch">银行卡号：</label>
	    <div class="inline-block">
	   		<input type="text" class="form-control" required id="account" name="account" placeholder="" value="{{account}}">
	   	</div>
	  </div>
	  <div class="form-group col-xs-12">
	    <label class="text-right" class="text-right" for="date">工资年月：</label>
	    <div class="inline-block">
	    	<input type="text" class="form-control" required id="date" name="date" placeholder="如：201511" value="{{date}}">
	    </div>
	     如：201511
	  </div>
	 	<div class="clearfix">
	 		<div class="form-group col-xs-6">
			    <label class="text-right" for="wages">应发工资：</label>
			    <div class="inline-block">
			    	<input type="text" class="form-control form-control-small J-counter J-counter-add" required id="wages" name="wages" placeholder="" value="{{wages}}">
			    </div>
				 元
			  </div>
			  <div class="form-group col-xs-6">
			    <label class="text-right" for="deduction_income_tax">扣个人所得税：</label>
			    <div class="inline-block">
			    	<input type="text" class="form-control form-control-small J-counter J-counter-cut" required id="deduction_income_tax" name="deduction_income_tax" placeholder="" value="{{deduction_income_tax}}">
			    </div>
			     元
			</div>
	 	</div>
	  	<div class="clearfix">
		 	<div class="form-group col-xs-6">
		 	    <label class="text-right" for="deduction_social_insurance">扣个人社保：</label>
		 	    <div class="inline-block">
		 	  		<input type="text" class="form-control form-control-small J-counter J-counter-cut" required id="deduction_social_insurance" name="deduction_social_insurance" placeholder="" value="{{deduction_social_insurance}}">
		 	    </div>
			 	元
		 	</div>
		 	<div class="form-group col-xs-6">
		 	    <label class="text-right" for="deduction_provident_fund">扣个人公积金：</label>
		 	    <div class="inline-block">
		 	  		<input type="text" class="form-control form-control-small J-counter J-counter-cut" required id="deduction_provident_fund" name="deduction_provident_fund" placeholder="" value="{{deduction_provident_fund}}">
		 	  	</div>
		 	   元
		 	</div>
		</div>
		<div class="clearfix">
		  <div class="form-group col-xs-6">
		    <label class="text-right" for="replacement">补发：</label>
		    <div class="inline-block">
		    	<input type="text" class="form-control form-control-small" required id="replacement" name="replacement" placeholder="" value="{{replacement}}">
		    </div>
		     元
		  </div>
		  <div class="form-group col-xs-6">
		    <label class="text-right" for="deduction_other">其他扣除：</label>
		    <div class="inline-block">
		    	<input type="text" class="form-control form-control-small" required id="deduction_other" name="deduction_other" placeholder="" value="{{deduction_other}}">
		    </div>
			 元
	  	   </div>
	  	</div>
	  <p class="text-center wages-tip divider-bottom col-xs-12">应发工资：<span id="wages-txt" class="c-money">{{actual_wages}}</span></p>
		<div  class="clearfix error-no-margin">
		  <div class="form-group col-xs-5 state-box">
		  	{{if state == 1 || state == -2}}
		  		<div class="inline-block">
					 <label class="vertical-top">
					    <input class="icheck" type="radio" required  name="state" value="2" placeholder="">
						 发放成功
					</label>
				</div>
				<label class="vertical-top">
				    <input class="icheck" type="radio" required  name="state"  value="-2">
					 发放失败
				</label>

			{{else}}
				<div class="inline-block">
					<label class="vertical-top">
					    <input class="icheck" type="radio" required  name="state" value="1" placeholder="">
						 审核通过
					</label>
				</div>
				<label class="vertical-top">
				    <input class="icheck" type="radio" required  name="state"  value="-1">
					 审核失败
				</label>
				
			{{/if}}
		  </div>
		  <div class="form-group col-xs-7">
		  	<input type="text" class="form-control" id="remark1" name="remark" placeholder="备注" value="">
		  </div>

		  <input type="hidden" name="base_id" value="{{base_id}}">
		  <input type="hidden" name="service_order_salary_id" value="{{service_order_salary_id}}">
		</div>

<!-- 		  <div class="text-center btn-footer col-xs-12">
	<input class="btn btn-primary" type="submit" value="确定">
	<input class="btn btn-gray layui-layer-close" type="button" value="取消">
</div> -->
		
	</form>
</template>