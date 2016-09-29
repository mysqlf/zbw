require('plug/selectordie/index');
require('plug/validate/validate');

let uploader = require('modules/upload'),
    tpl = require('plug/artTemplate'),
    batchTable = require('tpl/batch-table.vue'),
    { proUrl, batchFrom, salaryForm } = require('api/Insurance'), //API文件
    toIncrease = require('page/Insurance/toIncrease');
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
        $('.select').selectOrDie();
        self.showSelect();
        self.validate();
    },
    //下载

    //上传excel
    uploadExcel() {
        if ($('#salary').length == 1) {
            var excelOpts = {
                server: '/Company-Salary-uploadTemplateFile',
                accept: {
                    title: 'Application',
                    extensions: 'xls,xlsx',
                    mimeTypes: 'applicationnd.ms-excel'
                }
            }
        } else {
            var excelOpts = {
                server: '/Company-Insurance-uploadTemplateFile',
                accept: {
                    title: 'Application',
                    extensions: 'xls,xlsx',
                    mimeTypes: 'applicationnd.ms-excel'
                }
            }
        }
        let excelUpload = uploader.uploadCreate(excelOpts);

        uploader.uploadError(excelUpload);
        excelUpload.on('uploadSuccess', (file, response) => {
            if (response.status == 1) {

                $('#fileName').val(file.name);
                $('#filePath').val(response.info);
            } else {
                layer.alert(response.info);
            }
        })
    },
    //下拉框联动显示
    showSelect() {
        //会员套餐发送请求公积金类型
        $('#vip_select').change(function() {
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
                    // html += `<option value="${result[i].id}">
                    //         ${result[i].name}
                    //         </option>`
                    html +=`<option data-minamount="${result[i].minAmount}" data-maxamount="${result[i].maxAmount}" data-id="${result[i].id}" data-personscale="${result[i].personScale}" data-companyscale="${result[i].companyScale}" >
                        ${result[i].name}
                        </option>`;
                }
                $('#proProject').html(html).selectOrDie('update');

            }, (info) => {
                layer.alert(info);
            });
        });
        //公积金类型
        $('#proProject').change(function() {
            let $this = $(this);
            let proRuleId = $this.find('option:selected').data('id');
            $('#proRuleId').val(proRuleId);
        });
    },
    //验证导入
    validate() {
        $("#submitBtn").click(function() {
            $('#batchForm').submit();
        });
        $('#batchForm').validate({
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

                    }, ({ info }) => {
                        if (info) {
                            layer.alert(info);
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

                    }, ({ info }) => {
                        if (info) {
                            layer.alert(info);
                        }
                    })
                }


            }
        });
    }
}
module.exports = toIncreaseBatch;
