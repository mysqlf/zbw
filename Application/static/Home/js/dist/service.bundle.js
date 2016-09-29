webpackJsonp([7],{0:/*!*********************************!*\
  !*** ./js/entry/com/service.js ***!
  \*********************************/
function(a,exports,t){(function($){var a=t(98),i=$("script").eq(-1).data("init");a[i]&&a[i]()}).call(exports,t(1))},98:/*!************************************!*\
  !*** ./js/src/page/com/service.js ***!
  \************************************/
function(a,exports,t){(function($,i){var n=t(12),e={unpaid:function(){$(".pay").on("click",function(){n.paytip(this,"service")})},list:function(){$('[data-btn="again"]').on("click",function(){var a=$(this),t=a.data("id");n.layer.repeal({$obj:a,data:{orderId:t},url:"/Company-Information-recoverOrder",msg:"是否重新购买？"})}),$('[data-btn="revoke"]').on("click",function(){var a=$(this),t=a.data("id");n.layer.repeal({$obj:a,data:{orderId:t},url:"/Company-Information-cancelOrder",msg:"是否撤销购买？"})}),$('[data-btn="pay"]').on("click",function(){var a=$(this);a.data("id");n.paytip(this,"service")})},message:function(){$(".jcheck").on("click",function(){var a=$(this),t=a.find(".detail");1!=t.data("show")&&($(".detail").fadeOut(),n.service.checkMessage(this,function(a){if(1==a.status){var n=a.data;$(".detail").data("show",!1),t.fadeIn().html(n[0].detail),t.data("show",!0)}else i.alert("请求失败，稍后再试")}))})}};a.exports=e}).call(exports,t(1),t(3))}});
//# sourceMappingURL=service.bundle.js.map