
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
                if ((element.options[j].selected !=
                    element.options[j].defaultSelected &&
                    element.options[j].defaultSelected) ||
                    (!element.options[j].defaultSelected &&
                    !element.options[0].selected)) {
                    return true;
                }
            }
        }
    }
    return false;
}