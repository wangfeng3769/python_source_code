function TimeLineClass()
{
	this.defaultData={
		"style":"shijianzhou-2"

	}

	//某天订车时间显示
	this.addTimeLine=function(targetIdStr,arrTime)
	{
		if (typeof(arrTime)!="object" || arrTime.length==0)
		{
			return false;
		};
		var barWidth=$("#"+targetIdStr).width();
		if (barWidth==0)
		{
			barWidth=$("#"+targetIdStr).css('width').replace('px','');
		};
		
		var timeWidth;

		for (var i = 0; i < arrTime.length; i++) {
			timeWidth = getTimeWidth(arrTime[i].start_time,arrTime[i].end_time) * barWidth;
			timePosition = getPosition(arrTime[i].start_time)* barWidth;

			var pTag = $("<p></p>");
			pTag.addClass(this.defaultData.style);
			pTag.css("width",timeWidth+"px");
			pTag.css("margin-left",timePosition+"px");

			$("#"+targetIdStr).append(pTag);
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
}

/**
 * 序列化所有input标签
 * @return {[type]} [description]
 */
function serializeDateInput()
{
	var obj=$("input");
	var param=null;
	param='';
	for (var i = 0; i < obj.length; i++) {
		if (obj[i].value != undefined || obj[i].value!=null || obj[i].value!='' )
		{
			param += obj[i].name+"="+obj[i].value+"&";
		};
		
	}
	return param;

}

function caculateSubmit(strDivID)
{
	

}

function followCar(carId,callMethod)
{
	$.ajax({
	 		type: "POST",
		 	url: "../client/index.php?class=CarManager&item=follow&car_id="+carId,
		 	success: function(data)
		 	{
		 		if (callMethod!=null) {
		 			callMethod;
		 		}
		 		
		 	}
		 });
}

function showMsg(errorStr)
{	
	//初始化msg-box容器
	$('#msg-box').html('');
	var msgBox=$('#msg-box');
	var html=$('#msgTmp').html();

	html=html.replace(/\{\#msg\#\}/g,errorStr);
	//处理阴影层
	var h = $(document).height();
	var w = $(document).width();
	//添加阴影层#screen{width:100%;height:100%;position:absolute;top:0;left:0;display:none;z-index:100;background-color:#666;opacity:0.5;filter:alpha(opacity=50);-moz-opacity:0.5;}
	$(document.body).append('<div class="gray-screen" style="height: '+h+'px; display: block;background-color:#333;"></div>');


	html=html.replace(/\{\#height\#\}/g,h);
	html=html.replace(/\{\#width\#\}/g,w);	
	msgBox.append(html);
	msgBox.center();
	msgBox.fadeIn();
	closeWindowClick();

}
jQuery.fn.center = function(loaded) {
	var obj = this;
	body_width = parseInt($(window).width());
	body_height = parseInt($(window).height());
	block_width = parseInt(obj.width());
	block_height = parseInt(obj.height());
	
	left_position = parseInt((body_width/2) - (block_width/2)  + $(window).scrollLeft());
	if (body_width<block_width) { left_position = 0 + $(window).scrollLeft(); };
	
	top_position = parseInt((body_height/2) - (block_height/2) + $(window).scrollTop());
	if (body_height<block_height) { top_position = 0 + $(window).scrollTop(); };
	
	if(!loaded) {
		
		obj.css({'position': 'absolute'});
		obj.css({ 'top': top_position, 'left': left_position });
		$(window).bind('resize', function() { 
			obj.center(!loaded);
		});
		$(window).bind('scroll', function() { 
			obj.center(!loaded);
		});
		
	} else {
		obj.stop();
		obj.css({'position': 'absolute'});
		obj.animate({ 'top': top_position }, 200, 'linear');
	}
}

function decodeJson(str)
{
	return eval('('+str+')');
}
function closeWindowClick()
{
	$('.close-msg-window').live('click',function(e){
		// alert('sss');
		$('#msg-box').hide();
		$('.gray-screen').remove();
	});
}
function reflashClickButton()
{
	$('.reflash-button').live('click',function(){
		window.location.href='';
	});
}