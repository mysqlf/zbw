<!-- 修改密码 -->
<template>
    <div class="layer-wra">
        <form id="modifyPsw_form" method="post" class="modifyPsw-from clearfix">
<!--             <div class="form-items">
    <label for="lastPassword">原密码</label>
    <div class="ipt-box">
        <input type="password" name="lastPassword" class="ipt size3" required />
    </div>
</div> -->

            <dl class="horizontal horizontal-5em form-items form-newWord">
                <dt class="left text-right">原密码：</dt>
                <dd class="right ipt-box">
                    <input type="password" name="lastPassword" class="ipt size3" required />
                </dd>
            </dl>
            <dl class="horizontal horizontal-5em form-items form-newWord">
                <dt class="left text-right">新密码：</dt>
                <dd class="right ipt-box">
                    <input type="password" name="password" id="password" class="ipt  size3  new-ipt" required maxlength="20" />
                    <em class="pass-icon eye_icon J_show-psw">
                        <i class="icon icon-eyes"></i>
                    </em>
                    <span class="psw-level">
                        <i class="level-txt">低</i>
                        <i class="level-item level-item1"></i>
                        <i class="level-item level-item2"></i>
                        <i class="level-item level-item3"></i>
                    </span>
                </dd>
            </dl>
            <dl class="horizontal horizontal-5em form-items form-newWord">
                <dt class="left text-right">确认密码：</dt>
                <dd class="right ipt-box">
                    <input  type="password" name="comfirmPassword" class="ipt size3" required />
                </dd>
            </dl>
           <!--  <div class="form-items ">
               <label for="password">新密码</label>
               <div  class="ipt-box">
                   <input type="password" name="password" id="password" class="ipt form-newWord size3" required />
                   <em class="pass-icon eye_icon J_show-psw">
                       <i class="icon icon-eyes"></i>
                   </em>
                   <span class="psw-level">
                       <i class="level-txt">低</i>
                       <i class="level-item level-item1"></i>
                       <i class="level-item level-item2"></i>
                       <i class="level-item level-item3"></i>
                   </span>
               </div>
           </div> -->
            <!-- <div class="form-items">
                <label for="comfirmPassword">确认密码</label>
                <div class="ipt-box">
                    <input  type="password" name="comfirmPassword" class="ipt size3" required />
                </div>
            </div> -->
        </form>
    </div>
	
</template>
<script type="text/javascript">
    let { showPsw, togglePswStronge} = require('modules/form');
    module.exports = {
        init(){
            let self = this;
            showPsw();
            togglePswStronge();

        },
    }
</script>