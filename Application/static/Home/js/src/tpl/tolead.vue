<template>
<form class="tolead fromTable" id="tolead-form">
    <div class="tit">
        工资月份：<span id="abortAddDelDate">{{products[0].abortAddDelDateValue}}</span>
    </div>
    <table class="formTable">
        <tr>
            <td class="name">导入工资单：</td>
            <td id="treaty">
				<div class="uploading fl" style="display: none;">
					<div class="loading"><span></span></div>
				</div>
				<div class="uploaded fl" style="display: none;">
					<a href="javascript:;" class="preview jSrc jProtocol" data-src="">上传成功</a>
				</div>
				<div class="upload fl">
					<a href="javascript:;" class="uploadBtn" data-act="protocol">uploadBtn</a>
					<div class="jUpload">
						<div class="upload_inpt fl">
							<input class="zb_inpt" type="text" name="filePath" data-act="upload" readonly value="" />
							<span>浏览</span>
						</div>
					</div>
					<a href="#" class="toloadBtn" style="display: none;">重新上传</a>
				</div>
				<a href="/Company-Salary-downloadSalaryTemplate-type-xls.html" class="fl download">下载工资单模板</a>
			</td>
        </tr>
        <tr>
            <td class="name">服务产品：</td>
            <td>
            	<select id="productOrderId" name="productOrderId" required class="zb_inpt gzj-change">
					<option value="">请选择</option>
					{{each products}}
					<option value="{{$value.id}}" data-time="{{$value.abortAddDelDateValue}}">{{$value.product_name}}</option>
					{{/each}}
				</select>
            </td>
        </tr>
        <tr>
            <td class="name">城市：</td>
            <td>
            	<select id="location" name="location" required class="zb_inpt gzj-change">
					<option value="">请选择</option>

				</select>
            </td>
        </tr>
    </table>
</form>
</template>