let userAct = require('api/bill');
require('plug/validate/index');

module.exports = {
    invoiceValidate: function($form) {
        $form.validate({
            submitHandler: function() {
                var data = {
                    'id': 111,
                    'status': 1,
                    'invoice_amount': $('.layer-amount em').text() - 0,
                    'invoice_express_company': $('[name="invoice_express_company"]').val(),
                    'invoice_express_no': $('[name="invoice_express_no"]').val(),
                    'invoice_consignee': $('[name="invoice_consignee"]').val(),
                    'invoice_consignee_phone': $('[name="invoice_consignee_phone"]').val()
                };

                userAct.comBillpayment(data, function(json) {
                    layer.msg(json.msg, function() {
                        layer.closeAll();
                    });
                });
            },
            rules: {

            },
            message: {

            }

        });
    }

}
