let template = require('art-template'),
    userAct = require('api/bill'),
    dateFn = require('plug/datetimepicker/index');

require('plug/validate/index');

let bill = {
    init() {
        let self = this;

        dateFn.getYearMonth();

        $('[data-act="invoice"]').click(function() {
            let $amount = $(this).data('price');

            let data = {
                id: $('[name="id"]').val(),
                amount: $amount,
                company: $('[name="company_name"]').val(),
                contact_name: $('[name="contact_name"]').val(),
                contact_phone: $('[name="contact_phone"]').val()
            }

            layer.open({
                title: '开具发票',
                content: template.render(require('tpl/invoice.vue').template)(data),
                btn: ['确定', '取消'],
                area: '500px',
                yes: function() {
                    $('#J_invoice-form-layer').submit();
                },
                success() {
                    self.validate()
                }
            });
        })
    },
    validate() {
        $('#J_invoice-form-layer').validate({
            submitHandler: function() {
                let data = {
                    'id': $('[name="id"]').val(),
                    'status': 1,
                    'invoice_amount': ($('[data-act="invoice"]').data('price') + '').split(',').join('') - 0,
                    'invoice_express_company': $('[name="invoice_express_company"]').val(),
                    'invoice_express_no': $('[name="invoice_express_no"]').val(),
                    'invoice_consignee': $('[name="invoice_consignee"]').val(),
                    'invoice_consignee_phone': $('[name="invoice_consignee_phone"]').val()
                };

                userAct.comBillpayment(data, ({ msg }) => {
                    layer.msg(msg);

                }, ({ msg }) => {
                    layer.msg(msg);

                });
            },
            rules: {
                invoice_consignee_phone: {
                    istelephone: true
                }
            },
            message: {

            }

        });
    }
}

module.exports = bill;
