require('plug/validate/index');

let uploader = require('modules/upload'),
    tpl = require('plug/artTemplate'),
    batchTable = require('tpl/batch-table.vue'),
    { proUrl, batchFrom, salaryForm, getSalaryServiceProductOrder } = require('api/insurance'), //API文件
    toIncrease = require('page/toIncrease');

let toIncreaseBatch = {
    init() {
        let self = this;
        self.uploadExcel();
        toIncrease.vipSelect();

        if ($('#salary').length !== 1) {
            toIncrease.locationSelect();
        }

        toIncrease.serviceSelect();
        toIncrease.selectChange();

        self.showSelect();
        self.validate();
    },
    //下载

    //上传excel
    uploadExcel() {

        let category = 1,
            excelOpts = null;

        if ($('#salary').length == 1) {
            category = 2;

        }
        excelOpts = {
            server: '/Service-Business-uploadTemplateFile',
            formData: {
                category
            },
            accept: {
                title: 'Application',
                extensions: 'xls,xlsx',
                mimeTypes: 'applicationnd.ms-excel'
            }
        }

        let excelUpload = uploader.uploadCreate(excelOpts);

        uploader.uploadError(excelUpload);
        excelUpload.on('uploadSuccess', (file, response) => {
            if (response.status === 0) {

                $('#fileName').val(file.name);
                $('#filePath').val(response.msg);
            } else {
                layer.alert(response.msg);
            }
        })
    },
    //下拉框联动显示
    showSelect() {

        let $vip_select = $('#vip_select'),
            $location = $('#location');

        // 获取套餐详情
        $('#userId').change(function() {
            let companyUserId = $(this).val(),
                defaultTpl = `<option value="">请选择</option>`,
                html = defaultTpl;

            if (companyUserId === '') {
                $vip_select.html(defaultTpl).selectOrDie('update');
                $location.html(defaultTpl).selectOrDie('update');
                return
            }

            getSalaryServiceProductOrder({ companyUserId }, ({ result }) => {

                for (let i in result) {
                    html += `<option value="${result[i].product_id}">
                            ${result[i].product_name}
                            </option>`

                }

                $vip_select.html(html).selectOrDie('update');
                $location.html(defaultTpl).selectOrDie('update');
            })
        })

        //会员套餐发送请求公积金类型
        $vip_select.change(function() {
            let $this = $(this);
            if ($this.val() !== "") {
                $('#location_box').removeClass('hide');
            } else {
                $('#location_box').addClass('hide');
            }
        });
        //project_box
        $('#socialType').on('change', '.select_socialType', function() {
            let $this = $(this);
            if ($this.val() !== "") {
                $('#project_box').removeClass('hide');
            } else {
                $('#project_box').addClass('hide');
            }
        });

        //社保类型
        $('#project').change(function() {
            let $this = $(this),
                socRuleId = $this.find('option:selected').val(),
                templateId = $('#templateId').val(),
                companyId = $('#vip_select').find('option:selected').data('companyid'),
                dataJson = {
                    'type': 2,
                    'templateId': templateId,
                    'companyId': companyId
                };

            $('#socRuleId').val(socRuleId);
            proUrl(dataJson, (data) => {

                let result = data.result,
                    html = '<option value="">请选择</option>';
                for (let i = 0, len = result.length; i < len; i++) {
                    html += `<option value="${result[i].id}">
                            ${result[i].name}
                            </option>`
                }
                $('#proProject').html(html).selectOrDie('update');

            }, (msg) => {
                layer.alert(msg);
            });
        });

        //公积金类型
        $('#proProject').change(function() {
            let $this = $(this),
                proRuleId = $this.find('option:selected').val();
            $('#proRuleId').val(proRuleId);
        });
    },
    //验证导入
    validate() {
        $("#submitBtn").click(function() {
            $('#batchForm').submit();
        });
        let validator = $('#batchForm').validate({
            submitHandler(form) {
                let dataJson = $(form).serializeArray();
                if ($('#salary').length == 1) {
                    salaryForm(dataJson, (data) => {

                        let result = data.result.data,
                            tableData = [];
                        for (let i in result) {
                            tableData.push(result[i]);
                        }

                        $('#batch_table').html((tpl.render(batchTable.template)({ tableData }))).show();
                        // $('#batch_table').find('tbody tr:odd').find('td').addClass('even_bg');

                    }, ({ msg }) => {
                        if (msg) {
                            layer.alert(msg);
                        }
                    })
                } else {
                    batchFrom(dataJson, (data) => {

                        let result = data.result.data,
                            tableData = [];
                        for (let i in result) {
                            tableData.push(result[i]);
                        }

                        $('#batch_table').html((tpl.render(batchTable.template)({ tableData }))).show();
                        // $('#batch_table').find('tbody tr:odd').find('td').addClass('even_bg');

                    }, ({ msg }) => {
                        if (msg) {
                            layer.alert(msg);
                        }
                    })
                }


            }
        });
    }
}
module.exports = toIncreaseBatch;
