require('./icheck');
module.exports = {
    init(opts) {

        let defaults = {
            checkboxClass: 'icheckbox icheckbox_minimal-orange',
            radioClass: 'iradio iradio_minimal-orange',
            increaseArea: '20%' // optional
        }

        let setting = $.extend({}, opts, defaults);
        
        $('.icheck').iCheck(setting);

        return this;
    },
    checkAll(opts) {
        let defaults = {
            checkAll: '.icheck-all',
            checks: '.single-icheck'
        }

        let setting = $.extend({}, defaults, opts);
        let $ichecks = $(setting.checks);
        let $checkAll = $(setting.checkAll);

        $checkAll.on('ifChecked', function() {
            $ichecks.iCheck('check');
        })

        $checkAll.on('ifUnchecked', function() {
            let len = $ichecks.filter(':checked').length;

            if (len === $ichecks.length) {
                $ichecks.iCheck('uncheck');
            }

        })

        $ichecks.on('ifChanged', function() {
            let len = $ichecks.filter(':checked').length;

            if (len > 0 && len === $ichecks.length) {
                $checkAll.iCheck('check');
            } else if (len > 0 && $checkAll.is(':checked')) {
                $checkAll.iCheck('uncheck');
            }
        })
    }
}
