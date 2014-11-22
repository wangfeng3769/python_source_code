//获取ID
function byId(id)
{
	return document.getElementById(id);
}

//获取class
function getByClass(oParent,sClassName){
	var aElm=oParent.getElementsByTagName('*');
	var aArr=[];
	var re=new RegExp('\\b'+sClassName+'\\b','i');
	for(var i=0;i<aElm.length;i++){
		if(re.test(aElm[i].className)){
			aArr.push(aElm[i]);
		}
	}
	return aArr;
}

//获取滚动条高度
function scrollY(){
	return document.documentElement.scrollTop||document.body.scrollTop;
}

//横向滚动条
function scrollX(){
	return document.documentElement.scrollLeft || document.body.scrollLeft;
}

//获取可视区宽度
function viewWidth(){
	return document.documentElement.clientWidth;
}

//获取文本高度
function documentHeight(){
	return Math.max(document.body.offsetHeight,document.documentElement.clientHeight);
}

//获取文本宽度
function documentWidth(){
	return Math.max(document.body.offsetWidth,document.documentElement.clientWidth);
}

//获取可视区高度
function viewHeight(){
	return document.documentElement.clientHeight;
}

//获取下一个子节点
function next(obj){
	return obj.nextElementSibling || obj.nextSibling;
}

//绑定事件
function bindEvent(obj,events,fn){
		if(obj.addEventListener){
			obj.addEventListener(events,fn,false);
		}
		else{
			obj.attachEvent('on'+events,function(){
			
				fn.call(obj);
			
			});
		}		
}



function posTop(obj){
	var result = 0;
	while(obj){
		result += obj.offsetTop;
		obj = obj.offsetParent;
	}
	return result;
}

//删除cookie
function delCookie(key){
	setCookie(key,1,-1);	
}

//碰撞检测
function cdTest(obj1,obj2){
	var L1=obj1.offsetLeft;
	var R1=obj1.offsetLeft+obj1.offsetWidth;
	var T1=obj1.offsetTop;
	var B1=obj1.offsetTop+obj1.offsetHeight;
	
	var L2=obj2.offsetLeft;
	var R2=obj2.offsetLeft+obj2.offsetWidth;
	var T2=obj2.offsetTop;
	var B2=obj2.offsetTop+obj2.offsetHeight;
	
	if(R1<L2 || L1>R2 || B1<T2 || T1>B2){
		return false;
	}
	else{
		return true;
	}
}

//获得距离
function getDis(obj1,obj2){
	var a=obj1.offsetLeft-obj2.offsetLeft;
	var b=obj2.offsetTop-obj2.offsetTop;
	return Math.sqrt(Math.pow(a,2)+Math.pow(b,2));
}

//搜索提示文字消失显示方法
function Search(objInput,str){
	objInput.onfocus=function(){
		if(this.value==str){
			this.value='';
		}
	};
	
	objInput.onblur=function(){
		if(this.value==''){
			this.value=str;
		}
	};
}
	
//完美运动框架
function startMove(obj,json,endFn){
	
	clearInterval(obj.timer);
	
	obj.timer = setInterval(function(){
		
		var bBtn = true;
		
		for(var attr in json){
			
			var iCur = 0;
		
			if(attr == 'opacity'){
				if(Math.round(parseFloat(getStyle(obj,attr))*100)==0){
				iCur = Math.round(parseFloat(getStyle(obj,attr))*100);
				
				}
				else{
					iCur = Math.round(parseFloat(getStyle(obj,attr))*100) || 100;
				}	
			}
			else{
				iCur = parseInt(getStyle(obj,attr)) || 0;
			}
			
			var iSpeed = (json[attr] - iCur)/8;
		iSpeed = iSpeed >0 ? Math.ceil(iSpeed) : Math.floor(iSpeed);
			if(iCur!=json[attr]){
				bBtn = false;
			}
			
			if(attr == 'opacity'){
				obj.style.filter = 'alpha(opacity=' +(iCur + iSpeed)+ ')';
				obj.style.opacity = (iCur + iSpeed)/100;
				
			}
			else{
				obj.style[attr] = iCur + iSpeed + 'px';
			}
		}
		
		if(bBtn){
			clearInterval(obj.timer);
			
			if(endFn){
				endFn.call(obj);
			}
		}
		
	},30);

}


function getStyle(obj,attr){
	if(obj.currentStyle){
		return obj.currentStyle[attr];
	}
	else{
		return getComputedStyle(obj,false)[attr];
	}
}

//完美拖拽
function Drag(obj,bBtn){
	var disX=0;
	var disY=0;
	
	obj.onmousedown=function(ev){
		var ev=ev||window.event;
		if(bBtn){
			disX=ev.clientX-obj.parentNode.offsetLeft;
			disY=ev.clientY-obj.parentNode.offsetTop;
		}
		else{
			disX=ev.clientX-obj.offsetLeft;
			disY=ev.clientY-obj.offsetTop;
		}
		if(obj.setCapture){
			obj.setCapture();
		}
		document.onmousemove=function(ev){
			var ev=ev||window.event;
			var L=ev.clientX-disX;
			var T=ev.clientY-disY;
			if(L<0){
				L=0;
			}
			else if(L>viewWidth()-obj.offsetWidth){
				L=viewWidth()-obj.offsetWidth;
			}
			if(T<0){
				T=0;
			}
			else if(T>viewHeight()-obj.offsetHeight){
				T=viewHeight()-obj.offsetHeight;
			}
			if(bBtn){
				obj.parentNode.style.left=L+'px';
				obj.parentNode.style.top=T+'px';
			}
			else{
			obj.style.left=L+'px';
			obj.style.top=T+'px';
			}
		};
		document.onmouseup=function(){
			document.onmousemove=null;
			document.onmouseup=null;
			if(obj.releaseCapture){
				obj.releaseCapture();
			}
			setCookie('x',obj.offsetLeft,5);//建立cookie
			setCookie('y',obj.offsetTop,5);
		};
		return false;
	};
}

//tab选项卡函数
function Tab(atitle,abox){
	for(var i=0;i<atitle.length;i++){
		
		atitle[i].index=i;
		atitle[i].onclick=function(){
			for(var i=0;i<atitle.length;i++){
				atitle[i].className='';
				abox[i].style.display='none';
			}
			this.className='active';
			abox[this.index].style.display='block';
		};
		
	}
}

//自定义滚动条方法
function ScrollBar(bar,bar_outer,bar_box,bar_box_outer){
	var disY = 0;
	var bBtn = true;
	
	if( bar_box.offsetHeight<=bar_box_outer.offsetHeight){
		bar.style.display='none';
	}else{
        bar.style.display='block';
    }

	bar.onmousedown = function(ev){

		var ev = ev || window.event;
		disY = ev.clientY - bar.offsetTop;
		
		document.onmousemove = function(ev){
			var ev = ev || window.event;
			var T = ev.clientY - disY;
			
			if(T<0){
				T = 0;
			}
			else if(T>bar_outer.offsetHeight - bar.offsetHeight){
				T = bar_outer.offsetHeight - bar.offsetHeight;
			}
			
			bar.style.top = T + 'px';
			
			var soleY = T/(bar_outer.offsetHeight - bar.offsetHeight);
						
			bar_box.style.top = - soleY * (bar_box.offsetHeight - bar_box_outer.offsetHeight) + 'px';
			
		};
		
		document.onmouseup = function(){
			document.onmousemove = null;
			document.onmouseup = null;
		};
		
		return false;
	};
	
	if(bar_box.addEventListener){
		bar_box.addEventListener('DOMMouseScroll',toChange,false);
		bar_outer.addEventListener('DOMMouseScroll',toChange,false);
	}
	bar_box.onmousewheel = toChange;
	bar_outer.onmousewheel = toChange;
    if( bar_box.offsetHeight<=bar_box_outer.offsetHeight){
        bar_box.onmousewheel = null;
        bar_outer.onmousewheel = null;
    }
	
	//滚轮事件
	function toChange(ev){
		var ev = ev || window.event;
		var T = 0;
		
		if(ev.detail){
			bBtn = ev.detail>0 ? true : false;
		}
		else{
			bBtn = ev.wheelDelta<0 ? true : false;
		}
		
		if(bBtn){ //下
			T = bar.offsetTop + 30;
		}
		else{	//上
			T = bar.offsetTop - 30;
		}
		
		if(T<0){
			T = 0;
		}
		else if(T>bar_outer.offsetHeight - bar.offsetHeight){
			T = bar_outer.offsetHeight - bar.offsetHeight;
		}
		
		bar.style.top = T + 'px';
		
		var soleY = T/(bar_outer.offsetHeight - bar.offsetHeight);
		
		bar_box.style.top = - soleY * (bar_box.offsetHeight - bar_box_outer.offsetHeight) + 'px';
		
		if(ev.preventDefault){
			ev.preventDefault();
		}
		else{
			return false;
		}
		
	}
	
}


//设置提示文本
var g_aDefaultText=[];
var g_aDefaultTextColor=[];
var g_aNormalTextColor=[];
var g_iDefaultTextMaxIndex=0;

function setDefaultText(oInputElement, sDefaultText, sDefaultTextColor, sNormalTextColor)
{
	//将信息保存起来，以后使用
	g_aDefaultText.push(sDefaultText);
	g_aDefaultTextColor.push(sDefaultTextColor);
	g_aNormalTextColor.push(sNormalTextColor);
	
	//初始化为提示文本
	oInputElement.value=sDefaultText;
	oInputElement.style.color=sDefaultTextColor;
	
	//设置“是否正在显示提示文本”信息
	oInputElement.isDefault=true;
	oInputElement.index=g_iDefaultTextMaxIndex++;
	
	//添加事件
	oInputElement.onfocus=__defaultTextOnFocusHandler__;
	oInputElement.onblur=__defaultTextOnBlurHandler__;
}

function defaultTextSetText(oInputElement, sText)
{
	oInputElement.isDefault=false;
	oInputElement.value=sText;
	oInputElement.style.color=g_aNormalTextColor[oInputElement.index];
}

function __defaultTextOnFocusHandler__(ev)
{
	if(this.isDefault)
	{
		this.value='';
		this.style.color=g_aNormalTextColor[this.index];
	}
}

function __defaultTextOnBlurHandler__(ev)
{
	if(this.value.length)
	{
		this.isDefault=false;
	}
	else
	{
		this.isDefault=true;
		this.value=g_aDefaultText[this.index];
		this.style.color=g_aDefaultTextColor[this.index];
	}
}

//城市下拉列表
var g_aUl=[];
var g_aFnOnItemSelect=[];
var g_iDropDownMaxIndex=0;

function setDropDown(oUl, fnOnItemSelect){
	var aLis=oUl.getElementsByTagName('li');
	var i;
	
	g_aUl.push(oUl);
	g_aFnOnItemSelect.push(fnOnItemSelect);
	
	oUl.style.display='none';
	
	for(i=0;i<aLis.length;i++){
		aLis[i].className='';
		aLis[i].index=g_iDropDownMaxIndex;
		
		aLis[i].onmouseover=__dropDownOnMouseOverHandler__;
		aLis[i].onmouseout=__dropDownOnMouseOutHandler__;
		aLis[i].onmousedown=__dropDownOnMouseDownHandler__;
	}
	
	g_iDropDownMaxIndex++;
	
}

function __dropDownOnMouseOverHandler__(ev)
{
	this.className='active';
}

function __dropDownOnMouseOutHandler__(ev)
{
	this.className='';
}

function defaultTextSetText(oInputElement, sText){
	oInputElement.isDefault=false;
	oInputElement.value=sText;
}

function __dropDownOnMouseDownHandler__(ev){
	g_aFnOnItemSelect[this.index](this.innerHTML);	
	g_aUl[this.index].style.display='none';
}	



//首页轮播图显示
function show(obj){
	for(var i=0;i<obj.length;i++){
	
		obj[i].onclick = function(){
			for(var i=0;i<obj.length;i++){
				obj[i].index = i;
			}
			if(this.index<index){
				toRunLeft(index - this.index);
			}
			else if(this.index>index){
				toRunRight(this.index - index);
			}
		};
	}
}

//ajax函数
function ajax(url, fnOnSucc, fnOnFaild){
	var oAjax=null;
	//1.初始化Ajax对象
	if(window.ActiveXObject){
		oAjax=new ActiveXObject("Msxml2.XMLHTTP")||new ActiveXObject("Microsoft.XMLHTTP");
	}
	else{
		oAjax=new XMLHttpRequest();
	}
	
	//2.建立连接
	oAjax.open('get', url, true);
	
	//3.监控请求状态
	oAjax.onreadystatechange=function (){
		//readyState->Ajax对象内部的状态
		//status->服务器返回的请求结果
		if(oAjax.readyState==4){
			if(oAjax.status==200){
				if(fnOnSucc){
					fnOnSucc(oAjax.responseText);
				}
			}
			else{
				if(fnOnFaild){
					fnOnFaild(oAjax.status);
				}
			}
		}
	};
	
	//4.发送请求
	oAjax.send();
	//5.*清理
	//oAjax.onreadystatechange=null;
	//oAjax=null;
}

function ajaxPost(url, sData, fnOnSucc, fnOnFaild){
	var oAjax=null;
	//1.初始化Ajax对象
	if(window.ActiveXObject){
		oAjax=new ActiveXObject("Msxml2.XMLHTTP")||new ActiveXObject("Microsoft.XMLHTTP");
	}
	else{
		oAjax=new XMLHttpRequest();
	}
	
	//2.建立连接
	oAjax.open('post', url, true);
	
	//3.监控请求状态
	oAjax.onreadystatechange=function (){
		//readyState->Ajax对象内部的状态
		//status->服务器返回的请求结果
		if(oAjax.readyState==4){
			if(oAjax.status==200){
				if(fnOnSucc){
					fnOnSucc(oAjax.responseText);
				}
			}
			else{
				if(fnOnFaild){
					fnOnFaild(oAjax.status);
				}
			}
		}
		
	};
	
	//4.发送请求
	oAjax.setRequestHeader('content-type', 'urlencode');
	oAjax.send(sData);
	
}

//获取valu值
function getValueAjax(obj){
	return obj.value;
}


//更多热门影片弹窗
function MoreMovieWindow(obj){
	
	var oHot_movie_Box=document.getElementById('hot_movie_Box');
	var oClose = document.getElementById('close');
	var oMark_bg=document.getElementById('mark_bg');
	
	obj.onclick=function(){
		oHot_movie_Box.style.visibility='visible';
		oMark_bg.style.display='block';
		
		//可视区的宽
		var iViewWidth = document.documentElement.clientWidth;
		//可视区的高
		var iViewHeight = document.documentElement.clientHeight;
		//横向滚动条
		var iScrollLeft = document.documentElement.scrollLeft || document.body.scrollLeft;
		//纵向滚动条
		var iScrollTop = document.documentElement.scrollTop || document.body.scrollTop;
		
		oHot_movie_Box.style.left =iScrollLeft + (iViewWidth - oHot_movie_Box.offsetWidth)/2 + 'px';
		oHot_movie_Box.style.top = (iViewHeight - oHot_movie_Box.offsetHeight)/2 + iScrollTop + 'px';	
		
		oMark_bg.style.width = document.documentElement.clientWidth + 'px';
		oMark_bg.style.height = documentHeight() + 'px';
		
	};
	
	oClose.onclick=function(){
		oHot_movie_Box.style.visibility='hidden';
		oMark_bg.style.display='none';
	};
	
	window.onscroll = window.onresize = function() {
		//可视区的宽
		var iViewWidth = document.documentElement.clientWidth;
		//可视区的高
		var iViewHeight = document.documentElement.clientHeight;
		//横向滚动条
		var iScrollLeft = document.documentElement.scrollLeft || document.body.scrollLeft;
		//纵向滚动条
		var iScrollTop = document.documentElement.scrollTop || document.body.scrollTop;
		
		oHot_movie_Box.style.left =iScrollLeft + (iViewWidth - oHot_movie_Box.offsetWidth)/2 + 'px';
		oHot_movie_Box.style.top = (iViewHeight - oHot_movie_Box.offsetHeight)/2 + iScrollTop + 'px';	
		
		oMark_bg.style.width = document.documentElement.clientWidth + 'px';
		oMark_bg.style.height = documentHeight() + 'px';
	}
}

