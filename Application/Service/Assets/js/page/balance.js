let dateFn = require('plug/datetimepicker/index'),
    { dateRange } = require('modules/date');

let balance = {
    init() {
        dateFn.getYearMonthDay();
        dateRange('pay');

    }
}

module.exports = balance;
