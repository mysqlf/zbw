webpackJsonp([8],{0:/*!***************************!*\
  !*** ../js/entry/Bill.js ***!
  \***************************/
function(e,t,i){"use strict";var n=i(/*! modules/util */7),c=n.combinePageModule;c({index:i(/*! page/Bill/index.js */76)})},76:/*!********************************!*\
  !*** ../js/page/Bill/index.js ***!
  \********************************/
function(e,t,i){(function(t){"use strict";i(/*! plug/selectordie/index */2),i(/*! plug/validate/validate */3),i(/*! plug/datetimepicker/datetimepicker */8);var n={init:function(){var e=this;t(".select").selectOrDie(),e.timePicker(),e.validate()},timePicker:function(){t(".timepicker").datetimepicker({format:"yyyy-mm",weekStart:1,autoclose:!0,startView:3,minView:3,forceParse:!1,language:"zh-CN",endDate:new Date})},validate:function(){var e=t("#listForm");t("#submitBtn").click(function(){e.submit()})}};e.exports=n}).call(t,i(/*! jquery */1))}});
//# sourceMappingURL=Bill.bundle.js.map