var initFn = $('script').eq(-1).data('init');
var pages = {
    basic: require('page/com/basic.js'),
    extra: require('page/com/extra.js')
}

if(pages[initFn]){
    pages[initFn].init();
}
