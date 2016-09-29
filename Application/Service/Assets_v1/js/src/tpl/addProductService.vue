


<template>
    <form id="productService" action="" method="post" class="addTeam-form gutter-y clearfix">
        <div class="form-group col-xs-12">
            <label class="label-left vertical-top w-7em text-right">
                城市：
            </label>
            <div class="inline-block cityList">
                <input type="text" id="add_city" name="city" class="form-control" value="{{city}}">
            </div>
            <input id="location" type="hidden" name="service_location" value="{{location}}">
        </div>
        <div class="form-group col-xs-12">
            <label class="label-left vertical-top w-7em text-right">
                产品名称：
            </label>
            <div class="inline-block">
                <input type="text" class="form-control" name="product_name"  value="{{product_name}}" >
            </div>
        </div>
        <div class="form-group col-xs-12">
            <label class="label-left vertical-top w-7em text-right">
                联系QQ:
            </label>
            <div class="inline-block">
                <input type="text" class="form-control" name="qq" value="{{qq}}">
            </div>
        </div>
        <div class="form-group col-xs-12">
            <label for="" class="label-left vertical-top w-7em text-right">广告图：</label>
		    <div class="inline-block" id="ad_img" style="width:250px;">
            <div class="clearfix">
		      <div class="uploader-list" id="picker" name="file">点击上传</div>
		      <span>建议大小：260px * 240px</span>
            </div>
		      <div id="uploader-view">
		      	{{if advertising_url}}
                    <img style="width:100px;" src="{{advertising_url}}">
		      	{{/if}}
		      </div>
		    </div>
        </div>
		<div class="form-group col-xs-12">
            <div class="col-sm-9 col-sm-offset-2">
                <label class="gutter-right">
                    <input class="icheck" type="radio" name="service_state" checked  {{if state == 1}}checked{{/if}} value="1"> 发布中 
                </label>
                <label>
                    <input class="icheck" type="radio" name="service_state" {{if state == 0}}checked{{/if}} value="0"> 下架
                </label>
		      
		    </div>
        </div>
        <input type="hidden" id="img-url" name="advertising_url" value="{{advertising_url}}">
        <input type="hidden" name="service_id" value="{{id}}">
    </form>

</template>

