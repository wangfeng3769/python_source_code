<script type="text/javascript" language="javascript" src="calendar.js"></script>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<body>
		<table width=1000 align=left border=2>
			<tr>
				<td width=500>
					<form name="order" action="orderAdd.php" method="post">
						<table cellspacing=0 cellPadding=0 border=1 borderColorDark=#000000>
							<tr bgcolor=#799ED2>增加订单</tr>
							<tr bgcolor=#79FFF2>
								<td colspan=2>订单时间信息
							</tr>
							<tr>
								<td>开始时间<input name="ord_timeStart" onfocus="setday(this)"/></td>
								<td>结束时间<input name="ord_timeEnd" onfocus="setday(this)"/></td>
							</tr>
							<tr bgcolor=#79FFF2>
								<td colspan=2>折扣信息</td>
							</tr>
							<tr>
								<td>群组折扣<input name="group_discount" value="80"/></td>
								<td>类别折扣<input name="class_discount" value="90"/></td>
							</tr>
							<tr bgcolor=#79FFF2>
								<td colspan=2>公里信息</td>
							</tr>
							<tr>
								<td>公里价格<input name="unit_price" value="1.0"/></td>
								<td>预估数量<input name="excepted_count" value="150"/></td>
							</tr>
							<tr bgcolor=#79FFF2>
								<td colspan=2>计费时间分割点</td>
							</tr>
							<tr>
								<td colspan=2>分割时间<input name="separate_time" value="12:00"/></td>
							</tr>
							<tr bgcolor=#79FFF2>
								<td colspan=2>封顶价格</td>
							</tr>
							<tr>
								<td><input name="one_time" value="<?PHP echo date('Y-m-d', time());?>"/></td>
								<td><input name="one_price" value="210"/></td>
							</tr>
							<tr>
								<td><input name="two_time" value="<?PHP echo date('Y-m-d', time() + 3600*24);?>"/></td>
								<td><input name="two_price" value="210"/></td>
							</tr>
							<tr>
								<td><input name="three_time" value="<?PHP echo date('Y-m-d', time() + 3600*48);?>"/></td>
								<td><input name="three_price" value="240"/></td>
							</tr>
							<tr>
								<td><input name="four_time" value="<?PHP echo date('Y-m-d', time() + 3600*72);?>"/></td>
								<td><input name="four_price" value="240"/></td>
							</tr>
							<tr>
								<td><input name="five_time" value="<?PHP echo date('Y-m-d', time() + 3600*96);?>"/></td>
								<td><input name="five_price" value="240"/></td>
							</tr>
							<tr>
								<td><input name="six_time" value="<?PHP echo date('Y-m-d', time() + 3600*120);?>"/></td>
								<td><input name="six_price" value="240"/></td>
							</tr>
							<tr>
								<td><input name="seven_time" value="<?PHP echo date('Y-m-d', time() + 3600*144);?>"/></td>
								<td><input name="seven_price" value="240"/></td>
							</tr>
							<tr align=right>
								<td colspan=2><input type="submit" name="submit" value="Submit"></td>
							</tr>
						</table>
					</form>
				</td>
				<td valign="top">
					<form name="order" action="orderModify.php" method="post">
						<table cellspacing=0 cellPadding=0 border=1 borderColorDark=#000000>
							<tr bgcolor=#799ED2>计算订单违约费</tr>
							<tr bgcolor=#79FFF2>
								<td colspan=2>原始订单时间信息
							</tr>
							<tr>
								<td>开始时间<input name="ord_timeStart" onfocus="setday(this)"/></td>
								<td>结束时间<input name="ord_timeEnd" onfocus="setday(this)"/></td>
							</tr>
							<tr bgcolor=#79FFF2>
								<td colspan=2>新订单时间信息</td>
							</tr>
							<tr>
								<td>开始时间<input name="ord_timeStart_new" onfocus="setday(this)"/></td>
								<td>结束时间<input name="ord_timeEnd_new" onfocus="setday(this)"/></td>
							</tr>
							<tr bgcolor=#79FFF2>
								<td colspan=2>计费时间分割点</td>
							</tr>
							<tr>
								<td colspan=2>分割时间<input name="separate_time" value="12:00"/></td>
							</tr>
							<tr bgcolor=#79FFF2>
								<td colspan=2>每日时租金</td>
							</tr>
							<tr>
								<td><input name="one_time" value="<?PHP echo date('Y-m-d', time());?>"/></td>
								<td><input name="one_price" value="10"/></td>
							</tr>
							<tr>
								<td><input name="two_time" value="<?PHP echo date('Y-m-d', time() + 3600*24);?>"/></td>
								<td><input name="two_price" value="10"/></td>
							</tr>
							<tr>
								<td><input name="three_time" value="<?PHP echo date('Y-m-d', time() + 3600*48);?>"/></td>
								<td><input name="three_price" value="15"/></td>
							</tr>
							<tr>
								<td><input name="four_time" value="<?PHP echo date('Y-m-d', time() + 3600*72);?>"/></td>
								<td><input name="four_price" value="15"/></td>
							</tr>
							<tr>
								<td><input name="five_time" value="<?PHP echo date('Y-m-d', time() + 3600*96);?>"/></td>
								<td><input name="five_price" value="15"/></td>
							</tr>
							<tr>
								<td><input name="six_time" value="<?PHP echo date('Y-m-d', time() + 3600*120);?>"/></td>
								<td><input name="six_price" value="15"/></td>
							</tr>
							<tr>
								<td><input name="seven_time" value="<?PHP echo date('Y-m-d', time() + 3600*144);?>"/></td>
								<td><input name="seven_price" value="15"/></td>
							</tr>
							<tr align=right>
								<td colspan=2><input type="submit" name="submit" value="Submit"></td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
			<tr>
				<td>
					<form name="order" action="orderClose.php" method="post">
						<table cellspacing=0 cellPadding=0 border=1 borderColorDark=#000000>
							<tr bgcolor=#799ED2>订单结算</tr>
							<tr bgcolor=#79FFF2>
								<td colspan=2>订单时间</tr>
							<tr>
								<td>开始时间<input name="ord_timeStart" onfocus="setday(this)"/></td>
								<td>结束时间<input name="ord_timeEnd" onfocus="setday(this)"/></td>
							</tr>
							<tr bgcolor=#79FFF2>
								<td colspan=2>还车时间</td>
							</tr>
							<tr>
								<td colspan=2><input name="return_car"  onfocus="setday(this)"/>
							</tr>
							<tr bgcolor=#79FFF2>
								<td colspan=2>行驶信息</td>
							</tr>
							<tr>
								<td>公里价格<input name="unit_price" value="1.0"/></td>
								<td>实际数量<input name="excepted_count" value="10"/></td>
							</tr>
							<tr bgcolor=#79FFF2>
								<td colspan=2>计费时间分割点</td>
							</tr>
							<tr>
								<td colspan=2>分割时间<input name="separate_time" value="12:00"/></td>
							</tr>
							<tr bgcolor=#79FFF2>
								<td colspan=2>日封顶价格</td>
							</tr>
							<tr>
								<td><input name="one_time_top" value="<?PHP echo date('Y-m-d', time());?>"/></td>
								<td><input name="one_price_top" value="210"/></td>
							</tr>
							<tr>
								<td><input name="two_time_top" value="<?PHP echo date('Y-m-d', time() + 3600*24);?>"/></td>
								<td><input name="two_price_top" value="210"/></td>
							</tr>
							<tr>
								<td><input name="three_time_top" value="<?PHP echo date('Y-m-d', time() + 3600*48);?>"/></td>
								<td><input name="three_price_top" value="240"/></td>
							</tr>
							<tr>
								<td><input name="four_time_top" value="<?PHP echo date('Y-m-d', time() + 3600*72);?>"/></td>
								<td><input name="four_price_top" value="240"/></td>
							</tr>
							<tr>
								<td><input name="five_time_top" value="<?PHP echo date('Y-m-d', time() + 3600*96);?>"/></td>
								<td><input name="five_price_top" value="240"/></td>
							</tr>
							<tr>
								<td><input name="six_time_top" value="<?PHP echo date('Y-m-d', time() + 3600*120);?>"/></td>
								<td><input name="six_price_top" value="240"/></td>
							</tr>
							<tr>
								<td><input name="seven_time_top" value="<?PHP echo date('Y-m-d', time() + 3600*144);?>"/></td>
								<td><input name="seven_price_top" value="240"/></td>
							</tr>
							<tr bgcolor=#79FFF2>
								<td colspan=2>时段用车价格</td>
							</tr>
							<tr>
								<td><input name="one_time_hour" value="<?PHP echo date('Y-m-d', time());?>"/></td>
								<td><input name="one_price_hour" value="10"/></td>
							</tr>
							<tr>
								<td><input name="two_time_hour" value="<?PHP echo date('Y-m-d', time() + 3600*24);?>"/></td>
								<td><input name="two_price_hour" value="10"/></td>
							</tr>
							<tr>
								<td><input name="three_time_hour" value="<?PHP echo date('Y-m-d', time() + 3600*48);?>"/></td>
								<td><input name="three_price_hour" value="20"/></td>
							</tr>
							<tr>
								<td><input name="four_time_hour" value="<?PHP echo date('Y-m-d', time() + 3600*72);?>"/></td>
								<td><input name="four_price_hour" value="20"/></td>
							</tr>
							<tr>
								<td><input name="five_time_hour" value="<?PHP echo date('Y-m-d', time() + 3600*96);?>"/></td>
								<td><input name="five_price_hour" value="20"/></td>
							</tr>
							<tr>
								<td><input name="six_time_hour" value="<?PHP echo date('Y-m-d', time() + 3600*120);?>"/></td>
								<td><input name="six_price_hour" value="20"/></td>
							</tr>
							<tr>
								<td><input name="seven_time_hour" value="<?PHP echo date('Y-m-d', time() + 3600*144);?>"/></td>
								<td><input name="seven_price_hour" value="20"/></td>
							</tr>
							<tr bgcolor=#79FFF2>
								<td colspan=2>时段封顶价格</td>
							</tr>
							<tr>
								<td><input name="one_time_period" size=10 value="<?PHP echo date('Y-m-d', time());?>"><input name="one_time_start" size=5 value="9:00"><input name="one_time_end" size=5 value="15:00"></td>
								<td><input name="one_price_period" value="10"/></td>
							</tr>
							<tr>
								<td><input name="two_time_period" size=10 value="<?PHP echo date('Y-m-d', time() + 3600*24);?>"><input name="two_time_start" size=5 value="9:00"><input name="two_time_end" size=5 value="15:00"></td>
								<td><input name="two_price_period" value="10"/></td>
							</tr>
							<tr>
								<td><input name="three_time_period" size=10 value="<?PHP echo date('Y-m-d', time() + 3600*48);?>"><input name="three_time_start" size=5 value="9:00"><input name="three_time_end" size=5 value="15:00"></td>
								<td><input name="three_price_period" value="20"/></td>
							</tr>
							<tr>
								<td><input name="four_time_period" size=10 value="<?PHP echo date('Y-m-d', time() + 3600*72);?>"><input name="four_time_start" size=5 value="9:00"><input name="four_time_end" size=5 value="15:00"></td>
								<td><input name="four_price_period" value="20"/></td>
							</tr>
							<tr>
								<td><input name="five_time_period" size=10 value="<?PHP echo date('Y-m-d', time() + 3600*96);?>"><input name="five_time_start" size=5 value="9:00"><input name="five_time_end" size=5 value="15:00"></td>
								<td><input name="five_price_period" value="20"/></td>
							</tr>
							<tr>
								<td><input name="six_time_period" size=10 value="<?PHP echo date('Y-m-d', time() + 3600*120);?>"><input name="six_time_start" size=5 value="9:00"><input name="six_time_end" size=5 value="15:00"></td>
								<td><input name="six_price_period" value="20"/></td>
							</tr>
							<tr>
								<td><input name="seven_time_period" size=10 value="<?PHP echo date('Y-m-d', time() + 3600*144);?>"><input name="seven_time_start" size=5 value="9:00"><input name="seven_time_end" size=5 value="15:00"></td>
								<td><input name="seven_price_period" value="20"/></td>
							</tr>
							<tr align=right>
								<td colspan=2><input type="submit" name="submit" value="Submit"></td>
							</tr>
						</table>
					</form>
				</td>
				<td>
				</td>
			</tr>
		</table>
	</body>
</html>
