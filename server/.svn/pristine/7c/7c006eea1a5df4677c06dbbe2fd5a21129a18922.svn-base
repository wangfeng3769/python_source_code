<?php 
require_once (dirname(__FILE__) . '/../hfrm/Frm.php');
require_once (Frm::$ROOT_PATH . 'client/http/web.php');
// print_r($_REQUEST);
$op=selectOutput('ArrOutput','MemberClient');
//$className, $methodName,$argv
$ret=$op->output('calculateDeposit');
$car_id=$_GET['car_id'];
$money=$ret['content'];


 ?>




<div class="dingdan-xinxi">
	<p class="dingdan-xinxi-1">已经冻结押金：<b class="chengse"> <?php echo $money['frozen']/100;?></b>元</p>
	<p class="dingdan-xinxi-1">本次使用押金：<b class="chengse"><?php echo ($money['use_deposit']+$money['time_deposit'])/100 ?></b>元</p>
	<p class="dingdan-xinxi-1">本次违章押金：<b class="chengse"><?php echo $money['violate_deposit']/100; ?></b>元</p>
</div>
<?php if (false) { ?>
<div class="dingdan-xinxi">
	<p class="dingdan-xinxi-1">使用我的特权奖章：</p>

	<div class="jiangzhang-nr">
		<span class="jz-nr1">
			<img src="img/chutiyan.png"/><input name="" class="fuxuan" type="checkbox" value="" />免费初体验
		</span>
		<span class="jz-nr1">
			<img src="img/shengri.png"/><input name="" class="fuxuan" type="checkbox" value="" />生日我最大
		</span>
		<span class="jz-nr1">
			<img src="img/daren.png"/><input name="" class="fuxuan" type="checkbox" value="" />易多达人
		</span>
	</div>
	<p class="dingdan-xinxi-1"></p>
	<p class="dingdan-xinxi-1">您使用了生日我最大奖章，使用押金减半。</p>
</div>
<?php }?>
<div class="dingdan-xinxi" style="border:0;">
	<p class="dingdan-xinxi-1" style="font-weight:bold;">您应该支付的订车押金：<b class="chengse"> <?php echo $money['total_deposit']/100 ?> </b>元</p>
	<!-- <input id='car-id' name='car_id' value='1' type="text" style='display:none;'/> -->
	<!-- <a href="javascript:void(0);" class="anniu-5 order-submit" style="margin:20px 0 0 10px;">提交订单并支付</a> -->
</div>	



