<?php 

require_once (dirname(__FILE__) . '/../hfrm/Frm.php');
require_once (Frm::$ROOT_PATH . 'client/http/web.php');
$op=selectOutput('ArrOutput','MemberClient');
//$className, $methodName,$argv
// $ret=$op->output('payforOrderCallback');
$ret=$op->output('payforOrder');
$payResult=$ret['content'];
 ?>
<?php include 'header.html';?>

<div class="zhuce" style="height:420px;">

	<div class="zhuce-1">订单支付成功</div>
			<div class="renzheng-1" style=" margin: 10px 10px 10px 30px" >
	<p class="zhifu-1-txt" align="center" style="margin-left:30px; margin-bottom:20px;">您的易多账户可用余额为：<b class="chengse"> <?php echo ($payResult['amount']-$payResult['freeze_money'])/100 ?>元</b> </p>
	<p class="zhifu-1-txt" align="center" style=" width:600px;margin-left:30px; margin-bottom:20px;">您的易多账户冻结余额为：<b class="chengse"><?php echo $payResult['freeze_money']/100 ?>元</b></p>
			</div>
	<!-- <div class="zhifu-1" style=" height:35px;"><p class="zhifu-2-txt" align="center" style=" width:900px;margin-bottom:20px;">订单支付已超时，订单将关闭。如需订车，请<a href="#" class="anniu-lanse">重新提交订单</a>！</p>
	</div> -->

</div>		
<?php include 'footer.html';?>
</body>
</html>
