
<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>




<form method="post" action="" name="listForm">

<div class="list-div" id="listDiv">
<?php endif; ?>

<table width="100%" cellspacing="1"  cellpadding="2" id="list-table">
  <tr>
  	<th>订单号</th>
	<th>图片</th>
    <th>昵称</th>
    <th>车牌号</th>
    <th>用户名</th>
    <th>手机号</th>
    <th>操作</th>
  </tr>
  <?php $_from = $this->_var['car_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'car');if (count($_from)):
    foreach ($_from AS $this->_var['car']):
?>
  <tr align="center"  id="<?php echo $this->_var['car']['level']; ?>_<?php echo $this->_var['car']['id']; ?>">
  	<td ><?php echo $this->_var['car']['order_no']; ?></td>
	<td width="10%" ><img src="<?php echo $this->_var['car']['icon']; ?>" width="150" height="120"/></td>
    <td align="left" class="first-cell">
      <span><?php echo $this->_var['car']['name']; ?></span>
    </td>
    <td width="2%"><?php echo $this->_var['car']['number']; ?></td>
    <td width="10%"><?php echo $this->_var['car']['true_name']; ?></td>
    <td width="10%"><?php echo $this->_var['car']['phone']; ?></td>
    <td width="24%" align="center">
    <a href="car_bug.php?act=fix_close&car_id=<?php echo $this->_var['car']['car_id']; ?>">关门</a> 
    </td>
  </tr>
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</table>
<?php if ($this->_var['full_page']): ?>
</div>
</form>


<script language="JavaScript">

</script>


<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>