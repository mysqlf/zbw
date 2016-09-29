<template>
	<dl class="horizontal horizontal-5em col-12">
		<dt class="left">基数范围</dt>
		<dd class="right">
			<div class="inline-block">
				<input id="amount-min" class="ipt" type="text" name="amount_min" value="{{min}}" required placeholder="最低缴纳基数">
			</div>
			<span class="line">-</span>
			 
			<div class="inline-block">
				<input id="amount-max" class="ipt" type="text" name="amount_max" value="{{max}}" required placeholder="最高缴纳基数">
			</div>

		</dd>
	</dl>
	<table>
		<thead>
			<tr>
				<th width="80">能否补缴</th>
				<th width="150">险种</th>
				<th>最低缴纳基数</th>
				<th>单位比例</th>
				<th>个人比例</th>
				<th width="80">操作</th>
			</tr>
		</thead>
		<tbody id="J_rules-sb-list"></tbody>
	</table>
	<a id="addSbRules" href="javascript:;" class="btn">添加险种</a>

	<span class="tip">注：不包含的险种不设置数额即可。</span>
	<div class="">

			社保卡工本费 <input class="ipt" type="text" name="pro_cost" value="{{pro_cost || 0}}"  placeholder=""> 元/人
	</div>
	<div>
		其他收费
		
		<table>
			<thead>
				<tr>
					<th width="180">费用名称</th>
					<th>单位</th>
					<th>个人</th>
					<th width="80">操作</th>
				</tr>
			</thead>
			<tbody id="J_other-tpl"></tbody>
		</table>
		<a id="add-other-rules" href="javascript:;" class="btn">添加</a>
		<dl class="horizontal horizontal-5em">
			<dt class="left">残障金</dt>
			<dd class="right">
				<input class="ipt" type="text" name="disabled"  value="{{disabled || 0}}" required> 元/人/月
			</dd>
		</dl>
	</div>	
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
				dataArr = data.data;

			$(box).html(tpl.render( self.template )( dataArr ));

			listTpl.init('#J_rules-sb-list', dataArr);

			otherListTpl.init('#J_other-tpl', dataArr)

			self.toggleAddBtn();
			self.toggleOtherAddBtn();

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
						amount_min: {
							max,
						},
						amount_max: {
							min,
						}
					}
				})
			}).trigger('change')

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
