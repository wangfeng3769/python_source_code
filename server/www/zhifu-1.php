
<?php include 'header.html';?>
<?php 
//创建订单
require_once (dirname(__FILE__) . '/../hfrm/Frm.php');
require_once (Frm::$ROOT_PATH . 'client/http/web.php');
$op=selectOutput('ArrOutput','MemberClient');
if ($_POST['ref']=='modify'||$_POST['ref']=='cre') {
	$ret=$op->output('createOrder');
}
elseif ($_POST['ref']=='payfor') {
	$ret=$op->output('getOrderDeposit');
}
else
{
	echo '参数错误';
}

require_once (Frm::$ROOT_PATH . 'moneyAccountManager/CashAccountManager.php');
$cm=new CashAccountManager();
$isAfterPay=$cm->isAfterPayUser(UserManager::getLoginUserId());
if ($ret['errno']==2000) {
	$order=$ret['content'];
	if ($order['deposit']==0) {
		// echo "订单修改成功";
		// exit;
	}
	// print_r($order);
}
else
{
	echo $ret['errstr'];exit;
}

?>
<div class="zhuce" style="height:1030px;">
  <div class="zhuce-1">订单支付</div>
  <p class="zhifu-1-txt" align="center" style="margin-left:30px; margin-bottom:20px;">请立即支付订车押金：
    <b class="chengse"><?php echo $order['deposit']/100 ?>元</b> 
  </p>


  <?php if ($isAfterPay) { ?>
  <!-- 后付费 -->
  <form class="submit-pay" action="zhifu-2.php" method="POST">
  <p class="yongtu">
    <b><input name="public" type="radio" value="1" checked />公用 </b>
    <b>费用出处：</b> <input name="money_provenance" type="text" class="zhuce-sr" style="width:225px; margin-top:-5px;"/>
    <b>办事单位：</b> <input name="reason" type="text" class="zhuce-sr" style="width:225px; margin-top:-5px;"/>
  </p>
  <p class="yongtu"><b><input name="public" type="radio" value="0"/>私用</b></p>

  <span class="zhuce-1-1" style="width:600px;margin:-5px 0 10px 30px;">
  	<p class="zhifu-1-txt" style="width:140px;margin-left:30px;">请输入超级密码：</p>
  	<input id="password" name="super_password" type="password" class="zhuce-sr" style="width:225px">
  	<input id="order-id" name="order_id" type="text" class="zhuce-sr" style="width:225px;display:none;" value="<?php echo $order[orderId] ?>">
    <p class="sr-cw1" style="color:#585858;">密码只能使用英文字母或数字，长度6-16字符</p>
  </span>
  <span class="zhuce-1-1" style="margin:10px 0 50px 30px;">
  	<p class="denglu-1-2" style="width:201px">
  	<input name="t" type="text" style="width:225px;display:none;" value='e'/>
  	<input type="submit" class="anniu-zhifu" style="margin:10px 0 0 10px;">
  	</p>
  </span>
  </form>
 	<?php } ?>


  <p style="width:970px; height:25px; float:left; padding:8px 5px 4px 0; background:#fff; border-bottom:1px solid #ccc; margin:10px 0 20px 0;">


  		<b class="xuanze-1" map='zhifu-1'>押金预授权</b><b class="xuanze-2" map='renzheng-1'>充值易多账户</b>
  </p>
  
  <div class="zhifu-1 paymemt" style="height:300px; margin-top:0; border:0;">
  	<form class="submit-pay2" method="POST" action='zhifu-2.php'>
	    <p class="dingdan-xinxi-1" align="center" style=" width:850px; margin-bottom:20px;">
	  	网银支付：
	    </p>
	    <span style=" width:880px;float:left;">
	    	<input type="radio" class="radio" value="e" name="t" checked="">
	    	<img src="img/wyzf.jpg" style="margin-left:30px; margin-top:20px;">
	    </span>
	  	<p class="denglu-1-2" style=" float:left;width:201px">
	  	<input id="order-id" name="order_id" type="text" class="zhuce-sr" style="width:225px;display:none;" value="<?php echo $order[orderId] ?>">
	  	<input name="t" type="text" style="width:225px;display:none;" value='u'/>
	  	<input type="submit" class="anniu-zhifu" style=" padding-top:0;margin:20px 0 0 10px;">
	  	</p>
  	</form>
  </div>
  
	<div class="renzheng-1 paymemt" style=" height:320px;margin: 10px 10px 10px 0;display:none">

	  <span class="zhuce-1-1" style="width:600px;margin-bottom:20px;">

	  <p class="zhifu-1-txt" align="center" style="margin-left:30px; margin-right:10px;float:left;">您的易多账户可用余额为：<b class="chengse"><?php echo $order['amount']/100 ?>元</b></p><button class="anniu-9" style=" width:148px; height:43px; border:0;">易多账户充值</button>
	  </span>

	  <form class="submit-pay" action="zhifu-2.php" method="POST">
	  <span class="zhuce-1-1" style="width:600px;margin-bottom:20px;">
	  	<p class="zhifu-1-txt" style="width:140px;margin-left:30px;">请输入超级密码：</p>
	  	<input id="password" name="super_password" type="password" class="zhuce-sr" style="width:225px">
	  	<input id="order-id" name="order_id" type="text" class="zhuce-sr" style="width:225px;display:none;" value="<?php echo $order[orderId] ?>">
	    <input name="t" type="text" style="width:225px;display:none;" value='e'/>
	    <p class="sr-cw1" style="color:#585858;">密码只能使用英文字母或数字，长度6-16字符</p>
	  </span>

	  	
	  <span class="zhuce-1-1">
	  	<p class="denglu-1-2" style="width:201px">
	  	<input type="submit" class="anniu-zhifu" style=" padding-top:0;margin:20px 0 0 30px;">
	  	</p>
	  </span>
	  </form>
  </div>
		
  
  
</div>
<script type="text/javascript">
$('.charge').click(function(){
  window.location.href="charge.php";
});	
$('.submit-pay').submit(function(){
	if ($('#password').val()=='')
	{
		alert('请输入超级密码');
		return false;
	};
	return true;

});
$('.public-click').click(function(){
	$('.public-opt').show();
});
$('.pravite-click').click(function(){
	$('.public-opt').hide();
});
// hash = window.location.hash;
// if (hash.length>0)
// {
//     $(hash.replace('#','')).click();
// };
$('.xuanze-1,.xuanze-2').click(function(){
	var map=$(this).attr('map');
	$('.xuanze-1,.xuanze-2').css('border-bottom','5px solid #ccc');
	$(this).css('border-bottom','5px solid #00B4E6');
	$('.paymemt').hide();
	$('.'+map).show();
	
});
</script>
<?php include 'footer.html';?>

