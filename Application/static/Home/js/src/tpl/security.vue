<template>
	<div class="form-box" data-type="1">
        <div class="square">
            <label><input type="checkbox" id="security-checkbox" class="buy-server" name="sb_buy"/>购买社保</label>
        </div>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="formTable">
            {{each sb_classify.classify}}
                <tr>
                    <td class="name">{{$value.name}}：</td>
                    <td>
                        <select id="" name="sb_name[]" class="zb_inpt security-change">
                            <option value="">请选择</option>
                            {{each $value.child}}
                                <option value="{{$value.id}}">{{$value.name}}</option>
                            {{/each}}
                        </select>
                    </td>
                </tr>
            {{/each}}
            <tr>
                <td class="name">社保基数：</td>
                <td class="license">
                    <input class="zb_inpt security-change" type="text" required  name="pro_cost" id="sb_amount" placeholder="请输入社保基数" value="{{sb_rule.min}}"/>
                    <span class="cardinal">基数范围{{sb_rule.min}}到{{sb_rule.max}}</span>
                </td>
            </tr>
            <tr id="sb_date">
                <td class="name">起缴时间：</td>
                <td class="start">
                    <select name="sb_year" id="sb_year" class="zb_inpt security-change">
                        <option value="">请选择</option>
                        {{each years}}
                            <option value="{{$value}}">{{$value}}</option>
                        {{/each}}
                    </select>
                    年
                    <select name="sb_month" id="sb_month" class="zb_inpt security-change" style="margin-left: 20px;">
                        <option value="">请选择</option>
                    </select>
                    月
                </td>
            </tr>
        </table>
        <div id="sb_table" class="zbhide">
            <table class="pageTable zbhide" id="sb_table" width="676">
                <tr>
                    <td rowspan="2" class="c-1">险种 </td>
                    <th colspan="2">单位缴纳</th>
                    <th colspan="2">个人缴纳</th>
                    <td rowspan="2" class="c-6">合计金额</td>
                </tr>
                <tr>
                    <td class="c-2">比例</td>
                    <td class="c-3">金额</td>
                    <td class="c-4">比例</td>
                    <td class="c-5">金额</td>
                </tr>
                {{each sb_rule.items}}
                    <tr>
                        <td>
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
                        <td class="sum orange">/</td>
                    </tr>
                {{/each}}
                <tr>
                    <td>合计</td>
                    <td id="total-sum-company-scale">/</td>
                    <td id="total-sum-company">/</td>
                    <td id="total-sum-person-scale">/</td>
                    <td id="total-sum-person">/</td>
                    <td class="orange" id="total">/</td>
                </tr>
            </table>
        </div>
        <div class="circle">
            <label>
                <div class="fl"><input type="radio" class="ss-card" name="ss-card" id="has-ss-card" value="1" required checked />有社保卡</div>
                <div class="fl c1">卡号：</div>
                <div class="fl"><input type="text" id="ss_card_number" class="zb_inpt" name="ss_card_number" placeholder="请输入卡号" value="" /></div>
            </label>
            <label>
                <div class="fl"><input type="radio" class="ss-card" name="ss-card" id="no-ss-card" value="2" required />无社保卡</div>
                <div class="fl c1">工本费：<span id="sb-pro_cost">{{sb_rule.pro_cost}}</span>元<a class="material" href="javascript:;" data-material="{{sb_rule.material}}">所需材料</a></div> 
            </label>
        </div>
        <div class="subtotal">小计： <strong id="sb-total">0元/月</strong></div>
    </div>
    <div class="form-box" data-type="2">
        <div class="square">
            <label><input type="checkbox" id="gzj-checkbox" class="buy-server" name="gzj_buy"/>购买公积金</label>
        </div>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="formTable CPF">
            {{each gzj_classify.classify}}
                <tr>
                    <td class="name">{{$value.name}}：</td>
                    <td>
                        <select id="" name="gzj_name[]" class="zb_inpt gzj-change">
                            <option value="">请选择</option>
                            {{each $value.child}}
                                <option value="{{$value.id}}">{{$value.name}}</option>
                            {{/each}}
                        </select>
                    </td>
                </tr>
            {{/each}}

            <tr>
                <td class="name">公积金基数：</td>
                <td>
                    <input class="zb_inpt gzj-change" type="text"  required  name="gzj_pro_cost" id="gzj_amount" placeholder="请输入公积金基数" value="{{gzj_rule.min}}"/><span class="cardinal">基数范围{{gzj_rule.min}}到{{gzj_rule.max}}</span>
                </td>
            </tr>
            <tr>
                <td class="name">单位缴纳比例：</td>
                <td class="scale">
                    <input class="zb_inpt gzj-change" type="text" required  name="gzj_pro_cost_com" id="firme" placeholder="请输入单位缴纳比例" value="{{gzj_rule.companyMin}}"/>%
                    <span class="cardinal" id="comp-scale">比例为{{gzj_rule.company}}</span>
                </td>
            </tr>
            <tr>
                <td class="name">个人缴纳比例：</td>
                <td class="scale">
                    <input class="zb_inpt gzj-change" type="text" required  name="gzj_pro_cost_per" id="member" placeholder="请输入个人缴纳比例" value="{{gzj_rule.personMin}}"/>%<span class="cardinal">比例为{{gzj_rule.person}}</span>
                </td>
            </tr>
            <tr id="gzj_date">
                <td class="name">起缴时间：</td>
                <td class="start">
                    <select name="gzj_year" id="gzj_year" class="zb_inpt gzj-change">
                        <option value="">请选择</option>
                        {{each years}}
                        <option value="{{$value}}">{{$value}}</option>
                        {{/each}}
                    </select>
                    年
                    <select name="gzj_month" id="gzj_month" class="zb_inpt gzj-change" style="margin-left: 20px;">
                        <option value="">请选择</option>
                    </select>
                    月
                </td>
            </tr>
        </table>
        <div class="circle">
            <label>
                <div class="fl"><input type="radio" name="gzj_card" id="has-gzj-card" class="gzj-card" checked/>有公积金卡</div>
                <div class="fl c1">卡号：</div>
                <div class="fl"><input type="text" class="zb_inpt"  name="gzj_card_number" id="gzj_card_number" placeholder="请输入卡号" value=""  /></div>
            </label>
            <label>
                <div class="fl"><input type="radio" id="no-gzj-card" class="gzj-card" name="gzj_card" value="2" required />无公积金卡</div>
                <div class="fl c1">工本费：<span id="gzj-pro_cost">{{gzj_rule.pro_cost}}</span>元</div>
            </label>
        </div>
        <div class="subtotal">小计：<strong id="gzj-total">0元/月</strong></div>
    </div>
    <div class="subtotal disabled form-box" data-type="3">
        <div class="square">
            <label><input type="checkbox" id="czj-checkbox" class="buy-server" name="czj_buy" />购买残障金</label>
        </div>
        小计：<strong id="czj-total">0元/月</strong>
    </div>
    <div class="total" id="all-total">总计：0元/月</div>
</template>