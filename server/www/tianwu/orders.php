<?php 
		session_start();
		if ($_SESSION['uid']<1) {
			header('Location:index.php');
			exit();
		}
		$jsPaths=array('../js/jquery.form.js');
		include 'header.html';
	?>
		
		<div  class="neirong">
			<div  class="neirong-1">
				<!-- add by yangbei 2013-04-27 增加继续预定按钮 -->
				<a href="index.php" class="dl-bt-3" style=" margin:30px 300px 10px 360px;">继续预订</a>
				<!-- add by yangbei 2013-04-27 增加继续预定按钮 -->
				<p class="gongche-denglu-5" style="width:790px; margin-left:90px; padding-top:10px;border-top:1px solid #bbb;">我的预订：</p>
				<?php 
					
					require_once (Frm::$ROOT_PATH . '/client/http/web.php');
					require_once (Frm::$ROOT_PATH . 'order/classes/OrderRecord.php');
					require_once (Frm::$ROOT_PATH . 'order/classes/Order.php');
					$lanStatus=array('未支付','已预定','进行中','进行中','已取消','支付超时','还车超时','','订单完成','订单完成');
					$page = intval($_GET['page']);
					if (empty($page)) {
						$page=0;
					}
					$op=selectOutput('ArrOutput','MemberClient');
					$ret=$op->output('getUserInfo');
					$userInfo=$ret['content'];
					// print_r($userInfo);
					$or = new OrderRecord();
					$orders=$or -> getMyOrderPageList($userInfo['uid'],$page);
					$pager=$orders['pager'];
					$orders=$orders['orders'];
					unset($orders['pager']);
				
				?>




				<?php foreach ($orders as $order) {?>
				<span  class="yuyue">
					<p  class="yuyue-1">预约号：<?php echo $order['order_no'] ?><c style="margin-left: 425px;"> <?php echo $lanStatus[$order['order_stat']];?></c></p>
					<p  class="yuyue-left"><b>取车时间：<?php echo $order['order_start_time'] ?></b><b>还车时间：<?php echo $order['order_end_time'] ?></b></p>
					<?php 
		                if (($order['order_stat']==Order::$STATUS_NOT_PAY ||$order['order_stat']==Order::$STATUS_ORDERED) AND $order['modify_stat']== 0 )
		                {   ?>      
		                    <span id="vCancelOrder"><a  href="javascript:void(0)" order-id="<?php echo $order['order_id'] ?>" class="dl-bt-1 popbox-link"  style=" margin:40px 0 0 180px;padding-top: 11px;">取消预约</a></span>

		                <?php 
		                }
                		
                	?>       
					
				</span>
				
 				<?php } ?>
 				<span class="fanye" style="width: 460px;">
 					
 					<?php 
 						$pageName='';
 						$echoPage='';
 						$showNum = 5;
 						$j=0;
 						$tmpl='<a href="orders.php?page=%s" class="fanye-bt">%s</a>';
 						$tmpl2='<b class="fanye-bt" style="color:#CC3A50;">%s</b>';
 						echo sprintf($tmpl,'0','首页');
 						if ($pager['page']-1>0) {
 							echo sprintf($tmpl,$pager['page']-2,'上一页');
 						}
 						
 						if ($page!=$pager['pageCountNum']-1) {
 							if ($pager['pageCountNum']>$showNum) {
	 							$showNum =5;
	 						}else{
	 							$showNum = $pager['pageCountNum'];
	 						}
	 						if ($page>=$showNum-2) {
	 							$j=0;
	 							$j=$page;
	 							$showNum =$showNum+$page;
	 							if ($showNum>$pager['pageCountNum']) {
	 								$showNum=$pager['pageCountNum'];
	 							}

	 						}
 						}else{
 							if ($page-4>0) {
 								$j=$page-4;
 							}else{
 								$j=0;
 							}
 							
 							$showNum=$page+1;
 						}
 						for ($i=$j; $i < $showNum; $i++) {
	 							$pageName=$i+1;
	 							$echoPage=$i;
	 						if ($page==$i) {
	 							$echoStr=sprintf($tmpl2,$pageName);
	 						}else
	 						{
	 							$echoStr=sprintf($tmpl,$echoPage,$pageName);
	 						}
							
							echo $echoStr; 
	 						
 					} 
 					if ($pager['page']<$pager['pageCountNum']) {
 							echo sprintf($tmpl,$pager['page'],'下一页');
					}
						echo sprintf($tmpl,$pager['pageCountNum']-1,'末页');
						echo("<c style='position:relative;top:6px;left:5px;'>".'第'.$pager['page'].'页/共'.$pager['pageCountNum'].'页'."</c>");
 					?>

 				</span>
				 <form method="POST" action="xg-dingdan.php" id='subform' style="display:none;">
		            <input type="hidden" id="order-id" name="order_id" value="">
		            <input type="hidden" id="ref" name="ref" value="">
		        </form>
			</div>
		

		</div>		
		<div id="screen"></div>
		<div class="popbox">
			<div class="mainlist">
				<div class="tanchu-dd2">
					<span class="yuyue-1" style="width:300px; margin:15px 0 0 30px;">预约取消</span>
					<a href="#" class="guanbi close-btn" style=" margin:15px 0 0 10px;"></a>
					<p class="tanchu-dd-1" style="margin-top:30px;">您是否要取消预约？</p>
					<a href="javascript:" class="dl-bt-1 sure-cancel" style=" margin:25px 0 0 85px;" >是</a>
					<a href="#" class="dl-bt-1 close-btn" style=" margin:25px 0 0 10px;">否</a>
				</div>
			</div>
		</div>
		<!-- modify by yangbei 2013-04-27 修改图层z-index属性 -->
		<div id="msg-box" style="z-index:102;">

		</div>

<script id='msgTmp' type="text/template">
	<span class="tanchu-2" style="margin-left:400px; ">
		<p class="tanchu-dd-1" style=" width:500px;margin-top:45px;text-align:center;">{#msg#}</p>
		<a href="" class="dl-bt-1 close-msg-window" style=" margin:35px 0 0 230px;">返回</a>
	</span>
</script>
<script type="text/javascript">
$(document).ready(function(){
	//add by yangbei 2013-04-27 增加背景填充方法
	$(function() {
		$(".neirong").append("<img src='img/ht-bg.jpg' id='bigpic'>");
		cover();
		$(window).resize(function() {
			cover();
		});
	});

	function cover() {
		var win_width = $(window).width();
		var win_height = $(window).height();
		$(".neirong").attr({
			width: win_width,
			height: win_height
		});
	}
	//add by yangbei 2013-04-27 增加背景填充方法
	function makeScreenGray()
	{
		var h = $(document).height();
		$('#screen').css({ 'height': h });	
		$('#screen').show();
	}

	$('.close-btn').click(function(){
		$('.popbox').fadeOut(function(){ $('#screen').hide(); });
		return false;
	});
	
	$('.popbox-link').click(function(){
		makeScreenGray();

		var orderId=$(this).attr('order-id');
		$('.popbox').find('.sure-cancel').attr('order-id',orderId);
		$('.popbox').center();
		$('.popbox').fadeIn();
		return false;
	});
	
});
$('.sure-cancel').click(function() {
	var h = $(document).height();
	$('#screen').css({ 'height': h });
	$('#screen').show();
    var orderId=$(this).attr('order-id');
    
	$.ajax({
		type: "POST",
		url: "/client/http/member.php?item=twCancel&order_id=" + orderId,
		success: function(data) {
			var ret = eval("(" + data + ")");
			if (ret.errno == 2000) {
				//modify by yangbei 2013-04-27 修改显示阴影层
				//$('.close-btn').click();
				$(".tanchu-dd2").hide();
				showMsg('取消成功');
			}else
			{
				// $('.close-btn').click();
				$(".tanchu-dd2").hide();
				//modify by yangbei 2013-04-27 修改显示阴影层
				showMsg(ret.errstr);
			}
			function makeScreenGray()
			{
				var h = $(document).height();
				$('#screen').css({ 'height': h });	
				$('#screen').show();
			}
		}
	});
});
</script>
	</body>
</html>

<script type="text/javascript">
hash = window.location.hash;
if (hash.length > 0) {
	$(hash.replace('#', '.')).click();
};
</script>
