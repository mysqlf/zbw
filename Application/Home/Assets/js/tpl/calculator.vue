<template>
	<h3 class="state-title">社保计算器</h3>
	<div class="calculator horizontal clearOver" style="display: block;">
		<form class="left calculator-form">
			<dl class="form-input">
				<dt>选择城市</dt>
				<dd>
					<div class="city-picker-input">
						<span class="picker-txt">请选择</span>
						<input class="hidden-txt" type="hidden" name="location" required value="">
					</div>
				</dd>
			</dl>
			<dl class="form-input is-gjj-box">
				<dt>社保类型</dt>
				<dd>
					<select class="J_select2" name="classify_mixed[]" required>
						<option value="">请选择</option>
					</select>
				</dd>
				<dt>社保-五险基数 </dt>
				<dd>
					<input class="text-input" type="text" name="sb_amount" required>
				</dd>
			</dl>
			<dl class="form-input">
				<dd class="btn-box text-center">
					<a href="javascript:;" data-act="reset" class="button btn-border">重置</a>
					<a href="javascript:;" data-act="calculator" class="button buttonOrange">计算</a>
				</dd>
			</dl>
		</form>
		<div class="right">
			<div class="tips">社会与公积金缴费明细<span class="f-small c-gray">注意：以下仅供参考，具体收费以当地最新缴费政策为准</span></div>
			<table class="table table-bordered text-center">
				<thead>
					<tr>
						<th rowspan="2">缴纳项目</th>
						<th rowspan="2">社保基数</th>
						<th colspan="2">个人缴纳</th>
						<th colspan="2">企业缴纳</th>
					</tr>
					<tr>
						<th>缴纳比例</th>
						<th>缴纳金额（元）</th>
						<th>缴纳比例</th>
						<th>缴纳金额（元）</th>
					</tr>
				</thead>
				<tbody class="cal-content"></tbody>
			</table>
		</div>
	</div>
</template>

<script type="text/javascript">
	var tplFn = require('plug/artTemplate'),
	 	calTabletTpl = require('tpl/calculator-table.vue'),
		calTabletFormTpl = require('tpl/calculator-form.vue');

	require('plug/city-picker/city-picker');
	require('plug/selectordie/index');
	require('plug/validate/index');

	function CalTpl (opts){
		var defaults = {
				contrainer: '.calculator-box'
			}

		this.setting = $.extend({}, defaults, opts);
		this.$contrainer = $(this.setting.contrainer);
	}

	CalTpl.prototype.init = function(){
		var self = this;
		var $contrainer = self.$contrainer;
		var $citypicker = null;

		// 渲染计算器模版
		self.render($contrainer, module.exports.template);
		self.render($('.cal-content',$contrainer), calTabletTpl.template);

		self.validate();

		// 下拉选择
		$('.J_select2', $contrainer).selectOrDie();

		$citypicker = $('.city-picker-input', $contrainer);

		$citypicker.cityPicker({
			change: function(el,vals){

				self.ajax('/Index-getCityClass', {location: vals.district}, function(data){

					if(data.gjj_rule){
						calTabletFormTpl.init($contrainer, data);
					} else{
						layer.msg('当前城市没有社保规则');
						calTabletFormTpl.init($contrainer, {});
					}

				}, function(){
					layer.msg('当前城市没有社保规则');
					calTabletFormTpl.init($contrainer, {});
				})

				// 去除错误信息
				el.nextAll('.error.validator-error').remove()
			}
		});

		$('[data-act="reset"]',$contrainer).click(function(event) {
			self.init();
		});

		$('[data-act="calculator"]',$contrainer).click(function(event) {
			$('.calculator-form',self.$contrainer).submit();
		});
	};


	CalTpl.prototype.ajax = function(url,data, success, error){
		return $.post(url, data, function(data){
			if(data.state === 0){
				if(typeof success === 'function'){
					success(data.data);
				}
			} else {

				if(typeof error === 'function'){
					error(data);
				} else if(data.msg){
					layer.msg(data.msg);
				}
			}
		}, 'json');
	}

	//添加计算总数
	CalTpl.prototype.calulateTotal = function(result) {
		var { sb_result, gjj_result } = result;

		result.perTotal = ((( sb_result && sb_result.data.person) || 0)  +
						((gjj_result && gjj_result.data.person ) || 0 )).toFixed(2) - 0 ;

		result.compTotal = (((sb_result && sb_result.data.company ) || 0) +
						((gjj_result && gjj_result.data.company) || 0)).toFixed(2) - 0;

		return result;
	}

	CalTpl.prototype.validate = function() {
		var self = this,
			$form = $('.calculator-form', self.$contrainer);

		var validateObj = $form.validate({
			submitHandler: function(form){

				var data = $(form).serializeArray();

				$.post('/Index-calculate',data, function(result){
					result = self.calulateTotal(result);

					self.render($('.cal-content', self.$contrainer), calTabletTpl.template,result);
				},'json')
			},

			success: function(error, element){

				var $tip = $(element).nextAll('.input-tip');

				if($tip.length){

					if(element.name == "company_scale"){
						$tip.html('比例范围' + validateObj.settings.rules.company_scale.rangeScale)
					} else if(element.name == "person_scale"){
						$tip.html('比例范围' + validateObj.settings.rules.person_scale.rangeScale)
					}

					$tip.removeClass('tip-error');
				}
			},
			errorPlacement: function(error,element){
				var $tip = element.nextAll('.input-tip'),
					errorHtml = error.html();

				if($tip.length){
					if(errorHtml){
						if(errorHtml == 'false') {
							errorHtml = '';// 都选中时去掉提示
						}
						$tip.addClass('tip-error').show().html(errorHtml);
					}

				} else if(element.attr('type') == 'radio' || element.attr('type') == 'checkbox' || element.hasClass('hidden-txt') || element.hasClass('J_select2')){
					error.appendTo(element.parent().parent());
				} else{
					error.appendTo(element.parent());
				}
			},
			rules: {
				company_scale: {
		   			ispositivenum: true
		   		},
		   		person_scale: {
		   			ispositivenum: true
		   		}
			},
			messages: {
				location: {
					required: '请选择城市'
				},
				'classify_mixed[]': {
					required: '请选择分类'
				},
				gjj_amount: {
					required: '请填写公积金基数',
					rangeScale: '基数范围{0}'
				},
				sb_amount: {
					required: '请填写五险基数',
					rangeScale: '基数范围{0}'
				},
				company_scale: {
					required: '请填写公积金缴纳'
				},
				person_scale: {
					required: '请填写公积金缴纳'
				}
			}
		})

		return validateObj;
	}

	/**
	 * 渲染页面
	 * @param  {string jquery} box      需要渲染的节点
	 * @param  {string} template 模板
	 * @return {null}
	 */
	CalTpl.prototype.render = function(box,template,data) {
		var $box = this.$contrainer == box ? box : $(box,this.$contrainer);

		$box.html(tplFn.render(template)(data || {}));
	}

	module.exports = {
		calTpl: CalTpl
	};
</script>