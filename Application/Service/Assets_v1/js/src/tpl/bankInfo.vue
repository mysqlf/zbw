<template>
	{{if type===1}}
		<form id="bankInfo-form" action="">
			<div class="cardInfo clearfix2">
				<div class="form-group col-xs-12 ">
		            <label class="label-left vertical-top w-5em text-right">
		            	开户名：
		            </label>
		            <div class="inline-block">
		            	<input type="text" class="form-control form-control-middle" required name="account_name" placeholder="" value="{{account_name}}">
		            </div>
		        </div>
		        <div class="form-group col-xs-12">
		            <label class="label-left vertical-top w-5em text-right">
		            	账号：
		            </label>
		            <div class="inline-block">
		            	<input id="account" type="text" class="form-control form-control-middle" required name="account" placeholder="" value="{{account}}">
		            </div>
		        </div>
		        <div class="form-group col-xs-12">
		            <label class="label-left vertical-top w-5em text-right">
		            	银行名称：
		            </label>
		            <div class="inline-block">
		            	<select id="bank-select" class="base-size" name="bank" required placeholder="">
		            		<option value="">请选择</option>
		            		{{each bankList}}
								<option value="{{$value.bank}}" {{if $value.bank == bank}} selected {{/if}}>{{$value.bank}}</option>
		            		{{/each}}
		            	</select>
		            </div>
		        </div>
		        <div class="form-group col-xs-12">
		            <label class="label-left vertical-top w-5em text-right">
		            	支行名称：
		            </label>
		            <div class="inline-block">
		            	<input type="text" class="form-control form-control-middle" required name="branch" placeholder="" value="{{branch}}">
		            </div>
		        </div>
		        <div class="btn-footer text-center gutter-y clearfix2">
		        	<button type="submit" class="btn btn-lg btn-primary gutter-x">保 存</button>
					<button id="J-cancle" type="button" class="btn btn-lg gutter-x">取 消</button>
		        </div>
	        </div>
			<input type="hidden" name="id" value="{{id}}">
		</form>
	{{else if type===2}}

		<a href="javascript:;" data-act="modifyBankInfo" class="btn-border-success pull-right"><i class="icon icon-setBankCard"></i>修改</a>
		<div class="cardInfo clearfix2">
				<div>
		            <label class="label-left  w-5em text-right">
		            	开户名：
		            </label>
		            <div class="inline-block">
		            	{{account_name}}
		            </div>
		        </div>
		        <div>
		            <label class="label-left w-5em text-right">
		            	账号：
		            </label>
		            <div class="inline-block">
		            	{{account}}
		            </div>
		        </div>
		        <div>
		            <label class="label-left w-5em text-right">
		            	银行名称：
		            </label>
		            <div class="inline-block">
		            	{{bank}}
		            </div>
		        </div>
		        <div>
		            <label class="label-left w-5em text-right">
		            	支行名称：
		            </label>
		            <div class="inline-block">
		            	{{branch}}
		            </div>
		        </div>
	        </div>
    {{else}}

    	<a href="javascript:;" data-act="modifyBankInfo" class="btn-border-success pull-right"><i class="icon icon-setBankCard"></i>设置账号</a>
    	<div class="noCard clearfix2">
    		<i class="icon icon-promt"></i> 暂未设置账号，立即设置
    	</div>
	{{/if}}
</template>