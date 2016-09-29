 /**
 * 时间范围限制
 * @param  {string} ids id前缀
 * @return {null}    
 */
exports.dateRange = (...ids) => {

    ids.forEach( function(id, index) {
        let $begin = $('#'+ id +'-begin'),
        $end = $('#'+ id +'-end');

        $begin.on('changeDate', function(ev){

            $end.datetimepicker('setStartDate', ev.date);
        });

        $end.on('changeDate', function(ev){
            $begin.datetimepicker('setEndDate', ev.date);
        });
    });

}


exports.countDown = (end, begin) => {

    let begins = begin ?  new Date(begin) : new Date(),
        ends = new Date(end),
        checkTime = exports.checkTime;

    if(ends == 'Invalid Date' || begins == 'Invalid Date'){
        return false;
    }
    let ts = ends - begins, //计算剩余的毫秒数  
        dd = parseInt(ts / 1000 / 60 / 60 / 24, 10), //计算剩余的天数  
        hh = parseInt(ts / 1000 / 60 / 60 % 24, 10), //计算剩余的小时数  
        mm = parseInt(ts / 1000 / 60 % 60, 10),//计算剩余的分钟数  
        ss = parseInt(ts / 1000 % 60, 10); //计算剩余的秒数  

        dd = checkTime(dd);
        hh = checkTime(hh);
        mm = checkTime(mm);
        ss = checkTime(ss);
        return {
            dd,
            hh,
            mm,
            ss
        }
}

exports.checkTime = (i) => {
    if (i > 0 && i < 10) {
         i = "0" + i;
     }
     return i;
}


exports.countDownFn = (el) => {
    let $el = el ? $(el) : $('.J_pay-deadline');

    $el.each(function(){
            let $this = $(this),
                value = $this.text(),
                end = new Date(value),
                text = '',
                { timer } = $this.data(),
                endTime = end.getTime();

            if(endTime !== endTime ){
                $this.html(`/`)
            } 
            else if(endTime < new Date().getTime()){
                $this.html(`已截止`)
            }
            else {

                timer = setInterval(function(){
                    let ret = exports.countDown(end),
                    {
                        dd,
                        hh,
                        mm,
                        ss
                    } = ret;


                    if(!ret || end.getTime() < new Date().getTime()) {
                        clearInterval(timer);
                    }

                    $this.html(`${dd}天${hh}小时${mm}分${ss}秒`)
                },1000)

                $this.data('timer', timer);
            }
        })
}