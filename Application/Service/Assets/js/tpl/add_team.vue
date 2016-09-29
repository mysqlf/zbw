<!-- 新增账号 -->
<template>
    <div class="layer-wra add-team">
        <form method="post" id="addTeam_form" autocomplete="off">
            <div class="form-items">
                <label for="group">角色</label>
                <div class="ipt-box">
                    <select name="group" required>
                        <option value="">请选择</option>
                        <option value="2" {{if group == '2'}}selected{{/if}}>财务</option>
                        <option value="3" {{if group == '3'}}selected{{/if}}>客服</option>
                    </select>
                </div>
            </div>
            <div class="form-items">
                <label for="password">密码</label>
                <div class="ipt-box">
                    <input type="password" name="password" class="ipt w-normal" placeholder="6-20字符,包含字母、数字或符号" />
                </div>
            </div>
            <div class="form-items">
                <label for="username">账号</label>
                <div class="ipt-box">
                    <input type="text" name="username" class="ipt w-normal ellipsis" value="{{username}}" {{if username}}readonly{{/if}} />
                </div>
            </div>
            <div class="form-items">
                <label for="telphone">电话</label>
                <div class="ipt-box">
                    <input type="text" name="telphone" class="ipt w-normal ellipsis" value="{{telphone}}" required />
                </div>
            </div>
            <div class="form-items">
                <label for="name">姓名</label>
                <div class="ipt-box">
                    <input type="text" name="name" class="ipt w-normal ellipsis" value="{{name}}" required />
                </div>
            </div>
            <div class="form-items">
                <label for="qq">QQ</label>
                <div class="ipt-box">
                    <input type="text" name="qq" class="ipt w-normal ellipsis" value="{{qq}}" required />
                </div>
            </div>
            <div class="form-items">
                <label class="status" for="state">账号状态</label>
                <div class="ipt-box">
                    <label><input class="icheck" type="checkbox" name="state" value="1" {{if state == 1}}checked{{/if}} />启用</label>
                    <!-- <label><input class="icheck" type="radio" name="state" value="1" {{if state == 1}}checked{{/if}} required />启用</label>
                    <label><input class="icheck" type="radio" name="state" value="0" {{if state == 0}}checked{{/if}} required />停用</label> -->
                </div>
            </div>
            <input type="hidden" name="auth[]" value="User" />
            <input type="hidden" name="auth[]" value="Service" />
            {{if typeof id !== 'undefined'}}
            <input type="hidden" name="id" value="{{id}}" />
            {{/if}}
        </form>
    </div>
</template>

