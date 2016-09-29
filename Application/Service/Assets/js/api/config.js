/*require('mockData/index')*/


let { isFn } = require('modules/util');


$.ajaxSetup({
	timeout: 30000
})


exports.fetch = (url, data = {}, success, error, opts = {}) => {

	let defaults = {
		url: url,
		type: 'post',
		dataType: 'json',
		data: data,
		success(json){
			if(json.status - 0 === 0){
				if(isFn(success)){
					success(json);
				}
			} else {
				if(isFn(error)){
					error(json);
				}
			}
		}
	};

	return $.ajax($.extend({}, defaults, opts))

}

exports.concatApi = (api) => {
	let obj = {};

	for (let porp in api) {

		obj[porp] = (...arg) =>{
			arg.unshift(api[porp]);

			return exports.fetch.apply(this, arg);
		}
	}

	return obj;
}