<template>
	{{if paymentType == 2}}
		<div class="rules-panel white gjj-rules-panel">
		<h4 class="panel-title">
			公积金规则
		</h4>

	{{else}}
		<div>
	{{/if}}

		{{if paymentType == 1}}
			<dl class="horizontal horizontal-ipt horizontal-5em {{if paymentType == 1}}sb-amount-box{{/if}}">
				<dt class="left">基数范围：</dt>
				<dd class="right">
					<div class="inline-block size1">
						<input id="amount-min" class="ipt" type="number" name="minAmount" value="{{min}}" required placeholder="最低缴纳基数">
					</div>
					<span class="line">~</span>
					<div class="inline-block size1">
						<input id="amount-max" class="ipt" type="number" name="maxAmount" value="{{max}}" required placeholder="最高缴纳基数">
					</div>
					元

					{{if paymentType == 2}}
						<label>
							<input type="checkbox" class="icheck" value="1" {{if intval == 1}}checked{{/if}} name="intval">
							是否取整
						</label>
					{{/if}}

				</dd>
			</dl>
			<table>
				<thead>
					<tr>
						<th width="80">能否补缴</th>
						<th width="150">险种</th>
						<th>缴纳基数</th>
						<th>单位比例</th>
						<th>个人比例</th>
						<th width="80">操作</th>
					</tr>
				</thead>
				<tbody id="J_rules-sb-list"></tbody>
				<tfoot>
					<tr>
						<td class="text-left" colspan="6">
							<a id="addSbRules" href="javascript:;" class="btn-add"><i class="icon icon-add"></i>添加险种</a>
							<span class="tip f-small">注：不包含的险种不设置数额即可。</span>
						</td>
					</tr>
				</tfoot>
			</table>
		{{else if paymentType == 2}}
		<ul class="panel-list">
			<li>
				<dl class="horizontal horizontal-ipt horizontal-5em {{if paymentType == 1}}sb-amount-box{{/if}}">
					<dt class="left">基数范围：</dt>
					<dd class="right">
						<div class="inline-block size1">
							<input id="amount-min" class="ipt size0 " type="number" name="minAmount" value="{{min}}" required placeholder="最低缴纳基数">
						</div>
						<span class="line">~</span>
						<div class="inline-block size1">
							<input id="amount-max" class="ipt size0" type="number" name="maxAmount" value="{{max}}" required placeholder="最高缴纳基数">
						</div>
						元

						{{if paymentType == 2}}
							<label class="intval-label">
								<input type="checkbox" class="icheck" value="1" {{if intval == 1}}checked{{/if}} name="intval">
								是否取整
							</label>
						{{/if}}

					</dd>
				</dl>
			</li>
			<li class="clearfix J_scale-scope">
				<dl class="horizontal horizontal-5em fl">
					<dt class="left">单位比例：</dt>
					<dd class="right">

						<label class="inline-block icheck-label">
							<input class="icheck J_scale-select" type="radio" value="1" name="isComType" {{if iscomtype}}checked{{/if}}>
							比例范围
						</label>

						<label class="inline-block icheck-label">
							<input class="icheck J_scale-select" type="radio" value="2" name="isComType" {{if !iscomtype}}checked{{/if}}>
							固定比例
						</label>

					</dd>
				</dl>
				<div class="fl">
					<input class="ipt fixed-ratio dn ignore size0" 
						type="text" name="comScale" 
						value="{{if !iscomtype}}{{company.replace(/%/g,'')}}{{/if}}" 
						required placeholder="缴纳基数">

					<div class="scale-box inline-block">
						<div class="inline-block">
							<input class="ipt size0" 
							type="number" 
							name="comFixLow" 
							value="{{if iscomtype}}{{company.replace(/%/g,'') | split:'-','0'}}{{/if}}" 
							required placeholder="最低缴纳基数">
						</div>
						<span class="line">~</span>
						<div class="inline-block">
							<input  class="ipt size0" 
								type="number" name="comFixUp" 
								value="{{if iscomtype}}{{company.replace(/%/g,'') | split:'-', '1'}}{{/if}}" required placeholder="最高缴纳基数">
						</div>
					</div>
					%
					<span class="c-gray f-small fixed-ratio">（多个固定值，请用英文逗号隔开）</span>
				</div>
			</li>
			<li class="clearfix J_scale-scope">
				<dl class="horizontal horizontal-5em fl">
					<dt class="left">个人比例：</dt>
					<dd class="right">
						<label class="inline-block icheck-label">
							<input class="icheck J_scale-select" type="radio" value="1" name="isPerType" {{if ispertype}}checked{{/if}}>
							比例范围
						</label>

						<label class="inline-block icheck-label">
							<input class="icheck J_scale-select" type="radio" value="2" name="isPerType" {{if !ispertype}}checked{{/if}}>
							固定比例
						</label>
					</dd>
				</dl>
				<div class="fl">
					<input class="ipt fixed-ratio dn size0 ignore" type="text" name="perScale" 
						value="{{if !ispertype}}{{person.replace(/%/g,'')}}{{/if}}" required placeholder="缴纳基数">
					<div class="scale-box inline-block">
						<div class="inline-block">
							<input class="ipt size0" type="number" name="perFixLow" 
								value="{{if ispertype}}{{person.replace(/%/g,'') | split:'-','0'}}{{/if}}" 
								required placeholder="最低缴纳基数">
						</div>
						<span class="line">~</span>
						<div class="inline-block">
							<input  class="ipt size0" type="number" name="perFixUp" 
								value="{{if ispertype}}{{person.replace(/%/g,'') | split:'-','1'}}{{/if}}" 
								required placeholder="最高缴纳基数">
						</div>
					</div>
					%
					<span class="c-gray f-small fixed-ratio">（多个固定值，请用英文逗号隔开）</span>
				</div>
			</li>
		</ul>
			<a id="addSbRules" href="javascript:;" class="btn-add"><i class="icon icon-add"></i>添加险种</a>
		{{/if}}
	</div>
	{{if other}}
	<div class="rules-panel white">
		<h3 class="panel-title pay-title">
			其他收费
		</h3>

		<ul id="J_other-tpl" class="panel-list"></ul>

		<a id="add-other-rules" href="javascript:;" class="btn-add"><i class="icon icon-add"></i>添加</a>

	</div>
	{{/if}}

<!-- 	<div class="rules-panel white rules-panel-small">
	<dl class="horizontal horizontal-8em pro-cost-list">
		<dt class="left text-right">
			{{if paymentType == 1}}
				社保卡工本费：
			{{else if paymentType == 2}}
				公积金卡工本费：
			{{/if}}
		</dt>
		<dd class="right">
			<input class="ipt size0" type="number" name="pro_cost" value="{{pro_cost || 0}}" required> 元/人
		</dd>
	</dl>
	{{if paymentType == 1}}
		<dl class="horizontal horizontal-8em pro-cost-list">
			<dt class="left text-right">残障金：</dt>
			<dd class="right">
				<input class="ipt size0" type="number" name="disabled"  value="{{disabled || 0}}" required> 元/人/月
			</dd>
		</dl>
	{{/if}}
</div> -->
	
</template>

<script>
	let tpl = require('art-template'),
		icheckFn = require('plug/icheck/index'),
		listTpl = require('tpl/sb_rules_list.vue'),
		otherListTpl = require('tpl/pay_rules_list.vue'),
		{ modifyRules } = require('plug/validate/index');

		require('plug/selectordie');

	let rulesObj = {
		init(box, data){
			let self = rulesObj,
				dataArr = data;

			if(!dataArr) {
				return;
			}

			//重置
			self.remainArr = [];
			// 可选择其他费用
			self.remainOtherArr = [],

			dataArr.paymentType = $('#J_paymentType').val();

			// 判断是否固定基数 和 比例基数
			if(dataArr.paymentType === '2'){
				dataArr.ispertype = dataArr.person.indexOf('-') !== -1 || dataArr.person === ''  ? true : false;
				dataArr.iscomtype = dataArr.company.indexOf('-') !== -1 || dataArr.company === '' ? true : false;
			}

			$(box).html(tpl.render( self.template )( dataArr ));

			listTpl.init('#J_rules-sb-list', dataArr);

			otherListTpl.init('#J_other-tpl', dataArr)

			self.toggleAddBtn();
			self.toggleOtherAddBtn();

			$('.J_scale-select:checked').trigger('ifChanged');

			$('#J_sb-rules-tpl select').selectOrDie();

			// 删除列表
			$('#J_rules-sb-list').on('click', '[data-act="removeSbRules"]', function(){
				let $this = $(this),
					$item = $this.closest('.J_rules-item'),
					key = $this.data('key');

					self.remainArr.push(self.find(dataArr.items, key))

					self.toggleAddBtn();

					$item.remove();
			})

			// 删除其他收费列表
			$('#J_other-tpl').on('click', '[data-act="removeOtherRules"]', function(){
				let $this = $(this),
					$item = $this.closest('.J_pay-item'),
					key = $this.data('key');

					self.remainOtherArr.push(self.find(dataArr.other, key))

					self.toggleOtherAddBtn();

					$item.remove();
			})

			// 添加险种
			$('#addSbRules').click(function(){
				self.layer(1)
			})

			$('#add-other-rules').click(function(){
				self.layer(2)
			})

			// 限制最大最小
			$('#amount-min, #amount-max').change(function(){
				let min = parseFloat($('#amount-min').val()) || 0,
					max = parseFloat($('#amount-max').val()) || Infinity;
				modifyRules($('#J_add-form').data('validator'), {
					rules: {
						'amount[]': {
							min,
							max
						},
						'amountmax[]': {
							min,
							max
						},
						amount_min: {
							max,
						},
						amount_max: {
							min,
						}
					}
				})
			}).trigger('change');

		},
		/**
		 * 弹出选择
		 * @param  {种类} type 1 险种 2 其他费用
		 * @return {[type]}      [description]
		 */
		layer(type = 1){
			let self = rulesObj,
				remainArr = type == 1 ? self.remainArr : self.remainOtherArr,
				html = '';

				if(remainArr.length) {
					remainArr.forEach( function(item, index) {
						html += `<label class="icheck-label"><input data-index="${index}" class="icheck" type="checkbox" value="${item.name}" />${item.name}</label>`
					});

					layer.open({
						area: '380px',
						content: html,
						title:'添加其他费用',
						skin: 'add-rules',
						btn:['添加'],
						yes(index){
							let items = [],
								tempArr = [];

							$('.add-rules .icheck').each(function(){
								let $this = $(this),
									index = $this.data('index');

								if($this.is(':checked')) {
									items.push(remainArr[index]);
								} else {
									tempArr.push(remainArr[index]);
								}

							})

							if(type == 1) {
								self.remainArr = tempArr;
								self.toggleAddBtn();
								listTpl.init('#J_rules-sb-list',{items, type:'add'});
							} else {
								self.remainOtherArr = tempArr;
								self.toggleOtherAddBtn();
								otherListTpl.init('#J_other-tpl',{other: items, type:'add'});
							}

							layer.close(index);
						},
						success(){
							icheckFn.init();
						}
					})
				} 

		},
		// 可选择险种
		remainArr: [],
		// 可选择其他费用
		remainOtherArr: [],

		// 显示隐藏 添加按钮
		toggleAddBtn(){
			let self = rulesObj,
				$btn = $('#addSbRules');

			if(self.remainArr.length) {
				$btn.show();
			} else {
				$btn.hide();
			}
		},
		// 显示隐藏 添加按钮
		toggleOtherAddBtn(){
			let self = rulesObj,
				$btn = $('#add-other-rules');

			if(self.remainOtherArr.length) {
				$btn.show();
			} else {
				$btn.hide();
			}
		},
		find(items, key){

			return items.find((item) => {
				return item.name == key;
			})
		}
	}

	module.exports = rulesObj;
</script>
