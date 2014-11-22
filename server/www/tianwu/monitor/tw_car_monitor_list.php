<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>车辆监控</title>
<!--<script type="text/javascript" src="/cp/admin/js/frm.js"></script>-->
</head>
<script type="text/javascript">

function showMap(lng, lat) 
{
	window.location = '/tianwu/monitor/tw_monitor.php?item=showCarOnMap&lng=' + lng + '&lat=' + lat;
}
</script>
<?php 
$lanStatus=array('故障', '空闲状态', '使用状态', '使用状态', '使用状态','使用状态', '空闲状态', '空闲状态');
 ?>
<body>
	<br><br>
	<table width="900px" border="5" cellspacing="0" align="center" bordercolor="#888" style="text-align:center;border-collapse:collapse;">
		<caption><h2>监控中心</h2></caption>
		<tr style="font-weight: bold;background:rgb(134,202,182);height:40px;line-height:40px;">
			<td width="200px">车辆ID(车牌号)</td>
			<td width="150px">用户名</td>
			<td width="150px">联系方式</td>
			<td width="200px">车辆状态</td>
			<td width="150px">操作</td>
		</tr>
		<?php foreach( $carOrderArr as $row ){
			if (empty($row['machine_stat'])) {
				$row['machine_stat']=0;
			}

		?>
		<tr>
			<td><?php echo $row['id'] . '(' . $row['number'] . ')'; ?></td>

			<td><?php echo $row['order']['true_name']; ?></td>

			<td><?php echo $row['order']['phone']; ?></td>

			<td><?php echo $lanStatus[$row['machine_stat']]; ?></td>
			
		    <td>
				<button
					onclick="showMap('<?php echo $deviceList[$row['id']] -> longitude; ?>' , '<?php echo $deviceList[$row['id']] -> latitude; ?>')" style="width:70px">位置
				</button>
			</td>
		
     
		</tr>
		<?php }
		 ?>
	</table>
</body>
</html>
