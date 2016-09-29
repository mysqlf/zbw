let checkFn = require('plug/icheck/index'),
    { businessHandler } = require('modules/business'), // 代发工资和申报操作,
    dateFn = require('plug/datetimepicker/index'),
    { countDown } = require('modules/date');

let companyDeclare = {
    init() {
        checkFn.init();
        checkFn.checkAll();
        dateFn.getYearMonth();
        businessHandler();

        $('.deadline').each(function() {
            let $this = $(this),
                value = $this.text().trim(),
                end = new Date(value),
                { timer } = $this.data();

            if (value == '/') {
                $this.html(`/`);

            } else {
                if (end.getTime() < new Date().getTime()) {
                    $this.html(`已截止`);

                } else {

                    timer = setInterval(function() {
                        let ret = countDown(end),
                            {
                                dd,
                                hh,
                                mm,
                                ss
                            } = ret;


                        if (!ret || end.getTime() < new Date().getTime()) {
                            clearInterval(timer);
                        }

                        $this.html(`${dd}天${hh}小时${mm}分${ss}秒`)
                    }, 1000)

                    $this.data('timer', timer);
                }

            }
        });
    }
}

module.exports = companyDeclare;
