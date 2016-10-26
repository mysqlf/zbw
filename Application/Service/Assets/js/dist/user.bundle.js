webpackJsonp([10],{0:/*!***************************!*\
  !*** ../js/entry/user.js ***!
  \***************************/
function(e,r,n){"use strict";var i=n(/*! modules/util */4),a=i.combinePageModule;a({login:n(/*! page/login */136)})},136:/*!***************************!*\
  !*** ../js/page/login.js ***!
  \***************************/
function(e,r,n){(function(r,i){"use strict";var a=n(/*! api/user */28),t=a.login;n(/*! plug/validate/index */3);var u={init:function(){this.login(r("#login-form"))},login:function(e){e.validate({submitHandler:function(){var n=e.serializeArray();return t(n,function(e){window.location.replace("/Service-Service-index")},function(e){var n=e.msg;n&&(i.msg(n),r(".verifyimg").trigger("click"),r("#verify").val(""))}),!1},rules:{username:{required:!0,account:!1},password:{password:!0,unPureLetter:!1,unPureNum:!1,rangelength:!1}},messages:{username:{required:"请输入用户名",rangelength:"6-20字母、数字、下划线"},password:{required:"请输入密码",rangelength:"密码格式不正确"},verify:{required:"请输入验证码"}}})}};e.exports=u}).call(r,n(/*! jquery */1),n(/*! layer */2))}});
//# sourceMappingURL=user.bundle.js.map