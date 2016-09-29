require('./icheck');

let iCheck = {
    // 动态修改规则
    init() {
       iCheck.iCheck();

        return this;
    },
    checkAll(opts) {
        let defaults = {
            checkAll: '.icheck-all',
            checks: '.single-icheck'
        }

        let setting = $.extend({}, defaults, opts);
        let $ichecks = $(setting.checks).not(':disabled');
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
    },
    iCheck(opts){
        let defaults = {
            checkboxClass: 'icheckbox',
            radioClass: 'iradio',
            increaseArea: '16px' // optional
        },
        setting = $.extend(opts,defaults),
        el = opts && opts.el || '.icheck'
        $(el).iCheck(setting);
    }
}

module.exports = iCheck;
