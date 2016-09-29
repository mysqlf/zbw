<template>

	{{each other as item i}}
		<li class="J_pay-item clearfix">
			<div class="item item-name">
				{{item.name}}
				<input type="hidden" name="otherName[]" value="{{item.name}}">
			</div>
			<div class="item">
				单位：
				<div class="inline-block">
					<input class="ipt size0" type="number" name="companyOther[]" value="{{item.rules.company}}" required> 元
				</div>
			</div>
			<div class="item">
				个人：
				<div class="inline-block">
					<input class="ipt size0" type="number" name="personOther[]" value="{{item.rules.person}}" required> 元
				</div>
			</div>
			<div class="item">
				<a data-act="removeOtherRules" data-key="{{item.name}}" href="javascript:;"><i class="icon icon-del"></i></a>
			</div>
		</li>
	{{/each}}
</template>

<script>
	/*其他收费列表*/	
	let tpl = require('art-template');

	let listObj = {
		init(box, data){
			let self = listObj,
				html = tpl.render(self.template)(data);

			if(data.type == 'add') {
				$(box).append(html);
			} else {
				$(box).html(html);
			}
		}
	}

	module.exports = listObj;
</script>
