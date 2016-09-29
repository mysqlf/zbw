<template>	
	<div id="security-info" class="audit-form-title clearfix2 ">
	参保信息 <i class="icon icon-info"></i>
	</div>
	<div class="form-box" data-type="1">
		<div class="clearfix2 form-group ">
			<label>
			<input id="security-checkbox" class="buy-server icheck" type="checkbox" name="sb_buy" value="1"> 购买社保
			</label>
		</div>
		{{each sb_classify.classify}}
		<div class="form-group col-xs-12">
	        <label class="label-left vertical-top">
	        {{$value.name}}：
	        </label>
	        <div class="inline-block">
	       	<select class="base-size security-change" name="sb_name[]" >
	       		<option value="">请选择</option>
	       		{{each $value.child}}
	       			<option value="{{$value.id}}">{{$value.name}}</option>
	       		{{/each}}
	       	</select>
	       	</div>
	    </div>
	    {{/each}}

	    <div class="form-group col-xs-12">
	        <label class="label-left vertical-top">
	        	<span class="c-required">*</span>社保基数：
	        </label>
	        <div class="inline-block">
	       		<input type="text" class="form-control form-control-middle security-change" required id="sb_amount" name="pro_cost" placeholder="请输入社保基数" value="{{sb_rule.min}}">
	       	</div>
	       	<span class="gutter vertical-top">
	       		基数范围{{sb_rule.min}}到{{sb_rule.max}}
	       	</span>
	    </div>

	    <div class="form-group col-xs-12">
	        <label class="label-left vertical-top">
	        	<span class="c-required">*</span>起缴时间：
	        </label>
				<div class="inline-block vertical-top">

	           	<select id="sb_year" class="base-size" required name="sb_year">
	           		<option value="{{sb_year}}">{{sb_year}}</option>
	           	</select>
	           	年
			</div>
			<div class="inline-block gutter-x vertical-top">
				<select id="sb_month"  class="base-size" required name="sb_month">
	           		<option value="{{sb_month}}">{{sb_month}}</option>
	           	</select>
	           	月
			</div>
	       	
	    </div>

	    <div class="form-group col-xs-12 table-responsive" id="sb_table">
		    <table class="table table-bordered table-border text-center" style="width: 674px;">
		    	<thead>
		    		<tr>
		                <th rowspan="2" width="16%">
		                    险种
		                </th>
		                <th colspan="2">
		                	单位缴纳
		                </th>
		                <th colspan="2">
		                	个人缴纳
		                </th>
		                <th rowspan="2">合计金额</th>

		            </tr>
		            <tr>
		                <th>比例</th>
		                <th>
		 					金额
		                </td>
		                <th>比例</th>
		                <th>
		 					金额
		                </th>
		            </tr>
		    	</thead>
		        <tbody>

		            {{each sb_rule.items}}
		            <tr>
		                <td >
		                    {{$value.name}}
		                </td>
		                <td class="sum-company-scale">
		                	{{$value.rules.company}}

		                </td>
		                <td class="sum-company">/</td>
		                <td class="sum-person-scale">
		                	{{$value.rules.person}}
		                </td>
		                <td class="sum-person">/</td>
		                <td class="sum">
		                	/
		                </td>
		            </tr>
		        	{{/each}}
		        	<tr>
		                <td>
		                   合计
		                </td>
		                <td id="total-sum-company-scale">
		                	/
		                </td>
		                <td id="total-sum-company">/</td>
		                <td id="total-sum-person-scale">
		                	/
		                </td>
		                <td id="total-sum-person">/</td>

		                <td id="total">
		                	/
		                </td>
		            </tr>
		        </tbody>
		    </table>
		</div>
		
		<div class="form-group col-xs-12">
			<label class="label-left vertical-top">
			<input id="has-ss-card" class="ss-card icheck" type="radio" name="ss_card" value="1" required checked>
			有社保卡
			</label>
			<label class="gutter-left">卡号：</label>
			<div class="inline-block">
				<input id="ss_card_number" type="text" required class="form-control form-control-middle"  name="ss_card_number" placeholder="请输入卡号" value="">
			</div>
		</div>
		<div class="form-group col-xs-12">
			<label class="label-left">
			<input id="no-ss-card" type="radio" class="ss-card icheck" name="ss_card" value="2" required>
			无社保卡
			</label>
			<label class="gutter-x">工本费：<span id="sb-pro_cost">{{sb_rule.pro_cost}}</span></label>

			<a class="material c-primary" href="javascript:;" data-material="{{sb_rule.material}}">所需材料</a>
		</div>

		<div class="text-right clearfix2 c-money sum-total">小计： <span id="sb-total" class="f-bold">0元/月</span></div>

		 <div class="panel-audit clearfix2">
	    	<div class="title">
	    		{{if sb_updata_type_new == 2}}
	    		社保报减审核
	    		{{else}}
	    		社保报增审核
	    		{{/if}}
	    	</div>
	    	<div class="form-group col-xs-12">
	    		<label>
	    			<input type="radio" name="sb_state" value="0" class="sb_state states icheck" required>
	    			审核中
	    		</label>
	    	</div>
	    	<div class="form-group col-xs-12">
	    		<label>
	    			<input  type="radio" name="sb_state" value="1" class="sb_state states states-success icheck" required>
	    			审核通过

	    		</label>
	    	</div>
	    	<div class="form-group col-xs-12">
	    		<label class="guadan">
	    			<input class="icheck" type="checkbox" value="-1" name="sb_state_guadan" >
	    			挂起至下一订单
	    		</label>
	    	</div>
	    	<div class="form-group col-xs-12">
	    		<label>
	    			<input type="radio" name="sb_state" value="-1" class="sb_state states icheck" required>
	    			审核失败
	    		</label>
	    	</div>
	    	<div class="form-group col-xs-12">
	    		<label>
	    			备注
	    			<input class="input-remark gutter-left form-control" type="text" name="sb_remark" placeholder="反馈">
	    		</label>
	    	</div>
	    </div>
	</div>
	<div class="form-box" data-type="2" style="margin-top:80px;">
		<div class="clearfix2 form-group ">
			<label>
			<input class="buy-server icheck" id="fund-checkbox" type="checkbox" name="gzj_buy" value="1"> 购买公积金
			</label>
		</div>
		{{each gzj_classify.classify}}
		<div class="form-group col-xs-12">
	        <label class="label-left vertical-top">
	        {{$value.name}}：
	        </label>
	        <div class="inline-block">
	       	<select class="base-size gzj-change" name="gzj_name[]">
	       		<option value="">请选择</option>
	       		{{each $value.child}}
	       			<option value="{{$value.id}}">{{$value.name}}</option>
	       		{{/each}}
	       	</select>
	       	</div>
	    </div>
	    {{/each}}
		<div class="form-group col-xs-12">
	        <label>
	        	<span class="c-required">*</span>公积金基数：
	        </label>
	        <div class="inline-block">
	       		<input type="text" class="form-control form-control-middle gzj-change" required id="gzj_amount"  name="gzj_pro_cost" placeholder="请输入公积金基数" value="{{gzj_rule.min}}">
	       	</div>
	       	<span class="gutter">
	       		基数范围{{gzj_rule.min}}到{{gzj_rule.max}}
	       	</span>
	    </div>

	    <div class="form-group col-xs-12">
	        <label class="label-left">
	        	<span class="c-required">*</span>起缴时间：
	        </label>
				<div class="inline-block">
	           	<select id="gzj_year" class="base-size" required name="gzj_year">
	           		<option value="{{gzj_year}}">{{gzj_year}}</option>

	           	</select>
	           	年
			</div>
			<div class="inline-block gutter-x">
				<select id="gzj_month" class="base-size" required name="gzj_month">
	           		<option value="{{gzj_month}}">{{gzj_month}}</option>
	           	</select>
	           	月
			</div>
	    </div>
		 <div class="form-group col-xs-12">
	        <label>
	        	<span class="c-required">*</span>单位缴纳比例：
	        </label>
	        <div class="inline-block">
	       		<input type="text" class="form-control gzj-change" required id="firme"  name="gzj_pro_cost_com" placeholder="请输入单位缴纳比例" value=""> %
	       	</div>
	       	<span class="gutter vertical-top">
	       		比例为<span id="comp-scale">{{gzj_rule.company}}</span>
	       	</span>
	    </div>
	    <div class="form-group col-xs-12">
	        <label>
	        	<span class="c-required">*</span>个人缴纳比例：
	        </label>
	        <div class="inline-block">
	       		<input type="text" class="form-control gzj-change" required id="member"  name="gzj_pro_cost_per" placeholder="请输入个人缴纳比例" value=""> %
	       	</div>
	       	<span class="gutter vertical-top">
	       		比例为<span id="per-scale">{{gzj_rule.person}}</span>
	       	</span>
	    </div>
	    <div class="form-group col-xs-12">
			<label class="vertical-top">
			<input id="has-gzj-card" class="gzj-card icheck" type="radio" name="gzj_card" value="1" required checked>
			有公积金卡
			</label>
			<label class="gutter-left">卡号：</label>
			<div class="inline-block">
				<input id="gzj_card_number" type="text" required class="form-control form-control-middle"  name="gzj_card_number" placeholder="请输入卡号" value="">
			</div>
		</div>
		<div class="form-group col-xs-12">
			<label class="vertical-top">
			<input id="no-gzj-card" type="radio" class="gzj-card icheck" name="gzj_card" value="2" required>
			无公积金卡
			</label>
			<label class="gutter-x">工本费：<span id="gzj-pro_cost">{{gzj_rule.pro_cost}}</span></label>
		</div>
	    <div  class="text-right clearfix2 c-money sum-total">
	    	小计： <span id="gzj-total" class="f-bold">0元/月</span>
	    </div>
	    <div class="panel-audit clearfix2">
	    	<div class="title">
	    	{{if sb_updata_type_new == 2}}
	    		公积金报减审核 
    		{{else}}
    			公积金报增审核 
    		{{/if}}
    		</div>
	    	<div class="form-group col-xs-12">
	    		<label>
	    			<input type="radio" name="gzj_state" value="0" class="gzj_state states icheck" required>
	    			审核中
	    		</label>
	    	</div>
	    	<div class="form-group col-xs-12">
	    		<label>
	    			<input  type="radio" name="gzj_state" value="1" class="gzj_state states states-success icheck">
	    			审核通过

	    		</label>
	    	</div>
	    	<div class="form-group col-xs-12">
	    		<label class="guadan">
	    			<input class="icheck" type="checkbox" value="-1" name="gzj_state_guadan" >
	    			挂起至下一订单
	    		</label>
	    	</div>
	    	<div class="form-group col-xs-12">
	    		<label>
	    			<input type="radio" name="gzj_state" value="-1" class="gzj_state states icheck" >
	    			审核失败
	    		</label>
	    	</div>
	    	<div class="form-group col-xs-12">
	    		<label>
	    			
	    			备注
	    			<input class="input-remark gutter-left form-control" type="text" name="gzj_remark" placeholder="反馈">
	    		</label>
	    	</div>
	    </div>
	    <div class="text-right clearfix2 c-money sum-total czj-total">
	    	残障金： <span id="czj-total" class="f-bold">0元/月</span>


	    </div>
	    <div class="text-right clearfix2 c-money sum-total">
	    	<span class="all-total">
	    		总计： <strong id="all-total" class="f-bold">0元/月</strong>
	    	</span>
	    </div>
	    	
	   
	</div>
</template>