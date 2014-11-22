<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>车辆监控</title>
<script type="text/javascript" src="/cp/admin/js/frm.js"></script>
<script type="text/javascript" src="/js/jquery-1.9.0.js"></script>
<script type="text/javascript">

	var ajax = new FrmAjax();
	function carOp(id, op) 
	{
		var opName = '';
		switch( op ) {
			case 'openDoor':
				opName = '“开门”';
				break;
			case 'closeDoor':
				opName = '“关门”';
				break;
			case 'ring':
				opName = '“鸣笛”';
				break;
			case 'reset':
				opName = '“复位”';
				break;
		}
		if (confirm("您确定要" + opName +"ID为："+id+ "的车吗？")) {
			ajax.get('/client/http/monitor.php?item=' + op + '&id=' + id, function() {
				alert('成功。');
			});
		} else {

		}

	}

	function showMap(lng, lat) 
	{
		window.location = '/client/http/monitor.php?item=showCarOnMap&lng=' + lng + '&lat=' + lat;
	}

	function showTrack(number)
	{
		window.location = '/displayLine.php?CarNumber='+number;

	}

	function custom(id)
	{
        var op=prompt("请输入命令：");
        var opName = '';
        switch( op ) {
			case 'openDoor':
				opName = '“开门”';
				break;
			case 'closeDoor':
				opName = '“关门”';
				break;
			case 'ring':
				opName = '“鸣笛”';
				break;
			case 'reset':
				opName = '“复位”';
				break;
			case null:
			    opName='cancel';
			    break;
			case "":
			    opName='noInput';
			    break;
		    default:
		        opName = 'error';
		}
		if(opName!=='noInput'&&opName!=='error'&&opName!=='cancel')
		{
			if (confirm("您确定要" + opName +"ID为："+id+ "的车吗？"))
			{
				ajax.get('/client/http/monitor.php?item=' + op + '&id=' + id, function() 
				{
					alert(opName+'成功。');
				});
			}	
		}
		else if(opName=='error')
		{
            alert('输入命令有误！');
        }
        else if(opName=='noInput')
		{
            alert('请输入命令！');
        }
	}

$(document).ready(function()
{
	$("tr").mouseover(function()
	{
  		$(this).css("background-color","#888888");
	});
    
    $("tr").mouseout(function()
	{
  		$(this).css("background-color","white");
	});
});

</script>
</head>

<body>
	<table width="100%" border="1" style="text-align: center">
		<tr style="font-weight: bold">
			<td width="130pd">车辆ID(车牌号)</td>
			<td width="70pd">昵称</td>
			<td width="80pd">后台管理状态</td>
			<td width="110pd">最后心跳时间</td>
			<td width="110pd">最后停车时间</td>
			<td width="120pd">订单使用状态</td>

			<!-- delete by matao 2013.6.25 start -->
			<!-- <td width="70pd">VSS</td> -->
			<!-- delete by matao 2013.6.25 end-->

			<td width="70pd">车钥匙</td>
			<td width="70pd">油卡</td>
			<td width="70pd">车门</td>
			<td width="70pd">油量</td>
			<td width="70pd">电量</td>
			<td width="70pd">硬件版本</td>
			<td width="auto">操作</td>

			<!--add by matao 2013.6.25 start-->
			<td width="90pd">复位</td>
			<!--add by matao 2013.6.25 end-->
		</tr>
		<?php foreach( $carList as $row ){?>
		<tr>
			<td><?php echo $row['id'] . '(' . $row['number'] . ')'; ?></td>
			<td><?php echo $row['name']; ?></td>
			<td><?php echo $row['admin_stat']; ?></td>
			<td <?php
			if ($dShowArr[$row['id']]['offline'])
				echo 'bgcolor="red"';
			?>><?php echo $dShowArr[$row['id']]['last_heartbeat_time']; ?></td>
			<td <?php
			if ($dShowArr[$row['id']]['NoElectric'])
				echo 'bgcolor="red"';
			?>><?php echo $dShowArr[$row['id']]['VssRecTime']; ?></td>
			<td><?php echo $dShowArr[$row['id']]['order']; ?></td>

            <!-- delete by matao 2013.6.25 start -->
			<!-- <td><?php echo $deviceList[$row['id']] -> vss_counter; ?></td> -->
            <!--delete by matao 2013.6.25 end-->

			<td><?php echo $dShowArr[$row['id']]['key']; ?></td>
			<td><?php echo $dShowArr[$row['id']]['oil_card']; ?></td>
			<td><?php echo $dShowArr[$row['id']]['door']; ?></td>
			<td><?php echo $dShowArr[$row['id']]['oil']; ?></td>
			<td><?php echo $dShowArr[$row['id']]['voltage']; ?></td>
			<td><?php echo $deviceList[$row['id']] -> software_version; ?></td>
			<td><button
					onclick="carOp(<?php echo $deviceList[$row['id']] -> id; ?> , 'openDoor')" style="width:70px">开门</button>
				<button
					onclick="carOp(<?php echo $deviceList[$row['id']]->id?> , 'closeDoor')" style="width:70px">关门</button>
				<button
					onclick="carOp(<?php echo $deviceList[$row['id']]->id?> , 'ring')" style="width:70px">鸣笛</button>

                <!--delete by matao 2013.6.25 start-->
				<!-- <button
					onclick="carOp(<?php echo $deviceList[$row['id']]->id?> , 'reset')" style="width:70px">复位</button> -->
                <!--delete by matao 2013.6.25 end-->

				<button
					onclick="showMap('<?php echo $deviceList[$row['id']] -> longitude; ?>' , '<?php echo $deviceList[$row['id']] -> latitude; ?>')" style="width:70px">位置</button>
				
				<!--add by matao 2013.6.25 start-->
				<button
					onclick="showTrack('<?php echo $row['number']?>')" style="width:70px">轨迹</button>
				<button
					onclick="custom(<?php echo $deviceList[$row['id']]->id?>)" style="width:70px">自定义</button>
				<!--add by matao 2013.6.25 end-->	
			</td>

			<!--add by matao 2013.6.25 start-->
			<td>
				<button
					onclick="carOp(<?php echo $deviceList[$row['id']]->id?> , 'reset')" style="width:70px">复位</button>
			</td>
			<!--add by matao 2013.6.25 end-->
		</tr>
		<?php } ?>
	</table>
</body>
</html>
