<?php //订车列表
//初始化
        require_once (dirname(__FILE__) . '/../hfrm/Frm.php');
        require_once (Frm::$ROOT_PATH . 'client/http/web.php');
        $op=selectOutput('ArrOutput','MemberClient');
		//$className, $methodName,$argv
		$ret=$op->output('getPreferenceCarList');
		$myCarList=$ret['content'];

?>
<?php include 'header.html';?>


<div class="dingche" style="height: inherit;">
	<div class="left" style="height: inherit;">
		<p class="guanzhu">我关注的车：</p>
		<?php 
		if (empty($myCarList)) {
			echo '你没有关注的车';
			echo "<script LANGUAGE='Javascript'>"; 
			echo "location.href='zhaoche.php'"; 
			echo "</script>"; 
			die;
		} 
		$n=0;
		foreach ($myCarList as $car) {
			# code...
		 ?>
		<div <?php if($n==0){echo 'class="guangzhu-che jq-car-div"';}else{echo 'class="guangzhu-che1 jq-car-div"';} $n++;?> >
		<span class="che-pic" id="car-<?php echo $car['id'] ;?>" onclick="selectCar(<?php echo $car['id'] ;?>);">
			<img src="../cp/<?php echo $car['icon'] ?>"/>
		</span>
		<ul>
			<li class="qc-sx">
				<p class="chex"><?php echo $car['name'] ;?></p>
				<p style=" float:left;color:#02A3D9; margin-top:5px;" class='cancel-follow-b'> <a name='test' href="javascript:cancelFollow(<?php echo $car['id'] ;?>)">取消关注</a></p>
			</li>
			<li class="qc-sx"><?php echo $car['number'] ?></li>
			<li class="qc-sx"><?php echo $car['brand'].$car['model'].$car['gearbox']; ?></li>
			<li class="qc-sx"><?php echo $car['station'] ?></li>
		</ul>
		<ul style="margin-top:2px;">
			<!-- <a href="#" class="cxx-1" style="margin-left:30px;"title="价格"><font color="red"></font></a>
			<a href="#" class="cxx-2 map" title="地图显示"></a>
			<a href="#" class="cxx-3" title="价格"></a>
			<a href="#" class="cxx-4" title="价格"></a> -->
			<?php if ($car['timePriceMin']==$car['timePriceMax']) {?>
				<li class="qc-sx">
					<?php 
						if ($car['hourDiscount']!=1) { ?>
						<font color='red'>	 时租金:<?php echo ($car['timePriceMax']/100*$car['hourDiscount']).'元'; ?></font>
						<font style="text-decoration: line-through;">时租金:<?php echo ($car['timePriceMax']/100).'元'; ?></font>	
					<?php }else{?>
						时租金:<?php echo ($car['timePriceMax']/100)*$car['hourDiscount'].'元'; ?>
					<?php }?>
				</li>
		<?php }else{ ?>
				<li class="qc-sx">时租金:<?php echo ($car['timePriceMin']/100)*$car['hourDiscount'].'元-'.($car['timePriceMax']/100)*$car['hourDiscount'].'元'; ?></li>
			<?php } ?>
			
			<li class="qc-sx">公里价:<?php echo ($car['milePrice']/100).'元'; ?></li>
		</ul>
	</div>
	<?php } ?>
	</div>
	
<div>
<table cellpadding="0" cellspacing="0" class="result-op" srcid="10382" >
	<style>
		.op_traffic_main {
			margin-top: 12px
		}
		.op_traffic_main,.op_traffic_tab th {
			font-size: 13px
		}
		.op_traffic_time {
			width: 300px;
			height: 56px;
			margin-bottom: 10px;
			overflow: hidden
		}
		.op_traffic_left,.op_traffic_right {
			float: left
		}
		.op_traffic_left {
			width: 111px;
			border-right: 1px	solid #ccc
		}
		.op_traffic_right {
			padding-left: 30px
		}
		.op_traffic_title,.op_traffic_remark,.op_traffic_txt	span {
			color: #666
		}
		.op_traffic_title,.op_traffic_tab td {
			font-size: 14px
		}
		.op_traffic_title,.op_traffic_txt,.op_traffic_off {
			font-weight: bold
		}
		.op_traffic_txt,.op_traffic_off {
			color: #333;
			line-height: 30px
		}
		.op_traffic_txt {
			font-size: 30px;
			font-family: arial;
			padding: 9px	0 0 8px;
			*padding: 5px 0 0 8px
		}
		.op_traffic_txt span {
			display: inline-block;
			font-size: 14px;
			vertical-align: middle;
			padding: 0	6px 5px 8px;
			*padding: 0 6px 0 8px
		}
		.op_traffic_off {
			font-size: 23px;
			font-family: '微软雅黑','黑体';
			padding: 7px	0 0 6px
		}
		.op_traffic_note {
			line-height: 22px
		}
		.op_traffic_tab {
			width: 100%;
			margin: 4px	0
		}
		.op_traffic_tab th,.op_traffic_tab td {
			padding-left: 8px
		}
		.op_traffic_tab	th {
			background: url(http: //www.baidu.com/aladdin/img/table/bg.gif) repeat-x	0 -34px;
			height: 26px;
			line-height: 26px;
			font-weight: normal;
			text-align: left
		}
		.op_traffic_tab	td {
			border-bottom: 1px solid #eee;
			height: 34px;
			line-height: 34px
		}
		.op_traffic_remark,.op_traffic_holiday,.op_traffic_more {
			line-height: 20px;
			font-size: 12px
		}
		.op_traffic_more {
			padding-top: 4px;
			font-family: simsun
		}
		.op_traffic_clear {
			clear: both;
			height: 0;
			line-height: 0;
			font-size: 1px;
			margin-top: 0;
			visibility: hidden;
			overflow: hidden
		}
		.op_traffic_showurl {
			color: #008000
		}
	</style>
	<script type="text/javascript">
		function isToday(){
			var myDate = new Date();					//创建日期
			var rs = myDate.getDay();					//获取当前星期几
			if (rs!=0 && rs!=6){						//排除周六、日
				var fon = document.getElementById(rs);	//根据周几获取元素
				fon.style.color = "red";				//更改样式
			}
		}
	</script>
	<tbody>
		<tr>
			<td class="f">
				<div class="op_traffic_main">
					<div class="op_traffic_note">
						&nbsp;&nbsp;2013年7月7日--2013年10月5日，
						<em>
							限行
						</em>
						规则：
					</div>
					<table cellspacing="0" cellpadding="0" class="op_traffic_tab">
						<tbody>
							<tr>
								<th id="1">
									周一
								</th>
								<th id="2">
									周二
								</th>
								<th id="3">
									周三
								</th>
								<th id="4"> 
									周四
								</th>
								<th id="5">
									周五
								</th>
								<th id="6">
									公休/节假日
								</th>
							</tr>
							<tr>
								<td>
									3和8
								</td>
								<td>
									4和9
								</td>
								<td>
									5和0
								</td>
								<td>
									1和6
								</td>
								<td>
									2和7
								</td>
								<td>
									
									<em>
										不限行
									</em>
								</td>
							</tr>
						</tbody>
					</table>
					<div class="op_traffic_remark" >
						<!-- modify by yangbei 修改字体颜色 start -->
						<p style="color: #A8A8A8">注：含临时号牌，机动车车牌尾号为英文字母的按0号管理</p>
						<!-- modify by yangbei 修改字体颜色 end -->
					</div>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<!-- add by yangbei 添加优惠季公告 start-->
<hr/>
<!-- <span style="font-size: 16px;color:red">易多4月特惠季（4月10日-4月28日），所有车辆<font size="5">9.8</font>元/小时（<font size="5">不设日封顶价</font>），欢迎广大会员用车！</span> -->
<!-- add by yangbei 添加优惠季公告 end-->
<script type="text/javascript">
	window.onload=isToday();
</script>
</div>
<div class="dingche-right" style="width:779px; height:900px;" >
	<div id="timeline-info-div" class='jq-hide-div'>
	<span class="dingche-title">
		<p class="dingche-title-1">订车</p>
		<p class="zhuangtai-1">空闲时段</p>
		<p class="zhuangtai-2">已被预订</p><p class="zhuangtai-3" style=" display:none;">欢迎搭乘</p>
	</span>
<?php $today=time();?>
<?php foreach (array(0,1,2) as $v) {  ?>
	<div class="shijianzhou">
		<span class="dingche-title-2"><?php echo date('Y-m-d',$today+$v*24*3600) ?></span>
		<span class="shijianzhou-1" id="time-line-<?php echo $v; ?>">
	<!-- 		<p class="shijianzhou-2" style="width:0px; margin-left:0px;display=none;" ></p> -->
		</span>
		<span class="shijianzhou-sj">
			<p class="shijianzhou-sj-1"></p>
			<p class="shijianzhou-sj-1"></p>
			<p class="shijianzhou-sj-1"></p>
			<p class="shijianzhou-sj-1"></p>
			<p class="shijianzhou-sj-1"></p>
			<p class="shijianzhou-sj-1"></p>
			<p class="shijianzhou-sj-1"></p>
			<p class="shijianzhou-sj-1"></p>
		</span>
		<span class="shijianzhou-sj-txt">
			<p class="shijianzhou-sj-txt-1">0:00</p>
			<p class="shijianzhou-sj-txt-1">3:00</p>
			<p class="shijianzhou-sj-txt-1">6:00</p>
			<p class="shijianzhou-sj-txt-1">9:00</p>
			<p class="shijianzhou-sj-txt-1">12:00</p>
			<p class="shijianzhou-sj-txt-1">15:00</p>
			<p class="shijianzhou-sj-txt-1">18:00</p>
			<p class="shijianzhou-sj-txt-1">21:00</p>
		</span>
	</div>
<?php }  ?>

	<form action="zhifu-1.php" method="POST">
		<div class="dingdanshijian">
			<input id="car-id" name='car_id' type="text" style="width:180px;display:none;"/>
			<input  name='ref' value="cre" type="text" style="width:180px;display:none;"/>

			<span class="huizi-18" style="width:130px; margin-top:10px;">取车时间：</span>
			<input name="start_time" type="text" class="zhuce-sr start-time-input" style="width:220px"/>
			<img src="img/rili.jpg">
			<!-- <input id="get-time" name="start_time" type="text" class="zhuce-sr" style="width:100px">
			<img src="img/shijian.jpg"> -->
		</div>
		<div class=" dingdanshijian">
			<span class="huizi-18" style="width:130px; margin-top:10px;">还车时间：</span>
			<input name="end_time" type="text" class="zhuce-sr end-time-input" style="width:220px">
			<img src="img/rili.jpg">
			<!-- <input id="back-time" name="end_time" type="text" class="zhuce-sr" style="width:100px">
			<img src="img/shijian.jpg"> -->
		</div>
		<!-- <div class="dingdanshijian">
			<span class="huizi-18" style="width:130px; margin-top:10px;">用车时长：</span>
			<span class="huizi-18" style="width:50px; margin-top:10px;">3小时</span>
		</div> -->

		<?php if (false) { ?>
		<div class="dingdanshijian1">
			<input name="" type="checkbox" value="" class="dl-dx-1"><img src="img/yongche-1.jpg"><span class="huizi-18" style="width:40px; margin-top:5px;">私用</span><input name="" type="checkbox" value="" class="dl-dx-1"><img src="img/yongche-2.jpg"><span class="huizi-18" style="width:260px; margin-top:5px;">回家 18:00-下一天8:00，计费3小时</span>
		</div>
		<div class="dingdanshijian1">
			<input name="" type="checkbox" value="" class="dl-dx-1"><img src="img/yongche-1.jpg"><span class="huizi-18" style="width:40px; margin-top:5px;">公用</span><input name="" type="text" class="zhuce-sr" style="width:260px; margin-left:10px;">
		</div>
		<?php }  ?>
		<!-- <a href="#" class="anniu-4 submit" style="margin:20px 0 0 50px;">提交</a> -->
		<a href="#" class="anniu-4 submit-caculate" style="margin:20px 0 0 50px;">提交</a>
	</div><!-- 时间轴e -->

	<!-- 订单计费页面 -->
	<div id="caculate-submit" class='jq-hide-div'>
		<span class="dingche-title">
			<p class="dingche-title-1">订车费用结算</p>
		</span>		
		<div class="dingdan-xinxi">
			<?php 
			$user=$op->output('getUserInfo');
			$user=$user['content'];

			 ?>
			<p class="dingdan-xinxi-1" style="font-weight:bold;">亲爱的<b class="chengse" class="user-name">  <?php echo $user['true_name'] ?></b>，您预订了 <b class="chengse"> </b>易多汽车共享的 <b class='car-name'>小黄蜂，</b> <!-- 用于私用。 --></p>
			<p class="dingdan-xinxi-1">请确定以下信息后提交订单并支付费用。</p>
		</div>
		<div class="dingdan-xinxi">
			<p class="dingdan-xinxi-2"> 驾驶用户姓名： <b class='user-true-name'>  <?php echo $user['true_name'] ?></b> </p>
			<p class="dingdan-xinxi-2" >手机号码： <b class='user-phone'> <?php echo $user['phone'] ?></b> </p>
		</div>
		<div class="dingdan-xinxi">
			<p class="dingdan-xinxi-2">取车时间： <b class='order-start-time'>2013-1-25 09:00</b> </p><p class="dingdan-xinxi-2">还车时间： <b class="order-end-time">2010-1-25 14:00</b></p>
			<p class="dingdan-xinxi-2">取车网点： <b class="car-station"> 清华东食堂停车场 </b> </p>
			<p class="dingdan-xinxi-2">车型信息： <b class="car-model-info">小黄蜂/F3DM/自动挡/</b> </p>
			<p class="dingdan-xinxi-2">车牌号： <b class="car-number">京N292ED</b> </p>
		</div>	

		<div id="caculate-result">


		</div>

		<div class="dingdan-xinxi">
<!-- 			<a href="javascript:void(0);" class="anniu-5 order-submit" style="margin:20px 0 0 10px;">提交订单并支付</a> -->
			
			<input type='submit' class="anniu-5 order-submit" style="margin:20px 0 0 10px;"/>
			</form>
		</div>
	</div>	<!-- 订单计费页面 -->


</div>


	
</div>

	<!--  地图找车-->
<div id="car-map">


</div>	<!--  地图找车-->



<script type="text/javascript">
// alert($("#time-line").width());
<?php
		$jsonCarTime=array();
		$carList=array(); 
 		foreach ($myCarList as $car) {
 				$jsonCarTime[$car['id']] = $car['orderTime'];
 				$carList[$car['id']]=$car;
		}
 ?>
// var timeObj=<?php echo json_encode($jsonCarTime); ?>;
var carList=<?php  echo json_encode($carList);?>

// var timeLineTools = new TimeLineClass();
var carId=<?php echo $myCarList[0]['id']; ?>;
selectCar(carId);

$('.start-time-input').datetimepicker({'altTimeFormat':'Y-m-d',
	'onSelect':(function(e){
		$(".order-start-time").text(e);
	})													
});

$('.end-time-input').datetimepicker({'altTimeFormat':'Y-m-d',
	'onSelect':(function(e){
		$(".order-end-time").text(e);
	})													
});

$(".submit-caculate").click(function(){
	sendAjax(carId);
});
$(".jq-car-div").click(function(){
	var me = $(this);
	$('.guangzhu-che').addClass('guangzhu-che1');
	$('.guangzhu-che').removeClass('guangzhu-che');

	me.removeClass('guangzhu-che1');
	me.addClass('guangzhu-che');
})
// (function($) {
// 	$.timepicker.regional['zh-CN'] = {
// 		timeOnlyTitle: '选择时间',
// 		timeText: '时间',
// 		hourText: '小时',
// 		minuteText: '分钟',
// 		secondText: '秒钟',
// 		millisecText: '微秒',
// 		timezoneText: '时区',
// 		currentText: '现在时间',
// 		closeText: '关闭',
// 		timeFormat: 'HH:mm',
// 		amNames: ['AM', 'A'],
// 		pmNames: ['PM', 'P'],
// 		isRTL: false
// 	};
// 	$.timepicker.setDefaults($.timepicker.regional['zh-CN']);
// })(jQuery);

function sendAjax(carId)
{
	 $.ajax({
 		type: "POST",
	 	url: "dingche-tijiao.php?is_ajax=1&car_id="+carId,
	 	data:serializeDateInput(),
	 	success: function(data)
	 	{
	 		try {
				var ret = eval('('+data+')');
		 		if (ret.errCode!=2000)
	 			{
	 				alert(ret.errStr);
	 			}
			} catch(e) {
				$('.jq-hide-div').hide();
		 		$('#caculate-submit').show();
		 		$('#caculate-result').html(data);
			}
	 		
	 	}
 });
}

function createOrderAjax()
{
 	$.ajax({
 		type: "POST",
	 	url: "dingche-tijiao.php?is_ajax=1&car_id="+carId,
	 	data:serializeDateInput(),
	 	success: function(data)
	 	{
	 		var ret = eval('('+data+')');
	 		if (ret.errCode!=2000)
 			{
 				alert(ret.errStr);
 			}
 			else
 			{
		 		$('.jq-hide-div').hide();
		 		$('#caculate-submit').show();
		 		$('#caculate-result').html(data);
	 		}
	 	}
 	});
}

function selectCar(intCarId)
{
	var timeLineTools = new TimeLineClass();
	timeLineTools.addTimeLineByTags(carList[intCarId].orderTime);
	carId=intCarId;
	document.getElementById('car-id').value=carId;
	// $('#car-id').value=carId;
	$('.car-name').text(carList[intCarId].name);
	$('.car-number').text(carList[intCarId].number);
	$('.car-station').text(carList[intCarId].station);
	$('.car-model-info').text(carList[intCarId].brand+'/'+carList[intCarId].model+'/'+carList[intCarId].gearbox);
	// $('form').action='zhifu-1.php?car_id='+carId;
	$('.jq-hide-div').hide();
	$('#timeline-info-div').show();
}

function cancelFollow(carId)
{
	var p = this;	
	$.ajax({
 		type: "POST",
	 	url: "../client/index.php?class=UserManager&item=removeFollowByUserId&carId="+carId,
	 	success: function(data)
	 	{
	 	
	 		location.reload() ;
	 	}
 	});	

}

hash = window.location.hash;
if (hash.length>1)
{	
	//parseInt()hash=='car'
	hash=hash.replace('#','');
	if (hash.substring(0,10)=='select-car')
	{
		var carId2=parseInt(hash.replace('select-car',''));
		// alert(carId);
		selectCar(carId2);
		$('#car-'+carId2).parent().click();

	};
    // $(hash.replace('#','.')).click();
};
</script>


<?php include 'footer.html';?>