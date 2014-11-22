<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>易多汽车共享官网 - 中国首家自助快捷租车网，提供分时、自助的绿色出行方式</title>
<link href="css/yiduobangzhu.css" rel="stylesheet" type="text/css" />
<style>
a{color:#4F4F4F;text-decoration:none;}
.anniu-1:active{ background:url(img/anniu-1-2.png) no-repeat; color:#4F4F4F;}
.anniu-2:active{ color:#4F4F4F;}
.anniu-3:active{ color:#4F4F4F;}
.lan12:active{ color:#4F4F4F;}
.anniu-4:active{ background:url(img/anniu-2-2.png) no-repeat; color:#4F4F4F;}
.liebiao-xinxi-2:active{ background:#F4F4F4;color:#FB911B;}
.liebiao-xinxi-3:active{ background:#F4F4F4;color:#FB911B;}
.liebiao-xinxi-4:active{ background:#F4F4F4;color:#FB911B;}
</style>
<!--
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
-->



</head>
<body onload=applyContent()>
<?php include 'header.html';?>
<div class="dingche">
<div class="zhanghu-left" style="height:inherit;">

<div class="liebiao-1">
	<p class="liebiao-1-1">帮助手册</p>
	<ul class="liebiao-1-2">
		<a href="#" class="liebiao-xinxi-2" id="serDis" onclick="applyContent(event);return false;">·服务规则</a>
		<a href="#" class="liebiao-xinxi-2" id="help" onclick="extend(event);return false;">·新手上路</a>
		<div>
			<a href="#" class="liebiao-xinxi-3" id="Log" onclick="extend(event);return false;">注册登陆</a>
			<div>
				<a href="#" class="liebiao-xinxi-4" id="webLog" onclick="applyContent(event);return false;"style="display:none">网站</a>
		        <a href="#" class="liebiao-xinxi-4" id="appLog" onclick="applyContent(event);return false;"style="display:none">APP</a>
			</div>
            <a href="#" class="liebiao-xinxi-3" id="finCar" onclick="applyContent(event)">找车</a>
		    <a href="#" class="liebiao-xinxi-3" id="Sch" onclick="extend(event);return false;">订车</a>
		    <div>
		    	<a href="#" class="liebiao-xinxi-4" id="webSch" onclick="applyContent(event)" style="display:none">网站</a>
		    	<a href="#" class="liebiao-xinxi-4" id="appSch" onclick="applyContent(event)" style="display:none">APP</a>
            </div>
            <a href="#" class="liebiao-xinxi-3" id="pay" onclick="applyContent(event)">账户充值</a>
            <div>
            	<a href="#" class="liebiao-xinxi-4" id="" onclick="applyContent(event)" style="display:none">网站</a>
		        <a href="#" class="liebiao-xinxi-4" id="" onclick="applyContent(event)" style="display:none">APP</a>
            </div>
            <a href="#" class="liebiao-xinxi-3" id="Mod" onclick="extend(event);return false;">修改订单</a>
            <div>
            	<a href="#" class="liebiao-xinxi-4" id="webMod" onclick="applyContent(event)" style="display:none">网站</a>
		        <a href="#" class="liebiao-xinxi-4" id="appMod" onclick="applyContent(event)" style="display:none">APP</a>
            </div>
            <a href="#" class="liebiao-xinxi-3" id="use" onclick="applyContent(event)">取车还车</a>
		</div>
		<a href="#" class="liebiao-xinxi-2" id="ques">·常见问题</a>
	</ul>
</div>
</div>
<!--
<div class="zhanghu-right" style="height:2315px;">
	<img src="img/app-zhuce.jpg"/>
	<img src="img/web-zhuce.jpg" style="display:none;""/>
	<img src="img/zhaoche.jpg" style="display:none;""/>
	<img src="img/web-chongzhi.jpg" style="display:none;""/>
	<img src="img/web-xgdd.jpg" style="display:none;""/>
	<img src="img/web-dingche.jpg" style="display:none;""/>
	<img src="img/app-xgdd.jpg" style="display:none;""/>
	<img src="img/app-yongche.jpg" style="display:none;""/>
	<img src="img/app-dingche.jpg" style="display:none;""/>
</div>
-->
<div class="zhanghu-right" id="rPanel" style="height:2315px;">
	<img id="PanelofappLog" src="img/app-zhuce.jpg" />
	<img id="PanelofwebLog"src="img/web-zhuce.jpg" style="display:none;"/>
    
	<img id="PaneloffinCar" src="img/zhaoche.jpg" style="display:none;"/>

	<img id="PanelofwebSch" src="img/web-dingche.jpg" style="display:none;"/>
	<img id="PanelofappSch" src="img/app-dingche.jpg" style="display:none;"/>

	<img id="PanelofwebMod" src="img/web-xgdd.jpg" style="display:none;"/>
	<img id="PanelofappMod" src="img/app-xgdd.jpg" style="display:none;"/>

	<img id="Panelofuse" src="img/app-yongche.jpg" style="display:none;"/>

	<img id="Panelofpay" src="img/web-chongzhi.jpg" style="display:none;"/>
	
	<!--<iframe id="PanelofserDis" src="www.eduo/xieyi.php" style="display:none;width:980px;height:12140px;" frameBorder="0" scrolling="no">-->
		
	</iframe>
</div>
</div>
<script type="text/javascript">


function applyContent(e)
{
	var ev=e || event;
	var id;
	if(ev.target)
	if(ev.target.id)
	{
		id=ev.target.id;
		id="Panelof"+id;/*"Panelof** 是显示区域对应的左边栏名的前缀"*/
		var Panel=document.getElementById(id);
		var paPar;
		if(Panel!=null)
		{
			paPar=Panel.parentElement;
    	    for(var i=0;i<paPar.childElementCount;i++)
			paPar.children[i].style.display="none";

            Panel.style.display="";
	    }

	}
	
}


function extend(e)
{
	var ev=e || event;
	var id;
	if(ev.target)
	if(ev.target.id)
	{
	id=ev.target.id;
	var Panel=document.getElementById(id);
	var paPar;
	if(Panel!=null)
	{
		paPar=Panel.nextElementSibling;
        for(var i=0;i<paPar.childElementCount;i++)
        {
        	if(paPar.children[i].style.display=="")
		       paPar.children[i].style.display="none";
		    else
		     paPar.children[i].style.display="";
		    }

        Panel.style.display="";
	}
}
}


/*
function onmousedown(e)  
{  
	var eev=e || event;  
	var id=ev.target.id;//获取鼠标按下对应的对象的id   
	var result=$("#"+id).hasClass("serDis");//判断是否有 class是否是="idDrag"  
	if(result)
		alert"Hello";
}  
*/
</script>
<?php include 'footer.html';?>
</body>
</html>
