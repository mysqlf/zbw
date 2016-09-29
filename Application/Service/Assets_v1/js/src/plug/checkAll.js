// 全选
exports.checkAll = function(opts) {
    var defaults = {
        checkAll: '.icheck-all',
        checks: '.single-icheck'
    }

    var setting = $.extend({}, defaults, opts);
    var $ichecks = $(setting.checks);
    var $checkAll = $(setting.checkAll);

    $checkAll.on('change', function() {alert(1)
        var $this = $(this);
        console.log($checkAll)
        if ($this.is(':checked')) {
            $ichecks.prop('checked', true);
        } else {
            $ichecks.prop('checked', false);
        }
    })

    $ichecks.on('change', function() {
        var len = $ichecks.filter(':checked').length;

        if (len > 0 && len === $ichecks.length) {
            $checkAll.prop('checked', true);
        } else if (len > 0 && $checkAll.is(':checked')) {
            $checkAll.prop('checked', false);
        }
    })
}
