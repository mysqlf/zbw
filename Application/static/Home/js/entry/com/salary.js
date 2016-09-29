var pages = require('page/com/salary.js');
var initFn = $('script').eq(-1).data('init');
if(pages[initFn]){
    pages[initFn]();
}

