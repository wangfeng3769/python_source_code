<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>易多汽车共享官网 - 中国首家自助快捷租车网，提供分时、自助的绿色出行方式</title>
<link href="css/yiduo.css" rel="stylesheet" type="text/css" />
<style>
a{color:#4F4F4F;text-decoration:none;}
.anniu-1:active{ background:url(img/anniu-1-2.png) no-repeat; color:#4F4F4F;}
.anniu-4:active{ background:url(img/anniu-2-2.png) no-repeat; color:#4F4F4F;}
.anniu-2:active{ color:#4F4F4F;}
.anniu-3:active{ color:#4F4F4F;}
.anniu-5:active{ background:url(img/anniu-3-2.png) no-repeat; color:#4F4F4F;}
.anniu-9:active{ background:url(img/anniu-1-2.png) no-repeat; color:#4F4F4F;}
.xieyi:active{color:#4F4F4F; text-decoration:underline;}
</style>
<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="js/frm.js"></script>
<script type="text/javascript">
	function checkPhone() {
		var phone = $("#phone").val();
		if ('' == phone || isNaN(phone) || 11 != phone.length) {
			alert("请填写11位的手机号码。");

			return false;
		}

		return true;
	}

	function onGetCaptcha() {
		if (! checkPhone()) {
			return false;
		}

		var phone = $("#phone").val();
		SA.get(SA.SERVERAPI + '?class=SmsCaptcha&item=get&phone=' + phone, function() {
			alert("验证码已发送，请查收。");
		});

		var btn = $('#vGetCaptcha');
		btn.attr("disabled", true); 

		var time = 59;
		var timer = setInterval(function() {
			btn.text(time + "秒后重新获取"); --time;
			if (0 == time) {
				clearInterval(timer);

				btn.text("免费获取验证码");
				btn.attr("disabled", false); 
			}
		}, 1000);
	}

	function register() {
		if (! checkPhone()) {
			return false;
		}

		var phone = $("#phone").val();
		var password = $("#password").val();
		var captcha = $("#captcha").val();

		if (document.getElementById('password').value == "") {
			alert("请输入密码");
			document.getElementById('password').focus();
		} else if (document.getElementById('captcha').value == "") {
			alert("请输入手机验证码");
			document.getElementById('captcha').focus();
		} else {
			SA.get(SA.SERVERAPI + '?class=UserManager&item=captchaRegister&phone=' + phone + '&password=' + password + '&captcha=' + captcha, function() {
				window.location = "authentication.php";
			});
		}
	}


	$(document).ready(function() {
		$("#vGetCaptcha").click(onGetCaptcha);
		$("#vBtnConfirm").click(register);
	});
</script>
</head>

<body>
<?php include 'header.html';?>
	<div class="zhuce">
<div class="zhuce-1">找回密码</div>
		<div class="renzheng-1" style=" margin: 10px 10px 10px 230px" >
<p class="chengse24-2" align="center" style="margin-left:150px;">填写手机号，免费获取验证码，快速找回密码 </p>
<span class="zhuce-1-1"><p class="denglu-1-2" style="width:150px">手机号：</p><input id="phone" name="" type="text" class="zhuce-sr"  style="width:225px"/></span>
<p class="sr-cw" style="display:none;">您输入的手机号码错误，请重新输入！</p>
<span class="zhuce-1-1"><p class="denglu-1-2" style="width:150px">新密码：</p><input id="password" name="" type="password" class="zhuce-sr"  style="width:225px" />
</span>
<p class="sr-cw" style="color:#585858;">密码只能使用英文字母或数字，长度6-16字符</p>
<span class="zhuce-1-1"><p class="denglu-1-2" style="width:150px">重复密码：</p><input id="password" name="" type="password" class="zhuce-sr"  style="width:225px" />
</span>
<span class="zhuce-1-1"><p class="denglu-1-2" style="width:150px">验证码：</p><input id="captcha" name="" type="text"  class="zhuce-sr"  style="width:80px" /><button id="vGetCaptcha" class="anniu-9" style=" width:148px; height:43px; border:0">免费获取验证码</button> </span>
<p class="sr-cw" style="display:none;">输入验证码超时，请重新获取!</p>
<p class="sr-cw" style="display:none;">验证码错误，请重新获取!</p>
<span class="zhuce-1-1"><p class="denglu-1-2" style="width:360px"><a id="vBtnConfirm" href="#" class="anniu-5">确认修改</a></p></span>

		</div>

	</div>		
<?php include 'footer.html';?>
</body>
</html>
