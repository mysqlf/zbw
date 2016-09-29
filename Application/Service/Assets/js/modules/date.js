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
            $end.datetimepicker('setStartDate', $begin.val());

        });

        $end.on('changeDate', function(ev){
            $begin.datetimepicker('setEndDate', $end.val());

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
