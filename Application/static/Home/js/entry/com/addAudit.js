var addAudit=require("page/com/addAudit.js");
var isEdit = $('script').eq(-1).data('init');
addAudit.init(parseInt(isEdit));
