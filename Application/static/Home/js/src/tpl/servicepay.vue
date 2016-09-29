<template>
    <div class="payTip" id="payTip">
        <div class="details" style="width:160px;">
            <table class="tableForm">
                <tr>
                    <td class="name">产品名称：</td>
                    <td><strong>{{product_name}}</strong></td>
                </tr>
                <tr>
                    <td class="name">价格：</td>
                    <td><strong>{{beforeDiscountPrice}}</strong></td>
                </tr>
                <tr>
                    <td class="name">优惠价格：</td>
                    <td><strong>{{discountPrice}}</strong></td>
                </tr>
            </table>
        </div>
        <div class="total">
            所需实付金额：{{afterDiscountPrice}}元
        </div>
        <div class="paytype">
            <ul class="tit clearfix">
                <li class="active t1">线下支付</li>
                <li>在线支付</li>
            </ul>
            <div class="cont">
                <div class="item offline">
                    <h3>转账/汇款到以下账户</h3>
                    <p>户名：<strong>{{account_name}}</strong></p>
                    <p>账号：<strong>{{account}}</strong></p>
                    <p>开户行：<strong>{{openBank}}</strong></p>
                    <a href="javascript:;" class="blueB" data-id="{{id}}">确定</a>
                </div>
                <div class="item online">
                    <p>程序员哥哥正在抓紧开发在线支付功能，<br/>耐心等待哦~</p>
                    <a href="javascript:;" class="arrowsL"><i class="icon icon-arrowsL"></i>去线下支付</a>
                </div>
            </div>
        </div>
    </div>
</template>