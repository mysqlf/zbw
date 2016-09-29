<template>
    <form id="addTeam-form" action="" method="post" class="addTeam-form gutter-y clearfix" style="width:620px;">
        <div class="form-group col-xs-12">
            <label class="label-left vertical-top w-5em text-right">
                角色：
            </label>
            <div class="inline-block">
                <select class="base-size" name="group" required style="width: 210px;">
                    <option value="">请选择</option>
                    <option value="2" {{if group == '2'}}selected{{/if}}>财务</option>
                    <option value="3" {{if group == '3'}}selected{{/if}}>客服</option>
                </select>
            </div>
        </div>
        <div class="form-group col-xs-6" style='overflow: hidden;'>
            <label class="label-left vertical-top w-5em text-right">
                账号：
            </label>
            <div class="inline-block">
                <input type="text" class="form-control" minlength="4" maxlength="20" {{if username}}readonly{{/if}} required name="username" value="{{username}}"> 
            </div>
        </div>
        <div class="form-group col-xs-6">
            <label class="label-left vertical-top w-5em text-right">
                密码：
            </label>
            <div class="inline-block">
                {{if username}}
                <input type="password" class="form-control" name="password" placeholder="6-20字符,包含字母,数字或符号">
                {{else}}
                <input type="password" class="form-control" required name="password" placeholder="6-20字符,包含字母,数字或符号"> 
                {{/if}}
            </div>
        </div>
        <div class="form-group col-xs-6">
            <label class="label-left vertical-top w-5em text-right">
                姓名：
            </label>
            <div class="inline-block">
                <input type="text" class="form-control" required name="name" value="{{name}}"> 
            </div>
        </div>
        <div class="form-group col-xs-6">
            <label class="label-left vertical-top w-5em text-right">
                电话：
            </label>
            <div class="inline-block">
                <input type="text" class="form-control" required name="telphone" value="{{telphone}}"> 
            </div>
        </div>
        <div class="form-group col-xs-12">
            <label class="label-left vertical-top w-5em text-right">
                qq：
            </label>
            <div class="inline-block">
                <input type="text" class="form-control" required name="qq" value="{{qq}}"> 
            </div>
        </div>
        <div class="form-group col-xs-12">
            <label class="label-left vertical-top w-7em text-right" style='float:left'>
                账号状态：
            </label>
            <div class="inline-block vertical-top gutter-x col-xs-4">
                <input class="icheck" type="radio" name="state" {{if state == 1}}checked{{/if}} value="1" required >
                <label class=" vertical-top inline-block gutter-right">
                     启用
                </label>
                <input class="icheck" type="radio" name="state" value="0" {{if state == 0}}checked{{/if}} required>
                <label class="vertical-top vertical-top inline-block">
                     停用
                </label>
            </div>
        </div>

        <div class="form-group col-xs-12 user-auth">
            <label class="label-left vertical-top w-7em text-right c-dark">
                订单管理：
            </label>
            <label class="inline-block gutter-x vertical-top">
                <input type="checkbox" class="icheck" required name="auth[]" {{Order}} value="Order"> 
                报增减列表
            </label>
        </div>
        <div class="form-group col-xs-12 user-auth">
            <label class="label-left vertical-top w-7em text-right c-dark">
                账单管理：
            </label>
            <label class="inline-block gutter-x vertical-top">
                <input type="checkbox" class="icheck" required name="auth[]" {{Bill}}  value="Bill"> 
                企业账单
            </label>
        </div>
        <div class="form-group col-xs-12 user-auth">
            <label class="label-left vertical-top w-7em text-right c-dark">
                客户管理：
            </label>
            <label class="inline-block gutter-x vertical-top">
                <input type="checkbox" class="icheck" required name="auth[]" {{Members}} value="Members"> 
                企业产品订单
            </label>
        </div>
        <div class="form-group col-xs-12 user-auth">
            <label class="label-left vertical-top w-7em text-right c-dark">
                产品管理：
            </label>
            <label class="inline-block gutter-x vertical-top">
                <input type="checkbox" class="icheck" required name="auth[]" {{Product}} value="Product"> 
                产品管理
            </label>
        </div>
        
        <input type="hidden" name="auth[]" value="User">
        <input type="hidden" name="auth[]" value="Service">
        {{if typeof id !== 'undefined'}}
        <input type="hidden" name="id" value="{{id}}">
        {{/if}}
    </form>
            
</template>

