<template>
	<form id="assign-form" class="assign-form" method="post">

		<div class="form-group text-center">
	        <label class="label-left vertical-top">
	        <span class="c-required">*</span>分配给客服：
	        </label>
	        <div class="inline-block">
	       	<select class="base-size" name="admin_id" required>
	       		<option value="">请选择</option>
	       		{{each data}}
					<option value="{{$value.id}}">{{$value.name}}</option>
				{{/each}}
	       	</select>
	       	</div>
	    </div>

	    <input type="hidden" name="id" value="{{id}}">
    </form>
</template>