var tpl = require('art-template');

tpl.helper('replaceEmpty',function(value = '', splits){

    if(value === '' || typeof value === 'undefined') {
        value = splits || '/'
    }
    return value;
})

tpl.helper('getFirstVal',function(value){
	var splits = '';
	if(typeof value !== 'string'){
		return '';
	}
	if(value.indexOf(',') !== -1) {
		splits = ',';
	} else if(value.indexOf('-') !== -1){
		splits = '-';
	}
    return value.split(splits)[0].replace('%', '');
})

tpl.helper('split',function(value, fix, index){

	if(typeof value !== 'string'){
		return '';
	}

    return value.split(fix)[index];
})

tpl.helper('parseFloat',function(value){
    return parseFloat(value);
})

tpl.helper('toFixed',function(value, num = 2){
	let val = (value - 0);
	
    return val !== val ? value : val.toFixed(num) ;
})

module.exports = tpl;