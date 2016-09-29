var areaFn = {
	areaJson: (function(){
        if(typeof areaJson !== 'undefined') {
            return areaJson
        } else {
            return {};
        }
    })(),
    // 获取地区类型
    getAreaType: function(id) {
        var num = parseInt(id);

        //省级
        if (num % 1000000 == 0) {
            return 1;
        }
        //市级
        if (num % 10000 == 0) {
            return 2;
        }
        // 地区
        if (num % 100 == 0) {
            return 3;
        }
    },
    // 是否是直辖市
    isMunicipality: function(id) {
        var num = parseInt(id) / 1000000;

        if (num === 13 || num === 12 || num === 11 || num === 10) {
            return true;
        } else {
            return false;
        }
    },
    //没有省份的城市
    isCityWithoutProvince: function(id){
    	var num = parseInt(id) / 1000000;

    	if(this.isMunicipality(id) || num === 42 || num === 41 || num === 47){
    		
    		return true;
    	} else {
            return false;
        }
    },
    /**
     * 是否是子集
     * @return {Boolean} 
     */
    isSub: function(father, son) {
        var type = this.getAreaType(father);


        if(father - 0 === son - 0){
        	return false;
        }	
  		if (type === 1 && this.getAreaType(son) == 2 && parseInt(father / 1000000) === parseInt(son / 1000000)) {
            return true;
        } else if ( (this.isCityWithoutProvince(father) ||  (type === 2 && this.getAreaType(son) == 3) ) && parseInt(father / 10000) === parseInt(son / 10000)) {
            return true;
        } else {
            return false;
        }
    },
    /**
     * 获取子集
     * @param  {string number} id 城市id
     * @return {object}
     */
    getSubItem: function(id) {
        var obj = {},
            tempGroup,
            areaJson = this.areaJson;

        for (var i = 0, len = areaJson.length; i < len; i++) {
            var item = areaJson[i];

            if (this.isSub(id, item.id) || (typeof id === 'undefined' && this.getAreaType(item.id) === 1)) {
                var group = item.group;

                if (typeof tempGroup === 'undefined' || tempGroup === group) {

                    if (obj[group]) {
                        obj[group].push(item);
                    } else {
                        obj[group] = [item];
                    }
                }
            } 
        }
        return obj;
    },
    getById: function(id){
    	for (var i = 0, len = this.areaJson.length; i < len; i++) {

    		if(id - 0 === this.areaJson[i].id - 0){
    			return this.areaJson[i];
    		}
    	}
    },
    getParentById: function(id){
        var type = this.getAreaType(id),
            parentId = 0;

        switch (type) {
            case 3:
                parentId = parseInt(id / 10000) * 10000;
                break;
            case 2:
                parentId = parseInt(id / 1000000) * 1000000;
                break;
            case 1:
                parentId = id
                break;
            default:
                break;
        }

        return parentId;

    },
    getParentsById: function(id){
        var type = this.getAreaType(id),
            arr = [];

        while (type && type !== 1) {
            id = this.getParentById(id);
            type = this.getAreaType(id);

            arr.unshift(this.getById(id))
        }

        return arr;

    },
    isEmptyObj: function(obj){
    	var flag = true;

    	for (var i in obj){
    		flag = false;

    		break;
    	}

    	return flag;
    },
    render: function(data) {
        var html = '<dl class="horizontal ">',
            flag = true;

        for (var prop in data) {
            var arr = data[prop];

            flag = false;

            html += prop ? '<dt class="left">' + prop + '</dt><dd class="right">' : '<dd>';

            for (var i = 0, len = arr.length; i < len; i++) {
                var item = arr[i];

                html += '<a class="area-toggle" href="javascript:;" data-value="' + item.id + '">' + item.name + '</a>';
            }
            html += '</dd>';
        }

        html += '</dl>';

        if (flag) {
            html = '';
        }

        return html;
    }
}


module.exports = areaFn;