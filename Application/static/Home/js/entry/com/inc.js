var pages = require('page/com/inc.js')
var initFn = $('script').eq(-1).data('init');
if(pages[initFn]){
    pages[initFn]();
}
