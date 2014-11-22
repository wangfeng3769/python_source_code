document.domain = "qq.com";
var wiki={
"loadcss":function(v,c){
			var element = document.createElement("link");
			element.href = v;
			element.rel= "stylesheet";
			element.type = "text/css";
			if(c)
			element.charset=c;
			(document.head||document.getElementsByTagName("head")[0]).appendChild(element);
	},
"loadjs":function(v,c){
		var element = document.createElement("script");
		element.src = v;
		element.type = "text/javascript";
		if(c)
		element.charset=c;
		document.body.appendChild(element); 
	},
"login":function(u){
	var o=document.getElementById("login_status");
	wiki.u = u;
	if(u==undefined || u.hdlogin==undefined || u.hdlogin == '' || u.hdlogin == '0'){
			o.innerHTML = "<a title=\"点击此处登录\" class=\"txt\" href=\"javascript:void(0);\" id=\"loginBtn\">登录</a>";
	}else{
			o.innerHTML="<a  class=\"txt\" href=\"javascript:;\" title=\""+u.weibonick+"\">"+u.weibonick+"<em></em></a>"
						+" | "
						+"<a href=\"javascript:;\" id=\"logoutBtn\">退出</a>";
			window.hdlogin = u.hdlogin||'';
			window.regweibo = u.regweibo||'0';
	}	
},
"onscroll":function(){
	if (wiki.contentHeight<(wiki.docElement.scrollTop||window.pageYOffset)){
		$(".bottomnav").css({
		"position":"absolute",
		"bottom":"auto",
		"top":$(".mainwrapper").height()+/*35-30*/5
		})
	}else{
		if (!-[1,]&&!window.XMLHttpRequest){ 
		//ie6
			$(".bottomnav").css({
			"bottom":"auto",
			"top":(document.documentElement.scrollTop+document.documentElement.clientHeight-30)	
			});
		
		}else{
		//非ie6	
		$(".bottomnav").css({
		"position":"fixed",
		"bottom":"0",
		"top":"auto"	
		})
		}
	}
},
"bindNavEvent":function(pathname){
	var path = pathname.split("/").slice(2),nav=[],curNav,getId = function(s){return "n-"+encodeURIComponent(s).replace(/%/g,".");};
	$("#mw-panel").find(".portal").css("margin-top","-1px").find(".body").hide();
	$("#mw-panel").find("h5").click(function(){
		var t = $(this),b = t.next(".body"),p = t.parent();
		if (b.is(":visible")){
			b.slideUp("fast");
			p.removeClass("active");
		}else{
			b.find("li").find("em").addClass("none");
			b.slideDown("fast",function(){
				b.find("li").find("em").removeClass("none");
			});
			p.addClass("active");
		}
		$(this).parent().siblings(".portal").removeClass("active").find(".body").slideUp("fast");
	});
	if (path[0]){
		path[0] = decodeURIComponent(path[0]);
		nav[0] = getId(path[0]);
		nav[1] = getId(path[0]+"[热]");
		nav[2] = getId(path[0]+"[新]");
		nav[3] = getId(path[0]+"[荐]");
		
		$.each(nav,function(i,v){
			if ($("li[id="+v+"]").size()){
				curNav = $("li[id="+v+"]");
			}
		});
		
		if (curNav){
			curNav.addClass("active");
			curNav.parent().parent().show().prev("h5");
		}
		
		if ("api文档" === path[0].toLowerCase() && path.length>1){
			$('<a href="http://support.qq.com/discuss/857_1.shtml" target="_blank" style="position:absolute;top:100px;left:50%;margin-left:315px;width:130px;height:28px;line-height:28px;color:#2573A2;display:block;background:url(http://mat1.gtimg.com/app/opent/images/wiki/resource/btn_bg.gif) no-repeat;text-align:center;text-decoration:none;font-size:12px;">API接口问题反馈</a>').appendTo($("#content"));
		}
	}
},
"initDoc":function(){
	$("#bodyContent").find("a").each(function(){
		var url = $(this).attr("href");
		if (/^(http|https):\/\//.test(url)){
			$(this).attr("target","_blank");
			if ($(this).attr("href").search(/download/)>0){
				$(this).bind("click",function(event){
					var t=$(this),link=$(this).attr("href"),fileName = link.slice(link.lastIndexOf("/")+1);
					var img = new Image(1,1);
						img.src=['http://btrace.qq.com/collect',
						'?sIp=',wiki.u.ip||0,
						'&iQQ=',wiki.u.hdlogin,
						'&sBiz=','weibo.open.wiki',
						'&sOp=','/download/',
						'&iSta=',0,
						'&iTy=',1300,
						'&iFlow=',+new Date(),
						'&iFrom=',encodeURIComponent(document.reffer||''),
						'&iPubFrom=',encodeURIComponent(link),
						'&sUrl=',encodeURIComponent(location.href),
						'&iUrlType=','wiki',
						'&iPos=',0,
						'&sText=','wiki下载统计',
						'&iBak1=',0,
						'&iBak2=',0,
						'&sBak1=','',
						'&sBak2=',''
						].join('');
				});
			}
		}
		
	});
	$("#bodyContent").find(".paratable").find("a").each(function(){
		var url = $(this).attr("href");
		if (/^[^#]/.test(url)){
		$(this).attr("target","_blank");
		}
	});
	wiki.onscroll();
	$(".bottomnav").fadeIn("slow");
}
};

$(function(){
wiki.loadcss("http://mat1.gtimg.com/app/opent/css/login/login.css");
wiki.loadjs("http://mat1.gtimg.com/app/opent/js/login.js","utf-8");
wiki.loadjs("http://dev.open.t.qq.com/doc/?t="+(new Date().getTime()),"");
wiki.docElement=document.documentElement||document.body;
wiki.bindNavEvent(location.pathname);
setTimeout(function(){
	wiki.contentHeight=65/*35+30*/+$(".mainwrapper").height()-wiki.docElement.clientHeight;
	window.onscroll=wiki.onscroll;
	wiki.initDoc();
},1000);
});/*  |xGv00|9e196ebfa3cb9c305bf0d64e4ffa0a1f */