webpackJsonp([10],{0:/*!***************************!*\
  !*** ../js/entry/user.js ***!
  \***************************/
function(e,n,r){"use strict";var i=r(/*! modules/util */4),t=i.combinePageModule;t({login:r(/*! page/login */135)})},135:/*!***************************!*\
  !*** ../js/page/login.js ***!
  \***************************/
function(e,n,r){(function(n,i){"use strict";var t=r(/*! api/user */27),u=t.login;r(/*! plug/validate/index */3);var a={init:function(){this.login(n("#login-form"))},login:function(e){e.validate({submitHandler:function(){var n=e.serializeArray();return u(n,function(e){location.href="/Service-Service-index"},function(e){var n=e.msg;n&&i.msg(n)}),!1},rules:{username:{required:!0,account:!1},password:{password:!0,unPureLetter:!1,unPureNum:!1,rangelength:!1}},messages:{username:{required:"请输入用户名",rangelength:"6-20字母、数字、下划线"},password:{required:"请输入密码",rangelength:"密码格式不正确"}}})}};e.exports=a}).call(n,r(/*! jquery */1),r(/*! layer */2))}});
//# sourceMappingURL=user.bundle.js.map