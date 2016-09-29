webpackJsonp([6],{0:/*!*******************************!*\
  !*** ../js/entry/business.js ***!
  \*******************************/
function(e,t,a){"use strict";var n=a(4),i=n.combinePageModule;i({salary:a(137),company_declare:a(127)})},62:/*!*****************************!*\
  !*** ../js/api/business.js ***!
  \*****************************/
function(e,t,a){"use strict";var n=a(8),i=n.concatApi,r={updateSalaryOrder:"Service-Business-updateSalaryOrder",deleteSalaryOrder:"Service-Business-deleteSalaryOrder",updateInsuranceOrder:"Service-Business-updateInsuranceOrder"};e.exports=i(r)},64:/*!*********************************!*\
  !*** ../js/modules/business.js ***!
  \*********************************/
function(e,t,a){(function(e,n){"use strict";t.businessHandler=function(){var t=a(6),i=a(62),r=i.updateSalaryOrder,c=i.updateInsuranceOrder,l=a(5);e(".payroll-handle").click(function(i){var s=this,o=e(".single-icheck").filter(":checked").length;0===o?n.msg("请选择记录~"):!function(){var i=e(s).data(),o=i.act,u=i.page,d="",p=e("#J_salary-form").serializeArray();o&&!function(){var i=null,s="",f={},m={},v={},h="declare"==u?c:r;switch(o){case"batch_audit":v={value:1,text:"审核"},s="批量审核";break;case"batch_transact":v={value:3,text:"办理"},s="批量办理";break;default:v={value:3,text:"发放"},s="批量发放"}d=t.render(a(230).template)(v),n.open({title:s,skin:"batch-layer",content:d,btn:["确定","取消"],yes:function(){i.submit()},success:function(){i=e(".batch-layer form"),l.iCheck(),i.validate({submitHandler:function(t){var a=e(t).serializeArray();h(a.concat(p),function(e){var t=e.msg,a=void 0===t?s+"成功":t;n.msg(a,function(){location.href=location.href})},function(e){var t=e.msg,a=void 0===t?s+"失败":t;n.msg(a)})},rules:f,messages:m})}})}()}()})}}).call(t,a(1),a(2))},127:/*!*************************************!*\
  !*** ../js/page/company_declare.js ***!
  \*************************************/
function(e,t,a){(function(t){"use strict";var n=a(5),i=a(64),r=i.businessHandler,c=a(7),l=a(28),s=l.countDown,o={init:function(){n.init(),n.checkAll(),c.getYearMonth(),r(),t(".deadline").each(function(){var e=t(this),a=e.text().trim(),n=new Date(a),i=e.data(),r=i.timer;"/"==a?e.html("/"):n.getTime()<(new Date).getTime()?e.html("已截止"):(r=setInterval(function(){var t=s(n),a=t.dd,i=t.hh,c=t.mm,l=t.ss;(!t||n.getTime()<(new Date).getTime())&&clearInterval(r),e.html(a+"天"+i+"小时"+c+"分"+l+"秒")},1e3),e.data("timer",r))})}};e.exports=o}).call(t,a(1))},137:/*!****************************!*\
  !*** ../js/page/salary.js ***!
  \****************************/
function(e,t,a){(function(t,n){"use strict";var i=a(5),r=a(34),c=a(62),l=c.deleteSalaryOrder,s=a(7),o=a(28),u=o.dateRange,d=a(64),p=d.businessHandler;a(3);var f={init:function(){var e=this;i.init(),i.checkAll(),p(),e.upload(),s.getYearMonth(),s.getYearMonthDay(),u("declare"),t('[data-act="del-salary"]').click(function(){var e=t(this),a=e.data(),i=a.id,r=a.userid;n.open({title:"删除",content:"是否删除？",btn:["删除","取消"],yes:function(){l({id:i,userId:r},function(t){var a=t.msg,i=void 0===a?"删除成功":a;n.msg(i),e.closest("tr").remove()},function(e){var t=e.msg,a=void 0===t?"删除失败":t;n.msg(a)})}})})},upload:function(){var e=r.create({swf:"/Application/Service/Assets/js/plug/webuploader/Uploader.swf",server:"",auto:!1,prepareNextFile:!0,chunked:!0,duplicate:!0,accept:{title:"file",extensions:"",mimeTypes:""},pick:{id:".ipt-file",multiple:!0}});e.on("uploadAccept",function(e,t){}),e.on("uploadFinished",function(){}),t('[data-act="upload"]').click(function(){e.upload()})}};e.exports=f}).call(t,a(1),a(2))},208:/*!**********************************************************************************************************************************!*\
  !*** F:/item/zbw/~/tpl-html-loader!F:/item/zbw/~/tpl-loader-ie8/lib/selector.js?type=template&index=0!../js/tpl/batch_audit.vue ***!
  \**********************************************************************************************************************************/
function(e,t){e.exports='\n\n<div class="layer-cnt">\n	<form method="post" id="batchAudit_form" autocomplete="off">\n		<div class="layer-pannel">\n			<p>\n				<label class="radio-label">\n					<input type="radio" name="type" value="{{value}}" class="icheck" checked />\n					{{text}}成功\n				</label>\n				{{if text != \'发放\'}}\n				<label class="radio-label">\n					<input type="radio" name="type" value="-{{value}}" class="icheck" />\n					{{text}}失败\n				</label>\n				{{/if}}\n			</p>\n			<p>\n				<label>备注：</label><textarea class="remark" name="remark"></textarea>\n			</p>\n		</div>\n	</form>\n</div>\n'},230:/*!*********************************!*\
  !*** ../js/tpl/batch_audit.vue ***!
  \*********************************/
function(e,t,a){var n,i;i=a(208),e.exports=n||{},i&&(("function"==typeof e.exports?e.exports.options||(e.exports.options={}):e.exports).template=i)}});
//# sourceMappingURL=business.bundle.js.map