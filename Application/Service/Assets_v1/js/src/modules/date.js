module.exports = {
	getDiffYear: function(begin, end){
		return new Date(end).getFullYear() - new Date(begin).getFullYear();
	},
	createNumArr: function(begin, end){
		var arr = [],
			i = begin;

		if(begin > end) return;

		for(;i <= end; i++){
			arr.push(i);
		}

		return arr;
	}
}