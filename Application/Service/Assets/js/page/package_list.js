//服务套餐管理
let icheckFn = require('plug/icheck/index');

require('plug/validate/index');
require('plug/boxSelector/jquery.boxSelector');

let modules = {
    init() {

        let { delProduct } = require('api/package');

        // 删除套餐
        $('[data-act="removePackage"]').click(function() {
            let $this = $(this),
                { flag = false, product_id } = $this.data();

            if (flag) {
                return;
            }

            $this.data('flag', true);

            layer.confirm('你确定要删除该套餐吗', {
                title: '删除套餐',
                btn: ['删除', '取消'],
                yes(index) {

                    delProduct({ product_id }, () => {

                        $this.data('flag', false);
                        $this.closest('tr').remove();
                        layer.msg('删除成功');
                        layer.close(index);
                    }, ({ msg = '删除失败' }) => {
                        layer.msg(msg);
                    })

                },
                cancle() {
                    $this.data('flag', false);
                }
            })
        });

    },
    detailInit() {
        let self = modules;
        // 选择地区
        $("#btn_serviceCity").boxSelector({
            key: "city",
            selectType: "checkbox",
            max: 10,
            selectParent: false,
            closed() {
                $('#btn_serviceCity').attr('title', $('#name_serviceCity').val())
            }
        });
        icheckFn.iCheck();
        self.validate();

        $('[name="service_price_state"]').on('ifChecked', function() {
            let $ipt = $('[name="member_price"]');

            $ipt.addClass('ignore');
            $ipt.removeClass('validator-error');
            $ipt.siblings('span.error').remove();

        });

        $('[name="service_price_state"]').on('ifUnchecked', function() {
            $('[name="member_price"]').removeClass('ignore');

        });

    },
    validate() {
        $('#J_form').validate({
            submitHandler(form) {
                let $form = $(form),
                    data = $form.serializeArray(),
                    { flag } = $form.data(),
                    { saveProductDetail } = require('api/package');

                if (flag) {
                    return;
                }

                if (UEeditor.getContent()) {
                    $form.data('flag', true);

                    saveProductDetail(data, ({ msg = "保存成功" }) => {
                        layer.msg(msg, () => {
                            location.href = "/Service-Product-productList";
                        });
                    }, ({ msg = "保存失败" }) => {
                        layer.msg(msg);
                    }).complete(() => {

                        $form.data('flag', false);
                    });
                } else {
                    layer.msg('请填写产品详情');
                }



                return false;
            },
            rules: {
                member_price: {
                    min: 0
                },
                'service_price[]': {
                    min: 0
                }
            },
            messages: {
                member_price: {
                    min: '套餐费不能小于0'
                },
                'service_price[]': {
                    min: '服务费不能小于0'
                }
            }

        });
    }
}


module.exports = modules;
