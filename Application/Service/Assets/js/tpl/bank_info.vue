<!-- 银行账号信息 -->
<template>
	{{if type===1}}
		<form id="bankInfo_form" method="post">
			<div class="card-info card-info-ipt">
				<div class="form-items">
		            <label for="account_name">开户名</label>
		            <div class="ipt-box">
		            	<input type="text" name="account_name" class="ipt w-large ellipsis" value="{{account_name}}" required />
		            </div>
		        </div>
		        <div class="form-items">
		            <label for="account">账户</label>
		            <div class="ipt-box">
		            	<input type="text" name="account" id="account" class="ipt w-large ellipsis" value="{{account}}" required />
		            </div>
		        </div>
		        <div class="form-items">
		            <label for="bank">银行名称</label>
		            <div class="ipt-box box-large">
		            	<select name="bank" id="bank_select" class="base-size" required>
		            		<option value="">请选择</option>
		            		{{each bankList}}
								<option value="{{$value.bank}}" {{if $value.bank == bank}} selected {{/if}}>{{$value.bank}}</option>
		            		{{/each}}
		            	</select>
		            </div>
		        </div>
		        <div class="form-items">
		            <label for="bank">支行名称</label>
		            <div class="ipt-box">
		            	<input type="text" name="branch" class="ipt w-large ellipsis" value="{{branch}}" required />
		            </div>
		        </div>
		        <div class="form-items form-items-buttons">
		        	<button type="submit" class="btn-save">保存</button>
					<button type="button" class="btn-cancle" id="J_cancle">取消</button>
		        </div>
	        </div>
			<input type="hidden" name="id" value="{{id}}">
		</form>
	{{else if type===2}}
		<div class="card-info">
			<div class="form-items">
	            <label for="account_name">开户名</label>
	            <div class="ipt-box">{{account_name}}</div>
	        </div>
	        <div class="form-items">
	            <label for="account">账户</label>
	            <div class="ipt-box">{{account}}</div>
	        </div>
	        <div class="form-items">
	            <label for="bank">银行名称</label>
	            <div class="ipt-box">{{bank}}</div>
	        </div>
	        <div class="form-items">
	            <label for="bank">支行名称</label>
	            <div class="ipt-box">{{branch}}</div>
	        </div>
	        <button type="button" class="btn-modify" data-act="modify_bankInfo">修改</button>
        </div>
    {{else}}
    	<div class="noCard">
    		<i class="icon icon-promt"></i> 暂未设置账号，<a href="javascript:;" data-act="modify_bankInfo">立即设置</a>
    	</div>
	{{/if}}
</template>