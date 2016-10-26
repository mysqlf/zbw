
<template>
    <form id="productService" action="" method="post" class="layer-form clearfix">

        <dl class="horizontal horizontal-5em">
            <dt class="left text-right">城市：</dt>
            <dd class="right size4">
                <div class="city-picker-input" >
                    <span class="picker-txt">请选择</span>
                    <input id="J_location" class="hidden-txt" type="hidden" name="location" required value="{{location}}" >
                </div>
            </dd>
        </dl>

        <dl class="horizontal horizontal-5em">
            <dt class="left text-right">产品名称：</dt>
            <dd class="right">
                <input type="text" class="ipt size4" name="product_name"  value="{{product_name}}" >
            </dd>
        </dl>

        <dl class="horizontal horizontal-5em">
            <dt class="left text-right">产品内容：</dt>
            <dd class="right size4">
                 <textarea class="textarea" name="content" required maxlength="500">{{content}}</textarea>
            </dd>
        </dl>

        <dl class="horizontal horizontal-5em">
            <dt class="left text-right">联系QQ：</dt>
            <dd class="right">
                 <input type="text" class="ipt size4" name="qq" value="{{qq}}">
            </dd>
        </dl>

        <!-- <dl class="horizontal horizontal-5em" style="z-index: 0">
            <dt class="left text-right">广告图：</dt>
            <dd class="right">
                <div class="upload-box">
                    <div class="uploader-list J_picker" id="picker" name="file">点击上传</div>
                    <button type="button" class="J_picker-btn" class="btn">上传</button>
                </div>
                 <div class="f-small">（建议大小：260px * 240px）</div>
                 <div><input type="hidden" id="img-url" name="advertising_url" required value="{{advertising_url}}"></div>
                 

                 <div id="uploader-view">
                    <img class="{{if !advertising_url}}dn{{/if}}" src="{{advertising_url}}">
                  </div>
            </dd>
        </dl> -->

        <dl class="horizontal horizontal-5em">
            <dt class="left text-right">状态：</dt>
            <dd class="right">
                <label class="icheck-label">
                    <input class="icheck" type="radio" name="service_state" checked  {{if state == 1}}checked{{/if}} value="1"> 发布中 
                </label>
                <label class="icheck-label">
                    <input class="icheck" type="radio" name="service_state" {{if state == 0}}checked{{/if}} value="0"> 下架
                </label>
            </dd>
        </dl>
        <input type="hidden" name="service_id" value="{{id}}">
    </form>

</template>

