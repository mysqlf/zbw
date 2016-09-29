$.fn.limitHeight = function(options) {
    var defaults = {
        line: 1, //显示行数
        margin: 0
    };

    var opts = $.extend(true, {}, defaults, options);
    var num = opts.line;
    var h = parseInt(this.css('line-height')) * num + parseInt(this.css('paddingTop')) + opts.margin; //获取显示高度
    var el = this;

    return this.each(function() {
        var $box = $(this);
        var $readAll = $box.find('.read-all');

        $box.addClass('limit-height-box');

        if(!$readAll[0]) {
            $readAll = $('<span class="read-all"></span>').appendTo($box);
        }

        $readAll.off('click.readymore');
        $readAll.on('click.readymore', function() {

            let $this = $(this),
                $scope = $this.closest('.limit-height-box');

            if($scope.hasClass('active')) {
                $scope.removeClass('active')
                    .css({
                        maxHeight: h + 'px'
                    });
            } else {
                $scope.addClass('active')
                    .css({
                        maxHeight: 'none'
                    });
            }

        });

        setTimeout(function() {
            if ($box.hasClass('active')) {

                $box.css({
                        maxHeight: 'none'
                    });
                return true;
            };

            if ($box.innerHeight() > h) {
                $readAll.show();
            }

            $box.css({
                maxHeight: h + 'px'
            });

        }, 0)
    });

}
