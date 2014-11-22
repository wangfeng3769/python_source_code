<?PHP

include_once("userMonitoring2.php");
header("Content-Type:text/html;charset=utf-8"); 
 
/*--------------------------------生成表单及数据------------------------------------*/

@mysql_connect("localhost","root","")//创建连接
or die('Could not connect: ' . mysql_error());
@mysql_select_db("edo")//选择数据库
or die('DataBase Can not used:'. mysql_error());

@mysql_query("set names UTF8");
@date_default_timezone_set("Asia/Shanghai");
$today=time();
$year=date("Y");
$month=date("m");
$day=date("d");

$thisDayBegin=strtotime($year."-".$month."-".$day);
$lastDayBegin=strtotime($year."-".$month."-".($day-1));
$thisWeekBegin=strtotime('last Monday');
//echo date("Y-m-d H:i:s",$thisWeekBegin);echo "<br>";echo "<br>";echo "<br>";
$thisWeekBegin=(($thisDayBegin-$thisWeekBegin)>=604800)?$thisDayBegin:$thisWeekBegin;
//echo date("Y-m-d H:i:s",$thisWeekBegin);echo "<br>";echo "<br>";echo "<br>";
$lastWeekBegin=$thisWeekBegin-604800;
$thisMonthBegin=mktime(0,0,0,$month,01,$year);
$lastMonthBegin=mktime(0,0,0,$month-1,01,$year);

$numOfTerm=22;/*查询表项数量*/
$num=0;/*站点数量*/
$NumArray;/*所有查询结果容器*/
$baseArray;/*所有站点列表*/
$Array;/*临时数组*/
$totalofCol=0;/*列数据之合*/

/*1装入项目*/
$stations=@mysql_query("SELECT name FROM edo_cp_station");
while($row = mysql_fetch_array($stations,MYSQL_NUM))
{
  $num++;
 // echo $row[0]."<br>";
  $Array[]=($row[0]);
  $baseArray[]=($row[0]);
}
$Array[]="合计";/*所有站点的各项指标的和*/
$NumArray[]=$Array;


/*2装入注册*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findRegisterUserNumbyStation($baseArray[$i]);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*3装入认证*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findIdentyUserNumbyStation($baseArray[$i]);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*4装入活跃*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findActiveUserNumbyStation($baseArray[$i]);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*5装入不活跃*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findunActiveUserNumbyStation($baseArray[$i]);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*6装入沉默*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findSilentUserNumbyStation($baseArray[$i]);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*7装入冻结*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findFreezeUserNumbyStation($baseArray[$i]);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*8装入本日注册*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findRegisterUserNumbyTimebyStation($baseArray[$i],$thisDayBegin,$today);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*9装入本日认证*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findIdentyUserNumbyTimebyStation($baseArray[$i],$thisDayBegin,$today);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*10装入昨日注册*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findRegisterUserNumbyTimebyStation($baseArray[$i],$lastDayBegin,$thisDayBegin);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*11装入昨日认证*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findIdentyUserNumbyTimebyStation($baseArray[$i],$lastDayBegin,$thisDayBegin);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*12装入本周注册*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findRegisterUserNumbyTimebyStation($baseArray[$i],$thisWeekBegin,$today);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*13装入本周认证*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findIdentyUserNumbyTimebyStation($baseArray[$i],$thisWeekBegin,$today);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*14装入上周注册*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findRegisterUserNumbyTimebyStation($baseArray[$i],$lastWeekBegin,$thisWeekBegin);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*15装入上周认证*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findIdentyUserNumbyTimebyStation($baseArray[$i],$lastWeekBegin,$thisWeekBegin);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*16装入本月注册*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findRegisterUserNumbyTimebyStation($baseArray[$i],$thisMonthBegin,$today);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*17装入本月认证*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findIdentyUserNumbyTimebyStation($baseArray[$i],$thisMonthBegin,$today);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*18装入上月注册*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findRegisterUserNumbyTimebyStation($baseArray[$i],$lastMonthBegin,$thisMonthBegin);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*19装入上月认证*/
for($i=0;$i<$num;$i++)
{
  $Array[$i]=findIdentyUserNumbyTimebyStation($baseArray[$i],$lastMonthBegin,$thisMonthBegin);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol;$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*20装入车平均注册关注数*/
for($i=0;$i<$num;$i++)
{
  $numOfCar=findCarNumbyStation($baseArray[$i]);
  $numOfUser=findRegisterUserNumbyStation($baseArray[$i]);
  if($numOfCar==0 || $numOfUser==0)$Array[$i]=0;
  else $Array[$i]=number_format((double)($numOfUser/$numOfCar),2);
    $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*21装入车平均认证关注数*/
for($i=0;$i<$num;$i++)
{
  $numOfCar=findCarNumbyStation($baseArray[$i]);
  $numOfUser=findIdentyUserNumbyStation($baseArray[$i]);
  if($numOfCar==0 || $numOfUser==0)$Array[$i]=0;
  else $Array[$i]=number_format((double)($numOfUser/$numOfCar),2);
  $totalofCol+=$Array[$i];
}
$Array[$num]=$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*22装入某一站点一段时间内的车平均使用时长(小时)*/
for($i=0;$i<$num;$i++) 
{
  $time=calculateCarTotalUseTimebyStation($baseArray[$i],$thisWeekBegin,$today);
  $numOfCar=findCarNumbyStation($baseArray[$i]);
  if($numOfCar==0 || $time==0)$Array[$i]=0;
  else $Array[$i]=number_format((double)(($time/$numOfCar)/3600),2);/*$time/$numOfCar/3600把秒转化为小时*/
  $totalofCol+=$Array[$i];
  //echo $Array[$i];echo"<br>";
}
$Array[$num]=$totalofCol=0;/*列“合计”*/
$NumArray[]=$Array;


/*--------------------------------输出表单-------------------------------------*/

//echo "<table style='border:0px solid orange;padding:0'>";   
//$table="<table style='border:3px solid rgb(255,157,97);color:rgb(80,80,80);padding:0;'>
//<tr style='background:rgb(255,157,97);padding:0;margin:0;'>

$table="<table border='1' cellspacing='0' >
<tr style='background:rgb(255,157,97);'>
<th>站点</th>
<th>累计注册</th>
<th>累计认证</th>
<th>活跃用户</th>
<th>不活跃用户</th>
<th>沉默用户</th>
<th>停用用户</th>
<th>本日注册</th>
<th>本日认证</th>
<th>昨日注册</th>
<th>昨日认证</th>
<th>本周注册</th>
<th>本周认证</th>
<th>上周注册</th>
<th>上周认证</th>
<th>本月注册</th>
<th>本月认证</th>
<th>上月注册</th>
<th>上月认证</th>
<th>车平均注册关注数</th>
<th>车平均认证关注数</th>
<th>车周平均时长（h）</th>
</tr>";
//echo  $tableHr;

/*  <=$num代表又加了一列“合计”*/
  for($k=0;$k<=$num;$k++)
  {
    for($t=0;$t<$numOfTerm;$t++)
    {
      if("".$NumArray[$t][$k].""=="0")$NumArray[$t][$k]=" ";
      if($t==0)
      {
        if(($k%2)==0)
          $table.=("<tr style='background:rgb(134,202,182);'>"."<td>".$NumArray[$t][$k]."</td>");
        else
          $table.=("<tr>"."<td>".$NumArray[$t][$k]."</td>"); 
      }
      else if($t==$numOfTerm)
      {
        $table.=("<td>".$NumArray[$t][$k]."</td>"."</tr>");
      }
      else
      {
        $table.=("<td>".$NumArray[$t][$k]."</td>");
      }
    }
  }
$table.="</table>";
echo "<title>会员监控中心</title><h4 align='center'>会员监控中心</h4>";
echo $table;
echo "<br>";echo "<br>";

/*--------------------------------------第二张表-------------------------------------------*/
$totalArray= array();


/*2装入注册*/
  $totalArray[]=findRegisterUserNum();


/*3装入认证*/
 $totalArray[]=findIdentyUserNum();


/*4装入活跃*/
  $totalArray[]=findActiveUserNum();



/*5装入不活跃*/
  $totalArray[]=findunActiveUserNum();


/*6装入沉默*/
 $totalArray[]=findSilentUserNum();



 /*7装入冻结*/
  $totalArray[]=findFreezeUserNum();



 /*8装入本日注册*/
  $totalArray[]=findRegisterUserNumbyTime($thisDayBegin,$today);


/*9装入本日认证*/
  $totalArray[]=findIdentyUserNumbyTime($thisDayBegin,$today);


/*10装入昨日注册*/
  $totalArray[]=findRegisterUserNumbyTime($lastDayBegin,$thisDayBegin);

/*11装入昨日认证*/
  $totalArray[]=findIdentyUserNumbyTime($lastDayBegin,$thisDayBegin);


 /*12装入本周注册*/
  $totalArray[]=findRegisterUserNumbyTime($thisWeekBegin,$today);


/*13装入本周认证*/
  $totalArray[]=findIdentyUserNumbyTime($thisWeekBegin,$today);


 /*14装入上周注册*/
  $totalArray[]=findRegisterUserNumbyTime($lastWeekBegin,$thisWeekBegin);


 /*15装入上周认证*/
  $totalArray[]=findIdentyUserNumbyTime($lastWeekBegin,$thisWeekBegin);

/*16装入本月注册*/
  $totalArray[]=findRegisterUserNumbyTime($thisMonthBegin,$today);

/*17装入本月认证*/
  $totalArray[]=findIdentyUserNumbyTime($thisMonthBegin,$today);


 /*18装入上月注册*/
  $totalArray[]=findRegisterUserNumbyTime($lastMonthBegin,$thisMonthBegin);


 /*19装入上月认证*/
  $totalArray[]=findIdentyUserNumbyTime($lastMonthBegin,$thisMonthBegin);


/*20装入车平均注册关注数*/
  $numOfCar=findCarNum();
  $numOfUser=findRegisterFollowUserNum();
  if($numOfCar==0 || $numOfUser==0)$totalArray[]=0;
  else $totalArray[]=number_format((double)($numOfUser/$numOfCar),2);


/*21装入车平均认证关注数*/
  $numOfCar=findCarNum();
  $numOfUser=findIdentyFollowUserNum();
  if($numOfCar==0 || $numOfUser==0)$totalArray[]=0;
  else $totalArray[]=number_format((double)($numOfUser/$numOfCar),2);


/*22装入一段时间内的车平均使用时长(小时)*/
  $time=calculateCarTotalUseTime($thisWeekBegin,$today);
  $numOfCar=findCarNum();
  if($numOfCar==0 || $time==0)$totalArray[]=0;
  else $totalArray[]=number_format((double)(($time/$numOfCar)/3600),2);/*$time/$numOfCar/3600把秒转化为小时*/


$table="<table border='1' cellspacing='0' >
<tr style='background:rgb(255,157,97);'>
<th>累计注册</th>
<th>累计认证</th>
<th>活跃用户</th>
<th>不活跃用户</th>
<th>沉默用户</th>
<th>停用用户</th>
<th>本日注册</th>
<th>本日认证</th>
<th>昨日注册</th>
<th>昨日认证</th>
<th>本周注册</th>
<th>本周认证</th>
<th>上周注册</th>
<th>上周认证</th>
<th>本月注册</th>
<th>本月认证</th>
<th>上月注册</th>
<th>上月认证</th>
<th>车平均注册关注数</th>
<th>车平均认证关注数</th>
<th>车周平均时长（h）</th>
</tr>";
$table.="<tr style='background:rgb(134,202,182);'>";

for($i=0;$i<$numOfTerm-1;$i++)
    $table.=("<td>".$totalArray[$i]."</td>");

$table.="</tr></table>";
echo $table;
echo "<br>";echo "<br>";
@mysql_close();
