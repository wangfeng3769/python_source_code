<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>易多汽车共享官网 - 中国首家自助快捷租车网，提供分时、自助的绿色出行方式</title>
<link href="css/yiduo.css" rel="stylesheet" type="text/css" />
<style>
a{color:#4F4F4F;text-decoration:none;}
.anniu-1:active{ background:url(img/anniu-1-2.png) no-repeat; color:#4F4F4F;}
.anniu-2:active{ color:#4F4F4F;}
.anniu-3:active{ color:#4F4F4F;}
</style>
<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="js/frm.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#vBtnConfirm").click(function() {
			var userName = $("#vInputUserName").val();
			var password = $("#vInputPassword").val();
			if ("" == userName) {
				alert("用户名不能为空。");
				return;
			}

			SA.get(SA.SERVERAPI + "?class=UserManager&item=login&user_name=" + userName + "&password=" + password, function() {
				alert("登录成功。");
				
				window.location = "/";
			});
		});
	}); 
</script>
</head>
<body>
	<?php include 'header.html';?>
<div class="denglu">
<div class="denglu-1">
<p class="denglu-1-1">登陆</p>
<span class="dl-shuru"><p class="denglu-1-2" style="width:100px">用户名：</p><input id="vInputUserName" name="" type="text" class="dl-sr" /></span>
<p class="denglu-1-2" style="width:100px">密码：</p><input id="vInputPassword" name="" type="password" class="dl-sr" />
<p class="denglu-1-2" style="width:190px"><input name="" type="radio" value="" class="dl-dx" />记住密码</p>
<p class="denglu-1-2" style="width:350ipx"><a id="vBtnConfirm" href="#" class="anniu-1">确定</a> <a href="#" class="anniu-2"/>忘记密码？</a></p>
<p class="line-1"></p>
<p class="denglu-1-2" style="width:180px">使用合作网站登陆</p>
<span class="weibo-left">
<p class="weibo"><a href="../openPlatForm/renren/login.php?req_agent=web"><img src="img/renren.jpg"/><b class="weibo-txt">人人网</b></a></p>
<p class="weibo"><a href="../openPlatForm/qq/login.php?req_agent=web"><img src="img/tengxun.jpg"/><b class="weibo-txt">腾讯微博</b></a></p>
<p class="weibo"><a href="../openPlatForm/weibo/login.php?req_agent=web"><img src="img/xinlang.jpg"/><b class="weibo-txt">新浪微博</b></a></p>
</span>
<span class="weibo-right"><p class="denglu-1-2"style="width:120px">还没有账号？</p><a href="register.php" class="anniu-3">免费注册</a></span>
</div>

</div>
<?php include 'footer.html';?>
</body>
</html>
