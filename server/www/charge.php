<?php include 'header.html';?>
  <?php 
require_once (dirname(__FILE__) . '/../hfrm/Frm.php');
require_once (Frm::$ROOT_PATH . 'client/http/web.php');
$op=selectOutput('ArrOutput','MemberClient');

$ret=$op->output('getConsumeDetail');
$cashAccountLog=$ret['content'];

   ?>
<table>
	<form action="../client/http/member.php">
	<tr>
		金额:<input id="" name='amount' type="text" style="width:180px;"/>
		<input id="" name='t' value="web" type="text" style="width:180px;display:none;"/>
		<input id="" name='item' value="cashAccountChargeReq" type="text" style="width:180px;display:none;"/>
		<input type='submit' class="" style="margin:20px 0 0 10px;"/>
	</tr>
	</form>
</table>
<?php include 'footer.html';?>