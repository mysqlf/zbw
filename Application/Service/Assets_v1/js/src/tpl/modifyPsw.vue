<template>	
	<form id="modifyPsw-form" action="" method="post" class="modifyPsw-from clearfix">
		<div class="form-group col-xs-12">
            <label class="label-left vertical-top w-7em text-right">
                原密码：
            </label>
            <div class="inline-block">
                <input type="password" class="form-control" required name="lastPassword" placeholder="" minlength="6" maxlength="20"> 
            </div>
        </div>
        <div class="form-group col-xs-12">
            <label class="label-left vertical-top w-7em text-right">
                新密码：
            </label>
            <div  class="inline-block">
                <input id="password" type="password" required class="form-control"  name="password" placeholder="" minlength="6" maxlength="20"> 
            </div>
        </div>
        <div class="form-group col-xs-12">
            <label class="label-left vertical-top w-7em text-right">
                密码确认：
            </label>
            <div class="inline-block">
                <input  type="password" class="form-control" required name="comfirmPassword" placeholder="" > 
            </div>
        </div>
	</form>
</template>