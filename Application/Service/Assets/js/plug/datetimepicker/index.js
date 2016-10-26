require('./js/bootstrap-datetimepicker');
require('./js/locales/bootstrap-datetimepicker.zh-CN');
require('./css/bootstrap.min.css');
require('./css/bootstrap-datetimepicker.min.css');

module.exports = {
    getYearMonth(opts) {
        let defaults = {
            el: '.date',
            language: 'zh-CN',
            format: 'yyyy/mm',
            startView: 3,
            minView: 3,
            autoclose: true

        },
        setting = $.extend({}, defaults, opts)

        $(setting.el).datetimepicker(setting);
    },
    getYearMonthDay(opts) {

        let defaults = {
            el: '.date-day',
            language: 'zh-CN',
            format: 'yyyy/mm/dd',
            startView: 2,
            minView: 3,
            autoclose: true
        },
        setting = $.extend({}, defaults, opts)

        $(setting.el).datetimepicker(setting);
    }
}
