<template>
<form class="reduceTip fromTable" id="reduceTip">
    <input type="hidden" name="baseId" value="{{baseId}}">
    {{if status == 1 || status == 2}}
    <div class="checkbox square">
        <input type="checkbox" name="socIsCancel" value="1">报减社保
    </div>
    <table>
        <tr>
            <td class="name">停保原因：</td>
            <td><input class="zb_inpt" name="socNote" required disabled></td>
        </tr>
    </table>
    {{/if}}
    {{if status == 1 || status == 3}}
    <div class="checkbox square">
        <input type="checkbox" name="proIsCancel" value="1">报减公积金
    </div>
    <table>
        <tr>
            <td class="name">停保原因：</td>
            <td><input class="zb_inpt" name="proNote" required disabled></td>
        </tr>
    </table>
    {{/if}}
    <div class="btnCont">
        <a href="#" class="btnB">取消</a>
        <a href="#" class="btnO">提交减员</a>
    </div>
</form>
</template>