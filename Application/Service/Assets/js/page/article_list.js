/**
 * 文章列表
 */
let { removeArticle } = require('api/content'),
    dateFn = require('plug/datetimepicker/index'),
    { dateRange } = require('modules/date');

module.exports = {
    init() {

    	dateFn.getYearMonthDay();
    	dateRange('create');

        $('[data-act="removeArticle"]').click(function() {
            let $this = $(this),
                id = $this.data('id'),
                $tr = $this.closest('tr');

            layer.confirm('确定要删除该文章？', () => {
                removeArticle({ id }, () => {
                    layer.msg('修改成功');
                    $tr.remove();
                }, ({ msg = '修改失败' }) => {
                    layer.msg(msg);
                })
            })

        })
    }
}
