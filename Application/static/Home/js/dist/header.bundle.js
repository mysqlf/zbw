webpackJsonp([2],{0:/*!********************************!*\
  !*** ./js/entry/com/header.js ***!
  \********************************/
function(i,exports,o){var s=o(41);s.init()},41:/*!***********************************!*\
  !*** ./js/src/page/com/header.js ***!
  \***********************************/
function(i,exports,o){(function($){var s=o(12),n={init:function(){var i=this,o=$("#backTop");s.header.message(function(i){var o=i.msgCount,s=i.msgCount;return 1>o?void $("#allmsg").hide():($("#allmsg").css("display","block").html(o),void(s>0?$("#message").html("("+s+")").show():$("#message").hide()))}),$(window).scroll(function(){0==$(window).scrollTop()?o.css("visibility","hidden"):$(window).scrollTop()>0&&o.css("visibility","visible")}),o.click(function(){i.backTop()})},backTop:function(){$("body,html").animate({scrollTop:0},100)}};i.exports=n}).call(exports,o(1))}});
//# sourceMappingURL=header.bundle.js.map