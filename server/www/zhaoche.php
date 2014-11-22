<?php 
require_once (dirname(__FILE__) . '/../hfrm/Frm.php');
require_once (Frm::$ROOT_PATH . 'client/http/web.php');
$op=selectOutput('ArrOutput','MemberClient');
$op->output('emptyCall');
?>

<?php include 'header.html';?>

<style type="text/css">
	.followed{ width:69px; height:28px; float:left; background:url(./img/jiaguanzhu-1.png) no-repeat;}
</style>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.4"></script>
<div class="dingche" style="height:inherit;">
  <!-- <div class="left" style="height:inherit;">
	<p class="guanzhu">
	  我关注的车：
	</p>
	<div class="guangzhu-che" style="background:#fff;">
	  <span class="che-pic"><img src="img/qiche-picture.png"></span>
	  <ul>
		<li class="qc-sx">
		  <p class="chex">
			小黄蜂
		  </p>
		  <p style="float:left;color:#02A3D9; margin-top:5px;">
			取消关注
		  </p>
		</li>
		<li class="qc-sx">福克斯嘉年华手动挡
		</li>
		<li class="qc-sx">位置：清华大学北停车场B区
		</li>
	  </ul>
	  <ul style="margin-top:10px;">
		<li style="list-style: none">
		  <a href="#" class="cxx-1" style="margin-left:30px;" title="价格"></a><a href="#" class="cxx-2" title="价格"></a><a href="#" class="cxx-3" title="价格"></a><a href="#" class="cxx-4" title="价格"></a>
		</li>
	  </ul>
	</div>
	<div class="guangzhu-che1">
	  <span class="che-pic"><img src="img/qiche-picture.png"></span>
	  <ul>
		<li class="qc-sx">
		  <p class="chex">
			小黄蜂
		  </p>
		  <p style=" float:left;color:#02A3D9; margin-top:5px;">
			取消关注
		  </p>
		</li>
		<li class="qc-sx">福克斯嘉年华手动挡
		</li>
		<li class="qc-sx">位置：清华大学北停车场B区
		</li>
	  </ul>
	  <ul style="margin-top:10px;">
		<li style="list-style: none">
		  <a href="#" class="cxx-1" style="margin-left:30px;" title="价格"></a><a href="#" class="cxx-2" title="价格"></a><a href="#" class="cxx-3" title="价格"></a><a href="#" class="cxx-4" title="价格"></a>
		</li>
	  </ul>
	</div>
	<div class="guangzhu-che1">
	  <span class="che-pic"><img src="img/qiche-picture.png"></span>
	  <ul>
		<li class="qc-sx">
		  <p class="chex">
			小黄蜂
		  </p>
		  <p style=" float:left;color:#02A3D9; margin-top:5px;">
			取消关注
		  </p>
		</li>
		<li class="qc-sx">福克斯嘉年华手动挡
		</li>
		<li class="qc-sx">位置：清华大学北停车场B区
		</li>
	  </ul>
	  <ul style="margin-top:10px;">
		<li style="list-style: none">
		  <a href="#" class="cxx-1" style="margin-left:30px;" title="价格"></a><a href="#" class="cxx-2" title="价格"></a><a href="#" class="cxx-3" title="价格"></a><a href="#" class="cxx-4" title="价格"></a>
		</li>
	  </ul>
	</div>
  </div> -->
  <div class="right" style="background:#D0D0D0; width:779px; height:inherit;">
	<div class="dt-sr">
	  <!-- <span class="zhuce-1-1" style="margin-left:15px; margin-top:8px;">
	  	<input id="captcha" name="" type="text" value="输入要查找的位置" class="dt-sr-1" style="width:240px"/>
	  	<button id="vGetCaptcha" class="anniu-dt-sr" style=" width:106px; height:42px; border:0">
	  		<span class="zhuce-1-1" style="margin-left:15px; margin-top:8px;">搜索</span>
	  	</button>
	  </span> --> 
	  <!--<a href="#" class="quanping"><img src="img/quanping.png">全屏</a>-->
	  <!--<a href="#" class="quanping"><img src="img/xianshi.png">显示视野内全部车</a>-->
	</div>
	<div id="baidu-map" style="background:#D0D0D0; width:779px; height:600px;">

	</div>
	
	<div id="info-window-template" style="display:none">
		<div class="dt-tanchu" >
		  <span class="che-pic1" style="width:150px; height:120px;">
		  	<img id='car-icon' src="img/qiche-picture.png">
		  </span>
		  <ul>
			<li class="qc-sx1">
			  <p class="chex1" id='car-name'>
				小黄蜂	
			  </p><a id='follow-href' href="javascript:void(0);" class="jiaguangzhu" onclick="javascript:changeStat(this);"></a>
			</li>
			<li class="qc-sx1" id="car-model">福克斯嘉年华手动福克斯嘉年华手动挡挡
			</li>
			<li class="qc-sx1" style="width:200px;" id='car-price-hour'>
			</li>
			<li class="qc-sx1" style="width:200px;" id='car-price-mile'>
			</li>
			<li class="qc-sx1">位置：<b id='car-station'>清华大学北停车场B区</b>	 
			</li> </ul>
			<a href="dingche.php" class="anniu-dt-sr book-car" style=" margin:20px 0 0 10px; padding:12px 0 0 0;">订车</a>
		</div>
	</div>	
  </div>
</div>

<?php include 'footer.html';?>
</body>
</html>
<script type="text/javascript">

var stationsCars = null;

var map= new BaiDuMap('baidu-map');

function changeStat(t,callMethod)
{
	var me = $(t);

	followCar(me.attr('car-id'),callMethod);
	me.removeClass('jiaguangzhu followed');
	me.addClass('followed');
	// jq.find('#follow-href').bind("click",follow);
	href='javascript:';
	me.attr('onclick','');

}
function BaiDuMap(idStr)
{
	var me=this;
	var defaultPoint={lng:'116.404',lat:'39.915'};
	var stationIcon='img/cxx-pic-2.png';
	var carIcon='img/cxx-pic-2-2.png';
	var smallWindowWidth=407;
	var smallWindowHeight=179;
	// var followUrl='../client/index.php?class=CarManager&item=follow&car_id='
	this.userPoint=null;
	var carInMap={};

	// this.carPointArr=[];
	this.init=function(idStr)
	{
		var defaultZoom = 10;
	
		me.map = new BMap.Map(idStr);            // 创建Map实例
		me.map.centerAndZoom(new BMap.Point(defaultPoint.lng,defaultPoint.lat),10)
		

		getUserPoisition(me.map);
		me.map.addControl(new BMap.NavigationControl());  //添加默认缩放平移控件
		me.map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_TOP_RIGHT, type: BMAP_NAVIGATION_CONTROL_SMALL}));  //右上角，仅包含平移和缩放按钮
		me.map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_BOTTOM_LEFT, type: BMAP_NAVIGATION_CONTROL_PAN}));  //左下角，仅包含平移按钮
		me.map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_BOTTOM_RIGHT, type: BMAP_NAVIGATION_CONTROL_ZOOM}));  //右下角，仅包含缩放按钮

		
		return me;
	}
	
	this.setStationMarkPoint=function(objMap,lng,lat)
	{
		//创建站点标记
		setMarkPoint(objMap,lng,lat,stationIcon);
	}
	this.setCarMarkPoint=function(objMap,lng,lat,carInfo)
	{
		//创建车位标记
		if (carInMap[carInfo.id]==null||carInMap[carInfo.id]==undefined)
		{
			var marker = setMarkPoint(objMap,lng,lat,carIcon);
			setMarkInfoWindow(marker,carInfo);
			carInMap[carInfo.id]=carInfo;

		}
	}

	var setMarkPoint=function(objMap,lng,lat,iconUrl)
	{
		//创建站点标记
		var pt = new BMap.Point(lng, lat);
		var myIcon = new BMap.Icon(iconUrl, new BMap.Size(26,26));
		var marker = new BMap.Marker(pt,{icon:myIcon});  // 创建标注
		objMap.addOverlay(marker);              // 将标注添加到地图中	
		return marker;
		
	}
	var viewCarPosition=function(objMap,lng,lat)
	{
		$.ajax({
	 		type: "POST",
		 	url: "../client/http/member.php",
		 	data:{'item':'getNearby','longi':lng*1000000,'lati':lat*1000000,'screen_width':smallWindowWidth,'screen_height':smallWindowHeight},
		 	success: function(data)
		 	{
		 		var ret=eval('('+data+')');
		 		var station=null;
		 		var car=null
		 		mapViewportArr=[];
		 		if (ret.errno==2000)
	 			{

	 				stationsCars=ret.content;
	 				//添加站点显示
	 				for(i in stationsCars)
	 				{
	 					car=stationsCars[i];
 						me.setCarMarkPoint(objMap,car.longitude/1000000,car.latitude/1000000,car);

	 					
	 					//添加viewport点自适应显示
	 					mapViewportArr.push(new BMap.Point(car.longitude/1000000,car.latitude/1000000));
	 					
	 					//
	 					// for(j in stationsCars[i])
	 					// {
	 					// 	if (parseInt(j)==j)
 						// 	{
 						// 		car = stationsCars[i][j];
 						// 		//添加viewport点自适应显示
 						// 		mapViewportArr.push(new BMap.Point(car.last_back_lng/1000000,car.last_back_lat/1000000));

 						// 		me.setCarMarkPoint(objMap,car.last_back_lng/1000000,car.last_back_lat/1000000,car);

 								
 						// 	}
	 						
	 					// }
	 				}
	 			}
	 			else
	 			{
	 				alert(ret.errstr);
	 			}
	 			//最后添加
	 			mapViewportArr.push(me.userPoint);
		 		var viewParam=objMap.getViewport(mapViewportArr);
		 		objMap.centerAndZoom(new BMap.Point(viewParam.center.lng,viewParam.center.lat),viewParam.zoom)
		 		//添加监听
		 		objMap.addEventListener("tilesloaded",function()
		 			{move(objMap)});
		 	}

	 	});
	}
	
	var getUserPoisition = function(objMap)
	{
		var geolocation = new BMap.Geolocation();
		geolocation.getCurrentPosition(function(r){
		    if(this.getStatus() == BMAP_STATUS_SUCCESS){
		        var mk = new BMap.Marker(r.point);
		        objMap.addOverlay(mk);
		        objMap.panTo(r.point);
		        me.userPoint=new BMap.Point(r.point.lng,r.point.lat);
		        viewCarPosition(objMap,r.point.lng,r.point.lat);

		        // alert('您的位置：'+r.point.lng+','+r.point.lat);
		    }
		    else {
		        // alert('failed'+this.getStatus());
		        var myCity = new BMap.LocalCity();
				myCity.get(function(result){

					var cityName = result.name;
					// cityName = '桂林市';
				    // objMap.setCenter(cityName);
				    // alert(cityName);
					var myGeo = new BMap.Geocoder();

					myGeo.getPoint(cityName, function(point){
					  if (point) {
					  	var mk = new BMap.Marker(point);
				        objMap.addOverlay(mk);
				        objMap.panTo(point);
				        // alert(point.lng+'   '+point.lat);
				        me.userPoint=point;
				        viewCarPosition(objMap,point.lng,point.lat);
					  }
					}, cityName);
				});

		    }        
		});
	}
	// this.map.centerAndZoom(new BMap.Point(117.293928,40.029517),10);
	
	var setMarkInfoWindow=function(marker,carInfo)
	{
		//创建信息窗口）
		var jqObj=$('#info-window-template');
		// jqObj.css("display","block");
		setSmallWindowMs(jqObj,carInfo);
		// alert();
		var smallWindow = new BMap.InfoWindow(jqObj.html());
		//显示窗口大小
		smallWindow.setWidth(smallWindowWidth);
		smallWindow.setHeight(smallWindowHeight);

		marker.addEventListener("click", function(){this.openInfoWindow(smallWindow);});

	}

	var setSmallWindowMs=function(jq,carInfo)
	{
		jq.find('#car-icon').attr('src','../cp/'+carInfo.icon);
		jq.find('#car-name').text(carInfo.name);
		jq.find('#car-model').text(carInfo.brand+carInfo.model+carInfo.gearbox);
		var discountTmpl='<font color=red>#s1#元/小时</font> <font style="text-decoration: line-through;">#s2#元/小时</font>';

		if (carInfo.timePriceMin==carInfo.timePriceMax) {
			if (carInfo.hourDiscount==1||carInfo.hourDiscount==undefined)
			{
				jq.find('#car-price-hour').text("时间租金："+carInfo.timePriceMin/100+"元/小时；");	
			}
			else
			{
				var html = discountTmpl.replace('#s1#',carInfo.timePriceMin/100*carInfo.hourDiscount);
				html = html.replace('#s2#',carInfo.timePriceMin/100);
				jq.find('#car-price-hour').html("时间租金："+html);
			}
			
		}else
		{	
			if (carInfo.hourDiscount==1)
			{
				var html = discountTmpl.replace(/\{\#s1\#\}/g,carInfo.timePriceMin/100*carInfo.hourDiscount+'-'+carInfo.timePriceMax/100*carInfo.hourDiscount)
				html = html.replace(/\{\#s2\#\}/g,carInfo.timePriceMin/100+'-'+carInfo.timePriceMax/100);
				jq.find('#car-price-hour').text("时间租金："+html);
				
			}
			else
			{
				jq.find('#car-price-hour').text("时间租金："+carInfo.timePriceMin/100+"元/小时；");
			}
			
		}
		jq.find('#car-price-mile').text("里程租金："+carInfo.milePrice/100+"元/公里");
		jq.find('#car-station').text(carInfo.station);
		//modify by yangbei 2013-07-01 修改href参数增加关注功能
		// jq.find('.book-car').attr('href','dingche.php#select-car'+carInfo.id);	
		jq.find('.book-car').attr('href','javascript:autoFollowed('+carInfo.id+')');
		//modify by yangbei 2013-07-01 
		if (carInfo.followed==1)
		{
			jq.find('#follow-href').removeClass('jiaguangzhu followed');
			jq.find('#follow-href').addClass('followed');
			// jq.find('#follow-href').bind("click",follow);
			// href='javascript:alert(\'已关注该车\')';
			// jq.find('#follow-href').attr('href',href);

		}
		else
		{
			jq.find('#follow-href').removeClass('followed jiaguangzhu');
			jq.find('#follow-href').addClass('jiaguangzhu');
			// href='javascript:followCar('+carInfo.id+');';
			jq.find('#follow-href').attr('car-id',carInfo.id);
		}
		
		// jq.find('#follow-href').bind('click',function(){
		// 	alert('ddd');
		// });
		jq.find('#car-station').text(carInfo.station);
	}
	var move = function (objMap)
	{
		
		var bs = objMap.getBounds();   //获取可视区域
		var bssw = bs.getSouthWest();   //可视区域左下角
		var bsne = bs.getNorthEast();   //可视区域右上角
		// alert(bssw+'  '+bsne);
		$.ajax({
	 		type: "POST",
		 	url: "../client/http/member.php",
		 	data:{'item':'getCarListByRang','start_lati':bssw.lat*1000000,'end_lati':bsne.lat*1000000,'start_longi':bssw.lng*1000000,'end_longi':bsne.lng*1000000},
		 	success: function(data)
		 	{
		 		var ret=eval('('+data+')');
		 		var station=null;
		 		var car=null
		 		if (ret.errno==2000)
	 			{
	 				stationsCars=ret.content;
	 				//添加站点显示
	 				for(i in stationsCars)
	 				{
	 					car=stationsCars[i];
	 					me.setCarMarkPoint(objMap,car.longitude/1000000,car.latitude/1000000,car);

	 				}
	 			}
	 			else
	 			{
	 				alert(ret.errstr);
	 			}

		 	}	

	 	});
	}
	return this.init(idStr);

}
//add by yangbei 2013-07-01 添加订车自动关注功能

function autoFollowed (carId) {
	var t = document.getElementById('follow-href');
	changeStat(t,go2Dingche(carId));
	
}
function go2Dingche (carId) {
	window.location.href='dingche.php#select-car'+carId;
}
//add by yangbei 2013-07-01 
</script>