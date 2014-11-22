<?php 

require_once (dirname(__FILE__) . '/../hfrm/Frm.php');
require_once (Frm::$ROOT_PATH . 'client/http/web.php');
require_once (Frm::$ROOT_PATH . 'order/classes/Order.php');
$op=selectOutput('ArrOutput','MemberClient');
$ret=$op->output('getMyOrderList');
if ($ret['errno']==2000) {
	$orders=$ret['content'];
	// print_r($orders);

}
else
{
	echo $ret['errstr'];exit;
}
?>


<?php include 'header.html';?>

<style type="text/css">
.baikuang2{ width:470px;float:left; margin:10px 0 0 0;padding:20px 5px 20px 5px; background:#fff; border-top:1px solid #DADADA; border-bottom:1px solid #DADADA;}
</style>
<div style="border:1 solid #fff;">
	<?php foreach ($orders as $order) { ?>
	<span class="baikuang2">
		<p class="huise-24-dingdan"  >
			订单编号：
			<span id="vOrderNo" class="chengse-text"><?php echo $order['order_no'] ?></span>
		</p> <span id="vOrderStatus" class="dd-zhuangtai" >
			<?php 
			$lanStatus=array('未支付','已预定','进行中','已完成','已取消','支付超时','还车超时');
			$lanModify=array('','修改中','修改完成');
			echo $lanStatus[$order['order_stat']];
			echo ' '.$lanModify[$order['modify_stat']];
			?>

			</span>
		<p class="huise-18-dingdan" style="height:auto">
			预定起止时间：
			<span id="vOrderTime" class="jiesuan-lanse" ><?php echo $order['order_start_time'] ?> 至 <?php echo $order['order_end_time'] ?> </span>

		</p>
		<p class="huise-18-dingdan" style="height:auto">
			成交时间：
			<span id="vOrderAddTime" ><?php echo $order['add_time'] ?> </span>
		</p>
		<p class="huise-18-dingdan" style="height:auto; clear:left" >
			实收押金：
			<span id="vOrderDeposit"  style=" font-weight:bold;"> <?php echo $order['deposit']/100 ?>元</span>
		</p>
		<p class="huise-18-dingdan" style="height:auto; clear:left" >
			站点车型：
			<span id="vOrderStation" style=" font-weight:bold;" ><?php echo $order['station'] ?>，<?php echo $order['brand'].$order['name'] ?></span>
		</p>
		<p class="zhuangtai">
			<?php 
			
			
			if (($order['order_stat']==Order::$STATUS_NOT_PAY ||$order['order_stat']==Order::$STATUS_ORDERED ||$order['order_stat']==Order::$STATUS_STARTED) AND $order['modify_stat']== 0 )
			{   ?>
				

				<span> <a href="javascript:void(0)" order-id=<?php echo $order['order_id'] ?> class="anniu-3 modify-order">修改订单</a> </span>	

			<?php 
			}


			if (($order['order_stat']==Order::$STATUS_NOT_PAY ||$order['order_stat']==Order::$STATUS_ORDERED) AND $order['modify_stat']== 0 )
			{   ?>
				
				<span id="vCancelOrder"> <a href="javascript:void(0)" class="anniu-3">取消订单</a> </span>
			<?php 
			}


			if ($order['order_stat']==Order::$STATUS_NOT_PAY)
			{
				?>
				<span id="vOrderBtnPay"> <a href="javascript:;"  order-id=<?php echo $order['order_id'] ?>  class="anniu-3 payfor-order">支付</a> </span>
			<?php 
			}
			if ($order['order_stat']==Order::$STATUS_COMPLETED)
			{
				?>
				<span id="invoice"> <a href="javascript:void(0)" class="anniu-3" algin="center">发票索取</a> </span>
				<span id="showOrder" orderID=""> <a href="javascript:void(0)" class="anniu-3" algin="center">晒单</a> </span>
				<span id="bill" orderID=""> <a href="javascript:void(0)" class="anniu-3" algin="center">账单</a> </span>
			<?php 
			}
			 ?>

		</p> 
	</span>

	<?php } ?>
</div>

<form method="POST" action="xg-dingdan.php" id='subform' style="display:none;">
	<input type="hidden" id="order-id" name="order_id" value="">
	<input type="hidden" id="ref" name="ref" value="">
</form>
<script type="text/javascript">
$('.modify-order').click(function(){
	var me = this;
	var orderId=$(me).attr("order-id");
	$("#order-id").val(orderId);
	$("#ref").val("modify");
	document.getElementById('subform').submit();
	// $.post("xg-dingdan.php", { order_id: orderId} );
});

$('.payfor-order').click(function(){
	var me = this;
	var orderId=$(me).attr("order-id");
	$("#order-id").val(orderId);
	$("#ref").val("payfor");
	document.getElementById('subform').action="zhifu-1.php";
	document.getElementById('subform').submit();
	// $.post("xg-dingdan.php", { order_id: orderId} );
});
</script>
<?php include 'footer.html';?>

