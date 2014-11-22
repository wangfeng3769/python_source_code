var G = {
	baseUrl:'http://218.240.151.51/',
	// baseUrl:'http://192.168.0.9/',
	memberApi:'client/http/member.php',
	publicApi:'client/http/public.php',
	pageStack:[],
	nowPageClassName:'init',
	user:{}
};

(function Until(k)
{
	jq = k.$;

	func = {
		me : (function(me){return me})(this),
		init:function()
		{
 			//又登录过的执行登录操作
 			G.isIphone=true;
 			
 			var user = localStorage.getItem('user');
 			if (user!=null||user!=undefined)
			{
				G.user = JSON.parse(localStorage.getItem('user'));
				G.token=G.user.access_token;
			}
 			
 

 			//初始化,判断用户有无可用订单,如果有,则跳转到使用订单的页面
 			if (G.token!=null||G.token!=undefined)
			{
				
				func.getAvaliaOrder()
			}
			else
			{
				func.getCars();
			}
		},
		getCookie: function(a) {
			var b = document.cookie.indexOf(a),
				e = document.cookie.indexOf(";", b);
			return b == -1 ? "" : unescape(document.cookie.substring(b + a.length + 1, e > b ? e : document.cookie.length))
		},
		setCookie: function(a, b, e, c, g, h) {
			var d = new Date;
			d.setTime(d.getTime() + e);
			document.cookie = [escape(a), "=", escape(b), d ? "; expires=" + d.toGMTString() : "", c ? "; path=" + c : "/", g ? "; domain=" + g : "", h ? "; secure" : ""].join("")
		},
		bookCarView : function(url)
		{
			var checkApi="client/http/member.php?item=emptyCall";
			func.postJson(checkApi,{},function(ret){
				if (ret.errno==2000)
				{
					window.location.href=G.baseUrl+url;
				}
			});
		},
		getAvaliaOrder:function ()
		{
			func.postJson(G.memberApi,{item:'twGetUserAvailableCar'},function(ret){
			  		if (ret.errno==4005||ret.errno==4060||ret.errno==4020) {
			  			func.getCars();
			  			// window.location.href='index.html';
			  		}
			  		else
			  		{
			  			
			  			func.routerInUsePage();
			  		}
			  		return;
		  	});
		},
		routerInUsePage: function()
		{
			var uri='in-use.html';
  			var hrefArr=location.href.split('/');
  			var nowUri=hrefArr[hrefArr.length-1];
  			// alert(nowUri);
  			// if () {};
  			if (nowUri!=uri)
			{
				window.location.href='in-use.html';	
			};
		},
		login : function(userName,password,callback)
		{

			if(!G.isLogin||1)
			{
				
				this.postJson(G.publicApi,{item:'login',user_name:userName,password:password},function(obj){
					if (obj.errno==2000)
					{
						// G.isLogin=true;
						G.user={isLogin:true,access_token:obj.content.accessToken,userName:obj.content.uname};
                              // alert(JSON.stringify(G.user));
						localStorage.setItem('user',JSON.stringify(G.user));
						func.saveToken(obj.content.accessToken);
					}
					else
					{
						
					}
					callback(obj);
					
				});
			};
			
		},
		saveToken : function(token)
		{
			G.token=token;
		},
		singleAjax : (function()
		{
			var me1 = this;
			me1.isLoading=false;
			me1.run=function(url,jsonData,userCallback,retStr)
			{
				if (me1.isLoading==false)
				{
					me1.isLoading=true;
					func.postJson(url,jsonData,function(data){
						me1.isLoading=false;
						userCallback(data);
					},retStr);
				};
			};
			return this;
		})(),

		postJson : function(url,jsonData,callback,retStr)
		{

			// var unLoginCode=4060;
			// alert('postJson')
//            alert(JSON.stringify(G));
//            url=url+'?XDEBUG_SESSION=sublime.xdebug';
//            alert('G.token'+G.token);
//            alert('G.user.access_token:'+G.user.access_token);

			jsonData.access_token=G.token;
			$.ajax({
			   type: "POST",
			   url: G.baseUrl+url,
			   data: jsonData,
			   timeout:5000,
			   beforeSend:function(){func.loading.show();},
			   error:function(){func.loading.hide();func.showMsg('网络错误,无法访问网络'),func.singleAjax.isLoading=false;},
			   success: function(data){
				    if (retStr==true)
					{
						callback(data)
					}
					else
					{
						var jdata=eval('('+data+')');
						callback(jdata);
					}
					func.loading.hide();
			   	}
			});

			// $.post(G.baseUrl+url,jsonData,function(data){
   //                 // alert(JSON.stringify(jsonData));
			// 	if (retStr==true)
			// 	{
			// 		callback(data)
			// 	}
			// 	else
			// 	{
			// 		var jdata=eval('('+data+')');
			// 		callback(jdata);
			// 	}
			// 	func.loading.hide();
			// });
		},
		autoLogin:function()
		{
			// locatIp('objc://login/');
		},
		getCars : function()
		{
			var cars = [];
			var sourceDiv = $("#car-img-list-tpl")
			func.postJson(G.publicApi,{item:'twGetCarsInfo'},function(obj){
				if (obj.errno==2000)
				{
					// return obj.content; jumpNewPage : function(jsonData,oldPage,newPage)
					func.jumpNewPage({cars:obj.content},$(sourceDiv));
					// func.renderHTML($('.car-img-list-tmp'),{cars:obj.content},$('.car-img-list'));
				};
			});
		},
		//tpl:模板jq对象,data:jsonObject,target:目的标签的jq对象
		renderHTML : function(tpl,data)
		{
			juicer.register("get_img_url", function(url) {
					return G.baseUrl+'cp/'+url;
				});
			juicer.register('get_bookcar_url',function(carId)
			{
				return 'javascript:getCarInfo('+carId+');';
			});
			juicer.register('get_now_date',function(step){
				var now=new Date();
				var year = now.getFullYear();
				var month= doubleChar(now.getMonth()+1);
				var day = doubleChar(now.getDate()+step);
				return month+'-'+day+'-'+year;
			});
			juicer.register('get_select_minute',function(step){
				return doubleChar(step*10) ;
			});
			juicer.register('get_now_Ymd',function(step){
				var now=new Date();
				var year = now.getFullYear();
				var month= doubleChar(now.getMonth()+1);
				var day = doubleChar(now.getDate()+step);
				return year+'-'+month+'-'+day;
			});
			juicer.register('get_select_hour',function(step){
				return doubleChar(step);
			})
			var doubleChar = function (str){
				if (str.toString().length==1)
				{
					return '0'+str.toString();
				}
				else
				{
					return str;
				}
			}
			return juicer(tpl.html(), data);
			// target.html(juicer(tpl.html(), data));			
		},
		getRequest :function(name)
		{
			var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
			var r = window.location.search.substr(1).match(reg);
			if (r != null) return unescape(r[2]);
			return null;
		},
		getAllRequest : function()
		{
			var tmpMatchResult=[];
			var result={};
			var paramStr=window.location.search.substr(1);
			paramArr=paramStr.split('&');
			for (var i = 0; i < paramArr.length; i++) {
			 	matchResult=paramArr[i].split('&')[0].match("([a-zA-Z_-\s0-9]+)=([a-zA-Z_-\s0-9\/\.:]+)");
			 	if (matchResult[1]!=null &&matchResult[2]!=null)
		 		{
		 			eval("result."+matchResult[1]+"='"+matchResult[2]+"'");

		 		};
			 }; 

			return result;
			// var 
		},
		timeLineToos: (function TimeLineClass()
		{
			this.defaultData={
				"style":"shijianzhou-2"

			}

			//某天订车时间显示
			this.addTimeLine=function(targetObj,arrTime)
			{
				if (typeof(arrTime)!="object" || arrTime.length==0)
				{
					return false;
				};
				var barWidth=targetObj.width();
				if (barWidth==0)
				{
					barWidth=targetObj.css('width').replace('px','');
				};
				
				var timeWidth;

				for (var i = 0; i < arrTime.length; i++) {
					timeWidth = getTimeWidth(arrTime[i].start_time,arrTime[i].end_time) * barWidth;
					timePosition = getPosition(arrTime[i].start_time)* barWidth;

					var pTag = $("<p></p>");
					pTag.addClass(this.defaultData.style);
					pTag.css("width",timeWidth+"px");
					pTag.css("margin-left",timePosition+"px");

					targetObj.append(pTag);
				};
			}

			//3天的订车时间显示
			this.addTimeLineByTags=function(arrTime)
			{	
				//先删除所有时间轴的显示
				$('.shijianzhou-2').remove();
				
				if (typeof(arrTime)!="object" || arrTime.length==0)
				{
					return false;
				}


				for (var i = 0; i < arrTime.length; i++) {
					//硬编码此element id
					this.addTimeLine("time-line-"+i,arrTime[i]);
				};

			}

			var getPosition=function(strStart)
			{
				minit=formatTime(strStart);
				return minit/(60*24);
				
			}

			var getTimeWidth=function(strStart,strEnd)
			{
				minitStart=formatTime(strStart);
				minitEnd=formatTime(strEnd);

				return (minitEnd-minitStart)/(24*60);
			}

			var formatTime=function(str)
			{
				var arrTime=str.split(":");
				var minit=arrTime[0]*60+arrTime[1]*1;
				return minit;
			}
			return this;
		})(),

		jumpNewPage : function(jsonData,newPage)
		{
			var oldPageName=G.nowPageClassName;
			
			oldPage=$('.'+G.nowPageClassName);
			newPage=$(newPage);
			var container=oldPage.parent();


			oldPage.hide();
			G.pageStack.push(oldPage.attr('page'));
			
			//生成新页面renderHTML : function(tpl,data,target)
			var newPageObj = $(func.renderHTML(newPage,jsonData,container));
			//删除与新页面同类名的页面,防止页面重复
			var newPageName=newPageObj.attr('page');
			$('.'+newPageName).remove();

			G.nowPageClassName=newPageName;
			//显示新页面

			container.append(newPageObj);

			window.scrollTo(0,0);

		},

		animate : function(startPageObj,endPageObj)
		{

		},

		goback : function(e)
		{
			if (G.pageStack.length==1)
			{
				// func.showMsg('不能再返回了');
				console.log('不能再返回了');
				// return ;

			}else
			{
				var nowPage=$('.'+G.nowPageClassName);
				var prePage=$('.'+G.pageStack.pop());
				G.nowPageClassName = prePage.attr('page');
				nowPage.remove();
				prePage.show();
			}
			e.stopImmediatePropagation();
		},
		showMsg : function(str)
		{
			func.showNotice({msg:str});
		},
		showNotice: function(msgObj) {
			msgObj = $.extend({}, {
				msg: "",
				close: 2,
				url: ""
			}, msgObj);
			$(".win-notice").remove();
			var msgBox = $('<div class="win-notice"><em>' + msgObj.msg + "</em></div>").appendTo($(".dynamite-container"));
			G.isIE && msgBox.css("top", $(window).scrollTop() + $(window).height() + "px");
			var e = setTimeout(function() {
				clearTimeout(e);
				msgBox.animate({
					opacity: 0,
					duration:300,
					complete:(function() {
						msgBox.remove();
					})()
				});
				if(msgObj.url) window.location.href = msgObj.url
			}, msgObj.close * 1E3)
		},

		loading: (function() {
			
			var msgBox;
			this.show=function()
			{
				$(".win-notice").remove();
				msgBox = $('<div class="win-notice"><em>加载中...</em></div>').appendTo($(".dynamite-container"));
				G.isIE && msgBox.css("top", $(window).scrollTop() + $(window).height() + "px");
			};

			this.hide=function() {
				var win=$(".win-notice");
				if (win==null||win.length==0)
				{
					return;
				};
				msgBox.animate({
					opacity: 0,
					duration:300,
					complete:(function() {
						msgBox.remove();
					})()
				});
			};
			return this;
		})(),


	};

	k.Until=func;

})(window);

function locatIp(str)
{
	window.location =str;
}

function DateFormat(time){
		// alert(time);
		var d = new Date(parseInt(time*1E3));

		var res =d.getFullYear()+"-"+(d.getMonth()+1)+"-"+d.getDate()+" "+d.getHours()+":"+d.getMinutes();
		return res;
}