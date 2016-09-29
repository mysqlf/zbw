 /*
  * getUrlVal 获取url的value值
  * @param {string} key url的参数的键
  *
  *
  * 
  * setUrlVal 设置url的value值
  * @param {string} key url的参数的键
  * @param {string} value 需要设置的值
  *
  *
  * deleteUrlParam 删除url的value值
  * @param {string} key url的参数的键 
  *
  *
  * has 是否存在key
  * @param {string} key url的参数的键 
  *
  * 
  * splitUrl 切割url
  * @param {string} url  传入地址 默认当前地址
  * 
  */
 function DealUrl(opts){
    let defaults = {
       count: 3 // 条件从count 开始
    }
    this.url = location.href;
    this.urlArr = this.url.split('-');
    this.settings = $.extend({}, defaults, opts);

 }

DealUrl.prototype = {
	constructor : DealUrl,
  isWeiXin: function(url) {
      var url = url || location.href;

      // 微信链接
      if (url.indexOf('open.weixin.qq.com') !== -1 && url.indexOf('redirect_uri') !== -1) {
          return true;
      }
      return false;
  },
  isMix: function(url) {
      var url = url || location.href;

      if ((url.indexOf('?') !== -1 || url.indexOf('&') !== -1) && url.split('?')[0].indexOf('-') !== -1) {
        return true;
      }

      return false;
  },
  each: function(arr, fn, step){
      var flag = true,
          step = step || 1;

      for (var i = 0, len = arr.length; i < len; i+=step) {
          flag = fn(arr[i], i);
          if (flag == false) {
              break;
          }
      }
  },
  splitUrl: function(url){
      var url = url || location.href,
          splitUrl= [],
          self = this;

      if (self.isWeiXin(url)) {

          // 微信链接 网站链接在 redirect_uri参数上
          splitUrl = self.splitNormalUrl(url);
      } else if (self.isMix(url)) {

          // 两种规则都有 如http://192.168.67.85:8008/Ptime-nearJob-type-9912?id=10
          var mixArr = self.splitMixUrl(url);

          splitUrl = mixArr[0].concat(mixArr[1]);
      } else {
          splitUrl = self.splitUnnormalUrl(url);
      }

      return splitUrl;
  },
  splitUnnormalUrl: function(url){
      var url = url || location.href;
      var { count } = this.settings;
      var splitUrl= url.split('-');
      var newArr = [splitUrl[0],splitUrl.slice(1, count).join('-')];

      newArr = newArr.concat(splitUrl.slice(count))

      return newArr;
  },
  splitNormalUrl: function(url) {
      var url = url || location.href;
      var splitUrl = '';
      var urlArr = [];

      if (url.indexOf('?') == -1) {
         return '';
      }

      splitUrl = url.substr(url.lastIndexOf('?')+1).split('&');

      for (var i = 0, len = splitUrl.length; i < len; i++) {

          var arr = splitUrl[i].split('=');

          for (var j = 0, len2 = arr.length; j < len2; j++) {
              urlArr.push(arr[j]);
          }
      };

      return urlArr;
  },
  splitMixUrl: function(url) {
      var url = url || location.href,
          splitUrl = url.split('?'),
          self = this;
      
      return [
          self.splitUnnormalUrl(splitUrl[0]),
          self.splitNormalUrl('?' + splitUrl[1])
      ]

  },
  joinUnnormalUrl: function(arr) {
      var urlArr = arr;

      return urlArr.join('-');
  },
  joinNormalUrl: function(arr) {
      var urlArr = arr;
      var str = '?';
      
      this.each(urlArr, function(item, index) {
          if(index % 2){
              str += item + '&';
          } else {
              str += item + '=';
          }
          
      });
      return str.slice(0, -1);
  },
  joinMixUrl: function(arr) {

      // arr为二维数组 
      var self = this,
          urlStr = '',
          urlArr = arr;

      urlStr = self.joinUnnormalUrl(urlArr[0]) + self.joinNormalUrl(urlArr[1]);

      return urlStr;
  },
	getUrlVal : function(key, url){
	    var url = url || location.href,
          self = this,
          val = '',
	        urlArr = self.splitUrl(url);

      self.each(urlArr, function(item, index) {

          if(urlArr[index-1] == key){
            val = urlArr[index];

            return false;
          }
      });

      return val;
	},
	setUrlVal: function(key, value, url, rule){
	    var url = url || location.href,
          val = value,
          self = this,
  	      urlArr = self.splitUrl(url),
          settings = this.settings,
          flag = true,
          wxValUrl = '',
          newUrl = '';// 微信存的url
          rule = rule || 1; // 规则 如果为1 使用'-'策略 如果为2 使用'?'策略

      if (self.isWeiXin(url)) {

          wxValUrl = self.getUrlVal('redirect_uri');
          newUrl = self.setUrlVal(key, value, wxValUrl);
          setVal(urlArr, 'redirect_uri', newUrl);

      } else if (self.isMix(url)){
          urlArr = self.splitMixUrl(url);

          self.each(urlArr, function(item, index) {
              urlArr[index] = setVal(item, key, val);
          });

      } else {
          setVal(urlArr, key, val);
      }

      if (flag) {
          if (self.isMix(url) && !self.isWeiXin(url)) {
              if (rule == 1) {
                  urlArr[0].push(key, val);
              } else {
                  urlArr[1].push(key, val);
              }
          } else {

              urlArr.push(key, val);
          }
      }

      if (self.isWeiXin(url)){

          return self.joinNormalUrl(urlArr);

      } else if(self.isMix(url)) {

          return self.joinMixUrl(urlArr);
      } else {

          return self.joinUnnormalUrl(urlArr);
      }


      function setVal(arr, key, value) {

          self.each(arr, function(item, index) {

              if (arr[index] == key) {
                  arr[index + 1] = value;
                  flag = false;
                  return flag;
              }
          },2);

          return arr;
      }
      
  },

  deleteUrlParam: function(key){
        var urlArr = this.splitUrl();;

	    	for (var i = 1,len = urlArr.length ; i < len ; i++  ){
	            if(urlArr[i-1] == key){
	              urlArr.splice(i-1,2);
	              break;
	            }
	         }
       return urlArr.join('-');
  },
  has: function(key){
      var urlArr = this.splitUrl();
      var flag = false;

        for (var i = 1,len = urlArr.length ; i < len ; i++  ){
              if(urlArr[i-1] == key){
                flag = true;
                break;
              }
           };

        return flag;   
  }

}

module.exports = DealUrl;