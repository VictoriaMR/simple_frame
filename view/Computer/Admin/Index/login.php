<?php $this->load('Common.baseHeader');?>

<div id="login-bg">
	<img src="<?php echo $bg_img;?>">
</div>

<div class="login-box">
	<div class="poptip-content">
		扫码登录更安全
	</div>
	<div class="title">
		<a class="font-16 font-800 select" href="javascript:void(0);">密码登录</a>
		<a class="font-16 font-800" href="javascript:void(0);">短信登录</a>
	</div>
	<div class="clear"></div>
	<form class="margin-top-50">
		<div class="margin-bottom-20">
			<input type="input" class="input" name="phone" placeholder="请输入手机号码" autocomplete="off">
		</div>
		<div class="margin-bottom-20">
			<input type="password" class="input" name="password" placeholder="请输入密码" autocomplete="off">
		</div>
		<div class="btn font-16 font-600 pointer">登录</div>
	</form>
</div>
<script>
$(document).ready(function(){
	LOGIN.init();
});
</script>
<?php $this->load('Common.baseFooter');?>