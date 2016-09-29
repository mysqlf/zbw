<template>
	<dd class="is-gjj">
		<label>
			<input class="icheck" checked type="checkbox" name="isGjj" value="1">
			是否缴纳公积金
		</label>
	</dd>
	<dt class="small-gutter J_hide-by-checked">社保-公积金基数</dt>
	<dd class="J_hide-by-checked">
		<input class="toggle-ignore text-input ignore" type="text" name="gjj_amount" required value="{{min}}">
		<span class="f-small input-tip block">基数范围{{min}}-{{max}}</span>
	</dd>
	<dt class="small-gutter J_hide-by-checked">社保-公积金缴纳</dt>
	<dd class="scale-input-box J_hide-by-checked">
		<div class="scale-input scale-input-comp">
			<input class="toggle-ignore text-input text-right ignore" value="{{company | getFirstVal}}" type="text" name="company_scale" required>
			<span class="f-small input-tip block">比例范围{{company}}</span>
		</div>
		<div class="scale-input scale-input-per">
			<input class="toggle-ignore text-input text-right ignore" value="{{person | getFirstVal}}" type="text" name="person_scale" required>
			<span class="f-small input-tip block">比例范围{{person}}</span>
		</div>
	</dd>
</template>

<script type="text/javascript">
	var tplFn = require('plug/artTemplate'),
		util = require('modules/util');

	require('plug/icheck/icheck');

	module.exports = {
		init: function($contrainer,data) {
			if(!data.gjj_rule) {
				return ;
			}
			$('.form2-tpl',$contrainer).replaceWith(tplFn.render(this.template)(data.gjj_rule));

			util.modifyRules($('.calculator-form', $contrainer).data('validator'),{
				rules: {
					gjj_amount: {
						rangeScale: data.gjj_rule.min + '-' + data.gjj_rule.max
					},
					company_scale: {
						rangeScale: data.gjj_rule.company
					},
					person_scale: {
						rangeScale: data.gjj_rule.person
					}
				}
			})

			this.icheck($contrainer);
		},
		icheck: function($contrainer) {
			// 复选框
			$('.icheck',$contrainer).iCheck({
			    checkboxClass: 'icheckbox icheckbox_minimal-orange',
			    radioClass: 'iradio iradio_minimal-orange',
			    increaseArea: '20%' // optional
			}).on('ifChanged',function(){
				var $this = $(this),
					$gjjBox = $this.closest('.is-gjj-box'),
					$hidden = $gjjBox.find('.J_hide-by-checked'),
					$required = $gjjBox.find('.toggle-ignore');

					if($this.is(':checked')) {
						$hidden.show();
						$required.removeClass('ignore');

					} else {
						$hidden.hide();
						$required.addClass('ignore');
						$gjjBox.find('span.validator-error').remove();
					}
			}).trigger('ifChanged');
		}
	}
</script>
