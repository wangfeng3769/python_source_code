<?php 
require_once (dirname(__FILE__) . '/../hfrm/Frm.php');
require_once (Frm::$ROOT_PATH . 'client/http/web.php');
$op=selectOutput('ArrOutput','MemberClient');
$ret=$op->output('getConsumeDetail');
$cashAccountLog=$ret['content'];

?>


<?php include 'header.html';?>

<table>
<tr>
<th>金额</th>
<th>操作类型</th>
<th>时间</th>
<th>余额</th>
<th>备注</th>
</tr>
  <?php foreach ($cashAccountLog as $v) {?>
<tr>  
    <td text-align='center'><?php echo $v['money']/100 ?>元</td>
    <td text-align='center'><?php echo $v['use_type'] ?></td>
    <td text-align='center'><?php echo $v['time']; ?></td>
    <td text-align='center'><?php echo $v['balance_amount']/100 ?>元</td>
    <td text-align='center'><?php echo $v['remark'] ?></td>
</tr>
  <?php  } ?>
</table>

<script type="text/javascript">
$('.charge').click(function(){
  window.location.href="charge.php";
});

</script>
<?php include 'footer.html';?>
<?php exit; ?>