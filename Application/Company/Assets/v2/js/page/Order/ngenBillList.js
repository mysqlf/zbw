require('plug/selectordie/index');
require('plug/validate/validate');
require('plug/datetimepicker/datetimepicker');
let genBillList = require('page/Order/genBillList');
let ngenBillList = {
    init(){
        let self = this;
        genBillList.evenBg();
        $('.select').selectOrDie();
        genBillList.validate();
        genBillList.timePicker();
        genBillList.check();
    }
}
module.exports = ngenBillList;
