<?PHP
header("Content-Type:text/html;charset=utf-8"); 

/*------------------------------查询函数，返回类型为值类型--------------------------------------*/


/*查询某一站点车的数量（包括没有被关注的车）*/
function findCarNumbyStation($stationName)
{
  $numOfcar;
  //mysql_query("set nemes UTF8");
  $result=@mysql_query("SELECT COUNT(id) FROM edo_cp_car 
                        WHERE station IN 
                          (SELECT id FROM edo_cp_station WHERE name='$stationName')
                      ")
          or die("查询时出错！13");
   while($value= mysql_fetch_array($result))
  {
    $numOfcar=$value[0];
  }
  return $numOfcar;  
}

/*modify by matao 2013.4.21
某个用户和某个站点在一种关系中有过多次映射的统一按一次计算
具体体现在这是一个二次映射即复合函数，用户和站点的关系G(x)
是有用户和车辆的关系F(x)和车辆和站点的关系H(x)复合而来的，
即：用户A和车辆A1，A2 有映射关系，而车辆A1，A2 又同时和站
点C有映射关系，这时认为A和C只有一个映射关系即A->C。故此，在
计算某站点的关系人数时采用 DISTINCT 关键字。
*/


/* 查询某一站点的注册用户数量OK*/
function findRegisterUserNumbyStation($stationName)
{
  $users;
  //mysql_query("set names UTF8");
  $result=@mysql_query("SELECT COUNT(DISTINCT user_id) FROM edo_car_follow
                        WHERE user_id NOT IN
                          (SELECT sid FROM edo_staff)
                        AND car_id IN
                          (SELECT id FROM edo_cp_car WHERE station IN 
                            (SELECT id FROM edo_cp_station WHERE name='$stationName'))    
                      ")
          or die("查询时出错！14");

  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }
  return $users;
}


/* 查询某一站点的认证用户数量OK*/
function findIdentyUserNumbyStation($stationName)
{
  $users;
  //mysql_query("set names UTF8");
  $result=@mysql_query("SELECT COUNT(DISTINCT user_id) FROM edo_car_follow 
                        WHERE user_id IN
                          (SELECT user_id FROM edo_cp_identity_approve)
                        AND user_id NOT IN
                          (SELECT sid FROM edo_staff)                        
                        AND car_id IN
                          (SELECT id FROM edo_cp_car WHERE station IN 
                            (SELECT id FROM edo_cp_station WHERE name='$stationName'))
                      ")
          or die("查询时出错！15");
  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }      
  return $users;
}


/* 查询某一站点的活跃用户数量，即到今天为止30天之内有过成功交易订单的用户OK*/
function findActiveUserNumbyStation($stationName)
{
  $users;
  //date_default_timezone_set("Asia/Shanghai");
  $startTime=strtotime("-30 day");
  //mysql_query("set names UTF8");
  $result=@mysql_query("SELECT COUNT(DISTINCT user_id)FROM edo_cp_car_order
                        WHERE order_start_time>={$startTime} AND (order_stat=8 OR order_stat=9)
                        AND user_id NOT IN
                          (SELECT sid FROM edo_staff) 
                        AND car_id IN
                          (SELECT id FROM edo_cp_car WHERE station IN 
                            (SELECT id FROM edo_cp_station WHERE name='$stationName'))
                      ")
          or die("查询时出错！16");
  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }        
  return $users;
}


/* 查询某一站点的不活跃用户数量，即到今天为止30天之外有过成功交易订单的用户OK*/
function findunActiveUserNumbyStation($stationName)
{
  $users;
  //date_default_timezone_set("Asia/Shanghai");
  $startTime=strtotime("-30 day");
  //mysql_query("set names UTF8");
  $result=@mysql_query("SELECT COUNT(DISTINCT user_id) FROM edo_cp_car_order
                        WHERE order_start_time<{$startTime} AND (order_stat=8 OR order_stat=9)
                        AND user_id NOT IN
                          (SELECT user_id FROM edo_cp_car_order WHERE order_start_time>={$startTime} 
                            AND(order_stat=8 OR order_stat=9))
                        AND user_id NOT IN
                          (SELECT sid FROM edo_staff)
                        AND car_id IN
                          (SELECT id FROM edo_cp_car WHERE station IN 
                            (SELECT id FROM edo_cp_station WHERE name='$stationName'))
                      ")
          or die("查询时出错！17");
  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }          
  return $users;
} 


/* 查询某一站点的沉默用户数量，即到今天为止没有过交易订单的实名认证用户OK*/
function findSilentUserNumbyStation($stationName)
{
  $users;
  //mysql_query("set names UTF8");
  $result=@mysql_query("SELECT COUNT(DISTINCT user_id) FROM edo_car_follow 
                        WHERE user_id NOT IN
                          (SELECT user_id FROM edo_cp_car_order )
                        AND user_id NOT IN
                          (SELECT sid FROM edo_staff)  
                        AND user_id IN
                          (SELECT user_id FROM edo_cp_identity_approve)
                        AND car_id IN
                          (SELECT id FROM edo_cp_car WHERE station IN 
                            (SELECT id FROM edo_cp_station WHERE name='$stationName'))
                      ")
          or die("查询时出错！18");
  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }  
  return $users;
}


/* 查询某一站点的冻结用户数量OK*/
function findFreezeUserNumbyStation($stationName)
{
  $users;
  //mysql_query("set names UTF8");
  $result=@mysql_query("SELECT COUNT(DISTINCT user_id) FROM edo_car_follow 
                        WHERE user_id IN 
                          (SELECT uid FROM edo_user WHERE is_active=0)
                        AND user_id NOT IN
                          (SELECT sid FROM edo_staff) 
                        AND car_id IN
                          (SELECT id FROM edo_cp_car WHERE station IN 
                            (SELECT id FROM edo_cp_station WHERE name='$stationName'))
                      ")
          or die("查询时出错！19");
  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }        
  return $users;
}


/* 查询某一站点一段时间内的注册人数OK*/
function findRegisterUserNumbyTimebyStation($stationName,$startTime,$endTime)
{
  $users;
  //mysql_query("set names UTF8");
  $result=@mysql_query("SELECT COUNT(DISTINCT user_id) FROM edo_car_follow 
                        WHERE user_id IN
                          (SELECT uid FROM edo_user WHERE ctime>={$startTime} AND ctime<{$endTime})
                        AND user_id NOT IN
                            (SELECT sid FROM edo_staff)
                        AND car_id IN
                          (SELECT id FROM edo_cp_car WHERE station IN 
                            (SELECT id FROM edo_cp_station WHERE name='$stationName'))
                      ")
          or die("查询时出错！20");
  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }            
  return $users;
}


/* 查询某一站点一段时间内的认证人数OK*/
function findIdentyUserNumbyTimebyStation($stationName,$startTime,$endTime)
{
  $users;
  //mysql_query("set names UTF8");
  $result=@mysql_query("SELECT COUNT(DISTINCT user_id) FROM edo_car_follow
                        WHERE user_id IN
                          (SELECT user_id FROM edo_cp_identity_approve 
                            WHERE first_approve_time>={$startTime} AND first_approve_time<{$endTime})
                        AND user_id NOT IN
                          (SELECT sid FROM edo_staff)
                        AND car_id IN
                          (SELECT id FROM edo_cp_car WHERE station IN 
                            (SELECT id FROM edo_cp_station WHERE name='$stationName'))
                      ")
          or die("查询时出错！21");
  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }        
  return $users;
}

/*modify by matao 2013.4.21*/

/* 查询某一站点一段时间内的车总使用时长(秒) 按结束时间在计，算订单跨度为两个时间段的统一归到下一时间段计算。 且订单已经完成（8，9）*/
function calculateCarTotalUseTimebyStation($stationName,$startTime,$endTime)
{
  $totalStartTime;
  $totalEndTime;
  //mysql_query("set names UTF8");
  $result=@mysql_query("SELECT SUM(order_start_time) as _start,
                               SUM(CASE WHEN order_end_time>=real_end_time THEN order_end_time ELSE real_end_time end) as _end
                        FROM edo_cp_car_order 
                        WHERE ((order_end_time BETWEEN {$startTime} AND {$endTime}) 
                        OR (real_end_time BETWEEN {$startTime} 
                        AND {$endTime}))
                        AND (order_stat=8 OR order_stat=9) 
                        AND user_id NOT IN
                          (SELECT sid FROM edo_staff)
                        AND car_id IN
                          (SELECT id FROM edo_cp_car WHERE station IN 
                            (SELECT id FROM edo_cp_station WHERE name='$stationName'))
                      ")
                        
          or die("查询时出错！22");
   while($value= mysql_fetch_array($result))
  {
    $totalStartTime=$value['_start'];
    $totalEndTime=$value['_end'];
  }        
  $totalUseTime=$totalEndTime-$totalStartTime;
  return $totalUseTime;
}


/*查询车的数量（包括没有被关注的车）*/
function findCarNum()
{
  $numOfcar;
  //mysql_query("set nemes UTF8");
  $result=@mysql_query("SELECT COUNT(id) FROM edo_cp_car")
          or die("查询时出错！1");

   while($value= mysql_fetch_array($result))
  {
    $numOfcar=$value[0];
  }
  return $numOfcar;  
}


/* 查询注册用户数量OK*/
function findRegisterUserNum()
{
  $users;
  //mysql_query("set names UTF8");
  $result=@mysql_query("SELECT COUNT(uid) FROM edo_user
                        WHERE uid NOT IN
                          (SELECT sid FROM edo_staff)
                      ")
          or die("查询时出错! 2");

  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }
  return $users;
}


/* 查询认证用户数量OK*/
function findIdentyUserNum()
{
  $users;
  //mysql_query("set names UTF8");
  $result=@mysql_query("SELECT COUNT(user_id) FROM edo_cp_identity_approve
                        WHERE user_id NOT IN
                          (SELECT sid FROM edo_staff)
                      ")
          or die("查询时出错！3");

  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }      
  return $users;
}


/* 查询注册关注用户数量OK*/
function findRegisterFollowUserNum()
{
  $users;
  //mysql_query("set names UTF8");
  $result=@mysql_query("SELECT COUNT(DISTINCT user_id) FROM edo_car_follow
                        WHERE user_id NOT IN
                          (SELECT sid FROM edo_staff)
                      ")
          or die("查询时出错！4");

  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }
  return $users;
}


/* 查询认证关注用户数量OK*/
function findIdentyFollowUserNum()
{
  $users;
  //mysql_query("set names UTF8");
  $result=@mysql_query("SELECT COUNT(DISTINCT user_id) FROM edo_car_follow
                        WHERE user_id IN 
                          (SELECT user_id FROM edo_cp_identity_approve)
                        AND user_id NOT IN
                          (SELECT sid FROM edo_staff)
                      ")
          or die("查询时出错！5");

  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }     
  return $users;
}


/* 查询活跃用户数量，即到今天为止30天之内有过成功交易订单的用户OK*/
function findActiveUserNum()
{
  $users;
  //date_default_timezone_set("Asia/Shanghai");
  //mysql_query("set names UTF8");
  $startTime=strtotime("-30 day");
  //echo date("Y-m-d H:i:s",$startTime);echo "<br>";
  $result=@mysql_query("SELECT COUNT(DISTINCT user_id) FROM edo_cp_car_order 
                        WHERE order_start_time>={$startTime}
                        AND(order_stat=8 OR order_stat=9)
                        AND user_id NOT IN
                          (SELECT sid FROM edo_staff)
                      ")
          or die("查询时出错！6");

  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }         
  return $users;
}


/* 查询不活跃用户数量，即到今天为止30天之外有过成功交易订单的用户OK*/
function findunActiveUserNum()
{
  $users;
  //date_default_timezone_set("Asia/Shanghai");
  //mysql_query("set names UTF8");
  $startTime=strtotime("-30 day");
  $result=@mysql_query("SELECT COUNT(DISTINCT user_id) FROM edo_cp_car_order
                        WHERE order_start_time<{$startTime}
                        AND (order_stat=8 OR order_stat=9)
                        AND user_id NOT IN
                          (SELECT user_id FROM edo_cp_car_order 
                          WHERE order_start_time>={$startTime}
                          AND (order_stat=8 OR order_stat=9))
                        AND user_id NOT IN
                          (SELECT sid FROM edo_staff)
                      ")
          or die("查询时出错！7");

  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }          
  return $users;
} 
/* 查询沉默用户数量，即到今天为止没有过交易订单的实名认证用户OK*/
function findSilentUserNum()
{
  $users;
  //mysql_query("set names UTF8");
  $result=@mysql_query("SELECT COUNT(user_id) FROM edo_cp_identity_approve 
                        WHERE user_id NOT IN
                          (SELECT user_id FROM edo_cp_car_order)
                        AND user_id NOT IN
                          (SELECT sid FROM edo_staff)
                      ")

          or die("查询时出错！8");

  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }  
  return $users;
}


/* 查询的冻结用户数量OK*/
function findFreezeUserNum()
{
  $users;
  //mysql_query("set names UTF8");
  $result=@mysql_query("SELECT COUNT(uid) FROM edo_user WHERE is_active=0")
          or die("查询时出错！9");

  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }        
  return $users;
}


/* 查询一段时间内的注册人数OK*/
function findRegisterUserNumbyTime($startTime,$endTime)
{
  $users;
  //mysql_query("set names UTF8");
  $result=@mysql_query("SELECT COUNT(uid) FROM edo_user
                        WHERE ctime BETWEEN {$startTime} AND {$endTime}
                        AND uid NOT IN
                          (SELECT sid FROM edo_staff)
                      ")
          or die("查询时出错！10");

  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }            
  return $users;
}


/* 查询一段时间内的认证人数OK*/
function findIdentyUserNumbyTime($startTime,$endTime)
{
  $users;
  //mysql_query("set names UTF8");
  $result=@mysql_query("SELECT COUNT(user_id) FROM edo_cp_identity_approve
                        WHERE first_approve_time BETWEEN {$startTime} AND {$endTime}
                        AND user_id NOT IN
                            (SELECT sid FROM edo_staff)
                      ")
          or die("查询时出错！11");

  while($value= mysql_fetch_array($result))
  {
    $users=$value[0];
  }        
  return $users;
}


/* 查询一段时间内的车总使用时长(秒) 按结束时间在计，算订单跨度为两个时间段的统一归到下一时间段计算。 且订单已经完成（8，9）*/
function calculateCarTotalUseTime($startTime,$endTime)
{
  $totalStartTime;
  $totalEndTime;
  //@mysql_query("set names UTF8");
  $result=@mysql_query("SELECT SUM(order_start_time) as _start,
                               SUM(CASE WHEN order_end_time>=real_end_time THEN order_end_time ELSE real_end_time end) as _end
                        FROM edo_cp_car_order 
                        WHERE (order_end_time BETWEEN {$startTime} AND {$endTime}
                        OR real_end_time BETWEEN {$startTime} AND {$endTime})
                        AND user_id NOT IN
                          (SELECT sid FROM edo_staff)
                      ")
          or die("查询时出错！12");

   while($value= mysql_fetch_array($result))
  {
    $totalStartTime=$value['_start'];
    $totalEndTime=$value['_end'];
  }        
  $totalUseTime=$totalEndTime-$totalStartTime;
  return $totalUseTime;
}