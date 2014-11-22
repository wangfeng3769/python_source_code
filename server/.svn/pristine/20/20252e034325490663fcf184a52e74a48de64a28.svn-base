
	<?php include 'header.html';?>
	<?php 
		//订车列表
		require_once (dirname(__FILE__) . '/../hfrm/Frm.php');
		require_once (Frm::$ROOT_PATH . 'client/http/web.php');
		$op=selectOutput('ArrOutput','MemberClient');
		//$className, $methodName,$argv
		$ret=$op->output('getModifyOrderInfo');
		$car=$ret['content'];
		$myCarList[]=$car;
		 ?>
<div class="dingche">
<div class="left" style="height:inherit;">

		<?php 

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
			<li class="qc-sx"><?php echo $car['brand'].$car['model'].$car['gearbox']; ?></li>
			<li class="qc-sx"><?php echo $car['station'] ?></li>
		</ul>
		<ul style="margin-top:2px;">
			<!-- <a href="#" class="cxx-1" style="margin-left:30px;"title="价格"><font color="red"></font></a>
			<a href="#" class="cxx-2 map" title="地图显示"></a>
			<a href="#" class="cxx-3" title="价格"></a>
			<a href="#" class="cxx-4" title="价格"></a> -->
			<?php if ($car['timePriceMin']==$car['timePriceMax']) {?>
				<li class="qc-sx">时租金:<?php echo ($car['timePriceMax']/100).'元'; ?></li>
			<?php }else{ ?>
				<li class="qc-sx">时租金:<?php echo ($car['timePriceMin']/100).'元-'.($car['timePriceMax']/100).'元'; ?></li>
			<?php } ?>
			
			<li class="qc-sx">公里价:<?php echo ($car['milePrice']/100).'元'; ?></li>
		</ul>
	</div>
	<?php } ?>


</div>
		

<div class="dingche-right" style="width:779px; height:100%;" >
	<div id="timeline-info-div" class='jq-hide-div'>
	<span class="dingche-title">
		<p class="dingche-title-1">修改订单</p>
		<p class="zhuangtai-1">空闲时段</p>
		<p class="zhuangtai-2">已被预订</p><p class="zhuangtai-3" style=" display:none;">欢迎搭乘</p>
	</span>
<?php $today=time(); ?>
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
			<input name='ref' type="text" value='modify' style="width:180px;display:none;"/>
			<input id="old-order-id" name='old_order_id' type="text" value=<?php echo $myCarList[0]['old_order_id'] ?> style="width:180px;display:none;"/>
			<span class="huizi-18" style="width:130px; margin-top:10px;">取车时间：</span>
			<input name="start_time" type="text" class="zhuce-sr start-time-input" style="width:180px"/>
			<img src="img/rili.jpg">
			<!-- <input id="get-time" name="start_time" type="text" class="zhuce-sr" style="width:100px">
			<img src="img/shijian.jpg"> -->
		</div>
		<div class=" dingdanshijian">
			<span class="huizi-18" style="width:130px; margin-top:10px;">取车时间：</span>
			<input name="end_time" type="text" class="zhuce-sr end-time-input" style="width:180px">
			<img src="img/rili.jpg">
			<!-- <input id="back-time" name="end_time" type="text" class="zhuce-sr" style="width:100px">
			<img src="img/shijian.jpg"> -->
		</div>
		<div class="dingdanshijian">
			<span class="huizi-18" style="width:130px; margin-top:10px;">用车时长：</span><span class="huizi-18" style="width:50px; margin-top:10px;">3小时</span>
		</div>

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
	</div> <!-- 时间轴e -->


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
			<p class="dingdan-xinxi-1" style="font-weight:bold;">亲爱的<b class="chengse" class="user-name">  <?php echo $user['uname'] ?></b>，您预订了 <b class="chengse"> </b>易多汽车共享的 <b class='car-name'>小黄蜂，</b> <!-- 用于私用。 --></p>
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

	<!--  地图找车-->
<div id="car-map">


</div>	<!--  地图找车-->

</div>

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

$('.start-time-input').datetimepicker({'dateformat':'Y-m-d',
	'onSelect':(function(e){
		$(".order-start-time").text(e);
	})													
});

$('.end-time-input').datetimepicker({'dateformat':'Y-m-d',
	'onSelect':(function(e){
		$(".order-end-time").text(e);
	})													
});

$(".submit-caculate").click(function(){
	sendAjax(carId);
});


function sendAjax(carId)
{
	 $.ajax({
 		type: "POST",
	 	url: "dingche-tijiao.php?car_id="+carId,
	 	data:serializeDateInput(),
	 	success: function(data)
	 	{
	 		$('.jq-hide-div').hide();
	 		$('#caculate-submit').show();
	 		$('#caculate-result').html(data);
	 	}
 });
}

function createOrderAjax()
{
 	$.ajax({
 		type: "POST",
	 	url: "dingche-tijiao.php?car_id="+carId,
	 	data:serializeDateInput(),
	 	success: function(data)
	 	{
	 		$('.jq-hide-div').hide();
	 		$('#caculate-submit').show();
	 		$('#caculate-result').html(data);
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
	 	
	 		alert(data);
	 	}
 	});	

}
</script>
<?php include 'footer.html';?>

