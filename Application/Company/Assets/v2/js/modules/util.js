exports.isFn = (fn) => {
	return typeof fn === 'function';
}

/**
 * 合并多个页面入口
 * @param  {Object} pages [页面对象]
 * @return
 */
exports.combinePageModule = (pages) => {

	$(function(){
		//'script[data-init]' 兼容之前data-init写法
		let $script = $('script[data-module],script[data-init]');

		$script.each(function(index, el) {
			let $el = $(this),
				module = $el.data('module') || $el.data('init') || Object.keys(pages)[0],
				methods = $el.data('methods') || 'init';
				methods = methods.split(',');
			// 防止重复调用
			if($el.data('hasInit') || !pages[module]) {
				return true;
			}

			methods.forEach( function(item, index) {
				let arr = item.split('|'),
					init = arr[0].trim(),
					arg = arr[1] ? arr[1].trim().split(' ') : [];

				if(pages[module] && exports.isFn(pages[module][init])) {
					pages[module][init].apply(pages[module], arg);
					$el.data('hasInit', true);
				}
			});

		});
	})
}

// 动态修改规则
exports.modifyRules = (validateObj,opts) => {

   validateObj.settings = $.extend(true,{},validateObj.settings,opts)
}