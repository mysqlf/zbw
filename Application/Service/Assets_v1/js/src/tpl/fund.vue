<template>	
	<div class="audit-form-title clearfix2">
		参保信息 <i class="icon icon-info"></i>
		</div>
		<div class="clearfix2">
			<input id="security-checkbox" type="checkbox" name="buy"> 购买社保
		</div>
		{{each sb_classify}}
		<div class="form-group col-xs-12">
            <label>
            <span class="c-required">*</span>{{$value.name}}：
            </label>
           	<select class="base-size security-change" name="sb_name[]" required>
           		{{each $value.child}}
           			<option value="{{$value.id}}">{{$value.name}}</option>
           		{{/each}}
           	</select>
        </div>
        {{/each}}

        <div class="form-group col-xs-12">
            <label>
            <span class="c-required">*</span>社保基数：</label>
           	<input type="text" class="form-control form-control-middle security-change" required  name="pro_cost" placeholder="请输入社保基数" value="{{sb_rule.pro_cost}}">
           	<span class="gutter">
           		基数范围{{sb_rule.min}}到{{sb_rule.max}}
           	</span>
        </div>

        <div class="form-group col-xs-12">
            <label>
            	<span class="c-required">*</span>起缴时间：
            </label>
   			<div class="inline-block">
	           	<select class="base-size security-change" required name="year">
	           		<option value="">请选择</option>
	           		<option value="1">1</option>
	           	</select>
	           	年
			</div>
			<div class="inline-block gutter">
				<select class="base-size security-change" required name="month">
	           		<option value="">请选择</option>
	           		<option value="1">1</option>
	           	</select>
	           	月
			</div>
           	
        </div>

        <div class="form-group col-xs-12 table-responsive">
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
		                <td>
		                    {{$value}}
		                </td>
		                <td>
		                	{{sb_rule.rules[$index].company}}
		                </td>
		                <td class="sum-company">/</td>
		                <td>
		                	{{sb_rule.rules[$index].person}}
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
		                <td>
		                	/
		                </td>
		                <td class="total-sum-company">/</td>
		                <td>
		                	/
		                </td>
		                <td class="total-sum-person">/</td>

		                <td id="total">
		                	/
		                </td>
		            </tr>
		        </tbody>
		    </table>
		</div>

</template>