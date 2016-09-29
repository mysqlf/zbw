let { changeData } = require('api/index'),
    swiper = require('plug/swiper/index');

module.exports = {
    init() {
        var calTpl = require('tpl/calculator.vue');
        new calTpl.calTpl().init();
        this.queryTool();
        // this.changeQueryList();

        $('.sidebar-Qcode').hover(function() {
            $('.Qcode').show();
        }, function() {
            $('.Qcode').hide();
        });

    },
    queryTool() {
        //查询工具切换数据
        $('.query-tool-list li').click(function() {
            $(this).addClass('active').siblings().removeClass('active');
            var id = parseInt($(this).val());
            if (!id) return false;
            changeData({ 'id': id }, function(msg) {
                $('.query-tool-cnt').html(msg.data);
            });
        });
    }
}
