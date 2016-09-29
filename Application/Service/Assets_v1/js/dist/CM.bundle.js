webpackJsonp([0],[
/* 0 */
/*!************************!*\
  !*** ./js/entry/CM.js ***!
  \************************/
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {
	if(true){
		// http://mockjs.com/
		// 只有不是开发环境 才使用 mock模拟ajax
		//require('mockData/index.js');
	}
	
	
	__webpack_require__(/*! plug/bootstrap/index.js */ 2);
	__webpack_require__(/*! plug/placeholder.js */ 15);
	
	var initFn = $('script').eq(-1).data('init');
	
	var pages = {
		comBuyService: __webpack_require__(/*! page/comBuyService.js */ 16),
		comMemBersDetail: __webpack_require__(/*! page/comMemBersDetail.js */ 47)
	}
	
	if(pages[initFn]){
		pages[initFn].init();
	}
	
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! jquery */ 1)))

/***/ },
/* 1 */,
/* 2 */,
/* 3 */,
/* 4 */,
/* 5 */,
/* 6 */,
/* 7 */,
/* 8 */,
/* 9 */,
/* 10 */,
/* 11 */,
/* 12 */,
/* 13 */,
/* 14 */,
/* 15 */,
/* 16 */
/*!**************************************!*\
  !*** ./js/src/page/comBuyService.js ***!
  \**************************************/
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($, layer) {
	var template = __webpack_require__(/*! art-template */ 26),
	    validateFn = __webpack_require__(/*! modules/validate */ 32),
	    CMAct = __webpack_require__(/*! modules/actions/userManagement.js */ 35),
	    icheckFn = __webpack_require__(/*! modules/icheck */ 37);
	
	var comBuyService={
		init:function(){
			var self = this;
	
			$('[data-act="assign"]').click(function(){
				var data = $(this).data();
				self.assign(data);
			})
	
			//修改价格
			$('[data-act="changePrice"]').click(function(){
				var data = $(this).data();
	
				self.modifyPrice(data);
			})
	
			
		},
		modifyPrice: function(data){
			var self = this,
				$form = null;
	
			layer.open({
			    title: '修改价格',
			    skin: 'layer-form', //样式类名
			    area:['400px','auto'],
			    btn:['确定', '取消'],
			    yes:function(){
	
			    	$form.submit();
			    },
			    content: template.render(__webpack_require__(/*! tpl/modifyPrice.vue */ 43).template)(data)
			});
	
			$form = $('#modifyPrice-form');
			self.modifyPvalidate($form);
		},
		modifyPvalidate: function($form){
			return $form.validate({
				submitHandler:function(){
					var data = $form.serializeArray();
	
					CMAct.setPrice(data, function(){
						layer.msg('修改成功',{
							end: function(){
								location.reload();
							}
						});
						
					});
				},
				rules: {
					modify_price:{
						number: true
					}
				},
				messages:{
					modify_price:{
						required: '请输入优惠价'
					}
				}
			});
		},
		//分配
		assign:function(data){
			var self = this;
	
			CMAct.getCService(data,function(json){
				var retData = json,
					$form = null;
	
				retData.id = data.id;
	
				layer.open({
				    title: '分配',
				    skin: 'layer-form', //样式类名
				    area:['350px','auto'],
				    btn:['确定分配', '取消'],
				    yes:function(){
				    	$form.submit();
				    },
				    content: template.render(__webpack_require__(/*! tpl/assign.vue */ 45).template)(retData)
				});
	
				$form = $('#assign-form');
				self.assignValidate($form);
			})
		},
		assignValidate: function($form){
			return $form.validate({
				submitHandler:function(){
					var data = $form.serializeArray();
	
					CMAct.saveCService(data, function(){
						layer.msg('分配成功',{
							end: function(){
								layer.closeAll();
								location.reload();
							}
						});
						
					});
				},
				messages:{
					admin_id:{
						required: '请选择客服'
					}
				}
			});
		}
	}
	module.exports = comBuyService;
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! jquery */ 1), __webpack_require__(/*! layer */ 17)))

/***/ },
/* 17 */,
/* 18 */,
/* 19 */,
/* 20 */,
/* 21 */,
/* 22 */,
/* 23 */,
/* 24 */,
/* 25 */,
/* 26 */,
/* 27 */,
/* 28 */,
/* 29 */,
/* 30 */,
/* 31 */,
/* 32 */,
/* 33 */,
/* 34 */,
/* 35 */
/*!**************************************************!*\
  !*** ./js/src/modules/actions/userManagement.js ***!
  \**************************************************/
/***/ function(module, exports, __webpack_require__) {

	var baseAct = __webpack_require__(/*! ./base */ 36);
	
	
	module.exports = {
		// 获取客服
		getCService: function(data, cb){
			var opts = {
				data: data,
				url: '/Service-Members-cServideList'
			}
			return baseAct.ajax(opts,cb);
		},
		// 保存客服
		saveCService: function(data, cb){
			var opts = {
				data: data,
				url: '/Service-Members-comDisAdmin'
			}
			return baseAct.ajax(opts,cb);
		},
		// 设置价格
		setPrice: function(data, cb){
			var opts = {
				data: data,
				url: '/Service-Members-comSetPrice'
			}
			return baseAct.ajax(opts,cb);
		},
		// 设置服务
		setService: function(data, cb){var opts = {
				data: data,
				url: '/Service-Members-comSetService'
			}
			return baseAct.ajax(opts,cb);
		},
		// 获取参保地
		wLocation: function(data, cb){var opts = {
				data: data,
				url: '/Service-Members-wLocation'
			}
			return baseAct.ajax(opts,cb);
		},
		// 添加参保地
		addLocation: function(data, cb){var opts = {
				data: data,
				url: '/Service-Members-comAddLocation'
			}
			return baseAct.ajax(opts,cb);
		},
		comPayment: function(data, cb){
			var opts = {
				data: data,
				url: '/Service-Members-comPayment'
			}
			return baseAct.ajax(opts,cb);
		}
		
	}

/***/ },
/* 36 */,
/* 37 */,
/* 38 */,
/* 39 */,
/* 40 */,
/* 41 */,
/* 42 */,
/* 43 */
/*!************************************!*\
  !*** ./js/src/tpl/modifyPrice.vue ***!
  \************************************/
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_template__ = __webpack_require__(/*! !tpl-html-loader!./../../../../../../~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./modifyPrice.vue */ 44)
	module.exports = __vue_script__ || {}
	if (__vue_template__) {
	(typeof module.exports === "function" ? (module.exports.options || (module.exports.options = {})) : module.exports).template = __vue_template__
	}
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), false)
	  if (!hotAPI.compatible) return
	  var id = "_v-c8606fdc/modifyPrice.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 44 */
/*!*************************************************************************************************************************************!*\
  !*** F:/item/zbw/~/tpl-html-loader!F:/item/zbw/~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./js/src/tpl/modifyPrice.vue ***!
  \*************************************************************************************************************************************/
/***/ function(module, exports) {

	module.exports = "\r\n<!-- \t<table>\r\n    <tr>\r\n        <td align=\"right\">购买时间：</td>\r\n        <td>2015/11/21 12:12</td>\r\n    </tr>\r\n    <tr>\r\n        <td align=\"right\">企业：</td>\r\n        <td>智通人才</td>\r\n    </tr>\r\n    <tr>\r\n        <td align=\"right\">产品名称：</td>\r\n        <td>智尊宝会员年费</td>\r\n    </tr>\r\n    <tr>\r\n        <td align=\"right\">金额：</td>\r\n        <td>1600元</td>\r\n    </tr>\r\n    <tr>\r\n        <td align=\"right\">优惠价：</td>\r\n        <td>\r\n            <input type=\"text\">元</td>\r\n    </tr>\r\n</table> -->\r\n\t<form id=\"modifyPrice-form\" method=\"post\">\r\n\t\t<div class=\"form-group pad-0\">\r\n\t        <label class=\"label-left vertical-top text-right w-7em\">\r\n\t        购买时间：\r\n\t        </label>\r\n\t        <div class=\"inline-block\">\r\n\t       \t{{time}}\r\n\t       \t</div>\r\n\t    </div>\r\n\t    <div class=\"form-group pad-0\">\r\n\t        <label class=\"label-left vertical-top text-right w-7em\">\r\n\t        企业：\r\n\t        </label>\r\n\t        <div class=\"inline-block\">\r\n\t       \t{{product_name}}\r\n\t       \t</div>\r\n\t    </div>\r\n\t    <div class=\"form-group pad-0\">\r\n\t        <label class=\"label-left vertical-top text-right w-7em\">\r\n\t        产品名称：\r\n\t        </label>\r\n\t        <div class=\"inline-block\">\r\n\t       \t{{product_name}}\r\n\t       \t</div>\r\n\t    </div>\r\n\t    <div class=\"form-group pad-0\">\r\n\t        <label class=\"label-left vertical-top text-right w-7em\">\r\n\t        金额：\r\n\t        </label>\r\n\t        <div class=\"inline-block\">\r\n\t       \t<span class=\"c-money f-bold\">{{price}}元</span>\r\n\t       \t</div>\r\n\t    </div>\r\n\r\n\t    <div class=\"form-group pad-0 \">\r\n\t        <label class=\"label-left vertical-top text-right w-7em\">\r\n\t        优惠价：\r\n\t        </label>\r\n\t        <div class=\"inline-block\">\r\n\t       \t\t<input class=\"form-control\" name=\"modify_price\" type=\"text\" required>\r\n\t       \t</div>\r\n\t       \t元\r\n\t    </div>\r\n\r\n\t    <input type=\"hidden\" name=\"id\" value=\"{{id}}\">\r\n\r\n\t</form>\r\n\t\t\t\r\n";

/***/ },
/* 45 */
/*!*******************************!*\
  !*** ./js/src/tpl/assign.vue ***!
  \*******************************/
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_template__ = __webpack_require__(/*! !tpl-html-loader!./../../../../../../~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./assign.vue */ 46)
	module.exports = __vue_script__ || {}
	if (__vue_template__) {
	(typeof module.exports === "function" ? (module.exports.options || (module.exports.options = {})) : module.exports).template = __vue_template__
	}
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), false)
	  if (!hotAPI.compatible) return
	  var id = "_v-a14aee48/assign.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 46 */
/*!********************************************************************************************************************************!*\
  !*** F:/item/zbw/~/tpl-html-loader!F:/item/zbw/~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./js/src/tpl/assign.vue ***!
  \********************************************************************************************************************************/
/***/ function(module, exports) {

	module.exports = "\n\t<form id=\"assign-form\" class=\"assign-form\" method=\"post\">\n\n\t\t<div class=\"form-group text-center\">\n\t        <label class=\"label-left vertical-top\">\n\t        <span class=\"c-required\">*</span>分配给客服：\n\t        </label>\n\t        <div class=\"inline-block\">\n\t       \t<select class=\"base-size\" name=\"admin_id\" required>\n\t       \t\t<option value=\"\">请选择</option>\n\t       \t\t{{each data}}\n\t\t\t\t\t<option value=\"{{$value.id}}\">{{$value.name}}</option>\n\t\t\t\t{{/each}}\n\t       \t</select>\n\t       \t</div>\n\t    </div>\n\n\t    <input type=\"hidden\" name=\"id\" value=\"{{id}}\">\n    </form>\n";

/***/ },
/* 47 */
/*!*****************************************!*\
  !*** ./js/src/page/comMemBersDetail.js ***!
  \*****************************************/
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($, layer) {var template = __webpack_require__(/*! art-template */ 26),
	    validateFn = __webpack_require__(/*! modules/validate */ 32),
	    CMAct = __webpack_require__(/*! modules/actions/userManagement.js */ 35),
	    dateObj = __webpack_require__(/*! modules/date */ 48),
	    icheckFn = __webpack_require__(/*! modules/icheck */ 37);
	
	__webpack_require__(/*! plug/datetimepicker */ 49);
	
	module.exports = {
		init: function(){
			var self = this;
	
			$('[data-act="comSetService"]').click(function(){
				var $form = null,
					data = $(this).data().json;
					data.days = dateObj.createNumArr(1,28);
	
				layer.open({
					title:'设定服务状态',
					area: ['700px','auto'],
					btn: ['确认','取消'],
					skin: 'layer-form',
					content: template.render(__webpack_require__(/*! tpl/setCM.vue */ 54).template)(data),
					yes: function(){
						$form.submit();
					}
				});
	
				$form = $('#comSetService-form');
				icheckFn.init();
				self.setValidate($form);
	
				self.requiredEls = $('#state2-box').find('[required]');
	
				$('[name="service_state"]:checked').trigger('ifChecked');
	
				$('#overtime').datetimepicker({
					timepicker:false,
					minDate: $.now(),
					format:'Y-m-d',
					formatDate:'Y-m-d'
				})
			});
	
			$('body').on('ifChecked','[name="service_state"]',function(){
				var $state2Box = $('#state2-box')
	
				if(this.value == 2){
					self.requiredEls.prop('required', true);
				} else{
					self.requiredEls.prop('required', false);
					self.requiredEls.removeClass('error');
					$state2Box.find('span.error').remove();
				}
			})
	
	
			$('[ data-act="addLocation"]').click(function(){
				self.addOrModifyLocation(this);
			})
	
			$('[ data-act="modifyLocation"]').click(function(){
				self.addOrModifyLocation(this,1);
			})
	
			$('[ data-act="comPayment"]').click(function(){
				var data = $(this).data();
	
				layer.confirm('确认支付？',function(){
					CMAct.comPayment(data,function(){
						layer.msg('确认支付成功',{
							end: function(){
								location.reload();
							}
						});
					})
				})
			})
	
	
		},
		requiredEls: {},
		/**
		 * 修改和添加参保地
		 * @param {[type]} el   点击元素
		 * @param {[type]} type 1为修改 其他未新建
		 */
		addOrModifyLocation: function(el,type){
			var self = this,
				$form = null,
				data = $(el).data(),
				title = type ? '修改' : '添加';
	
				CMAct.wLocation(data,function(json){
					var retData = data;
	
					retData.locationArr = json.data
	
					layer.open({
						title: title + '参保地',
						area: ['470px','auto'],
						btn: ['确认','取消'],
						skin: 'layer-form',
						content: template.render(__webpack_require__(/*! tpl/addLocation.vue */ 56).template)(retData),
						yes: function(){
							$form.submit();
						}
					});
	
					$form = $('#addLocation-form');
					self.addValidate($form,type);
				})
		},
		setValidate: function($form){
			return $form.validate({
				submitHandler:function(){
					var data = $form.serializeArray();
	
					CMAct.setService(data, function(){
						layer.msg('设置成功',{
							end: function(){
								location.reload();
							}
						});
	
					});
	
				},
				rules: {
					overtime:{
	
					}
				},
				messages:{
					overtime:{
						required: '请选择服务有效期'
					},
					abort_add_del_date:{
						required: '请选择报增减截止日'
					},
					create_bill_date:{
						required: '请选择账单日'
					},
					abort_payment_date:{
						required: '请选择支付截止日期'
					}
					
				}
			});
		},
		addValidate: function($form,type){
			var msg = type ? '修改' : '添加';
	
			return $form.validate({
				submitHandler:function(){
					var data = $form.serializeArray();
	
					CMAct.addLocation(data, function(){
						layer.msg(msg+'成功',{
							end: function(){
								location.reload();
							}
						});
	
					});
	
				},
				rules: {
					af_service_price:{
						number: true
					},
					ss_service_price:{
						number: true
					}
				},
				messages:{
					location:{
						required: '请选择参保地'
					},
					af_service_price:{
						required: '请输入代发工资服务费'
					},
					ss_service_price:{
						required: '请输入社保/公积金服务费'
					}
				}
			});
		}
	}
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! jquery */ 1), __webpack_require__(/*! layer */ 17)))

/***/ },
/* 48 */
/*!********************************!*\
  !*** ./js/src/modules/date.js ***!
  \********************************/
/***/ function(module, exports) {

	module.exports = {
		getDiffYear: function(begin, end){
			return new Date(end).getFullYear() - new Date(begin).getFullYear();
		},
		createNumArr: function(begin, end){
			var arr = [],
				i = begin;
	
			if(begin > end) return;
	
			for(;i <= end; i++){
				arr.push(i);
			}
	
			return arr;
		}
	}

/***/ },
/* 49 */
/*!***************************************!*\
  !*** ./js/src/plug/datetimepicker.js ***!
  \***************************************/
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {
	__webpack_require__(/*! jquery-datetimepicker/jquery.datetimepicker.css */ 50)
	__webpack_require__(/*! jquery-datetimepicker/build/jquery.datetimepicker.full */ 52);
	$.datetimepicker.setLocale('zh');
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! jquery */ 1)))

/***/ },
/* 50 */
/*!*********************************************************************!*\
  !*** F:/item/zbw/~/jquery-datetimepicker/jquery.datetimepicker.css ***!
  \*********************************************************************/
/***/ function(module, exports, __webpack_require__) {

	// style-loader: Adds some css to the DOM by adding a <style> tag
	
	// load the styles
	var content = __webpack_require__(/*! !./../css-loader!./jquery.datetimepicker.css */ 51);
	if(typeof content === 'string') content = [[module.id, content, '']];
	// add the styles to the DOM
	var update = __webpack_require__(/*! ./../style-loader/addStyles.js */ 25)(content, {});
	if(content.locals) module.exports = content.locals;
	// Hot Module Replacement
	if(false) {
		// When the styles change, update the <style> tags
		if(!content.locals) {
			module.hot.accept("!!./../css-loader/index.js!./jquery.datetimepicker.css", function() {
				var newContent = require("!!./../css-loader/index.js!./jquery.datetimepicker.css");
				if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
				update(newContent);
			});
		}
		// When the module is disposed, remove the <style> tags
		module.hot.dispose(function() { update(); });
	}

/***/ },
/* 51 */
/*!**********************************************************************************************!*\
  !*** F:/item/zbw/~/css-loader!F:/item/zbw/~/jquery-datetimepicker/jquery.datetimepicker.css ***!
  \**********************************************************************************************/
/***/ function(module, exports, __webpack_require__) {

	exports = module.exports = __webpack_require__(/*! ./../css-loader/lib/css-base.js */ 20)();
	// imports
	
	
	// module
	exports.push([module.id, ".xdsoft_datetimepicker {\r\n\tbox-shadow: 0 5px 15px -5px rgba(0, 0, 0, 0.506);\r\n\tbackground: #fff;\r\n\tborder-bottom: 1px solid #bbb;\r\n\tborder-left: 1px solid #ccc;\r\n\tborder-right: 1px solid #ccc;\r\n\tborder-top: 1px solid #ccc;\r\n\tcolor: #333;\r\n\tfont-family: \"Helvetica Neue\", Helvetica, Arial, sans-serif;\r\n\tpadding: 8px;\r\n\tpadding-left: 0;\r\n\tpadding-top: 2px;\r\n\tposition: absolute;\r\n\tz-index: 9999;\r\n\t-moz-box-sizing: border-box;\r\n\tbox-sizing: border-box;\r\n\tdisplay: none;\r\n}\r\n.xdsoft_datetimepicker.xdsoft_rtl {\r\n\tpadding: 8px 0 8px 8px;\r\n}\r\n\r\n.xdsoft_datetimepicker iframe {\r\n\tposition: absolute;\r\n\tleft: 0;\r\n\ttop: 0;\r\n\twidth: 75px;\r\n\theight: 210px;\r\n\tbackground: transparent;\r\n\tborder: none;\r\n}\r\n\r\n/*For IE8 or lower*/\r\n.xdsoft_datetimepicker button {\r\n\tborder: none !important;\r\n}\r\n\r\n.xdsoft_noselect {\r\n\t-webkit-touch-callout: none;\r\n\t-webkit-user-select: none;\r\n\t-khtml-user-select: none;\r\n\t-moz-user-select: none;\r\n\t-ms-user-select: none;\r\n\t-o-user-select: none;\r\n\tuser-select: none;\r\n}\r\n\r\n.xdsoft_noselect::selection { background: transparent }\r\n.xdsoft_noselect::-moz-selection { background: transparent }\r\n\r\n.xdsoft_datetimepicker.xdsoft_inline {\r\n\tdisplay: inline-block;\r\n\tposition: static;\r\n\tbox-shadow: none;\r\n}\r\n\r\n.xdsoft_datetimepicker * {\r\n\t-moz-box-sizing: border-box;\r\n\tbox-sizing: border-box;\r\n\tpadding: 0;\r\n\tmargin: 0;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_datepicker, .xdsoft_datetimepicker .xdsoft_timepicker {\r\n\tdisplay: none;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_datepicker.active, .xdsoft_datetimepicker .xdsoft_timepicker.active {\r\n\tdisplay: block;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_datepicker {\r\n\twidth: 224px;\r\n\tfloat: left;\r\n\tmargin-left: 8px;\r\n}\r\n.xdsoft_datetimepicker.xdsoft_rtl .xdsoft_datepicker {\r\n\tfloat: right;\r\n\tmargin-right: 8px;\r\n\tmargin-left: 0;\r\n}\r\n\r\n.xdsoft_datetimepicker.xdsoft_showweeks .xdsoft_datepicker {\r\n\twidth: 256px;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_timepicker {\r\n\twidth: 58px;\r\n\tfloat: left;\r\n\ttext-align: center;\r\n\tmargin-left: 8px;\r\n\tmargin-top: 0;\r\n}\r\n.xdsoft_datetimepicker.xdsoft_rtl .xdsoft_timepicker {\r\n\tfloat: right;\r\n\tmargin-right: 8px;\r\n\tmargin-left: 0;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_datepicker.active+.xdsoft_timepicker {\r\n\tmargin-top: 8px;\r\n\tmargin-bottom: 3px\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_mounthpicker {\r\n\tposition: relative;\r\n\ttext-align: center;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_label i,\r\n.xdsoft_datetimepicker .xdsoft_prev,\r\n.xdsoft_datetimepicker .xdsoft_next,\r\n.xdsoft_datetimepicker .xdsoft_today_button {\r\n\tbackground-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAAAeCAYAAADaW7vzAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6Q0NBRjI1NjM0M0UwMTFFNDk4NkFGMzJFQkQzQjEwRUIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6Q0NBRjI1NjQ0M0UwMTFFNDk4NkFGMzJFQkQzQjEwRUIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpDQ0FGMjU2MTQzRTAxMUU0OTg2QUYzMkVCRDNCMTBFQiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpDQ0FGMjU2MjQzRTAxMUU0OTg2QUYzMkVCRDNCMTBFQiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PoNEP54AAAIOSURBVHja7Jq9TsMwEMcxrZD4WpBYeKUCe+kTMCACHZh4BFfHO/AAIHZGFhYkBBsSEqxsLCAgXKhbXYOTxh9pfJVP+qutnZ5s/5Lz2Y5I03QhWji2GIcgAokWgfCxNvcOCCGKqiSqhUp0laHOne05vdEyGMfkdxJDVjgwDlEQgYQBgx+ULJaWSXXS6r/ER5FBVR8VfGftTKcITNs+a1XpcFoExREIDF14AVIFxgQUS+h520cdud6wNkC0UBw6BCO/HoCYwBhD8QCkQ/x1mwDyD4plh4D6DDV0TAGyo4HcawLIBBSLDkHeH0Mg2yVP3l4TQMZQDDsEOl/MgHQqhMNuE0D+oBh0CIr8MAKyazBH9WyBuKxDWgbXfjNf32TZ1KWm/Ap1oSk/R53UtQ5xTh3LUlMmT8gt6g51Q9p+SobxgJQ/qmsfZhWywGFSl0yBjCLJCMgXail3b7+rumdVJ2YRss4cN+r6qAHDkPWjPjdJCF4n9RmAD/V9A/Wp4NQassDjwlB6XBiCxcJQWmZZb8THFilfy/lfrTvLghq2TqTHrRMTKNJ0sIhdo15RT+RpyWwFdY96UZ/LdQKBGjcXpcc1AlSFEfLmouD+1knuxBDUVrvOBmoOC/rEcN7OQxKVeJTCiAdUzUJhA2Oez9QTkp72OTVcxDcXY8iKNkxGAJXmJCOQwOa6dhyXsOa6XwEGAKdeb5ET3rQdAAAAAElFTkSuQmCC);\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_label i {\r\n\topacity: 0.5;\r\n\tbackground-position: -92px -19px;\r\n\tdisplay: inline-block;\r\n\twidth: 9px;\r\n\theight: 20px;\r\n\tvertical-align: middle;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_prev {\r\n\tfloat: left;\r\n\tbackground-position: -20px 0;\r\n}\r\n.xdsoft_datetimepicker .xdsoft_today_button {\r\n\tfloat: left;\r\n\tbackground-position: -70px 0;\r\n\tmargin-left: 5px;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_next {\r\n\tfloat: right;\r\n\tbackground-position: 0 0;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_next,\r\n.xdsoft_datetimepicker .xdsoft_prev ,\r\n.xdsoft_datetimepicker .xdsoft_today_button {\r\n\tbackground-color: transparent;\r\n\tbackground-repeat: no-repeat;\r\n\tborder: 0 none;\r\n\tcursor: pointer;\r\n\tdisplay: block;\r\n\theight: 30px;\r\n\topacity: 0.5;\r\n\t-ms-filter: \"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)\";\r\n\toutline: medium none;\r\n\toverflow: hidden;\r\n\tpadding: 0;\r\n\tposition: relative;\r\n\ttext-indent: 100%;\r\n\twhite-space: nowrap;\r\n\twidth: 20px;\r\n\tmin-width: 0;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_prev,\r\n.xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_next {\r\n\tfloat: none;\r\n\tbackground-position: -40px -15px;\r\n\theight: 15px;\r\n\twidth: 30px;\r\n\tdisplay: block;\r\n\tmargin-left: 14px;\r\n\tmargin-top: 7px;\r\n}\r\n.xdsoft_datetimepicker.xdsoft_rtl .xdsoft_timepicker .xdsoft_prev,\r\n.xdsoft_datetimepicker.xdsoft_rtl .xdsoft_timepicker .xdsoft_next {\r\n\tfloat: none;\r\n\tmargin-left: 0;\r\n\tmargin-right: 14px;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_prev {\r\n\tbackground-position: -40px 0;\r\n\tmargin-bottom: 7px;\r\n\tmargin-top: 0;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_time_box {\r\n\theight: 151px;\r\n\toverflow: hidden;\r\n\tborder-bottom: 1px solid #ddd;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_time_box >div >div {\r\n\tbackground: #f5f5f5;\r\n\tborder-top: 1px solid #ddd;\r\n\tcolor: #666;\r\n\tfont-size: 12px;\r\n\ttext-align: center;\r\n\tborder-collapse: collapse;\r\n\tcursor: pointer;\r\n\tborder-bottom-width: 0;\r\n\theight: 25px;\r\n\tline-height: 25px;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_time_box >div > div:first-child {\r\n\tborder-top-width: 0;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_today_button:hover,\r\n.xdsoft_datetimepicker .xdsoft_next:hover,\r\n.xdsoft_datetimepicker .xdsoft_prev:hover {\r\n\topacity: 1;\r\n\t-ms-filter: \"progid:DXImageTransform.Microsoft.Alpha(Opacity=100)\";\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_label {\r\n\tdisplay: inline;\r\n\tposition: relative;\r\n\tz-index: 9999;\r\n\tmargin: 0;\r\n\tpadding: 5px 3px;\r\n\tfont-size: 14px;\r\n\tline-height: 20px;\r\n\tfont-weight: bold;\r\n\tbackground-color: #fff;\r\n\tfloat: left;\r\n\twidth: 182px;\r\n\ttext-align: center;\r\n\tcursor: pointer;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_label:hover>span {\r\n\ttext-decoration: underline;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_label:hover i {\r\n\topacity: 1.0;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_label > .xdsoft_select {\r\n\tborder: 1px solid #ccc;\r\n\tposition: absolute;\r\n\tright: 0;\r\n\ttop: 30px;\r\n\tz-index: 101;\r\n\tdisplay: none;\r\n\tbackground: #fff;\r\n\tmax-height: 160px;\r\n\toverflow-y: hidden;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_label > .xdsoft_select.xdsoft_monthselect{ right: -7px }\r\n.xdsoft_datetimepicker .xdsoft_label > .xdsoft_select.xdsoft_yearselect{ right: 2px }\r\n.xdsoft_datetimepicker .xdsoft_label > .xdsoft_select > div > .xdsoft_option:hover {\r\n\tcolor: #fff;\r\n\tbackground: #ff8000;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_label > .xdsoft_select > div > .xdsoft_option {\r\n\tpadding: 2px 10px 2px 5px;\r\n\ttext-decoration: none !important;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_label > .xdsoft_select > div > .xdsoft_option.xdsoft_current {\r\n\tbackground: #33aaff;\r\n\tbox-shadow: #178fe5 0 1px 3px 0 inset;\r\n\tcolor: #fff;\r\n\tfont-weight: 700;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_month {\r\n\twidth: 100px;\r\n\ttext-align: right;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_calendar {\r\n\tclear: both;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_year{\r\n\twidth: 48px;\r\n\tmargin-left: 5px;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_calendar table {\r\n\tborder-collapse: collapse;\r\n\twidth: 100%;\r\n\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_calendar td > div {\r\n\tpadding-right: 5px;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_calendar th {\r\n\theight: 25px;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_calendar td,.xdsoft_datetimepicker .xdsoft_calendar th {\r\n\twidth: 14.2857142%;\r\n\tbackground: #f5f5f5;\r\n\tborder: 1px solid #ddd;\r\n\tcolor: #666;\r\n\tfont-size: 12px;\r\n\ttext-align: right;\r\n\tvertical-align: middle;\r\n\tpadding: 0;\r\n\tborder-collapse: collapse;\r\n\tcursor: pointer;\r\n\theight: 25px;\r\n}\r\n.xdsoft_datetimepicker.xdsoft_showweeks .xdsoft_calendar td,.xdsoft_datetimepicker.xdsoft_showweeks .xdsoft_calendar th {\r\n\twidth: 12.5%;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_calendar th {\r\n\tbackground: #f1f1f1;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_today {\r\n\tcolor: #33aaff;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_highlighted_default {\r\n\tbackground: #ffe9d2;\r\n\tbox-shadow: #ffb871 0 1px 4px 0 inset;\r\n\tcolor: #000;\r\n}\r\n.xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_highlighted_mint {\r\n\tbackground: #c1ffc9;\r\n\tbox-shadow: #00dd1c 0 1px 4px 0 inset;\r\n\tcolor: #000;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_default,\r\n.xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_current,\r\n.xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_time_box >div >div.xdsoft_current {\r\n\tbackground: #33aaff;\r\n\tbox-shadow: #178fe5 0 1px 3px 0 inset;\r\n\tcolor: #fff;\r\n\tfont-weight: 700;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_other_month,\r\n.xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_disabled,\r\n.xdsoft_datetimepicker .xdsoft_time_box >div >div.xdsoft_disabled {\r\n\topacity: 0.5;\r\n\t-ms-filter: \"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)\";\r\n\tcursor: default;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_other_month.xdsoft_disabled {\r\n\topacity: 0.2;\r\n\t-ms-filter: \"progid:DXImageTransform.Microsoft.Alpha(Opacity=20)\";\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_calendar td:hover,\r\n.xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_time_box >div >div:hover {\r\n\tcolor: #fff !important;\r\n\tbackground: #ff8000 !important;\r\n\tbox-shadow: none !important;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_current.xdsoft_disabled:hover,\r\n.xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_time_box>div>div.xdsoft_current.xdsoft_disabled:hover {\r\n\tbackground: #33aaff !important;\r\n\tbox-shadow: #178fe5 0 1px 3px 0 inset !important;\r\n\tcolor: #fff !important;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_disabled:hover,\r\n.xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_time_box >div >div.xdsoft_disabled:hover {\r\n\tcolor: inherit\t!important;\r\n\tbackground: inherit !important;\r\n\tbox-shadow: inherit !important;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_calendar th {\r\n\tfont-weight: 700;\r\n\ttext-align: center;\r\n\tcolor: #999;\r\n\tcursor: default;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_copyright {\r\n\tcolor: #ccc !important;\r\n\tfont-size: 10px;\r\n\tclear: both;\r\n\tfloat: none;\r\n\tmargin-left: 8px;\r\n}\r\n\r\n.xdsoft_datetimepicker .xdsoft_copyright a { color: #eee !important }\r\n.xdsoft_datetimepicker .xdsoft_copyright a:hover { color: #aaa !important }\r\n\r\n.xdsoft_time_box {\r\n\tposition: relative;\r\n\tborder: 1px solid #ccc;\r\n}\r\n.xdsoft_scrollbar >.xdsoft_scroller {\r\n\tbackground: #ccc !important;\r\n\theight: 20px;\r\n\tborder-radius: 3px;\r\n}\r\n.xdsoft_scrollbar {\r\n\tposition: absolute;\r\n\twidth: 7px;\r\n\tright: 0;\r\n\ttop: 0;\r\n\tbottom: 0;\r\n\tcursor: pointer;\r\n}\r\n.xdsoft_datetimepicker.xdsoft_rtl .xdsoft_scrollbar {\r\n\tleft: 0;\r\n\tright: auto;\r\n}\r\n.xdsoft_scroller_box {\r\n\tposition: relative;\r\n}\r\n\r\n.xdsoft_datetimepicker.xdsoft_dark {\r\n\tbox-shadow: 0 5px 15px -5px rgba(255, 255, 255, 0.506);\r\n\tbackground: #000;\r\n\tborder-bottom: 1px solid #444;\r\n\tborder-left: 1px solid #333;\r\n\tborder-right: 1px solid #333;\r\n\tborder-top: 1px solid #333;\r\n\tcolor: #ccc;\r\n}\r\n\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_timepicker .xdsoft_time_box {\r\n\tborder-bottom: 1px solid #222;\r\n}\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_timepicker .xdsoft_time_box >div >div {\r\n\tbackground: #0a0a0a;\r\n\tborder-top: 1px solid #222;\r\n\tcolor: #999;\r\n}\r\n\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_label {\r\n\tbackground-color: #000;\r\n}\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_label > .xdsoft_select {\r\n\tborder: 1px solid #333;\r\n\tbackground: #000;\r\n}\r\n\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_label > .xdsoft_select > div > .xdsoft_option:hover {\r\n\tcolor: #000;\r\n\tbackground: #007fff;\r\n}\r\n\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_label > .xdsoft_select > div > .xdsoft_option.xdsoft_current {\r\n\tbackground: #cc5500;\r\n\tbox-shadow: #b03e00 0 1px 3px 0 inset;\r\n\tcolor: #000;\r\n}\r\n\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_label i,\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_prev,\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_next,\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_today_button {\r\n\tbackground-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAAAeCAYAAADaW7vzAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QUExQUUzOTA0M0UyMTFFNDlBM0FFQTJENTExRDVBODYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QUExQUUzOTE0M0UyMTFFNDlBM0FFQTJENTExRDVBODYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpBQTFBRTM4RTQzRTIxMUU0OUEzQUVBMkQ1MTFENUE4NiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpBQTFBRTM4RjQzRTIxMUU0OUEzQUVBMkQ1MTFENUE4NiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Pp0VxGEAAAIASURBVHja7JrNSgMxEMebtgh+3MSLr1T1Xn2CHoSKB08+QmR8Bx9A8e7RixdB9CKCoNdexIugxFlJa7rNZneTbLIpM/CnNLsdMvNjM8l0mRCiQ9Ye61IKCAgZAUnH+mU3MMZaHYChBnJUDzWOFZdVfc5+ZFLbrWDeXPwbxIqrLLfaeS0hEBVGIRQCEiZoHQwtlGSByCCdYBl8g8egTTAWoKQMRBRBcZxYlhzhKegqMOageErsCHVkk3hXIFooDgHB1KkHIHVgzKB4ADJQ/A1jAFmAYhkQqA5TOBtocrKrgXwQA8gcFIuAIO8sQSA7hidvPwaQGZSaAYHOUWJABhWWw2EMIH9QagQERU4SArJXo0ZZL18uvaxejXt/Em8xjVBXmvFr1KVm/AJ10tRe2XnraNqaJvKE3KHuUbfK1E+VHB0q40/y3sdQSxY4FHWeKJCunP8UyDdqJZenT3ntVV5jIYCAh20vT7ioP8tpf6E2lfEMwERe+whV1MHjwZB7PBiCxcGQWwKZKD62lfGNnP/1poFAA60T7rF1UgcKd2id3KDeUS+oLWV8DfWAepOfq00CgQabi9zjcgJVYVD7PVzQUAUGAQkbNJTBICDhgwYTjDYD6XeW08ZKh+A4pYkzenOxXUbvZcWz7E8ykRMnIHGX1XPl+1m2vPYpL+2qdb8CDAARlKFEz/ZVkAAAAABJRU5ErkJggg==);\r\n}\r\n\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_calendar td,\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_calendar th {\r\n\tbackground: #0a0a0a;\r\n\tborder: 1px solid #222;\r\n\tcolor: #999;\r\n}\r\n\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_calendar th {\r\n\tbackground: #0e0e0e;\r\n}\r\n\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_calendar td.xdsoft_today {\r\n\tcolor: #cc5500;\r\n}\r\n\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_calendar td.xdsoft_highlighted_default {\r\n\tbackground: #ffe9d2;\r\n\tbox-shadow: #ffb871 0 1px 4px 0 inset;\r\n\tcolor:#000;\r\n}\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_calendar td.xdsoft_highlighted_mint {\r\n\tbackground: #c1ffc9;\r\n\tbox-shadow: #00dd1c 0 1px 4px 0 inset;\r\n\tcolor:#000;\r\n}\r\n\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_calendar td.xdsoft_default,\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_calendar td.xdsoft_current,\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_timepicker .xdsoft_time_box >div >div.xdsoft_current {\r\n\tbackground: #cc5500;\r\n\tbox-shadow: #b03e00 0 1px 3px 0 inset;\r\n\tcolor: #000;\r\n}\r\n\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_calendar td:hover,\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_timepicker .xdsoft_time_box >div >div:hover {\r\n\tcolor: #000 !important;\r\n\tbackground: #007fff !important;\r\n}\r\n\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_calendar th {\r\n\tcolor: #666;\r\n}\r\n\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_copyright { color: #333 !important }\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_copyright a { color: #111 !important }\r\n.xdsoft_datetimepicker.xdsoft_dark .xdsoft_copyright a:hover { color: #555 !important }\r\n\r\n.xdsoft_dark .xdsoft_time_box {\r\n\tborder: 1px solid #333;\r\n}\r\n\r\n.xdsoft_dark .xdsoft_scrollbar >.xdsoft_scroller {\r\n\tbackground: #333 !important;\r\n}\r\n.xdsoft_datetimepicker .xdsoft_save_selected {\r\n    display: block;\r\n    border: 1px solid #dddddd !important;\r\n    margin-top: 5px;\r\n    width: 100%;\r\n    color: #454551;\r\n    font-size: 13px;\r\n}\r\n.xdsoft_datetimepicker .blue-gradient-button {\r\n\tfont-family: \"museo-sans\", \"Book Antiqua\", sans-serif;\r\n\tfont-size: 12px;\r\n\tfont-weight: 300;\r\n\tcolor: #82878c;\r\n\theight: 28px;\r\n\tposition: relative;\r\n\tpadding: 4px 17px 4px 33px;\r\n\tborder: 1px solid #d7d8da;\r\n\tbackground: -moz-linear-gradient(top, #fff 0%, #f4f8fa 73%);\r\n\t/* FF3.6+ */\r\n\tbackground: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #fff), color-stop(73%, #f4f8fa));\r\n\t/* Chrome,Safari4+ */\r\n\tbackground: -webkit-linear-gradient(top, #fff 0%, #f4f8fa 73%);\r\n\t/* Chrome10+,Safari5.1+ */\r\n\tbackground: -o-linear-gradient(top, #fff 0%, #f4f8fa 73%);\r\n\t/* Opera 11.10+ */\r\n\tbackground: -ms-linear-gradient(top, #fff 0%, #f4f8fa 73%);\r\n\t/* IE10+ */\r\n\tbackground: linear-gradient(to bottom, #fff 0%, #f4f8fa 73%);\r\n\t/* W3C */\r\n\tfilter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fff', endColorstr='#f4f8fa',GradientType=0 );\r\n/* IE6-9 */\r\n}\r\n.xdsoft_datetimepicker .blue-gradient-button:hover, .xdsoft_datetimepicker .blue-gradient-button:focus, .xdsoft_datetimepicker .blue-gradient-button:hover span, .xdsoft_datetimepicker .blue-gradient-button:focus span {\r\n  color: #454551;\r\n  background: -moz-linear-gradient(top, #f4f8fa 0%, #FFF 73%);\r\n  /* FF3.6+ */\r\n  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #f4f8fa), color-stop(73%, #FFF));\r\n  /* Chrome,Safari4+ */\r\n  background: -webkit-linear-gradient(top, #f4f8fa 0%, #FFF 73%);\r\n  /* Chrome10+,Safari5.1+ */\r\n  background: -o-linear-gradient(top, #f4f8fa 0%, #FFF 73%);\r\n  /* Opera 11.10+ */\r\n  background: -ms-linear-gradient(top, #f4f8fa 0%, #FFF 73%);\r\n  /* IE10+ */\r\n  background: linear-gradient(to bottom, #f4f8fa 0%, #FFF 73%);\r\n  /* W3C */\r\n  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f4f8fa', endColorstr='#FFF',GradientType=0 );\r\n  /* IE6-9 */\r\n}\r\n", ""]);
	
	// exports


/***/ },
/* 52 */
/*!*******************************************************************************!*\
  !*** F:/item/zbw/~/jquery-datetimepicker/build/jquery.datetimepicker.full.js ***!
  \*******************************************************************************/
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2015
	 * @version 1.3.3
	 *
	 * Date formatter utility library that allows formatting date/time variables or Date objects using PHP DateTime format.
	 * @see http://php.net/manual/en/function.date.php
	 *
	 * For more JQuery plugins visit http://plugins.krajee.com
	 * For more Yii related demos visit http://demos.krajee.com
	 */
	var DateFormatter;
	(function () {
	    "use strict";
	
	    var _compare, _lpad, _extend, defaultSettings, DAY, HOUR;
	    DAY = 1000 * 60 * 60 * 24;
	    HOUR = 3600;
	
	    _compare = function (str1, str2) {
	        return typeof(str1) === 'string' && typeof(str2) === 'string' && str1.toLowerCase() === str2.toLowerCase();
	    };
	    _lpad = function (value, length, char) {
	        var chr = char || '0', val = value.toString();
	        return val.length < length ? _lpad(chr + val, length) : val;
	    };
	    _extend = function (out) {
	        var i, obj;
	        out = out || {};
	        for (i = 1; i < arguments.length; i++) {
	            obj = arguments[i];
	            if (!obj) {
	                continue;
	            }
	            for (var key in obj) {
	                if (obj.hasOwnProperty(key)) {
	                    if (typeof obj[key] === 'object') {
	                        _extend(out[key], obj[key]);
	                    } else {
	                        out[key] = obj[key];
	                    }
	                }
	            }
	        }
	        return out;
	    };
	    defaultSettings = {
	        dateSettings: {
	            days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
	            daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
	            months: [
	                'January', 'February', 'March', 'April', 'May', 'June', 'July',
	                'August', 'September', 'October', 'November', 'December'
	            ],
	            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
	            meridiem: ['AM', 'PM'],
	            ordinal: function (number) {
	                var n = number % 10, suffixes = {1: 'st', 2: 'nd', 3: 'rd'};
	                return Math.floor(number % 100 / 10) === 1 || !suffixes[n] ? 'th' : suffixes[n];
	            }
	        },
	        separators: /[ \-+\/\.T:@]/g,
	        validParts: /[dDjlNSwzWFmMntLoYyaABgGhHisueTIOPZcrU]/g,
	        intParts: /[djwNzmnyYhHgGis]/g,
	        tzParts: /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
	        tzClip: /[^-+\dA-Z]/g
	    };
	
	    DateFormatter = function (options) {
	        var self = this, config = _extend(defaultSettings, options);
	        self.dateSettings = config.dateSettings;
	        self.separators = config.separators;
	        self.validParts = config.validParts;
	        self.intParts = config.intParts;
	        self.tzParts = config.tzParts;
	        self.tzClip = config.tzClip;
	    };
	
	    DateFormatter.prototype = {
	        constructor: DateFormatter,
	        parseDate: function (vDate, vFormat) {
	            var self = this, vFormatParts, vDateParts, i, vDateFlag = false, vTimeFlag = false, vDatePart, iDatePart,
	                vSettings = self.dateSettings, vMonth, vMeriIndex, vMeriOffset, len, mer,
	                out = {date: null, year: null, month: null, day: null, hour: 0, min: 0, sec: 0};
	            if (!vDate) {
	                return undefined;
	            }
	            if (vDate instanceof Date) {
	                return vDate;
	            }
	            if (typeof vDate === 'number') {
	                return new Date(vDate);
	            }
	            if (vFormat === 'U') {
	                i = parseInt(vDate);
	                return i ? new Date(i * 1000) : vDate;
	            }
	            if (typeof vDate !== 'string') {
	                return '';
	            }
	            vFormatParts = vFormat.match(self.validParts);
	            if (!vFormatParts || vFormatParts.length === 0) {
	                throw new Error("Invalid date format definition.");
	            }
	            vDateParts = vDate.replace(self.separators, '\0').split('\0');
	            for (i = 0; i < vDateParts.length; i++) {
	                vDatePart = vDateParts[i];
	                iDatePart = parseInt(vDatePart);
	                switch (vFormatParts[i]) {
	                    case 'y':
	                    case 'Y':
	                        len = vDatePart.length;
	                        if (len === 2) {
	                            out.year = parseInt((iDatePart < 70 ? '20' : '19') + vDatePart);
	                        } else if (len === 4) {
	                            out.year = iDatePart;
	                        }
	                        vDateFlag = true;
	                        break;
	                    case 'm':
	                    case 'n':
	                    case 'M':
	                    case 'F':
	                        if (isNaN(vDatePart)) {
	                            vMonth = vSettings.monthsShort.indexOf(vDatePart);
	                            if (vMonth > -1) {
	                                out.month = vMonth + 1;
	                            }
	                            vMonth = vSettings.months.indexOf(vDatePart);
	                            if (vMonth > -1) {
	                                out.month = vMonth + 1;
	                            }
	                        } else {
	                            if (iDatePart >= 1 && iDatePart <= 12) {
	                                out.month = iDatePart;
	                            }
	                        }
	                        vDateFlag = true;
	                        break;
	                    case 'd':
	                    case 'j':
	                        if (iDatePart >= 1 && iDatePart <= 31) {
	                            out.day = iDatePart;
	                        }
	                        vDateFlag = true;
	                        break;
	                    case 'g':
	                    case 'h':
	                        vMeriIndex = (vFormatParts.indexOf('a') > -1) ? vFormatParts.indexOf('a') :
	                            (vFormatParts.indexOf('A') > -1) ? vFormatParts.indexOf('A') : -1;
	                        mer = vDateParts[vMeriIndex];
	                        if (vMeriIndex > -1) {
	                            vMeriOffset = _compare(mer, vSettings.meridiem[0]) ? 0 :
	                                (_compare(mer, vSettings.meridiem[1]) ? 12 : -1);
	                            if (iDatePart >= 1 && iDatePart <= 12 && vMeriOffset > -1) {
	                                out.hour = iDatePart + vMeriOffset - 1;
	                            } else if (iDatePart >= 0 && iDatePart <= 23) {
	                                out.hour = iDatePart;
	                            }
	                        } else if (iDatePart >= 0 && iDatePart <= 23) {
	                            out.hour = iDatePart;
	                        }
	                        vTimeFlag = true;
	                        break;
	                    case 'G':
	                    case 'H':
	                        if (iDatePart >= 0 && iDatePart <= 23) {
	                            out.hour = iDatePart;
	                        }
	                        vTimeFlag = true;
	                        break;
	                    case 'i':
	                        if (iDatePart >= 0 && iDatePart <= 59) {
	                            out.min = iDatePart;
	                        }
	                        vTimeFlag = true;
	                        break;
	                    case 's':
	                        if (iDatePart >= 0 && iDatePart <= 59) {
	                            out.sec = iDatePart;
	                        }
	                        vTimeFlag = true;
	                        break;
	                }
	            }
	            if (vDateFlag === true && out.year && out.month && out.day) {
	                out.date = new Date(out.year, out.month - 1, out.day, out.hour, out.min, out.sec, 0);
	            } else {
	                if (vTimeFlag !== true) {
	                    return false;
	                }
	                out.date = new Date(0, 0, 0, out.hour, out.min, out.sec, 0);
	            }
	            return out.date;
	        },
	        guessDate: function (vDateStr, vFormat) {
	            if (typeof vDateStr !== 'string') {
	                return vDateStr;
	            }
	            var self = this, vParts = vDateStr.replace(self.separators, '\0').split('\0'), vPattern = /^[djmn]/g,
	                vFormatParts = vFormat.match(self.validParts), vDate = new Date(), vDigit = 0, vYear, i, iPart, iSec;
	
	            if (!vPattern.test(vFormatParts[0])) {
	                return vDateStr;
	            }
	
	            for (i = 0; i < vParts.length; i++) {
	                vDigit = 2;
	                iPart = vParts[i];
	                iSec = parseInt(iPart.substr(0, 2));
	                switch (i) {
	                    case 0:
	                        if (vFormatParts[0] === 'm' || vFormatParts[0] === 'n') {
	                            vDate.setMonth(iSec - 1);
	                        } else {
	                            vDate.setDate(iSec);
	                        }
	                        break;
	                    case 1:
	                        if (vFormatParts[0] === 'm' || vFormatParts[0] === 'n') {
	                            vDate.setDate(iSec);
	                        } else {
	                            vDate.setMonth(iSec - 1);
	                        }
	                        break;
	                    case 2:
	                        vYear = vDate.getFullYear();
	                        if (iPart.length < 4) {
	                            vDate.setFullYear(parseInt(vYear.toString().substr(0, 4 - iPart.length) + iPart));
	                            vDigit = iPart.length;
	                        } else {
	                            vDate.setFullYear = parseInt(iPart.substr(0, 4));
	                            vDigit = 4;
	                        }
	                        break;
	                    case 3:
	                        vDate.setHours(iSec);
	                        break;
	                    case 4:
	                        vDate.setMinutes(iSec);
	                        break;
	                    case 5:
	                        vDate.setSeconds(iSec);
	                        break;
	                }
	                if (iPart.substr(vDigit).length > 0) {
	                    vParts.splice(i + 1, 0, iPart.substr(vDigit));
	                }
	            }
	            return vDate;
	        },
	        parseFormat: function (vChar, vDate) {
	            var self = this, vSettings = self.dateSettings, fmt, backspace = /\\?(.?)/gi, doFormat = function (t, s) {
	                return fmt[t] ? fmt[t]() : s;
	            };
	            fmt = {
	                /////////
	                // DAY //
	                /////////
	                /**
	                 * Day of month with leading 0: `01..31`
	                 * @return {string}
	                 */
	                d: function () {
	                    return _lpad(fmt.j(), 2);
	                },
	                /**
	                 * Shorthand day name: `Mon...Sun`
	                 * @return {string}
	                 */
	                D: function () {
	                    return vSettings.daysShort[fmt.w()];
	                },
	                /**
	                 * Day of month: `1..31`
	                 * @return {number}
	                 */
	                j: function () {
	                    return vDate.getDate();
	                },
	                /**
	                 * Full day name: `Monday...Sunday`
	                 * @return {number}
	                 */
	                l: function () {
	                    return vSettings.days[fmt.w()];
	                },
	                /**
	                 * ISO-8601 day of week: `1[Mon]..7[Sun]`
	                 * @return {number}
	                 */
	                N: function () {
	                    return fmt.w() || 7;
	                },
	                /**
	                 * Day of week: `0[Sun]..6[Sat]`
	                 * @return {number}
	                 */
	                w: function () {
	                    return vDate.getDay();
	                },
	                /**
	                 * Day of year: `0..365`
	                 * @return {number}
	                 */
	                z: function () {
	                    var a = new Date(fmt.Y(), fmt.n() - 1, fmt.j()), b = new Date(fmt.Y(), 0, 1);
	                    return Math.round((a - b) / DAY);
	                },
	
	                //////////
	                // WEEK //
	                //////////
	                /**
	                 * ISO-8601 week number
	                 * @return {number}
	                 */
	                W: function () {
	                    var a = new Date(fmt.Y(), fmt.n() - 1, fmt.j() - fmt.N() + 3), b = new Date(a.getFullYear(), 0, 4);
	                    return _lpad(1 + Math.round((a - b) / DAY / 7), 2);
	                },
	
	                ///////////
	                // MONTH //
	                ///////////
	                /**
	                 * Full month name: `January...December`
	                 * @return {string}
	                 */
	                F: function () {
	                    return vSettings.months[vDate.getMonth()];
	                },
	                /**
	                 * Month w/leading 0: `01..12`
	                 * @return {string}
	                 */
	                m: function () {
	                    return _lpad(fmt.n(), 2);
	                },
	                /**
	                 * Shorthand month name; `Jan...Dec`
	                 * @return {string}
	                 */
	                M: function () {
	                    return vSettings.monthsShort[vDate.getMonth()];
	                },
	                /**
	                 * Month: `1...12`
	                 * @return {number}
	                 */
	                n: function () {
	                    return vDate.getMonth() + 1;
	                },
	                /**
	                 * Days in month: `28...31`
	                 * @return {number}
	                 */
	                t: function () {
	                    return (new Date(fmt.Y(), fmt.n(), 0)).getDate();
	                },
	
	                //////////
	                // YEAR //
	                //////////
	                /**
	                 * Is leap year? `0 or 1`
	                 * @return {number}
	                 */
	                L: function () {
	                    var Y = fmt.Y();
	                    return (Y % 4 === 0 && Y % 100 !== 0 || Y % 400 === 0) ? 1 : 0;
	                },
	                /**
	                 * ISO-8601 year
	                 * @return {number}
	                 */
	                o: function () {
	                    var n = fmt.n(), W = fmt.W(), Y = fmt.Y();
	                    return Y + (n === 12 && W < 9 ? 1 : n === 1 && W > 9 ? -1 : 0);
	                },
	                /**
	                 * Full year: `e.g. 1980...2010`
	                 * @return {number}
	                 */
	                Y: function () {
	                    return vDate.getFullYear();
	                },
	                /**
	                 * Last two digits of year: `00...99`
	                 * @return {string}
	                 */
	                y: function () {
	                    return fmt.Y().toString().slice(-2);
	                },
	
	                //////////
	                // TIME //
	                //////////
	                /**
	                 * Meridian lower: `am or pm`
	                 * @return {string}
	                 */
	                a: function () {
	                    return fmt.A().toLowerCase();
	                },
	                /**
	                 * Meridian upper: `AM or PM`
	                 * @return {string}
	                 */
	                A: function () {
	                    var n = fmt.G() < 12 ? 0 : 1;
	                    return vSettings.meridiem[n];
	                },
	                /**
	                 * Swatch Internet time: `000..999`
	                 * @return {string}
	                 */
	                B: function () {
	                    var H = vDate.getUTCHours() * HOUR, i = vDate.getUTCMinutes() * 60, s = vDate.getUTCSeconds();
	                    return _lpad(Math.floor((H + i + s + HOUR) / 86.4) % 1000, 3);
	                },
	                /**
	                 * 12-Hours: `1..12`
	                 * @return {number}
	                 */
	                g: function () {
	                    return fmt.G() % 12 || 12;
	                },
	                /**
	                 * 24-Hours: `0..23`
	                 * @return {number}
	                 */
	                G: function () {
	                    return vDate.getHours();
	                },
	                /**
	                 * 12-Hours with leading 0: `01..12`
	                 * @return {string}
	                 */
	                h: function () {
	                    return _lpad(fmt.g(), 2);
	                },
	                /**
	                 * 24-Hours w/leading 0: `00..23`
	                 * @return {string}
	                 */
	                H: function () {
	                    return _lpad(fmt.G(), 2);
	                },
	                /**
	                 * Minutes w/leading 0: `00..59`
	                 * @return {string}
	                 */
	                i: function () {
	                    return _lpad(vDate.getMinutes(), 2);
	                },
	                /**
	                 * Seconds w/leading 0: `00..59`
	                 * @return {string}
	                 */
	                s: function () {
	                    return _lpad(vDate.getSeconds(), 2);
	                },
	                /**
	                 * Microseconds: `000000-999000`
	                 * @return {string}
	                 */
	                u: function () {
	                    return _lpad(vDate.getMilliseconds() * 1000, 6);
	                },
	
	                //////////////
	                // TIMEZONE //
	                //////////////
	                /**
	                 * Timezone identifier: `e.g. Atlantic/Azores, ...`
	                 * @return {string}
	                 */
	                e: function () {
	                    var str = /\((.*)\)/.exec(String(vDate))[1];
	                    return str || 'Coordinated Universal Time';
	                },
	                /**
	                 * Timezone abbreviation: `e.g. EST, MDT, ...`
	                 * @return {string}
	                 */
	                T: function () {
	                    var str = (String(vDate).match(self.tzParts) || [""]).pop().replace(self.tzClip, "");
	                    return str || 'UTC';
	                },
	                /**
	                 * DST observed? `0 or 1`
	                 * @return {number}
	                 */
	                I: function () {
	                    var a = new Date(fmt.Y(), 0), c = Date.UTC(fmt.Y(), 0),
	                        b = new Date(fmt.Y(), 6), d = Date.UTC(fmt.Y(), 6);
	                    return ((a - c) !== (b - d)) ? 1 : 0;
	                },
	                /**
	                 * Difference to GMT in hour format: `e.g. +0200`
	                 * @return {string}
	                 */
	                O: function () {
	                    var tzo = vDate.getTimezoneOffset(), a = Math.abs(tzo);
	                    return (tzo > 0 ? '-' : '+') + _lpad(Math.floor(a / 60) * 100 + a % 60, 4);
	                },
	                /**
	                 * Difference to GMT with colon: `e.g. +02:00`
	                 * @return {string}
	                 */
	                P: function () {
	                    var O = fmt.O();
	                    return (O.substr(0, 3) + ':' + O.substr(3, 2));
	                },
	                /**
	                 * Timezone offset in seconds: `-43200...50400`
	                 * @return {number}
	                 */
	                Z: function () {
	                    return -vDate.getTimezoneOffset() * 60;
	                },
	
	                ////////////////////
	                // FULL DATE TIME //
	                ////////////////////
	                /**
	                 * ISO-8601 date
	                 * @return {string}
	                 */
	                c: function () {
	                    return 'Y-m-d\\TH:i:sP'.replace(backspace, doFormat);
	                },
	                /**
	                 * RFC 2822 date
	                 * @return {string}
	                 */
	                r: function () {
	                    return 'D, d M Y H:i:s O'.replace(backspace, doFormat);
	                },
	                /**
	                 * Seconds since UNIX epoch
	                 * @return {number}
	                 */
	                U: function () {
	                    return vDate.getTime() / 1000 || 0;
	                }
	            };
	            return doFormat(vChar, vChar);
	        },
	        formatDate: function (vDate, vFormat) {
	            var self = this, i, n, len, str, vChar, vDateStr = '';
	            if (typeof vDate === 'string') {
	                vDate = self.parseDate(vDate, vFormat);
	                if (vDate === false) {
	                    return false;
	                }
	            }
	            if (vDate instanceof Date) {
	                len = vFormat.length;
	                for (i = 0; i < len; i++) {
	                    vChar = vFormat.charAt(i);
	                    if (vChar === 'S') {
	                        continue;
	                    }
	                    str = self.parseFormat(vChar, vDate);
	                    if (i !== (len - 1) && self.intParts.test(vChar) && vFormat.charAt(i + 1) === 'S') {
	                        n = parseInt(str);
	                        str += self.dateSettings.ordinal(n);
	                    }
	                    vDateStr += str;
	                }
	                return vDateStr;
	            }
	            return '';
	        }
	    };
	})();/**
	 * @preserve jQuery DateTimePicker plugin v2.5.4
	 * @homepage http://xdsoft.net/jqplugins/datetimepicker/
	 * @author Chupurnov Valeriy (<chupurnov@gmail.com>)
	 */
	/*global DateFormatter, document,window,jQuery,setTimeout,clearTimeout,HighlightedDate,getCurrentValue*/
	;(function (factory) {
		if ( true ) {
			// AMD. Register as an anonymous module.
			!(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ 1), __webpack_require__(/*! jquery-mousewheel */ 53)], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
		} else if (typeof exports === 'object') {
			// Node/CommonJS style for Browserify
			module.exports = factory;
		} else {
			// Browser globals
			factory(jQuery);
		}
	}(function ($) {
		'use strict';
		var default_options  = {
			i18n: {
				ar: { // Arabic
					months: [
						"كانون الثاني", "شباط", "آذار", "نيسان", "مايو", "حزيران", "تموز", "آب", "أيلول", "تشرين الأول", "تشرين الثاني", "كانون الأول"
					],
					dayOfWeekShort: [
						"ن", "ث", "ع", "خ", "ج", "س", "ح"
					],
					dayOfWeek: ["الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت", "الأحد"]
				},
				ro: { // Romanian
					months: [
						"Ianuarie", "Februarie", "Martie", "Aprilie", "Mai", "Iunie", "Iulie", "August", "Septembrie", "Octombrie", "Noiembrie", "Decembrie"
					],
					dayOfWeekShort: [
						"Du", "Lu", "Ma", "Mi", "Jo", "Vi", "Sâ"
					],
					dayOfWeek: ["Duminică", "Luni", "Marţi", "Miercuri", "Joi", "Vineri", "Sâmbătă"]
				},
				id: { // Indonesian
					months: [
						"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"
					],
					dayOfWeekShort: [
						"Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"
					],
					dayOfWeek: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]
				},
				is: { // Icelandic
					months: [
						"Janúar", "Febrúar", "Mars", "Apríl", "Maí", "Júní", "Júlí", "Ágúst", "September", "Október", "Nóvember", "Desember"
					],
					dayOfWeekShort: [
						"Sun", "Mán", "Þrið", "Mið", "Fim", "Fös", "Lau"
					],
					dayOfWeek: ["Sunnudagur", "Mánudagur", "Þriðjudagur", "Miðvikudagur", "Fimmtudagur", "Föstudagur", "Laugardagur"]
				},
				bg: { // Bulgarian
					months: [
						"Януари", "Февруари", "Март", "Април", "Май", "Юни", "Юли", "Август", "Септември", "Октомври", "Ноември", "Декември"
					],
					dayOfWeekShort: [
						"Нд", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"
					],
					dayOfWeek: ["Неделя", "Понеделник", "Вторник", "Сряда", "Четвъртък", "Петък", "Събота"]
				},
				fa: { // Persian/Farsi
					months: [
						'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
					],
					dayOfWeekShort: [
						'یکشنبه', 'دوشنبه', 'سه شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه'
					],
					dayOfWeek: ["یک‌شنبه", "دوشنبه", "سه‌شنبه", "چهارشنبه", "پنج‌شنبه", "جمعه", "شنبه", "یک‌شنبه"]
				},
				ru: { // Russian
					months: [
						'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
					],
					dayOfWeekShort: [
						"Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"
					],
					dayOfWeek: ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"]
				},
				uk: { // Ukrainian
					months: [
						'Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Вересень', 'Жовтень', 'Листопад', 'Грудень'
					],
					dayOfWeekShort: [
						"Ндл", "Пнд", "Втр", "Срд", "Чтв", "Птн", "Сбт"
					],
					dayOfWeek: ["Неділя", "Понеділок", "Вівторок", "Середа", "Четвер", "П'ятниця", "Субота"]
				},
				en: { // English
					months: [
						"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
					],
					dayOfWeekShort: [
						"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"
					],
					dayOfWeek: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]
				},
				el: { // Ελληνικά
					months: [
						"Ιανουάριος", "Φεβρουάριος", "Μάρτιος", "Απρίλιος", "Μάιος", "Ιούνιος", "Ιούλιος", "Αύγουστος", "Σεπτέμβριος", "Οκτώβριος", "Νοέμβριος", "Δεκέμβριος"
					],
					dayOfWeekShort: [
						"Κυρ", "Δευ", "Τρι", "Τετ", "Πεμ", "Παρ", "Σαβ"
					],
					dayOfWeek: ["Κυριακή", "Δευτέρα", "Τρίτη", "Τετάρτη", "Πέμπτη", "Παρασκευή", "Σάββατο"]
				},
				de: { // German
					months: [
						'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'
					],
					dayOfWeekShort: [
						"So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"
					],
					dayOfWeek: ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"]
				},
				nl: { // Dutch
					months: [
						"januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december"
					],
					dayOfWeekShort: [
						"zo", "ma", "di", "wo", "do", "vr", "za"
					],
					dayOfWeek: ["zondag", "maandag", "dinsdag", "woensdag", "donderdag", "vrijdag", "zaterdag"]
				},
				tr: { // Turkish
					months: [
						"Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"
					],
					dayOfWeekShort: [
						"Paz", "Pts", "Sal", "Çar", "Per", "Cum", "Cts"
					],
					dayOfWeek: ["Pazar", "Pazartesi", "Salı", "Çarşamba", "Perşembe", "Cuma", "Cumartesi"]
				},
				fr: { //French
					months: [
						"Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"
					],
					dayOfWeekShort: [
						"Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"
					],
					dayOfWeek: ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"]
				},
				es: { // Spanish
					months: [
						"Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
					],
					dayOfWeekShort: [
						"Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"
					],
					dayOfWeek: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"]
				},
				th: { // Thai
					months: [
						'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
					],
					dayOfWeekShort: [
						'อา.', 'จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.'
					],
					dayOfWeek: ["อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัส", "ศุกร์", "เสาร์", "อาทิตย์"]
				},
				pl: { // Polish
					months: [
						"styczeń", "luty", "marzec", "kwiecień", "maj", "czerwiec", "lipiec", "sierpień", "wrzesień", "październik", "listopad", "grudzień"
					],
					dayOfWeekShort: [
						"nd", "pn", "wt", "śr", "cz", "pt", "sb"
					],
					dayOfWeek: ["niedziela", "poniedziałek", "wtorek", "środa", "czwartek", "piątek", "sobota"]
				},
				pt: { // Portuguese
					months: [
						"Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
					],
					dayOfWeekShort: [
						"Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"
					],
					dayOfWeek: ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"]
				},
				ch: { // Simplified Chinese
					months: [
						"一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"
					],
					dayOfWeekShort: [
						"日", "一", "二", "三", "四", "五", "六"
					]
				},
				se: { // Swedish
					months: [
						"Januari", "Februari", "Mars", "April", "Maj", "Juni", "Juli", "Augusti", "September",  "Oktober", "November", "December"
					],
					dayOfWeekShort: [
						"Sön", "Mån", "Tis", "Ons", "Tor", "Fre", "Lör"
					]
				},
				kr: { // Korean
					months: [
						"1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"
					],
					dayOfWeekShort: [
						"일", "월", "화", "수", "목", "금", "토"
					],
					dayOfWeek: ["일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일"]
				},
				it: { // Italian
					months: [
						"Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"
					],
					dayOfWeekShort: [
						"Dom", "Lun", "Mar", "Mer", "Gio", "Ven", "Sab"
					],
					dayOfWeek: ["Domenica", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato"]
				},
				da: { // Dansk
					months: [
						"January", "Februar", "Marts", "April", "Maj", "Juni", "July", "August", "September", "Oktober", "November", "December"
					],
					dayOfWeekShort: [
						"Søn", "Man", "Tir", "Ons", "Tor", "Fre", "Lør"
					],
					dayOfWeek: ["søndag", "mandag", "tirsdag", "onsdag", "torsdag", "fredag", "lørdag"]
				},
				no: { // Norwegian
					months: [
						"Januar", "Februar", "Mars", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Desember"
					],
					dayOfWeekShort: [
						"Søn", "Man", "Tir", "Ons", "Tor", "Fre", "Lør"
					],
					dayOfWeek: ['Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag']
				},
				ja: { // Japanese
					months: [
						"1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"
					],
					dayOfWeekShort: [
						"日", "月", "火", "水", "木", "金", "土"
					],
					dayOfWeek: ["日曜", "月曜", "火曜", "水曜", "木曜", "金曜", "土曜"]
				},
				vi: { // Vietnamese
					months: [
						"Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
					],
					dayOfWeekShort: [
						"CN", "T2", "T3", "T4", "T5", "T6", "T7"
					],
					dayOfWeek: ["Chủ nhật", "Thứ hai", "Thứ ba", "Thứ tư", "Thứ năm", "Thứ sáu", "Thứ bảy"]
				},
				sl: { // Slovenščina
					months: [
						"Januar", "Februar", "Marec", "April", "Maj", "Junij", "Julij", "Avgust", "September", "Oktober", "November", "December"
					],
					dayOfWeekShort: [
						"Ned", "Pon", "Tor", "Sre", "Čet", "Pet", "Sob"
					],
					dayOfWeek: ["Nedelja", "Ponedeljek", "Torek", "Sreda", "Četrtek", "Petek", "Sobota"]
				},
				cs: { // Čeština
					months: [
						"Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec"
					],
					dayOfWeekShort: [
						"Ne", "Po", "Út", "St", "Čt", "Pá", "So"
					]
				},
				hu: { // Hungarian
					months: [
						"Január", "Február", "Március", "Április", "Május", "Június", "Július", "Augusztus", "Szeptember", "Október", "November", "December"
					],
					dayOfWeekShort: [
						"Va", "Hé", "Ke", "Sze", "Cs", "Pé", "Szo"
					],
					dayOfWeek: ["vasárnap", "hétfő", "kedd", "szerda", "csütörtök", "péntek", "szombat"]
				},
				az: { //Azerbaijanian (Azeri)
					months: [
						"Yanvar", "Fevral", "Mart", "Aprel", "May", "Iyun", "Iyul", "Avqust", "Sentyabr", "Oktyabr", "Noyabr", "Dekabr"
					],
					dayOfWeekShort: [
						"B", "Be", "Ça", "Ç", "Ca", "C", "Ş"
					],
					dayOfWeek: ["Bazar", "Bazar ertəsi", "Çərşənbə axşamı", "Çərşənbə", "Cümə axşamı", "Cümə", "Şənbə"]
				},
				bs: { //Bosanski
					months: [
						"Januar", "Februar", "Mart", "April", "Maj", "Jun", "Jul", "Avgust", "Septembar", "Oktobar", "Novembar", "Decembar"
					],
					dayOfWeekShort: [
						"Ned", "Pon", "Uto", "Sri", "Čet", "Pet", "Sub"
					],
					dayOfWeek: ["Nedjelja","Ponedjeljak", "Utorak", "Srijeda", "Četvrtak", "Petak", "Subota"]
				},
				ca: { //Català
					months: [
						"Gener", "Febrer", "Març", "Abril", "Maig", "Juny", "Juliol", "Agost", "Setembre", "Octubre", "Novembre", "Desembre"
					],
					dayOfWeekShort: [
						"Dg", "Dl", "Dt", "Dc", "Dj", "Dv", "Ds"
					],
					dayOfWeek: ["Diumenge", "Dilluns", "Dimarts", "Dimecres", "Dijous", "Divendres", "Dissabte"]
				},
				'en-GB': { //English (British)
					months: [
						"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
					],
					dayOfWeekShort: [
						"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"
					],
					dayOfWeek: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]
				},
				et: { //"Eesti"
					months: [
						"Jaanuar", "Veebruar", "Märts", "Aprill", "Mai", "Juuni", "Juuli", "August", "September", "Oktoober", "November", "Detsember"
					],
					dayOfWeekShort: [
						"P", "E", "T", "K", "N", "R", "L"
					],
					dayOfWeek: ["Pühapäev", "Esmaspäev", "Teisipäev", "Kolmapäev", "Neljapäev", "Reede", "Laupäev"]
				},
				eu: { //Euskara
					months: [
						"Urtarrila", "Otsaila", "Martxoa", "Apirila", "Maiatza", "Ekaina", "Uztaila", "Abuztua", "Iraila", "Urria", "Azaroa", "Abendua"
					],
					dayOfWeekShort: [
						"Ig.", "Al.", "Ar.", "Az.", "Og.", "Or.", "La."
					],
					dayOfWeek: ['Igandea', 'Astelehena', 'Asteartea', 'Asteazkena', 'Osteguna', 'Ostirala', 'Larunbata']
				},
				fi: { //Finnish (Suomi)
					months: [
						"Tammikuu", "Helmikuu", "Maaliskuu", "Huhtikuu", "Toukokuu", "Kesäkuu", "Heinäkuu", "Elokuu", "Syyskuu", "Lokakuu", "Marraskuu", "Joulukuu"
					],
					dayOfWeekShort: [
						"Su", "Ma", "Ti", "Ke", "To", "Pe", "La"
					],
					dayOfWeek: ["sunnuntai", "maanantai", "tiistai", "keskiviikko", "torstai", "perjantai", "lauantai"]
				},
				gl: { //Galego
					months: [
						"Xan", "Feb", "Maz", "Abr", "Mai", "Xun", "Xul", "Ago", "Set", "Out", "Nov", "Dec"
					],
					dayOfWeekShort: [
						"Dom", "Lun", "Mar", "Mer", "Xov", "Ven", "Sab"
					],
					dayOfWeek: ["Domingo", "Luns", "Martes", "Mércores", "Xoves", "Venres", "Sábado"]
				},
				hr: { //Hrvatski
					months: [
						"Siječanj", "Veljača", "Ožujak", "Travanj", "Svibanj", "Lipanj", "Srpanj", "Kolovoz", "Rujan", "Listopad", "Studeni", "Prosinac"
					],
					dayOfWeekShort: [
						"Ned", "Pon", "Uto", "Sri", "Čet", "Pet", "Sub"
					],
					dayOfWeek: ["Nedjelja", "Ponedjeljak", "Utorak", "Srijeda", "Četvrtak", "Petak", "Subota"]
				},
				ko: { //Korean (한국어)
					months: [
						"1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"
					],
					dayOfWeekShort: [
						"일", "월", "화", "수", "목", "금", "토"
					],
					dayOfWeek: ["일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일"]
				},
				lt: { //Lithuanian (lietuvių)
					months: [
						"Sausio", "Vasario", "Kovo", "Balandžio", "Gegužės", "Birželio", "Liepos", "Rugpjūčio", "Rugsėjo", "Spalio", "Lapkričio", "Gruodžio"
					],
					dayOfWeekShort: [
						"Sek", "Pir", "Ant", "Tre", "Ket", "Pen", "Šeš"
					],
					dayOfWeek: ["Sekmadienis", "Pirmadienis", "Antradienis", "Trečiadienis", "Ketvirtadienis", "Penktadienis", "Šeštadienis"]
				},
				lv: { //Latvian (Latviešu)
					months: [
						"Janvāris", "Februāris", "Marts", "Aprīlis ", "Maijs", "Jūnijs", "Jūlijs", "Augusts", "Septembris", "Oktobris", "Novembris", "Decembris"
					],
					dayOfWeekShort: [
						"Sv", "Pr", "Ot", "Tr", "Ct", "Pk", "St"
					],
					dayOfWeek: ["Svētdiena", "Pirmdiena", "Otrdiena", "Trešdiena", "Ceturtdiena", "Piektdiena", "Sestdiena"]
				},
				mk: { //Macedonian (Македонски)
					months: [
						"јануари", "февруари", "март", "април", "мај", "јуни", "јули", "август", "септември", "октомври", "ноември", "декември"
					],
					dayOfWeekShort: [
						"нед", "пон", "вто", "сре", "чет", "пет", "саб"
					],
					dayOfWeek: ["Недела", "Понеделник", "Вторник", "Среда", "Четврток", "Петок", "Сабота"]
				},
				mn: { //Mongolian (Монгол)
					months: [
						"1-р сар", "2-р сар", "3-р сар", "4-р сар", "5-р сар", "6-р сар", "7-р сар", "8-р сар", "9-р сар", "10-р сар", "11-р сар", "12-р сар"
					],
					dayOfWeekShort: [
						"Дав", "Мяг", "Лха", "Пүр", "Бсн", "Бям", "Ням"
					],
					dayOfWeek: ["Даваа", "Мягмар", "Лхагва", "Пүрэв", "Баасан", "Бямба", "Ням"]
				},
				'pt-BR': { //Português(Brasil)
					months: [
						"Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
					],
					dayOfWeekShort: [
						"Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"
					],
					dayOfWeek: ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"]
				},
				sk: { //Slovenčina
					months: [
						"Január", "Február", "Marec", "Apríl", "Máj", "Jún", "Júl", "August", "September", "Október", "November", "December"
					],
					dayOfWeekShort: [
						"Ne", "Po", "Ut", "St", "Št", "Pi", "So"
					],
					dayOfWeek: ["Nedeľa", "Pondelok", "Utorok", "Streda", "Štvrtok", "Piatok", "Sobota"]
				},
				sq: { //Albanian (Shqip)
					months: [
						"Janar", "Shkurt", "Mars", "Prill", "Maj", "Qershor", "Korrik", "Gusht", "Shtator", "Tetor", "Nëntor", "Dhjetor"
					],
					dayOfWeekShort: [
						"Die", "Hën", "Mar", "Mër", "Enj", "Pre", "Shtu"
					],
					dayOfWeek: ["E Diel", "E Hënë", "E Martē", "E Mërkurë", "E Enjte", "E Premte", "E Shtunë"]
				},
				'sr-YU': { //Serbian (Srpski)
					months: [
						"Januar", "Februar", "Mart", "April", "Maj", "Jun", "Jul", "Avgust", "Septembar", "Oktobar", "Novembar", "Decembar"
					],
					dayOfWeekShort: [
						"Ned", "Pon", "Uto", "Sre", "čet", "Pet", "Sub"
					],
					dayOfWeek: ["Nedelja","Ponedeljak", "Utorak", "Sreda", "Četvrtak", "Petak", "Subota"]
				},
				sr: { //Serbian Cyrillic (Српски)
					months: [
						"јануар", "фебруар", "март", "април", "мај", "јун", "јул", "август", "септембар", "октобар", "новембар", "децембар"
					],
					dayOfWeekShort: [
						"нед", "пон", "уто", "сре", "чет", "пет", "суб"
					],
					dayOfWeek: ["Недеља","Понедељак", "Уторак", "Среда", "Четвртак", "Петак", "Субота"]
				},
				sv: { //Svenska
					months: [
						"Januari", "Februari", "Mars", "April", "Maj", "Juni", "Juli", "Augusti", "September", "Oktober", "November", "December"
					],
					dayOfWeekShort: [
						"Sön", "Mån", "Tis", "Ons", "Tor", "Fre", "Lör"
					],
					dayOfWeek: ["Söndag", "Måndag", "Tisdag", "Onsdag", "Torsdag", "Fredag", "Lördag"]
				},
				'zh-TW': { //Traditional Chinese (繁體中文)
					months: [
						"一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"
					],
					dayOfWeekShort: [
						"日", "一", "二", "三", "四", "五", "六"
					],
					dayOfWeek: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"]
				},
				zh: { //Simplified Chinese (简体中文)
					months: [
						"一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"
					],
					dayOfWeekShort: [
						"日", "一", "二", "三", "四", "五", "六"
					],
					dayOfWeek: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"]
				},
				he: { //Hebrew (עברית)
					months: [
						'ינואר', 'פברואר', 'מרץ', 'אפריל', 'מאי', 'יוני', 'יולי', 'אוגוסט', 'ספטמבר', 'אוקטובר', 'נובמבר', 'דצמבר'
					],
					dayOfWeekShort: [
						'א\'', 'ב\'', 'ג\'', 'ד\'', 'ה\'', 'ו\'', 'שבת'
					],
					dayOfWeek: ["ראשון", "שני", "שלישי", "רביעי", "חמישי", "שישי", "שבת", "ראשון"]
				},
				hy: { // Armenian
					months: [
						"Հունվար", "Փետրվար", "Մարտ", "Ապրիլ", "Մայիս", "Հունիս", "Հուլիս", "Օգոստոս", "Սեպտեմբեր", "Հոկտեմբեր", "Նոյեմբեր", "Դեկտեմբեր"
					],
					dayOfWeekShort: [
						"Կի", "Երկ", "Երք", "Չոր", "Հնգ", "Ուրբ", "Շբթ"
					],
					dayOfWeek: ["Կիրակի", "Երկուշաբթի", "Երեքշաբթի", "Չորեքշաբթի", "Հինգշաբթի", "Ուրբաթ", "Շաբաթ"]
				},
				kg: { // Kyrgyz
					months: [
						'Үчтүн айы', 'Бирдин айы', 'Жалган Куран', 'Чын Куран', 'Бугу', 'Кулжа', 'Теке', 'Баш Оона', 'Аяк Оона', 'Тогуздун айы', 'Жетинин айы', 'Бештин айы'
					],
					dayOfWeekShort: [
						"Жек", "Дүй", "Шей", "Шар", "Бей", "Жум", "Ише"
					],
					dayOfWeek: [
						"Жекшемб", "Дүйшөмб", "Шейшемб", "Шаршемб", "Бейшемби", "Жума", "Ишенб"
					]
				},
				rm: { // Romansh
					months: [
						"Schaner", "Favrer", "Mars", "Avrigl", "Matg", "Zercladur", "Fanadur", "Avust", "Settember", "October", "November", "December"
					],
					dayOfWeekShort: [
						"Du", "Gli", "Ma", "Me", "Gie", "Ve", "So"
					],
					dayOfWeek: [
						"Dumengia", "Glindesdi", "Mardi", "Mesemna", "Gievgia", "Venderdi", "Sonda"
					]
				},
				ka: { // Georgian
					months: [
						'იანვარი', 'თებერვალი', 'მარტი', 'აპრილი', 'მაისი', 'ივნისი', 'ივლისი', 'აგვისტო', 'სექტემბერი', 'ოქტომბერი', 'ნოემბერი', 'დეკემბერი'
					],
					dayOfWeekShort: [
						"კვ", "ორშ", "სამშ", "ოთხ", "ხუთ", "პარ", "შაბ"
					],
					dayOfWeek: ["კვირა", "ორშაბათი", "სამშაბათი", "ოთხშაბათი", "ხუთშაბათი", "პარასკევი", "შაბათი"]
				},
			},
			value: '',
			rtl: false,
	
			format:	'Y/m/d H:i',
			formatTime:	'H:i',
			formatDate:	'Y/m/d',
	
			startDate:	false, // new Date(), '1986/12/08', '-1970/01/05','-1970/01/05',
			step: 60,
			monthChangeSpinner: true,
	
			closeOnDateSelect: false,
			closeOnTimeSelect: true,
			closeOnWithoutClick: true,
			closeOnInputClick: true,
	
			timepicker: true,
			datepicker: true,
			weeks: false,
	
			defaultTime: false,	// use formatTime format (ex. '10:00' for formatTime:	'H:i')
			defaultDate: false,	// use formatDate format (ex new Date() or '1986/12/08' or '-1970/01/05' or '-1970/01/05')
	
			minDate: false,
			maxDate: false,
			minTime: false,
			maxTime: false,
			disabledMinTime: false,
			disabledMaxTime: false,
	
			allowTimes: [],
			opened: false,
			initTime: true,
			inline: false,
			theme: '',
	
			onSelectDate: function () {},
			onSelectTime: function () {},
			onChangeMonth: function () {},
			onGetWeekOfYear: function () {},
			onChangeYear: function () {},
			onChangeDateTime: function () {},
			onShow: function () {},
			onClose: function () {},
			onGenerate: function () {},
	
			withoutCopyright: true,
			inverseButton: false,
			hours12: false,
			next: 'xdsoft_next',
			prev : 'xdsoft_prev',
			dayOfWeekStart: 0,
			parentID: 'body',
			timeHeightInTimePicker: 25,
			timepickerScrollbar: true,
			todayButton: true,
			prevButton: true,
			nextButton: true,
			defaultSelect: true,
	
			scrollMonth: true,
			scrollTime: true,
			scrollInput: true,
	
			lazyInit: false,
			mask: false,
			validateOnBlur: true,
			allowBlank: true,
			yearStart: 1950,
			yearEnd: 2050,
			monthStart: 0,
			monthEnd: 11,
			style: '',
			id: '',
			fixed: false,
			roundTime: 'round', // ceil, floor
			className: '',
			weekends: [],
			highlightedDates: [],
			highlightedPeriods: [],
			allowDates : [],
			allowDateRe : null,
			disabledDates : [],
			disabledWeekDays: [],
			yearOffset: 0,
			beforeShowDay: null,
	
			enterLikeTab: true,
			showApplyButton: false
		};
	
		var dateHelper = null,
			globalLocaleDefault = 'en',
			globalLocale = 'en';
	
		var dateFormatterOptionsDefault = {
			meridiem: ['AM', 'PM']
		};
	
		var initDateFormatter = function(){
			var locale = default_options.i18n[globalLocale],
				opts = {
					days: locale.dayOfWeek,
					daysShort: locale.dayOfWeekShort,
					months: locale.months,
					monthsShort: $.map(locale.months, function(n){ return n.substring(0, 3) }),
				};
	
		 	dateHelper = new DateFormatter({
				dateSettings: $.extend({}, dateFormatterOptionsDefault, opts)
			});
		};
	
		// for locale settings
		$.datetimepicker = {
			setLocale: function(locale){
				var newLocale = default_options.i18n[locale]?locale:globalLocaleDefault;
				if(globalLocale != newLocale){
					globalLocale = newLocale;
					// reinit date formatter
					initDateFormatter();
				}
			},
			setDateFormatter: function(dateFormatter) {
				dateHelper = dateFormatter;
			},
			RFC_2822: 'D, d M Y H:i:s O',
			ATOM: 'Y-m-d\TH:i:sP',
			ISO_8601: 'Y-m-d\TH:i:sO',
			RFC_822: 'D, d M y H:i:s O',
			RFC_850: 'l, d-M-y H:i:s T',
			RFC_1036: 'D, d M y H:i:s O',
			RFC_1123: 'D, d M Y H:i:s O',
			RSS: 'D, d M Y H:i:s O',
			W3C: 'Y-m-d\TH:i:sP'
		};
	
		// first init date formatter
		initDateFormatter();
	
		// fix for ie8
		if (!window.getComputedStyle) {
			window.getComputedStyle = function (el, pseudo) {
				this.el = el;
				this.getPropertyValue = function (prop) {
					var re = /(\-([a-z]){1})/g;
					if (prop === 'float') {
						prop = 'styleFloat';
					}
					if (re.test(prop)) {
						prop = prop.replace(re, function (a, b, c) {
							return c.toUpperCase();
						});
					}
					return el.currentStyle[prop] || null;
				};
				return this;
			};
		}
		if (!Array.prototype.indexOf) {
			Array.prototype.indexOf = function (obj, start) {
				var i, j;
				for (i = (start || 0), j = this.length; i < j; i += 1) {
					if (this[i] === obj) { return i; }
				}
				return -1;
			};
		}
		Date.prototype.countDaysInMonth = function () {
			return new Date(this.getFullYear(), this.getMonth() + 1, 0).getDate();
		};
		$.fn.xdsoftScroller = function (percent) {
			return this.each(function () {
				var timeboxparent = $(this),
					pointerEventToXY = function (e) {
						var out = {x: 0, y: 0},
							touch;
						if (e.type === 'touchstart' || e.type === 'touchmove' || e.type === 'touchend' || e.type === 'touchcancel') {
							touch  = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
							out.x = touch.clientX;
							out.y = touch.clientY;
						} else if (e.type === 'mousedown' || e.type === 'mouseup' || e.type === 'mousemove' || e.type === 'mouseover' || e.type === 'mouseout' || e.type === 'mouseenter' || e.type === 'mouseleave') {
							out.x = e.clientX;
							out.y = e.clientY;
						}
						return out;
					},
					timebox,
					parentHeight,
					height,
					scrollbar,
					scroller,
					maximumOffset = 100,
					start = false,
					startY = 0,
					startTop = 0,
					h1 = 0,
					touchStart = false,
					startTopScroll = 0,
					calcOffset = function () {};
				if (percent === 'hide') {
					timeboxparent.find('.xdsoft_scrollbar').hide();
					return;
				}
				if (!$(this).hasClass('xdsoft_scroller_box')) {
					timebox = timeboxparent.children().eq(0);
					parentHeight = timeboxparent[0].clientHeight;
					height = timebox[0].offsetHeight;
					scrollbar = $('<div class="xdsoft_scrollbar"></div>');
					scroller = $('<div class="xdsoft_scroller"></div>');
					scrollbar.append(scroller);
	
					timeboxparent.addClass('xdsoft_scroller_box').append(scrollbar);
					calcOffset = function calcOffset(event) {
						var offset = pointerEventToXY(event).y - startY + startTopScroll;
						if (offset < 0) {
							offset = 0;
						}
						if (offset + scroller[0].offsetHeight > h1) {
							offset = h1 - scroller[0].offsetHeight;
						}
						timeboxparent.trigger('scroll_element.xdsoft_scroller', [maximumOffset ? offset / maximumOffset : 0]);
					};
	
					scroller
						.on('touchstart.xdsoft_scroller mousedown.xdsoft_scroller', function (event) {
							if (!parentHeight) {
								timeboxparent.trigger('resize_scroll.xdsoft_scroller', [percent]);
							}
	
							startY = pointerEventToXY(event).y;
							startTopScroll = parseInt(scroller.css('margin-top'), 10);
							h1 = scrollbar[0].offsetHeight;
	
							if (event.type === 'mousedown' || event.type === 'touchstart') {
								if (document) {
									$(document.body).addClass('xdsoft_noselect');
								}
								$([document.body, window]).on('touchend mouseup.xdsoft_scroller', function arguments_callee() {
									$([document.body, window]).off('touchend mouseup.xdsoft_scroller', arguments_callee)
										.off('mousemove.xdsoft_scroller', calcOffset)
										.removeClass('xdsoft_noselect');
								});
								$(document.body).on('mousemove.xdsoft_scroller', calcOffset);
							} else {
								touchStart = true;
								event.stopPropagation();
								event.preventDefault();
							}
						})
						.on('touchmove', function (event) {
							if (touchStart) {
								event.preventDefault();
								calcOffset(event);
							}
						})
						.on('touchend touchcancel', function () {
							touchStart =  false;
							startTopScroll = 0;
						});
	
					timeboxparent
						.on('scroll_element.xdsoft_scroller', function (event, percentage) {
							if (!parentHeight) {
								timeboxparent.trigger('resize_scroll.xdsoft_scroller', [percentage, true]);
							}
							percentage = percentage > 1 ? 1 : (percentage < 0 || isNaN(percentage)) ? 0 : percentage;
	
							scroller.css('margin-top', maximumOffset * percentage);
	
							setTimeout(function () {
								timebox.css('marginTop', -parseInt((timebox[0].offsetHeight - parentHeight) * percentage, 10));
							}, 10);
						})
						.on('resize_scroll.xdsoft_scroller', function (event, percentage, noTriggerScroll) {
							var percent, sh;
							parentHeight = timeboxparent[0].clientHeight;
							height = timebox[0].offsetHeight;
							percent = parentHeight / height;
							sh = percent * scrollbar[0].offsetHeight;
							if (percent > 1) {
								scroller.hide();
							} else {
								scroller.show();
								scroller.css('height', parseInt(sh > 10 ? sh : 10, 10));
								maximumOffset = scrollbar[0].offsetHeight - scroller[0].offsetHeight;
								if (noTriggerScroll !== true) {
									timeboxparent.trigger('scroll_element.xdsoft_scroller', [percentage || Math.abs(parseInt(timebox.css('marginTop'), 10)) / (height - parentHeight)]);
								}
							}
						});
	
					timeboxparent.on('mousewheel', function (event) {
						var top = Math.abs(parseInt(timebox.css('marginTop'), 10));
	
						top = top - (event.deltaY * 20);
						if (top < 0) {
							top = 0;
						}
	
						timeboxparent.trigger('scroll_element.xdsoft_scroller', [top / (height - parentHeight)]);
						event.stopPropagation();
						return false;
					});
	
					timeboxparent.on('touchstart', function (event) {
						start = pointerEventToXY(event);
						startTop = Math.abs(parseInt(timebox.css('marginTop'), 10));
					});
	
					timeboxparent.on('touchmove', function (event) {
						if (start) {
							event.preventDefault();
							var coord = pointerEventToXY(event);
							timeboxparent.trigger('scroll_element.xdsoft_scroller', [(startTop - (coord.y - start.y)) / (height - parentHeight)]);
						}
					});
	
					timeboxparent.on('touchend touchcancel', function () {
						start = false;
						startTop = 0;
					});
				}
				timeboxparent.trigger('resize_scroll.xdsoft_scroller', [percent]);
			});
		};
	
		$.fn.datetimepicker = function (opt, opt2) {
			var result = this,
				KEY0 = 48,
				KEY9 = 57,
				_KEY0 = 96,
				_KEY9 = 105,
				CTRLKEY = 17,
				DEL = 46,
				ENTER = 13,
				ESC = 27,
				BACKSPACE = 8,
				ARROWLEFT = 37,
				ARROWUP = 38,
				ARROWRIGHT = 39,
				ARROWDOWN = 40,
				TAB = 9,
				F5 = 116,
				AKEY = 65,
				CKEY = 67,
				VKEY = 86,
				ZKEY = 90,
				YKEY = 89,
				ctrlDown	=	false,
				options = ($.isPlainObject(opt) || !opt) ? $.extend(true, {}, default_options, opt) : $.extend(true, {}, default_options),
	
				lazyInitTimer = 0,
				createDateTimePicker,
				destroyDateTimePicker,
	
				lazyInit = function (input) {
					input
						.on('open.xdsoft focusin.xdsoft mousedown.xdsoft touchstart', function initOnActionCallback() {
							if (input.is(':disabled') || input.data('xdsoft_datetimepicker')) {
								return;
							}
							clearTimeout(lazyInitTimer);
							lazyInitTimer = setTimeout(function () {
	
								if (!input.data('xdsoft_datetimepicker')) {
									createDateTimePicker(input);
								}
								input
									.off('open.xdsoft focusin.xdsoft mousedown.xdsoft touchstart', initOnActionCallback)
									.trigger('open.xdsoft');
							}, 100);
						});
				};
	
			createDateTimePicker = function (input) {
				var datetimepicker = $('<div class="xdsoft_datetimepicker xdsoft_noselect"></div>'),
					xdsoft_copyright = $('<div class="xdsoft_copyright"><a target="_blank" href="http://xdsoft.net/jqplugins/datetimepicker/">xdsoft.net</a></div>'),
					datepicker = $('<div class="xdsoft_datepicker active"></div>'),
					mounth_picker = $('<div class="xdsoft_mounthpicker"><button type="button" class="xdsoft_prev"></button><button type="button" class="xdsoft_today_button"></button>' +
						'<div class="xdsoft_label xdsoft_month"><span></span><i></i></div>' +
						'<div class="xdsoft_label xdsoft_year"><span></span><i></i></div>' +
						'<button type="button" class="xdsoft_next"></button></div>'),
					calendar = $('<div class="xdsoft_calendar"></div>'),
					timepicker = $('<div class="xdsoft_timepicker active"><button type="button" class="xdsoft_prev"></button><div class="xdsoft_time_box"></div><button type="button" class="xdsoft_next"></button></div>'),
					timeboxparent = timepicker.find('.xdsoft_time_box').eq(0),
					timebox = $('<div class="xdsoft_time_variant"></div>'),
					applyButton = $('<button type="button" class="xdsoft_save_selected blue-gradient-button">Save Selected</button>'),
	
					monthselect = $('<div class="xdsoft_select xdsoft_monthselect"><div></div></div>'),
					yearselect = $('<div class="xdsoft_select xdsoft_yearselect"><div></div></div>'),
					triggerAfterOpen = false,
					XDSoft_datetime,
	
					xchangeTimer,
					timerclick,
					current_time_index,
					setPos,
					timer = 0,
					_xdsoft_datetime,
					forEachAncestorOf,
					throttle;
	
				if (options.id) {
					datetimepicker.attr('id', options.id);
				}
				if (options.style) {
					datetimepicker.attr('style', options.style);
				}
				if (options.weeks) {
					datetimepicker.addClass('xdsoft_showweeks');
				}
				if (options.rtl) {
					datetimepicker.addClass('xdsoft_rtl');
				}
	
				datetimepicker.addClass('xdsoft_' + options.theme);
				datetimepicker.addClass(options.className);
	
				mounth_picker
					.find('.xdsoft_month span')
						.after(monthselect);
				mounth_picker
					.find('.xdsoft_year span')
						.after(yearselect);
	
				mounth_picker
					.find('.xdsoft_month,.xdsoft_year')
						.on('touchstart mousedown.xdsoft', function (event) {
						var select = $(this).find('.xdsoft_select').eq(0),
							val = 0,
							top = 0,
							visible = select.is(':visible'),
							items,
							i;
	
						mounth_picker
							.find('.xdsoft_select')
								.hide();
						if (_xdsoft_datetime.currentTime) {
							val = _xdsoft_datetime.currentTime[$(this).hasClass('xdsoft_month') ? 'getMonth' : 'getFullYear']();
						}
	
						select[visible ? 'hide' : 'show']();
						for (items = select.find('div.xdsoft_option'), i = 0; i < items.length; i += 1) {
							if (items.eq(i).data('value') === val) {
								break;
							} else {
								top += items[0].offsetHeight;
							}
						}
	
						select.xdsoftScroller(top / (select.children()[0].offsetHeight - (select[0].clientHeight)));
						event.stopPropagation();
						return false;
					});
	
				mounth_picker
					.find('.xdsoft_select')
						.xdsoftScroller()
					.on('touchstart mousedown.xdsoft', function (event) {
						event.stopPropagation();
						event.preventDefault();
					})
					.on('touchstart mousedown.xdsoft', '.xdsoft_option', function () {
						if (_xdsoft_datetime.currentTime === undefined || _xdsoft_datetime.currentTime === null) {
							_xdsoft_datetime.currentTime = _xdsoft_datetime.now();
						}
	
						var year = _xdsoft_datetime.currentTime.getFullYear();
						if (_xdsoft_datetime && _xdsoft_datetime.currentTime) {
							_xdsoft_datetime.currentTime[$(this).parent().parent().hasClass('xdsoft_monthselect') ? 'setMonth' : 'setFullYear']($(this).data('value'));
						}
	
						$(this).parent().parent().hide();
	
						datetimepicker.trigger('xchange.xdsoft');
						if (options.onChangeMonth && $.isFunction(options.onChangeMonth)) {
							options.onChangeMonth.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'));
						}
	
						if (year !== _xdsoft_datetime.currentTime.getFullYear() && $.isFunction(options.onChangeYear)) {
							options.onChangeYear.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'));
						}
					});
	
				datetimepicker.getValue = function () {
					return _xdsoft_datetime.getCurrentTime();
				};
	
				datetimepicker.setOptions = function (_options) {
					var highlightedDates = {};
	
					options = $.extend(true, {}, options, _options);
	
					if (_options.allowTimes && $.isArray(_options.allowTimes) && _options.allowTimes.length) {
						options.allowTimes = $.extend(true, [], _options.allowTimes);
					}
	
					if (_options.weekends && $.isArray(_options.weekends) && _options.weekends.length) {
						options.weekends = $.extend(true, [], _options.weekends);
					}
	
					if (_options.allowDates && $.isArray(_options.allowDates) && _options.allowDates.length) {
						options.allowDates = $.extend(true, [], _options.allowDates);
					}
	
					if (_options.allowDateRe && Object.prototype.toString.call(_options.allowDateRe)==="[object String]") {
						options.allowDateRe = new RegExp(_options.allowDateRe);
					}
	
					if (_options.highlightedDates && $.isArray(_options.highlightedDates) && _options.highlightedDates.length) {
						$.each(_options.highlightedDates, function (index, value) {
							var splitData = $.map(value.split(','), $.trim),
								exDesc,
								hDate = new HighlightedDate(dateHelper.parseDate(splitData[0], options.formatDate), splitData[1], splitData[2]), // date, desc, style
								keyDate = dateHelper.formatDate(hDate.date, options.formatDate);
							if (highlightedDates[keyDate] !== undefined) {
								exDesc = highlightedDates[keyDate].desc;
								if (exDesc && exDesc.length && hDate.desc && hDate.desc.length) {
									highlightedDates[keyDate].desc = exDesc + "\n" + hDate.desc;
								}
							} else {
								highlightedDates[keyDate] = hDate;
							}
						});
	
						options.highlightedDates = $.extend(true, [], highlightedDates);
					}
	
					if (_options.highlightedPeriods && $.isArray(_options.highlightedPeriods) && _options.highlightedPeriods.length) {
						highlightedDates = $.extend(true, [], options.highlightedDates);
						$.each(_options.highlightedPeriods, function (index, value) {
							var dateTest, // start date
								dateEnd,
								desc,
								hDate,
								keyDate,
								exDesc,
								style;
							if ($.isArray(value)) {
								dateTest = value[0];
								dateEnd = value[1];
								desc = value[2];
								style = value[3];
							}
							else {
								var splitData = $.map(value.split(','), $.trim);
								dateTest = dateHelper.parseDate(splitData[0], options.formatDate);
								dateEnd = dateHelper.parseDate(splitData[1], options.formatDate);
								desc = splitData[2];
								style = splitData[3];
							}
	
							while (dateTest <= dateEnd) {
								hDate = new HighlightedDate(dateTest, desc, style);
								keyDate = dateHelper.formatDate(dateTest, options.formatDate);
								dateTest.setDate(dateTest.getDate() + 1);
								if (highlightedDates[keyDate] !== undefined) {
									exDesc = highlightedDates[keyDate].desc;
									if (exDesc && exDesc.length && hDate.desc && hDate.desc.length) {
										highlightedDates[keyDate].desc = exDesc + "\n" + hDate.desc;
									}
								} else {
									highlightedDates[keyDate] = hDate;
								}
							}
						});
	
						options.highlightedDates = $.extend(true, [], highlightedDates);
					}
	
					if (_options.disabledDates && $.isArray(_options.disabledDates) && _options.disabledDates.length) {
						options.disabledDates = $.extend(true, [], _options.disabledDates);
					}
	
					if (_options.disabledWeekDays && $.isArray(_options.disabledWeekDays) && _options.disabledWeekDays.length) {
						options.disabledWeekDays = $.extend(true, [], _options.disabledWeekDays);
					}
	
					if ((options.open || options.opened) && (!options.inline)) {
						input.trigger('open.xdsoft');
					}
	
					if (options.inline) {
						triggerAfterOpen = true;
						datetimepicker.addClass('xdsoft_inline');
						input.after(datetimepicker).hide();
					}
	
					if (options.inverseButton) {
						options.next = 'xdsoft_prev';
						options.prev = 'xdsoft_next';
					}
	
					if (options.datepicker) {
						datepicker.addClass('active');
					} else {
						datepicker.removeClass('active');
					}
	
					if (options.timepicker) {
						timepicker.addClass('active');
					} else {
						timepicker.removeClass('active');
					}
	
					if (options.value) {
						_xdsoft_datetime.setCurrentTime(options.value);
						if (input && input.val) {
							input.val(_xdsoft_datetime.str);
						}
					}
	
					if (isNaN(options.dayOfWeekStart)) {
						options.dayOfWeekStart = 0;
					} else {
						options.dayOfWeekStart = parseInt(options.dayOfWeekStart, 10) % 7;
					}
	
					if (!options.timepickerScrollbar) {
						timeboxparent.xdsoftScroller('hide');
					}
	
					if (options.minDate && /^[\+\-](.*)$/.test(options.minDate)) {
						options.minDate = dateHelper.formatDate(_xdsoft_datetime.strToDateTime(options.minDate), options.formatDate);
					}
	
					if (options.maxDate &&  /^[\+\-](.*)$/.test(options.maxDate)) {
						options.maxDate = dateHelper.formatDate(_xdsoft_datetime.strToDateTime(options.maxDate), options.formatDate);
					}
	
					applyButton.toggle(options.showApplyButton);
	
					mounth_picker
						.find('.xdsoft_today_button')
							.css('visibility', !options.todayButton ? 'hidden' : 'visible');
	
					mounth_picker
						.find('.' + options.prev)
							.css('visibility', !options.prevButton ? 'hidden' : 'visible');
	
					mounth_picker
						.find('.' + options.next)
							.css('visibility', !options.nextButton ? 'hidden' : 'visible');
	
					setMask(options);
	
					if (options.validateOnBlur) {
						input
							.off('blur.xdsoft')
							.on('blur.xdsoft', function () {
								if (options.allowBlank && (!$.trim($(this).val()).length || (typeof options.mask == "string" && $.trim($(this).val()) === options.mask.replace(/[0-9]/g, '_')))) {
									$(this).val(null);
									datetimepicker.data('xdsoft_datetime').empty();
								} else {
									var d = dateHelper.parseDate($(this).val(), options.format);
									if (d) { // parseDate() may skip some invalid parts like date or time, so make it clear for user: show parsed date/time
										$(this).val(dateHelper.formatDate(d, options.format));
									} else {
										var splittedHours   = +([$(this).val()[0], $(this).val()[1]].join('')),
											splittedMinutes = +([$(this).val()[2], $(this).val()[3]].join(''));
		
										// parse the numbers as 0312 => 03:12
										if (!options.datepicker && options.timepicker && splittedHours >= 0 && splittedHours < 24 && splittedMinutes >= 0 && splittedMinutes < 60) {
											$(this).val([splittedHours, splittedMinutes].map(function (item) {
												return item > 9 ? item : '0' + item;
											}).join(':'));
										} else {
											$(this).val(dateHelper.formatDate(_xdsoft_datetime.now(), options.format));
										}
									}
									datetimepicker.data('xdsoft_datetime').setCurrentTime($(this).val());
								}
	
								datetimepicker.trigger('changedatetime.xdsoft');
								datetimepicker.trigger('close.xdsoft');
							});
					}
					options.dayOfWeekStartPrev = (options.dayOfWeekStart === 0) ? 6 : options.dayOfWeekStart - 1;
	
					datetimepicker
						.trigger('xchange.xdsoft')
						.trigger('afterOpen.xdsoft');
				};
	
				datetimepicker
					.data('options', options)
					.on('touchstart mousedown.xdsoft', function (event) {
						event.stopPropagation();
						event.preventDefault();
						yearselect.hide();
						monthselect.hide();
						return false;
					});
	
				//scroll_element = timepicker.find('.xdsoft_time_box');
				timeboxparent.append(timebox);
				timeboxparent.xdsoftScroller();
	
				datetimepicker.on('afterOpen.xdsoft', function () {
					timeboxparent.xdsoftScroller();
				});
	
				datetimepicker
					.append(datepicker)
					.append(timepicker);
	
				if (options.withoutCopyright !== true) {
					datetimepicker
						.append(xdsoft_copyright);
				}
	
				datepicker
					.append(mounth_picker)
					.append(calendar)
					.append(applyButton);
	
				$(options.parentID)
					.append(datetimepicker);
	
				XDSoft_datetime = function () {
					var _this = this;
					_this.now = function (norecursion) {
						var d = new Date(),
							date,
							time;
	
						if (!norecursion && options.defaultDate) {
							date = _this.strToDateTime(options.defaultDate);
							d.setFullYear(date.getFullYear());
							d.setMonth(date.getMonth());
							d.setDate(date.getDate());
						}
	
						if (options.yearOffset) {
							d.setFullYear(d.getFullYear() + options.yearOffset);
						}
	
						if (!norecursion && options.defaultTime) {
							time = _this.strtotime(options.defaultTime);
							d.setHours(time.getHours());
							d.setMinutes(time.getMinutes());
						}
						return d;
					};
	
					_this.isValidDate = function (d) {
						if (Object.prototype.toString.call(d) !== "[object Date]") {
							return false;
						}
						return !isNaN(d.getTime());
					};
	
					_this.setCurrentTime = function (dTime, requireValidDate) {
						if (typeof dTime === 'string') {
							_this.currentTime = _this.strToDateTime(dTime);
						}
						else if (_this.isValidDate(dTime)) {
							_this.currentTime = dTime;
						}
						else if (!dTime && !requireValidDate && options.allowBlank) {
							_this.currentTime = null;
						}
						else {
							_this.currentTime = _this.now();
						}
						
						datetimepicker.trigger('xchange.xdsoft');
					};
	
					_this.empty = function () {
						_this.currentTime = null;
					};
	
					_this.getCurrentTime = function (dTime) {
						return _this.currentTime;
					};
	
					_this.nextMonth = function () {
	
						if (_this.currentTime === undefined || _this.currentTime === null) {
							_this.currentTime = _this.now();
						}
	
						var month = _this.currentTime.getMonth() + 1,
							year;
						if (month === 12) {
							_this.currentTime.setFullYear(_this.currentTime.getFullYear() + 1);
							month = 0;
						}
	
						year = _this.currentTime.getFullYear();
	
						_this.currentTime.setDate(
							Math.min(
								new Date(_this.currentTime.getFullYear(), month + 1, 0).getDate(),
								_this.currentTime.getDate()
							)
						);
						_this.currentTime.setMonth(month);
	
						if (options.onChangeMonth && $.isFunction(options.onChangeMonth)) {
							options.onChangeMonth.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'));
						}
	
						if (year !== _this.currentTime.getFullYear() && $.isFunction(options.onChangeYear)) {
							options.onChangeYear.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'));
						}
	
						datetimepicker.trigger('xchange.xdsoft');
						return month;
					};
	
					_this.prevMonth = function () {
	
						if (_this.currentTime === undefined || _this.currentTime === null) {
							_this.currentTime = _this.now();
						}
	
						var month = _this.currentTime.getMonth() - 1;
						if (month === -1) {
							_this.currentTime.setFullYear(_this.currentTime.getFullYear() - 1);
							month = 11;
						}
						_this.currentTime.setDate(
							Math.min(
								new Date(_this.currentTime.getFullYear(), month + 1, 0).getDate(),
								_this.currentTime.getDate()
							)
						);
						_this.currentTime.setMonth(month);
						if (options.onChangeMonth && $.isFunction(options.onChangeMonth)) {
							options.onChangeMonth.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'));
						}
						datetimepicker.trigger('xchange.xdsoft');
						return month;
					};
	
					_this.getWeekOfYear = function (datetime) {
						if (options.onGetWeekOfYear && $.isFunction(options.onGetWeekOfYear)) {
							var week = options.onGetWeekOfYear.call(datetimepicker, datetime);
							if (typeof week !== 'undefined') {
								return week;
							}
						}
						var onejan = new Date(datetime.getFullYear(), 0, 1);
						//First week of the year is th one with the first Thursday according to ISO8601
						if(onejan.getDay()!=4)
							onejan.setMonth(0, 1 + ((4 - onejan.getDay()+ 7) % 7));
						return Math.ceil((((datetime - onejan) / 86400000) + onejan.getDay() + 1) / 7);
					};
	
					_this.strToDateTime = function (sDateTime) {
						var tmpDate = [], timeOffset, currentTime;
	
						if (sDateTime && sDateTime instanceof Date && _this.isValidDate(sDateTime)) {
							return sDateTime;
						}
	
						tmpDate = /^(\+|\-)(.*)$/.exec(sDateTime);
						if (tmpDate) {
							tmpDate[2] = dateHelper.parseDate(tmpDate[2], options.formatDate);
						}
						if (tmpDate  && tmpDate[2]) {
							timeOffset = tmpDate[2].getTime() - (tmpDate[2].getTimezoneOffset()) * 60000;
							currentTime = new Date((_this.now(true)).getTime() + parseInt(tmpDate[1] + '1', 10) * timeOffset);
						} else {
							currentTime = sDateTime ? dateHelper.parseDate(sDateTime, options.format) : _this.now();
						}
	
						if (!_this.isValidDate(currentTime)) {
							currentTime = _this.now();
						}
	
						return currentTime;
					};
	
					_this.strToDate = function (sDate) {
						if (sDate && sDate instanceof Date && _this.isValidDate(sDate)) {
							return sDate;
						}
	
						var currentTime = sDate ? dateHelper.parseDate(sDate, options.formatDate) : _this.now(true);
						if (!_this.isValidDate(currentTime)) {
							currentTime = _this.now(true);
						}
						return currentTime;
					};
	
					_this.strtotime = function (sTime) {
						if (sTime && sTime instanceof Date && _this.isValidDate(sTime)) {
							return sTime;
						}
						var currentTime = sTime ? dateHelper.parseDate(sTime, options.formatTime) : _this.now(true);
						if (!_this.isValidDate(currentTime)) {
							currentTime = _this.now(true);
						}
						return currentTime;
					};
	
					_this.str = function () {
						return dateHelper.formatDate(_this.currentTime, options.format);
					};
					_this.currentTime = this.now();
				};
	
				_xdsoft_datetime = new XDSoft_datetime();
	
				applyButton.on('touchend click', function (e) {//pathbrite
					e.preventDefault();
					datetimepicker.data('changed', true);
					_xdsoft_datetime.setCurrentTime(getCurrentValue());
					input.val(_xdsoft_datetime.str());
					datetimepicker.trigger('close.xdsoft');
				});
				mounth_picker
					.find('.xdsoft_today_button')
					.on('touchend mousedown.xdsoft', function () {
						datetimepicker.data('changed', true);
						_xdsoft_datetime.setCurrentTime(0, true);
						datetimepicker.trigger('afterOpen.xdsoft');
					}).on('dblclick.xdsoft', function () {
						var currentDate = _xdsoft_datetime.getCurrentTime(), minDate, maxDate;
						currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate());
						minDate = _xdsoft_datetime.strToDate(options.minDate);
						minDate = new Date(minDate.getFullYear(), minDate.getMonth(), minDate.getDate());
						if (currentDate < minDate) {
							return;
						}
						maxDate = _xdsoft_datetime.strToDate(options.maxDate);
						maxDate = new Date(maxDate.getFullYear(), maxDate.getMonth(), maxDate.getDate());
						if (currentDate > maxDate) {
							return;
						}
						input.val(_xdsoft_datetime.str());
						input.trigger('change');
						datetimepicker.trigger('close.xdsoft');
					});
				mounth_picker
					.find('.xdsoft_prev,.xdsoft_next')
					.on('touchend mousedown.xdsoft', function () {
						var $this = $(this),
							timer = 0,
							stop = false;
	
						(function arguments_callee1(v) {
							if ($this.hasClass(options.next)) {
								_xdsoft_datetime.nextMonth();
							} else if ($this.hasClass(options.prev)) {
								_xdsoft_datetime.prevMonth();
							}
							if (options.monthChangeSpinner) {
								if (!stop) {
									timer = setTimeout(arguments_callee1, v || 100);
								}
							}
						}(500));
	
						$([document.body, window]).on('touchend mouseup.xdsoft', function arguments_callee2() {
							clearTimeout(timer);
							stop = true;
							$([document.body, window]).off('touchend mouseup.xdsoft', arguments_callee2);
						});
					});
	
				timepicker
					.find('.xdsoft_prev,.xdsoft_next')
					.on('touchend mousedown.xdsoft', function () {
						var $this = $(this),
							timer = 0,
							stop = false,
							period = 110;
						(function arguments_callee4(v) {
							var pheight = timeboxparent[0].clientHeight,
								height = timebox[0].offsetHeight,
								top = Math.abs(parseInt(timebox.css('marginTop'), 10));
							if ($this.hasClass(options.next) && (height - pheight) - options.timeHeightInTimePicker >= top) {
								timebox.css('marginTop', '-' + (top + options.timeHeightInTimePicker) + 'px');
							} else if ($this.hasClass(options.prev) && top - options.timeHeightInTimePicker >= 0) {
								timebox.css('marginTop', '-' + (top - options.timeHeightInTimePicker) + 'px');
							}
	                        /**
	                         * Fixed bug:
	                         * When using css3 transition, it will cause a bug that you cannot scroll the timepicker list.
	                         * The reason is that the transition-duration time, if you set it to 0, all things fine, otherwise, this
	                         * would cause a bug when you use jquery.css method.
	                         * Let's say: * { transition: all .5s ease; }
	                         * jquery timebox.css('marginTop') will return the original value which is before you clicking the next/prev button,
	                         * meanwhile the timebox[0].style.marginTop will return the right value which is after you clicking the
	                         * next/prev button.
	                         * 
	                         * What we should do:
	                         * Replace timebox.css('marginTop') with timebox[0].style.marginTop.
	                         */
	                        timeboxparent.trigger('scroll_element.xdsoft_scroller', [Math.abs(parseInt(timebox[0].style.marginTop, 10) / (height - pheight))]);
							period = (period > 10) ? 10 : period - 10;
							if (!stop) {
								timer = setTimeout(arguments_callee4, v || period);
							}
						}(500));
						$([document.body, window]).on('touchend mouseup.xdsoft', function arguments_callee5() {
							clearTimeout(timer);
							stop = true;
							$([document.body, window])
								.off('touchend mouseup.xdsoft', arguments_callee5);
						});
					});
	
				xchangeTimer = 0;
				// base handler - generating a calendar and timepicker
				datetimepicker
					.on('xchange.xdsoft', function (event) {
						clearTimeout(xchangeTimer);
						xchangeTimer = setTimeout(function () {
	
							if (_xdsoft_datetime.currentTime === undefined || _xdsoft_datetime.currentTime === null) {
								//In case blanks are allowed, delay construction until we have a valid date 
								if (options.allowBlank)
									return;
									
								_xdsoft_datetime.currentTime = _xdsoft_datetime.now();
							}
	
							var table =	'',
								start = new Date(_xdsoft_datetime.currentTime.getFullYear(), _xdsoft_datetime.currentTime.getMonth(), 1, 12, 0, 0),
								i = 0,
								j,
								today = _xdsoft_datetime.now(),
								maxDate = false,
								minDate = false,
								hDate,
								day,
								d,
								y,
								m,
								w,
								classes = [],
								customDateSettings,
								newRow = true,
								time = '',
								h = '',
								line_time,
								description;
	
							while (start.getDay() !== options.dayOfWeekStart) {
								start.setDate(start.getDate() - 1);
							}
	
							table += '<table><thead><tr>';
	
							if (options.weeks) {
								table += '<th></th>';
							}
	
							for (j = 0; j < 7; j += 1) {
								table += '<th>' + options.i18n[globalLocale].dayOfWeekShort[(j + options.dayOfWeekStart) % 7] + '</th>';
							}
	
							table += '</tr></thead>';
							table += '<tbody>';
	
							if (options.maxDate !== false) {
								maxDate = _xdsoft_datetime.strToDate(options.maxDate);
								maxDate = new Date(maxDate.getFullYear(), maxDate.getMonth(), maxDate.getDate(), 23, 59, 59, 999);
							}
	
							if (options.minDate !== false) {
								minDate = _xdsoft_datetime.strToDate(options.minDate);
								minDate = new Date(minDate.getFullYear(), minDate.getMonth(), minDate.getDate());
							}
	
							while (i < _xdsoft_datetime.currentTime.countDaysInMonth() || start.getDay() !== options.dayOfWeekStart || _xdsoft_datetime.currentTime.getMonth() === start.getMonth()) {
								classes = [];
								i += 1;
	
								day = start.getDay();
								d = start.getDate();
								y = start.getFullYear();
								m = start.getMonth();
								w = _xdsoft_datetime.getWeekOfYear(start);
								description = '';
	
								classes.push('xdsoft_date');
	
								if (options.beforeShowDay && $.isFunction(options.beforeShowDay.call)) {
									customDateSettings = options.beforeShowDay.call(datetimepicker, start);
								} else {
									customDateSettings = null;
								}
	
								if(options.allowDateRe && Object.prototype.toString.call(options.allowDateRe) === "[object RegExp]"){
									if(!options.allowDateRe.test(dateHelper.formatDate(start, options.formatDate))){
										classes.push('xdsoft_disabled');
									}
								} else if(options.allowDates && options.allowDates.length>0){
									if(options.allowDates.indexOf(dateHelper.formatDate(start, options.formatDate)) === -1){
										classes.push('xdsoft_disabled');
									}
								} else if ((maxDate !== false && start > maxDate) || (minDate !== false && start < minDate) || (customDateSettings && customDateSettings[0] === false)) {
									classes.push('xdsoft_disabled');
								} else if (options.disabledDates.indexOf(dateHelper.formatDate(start, options.formatDate)) !== -1) {
									classes.push('xdsoft_disabled');
								} else if (options.disabledWeekDays.indexOf(day) !== -1) {
									classes.push('xdsoft_disabled');
								}else if (input.is('[readonly]')) {
									classes.push('xdsoft_disabled');
								}
	
								if (customDateSettings && customDateSettings[1] !== "") {
									classes.push(customDateSettings[1]);
								}
	
								if (_xdsoft_datetime.currentTime.getMonth() !== m) {
									classes.push('xdsoft_other_month');
								}
	
								if ((options.defaultSelect || datetimepicker.data('changed')) && dateHelper.formatDate(_xdsoft_datetime.currentTime, options.formatDate) === dateHelper.formatDate(start, options.formatDate)) {
									classes.push('xdsoft_current');
								}
	
								if (dateHelper.formatDate(today, options.formatDate) === dateHelper.formatDate(start, options.formatDate)) {
									classes.push('xdsoft_today');
								}
	
								if (start.getDay() === 0 || start.getDay() === 6 || options.weekends.indexOf(dateHelper.formatDate(start, options.formatDate)) !== -1) {
									classes.push('xdsoft_weekend');
								}
	
								if (options.highlightedDates[dateHelper.formatDate(start, options.formatDate)] !== undefined) {
									hDate = options.highlightedDates[dateHelper.formatDate(start, options.formatDate)];
									classes.push(hDate.style === undefined ? 'xdsoft_highlighted_default' : hDate.style);
									description = hDate.desc === undefined ? '' : hDate.desc;
								}
	
								if (options.beforeShowDay && $.isFunction(options.beforeShowDay)) {
									classes.push(options.beforeShowDay(start));
								}
	
								if (newRow) {
									table += '<tr>';
									newRow = false;
									if (options.weeks) {
										table += '<th>' + w + '</th>';
									}
								}
	
								table += '<td data-date="' + d + '" data-month="' + m + '" data-year="' + y + '"' + ' class="xdsoft_date xdsoft_day_of_week' + start.getDay() + ' ' + classes.join(' ') + '" title="' + description + '">' +
											'<div>' + d + '</div>' +
										'</td>';
	
								if (start.getDay() === options.dayOfWeekStartPrev) {
									table += '</tr>';
									newRow = true;
								}
	
								start.setDate(d + 1);
							}
							table += '</tbody></table>';
	
							calendar.html(table);
	
							mounth_picker.find('.xdsoft_label span').eq(0).text(options.i18n[globalLocale].months[_xdsoft_datetime.currentTime.getMonth()]);
							mounth_picker.find('.xdsoft_label span').eq(1).text(_xdsoft_datetime.currentTime.getFullYear());
	
							// generate timebox
							time = '';
							h = '';
							m = '';
	
							line_time = function line_time(h, m) {
								var now = _xdsoft_datetime.now(), optionDateTime, current_time,
									isALlowTimesInit = options.allowTimes && $.isArray(options.allowTimes) && options.allowTimes.length;
								now.setHours(h);
								h = parseInt(now.getHours(), 10);
								now.setMinutes(m);
								m = parseInt(now.getMinutes(), 10);
								optionDateTime = new Date(_xdsoft_datetime.currentTime);
								optionDateTime.setHours(h);
								optionDateTime.setMinutes(m);
								classes = [];			
								if ((options.minDateTime !== false && options.minDateTime > optionDateTime) || (options.maxTime !== false && _xdsoft_datetime.strtotime(options.maxTime).getTime() < now.getTime()) || (options.minTime !== false && _xdsoft_datetime.strtotime(options.minTime).getTime() > now.getTime())) {
									classes.push('xdsoft_disabled');
								} else if ((options.minDateTime !== false && options.minDateTime > optionDateTime) || ((options.disabledMinTime !== false && now.getTime() > _xdsoft_datetime.strtotime(options.disabledMinTime).getTime()) && (options.disabledMaxTime !== false && now.getTime() < _xdsoft_datetime.strtotime(options.disabledMaxTime).getTime()))) {
									classes.push('xdsoft_disabled');
								} else if (input.is('[readonly]')) {
									classes.push('xdsoft_disabled');
								}
	
								current_time = new Date(_xdsoft_datetime.currentTime);
								current_time.setHours(parseInt(_xdsoft_datetime.currentTime.getHours(), 10));
	
								if (!isALlowTimesInit) {
									current_time.setMinutes(Math[options.roundTime](_xdsoft_datetime.currentTime.getMinutes() / options.step) * options.step);
								}
	
								if ((options.initTime || options.defaultSelect || datetimepicker.data('changed')) && current_time.getHours() === parseInt(h, 10) && ((!isALlowTimesInit && options.step > 59) || current_time.getMinutes() === parseInt(m, 10))) {
									if (options.defaultSelect || datetimepicker.data('changed')) {
										classes.push('xdsoft_current');
									} else if (options.initTime) {
										classes.push('xdsoft_init_time');
									}
								}
								if (parseInt(today.getHours(), 10) === parseInt(h, 10) && parseInt(today.getMinutes(), 10) === parseInt(m, 10)) {
									classes.push('xdsoft_today');
								}
								time += '<div class="xdsoft_time ' + classes.join(' ') + '" data-hour="' + h + '" data-minute="' + m + '">' + dateHelper.formatDate(now, options.formatTime) + '</div>';
							};
	
							if (!options.allowTimes || !$.isArray(options.allowTimes) || !options.allowTimes.length) {
								for (i = 0, j = 0; i < (options.hours12 ? 12 : 24); i += 1) {
									for (j = 0; j < 60; j += options.step) {
										h = (i < 10 ? '0' : '') + i;
										m = (j < 10 ? '0' : '') + j;
										line_time(h, m);
									}
								}
							} else {
								for (i = 0; i < options.allowTimes.length; i += 1) {
									h = _xdsoft_datetime.strtotime(options.allowTimes[i]).getHours();
									m = _xdsoft_datetime.strtotime(options.allowTimes[i]).getMinutes();
									line_time(h, m);
								}
							}
	
							timebox.html(time);
	
							opt = '';
							i = 0;
	
							for (i = parseInt(options.yearStart, 10) + options.yearOffset; i <= parseInt(options.yearEnd, 10) + options.yearOffset; i += 1) {
								opt += '<div class="xdsoft_option ' + (_xdsoft_datetime.currentTime.getFullYear() === i ? 'xdsoft_current' : '') + '" data-value="' + i + '">' + i + '</div>';
							}
							yearselect.children().eq(0)
													.html(opt);
	
							for (i = parseInt(options.monthStart, 10), opt = ''; i <= parseInt(options.monthEnd, 10); i += 1) {
								opt += '<div class="xdsoft_option ' + (_xdsoft_datetime.currentTime.getMonth() === i ? 'xdsoft_current' : '') + '" data-value="' + i + '">' + options.i18n[globalLocale].months[i] + '</div>';
							}
							monthselect.children().eq(0).html(opt);
							$(datetimepicker)
								.trigger('generate.xdsoft');
						}, 10);
						event.stopPropagation();
					})
					.on('afterOpen.xdsoft', function () {
						if (options.timepicker) {
							var classType, pheight, height, top;
							if (timebox.find('.xdsoft_current').length) {
								classType = '.xdsoft_current';
							} else if (timebox.find('.xdsoft_init_time').length) {
								classType = '.xdsoft_init_time';
							}
							if (classType) {
								pheight = timeboxparent[0].clientHeight;
								height = timebox[0].offsetHeight;
								top = timebox.find(classType).index() * options.timeHeightInTimePicker + 1;
								if ((height - pheight) < top) {
									top = height - pheight;
								}
								timeboxparent.trigger('scroll_element.xdsoft_scroller', [parseInt(top, 10) / (height - pheight)]);
							} else {
								timeboxparent.trigger('scroll_element.xdsoft_scroller', [0]);
							}
						}
					});
	
				timerclick = 0;
				calendar
					.on('touchend click.xdsoft', 'td', function (xdevent) {
						xdevent.stopPropagation();  // Prevents closing of Pop-ups, Modals and Flyouts in Bootstrap
						timerclick += 1;
						var $this = $(this),
							currentTime = _xdsoft_datetime.currentTime;
	
						if (currentTime === undefined || currentTime === null) {
							_xdsoft_datetime.currentTime = _xdsoft_datetime.now();
							currentTime = _xdsoft_datetime.currentTime;
						}
	
						if ($this.hasClass('xdsoft_disabled')) {
							return false;
						}
	
						currentTime.setDate(1);
						currentTime.setFullYear($this.data('year'));
						currentTime.setMonth($this.data('month'));
						currentTime.setDate($this.data('date'));
	
						datetimepicker.trigger('select.xdsoft', [currentTime]);
	
						input.val(_xdsoft_datetime.str());
	
						if (options.onSelectDate &&	$.isFunction(options.onSelectDate)) {
							options.onSelectDate.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'), xdevent);
						}
	
						datetimepicker.data('changed', true);
						datetimepicker.trigger('xchange.xdsoft');
						datetimepicker.trigger('changedatetime.xdsoft');
						if ((timerclick > 1 || (options.closeOnDateSelect === true || (options.closeOnDateSelect === false && !options.timepicker))) && !options.inline) {
							datetimepicker.trigger('close.xdsoft');
						}
						setTimeout(function () {
							timerclick = 0;
						}, 200);
					});
	
				timebox
					.on('touchend click.xdsoft', 'div', function (xdevent) {
						xdevent.stopPropagation();
						var $this = $(this),
							currentTime = _xdsoft_datetime.currentTime;
	
						if (currentTime === undefined || currentTime === null) {
							_xdsoft_datetime.currentTime = _xdsoft_datetime.now();
							currentTime = _xdsoft_datetime.currentTime;
						}
	
						if ($this.hasClass('xdsoft_disabled')) {
							return false;
						}
						currentTime.setHours($this.data('hour'));
						currentTime.setMinutes($this.data('minute'));
						datetimepicker.trigger('select.xdsoft', [currentTime]);
	
						datetimepicker.data('input').val(_xdsoft_datetime.str());
	
						if (options.onSelectTime && $.isFunction(options.onSelectTime)) {
							options.onSelectTime.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'), xdevent);
						}
						datetimepicker.data('changed', true);
						datetimepicker.trigger('xchange.xdsoft');
						datetimepicker.trigger('changedatetime.xdsoft');
						if (options.inline !== true && options.closeOnTimeSelect === true) {
							datetimepicker.trigger('close.xdsoft');
						}
					});
	
				datepicker
					.on('mousewheel.xdsoft', function (event) {
						if (!options.scrollMonth) {
							return true;
						}
						if (event.deltaY < 0) {
							_xdsoft_datetime.nextMonth();
						} else {
							_xdsoft_datetime.prevMonth();
						}
						return false;
					});
	
				input
					.on('mousewheel.xdsoft', function (event) {
						if (!options.scrollInput) {
							return true;
						}
						if (!options.datepicker && options.timepicker) {
							current_time_index = timebox.find('.xdsoft_current').length ? timebox.find('.xdsoft_current').eq(0).index() : 0;
							if (current_time_index + event.deltaY >= 0 && current_time_index + event.deltaY < timebox.children().length) {
								current_time_index += event.deltaY;
							}
							if (timebox.children().eq(current_time_index).length) {
								timebox.children().eq(current_time_index).trigger('mousedown');
							}
							return false;
						}
						if (options.datepicker && !options.timepicker) {
							datepicker.trigger(event, [event.deltaY, event.deltaX, event.deltaY]);
							if (input.val) {
								input.val(_xdsoft_datetime.str());
							}
							datetimepicker.trigger('changedatetime.xdsoft');
							return false;
						}
					});
	
				datetimepicker
					.on('changedatetime.xdsoft', function (event) {
						if (options.onChangeDateTime && $.isFunction(options.onChangeDateTime)) {
							var $input = datetimepicker.data('input');
							options.onChangeDateTime.call(datetimepicker, _xdsoft_datetime.currentTime, $input, event);
							delete options.value;
							$input.trigger('change');
						}
					})
					.on('generate.xdsoft', function () {
						if (options.onGenerate && $.isFunction(options.onGenerate)) {
							options.onGenerate.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'));
						}
						if (triggerAfterOpen) {
							datetimepicker.trigger('afterOpen.xdsoft');
							triggerAfterOpen = false;
						}
					})
					.on('click.xdsoft', function (xdevent) {
						xdevent.stopPropagation();
					});
	
				current_time_index = 0;
	
				/**
				 * Runs the callback for each of the specified node's ancestors.
				 *
				 * Return FALSE from the callback to stop ascending.
				 *
				 * @param {DOMNode} node
				 * @param {Function} callback
				 * @returns {undefined}
				 */
				forEachAncestorOf = function (node, callback) {
					do {
						node = node.parentNode;
	
						if (callback(node) === false) {
							break;
						}
					} while (node.nodeName !== 'HTML');
				};
	
				/**
				 * Sets the position of the picker.
				 *
				 * @returns {undefined}
				 */
				setPos = function () {
					var dateInputOffset,
						dateInputElem,
						verticalPosition,
						left,
						position,
						datetimepickerElem,
						dateInputHasFixedAncestor,
						$dateInput,
						windowWidth,
						verticalAnchorEdge,
						datetimepickerCss,
						windowHeight,
						windowScrollTop;
	
					$dateInput = datetimepicker.data('input');
					dateInputOffset = $dateInput.offset();
					dateInputElem = $dateInput[0];
	
					verticalAnchorEdge = 'top';
					verticalPosition = (dateInputOffset.top + dateInputElem.offsetHeight) - 1;
					left = dateInputOffset.left;
					position = "absolute";
	
					windowWidth = $(window).width();
					windowHeight = $(window).height();
					windowScrollTop = $(window).scrollTop();
	
					if ((document.documentElement.clientWidth - dateInputOffset.left) < datepicker.parent().outerWidth(true)) {
						var diff = datepicker.parent().outerWidth(true) - dateInputElem.offsetWidth;
						left = left - diff;
					}
	
					if ($dateInput.parent().css('direction') === 'rtl') {
						left -= (datetimepicker.outerWidth() - $dateInput.outerWidth());
					}
	
					if (options.fixed) {
						verticalPosition -= windowScrollTop;
						left -= $(window).scrollLeft();
						position = "fixed";
					} else {
						dateInputHasFixedAncestor = false;
	
						forEachAncestorOf(dateInputElem, function (ancestorNode) {
							if (window.getComputedStyle(ancestorNode).getPropertyValue('position') === 'fixed') {
								dateInputHasFixedAncestor = true;
								return false;
							}
						});
	
						if (dateInputHasFixedAncestor) {
							position = 'fixed';
	
							//If the picker won't fit entirely within the viewport then display it above the date input.
							if (verticalPosition + datetimepicker.outerHeight() > windowHeight + windowScrollTop) {
								verticalAnchorEdge = 'bottom';
								verticalPosition = (windowHeight + windowScrollTop) - dateInputOffset.top;
							} else {
								verticalPosition -= windowScrollTop;
							}
						} else {
							if (verticalPosition + dateInputElem.offsetHeight > windowHeight + windowScrollTop) {
								verticalPosition = dateInputOffset.top - dateInputElem.offsetHeight + 1;
							}
						}
	
						if (verticalPosition < 0) {
							verticalPosition = 0;
						}
	
						if (left + dateInputElem.offsetWidth > windowWidth) {
							left = windowWidth - dateInputElem.offsetWidth;
						}
					}
	
					datetimepickerElem = datetimepicker[0];
	
					forEachAncestorOf(datetimepickerElem, function (ancestorNode) {
						var ancestorNodePosition;
	
						ancestorNodePosition = window.getComputedStyle(ancestorNode).getPropertyValue('position');
	
						if (ancestorNodePosition === 'relative' && windowWidth >= ancestorNode.offsetWidth) {
							left = left - ((windowWidth - ancestorNode.offsetWidth) / 2);
							return false;
						}
					});
	
					datetimepickerCss = {
						position: position,
						left: left,
						top: '',  //Initialize to prevent previous values interfering with new ones.
						bottom: ''  //Initialize to prevent previous values interfering with new ones.
					};
	
					datetimepickerCss[verticalAnchorEdge] = verticalPosition;
	
					datetimepicker.css(datetimepickerCss);
				};
	
				datetimepicker
					.on('open.xdsoft', function (event) {
						var onShow = true;
						if (options.onShow && $.isFunction(options.onShow)) {
							onShow = options.onShow.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'), event);
						}
						if (onShow !== false) {
							datetimepicker.show();
							setPos();
							$(window)
								.off('resize.xdsoft', setPos)
								.on('resize.xdsoft', setPos);
	
							if (options.closeOnWithoutClick) {
								$([document.body, window]).on('touchstart mousedown.xdsoft', function arguments_callee6() {
									datetimepicker.trigger('close.xdsoft');
									$([document.body, window]).off('touchstart mousedown.xdsoft', arguments_callee6);
								});
							}
						}
					})
					.on('close.xdsoft', function (event) {
						var onClose = true;
						mounth_picker
							.find('.xdsoft_month,.xdsoft_year')
								.find('.xdsoft_select')
									.hide();
						if (options.onClose && $.isFunction(options.onClose)) {
							onClose = options.onClose.call(datetimepicker, _xdsoft_datetime.currentTime, datetimepicker.data('input'), event);
						}
						if (onClose !== false && !options.opened && !options.inline) {
							datetimepicker.hide();
						}
						event.stopPropagation();
					})
					.on('toggle.xdsoft', function () {
						if (datetimepicker.is(':visible')) {
							datetimepicker.trigger('close.xdsoft');
						} else {
							datetimepicker.trigger('open.xdsoft');
						}
					})
					.data('input', input);
	
				timer = 0;
	
				datetimepicker.data('xdsoft_datetime', _xdsoft_datetime);
				datetimepicker.setOptions(options);
	
				function getCurrentValue() {
					var ct = false, time;
	
					if (options.startDate) {
						ct = _xdsoft_datetime.strToDate(options.startDate);
					} else {
						ct = options.value || ((input && input.val && input.val()) ? input.val() : '');
						if (ct) {
							ct = _xdsoft_datetime.strToDateTime(ct);
						} else if (options.defaultDate) {
							ct = _xdsoft_datetime.strToDateTime(options.defaultDate);
							if (options.defaultTime) {
								time = _xdsoft_datetime.strtotime(options.defaultTime);
								ct.setHours(time.getHours());
								ct.setMinutes(time.getMinutes());
							}
						}
					}
	
					if (ct && _xdsoft_datetime.isValidDate(ct)) {
						datetimepicker.data('changed', true);
					} else {
						ct = '';
					}
	
					return ct || 0;
				}
	
				function setMask(options) {
	
					var isValidValue = function (mask, value) {
						var reg = mask
							.replace(/([\[\]\/\{\}\(\)\-\.\+]{1})/g, '\\$1')
							.replace(/_/g, '{digit+}')
							.replace(/([0-9]{1})/g, '{digit$1}')
							.replace(/\{digit([0-9]{1})\}/g, '[0-$1_]{1}')
							.replace(/\{digit[\+]\}/g, '[0-9_]{1}');
						return (new RegExp(reg)).test(value);
					},
					getCaretPos = function (input) {
						try {
							if (document.selection && document.selection.createRange) {
								var range = document.selection.createRange();
								return range.getBookmark().charCodeAt(2) - 2;
							}
							if (input.setSelectionRange) {
								return input.selectionStart;
							}
						} catch (e) {
							return 0;
						}
					},
					setCaretPos = function (node, pos) {
						node = (typeof node === "string" || node instanceof String) ? document.getElementById(node) : node;
						if (!node) {
							return false;
						}
						if (node.createTextRange) {
							var textRange = node.createTextRange();
							textRange.collapse(true);
							textRange.moveEnd('character', pos);
							textRange.moveStart('character', pos);
							textRange.select();
							return true;
						}
						if (node.setSelectionRange) {
							node.setSelectionRange(pos, pos);
							return true;
						}
						return false;
					};
					if(options.mask) {
						input.off('keydown.xdsoft');
					}
					if (options.mask === true) {
															if (typeof moment != 'undefined') {
																		options.mask = options.format
																				.replace(/Y{4}/g, '9999')
																				.replace(/Y{2}/g, '99')
																				.replace(/M{2}/g, '19')
																				.replace(/D{2}/g, '39')
																				.replace(/H{2}/g, '29')
																				.replace(/m{2}/g, '59')
																				.replace(/s{2}/g, '59');
															} else {
																		options.mask = options.format
																				.replace(/Y/g, '9999')
																				.replace(/F/g, '9999')
																				.replace(/m/g, '19')
																				.replace(/d/g, '39')
																				.replace(/H/g, '29')
																				.replace(/i/g, '59')
																				.replace(/s/g, '59');
															}
					}
	
					if ($.type(options.mask) === 'string') {
						if (!isValidValue(options.mask, input.val())) {
							input.val(options.mask.replace(/[0-9]/g, '_'));
							setCaretPos(input[0], 0);
						}
	
						input.on('keydown.xdsoft', function (event) {
							var val = this.value,
								key = event.which,
								pos,
								digit;
	
							if (((key >= KEY0 && key <= KEY9) || (key >= _KEY0 && key <= _KEY9)) || (key === BACKSPACE || key === DEL)) {
								pos = getCaretPos(this);
								digit = (key !== BACKSPACE && key !== DEL) ? String.fromCharCode((_KEY0 <= key && key <= _KEY9) ? key - KEY0 : key) : '_';
	
								if ((key === BACKSPACE || key === DEL) && pos) {
									pos -= 1;
									digit = '_';
								}
	
								while (/[^0-9_]/.test(options.mask.substr(pos, 1)) && pos < options.mask.length && pos > 0) {
									pos += (key === BACKSPACE || key === DEL) ? -1 : 1;
								}
	
								val = val.substr(0, pos) + digit + val.substr(pos + 1);
								if ($.trim(val) === '') {
									val = options.mask.replace(/[0-9]/g, '_');
								} else {
									if (pos === options.mask.length) {
										event.preventDefault();
										return false;
									}
								}
	
								pos += (key === BACKSPACE || key === DEL) ? 0 : 1;
								while (/[^0-9_]/.test(options.mask.substr(pos, 1)) && pos < options.mask.length && pos > 0) {
									pos += (key === BACKSPACE || key === DEL) ? -1 : 1;
								}
	
								if (isValidValue(options.mask, val)) {
									this.value = val;
									setCaretPos(this, pos);
								} else if ($.trim(val) === '') {
									this.value = options.mask.replace(/[0-9]/g, '_');
								} else {
									input.trigger('error_input.xdsoft');
								}
							} else {
								if (([AKEY, CKEY, VKEY, ZKEY, YKEY].indexOf(key) !== -1 && ctrlDown) || [ESC, ARROWUP, ARROWDOWN, ARROWLEFT, ARROWRIGHT, F5, CTRLKEY, TAB, ENTER].indexOf(key) !== -1) {
									return true;
								}
							}
	
							event.preventDefault();
							return false;
						});
					}
				}
	
				_xdsoft_datetime.setCurrentTime(getCurrentValue());
	
				input
					.data('xdsoft_datetimepicker', datetimepicker)
					.on('open.xdsoft focusin.xdsoft mousedown.xdsoft touchstart', function () {
						if (input.is(':disabled') || (input.data('xdsoft_datetimepicker').is(':visible') && options.closeOnInputClick)) {
							return;
						}
						clearTimeout(timer);
						timer = setTimeout(function () {
							if (input.is(':disabled')) {
								return;
							}
	
							triggerAfterOpen = true;
							_xdsoft_datetime.setCurrentTime(getCurrentValue(), true);
							if(options.mask) {
								setMask(options);
							}
							datetimepicker.trigger('open.xdsoft');
						}, 100);
					})
					.on('keydown.xdsoft', function (event) {
						var elementSelector,
							key = event.which;
						if ([ENTER].indexOf(key) !== -1 && options.enterLikeTab) {
							elementSelector = $("input:visible,textarea:visible,button:visible,a:visible");
							datetimepicker.trigger('close.xdsoft');
							elementSelector.eq(elementSelector.index(this) + 1).focus();
							return false;
						}
						if ([TAB].indexOf(key) !== -1) {
							datetimepicker.trigger('close.xdsoft');
							return true;
						}
					})
					.on('blur.xdsoft', function () {
						datetimepicker.trigger('close.xdsoft');
					});
			};
			destroyDateTimePicker = function (input) {
				var datetimepicker = input.data('xdsoft_datetimepicker');
				if (datetimepicker) {
					datetimepicker.data('xdsoft_datetime', null);
					datetimepicker.remove();
					input
						.data('xdsoft_datetimepicker', null)
						.off('.xdsoft');
					$(window).off('resize.xdsoft');
					$([window, document.body]).off('mousedown.xdsoft touchstart');
					if (input.unmousewheel) {
						input.unmousewheel();
					}
				}
			};
			$(document)
				.off('keydown.xdsoftctrl keyup.xdsoftctrl')
				.on('keydown.xdsoftctrl', function (e) {
					if (e.keyCode === CTRLKEY) {
						ctrlDown = true;
					}
				})
				.on('keyup.xdsoftctrl', function (e) {
					if (e.keyCode === CTRLKEY) {
						ctrlDown = false;
					}
				});
	
			this.each(function () {
				var datetimepicker = $(this).data('xdsoft_datetimepicker'), $input;
				if (datetimepicker) {
					if ($.type(opt) === 'string') {
						switch (opt) {
						case 'show':
							$(this).select().focus();
							datetimepicker.trigger('open.xdsoft');
							break;
						case 'hide':
							datetimepicker.trigger('close.xdsoft');
							break;
						case 'toggle':
							datetimepicker.trigger('toggle.xdsoft');
							break;
						case 'destroy':
							destroyDateTimePicker($(this));
							break;
						case 'reset':
							this.value = this.defaultValue;
							if (!this.value || !datetimepicker.data('xdsoft_datetime').isValidDate(dateHelper.parseDate(this.value, options.format))) {
								datetimepicker.data('changed', false);
							}
							datetimepicker.data('xdsoft_datetime').setCurrentTime(this.value);
							break;
						case 'validate':
							$input = datetimepicker.data('input');
							$input.trigger('blur.xdsoft');
							break;
						default:
							if (datetimepicker[opt] && $.isFunction(datetimepicker[opt])) {
								result = datetimepicker[opt](opt2);
							}
						}
					} else {
						datetimepicker
							.setOptions(opt);
					}
					return 0;
				}
				if ($.type(opt) !== 'string') {
					if (!options.lazyInit || options.open || options.inline) {
						createDateTimePicker($(this));
					} else {
						lazyInit($(this));
					}
				}
			});
	
			return result;
		};
	
		$.fn.datetimepicker.defaults = default_options;
	
		function HighlightedDate(date, desc, style) {
			"use strict";
			this.date = date;
			this.desc = desc;
			this.style = style;
		}
	}));
	/*!
	 * jQuery Mousewheel 3.1.13
	 *
	 * Copyright jQuery Foundation and other contributors
	 * Released under the MIT license
	 * http://jquery.org/license
	 */
	
	(function (factory) {
	    if ( true ) {
	        // AMD. Register as an anonymous module.
	        !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ 1)], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	    } else if (typeof exports === 'object') {
	        // Node/CommonJS style for Browserify
	        module.exports = factory;
	    } else {
	        // Browser globals
	        factory(jQuery);
	    }
	}(function ($) {
	
	    var toFix  = ['wheel', 'mousewheel', 'DOMMouseScroll', 'MozMousePixelScroll'],
	        toBind = ( 'onwheel' in document || document.documentMode >= 9 ) ?
	                    ['wheel'] : ['mousewheel', 'DomMouseScroll', 'MozMousePixelScroll'],
	        slice  = Array.prototype.slice,
	        nullLowestDeltaTimeout, lowestDelta;
	
	    if ( $.event.fixHooks ) {
	        for ( var i = toFix.length; i; ) {
	            $.event.fixHooks[ toFix[--i] ] = $.event.mouseHooks;
	        }
	    }
	
	    var special = $.event.special.mousewheel = {
	        version: '3.1.12',
	
	        setup: function() {
	            if ( this.addEventListener ) {
	                for ( var i = toBind.length; i; ) {
	                    this.addEventListener( toBind[--i], handler, false );
	                }
	            } else {
	                this.onmousewheel = handler;
	            }
	            // Store the line height and page height for this particular element
	            $.data(this, 'mousewheel-line-height', special.getLineHeight(this));
	            $.data(this, 'mousewheel-page-height', special.getPageHeight(this));
	        },
	
	        teardown: function() {
	            if ( this.removeEventListener ) {
	                for ( var i = toBind.length; i; ) {
	                    this.removeEventListener( toBind[--i], handler, false );
	                }
	            } else {
	                this.onmousewheel = null;
	            }
	            // Clean up the data we added to the element
	            $.removeData(this, 'mousewheel-line-height');
	            $.removeData(this, 'mousewheel-page-height');
	        },
	
	        getLineHeight: function(elem) {
	            var $elem = $(elem),
	                $parent = $elem['offsetParent' in $.fn ? 'offsetParent' : 'parent']();
	            if (!$parent.length) {
	                $parent = $('body');
	            }
	            return parseInt($parent.css('fontSize'), 10) || parseInt($elem.css('fontSize'), 10) || 16;
	        },
	
	        getPageHeight: function(elem) {
	            return $(elem).height();
	        },
	
	        settings: {
	            adjustOldDeltas: true, // see shouldAdjustOldDeltas() below
	            normalizeOffset: true  // calls getBoundingClientRect for each event
	        }
	    };
	
	    $.fn.extend({
	        mousewheel: function(fn) {
	            return fn ? this.bind('mousewheel', fn) : this.trigger('mousewheel');
	        },
	
	        unmousewheel: function(fn) {
	            return this.unbind('mousewheel', fn);
	        }
	    });
	
	
	    function handler(event) {
	        var orgEvent   = event || window.event,
	            args       = slice.call(arguments, 1),
	            delta      = 0,
	            deltaX     = 0,
	            deltaY     = 0,
	            absDelta   = 0,
	            offsetX    = 0,
	            offsetY    = 0;
	        event = $.event.fix(orgEvent);
	        event.type = 'mousewheel';
	
	        // Old school scrollwheel delta
	        if ( 'detail'      in orgEvent ) { deltaY = orgEvent.detail * -1;      }
	        if ( 'wheelDelta'  in orgEvent ) { deltaY = orgEvent.wheelDelta;       }
	        if ( 'wheelDeltaY' in orgEvent ) { deltaY = orgEvent.wheelDeltaY;      }
	        if ( 'wheelDeltaX' in orgEvent ) { deltaX = orgEvent.wheelDeltaX * -1; }
	
	        // Firefox < 17 horizontal scrolling related to DOMMouseScroll event
	        if ( 'axis' in orgEvent && orgEvent.axis === orgEvent.HORIZONTAL_AXIS ) {
	            deltaX = deltaY * -1;
	            deltaY = 0;
	        }
	
	        // Set delta to be deltaY or deltaX if deltaY is 0 for backwards compatabilitiy
	        delta = deltaY === 0 ? deltaX : deltaY;
	
	        // New school wheel delta (wheel event)
	        if ( 'deltaY' in orgEvent ) {
	            deltaY = orgEvent.deltaY * -1;
	            delta  = deltaY;
	        }
	        if ( 'deltaX' in orgEvent ) {
	            deltaX = orgEvent.deltaX;
	            if ( deltaY === 0 ) { delta  = deltaX * -1; }
	        }
	
	        // No change actually happened, no reason to go any further
	        if ( deltaY === 0 && deltaX === 0 ) { return; }
	
	        // Need to convert lines and pages to pixels if we aren't already in pixels
	        // There are three delta modes:
	        //   * deltaMode 0 is by pixels, nothing to do
	        //   * deltaMode 1 is by lines
	        //   * deltaMode 2 is by pages
	        if ( orgEvent.deltaMode === 1 ) {
	            var lineHeight = $.data(this, 'mousewheel-line-height');
	            delta  *= lineHeight;
	            deltaY *= lineHeight;
	            deltaX *= lineHeight;
	        } else if ( orgEvent.deltaMode === 2 ) {
	            var pageHeight = $.data(this, 'mousewheel-page-height');
	            delta  *= pageHeight;
	            deltaY *= pageHeight;
	            deltaX *= pageHeight;
	        }
	
	        // Store lowest absolute delta to normalize the delta values
	        absDelta = Math.max( Math.abs(deltaY), Math.abs(deltaX) );
	
	        if ( !lowestDelta || absDelta < lowestDelta ) {
	            lowestDelta = absDelta;
	
	            // Adjust older deltas if necessary
	            if ( shouldAdjustOldDeltas(orgEvent, absDelta) ) {
	                lowestDelta /= 40;
	            }
	        }
	
	        // Adjust older deltas if necessary
	        if ( shouldAdjustOldDeltas(orgEvent, absDelta) ) {
	            // Divide all the things by 40!
	            delta  /= 40;
	            deltaX /= 40;
	            deltaY /= 40;
	        }
	
	        // Get a whole, normalized value for the deltas
	        delta  = Math[ delta  >= 1 ? 'floor' : 'ceil' ](delta  / lowestDelta);
	        deltaX = Math[ deltaX >= 1 ? 'floor' : 'ceil' ](deltaX / lowestDelta);
	        deltaY = Math[ deltaY >= 1 ? 'floor' : 'ceil' ](deltaY / lowestDelta);
	
	        // Normalise offsetX and offsetY properties
	        if ( special.settings.normalizeOffset && this.getBoundingClientRect ) {
	            var boundingRect = this.getBoundingClientRect();
	            offsetX = event.clientX - boundingRect.left;
	            offsetY = event.clientY - boundingRect.top;
	        }
	
	        // Add information to the event object
	        event.deltaX = deltaX;
	        event.deltaY = deltaY;
	        event.deltaFactor = lowestDelta;
	        event.offsetX = offsetX;
	        event.offsetY = offsetY;
	        // Go ahead and set deltaMode to 0 since we converted to pixels
	        // Although this is a little odd since we overwrite the deltaX/Y
	        // properties with normalized deltas.
	        event.deltaMode = 0;
	
	        // Add event and delta to the front of the arguments
	        args.unshift(event, delta, deltaX, deltaY);
	
	        // Clearout lowestDelta after sometime to better
	        // handle multiple device types that give different
	        // a different lowestDelta
	        // Ex: trackpad = 3 and mouse wheel = 120
	        if (nullLowestDeltaTimeout) { clearTimeout(nullLowestDeltaTimeout); }
	        nullLowestDeltaTimeout = setTimeout(nullLowestDelta, 200);
	
	        return ($.event.dispatch || $.event.handle).apply(this, args);
	    }
	
	    function nullLowestDelta() {
	        lowestDelta = null;
	    }
	
	    function shouldAdjustOldDeltas(orgEvent, absDelta) {
	        // If this is an older event and the delta is divisable by 120,
	        // then we are assuming that the browser is treating this as an
	        // older mouse wheel event and that we should divide the deltas
	        // by 40 to try and get a more usable deltaFactor.
	        // Side note, this actually impacts the reported scroll distance
	        // in older browsers and can cause scrolling to be slower than native.
	        // Turn this off by setting $.event.special.mousewheel.settings.adjustOldDeltas to false.
	        return special.settings.adjustOldDeltas && orgEvent.type === 'mousewheel' && absDelta % 120 === 0;
	    }
	
	}));


/***/ },
/* 53 */
/*!************************************************************!*\
  !*** F:/item/zbw/~/jquery-mousewheel/jquery.mousewheel.js ***!
  \************************************************************/
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	 * jQuery Mousewheel 3.1.13
	 *
	 * Copyright jQuery Foundation and other contributors
	 * Released under the MIT license
	 * http://jquery.org/license
	 */
	
	(function (factory) {
	    if ( true ) {
	        // AMD. Register as an anonymous module.
	        !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ 1)], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	    } else if (typeof exports === 'object') {
	        // Node/CommonJS style for Browserify
	        module.exports = factory;
	    } else {
	        // Browser globals
	        factory(jQuery);
	    }
	}(function ($) {
	
	    var toFix  = ['wheel', 'mousewheel', 'DOMMouseScroll', 'MozMousePixelScroll'],
	        toBind = ( 'onwheel' in document || document.documentMode >= 9 ) ?
	                    ['wheel'] : ['mousewheel', 'DomMouseScroll', 'MozMousePixelScroll'],
	        slice  = Array.prototype.slice,
	        nullLowestDeltaTimeout, lowestDelta;
	
	    if ( $.event.fixHooks ) {
	        for ( var i = toFix.length; i; ) {
	            $.event.fixHooks[ toFix[--i] ] = $.event.mouseHooks;
	        }
	    }
	
	    var special = $.event.special.mousewheel = {
	        version: '3.1.12',
	
	        setup: function() {
	            if ( this.addEventListener ) {
	                for ( var i = toBind.length; i; ) {
	                    this.addEventListener( toBind[--i], handler, false );
	                }
	            } else {
	                this.onmousewheel = handler;
	            }
	            // Store the line height and page height for this particular element
	            $.data(this, 'mousewheel-line-height', special.getLineHeight(this));
	            $.data(this, 'mousewheel-page-height', special.getPageHeight(this));
	        },
	
	        teardown: function() {
	            if ( this.removeEventListener ) {
	                for ( var i = toBind.length; i; ) {
	                    this.removeEventListener( toBind[--i], handler, false );
	                }
	            } else {
	                this.onmousewheel = null;
	            }
	            // Clean up the data we added to the element
	            $.removeData(this, 'mousewheel-line-height');
	            $.removeData(this, 'mousewheel-page-height');
	        },
	
	        getLineHeight: function(elem) {
	            var $elem = $(elem),
	                $parent = $elem['offsetParent' in $.fn ? 'offsetParent' : 'parent']();
	            if (!$parent.length) {
	                $parent = $('body');
	            }
	            return parseInt($parent.css('fontSize'), 10) || parseInt($elem.css('fontSize'), 10) || 16;
	        },
	
	        getPageHeight: function(elem) {
	            return $(elem).height();
	        },
	
	        settings: {
	            adjustOldDeltas: true, // see shouldAdjustOldDeltas() below
	            normalizeOffset: true  // calls getBoundingClientRect for each event
	        }
	    };
	
	    $.fn.extend({
	        mousewheel: function(fn) {
	            return fn ? this.bind('mousewheel', fn) : this.trigger('mousewheel');
	        },
	
	        unmousewheel: function(fn) {
	            return this.unbind('mousewheel', fn);
	        }
	    });
	
	
	    function handler(event) {
	        var orgEvent   = event || window.event,
	            args       = slice.call(arguments, 1),
	            delta      = 0,
	            deltaX     = 0,
	            deltaY     = 0,
	            absDelta   = 0,
	            offsetX    = 0,
	            offsetY    = 0;
	        event = $.event.fix(orgEvent);
	        event.type = 'mousewheel';
	
	        // Old school scrollwheel delta
	        if ( 'detail'      in orgEvent ) { deltaY = orgEvent.detail * -1;      }
	        if ( 'wheelDelta'  in orgEvent ) { deltaY = orgEvent.wheelDelta;       }
	        if ( 'wheelDeltaY' in orgEvent ) { deltaY = orgEvent.wheelDeltaY;      }
	        if ( 'wheelDeltaX' in orgEvent ) { deltaX = orgEvent.wheelDeltaX * -1; }
	
	        // Firefox < 17 horizontal scrolling related to DOMMouseScroll event
	        if ( 'axis' in orgEvent && orgEvent.axis === orgEvent.HORIZONTAL_AXIS ) {
	            deltaX = deltaY * -1;
	            deltaY = 0;
	        }
	
	        // Set delta to be deltaY or deltaX if deltaY is 0 for backwards compatabilitiy
	        delta = deltaY === 0 ? deltaX : deltaY;
	
	        // New school wheel delta (wheel event)
	        if ( 'deltaY' in orgEvent ) {
	            deltaY = orgEvent.deltaY * -1;
	            delta  = deltaY;
	        }
	        if ( 'deltaX' in orgEvent ) {
	            deltaX = orgEvent.deltaX;
	            if ( deltaY === 0 ) { delta  = deltaX * -1; }
	        }
	
	        // No change actually happened, no reason to go any further
	        if ( deltaY === 0 && deltaX === 0 ) { return; }
	
	        // Need to convert lines and pages to pixels if we aren't already in pixels
	        // There are three delta modes:
	        //   * deltaMode 0 is by pixels, nothing to do
	        //   * deltaMode 1 is by lines
	        //   * deltaMode 2 is by pages
	        if ( orgEvent.deltaMode === 1 ) {
	            var lineHeight = $.data(this, 'mousewheel-line-height');
	            delta  *= lineHeight;
	            deltaY *= lineHeight;
	            deltaX *= lineHeight;
	        } else if ( orgEvent.deltaMode === 2 ) {
	            var pageHeight = $.data(this, 'mousewheel-page-height');
	            delta  *= pageHeight;
	            deltaY *= pageHeight;
	            deltaX *= pageHeight;
	        }
	
	        // Store lowest absolute delta to normalize the delta values
	        absDelta = Math.max( Math.abs(deltaY), Math.abs(deltaX) );
	
	        if ( !lowestDelta || absDelta < lowestDelta ) {
	            lowestDelta = absDelta;
	
	            // Adjust older deltas if necessary
	            if ( shouldAdjustOldDeltas(orgEvent, absDelta) ) {
	                lowestDelta /= 40;
	            }
	        }
	
	        // Adjust older deltas if necessary
	        if ( shouldAdjustOldDeltas(orgEvent, absDelta) ) {
	            // Divide all the things by 40!
	            delta  /= 40;
	            deltaX /= 40;
	            deltaY /= 40;
	        }
	
	        // Get a whole, normalized value for the deltas
	        delta  = Math[ delta  >= 1 ? 'floor' : 'ceil' ](delta  / lowestDelta);
	        deltaX = Math[ deltaX >= 1 ? 'floor' : 'ceil' ](deltaX / lowestDelta);
	        deltaY = Math[ deltaY >= 1 ? 'floor' : 'ceil' ](deltaY / lowestDelta);
	
	        // Normalise offsetX and offsetY properties
	        if ( special.settings.normalizeOffset && this.getBoundingClientRect ) {
	            var boundingRect = this.getBoundingClientRect();
	            offsetX = event.clientX - boundingRect.left;
	            offsetY = event.clientY - boundingRect.top;
	        }
	
	        // Add information to the event object
	        event.deltaX = deltaX;
	        event.deltaY = deltaY;
	        event.deltaFactor = lowestDelta;
	        event.offsetX = offsetX;
	        event.offsetY = offsetY;
	        // Go ahead and set deltaMode to 0 since we converted to pixels
	        // Although this is a little odd since we overwrite the deltaX/Y
	        // properties with normalized deltas.
	        event.deltaMode = 0;
	
	        // Add event and delta to the front of the arguments
	        args.unshift(event, delta, deltaX, deltaY);
	
	        // Clearout lowestDelta after sometime to better
	        // handle multiple device types that give different
	        // a different lowestDelta
	        // Ex: trackpad = 3 and mouse wheel = 120
	        if (nullLowestDeltaTimeout) { clearTimeout(nullLowestDeltaTimeout); }
	        nullLowestDeltaTimeout = setTimeout(nullLowestDelta, 200);
	
	        return ($.event.dispatch || $.event.handle).apply(this, args);
	    }
	
	    function nullLowestDelta() {
	        lowestDelta = null;
	    }
	
	    function shouldAdjustOldDeltas(orgEvent, absDelta) {
	        // If this is an older event and the delta is divisable by 120,
	        // then we are assuming that the browser is treating this as an
	        // older mouse wheel event and that we should divide the deltas
	        // by 40 to try and get a more usable deltaFactor.
	        // Side note, this actually impacts the reported scroll distance
	        // in older browsers and can cause scrolling to be slower than native.
	        // Turn this off by setting $.event.special.mousewheel.settings.adjustOldDeltas to false.
	        return special.settings.adjustOldDeltas && orgEvent.type === 'mousewheel' && absDelta % 120 === 0;
	    }
	
	}));


/***/ },
/* 54 */
/*!******************************!*\
  !*** ./js/src/tpl/setCM.vue ***!
  \******************************/
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_template__ = __webpack_require__(/*! !tpl-html-loader!./../../../../../../~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./setCM.vue */ 55)
	module.exports = __vue_script__ || {}
	if (__vue_template__) {
	(typeof module.exports === "function" ? (module.exports.options || (module.exports.options = {})) : module.exports).template = __vue_template__
	}
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), false)
	  if (!hotAPI.compatible) return
	  var id = "_v-328a0d6f/setCM.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 55 */
/*!*******************************************************************************************************************************!*\
  !*** F:/item/zbw/~/tpl-html-loader!F:/item/zbw/~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./js/src/tpl/setCM.vue ***!
  \*******************************************************************************************************************************/
/***/ function(module, exports) {

	module.exports = "\n\t<form id=\"comSetService-form\" class=\"comSetService-form\" method=\"post\">\n\t\t<div class=\"form-group border-bottom check-box\">\n\t\t\t<div class=\"vertical-top inline-block \">\n\t\t\t\t<input id=\"state1\" class=\"icheck\" type=\"radio\" name=\"service_state\" required {{if service_state == 0}}checked{{/if}} {{if service_state == 2}}disabled{{/if}} value=\"0\">\n\t\t\t</div>\n            <label for=\"state1\" class=\"f-bold vertical-top inline-block {{if service_state == 2}}disabled{{/if}}\">\n                 未签约\n            </label>\n\t    </div>\n\t    <div id=\"state2-box\" class=\"border-bottom clearfix\">\n\t\t    <div class=\"form-group col-xs-12\">\n\t\t\t\t<div class=\"vertical-top inline-block \">\n\t\t\t\t\t<input id=\"state2\" class=\"icheck\" type=\"radio\" name=\"service_state\" {{if service_state == 2}}checked{{/if}} required value=\"2\">\n\t\t\t\t</div>\n\t            <label for=\"state2\" class=\"vertical-top inline-block f-bold\">\n\t                 服务中\n\t            </label>\n\t\t    </div>\n\t\t    <div class=\"clearfix2\">\n\t\t\t    <div class=\"form-group col-xs-6\">\n\t\t            <label class=\"label-left vertical-top w-7em text-right\">\n\t\t                服务有效期：\n\t\t            </label>\n\t\t            <div class=\"inline-block\">\n\t\t                <input id=\"overtime\" type=\"text\" readonly class=\"form-control\" required name=\"overtime\" value=\"{{overtime}}\"> \n\t\t            </div>\n\t\t        </div>\n\t\t        <div class=\"form-group col-xs-6\">\n\t\t            <label class=\"label-left vertical-top w-7em text-right\">\n\t\t                报增减截止日：\n\t\t            </label>\n\t\t            <div class=\"inline-block vertical-top\" >\n\t\t                <select class=\"base-size\" name=\"abort_add_del_date\" required>\n\t\t                \t<option value=\"\">请选择</option>\n\t\t                \t{{each days}}\n\t\t\t\t\t\t\t<option value=\"{{$value}}\" {{if $value == abort_add_del_date}}selected{{/if}}>\n\t\t\t\t\t\t\t\t{{$value}}\n\t\t\t\t\t\t\t</option>\n\t\t                \t{{/each}}\n\t\t                </select>\n\t\t                <div class=\"inline-block vertical-top\">号</div>\n\t\t            </div>\n\t\t        </div>\n\t        </div>\n\t\t        <div class=\"clearfix2\">\n\t\t        <div class=\"form-group col-xs-6\">\n\t\t            <label class=\"label-left vertical-top w-7em text-right\">\n\t\t                账单日：\n\t\t            </label>\n\t\t            <div class=\"inline-block vertical-top\" >\n\t\t                <select class=\"gutter-right\" name=\"bill_month_state\" required>\n\t\t                \t<option value=\"\">请选择</option>\n\t\t                \t<option value=\"0\" {{if 0 == bill_month_state}}selected{{/if}}>当月</option>\n\t\t                \t<option value=\"1\" {{if 1 == bill_month_state}}selected{{/if}}>次月</option>\n\t\t                </select>\n\t\t            </div>\n\t\t            <div class=\"inline-block vertical-top\" >\n\t\t                <select class=\"\" name=\"create_bill_date\" required>\n\t\t                \t<option value=\"\">请选择</option>\n\t\t                \t{{each days}}\n\t\t\t\t\t\t\t<option value=\"{{$value}}\" {{if $value == create_bill_date}}selected{{/if}}>\n\t\t\t\t\t\t\t\t{{$value}}\n\t\t\t\t\t\t\t</option>\n\t\t                \t{{/each}}\n\t\t                </select>\n\t\t            </div>\n\t\t            <div class=\"inline-block vertical-top\">号</div>\n\t\t            \n\t\t        </div>\n\t\t        <div class=\"form-group col-xs-6\">\n\t\t            <label class=\"label-left vertical-top w-7em text-right\">\n\t\t                支付截止日期：\n\t\t            </label>\n\t\t            <div class=\"inline-block vertical-top\" >\n\t\t                <select class=\"gutter-right\" name=\"payment_month_state\" required>\n\t\t                \t<option value=\"\">请选择</option>\n\t\t                \t<option value=\"0\" {{if 0 == payment_month_state}}selected{{/if}}>当月</option>\n\t\t                \t<option value=\"1\" {{if 1 == payment_month_state}}selected{{/if}}>次月</option>\n\t\t                </select>\n\t\t            </div>\n\t\t            <div class=\"inline-block vertical-top\" >\n\t\t                <select class=\"\" name=\"abort_payment_date\" required>\n\t\t                \t<option value=\"\">请选择</option>\n\t\t                \t{{each days}}\n\t\t\t\t\t\t\t<option value=\"{{$value}}\" {{if $value == abort_payment_date}}selected{{/if}}>\n\t\t\t\t\t\t\t\t{{$value}}\n\t\t\t\t\t\t\t</option>\n\t\t                \t{{/each}}\n\t\t                </select>\n\t\t            </div>\n\t\t            号\n\t\t        </div>\n\t\t    </div>\n\t\t\t<div class=\"form-group col-xs-12 check-box\">\n\t            <label class=\"label-left vertical-top w-7em text-right\">\n\t                是否代发工资：\n\t            </label>\n\t            <div class=\"inline-block vertical-top gutter-x\">\n\t                <input id=\"is_salary1\" class=\"icheck\" type=\"radio\" name=\"is_salary\" value=\"1\" required {{if 1 == is_salary}}checked{{/if}}>\n\t                <label for=\"is_salary1\" class=\" ve\n\t                rtical-top inline-block gutter-right\">\n\t                     是\n\t                </label>\n\t                <input id=\"is_salary2\" class=\"icheck\" type=\"radio\" name=\"is_salary\" value=\"0\" required {{if 0 == is_salary}}checked{{/if}}>\n\t                <label for=\"is_salary2\" class=\"vertical-top vertical-top inline-block\">\n\t                     否\n\t                </label>\n\t            </div>\n\t        </div>\n\t        \n\t\t</div>\n\t\t<div class=\"form-group\">\n\t\t\t<div class=\"vertical-top inline-block \">\n\t\t\t\t<input id=\"state3\" class=\"icheck\" type=\"radio\" name=\"service_state\" required value=\"3\">\n\t\t\t</div>\n            <label for=\"state3\" class=\"f-bold vertical-top inline-block\">\n                 服务结束\n            </label>\n\t    </div>\n\t    <input type=\"hidden\" name=\"id\" value=\"{{id}}\">\n\t</form>\n";

/***/ },
/* 56 */
/*!************************************!*\
  !*** ./js/src/tpl/addLocation.vue ***!
  \************************************/
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_template__ = __webpack_require__(/*! !tpl-html-loader!./../../../../../../~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./addLocation.vue */ 57)
	module.exports = __vue_script__ || {}
	if (__vue_template__) {
	(typeof module.exports === "function" ? (module.exports.options || (module.exports.options = {})) : module.exports).template = __vue_template__
	}
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), false)
	  if (!hotAPI.compatible) return
	  var id = "_v-663ea94e/addLocation.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 57 */
/*!*************************************************************************************************************************************!*\
  !*** F:/item/zbw/~/tpl-html-loader!F:/item/zbw/~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./js/src/tpl/addLocation.vue ***!
  \*************************************************************************************************************************************/
/***/ function(module, exports) {

	module.exports = "\n<form id=\"addLocation-form\" class=\"addLocation-form\">\n\t<div class=\"form-group col-xs-12\">\n        <label class=\"label-left vertical-top w-10em text-right\">\n            参保地：\n        </label>\n        <div class=\"inline-block vertical-top\" >\n            <select class=\"gutter-right base-size\" name=\"location\" required>\n            \t<option value=\"\">请选择</option>\n            \t{{each locationArr}}\n            \t<option value=\"{{$value.location}}\" {{if location == $value.location}}selected{{/if}}>{{$value.name}}</option>\n            \t{{/each}}\n            </select>\n        </div>\n        \n    </div>\n\t<div class=\"form-group col-xs-12\">\n        <label class=\"label-left vertical-top w-10em text-right\">\n            社保/公积金服务费：\n        </label>\n        <div class=\"inline-block\">\n            <input id=\"ss_service_price\" type=\"text\" class=\"form-control\" required name=\"ss_service_price\" value=\"{{ss_service_price}}\"> \n        </div>\n        <div class=\"inline-block\">元/人/月</div>\n    </div>\n    <div class=\"form-group col-xs-12\">\n        <label class=\"label-left vertical-top w-10em text-right\">\n            代发工资服务费：\n        </label>\n        <div class=\"inline-block\">\n            <input id=\"af_service_price\" type=\"text\" class=\"form-control\" required name=\"af_service_price\" value=\"{{af_service_price}}\"> \n        </div>\n        <div class=\"inline-block\">元/人/月</div>\n    </div>\n    <input type=\"hidden\" name=\"id\" value=\"{{id}}\">\n    <input type=\"hidden\" name=\"location_id\" value=\"{{location_id}}\">\n    \n</form>\n";

/***/ }
]);
//# sourceMappingURL=CM.bundle.js.map