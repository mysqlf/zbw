// 代发工资和申报操作
exports.businessHandler = () => {
    let template = require('art-template'),
        { updateSalaryOrder, updateInsuranceOrder } = require('api/business'),
        checkFn = require('plug/icheck/index');

    $('.payroll-handle').click(function(e) {
        let checkLen = $('.single-icheck').filter(':checked').length;

        if (checkLen === 0) {
            layer.msg('请选择记录~');
        } else {

            let { act, page } = $(this).data(),
                html = '',
                selectedData = $('#J_salary-form').serializeArray();// 获取选中的checkbox

            // 批量审核、批量发放
            if (act) {

                let $form = null,
                    title = '',
                    rules = {},
                    messages = {},
                    tplData = {},
                    updateAct = page == 'declare' ? updateInsuranceOrder : updateSalaryOrder;

                switch (act) {
                    case 'batch_audit':
                        tplData = {
                            value: 1,
                            text: '审核'
                        };
                        title = '批量审核';
                        break;
                    case 'batch_transact':
                        tplData = {
                            value: 3,
                            text: '办理'
                        };
                        title = '批量办理';
                        break;

                    default:
                        tplData = {
                            value: 3,
                            text: '发放'
                        };
                        title = '批量发放';
                        break;
                }


                html = template.render(require('tpl/batch_audit.vue').template)(tplData);

                layer.open({
                    title,
                    skin: 'batch-layer',
                    content: html,
                    btn: ['确定', '取消'],
                    yes() {
                        $form.submit();
                    },
                    success(){
                        $form = $('.batch-layer form');
                        checkFn.iCheck();
                        checkFn.checkAll();
                        $form.validate({
                            submitHandler(form){
                                let formData = $(form).serializeArray();

                                updateAct(formData.concat(selectedData), ({ msg = title + '成功' }) => {
                                    layer.msg(msg, ()=> {
                                        location.href = location.href;
                                    });

                                }, ({ msg = title + '失败' }) => {
                                    layer.msg(msg);
                                })
                            },
                            rules,
                            messages
                        });
                    }
                });
            }
        }
    });
}