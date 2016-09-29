webpackJsonp([2],[
/* 0 */
/*!***************************!*\
  !*** ./js/entry/order.js ***!
  \***************************/
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
		payroll: __webpack_require__(/*! page/payroll.js */ 63),
		addAudit: __webpack_require__(/*! page/addAudit.js */ 105)
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
/* 16 */,
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
/* 35 */,
/* 36 */,
/* 37 */,
/* 38 */,
/* 39 */,
/* 40 */,
/* 41 */,
/* 42 */,
/* 43 */,
/* 44 */,
/* 45 */,
/* 46 */,
/* 47 */,
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
/* 49 */,
/* 50 */,
/* 51 */,
/* 52 */,
/* 53 */,
/* 54 */,
/* 55 */,
/* 56 */,
/* 57 */,
/* 58 */,
/* 59 */,
/* 60 */,
/* 61 */,
/* 62 */,
/* 63 */
/*!********************************!*\
  !*** ./js/src/page/payroll.js ***!
  \********************************/
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($, layer) {// 代发工资
	var template = __webpack_require__(/*! art-template */ 26),
	    validateFn = __webpack_require__(/*! modules/validate */ 32),
	    icheckFn = __webpack_require__(/*! modules/icheck */ 37),
	    orderAct = __webpack_require__(/*! modules/actions/order */ 64),
	    tool = __webpack_require__(/*! lib/tool */ 65),
	    BIN = __webpack_require__(/*! plug/bankcardinfo */ 66);
	
	var payroll = {
	    bankList: __webpack_require__(/*! modules/bankData.js */ 100),
	    init: function() {
	
	        var self = this,
	            $payrollForm = $('#payroll-form');
	
	        icheckFn.init().checkAll();
	
	        // 挂起提示
	        $('[data-toggle="popover"]').popover({
	            container: 'body',
	            placement: 'top',
	            title: '提示',
	            trigger: 'hover'
	        });
	
	        $(window).resize(function(event) {
	            tool.fixedTableHeight();
	        }).resize();
	
	        $('body').on('change', '#account', function() {
	            var $bankSelect = $('#bank-select');
	
	            BIN.getBankBin(this.value, function(err, data) {
	                if (!err) {
	                    $bankSelect.val(data.bankName) /*.prop('disabled', true);*/
	                }
	                /* else{
	                    $bankSelect.prop('disabled', false);
	                }*/
	
	            })
	        })
	
	        // 导出订单
	        /*      $('[data-act="exportPayroll"]').click(function() {
	                    self.exportPayroll($payrollForm.serialize())
	                });*/
	
	        //更新工资状态
	        $('[data-act="updatePayrollStatus"]').click(function() {
	            self.updatePayrollStatus($payrollForm.serializeArray())
	        });
	
	        //查看工资
	        $('[data-act="viewPayroll"]').click(function() {
	                var data = $(this).data();
	
	                orderAct.viewPayroll(data, function(json) {
	
	                    var html = template.render(__webpack_require__(/*! tpl/payrollView.vue */ 101).template)(json.data);
	
	                    layer.open({
	                        type: 1,
	                        title: '查看工资',
	                        area: ['570px', 'auto'], //宽高
	                        content: html
	                    });
	                    self.validatePayroll($('#payroll-audit-form'));
	                })
	            })
	            // 审核通过
	        $('[data-act="verifyPayroll"]').click(function() {
	            var data = $(this).data();
	
	            orderAct.viewPayroll(data, function(json) {
	                var ret = json.data;
	
	                ret.bankList = self.bankList;
	
	                var html = template.render(__webpack_require__(/*! tpl/payrollAudit.vue */ 103).template)(ret);
	
	                layer.open({
	                    type: 1,
	                    title: '审核工资',
	                    area: ['570px', 'auto'], //宽高
	                    btn: ['确定', '取消'],
	                    skin: 'layer-form',
	                    content: html,
	                    yes: function() {
	                        var $form = $('#payroll-audit-form');
	                        self.validatePayroll($form);
	                        $form.submit();
	                    }
	                });
	
	                self.validatePayroll($('#payroll-audit-form'));
	                icheckFn.init().checkAll();
	            })
	
	        });
	
	        // 取消审核
	        $('[data-act="delPayroll"]').click(function() {
	            var data = $(this).data();
	
	            layer.confirm('确认撤销？', function() {
	                orderAct.delPayroll(data, function() {
	                    layer.msg('撤销成功');
	                    location.reload();
	                })
	            })
	
	        });
	
	
	        $('body').on('change', '.J-counter', function() {
	            var add = cut = 0;
	
	            $('.J-counter-add').each(function() {
	                var num = parseFloat(this.value) || 0;
	                add += num;
	            })
	
	            $('.J-counter-cut').each(function() {
	                var num = parseFloat(this.value) || 0;
	                cut += num;
	            })
	
	            $('#wages-txt').html(add - cut)
	        })
	    },
	    // 工资审核
	    validatePayroll: function($form) {
	        $form.validate({
	            submitHandler: function() {
	                var formData = $form.serializeArray();
	
	                orderAct.savePayroll(formData, function() {
	                    layer.msg('审核成功', {
	                        end: function() {
	                            location.reload();
	                        }
	                    });
	
	                })
	
	                return false;
	            },
	            rules: {
	                wages: {
	                    number: true
	                },
	                deduction_income_tax: {
	                    number: true
	                },
	                deduction_social_insurance: {
	                    number: true
	                },
	                deduction_provident_fund: {
	                    number: true
	                },
	                replacement: {
	                    number: true
	                },
	                deduction_order: {
	                    number: true
	                }
	            },
	            messages: {
	                bank: {
	                    required: '请选择银行'
	                },
	                branch: {
	                    required: '请输入支行名称'
	                },
	                account: {
	                    required: '请输入银行卡号'
	                },
	                date: {
	                    required: '请输入工资年月'
	                },
	                wages: {
	                    required: '请输入应发工资'
	                },
	                deduction_income_tax: {
	                    required: '请输入个人所得税'
	                },
	                deduction_social_insurance: {
	                    required: '请输入扣个人社保'
	                },
	                deduction_provident_fund: {
	                    required: '请输入扣个人公积金'
	                },
	                replacement: {
	                    required: '请输入补发'
	                },
	                deduction_order: {
	                    required: '请输入其他扣除'
	                },
	                state: {
	                    required: '请选择状态'
	                }
	            }
	        })
	    },
	    // 导出工资单
	    /*    exportPayroll: function(url){
	            if(url === ''){
	                layer.alert('请选择工资单');
	            } else{
	                location.href = '/Service-Service-index?'+ url;
	            }
	        },*/
	    // 防止多次提交
	    ajaxChace: {},
	    /**
	     * 更新工资状态
	     * @param  {object}   ids id的集合
	     * @param  {Function} success  成功回调
	     * @param  {Function} fail 失败回调
	     */
	    updatePayrollStatus: function(ids, success, fail) {
	        var self = this;
	        var url = '/Order-comOrderDetail';
	
	        if (!ids.length) {
	            layer.alert('请选择工资单');
	        } else {
	            // 防止多次提交
	            if (self.ajaxChace[url]) {
	                return;
	            }
	
	            $.ajax({
	                type: 'post',
	                url: url,
	                dataType: 'json',
	                beforeSend: function(ajaxObj, opts) {
	                    self.ajaxChace[url] = true;
	                }
	            }).success(function(data) {
	                var status = data.status - 0;
	                var msg = data.msg;
	
	                if (status === 0) {
	                    if (typeof success === 'function') {
	                        success(data);
	                    }
	                }
	
	                if (msg) {
	                    layer.alert(msg)
	                }
	
	            }).fail(function() {
	                if (typeof fail === 'function') {
	                    fail();
	                }
	            }).complete(function() {
	                self.ajaxChace[url] = false;
	            })
	        }
	    },
	    verifyPayroll: function(success) {
	        var self = this;
	        var url = '/Order-payrol';
	
	        if (self.ajaxChace[url]) {
	            return;
	        }
	
	        $.ajax({
	            type: 'post',
	            url: url,
	            dataType: 'json',
	            beforeSend: function(ajaxObj, opts) {
	                self.ajaxChace[url] = true;
	            }
	        }).success(function(data) {
	            var status = data.status - 0;
	            var msg = data.msg;
	
	            if (status === 0) {
	                if (typeof success === 'function') {
	                    success(data);
	                }
	            } else if (msg) {
	                layer.alert(msg)
	            }
	
	        }).complete(function() {
	            self.ajaxChace[url] = false;
	        })
	    }
	}
	
	module.exports = payroll;
	
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! jquery */ 1), __webpack_require__(/*! layer */ 17)))

/***/ },
/* 64 */
/*!*****************************************!*\
  !*** ./js/src/modules/actions/order.js ***!
  \*****************************************/
/***/ function(module, exports, __webpack_require__) {

	var baseAct = __webpack_require__(/*! ./base */ 36);
	
	
	module.exports = {
		// 查看工资
		viewPayroll: function(data, cb){
			var opts = {
				data: data,
				url: '/Service-Order-salaryAudit'
			}
			return baseAct.ajax(opts,cb);
		},
		savePayroll: function(data, cb){
			var opts = {
				data: data,
				url: '/Service-Order-salaryData'
			}
			return baseAct.ajax(opts,cb);
		},
		// 撤销
		delPayroll: function(data, cb){
			var opts = {
				data: data,
				url: '/Service-Order-salaryRevoke'
			}
			return baseAct.ajax(opts,cb);
		}
	}

/***/ },
/* 65 */
/*!****************************!*\
  !*** ./js/src/lib/tool.js ***!
  \****************************/
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {
	// 生成一个连续的数字
	exports.createNumbers = function(begin, end){
		var arr = [];
	
		while(begin <= end){
			arr.push(begin);
			begin++
		}
	
		return arr;
	}
	
	// 检查表单是否 被修改过
	exports.formIsDirty = function(form) {
	
		var i = 0,
			len = form ? form.elements.length : 0;
	
	    for (; i < len; i++) {
	    	
	        var element = form.elements[i];
	        var type = element.type;
	
	        if (type == "checkbox" || type == "radio") {
	            if (element.checked != element.defaultChecked) {
	                return true;
	            }
	        } else if (type == "hidden" || type == "password" ||
	            type == "text" || type == "textarea") {
	            if (element.value != element.defaultValue) {
	                return true;
	            }
	        } else if (type == "select-one" || type == "select-multiple") {
	            for (var j = 0; j < element.options.length; j++) {
	                if (element.options[j].selected !=
	                    element.options[j].defaultSelected) {
	                    return true;
	                }
	            }
	        }
	    }
	    return false;
	}
	
	exports.fixedTableHeight = function(){
	    $('td>table').each(function(){
	        $(this).height($(this).parent().height())
	    })
	}
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! jquery */ 1)))

/***/ },
/* 66 */
/*!*************************************!*\
  !*** ./js/src/plug/bankcardinfo.js ***!
  \*************************************/
/***/ function(module, exports, __webpack_require__) {

	//cardType:DC->储蓄卡,CC->信用卡
	(function(){
		var root = this;
		var cardTypeMap = {
			DC:"储蓄卡",
			CC:"信用卡",
			SCC:"准贷记卡",
			PC:"预付费卡"
		};
		function isFunction(fn){
			return Object.prototype.toString.call(fn) === '[object Function]';
		}
		function extend(target,source){
			var result = {};
			var key;
			target = target || {};
			source = source || {};
			for(key in target){
				if(target.hasOwnProperty(key)){
					result[key] = target[key];
				}
			}
			for(key in source){
				if(source.hasOwnProperty(key)){
					result[key] = source[key];
				}
			}
			return result;
		}
		function getCardTypeName(cardType){
			if(cardTypeMap[cardType]){
				return cardTypeMap[cardType]
			}
			return undefined;
		}
		var bankcardList = [
			{
				bankName:"中国邮政储蓄银行",
				bankCode:"PSBC",
				patterns:[
					{
						reg:/^(621096|621098|622150|622151|622181|622188|622199|955100|621095|620062|621285|621798|621799|621797|620529|621622|621599|621674|623218|623219)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(62215049|62215050|62215051|62218850|62218851|62218849)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622812|622810|622811|628310|625919)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"中国工商银行",
				bankCode:"ICBC",
				patterns:[
					{
						reg:/^(620200|620302|620402|620403|620404|620406|620407|620409|620410|620411|620412|620502|620503|620405|620408|620512|620602|620604|620607|620611|620612|620704|620706|620707|620708|620709|620710|620609|620712|620713|620714|620802|620711|620904|620905|621001|620902|621103|621105|621106|621107|621102|621203|621204|621205|621206|621207|621208|621209|621210|621302|621303|621202|621305|621306|621307|621309|621311|621313|621211|621315|621304|621402|621404|621405|621406|621407|621408|621409|621410|621502|621317|621511|621602|621603|621604|621605|621608|621609|621610|621611|621612|621613|621614|621615|621616|621617|621607|621606|621804|621807|621813|621814|621817|621901|621904|621905|621906|621907|621908|621909|621910|621911|621912|621913|621915|622002|621903|622004|622005|622006|622007|622008|622010|622011|622012|621914|622015|622016|622003|622018|622019|622020|622102|622103|622104|622105|622013|622111|622114|622017|622110|622303|622304|622305|622306|622307|622308|622309|622314|622315|622317|622302|622402|622403|622404|622313|622504|622505|622509|622513|622517|622502|622604|622605|622606|622510|622703|622715|622806|622902|622903|622706|623002|623006|623008|623011|623012|622904|623015|623100|623202|623301|623400|623500|623602|623803|623901|623014|624100|624200|624301|624402|623700|624000)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622200|622202|622203|622208|621225|620058|621281|900000|621558|621559|621722|621723|620086|621226|621618|620516|621227|621288|621721|900010|623062|621670|621720|621379|621240|621724|621762|621414|621375|622926|622927|622928|622929|622930|622931|621733|621732|621372|621369|621763)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(402791|427028|427038|548259|621376|621423|621428|621434|621761|621749|621300|621378|622944|622949|621371|621730|621734|621433|621370|621764|621464|621765|621750|621377|621367|621374|621731|621781)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(9558)\d{15}$/g,
						cardType:"DC"
					},
					{
						reg:/^(370246|370248|370249|370247|370267|374738|374739)\d{9}$/g,
						cardType:"CC"
					},
					{
						reg:/^(427010|427018|427019|427020|427029|427030|427039|438125|438126|451804|451810|451811|458071|489734|489735|489736|510529|427062|524091|427064|530970|530990|558360|524047|525498|622230|622231|622232|622233|622234|622235|622237|622239|622240|622245|622238|451804|451810|451811|458071|628288|628286|622206|526836|513685|543098|458441|622246|544210|548943|356879|356880|356881|356882|528856|625330|625331|625332|622236|524374|550213|625929|625927|625939|625987|625930|625114|622159|625021|625022|625932|622889|625900|625915|625916|622171|625931|625113|625928|625914|625986|625925|625921|625926|625942|622158|625917|625922|625934|625933|625920|625924|625017|625018|625019)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(45806|53098|45806|53098)\d{11}$/g,
						cardType:"CC"
					},
					{
						reg:/^(622210|622211|622212|622213|622214|622220|622223|622225|622229|622215|622224)\d{10}$/g,
						cardType:"SCC"
					},
					{
						reg:/^(620054|620142|620184|620030|620050|620143|620149|620124|620183|620094|620186|620148|620185)\d{10}$/g,
						cardType:"PC"
					},
					{
						reg:/^(620114|620187|620046)\d{13}$/g,
						cardType:"PC"
					}
				]
			},
			{
				bankName:"中国农业银行",
				bankCode:"ABC",
				patterns:[
					{
						reg:/^(622841|622824|622826|622848|620059|621282|622828|622823|621336|621619|622821|622822|622825|622827|622845|622849|623018|623206|621671|622840|622843|622844|622846|622847|620501)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(95595|95596|95597|95598|95599)\d{14}$/g,
						cardType:"DC"
					},
					{
						reg:/^(103)\d{16}$/g,
						cardType:"DC"
					},
					{
						reg:/^(403361|404117|404118|404119|404120|404121|463758|519412|519413|520082|520083|552599|558730|514027|622836|622837|628268|625996|625998|625997|622838|625336|625826|625827|544243|548478|628269)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(622820|622830)\d{10}$/g,
						cardType:"SCC"
					}
				]
			},
			{
				bankName:"中国银行",
				bankCode:"BOC",
				patterns:[
					{
						reg:/^(621660|621661|621662|621663|621665|621667|621668|621669|621666|456351|601382|621256|621212|621283|620061|621725|621330|621331|621332|621333|621297|621568|621569|621672|623208|621620|621756|621757|621758|621759|621785|621786|621787|621788|621789|621790|622273|622274|622771|622772|622770|621741|621041)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(621293|621294|621342|621343|621364|621394|621648|621248|621215|621249|621231|621638|621334|621395|623040|622348)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(625908|625910|625909|356833|356835|409665|409666|409668|409669|409670|409671|409672|512315|512316|512411|512412|514957|409667|438088|552742|553131|514958|622760|628388|518377|622788|628313|628312|622750|622751|625145|622479|622480|622789|625140|622346|622347)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(518378|518379|518474|518475|518476|524865|525745|525746|547766|558868|622752|622753|622755|524864|622757|622758|622759|622761|622762|622763|622756|622754|622764|622765|558869|625905|625906|625907|625333)\d{10}$/g,
						cardType:"SCC"
					},
					{
						reg:/^(53591|49102|377677)\d{11}$/g,
						cardType:"SCC"
					},
					{
						reg:/^(620514|620025|620026|620210|620211|620019|620035|620202|620203|620048|620515|920000)\d{10}$/g,
						cardType:"PC"
					},
					{
						reg:/^(620040|620531|620513|921000|620038)\d{13}$/g,
						cardType:"PC"
					}
				]
			},
			{
				bankName:"中国建设银行",
				bankCode:"CCB",
				patterns:[
					{
						reg:/^(621284|436742|589970|620060|621081|621467|621598|621621|621700|622280|622700|623211|623668)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(421349|434061|434062|524094|526410|552245|621080|621082|621466|621488|621499|622966|622988|622382|621487|621083|621084|620107)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(436742193|622280193)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(553242)\d{12}$/g,
						cardType:"CC"
					},
					{
						reg:/^(625362|625363|628316|628317|356896|356899|356895|436718|436738|436745|436748|489592|531693|532450|532458|544887|552801|557080|558895|559051|622166|622168|622708|625964|625965|625966|628266|628366|622381|622675|622676|622677)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(5453242|5491031|5544033)\d{11}$/g,
						cardType:"CC"
					},
					{
						reg:/^(622725|622728|436728|453242|491031|544033|622707|625955|625956)\d{10}$/g,
						cardType:"SCC"
					},
					{
						reg:/^(53242|53243)\d{11}$/g,
						cardType:"SCC"
					}
				]
			},
			{
				bankName:"中国交通银行",
				bankCode:"COMM",
				patterns:[
					{
						reg:/^(622261|622260|622262|621002|621069|621436|621335)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(620013)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(405512|601428|405512|601428|622258|622259|405512|601428)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(49104|53783)\d{11}$/g,
						cardType:"CC"
					},
					{
						reg:/^(434910|458123|458124|520169|522964|552853|622250|622251|521899|622253|622656|628216|622252|955590|955591|955592|955593|628218|625028|625029)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(622254|622255|622256|622257|622284)\d{10}$/g,
						cardType:"SCC"
					},
					{
						reg:/^(620021|620521)\d{13}$/g,
						cardType:"PC"
					}
				]
			},
			{
				bankName:"招商银行",
				bankCode:"CMB",
				patterns:[
					{
						reg:/^(402658|410062|468203|512425|524011|622580|622588|622598|622609|95555|621286|621483|621485|621486|621299)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(690755)\d{9}$/g,
						cardType:"DC"
					},
					{
						reg:/^(690755)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(356885|356886|356887|356888|356890|439188|439227|479228|479229|521302|356889|545620|545621|545947|545948|552534|552587|622575|622576|622577|622578|622579|545619|622581|622582|545623|628290|439225|518710|518718|628362|439226|628262|625802|625803)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(370285|370286|370287|370289)\d{9}$/g,
						cardType:"CC"
					},
					{
						reg:/^(620520)\d{13}$/g,
						cardType:"PC"
					}
				]
			},
			{
				bankName:"中国民生银行",
				bankCode:"CMBC",
				patterns:[
					{
						reg:/^(622615|622616|622618|622622|622617|622619|415599|421393|421865|427570|427571|472067|472068|622620)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(545392|545393|545431|545447|356859|356857|407405|421869|421870|421871|512466|356856|528948|552288|622600|622601|622602|517636|622621|628258|556610|622603|464580|464581|523952|545217|553161|356858|622623|625912|625913|625911)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(377155|377152|377153|377158)\d{9}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"中国光大银行",
				bankCode:"CEB",
				patterns:[
					{
						reg:/^(303)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(90030)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(620535)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(620085|622660|622662|622663|622664|622665|622666|622667|622669|622670|622671|622672|622668|622661|622674|622673|620518|621489|621492)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(356837|356838|486497|622657|622685|622659|622687|625978|625980|625981|625979|356839|356840|406252|406254|425862|481699|524090|543159|622161|622570|622650|622655|622658|625975|625977|628201|628202|625339|625976)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"中信银行",
				bankCode:"CITIC",
				patterns:[
					{
						reg:/^(433670|433680|442729|442730|620082|622690|622691|622692|622696|622698|622998|622999|433671|968807|968808|968809|621771|621767|621768|621770|621772|621773|622453|622456)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622459)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(376968|376969|376966)\d{9}$/g,
						cardType:"CC"
					},
					{
						reg:/^(400360|403391|403392|404158|404159|404171|404172|404173|404174|404157|433667|433668|433669|514906|403393|520108|433666|558916|622678|622679|622680|622688|622689|628206|556617|628209|518212|628208|356390|356391|356392|622916|622918|622919)\d{10}$/g,
						cardType:"CC"			
					}
				]
			},
			{
				bankName:"华夏银行",
				bankCode:"HXBANK",
				patterns:[
					{
						reg:/^(622630|622631|622632|622633|999999|621222|623020|623021|623022|623023)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(523959|528709|539867|539868|622637|622638|628318|528708|622636|625967|625968|625969)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"深发/平安银行",
				bankCode:"SPABANK",
				patterns:[
					{
						reg:/^(621626|623058)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(602907|622986|622989|622298|627069|627068|627066|627067|412963|415752|415753|622535|622536|622538|622539|998800|412962|622983)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(531659|622157|528020|622155|622156|526855|356869|356868|625360|625361|628296|435744|435745|483536|622525|622526|998801|998802)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(620010)\d{10}$/g,
						cardType:"PC"
					}
				]
			},
			{
				bankName:"兴业银行",
				bankCode:"CIB",
				patterns:[
					{
						reg:/^(438589)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(90592)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(966666|622909|438588|622908)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(461982|486493|486494|486861|523036|451289|527414|528057|622901|622902|622922|628212|451290|524070|625084|625085|625086|625087|548738|549633|552398|625082|625083|625960|625961|625962|625963)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(620010)\d{10}$/g,
						cardType:"PC"
					}
				]
			},
			{
				bankName:"上海银行",
				bankCode:"SHBANK",
				patterns:[
					{
						reg:/^(621050|622172|622985|622987|620522|622267|622278|622279|622468|622892|940021)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(438600)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(356827|356828|356830|402673|402674|486466|519498|520131|524031|548838|622148|622149|622268|356829|622300|628230|622269|625099|625953)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"浦东发展银行",
				bankCode:"SPDB",
				patterns:[
					{
						reg:/^(622516|622517|622518|622521|622522|622523|984301|984303|621352|621793|621795|621796|621351|621390|621792|621791)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(84301|84336|84373|84385|84390|87000|87010|87030|87040|84380|84361|87050|84342)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(356851|356852|404738|404739|456418|498451|515672|356850|517650|525998|622177|622277|628222|622500|628221|622176|622276|622228|625957|625958|625993|625831)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(622520|622519)\d{10}$/g,
						cardType:"SCC"
					},
					{
						reg:/^(620530)\d{13}$/g,
						cardType:"PC"
					}
				]
			},
			{
				bankName:"广发银行",
				bankCode:"GDB",
				patterns:[
					{
						reg:/^(622516|622517|622518|622521|622522|622523|984301|984303|621352|621793|621795|621796|621351|621390|621792|621791)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622568|6858001|6858009|621462)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(9111)\d{15}$/g,
						cardType:"DC"
					},
					{
						reg:/^(406365|406366|428911|436768|436769|436770|487013|491032|491033|491034|491035|491036|491037|491038|436771|518364|520152|520382|541709|541710|548844|552794|493427|622555|622556|622557|622558|622559|622560|528931|558894|625072|625071|628260|628259|625805|625806|625807|625808|625809|625810)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(685800|6858000)\d{13}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"渤海银行",
				bankCode:"BOHAIB",
				patterns:[
					{
						reg:/^(621268|622684|622884|621453)\d{10}$/g,
						cardType:"DC"
					}
				]
			},
			{
				bankName:"广州银行",
				bankCode:"GCB",
				patterns:[
					{
						reg:/^(603445|622467|940016|621463)\d{13}$/g,
						cardType:"DC"
					}
				]
			},
			{
				bankName:"金华银行",
				bankCode:"JHBANK",
				patterns:[
					{
						reg:/^(622449|940051)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622450|628204)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"温州银行",
				bankCode:"WZCB",
				patterns:[
					{
						reg:/^(621977)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622868|622899|628255)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"徽商银行",
				bankCode:"HSBANK",
				patterns:[
					{
						reg:/^(622877|622879|621775|623203)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(603601|622137|622327|622340|622366)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628251|622651|625828)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"江苏银行",
				bankCode:"JSBANK",
				patterns:[
					{
						reg:/^(621076|622173|622131|621579|622876)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(504923|622422|622447|940076)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628210|622283|625902)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"南京银行",
				bankCode:"NJCB",
				patterns:[
					{
						reg:/^(621777|622305|621259)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622303|628242|622595|622596)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"宁波银行",
				bankCode:"NBBANK",
				patterns:[
					{
						reg:/^(621279|622281|622316|940022)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(621418)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(625903|622778|628207|512431|520194|622282|622318)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"北京银行",
				bankCode:"BJBANK",
				patterns:[
					{
						reg:/^(623111|421317|422161|602969|422160|621030|621420|621468)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(522001|622163|622853|628203|622851|622852)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"北京农村商业银行",
				bankCode:"BJRCB",
				patterns:[
					{
						reg:/^(620088|621068|622138|621066|621560)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(625526|625186|628336)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"汇丰银行",
				bankCode:"HSBC",
				patterns:[
					{
						reg:/^(622946)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622406|621442)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622407|621443)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622360|622361|625034|625096|625098)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"渣打银行",
				bankCode:"SCB",
				patterns:[
					{
						reg:/^(622948|621740|622942|622994)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622482|622483|622484)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"花旗银行",
				bankCode:"CITI",
				patterns:[
					{
						reg:/^(621062|621063)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(625076|625077|625074|625075|622371|625091)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"东亚银行",
				bankCode:"HKBEA",
				patterns:[
					{
						reg:/^(622933|622938|623031|622943|621411)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622372|622471|622472|622265|622266|625972|625973)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(622365)\d{11}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"广东华兴银行",
				bankCode:"GHB",
				patterns:[
					{
						reg:/^(621469|621625)\d{13}$/g,
						cardType:"DC"
					}
				]
			},
			{
				bankName:"深圳农村商业银行",
				bankCode:"SRCB",
				patterns:[
					{
						reg:/^(622128|622129|623035)\d{10}$/g,
						cardType:"DC"
					}
				]
			},
			{
				bankName:"广州农村商业银行股份有限公司",
				bankCode:"GZRCU",
				patterns:[
					{
						reg:/^(909810|940035|621522|622439)\d{12}$/g,
						cardType:"DC"
					}
				]
			},
			{
				bankName:"东莞农村商业银行",
				bankCode:"DRCBCL",
				patterns:[
					{
						reg:/^(622328|940062|623038)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(625288|625888)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"东莞市商业银行",
				bankCode:"BOD",
				patterns:[
					{
						reg:/^(622333|940050)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(621439|623010)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622888)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"广东省农村信用社联合社",
				bankCode:"GDRCC",
				patterns:[
					{
						reg:/^(622302)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622477|622509|622510|622362|621018|621518)\d{13}$/g,
						cardType:"DC"
					}
				]
			},
			{
				bankName:"大新银行",
				bankCode:"DSB",
				patterns:[
					{
						reg:/^(622297|621277)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622375|622489)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622293|622295|622296|622373|622451|622294|625940)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"永亨银行",
				bankCode:"WHB",
				patterns:[
					{
						reg:/^(622871|622958|622963|622957|622861|622932|622862|621298)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622798|625010|622775|622785)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"星展银行香港有限公司",
				bankCode:"DBS",
				patterns:[
					{
						reg:/^(621016|621015)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622487|622490|622491|622492)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622487|622490|622491|622492|621744|621745|621746|621747)\d{11}$/g,
						cardType:"DC"
					}
				]
			},
			{
				bankName:"恒丰银行",
				bankCode:"EGBANK",
				patterns:[
					{
						reg:/^(623078)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622384|940034)\d{11}$/g,
						cardType:"DC"
					}
				]
			},
			{
				bankName:"天津市商业银行",
				bankCode:"TCCB",
				patterns:[
					{
						reg:/^(940015|622331)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(6091201)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622426|628205)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"浙商银行",
				bankCode:"CZBANK",
				patterns:[
					{
						reg:/^(621019|622309|621019)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(6223091100|6223092900|6223093310|6223093320|6223093330|6223093370|6223093380|6223096510|6223097910)\d{9}$/g,
						cardType:"DC"
					}
				]
			},
			{
				bankName:"南洋商业银行",
				bankCode:"NCB",
				patterns:[
					{
						reg:/^(621213|621289|621290|621291|621292|621042|621743)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(623041|622351)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(625046|625044|625058|622349|622350)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(620208|620209|625093|625095)\d{10}$/g,
						cardType:"PC"
					}
				]
			},
			{
				bankName:"厦门银行",
				bankCode:"XMBANK",
				patterns:[
					{
						reg:/^(622393|940023)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(6886592)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(623019|621600|)\d{13}$/g,
						cardType:"DC"
					}
				]
			},
			{
				bankName:"福建海峡银行",
				bankCode:"FJHXBC",
				patterns:[
					{
						reg:/^(622388)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(621267|623063)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(620043|)\d{12}$/g,
						cardType:"PC"
					}
				]
			},
			{
				bankName:"吉林银行",
				bankCode:"JLBANK",
				patterns:[
					{
						reg:/^(622865|623131)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(940012)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622178|622179|628358)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{
				bankName:"汉口银行",
				bankCode:"HKB",
				patterns:[
					{
						reg:/^(990027)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622325|623105|623029)\d{10}$/g,
						cardType:"DC"
					}
				]
			},
			{
				bankName:"盛京银行",
				bankCode:"SJBANK",
				patterns:[
					{
						reg:/^(566666)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622455|940039)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(623108|623081)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622466|628285)\d{10}$/g,
						cardType:"CC"
					}
				]
			},	
			{																			
				bankName:"大连银行",
				bankCode:"DLB",
				patterns:[
					{
						reg:/^(603708)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622993|623069|623070|623172|623173)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622383|622385|628299)\d{10}$/g,
						cardType:"CC"
					}
				]
			},	
			{																							
				bankName:"河北银行",
				bankCode:"BHB",
				patterns:[
					{
						reg:/^(622498|622499|623000|940046)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622921|628321)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{																						
				bankName:"乌鲁木齐市商业银行",
				bankCode:"URMQCCB",
				patterns:[
					{
						reg:/^(621751|622143|940001|621754)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622476|628278)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{																							
				bankName:"绍兴银行",
				bankCode:"SXCB",
				patterns:[
					{
						reg:/^(622486)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(603602|623026|623086)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628291)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{																							
				bankName:"成都商业银行",
				bankCode:"CDCB",
				patterns:[
					{
						reg:/^(622152|622154|622996|622997|940027|622153|622135|621482|621532)\d{13}$/g,
						cardType:"DC"
					}
				]
			},
			{																						
				bankName:"抚顺银行",
				bankCode:"FSCB",
				patterns:[
					{
						reg:/^(622442)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(940053)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622442|623099)\d{13}$/g,
						cardType:"DC"
					}
				]
			},
			{																						
				bankName:"郑州银行",
				bankCode:"ZZBANK",
				patterns:[
					{
						reg:/^(622421)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(940056)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(96828)\d{11}$/g,
						cardType:"DC"
					}
				]
			},
			{																						
				bankName:"宁夏银行",
				bankCode:"NXBANK",
				patterns:[
					{
						reg:/^(621529|622429|621417|623089|623200)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628214|625529|622428)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{																						
				bankName:"重庆银行",
				bankCode:"CQBANK",
				patterns:[
					{
						reg:/^(9896)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622134|940018|623016)\d{10}$/g,
						cardType:"DC"
					}
				]
			},
			{																						
				bankName:"哈尔滨银行",
				bankCode:"HRBANK",
				patterns:[
					{
						reg:/^(621577|622425)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(940049)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622425)\d{11}$/g,
						cardType:"DC"
					}
				]
			},
			{																							
				bankName:"兰州银行",
				bankCode:"LZYH",
				patterns:[
					{
						reg:/^(622139|940040|628263)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(621242|621538|621496)\d{13}$/g,
						cardType:"DC"
					}
				]
			},
			{																						
				bankName:"青岛银行",
				bankCode:"QDCCB",
				patterns:[
					{
						reg:/^(621252|622146|940061|628239)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(621419|623170)\d{13}$/g,
						cardType:"DC"
					}
				]
			},
			{																						
				bankName:"秦皇岛市商业银行",
				bankCode:"QHDCCB",
				patterns:[
					{
						reg:/^(62249802|94004602)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(621237|623003)\d{13}$/g,
						cardType:"DC"
					}
				]
			},
			{																						
				bankName:"青海银行",
				bankCode:"BOQH",
				patterns:[
					{
						reg:/^(622310|940068)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622817|628287|625959)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(62536601)\d{8}$/g,
						cardType:"CC"
					}
				]
			},
			{																						
				bankName:"台州银行",
				bankCode:"TZCB",
				patterns:[
					{
						reg:/^(622427)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(940069)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(623039)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622321|628273)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(625001)\d{10}$/g,
						cardType:"SCC"
					}
				]
			},
			{																						
				bankName:"长沙银行",
				bankCode:"CSCB",
				patterns:[
					{
						reg:/^(694301)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(940071|622368|621446)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(625901|622898|622900|628281|628282|622806|628283)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(620519)\d{13}$/g,
						cardType:"PC"
					}
				]
			},
			{																						
				bankName:"泉州银行",
				bankCode:"BOQZ",
				patterns:[
					{
						reg:/^(683970|940074)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622370)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(621437)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628319)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{																						
				bankName:"包商银行",
				bankCode:"BSB",
				patterns:[
					{
						reg:/^(622336|621760)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622165)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622315|625950|628295)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{																						
				bankName:"龙江银行",
				bankCode:"DAQINGB",
				patterns:[
					{
						reg:/^(621037|621097|621588|622977)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(62321601)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622860)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622644|628333)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{																						
				bankName:"上海农商银行",
				bankCode:"SHRCB",
				patterns:[
					{
						reg:/^(622478|940013|621495)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(625500)\d{10}$/g,
						cardType:"SCC"
					},
					{
						reg:/^(622611|622722|628211|625989)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{		
				bankName:"浙江泰隆商业银行",
				bankCode:"ZJQL",
				patterns:[
					{
						reg:/^(622717)\d{10}$/g,
						cardType:"SCC"
					},
					{
						reg:/^(628275|622565|622287)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{																							
				bankName:"内蒙古银行",
				bankCode:"H3CB",
				patterns:[
					{
						reg:/^(622147|621633)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628252)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{		
				bankName:"广西北部湾银行",
				bankCode:"BGB",
				patterns:[
					{
						reg:/^(623001)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628227)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{		
				bankName:"桂林银行",
				bankCode:"GLBANK",
				patterns:[
					{
						reg:/^(621456)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(621562)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628219)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{																						
				bankName:"龙江银行",
				bankCode:"DAQINGB",
				patterns:[
					{
						reg:/^(621037|621097|621588|622977)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(62321601)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622475|622860)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(625588)\d{10}$/g,
						cardType:"SCC"
					},
					{
						reg:/^(622270|628368|625090|622644|628333)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{			
				bankName:"成都农村商业银行",
				bankCode:"CDRCB",
				patterns:[
					{
						reg:/^(623088)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622829|628301|622808|628308)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{			
				bankName:"福建省农村信用社联合社",
				bankCode:"FJNX",
				patterns:[
					{
						reg:/^(622127|622184|621701|621251|621589|623036)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628232|622802|622290)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{			
				bankName:"天津农村商业银行",
				bankCode:"TRCB",
				patterns:[
					{
						reg:/^(622531|622329)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622829|628301)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{			
				bankName:"江苏省农村信用社联合社",
				bankCode:"JSRCU",
				patterns:[
					{
						reg:/^(621578|623066|622452|622324)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622815|622816|628226)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{			
				bankName:"湖南农村信用社联合社",
				bankCode:"SLH",
				patterns:[
					{
						reg:/^(622906|628386|625519|625506)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{			
				bankName:"江西省农村信用社联合社",
				bankCode:"JXNCX",
				patterns:[
					{
						reg:/^(621592)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628392)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{				
				bankName:"商丘市商业银行",
				bankCode:"SCBBANK",
				patterns:[
					{
						reg:/^(621748)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628271)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{				
				bankName:"华融湘江银行",
				bankCode:"HRXJB",
				patterns:[
					{
						reg:/^(621366|621388)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628328)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{			
				bankName:"衡水市商业银行",
				bankCode:"HSBK",
				patterns:[
					{
						reg:/^(621239|623068)\d{13}$/g,
						cardType:"DC"
					}
				]
			},
			{			
				bankName:"重庆南川石银村镇银行",
				bankCode:"CQNCSYCZ",
				patterns:[
					{
						reg:/^(621653004)\d{10}$/g,
						cardType:"DC"
					}
				]
			},
			{			
				bankName:"湖南省农村信用社联合社",
				bankCode:"HNRCC",
				patterns:[
					{
						reg:/^(622169|621519|621539|623090)\d{13}$/g,
						cardType:"DC"
					}
				]
			},
			{				
				bankName:"邢台银行",
				bankCode:"XTB",
				patterns:[
					{
						reg:/^(621238|620528)\d{13}$/g,
						cardType:"DC"
					}
				]
			},
			{				
				bankName:"临汾市尧都区农村信用合作联社",
				bankCode:"LPRDNCXYS",
				patterns:[
					{
						reg:/^(628382|625158)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{				
				bankName:"东营银行",
				bankCode:"DYCCB",
				patterns:[
					{
						reg:/^(621004)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628217)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{				
				bankName:"上饶银行",
				bankCode:"SRBANK",
				patterns:[
					{
						reg:/^(621416)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628217)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{				
				bankName:"德州银行",
				bankCode:"DZBANK",
				patterns:[
					{
						reg:/^(622937)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628397)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{				
				bankName:"承德银行",
				bankCode:"CDB",
				patterns:[
					{
						reg:/^(628229)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{				
				bankName:"云南省农村信用社",
				bankCode:"YNRCC",
				patterns:[
					{
						reg:/^(622469|628307)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{				
				bankName:"柳州银行",
				bankCode:"LZCCB",
				patterns:[
					{
						reg:/^(622292|622291|621412)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622880|622881)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(62829)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{				
				bankName:"威海市商业银行",
				bankCode:"WHSYBANK",
				patterns:[
					{
						reg:/^(623102)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628234)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{				
				bankName:"湖州银行",
				bankCode:"HZBANK",
				patterns:[
					{
						reg:/^(628306)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{				
				bankName:"潍坊银行",
				bankCode:"BANKWF",
				patterns:[
					{
						reg:/^(622391|940072)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628391)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{				
				bankName:"赣州银行",
				bankCode:"GZB",
				patterns:[
					{
						reg:/^(622967|940073)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628233)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{					
				bankName:"日照银行",
				bankCode:"RZGWYBANK",
				patterns:[
					{
						reg:/^(628257)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{				
				bankName:"南昌银行",
				bankCode:"NCB",
				patterns:[
					{
						reg:/^(621269|622275)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(940006)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628305)\d{11}$/g,
						cardType:"CC"
					}
				]
			},
			{					
				bankName:"贵阳银行",
				bankCode:"GYCB",
				patterns:[
					{
						reg:/^(622133|621735)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(888)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628213)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{				
				bankName:"锦州银行",
				bankCode:"BOJZ",
				patterns:[
					{
						reg:/^(622990|940003)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628261)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{					
				bankName:"齐商银行",
				bankCode:"QSBANK",
				patterns:[
					{
						reg:/^(622311|940057)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628311)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{				
				bankName:"珠海华润银行",
				bankCode:"RBOZ",
				patterns:[
					{
						reg:/^(622363|940048)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628270)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{				
				bankName:"葫芦岛市商业银行",
				bankCode:"HLDCCB",
				patterns:[
					{
						reg:/^(622398|940054)\d{10}$/g,
						cardType:"DC"
					}
				]
			},
			{				
				bankName:"宜昌市商业银行",
				bankCode:"HBC",
				patterns:[
					{
						reg:/^(940055)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622397)\d{11}$/g,
						cardType:"CC"
					}
				]
			},
			{					
				bankName:"杭州商业银行",
				bankCode:"HZCB",
				patterns:[
					{
						reg:/^(603367|622878)\d{12}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622397)\d{11}$/g,
						cardType:"CC"
					}
				]
			},
			{					
				bankName:"苏州市商业银行",
				bankCode:"JSBANK",
				patterns:[
					{
						reg:/^(603506)\d{13}$/g,
						cardType:"DC"
					}
				]
			},
			{				
				bankName:"辽阳银行",
				bankCode:"LYCB",
				patterns:[
					{
						reg:/^(622399|940043)\d{11}$/g,
						cardType:"DC"
					}
				]
			},
			{					
				bankName:"洛阳银行",
				bankCode:"LYB",
				patterns:[
					{
						reg:/^(622420|940041)\d{11}$/g,
						cardType:"DC"
					}
				]
			},
			{					
				bankName:"焦作市商业银行",
				bankCode:"JZCBANK",
				patterns:[
					{
						reg:/^(622338)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(940032)\d{10}$/g,
						cardType:"DC"
					}
				]
			},
			{						
				bankName:"镇江市商业银行",
				bankCode:"ZJCCB",
				patterns:[
					{
						reg:/^(622394|940025)\d{10}$/g,
						cardType:"DC"
					}
				]
			},
			{						
				bankName:"法国兴业银行",
				bankCode:"FGXYBANK",
				patterns:[
					{
						reg:/^(621245)\d{10}$/g,
						cardType:"DC"
					}
				]
			},
			{					
				bankName:"大华银行",
				bankCode:"DYBANK",
				patterns:[
					{
						reg:/^(621328)\d{13}$/g,
						cardType:"DC"
					}
				]
			},
			{					
				bankName:"企业银行",
				bankCode:"DIYEBANK",
				patterns:[
					{
						reg:/^(621651)\d{13}$/g,
						cardType:"DC"
					}
				]
			},
			{					
				bankName:"华侨银行",
				bankCode:"HQBANK",
				patterns:[
					{
						reg:/^(621077)\d{10}$/g,
						cardType:"DC"
					}
				]
			},
			{					
				bankName:"恒生银行",
				bankCode:"HSB",
				patterns:[
					{
						reg:/^(622409|621441)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622410|621440)\d{11}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622950|622951)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(625026|625024|622376|622378|622377|625092)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{						
				bankName:"临沂商业银行",
				bankCode:"LSB",
				patterns:[
					{
						reg:/^(622359|940066)\d{13}$/g,
						cardType:"DC"
					}
				]
			},
			{					
				bankName:"烟台商业银行",
				bankCode:"YTCB",
				patterns:[
					{
						reg:/^(622886)\d{10}$/g,
						cardType:"DC"
					}
				]
			},
			{					
				bankName:"齐鲁银行",
				bankCode:"QLB",
				patterns:[
					{
						reg:/^(940008|622379)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(628379)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{					
				bankName:"BC卡公司",
				bankCode:"BCCC",
				patterns:[
					{
						reg:/^(620011|620027|620031|620039|620103|620106|620120|620123|620125|620220|620278|620812|621006|621011|621012|621020|621023|621025|621027|621031|620132|621039|621078|621220|621003)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(625003|625011|625012|625020|625023|625025|625027|625031|621032|625039|625078|625079|625103|625106|625006|625112|625120|625123|625125|625127|625131|625032|625139|625178|625179|625220|625320|625111|625132|625244)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{					
				bankName:"集友银行",
				bankCode:"CYB",
				patterns:[
					{
						reg:/^(622355|623042)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(621043|621742)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622352|622353|625048|625053|625060)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(620206|620207)\d{10}$/g,
						cardType:"PC"
					}
				]
			},	
			{					
				bankName:"大丰银行",
				bankCode:"TFB",
				patterns:[
					{
						reg:/^(622547|622548|622546)\d{13}$/g,
						cardType:"DC"
					},
					{
						reg:/^(625198|625196|625147)\d{10}$/g,
						cardType:"CC"
					},
					{
						reg:/^(620072)\d{13}$/g,
						cardType:"PC"
					},
					{
						reg:/^(620204|620205)\d{10}$/g,
						cardType:"PC"
					}
				]
			},
			{					
				bankName:"AEON信贷财务亚洲有限公司",
				bankCode:"AEON",
				patterns:[
					{
						reg:/^(621064|622941|622974)\d{10}$/g,
						cardType:"DC"
					},
					{
						reg:/^(622493)\d{10}$/g,
						cardType:"CC"
					}
				]
			},
			{															
				bankName:"澳门BDA",
				bankCode:"MABDA",
				patterns:[
					{
						reg:/^(621274|621324)\d{13}$/g,
						cardType:"DC"
					}
				]
			}																						
		]
		function getBankNameByBankCode(bankcode){
			for(var i = 0 , len = bankcardList.length ; i < len ; i++){
				var bankcard = bankcardList[i];
				if(bankcode == bankcard.bankCode){
					return bankcard.bankName;
				}
			}
			return "";
		}
		function _getBankInfoByCardNo(cardNo,cbf){
			for(var i = 0 , len = bankcardList.length ; i < len ; i++){
				var bankcard = bankcardList[i];
				var patterns = bankcard.patterns;
				for(var j = 0 , jLen = patterns.length ; j < jLen ; j++){
					var pattern = patterns[j];
					if((new RegExp(pattern.reg)).test(cardNo)){
						var info = extend(bankcard,pattern);
						delete info.patterns;
						delete info.reg;
						info['cardTypeName'] = getCardTypeName(info['cardType']);
						return cbf(null,info);
					}
				} 
			}
			return cbf(null);
		}
		function _getBankInfoByCardNoAsync(cardNo,cbf){
			var errMsg = "";
			_getBankInfoByCardNo(cardNo,function(err,info){
				if(!err && info){
					return cbf(null,info);
				}else{
					if (typeof module !== 'undefined' && module.exports) {
						var https = __webpack_require__(/*! https */ 67); 
						https.get("https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo="+cardNo+"&cardBinCheck=true",function(res){
							if(res.statusCode == 200){
								var chunk = "";
								res.on('data', function(d) {
									chunk += d;
								});
								res.on('end',function(){
									try{
										var bankInfo = JSON.parse(chunk);
										if(bankInfo.validated){
											var info = {};
											info['bankName'] = getBankNameByBankCode(bankInfo.bank);
											info['cardType'] = bankInfo.cardType;
											info['bankCode'] = bankInfo.bank;
											info['cardTypeName'] = getCardTypeName(bankInfo.cardType);
											info['backName'] = info['bankName'];//向下兼容，修改字段错别字
											cbf(null,info);
										}else{
											errMsg = cardNo+":该银行卡不存在,"+chunk;
											cbf(errMsg);
										}						
									}catch(e){
										errMsg = cardNo+':获取alipay接口信息出错了,返回json格式不正确';
										cbf(errMsg);
									}						
								})
							}else{
								errMsg = cardNo+':获取alipay接口信息出错了,statusCode,'+res.statusCode;						
								cbf(errMsg);
							}			
						}).on('error', function(e) {
							errMsg = cardNo+':获取alipay接口信息出错了';
							cbf(errMsg);
						});
					}else{
						cbf(cardNo+":该银行卡不存在");
					}
				}
			});
		}
		function getBankBin(cardNo,cbf){
			var	errMsg = '';
			if(!isFunction(cbf)){
				cbf = function(){};
			}
			if(isNaN(cardNo)){
				cardNo = parseInt(cardNo);
				if(isNaN(cardNo)){
					checkFlag = false;
					errMsg = cardNo+':银行卡号必须是数字';
					return cbf(errMsg)
				}
			}
			if(cardNo.toString().length < 15 || cardNo.toString().length > 19){
				checkFlag = false;
				errMsg = cardNo+':银行卡位数必须是15到19位';
				return cbf(errMsg)
			}
			_getBankInfoByCardNoAsync(cardNo,function(err,bin){
				cbf(err,bin);
			});
		}
		if (true){
			if(typeof module !== 'undefined' && module.exports){
				exports = module.exports = {getBankBin:getBankBin};
			}
			exports.getBankBin = getBankBin; 
		}else if(typeof define === 'function' && define.amd){
			define('bankInfo', [], function(){
				return {getBankBin:getBankBin};
			});
		}else if(typeof define === 'function' && define.cmd){
			define(function(){
				return {getBankBin:getBankBin};
			})
		}else{
			root.getBankBin = getBankBin;
		}
	}.call(this));

/***/ },
/* 67 */
/*!*********************************************!*\
  !*** (webpack)/~/https-browserify/index.js ***!
  \*********************************************/
/***/ function(module, exports, __webpack_require__) {

	var http = __webpack_require__(/*! http */ 68);
	
	var https = module.exports;
	
	for (var key in http) {
	    if (http.hasOwnProperty(key)) https[key] = http[key];
	};
	
	https.request = function (params, cb) {
	    if (!params) params = {};
	    params.scheme = 'https';
	    return http.request.call(this, params, cb);
	}


/***/ },
/* 68 */
/*!********************************************!*\
  !*** (webpack)/~/http-browserify/index.js ***!
  \********************************************/
/***/ function(module, exports, __webpack_require__) {

	var http = module.exports;
	var EventEmitter = __webpack_require__(/*! events */ 69).EventEmitter;
	var Request = __webpack_require__(/*! ./lib/request */ 70);
	var url = __webpack_require__(/*! url */ 94)
	
	http.request = function (params, cb) {
	    if (typeof params === 'string') {
	        params = url.parse(params)
	    }
	    if (!params) params = {};
	    if (!params.host && !params.port) {
	        params.port = parseInt(window.location.port, 10);
	    }
	    if (!params.host && params.hostname) {
	        params.host = params.hostname;
	    }
	
	    if (!params.protocol) {
	        if (params.scheme) {
	            params.protocol = params.scheme + ':';
	        } else {
	            params.protocol = window.location.protocol;
	        }
	    }
	
	    if (!params.host) {
	        params.host = window.location.hostname || window.location.host;
	    }
	    if (/:/.test(params.host)) {
	        if (!params.port) {
	            params.port = params.host.split(':')[1];
	        }
	        params.host = params.host.split(':')[0];
	    }
	    if (!params.port) params.port = params.protocol == 'https:' ? 443 : 80;
	    
	    var req = new Request(new xhrHttp, params);
	    if (cb) req.on('response', cb);
	    return req;
	};
	
	http.get = function (params, cb) {
	    params.method = 'GET';
	    var req = http.request(params, cb);
	    req.end();
	    return req;
	};
	
	http.Agent = function () {};
	http.Agent.defaultMaxSockets = 4;
	
	var xhrHttp = (function () {
	    if (typeof window === 'undefined') {
	        throw new Error('no window object present');
	    }
	    else if (window.XMLHttpRequest) {
	        return window.XMLHttpRequest;
	    }
	    else if (window.ActiveXObject) {
	        var axs = [
	            'Msxml2.XMLHTTP.6.0',
	            'Msxml2.XMLHTTP.3.0',
	            'Microsoft.XMLHTTP'
	        ];
	        for (var i = 0; i < axs.length; i++) {
	            try {
	                var ax = new(window.ActiveXObject)(axs[i]);
	                return function () {
	                    if (ax) {
	                        var ax_ = ax;
	                        ax = null;
	                        return ax_;
	                    }
	                    else {
	                        return new(window.ActiveXObject)(axs[i]);
	                    }
	                };
	            }
	            catch (e) {}
	        }
	        throw new Error('ajax not supported in this browser')
	    }
	    else {
	        throw new Error('ajax not supported in this browser');
	    }
	})();
	
	http.STATUS_CODES = {
	    100 : 'Continue',
	    101 : 'Switching Protocols',
	    102 : 'Processing',                 // RFC 2518, obsoleted by RFC 4918
	    200 : 'OK',
	    201 : 'Created',
	    202 : 'Accepted',
	    203 : 'Non-Authoritative Information',
	    204 : 'No Content',
	    205 : 'Reset Content',
	    206 : 'Partial Content',
	    207 : 'Multi-Status',               // RFC 4918
	    300 : 'Multiple Choices',
	    301 : 'Moved Permanently',
	    302 : 'Moved Temporarily',
	    303 : 'See Other',
	    304 : 'Not Modified',
	    305 : 'Use Proxy',
	    307 : 'Temporary Redirect',
	    400 : 'Bad Request',
	    401 : 'Unauthorized',
	    402 : 'Payment Required',
	    403 : 'Forbidden',
	    404 : 'Not Found',
	    405 : 'Method Not Allowed',
	    406 : 'Not Acceptable',
	    407 : 'Proxy Authentication Required',
	    408 : 'Request Time-out',
	    409 : 'Conflict',
	    410 : 'Gone',
	    411 : 'Length Required',
	    412 : 'Precondition Failed',
	    413 : 'Request Entity Too Large',
	    414 : 'Request-URI Too Large',
	    415 : 'Unsupported Media Type',
	    416 : 'Requested Range Not Satisfiable',
	    417 : 'Expectation Failed',
	    418 : 'I\'m a teapot',              // RFC 2324
	    422 : 'Unprocessable Entity',       // RFC 4918
	    423 : 'Locked',                     // RFC 4918
	    424 : 'Failed Dependency',          // RFC 4918
	    425 : 'Unordered Collection',       // RFC 4918
	    426 : 'Upgrade Required',           // RFC 2817
	    428 : 'Precondition Required',      // RFC 6585
	    429 : 'Too Many Requests',          // RFC 6585
	    431 : 'Request Header Fields Too Large',// RFC 6585
	    500 : 'Internal Server Error',
	    501 : 'Not Implemented',
	    502 : 'Bad Gateway',
	    503 : 'Service Unavailable',
	    504 : 'Gateway Time-out',
	    505 : 'HTTP Version Not Supported',
	    506 : 'Variant Also Negotiates',    // RFC 2295
	    507 : 'Insufficient Storage',       // RFC 4918
	    509 : 'Bandwidth Limit Exceeded',
	    510 : 'Not Extended',               // RFC 2774
	    511 : 'Network Authentication Required' // RFC 6585
	};

/***/ },
/* 69 */
/*!************************************!*\
  !*** (webpack)/~/events/events.js ***!
  \************************************/
/***/ function(module, exports) {

	// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.
	
	function EventEmitter() {
	  this._events = this._events || {};
	  this._maxListeners = this._maxListeners || undefined;
	}
	module.exports = EventEmitter;
	
	// Backwards-compat with node 0.10.x
	EventEmitter.EventEmitter = EventEmitter;
	
	EventEmitter.prototype._events = undefined;
	EventEmitter.prototype._maxListeners = undefined;
	
	// By default EventEmitters will print a warning if more than 10 listeners are
	// added to it. This is a useful default which helps finding memory leaks.
	EventEmitter.defaultMaxListeners = 10;
	
	// Obviously not all Emitters should be limited to 10. This function allows
	// that to be increased. Set to zero for unlimited.
	EventEmitter.prototype.setMaxListeners = function(n) {
	  if (!isNumber(n) || n < 0 || isNaN(n))
	    throw TypeError('n must be a positive number');
	  this._maxListeners = n;
	  return this;
	};
	
	EventEmitter.prototype.emit = function(type) {
	  var er, handler, len, args, i, listeners;
	
	  if (!this._events)
	    this._events = {};
	
	  // If there is no 'error' event listener then throw.
	  if (type === 'error') {
	    if (!this._events.error ||
	        (isObject(this._events.error) && !this._events.error.length)) {
	      er = arguments[1];
	      if (er instanceof Error) {
	        throw er; // Unhandled 'error' event
	      }
	      throw TypeError('Uncaught, unspecified "error" event.');
	    }
	  }
	
	  handler = this._events[type];
	
	  if (isUndefined(handler))
	    return false;
	
	  if (isFunction(handler)) {
	    switch (arguments.length) {
	      // fast cases
	      case 1:
	        handler.call(this);
	        break;
	      case 2:
	        handler.call(this, arguments[1]);
	        break;
	      case 3:
	        handler.call(this, arguments[1], arguments[2]);
	        break;
	      // slower
	      default:
	        args = Array.prototype.slice.call(arguments, 1);
	        handler.apply(this, args);
	    }
	  } else if (isObject(handler)) {
	    args = Array.prototype.slice.call(arguments, 1);
	    listeners = handler.slice();
	    len = listeners.length;
	    for (i = 0; i < len; i++)
	      listeners[i].apply(this, args);
	  }
	
	  return true;
	};
	
	EventEmitter.prototype.addListener = function(type, listener) {
	  var m;
	
	  if (!isFunction(listener))
	    throw TypeError('listener must be a function');
	
	  if (!this._events)
	    this._events = {};
	
	  // To avoid recursion in the case that type === "newListener"! Before
	  // adding it to the listeners, first emit "newListener".
	  if (this._events.newListener)
	    this.emit('newListener', type,
	              isFunction(listener.listener) ?
	              listener.listener : listener);
	
	  if (!this._events[type])
	    // Optimize the case of one listener. Don't need the extra array object.
	    this._events[type] = listener;
	  else if (isObject(this._events[type]))
	    // If we've already got an array, just append.
	    this._events[type].push(listener);
	  else
	    // Adding the second element, need to change to array.
	    this._events[type] = [this._events[type], listener];
	
	  // Check for listener leak
	  if (isObject(this._events[type]) && !this._events[type].warned) {
	    if (!isUndefined(this._maxListeners)) {
	      m = this._maxListeners;
	    } else {
	      m = EventEmitter.defaultMaxListeners;
	    }
	
	    if (m && m > 0 && this._events[type].length > m) {
	      this._events[type].warned = true;
	      console.error('(node) warning: possible EventEmitter memory ' +
	                    'leak detected. %d listeners added. ' +
	                    'Use emitter.setMaxListeners() to increase limit.',
	                    this._events[type].length);
	      if (typeof console.trace === 'function') {
	        // not supported in IE 10
	        console.trace();
	      }
	    }
	  }
	
	  return this;
	};
	
	EventEmitter.prototype.on = EventEmitter.prototype.addListener;
	
	EventEmitter.prototype.once = function(type, listener) {
	  if (!isFunction(listener))
	    throw TypeError('listener must be a function');
	
	  var fired = false;
	
	  function g() {
	    this.removeListener(type, g);
	
	    if (!fired) {
	      fired = true;
	      listener.apply(this, arguments);
	    }
	  }
	
	  g.listener = listener;
	  this.on(type, g);
	
	  return this;
	};
	
	// emits a 'removeListener' event iff the listener was removed
	EventEmitter.prototype.removeListener = function(type, listener) {
	  var list, position, length, i;
	
	  if (!isFunction(listener))
	    throw TypeError('listener must be a function');
	
	  if (!this._events || !this._events[type])
	    return this;
	
	  list = this._events[type];
	  length = list.length;
	  position = -1;
	
	  if (list === listener ||
	      (isFunction(list.listener) && list.listener === listener)) {
	    delete this._events[type];
	    if (this._events.removeListener)
	      this.emit('removeListener', type, listener);
	
	  } else if (isObject(list)) {
	    for (i = length; i-- > 0;) {
	      if (list[i] === listener ||
	          (list[i].listener && list[i].listener === listener)) {
	        position = i;
	        break;
	      }
	    }
	
	    if (position < 0)
	      return this;
	
	    if (list.length === 1) {
	      list.length = 0;
	      delete this._events[type];
	    } else {
	      list.splice(position, 1);
	    }
	
	    if (this._events.removeListener)
	      this.emit('removeListener', type, listener);
	  }
	
	  return this;
	};
	
	EventEmitter.prototype.removeAllListeners = function(type) {
	  var key, listeners;
	
	  if (!this._events)
	    return this;
	
	  // not listening for removeListener, no need to emit
	  if (!this._events.removeListener) {
	    if (arguments.length === 0)
	      this._events = {};
	    else if (this._events[type])
	      delete this._events[type];
	    return this;
	  }
	
	  // emit removeListener for all listeners on all events
	  if (arguments.length === 0) {
	    for (key in this._events) {
	      if (key === 'removeListener') continue;
	      this.removeAllListeners(key);
	    }
	    this.removeAllListeners('removeListener');
	    this._events = {};
	    return this;
	  }
	
	  listeners = this._events[type];
	
	  if (isFunction(listeners)) {
	    this.removeListener(type, listeners);
	  } else if (listeners) {
	    // LIFO order
	    while (listeners.length)
	      this.removeListener(type, listeners[listeners.length - 1]);
	  }
	  delete this._events[type];
	
	  return this;
	};
	
	EventEmitter.prototype.listeners = function(type) {
	  var ret;
	  if (!this._events || !this._events[type])
	    ret = [];
	  else if (isFunction(this._events[type]))
	    ret = [this._events[type]];
	  else
	    ret = this._events[type].slice();
	  return ret;
	};
	
	EventEmitter.prototype.listenerCount = function(type) {
	  if (this._events) {
	    var evlistener = this._events[type];
	
	    if (isFunction(evlistener))
	      return 1;
	    else if (evlistener)
	      return evlistener.length;
	  }
	  return 0;
	};
	
	EventEmitter.listenerCount = function(emitter, type) {
	  return emitter.listenerCount(type);
	};
	
	function isFunction(arg) {
	  return typeof arg === 'function';
	}
	
	function isNumber(arg) {
	  return typeof arg === 'number';
	}
	
	function isObject(arg) {
	  return typeof arg === 'object' && arg !== null;
	}
	
	function isUndefined(arg) {
	  return arg === void 0;
	}


/***/ },
/* 70 */
/*!**************************************************!*\
  !*** (webpack)/~/http-browserify/lib/request.js ***!
  \**************************************************/
/***/ function(module, exports, __webpack_require__) {

	var Stream = __webpack_require__(/*! stream */ 71);
	var Response = __webpack_require__(/*! ./response */ 90);
	var Base64 = __webpack_require__(/*! Base64 */ 93);
	var inherits = __webpack_require__(/*! inherits */ 72);
	
	var Request = module.exports = function (xhr, params) {
	    var self = this;
	    self.writable = true;
	    self.xhr = xhr;
	    self.body = [];
	    
	    self.uri = (params.protocol || 'http:') + '//'
	        + params.host
	        + (params.port ? ':' + params.port : '')
	        + (params.path || '/')
	    ;
	    
	    if (typeof params.withCredentials === 'undefined') {
	        params.withCredentials = true;
	    }
	
	    try { xhr.withCredentials = params.withCredentials }
	    catch (e) {}
	    
	    if (params.responseType) try { xhr.responseType = params.responseType }
	    catch (e) {}
	    
	    xhr.open(
	        params.method || 'GET',
	        self.uri,
	        true
	    );
	
	    xhr.onerror = function(event) {
	        self.emit('error', new Error('Network error'));
	    };
	
	    self._headers = {};
	    
	    if (params.headers) {
	        var keys = objectKeys(params.headers);
	        for (var i = 0; i < keys.length; i++) {
	            var key = keys[i];
	            if (!self.isSafeRequestHeader(key)) continue;
	            var value = params.headers[key];
	            self.setHeader(key, value);
	        }
	    }
	    
	    if (params.auth) {
	        //basic auth
	        this.setHeader('Authorization', 'Basic ' + Base64.btoa(params.auth));
	    }
	
	    var res = new Response;
	    res.on('close', function () {
	        self.emit('close');
	    });
	    
	    res.on('ready', function () {
	        self.emit('response', res);
	    });
	
	    res.on('error', function (err) {
	        self.emit('error', err);
	    });
	    
	    xhr.onreadystatechange = function () {
	        // Fix for IE9 bug
	        // SCRIPT575: Could not complete the operation due to error c00c023f
	        // It happens when a request is aborted, calling the success callback anyway with readyState === 4
	        if (xhr.__aborted) return;
	        res.handle(xhr);
	    };
	};
	
	inherits(Request, Stream);
	
	Request.prototype.setHeader = function (key, value) {
	    this._headers[key.toLowerCase()] = value
	};
	
	Request.prototype.getHeader = function (key) {
	    return this._headers[key.toLowerCase()]
	};
	
	Request.prototype.removeHeader = function (key) {
	    delete this._headers[key.toLowerCase()]
	};
	
	Request.prototype.write = function (s) {
	    this.body.push(s);
	};
	
	Request.prototype.destroy = function (s) {
	    this.xhr.__aborted = true;
	    this.xhr.abort();
	    this.emit('close');
	};
	
	Request.prototype.end = function (s) {
	    if (s !== undefined) this.body.push(s);
	
	    var keys = objectKeys(this._headers);
	    for (var i = 0; i < keys.length; i++) {
	        var key = keys[i];
	        var value = this._headers[key];
	        if (isArray(value)) {
	            for (var j = 0; j < value.length; j++) {
	                this.xhr.setRequestHeader(key, value[j]);
	            }
	        }
	        else this.xhr.setRequestHeader(key, value)
	    }
	
	    if (this.body.length === 0) {
	        this.xhr.send('');
	    }
	    else if (typeof this.body[0] === 'string') {
	        this.xhr.send(this.body.join(''));
	    }
	    else if (isArray(this.body[0])) {
	        var body = [];
	        for (var i = 0; i < this.body.length; i++) {
	            body.push.apply(body, this.body[i]);
	        }
	        this.xhr.send(body);
	    }
	    else if (/Array/.test(Object.prototype.toString.call(this.body[0]))) {
	        var len = 0;
	        for (var i = 0; i < this.body.length; i++) {
	            len += this.body[i].length;
	        }
	        var body = new(this.body[0].constructor)(len);
	        var k = 0;
	        
	        for (var i = 0; i < this.body.length; i++) {
	            var b = this.body[i];
	            for (var j = 0; j < b.length; j++) {
	                body[k++] = b[j];
	            }
	        }
	        this.xhr.send(body);
	    }
	    else if (isXHR2Compatible(this.body[0])) {
	        this.xhr.send(this.body[0]);
	    }
	    else {
	        var body = '';
	        for (var i = 0; i < this.body.length; i++) {
	            body += this.body[i].toString();
	        }
	        this.xhr.send(body);
	    }
	};
	
	// Taken from http://dxr.mozilla.org/mozilla/mozilla-central/content/base/src/nsXMLHttpRequest.cpp.html
	Request.unsafeHeaders = [
	    "accept-charset",
	    "accept-encoding",
	    "access-control-request-headers",
	    "access-control-request-method",
	    "connection",
	    "content-length",
	    "cookie",
	    "cookie2",
	    "content-transfer-encoding",
	    "date",
	    "expect",
	    "host",
	    "keep-alive",
	    "origin",
	    "referer",
	    "te",
	    "trailer",
	    "transfer-encoding",
	    "upgrade",
	    "user-agent",
	    "via"
	];
	
	Request.prototype.isSafeRequestHeader = function (headerName) {
	    if (!headerName) return false;
	    return indexOf(Request.unsafeHeaders, headerName.toLowerCase()) === -1;
	};
	
	var objectKeys = Object.keys || function (obj) {
	    var keys = [];
	    for (var key in obj) keys.push(key);
	    return keys;
	};
	
	var isArray = Array.isArray || function (xs) {
	    return Object.prototype.toString.call(xs) === '[object Array]';
	};
	
	var indexOf = function (xs, x) {
	    if (xs.indexOf) return xs.indexOf(x);
	    for (var i = 0; i < xs.length; i++) {
	        if (xs[i] === x) return i;
	    }
	    return -1;
	};
	
	var isXHR2Compatible = function (obj) {
	    if (typeof Blob !== 'undefined' && obj instanceof Blob) return true;
	    if (typeof ArrayBuffer !== 'undefined' && obj instanceof ArrayBuffer) return true;
	    if (typeof FormData !== 'undefined' && obj instanceof FormData) return true;
	};


/***/ },
/* 71 */
/*!**********************************************!*\
  !*** (webpack)/~/stream-browserify/index.js ***!
  \**********************************************/
/***/ function(module, exports, __webpack_require__) {

	// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.
	
	module.exports = Stream;
	
	var EE = __webpack_require__(/*! events */ 69).EventEmitter;
	var inherits = __webpack_require__(/*! inherits */ 72);
	
	inherits(Stream, EE);
	Stream.Readable = __webpack_require__(/*! readable-stream/readable.js */ 73);
	Stream.Writable = __webpack_require__(/*! readable-stream/writable.js */ 86);
	Stream.Duplex = __webpack_require__(/*! readable-stream/duplex.js */ 87);
	Stream.Transform = __webpack_require__(/*! readable-stream/transform.js */ 88);
	Stream.PassThrough = __webpack_require__(/*! readable-stream/passthrough.js */ 89);
	
	// Backwards-compat with node 0.4.x
	Stream.Stream = Stream;
	
	
	
	// old-style streams.  Note that the pipe method (the only relevant
	// part of this class) is overridden in the Readable class.
	
	function Stream() {
	  EE.call(this);
	}
	
	Stream.prototype.pipe = function(dest, options) {
	  var source = this;
	
	  function ondata(chunk) {
	    if (dest.writable) {
	      if (false === dest.write(chunk) && source.pause) {
	        source.pause();
	      }
	    }
	  }
	
	  source.on('data', ondata);
	
	  function ondrain() {
	    if (source.readable && source.resume) {
	      source.resume();
	    }
	  }
	
	  dest.on('drain', ondrain);
	
	  // If the 'end' option is not supplied, dest.end() will be called when
	  // source gets the 'end' or 'close' events.  Only dest.end() once.
	  if (!dest._isStdio && (!options || options.end !== false)) {
	    source.on('end', onend);
	    source.on('close', onclose);
	  }
	
	  var didOnEnd = false;
	  function onend() {
	    if (didOnEnd) return;
	    didOnEnd = true;
	
	    dest.end();
	  }
	
	
	  function onclose() {
	    if (didOnEnd) return;
	    didOnEnd = true;
	
	    if (typeof dest.destroy === 'function') dest.destroy();
	  }
	
	  // don't leave dangling pipes when there are errors.
	  function onerror(er) {
	    cleanup();
	    if (EE.listenerCount(this, 'error') === 0) {
	      throw er; // Unhandled stream error in pipe.
	    }
	  }
	
	  source.on('error', onerror);
	  dest.on('error', onerror);
	
	  // remove all the event listeners that were added.
	  function cleanup() {
	    source.removeListener('data', ondata);
	    dest.removeListener('drain', ondrain);
	
	    source.removeListener('end', onend);
	    source.removeListener('close', onclose);
	
	    source.removeListener('error', onerror);
	    dest.removeListener('error', onerror);
	
	    source.removeListener('end', cleanup);
	    source.removeListener('close', cleanup);
	
	    dest.removeListener('close', cleanup);
	  }
	
	  source.on('end', cleanup);
	  source.on('close', cleanup);
	
	  dest.on('close', cleanup);
	
	  dest.emit('pipe', source);
	
	  // Allow for unix-like usage: A.pipe(B).pipe(C)
	  return dest;
	};


/***/ },
/* 72 */
/*!************************************************!*\
  !*** (webpack)/~/inherits/inherits_browser.js ***!
  \************************************************/
/***/ function(module, exports) {

	if (typeof Object.create === 'function') {
	  // implementation from standard node.js 'util' module
	  module.exports = function inherits(ctor, superCtor) {
	    ctor.super_ = superCtor
	    ctor.prototype = Object.create(superCtor.prototype, {
	      constructor: {
	        value: ctor,
	        enumerable: false,
	        writable: true,
	        configurable: true
	      }
	    });
	  };
	} else {
	  // old school shim for old browsers
	  module.exports = function inherits(ctor, superCtor) {
	    ctor.super_ = superCtor
	    var TempCtor = function () {}
	    TempCtor.prototype = superCtor.prototype
	    ctor.prototype = new TempCtor()
	    ctor.prototype.constructor = ctor
	  }
	}


/***/ },
/* 73 */
/*!*******************************************************************!*\
  !*** (webpack)/~/stream-browserify/~/readable-stream/readable.js ***!
  \*******************************************************************/
/***/ function(module, exports, __webpack_require__) {

	exports = module.exports = __webpack_require__(/*! ./lib/_stream_readable.js */ 74);
	exports.Stream = __webpack_require__(/*! stream */ 71);
	exports.Readable = exports;
	exports.Writable = __webpack_require__(/*! ./lib/_stream_writable.js */ 82);
	exports.Duplex = __webpack_require__(/*! ./lib/_stream_duplex.js */ 81);
	exports.Transform = __webpack_require__(/*! ./lib/_stream_transform.js */ 84);
	exports.PassThrough = __webpack_require__(/*! ./lib/_stream_passthrough.js */ 85);


/***/ },
/* 74 */
/*!*******************************************************************************!*\
  !*** (webpack)/~/stream-browserify/~/readable-stream/lib/_stream_readable.js ***!
  \*******************************************************************************/
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(process) {// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.
	
	module.exports = Readable;
	
	/*<replacement>*/
	var isArray = __webpack_require__(/*! isarray */ 75);
	/*</replacement>*/
	
	
	/*<replacement>*/
	var Buffer = __webpack_require__(/*! buffer */ 76).Buffer;
	/*</replacement>*/
	
	Readable.ReadableState = ReadableState;
	
	var EE = __webpack_require__(/*! events */ 69).EventEmitter;
	
	/*<replacement>*/
	if (!EE.listenerCount) EE.listenerCount = function(emitter, type) {
	  return emitter.listeners(type).length;
	};
	/*</replacement>*/
	
	var Stream = __webpack_require__(/*! stream */ 71);
	
	/*<replacement>*/
	var util = __webpack_require__(/*! core-util-is */ 79);
	util.inherits = __webpack_require__(/*! inherits */ 72);
	/*</replacement>*/
	
	var StringDecoder;
	
	
	/*<replacement>*/
	var debug = __webpack_require__(/*! util */ 80);
	if (debug && debug.debuglog) {
	  debug = debug.debuglog('stream');
	} else {
	  debug = function () {};
	}
	/*</replacement>*/
	
	
	util.inherits(Readable, Stream);
	
	function ReadableState(options, stream) {
	  var Duplex = __webpack_require__(/*! ./_stream_duplex */ 81);
	
	  options = options || {};
	
	  // the point at which it stops calling _read() to fill the buffer
	  // Note: 0 is a valid value, means "don't call _read preemptively ever"
	  var hwm = options.highWaterMark;
	  var defaultHwm = options.objectMode ? 16 : 16 * 1024;
	  this.highWaterMark = (hwm || hwm === 0) ? hwm : defaultHwm;
	
	  // cast to ints.
	  this.highWaterMark = ~~this.highWaterMark;
	
	  this.buffer = [];
	  this.length = 0;
	  this.pipes = null;
	  this.pipesCount = 0;
	  this.flowing = null;
	  this.ended = false;
	  this.endEmitted = false;
	  this.reading = false;
	
	  // a flag to be able to tell if the onwrite cb is called immediately,
	  // or on a later tick.  We set this to true at first, because any
	  // actions that shouldn't happen until "later" should generally also
	  // not happen before the first write call.
	  this.sync = true;
	
	  // whenever we return null, then we set a flag to say
	  // that we're awaiting a 'readable' event emission.
	  this.needReadable = false;
	  this.emittedReadable = false;
	  this.readableListening = false;
	
	
	  // object stream flag. Used to make read(n) ignore n and to
	  // make all the buffer merging and length checks go away
	  this.objectMode = !!options.objectMode;
	
	  if (stream instanceof Duplex)
	    this.objectMode = this.objectMode || !!options.readableObjectMode;
	
	  // Crypto is kind of old and crusty.  Historically, its default string
	  // encoding is 'binary' so we have to make this configurable.
	  // Everything else in the universe uses 'utf8', though.
	  this.defaultEncoding = options.defaultEncoding || 'utf8';
	
	  // when piping, we only care about 'readable' events that happen
	  // after read()ing all the bytes and not getting any pushback.
	  this.ranOut = false;
	
	  // the number of writers that are awaiting a drain event in .pipe()s
	  this.awaitDrain = 0;
	
	  // if true, a maybeReadMore has been scheduled
	  this.readingMore = false;
	
	  this.decoder = null;
	  this.encoding = null;
	  if (options.encoding) {
	    if (!StringDecoder)
	      StringDecoder = __webpack_require__(/*! string_decoder/ */ 83).StringDecoder;
	    this.decoder = new StringDecoder(options.encoding);
	    this.encoding = options.encoding;
	  }
	}
	
	function Readable(options) {
	  var Duplex = __webpack_require__(/*! ./_stream_duplex */ 81);
	
	  if (!(this instanceof Readable))
	    return new Readable(options);
	
	  this._readableState = new ReadableState(options, this);
	
	  // legacy
	  this.readable = true;
	
	  Stream.call(this);
	}
	
	// Manually shove something into the read() buffer.
	// This returns true if the highWaterMark has not been hit yet,
	// similar to how Writable.write() returns true if you should
	// write() some more.
	Readable.prototype.push = function(chunk, encoding) {
	  var state = this._readableState;
	
	  if (util.isString(chunk) && !state.objectMode) {
	    encoding = encoding || state.defaultEncoding;
	    if (encoding !== state.encoding) {
	      chunk = new Buffer(chunk, encoding);
	      encoding = '';
	    }
	  }
	
	  return readableAddChunk(this, state, chunk, encoding, false);
	};
	
	// Unshift should *always* be something directly out of read()
	Readable.prototype.unshift = function(chunk) {
	  var state = this._readableState;
	  return readableAddChunk(this, state, chunk, '', true);
	};
	
	function readableAddChunk(stream, state, chunk, encoding, addToFront) {
	  var er = chunkInvalid(state, chunk);
	  if (er) {
	    stream.emit('error', er);
	  } else if (util.isNullOrUndefined(chunk)) {
	    state.reading = false;
	    if (!state.ended)
	      onEofChunk(stream, state);
	  } else if (state.objectMode || chunk && chunk.length > 0) {
	    if (state.ended && !addToFront) {
	      var e = new Error('stream.push() after EOF');
	      stream.emit('error', e);
	    } else if (state.endEmitted && addToFront) {
	      var e = new Error('stream.unshift() after end event');
	      stream.emit('error', e);
	    } else {
	      if (state.decoder && !addToFront && !encoding)
	        chunk = state.decoder.write(chunk);
	
	      if (!addToFront)
	        state.reading = false;
	
	      // if we want the data now, just emit it.
	      if (state.flowing && state.length === 0 && !state.sync) {
	        stream.emit('data', chunk);
	        stream.read(0);
	      } else {
	        // update the buffer info.
	        state.length += state.objectMode ? 1 : chunk.length;
	        if (addToFront)
	          state.buffer.unshift(chunk);
	        else
	          state.buffer.push(chunk);
	
	        if (state.needReadable)
	          emitReadable(stream);
	      }
	
	      maybeReadMore(stream, state);
	    }
	  } else if (!addToFront) {
	    state.reading = false;
	  }
	
	  return needMoreData(state);
	}
	
	
	
	// if it's past the high water mark, we can push in some more.
	// Also, if we have no data yet, we can stand some
	// more bytes.  This is to work around cases where hwm=0,
	// such as the repl.  Also, if the push() triggered a
	// readable event, and the user called read(largeNumber) such that
	// needReadable was set, then we ought to push more, so that another
	// 'readable' event will be triggered.
	function needMoreData(state) {
	  return !state.ended &&
	         (state.needReadable ||
	          state.length < state.highWaterMark ||
	          state.length === 0);
	}
	
	// backwards compatibility.
	Readable.prototype.setEncoding = function(enc) {
	  if (!StringDecoder)
	    StringDecoder = __webpack_require__(/*! string_decoder/ */ 83).StringDecoder;
	  this._readableState.decoder = new StringDecoder(enc);
	  this._readableState.encoding = enc;
	  return this;
	};
	
	// Don't raise the hwm > 128MB
	var MAX_HWM = 0x800000;
	function roundUpToNextPowerOf2(n) {
	  if (n >= MAX_HWM) {
	    n = MAX_HWM;
	  } else {
	    // Get the next highest power of 2
	    n--;
	    for (var p = 1; p < 32; p <<= 1) n |= n >> p;
	    n++;
	  }
	  return n;
	}
	
	function howMuchToRead(n, state) {
	  if (state.length === 0 && state.ended)
	    return 0;
	
	  if (state.objectMode)
	    return n === 0 ? 0 : 1;
	
	  if (isNaN(n) || util.isNull(n)) {
	    // only flow one buffer at a time
	    if (state.flowing && state.buffer.length)
	      return state.buffer[0].length;
	    else
	      return state.length;
	  }
	
	  if (n <= 0)
	    return 0;
	
	  // If we're asking for more than the target buffer level,
	  // then raise the water mark.  Bump up to the next highest
	  // power of 2, to prevent increasing it excessively in tiny
	  // amounts.
	  if (n > state.highWaterMark)
	    state.highWaterMark = roundUpToNextPowerOf2(n);
	
	  // don't have that much.  return null, unless we've ended.
	  if (n > state.length) {
	    if (!state.ended) {
	      state.needReadable = true;
	      return 0;
	    } else
	      return state.length;
	  }
	
	  return n;
	}
	
	// you can override either this method, or the async _read(n) below.
	Readable.prototype.read = function(n) {
	  debug('read', n);
	  var state = this._readableState;
	  var nOrig = n;
	
	  if (!util.isNumber(n) || n > 0)
	    state.emittedReadable = false;
	
	  // if we're doing read(0) to trigger a readable event, but we
	  // already have a bunch of data in the buffer, then just trigger
	  // the 'readable' event and move on.
	  if (n === 0 &&
	      state.needReadable &&
	      (state.length >= state.highWaterMark || state.ended)) {
	    debug('read: emitReadable', state.length, state.ended);
	    if (state.length === 0 && state.ended)
	      endReadable(this);
	    else
	      emitReadable(this);
	    return null;
	  }
	
	  n = howMuchToRead(n, state);
	
	  // if we've ended, and we're now clear, then finish it up.
	  if (n === 0 && state.ended) {
	    if (state.length === 0)
	      endReadable(this);
	    return null;
	  }
	
	  // All the actual chunk generation logic needs to be
	  // *below* the call to _read.  The reason is that in certain
	  // synthetic stream cases, such as passthrough streams, _read
	  // may be a completely synchronous operation which may change
	  // the state of the read buffer, providing enough data when
	  // before there was *not* enough.
	  //
	  // So, the steps are:
	  // 1. Figure out what the state of things will be after we do
	  // a read from the buffer.
	  //
	  // 2. If that resulting state will trigger a _read, then call _read.
	  // Note that this may be asynchronous, or synchronous.  Yes, it is
	  // deeply ugly to write APIs this way, but that still doesn't mean
	  // that the Readable class should behave improperly, as streams are
	  // designed to be sync/async agnostic.
	  // Take note if the _read call is sync or async (ie, if the read call
	  // has returned yet), so that we know whether or not it's safe to emit
	  // 'readable' etc.
	  //
	  // 3. Actually pull the requested chunks out of the buffer and return.
	
	  // if we need a readable event, then we need to do some reading.
	  var doRead = state.needReadable;
	  debug('need readable', doRead);
	
	  // if we currently have less than the highWaterMark, then also read some
	  if (state.length === 0 || state.length - n < state.highWaterMark) {
	    doRead = true;
	    debug('length less than watermark', doRead);
	  }
	
	  // however, if we've ended, then there's no point, and if we're already
	  // reading, then it's unnecessary.
	  if (state.ended || state.reading) {
	    doRead = false;
	    debug('reading or ended', doRead);
	  }
	
	  if (doRead) {
	    debug('do read');
	    state.reading = true;
	    state.sync = true;
	    // if the length is currently zero, then we *need* a readable event.
	    if (state.length === 0)
	      state.needReadable = true;
	    // call internal read method
	    this._read(state.highWaterMark);
	    state.sync = false;
	  }
	
	  // If _read pushed data synchronously, then `reading` will be false,
	  // and we need to re-evaluate how much data we can return to the user.
	  if (doRead && !state.reading)
	    n = howMuchToRead(nOrig, state);
	
	  var ret;
	  if (n > 0)
	    ret = fromList(n, state);
	  else
	    ret = null;
	
	  if (util.isNull(ret)) {
	    state.needReadable = true;
	    n = 0;
	  }
	
	  state.length -= n;
	
	  // If we have nothing in the buffer, then we want to know
	  // as soon as we *do* get something into the buffer.
	  if (state.length === 0 && !state.ended)
	    state.needReadable = true;
	
	  // If we tried to read() past the EOF, then emit end on the next tick.
	  if (nOrig !== n && state.ended && state.length === 0)
	    endReadable(this);
	
	  if (!util.isNull(ret))
	    this.emit('data', ret);
	
	  return ret;
	};
	
	function chunkInvalid(state, chunk) {
	  var er = null;
	  if (!util.isBuffer(chunk) &&
	      !util.isString(chunk) &&
	      !util.isNullOrUndefined(chunk) &&
	      !state.objectMode) {
	    er = new TypeError('Invalid non-string/buffer chunk');
	  }
	  return er;
	}
	
	
	function onEofChunk(stream, state) {
	  if (state.decoder && !state.ended) {
	    var chunk = state.decoder.end();
	    if (chunk && chunk.length) {
	      state.buffer.push(chunk);
	      state.length += state.objectMode ? 1 : chunk.length;
	    }
	  }
	  state.ended = true;
	
	  // emit 'readable' now to make sure it gets picked up.
	  emitReadable(stream);
	}
	
	// Don't emit readable right away in sync mode, because this can trigger
	// another read() call => stack overflow.  This way, it might trigger
	// a nextTick recursion warning, but that's not so bad.
	function emitReadable(stream) {
	  var state = stream._readableState;
	  state.needReadable = false;
	  if (!state.emittedReadable) {
	    debug('emitReadable', state.flowing);
	    state.emittedReadable = true;
	    if (state.sync)
	      process.nextTick(function() {
	        emitReadable_(stream);
	      });
	    else
	      emitReadable_(stream);
	  }
	}
	
	function emitReadable_(stream) {
	  debug('emit readable');
	  stream.emit('readable');
	  flow(stream);
	}
	
	
	// at this point, the user has presumably seen the 'readable' event,
	// and called read() to consume some data.  that may have triggered
	// in turn another _read(n) call, in which case reading = true if
	// it's in progress.
	// However, if we're not ended, or reading, and the length < hwm,
	// then go ahead and try to read some more preemptively.
	function maybeReadMore(stream, state) {
	  if (!state.readingMore) {
	    state.readingMore = true;
	    process.nextTick(function() {
	      maybeReadMore_(stream, state);
	    });
	  }
	}
	
	function maybeReadMore_(stream, state) {
	  var len = state.length;
	  while (!state.reading && !state.flowing && !state.ended &&
	         state.length < state.highWaterMark) {
	    debug('maybeReadMore read 0');
	    stream.read(0);
	    if (len === state.length)
	      // didn't get any data, stop spinning.
	      break;
	    else
	      len = state.length;
	  }
	  state.readingMore = false;
	}
	
	// abstract method.  to be overridden in specific implementation classes.
	// call cb(er, data) where data is <= n in length.
	// for virtual (non-string, non-buffer) streams, "length" is somewhat
	// arbitrary, and perhaps not very meaningful.
	Readable.prototype._read = function(n) {
	  this.emit('error', new Error('not implemented'));
	};
	
	Readable.prototype.pipe = function(dest, pipeOpts) {
	  var src = this;
	  var state = this._readableState;
	
	  switch (state.pipesCount) {
	    case 0:
	      state.pipes = dest;
	      break;
	    case 1:
	      state.pipes = [state.pipes, dest];
	      break;
	    default:
	      state.pipes.push(dest);
	      break;
	  }
	  state.pipesCount += 1;
	  debug('pipe count=%d opts=%j', state.pipesCount, pipeOpts);
	
	  var doEnd = (!pipeOpts || pipeOpts.end !== false) &&
	              dest !== process.stdout &&
	              dest !== process.stderr;
	
	  var endFn = doEnd ? onend : cleanup;
	  if (state.endEmitted)
	    process.nextTick(endFn);
	  else
	    src.once('end', endFn);
	
	  dest.on('unpipe', onunpipe);
	  function onunpipe(readable) {
	    debug('onunpipe');
	    if (readable === src) {
	      cleanup();
	    }
	  }
	
	  function onend() {
	    debug('onend');
	    dest.end();
	  }
	
	  // when the dest drains, it reduces the awaitDrain counter
	  // on the source.  This would be more elegant with a .once()
	  // handler in flow(), but adding and removing repeatedly is
	  // too slow.
	  var ondrain = pipeOnDrain(src);
	  dest.on('drain', ondrain);
	
	  function cleanup() {
	    debug('cleanup');
	    // cleanup event handlers once the pipe is broken
	    dest.removeListener('close', onclose);
	    dest.removeListener('finish', onfinish);
	    dest.removeListener('drain', ondrain);
	    dest.removeListener('error', onerror);
	    dest.removeListener('unpipe', onunpipe);
	    src.removeListener('end', onend);
	    src.removeListener('end', cleanup);
	    src.removeListener('data', ondata);
	
	    // if the reader is waiting for a drain event from this
	    // specific writer, then it would cause it to never start
	    // flowing again.
	    // So, if this is awaiting a drain, then we just call it now.
	    // If we don't know, then assume that we are waiting for one.
	    if (state.awaitDrain &&
	        (!dest._writableState || dest._writableState.needDrain))
	      ondrain();
	  }
	
	  src.on('data', ondata);
	  function ondata(chunk) {
	    debug('ondata');
	    var ret = dest.write(chunk);
	    if (false === ret) {
	      debug('false write response, pause',
	            src._readableState.awaitDrain);
	      src._readableState.awaitDrain++;
	      src.pause();
	    }
	  }
	
	  // if the dest has an error, then stop piping into it.
	  // however, don't suppress the throwing behavior for this.
	  function onerror(er) {
	    debug('onerror', er);
	    unpipe();
	    dest.removeListener('error', onerror);
	    if (EE.listenerCount(dest, 'error') === 0)
	      dest.emit('error', er);
	  }
	  // This is a brutally ugly hack to make sure that our error handler
	  // is attached before any userland ones.  NEVER DO THIS.
	  if (!dest._events || !dest._events.error)
	    dest.on('error', onerror);
	  else if (isArray(dest._events.error))
	    dest._events.error.unshift(onerror);
	  else
	    dest._events.error = [onerror, dest._events.error];
	
	
	
	  // Both close and finish should trigger unpipe, but only once.
	  function onclose() {
	    dest.removeListener('finish', onfinish);
	    unpipe();
	  }
	  dest.once('close', onclose);
	  function onfinish() {
	    debug('onfinish');
	    dest.removeListener('close', onclose);
	    unpipe();
	  }
	  dest.once('finish', onfinish);
	
	  function unpipe() {
	    debug('unpipe');
	    src.unpipe(dest);
	  }
	
	  // tell the dest that it's being piped to
	  dest.emit('pipe', src);
	
	  // start the flow if it hasn't been started already.
	  if (!state.flowing) {
	    debug('pipe resume');
	    src.resume();
	  }
	
	  return dest;
	};
	
	function pipeOnDrain(src) {
	  return function() {
	    var state = src._readableState;
	    debug('pipeOnDrain', state.awaitDrain);
	    if (state.awaitDrain)
	      state.awaitDrain--;
	    if (state.awaitDrain === 0 && EE.listenerCount(src, 'data')) {
	      state.flowing = true;
	      flow(src);
	    }
	  };
	}
	
	
	Readable.prototype.unpipe = function(dest) {
	  var state = this._readableState;
	
	  // if we're not piping anywhere, then do nothing.
	  if (state.pipesCount === 0)
	    return this;
	
	  // just one destination.  most common case.
	  if (state.pipesCount === 1) {
	    // passed in one, but it's not the right one.
	    if (dest && dest !== state.pipes)
	      return this;
	
	    if (!dest)
	      dest = state.pipes;
	
	    // got a match.
	    state.pipes = null;
	    state.pipesCount = 0;
	    state.flowing = false;
	    if (dest)
	      dest.emit('unpipe', this);
	    return this;
	  }
	
	  // slow case. multiple pipe destinations.
	
	  if (!dest) {
	    // remove all.
	    var dests = state.pipes;
	    var len = state.pipesCount;
	    state.pipes = null;
	    state.pipesCount = 0;
	    state.flowing = false;
	
	    for (var i = 0; i < len; i++)
	      dests[i].emit('unpipe', this);
	    return this;
	  }
	
	  // try to find the right one.
	  var i = indexOf(state.pipes, dest);
	  if (i === -1)
	    return this;
	
	  state.pipes.splice(i, 1);
	  state.pipesCount -= 1;
	  if (state.pipesCount === 1)
	    state.pipes = state.pipes[0];
	
	  dest.emit('unpipe', this);
	
	  return this;
	};
	
	// set up data events if they are asked for
	// Ensure readable listeners eventually get something
	Readable.prototype.on = function(ev, fn) {
	  var res = Stream.prototype.on.call(this, ev, fn);
	
	  // If listening to data, and it has not explicitly been paused,
	  // then call resume to start the flow of data on the next tick.
	  if (ev === 'data' && false !== this._readableState.flowing) {
	    this.resume();
	  }
	
	  if (ev === 'readable' && this.readable) {
	    var state = this._readableState;
	    if (!state.readableListening) {
	      state.readableListening = true;
	      state.emittedReadable = false;
	      state.needReadable = true;
	      if (!state.reading) {
	        var self = this;
	        process.nextTick(function() {
	          debug('readable nexttick read 0');
	          self.read(0);
	        });
	      } else if (state.length) {
	        emitReadable(this, state);
	      }
	    }
	  }
	
	  return res;
	};
	Readable.prototype.addListener = Readable.prototype.on;
	
	// pause() and resume() are remnants of the legacy readable stream API
	// If the user uses them, then switch into old mode.
	Readable.prototype.resume = function() {
	  var state = this._readableState;
	  if (!state.flowing) {
	    debug('resume');
	    state.flowing = true;
	    if (!state.reading) {
	      debug('resume read 0');
	      this.read(0);
	    }
	    resume(this, state);
	  }
	  return this;
	};
	
	function resume(stream, state) {
	  if (!state.resumeScheduled) {
	    state.resumeScheduled = true;
	    process.nextTick(function() {
	      resume_(stream, state);
	    });
	  }
	}
	
	function resume_(stream, state) {
	  state.resumeScheduled = false;
	  stream.emit('resume');
	  flow(stream);
	  if (state.flowing && !state.reading)
	    stream.read(0);
	}
	
	Readable.prototype.pause = function() {
	  debug('call pause flowing=%j', this._readableState.flowing);
	  if (false !== this._readableState.flowing) {
	    debug('pause');
	    this._readableState.flowing = false;
	    this.emit('pause');
	  }
	  return this;
	};
	
	function flow(stream) {
	  var state = stream._readableState;
	  debug('flow', state.flowing);
	  if (state.flowing) {
	    do {
	      var chunk = stream.read();
	    } while (null !== chunk && state.flowing);
	  }
	}
	
	// wrap an old-style stream as the async data source.
	// This is *not* part of the readable stream interface.
	// It is an ugly unfortunate mess of history.
	Readable.prototype.wrap = function(stream) {
	  var state = this._readableState;
	  var paused = false;
	
	  var self = this;
	  stream.on('end', function() {
	    debug('wrapped end');
	    if (state.decoder && !state.ended) {
	      var chunk = state.decoder.end();
	      if (chunk && chunk.length)
	        self.push(chunk);
	    }
	
	    self.push(null);
	  });
	
	  stream.on('data', function(chunk) {
	    debug('wrapped data');
	    if (state.decoder)
	      chunk = state.decoder.write(chunk);
	    if (!chunk || !state.objectMode && !chunk.length)
	      return;
	
	    var ret = self.push(chunk);
	    if (!ret) {
	      paused = true;
	      stream.pause();
	    }
	  });
	
	  // proxy all the other methods.
	  // important when wrapping filters and duplexes.
	  for (var i in stream) {
	    if (util.isFunction(stream[i]) && util.isUndefined(this[i])) {
	      this[i] = function(method) { return function() {
	        return stream[method].apply(stream, arguments);
	      }}(i);
	    }
	  }
	
	  // proxy certain important events.
	  var events = ['error', 'close', 'destroy', 'pause', 'resume'];
	  forEach(events, function(ev) {
	    stream.on(ev, self.emit.bind(self, ev));
	  });
	
	  // when we try to consume some more bytes, simply unpause the
	  // underlying stream.
	  self._read = function(n) {
	    debug('wrapped _read', n);
	    if (paused) {
	      paused = false;
	      stream.resume();
	    }
	  };
	
	  return self;
	};
	
	
	
	// exposed for testing purposes only.
	Readable._fromList = fromList;
	
	// Pluck off n bytes from an array of buffers.
	// Length is the combined lengths of all the buffers in the list.
	function fromList(n, state) {
	  var list = state.buffer;
	  var length = state.length;
	  var stringMode = !!state.decoder;
	  var objectMode = !!state.objectMode;
	  var ret;
	
	  // nothing in the list, definitely empty.
	  if (list.length === 0)
	    return null;
	
	  if (length === 0)
	    ret = null;
	  else if (objectMode)
	    ret = list.shift();
	  else if (!n || n >= length) {
	    // read it all, truncate the array.
	    if (stringMode)
	      ret = list.join('');
	    else
	      ret = Buffer.concat(list, length);
	    list.length = 0;
	  } else {
	    // read just some of it.
	    if (n < list[0].length) {
	      // just take a part of the first list item.
	      // slice is the same for buffers and strings.
	      var buf = list[0];
	      ret = buf.slice(0, n);
	      list[0] = buf.slice(n);
	    } else if (n === list[0].length) {
	      // first list is a perfect match
	      ret = list.shift();
	    } else {
	      // complex case.
	      // we have enough to cover it, but it spans past the first buffer.
	      if (stringMode)
	        ret = '';
	      else
	        ret = new Buffer(n);
	
	      var c = 0;
	      for (var i = 0, l = list.length; i < l && c < n; i++) {
	        var buf = list[0];
	        var cpy = Math.min(n - c, buf.length);
	
	        if (stringMode)
	          ret += buf.slice(0, cpy);
	        else
	          buf.copy(ret, c, 0, cpy);
	
	        if (cpy < buf.length)
	          list[0] = buf.slice(cpy);
	        else
	          list.shift();
	
	        c += cpy;
	      }
	    }
	  }
	
	  return ret;
	}
	
	function endReadable(stream) {
	  var state = stream._readableState;
	
	  // If we get here before consuming all the bytes, then that is a
	  // bug in node.  Should never happen.
	  if (state.length > 0)
	    throw new Error('endReadable called on non-empty stream');
	
	  if (!state.endEmitted) {
	    state.ended = true;
	    process.nextTick(function() {
	      // Check that we didn't get one last unshift.
	      if (!state.endEmitted && state.length === 0) {
	        state.endEmitted = true;
	        stream.readable = false;
	        stream.emit('end');
	      }
	    });
	  }
	}
	
	function forEach (xs, f) {
	  for (var i = 0, l = xs.length; i < l; i++) {
	    f(xs[i], i);
	  }
	}
	
	function indexOf (xs, x) {
	  for (var i = 0, l = xs.length; i < l; i++) {
	    if (xs[i] === x) return i;
	  }
	  return -1;
	}
	
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! (webpack)/~/process/browser.js */ 30)))

/***/ },
/* 75 */
/*!************************************!*\
  !*** (webpack)/~/isarray/index.js ***!
  \************************************/
/***/ function(module, exports) {

	module.exports = Array.isArray || function (arr) {
	  return Object.prototype.toString.call(arr) == '[object Array]';
	};


/***/ },
/* 76 */
/*!***********************************!*\
  !*** (webpack)/~/buffer/index.js ***!
  \***********************************/
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(Buffer, global) {/*!
	 * The buffer module from node.js, for the browser.
	 *
	 * @author   Feross Aboukhadijeh <feross@feross.org> <http://feross.org>
	 * @license  MIT
	 */
	/* eslint-disable no-proto */
	
	var base64 = __webpack_require__(/*! base64-js */ 77)
	var ieee754 = __webpack_require__(/*! ieee754 */ 78)
	var isArray = __webpack_require__(/*! isarray */ 75)
	
	exports.Buffer = Buffer
	exports.SlowBuffer = SlowBuffer
	exports.INSPECT_MAX_BYTES = 50
	Buffer.poolSize = 8192 // not used by this implementation
	
	var rootParent = {}
	
	/**
	 * If `Buffer.TYPED_ARRAY_SUPPORT`:
	 *   === true    Use Uint8Array implementation (fastest)
	 *   === false   Use Object implementation (most compatible, even IE6)
	 *
	 * Browsers that support typed arrays are IE 10+, Firefox 4+, Chrome 7+, Safari 5.1+,
	 * Opera 11.6+, iOS 4.2+.
	 *
	 * Due to various browser bugs, sometimes the Object implementation will be used even
	 * when the browser supports typed arrays.
	 *
	 * Note:
	 *
	 *   - Firefox 4-29 lacks support for adding new properties to `Uint8Array` instances,
	 *     See: https://bugzilla.mozilla.org/show_bug.cgi?id=695438.
	 *
	 *   - Safari 5-7 lacks support for changing the `Object.prototype.constructor` property
	 *     on objects.
	 *
	 *   - Chrome 9-10 is missing the `TypedArray.prototype.subarray` function.
	 *
	 *   - IE10 has a broken `TypedArray.prototype.subarray` function which returns arrays of
	 *     incorrect length in some situations.
	
	 * We detect these buggy browsers and set `Buffer.TYPED_ARRAY_SUPPORT` to `false` so they
	 * get the Object implementation, which is slower but behaves correctly.
	 */
	Buffer.TYPED_ARRAY_SUPPORT = global.TYPED_ARRAY_SUPPORT !== undefined
	  ? global.TYPED_ARRAY_SUPPORT
	  : typedArraySupport()
	
	function typedArraySupport () {
	  function Bar () {}
	  try {
	    var arr = new Uint8Array(1)
	    arr.foo = function () { return 42 }
	    arr.constructor = Bar
	    return arr.foo() === 42 && // typed array instances can be augmented
	        arr.constructor === Bar && // constructor can be set
	        typeof arr.subarray === 'function' && // chrome 9-10 lack `subarray`
	        arr.subarray(1, 1).byteLength === 0 // ie10 has broken `subarray`
	  } catch (e) {
	    return false
	  }
	}
	
	function kMaxLength () {
	  return Buffer.TYPED_ARRAY_SUPPORT
	    ? 0x7fffffff
	    : 0x3fffffff
	}
	
	/**
	 * Class: Buffer
	 * =============
	 *
	 * The Buffer constructor returns instances of `Uint8Array` that are augmented
	 * with function properties for all the node `Buffer` API functions. We use
	 * `Uint8Array` so that square bracket notation works as expected -- it returns
	 * a single octet.
	 *
	 * By augmenting the instances, we can avoid modifying the `Uint8Array`
	 * prototype.
	 */
	function Buffer (arg) {
	  if (!(this instanceof Buffer)) {
	    // Avoid going through an ArgumentsAdaptorTrampoline in the common case.
	    if (arguments.length > 1) return new Buffer(arg, arguments[1])
	    return new Buffer(arg)
	  }
	
	  this.length = 0
	  this.parent = undefined
	
	  // Common case.
	  if (typeof arg === 'number') {
	    return fromNumber(this, arg)
	  }
	
	  // Slightly less common case.
	  if (typeof arg === 'string') {
	    return fromString(this, arg, arguments.length > 1 ? arguments[1] : 'utf8')
	  }
	
	  // Unusual.
	  return fromObject(this, arg)
	}
	
	function fromNumber (that, length) {
	  that = allocate(that, length < 0 ? 0 : checked(length) | 0)
	  if (!Buffer.TYPED_ARRAY_SUPPORT) {
	    for (var i = 0; i < length; i++) {
	      that[i] = 0
	    }
	  }
	  return that
	}
	
	function fromString (that, string, encoding) {
	  if (typeof encoding !== 'string' || encoding === '') encoding = 'utf8'
	
	  // Assumption: byteLength() return value is always < kMaxLength.
	  var length = byteLength(string, encoding) | 0
	  that = allocate(that, length)
	
	  that.write(string, encoding)
	  return that
	}
	
	function fromObject (that, object) {
	  if (Buffer.isBuffer(object)) return fromBuffer(that, object)
	
	  if (isArray(object)) return fromArray(that, object)
	
	  if (object == null) {
	    throw new TypeError('must start with number, buffer, array or string')
	  }
	
	  if (typeof ArrayBuffer !== 'undefined') {
	    if (object.buffer instanceof ArrayBuffer) {
	      return fromTypedArray(that, object)
	    }
	    if (object instanceof ArrayBuffer) {
	      return fromArrayBuffer(that, object)
	    }
	  }
	
	  if (object.length) return fromArrayLike(that, object)
	
	  return fromJsonObject(that, object)
	}
	
	function fromBuffer (that, buffer) {
	  var length = checked(buffer.length) | 0
	  that = allocate(that, length)
	  buffer.copy(that, 0, 0, length)
	  return that
	}
	
	function fromArray (that, array) {
	  var length = checked(array.length) | 0
	  that = allocate(that, length)
	  for (var i = 0; i < length; i += 1) {
	    that[i] = array[i] & 255
	  }
	  return that
	}
	
	// Duplicate of fromArray() to keep fromArray() monomorphic.
	function fromTypedArray (that, array) {
	  var length = checked(array.length) | 0
	  that = allocate(that, length)
	  // Truncating the elements is probably not what people expect from typed
	  // arrays with BYTES_PER_ELEMENT > 1 but it's compatible with the behavior
	  // of the old Buffer constructor.
	  for (var i = 0; i < length; i += 1) {
	    that[i] = array[i] & 255
	  }
	  return that
	}
	
	function fromArrayBuffer (that, array) {
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    // Return an augmented `Uint8Array` instance, for best performance
	    array.byteLength
	    that = Buffer._augment(new Uint8Array(array))
	  } else {
	    // Fallback: Return an object instance of the Buffer class
	    that = fromTypedArray(that, new Uint8Array(array))
	  }
	  return that
	}
	
	function fromArrayLike (that, array) {
	  var length = checked(array.length) | 0
	  that = allocate(that, length)
	  for (var i = 0; i < length; i += 1) {
	    that[i] = array[i] & 255
	  }
	  return that
	}
	
	// Deserialize { type: 'Buffer', data: [1,2,3,...] } into a Buffer object.
	// Returns a zero-length buffer for inputs that don't conform to the spec.
	function fromJsonObject (that, object) {
	  var array
	  var length = 0
	
	  if (object.type === 'Buffer' && isArray(object.data)) {
	    array = object.data
	    length = checked(array.length) | 0
	  }
	  that = allocate(that, length)
	
	  for (var i = 0; i < length; i += 1) {
	    that[i] = array[i] & 255
	  }
	  return that
	}
	
	if (Buffer.TYPED_ARRAY_SUPPORT) {
	  Buffer.prototype.__proto__ = Uint8Array.prototype
	  Buffer.__proto__ = Uint8Array
	}
	
	function allocate (that, length) {
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    // Return an augmented `Uint8Array` instance, for best performance
	    that = Buffer._augment(new Uint8Array(length))
	    that.__proto__ = Buffer.prototype
	  } else {
	    // Fallback: Return an object instance of the Buffer class
	    that.length = length
	    that._isBuffer = true
	  }
	
	  var fromPool = length !== 0 && length <= Buffer.poolSize >>> 1
	  if (fromPool) that.parent = rootParent
	
	  return that
	}
	
	function checked (length) {
	  // Note: cannot use `length < kMaxLength` here because that fails when
	  // length is NaN (which is otherwise coerced to zero.)
	  if (length >= kMaxLength()) {
	    throw new RangeError('Attempt to allocate Buffer larger than maximum ' +
	                         'size: 0x' + kMaxLength().toString(16) + ' bytes')
	  }
	  return length | 0
	}
	
	function SlowBuffer (subject, encoding) {
	  if (!(this instanceof SlowBuffer)) return new SlowBuffer(subject, encoding)
	
	  var buf = new Buffer(subject, encoding)
	  delete buf.parent
	  return buf
	}
	
	Buffer.isBuffer = function isBuffer (b) {
	  return !!(b != null && b._isBuffer)
	}
	
	Buffer.compare = function compare (a, b) {
	  if (!Buffer.isBuffer(a) || !Buffer.isBuffer(b)) {
	    throw new TypeError('Arguments must be Buffers')
	  }
	
	  if (a === b) return 0
	
	  var x = a.length
	  var y = b.length
	
	  var i = 0
	  var len = Math.min(x, y)
	  while (i < len) {
	    if (a[i] !== b[i]) break
	
	    ++i
	  }
	
	  if (i !== len) {
	    x = a[i]
	    y = b[i]
	  }
	
	  if (x < y) return -1
	  if (y < x) return 1
	  return 0
	}
	
	Buffer.isEncoding = function isEncoding (encoding) {
	  switch (String(encoding).toLowerCase()) {
	    case 'hex':
	    case 'utf8':
	    case 'utf-8':
	    case 'ascii':
	    case 'binary':
	    case 'base64':
	    case 'raw':
	    case 'ucs2':
	    case 'ucs-2':
	    case 'utf16le':
	    case 'utf-16le':
	      return true
	    default:
	      return false
	  }
	}
	
	Buffer.concat = function concat (list, length) {
	  if (!isArray(list)) throw new TypeError('list argument must be an Array of Buffers.')
	
	  if (list.length === 0) {
	    return new Buffer(0)
	  }
	
	  var i
	  if (length === undefined) {
	    length = 0
	    for (i = 0; i < list.length; i++) {
	      length += list[i].length
	    }
	  }
	
	  var buf = new Buffer(length)
	  var pos = 0
	  for (i = 0; i < list.length; i++) {
	    var item = list[i]
	    item.copy(buf, pos)
	    pos += item.length
	  }
	  return buf
	}
	
	function byteLength (string, encoding) {
	  if (typeof string !== 'string') string = '' + string
	
	  var len = string.length
	  if (len === 0) return 0
	
	  // Use a for loop to avoid recursion
	  var loweredCase = false
	  for (;;) {
	    switch (encoding) {
	      case 'ascii':
	      case 'binary':
	      // Deprecated
	      case 'raw':
	      case 'raws':
	        return len
	      case 'utf8':
	      case 'utf-8':
	        return utf8ToBytes(string).length
	      case 'ucs2':
	      case 'ucs-2':
	      case 'utf16le':
	      case 'utf-16le':
	        return len * 2
	      case 'hex':
	        return len >>> 1
	      case 'base64':
	        return base64ToBytes(string).length
	      default:
	        if (loweredCase) return utf8ToBytes(string).length // assume utf8
	        encoding = ('' + encoding).toLowerCase()
	        loweredCase = true
	    }
	  }
	}
	Buffer.byteLength = byteLength
	
	// pre-set for values that may exist in the future
	Buffer.prototype.length = undefined
	Buffer.prototype.parent = undefined
	
	function slowToString (encoding, start, end) {
	  var loweredCase = false
	
	  start = start | 0
	  end = end === undefined || end === Infinity ? this.length : end | 0
	
	  if (!encoding) encoding = 'utf8'
	  if (start < 0) start = 0
	  if (end > this.length) end = this.length
	  if (end <= start) return ''
	
	  while (true) {
	    switch (encoding) {
	      case 'hex':
	        return hexSlice(this, start, end)
	
	      case 'utf8':
	      case 'utf-8':
	        return utf8Slice(this, start, end)
	
	      case 'ascii':
	        return asciiSlice(this, start, end)
	
	      case 'binary':
	        return binarySlice(this, start, end)
	
	      case 'base64':
	        return base64Slice(this, start, end)
	
	      case 'ucs2':
	      case 'ucs-2':
	      case 'utf16le':
	      case 'utf-16le':
	        return utf16leSlice(this, start, end)
	
	      default:
	        if (loweredCase) throw new TypeError('Unknown encoding: ' + encoding)
	        encoding = (encoding + '').toLowerCase()
	        loweredCase = true
	    }
	  }
	}
	
	Buffer.prototype.toString = function toString () {
	  var length = this.length | 0
	  if (length === 0) return ''
	  if (arguments.length === 0) return utf8Slice(this, 0, length)
	  return slowToString.apply(this, arguments)
	}
	
	Buffer.prototype.equals = function equals (b) {
	  if (!Buffer.isBuffer(b)) throw new TypeError('Argument must be a Buffer')
	  if (this === b) return true
	  return Buffer.compare(this, b) === 0
	}
	
	Buffer.prototype.inspect = function inspect () {
	  var str = ''
	  var max = exports.INSPECT_MAX_BYTES
	  if (this.length > 0) {
	    str = this.toString('hex', 0, max).match(/.{2}/g).join(' ')
	    if (this.length > max) str += ' ... '
	  }
	  return '<Buffer ' + str + '>'
	}
	
	Buffer.prototype.compare = function compare (b) {
	  if (!Buffer.isBuffer(b)) throw new TypeError('Argument must be a Buffer')
	  if (this === b) return 0
	  return Buffer.compare(this, b)
	}
	
	Buffer.prototype.indexOf = function indexOf (val, byteOffset) {
	  if (byteOffset > 0x7fffffff) byteOffset = 0x7fffffff
	  else if (byteOffset < -0x80000000) byteOffset = -0x80000000
	  byteOffset >>= 0
	
	  if (this.length === 0) return -1
	  if (byteOffset >= this.length) return -1
	
	  // Negative offsets start from the end of the buffer
	  if (byteOffset < 0) byteOffset = Math.max(this.length + byteOffset, 0)
	
	  if (typeof val === 'string') {
	    if (val.length === 0) return -1 // special case: looking for empty string always fails
	    return String.prototype.indexOf.call(this, val, byteOffset)
	  }
	  if (Buffer.isBuffer(val)) {
	    return arrayIndexOf(this, val, byteOffset)
	  }
	  if (typeof val === 'number') {
	    if (Buffer.TYPED_ARRAY_SUPPORT && Uint8Array.prototype.indexOf === 'function') {
	      return Uint8Array.prototype.indexOf.call(this, val, byteOffset)
	    }
	    return arrayIndexOf(this, [ val ], byteOffset)
	  }
	
	  function arrayIndexOf (arr, val, byteOffset) {
	    var foundIndex = -1
	    for (var i = 0; byteOffset + i < arr.length; i++) {
	      if (arr[byteOffset + i] === val[foundIndex === -1 ? 0 : i - foundIndex]) {
	        if (foundIndex === -1) foundIndex = i
	        if (i - foundIndex + 1 === val.length) return byteOffset + foundIndex
	      } else {
	        foundIndex = -1
	      }
	    }
	    return -1
	  }
	
	  throw new TypeError('val must be string, number or Buffer')
	}
	
	// `get` is deprecated
	Buffer.prototype.get = function get (offset) {
	  console.log('.get() is deprecated. Access using array indexes instead.')
	  return this.readUInt8(offset)
	}
	
	// `set` is deprecated
	Buffer.prototype.set = function set (v, offset) {
	  console.log('.set() is deprecated. Access using array indexes instead.')
	  return this.writeUInt8(v, offset)
	}
	
	function hexWrite (buf, string, offset, length) {
	  offset = Number(offset) || 0
	  var remaining = buf.length - offset
	  if (!length) {
	    length = remaining
	  } else {
	    length = Number(length)
	    if (length > remaining) {
	      length = remaining
	    }
	  }
	
	  // must be an even number of digits
	  var strLen = string.length
	  if (strLen % 2 !== 0) throw new Error('Invalid hex string')
	
	  if (length > strLen / 2) {
	    length = strLen / 2
	  }
	  for (var i = 0; i < length; i++) {
	    var parsed = parseInt(string.substr(i * 2, 2), 16)
	    if (isNaN(parsed)) throw new Error('Invalid hex string')
	    buf[offset + i] = parsed
	  }
	  return i
	}
	
	function utf8Write (buf, string, offset, length) {
	  return blitBuffer(utf8ToBytes(string, buf.length - offset), buf, offset, length)
	}
	
	function asciiWrite (buf, string, offset, length) {
	  return blitBuffer(asciiToBytes(string), buf, offset, length)
	}
	
	function binaryWrite (buf, string, offset, length) {
	  return asciiWrite(buf, string, offset, length)
	}
	
	function base64Write (buf, string, offset, length) {
	  return blitBuffer(base64ToBytes(string), buf, offset, length)
	}
	
	function ucs2Write (buf, string, offset, length) {
	  return blitBuffer(utf16leToBytes(string, buf.length - offset), buf, offset, length)
	}
	
	Buffer.prototype.write = function write (string, offset, length, encoding) {
	  // Buffer#write(string)
	  if (offset === undefined) {
	    encoding = 'utf8'
	    length = this.length
	    offset = 0
	  // Buffer#write(string, encoding)
	  } else if (length === undefined && typeof offset === 'string') {
	    encoding = offset
	    length = this.length
	    offset = 0
	  // Buffer#write(string, offset[, length][, encoding])
	  } else if (isFinite(offset)) {
	    offset = offset | 0
	    if (isFinite(length)) {
	      length = length | 0
	      if (encoding === undefined) encoding = 'utf8'
	    } else {
	      encoding = length
	      length = undefined
	    }
	  // legacy write(string, encoding, offset, length) - remove in v0.13
	  } else {
	    var swap = encoding
	    encoding = offset
	    offset = length | 0
	    length = swap
	  }
	
	  var remaining = this.length - offset
	  if (length === undefined || length > remaining) length = remaining
	
	  if ((string.length > 0 && (length < 0 || offset < 0)) || offset > this.length) {
	    throw new RangeError('attempt to write outside buffer bounds')
	  }
	
	  if (!encoding) encoding = 'utf8'
	
	  var loweredCase = false
	  for (;;) {
	    switch (encoding) {
	      case 'hex':
	        return hexWrite(this, string, offset, length)
	
	      case 'utf8':
	      case 'utf-8':
	        return utf8Write(this, string, offset, length)
	
	      case 'ascii':
	        return asciiWrite(this, string, offset, length)
	
	      case 'binary':
	        return binaryWrite(this, string, offset, length)
	
	      case 'base64':
	        // Warning: maxLength not taken into account in base64Write
	        return base64Write(this, string, offset, length)
	
	      case 'ucs2':
	      case 'ucs-2':
	      case 'utf16le':
	      case 'utf-16le':
	        return ucs2Write(this, string, offset, length)
	
	      default:
	        if (loweredCase) throw new TypeError('Unknown encoding: ' + encoding)
	        encoding = ('' + encoding).toLowerCase()
	        loweredCase = true
	    }
	  }
	}
	
	Buffer.prototype.toJSON = function toJSON () {
	  return {
	    type: 'Buffer',
	    data: Array.prototype.slice.call(this._arr || this, 0)
	  }
	}
	
	function base64Slice (buf, start, end) {
	  if (start === 0 && end === buf.length) {
	    return base64.fromByteArray(buf)
	  } else {
	    return base64.fromByteArray(buf.slice(start, end))
	  }
	}
	
	function utf8Slice (buf, start, end) {
	  end = Math.min(buf.length, end)
	  var res = []
	
	  var i = start
	  while (i < end) {
	    var firstByte = buf[i]
	    var codePoint = null
	    var bytesPerSequence = (firstByte > 0xEF) ? 4
	      : (firstByte > 0xDF) ? 3
	      : (firstByte > 0xBF) ? 2
	      : 1
	
	    if (i + bytesPerSequence <= end) {
	      var secondByte, thirdByte, fourthByte, tempCodePoint
	
	      switch (bytesPerSequence) {
	        case 1:
	          if (firstByte < 0x80) {
	            codePoint = firstByte
	          }
	          break
	        case 2:
	          secondByte = buf[i + 1]
	          if ((secondByte & 0xC0) === 0x80) {
	            tempCodePoint = (firstByte & 0x1F) << 0x6 | (secondByte & 0x3F)
	            if (tempCodePoint > 0x7F) {
	              codePoint = tempCodePoint
	            }
	          }
	          break
	        case 3:
	          secondByte = buf[i + 1]
	          thirdByte = buf[i + 2]
	          if ((secondByte & 0xC0) === 0x80 && (thirdByte & 0xC0) === 0x80) {
	            tempCodePoint = (firstByte & 0xF) << 0xC | (secondByte & 0x3F) << 0x6 | (thirdByte & 0x3F)
	            if (tempCodePoint > 0x7FF && (tempCodePoint < 0xD800 || tempCodePoint > 0xDFFF)) {
	              codePoint = tempCodePoint
	            }
	          }
	          break
	        case 4:
	          secondByte = buf[i + 1]
	          thirdByte = buf[i + 2]
	          fourthByte = buf[i + 3]
	          if ((secondByte & 0xC0) === 0x80 && (thirdByte & 0xC0) === 0x80 && (fourthByte & 0xC0) === 0x80) {
	            tempCodePoint = (firstByte & 0xF) << 0x12 | (secondByte & 0x3F) << 0xC | (thirdByte & 0x3F) << 0x6 | (fourthByte & 0x3F)
	            if (tempCodePoint > 0xFFFF && tempCodePoint < 0x110000) {
	              codePoint = tempCodePoint
	            }
	          }
	      }
	    }
	
	    if (codePoint === null) {
	      // we did not generate a valid codePoint so insert a
	      // replacement char (U+FFFD) and advance only 1 byte
	      codePoint = 0xFFFD
	      bytesPerSequence = 1
	    } else if (codePoint > 0xFFFF) {
	      // encode to utf16 (surrogate pair dance)
	      codePoint -= 0x10000
	      res.push(codePoint >>> 10 & 0x3FF | 0xD800)
	      codePoint = 0xDC00 | codePoint & 0x3FF
	    }
	
	    res.push(codePoint)
	    i += bytesPerSequence
	  }
	
	  return decodeCodePointsArray(res)
	}
	
	// Based on http://stackoverflow.com/a/22747272/680742, the browser with
	// the lowest limit is Chrome, with 0x10000 args.
	// We go 1 magnitude less, for safety
	var MAX_ARGUMENTS_LENGTH = 0x1000
	
	function decodeCodePointsArray (codePoints) {
	  var len = codePoints.length
	  if (len <= MAX_ARGUMENTS_LENGTH) {
	    return String.fromCharCode.apply(String, codePoints) // avoid extra slice()
	  }
	
	  // Decode in chunks to avoid "call stack size exceeded".
	  var res = ''
	  var i = 0
	  while (i < len) {
	    res += String.fromCharCode.apply(
	      String,
	      codePoints.slice(i, i += MAX_ARGUMENTS_LENGTH)
	    )
	  }
	  return res
	}
	
	function asciiSlice (buf, start, end) {
	  var ret = ''
	  end = Math.min(buf.length, end)
	
	  for (var i = start; i < end; i++) {
	    ret += String.fromCharCode(buf[i] & 0x7F)
	  }
	  return ret
	}
	
	function binarySlice (buf, start, end) {
	  var ret = ''
	  end = Math.min(buf.length, end)
	
	  for (var i = start; i < end; i++) {
	    ret += String.fromCharCode(buf[i])
	  }
	  return ret
	}
	
	function hexSlice (buf, start, end) {
	  var len = buf.length
	
	  if (!start || start < 0) start = 0
	  if (!end || end < 0 || end > len) end = len
	
	  var out = ''
	  for (var i = start; i < end; i++) {
	    out += toHex(buf[i])
	  }
	  return out
	}
	
	function utf16leSlice (buf, start, end) {
	  var bytes = buf.slice(start, end)
	  var res = ''
	  for (var i = 0; i < bytes.length; i += 2) {
	    res += String.fromCharCode(bytes[i] + bytes[i + 1] * 256)
	  }
	  return res
	}
	
	Buffer.prototype.slice = function slice (start, end) {
	  var len = this.length
	  start = ~~start
	  end = end === undefined ? len : ~~end
	
	  if (start < 0) {
	    start += len
	    if (start < 0) start = 0
	  } else if (start > len) {
	    start = len
	  }
	
	  if (end < 0) {
	    end += len
	    if (end < 0) end = 0
	  } else if (end > len) {
	    end = len
	  }
	
	  if (end < start) end = start
	
	  var newBuf
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    newBuf = Buffer._augment(this.subarray(start, end))
	  } else {
	    var sliceLen = end - start
	    newBuf = new Buffer(sliceLen, undefined)
	    for (var i = 0; i < sliceLen; i++) {
	      newBuf[i] = this[i + start]
	    }
	  }
	
	  if (newBuf.length) newBuf.parent = this.parent || this
	
	  return newBuf
	}
	
	/*
	 * Need to make sure that buffer isn't trying to write out of bounds.
	 */
	function checkOffset (offset, ext, length) {
	  if ((offset % 1) !== 0 || offset < 0) throw new RangeError('offset is not uint')
	  if (offset + ext > length) throw new RangeError('Trying to access beyond buffer length')
	}
	
	Buffer.prototype.readUIntLE = function readUIntLE (offset, byteLength, noAssert) {
	  offset = offset | 0
	  byteLength = byteLength | 0
	  if (!noAssert) checkOffset(offset, byteLength, this.length)
	
	  var val = this[offset]
	  var mul = 1
	  var i = 0
	  while (++i < byteLength && (mul *= 0x100)) {
	    val += this[offset + i] * mul
	  }
	
	  return val
	}
	
	Buffer.prototype.readUIntBE = function readUIntBE (offset, byteLength, noAssert) {
	  offset = offset | 0
	  byteLength = byteLength | 0
	  if (!noAssert) {
	    checkOffset(offset, byteLength, this.length)
	  }
	
	  var val = this[offset + --byteLength]
	  var mul = 1
	  while (byteLength > 0 && (mul *= 0x100)) {
	    val += this[offset + --byteLength] * mul
	  }
	
	  return val
	}
	
	Buffer.prototype.readUInt8 = function readUInt8 (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 1, this.length)
	  return this[offset]
	}
	
	Buffer.prototype.readUInt16LE = function readUInt16LE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 2, this.length)
	  return this[offset] | (this[offset + 1] << 8)
	}
	
	Buffer.prototype.readUInt16BE = function readUInt16BE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 2, this.length)
	  return (this[offset] << 8) | this[offset + 1]
	}
	
	Buffer.prototype.readUInt32LE = function readUInt32LE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 4, this.length)
	
	  return ((this[offset]) |
	      (this[offset + 1] << 8) |
	      (this[offset + 2] << 16)) +
	      (this[offset + 3] * 0x1000000)
	}
	
	Buffer.prototype.readUInt32BE = function readUInt32BE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 4, this.length)
	
	  return (this[offset] * 0x1000000) +
	    ((this[offset + 1] << 16) |
	    (this[offset + 2] << 8) |
	    this[offset + 3])
	}
	
	Buffer.prototype.readIntLE = function readIntLE (offset, byteLength, noAssert) {
	  offset = offset | 0
	  byteLength = byteLength | 0
	  if (!noAssert) checkOffset(offset, byteLength, this.length)
	
	  var val = this[offset]
	  var mul = 1
	  var i = 0
	  while (++i < byteLength && (mul *= 0x100)) {
	    val += this[offset + i] * mul
	  }
	  mul *= 0x80
	
	  if (val >= mul) val -= Math.pow(2, 8 * byteLength)
	
	  return val
	}
	
	Buffer.prototype.readIntBE = function readIntBE (offset, byteLength, noAssert) {
	  offset = offset | 0
	  byteLength = byteLength | 0
	  if (!noAssert) checkOffset(offset, byteLength, this.length)
	
	  var i = byteLength
	  var mul = 1
	  var val = this[offset + --i]
	  while (i > 0 && (mul *= 0x100)) {
	    val += this[offset + --i] * mul
	  }
	  mul *= 0x80
	
	  if (val >= mul) val -= Math.pow(2, 8 * byteLength)
	
	  return val
	}
	
	Buffer.prototype.readInt8 = function readInt8 (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 1, this.length)
	  if (!(this[offset] & 0x80)) return (this[offset])
	  return ((0xff - this[offset] + 1) * -1)
	}
	
	Buffer.prototype.readInt16LE = function readInt16LE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 2, this.length)
	  var val = this[offset] | (this[offset + 1] << 8)
	  return (val & 0x8000) ? val | 0xFFFF0000 : val
	}
	
	Buffer.prototype.readInt16BE = function readInt16BE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 2, this.length)
	  var val = this[offset + 1] | (this[offset] << 8)
	  return (val & 0x8000) ? val | 0xFFFF0000 : val
	}
	
	Buffer.prototype.readInt32LE = function readInt32LE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 4, this.length)
	
	  return (this[offset]) |
	    (this[offset + 1] << 8) |
	    (this[offset + 2] << 16) |
	    (this[offset + 3] << 24)
	}
	
	Buffer.prototype.readInt32BE = function readInt32BE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 4, this.length)
	
	  return (this[offset] << 24) |
	    (this[offset + 1] << 16) |
	    (this[offset + 2] << 8) |
	    (this[offset + 3])
	}
	
	Buffer.prototype.readFloatLE = function readFloatLE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 4, this.length)
	  return ieee754.read(this, offset, true, 23, 4)
	}
	
	Buffer.prototype.readFloatBE = function readFloatBE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 4, this.length)
	  return ieee754.read(this, offset, false, 23, 4)
	}
	
	Buffer.prototype.readDoubleLE = function readDoubleLE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 8, this.length)
	  return ieee754.read(this, offset, true, 52, 8)
	}
	
	Buffer.prototype.readDoubleBE = function readDoubleBE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 8, this.length)
	  return ieee754.read(this, offset, false, 52, 8)
	}
	
	function checkInt (buf, value, offset, ext, max, min) {
	  if (!Buffer.isBuffer(buf)) throw new TypeError('buffer must be a Buffer instance')
	  if (value > max || value < min) throw new RangeError('value is out of bounds')
	  if (offset + ext > buf.length) throw new RangeError('index out of range')
	}
	
	Buffer.prototype.writeUIntLE = function writeUIntLE (value, offset, byteLength, noAssert) {
	  value = +value
	  offset = offset | 0
	  byteLength = byteLength | 0
	  if (!noAssert) checkInt(this, value, offset, byteLength, Math.pow(2, 8 * byteLength), 0)
	
	  var mul = 1
	  var i = 0
	  this[offset] = value & 0xFF
	  while (++i < byteLength && (mul *= 0x100)) {
	    this[offset + i] = (value / mul) & 0xFF
	  }
	
	  return offset + byteLength
	}
	
	Buffer.prototype.writeUIntBE = function writeUIntBE (value, offset, byteLength, noAssert) {
	  value = +value
	  offset = offset | 0
	  byteLength = byteLength | 0
	  if (!noAssert) checkInt(this, value, offset, byteLength, Math.pow(2, 8 * byteLength), 0)
	
	  var i = byteLength - 1
	  var mul = 1
	  this[offset + i] = value & 0xFF
	  while (--i >= 0 && (mul *= 0x100)) {
	    this[offset + i] = (value / mul) & 0xFF
	  }
	
	  return offset + byteLength
	}
	
	Buffer.prototype.writeUInt8 = function writeUInt8 (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 1, 0xff, 0)
	  if (!Buffer.TYPED_ARRAY_SUPPORT) value = Math.floor(value)
	  this[offset] = (value & 0xff)
	  return offset + 1
	}
	
	function objectWriteUInt16 (buf, value, offset, littleEndian) {
	  if (value < 0) value = 0xffff + value + 1
	  for (var i = 0, j = Math.min(buf.length - offset, 2); i < j; i++) {
	    buf[offset + i] = (value & (0xff << (8 * (littleEndian ? i : 1 - i)))) >>>
	      (littleEndian ? i : 1 - i) * 8
	  }
	}
	
	Buffer.prototype.writeUInt16LE = function writeUInt16LE (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 2, 0xffff, 0)
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    this[offset] = (value & 0xff)
	    this[offset + 1] = (value >>> 8)
	  } else {
	    objectWriteUInt16(this, value, offset, true)
	  }
	  return offset + 2
	}
	
	Buffer.prototype.writeUInt16BE = function writeUInt16BE (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 2, 0xffff, 0)
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    this[offset] = (value >>> 8)
	    this[offset + 1] = (value & 0xff)
	  } else {
	    objectWriteUInt16(this, value, offset, false)
	  }
	  return offset + 2
	}
	
	function objectWriteUInt32 (buf, value, offset, littleEndian) {
	  if (value < 0) value = 0xffffffff + value + 1
	  for (var i = 0, j = Math.min(buf.length - offset, 4); i < j; i++) {
	    buf[offset + i] = (value >>> (littleEndian ? i : 3 - i) * 8) & 0xff
	  }
	}
	
	Buffer.prototype.writeUInt32LE = function writeUInt32LE (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 4, 0xffffffff, 0)
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    this[offset + 3] = (value >>> 24)
	    this[offset + 2] = (value >>> 16)
	    this[offset + 1] = (value >>> 8)
	    this[offset] = (value & 0xff)
	  } else {
	    objectWriteUInt32(this, value, offset, true)
	  }
	  return offset + 4
	}
	
	Buffer.prototype.writeUInt32BE = function writeUInt32BE (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 4, 0xffffffff, 0)
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    this[offset] = (value >>> 24)
	    this[offset + 1] = (value >>> 16)
	    this[offset + 2] = (value >>> 8)
	    this[offset + 3] = (value & 0xff)
	  } else {
	    objectWriteUInt32(this, value, offset, false)
	  }
	  return offset + 4
	}
	
	Buffer.prototype.writeIntLE = function writeIntLE (value, offset, byteLength, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) {
	    var limit = Math.pow(2, 8 * byteLength - 1)
	
	    checkInt(this, value, offset, byteLength, limit - 1, -limit)
	  }
	
	  var i = 0
	  var mul = 1
	  var sub = value < 0 ? 1 : 0
	  this[offset] = value & 0xFF
	  while (++i < byteLength && (mul *= 0x100)) {
	    this[offset + i] = ((value / mul) >> 0) - sub & 0xFF
	  }
	
	  return offset + byteLength
	}
	
	Buffer.prototype.writeIntBE = function writeIntBE (value, offset, byteLength, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) {
	    var limit = Math.pow(2, 8 * byteLength - 1)
	
	    checkInt(this, value, offset, byteLength, limit - 1, -limit)
	  }
	
	  var i = byteLength - 1
	  var mul = 1
	  var sub = value < 0 ? 1 : 0
	  this[offset + i] = value & 0xFF
	  while (--i >= 0 && (mul *= 0x100)) {
	    this[offset + i] = ((value / mul) >> 0) - sub & 0xFF
	  }
	
	  return offset + byteLength
	}
	
	Buffer.prototype.writeInt8 = function writeInt8 (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 1, 0x7f, -0x80)
	  if (!Buffer.TYPED_ARRAY_SUPPORT) value = Math.floor(value)
	  if (value < 0) value = 0xff + value + 1
	  this[offset] = (value & 0xff)
	  return offset + 1
	}
	
	Buffer.prototype.writeInt16LE = function writeInt16LE (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 2, 0x7fff, -0x8000)
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    this[offset] = (value & 0xff)
	    this[offset + 1] = (value >>> 8)
	  } else {
	    objectWriteUInt16(this, value, offset, true)
	  }
	  return offset + 2
	}
	
	Buffer.prototype.writeInt16BE = function writeInt16BE (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 2, 0x7fff, -0x8000)
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    this[offset] = (value >>> 8)
	    this[offset + 1] = (value & 0xff)
	  } else {
	    objectWriteUInt16(this, value, offset, false)
	  }
	  return offset + 2
	}
	
	Buffer.prototype.writeInt32LE = function writeInt32LE (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 4, 0x7fffffff, -0x80000000)
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    this[offset] = (value & 0xff)
	    this[offset + 1] = (value >>> 8)
	    this[offset + 2] = (value >>> 16)
	    this[offset + 3] = (value >>> 24)
	  } else {
	    objectWriteUInt32(this, value, offset, true)
	  }
	  return offset + 4
	}
	
	Buffer.prototype.writeInt32BE = function writeInt32BE (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 4, 0x7fffffff, -0x80000000)
	  if (value < 0) value = 0xffffffff + value + 1
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    this[offset] = (value >>> 24)
	    this[offset + 1] = (value >>> 16)
	    this[offset + 2] = (value >>> 8)
	    this[offset + 3] = (value & 0xff)
	  } else {
	    objectWriteUInt32(this, value, offset, false)
	  }
	  return offset + 4
	}
	
	function checkIEEE754 (buf, value, offset, ext, max, min) {
	  if (value > max || value < min) throw new RangeError('value is out of bounds')
	  if (offset + ext > buf.length) throw new RangeError('index out of range')
	  if (offset < 0) throw new RangeError('index out of range')
	}
	
	function writeFloat (buf, value, offset, littleEndian, noAssert) {
	  if (!noAssert) {
	    checkIEEE754(buf, value, offset, 4, 3.4028234663852886e+38, -3.4028234663852886e+38)
	  }
	  ieee754.write(buf, value, offset, littleEndian, 23, 4)
	  return offset + 4
	}
	
	Buffer.prototype.writeFloatLE = function writeFloatLE (value, offset, noAssert) {
	  return writeFloat(this, value, offset, true, noAssert)
	}
	
	Buffer.prototype.writeFloatBE = function writeFloatBE (value, offset, noAssert) {
	  return writeFloat(this, value, offset, false, noAssert)
	}
	
	function writeDouble (buf, value, offset, littleEndian, noAssert) {
	  if (!noAssert) {
	    checkIEEE754(buf, value, offset, 8, 1.7976931348623157E+308, -1.7976931348623157E+308)
	  }
	  ieee754.write(buf, value, offset, littleEndian, 52, 8)
	  return offset + 8
	}
	
	Buffer.prototype.writeDoubleLE = function writeDoubleLE (value, offset, noAssert) {
	  return writeDouble(this, value, offset, true, noAssert)
	}
	
	Buffer.prototype.writeDoubleBE = function writeDoubleBE (value, offset, noAssert) {
	  return writeDouble(this, value, offset, false, noAssert)
	}
	
	// copy(targetBuffer, targetStart=0, sourceStart=0, sourceEnd=buffer.length)
	Buffer.prototype.copy = function copy (target, targetStart, start, end) {
	  if (!start) start = 0
	  if (!end && end !== 0) end = this.length
	  if (targetStart >= target.length) targetStart = target.length
	  if (!targetStart) targetStart = 0
	  if (end > 0 && end < start) end = start
	
	  // Copy 0 bytes; we're done
	  if (end === start) return 0
	  if (target.length === 0 || this.length === 0) return 0
	
	  // Fatal error conditions
	  if (targetStart < 0) {
	    throw new RangeError('targetStart out of bounds')
	  }
	  if (start < 0 || start >= this.length) throw new RangeError('sourceStart out of bounds')
	  if (end < 0) throw new RangeError('sourceEnd out of bounds')
	
	  // Are we oob?
	  if (end > this.length) end = this.length
	  if (target.length - targetStart < end - start) {
	    end = target.length - targetStart + start
	  }
	
	  var len = end - start
	  var i
	
	  if (this === target && start < targetStart && targetStart < end) {
	    // descending copy from end
	    for (i = len - 1; i >= 0; i--) {
	      target[i + targetStart] = this[i + start]
	    }
	  } else if (len < 1000 || !Buffer.TYPED_ARRAY_SUPPORT) {
	    // ascending copy from start
	    for (i = 0; i < len; i++) {
	      target[i + targetStart] = this[i + start]
	    }
	  } else {
	    target._set(this.subarray(start, start + len), targetStart)
	  }
	
	  return len
	}
	
	// fill(value, start=0, end=buffer.length)
	Buffer.prototype.fill = function fill (value, start, end) {
	  if (!value) value = 0
	  if (!start) start = 0
	  if (!end) end = this.length
	
	  if (end < start) throw new RangeError('end < start')
	
	  // Fill 0 bytes; we're done
	  if (end === start) return
	  if (this.length === 0) return
	
	  if (start < 0 || start >= this.length) throw new RangeError('start out of bounds')
	  if (end < 0 || end > this.length) throw new RangeError('end out of bounds')
	
	  var i
	  if (typeof value === 'number') {
	    for (i = start; i < end; i++) {
	      this[i] = value
	    }
	  } else {
	    var bytes = utf8ToBytes(value.toString())
	    var len = bytes.length
	    for (i = start; i < end; i++) {
	      this[i] = bytes[i % len]
	    }
	  }
	
	  return this
	}
	
	/**
	 * Creates a new `ArrayBuffer` with the *copied* memory of the buffer instance.
	 * Added in Node 0.12. Only available in browsers that support ArrayBuffer.
	 */
	Buffer.prototype.toArrayBuffer = function toArrayBuffer () {
	  if (typeof Uint8Array !== 'undefined') {
	    if (Buffer.TYPED_ARRAY_SUPPORT) {
	      return (new Buffer(this)).buffer
	    } else {
	      var buf = new Uint8Array(this.length)
	      for (var i = 0, len = buf.length; i < len; i += 1) {
	        buf[i] = this[i]
	      }
	      return buf.buffer
	    }
	  } else {
	    throw new TypeError('Buffer.toArrayBuffer not supported in this browser')
	  }
	}
	
	// HELPER FUNCTIONS
	// ================
	
	var BP = Buffer.prototype
	
	/**
	 * Augment a Uint8Array *instance* (not the Uint8Array class!) with Buffer methods
	 */
	Buffer._augment = function _augment (arr) {
	  arr.constructor = Buffer
	  arr._isBuffer = true
	
	  // save reference to original Uint8Array set method before overwriting
	  arr._set = arr.set
	
	  // deprecated
	  arr.get = BP.get
	  arr.set = BP.set
	
	  arr.write = BP.write
	  arr.toString = BP.toString
	  arr.toLocaleString = BP.toString
	  arr.toJSON = BP.toJSON
	  arr.equals = BP.equals
	  arr.compare = BP.compare
	  arr.indexOf = BP.indexOf
	  arr.copy = BP.copy
	  arr.slice = BP.slice
	  arr.readUIntLE = BP.readUIntLE
	  arr.readUIntBE = BP.readUIntBE
	  arr.readUInt8 = BP.readUInt8
	  arr.readUInt16LE = BP.readUInt16LE
	  arr.readUInt16BE = BP.readUInt16BE
	  arr.readUInt32LE = BP.readUInt32LE
	  arr.readUInt32BE = BP.readUInt32BE
	  arr.readIntLE = BP.readIntLE
	  arr.readIntBE = BP.readIntBE
	  arr.readInt8 = BP.readInt8
	  arr.readInt16LE = BP.readInt16LE
	  arr.readInt16BE = BP.readInt16BE
	  arr.readInt32LE = BP.readInt32LE
	  arr.readInt32BE = BP.readInt32BE
	  arr.readFloatLE = BP.readFloatLE
	  arr.readFloatBE = BP.readFloatBE
	  arr.readDoubleLE = BP.readDoubleLE
	  arr.readDoubleBE = BP.readDoubleBE
	  arr.writeUInt8 = BP.writeUInt8
	  arr.writeUIntLE = BP.writeUIntLE
	  arr.writeUIntBE = BP.writeUIntBE
	  arr.writeUInt16LE = BP.writeUInt16LE
	  arr.writeUInt16BE = BP.writeUInt16BE
	  arr.writeUInt32LE = BP.writeUInt32LE
	  arr.writeUInt32BE = BP.writeUInt32BE
	  arr.writeIntLE = BP.writeIntLE
	  arr.writeIntBE = BP.writeIntBE
	  arr.writeInt8 = BP.writeInt8
	  arr.writeInt16LE = BP.writeInt16LE
	  arr.writeInt16BE = BP.writeInt16BE
	  arr.writeInt32LE = BP.writeInt32LE
	  arr.writeInt32BE = BP.writeInt32BE
	  arr.writeFloatLE = BP.writeFloatLE
	  arr.writeFloatBE = BP.writeFloatBE
	  arr.writeDoubleLE = BP.writeDoubleLE
	  arr.writeDoubleBE = BP.writeDoubleBE
	  arr.fill = BP.fill
	  arr.inspect = BP.inspect
	  arr.toArrayBuffer = BP.toArrayBuffer
	
	  return arr
	}
	
	var INVALID_BASE64_RE = /[^+\/0-9A-Za-z-_]/g
	
	function base64clean (str) {
	  // Node strips out invalid characters like \n and \t from the string, base64-js does not
	  str = stringtrim(str).replace(INVALID_BASE64_RE, '')
	  // Node converts strings with length < 2 to ''
	  if (str.length < 2) return ''
	  // Node allows for non-padded base64 strings (missing trailing ===), base64-js does not
	  while (str.length % 4 !== 0) {
	    str = str + '='
	  }
	  return str
	}
	
	function stringtrim (str) {
	  if (str.trim) return str.trim()
	  return str.replace(/^\s+|\s+$/g, '')
	}
	
	function toHex (n) {
	  if (n < 16) return '0' + n.toString(16)
	  return n.toString(16)
	}
	
	function utf8ToBytes (string, units) {
	  units = units || Infinity
	  var codePoint
	  var length = string.length
	  var leadSurrogate = null
	  var bytes = []
	
	  for (var i = 0; i < length; i++) {
	    codePoint = string.charCodeAt(i)
	
	    // is surrogate component
	    if (codePoint > 0xD7FF && codePoint < 0xE000) {
	      // last char was a lead
	      if (!leadSurrogate) {
	        // no lead yet
	        if (codePoint > 0xDBFF) {
	          // unexpected trail
	          if ((units -= 3) > -1) bytes.push(0xEF, 0xBF, 0xBD)
	          continue
	        } else if (i + 1 === length) {
	          // unpaired lead
	          if ((units -= 3) > -1) bytes.push(0xEF, 0xBF, 0xBD)
	          continue
	        }
	
	        // valid lead
	        leadSurrogate = codePoint
	
	        continue
	      }
	
	      // 2 leads in a row
	      if (codePoint < 0xDC00) {
	        if ((units -= 3) > -1) bytes.push(0xEF, 0xBF, 0xBD)
	        leadSurrogate = codePoint
	        continue
	      }
	
	      // valid surrogate pair
	      codePoint = (leadSurrogate - 0xD800 << 10 | codePoint - 0xDC00) + 0x10000
	    } else if (leadSurrogate) {
	      // valid bmp char, but last char was a lead
	      if ((units -= 3) > -1) bytes.push(0xEF, 0xBF, 0xBD)
	    }
	
	    leadSurrogate = null
	
	    // encode utf8
	    if (codePoint < 0x80) {
	      if ((units -= 1) < 0) break
	      bytes.push(codePoint)
	    } else if (codePoint < 0x800) {
	      if ((units -= 2) < 0) break
	      bytes.push(
	        codePoint >> 0x6 | 0xC0,
	        codePoint & 0x3F | 0x80
	      )
	    } else if (codePoint < 0x10000) {
	      if ((units -= 3) < 0) break
	      bytes.push(
	        codePoint >> 0xC | 0xE0,
	        codePoint >> 0x6 & 0x3F | 0x80,
	        codePoint & 0x3F | 0x80
	      )
	    } else if (codePoint < 0x110000) {
	      if ((units -= 4) < 0) break
	      bytes.push(
	        codePoint >> 0x12 | 0xF0,
	        codePoint >> 0xC & 0x3F | 0x80,
	        codePoint >> 0x6 & 0x3F | 0x80,
	        codePoint & 0x3F | 0x80
	      )
	    } else {
	      throw new Error('Invalid code point')
	    }
	  }
	
	  return bytes
	}
	
	function asciiToBytes (str) {
	  var byteArray = []
	  for (var i = 0; i < str.length; i++) {
	    // Node's code seems to be doing this and not & 0x7F..
	    byteArray.push(str.charCodeAt(i) & 0xFF)
	  }
	  return byteArray
	}
	
	function utf16leToBytes (str, units) {
	  var c, hi, lo
	  var byteArray = []
	  for (var i = 0; i < str.length; i++) {
	    if ((units -= 2) < 0) break
	
	    c = str.charCodeAt(i)
	    hi = c >> 8
	    lo = c % 256
	    byteArray.push(lo)
	    byteArray.push(hi)
	  }
	
	  return byteArray
	}
	
	function base64ToBytes (str) {
	  return base64.toByteArray(base64clean(str))
	}
	
	function blitBuffer (src, dst, offset, length) {
	  for (var i = 0; i < length; i++) {
	    if ((i + offset >= dst.length) || (i >= src.length)) break
	    dst[i + offset] = src[i]
	  }
	  return i
	}
	
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! (webpack)/~/buffer/index.js */ 76).Buffer, (function() { return this; }())))

/***/ },
/* 77 */
/*!****************************************!*\
  !*** (webpack)/~/base64-js/lib/b64.js ***!
  \****************************************/
/***/ function(module, exports, __webpack_require__) {

	var lookup = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
	
	;(function (exports) {
		'use strict';
	
	  var Arr = (typeof Uint8Array !== 'undefined')
	    ? Uint8Array
	    : Array
	
		var PLUS   = '+'.charCodeAt(0)
		var SLASH  = '/'.charCodeAt(0)
		var NUMBER = '0'.charCodeAt(0)
		var LOWER  = 'a'.charCodeAt(0)
		var UPPER  = 'A'.charCodeAt(0)
		var PLUS_URL_SAFE = '-'.charCodeAt(0)
		var SLASH_URL_SAFE = '_'.charCodeAt(0)
	
		function decode (elt) {
			var code = elt.charCodeAt(0)
			if (code === PLUS ||
			    code === PLUS_URL_SAFE)
				return 62 // '+'
			if (code === SLASH ||
			    code === SLASH_URL_SAFE)
				return 63 // '/'
			if (code < NUMBER)
				return -1 //no match
			if (code < NUMBER + 10)
				return code - NUMBER + 26 + 26
			if (code < UPPER + 26)
				return code - UPPER
			if (code < LOWER + 26)
				return code - LOWER + 26
		}
	
		function b64ToByteArray (b64) {
			var i, j, l, tmp, placeHolders, arr
	
			if (b64.length % 4 > 0) {
				throw new Error('Invalid string. Length must be a multiple of 4')
			}
	
			// the number of equal signs (place holders)
			// if there are two placeholders, than the two characters before it
			// represent one byte
			// if there is only one, then the three characters before it represent 2 bytes
			// this is just a cheap hack to not do indexOf twice
			var len = b64.length
			placeHolders = '=' === b64.charAt(len - 2) ? 2 : '=' === b64.charAt(len - 1) ? 1 : 0
	
			// base64 is 4/3 + up to two characters of the original data
			arr = new Arr(b64.length * 3 / 4 - placeHolders)
	
			// if there are placeholders, only get up to the last complete 4 chars
			l = placeHolders > 0 ? b64.length - 4 : b64.length
	
			var L = 0
	
			function push (v) {
				arr[L++] = v
			}
	
			for (i = 0, j = 0; i < l; i += 4, j += 3) {
				tmp = (decode(b64.charAt(i)) << 18) | (decode(b64.charAt(i + 1)) << 12) | (decode(b64.charAt(i + 2)) << 6) | decode(b64.charAt(i + 3))
				push((tmp & 0xFF0000) >> 16)
				push((tmp & 0xFF00) >> 8)
				push(tmp & 0xFF)
			}
	
			if (placeHolders === 2) {
				tmp = (decode(b64.charAt(i)) << 2) | (decode(b64.charAt(i + 1)) >> 4)
				push(tmp & 0xFF)
			} else if (placeHolders === 1) {
				tmp = (decode(b64.charAt(i)) << 10) | (decode(b64.charAt(i + 1)) << 4) | (decode(b64.charAt(i + 2)) >> 2)
				push((tmp >> 8) & 0xFF)
				push(tmp & 0xFF)
			}
	
			return arr
		}
	
		function uint8ToBase64 (uint8) {
			var i,
				extraBytes = uint8.length % 3, // if we have 1 byte left, pad 2 bytes
				output = "",
				temp, length
	
			function encode (num) {
				return lookup.charAt(num)
			}
	
			function tripletToBase64 (num) {
				return encode(num >> 18 & 0x3F) + encode(num >> 12 & 0x3F) + encode(num >> 6 & 0x3F) + encode(num & 0x3F)
			}
	
			// go through the array every three bytes, we'll deal with trailing stuff later
			for (i = 0, length = uint8.length - extraBytes; i < length; i += 3) {
				temp = (uint8[i] << 16) + (uint8[i + 1] << 8) + (uint8[i + 2])
				output += tripletToBase64(temp)
			}
	
			// pad the end with zeros, but make sure to not forget the extra bytes
			switch (extraBytes) {
				case 1:
					temp = uint8[uint8.length - 1]
					output += encode(temp >> 2)
					output += encode((temp << 4) & 0x3F)
					output += '=='
					break
				case 2:
					temp = (uint8[uint8.length - 2] << 8) + (uint8[uint8.length - 1])
					output += encode(temp >> 10)
					output += encode((temp >> 4) & 0x3F)
					output += encode((temp << 2) & 0x3F)
					output += '='
					break
			}
	
			return output
		}
	
		exports.toByteArray = b64ToByteArray
		exports.fromByteArray = uint8ToBase64
	}( false ? (this.base64js = {}) : exports))


/***/ },
/* 78 */
/*!************************************!*\
  !*** (webpack)/~/ieee754/index.js ***!
  \************************************/
/***/ function(module, exports) {

	exports.read = function (buffer, offset, isLE, mLen, nBytes) {
	  var e, m
	  var eLen = nBytes * 8 - mLen - 1
	  var eMax = (1 << eLen) - 1
	  var eBias = eMax >> 1
	  var nBits = -7
	  var i = isLE ? (nBytes - 1) : 0
	  var d = isLE ? -1 : 1
	  var s = buffer[offset + i]
	
	  i += d
	
	  e = s & ((1 << (-nBits)) - 1)
	  s >>= (-nBits)
	  nBits += eLen
	  for (; nBits > 0; e = e * 256 + buffer[offset + i], i += d, nBits -= 8) {}
	
	  m = e & ((1 << (-nBits)) - 1)
	  e >>= (-nBits)
	  nBits += mLen
	  for (; nBits > 0; m = m * 256 + buffer[offset + i], i += d, nBits -= 8) {}
	
	  if (e === 0) {
	    e = 1 - eBias
	  } else if (e === eMax) {
	    return m ? NaN : ((s ? -1 : 1) * Infinity)
	  } else {
	    m = m + Math.pow(2, mLen)
	    e = e - eBias
	  }
	  return (s ? -1 : 1) * m * Math.pow(2, e - mLen)
	}
	
	exports.write = function (buffer, value, offset, isLE, mLen, nBytes) {
	  var e, m, c
	  var eLen = nBytes * 8 - mLen - 1
	  var eMax = (1 << eLen) - 1
	  var eBias = eMax >> 1
	  var rt = (mLen === 23 ? Math.pow(2, -24) - Math.pow(2, -77) : 0)
	  var i = isLE ? 0 : (nBytes - 1)
	  var d = isLE ? 1 : -1
	  var s = value < 0 || (value === 0 && 1 / value < 0) ? 1 : 0
	
	  value = Math.abs(value)
	
	  if (isNaN(value) || value === Infinity) {
	    m = isNaN(value) ? 1 : 0
	    e = eMax
	  } else {
	    e = Math.floor(Math.log(value) / Math.LN2)
	    if (value * (c = Math.pow(2, -e)) < 1) {
	      e--
	      c *= 2
	    }
	    if (e + eBias >= 1) {
	      value += rt / c
	    } else {
	      value += rt * Math.pow(2, 1 - eBias)
	    }
	    if (value * c >= 2) {
	      e++
	      c /= 2
	    }
	
	    if (e + eBias >= eMax) {
	      m = 0
	      e = eMax
	    } else if (e + eBias >= 1) {
	      m = (value * c - 1) * Math.pow(2, mLen)
	      e = e + eBias
	    } else {
	      m = value * Math.pow(2, eBias - 1) * Math.pow(2, mLen)
	      e = 0
	    }
	  }
	
	  for (; mLen >= 8; buffer[offset + i] = m & 0xff, i += d, m /= 256, mLen -= 8) {}
	
	  e = (e << mLen) | m
	  eLen += mLen
	  for (; eLen > 0; buffer[offset + i] = e & 0xff, i += d, e /= 256, eLen -= 8) {}
	
	  buffer[offset + i - d] |= s * 128
	}


/***/ },
/* 79 */
/*!********************************************!*\
  !*** (webpack)/~/core-util-is/lib/util.js ***!
  \********************************************/
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(Buffer) {// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.
	
	// NOTE: These type checking functions intentionally don't use `instanceof`
	// because it is fragile and can be easily faked with `Object.create()`.
	
	function isArray(arg) {
	  if (Array.isArray) {
	    return Array.isArray(arg);
	  }
	  return objectToString(arg) === '[object Array]';
	}
	exports.isArray = isArray;
	
	function isBoolean(arg) {
	  return typeof arg === 'boolean';
	}
	exports.isBoolean = isBoolean;
	
	function isNull(arg) {
	  return arg === null;
	}
	exports.isNull = isNull;
	
	function isNullOrUndefined(arg) {
	  return arg == null;
	}
	exports.isNullOrUndefined = isNullOrUndefined;
	
	function isNumber(arg) {
	  return typeof arg === 'number';
	}
	exports.isNumber = isNumber;
	
	function isString(arg) {
	  return typeof arg === 'string';
	}
	exports.isString = isString;
	
	function isSymbol(arg) {
	  return typeof arg === 'symbol';
	}
	exports.isSymbol = isSymbol;
	
	function isUndefined(arg) {
	  return arg === void 0;
	}
	exports.isUndefined = isUndefined;
	
	function isRegExp(re) {
	  return objectToString(re) === '[object RegExp]';
	}
	exports.isRegExp = isRegExp;
	
	function isObject(arg) {
	  return typeof arg === 'object' && arg !== null;
	}
	exports.isObject = isObject;
	
	function isDate(d) {
	  return objectToString(d) === '[object Date]';
	}
	exports.isDate = isDate;
	
	function isError(e) {
	  return (objectToString(e) === '[object Error]' || e instanceof Error);
	}
	exports.isError = isError;
	
	function isFunction(arg) {
	  return typeof arg === 'function';
	}
	exports.isFunction = isFunction;
	
	function isPrimitive(arg) {
	  return arg === null ||
	         typeof arg === 'boolean' ||
	         typeof arg === 'number' ||
	         typeof arg === 'string' ||
	         typeof arg === 'symbol' ||  // ES6 symbol
	         typeof arg === 'undefined';
	}
	exports.isPrimitive = isPrimitive;
	
	exports.isBuffer = Buffer.isBuffer;
	
	function objectToString(o) {
	  return Object.prototype.toString.call(o);
	}
	
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! (webpack)/~/buffer/index.js */ 76).Buffer))

/***/ },
/* 80 */
/*!**********************!*\
  !*** util (ignored) ***!
  \**********************/
/***/ function(module, exports) {

	/* (ignored) */

/***/ },
/* 81 */
/*!*****************************************************************************!*\
  !*** (webpack)/~/stream-browserify/~/readable-stream/lib/_stream_duplex.js ***!
  \*****************************************************************************/
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(process) {// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.
	
	// a duplex stream is just a stream that is both readable and writable.
	// Since JS doesn't have multiple prototypal inheritance, this class
	// prototypally inherits from Readable, and then parasitically from
	// Writable.
	
	module.exports = Duplex;
	
	/*<replacement>*/
	var objectKeys = Object.keys || function (obj) {
	  var keys = [];
	  for (var key in obj) keys.push(key);
	  return keys;
	}
	/*</replacement>*/
	
	
	/*<replacement>*/
	var util = __webpack_require__(/*! core-util-is */ 79);
	util.inherits = __webpack_require__(/*! inherits */ 72);
	/*</replacement>*/
	
	var Readable = __webpack_require__(/*! ./_stream_readable */ 74);
	var Writable = __webpack_require__(/*! ./_stream_writable */ 82);
	
	util.inherits(Duplex, Readable);
	
	forEach(objectKeys(Writable.prototype), function(method) {
	  if (!Duplex.prototype[method])
	    Duplex.prototype[method] = Writable.prototype[method];
	});
	
	function Duplex(options) {
	  if (!(this instanceof Duplex))
	    return new Duplex(options);
	
	  Readable.call(this, options);
	  Writable.call(this, options);
	
	  if (options && options.readable === false)
	    this.readable = false;
	
	  if (options && options.writable === false)
	    this.writable = false;
	
	  this.allowHalfOpen = true;
	  if (options && options.allowHalfOpen === false)
	    this.allowHalfOpen = false;
	
	  this.once('end', onend);
	}
	
	// the no-half-open enforcer
	function onend() {
	  // if we allow half-open state, or if the writable side ended,
	  // then we're ok.
	  if (this.allowHalfOpen || this._writableState.ended)
	    return;
	
	  // no more data can be written.
	  // But allow more writes to happen in this tick.
	  process.nextTick(this.end.bind(this));
	}
	
	function forEach (xs, f) {
	  for (var i = 0, l = xs.length; i < l; i++) {
	    f(xs[i], i);
	  }
	}
	
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! (webpack)/~/process/browser.js */ 30)))

/***/ },
/* 82 */
/*!*******************************************************************************!*\
  !*** (webpack)/~/stream-browserify/~/readable-stream/lib/_stream_writable.js ***!
  \*******************************************************************************/
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(process) {// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.
	
	// A bit simpler than readable streams.
	// Implement an async ._write(chunk, cb), and it'll handle all
	// the drain event emission and buffering.
	
	module.exports = Writable;
	
	/*<replacement>*/
	var Buffer = __webpack_require__(/*! buffer */ 76).Buffer;
	/*</replacement>*/
	
	Writable.WritableState = WritableState;
	
	
	/*<replacement>*/
	var util = __webpack_require__(/*! core-util-is */ 79);
	util.inherits = __webpack_require__(/*! inherits */ 72);
	/*</replacement>*/
	
	var Stream = __webpack_require__(/*! stream */ 71);
	
	util.inherits(Writable, Stream);
	
	function WriteReq(chunk, encoding, cb) {
	  this.chunk = chunk;
	  this.encoding = encoding;
	  this.callback = cb;
	}
	
	function WritableState(options, stream) {
	  var Duplex = __webpack_require__(/*! ./_stream_duplex */ 81);
	
	  options = options || {};
	
	  // the point at which write() starts returning false
	  // Note: 0 is a valid value, means that we always return false if
	  // the entire buffer is not flushed immediately on write()
	  var hwm = options.highWaterMark;
	  var defaultHwm = options.objectMode ? 16 : 16 * 1024;
	  this.highWaterMark = (hwm || hwm === 0) ? hwm : defaultHwm;
	
	  // object stream flag to indicate whether or not this stream
	  // contains buffers or objects.
	  this.objectMode = !!options.objectMode;
	
	  if (stream instanceof Duplex)
	    this.objectMode = this.objectMode || !!options.writableObjectMode;
	
	  // cast to ints.
	  this.highWaterMark = ~~this.highWaterMark;
	
	  this.needDrain = false;
	  // at the start of calling end()
	  this.ending = false;
	  // when end() has been called, and returned
	  this.ended = false;
	  // when 'finish' is emitted
	  this.finished = false;
	
	  // should we decode strings into buffers before passing to _write?
	  // this is here so that some node-core streams can optimize string
	  // handling at a lower level.
	  var noDecode = options.decodeStrings === false;
	  this.decodeStrings = !noDecode;
	
	  // Crypto is kind of old and crusty.  Historically, its default string
	  // encoding is 'binary' so we have to make this configurable.
	  // Everything else in the universe uses 'utf8', though.
	  this.defaultEncoding = options.defaultEncoding || 'utf8';
	
	  // not an actual buffer we keep track of, but a measurement
	  // of how much we're waiting to get pushed to some underlying
	  // socket or file.
	  this.length = 0;
	
	  // a flag to see when we're in the middle of a write.
	  this.writing = false;
	
	  // when true all writes will be buffered until .uncork() call
	  this.corked = 0;
	
	  // a flag to be able to tell if the onwrite cb is called immediately,
	  // or on a later tick.  We set this to true at first, because any
	  // actions that shouldn't happen until "later" should generally also
	  // not happen before the first write call.
	  this.sync = true;
	
	  // a flag to know if we're processing previously buffered items, which
	  // may call the _write() callback in the same tick, so that we don't
	  // end up in an overlapped onwrite situation.
	  this.bufferProcessing = false;
	
	  // the callback that's passed to _write(chunk,cb)
	  this.onwrite = function(er) {
	    onwrite(stream, er);
	  };
	
	  // the callback that the user supplies to write(chunk,encoding,cb)
	  this.writecb = null;
	
	  // the amount that is being written when _write is called.
	  this.writelen = 0;
	
	  this.buffer = [];
	
	  // number of pending user-supplied write callbacks
	  // this must be 0 before 'finish' can be emitted
	  this.pendingcb = 0;
	
	  // emit prefinish if the only thing we're waiting for is _write cbs
	  // This is relevant for synchronous Transform streams
	  this.prefinished = false;
	
	  // True if the error was already emitted and should not be thrown again
	  this.errorEmitted = false;
	}
	
	function Writable(options) {
	  var Duplex = __webpack_require__(/*! ./_stream_duplex */ 81);
	
	  // Writable ctor is applied to Duplexes, though they're not
	  // instanceof Writable, they're instanceof Readable.
	  if (!(this instanceof Writable) && !(this instanceof Duplex))
	    return new Writable(options);
	
	  this._writableState = new WritableState(options, this);
	
	  // legacy.
	  this.writable = true;
	
	  Stream.call(this);
	}
	
	// Otherwise people can pipe Writable streams, which is just wrong.
	Writable.prototype.pipe = function() {
	  this.emit('error', new Error('Cannot pipe. Not readable.'));
	};
	
	
	function writeAfterEnd(stream, state, cb) {
	  var er = new Error('write after end');
	  // TODO: defer error events consistently everywhere, not just the cb
	  stream.emit('error', er);
	  process.nextTick(function() {
	    cb(er);
	  });
	}
	
	// If we get something that is not a buffer, string, null, or undefined,
	// and we're not in objectMode, then that's an error.
	// Otherwise stream chunks are all considered to be of length=1, and the
	// watermarks determine how many objects to keep in the buffer, rather than
	// how many bytes or characters.
	function validChunk(stream, state, chunk, cb) {
	  var valid = true;
	  if (!util.isBuffer(chunk) &&
	      !util.isString(chunk) &&
	      !util.isNullOrUndefined(chunk) &&
	      !state.objectMode) {
	    var er = new TypeError('Invalid non-string/buffer chunk');
	    stream.emit('error', er);
	    process.nextTick(function() {
	      cb(er);
	    });
	    valid = false;
	  }
	  return valid;
	}
	
	Writable.prototype.write = function(chunk, encoding, cb) {
	  var state = this._writableState;
	  var ret = false;
	
	  if (util.isFunction(encoding)) {
	    cb = encoding;
	    encoding = null;
	  }
	
	  if (util.isBuffer(chunk))
	    encoding = 'buffer';
	  else if (!encoding)
	    encoding = state.defaultEncoding;
	
	  if (!util.isFunction(cb))
	    cb = function() {};
	
	  if (state.ended)
	    writeAfterEnd(this, state, cb);
	  else if (validChunk(this, state, chunk, cb)) {
	    state.pendingcb++;
	    ret = writeOrBuffer(this, state, chunk, encoding, cb);
	  }
	
	  return ret;
	};
	
	Writable.prototype.cork = function() {
	  var state = this._writableState;
	
	  state.corked++;
	};
	
	Writable.prototype.uncork = function() {
	  var state = this._writableState;
	
	  if (state.corked) {
	    state.corked--;
	
	    if (!state.writing &&
	        !state.corked &&
	        !state.finished &&
	        !state.bufferProcessing &&
	        state.buffer.length)
	      clearBuffer(this, state);
	  }
	};
	
	function decodeChunk(state, chunk, encoding) {
	  if (!state.objectMode &&
	      state.decodeStrings !== false &&
	      util.isString(chunk)) {
	    chunk = new Buffer(chunk, encoding);
	  }
	  return chunk;
	}
	
	// if we're already writing something, then just put this
	// in the queue, and wait our turn.  Otherwise, call _write
	// If we return false, then we need a drain event, so set that flag.
	function writeOrBuffer(stream, state, chunk, encoding, cb) {
	  chunk = decodeChunk(state, chunk, encoding);
	  if (util.isBuffer(chunk))
	    encoding = 'buffer';
	  var len = state.objectMode ? 1 : chunk.length;
	
	  state.length += len;
	
	  var ret = state.length < state.highWaterMark;
	  // we must ensure that previous needDrain will not be reset to false.
	  if (!ret)
	    state.needDrain = true;
	
	  if (state.writing || state.corked)
	    state.buffer.push(new WriteReq(chunk, encoding, cb));
	  else
	    doWrite(stream, state, false, len, chunk, encoding, cb);
	
	  return ret;
	}
	
	function doWrite(stream, state, writev, len, chunk, encoding, cb) {
	  state.writelen = len;
	  state.writecb = cb;
	  state.writing = true;
	  state.sync = true;
	  if (writev)
	    stream._writev(chunk, state.onwrite);
	  else
	    stream._write(chunk, encoding, state.onwrite);
	  state.sync = false;
	}
	
	function onwriteError(stream, state, sync, er, cb) {
	  if (sync)
	    process.nextTick(function() {
	      state.pendingcb--;
	      cb(er);
	    });
	  else {
	    state.pendingcb--;
	    cb(er);
	  }
	
	  stream._writableState.errorEmitted = true;
	  stream.emit('error', er);
	}
	
	function onwriteStateUpdate(state) {
	  state.writing = false;
	  state.writecb = null;
	  state.length -= state.writelen;
	  state.writelen = 0;
	}
	
	function onwrite(stream, er) {
	  var state = stream._writableState;
	  var sync = state.sync;
	  var cb = state.writecb;
	
	  onwriteStateUpdate(state);
	
	  if (er)
	    onwriteError(stream, state, sync, er, cb);
	  else {
	    // Check if we're actually ready to finish, but don't emit yet
	    var finished = needFinish(stream, state);
	
	    if (!finished &&
	        !state.corked &&
	        !state.bufferProcessing &&
	        state.buffer.length) {
	      clearBuffer(stream, state);
	    }
	
	    if (sync) {
	      process.nextTick(function() {
	        afterWrite(stream, state, finished, cb);
	      });
	    } else {
	      afterWrite(stream, state, finished, cb);
	    }
	  }
	}
	
	function afterWrite(stream, state, finished, cb) {
	  if (!finished)
	    onwriteDrain(stream, state);
	  state.pendingcb--;
	  cb();
	  finishMaybe(stream, state);
	}
	
	// Must force callback to be called on nextTick, so that we don't
	// emit 'drain' before the write() consumer gets the 'false' return
	// value, and has a chance to attach a 'drain' listener.
	function onwriteDrain(stream, state) {
	  if (state.length === 0 && state.needDrain) {
	    state.needDrain = false;
	    stream.emit('drain');
	  }
	}
	
	
	// if there's something in the buffer waiting, then process it
	function clearBuffer(stream, state) {
	  state.bufferProcessing = true;
	
	  if (stream._writev && state.buffer.length > 1) {
	    // Fast case, write everything using _writev()
	    var cbs = [];
	    for (var c = 0; c < state.buffer.length; c++)
	      cbs.push(state.buffer[c].callback);
	
	    // count the one we are adding, as well.
	    // TODO(isaacs) clean this up
	    state.pendingcb++;
	    doWrite(stream, state, true, state.length, state.buffer, '', function(err) {
	      for (var i = 0; i < cbs.length; i++) {
	        state.pendingcb--;
	        cbs[i](err);
	      }
	    });
	
	    // Clear buffer
	    state.buffer = [];
	  } else {
	    // Slow case, write chunks one-by-one
	    for (var c = 0; c < state.buffer.length; c++) {
	      var entry = state.buffer[c];
	      var chunk = entry.chunk;
	      var encoding = entry.encoding;
	      var cb = entry.callback;
	      var len = state.objectMode ? 1 : chunk.length;
	
	      doWrite(stream, state, false, len, chunk, encoding, cb);
	
	      // if we didn't call the onwrite immediately, then
	      // it means that we need to wait until it does.
	      // also, that means that the chunk and cb are currently
	      // being processed, so move the buffer counter past them.
	      if (state.writing) {
	        c++;
	        break;
	      }
	    }
	
	    if (c < state.buffer.length)
	      state.buffer = state.buffer.slice(c);
	    else
	      state.buffer.length = 0;
	  }
	
	  state.bufferProcessing = false;
	}
	
	Writable.prototype._write = function(chunk, encoding, cb) {
	  cb(new Error('not implemented'));
	
	};
	
	Writable.prototype._writev = null;
	
	Writable.prototype.end = function(chunk, encoding, cb) {
	  var state = this._writableState;
	
	  if (util.isFunction(chunk)) {
	    cb = chunk;
	    chunk = null;
	    encoding = null;
	  } else if (util.isFunction(encoding)) {
	    cb = encoding;
	    encoding = null;
	  }
	
	  if (!util.isNullOrUndefined(chunk))
	    this.write(chunk, encoding);
	
	  // .end() fully uncorks
	  if (state.corked) {
	    state.corked = 1;
	    this.uncork();
	  }
	
	  // ignore unnecessary end() calls.
	  if (!state.ending && !state.finished)
	    endWritable(this, state, cb);
	};
	
	
	function needFinish(stream, state) {
	  return (state.ending &&
	          state.length === 0 &&
	          !state.finished &&
	          !state.writing);
	}
	
	function prefinish(stream, state) {
	  if (!state.prefinished) {
	    state.prefinished = true;
	    stream.emit('prefinish');
	  }
	}
	
	function finishMaybe(stream, state) {
	  var need = needFinish(stream, state);
	  if (need) {
	    if (state.pendingcb === 0) {
	      prefinish(stream, state);
	      state.finished = true;
	      stream.emit('finish');
	    } else
	      prefinish(stream, state);
	  }
	  return need;
	}
	
	function endWritable(stream, state, cb) {
	  state.ending = true;
	  finishMaybe(stream, state);
	  if (cb) {
	    if (state.finished)
	      process.nextTick(cb);
	    else
	      stream.once('finish', cb);
	  }
	  state.ended = true;
	}
	
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! (webpack)/~/process/browser.js */ 30)))

/***/ },
/* 83 */
/*!*******************************************!*\
  !*** (webpack)/~/string_decoder/index.js ***!
  \*******************************************/
/***/ function(module, exports, __webpack_require__) {

	// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.
	
	var Buffer = __webpack_require__(/*! buffer */ 76).Buffer;
	
	var isBufferEncoding = Buffer.isEncoding
	  || function(encoding) {
	       switch (encoding && encoding.toLowerCase()) {
	         case 'hex': case 'utf8': case 'utf-8': case 'ascii': case 'binary': case 'base64': case 'ucs2': case 'ucs-2': case 'utf16le': case 'utf-16le': case 'raw': return true;
	         default: return false;
	       }
	     }
	
	
	function assertEncoding(encoding) {
	  if (encoding && !isBufferEncoding(encoding)) {
	    throw new Error('Unknown encoding: ' + encoding);
	  }
	}
	
	// StringDecoder provides an interface for efficiently splitting a series of
	// buffers into a series of JS strings without breaking apart multi-byte
	// characters. CESU-8 is handled as part of the UTF-8 encoding.
	//
	// @TODO Handling all encodings inside a single object makes it very difficult
	// to reason about this code, so it should be split up in the future.
	// @TODO There should be a utf8-strict encoding that rejects invalid UTF-8 code
	// points as used by CESU-8.
	var StringDecoder = exports.StringDecoder = function(encoding) {
	  this.encoding = (encoding || 'utf8').toLowerCase().replace(/[-_]/, '');
	  assertEncoding(encoding);
	  switch (this.encoding) {
	    case 'utf8':
	      // CESU-8 represents each of Surrogate Pair by 3-bytes
	      this.surrogateSize = 3;
	      break;
	    case 'ucs2':
	    case 'utf16le':
	      // UTF-16 represents each of Surrogate Pair by 2-bytes
	      this.surrogateSize = 2;
	      this.detectIncompleteChar = utf16DetectIncompleteChar;
	      break;
	    case 'base64':
	      // Base-64 stores 3 bytes in 4 chars, and pads the remainder.
	      this.surrogateSize = 3;
	      this.detectIncompleteChar = base64DetectIncompleteChar;
	      break;
	    default:
	      this.write = passThroughWrite;
	      return;
	  }
	
	  // Enough space to store all bytes of a single character. UTF-8 needs 4
	  // bytes, but CESU-8 may require up to 6 (3 bytes per surrogate).
	  this.charBuffer = new Buffer(6);
	  // Number of bytes received for the current incomplete multi-byte character.
	  this.charReceived = 0;
	  // Number of bytes expected for the current incomplete multi-byte character.
	  this.charLength = 0;
	};
	
	
	// write decodes the given buffer and returns it as JS string that is
	// guaranteed to not contain any partial multi-byte characters. Any partial
	// character found at the end of the buffer is buffered up, and will be
	// returned when calling write again with the remaining bytes.
	//
	// Note: Converting a Buffer containing an orphan surrogate to a String
	// currently works, but converting a String to a Buffer (via `new Buffer`, or
	// Buffer#write) will replace incomplete surrogates with the unicode
	// replacement character. See https://codereview.chromium.org/121173009/ .
	StringDecoder.prototype.write = function(buffer) {
	  var charStr = '';
	  // if our last write ended with an incomplete multibyte character
	  while (this.charLength) {
	    // determine how many remaining bytes this buffer has to offer for this char
	    var available = (buffer.length >= this.charLength - this.charReceived) ?
	        this.charLength - this.charReceived :
	        buffer.length;
	
	    // add the new bytes to the char buffer
	    buffer.copy(this.charBuffer, this.charReceived, 0, available);
	    this.charReceived += available;
	
	    if (this.charReceived < this.charLength) {
	      // still not enough chars in this buffer? wait for more ...
	      return '';
	    }
	
	    // remove bytes belonging to the current character from the buffer
	    buffer = buffer.slice(available, buffer.length);
	
	    // get the character that was split
	    charStr = this.charBuffer.slice(0, this.charLength).toString(this.encoding);
	
	    // CESU-8: lead surrogate (D800-DBFF) is also the incomplete character
	    var charCode = charStr.charCodeAt(charStr.length - 1);
	    if (charCode >= 0xD800 && charCode <= 0xDBFF) {
	      this.charLength += this.surrogateSize;
	      charStr = '';
	      continue;
	    }
	    this.charReceived = this.charLength = 0;
	
	    // if there are no more bytes in this buffer, just emit our char
	    if (buffer.length === 0) {
	      return charStr;
	    }
	    break;
	  }
	
	  // determine and set charLength / charReceived
	  this.detectIncompleteChar(buffer);
	
	  var end = buffer.length;
	  if (this.charLength) {
	    // buffer the incomplete character bytes we got
	    buffer.copy(this.charBuffer, 0, buffer.length - this.charReceived, end);
	    end -= this.charReceived;
	  }
	
	  charStr += buffer.toString(this.encoding, 0, end);
	
	  var end = charStr.length - 1;
	  var charCode = charStr.charCodeAt(end);
	  // CESU-8: lead surrogate (D800-DBFF) is also the incomplete character
	  if (charCode >= 0xD800 && charCode <= 0xDBFF) {
	    var size = this.surrogateSize;
	    this.charLength += size;
	    this.charReceived += size;
	    this.charBuffer.copy(this.charBuffer, size, 0, size);
	    buffer.copy(this.charBuffer, 0, 0, size);
	    return charStr.substring(0, end);
	  }
	
	  // or just emit the charStr
	  return charStr;
	};
	
	// detectIncompleteChar determines if there is an incomplete UTF-8 character at
	// the end of the given buffer. If so, it sets this.charLength to the byte
	// length that character, and sets this.charReceived to the number of bytes
	// that are available for this character.
	StringDecoder.prototype.detectIncompleteChar = function(buffer) {
	  // determine how many bytes we have to check at the end of this buffer
	  var i = (buffer.length >= 3) ? 3 : buffer.length;
	
	  // Figure out if one of the last i bytes of our buffer announces an
	  // incomplete char.
	  for (; i > 0; i--) {
	    var c = buffer[buffer.length - i];
	
	    // See http://en.wikipedia.org/wiki/UTF-8#Description
	
	    // 110XXXXX
	    if (i == 1 && c >> 5 == 0x06) {
	      this.charLength = 2;
	      break;
	    }
	
	    // 1110XXXX
	    if (i <= 2 && c >> 4 == 0x0E) {
	      this.charLength = 3;
	      break;
	    }
	
	    // 11110XXX
	    if (i <= 3 && c >> 3 == 0x1E) {
	      this.charLength = 4;
	      break;
	    }
	  }
	  this.charReceived = i;
	};
	
	StringDecoder.prototype.end = function(buffer) {
	  var res = '';
	  if (buffer && buffer.length)
	    res = this.write(buffer);
	
	  if (this.charReceived) {
	    var cr = this.charReceived;
	    var buf = this.charBuffer;
	    var enc = this.encoding;
	    res += buf.slice(0, cr).toString(enc);
	  }
	
	  return res;
	};
	
	function passThroughWrite(buffer) {
	  return buffer.toString(this.encoding);
	}
	
	function utf16DetectIncompleteChar(buffer) {
	  this.charReceived = buffer.length % 2;
	  this.charLength = this.charReceived ? 2 : 0;
	}
	
	function base64DetectIncompleteChar(buffer) {
	  this.charReceived = buffer.length % 3;
	  this.charLength = this.charReceived ? 3 : 0;
	}


/***/ },
/* 84 */
/*!********************************************************************************!*\
  !*** (webpack)/~/stream-browserify/~/readable-stream/lib/_stream_transform.js ***!
  \********************************************************************************/
/***/ function(module, exports, __webpack_require__) {

	// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.
	
	
	// a transform stream is a readable/writable stream where you do
	// something with the data.  Sometimes it's called a "filter",
	// but that's not a great name for it, since that implies a thing where
	// some bits pass through, and others are simply ignored.  (That would
	// be a valid example of a transform, of course.)
	//
	// While the output is causally related to the input, it's not a
	// necessarily symmetric or synchronous transformation.  For example,
	// a zlib stream might take multiple plain-text writes(), and then
	// emit a single compressed chunk some time in the future.
	//
	// Here's how this works:
	//
	// The Transform stream has all the aspects of the readable and writable
	// stream classes.  When you write(chunk), that calls _write(chunk,cb)
	// internally, and returns false if there's a lot of pending writes
	// buffered up.  When you call read(), that calls _read(n) until
	// there's enough pending readable data buffered up.
	//
	// In a transform stream, the written data is placed in a buffer.  When
	// _read(n) is called, it transforms the queued up data, calling the
	// buffered _write cb's as it consumes chunks.  If consuming a single
	// written chunk would result in multiple output chunks, then the first
	// outputted bit calls the readcb, and subsequent chunks just go into
	// the read buffer, and will cause it to emit 'readable' if necessary.
	//
	// This way, back-pressure is actually determined by the reading side,
	// since _read has to be called to start processing a new chunk.  However,
	// a pathological inflate type of transform can cause excessive buffering
	// here.  For example, imagine a stream where every byte of input is
	// interpreted as an integer from 0-255, and then results in that many
	// bytes of output.  Writing the 4 bytes {ff,ff,ff,ff} would result in
	// 1kb of data being output.  In this case, you could write a very small
	// amount of input, and end up with a very large amount of output.  In
	// such a pathological inflating mechanism, there'd be no way to tell
	// the system to stop doing the transform.  A single 4MB write could
	// cause the system to run out of memory.
	//
	// However, even in such a pathological case, only a single written chunk
	// would be consumed, and then the rest would wait (un-transformed) until
	// the results of the previous transformed chunk were consumed.
	
	module.exports = Transform;
	
	var Duplex = __webpack_require__(/*! ./_stream_duplex */ 81);
	
	/*<replacement>*/
	var util = __webpack_require__(/*! core-util-is */ 79);
	util.inherits = __webpack_require__(/*! inherits */ 72);
	/*</replacement>*/
	
	util.inherits(Transform, Duplex);
	
	
	function TransformState(options, stream) {
	  this.afterTransform = function(er, data) {
	    return afterTransform(stream, er, data);
	  };
	
	  this.needTransform = false;
	  this.transforming = false;
	  this.writecb = null;
	  this.writechunk = null;
	}
	
	function afterTransform(stream, er, data) {
	  var ts = stream._transformState;
	  ts.transforming = false;
	
	  var cb = ts.writecb;
	
	  if (!cb)
	    return stream.emit('error', new Error('no writecb in Transform class'));
	
	  ts.writechunk = null;
	  ts.writecb = null;
	
	  if (!util.isNullOrUndefined(data))
	    stream.push(data);
	
	  if (cb)
	    cb(er);
	
	  var rs = stream._readableState;
	  rs.reading = false;
	  if (rs.needReadable || rs.length < rs.highWaterMark) {
	    stream._read(rs.highWaterMark);
	  }
	}
	
	
	function Transform(options) {
	  if (!(this instanceof Transform))
	    return new Transform(options);
	
	  Duplex.call(this, options);
	
	  this._transformState = new TransformState(options, this);
	
	  // when the writable side finishes, then flush out anything remaining.
	  var stream = this;
	
	  // start out asking for a readable event once data is transformed.
	  this._readableState.needReadable = true;
	
	  // we have implemented the _read method, and done the other things
	  // that Readable wants before the first _read call, so unset the
	  // sync guard flag.
	  this._readableState.sync = false;
	
	  this.once('prefinish', function() {
	    if (util.isFunction(this._flush))
	      this._flush(function(er) {
	        done(stream, er);
	      });
	    else
	      done(stream);
	  });
	}
	
	Transform.prototype.push = function(chunk, encoding) {
	  this._transformState.needTransform = false;
	  return Duplex.prototype.push.call(this, chunk, encoding);
	};
	
	// This is the part where you do stuff!
	// override this function in implementation classes.
	// 'chunk' is an input chunk.
	//
	// Call `push(newChunk)` to pass along transformed output
	// to the readable side.  You may call 'push' zero or more times.
	//
	// Call `cb(err)` when you are done with this chunk.  If you pass
	// an error, then that'll put the hurt on the whole operation.  If you
	// never call cb(), then you'll never get another chunk.
	Transform.prototype._transform = function(chunk, encoding, cb) {
	  throw new Error('not implemented');
	};
	
	Transform.prototype._write = function(chunk, encoding, cb) {
	  var ts = this._transformState;
	  ts.writecb = cb;
	  ts.writechunk = chunk;
	  ts.writeencoding = encoding;
	  if (!ts.transforming) {
	    var rs = this._readableState;
	    if (ts.needTransform ||
	        rs.needReadable ||
	        rs.length < rs.highWaterMark)
	      this._read(rs.highWaterMark);
	  }
	};
	
	// Doesn't matter what the args are here.
	// _transform does all the work.
	// That we got here means that the readable side wants more data.
	Transform.prototype._read = function(n) {
	  var ts = this._transformState;
	
	  if (!util.isNull(ts.writechunk) && ts.writecb && !ts.transforming) {
	    ts.transforming = true;
	    this._transform(ts.writechunk, ts.writeencoding, ts.afterTransform);
	  } else {
	    // mark that we need a transform, so that any data that comes in
	    // will get processed, now that we've asked for it.
	    ts.needTransform = true;
	  }
	};
	
	
	function done(stream, er) {
	  if (er)
	    return stream.emit('error', er);
	
	  // if there's nothing in the write buffer, then that means
	  // that nothing more will ever be provided
	  var ws = stream._writableState;
	  var ts = stream._transformState;
	
	  if (ws.length)
	    throw new Error('calling transform done when ws.length != 0');
	
	  if (ts.transforming)
	    throw new Error('calling transform done when still transforming');
	
	  return stream.push(null);
	}


/***/ },
/* 85 */
/*!**********************************************************************************!*\
  !*** (webpack)/~/stream-browserify/~/readable-stream/lib/_stream_passthrough.js ***!
  \**********************************************************************************/
/***/ function(module, exports, __webpack_require__) {

	// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.
	
	// a passthrough stream.
	// basically just the most minimal sort of Transform stream.
	// Every written chunk gets output as-is.
	
	module.exports = PassThrough;
	
	var Transform = __webpack_require__(/*! ./_stream_transform */ 84);
	
	/*<replacement>*/
	var util = __webpack_require__(/*! core-util-is */ 79);
	util.inherits = __webpack_require__(/*! inherits */ 72);
	/*</replacement>*/
	
	util.inherits(PassThrough, Transform);
	
	function PassThrough(options) {
	  if (!(this instanceof PassThrough))
	    return new PassThrough(options);
	
	  Transform.call(this, options);
	}
	
	PassThrough.prototype._transform = function(chunk, encoding, cb) {
	  cb(null, chunk);
	};


/***/ },
/* 86 */
/*!*******************************************************************!*\
  !*** (webpack)/~/stream-browserify/~/readable-stream/writable.js ***!
  \*******************************************************************/
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(/*! ./lib/_stream_writable.js */ 82)


/***/ },
/* 87 */
/*!*****************************************************************!*\
  !*** (webpack)/~/stream-browserify/~/readable-stream/duplex.js ***!
  \*****************************************************************/
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(/*! ./lib/_stream_duplex.js */ 81)


/***/ },
/* 88 */
/*!********************************************************************!*\
  !*** (webpack)/~/stream-browserify/~/readable-stream/transform.js ***!
  \********************************************************************/
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(/*! ./lib/_stream_transform.js */ 84)


/***/ },
/* 89 */
/*!**********************************************************************!*\
  !*** (webpack)/~/stream-browserify/~/readable-stream/passthrough.js ***!
  \**********************************************************************/
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(/*! ./lib/_stream_passthrough.js */ 85)


/***/ },
/* 90 */
/*!***************************************************!*\
  !*** (webpack)/~/http-browserify/lib/response.js ***!
  \***************************************************/
/***/ function(module, exports, __webpack_require__) {

	var Stream = __webpack_require__(/*! stream */ 71);
	var util = __webpack_require__(/*! util */ 91);
	
	var Response = module.exports = function (res) {
	    this.offset = 0;
	    this.readable = true;
	};
	
	util.inherits(Response, Stream);
	
	var capable = {
	    streaming : true,
	    status2 : true
	};
	
	function parseHeaders (res) {
	    var lines = res.getAllResponseHeaders().split(/\r?\n/);
	    var headers = {};
	    for (var i = 0; i < lines.length; i++) {
	        var line = lines[i];
	        if (line === '') continue;
	        
	        var m = line.match(/^([^:]+):\s*(.*)/);
	        if (m) {
	            var key = m[1].toLowerCase(), value = m[2];
	            
	            if (headers[key] !== undefined) {
	            
	                if (isArray(headers[key])) {
	                    headers[key].push(value);
	                }
	                else {
	                    headers[key] = [ headers[key], value ];
	                }
	            }
	            else {
	                headers[key] = value;
	            }
	        }
	        else {
	            headers[line] = true;
	        }
	    }
	    return headers;
	}
	
	Response.prototype.getResponse = function (xhr) {
	    var respType = String(xhr.responseType).toLowerCase();
	    if (respType === 'blob') return xhr.responseBlob || xhr.response;
	    if (respType === 'arraybuffer') return xhr.response;
	    return xhr.responseText;
	}
	
	Response.prototype.getHeader = function (key) {
	    return this.headers[key.toLowerCase()];
	};
	
	Response.prototype.handle = function (res) {
	    if (res.readyState === 2 && capable.status2) {
	        try {
	            this.statusCode = res.status;
	            this.headers = parseHeaders(res);
	        }
	        catch (err) {
	            capable.status2 = false;
	        }
	        
	        if (capable.status2) {
	            this.emit('ready');
	        }
	    }
	    else if (capable.streaming && res.readyState === 3) {
	        try {
	            if (!this.statusCode) {
	                this.statusCode = res.status;
	                this.headers = parseHeaders(res);
	                this.emit('ready');
	            }
	        }
	        catch (err) {}
	        
	        try {
	            this._emitData(res);
	        }
	        catch (err) {
	            capable.streaming = false;
	        }
	    }
	    else if (res.readyState === 4) {
	        if (!this.statusCode) {
	            this.statusCode = res.status;
	            this.emit('ready');
	        }
	        this._emitData(res);
	        
	        if (res.error) {
	            this.emit('error', this.getResponse(res));
	        }
	        else this.emit('end');
	        
	        this.emit('close');
	    }
	};
	
	Response.prototype._emitData = function (res) {
	    var respBody = this.getResponse(res);
	    if (respBody.toString().match(/ArrayBuffer/)) {
	        this.emit('data', new Uint8Array(respBody, this.offset));
	        this.offset = respBody.byteLength;
	        return;
	    }
	    if (respBody.length > this.offset) {
	        this.emit('data', respBody.slice(this.offset));
	        this.offset = respBody.length;
	    }
	};
	
	var isArray = Array.isArray || function (xs) {
	    return Object.prototype.toString.call(xs) === '[object Array]';
	};


/***/ },
/* 91 */
/*!********************************!*\
  !*** (webpack)/~/util/util.js ***!
  \********************************/
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global, process) {// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.
	
	var formatRegExp = /%[sdj%]/g;
	exports.format = function(f) {
	  if (!isString(f)) {
	    var objects = [];
	    for (var i = 0; i < arguments.length; i++) {
	      objects.push(inspect(arguments[i]));
	    }
	    return objects.join(' ');
	  }
	
	  var i = 1;
	  var args = arguments;
	  var len = args.length;
	  var str = String(f).replace(formatRegExp, function(x) {
	    if (x === '%%') return '%';
	    if (i >= len) return x;
	    switch (x) {
	      case '%s': return String(args[i++]);
	      case '%d': return Number(args[i++]);
	      case '%j':
	        try {
	          return JSON.stringify(args[i++]);
	        } catch (_) {
	          return '[Circular]';
	        }
	      default:
	        return x;
	    }
	  });
	  for (var x = args[i]; i < len; x = args[++i]) {
	    if (isNull(x) || !isObject(x)) {
	      str += ' ' + x;
	    } else {
	      str += ' ' + inspect(x);
	    }
	  }
	  return str;
	};
	
	
	// Mark that a method should not be used.
	// Returns a modified function which warns once by default.
	// If --no-deprecation is set, then it is a no-op.
	exports.deprecate = function(fn, msg) {
	  // Allow for deprecating things in the process of starting up.
	  if (isUndefined(global.process)) {
	    return function() {
	      return exports.deprecate(fn, msg).apply(this, arguments);
	    };
	  }
	
	  if (process.noDeprecation === true) {
	    return fn;
	  }
	
	  var warned = false;
	  function deprecated() {
	    if (!warned) {
	      if (process.throwDeprecation) {
	        throw new Error(msg);
	      } else if (process.traceDeprecation) {
	        console.trace(msg);
	      } else {
	        console.error(msg);
	      }
	      warned = true;
	    }
	    return fn.apply(this, arguments);
	  }
	
	  return deprecated;
	};
	
	
	var debugs = {};
	var debugEnviron;
	exports.debuglog = function(set) {
	  if (isUndefined(debugEnviron))
	    debugEnviron = ({"NODE_ENV":"development"}).NODE_DEBUG || '';
	  set = set.toUpperCase();
	  if (!debugs[set]) {
	    if (new RegExp('\\b' + set + '\\b', 'i').test(debugEnviron)) {
	      var pid = process.pid;
	      debugs[set] = function() {
	        var msg = exports.format.apply(exports, arguments);
	        console.error('%s %d: %s', set, pid, msg);
	      };
	    } else {
	      debugs[set] = function() {};
	    }
	  }
	  return debugs[set];
	};
	
	
	/**
	 * Echos the value of a value. Trys to print the value out
	 * in the best way possible given the different types.
	 *
	 * @param {Object} obj The object to print out.
	 * @param {Object} opts Optional options object that alters the output.
	 */
	/* legacy: obj, showHidden, depth, colors*/
	function inspect(obj, opts) {
	  // default options
	  var ctx = {
	    seen: [],
	    stylize: stylizeNoColor
	  };
	  // legacy...
	  if (arguments.length >= 3) ctx.depth = arguments[2];
	  if (arguments.length >= 4) ctx.colors = arguments[3];
	  if (isBoolean(opts)) {
	    // legacy...
	    ctx.showHidden = opts;
	  } else if (opts) {
	    // got an "options" object
	    exports._extend(ctx, opts);
	  }
	  // set default options
	  if (isUndefined(ctx.showHidden)) ctx.showHidden = false;
	  if (isUndefined(ctx.depth)) ctx.depth = 2;
	  if (isUndefined(ctx.colors)) ctx.colors = false;
	  if (isUndefined(ctx.customInspect)) ctx.customInspect = true;
	  if (ctx.colors) ctx.stylize = stylizeWithColor;
	  return formatValue(ctx, obj, ctx.depth);
	}
	exports.inspect = inspect;
	
	
	// http://en.wikipedia.org/wiki/ANSI_escape_code#graphics
	inspect.colors = {
	  'bold' : [1, 22],
	  'italic' : [3, 23],
	  'underline' : [4, 24],
	  'inverse' : [7, 27],
	  'white' : [37, 39],
	  'grey' : [90, 39],
	  'black' : [30, 39],
	  'blue' : [34, 39],
	  'cyan' : [36, 39],
	  'green' : [32, 39],
	  'magenta' : [35, 39],
	  'red' : [31, 39],
	  'yellow' : [33, 39]
	};
	
	// Don't use 'blue' not visible on cmd.exe
	inspect.styles = {
	  'special': 'cyan',
	  'number': 'yellow',
	  'boolean': 'yellow',
	  'undefined': 'grey',
	  'null': 'bold',
	  'string': 'green',
	  'date': 'magenta',
	  // "name": intentionally not styling
	  'regexp': 'red'
	};
	
	
	function stylizeWithColor(str, styleType) {
	  var style = inspect.styles[styleType];
	
	  if (style) {
	    return '\u001b[' + inspect.colors[style][0] + 'm' + str +
	           '\u001b[' + inspect.colors[style][1] + 'm';
	  } else {
	    return str;
	  }
	}
	
	
	function stylizeNoColor(str, styleType) {
	  return str;
	}
	
	
	function arrayToHash(array) {
	  var hash = {};
	
	  array.forEach(function(val, idx) {
	    hash[val] = true;
	  });
	
	  return hash;
	}
	
	
	function formatValue(ctx, value, recurseTimes) {
	  // Provide a hook for user-specified inspect functions.
	  // Check that value is an object with an inspect function on it
	  if (ctx.customInspect &&
	      value &&
	      isFunction(value.inspect) &&
	      // Filter out the util module, it's inspect function is special
	      value.inspect !== exports.inspect &&
	      // Also filter out any prototype objects using the circular check.
	      !(value.constructor && value.constructor.prototype === value)) {
	    var ret = value.inspect(recurseTimes, ctx);
	    if (!isString(ret)) {
	      ret = formatValue(ctx, ret, recurseTimes);
	    }
	    return ret;
	  }
	
	  // Primitive types cannot have properties
	  var primitive = formatPrimitive(ctx, value);
	  if (primitive) {
	    return primitive;
	  }
	
	  // Look up the keys of the object.
	  var keys = Object.keys(value);
	  var visibleKeys = arrayToHash(keys);
	
	  if (ctx.showHidden) {
	    keys = Object.getOwnPropertyNames(value);
	  }
	
	  // IE doesn't make error fields non-enumerable
	  // http://msdn.microsoft.com/en-us/library/ie/dww52sbt(v=vs.94).aspx
	  if (isError(value)
	      && (keys.indexOf('message') >= 0 || keys.indexOf('description') >= 0)) {
	    return formatError(value);
	  }
	
	  // Some type of object without properties can be shortcutted.
	  if (keys.length === 0) {
	    if (isFunction(value)) {
	      var name = value.name ? ': ' + value.name : '';
	      return ctx.stylize('[Function' + name + ']', 'special');
	    }
	    if (isRegExp(value)) {
	      return ctx.stylize(RegExp.prototype.toString.call(value), 'regexp');
	    }
	    if (isDate(value)) {
	      return ctx.stylize(Date.prototype.toString.call(value), 'date');
	    }
	    if (isError(value)) {
	      return formatError(value);
	    }
	  }
	
	  var base = '', array = false, braces = ['{', '}'];
	
	  // Make Array say that they are Array
	  if (isArray(value)) {
	    array = true;
	    braces = ['[', ']'];
	  }
	
	  // Make functions say that they are functions
	  if (isFunction(value)) {
	    var n = value.name ? ': ' + value.name : '';
	    base = ' [Function' + n + ']';
	  }
	
	  // Make RegExps say that they are RegExps
	  if (isRegExp(value)) {
	    base = ' ' + RegExp.prototype.toString.call(value);
	  }
	
	  // Make dates with properties first say the date
	  if (isDate(value)) {
	    base = ' ' + Date.prototype.toUTCString.call(value);
	  }
	
	  // Make error with message first say the error
	  if (isError(value)) {
	    base = ' ' + formatError(value);
	  }
	
	  if (keys.length === 0 && (!array || value.length == 0)) {
	    return braces[0] + base + braces[1];
	  }
	
	  if (recurseTimes < 0) {
	    if (isRegExp(value)) {
	      return ctx.stylize(RegExp.prototype.toString.call(value), 'regexp');
	    } else {
	      return ctx.stylize('[Object]', 'special');
	    }
	  }
	
	  ctx.seen.push(value);
	
	  var output;
	  if (array) {
	    output = formatArray(ctx, value, recurseTimes, visibleKeys, keys);
	  } else {
	    output = keys.map(function(key) {
	      return formatProperty(ctx, value, recurseTimes, visibleKeys, key, array);
	    });
	  }
	
	  ctx.seen.pop();
	
	  return reduceToSingleString(output, base, braces);
	}
	
	
	function formatPrimitive(ctx, value) {
	  if (isUndefined(value))
	    return ctx.stylize('undefined', 'undefined');
	  if (isString(value)) {
	    var simple = '\'' + JSON.stringify(value).replace(/^"|"$/g, '')
	                                             .replace(/'/g, "\\'")
	                                             .replace(/\\"/g, '"') + '\'';
	    return ctx.stylize(simple, 'string');
	  }
	  if (isNumber(value))
	    return ctx.stylize('' + value, 'number');
	  if (isBoolean(value))
	    return ctx.stylize('' + value, 'boolean');
	  // For some reason typeof null is "object", so special case here.
	  if (isNull(value))
	    return ctx.stylize('null', 'null');
	}
	
	
	function formatError(value) {
	  return '[' + Error.prototype.toString.call(value) + ']';
	}
	
	
	function formatArray(ctx, value, recurseTimes, visibleKeys, keys) {
	  var output = [];
	  for (var i = 0, l = value.length; i < l; ++i) {
	    if (hasOwnProperty(value, String(i))) {
	      output.push(formatProperty(ctx, value, recurseTimes, visibleKeys,
	          String(i), true));
	    } else {
	      output.push('');
	    }
	  }
	  keys.forEach(function(key) {
	    if (!key.match(/^\d+$/)) {
	      output.push(formatProperty(ctx, value, recurseTimes, visibleKeys,
	          key, true));
	    }
	  });
	  return output;
	}
	
	
	function formatProperty(ctx, value, recurseTimes, visibleKeys, key, array) {
	  var name, str, desc;
	  desc = Object.getOwnPropertyDescriptor(value, key) || { value: value[key] };
	  if (desc.get) {
	    if (desc.set) {
	      str = ctx.stylize('[Getter/Setter]', 'special');
	    } else {
	      str = ctx.stylize('[Getter]', 'special');
	    }
	  } else {
	    if (desc.set) {
	      str = ctx.stylize('[Setter]', 'special');
	    }
	  }
	  if (!hasOwnProperty(visibleKeys, key)) {
	    name = '[' + key + ']';
	  }
	  if (!str) {
	    if (ctx.seen.indexOf(desc.value) < 0) {
	      if (isNull(recurseTimes)) {
	        str = formatValue(ctx, desc.value, null);
	      } else {
	        str = formatValue(ctx, desc.value, recurseTimes - 1);
	      }
	      if (str.indexOf('\n') > -1) {
	        if (array) {
	          str = str.split('\n').map(function(line) {
	            return '  ' + line;
	          }).join('\n').substr(2);
	        } else {
	          str = '\n' + str.split('\n').map(function(line) {
	            return '   ' + line;
	          }).join('\n');
	        }
	      }
	    } else {
	      str = ctx.stylize('[Circular]', 'special');
	    }
	  }
	  if (isUndefined(name)) {
	    if (array && key.match(/^\d+$/)) {
	      return str;
	    }
	    name = JSON.stringify('' + key);
	    if (name.match(/^"([a-zA-Z_][a-zA-Z_0-9]*)"$/)) {
	      name = name.substr(1, name.length - 2);
	      name = ctx.stylize(name, 'name');
	    } else {
	      name = name.replace(/'/g, "\\'")
	                 .replace(/\\"/g, '"')
	                 .replace(/(^"|"$)/g, "'");
	      name = ctx.stylize(name, 'string');
	    }
	  }
	
	  return name + ': ' + str;
	}
	
	
	function reduceToSingleString(output, base, braces) {
	  var numLinesEst = 0;
	  var length = output.reduce(function(prev, cur) {
	    numLinesEst++;
	    if (cur.indexOf('\n') >= 0) numLinesEst++;
	    return prev + cur.replace(/\u001b\[\d\d?m/g, '').length + 1;
	  }, 0);
	
	  if (length > 60) {
	    return braces[0] +
	           (base === '' ? '' : base + '\n ') +
	           ' ' +
	           output.join(',\n  ') +
	           ' ' +
	           braces[1];
	  }
	
	  return braces[0] + base + ' ' + output.join(', ') + ' ' + braces[1];
	}
	
	
	// NOTE: These type checking functions intentionally don't use `instanceof`
	// because it is fragile and can be easily faked with `Object.create()`.
	function isArray(ar) {
	  return Array.isArray(ar);
	}
	exports.isArray = isArray;
	
	function isBoolean(arg) {
	  return typeof arg === 'boolean';
	}
	exports.isBoolean = isBoolean;
	
	function isNull(arg) {
	  return arg === null;
	}
	exports.isNull = isNull;
	
	function isNullOrUndefined(arg) {
	  return arg == null;
	}
	exports.isNullOrUndefined = isNullOrUndefined;
	
	function isNumber(arg) {
	  return typeof arg === 'number';
	}
	exports.isNumber = isNumber;
	
	function isString(arg) {
	  return typeof arg === 'string';
	}
	exports.isString = isString;
	
	function isSymbol(arg) {
	  return typeof arg === 'symbol';
	}
	exports.isSymbol = isSymbol;
	
	function isUndefined(arg) {
	  return arg === void 0;
	}
	exports.isUndefined = isUndefined;
	
	function isRegExp(re) {
	  return isObject(re) && objectToString(re) === '[object RegExp]';
	}
	exports.isRegExp = isRegExp;
	
	function isObject(arg) {
	  return typeof arg === 'object' && arg !== null;
	}
	exports.isObject = isObject;
	
	function isDate(d) {
	  return isObject(d) && objectToString(d) === '[object Date]';
	}
	exports.isDate = isDate;
	
	function isError(e) {
	  return isObject(e) &&
	      (objectToString(e) === '[object Error]' || e instanceof Error);
	}
	exports.isError = isError;
	
	function isFunction(arg) {
	  return typeof arg === 'function';
	}
	exports.isFunction = isFunction;
	
	function isPrimitive(arg) {
	  return arg === null ||
	         typeof arg === 'boolean' ||
	         typeof arg === 'number' ||
	         typeof arg === 'string' ||
	         typeof arg === 'symbol' ||  // ES6 symbol
	         typeof arg === 'undefined';
	}
	exports.isPrimitive = isPrimitive;
	
	exports.isBuffer = __webpack_require__(/*! ./support/isBuffer */ 92);
	
	function objectToString(o) {
	  return Object.prototype.toString.call(o);
	}
	
	
	function pad(n) {
	  return n < 10 ? '0' + n.toString(10) : n.toString(10);
	}
	
	
	var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep',
	              'Oct', 'Nov', 'Dec'];
	
	// 26 Feb 16:19:34
	function timestamp() {
	  var d = new Date();
	  var time = [pad(d.getHours()),
	              pad(d.getMinutes()),
	              pad(d.getSeconds())].join(':');
	  return [d.getDate(), months[d.getMonth()], time].join(' ');
	}
	
	
	// log is just a thin wrapper to console.log that prepends a timestamp
	exports.log = function() {
	  console.log('%s - %s', timestamp(), exports.format.apply(exports, arguments));
	};
	
	
	/**
	 * Inherit the prototype methods from one constructor into another.
	 *
	 * The Function.prototype.inherits from lang.js rewritten as a standalone
	 * function (not on Function.prototype). NOTE: If this file is to be loaded
	 * during bootstrapping this function needs to be rewritten using some native
	 * functions as prototype setup using normal JavaScript does not work as
	 * expected during bootstrapping (see mirror.js in r114903).
	 *
	 * @param {function} ctor Constructor function which needs to inherit the
	 *     prototype.
	 * @param {function} superCtor Constructor function to inherit prototype from.
	 */
	exports.inherits = __webpack_require__(/*! inherits */ 72);
	
	exports._extend = function(origin, add) {
	  // Don't do anything if add isn't an object
	  if (!add || !isObject(add)) return origin;
	
	  var keys = Object.keys(add);
	  var i = keys.length;
	  while (i--) {
	    origin[keys[i]] = add[keys[i]];
	  }
	  return origin;
	};
	
	function hasOwnProperty(obj, prop) {
	  return Object.prototype.hasOwnProperty.call(obj, prop);
	}
	
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }()), __webpack_require__(/*! (webpack)/~/process/browser.js */ 30)))

/***/ },
/* 92 */
/*!***************************************************!*\
  !*** (webpack)/~/util/support/isBufferBrowser.js ***!
  \***************************************************/
/***/ function(module, exports) {

	module.exports = function isBuffer(arg) {
	  return arg && typeof arg === 'object'
	    && typeof arg.copy === 'function'
	    && typeof arg.fill === 'function'
	    && typeof arg.readUInt8 === 'function';
	}

/***/ },
/* 93 */
/*!************************************!*\
  !*** (webpack)/~/Base64/base64.js ***!
  \************************************/
/***/ function(module, exports, __webpack_require__) {

	;(function () {
	
	  var object =  true ? exports : this; // #8: web workers
	  var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
	
	  function InvalidCharacterError(message) {
	    this.message = message;
	  }
	  InvalidCharacterError.prototype = new Error;
	  InvalidCharacterError.prototype.name = 'InvalidCharacterError';
	
	  // encoder
	  // [https://gist.github.com/999166] by [https://github.com/nignag]
	  object.btoa || (
	  object.btoa = function (input) {
	    for (
	      // initialize result and counter
	      var block, charCode, idx = 0, map = chars, output = '';
	      // if the next input index does not exist:
	      //   change the mapping table to "="
	      //   check if d has no fractional digits
	      input.charAt(idx | 0) || (map = '=', idx % 1);
	      // "8 - idx % 1 * 8" generates the sequence 2, 4, 6, 8
	      output += map.charAt(63 & block >> 8 - idx % 1 * 8)
	    ) {
	      charCode = input.charCodeAt(idx += 3/4);
	      if (charCode > 0xFF) {
	        throw new InvalidCharacterError("'btoa' failed: The string to be encoded contains characters outside of the Latin1 range.");
	      }
	      block = block << 8 | charCode;
	    }
	    return output;
	  });
	
	  // decoder
	  // [https://gist.github.com/1020396] by [https://github.com/atk]
	  object.atob || (
	  object.atob = function (input) {
	    input = input.replace(/=+$/, '');
	    if (input.length % 4 == 1) {
	      throw new InvalidCharacterError("'atob' failed: The string to be decoded is not correctly encoded.");
	    }
	    for (
	      // initialize result and counters
	      var bc = 0, bs, buffer, idx = 0, output = '';
	      // get next character
	      buffer = input.charAt(idx++);
	      // character found in table? initialize bit storage and add its ascii value;
	      ~buffer && (bs = bc % 4 ? bs * 64 + buffer : buffer,
	        // and if not first of each 4 characters,
	        // convert the first 8 bits to one ascii character
	        bc++ % 4) ? output += String.fromCharCode(255 & bs >> (-2 * bc & 6)) : 0
	    ) {
	      // try to find character in table (0-63, not found => -1)
	      buffer = chars.indexOf(buffer);
	    }
	    return output;
	  });
	
	}());


/***/ },
/* 94 */
/*!******************************!*\
  !*** (webpack)/~/url/url.js ***!
  \******************************/
/***/ function(module, exports, __webpack_require__) {

	// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.
	
	var punycode = __webpack_require__(/*! punycode */ 95);
	
	exports.parse = urlParse;
	exports.resolve = urlResolve;
	exports.resolveObject = urlResolveObject;
	exports.format = urlFormat;
	
	exports.Url = Url;
	
	function Url() {
	  this.protocol = null;
	  this.slashes = null;
	  this.auth = null;
	  this.host = null;
	  this.port = null;
	  this.hostname = null;
	  this.hash = null;
	  this.search = null;
	  this.query = null;
	  this.pathname = null;
	  this.path = null;
	  this.href = null;
	}
	
	// Reference: RFC 3986, RFC 1808, RFC 2396
	
	// define these here so at least they only have to be
	// compiled once on the first module load.
	var protocolPattern = /^([a-z0-9.+-]+:)/i,
	    portPattern = /:[0-9]*$/,
	
	    // RFC 2396: characters reserved for delimiting URLs.
	    // We actually just auto-escape these.
	    delims = ['<', '>', '"', '`', ' ', '\r', '\n', '\t'],
	
	    // RFC 2396: characters not allowed for various reasons.
	    unwise = ['{', '}', '|', '\\', '^', '`'].concat(delims),
	
	    // Allowed by RFCs, but cause of XSS attacks.  Always escape these.
	    autoEscape = ['\''].concat(unwise),
	    // Characters that are never ever allowed in a hostname.
	    // Note that any invalid chars are also handled, but these
	    // are the ones that are *expected* to be seen, so we fast-path
	    // them.
	    nonHostChars = ['%', '/', '?', ';', '#'].concat(autoEscape),
	    hostEndingChars = ['/', '?', '#'],
	    hostnameMaxLen = 255,
	    hostnamePartPattern = /^[a-z0-9A-Z_-]{0,63}$/,
	    hostnamePartStart = /^([a-z0-9A-Z_-]{0,63})(.*)$/,
	    // protocols that can allow "unsafe" and "unwise" chars.
	    unsafeProtocol = {
	      'javascript': true,
	      'javascript:': true
	    },
	    // protocols that never have a hostname.
	    hostlessProtocol = {
	      'javascript': true,
	      'javascript:': true
	    },
	    // protocols that always contain a // bit.
	    slashedProtocol = {
	      'http': true,
	      'https': true,
	      'ftp': true,
	      'gopher': true,
	      'file': true,
	      'http:': true,
	      'https:': true,
	      'ftp:': true,
	      'gopher:': true,
	      'file:': true
	    },
	    querystring = __webpack_require__(/*! querystring */ 97);
	
	function urlParse(url, parseQueryString, slashesDenoteHost) {
	  if (url && isObject(url) && url instanceof Url) return url;
	
	  var u = new Url;
	  u.parse(url, parseQueryString, slashesDenoteHost);
	  return u;
	}
	
	Url.prototype.parse = function(url, parseQueryString, slashesDenoteHost) {
	  if (!isString(url)) {
	    throw new TypeError("Parameter 'url' must be a string, not " + typeof url);
	  }
	
	  var rest = url;
	
	  // trim before proceeding.
	  // This is to support parse stuff like "  http://foo.com  \n"
	  rest = rest.trim();
	
	  var proto = protocolPattern.exec(rest);
	  if (proto) {
	    proto = proto[0];
	    var lowerProto = proto.toLowerCase();
	    this.protocol = lowerProto;
	    rest = rest.substr(proto.length);
	  }
	
	  // figure out if it's got a host
	  // user@server is *always* interpreted as a hostname, and url
	  // resolution will treat //foo/bar as host=foo,path=bar because that's
	  // how the browser resolves relative URLs.
	  if (slashesDenoteHost || proto || rest.match(/^\/\/[^@\/]+@[^@\/]+/)) {
	    var slashes = rest.substr(0, 2) === '//';
	    if (slashes && !(proto && hostlessProtocol[proto])) {
	      rest = rest.substr(2);
	      this.slashes = true;
	    }
	  }
	
	  if (!hostlessProtocol[proto] &&
	      (slashes || (proto && !slashedProtocol[proto]))) {
	
	    // there's a hostname.
	    // the first instance of /, ?, ;, or # ends the host.
	    //
	    // If there is an @ in the hostname, then non-host chars *are* allowed
	    // to the left of the last @ sign, unless some host-ending character
	    // comes *before* the @-sign.
	    // URLs are obnoxious.
	    //
	    // ex:
	    // http://a@b@c/ => user:a@b host:c
	    // http://a@b?@c => user:a host:c path:/?@c
	
	    // v0.12 TODO(isaacs): This is not quite how Chrome does things.
	    // Review our test case against browsers more comprehensively.
	
	    // find the first instance of any hostEndingChars
	    var hostEnd = -1;
	    for (var i = 0; i < hostEndingChars.length; i++) {
	      var hec = rest.indexOf(hostEndingChars[i]);
	      if (hec !== -1 && (hostEnd === -1 || hec < hostEnd))
	        hostEnd = hec;
	    }
	
	    // at this point, either we have an explicit point where the
	    // auth portion cannot go past, or the last @ char is the decider.
	    var auth, atSign;
	    if (hostEnd === -1) {
	      // atSign can be anywhere.
	      atSign = rest.lastIndexOf('@');
	    } else {
	      // atSign must be in auth portion.
	      // http://a@b/c@d => host:b auth:a path:/c@d
	      atSign = rest.lastIndexOf('@', hostEnd);
	    }
	
	    // Now we have a portion which is definitely the auth.
	    // Pull that off.
	    if (atSign !== -1) {
	      auth = rest.slice(0, atSign);
	      rest = rest.slice(atSign + 1);
	      this.auth = decodeURIComponent(auth);
	    }
	
	    // the host is the remaining to the left of the first non-host char
	    hostEnd = -1;
	    for (var i = 0; i < nonHostChars.length; i++) {
	      var hec = rest.indexOf(nonHostChars[i]);
	      if (hec !== -1 && (hostEnd === -1 || hec < hostEnd))
	        hostEnd = hec;
	    }
	    // if we still have not hit it, then the entire thing is a host.
	    if (hostEnd === -1)
	      hostEnd = rest.length;
	
	    this.host = rest.slice(0, hostEnd);
	    rest = rest.slice(hostEnd);
	
	    // pull out port.
	    this.parseHost();
	
	    // we've indicated that there is a hostname,
	    // so even if it's empty, it has to be present.
	    this.hostname = this.hostname || '';
	
	    // if hostname begins with [ and ends with ]
	    // assume that it's an IPv6 address.
	    var ipv6Hostname = this.hostname[0] === '[' &&
	        this.hostname[this.hostname.length - 1] === ']';
	
	    // validate a little.
	    if (!ipv6Hostname) {
	      var hostparts = this.hostname.split(/\./);
	      for (var i = 0, l = hostparts.length; i < l; i++) {
	        var part = hostparts[i];
	        if (!part) continue;
	        if (!part.match(hostnamePartPattern)) {
	          var newpart = '';
	          for (var j = 0, k = part.length; j < k; j++) {
	            if (part.charCodeAt(j) > 127) {
	              // we replace non-ASCII char with a temporary placeholder
	              // we need this to make sure size of hostname is not
	              // broken by replacing non-ASCII by nothing
	              newpart += 'x';
	            } else {
	              newpart += part[j];
	            }
	          }
	          // we test again with ASCII char only
	          if (!newpart.match(hostnamePartPattern)) {
	            var validParts = hostparts.slice(0, i);
	            var notHost = hostparts.slice(i + 1);
	            var bit = part.match(hostnamePartStart);
	            if (bit) {
	              validParts.push(bit[1]);
	              notHost.unshift(bit[2]);
	            }
	            if (notHost.length) {
	              rest = '/' + notHost.join('.') + rest;
	            }
	            this.hostname = validParts.join('.');
	            break;
	          }
	        }
	      }
	    }
	
	    if (this.hostname.length > hostnameMaxLen) {
	      this.hostname = '';
	    } else {
	      // hostnames are always lower case.
	      this.hostname = this.hostname.toLowerCase();
	    }
	
	    if (!ipv6Hostname) {
	      // IDNA Support: Returns a puny coded representation of "domain".
	      // It only converts the part of the domain name that
	      // has non ASCII characters. I.e. it dosent matter if
	      // you call it with a domain that already is in ASCII.
	      var domainArray = this.hostname.split('.');
	      var newOut = [];
	      for (var i = 0; i < domainArray.length; ++i) {
	        var s = domainArray[i];
	        newOut.push(s.match(/[^A-Za-z0-9_-]/) ?
	            'xn--' + punycode.encode(s) : s);
	      }
	      this.hostname = newOut.join('.');
	    }
	
	    var p = this.port ? ':' + this.port : '';
	    var h = this.hostname || '';
	    this.host = h + p;
	    this.href += this.host;
	
	    // strip [ and ] from the hostname
	    // the host field still retains them, though
	    if (ipv6Hostname) {
	      this.hostname = this.hostname.substr(1, this.hostname.length - 2);
	      if (rest[0] !== '/') {
	        rest = '/' + rest;
	      }
	    }
	  }
	
	  // now rest is set to the post-host stuff.
	  // chop off any delim chars.
	  if (!unsafeProtocol[lowerProto]) {
	
	    // First, make 100% sure that any "autoEscape" chars get
	    // escaped, even if encodeURIComponent doesn't think they
	    // need to be.
	    for (var i = 0, l = autoEscape.length; i < l; i++) {
	      var ae = autoEscape[i];
	      var esc = encodeURIComponent(ae);
	      if (esc === ae) {
	        esc = escape(ae);
	      }
	      rest = rest.split(ae).join(esc);
	    }
	  }
	
	
	  // chop off from the tail first.
	  var hash = rest.indexOf('#');
	  if (hash !== -1) {
	    // got a fragment string.
	    this.hash = rest.substr(hash);
	    rest = rest.slice(0, hash);
	  }
	  var qm = rest.indexOf('?');
	  if (qm !== -1) {
	    this.search = rest.substr(qm);
	    this.query = rest.substr(qm + 1);
	    if (parseQueryString) {
	      this.query = querystring.parse(this.query);
	    }
	    rest = rest.slice(0, qm);
	  } else if (parseQueryString) {
	    // no query string, but parseQueryString still requested
	    this.search = '';
	    this.query = {};
	  }
	  if (rest) this.pathname = rest;
	  if (slashedProtocol[lowerProto] &&
	      this.hostname && !this.pathname) {
	    this.pathname = '/';
	  }
	
	  //to support http.request
	  if (this.pathname || this.search) {
	    var p = this.pathname || '';
	    var s = this.search || '';
	    this.path = p + s;
	  }
	
	  // finally, reconstruct the href based on what has been validated.
	  this.href = this.format();
	  return this;
	};
	
	// format a parsed object into a url string
	function urlFormat(obj) {
	  // ensure it's an object, and not a string url.
	  // If it's an obj, this is a no-op.
	  // this way, you can call url_format() on strings
	  // to clean up potentially wonky urls.
	  if (isString(obj)) obj = urlParse(obj);
	  if (!(obj instanceof Url)) return Url.prototype.format.call(obj);
	  return obj.format();
	}
	
	Url.prototype.format = function() {
	  var auth = this.auth || '';
	  if (auth) {
	    auth = encodeURIComponent(auth);
	    auth = auth.replace(/%3A/i, ':');
	    auth += '@';
	  }
	
	  var protocol = this.protocol || '',
	      pathname = this.pathname || '',
	      hash = this.hash || '',
	      host = false,
	      query = '';
	
	  if (this.host) {
	    host = auth + this.host;
	  } else if (this.hostname) {
	    host = auth + (this.hostname.indexOf(':') === -1 ?
	        this.hostname :
	        '[' + this.hostname + ']');
	    if (this.port) {
	      host += ':' + this.port;
	    }
	  }
	
	  if (this.query &&
	      isObject(this.query) &&
	      Object.keys(this.query).length) {
	    query = querystring.stringify(this.query);
	  }
	
	  var search = this.search || (query && ('?' + query)) || '';
	
	  if (protocol && protocol.substr(-1) !== ':') protocol += ':';
	
	  // only the slashedProtocols get the //.  Not mailto:, xmpp:, etc.
	  // unless they had them to begin with.
	  if (this.slashes ||
	      (!protocol || slashedProtocol[protocol]) && host !== false) {
	    host = '//' + (host || '');
	    if (pathname && pathname.charAt(0) !== '/') pathname = '/' + pathname;
	  } else if (!host) {
	    host = '';
	  }
	
	  if (hash && hash.charAt(0) !== '#') hash = '#' + hash;
	  if (search && search.charAt(0) !== '?') search = '?' + search;
	
	  pathname = pathname.replace(/[?#]/g, function(match) {
	    return encodeURIComponent(match);
	  });
	  search = search.replace('#', '%23');
	
	  return protocol + host + pathname + search + hash;
	};
	
	function urlResolve(source, relative) {
	  return urlParse(source, false, true).resolve(relative);
	}
	
	Url.prototype.resolve = function(relative) {
	  return this.resolveObject(urlParse(relative, false, true)).format();
	};
	
	function urlResolveObject(source, relative) {
	  if (!source) return relative;
	  return urlParse(source, false, true).resolveObject(relative);
	}
	
	Url.prototype.resolveObject = function(relative) {
	  if (isString(relative)) {
	    var rel = new Url();
	    rel.parse(relative, false, true);
	    relative = rel;
	  }
	
	  var result = new Url();
	  Object.keys(this).forEach(function(k) {
	    result[k] = this[k];
	  }, this);
	
	  // hash is always overridden, no matter what.
	  // even href="" will remove it.
	  result.hash = relative.hash;
	
	  // if the relative url is empty, then there's nothing left to do here.
	  if (relative.href === '') {
	    result.href = result.format();
	    return result;
	  }
	
	  // hrefs like //foo/bar always cut to the protocol.
	  if (relative.slashes && !relative.protocol) {
	    // take everything except the protocol from relative
	    Object.keys(relative).forEach(function(k) {
	      if (k !== 'protocol')
	        result[k] = relative[k];
	    });
	
	    //urlParse appends trailing / to urls like http://www.example.com
	    if (slashedProtocol[result.protocol] &&
	        result.hostname && !result.pathname) {
	      result.path = result.pathname = '/';
	    }
	
	    result.href = result.format();
	    return result;
	  }
	
	  if (relative.protocol && relative.protocol !== result.protocol) {
	    // if it's a known url protocol, then changing
	    // the protocol does weird things
	    // first, if it's not file:, then we MUST have a host,
	    // and if there was a path
	    // to begin with, then we MUST have a path.
	    // if it is file:, then the host is dropped,
	    // because that's known to be hostless.
	    // anything else is assumed to be absolute.
	    if (!slashedProtocol[relative.protocol]) {
	      Object.keys(relative).forEach(function(k) {
	        result[k] = relative[k];
	      });
	      result.href = result.format();
	      return result;
	    }
	
	    result.protocol = relative.protocol;
	    if (!relative.host && !hostlessProtocol[relative.protocol]) {
	      var relPath = (relative.pathname || '').split('/');
	      while (relPath.length && !(relative.host = relPath.shift()));
	      if (!relative.host) relative.host = '';
	      if (!relative.hostname) relative.hostname = '';
	      if (relPath[0] !== '') relPath.unshift('');
	      if (relPath.length < 2) relPath.unshift('');
	      result.pathname = relPath.join('/');
	    } else {
	      result.pathname = relative.pathname;
	    }
	    result.search = relative.search;
	    result.query = relative.query;
	    result.host = relative.host || '';
	    result.auth = relative.auth;
	    result.hostname = relative.hostname || relative.host;
	    result.port = relative.port;
	    // to support http.request
	    if (result.pathname || result.search) {
	      var p = result.pathname || '';
	      var s = result.search || '';
	      result.path = p + s;
	    }
	    result.slashes = result.slashes || relative.slashes;
	    result.href = result.format();
	    return result;
	  }
	
	  var isSourceAbs = (result.pathname && result.pathname.charAt(0) === '/'),
	      isRelAbs = (
	          relative.host ||
	          relative.pathname && relative.pathname.charAt(0) === '/'
	      ),
	      mustEndAbs = (isRelAbs || isSourceAbs ||
	                    (result.host && relative.pathname)),
	      removeAllDots = mustEndAbs,
	      srcPath = result.pathname && result.pathname.split('/') || [],
	      relPath = relative.pathname && relative.pathname.split('/') || [],
	      psychotic = result.protocol && !slashedProtocol[result.protocol];
	
	  // if the url is a non-slashed url, then relative
	  // links like ../.. should be able
	  // to crawl up to the hostname, as well.  This is strange.
	  // result.protocol has already been set by now.
	  // Later on, put the first path part into the host field.
	  if (psychotic) {
	    result.hostname = '';
	    result.port = null;
	    if (result.host) {
	      if (srcPath[0] === '') srcPath[0] = result.host;
	      else srcPath.unshift(result.host);
	    }
	    result.host = '';
	    if (relative.protocol) {
	      relative.hostname = null;
	      relative.port = null;
	      if (relative.host) {
	        if (relPath[0] === '') relPath[0] = relative.host;
	        else relPath.unshift(relative.host);
	      }
	      relative.host = null;
	    }
	    mustEndAbs = mustEndAbs && (relPath[0] === '' || srcPath[0] === '');
	  }
	
	  if (isRelAbs) {
	    // it's absolute.
	    result.host = (relative.host || relative.host === '') ?
	                  relative.host : result.host;
	    result.hostname = (relative.hostname || relative.hostname === '') ?
	                      relative.hostname : result.hostname;
	    result.search = relative.search;
	    result.query = relative.query;
	    srcPath = relPath;
	    // fall through to the dot-handling below.
	  } else if (relPath.length) {
	    // it's relative
	    // throw away the existing file, and take the new path instead.
	    if (!srcPath) srcPath = [];
	    srcPath.pop();
	    srcPath = srcPath.concat(relPath);
	    result.search = relative.search;
	    result.query = relative.query;
	  } else if (!isNullOrUndefined(relative.search)) {
	    // just pull out the search.
	    // like href='?foo'.
	    // Put this after the other two cases because it simplifies the booleans
	    if (psychotic) {
	      result.hostname = result.host = srcPath.shift();
	      //occationaly the auth can get stuck only in host
	      //this especialy happens in cases like
	      //url.resolveObject('mailto:local1@domain1', 'local2@domain2')
	      var authInHost = result.host && result.host.indexOf('@') > 0 ?
	                       result.host.split('@') : false;
	      if (authInHost) {
	        result.auth = authInHost.shift();
	        result.host = result.hostname = authInHost.shift();
	      }
	    }
	    result.search = relative.search;
	    result.query = relative.query;
	    //to support http.request
	    if (!isNull(result.pathname) || !isNull(result.search)) {
	      result.path = (result.pathname ? result.pathname : '') +
	                    (result.search ? result.search : '');
	    }
	    result.href = result.format();
	    return result;
	  }
	
	  if (!srcPath.length) {
	    // no path at all.  easy.
	    // we've already handled the other stuff above.
	    result.pathname = null;
	    //to support http.request
	    if (result.search) {
	      result.path = '/' + result.search;
	    } else {
	      result.path = null;
	    }
	    result.href = result.format();
	    return result;
	  }
	
	  // if a url ENDs in . or .., then it must get a trailing slash.
	  // however, if it ends in anything else non-slashy,
	  // then it must NOT get a trailing slash.
	  var last = srcPath.slice(-1)[0];
	  var hasTrailingSlash = (
	      (result.host || relative.host) && (last === '.' || last === '..') ||
	      last === '');
	
	  // strip single dots, resolve double dots to parent dir
	  // if the path tries to go above the root, `up` ends up > 0
	  var up = 0;
	  for (var i = srcPath.length; i >= 0; i--) {
	    last = srcPath[i];
	    if (last == '.') {
	      srcPath.splice(i, 1);
	    } else if (last === '..') {
	      srcPath.splice(i, 1);
	      up++;
	    } else if (up) {
	      srcPath.splice(i, 1);
	      up--;
	    }
	  }
	
	  // if the path is allowed to go above the root, restore leading ..s
	  if (!mustEndAbs && !removeAllDots) {
	    for (; up--; up) {
	      srcPath.unshift('..');
	    }
	  }
	
	  if (mustEndAbs && srcPath[0] !== '' &&
	      (!srcPath[0] || srcPath[0].charAt(0) !== '/')) {
	    srcPath.unshift('');
	  }
	
	  if (hasTrailingSlash && (srcPath.join('/').substr(-1) !== '/')) {
	    srcPath.push('');
	  }
	
	  var isAbsolute = srcPath[0] === '' ||
	      (srcPath[0] && srcPath[0].charAt(0) === '/');
	
	  // put the host back
	  if (psychotic) {
	    result.hostname = result.host = isAbsolute ? '' :
	                                    srcPath.length ? srcPath.shift() : '';
	    //occationaly the auth can get stuck only in host
	    //this especialy happens in cases like
	    //url.resolveObject('mailto:local1@domain1', 'local2@domain2')
	    var authInHost = result.host && result.host.indexOf('@') > 0 ?
	                     result.host.split('@') : false;
	    if (authInHost) {
	      result.auth = authInHost.shift();
	      result.host = result.hostname = authInHost.shift();
	    }
	  }
	
	  mustEndAbs = mustEndAbs || (result.host && srcPath.length);
	
	  if (mustEndAbs && !isAbsolute) {
	    srcPath.unshift('');
	  }
	
	  if (!srcPath.length) {
	    result.pathname = null;
	    result.path = null;
	  } else {
	    result.pathname = srcPath.join('/');
	  }
	
	  //to support request.http
	  if (!isNull(result.pathname) || !isNull(result.search)) {
	    result.path = (result.pathname ? result.pathname : '') +
	                  (result.search ? result.search : '');
	  }
	  result.auth = relative.auth || result.auth;
	  result.slashes = result.slashes || relative.slashes;
	  result.href = result.format();
	  return result;
	};
	
	Url.prototype.parseHost = function() {
	  var host = this.host;
	  var port = portPattern.exec(host);
	  if (port) {
	    port = port[0];
	    if (port !== ':') {
	      this.port = port.substr(1);
	    }
	    host = host.substr(0, host.length - port.length);
	  }
	  if (host) this.hostname = host;
	};
	
	function isString(arg) {
	  return typeof arg === "string";
	}
	
	function isObject(arg) {
	  return typeof arg === 'object' && arg !== null;
	}
	
	function isNull(arg) {
	  return arg === null;
	}
	function isNullOrUndefined(arg) {
	  return  arg == null;
	}


/***/ },
/* 95 */
/*!****************************************!*\
  !*** (webpack)/~/punycode/punycode.js ***!
  \****************************************/
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_RESULT__;/* WEBPACK VAR INJECTION */(function(module, global) {/*! https://mths.be/punycode v1.3.2 by @mathias */
	;(function(root) {
	
		/** Detect free variables */
		var freeExports = typeof exports == 'object' && exports &&
			!exports.nodeType && exports;
		var freeModule = typeof module == 'object' && module &&
			!module.nodeType && module;
		var freeGlobal = typeof global == 'object' && global;
		if (
			freeGlobal.global === freeGlobal ||
			freeGlobal.window === freeGlobal ||
			freeGlobal.self === freeGlobal
		) {
			root = freeGlobal;
		}
	
		/**
		 * The `punycode` object.
		 * @name punycode
		 * @type Object
		 */
		var punycode,
	
		/** Highest positive signed 32-bit float value */
		maxInt = 2147483647, // aka. 0x7FFFFFFF or 2^31-1
	
		/** Bootstring parameters */
		base = 36,
		tMin = 1,
		tMax = 26,
		skew = 38,
		damp = 700,
		initialBias = 72,
		initialN = 128, // 0x80
		delimiter = '-', // '\x2D'
	
		/** Regular expressions */
		regexPunycode = /^xn--/,
		regexNonASCII = /[^\x20-\x7E]/, // unprintable ASCII chars + non-ASCII chars
		regexSeparators = /[\x2E\u3002\uFF0E\uFF61]/g, // RFC 3490 separators
	
		/** Error messages */
		errors = {
			'overflow': 'Overflow: input needs wider integers to process',
			'not-basic': 'Illegal input >= 0x80 (not a basic code point)',
			'invalid-input': 'Invalid input'
		},
	
		/** Convenience shortcuts */
		baseMinusTMin = base - tMin,
		floor = Math.floor,
		stringFromCharCode = String.fromCharCode,
	
		/** Temporary variable */
		key;
	
		/*--------------------------------------------------------------------------*/
	
		/**
		 * A generic error utility function.
		 * @private
		 * @param {String} type The error type.
		 * @returns {Error} Throws a `RangeError` with the applicable error message.
		 */
		function error(type) {
			throw RangeError(errors[type]);
		}
	
		/**
		 * A generic `Array#map` utility function.
		 * @private
		 * @param {Array} array The array to iterate over.
		 * @param {Function} callback The function that gets called for every array
		 * item.
		 * @returns {Array} A new array of values returned by the callback function.
		 */
		function map(array, fn) {
			var length = array.length;
			var result = [];
			while (length--) {
				result[length] = fn(array[length]);
			}
			return result;
		}
	
		/**
		 * A simple `Array#map`-like wrapper to work with domain name strings or email
		 * addresses.
		 * @private
		 * @param {String} domain The domain name or email address.
		 * @param {Function} callback The function that gets called for every
		 * character.
		 * @returns {Array} A new string of characters returned by the callback
		 * function.
		 */
		function mapDomain(string, fn) {
			var parts = string.split('@');
			var result = '';
			if (parts.length > 1) {
				// In email addresses, only the domain name should be punycoded. Leave
				// the local part (i.e. everything up to `@`) intact.
				result = parts[0] + '@';
				string = parts[1];
			}
			// Avoid `split(regex)` for IE8 compatibility. See #17.
			string = string.replace(regexSeparators, '\x2E');
			var labels = string.split('.');
			var encoded = map(labels, fn).join('.');
			return result + encoded;
		}
	
		/**
		 * Creates an array containing the numeric code points of each Unicode
		 * character in the string. While JavaScript uses UCS-2 internally,
		 * this function will convert a pair of surrogate halves (each of which
		 * UCS-2 exposes as separate characters) into a single code point,
		 * matching UTF-16.
		 * @see `punycode.ucs2.encode`
		 * @see <https://mathiasbynens.be/notes/javascript-encoding>
		 * @memberOf punycode.ucs2
		 * @name decode
		 * @param {String} string The Unicode input string (UCS-2).
		 * @returns {Array} The new array of code points.
		 */
		function ucs2decode(string) {
			var output = [],
			    counter = 0,
			    length = string.length,
			    value,
			    extra;
			while (counter < length) {
				value = string.charCodeAt(counter++);
				if (value >= 0xD800 && value <= 0xDBFF && counter < length) {
					// high surrogate, and there is a next character
					extra = string.charCodeAt(counter++);
					if ((extra & 0xFC00) == 0xDC00) { // low surrogate
						output.push(((value & 0x3FF) << 10) + (extra & 0x3FF) + 0x10000);
					} else {
						// unmatched surrogate; only append this code unit, in case the next
						// code unit is the high surrogate of a surrogate pair
						output.push(value);
						counter--;
					}
				} else {
					output.push(value);
				}
			}
			return output;
		}
	
		/**
		 * Creates a string based on an array of numeric code points.
		 * @see `punycode.ucs2.decode`
		 * @memberOf punycode.ucs2
		 * @name encode
		 * @param {Array} codePoints The array of numeric code points.
		 * @returns {String} The new Unicode string (UCS-2).
		 */
		function ucs2encode(array) {
			return map(array, function(value) {
				var output = '';
				if (value > 0xFFFF) {
					value -= 0x10000;
					output += stringFromCharCode(value >>> 10 & 0x3FF | 0xD800);
					value = 0xDC00 | value & 0x3FF;
				}
				output += stringFromCharCode(value);
				return output;
			}).join('');
		}
	
		/**
		 * Converts a basic code point into a digit/integer.
		 * @see `digitToBasic()`
		 * @private
		 * @param {Number} codePoint The basic numeric code point value.
		 * @returns {Number} The numeric value of a basic code point (for use in
		 * representing integers) in the range `0` to `base - 1`, or `base` if
		 * the code point does not represent a value.
		 */
		function basicToDigit(codePoint) {
			if (codePoint - 48 < 10) {
				return codePoint - 22;
			}
			if (codePoint - 65 < 26) {
				return codePoint - 65;
			}
			if (codePoint - 97 < 26) {
				return codePoint - 97;
			}
			return base;
		}
	
		/**
		 * Converts a digit/integer into a basic code point.
		 * @see `basicToDigit()`
		 * @private
		 * @param {Number} digit The numeric value of a basic code point.
		 * @returns {Number} The basic code point whose value (when used for
		 * representing integers) is `digit`, which needs to be in the range
		 * `0` to `base - 1`. If `flag` is non-zero, the uppercase form is
		 * used; else, the lowercase form is used. The behavior is undefined
		 * if `flag` is non-zero and `digit` has no uppercase form.
		 */
		function digitToBasic(digit, flag) {
			//  0..25 map to ASCII a..z or A..Z
			// 26..35 map to ASCII 0..9
			return digit + 22 + 75 * (digit < 26) - ((flag != 0) << 5);
		}
	
		/**
		 * Bias adaptation function as per section 3.4 of RFC 3492.
		 * http://tools.ietf.org/html/rfc3492#section-3.4
		 * @private
		 */
		function adapt(delta, numPoints, firstTime) {
			var k = 0;
			delta = firstTime ? floor(delta / damp) : delta >> 1;
			delta += floor(delta / numPoints);
			for (/* no initialization */; delta > baseMinusTMin * tMax >> 1; k += base) {
				delta = floor(delta / baseMinusTMin);
			}
			return floor(k + (baseMinusTMin + 1) * delta / (delta + skew));
		}
	
		/**
		 * Converts a Punycode string of ASCII-only symbols to a string of Unicode
		 * symbols.
		 * @memberOf punycode
		 * @param {String} input The Punycode string of ASCII-only symbols.
		 * @returns {String} The resulting string of Unicode symbols.
		 */
		function decode(input) {
			// Don't use UCS-2
			var output = [],
			    inputLength = input.length,
			    out,
			    i = 0,
			    n = initialN,
			    bias = initialBias,
			    basic,
			    j,
			    index,
			    oldi,
			    w,
			    k,
			    digit,
			    t,
			    /** Cached calculation results */
			    baseMinusT;
	
			// Handle the basic code points: let `basic` be the number of input code
			// points before the last delimiter, or `0` if there is none, then copy
			// the first basic code points to the output.
	
			basic = input.lastIndexOf(delimiter);
			if (basic < 0) {
				basic = 0;
			}
	
			for (j = 0; j < basic; ++j) {
				// if it's not a basic code point
				if (input.charCodeAt(j) >= 0x80) {
					error('not-basic');
				}
				output.push(input.charCodeAt(j));
			}
	
			// Main decoding loop: start just after the last delimiter if any basic code
			// points were copied; start at the beginning otherwise.
	
			for (index = basic > 0 ? basic + 1 : 0; index < inputLength; /* no final expression */) {
	
				// `index` is the index of the next character to be consumed.
				// Decode a generalized variable-length integer into `delta`,
				// which gets added to `i`. The overflow checking is easier
				// if we increase `i` as we go, then subtract off its starting
				// value at the end to obtain `delta`.
				for (oldi = i, w = 1, k = base; /* no condition */; k += base) {
	
					if (index >= inputLength) {
						error('invalid-input');
					}
	
					digit = basicToDigit(input.charCodeAt(index++));
	
					if (digit >= base || digit > floor((maxInt - i) / w)) {
						error('overflow');
					}
	
					i += digit * w;
					t = k <= bias ? tMin : (k >= bias + tMax ? tMax : k - bias);
	
					if (digit < t) {
						break;
					}
	
					baseMinusT = base - t;
					if (w > floor(maxInt / baseMinusT)) {
						error('overflow');
					}
	
					w *= baseMinusT;
	
				}
	
				out = output.length + 1;
				bias = adapt(i - oldi, out, oldi == 0);
	
				// `i` was supposed to wrap around from `out` to `0`,
				// incrementing `n` each time, so we'll fix that now:
				if (floor(i / out) > maxInt - n) {
					error('overflow');
				}
	
				n += floor(i / out);
				i %= out;
	
				// Insert `n` at position `i` of the output
				output.splice(i++, 0, n);
	
			}
	
			return ucs2encode(output);
		}
	
		/**
		 * Converts a string of Unicode symbols (e.g. a domain name label) to a
		 * Punycode string of ASCII-only symbols.
		 * @memberOf punycode
		 * @param {String} input The string of Unicode symbols.
		 * @returns {String} The resulting Punycode string of ASCII-only symbols.
		 */
		function encode(input) {
			var n,
			    delta,
			    handledCPCount,
			    basicLength,
			    bias,
			    j,
			    m,
			    q,
			    k,
			    t,
			    currentValue,
			    output = [],
			    /** `inputLength` will hold the number of code points in `input`. */
			    inputLength,
			    /** Cached calculation results */
			    handledCPCountPlusOne,
			    baseMinusT,
			    qMinusT;
	
			// Convert the input in UCS-2 to Unicode
			input = ucs2decode(input);
	
			// Cache the length
			inputLength = input.length;
	
			// Initialize the state
			n = initialN;
			delta = 0;
			bias = initialBias;
	
			// Handle the basic code points
			for (j = 0; j < inputLength; ++j) {
				currentValue = input[j];
				if (currentValue < 0x80) {
					output.push(stringFromCharCode(currentValue));
				}
			}
	
			handledCPCount = basicLength = output.length;
	
			// `handledCPCount` is the number of code points that have been handled;
			// `basicLength` is the number of basic code points.
	
			// Finish the basic string - if it is not empty - with a delimiter
			if (basicLength) {
				output.push(delimiter);
			}
	
			// Main encoding loop:
			while (handledCPCount < inputLength) {
	
				// All non-basic code points < n have been handled already. Find the next
				// larger one:
				for (m = maxInt, j = 0; j < inputLength; ++j) {
					currentValue = input[j];
					if (currentValue >= n && currentValue < m) {
						m = currentValue;
					}
				}
	
				// Increase `delta` enough to advance the decoder's <n,i> state to <m,0>,
				// but guard against overflow
				handledCPCountPlusOne = handledCPCount + 1;
				if (m - n > floor((maxInt - delta) / handledCPCountPlusOne)) {
					error('overflow');
				}
	
				delta += (m - n) * handledCPCountPlusOne;
				n = m;
	
				for (j = 0; j < inputLength; ++j) {
					currentValue = input[j];
	
					if (currentValue < n && ++delta > maxInt) {
						error('overflow');
					}
	
					if (currentValue == n) {
						// Represent delta as a generalized variable-length integer
						for (q = delta, k = base; /* no condition */; k += base) {
							t = k <= bias ? tMin : (k >= bias + tMax ? tMax : k - bias);
							if (q < t) {
								break;
							}
							qMinusT = q - t;
							baseMinusT = base - t;
							output.push(
								stringFromCharCode(digitToBasic(t + qMinusT % baseMinusT, 0))
							);
							q = floor(qMinusT / baseMinusT);
						}
	
						output.push(stringFromCharCode(digitToBasic(q, 0)));
						bias = adapt(delta, handledCPCountPlusOne, handledCPCount == basicLength);
						delta = 0;
						++handledCPCount;
					}
				}
	
				++delta;
				++n;
	
			}
			return output.join('');
		}
	
		/**
		 * Converts a Punycode string representing a domain name or an email address
		 * to Unicode. Only the Punycoded parts of the input will be converted, i.e.
		 * it doesn't matter if you call it on a string that has already been
		 * converted to Unicode.
		 * @memberOf punycode
		 * @param {String} input The Punycoded domain name or email address to
		 * convert to Unicode.
		 * @returns {String} The Unicode representation of the given Punycode
		 * string.
		 */
		function toUnicode(input) {
			return mapDomain(input, function(string) {
				return regexPunycode.test(string)
					? decode(string.slice(4).toLowerCase())
					: string;
			});
		}
	
		/**
		 * Converts a Unicode string representing a domain name or an email address to
		 * Punycode. Only the non-ASCII parts of the domain name will be converted,
		 * i.e. it doesn't matter if you call it with a domain that's already in
		 * ASCII.
		 * @memberOf punycode
		 * @param {String} input The domain name or email address to convert, as a
		 * Unicode string.
		 * @returns {String} The Punycode representation of the given domain name or
		 * email address.
		 */
		function toASCII(input) {
			return mapDomain(input, function(string) {
				return regexNonASCII.test(string)
					? 'xn--' + encode(string)
					: string;
			});
		}
	
		/*--------------------------------------------------------------------------*/
	
		/** Define the public API */
		punycode = {
			/**
			 * A string representing the current Punycode.js version number.
			 * @memberOf punycode
			 * @type String
			 */
			'version': '1.3.2',
			/**
			 * An object of methods to convert from JavaScript's internal character
			 * representation (UCS-2) to Unicode code points, and back.
			 * @see <https://mathiasbynens.be/notes/javascript-encoding>
			 * @memberOf punycode
			 * @type Object
			 */
			'ucs2': {
				'decode': ucs2decode,
				'encode': ucs2encode
			},
			'decode': decode,
			'encode': encode,
			'toASCII': toASCII,
			'toUnicode': toUnicode
		};
	
		/** Expose `punycode` */
		// Some AMD build optimizers, like r.js, check for specific condition patterns
		// like the following:
		if (
			true
		) {
			!(__WEBPACK_AMD_DEFINE_RESULT__ = function() {
				return punycode;
			}.call(exports, __webpack_require__, exports, module), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
		} else if (freeExports && freeModule) {
			if (module.exports == freeExports) { // in Node.js or RingoJS v0.8.0+
				freeModule.exports = punycode;
			} else { // in Narwhal or RingoJS v0.7.0-
				for (key in punycode) {
					punycode.hasOwnProperty(key) && (freeExports[key] = punycode[key]);
				}
			}
		} else { // in Rhino or a web browser
			root.punycode = punycode;
		}
	
	}(this));
	
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! ./../../buildin/module.js */ 96)(module), (function() { return this; }())))

/***/ },
/* 96 */
/*!***********************************!*\
  !*** (webpack)/buildin/module.js ***!
  \***********************************/
/***/ function(module, exports) {

	module.exports = function(module) {
		if(!module.webpackPolyfill) {
			module.deprecate = function() {};
			module.paths = [];
			// module.parent = undefined by default
			module.children = [];
			module.webpackPolyfill = 1;
		}
		return module;
	}


/***/ },
/* 97 */
/*!****************************************!*\
  !*** (webpack)/~/querystring/index.js ***!
  \****************************************/
/***/ function(module, exports, __webpack_require__) {

	'use strict';
	
	exports.decode = exports.parse = __webpack_require__(/*! ./decode */ 98);
	exports.encode = exports.stringify = __webpack_require__(/*! ./encode */ 99);


/***/ },
/* 98 */
/*!*****************************************!*\
  !*** (webpack)/~/querystring/decode.js ***!
  \*****************************************/
/***/ function(module, exports) {

	// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.
	
	'use strict';
	
	// If obj.hasOwnProperty has been overridden, then calling
	// obj.hasOwnProperty(prop) will break.
	// See: https://github.com/joyent/node/issues/1707
	function hasOwnProperty(obj, prop) {
	  return Object.prototype.hasOwnProperty.call(obj, prop);
	}
	
	module.exports = function(qs, sep, eq, options) {
	  sep = sep || '&';
	  eq = eq || '=';
	  var obj = {};
	
	  if (typeof qs !== 'string' || qs.length === 0) {
	    return obj;
	  }
	
	  var regexp = /\+/g;
	  qs = qs.split(sep);
	
	  var maxKeys = 1000;
	  if (options && typeof options.maxKeys === 'number') {
	    maxKeys = options.maxKeys;
	  }
	
	  var len = qs.length;
	  // maxKeys <= 0 means that we should not limit keys count
	  if (maxKeys > 0 && len > maxKeys) {
	    len = maxKeys;
	  }
	
	  for (var i = 0; i < len; ++i) {
	    var x = qs[i].replace(regexp, '%20'),
	        idx = x.indexOf(eq),
	        kstr, vstr, k, v;
	
	    if (idx >= 0) {
	      kstr = x.substr(0, idx);
	      vstr = x.substr(idx + 1);
	    } else {
	      kstr = x;
	      vstr = '';
	    }
	
	    k = decodeURIComponent(kstr);
	    v = decodeURIComponent(vstr);
	
	    if (!hasOwnProperty(obj, k)) {
	      obj[k] = v;
	    } else if (Array.isArray(obj[k])) {
	      obj[k].push(v);
	    } else {
	      obj[k] = [obj[k], v];
	    }
	  }
	
	  return obj;
	};


/***/ },
/* 99 */
/*!*****************************************!*\
  !*** (webpack)/~/querystring/encode.js ***!
  \*****************************************/
/***/ function(module, exports) {

	// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.
	
	'use strict';
	
	var stringifyPrimitive = function(v) {
	  switch (typeof v) {
	    case 'string':
	      return v;
	
	    case 'boolean':
	      return v ? 'true' : 'false';
	
	    case 'number':
	      return isFinite(v) ? v : '';
	
	    default:
	      return '';
	  }
	};
	
	module.exports = function(obj, sep, eq, name) {
	  sep = sep || '&';
	  eq = eq || '=';
	  if (obj === null) {
	    obj = undefined;
	  }
	
	  if (typeof obj === 'object') {
	    return Object.keys(obj).map(function(k) {
	      var ks = encodeURIComponent(stringifyPrimitive(k)) + eq;
	      if (Array.isArray(obj[k])) {
	        return obj[k].map(function(v) {
	          return ks + encodeURIComponent(stringifyPrimitive(v));
	        }).join(sep);
	      } else {
	        return ks + encodeURIComponent(stringifyPrimitive(obj[k]));
	      }
	    }).join(sep);
	
	  }
	
	  if (!name) return '';
	  return encodeURIComponent(stringifyPrimitive(name)) + eq +
	         encodeURIComponent(stringifyPrimitive(obj));
	};


/***/ },
/* 100 */
/*!************************************!*\
  !*** ./js/src/modules/bankData.js ***!
  \************************************/
/***/ function(module, exports) {

	module.exports = [{ "sortName": "ICBC", "bank": "中国工商银行", "hot": true }, { "sortName": "ABC", "bank": "中国农业银行", "hot": true }, { "sortName": "CCB", "bank": "中国建设银行", "hot": true }, { "sortName": "CMB", "bank": "招商银行", "hot": true }, { "sortName": "BOC", "bank": "中国银行", "hot": true }, { "sortName": "PSBC", "bank": "中国邮政储蓄银行", "hot": true }, { "sortName": "COMM", "bank": "交通银行", "hot": true }, { "sortName": "CITIC", "bank": "中信银行", "hot": true }, { "sortName": "CMBC", "bank": "中国民生银行", "hot": true }, { "sortName": "CEB", "bank": "中国光大银行", "hot": true }, { "sortName": "CIB", "bank": "兴业银行", "hot": true }, { "sortName": "SPDB", "bank": "浦发银行", "hot": true }, { "sortName": "GDB", "bank": "广发银行", "hot": true }, { "sortName": "SPABANK", "bank": "平安银行", "hot": true }, { "sortName": "HXBANK", "bank": "华夏银行", "hot": true }, { "sortName": "BJBANK", "bank": "北京银行", "hot": true }, { "sortName": "SHBANK", "bank": "上海银行", "hot": true }, { "sortName": "JSBANK", "bank": "江苏银行", "hot": true }, { "sortName": "BJRCB", "bank": "北京农商行", "hot": true }, { "sortName": "JSRCU", "bank": "江苏省农村信用社联合社", "hot": true }, { "sortName": "ARCU", "bank": "安徽省农村信用社" }, { "sortName": "ASCB", "bank": "鞍山银行" }, { "sortName": "BJBANK", "bank": "北京银行" }, { "sortName": "BJRCB", "bank": "北京农商行" }, { "sortName": "BOHAIB", "bank": "渤海银行" }, { "sortName": "BSB", "bank": "包商银行" }, { "sortName": "CABANK", "bank": "长安银行" }, { "sortName": "CSCB", "bank": "长沙银行" }, { "sortName": "BOCZ", "bank": "沧州银行" }, { "sortName": "CDCB", "bank": "成都银行" }, { "sortName": "CRCBANK", "bank": "重庆农村商业银行" }, { "sortName": "CSRCB", "bank": "常熟农商银行" }, { "sortName": "CQBANK", "bank": "重庆银行" }, { "sortName": "CCQTGB", "bank": "重庆三峡银行" }, { "sortName": "BOCD", "bank": "承德银行" }, { "sortName": "CDRCB", "bank": "成都农商银行" }, { "sortName": "HKBEA", "bank": "东亚银行" }, { "sortName": "DYCB", "bank": "德阳银行" }, { "sortName": "DLB", "bank": "大连银行" }, { "sortName": "DRCBCL", "bank": "东莞农村商业银行" }, { "sortName": "BOD", "bank": "东莞银行" }, { "sortName": "DZBANK", "bank": "德州银行" }, { "sortName": "DYCCB", "bank": "东营银行" }, { "sortName": "ORBANK", "bank": "鄂尔多斯银行" }, { "sortName": "FBBANK", "bank": "富邦华一银行" }, { "sortName": "FJNX", "bank": "福建省农村信用社联合社" }, { "sortName": "FXCB", "bank": "阜新银行" }, { "sortName": "FDB", "bank": "富滇银行" }, { "sortName": "FJHXBC", "bank": "福建海峡银行" }, { "sortName": "GDB", "bank": "广发银行" }, { "sortName": "GYCB", "bank": "贵阳银行" }, { "sortName": "GLBANK", "bank": "桂林银行" }, { "sortName": "GZRCU", "bank": "贵州省农村信用社联合社" }, { "sortName": "BGB", "bank": "广西北部湾银行" }, { "sortName": "GDRCC", "bank": "广东省农村信用社联合社" }, { "sortName": "GHB", "bank": "广东华兴银行" }, { "sortName": "GRCB", "bank": "广州农村商业银行" }, { "sortName": "GSRCU", "bank": "甘肃省农村信用社" }, { "sortName": "GZB", "bank": "赣州银行" }, { "sortName": "GXRCU", "bank": "广西壮族自治区农村信用社联合社" }, { "sortName": "NYBANK", "bank": "广东南粤银行" }, { "sortName": "GCB", "bank": "广州银行" }, { "sortName": "HXBANK", "bank": "华夏银行" }, { "sortName": "EGBANK", "bank": "恒丰银行" }, { "sortName": "HURCB", "bank": "湖北省农村信用合作联社" }, { "sortName": "HANABANK", "bank": "韩亚银行" }, { "sortName": "QYBANK", "bank": "韩国企业银行" }, { "sortName": "HRXJB", "bank": "华融湘江银行" }, { "sortName": "HNRCC", "bank": "湖南省农村信用社" }, { "sortName": "HNRCU", "bank": "河南省农村信用社" }, { "sortName": "HSBANK", "bank": "徽商银行" }, { "sortName": "HRBANK", "bank": "哈尔滨银行" }, { "sortName": "HLDB", "bank": "葫芦岛银行" }, { "sortName": "UBCHN", "bank": "海口联合农商银行" }, { "sortName": "HZCB", "bank": "杭州银行" }, { "sortName": "BHB", "bank": "河北银行" }, { "sortName": "HBRCU", "bank": "河北省农村信用社联合社" }, { "sortName": "HDBANK", "bank": "邯郸银行" }, { "sortName": "BOHN", "bank": "海南省农村信用社" }, { "sortName": "HKB", "bank": "汉口银行" }, { "sortName": "HBC", "bank": "湖北银行" }, { "sortName": "HSBK", "bank": "衡水市商业银行" }, { "sortName": "HZCCB", "bank": "湖州银行" }, { "sortName": "HLJRCU", "bank": "黑龙江省农村信用社联合社" }, { "sortName": "COMM", "bank": "交通银行" }, { "sortName": "JSBANK", "bank": "江苏银行" }, { "sortName": "JSRCU", "bank": "江苏省农村信用社联合社" }, { "sortName": "JNBANK", "bank": "济宁银行" }, { "sortName": "JLBANK", "bank": "吉林银行" }, { "sortName": "JZCBANK", "bank": "焦作市商业银行" }, { "sortName": "JLRCU", "bank": "吉林省农村信用社联合社" }, { "sortName": "JSB", "bank": "晋商银行" }, { "sortName": "TCRCB", "bank": "江苏太仓农村商业银行" }, { "sortName": "JINCHB", "bank": "晋城银行" }, { "sortName": "JXRCU", "bank": "江西省农村信用社" }, { "sortName": "NCB", "bank": "江西银行" }, { "sortName": "JJBANK", "bank": "九江银行" }, { "sortName": "JXBANK", "bank": "嘉兴银行" }, { "sortName": "JZBANK", "bank": "晋中银行" }, { "sortName": "CJCCB", "bank": "江苏长江商业银行" }, { "sortName": "JRCB", "bank": "江苏江阴农村商业银行" }, { "sortName": "JHBANK", "bank": "金华银行" }, { "sortName": "BOJZ", "bank": "锦州银行" }, { "sortName": "KLB", "bank": "昆仑银行" }, { "sortName": "KSRB", "bank": "昆山农村商业银行" }, { "sortName": "LSBANK", "bank": "莱商银行" }, { "sortName": "DAQINGB", "bank": "龙江银行" }, { "sortName": "LHBANK", "bank": "漯河银行" }, { "sortName": "LNRCC", "bank": "辽宁省农村信用社" }, { "sortName": "LZCCB", "bank": "柳州银行" }, { "sortName": "LZYH", "bank": "兰州银行" }, { "sortName": "LSBC", "bank": "临商银行" }, { "sortName": "URB", "bank": "联合村镇银行" }, { "sortName": "BOL", "bank": "洛阳银行" }, { "sortName": "LANGFB", "bank": "廊坊银行" }, { "sortName": "MYBANK", "bank": "绵阳市商业银行" }, { "sortName": "NMGNXS", "bank": "内蒙古农村信用社联合社" }, { "sortName": "NBBANK", "bank": "宁波银行" }, { "sortName": "NHB", "bank": "南海农商银行" }, { "sortName": "NBCBANK", "bank": "宁波通商银行" }, { "sortName": "NJCB", "bank": "南京银行" }, { "sortName": "NXBANK", "bank": "宁夏银行" }, { "sortName": "NXRCU", "bank": "宁夏黄河农村商业银行" }, { "sortName": "CGNB", "bank": "南充市商业银行" }, { "sortName": "BNY", "bank": "南阳市商业银行" }, { "sortName": "H3CB", "bank": "内蒙古银行" }, { "sortName": "SPDB", "bank": "浦发银行" }, { "sortName": "SPABANK", "bank": "平安银行" }, { "sortName": "PZHCCB", "bank": "攀枝花市商业银行" }, { "sortName": "BOP", "bank": "平顶山银行" }, { "sortName": "ZBCB", "bank": "齐商银行" }, { "sortName": "QDCCB", "bank": "青岛银行" }, { "sortName": "BOQZ", "bank": "泉州银行" }, { "sortName": "BOQH", "bank": "青海银行" }, { "sortName": "QLBANK", "bank": "齐鲁银行" }, { "sortName": "RZB", "bank": "日照银行" }, { "sortName": "SHBANK", "bank": "上海银行" }, { "sortName": "SXCB", "bank": "绍兴银行" }, { "sortName": "SRCB", "bank": "深圳农村商业银行" }, { "sortName": "SDEB", "bank": "顺德农商银行" }, { "sortName": "SRBANK", "bank": "上饶银行" }, { "sortName": "SXRCU", "bank": "山西省农村信用社" }, { "sortName": "SXRCCU", "bank": "陕西信合" }, { "sortName": "SHRCB", "bank": "上海农商银行" }, { "sortName": "BOSZ", "bank": "苏州银行" }, { "sortName": "SCRCU", "bank": "四川省农村信用社联合社" }, { "sortName": "SDRCU", "bank": "山东省农村信用社联合社" }, { "sortName": "TCCB", "bank": "天津银行" }, { "sortName": "TACCB", "bank": "泰安市商业银行" }, { "sortName": "TRCB", "bank": "天津农商银行" }, { "sortName": "TJBHB", "bank": "天津滨海农村商业银行" }, { "sortName": "TZCB", "bank": "台州银行" }, { "sortName": "BANKWF", "bank": "潍坊银行" }, { "sortName": "URMQCCB", "bank": "乌鲁木齐市商业银行" }, { "sortName": "WHCCB", "bank": "威海市商业银行" }, { "sortName": "KEB", "bank": "外换银行" }, { "sortName": "WZCB", "bank": "温州银行" }, { "sortName": "WHRCB", "bank": "武汉农村商业银行" }, { "sortName": "WRCB", "bank": "无锡农村商业银行" }, { "sortName": "WJRCB", "bank": "吴江农村商业银行" }, { "sortName": "CIB", "bank": "兴业银行" }, { "sortName": "XTB", "bank": "邢台银行" }, { "sortName": "XJRCU", "bank": "新疆农村信用社" }, { "sortName": "XMBANK", "bank": "厦门银行" }, { "sortName": "XABANK", "bank": "西安银行" }, { "sortName": "BOSH", "bank": "新韩银行" }, { "sortName": "NBYZ", "bank": "鄞州银行" }, { "sortName": "YTBANK", "bank": "烟台银行" }, { "sortName": "WOORI", "bank": "友利银行" }, { "sortName": "YNRCC", "bank": "云南省农村信用社" }, { "sortName": "BOYK", "bank": "营口银行" }, { "sortName": "ICBC", "bank": "中国工商银行" }, { "sortName": "ABC", "bank": "中国农业银行" }, { "sortName": "CCB", "bank": "中国建设银行" }, { "sortName": "CMB", "bank": "招商银行" }, { "sortName": "BOC", "bank": "中国银行" }, { "sortName": "PSBC", "bank": "中国邮政储蓄银行" }, { "sortName": "CITIC", "bank": "中信银行" }, { "sortName": "CMBC", "bank": "中国民生银行" }, { "sortName": "CEB", "bank": "中国光大银行" }, { "sortName": "MTBANK", "bank": "浙江民泰商业银行" }, { "sortName": "CZBANK", "bank": "浙商银行" }, { "sortName": "ZZYH", "bank": "枣庄银行" }, { "sortName": "RBOZ", "bank": "珠海华润银行" }, { "sortName": "ZZBANK", "bank": "郑州银行" }, { "sortName": "ZRCBANK", "bank": "张家港农村商业银行" }, { "sortName": "ZYB", "bank": "中原银行" }, { "sortName": "ZJKCCB", "bank": "张家口市商业银行" }, { "sortName": "CZCB", "bank": "浙江稠州商业银行" }, { "sortName": "ZJTLCB", "bank": "浙江泰隆商业银行" }, { "sortName": "ZJNX", "bank": "浙江省农村信用社联合社" }, { "sortName": "ZGCCB", "bank": "自贡市商业银行" }]


/***/ },
/* 101 */
/*!************************************!*\
  !*** ./js/src/tpl/payrollView.vue ***!
  \************************************/
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_template__ = __webpack_require__(/*! !tpl-html-loader!./../../../../../../~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./payrollView.vue */ 102)
	module.exports = __vue_script__ || {}
	if (__vue_template__) {
	(typeof module.exports === "function" ? (module.exports.options || (module.exports.options = {})) : module.exports).template = __vue_template__
	}
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), false)
	  if (!hotAPI.compatible) return
	  var id = "_v-4e5d1166/payrollView.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 102 */
/*!*************************************************************************************************************************************!*\
  !*** F:/item/zbw/~/tpl-html-loader!F:/item/zbw/~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./js/src/tpl/payrollView.vue ***!
  \*************************************************************************************************************************************/
/***/ function(module, exports) {

	module.exports = "\n<form id=\"payroll-audit-form\" class=\"form-inline gutter clearfix\">\n  <div class=\"form-group col-xs-6\">\n  \t<label class=\"text-right\">&nbsp;&nbsp;姓名：</label>{{user_name}}\n  </div>\n  <div class=\"form-group col-xs-6\">\n  <label class=\"text-right\">身份证号码：</label>{{card_num}}\n  </div>\n  <div class=\"form-group col-xs-6\">\n    <label class=\"text-right\">银行名称：</label>\n    {{bank}}\n  </div>\n  <div class=\"form-group col-xs-6\">\n    <label class=\"text-right\">支行名称：</label>\n    {{branch}}\n  </div>\n  <div class=\"form-group col-xs-6\">\n    <label class=\"text-right\" for=\"branch\">银行卡号：</label>\n    {{account}}\n  </div>\n  <div class=\"form-group col-xs-6\">\n    <label class=\"text-right\" for=\"date\">工资年月：</label>\n    {{date}}\n  </div>\n \t<div class=\"form-group col-xs-6\">\n\t    <label class=\"text-right\">应发工资：</label>\n\t    {{wages}}\n\t\t 元\n\t  </div>\n\t  <div class=\"form-group col-xs-6\">\n\t    <label class=\"text-right\">扣个人所得税：</label>\n\t    {{deduction_income_tax}}\n\t     元\n\t</div>\n  \t<div class=\"form-group col-xs-6\">\n\t \t  <label class=\"text-right\">扣个人社保：</label>\n\t \t  {{deduction_social_insurance}}\n\t\t 元\n\t \t</div>\n\t \t<div class=\"form-group col-xs-6\">\n\t \t  <label class=\"text-right\">扣个人公积金：</label>\n\t \t  {{deduction_provident_fund}}\n\t \t   元\n\t \t</div>\n\t<div class=\"form-group col-xs-6\">\n\t    <label class=\"text-right\">补发：</label>\n\t    \t{{replacement}}\n\t     元\n\t  </div>\n\t  <div class=\"form-group col-xs-6\">\n\t    <label class=\"text-right\">其他扣除：</label>\n\t    {{deduction_other}}\n\t\t 元\n  \t   </div>\n  <p class=\"text-center wages-tip divider-top col-xs-12\">实发工资：<span id=\"wages-txt\" class=\"c-money\">{{actual_wages}}</span></p>\n\n</form>\n";

/***/ },
/* 103 */
/*!*************************************!*\
  !*** ./js/src/tpl/payrollAudit.vue ***!
  \*************************************/
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_template__ = __webpack_require__(/*! !tpl-html-loader!./../../../../../../~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./payrollAudit.vue */ 104)
	module.exports = __vue_script__ || {}
	if (__vue_template__) {
	(typeof module.exports === "function" ? (module.exports.options || (module.exports.options = {})) : module.exports).template = __vue_template__
	}
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), false)
	  if (!hotAPI.compatible) return
	  var id = "_v-a583d33a/payrollAudit.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 104 */
/*!**************************************************************************************************************************************!*\
  !*** F:/item/zbw/~/tpl-html-loader!F:/item/zbw/~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./js/src/tpl/payrollAudit.vue ***!
  \**************************************************************************************************************************************/
/***/ function(module, exports) {

	module.exports = "\t\n\t<form id=\"payroll-audit-form\" class=\"form-inline gutter clearfix\" method=\"post\" action=\"\">\n\t  <div class=\"form-group col-xs-12\">\n\t  <p class=\"col-xs-4\">&nbsp;&nbsp;姓名：{{user_name}}</p>\n\t  <p class=\"col-xs-8\">身份证号码：{{card_num}}</p>\n\t  </div>\n\t  <div class=\"form-group col-xs-12\">\n\t    <label class=\"text-right\" for=\"bank\">银行名称：</label>\n\t    <div class=\"inline-block\">\n\t\t    <select id=\"bank-select\" class=\"base-size\" name=\"bank\" required>\n\t\t    \t<option value=\"\">请选择</option>\n\n\t\t    \t{{each bankList}}\n\t\t    \t\t<option value=\"{{$value.bank}}\" {{if $value.bank == bank}}selected{{/if}}>{{$value.bank}}</option>\n\t\t    \t{{/each}}\n\t\t    </select>\n\t    </div>\n\t  </div>\n\t  <div class=\"form-group col-xs-12\" >\n\t    <label class=\"text-right\" for=\"branch\">支行名称：</label>\n\t    <div class=\"inline-block\">\n\t    \t<input type=\"text\" class=\"form-control\" required id=\"branch\" name=\"branch\" placeholder=\"\" value=\"{{branch}}\">\n\t    </div>\n\t    \n\t  </div>\n\t  <div class=\"form-group col-xs-12\">\n\t    <label class=\"text-right\" for=\"branch\">银行卡号：</label>\n\t    <div class=\"inline-block\">\n\t   \t\t<input type=\"text\" class=\"form-control\" required id=\"account\" name=\"account\" placeholder=\"\" value=\"{{account}}\">\n\t   \t</div>\n\t  </div>\n\t  <div class=\"form-group col-xs-12\">\n\t    <label class=\"text-right\" class=\"text-right\" for=\"date\">工资年月：</label>\n\t    <div class=\"inline-block\">\n\t    \t<input type=\"text\" class=\"form-control\" required id=\"date\" name=\"date\" placeholder=\"如：201511\" value=\"{{date}}\">\n\t    </div>\n\t     如：201511\n\t  </div>\n\t \t<div class=\"clearfix\">\n\t \t\t<div class=\"form-group col-xs-6\">\n\t\t\t    <label class=\"text-right\" for=\"wages\">应发工资：</label>\n\t\t\t    <div class=\"inline-block\">\n\t\t\t    \t<input type=\"text\" class=\"form-control form-control-small J-counter J-counter-add\" required id=\"wages\" name=\"wages\" placeholder=\"\" value=\"{{wages}}\">\n\t\t\t    </div>\n\t\t\t\t 元\n\t\t\t  </div>\n\t\t\t  <div class=\"form-group col-xs-6\">\n\t\t\t    <label class=\"text-right\" for=\"deduction_income_tax\">扣个人所得税：</label>\n\t\t\t    <div class=\"inline-block\">\n\t\t\t    \t<input type=\"text\" class=\"form-control form-control-small J-counter J-counter-cut\" required id=\"deduction_income_tax\" name=\"deduction_income_tax\" placeholder=\"\" value=\"{{deduction_income_tax}}\">\n\t\t\t    </div>\n\t\t\t     元\n\t\t\t</div>\n\t \t</div>\n\t  \t<div class=\"clearfix\">\n\t\t \t<div class=\"form-group col-xs-6\">\n\t\t \t    <label class=\"text-right\" for=\"deduction_social_insurance\">扣个人社保：</label>\n\t\t \t    <div class=\"inline-block\">\n\t\t \t  \t\t<input type=\"text\" class=\"form-control form-control-small J-counter J-counter-cut\" required id=\"deduction_social_insurance\" name=\"deduction_social_insurance\" placeholder=\"\" value=\"{{deduction_social_insurance}}\">\n\t\t \t    </div>\n\t\t\t \t元\n\t\t \t</div>\n\t\t \t<div class=\"form-group col-xs-6\">\n\t\t \t    <label class=\"text-right\" for=\"deduction_provident_fund\">扣个人公积金：</label>\n\t\t \t    <div class=\"inline-block\">\n\t\t \t  \t\t<input type=\"text\" class=\"form-control form-control-small J-counter J-counter-cut\" required id=\"deduction_provident_fund\" name=\"deduction_provident_fund\" placeholder=\"\" value=\"{{deduction_provident_fund}}\">\n\t\t \t  \t</div>\n\t\t \t   元\n\t\t \t</div>\n\t\t</div>\n\t\t<div class=\"clearfix\">\n\t\t  <div class=\"form-group col-xs-6\">\n\t\t    <label class=\"text-right\" for=\"replacement\">补发：</label>\n\t\t    <div class=\"inline-block\">\n\t\t    \t<input type=\"text\" class=\"form-control form-control-small\" required id=\"replacement\" name=\"replacement\" placeholder=\"\" value=\"{{replacement}}\">\n\t\t    </div>\n\t\t     元\n\t\t  </div>\n\t\t  <div class=\"form-group col-xs-6\">\n\t\t    <label class=\"text-right\" for=\"deduction_other\">其他扣除：</label>\n\t\t    <div class=\"inline-block\">\n\t\t    \t<input type=\"text\" class=\"form-control form-control-small\" required id=\"deduction_other\" name=\"deduction_other\" placeholder=\"\" value=\"{{deduction_other}}\">\n\t\t    </div>\n\t\t\t 元\n\t  \t   </div>\n\t  \t</div>\n\t  <p class=\"text-center wages-tip divider-bottom col-xs-12\">应发工资：<span id=\"wages-txt\" class=\"c-money\">{{actual_wages}}</span></p>\n\t\t<div  class=\"clearfix error-no-margin\">\n\t\t  <div class=\"form-group col-xs-5 state-box\">\n\t\t  \t{{if state == 1 || state == -2}}\n\t\t  \t\t<div class=\"inline-block\">\n\t\t\t\t\t <label class=\"vertical-top\">\n\t\t\t\t\t    <input class=\"icheck\" type=\"radio\" required  name=\"state\" value=\"2\" placeholder=\"\">\n\t\t\t\t\t\t 发放成功\n\t\t\t\t\t</label>\n\t\t\t\t</div>\n\t\t\t\t<label class=\"vertical-top\">\n\t\t\t\t    <input class=\"icheck\" type=\"radio\" required  name=\"state\"  value=\"-2\">\n\t\t\t\t\t 发放失败\n\t\t\t\t</label>\n\n\t\t\t{{else}}\n\t\t\t\t<div class=\"inline-block\">\n\t\t\t\t\t<label class=\"vertical-top\">\n\t\t\t\t\t    <input class=\"icheck\" type=\"radio\" required  name=\"state\" value=\"1\" placeholder=\"\">\n\t\t\t\t\t\t 审核通过\n\t\t\t\t\t</label>\n\t\t\t\t</div>\n\t\t\t\t<label class=\"vertical-top\">\n\t\t\t\t    <input class=\"icheck\" type=\"radio\" required  name=\"state\"  value=\"-1\">\n\t\t\t\t\t 审核失败\n\t\t\t\t</label>\n\t\t\t\t\n\t\t\t{{/if}}\n\t\t  </div>\n\t\t  <div class=\"form-group col-xs-7\">\n\t\t  \t<input type=\"text\" class=\"form-control\" id=\"remark1\" name=\"remark\" placeholder=\"备注\" value=\"\">\n\t\t  </div>\n\n\t\t  <input type=\"hidden\" name=\"base_id\" value=\"{{base_id}}\">\n\t\t  <input type=\"hidden\" name=\"service_order_salary_id\" value=\"{{service_order_salary_id}}\">\n\t\t</div>\n\n<!-- \t\t  <div class=\"text-center btn-footer col-xs-12\">\n\t<input class=\"btn btn-primary\" type=\"submit\" value=\"确定\">\n\t<input class=\"btn btn-gray layui-layer-close\" type=\"button\" value=\"取消\">\n</div> -->\n\t\t\n\t</form>\n";

/***/ },
/* 105 */
/*!*********************************!*\
  !*** ./js/src/page/addAudit.js ***!
  \*********************************/
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($, layer) {// 代发工资
	var template = __webpack_require__(/*! art-template */ 26),
	    idCard =__webpack_require__(/*! modules/idCard */ 106),
	    dateObj =__webpack_require__(/*! modules/date */ 48),
	    validateFn = __webpack_require__(/*! modules/validate */ 32),
	    icheckFn = __webpack_require__(/*! modules/icheck */ 37);
	
	
	module.exports  = {
	    init: function() {
	    	var self = this;
	
	        // 复选框和单选框
	        icheckFn.init();
	
	        self.validate();
	
	        self.bindEvent();
	
	        self.computeAll();
	
	    },
	    bindEvent: function(){
	        var self = this,
	            $card_num = $('#card_num');
	
	         // 获取年龄
	        $card_num.change(function(){
	            var val = this.value,
	                begin = 0,
	                end = 0;
	
	            if(self.validateObj.element('#card_num')){
	                begin = idCard.getBirthday(val),
	                end = $.now();
	                $('#age').html(dateObj.getDiffYear(begin, end));
	            }
	        });
	
	        if($card_num.val() !==''){
	            $card_num.trigger('change');
	        }
	
	        // 社保地址
	        $('body').on('change', '#location', function(){
	            var data = {},
	                val = this.value;
	
	            if(val === ''){
	                //$('#socialSecurity-tel').html(self.initTpl);
	               // icheckFn.init();
	                return;
	            }
	
	            data.location = val;
	            self.getDataByArea(data,function(){
	
	                self.requiredObj.sb = $('[required]','.form-box[data-type="1"]');
	                self.requiredObj.ss = $('[required]','.form-box[data-type="2"]');
	
	                $('.buy-server').trigger('ifChanged');
	
	                self.counterValidate('.security-change');
	                self.counterValidate('.gzj-change');
	
	                // 单选多选 样式
	                icheckFn.init();
	
	            });
	        })
	
	        //查看图片
	        $('body').on('click', '#view-pic', function(){
	            var hrefs = $(this).data('href').split(','),
	                html = '',
	                i = 0,
	                len = hrefs.length;
	            if(!len){
	                return
	            }
	            while(i < len){
	                html += '<a href="'+ hrefs[i] +'" target="_blank" title="点击查看原图"><img src="'+ hrefs[i] +'" style="cursor: zoom-in;max-width:300px;max-height:450px; margin: 10px;"></a>';
	                i++;
	            }
	
	            layer.open({
	                title: '查看图片',
	                area:'700px',
	                btn:[],
	                content:html
	            })
	
	            return false;
	        })
	
	
	        // 挂单
	        $('body').on('ifChecked','.panel-audit .guadan .icheck', function(){
	            var $this = $(this),
	                $closest = $this.closest('.panel-audit'),
	                $success = $('.states-success',$closest);
	
	            $success.iCheck('check');
	        })
	
	        // 审核状态改变
	        $('body').on('ifChecked', '.panel-audit .states:not(.states-success)', function(){
	            var $this = $(this)
	                $closest = $this.closest('.panel-audit'),
	                $guadan = $('.guadan .icheck',$closest);
	
	                $guadan.iCheck('uncheck');
	        })
	
	        // 填写社保时候 社保卡必填
	        $('body').on('ifChanged','.ss-card', function(){
	            var $this = $(this),
	                $cardNum = $('#ss_card_number'),
	                $has = $('#has-ss-card');
	
	             if($has.is(':checked') && $('#security-checkbox').is(':checked')){
	                $cardNum.prop('required', true);
	             } else{
	                $cardNum.prop('required', false).removeClass('error');
	
	                $('#' + $cardNum.attr('id') + '-error.validator-error').remove();
	             }
	             self.computeSbTotal();
	             self.computeAll();
	        })
	
	        // 填写公积金卡时候 公积金卡必填
	        $('body').on('ifChanged','.gzj-card', function(){
	            var $this = $(this),
	                $cardNum = $('#gzj_card_number'),
	                $has = $('#has-gzj-card');
	
	             if($has.is(':checked') && $('#gzj-checkbox').is(':checked')){
	                $cardNum.prop('required', true);
	             } else{
	                $cardNum.prop('required', false).removeClass('error');
	
	                $('#' + $cardNum.attr('id') + '-error.validator-error').remove();
	             }
	
	             self.computeGzjTotal();
	             self.computeAll();
	        })
	
	        // 所需材料
	        $('body').on('click', '.material', function(){
	            var msg = $(this).data('material') || '不需要材料';
	
	            layer.open({
	                title:'所需材料',
	                content: msg
	            })
	        })
	
	        // 计算社保 并渲染
	        $('body').on('change', '.security-change', function(){
	            self.counterValidate('.security-change');
	        }).trigger('change');
	
	        $('body').on('change', '.gzj-change', function(){
	             self.counterValidate('.gzj-change');
	        }).trigger('change');
	
	        // 处理必填增删
	        $('body').on('ifChanged','.buy-server', function(){
	            var $this = $(this),
	                type = $this.closest('.form-box').data('type');
	
	            if($this.is(':checked')){
	                self.addRequired(type);
	            } else{
	                self.removeRequired(type);
	            }
	            $('.ss-card,.gzj-card').trigger('ifChanged');
	
	        })
	
	        $('.buy-server').trigger('ifChanged');
	
	        /*$('body').on('change','#sb_year', function(){
	            var defaultHtml = self.createOptions('','请选择'),
	                index = $(this).find('option:selected').index(),
	                $sb_month = $('#sb_month'),
	                months = self.dates.sb.months;
	
	            if(this.value === ''){
	                $sb_month.html(defaultHtml);
	            } else{
	                $sb_month.html(defaultHtml + self.createOptionsByArr(months[index-1]))
	            }
	
	        })*/
	
	    },
	    // 起缴时间
	    dates:{
	        sb:{},
	        gzj:{}
	    },
	    // 默认起缴时间
	    defaultDate:{
	        sb_year: $('#sb_year').val(),
	        sb_month: $('#sb_month').val(),
	        gzj_year: $('#gzj_year').val(),
	        gzj_month: $('#gzj_month').val()
	    },
	    createOptions: function(value, txt){
	        return '<option value="'+ value +'">'+ txt +'</option>';
	    },
	    createOptionsByArr: function(arr){
	        var self = this,
	            html = '',
	            i = 0,
	            len = arr.length;
	
	        if(typeof arr[0] === 'object'){
	            for(;i<len;i++){
	
	                html += self.createOptions(arr[i].value,arr[i].text);
	            }
	        } else{
	            for(;i<len;i++){
	
	                html += self.createOptions(arr[i],arr[i]);
	            }
	        }
	
	        return html;
	    },
	    initTpl: $('#socialSecurity-tel').html(),
	    counter:{
	        sb:{
	            sum: parseFloat($('#sb-total').text()) || 0,//小计
	            total: parseFloat($('#total').text()) || 0,//总额
	            pro_cost:parseFloat($('#sb-pro_cost').text()) || 0// 工本费
	        },
	        ss: {
	            sum: parseFloat($('#gzj-total').text()),
	            total: (function(){
	                var total = parseFloat($('#gzj-total').text()) || 0;
	
	                if($('#no-gzj-card').is(':checked')){
	                    total -= parseFloat($('#gzj-pro_cost').text()) || 0;
	                }
	
	                return total;
	            })(),
	            pro_cost: parseFloat($('#gzj-pro_cost').text()) || 0
	        }
	    },
	    ajaxCache:{},
	    /**
	     * 计算总额前校验
	     * @param  {string|jquery} className 选择器
	     * @return {boolen}           返回结果 true 或者 false
	     */
	    counterValidate: function(className){
	        var self = this,
	            formArr = self.serializeArray('.security-change,.gzj-change'),
	            $el = $(className),
	            flag = true,
	            i = 0,
	            len = formArr.length,
	            data = {},
	            sb_type_id_arr = [],
	            gzj_type_id_arr = [];
	
	        for(; i < len; i++){
	            /*if(formArr[i].value === ''){
	                return false;
	            }*/
	            // 参数矫正
	            switch (formArr[i].name) {
	                case 'sb_name[]':
	
	                    sb_type_id_arr.push(formArr[i].value);
	
	                    break;
	                case 'pro_cost':
	                    data.sb_amount =  formArr[i].value;
	                    break;
	                case 'gzj_name[]':
	                    gzj_type_id_arr.push(formArr[i].value);
	                    break;
	                case 'gzj_pro_cost':
	                    data.gzj_amount =  formArr[i].value;
	                    break;
	                case 'gzj_pro_cost_com':
	                    data.firme =  formArr[i].value;
	                    break;
	                case 'gzj_pro_cost_per':
	                    data.member =  formArr[i].value;
	                    break;
	                default:
	                    break;
	            }
	
	        }
	        // 必须要由大到小排序
	        data.sb_type_id = sb_type_id_arr.sort(function(a,b){return a<b?1:-1}).join('|');
	        data.gzj_type_id = gzj_type_id_arr.sort(function(a,b){return a<b?1:-1}).join('|');
	
	        data.temple_id = $('#template_id').val();
	        data.sb_amount=$('#sb_amount').val();
	        data.gzj_amount=$('#gzj_amount').val();
	        data.sb_month=self.monthDiffer($('#sb_month').val());
	        data.gzj_month=self.monthDiffer($('#gzj_month').val());
	        data.member=$('#member').val();
	        data.firme=$('#firme').val();
	
	        // 是否通过验证
	        $el.each(function(){
	            return flag = self.validateObj.element(this);
	        })
	
	        if(!flag) return;
	
	        data.temple_id = $('#template_id').val();
	
	        if($el.hasClass('gzj-change')){
	            self.computeGzj(data);
	        } else{
	            self.computeSecurity(data);
	        }
	    },
	    /**
	     * 添加必填
	     * @param {string ｜ number} type 1为社保 2为公积金
	     */
	    addRequired: function(type){
	        var self = this;
	
	        if((type-0) === 1){
	            self.requiredObj.sb.prop('required', true);
	        } else{
	            self.requiredObj.ss.prop('required', true);
	        }
	    },
	    /**
	     * 删除必填
	     * @param {string ｜ number} type 1为社保 2为公积金
	     */
	    removeRequired: function(type){
	        var self = this;
	
	        if((type-0) === 1){
	            self.requiredObj.sb.prop('required', false).removeClass('error');
	        } else{
	            self.requiredObj.ss.prop('required', false).removeClass('error');
	        }
	
	        $('span.error.validator-error','.form-box[data-type="' + type+'"]').remove();
	
	    },
	    // 序列化 单个元素表单
	    serializeArray: function(inputs){
	        var arr = [];
	
	        $(inputs).each(function(){
	            var name = this.name,
	                val = this.value;
	
	            arr.push({
	                name: name,
	                value: val
	            })
	        })
	
	        return arr;
	    },
	    // 序列化 单个元素表单 转化成对象形式
	    serializeObj:function(inputs){
	        var obj = {};
	
	        $(inputs).each(function(){
	            var name = this.name,
	                val = this.value;
	            if(name.indexOf('[]') !== -1){
	                obj[name] ? obj[name].push(val) : obj[name] = [val];
	            } else{
	                obj[name] = val
	            }
	            
	        })
	
	        return obj;
	    },
	    requiredObj: {
	        sb: $('[required]','.form-box[data-type="1"]'),//社保
	        ss:  $('[required]','.form-box[data-type="2"]')// 公积金
	    },
	    getYears: function(time){
	
	        var date = new Date(time),
	            year = date.getFullYear(),
	            month = date.getMonth() + 1,
	            years = [],
	            months = [],
	            step = 5,
	            down = month - step
	
	        if(down < 0){
	            var arr = [],
	                arr2 = [],
	                j = down;
	
	            years = [year-1,year];
	
	            while(j !== 0){
	                j++;
	                arr.push(12+j);
	            }
	
	            for(var i = 1; i <= month; i++){
	
	                arr2.push(i)
	            }
	
	            months = [arr,arr2]
	        }else{
	            years = [year];
	            for(var i = 1; i <= down; i++){
	                months.push(i)
	            }
	        }
	
	        return {
	            years: years,
	            months: months
	        };
	    },
	    // 根据地区获取数据
	    getDataByArea: function(data, cb){
	        var self = this,
	            url = '/Index-getCityClass';
	        // 取消之前的ajax
	        self.abort(url);
	        $.ajax({
	            type: 'post',
	            dataType: 'json',
	            data: data,
	            url: url,
	            success: function(json){
	
	                var data = json,
	                    status = 0,
	                    html = '';
	
	                //data.sb_rule.years = self.getYears($.now());
	                if(!data.sb_rule || !data.gzj_rule){
	                    return
	                }
	
	                /*data.sb_rule.dates = self.dates.sb = self.getYears($.now());
	                data.gzj_rule.dates = self.dates.gzj = self.getYears(new Date('2012-10-2'));*/
	
	                data = $.extend(self.defaultDate,data,self.serializeObj('[type="hidden"]'));
	     
	                html = template.render(__webpack_require__(/*! tpl/security.vue */ 107).template)(data);
	
	                if(status !== 0){
	                    layer.alert(json.msg);
	                    return;
	                }
	
	                $('#socialSecurity-tel').html(html);
	
	                $('#template_id').val(data.sb_classify.classify[0].template_id);
	
	                //select 加上默认值
	                self.changeVal(data);
	
	                self.computeScaleBoth();
	                // 重置计数
	                self.resetCounter();
	
	                // 记录工本费
	                self.counter.sb.pro_cost = data.sb_rule.pro_cost - 0;
	                self.counter.ss.pro_cost = data.gzj_rule.pro_cost - 0;
	
	                if(typeof cb === 'function'){
	                    cb();
	                }
	
	                validateFn.modifyRules(self.validateObj, {
	                    rules: {
	                        gzj_pro_cost_com: {
	                            rangeScale: data.gzj_rule.company
	                        },
	                        gzj_pro_cost_per: {
	                            rangeScale: data.gzj_rule.person
	                        }
	                    },
	                    messages: {
	                        gzj_pro_cost_com: {
	                            rangeScale: '可输入范围为' + data.gzj_rule.company
	                        },
	                        gzj_pro_cost_per: {
	                            rangeScale: '可输入范围为' + data.gzj_rule.person
	                        }
	                    }
	                })
	            },
	            error:function() {
	                //默认取消弹服务繁忙
	            }
	        }).complete(function(){
	            self.resetAjaxCache(url)
	        })
	    },
	    validateObj: null,
	    sendAudit: function(url,data, cb){
	        $.ajax({
	            type: 'post',
	            data: data,
	            url: url,
	            dataType: 'json',
	            success: function(json){
	                var data = json.data,
	                    stauts = json.status - 0;
	                if(stauts === 0){
	
	                    if(typeof cb === 'function'){
	                        cb()
	                    }
	                } else{
	                    layer.alert(json.msg)
	                }
	            },
	            error:function() {
	                //默认取消弹服务繁忙
	            }
	
	        })
	    },
	    validate: function(){
	        var self = this;
	
	        self.validateObj = $('#add-audit-form').validate({
	            submitHandler: function(form){
	
	                if($('.buy-server:checked').length){
	
	                    // 如果购买社保被勾选并且 返回的sb规则为null
	                    if(!self.retComputeData.sb_result && $('#security-checkbox').is(':checked')){
	                        layer.alert('选择社保模板不存在')
	                        return false;
	                    }
	
	                    // 如果购买公积金被勾选并且 返回的gzj规则为null
	                    if(!self.retComputeData.gzj_result && $('#fund-checkbox').is(':checked')){
	                        layer.alert('选择公积金模板不存在')
	                        return false;
	                    }
	
	                    var $form = $(form),
	                        url = 'Service-Order-audit',
	                        data = $form.serializeArray();
	
	                    self.sendAudit(url, data,function(){
	                        layer.msg('审核成功', {
	                            end: function(){
	                                location.reload();
	                            }
	                        })
	                    });
	                } else{
	                    layer.alert('购买公积金和购买社保至少选一项')
	                }
	            return false
	            },
	            rules: {
	                card_num:{
	                    
	                    isIdCard: true
	                },
	                mobile: {
	                    istelephone: true
	                },
	                ss_card_number:{
	                    SSN: true
	                },
	                gzj_card_number:{
	                    SSN: true
	                },
	                pro_cost:{
	                    number: true
	                },
	                gzj_pro_cost: {
	                    number: true
	                },
	                gzj_pro_cost_com: {
	                    number: true
	                },
	                gzj_pro_cost_per: {
	                    number: true
	                },
	                gzj_pro_cost_com: {
	                    rangeScale: $('#comp-scale').text()
	                },
	                gzj_pro_cost_per: {
	                    rangeScale: $('#per-scale').text()
	                }
	
	            },
	
	            messages:{
	                gzj_pro_cost_com: {
	                    rangeScale: '可输入范围为' + $('#comp-scale').text()
	                },
	                gzj_pro_cost_per: {
	                    rangeScale: '可输入范围为' + $('#per-scale').text()
	                },
	                card_num: {
	                    required: '请输入身份证号'
	                },
	                user_name: {
	                    required: '请输入姓名'
	                },
	                sex: {
	                    required: '请选择性别'
	                },
	                mobile: {
	                    required: '请输入手机号码'
	                },
	                location: {
	                    required: '请选择参保地'
	                },
	                ss_card_number:{
	                    required: '请输入卡号'
	                },
	                gzj_card_number: {
	                    required: '请输入卡号'
	                },
	                pro_cost: {
	                    required: '请输入社保基数'
	                },
	                sb_year: {
	                    required: '请选择起缴年份'
	                },
	                sb_month: {
	                    required: '请选择起缴月份'
	                },
	                sb_state: {
	                    required: '请选择审核状态'
	                },
	                gzj_pro_cost: {
	                    required: '请输入公积金基数'
	                },
	                gzj_year: {
	                    required: '请选择起缴年份'
	                },
	                gzj_month: {
	                    required: '请选择起缴月份'
	                },
	                gzj_pro_cost_com: {
	                    required: '请输入单位缴纳比例'
	                },
	                gzj_pro_cost_per: {
	                    required: '请输入个人缴纳比例'
	                },
	                gzj_state: {
	                    required: '请选择审核状态'
	                }
	            }
	        })
	    },
	    /* 计算时返回的数据 
	     * 用于处理 规则为null的时候 
	     * 如果勾选购买保险 阻止提交
	     * 第一次可以被提交
	     **/
	    retComputeData: {
	        sb_result: {},
	        gzj_result: {}
	    } ,
	    //发送计算请求 （公积金）
	    sendCompute: function(data,type){
	        var self = this,
	            url = '/Index-calculation',
	            urlType = url+type;
	        self.abort(urlType);
	
	        return $.ajax({
	            type: 'post',
	            dataType: 'json',
	            data: data,
	            url: url,
	            beforeSend: function(ajax, opts){
	                self.ajaxCache[urlType] = ajax;
	            },
	            success: function(ret){
	                self.retComputeData = ret;
	            },
	            error: function() {
	                //默认取消弹服务繁忙
	            }
	        }).complete(function(){
	            self.resetAjaxCache(urlType);
	        })
	    },
	    // 计算社保总额
	    computeSecurity: function(data){
	        var self = this;
	
	        self.sendCompute(data,1)
	            .success(function(json){
	                var $sb_table=$("#sb_table");
	                if(!json.sb_result) {
	                    $sb_table.hide();
	
	                    $('#sb-pro_cost').html(0);
	                    self.resetSbCounter();
	                    self.computeSbTotal();
	                    self.computeAll();
	                    return;
	                }
	
	                var data = json.sb_result.data,
	                    items = data.items,
	                    totalCompany = totalPerson = 0,
	                    $sumCompany = $('.sum-company'),
	                    $sumPerson = $('.sum-person'),
	                    $sumCompScale = $('.sum-company-scale'),
	                    $sumPerScale = $('.sum-person-scale'),
	                    $sum = $('.sum'),
	                    status = json.sb_result.state - 0,
	                    allTotal = 0;
	
	                if(status !== 0){
	                    return false;
	                }
	
	                var compTotalScale = perTotalScale = 0;
	
	                self.securityTable(data);
	
	               /* $(items).each(function(index, el) {
	                    var perData = items[index],
	                        comSum = perData.company.sum,
	                        perSum = perData.person.sum;
	
	                    $sumCompScale.eq(index).html(perData.company.scale + '+' + perData.company.fixedSum);
	                    $sumPerScale.eq(index).html(perData.person.scale + '+' + perData.person.fixedSum);
	                    $sumCompany.eq(index).html(comSum);
	                    $sumPerson.eq(index).html(perSum);
	                    $sum.eq(index).html(perData.total);
	                })*/
	
	
	                allTotal = (data.company - 0) + (data.person - 0)
	
	                self.counter.sb.pro_cost = data.pro_cost - 0 || 0;
	
	                $('#total-sum-company').html(data.company);
	                $('#total-sum-person').html(data.person);
	                $('#total').html(allTotal.toFixed(2));
	                $('#sb-pro_cost').html(self.counter.sb.pro_cost);
	
	                $('#czj-total').html(((json.czj_result && json.czj_result.price) || 0) + '元/月')
	
	                self.computeScaleBoth();
	                // 记录社保总额
	                self.counter.sb.total = allTotal;
	                
	
	                self.computeSbTotal();
	                self.computeAll();
	
	                self.computeScale($('.sum-company-scale'));
	
	                $sb_table.show();
	
	            })
	    },
	    computeScaleBoth: function(){
	        var self = this;
	
	        $('#total-sum-company-scale').html(self.computeScale($('.sum-company-scale')));
	        $('#total-sum-person-scale').html(self.computeScale($('.sum-person-scale')));
	    },
	    computeScale: function($el){
	        var scaleSum = scaleFixedSum = 0;
	
	        $el.each(function(){
	            var $this = $(this),
	                val = $this.text(),
	                arr = val.split('+');
	
	            for(var i = 0, len = arr.length; i < len; i++){
	                if(arr[i].indexOf('%') !== -1){
	                    scaleSum += (arr[i].replace('%', '') - 0)
	                } else{
	                    scaleFixedSum += (arr[i]-0)
	                }
	            }
	
	        })
	        return scaleSum.toFixed(2) + '%+' + scaleFixedSum.toFixed(2)
	    },
	    // 取消ajax
	    abort: function(url){
	
	        var cacheObj = this.ajaxCache[url];
	
	        if(cacheObj){
	            cacheObj.abort();
	        }
	    },
	    resetAjaxCache: function (url) {
	        this.ajaxCache[url] = false;
	    },
	    // 计算公积金总额
	    computeGzj: function(data){
	        var self = this;
	
	
	        self.sendCompute(data,2)
	        .success(function(json){
	            if(!json.gzj_result) {
	
	                $('#gzj-pro_cost').html(0);
	                self.resetGzjCounter();
	                self.computeGzjTotal();
	                self.computeAll();
	                return;
	            }
	
	            var data = json.gzj_result.data,
	                status = json.gzj_result.state - 0;
	
	            if(status === 0){
	                self.counter.ss.total = data.person + data.company;
	
	                self.counter.ss.pro_cost = data.pro_cost - 0 || 0;
	
	                $('#gzj-pro_cost').html(self.counter.ss.pro_cost);
	
	                self.computeGzjTotal();
	                self.computeAll();
	            }
	        })
	
	    },
	    resetSbCounter: function(){
	        this.counter.sb = {
	            sum: 0,//小计
	            total: 0,//总额
	            pro_cost:0// 工本费
	        }
	    }, 
	    resetGzjCounter: function(){
	        this.counter.ss = {
	            sum: 0,//小计
	            total: 0,
	            pro_cost:0,
	            czj: 0
	        }
	    },
	    resetCounter: function(){
	        this.resetSbCounter();
	        this.resetGzjCounter();
	    },
	    // 计算社保小计
	    computeSbTotal: function(){
	        var total = this.counter.sb.total;
	
	        if($('#no-ss-card').is(':checked')){
	            total += this.counter.sb.pro_cost;
	        }
	
	        this.counter.sb.sum = total;
	
	        $('#sb-total').html(total.toFixed(2) + '元/月')
	    },
	    computeGzjTotal: function(){
	        var total = this.counter.ss.total;
	
	        if($('#no-gzj-card').is(':checked')){
	
	            total += this.counter.ss.pro_cost;
	        }
	
	        this.counter.ss.sum = total;
	
	        $('#gzj-total').html(total.toFixed(2) + '元/月')
	    },
	    computeAll: function(){
	        var total = 0;
	
	        if($('#security-checkbox').is(':checked')){
	            total += this.counter.sb.sum;
	        }
	
	        if($('#fund-checkbox').is(':checked')){
	            total += this.counter.ss.sum ;
	        }
	
	        total += (parseFloat($('#czj-total').text() ) || 0);
	
	        $('#all-total').html(total.toFixed(2) + '元/月');
	    },
	    //select 加上默认值
	    changeVal:function(data){
	        var sb_change_valArr=[],
	            gzj_change_valArr=[],
	            sb_classify=data.sb_classify.classify_mixed,
	            gzj_classify=data.gzj_classify.classify_mixed;
	        //社保
	        if(sb_classify != null){
	            sb_change_valArr=sb_classify.split("|");
	        }
	        //公职金
	        if(gzj_classify != null){
	            gzj_change_valArr=gzj_classify.split("|");
	        }
	
	        for(var i=0;sb_change_valArr.length > i;i++){
	            $(".security-change").find('[value="'+sb_change_valArr[i]+'"]').attr("selected",true);
	        }
	
	        for(var i=0;gzj_change_valArr.length > i;i++){
	            $(".gzj-change").find('[value="'+gzj_change_valArr[i]+'"]').attr("selected",true);
	        }
	    },
	    securityTable:function(data){
	        var html = template.render(__webpack_require__(/*! tpl/securityTable.vue */ 109).template)(data);
	        $("#sb_table").html(html);
	    },
	    monthDiffer:function(){
	        /*var month=this.orderDate;
	        if(month == ""){
	            return 1;
	        }
	        return (orderDate-0)-(month-0)+1;*/
	        return 1;
	    }
	
	}
	
	
	
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! jquery */ 1), __webpack_require__(/*! layer */ 17)))

/***/ },
/* 106 */
/*!**********************************!*\
  !*** ./js/src/modules/idCard.js ***!
  \**********************************/
/***/ function(module, exports) {

	
	module.exports = {
		// 验证身份证的合法性的正则
		pattern: new RegExp(/(^\d{15}$)|(^\d{17}(\d|x|X)$)/i),
		// 验证长度与格式规范性的正则
		pattern2: new RegExp(/^([110|120|130|131]\d[1-9]\d{4}|[2-9]\d{7})((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/),
		//身份证的地区代码对照  
		city:{
			11 : "北京",
			12 : "天津",
			13 : "河北",
			14 : "山西",
			15 : "内蒙古",
			21 : "辽宁",
			22 : "吉林",
			23 : "黑龙江",
			31 : "上海",
			32 : "江苏",
			33 : "浙江",
			34 : "安徽",
			35 : "福建",
			36 : "江西",
			37 : "山东",
			41 : "河南",
			42 : "湖北",
			43 : "湖南",
			44 : "广东",
			45 : "广西",
			46 : "海南",
			50 : "重庆",
			51 : "四川",
			52 : "贵州",
			53 : "云南",
			54 : "西藏",
			61 : "陕西",
			62 : "甘肃",
			63 : "青海",
			64 : "宁夏",
			65 : "新疆",
			71 : "台湾",
			81 : "香港",
			82 : "澳门",
			91 : "国外"
		},
		getBirthday: function(person_id){
			var self = this,
				birthday;
	
			if (self.pattern.exec(person_id)) {
	
				if(self.pattern2.exec(person_id)){
					// 获取15位证件号中的出生日期并转为正常日期       
					birthday = "19" + person_id.substring(6, 8)
							 + "-" + person_id.substring(8, 10)
							 + "-" + person_id.substring(10, 12);
				} else{
					person_id = person_id.replace(/x|X$/i, "a");
	
					// 获取18位证件号中的出生日期  
					birthday = person_id.substring(6, 10) + "-"
							 + person_id.substring(10, 12) + "-"
							 + person_id.substring(12, 14);
				}
			}
	
			return birthday;
		},
		//检测证件地区的合法性
		isCity: function(person_id){
			if (this.city[parseInt(person_id.substring(0, 2))] == null) {                            
				return false;
			}
			return true;
		},
		// 校验18位身份证号码的合法性
		isLength: function(person_id){
			var sum = 0;
	
			for ( var i = 17; i >= 0; i--) {
				sum += (Math.pow(2, i) % 11) * parseInt(person_id.charAt(17 - i), 11);
			}
			if (sum % 11 != 1) {
				return false;
			}
		},
		// 生日合法性
		isBirthday: function(person_id){
			var self = this,
				birthday = this.getBirthday(person_id),
				dateStr = new Date(birthday.replace(/-/g, "/"));
	
			if (birthday != (dateStr.getFullYear() + "-"
				+ self.appendZore(dateStr.getMonth() + 1)
				+ "-" + self.appendZore(dateStr.getDate()))) {
				return false;
			}
		},
		appendZore:function(temp){
			if(temp<10) {  
		        return "0"+temp;  
		    }  
		    else {  
		        return temp;  
		    }  
		}
	}


/***/ },
/* 107 */
/*!*********************************!*\
  !*** ./js/src/tpl/security.vue ***!
  \*********************************/
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_template__ = __webpack_require__(/*! !tpl-html-loader!./../../../../../../~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./security.vue */ 108)
	module.exports = __vue_script__ || {}
	if (__vue_template__) {
	(typeof module.exports === "function" ? (module.exports.options || (module.exports.options = {})) : module.exports).template = __vue_template__
	}
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), false)
	  if (!hotAPI.compatible) return
	  var id = "_v-3d210d6d/security.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 108 */
/*!**********************************************************************************************************************************!*\
  !*** F:/item/zbw/~/tpl-html-loader!F:/item/zbw/~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./js/src/tpl/security.vue ***!
  \**********************************************************************************************************************************/
/***/ function(module, exports) {

	module.exports = "\t\n\t<div id=\"security-info\" class=\"audit-form-title clearfix2 \">\n\t参保信息 <i class=\"icon icon-info\"></i>\n\t</div>\n\t<div class=\"form-box\" data-type=\"1\">\n\t\t<div class=\"clearfix2 form-group \">\n\t\t\t<label>\n\t\t\t<input id=\"security-checkbox\" class=\"buy-server icheck\" type=\"checkbox\" name=\"sb_buy\" value=\"1\"> 购买社保\n\t\t\t</label>\n\t\t</div>\n\t\t{{each sb_classify.classify}}\n\t\t<div class=\"form-group col-xs-12\">\n\t        <label class=\"label-left vertical-top\">\n\t        {{$value.name}}：\n\t        </label>\n\t        <div class=\"inline-block\">\n\t       \t<select class=\"base-size security-change\" name=\"sb_name[]\" >\n\t       \t\t<option value=\"\">请选择</option>\n\t       \t\t{{each $value.child}}\n\t       \t\t\t<option value=\"{{$value.id}}\">{{$value.name}}</option>\n\t       \t\t{{/each}}\n\t       \t</select>\n\t       \t</div>\n\t    </div>\n\t    {{/each}}\n\n\t    <div class=\"form-group col-xs-12\">\n\t        <label class=\"label-left vertical-top\">\n\t        \t<span class=\"c-required\">*</span>社保基数：\n\t        </label>\n\t        <div class=\"inline-block\">\n\t       \t\t<input type=\"text\" class=\"form-control form-control-middle security-change\" required id=\"sb_amount\" name=\"pro_cost\" placeholder=\"请输入社保基数\" value=\"{{sb_rule.min}}\">\n\t       \t</div>\n\t       \t<span class=\"gutter vertical-top\">\n\t       \t\t基数范围{{sb_rule.min}}到{{sb_rule.max}}\n\t       \t</span>\n\t    </div>\n\n\t    <div class=\"form-group col-xs-12\">\n\t        <label class=\"label-left vertical-top\">\n\t        \t<span class=\"c-required\">*</span>起缴时间：\n\t        </label>\n\t\t\t\t<div class=\"inline-block vertical-top\">\n\n\t           \t<select id=\"sb_year\" class=\"base-size\" required name=\"sb_year\">\n\t           \t\t<option value=\"{{sb_year}}\">{{sb_year}}</option>\n\t           \t</select>\n\t           \t年\n\t\t\t</div>\n\t\t\t<div class=\"inline-block gutter-x vertical-top\">\n\t\t\t\t<select id=\"sb_month\"  class=\"base-size\" required name=\"sb_month\">\n\t           \t\t<option value=\"{{sb_month}}\">{{sb_month}}</option>\n\t           \t</select>\n\t           \t月\n\t\t\t</div>\n\t       \t\n\t    </div>\n\n\t    <div class=\"form-group col-xs-12 table-responsive\" id=\"sb_table\">\n\t\t    <table class=\"table table-bordered table-border text-center\" style=\"width: 674px;\">\n\t\t    \t<thead>\n\t\t    \t\t<tr>\n\t\t                <th rowspan=\"2\" width=\"16%\">\n\t\t                    险种\n\t\t                </th>\n\t\t                <th colspan=\"2\">\n\t\t                \t单位缴纳\n\t\t                </th>\n\t\t                <th colspan=\"2\">\n\t\t                \t个人缴纳\n\t\t                </th>\n\t\t                <th rowspan=\"2\">合计金额</th>\n\n\t\t            </tr>\n\t\t            <tr>\n\t\t                <th>比例</th>\n\t\t                <th>\n\t\t \t\t\t\t\t金额\n\t\t                </td>\n\t\t                <th>比例</th>\n\t\t                <th>\n\t\t \t\t\t\t\t金额\n\t\t                </th>\n\t\t            </tr>\n\t\t    \t</thead>\n\t\t        <tbody>\n\n\t\t            {{each sb_rule.items}}\n\t\t            <tr>\n\t\t                <td >\n\t\t                    {{$value.name}}\n\t\t                </td>\n\t\t                <td class=\"sum-company-scale\">\n\t\t                \t{{$value.rules.company}}\n\n\t\t                </td>\n\t\t                <td class=\"sum-company\">/</td>\n\t\t                <td class=\"sum-person-scale\">\n\t\t                \t{{$value.rules.person}}\n\t\t                </td>\n\t\t                <td class=\"sum-person\">/</td>\n\t\t                <td class=\"sum\">\n\t\t                \t/\n\t\t                </td>\n\t\t            </tr>\n\t\t        \t{{/each}}\n\t\t        \t<tr>\n\t\t                <td>\n\t\t                   合计\n\t\t                </td>\n\t\t                <td id=\"total-sum-company-scale\">\n\t\t                \t/\n\t\t                </td>\n\t\t                <td id=\"total-sum-company\">/</td>\n\t\t                <td id=\"total-sum-person-scale\">\n\t\t                \t/\n\t\t                </td>\n\t\t                <td id=\"total-sum-person\">/</td>\n\n\t\t                <td id=\"total\">\n\t\t                \t/\n\t\t                </td>\n\t\t            </tr>\n\t\t        </tbody>\n\t\t    </table>\n\t\t</div>\n\t\t\n\t\t<div class=\"form-group col-xs-12\">\n\t\t\t<label class=\"label-left vertical-top\">\n\t\t\t<input id=\"has-ss-card\" class=\"ss-card icheck\" type=\"radio\" name=\"ss_card\" value=\"1\" required checked>\n\t\t\t有社保卡\n\t\t\t</label>\n\t\t\t<label class=\"gutter-left\">卡号：</label>\n\t\t\t<div class=\"inline-block\">\n\t\t\t\t<input id=\"ss_card_number\" type=\"text\" required class=\"form-control form-control-middle\"  name=\"ss_card_number\" placeholder=\"请输入卡号\" value=\"\">\n\t\t\t</div>\n\t\t</div>\n\t\t<div class=\"form-group col-xs-12\">\n\t\t\t<label class=\"label-left\">\n\t\t\t<input id=\"no-ss-card\" type=\"radio\" class=\"ss-card icheck\" name=\"ss_card\" value=\"2\" required>\n\t\t\t无社保卡\n\t\t\t</label>\n\t\t\t<label class=\"gutter-x\">工本费：<span id=\"sb-pro_cost\">{{sb_rule.pro_cost}}</span></label>\n\n\t\t\t<a class=\"material c-primary\" href=\"javascript:;\" data-material=\"{{sb_rule.material}}\">所需材料</a>\n\t\t</div>\n\n\t\t<div class=\"text-right clearfix2 c-money sum-total\">小计： <span id=\"sb-total\" class=\"f-bold\">0元/月</span></div>\n\n\t\t <div class=\"panel-audit clearfix2\">\n\t    \t<div class=\"title\">\n\t    \t\t{{if sb_updata_type_new == 2}}\n\t    \t\t社保报减审核\n\t    \t\t{{else}}\n\t    \t\t社保报增审核\n\t    \t\t{{/if}}\n\t    \t</div>\n\t    \t<div class=\"form-group col-xs-12\">\n\t    \t\t<label>\n\t    \t\t\t<input type=\"radio\" name=\"sb_state\" value=\"0\" class=\"sb_state states icheck\" required>\n\t    \t\t\t审核中\n\t    \t\t</label>\n\t    \t</div>\n\t    \t<div class=\"form-group col-xs-12\">\n\t    \t\t<label>\n\t    \t\t\t<input  type=\"radio\" name=\"sb_state\" value=\"1\" class=\"sb_state states states-success icheck\" required>\n\t    \t\t\t审核通过\n\n\t    \t\t</label>\n\t    \t</div>\n\t    \t<div class=\"form-group col-xs-12\">\n\t    \t\t<label class=\"guadan\">\n\t    \t\t\t<input class=\"icheck\" type=\"checkbox\" value=\"-1\" name=\"sb_state_guadan\" >\n\t    \t\t\t挂起至下一订单\n\t    \t\t</label>\n\t    \t</div>\n\t    \t<div class=\"form-group col-xs-12\">\n\t    \t\t<label>\n\t    \t\t\t<input type=\"radio\" name=\"sb_state\" value=\"-1\" class=\"sb_state states icheck\" required>\n\t    \t\t\t审核失败\n\t    \t\t</label>\n\t    \t</div>\n\t    \t<div class=\"form-group col-xs-12\">\n\t    \t\t<label>\n\t    \t\t\t备注\n\t    \t\t\t<input class=\"input-remark gutter-left form-control\" type=\"text\" name=\"sb_remark\" placeholder=\"反馈\">\n\t    \t\t</label>\n\t    \t</div>\n\t    </div>\n\t</div>\n\t<div class=\"form-box\" data-type=\"2\" style=\"margin-top:80px;\">\n\t\t<div class=\"clearfix2 form-group \">\n\t\t\t<label>\n\t\t\t<input class=\"buy-server icheck\" id=\"fund-checkbox\" type=\"checkbox\" name=\"gzj_buy\" value=\"1\"> 购买公积金\n\t\t\t</label>\n\t\t</div>\n\t\t{{each gzj_classify.classify}}\n\t\t<div class=\"form-group col-xs-12\">\n\t        <label class=\"label-left vertical-top\">\n\t        {{$value.name}}：\n\t        </label>\n\t        <div class=\"inline-block\">\n\t       \t<select class=\"base-size gzj-change\" name=\"gzj_name[]\">\n\t       \t\t<option value=\"\">请选择</option>\n\t       \t\t{{each $value.child}}\n\t       \t\t\t<option value=\"{{$value.id}}\">{{$value.name}}</option>\n\t       \t\t{{/each}}\n\t       \t</select>\n\t       \t</div>\n\t    </div>\n\t    {{/each}}\n\t\t<div class=\"form-group col-xs-12\">\n\t        <label>\n\t        \t<span class=\"c-required\">*</span>公积金基数：\n\t        </label>\n\t        <div class=\"inline-block\">\n\t       \t\t<input type=\"text\" class=\"form-control form-control-middle gzj-change\" required id=\"gzj_amount\"  name=\"gzj_pro_cost\" placeholder=\"请输入公积金基数\" value=\"{{gzj_rule.min}}\">\n\t       \t</div>\n\t       \t<span class=\"gutter\">\n\t       \t\t基数范围{{gzj_rule.min}}到{{gzj_rule.max}}\n\t       \t</span>\n\t    </div>\n\n\t    <div class=\"form-group col-xs-12\">\n\t        <label class=\"label-left\">\n\t        \t<span class=\"c-required\">*</span>起缴时间：\n\t        </label>\n\t\t\t\t<div class=\"inline-block\">\n\t           \t<select id=\"gzj_year\" class=\"base-size\" required name=\"gzj_year\">\n\t           \t\t<option value=\"{{gzj_year}}\">{{gzj_year}}</option>\n\n\t           \t</select>\n\t           \t年\n\t\t\t</div>\n\t\t\t<div class=\"inline-block gutter-x\">\n\t\t\t\t<select id=\"gzj_month\" class=\"base-size\" required name=\"gzj_month\">\n\t           \t\t<option value=\"{{gzj_month}}\">{{gzj_month}}</option>\n\t           \t</select>\n\t           \t月\n\t\t\t</div>\n\t    </div>\n\t\t <div class=\"form-group col-xs-12\">\n\t        <label>\n\t        \t<span class=\"c-required\">*</span>单位缴纳比例：\n\t        </label>\n\t        <div class=\"inline-block\">\n\t       \t\t<input type=\"text\" class=\"form-control gzj-change\" required id=\"firme\"  name=\"gzj_pro_cost_com\" placeholder=\"请输入单位缴纳比例\" value=\"\"> %\n\t       \t</div>\n\t       \t<span class=\"gutter vertical-top\">\n\t       \t\t比例为<span id=\"comp-scale\">{{gzj_rule.company}}</span>\n\t       \t</span>\n\t    </div>\n\t    <div class=\"form-group col-xs-12\">\n\t        <label>\n\t        \t<span class=\"c-required\">*</span>个人缴纳比例：\n\t        </label>\n\t        <div class=\"inline-block\">\n\t       \t\t<input type=\"text\" class=\"form-control gzj-change\" required id=\"member\"  name=\"gzj_pro_cost_per\" placeholder=\"请输入个人缴纳比例\" value=\"\"> %\n\t       \t</div>\n\t       \t<span class=\"gutter vertical-top\">\n\t       \t\t比例为<span id=\"per-scale\">{{gzj_rule.person}}</span>\n\t       \t</span>\n\t    </div>\n\t    <div class=\"form-group col-xs-12\">\n\t\t\t<label class=\"vertical-top\">\n\t\t\t<input id=\"has-gzj-card\" class=\"gzj-card icheck\" type=\"radio\" name=\"gzj_card\" value=\"1\" required checked>\n\t\t\t有公积金卡\n\t\t\t</label>\n\t\t\t<label class=\"gutter-left\">卡号：</label>\n\t\t\t<div class=\"inline-block\">\n\t\t\t\t<input id=\"gzj_card_number\" type=\"text\" required class=\"form-control form-control-middle\"  name=\"gzj_card_number\" placeholder=\"请输入卡号\" value=\"\">\n\t\t\t</div>\n\t\t</div>\n\t\t<div class=\"form-group col-xs-12\">\n\t\t\t<label class=\"vertical-top\">\n\t\t\t<input id=\"no-gzj-card\" type=\"radio\" class=\"gzj-card icheck\" name=\"gzj_card\" value=\"2\" required>\n\t\t\t无公积金卡\n\t\t\t</label>\n\t\t\t<label class=\"gutter-x\">工本费：<span id=\"gzj-pro_cost\">{{gzj_rule.pro_cost}}</span></label>\n\t\t</div>\n\t    <div  class=\"text-right clearfix2 c-money sum-total\">\n\t    \t小计： <span id=\"gzj-total\" class=\"f-bold\">0元/月</span>\n\t    </div>\n\t    <div class=\"panel-audit clearfix2\">\n\t    \t<div class=\"title\">\n\t    \t{{if sb_updata_type_new == 2}}\n\t    \t\t公积金报减审核 \n    \t\t{{else}}\n    \t\t\t公积金报增审核 \n    \t\t{{/if}}\n    \t\t</div>\n\t    \t<div class=\"form-group col-xs-12\">\n\t    \t\t<label>\n\t    \t\t\t<input type=\"radio\" name=\"gzj_state\" value=\"0\" class=\"gzj_state states icheck\" required>\n\t    \t\t\t审核中\n\t    \t\t</label>\n\t    \t</div>\n\t    \t<div class=\"form-group col-xs-12\">\n\t    \t\t<label>\n\t    \t\t\t<input  type=\"radio\" name=\"gzj_state\" value=\"1\" class=\"gzj_state states states-success icheck\">\n\t    \t\t\t审核通过\n\n\t    \t\t</label>\n\t    \t</div>\n\t    \t<div class=\"form-group col-xs-12\">\n\t    \t\t<label class=\"guadan\">\n\t    \t\t\t<input class=\"icheck\" type=\"checkbox\" value=\"-1\" name=\"gzj_state_guadan\" >\n\t    \t\t\t挂起至下一订单\n\t    \t\t</label>\n\t    \t</div>\n\t    \t<div class=\"form-group col-xs-12\">\n\t    \t\t<label>\n\t    \t\t\t<input type=\"radio\" name=\"gzj_state\" value=\"-1\" class=\"gzj_state states icheck\" >\n\t    \t\t\t审核失败\n\t    \t\t</label>\n\t    \t</div>\n\t    \t<div class=\"form-group col-xs-12\">\n\t    \t\t<label>\n\t    \t\t\t\n\t    \t\t\t备注\n\t    \t\t\t<input class=\"input-remark gutter-left form-control\" type=\"text\" name=\"gzj_remark\" placeholder=\"反馈\">\n\t    \t\t</label>\n\t    \t</div>\n\t    </div>\n\t    <div class=\"text-right clearfix2 c-money sum-total czj-total\">\n\t    \t残障金： <span id=\"czj-total\" class=\"f-bold\">0元/月</span>\n\n\n\t    </div>\n\t    <div class=\"text-right clearfix2 c-money sum-total\">\n\t    \t<span class=\"all-total\">\n\t    \t\t总计： <strong id=\"all-total\" class=\"f-bold\">0元/月</strong>\n\t    \t</span>\n\t    </div>\n\t    \t\n\t   \n\t</div>\n";

/***/ },
/* 109 */
/*!**************************************!*\
  !*** ./js/src/tpl/securityTable.vue ***!
  \**************************************/
/***/ function(module, exports, __webpack_require__) {

	var __vue_script__, __vue_template__
	__vue_template__ = __webpack_require__(/*! !tpl-html-loader!./../../../../../../~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./securityTable.vue */ 110)
	module.exports = __vue_script__ || {}
	if (__vue_template__) {
	(typeof module.exports === "function" ? (module.exports.options || (module.exports.options = {})) : module.exports).template = __vue_template__
	}
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), false)
	  if (!hotAPI.compatible) return
	  var id = "_v-59b09991/securityTable.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, __vue_template__)
	  }
	})()}

/***/ },
/* 110 */
/*!***************************************************************************************************************************************!*\
  !*** F:/item/zbw/~/tpl-html-loader!F:/item/zbw/~/tpl-loader-ie8/lib/selector.js?type=template&index=0!./js/src/tpl/securityTable.vue ***!
  \***************************************************************************************************************************************/
/***/ function(module, exports) {

	module.exports = "\n<table class=\"table table-bordered table-border text-center\" style=\"width: 674px;\">\n    <thead>\n        <tr>\n            <th rowspan=\"2\" width=\"16%\">\n                险种\n            </th>\n            <th colspan=\"2\">\n                单位缴纳\n            </th>\n            <th colspan=\"2\">\n                个人缴纳\n            </th>\n            <th rowspan=\"2\">合计金额</th>\n\n        </tr>\n        <tr>\n            <th>比例</th>\n            <th>\n                金额\n            </td>\n            <th>比例</th>\n            <th>\n                金额\n            </th>\n        </tr>\n    </thead>\n    <tbody>\n\n        {{each items}}\n            <tr>\n                <td>\n                    {{$value.name}}\n                </td>\n                <td class=\"sum-company-scale\">\n                    {{$value.company.scale}}+{{$value.company.fixedSum}}\n\n                </td>\n                <td class=\"sum-company\">{{$value.company.sum}}</td>\n                <td class=\"sum-person-scale\">\n                    {{$value.person.scale}}+{{$value.person.fixedSum}}\n                </td>\n                <td class=\"sum-person\">{{$value.person.sum}}</td>\n                <td class=\"sum\">{{$value.total}}</td>\n            </tr>\n        {{/each}}\n        <tr>\n            <td>\n               合计\n            </td>\n            <td id=\"total-sum-company-scale\">\n                /\n            </td>\n            <td id=\"total-sum-company\">/</td>\n            <td id=\"total-sum-person-scale\">\n                /\n            </td>\n            <td id=\"total-sum-person\">/</td>\n\n            <td id=\"total\">\n                /\n            </td>\n        </tr>\n    </tbody>\n</table>\n";

/***/ }
]);
//# sourceMappingURL=order.bundle.js.map