$.fn.limitHeight = function(options) {
    var defaults = {
        line: 1, //显示行数
        margin: 0,
        child: '', // 激活的子元素 被隐藏在下面 需要直接展示
        chlidActive: 'active'
    };

    return this.each(function() {
        var $box = $(this);
        var $readAll = $box.find('.read-all');
        var opts = $.extend(true, {}, defaults, options);
        var num = opts.line;
        var h = 0,
            lh = 1,
            fs = 1;

        $box.addClass('limit-height-box');

        lh = $box.css('line-height');
        fs = parseInt($box.css('font-size'));

        lh = lh - 0 == lh - 0  ? parseInt(lh * fs) : parseInt(lh);

        h = lh * num + parseInt($box.css('paddingTop')) + opts.margin; //获取显示高度

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

        if(opts.child) {
          let $child = $box.find(opts.child),
              activeW = 0,
              boxW = $box.innerWidth() - parseInt($box.css('paddingLeft')) - parseInt($box.css('paddingRight'));

          $child.each(function(index, el) {
              let $this = $(this);

              activeW += parseInt($this.innerWidth()) + parseInt($this.css('marginLeft')) + parseInt($this.css('marginRight'))
              if($this.hasClass('active')) return false;
          });
          // 如果被激活的内容在隐藏下下面 直接展开
         if(boxW < activeW) {
            $box.addClass('active');
            $readAll.show();
         }
        }

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
