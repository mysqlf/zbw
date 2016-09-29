<template>
    <div class="payTip" id="payTip">
        <div class="details">
            <p>账单名称：<strong>{{billName}}</strong></p>
            <p>账单年月：<strong>{{orderDateValue}}</strong></p>
            <p>付款金额：<strong class="orange">{{price}}元</strong></p>
            <p>往期差额：<strong class="blue">{{balance_total}}元</strong></p>
        </div>
        <div class="total">
            所需实付金额：{{totalPrice}}元
        </div>
        <div class="paytype">
            <ul class="tit clearfix">
                <li class="active t1">线下支付</li>
                <li>在线支付</li>
            </ul>
            <div class="cont">
                <div class="item offline">
                    <h3>转账/汇款到以下账户</h3>
                    <p>户名：<strong>{{productOrder.account_name}}</strong></p>
                    <p>账号：<strong>{{productOrder.account}}</strong></p>
                    <p>开户行：<strong>{{openBank}}</strong></p>
                    <a href="javascript:;" class="blueB" data-No="{{bill_no}}" data-id="{{id}}">确定</a>
                </div>
                <div class="item online">
                    <p>程序员哥哥正在抓紧开发在线支付功能，<br/>耐心等待哦~</p>
                    <a href="javascript:;" class="arrowsL"><i class="icon icon-arrowsL"></i>去线下支付</a>
                </div>
            </div>
        </div>
    </div>
</template>