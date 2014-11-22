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
.anniu-4:active{ background:url(img/anniu-2-2.png) no-repeat; color:#4F4F4F;}\
.anniu-5:active{ background:url(img/anniu-3-2.png) no-repeat; color:#4F4F4F;}
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
<div class="dingche">
<div class="zhuce" style="width:900px;height:100%;">
<span class="zhuce-1">订单修改成功，请支付订车差价</span>
<div class="dingdan-xinxi"><p class="dingdan-xinxi-1">已经冻结押金：<b class="chengse">600.00</b>元</p><p class="dingdan-xinxi-1">补交使用押金：<b class="chengse">200.00</b>元</p><p class="dingdan-xinxi-1">本次违章押金：<b class="chengse">500.00</b>元</p></div>
<div class="dingdan-xinxi" style="border:0;"><p class="dingdan-xinxi-1" style="font-weight:bold;">您应该支付的订车押金：<b class="chengse">200.00</b>元</p>
		<a href="#" class="anniu-5" style="margin:20px 0 0 10px;">提交订单并支付</a></div></div>
</div>
<?php include 'footer.html';?>
</body>
</html>
