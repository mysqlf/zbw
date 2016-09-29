<template>
<!-- 	<table>
    <tr>
        <td align="right">购买时间：</td>
        <td>2015/11/21 12:12</td>
    </tr>
    <tr>
        <td align="right">企业：</td>
        <td>智通人才</td>
    </tr>
    <tr>
        <td align="right">产品名称：</td>
        <td>智尊宝会员年费</td>
    </tr>
    <tr>
        <td align="right">金额：</td>
        <td>1600元</td>
    </tr>
    <tr>
        <td align="right">优惠价：</td>
        <td>
            <input type="text">元</td>
    </tr>
</table> -->
	<form id="modifyPrice-form" method="post">
		<div class="form-group pad-0">
	        <label class="label-left vertical-top text-right w-7em">
	        购买时间：
	        </label>
	        <div class="inline-block">
	       	{{time}}
	       	</div>
	    </div>
	    <div class="form-group pad-0">
	        <label class="label-left vertical-top text-right w-7em">
	        企业：
	        </label>
	        <div class="inline-block">
	       	{{product_name}}
	       	</div>
	    </div>
	    <div class="form-group pad-0">
	        <label class="label-left vertical-top text-right w-7em">
	        产品名称：
	        </label>
	        <div class="inline-block">
	       	{{product_name}}
	       	</div>
	    </div>
	    <div class="form-group pad-0">
	        <label class="label-left vertical-top text-right w-7em">
	        金额：
	        </label>
	        <div class="inline-block">
	       	<span class="c-money f-bold">{{price}}元</span>
	       	</div>
	    </div>

	    <div class="form-group pad-0 ">
	        <label class="label-left vertical-top text-right w-7em">
	        优惠价：
	        </label>
	        <div class="inline-block">
	       		<input class="form-control" name="modify_price" type="text" required>
	       	</div>
	       	元
	    </div>

	    <input type="hidden" name="id" value="{{id}}">

	</form>
			
</template>