/**
 * 公用样式
 */

require('plug/selectordie');

let interact = {
    init() {
        let self = this;

        self.select();

        self.showSidebar();

        self.tab();

        self.setList();

        self.setTableHeight();

        $('[data-act="submit"]').click(function() {
            $(this).closest('form').submit();
        })
    },
    // 下拉
    select() {
        $("select").selectOrDie();

        // select 失去焦点不校验
        $('body').on('change.validate', 'select,.city-picker-input .hidden-txt', function() {
            let validator = $(this).closest('form').data('validator');

            if (validator) {
                validator.element(this);
            }
        })
    },
    // 侧边栏
    showSidebar() {
        $('.sidebar dl').each(function(index, el) {
            var $this = $(this);
            if ($this.hasClass('active')) {
                $this.children('dd').show();
            }
            $this.click(function(event) {
                $this.addClass('active').siblings('dl').removeClass('active');
                $this.children('dd').slideDown(300);
                $this.siblings('dl').children('dd').slideUp(300);
            });
        });
    },
    // 查询选项切换
    tab() {
        $('.query-tags-wra > span').each(function() {
            var $this = $(this);

            $this.click(() => {
                $this.addClass('active');
                $this.siblings('span').removeClass('active');
            });
        });
    },
    // 头部设置
    setList() {
        $('.header-set').hover(function() {
            $('.header-set').addClass('active');

        }, function() {
            $('.header-set').removeClass('active');

        });
    },
    setTableHeight() {
        if (!!window.ActiveXObject || "ActiveXObject" in window) {
            setHeight();

            $(window).resize(function() {
                let $offWidth = document.body.offsetWidth,
                    $screenWidth = window.screen.width;

                if ($offWidth < $screenWidth) {
                    setHeight();
                }

            });

        }

        function setHeight() {
            $('table.inner-tbl').each(function() {
                let $this = $(this),
                    $height = $this.closest('tr').height();

                $this.height('84px');
            });
        }

    }
}

module.exports = interact;
