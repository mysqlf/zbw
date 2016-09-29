<template>
	<input type="hidden" name="template_id" value="{{template_id}}">
	{{each sb_classify as item i}}
	<dt>{{item.name}}</dt>
	<dd class="J_sb-select2">
		<select class="J_select2" name="classify_mixed[]" data-type = "1">
			<option value="">请选择</option>
			{{each item.classify_mixed as item2 j}}
			<option value="{{item2.id}}">{{item2.name}}</option>
			{{/each}}
		</select>
	</dd>
	{{/each}}
	<dt>社保-五险基数 </dt>
	<dd>
		<input class="J_insuranceBase-input text-input" type="text" name="sb_amount" required>
		<span class="J_insuranceBase-tip f-small input-tip block"></span>
	</dd>
	<div class="form2-tpl"></div>
</template>

<script type="text/javascript">
	var tplFn = require('plug/artTemplate'), 
		calTabletFormTpl2 = require('tpl/calculator-form2.vue'),
		util = require('modules/util');

	require('plug/selectordie/index');

	module.exports = {
		init($contrainer,data) {

			let validateObj = $('.calculator-form', $contrainer).data('validator');

			$('.is-gjj-box',$contrainer).html(tplFn.render(this.template)(data || {}));

			calTabletFormTpl2.init($contrainer,data);

			// 下拉选择
			$('.J_select2',$contrainer).selectOrDie({
				onChange() {
					$(this).trigger('blur');
				}
			});

			$('.J_sb-select2 .J_select2', $contrainer).on('change', function(){
				let $this = $(this);
/*					vals = '';

				$('.J_sb-select2 .J_select2', $contrainer).each(function(){
					vals += this.value;
				})

				if(vals === ''){
					return;
				}*/
				let formData = $('.calculator-form', $contrainer).serializeArray();
				// 分类类型 1社保 2公积金
				formData.push({
					name: 'type',
					value: $this.data('type')
				});

				$.post('/Index-changeClassify', formData, (json) => {

					let $insuranceBase = $('.J_insuranceBase-input', $contrainer);

					if(json){
						let { max, min } = json;

						$('.J_insuranceBase-tip', $contrainer)
							.removeClass('error validator-error')
							.html('基数范围'+ min + '-' + max)
							.show();

						util.modifyRules(validateObj, {
							rules: {
								sb_amount: {
									rangeScale: min + '-' + max,
									fail: false
								},
								messages: {
									sb_amount: {
										required: '请填写五险基数'
									}
								}
							}
						})

						$insuranceBase.val(min).prop('readonly', false).focus().blur();

					} else {
						let tipArr = [],
							tip = 'false';// 为空

						$('[name="classify_mixed[]"]', $contrainer).each(function(){
							let val = this.value,
								txt = this.options[this.selectedIndex].innerHTML;

							if(val !== ''){
								tipArr.push(txt);
							}
						});

						tip = tipArr.length ? `当前分类：<span class="f-bold">${tipArr.join('+')}</span>，暂且没有规则!` :  tip;

						$insuranceBase.prop('readonly', true)

						validateObj.showErrors({
							sb_amount: tip
						});

						util.modifyRules(validateObj, {
							rules: {
								sb_amount: {
									fail: true
								}
							},
							messages: {
								sb_amount: {
									fail: tip,
									required: tip
								}
							}
						})

						$insuranceBase.val('');
					}

				}, 'json');
			}).trigger('change');
		}
	}
</script>
