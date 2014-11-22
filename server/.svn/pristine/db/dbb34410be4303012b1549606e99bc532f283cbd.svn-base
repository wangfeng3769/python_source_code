<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>无标题文档</title>
	</head>

	<body>
		<table width="100%" border="1">
			<tr>
				<td width="150pd">车辆ID(车牌号)</td>
				<td><?php echo $row['id'] . '(' . $row['number'] . ')'; ?></td>
				<td width="150pd">昵称</td>
				<td><?php echo $row['name']; ?></td>
			</tr>
			<tr>
				<td>后台管理状态</td>
				<td><?php echo $row['admin_stat']; ?></td>
				<td>最后心跳时间</td>
				<td <?php
				if ($dShowArr[$row['id']]['offline'])
					echo 'bgcolor="red"';
			?>><?php echo $dShowArr[$row['id']]['last_heartbeat_time']; ?></td>
			</tr>
			<tr>
				<td>最后停车时间</td>
				<td <?php
				if ($dShowArr[$row['id']]['NoElectric'])
					echo 'bgcolor="red"';
			?>><?php echo $dShowArr[$row['id']]['VssRecTime']; ?></td>
				<td>订单使用状态</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>车钥匙</td>
				<td>&nbsp;</td>
				<td>油卡</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>车门</td>
				<td>&nbsp;</td>
				<td>油量</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>电量</td>
				<td>&nbsp;</td>
				<td>硬件版本</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4" align="center">
				<button
				onclick="carOp(<?php echo $deviceList[$row['id']] -> id; ?> , 'openDoor')" style="width:70px">
					开门
				</button>
				<button
				onclick="carOp(<?php echo $deviceList[$row['id']]->id?> , 'closeDoor')" style="width:70px">
					关门
				</button>
				<button
				onclick="carOp(<?php echo $deviceList[$row['id']]->id?> , 'ring')" style="width:70px">
					鸣笛
				</button>
				<button
				onclick="carOp(<?php echo $deviceList[$row['id']]->id?> , 'reset')" style="width:70px">
					复位
				</button>
				<button
				onclick="showMap('<?php echo $deviceList[$row['id']] -> longitude; ?>' , '<?php echo $deviceList[$row['id']] -> latitude; ?>')" style="width:70px">
					位置
				</button></td>
			</tr>
		</table>
	</body>
</html>
