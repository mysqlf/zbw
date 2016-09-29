<template>
	<div class="passWord_tpl">
		<form id="listForm">
			<div class="inp_box block">
				<label>原密码</label>
				<input type="password" class="text" value="" name="oldPassword" required>
			</div>
			<div class="input-box">
				<div class="inp_box block">
					<label>新密码</label>
					<input type="password" class="text" value="" name="newPassword" required maxlength="20" id="J_pswStronge">
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
				<div class="inp_box block mb">
					<label>密码确认</label>
					<input type="password" class="text" value="" name="comfirmPassword" required>
				</div>
			</div>
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