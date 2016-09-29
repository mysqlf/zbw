webpackJsonp([1],{

/***/ 0:
/*!**************************!*\
  !*** ./js/entry/bill.js ***!
  \**************************/
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {if(true){
		// http://mockjs.com/
		// 只有不是开发环境 才使用 mock模拟ajax
		//require('mockData/index.js');
	}
	
	
	__webpack_require__(/*! plug/bootstrap/index.js */ 2);
	__webpack_require__(/*! plug/placeholder.js */ 15);
	var initFn = $('script').eq(-1).data('init');
	
	var pages = {
		comBillList: __webpack_require__(/*! page/comBillList.js */ 58),
		comBillDetail: __webpack_require__(/*! page/comBillDetail.js */ 62)
	
	}
	
	if(pages[initFn]){
		pages[initFn].init();
	}
	
	
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! jquery */ 1)))

/***/ },

/***/ 58:
/*!************************************!*\
  !*** ./js/src/page/comBillList.js ***!
  \************************************/
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($, layer) {
	//企业账单列表
	var template = __webpack_require__(/*! art-template */ 26),
		validateFn = __webpack_require__(/*! modules/validate */ 32),
		icheckFn = __webpack_require__(/*! modules/icheck */ 37),
		billAct = __webpack_require__(/*! modules/actions/bill */ 59);
	
	var comBillList={
		init:function(){
			this.billPay();
	
			icheckFn.init().checkAll();
	
			$('[data-act="export-bill"]').click(function(){
				var url = 'Service-Bill-downloadBill?',
					$checked = $('#bill-list-form').find('.single-icheck:checked'),
					idArr = [],
					noArr = [],
					len = $checked.length;
	
				if(!len){
					layer.alert('至少选择一个账单')
				} else{
					$checked.each(function(index){
						var $this = $(this);
	
						idArr.push($this.data('bill_id'))
						noArr.push($this.data('bill_no'))
					})
	
					location.href = url + 'bill_id=' + idArr.join(',') + '&bill_no=' + noArr.join(',');
				}
	
			})
	
		},
		billPay:function(){
			var self = this;
	
			$('[data-act="comBillpayment"]').click(function(){
				var dataAct = $(this).data(),
					html = template.render(__webpack_require__(/*! tpl/payBill.vue */ 60).template)(dataAct);
				var $form = null;
	
				layer.open({
				    type: 1,
				    skin: 'bill-pay', //样式类名
				    closeBtn: 0, //不显示关闭按钮
				    shift: 5,
				    shadeClose: true, //开启遮罩关闭
				    area:['400px','auto'],
				    btn:['确定', '取消'],
				    yes:function(){
	
				    	$form.submit();
				    },
				    content: html
				});
	
				$form = $('#pay-form');
				self.validateObj = self.validate($form);
			})
		},
		validateObj: null,
		validate: function($form){
			var self = this;
	
			return $form.validate({
				submitHandler: function(){
					layer.confirm('确定保存？', {
					    btn: ['确定','取消'] //按钮
					}, function(){
	
						var data = $form.serializeArray();
	
						var loadIndex = layer.msg('提交中..');
	
						billAct.comBillpayment(data, function(json){
							layer.msg(json.msg, {time:2000},function(){
								layer.closeAll();
								location.reload();
							});
						}).complete(function(){
							layer.close(loadIndex);
						});
	
					});
	
					return false;
				},
				rules: {
					actual_price:{
						number: true
					}
				},
				messages: {
					actual_price: {
						required: '请输入实付金额'
					}
				}
			})
	
		}
	}
	module.exports = comBillList;
	
		
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! jquery */ 1), __webpack_require__(/*! layer */ 17)))

/***/ },

/***/ 59:
/*!****************************************!*\
  !*** ./js/src/modules/actions/bill.js ***!
  \****************************************/
/***/ function(module, exports, __webpack_require__) {

	var baseAct = __webpack_require__(/*! ./base */ 36);
	
	// 账单动作
	module.exports = {
		// 企业账单付款
		comBillpayment: function(data, cb){
			var opts = {
				data: data,
				url: '/Service-Bill-comBillpayment'
			}
			return baseAct.ajax(opts,cb);
		}
	}

/***/ },

/***/ 60:
/*!********************************!*\
  !*** ./js/src/tpl/payBill.vue ***!
  \********************************/
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_template__ = __webpack_require__(/*! !tpl-html-loader!./../../../../../../~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./payBill.vue */ 61)
	module.exports = __vue_script__ || {}
	if (__vue_template__) {
	(typeof module.exports === "function" ? (module.exports.options || (module.exports.options = {})) : module.exports).template = __vue_template__
	}
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), false)
	  if (!hotAPI.compatible) return
	  var id = "_v-4fa8c49c/payBill.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },

/***/ 61:
/*!*********************************************************************************************************************************!*\
  !*** F:/item/zbw/~/tpl-html-loader!F:/item/zbw/~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./js/src/tpl/payBill.vue ***!
  \*********************************************************************************************************************************/
/***/ function(module, exports) {

	module.exports = "\t\n\t<form id=\"pay-form\" class=\"pay-form-layer\" action=\"\">\n\t\t<div class=\"total-pay\">\n\t\t\t<label class=\"label-left vertical-top\">账单总额：</label>\n\t\t\t<span class=\"c-money f-bold\">{{pay}}元</span>\n\t\t</div>\n\t\t<div class=\"form-group\">\n\t\t\t<label class=\"label-left vertical-top text-right\">\n\t        \t实付：\n\t        </label>\n\t        <div class=\"inline-block\">\n\t        \t<input type=\"text\" class=\"form-control \" required name=\"actual_price\" placeholder=\"\" value=\"\">\n\t        </div>\n\t    </div>\n\t    <div class=\"form-group\">\n\t\t\t<label class=\"label-left vertical-top text-right\">\n\t        \t备注：\n\t        </label>\n\t        <div class=\"inline-block\">\n\t        \t<input type=\"text\" class=\"form-control \" name=\"note\" placeholder=\"备注\" value=\"\">\n\t        </div>\n\t    </div>\n\t    <input type=\"hidden\" name=\"id\" value=\"{{id}}\">\n\t    <input type=\"hidden\" name=\"bill_no\" value=\"{{bill_no}}\">\n    </form>\n";

/***/ },

/***/ 62:
/*!**************************************!*\
  !*** ./js/src/page/comBillDetail.js ***!
  \**************************************/
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($, layer) {
	//企业账单列表
	var validateFn = __webpack_require__(/*! modules/validate */ 32),
		icheckFn = __webpack_require__(/*! modules/icheck */ 37),
		billAct = __webpack_require__(/*! modules/actions/bill */ 59);
	
	var comBillList={
		init:function(){
			this.billPay();
			icheckFn.init();
	
	
		},
		billPay:function(){
			var self = this;
	
			$('[data-act="comBillpayment"]').click(function(){
	
				$form = $('#pay-form');
				self.validateObj = self.validate($form);
			})
		},
		validateObj: null,
		validate: function($form){
			var self = this;
	
			return $form.validate({
				submitHandler: function(){
					layer.confirm('确定保存？', {
					    btn: ['确定','取消'] //按钮
					}, function(){
	
						var data = $form.serializeArray();
	
						var loadIndex = layer.msg('提交中..');
	
						billAct.comBillpayment(data, function(json){
							layer.msg(json.msg, {time:2000},function(){
								layer.closeAll();
								location.reload();
							});
						}).complete(function(){
							layer.close(loadIndex);
						});
	
					});
	
					return false;
				},
				rules: {
					actual_price:{
						number: true
					}
				},
				messages: {
					actual_price: {
						required: '请输入实付金额'
					},
					has_pay: {
						required: '请选择确认付款'
					}
				}
			})
	
		}
	}
	module.exports = comBillList;
	
		
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! jquery */ 1), __webpack_require__(/*! layer */ 17)))

/***/ }

});
//# sourceMappingURL=bill.bundle.js.map