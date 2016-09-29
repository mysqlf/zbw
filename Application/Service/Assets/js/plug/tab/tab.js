! function($) {
    $.fn.tab = function(options) {

        var defaults = {
            events: 'click'
        }

        return this.each(function(index, el) {
            var $el = $(this);

            var opts = $.extend({}, defaults, $el.data(), typeof options == 'object' && options);

            $el.on(opts.events, '.tab-toggle>li>a', function() {
                var target = $(this).data('target');

                $el.trigger('change.tab', target);
            })

            $el.on('change.tab', function(e, target) {
                var $target = $el.find('.tab-toggle>li>a[data-target="' + target + '"]');

                if ($target.length) {
                    $el.find('.tab-toggle>li>a').removeClass('active');
                    $target.addClass('active');

                    $el.find('.tab-content .item').removeClass('active');
                    $(target, $el).addClass('active');

                    $el.trigger('show.tab', $el)
                }
            })
        });
    }
}(jQuery);
