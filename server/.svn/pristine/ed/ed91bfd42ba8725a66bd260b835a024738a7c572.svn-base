<?php 
require_once (dirname(__FILE__) . '/../hfrm/Frm.php');
require_once (Frm::$ROOT_PATH . 'client/http/web.php');
$op=selectOutput('ArrOutput','MemberClient');
$ret=$op->output('getUserInfo');
$myCarList=$ret['content'];

$userInfo=$ret['content'];
// print_r($userInfo);
$approveMsg=$userInfo['approve_id']>0 ? '已实名认证' : '<a href="authentication.php">未完成实名认证</a>';

$violateInfo=$op->output('getViolateInfo');
$violateInfo=$violateInfo['content'];
require_once (Frm::$ROOT_PATH . 'order/classes/StatisticsOrder.php');
require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
require_once (Frm::$ROOT_PATH . 'order/classes/Order.php');
$orderedCount=StatisticsOrder::getCountOrderByStat(UserManager::getLoginUserId(),array(Order::$STATUS_DOOR_CLOSED,Order::$STATUS_SETTLED));

$orderingCount=StatisticsOrder::getCountOrderByStat(UserManager::getLoginUserId(),array(Order::$STATUS_STARTED,Order::$STATUS_ORDERED,Order::$STATUS_PAY_TIMEOUT,Order::$STATUS_COMPLETED));

$lanStatus=array('未支付','已预定','进行中','进行中','已取消','支付超时','还车超时','','订单完成','订单完成');
$lanModify=array('','修改中','修改完成');

$ret=$op->output('getMyOrderList');
if ($ret['errno']==2000) {
    $orders=$ret['content'];
}

 ?>


    <?php include 'header.html';?>
    <script type="text/javascript" src="js/jquery.form.js"></script>
    <link href="css/yiduo.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .baikuang2{ width:470px;float:left; margin:10px 0 0 0;padding:20px 5px 20px 5px; background:#fff; border-top:1px solid #DADADA; border-bottom:1px solid #DADADA;}
        a{color:#4F4F4F;text-decoration:none;}
        .anniu-1:active{ background:url(img/anniu-1-2.png) no-repeat; color:#4F4F4F;}
        .anniu-4:active{ background:url(img/anniu-2-2.png) no-repeat; color:#4F4F4F;}
        .anniu-5:active{ background:url(img/anniu-3-2.png) no-repeat; color:#4F4F4F;}
        .anniu-2:active{ color:#4F4F4F;}
        .anniu-3:active{ color:#4F4F4F;}
        .anniu-xgmm:active{ background:url(img/anniu-2-2.png) no-repeat; color:#4F4F4F;}
        .anniu-9:active{ background:url(img/anniu-1-2.png) no-repeat; color:#4F4F4F;}
        .xieyi:active{color:#4F4F4F; text-decoration:underline;}
        .baikuang-liebiao{ width:750px;float:right; margin:0;padding:20px 5px 30px 5px;}
        .dingdan-zhuangtai{ width:900px; float:left; margin:0;}
        .anniu-dingdan{width:100px; height:25px;float:left; background-color: #FF9900;margin:20px 0 0 20px;text-align:center; padding:13px 0 0 0;font-family:"宋体"; font-size:14px; font-weight:bold;color:#4F4F4F;}

        .anniu-dingdan:active{ background-color:#CCC;color:#4F4F4F;}
        .fanye{ width:25px; height:20px; float:left; margin:0 0 0 10px;background-color:#CCC;text-align:center; padding:5px 0 0 0;font-family:"宋体"; font-size:14px; font-weight:bold;color:#4F4F4F;}
        .popbox{display:none;z-index:120;}

        #screen,.gray-screen{width:100%;height:100%;position:absolute;top:0;left:0;display:none;z-index:100;background-color:#666;opacity:0.5;filter:alpha(opacity=50);-moz-opacity:0.5;}
    </style>
<div class="dingche">
<div class="zhanghu-left" style="height:inherit;">
<div class="liebiao-1">
    <p class="liebiao-1-1">
        订车管理
    </p>
    <ul class="liebiao-1-2">
        <li class="liebiao-xinxi"><a href="#" class='order-list-click'>我的订单</a>
        </li>
    </ul>
</div>
<div class="liebiao-1">
    <p class="liebiao-1-1">
        交易管理
    </p>
    <ul class="liebiao-1-2">
        <li class="liebiao-xinxi"><a href="#" class='violate-click'>我的违章</a>
        </li>
       <!--  <li class="liebiao-xinxi"><a href="#" class="invoice-click">索取发票</a>
        </li> -->
        <!-- <li class="liebiao-xinxi">优惠权利
        </li> -->
    </ul>
</div>
<div class="liebiao-1">
    <p class="liebiao-1-1">
        个人信息管理
    </p>
    <ul class="liebiao-1-2">
        <li class="liebiao-xinxi binding-weibo-click"><a href="#">绑定微博</a>
        </li>
        <?php  if(empty($userInfo['phone'])){?>
        <li class="liebiao-xinxi">
             <a href="bangdingshouji.php" class="lan12">绑定手机</a>
         </li>
         <?php }?>
        <?php if (empty($userInfo['super_password'])) { ?>
            <li class="liebiao-xinxi"><a href="#" class="super-pass-click">设置超级密码</a>
            </li>
        <?php } ?>
        <li class="liebiao-xinxi"><a href="#" class="modify-supass-click">修改超级密码</a></li>

        <li class="liebiao-xinxi"><a href="#" class="pass-click">修改密码</a></li>
        <?php if($userInfo['approve_id']<1){ ?>
         <li class="liebiao-xinxi">
            <a href="authentication.php">实名认证</a>
        </li>
        <?php } ?>
       
    </ul>
</div>
<div class="liebiao-1">
    <p class="liebiao-1-1">
        易多现金账户
    </p>
    <ul class="liebiao-1-2">
        <li class="liebiao-xinxi"><a class='charge-click' href="#">充值</a>
        </li>
        <li class="liebiao-xinxi"> <a href="#" class="consume-list-click">账单</a></li>
        <!-- <li class="liebiao-xinxi">提现</li><li class="liebiao-line"></li><li class="liebiao-xinxi">马上支付</li><li class="liebiao-line"></li><li class="liebiao-xinxi">账单</li> -->
    </ul>
</div>

<div class="liebiao-1"><p class="liebiao-1-1">服务中心</p><ul class="liebiao-1-2">
    <!-- <li class="liebiao-xinxi">我的投诉</li> -->
    <!-- <li class="liebiao-line"></li> -->
    <li class="liebiao-xinxi"><a href="xieyi.php">协议升级</a></li>
</ul>
</div>

</div>

<div class="zhanghu-right detail jq-content" style="height:inherit;">
    <span class="zhanghu-xinxi">
        <p class="lan14"><?php echo $userInfo['true_name'] ?></p> 
        <p class="hei12"><?php echo $approveMsg; ?></p>
        <?php  if(empty($userInfo['phone'])){?><a ="bangdingshouji.php" class="lan12">绑定手机</a><?php } ?>
    </span>
<div  style=" width:780px;"class="zhangmubiao">
    <ul>
            <li  style=" width:85px;border-right:1px solid #D8D9DB;">账户名称：</li>

            <li style=" width:278px; background:#F4F4F4;font-weight:bold;border-right:1px solid #D8D9DB;"><?php echo $userInfo['phone'] ?></li>

            <li style=" width:100px;border-right:1px solid #D8D9DB;">账户余额：</li>

            <li style=" width:254px;background:#F4F4F4;font-weight:bold;" ><?php echo ($userInfo['amount']-$userInfo['freeze_money'])/100; ?>元</li>
    </ul>
    <ul>
        <li  style=" width:85px;border-right:1px solid #D8D9DB;">违章记录：</li>

        <li style=" width:278px;background:#F4F4F4;font-weight:bold;border-right:1px solid #D8D9DB;"><p class="hei12"><?php echo count($violateInfo); ?>次</p></li>

        <li style=" width:100px;border-right:1px solid #D8D9DB;">已完成订单：</li>

        <li style=" width:254px;background:#F4F4F4;font-weight:bold;" ><?php echo $orderedCount ?>个</li>
    </ul>
    <ul>
        <li  style=" width:85px;border-right:1px solid #D8D9DB;">优惠权利</li>

        <li style=" width:278px;background:#F4F4F4;font-weight:bold;border-right:1px solid #D8D9DB;"><a href="#" class="lan12">生日我最大</a><a href="#" class="lan12">免费初体验</a></li>

        <li style=" width:100px;border-right:1px solid #D8D9DB;">正在进行的订单：</li>

        <li style=" width:254px;background:#F4F4F4;font-weight:bold;" ><?php echo $orderingCount; ?>个</li>
    </ul>
    <ul>
        <li  style=" width:85px;border-right:1px solid #D8D9DB;"> </li>

        <li style=" width:278px;background:#F4F4F4;font-weight:bold;border-right:1px solid #D8D9DB;"> </li>

        <li style=" width:100px;border-right:1px solid #D8D9DB;"> </li>

        <li style=" width:254px;background:#F4F4F4;font-weight:bold;" > </li>
      </ul>
</div>

<span class="zhanghu-xinxi"><p class="lan14">最近订单：</p> <p class="lan12" style="float:right;"><a href="#" class='order-list-click'>全部订单</a></p></span>
<div  style=" width:780px;" class="zhangmubiao">
  <ul>
    <li  style=" width:120px; background:#F4F4F4;font-weight:bold;border-right:1px solid #D8D9DB;">订单编号</li>    
    <li style=" width:130px; background:#F4F4F4;font-weight:bold; border-right:1px solid #D8D9DB;">车型</li>
    <li style=" width:170px; background:#F4F4F4;font-weight:bold;border-right:1px solid #D8D9DB;">取车时间</li>
    <li style=" width:170px;background:#F4F4F4;font-weight:bold;border-right:1px solid #D8D9DB;" >还车时间</li>
    <li style=" width:111px;background:#F4F4F4;font-weight:bold;" >订单状态</li>
  </ul>

    <?php 

    $num =0;
    foreach ($orders as $v) {

        $num++;
        if ($num>10) {
            break;
        }
    ?>

   <ul>
    <li  style=" width:120px;border-right:1px solid #D8D9DB;"><?php echo $v['order_no']; ?></li>

    <li style=" width:130px;border-right:1px solid #D8D9DB;"><?php echo $v['brand'].$v['name']; ?></li>

    <li style=" width:170px;border-right:1px solid #D8D9DB;"><?php echo $v['order_start_time']; ?></li>

    <li style=" width:170px;border-right:1px solid #D8D9DB;" ><?php echo $v['order_end_time']; ?></li>
    <li style=" width:111px;" ><?php 

            echo $lanStatus[$v['order_stat']];
            echo ' '.$lanModify[$v['modify_stat']];
            ?></li>
  </ul> 

    <?php } ?>
 
</div>
<!-- <span class="zhanghu-xinxi"><p class="lan14">优惠活动：</p></span>
<div class="youhui"><img src="img/youhui-pic.jpg"/><div class="youhui-neirong"><p class="hei14" style="width:380px; margin-bottom:5px;">易多清华新增5款车型，试驾赢电影票</p><p class="hei12" style="width:380px;">新增车型：本田雅阁、别克英朗、索纳塔、大众polo、甲壳虫<br/>活动内容：每周从订车成功的用户中抽取1名幸运用户送电影票2张<br/>活动时间：2013年1月13日至2014年1月1日
</p></div><a href="#" class="anniu-4">去看看</a></div> -->

</div>
<!-- 绑定信息 -->
<div class="binding-login jq-content" style='display:none;'> 
    <div class="">
    <span class="weibo-left">
    <p class="weibo"><a href="../openPlatForm/renren/login.php?req_agent=web"><img src="img/renren.jpg"><b class="weibo-txt">人人网</b></a></p>
    <p class="weibo"><a href="../openPlatForm/qq/login.php?req_agent=web"><img src="img/tengxun.jpg"><b class="weibo-txt">腾讯微博</b></a></p>
    <p class="weibo"><a href="../openPlatForm/weibo/login.php?req_agent=web"><img src="img/xinlang.jpg"><b class="weibo-txt">新浪微博</b></a></p>
    </span>
    </div>

</div>

<!-- 违章列表 -->
<div class="violate-list jq-content" style='display:none;' > 
    <div  style=" width:800px;" class="zhangmubiao">
      <ul>
        <li  style=" width:82px; background:#F4F4F4;font-weight:bold;border-right:1px solid #D8D9DB;">订单编号</li>    
        <li style=" width:80px; background:#F4F4F4;font-weight:bold; border-right:1px solid #D8D9DB;">违章时间</li>
        <li style=" width:80px; background:#F4F4F4;font-weight:bold;border-right:1px solid #D8D9DB;">违章地点</li>
        <li style=" width:80px;background:#F4F4F4;font-weight:bold;border-right:1px solid #D8D9DB;" >违章类型</li>
        <li style=" width:80px;background:#F4F4F4;font-weight:bold;border-right:1px solid #D8D9DB;" >违章罚金</li>
        <li style=" width:80px;background:#F4F4F4;font-weight:bold;border-right:1px solid #D8D9DB;" >扣分分数</li>
        <li style=" width:85px;background:#F4F4F4;font-weight:bold;border-right:1px solid #D8D9DB;" >代办费用</li>
        <li style=" width:85px;background:#F4F4F4;font-weight:bold;border-right:1px solid #D8D9DB;" >违章车牌号</li>
      </ul>

        <?php foreach ($violateInfo as $v) {

        ?>

       <ul>
        <li  style=" width:120px;border-right:1px solid #D8D9DB;"><?php echo $v['order_no']; ?></li>

        <li style=" width:130px;border-right:1px solid #D8D9DB;"><?php echo $v['violate_time']; ?></li>

        <li style=" width:170px;border-right:1px solid #D8D9DB;"><?php echo $v['violate_location']; ?></li>

        <li style=" width:170px;border-right:1px solid #D8D9DB;" ><?php echo $v['violate_type']; ?></li>
        <li style=" width:170px;border-right:1px solid #D8D9DB;" ><?php echo $v['violate_cost']; ?></li>
        <li style=" width:170px;border-right:1px solid #D8D9DB;" ><?php echo $v['violate_point']; ?></li>
        <li style=" width:170px;border-right:1px solid #D8D9DB;" ><?php echo $v['agency_cost']; ?></li>
        <li style=" width:170px;border-right:1px solid #D8D9DB;" ><?php echo $v['number']; ?></li>

      </ul> 

        <?php } ?>
 
    </div>

</div>
<!-- 设置超级密码 -->


<!--<div class="jq-content charge-money" style='display:none;'>
    <form action="../client/http/member.php" >
    <tr>
        金额:<input id="" name='amount' type="text" style="width:180px;"/>
        <input id="" name='t' value="web" type="text" style="width:180px;display:none;"/>
        <input id="" name='item' value="cashAccountChargeReq" type="text" style="width:180px;display:none;"/>
        <input type='submit' class="" style="margin:20px 0 0 10px;"/>
    </tr>
    </form>
</div>-->
<!-- 发票索取 -->
<div class="jq-content get-invoice" style='display:none;'>
    <form  action="../client/index.php" class='subform' op-name='索取发票'>
       <!--  <table>
            <tr>
                <td>发票抬头:</td>
                <td><textarea name="invoice_title" style=" height:100%;"></textarea></td>
            </tr>
            <tr>
                <td>寄送地址:</td>
                <td><textarea name="invoice_address" style=" height:100%;"></textarea></td>
            </tr>
            <tr>
                <td>新地址作为登记的发票地址:</td>
                <td><input name='isFavor' type='Checkbox'></td>
            </tr>
            <tr>
                <td>联系人:</td>
                <td><input datatype="Require"  require="ture" name="invoice_user" maxlength="60" size="30" value=""></td>
            </tr>
            <tr>
                <td>联系人手机:</td>
                <td><input datatype="Require"  require="ture" name="invoice_iphone" maxlength="60" size="30" value=""></td>
            </tr>
            <tr>
                <td>
                    <input type="submit" class="button anniu-2" value=" 确定 ">
                    
                    <input type="text" class='order-id' name='order_id' value="" style="display:none;">
                    <input type="text" name='class' value="OrderManager" style="display:none;">
                    <input type="text" name='item' value="askForInvoice" style="display:none;">
                </td>
            </tr>
        </table> -->
        <div class="zhanghu-right" id= "get-invoice" style="height:inherit;">
            <div class="fapiao-title">索取发票</div>
            <span class="fapiao-title1">请填写发票相关信息</span>
            <div class="fapiao-neirong">
                <!-- <div class="zhanghu-tc-title2" style="margin-left:175px;">
                    发票金额：<b class="tc-chengsezi" id="">380.00元</b>
                </div> -->
                <div class="zhanghu-tc-title2">
                    <p class="tc-lanmu">发票抬头：</p><textarea name="invoice_title" cols="1" rows="1" class="zhuce-sr"  style="width:275px; margin-right:5px;"></textarea>
                </div>
                <div class="zhanghu-tc-title2" style="height:85px;">
                    <p class="tc-lanmu">寄送地址：</p><textarea name="invoice_address" cols="1" rows="2" class="zhuce-sr"  style="width:275px; height:80px;margin-right:5px;"></textarea>
                </div>
                <div class="zhanghu-tc-title2">
                    <p class="tc-lanmu">联系人：</p><input datatype="Require"  require="ture" name="invoice_user" class="zhuce-sr"  style="width:275px; margin-right:5px;"/>
                </div>
                <div class="zhanghu-tc-title2">
                    <p class="tc-lanmu">电话：</p><input datatype="Require"  require="ture" name="invoice_iphone" class="zhuce-sr"  style="width:275px; margin-right:5px;"/>
                </div>
                <div class="zhanghu-tc-title2">
                    <input name='isFavor' type="checkbox" value="true" style="float:left; margin:5px 0 0 120px;"/>
                    <p class="tc-lanmu" style="width:230px; margin:0;">新地址作为发票的寄送地址</p>
                </div>
                <div class="zhanghu-tc-title2">
                    <!-- <a href="#" class="anniu-5" style=" margin-left:117px;">确认</a> -->
                    <input type="submit" class="anniu-5" value="确认" style=" margin-left:117px;height:43px;">
                    <input type="text" class='order-id' name='order_id' value="" style="display:none;">
                    <input type="text" name='class' value="OrderManager" style="display:none;">
                    <input type="text" name='item' value="askForInvoice" style="display:none;">
                </div>
            </div>
        </div>
    </form>
    
</div>
<div class="zhanghu-right" id="get-invoice-succ" style="display:none;height:inherit">

<div class="fapiao-title">发票信息已提交成功</div>
<span class="fapiao-title1">发票会尽快寄出，请注意查收！</span>
<div class="fapiao-neirong">
<div class="zhanghu-tc-title2"><a href="zhanghu_view.php" class="anniu-5" style=" margin-left:117px;">我知道了</a></div>
</div>
</div>
    
<!--<?php 
            $user=$op->output('getUserInfo');
            $user=$user['content'];

             ?>-->



<!-- 对账单 -->
<div class="jq-content consume-list" style='display:none;'>
    <?php 
    $ret=$op->output('getConsumeDetail');
    $cashAccountLog=$ret['content'];
     ?>
    <table>
    <tr>
    <th align="center">金额</th>
    <th align="center">操作类型</th>
    <th align="center">时间</th>
    <th align="center">余额</th>
    <th align="center">备注</th>
    </tr>
      <?php foreach ($cashAccountLog as $v) {?>
    <tr>  
        <td align="center"><?php echo $v['money']/100 ?>元</td>
        <td align="center"><?php echo $v['use_type'] ?></td>
        <td align="center"><?php echo $v['time']; ?></td>
        <td align="center"><?php echo $v['balance_amount']/100 ?>元</td>
        <td align="center"><?php echo $v['remark'] ?></td>
    </tr>
      <?php  } ?>
    </table>
</div>




<style type="text/css">
    
</style>


<div class="zhanghu-right jq-content order-list " style="height:inherit;display: none;">
    <?php 
    $ret=$op->output('getMyOrderList');
    if ($ret['errno']==2000) {
        $orders=$ret['content'];
        // print_r($orders);

    }

     ?>
    <?php foreach ($orders as $order) { ?>
       <span class="baikuang-liebiao" id="Menu_1">
            <p class="dingdan-liebiao-1" style="background-color: #EAEAEA; padding:3px 0 3px 15px;">
                订单编号：
                <span id="vOrderNo" class="chengse-text"><?php echo $order['order_no'] ?></span>
                <span id="vOrderStatus" style=" margin-left:40px; font-weight:bold;">
                <?php 

                echo $lanStatus[$order['order_stat']];
                echo ' '.$lanModify[$order['modify_stat']];
                ?> 
                </span>
            </p> 

            <p class="dingdan-liebiao-1" style="height:auto">
                预定起止时间：
                <span id="vOrderTime"><?php echo $order['order_start_time'] ?> 至 <?php echo $order['order_end_time'] ?> </span>

            </p>
            <p class="dingdan-liebiao-1" style="height:auto">
                成交时间：
                <span id="vOrderAddTime"><?php echo $order['add_time'] ?>  </span>
            </p>
            <p class="dingdan-liebiao-1" style="height:auto; clear:left">
                实收押金：
                <span id="vOrderDeposit" > <?php echo $order['deposit']/100 ?>元</span>
            </p>
            <p class="dingdan-liebiao-1" style="height:auto; clear:left">
                站点车型：
                <span id="vOrderStation" ><?php echo $order['station'] ?>，<?php echo $order['brand'].$order['name'] ?></span>
            </p>
            <p class="dingdan-zhuangtai">


                <?php 
                
                
                if (($order['order_stat']==Order::$STATUS_NOT_PAY ||$order['order_stat']==Order::$STATUS_ORDERED ||$order['order_stat']==Order::$STATUS_STARTED) AND $order['modify_stat']== 0 )
                {   ?>
                    

                    <span> <a href="javascript:void(0)" order-id=<?php echo $order['order_id'] ?> class="anniu-dingdan modify-order">修改订单</a> </span> 

                <?php 
                }


                if (($order['order_stat']==Order::$STATUS_NOT_PAY ||$order['order_stat']==Order::$STATUS_ORDERED) AND $order['modify_stat']== 0 )
                {   ?>
                    <!-- modify by yangbei 2013-04-27修改ID为class -->
                    <!-- <span id="vCancelOrder"> -->
                    <!-- modify by yangbei 2013-04-27修改ID为class -->    
                    <span class="vCancelOrder">
                        <a href="javascript:void(0)" order-id=<?php echo $order['order_id'] ?> orderNo=<?php echo $order['order_no'] ?> class="anniu-dingdan">取消订单</a>
                    </span>

                <?php 
                }


                if ($order['order_stat']==Order::$STATUS_NOT_PAY)
                {
                    ?>
                    <span id="vOrderBtnPay"> <a href="javascript:;"  order-id=<?php echo $order['order_id'] ?>  class="anniu-dingdan payfor-order">支付</a> </span>
                <?php 
                }
                if ($order['order_stat']==Order::$STATUS_DOOR_CLOSED||$order['order_stat']==Order::$STATUS_SETTLED)
                {
                    ?>
                    <span id="invoice"> <a href="javascript:void(0)" order-id=<?php echo $order['order_id'] ?> class="anniu-dingdan invoice-click" algin="center">发票索取</a> </span>
                    <span id="showOrder" order-id=<?php echo $order['order_id'] ?>> <a href="javascript:void(0)" class="anniu-dingdan" algin="center">晒单</a> </span>
                    <!-- <span id="bill" order-id=<?php echo $order['order_id'] ?>> <a href="javascript:void(0)" class="anniu-3" algin="center">账单</a> </span> -->
                <?php 
                }
                 ?>

                <!-- <a href="#" class="anniu-dingdan">修改订单</a>
                <a href="#" class="anniu-dingdan">取消订单</a>
                <a href="#" class="anniu-dingdan" style="display:none;">发起同行</a> -->
            </p> 
            <span class="zhuce-1-1 cancel-div" order-id=<?php echo $order['order_id'] ?> style="width:800px;display:none;">
                <p class="denglu-1-2" style="width:100px">超级密码：</p>
                <input id="" name="super-password" type="password" class="zhuce-sr super-password"  style="width:225px; margin-right:10px;"/>
                <a href="javascript:;" class="anniu-4 cancel cancel-cancel" style="margin-left:10px;">取消</a>
                <a href="javascript:;" class="anniu-4 sure-cancel" style="margin-left:10px;">确认</a>
            </span>
        </span>

    <?php } ?>
        <form method="POST" action="xg-dingdan.php" id='subform' style="display:none;">
        <input type="hidden" id="order-id" name="order_id" value="">
        <input type="hidden" id="ref" name="ref" value="">
    </form>
</div>
<div id="screen"></div>
<div class="popbox">
    <div class="mainlist">
        <div class="zhanghu-tc" style="float:left;">
            <!-- 取消订单成功页面 -->
            <div id="cancelOrderSucc" class="content" style="display:none">
                <div class="zhanghu-tc-title">取消订单成功</div>
                <div class="zhanghu-tc-title1">
                    订单编号： <b class="tc-huisezi" id="orderNumber">EXGU8899</b>
                </div>
                <div class="zhanghu-tc-title2">
                    收取退订费用：<b class="tc-chengsezi" id="charge2">20.00元</b>
                </div>
                <div class="zhanghu-tc-title2" id="retInfo">
    
                </div>

                <div class="zhanghu-tc-title2">
                    <a href="zhanghu_view.php" class="anniu-5" style=" margin-left:80px;">知道了</a>
                </div>
            </div>
        
            <!-- 取消订单页面 -->
            <div id="cancelOrder" class="content" style="display:none">
                <div class="zhanghu-tc-title">取消订单</div>
                <div style="margin-left:70px;">
                    <div class="zhanghu-tc-title1">
                    订单编号： <b class="tc-huisezi" id="orderNumber2">EXGU8899</b>
                    <b style="display:none;" id="orderId"></b>
                    </div>
                    <div class="zhanghu-tc-title2">
                        退订费： <b class="tc-chengsezi" id="charge">20.00元</b>
                    </div>
                    <div class="zhanghu-tc-title2">
                        请输入超级密码，以确认取消订单!
                    </div>
                    <div class="zhanghu-tc-title2">
                        <input id="superpassword" name="" type="password" class="zhuce-sr" style="width:270px;"/>
                        <c id="cancelInfo" style="color:red"></c>
                    </div>
                    <div class="zhanghu-tc-title2">
                        <input type="submit" value="确定" onclick="sureCancel();" class="anniu-xgmm" style="height:42px;border:none;" />
                        <a href="javascript:closeWin();"  class="anniu-xgmm" style="position:absolute;right:240px">取消</a>
                    </div>
                </div>
                
            </div>
            
            <!-- 修改超级密码页面 -->
            <div id="modifySuPass" class="content" style="display:none;margin-left:50px;">
                <form action='../client/http/member.php' class='subform' op-name='修改超级密码' >
                    <div class="zhanghu-tc-title">修改超级密码</div>
                    <div class="zhanghu-tc-shuoming" style="border:0; height:10px; margin-top:5px;">
                        <img src="img/tc-tishi-pic.jpg"/>
                        定期更换密码可以让您的账户更安全！
                    </div>
                    <div class="zhanghu-tc-title2">
                        <p class="tc-lanmu">当前密码：</p>
                        <input id="oldSuPass" name="oldpassword"  type="password" class="zhuce-sr"  style="width:225px; margin-right:5px;"/>
                        <p id="oldPassError" style="font-size:14px;color:red;display: none;margin-top: 10px;">原密码错误</p>
                    </div>
                    <div class="zhanghu-tc-title2">
                        <p class="tc-lanmu">新密码：</p>
                        <input id="newSuPass" name="password" onblur="checkMoSuperPass();" type="password" class="zhuce-sr"  style="width:225px; margin-right:5px;"/>
                        <p id="modifySuNotice1" style="color: green;font-size:12px;margin-top:5px;">密码格式为：不小于六位</p>
                        <p id="modifySuNotice4" style="display:none;color: red;font-size:12px;margin-top:5px;">不能与登陆密码相同</p>
                    </div>
                    <div class="zhanghu-tc-title2">
                        <p class="tc-lanmu">确认新密码：</p>
                        <input id="newSuPass2" name="" onblur="isMoRight()" type="password" class="zhuce-sr"  style="width:225px; margin-right:5px;"/>
                        <p id="modifySuNotice2" style="font-size:12px;color: red;display: none;margin-top:5px;">输入不一致，重新输入</p>
                        <p id="modifySuNotice3" style="font-size:12px;color: red;display: none;margin-top:5px;">超级密码不能为空</p>
                    </div>
                    <div class="zhanghu-tc-title2">
                        <input type="text" style="display:none;" name='item' value="modifySuperPassword">
                        <input type="submit" value="确定" class="anniu-xgmm" style="height:42px;border:none;position:absolute;bottom:80px;left:210px;text-align: center" />
                        <a href="javascript:closeWin();"  class="anniu-xgmm" style="position:absolute;bottom:80px;right:240px">取消</a>
                    </div>
                    <div class="zhanghu-tc-shuoming">请确保超级密码与登录密码不同，建议密码采用字母与数字混合，并且不少于6位！</div>
                </form>
            </div>
            
            <!-- 设置超级密码页面 -->
            <div id="setSuPass" class="content" style="display:none">

                    <form action='../client/http/member.php' class='subform' op-name='设置超级密码' >
                            <div class="zhanghu-tc4" >
                            <div class="zhanghu-tc-title">设置超级密码</div>
                            <div class="zhanghu-tc-shuoming" style="border:0; height:10px; margin-top:5px; margin-bottom:20px;">
                                <img src="img/tc-tishi-pic.jpg"/>说明：当您进行一些与资金相关或用车等操作时，系统会要求您输入超级密码，以保证您账户的安全。
                            </div>
                            <div class="zhanghu-tc-title2">
                                <p class="tc-lanmu">输入密码：</p>
                                <input id="suPass" onblur="checkSuperPass();" name="password" type="password" class="zhuce-sr"  style="width:225px; margin-right:5px;"/>
                                <p class="note" style="color: green;font-size:12px;margin-top:5px;">密码格式为：不小于六位</p>
                                <p class="note4" style="display:none;color: red;font-size:12px;margin-top:5px;">不能与登陆密码相同</p>
                            </div>
                            <div class="zhanghu-tc-title2">
                                <p class="tc-lanmu">确认密码：</p>
                                <input id="suPass2" name="password2" type="password" class="zhuce-sr"  style="width:225px; margin-right:5px;" onblur="isRight()"/>
                                 <p class="note2" style="font-size:12px;color: red;display: none;margin-top:5px;">输入不一致，重新输入</p>
                                <p class="note3" style="font-size:12px;color: red;display: none;margin-top:5px;">超级密码不能为空</p>
                            </div>
                            <div class="zhanghu-tc-title2">
                                <input type="text" style="display:none;" name='item' value="setSuperPassword">
                                <input type="submit" value="确定" class="anniu-xgmm" style="height:42px;border:none;position:absolute;bottom:80px;left:210px;text-align: center" />
                                <a href="javascript:closeWin();"  class="anniu-xgmm" style="position:absolute;bottom:80px;right:240px">取消</a>
                            </div>
                            <div class="zhanghu-tc-shuoming" style=" margin-top:55px;">请确保超级密码与登录密码不同，建议密码采用字母与数字混合，并且不少于6位！</div>
                        </div>  
                    </form>

            </div>
            
            <!-- 充值页面-->
            <div id="topUp" class="content" style="display:none">

                    <form id="chongzhi" action="../client/http/member.php"  onsubmit="return checkinput();"  target="_blank"> 
                        <div class="zhanghu-tc-title">充值</div>
                        <div class="zhanghu-tc-title2" style="height:35px;">账户名称：<b class="tc-huisezi"><?php echo $user['true_name'] ?></b></div>
                        <div class="zhanghu-tc-title2">账户余额：<b class="tc-chengsezi" ><?php echo ($userInfo['amount']-$userInfo['freeze_money'])/100;?>元</b></div>
                        <div class="zhanghu-tc-title2"><p class="tc-lanmu" style=" width:92px;text-align:left; magin-top:4px;">充值金额：</p><input name='amount' type="text" id="tt" class="zhuce-sr"  style="width:200px; magin:0px;"/>
                            <input type="submit" id="submit1" value="充值" class="anniu-xgmm" style="height:42px;border:none;position:absolute;bottom:90px;left:210px;text-align: center"/>
                            <a href="javascript:closeWin();" class="anniu-xgmm" style="position:absolute;bottom:90px;right:270px">取消</a>
                             <input onblur="notfu()" onfocus="bb()" id="text1" name='item' value="cashAccountChargeReq" type="text" style="width:180px;display:none;"/>
                             <p class="precharge" style="font-size:14px;color:green;margin-top: 6px;"><font color='red' style="font-size:20px">元</font>   请输入要充值的钱数</p> 
                        </div>
                        <input id="" name='t' value="web" type="text" style="width:180px;display:none;"/>
                        <input id="" name='item' value="cashAccountChargeReq" type="text" style="width:180px;display:none;"/>
                        <div class="zhanghu-tc-shuoming" style=" margin-top:75px;">说明：使用手机充值只支持信用卡或开通了“银联无卡支付”业务的储蓄卡。如何开通“银联无卡支付”业务，请登录<a href="http://www.eduoauto.com/help/uppay">http：//www.eduoauto.com/help/uppay</a></div>
                    </form>

            </div>

            <!-- 修改密码页面-->
            <div id="modifyPass" class="content" style="display:none">


                    <form action="../client/index.php" class='subform' op-name='修改密码'>
                        <div class="zhanghu-tc5">
                        <div class="zhanghu-tc-title">修改密码</div>
                        <div class="zhanghu-tc-shuoming" style="border:0; height:10px; margin-top:5px;">
                            <img src="img/tc-tishi-pic.jpg"/>定期更换密码可以让您的账户更安全！
                        </div>
                        <div class="zhanghu-tc-title2">
                            <p class="tc-lanmu">当前密码：</p>
                            <input onblur="checkNull()" id="password" datatype="Require" type='password'  require="ture" name="old" class="zhuce-sr"  style="width:225px; margin-right:5px;" />
                            <p class="CurrentPassNote" style="font-size:14px;color:green;margin-top: 10px;">请输入原密码</p>
                            <p class="CurrentPassError" style="font-size:14px;color:red;display: none;margin-top: 10px;">原密码错误</p>
                        </div>
                        <div class="zhanghu-tc-title2">
                            <p class="tc-lanmu">新密码：</p>
                            <input onblur="checkPass()"  onfocus="cc()" id="password" datatype="Require" type='password'  require="ture" name="new" class="zhuce-sr"  style="width:225px; margin-right:5px;" onblur="ValidateInput('password',this.value)" onkeydown="validatePwdStrong(this.value);"/>
                            <p class="NewPassNote"style="font-size:14px;color:green;margin-top: 10px;">密码不小于六位</p>
                        </div>
                        <div class="zhanghu-tc-title2">
                            <p class="tc-lanmu">确认新密码：</p>
                            <input  onblur="confirmPass()" onfocus="cc()" id="password" datatype="Require" type='password'  require="ture" name="new2" class="zhuce-sr"  style="width:225px; margin-right:5px;"/>
                            <p class="ConfirmPassNote" style="font-size:14px;color:green;margin-top: 10px;">请再次输入密码</p>
                            <p class="ConfirmPassError" style="font-size:14px;color:red;display: none;margin-top: 10px;">两次密码输入不一致</p>
                        </div>
                        <div class="zhanghu-tc-title2">
                            <input type="text" name='class' value="UserManager" style="display:none;">
                            <input type="text" name='item' value="modifyPassword" style="display:none;">
                            <input type="submit" value="确定" class="anniu-xgmm" style="height:42px;border:none;position:absolute;bottom:80px;left:210px;text-align: center" />
                            <a href="javascript:closeWin();"  class="anniu-xgmm" style="position:absolute;bottom:80px;right:240px">取消</a>
                        </div>
                    </div>
                    </form>
 
            </div>
            <!-- 晒单页面 -->
            <div id="showOrder" class="content" style="display:none;">
                <div class="zhanghu-tc-title" style="border:0;">晒单</div>
                <div class="zhanghu-tc-shuoming" style="border:0; height:100px; margin-top:5px;">
                    <textarea name="" cols="" rows="4" class="shaidan"></textarea>
                </div>
                <div class="shaidan-1">
                    <img src="img/shaidan-pic.jpg"/>
                    <span class="shaidan-weibo">
                        <input name="" type="checkbox" value="" style=" float:left;"/>
                        <p class="shaidan-weibo-1">晒到QQ微博</p>
                    </span>
                    <span class="shaidan-weibo">
                        <input name="" type="checkbox" value="" style=" float:left;"/>
                        <p class="shaidan-weibo-1">晒到新浪微博</p>
                    </span>
                    <span class="shaidan-weibo">
                        <input name="" type="checkbox" value="" style=" float:left;"/>
                        <p class="shaidan-weibo-1">晒到人人网</p>
                    </span>
                </div>
                <div class="zhanghu-tc-title2">
                    <a href="#" class="anniu-5" style=" margin-left:130px;">晒单</a>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
<script type="text/javascript">
    $('.binding-weibo-click').click(function(){
        $('.jq-content').hide();
        $('.binding-login').show();
    });
    $('.violate-click').click(function(){
        $('.jq-content').hide();
        $('.violate-list').show();   
    })
    $('.super-pass-click').click(function(){
        show('setSuPass');
    })
    $('.modify-supass-click').click(function(){
        show('modifySuPass');
    })

    $('.charge-click').click(function(){
       show('topUp');
    })

    $('.invoice-click').click(function(){

        var orderId=$(this).attr("order-id");
        $('.get-invoice').find('.order-id').val(orderId);
        $('.jq-content').hide();
        $('.get-invoice').show();   
    })

    $('.pass-click').click(function(){
        show('modifyPass');
    })
    $('.order-list-click').click(function(){
        $('.jq-content').hide();
        $('.order-list').show("slow");   
    })
    $('.consume-list-click').click(function(){
        $('.jq-content').hide();
        $('.consume-list').show();   
    })
    $('.cancel-cancel').click(function(){
        $(this).parent().hide("slow");
    });
    function sureCancel(){

        var orderId=$("#orderId").html();
        // alert(orderId);
        var password=$('#superpassword').val();
        if (password.length<1)
        {
            $("#cancelInfo").html('请输入超级密码');
            return false;
        };
        $.get("../client/http/member.php?item=cancel&order_id="+orderId+'&super_password='+password, function(data){

            var cancelRet=eval("("+data+")");
            if (cancelRet.errno==2000)
            {
                $("#cancelOrder").hide();
                show('cancelOrderSucc');
                $("#retInfo").html(cancelRet.content);

            }
            else
            {
                $("#cancelInfo").html(cancelRet.errstr);
                $("#superpassword").val("");
            }
        });
    }
    $('.vCancelOrder').click(function(){
        var buttonMe = this;
        var orderId = $(buttonMe).find('.anniu-dingdan').attr("order-id");
        var orderNo = $(buttonMe).find('.anniu-dingdan').attr("orderNo"); 
         $.ajax({
            type: "POST",
            url: "../client/http/member.php?item=getCancelOrderCommission&order_id="+orderId,
            success: function(data)
            {
                var ret=eval("("+data+")");
                if (ret.errno==2000)
                {

                    $("#charge").html(ret.content/100);
                    $("#charge2").html(ret.content/100);
                    $("#orderId").html(orderId);
                    $("#orderNumber").html(orderNo);
                    $("#orderNumber2").html(orderNo);
                    show('cancelOrder');
                    return;
                }
            }
        }); 
        // $("#orderNumber").html(orderNo);
        // $("#orderNumber2").html(orderNo);
        // show('cancelOrder');
        
    });

    $('.subform').submit(function() {
        // 提交表单

        var opName = $(this).attr('op-name');
        // alert(opName);
        if ((!check() && '修改密码' == opName) || ('设置超级密码' == opName && !isRight()) || ('修改超级密码' == opName && !modifySuCheck()) ) {

        } else {
            $(this).ajaxSubmit(function(data) {

                data = eval('(' + data + ')');
                if (data.errno == 2000) {
                    // alert(opName);
                    if ('索取发票'==opName) {
                        $("#get-invoice").hide();
                        $("#get-invoice-succ").show();
                    }else{
                       alert(opName + '成功');
                        window.location.href = 'zhanghu_view.php'; 
                    }
                    
                   
                }else{
                    if (opName == "修改密码") {
                        
                    } else {
                       
                    }

                     switch (opName) {
                        case '修改密码':
                            $(".CurrentPassNote").hide();
                            $(".CurrentPassError").show();
                            window.setTimeout(function() {jQuery("input[name='old']").focus();}, 0);
                            break;
                        case '设置超级密码':
                            $(".note").hide();
                            $(".note4").show();
                            break;
                        case '索取发票':
                            $('.get-invoice').hide();
                            $('.get-invoice-succ').show();
                            break;
                        case '修改超级密码':
                            $('#oldPassError').show();
                            $('#oldPassError').html(data.errstr);
                            break;    
                        default:
                             alert(data.errstr);
                    }
                }

            });
        }


        return false;

    });


    $('.modify-order').click(function(){
        var me = this;
        var orderId=$(me).attr("order-id");
        $("#order-id").val(orderId);
        $("#ref").val("modify");
        document.getElementById('subform').submit();
        // $.post("xg-dingdan.php", { order_id: orderId} );
    });

    $('.payfor-order').click(function(){
        var me = this;
        var orderId=$(me).attr("order-id");
        $("#order-id").val(orderId);
        $("#ref").val("payfor");
        document.getElementById('subform').action="zhifu-1.php";
        document.getElementById('subform').submit();
        // $.post("xg-dingdan.php", { order_id: orderId} );
    });
    hash = window.location.hash;
    if (hash.length>0)
    {
        $(hash.replace('#','.')).click();
    };
function makeScreenGray(){
        var h = $(document).height();
        $('#screen').css({ 'height': h });  
        $('#screen').show();
}

function closeWin(){
    $(".content").hide();
    $('.popbox').fadeOut(function(){ $('#screen').hide(); });
    // return false;
}

function show(str){
    makeScreenGray();
    var tt = ("#"+str+"");
    $(tt).show();
    $('.popbox').center();
    $('.popbox').fadeIn();
    return false;   
}
//充值检查 start
function bb(){
    var oInput = document.getElementById("submit1").focus();
}
function checkinput(){
    var s = document.getElementById("tt").value;
    var patrn=/^[0-9]{1,20}$/;
    if(s<=0||!patrn.exec(s)){
        $('.precharge').css("color","red");
        document.getElementById("tt").focus();
        document.getElementById("tt").value='';
        return false;
    }else{
        return true;
    }
}
//充值检查 end

//修改密码检查
function cc(){
    var old = $("input[name='old']").val();
    if (old=="") {
        window.setTimeout( function(){ jQuery("input[name='old']").focus(); }, 0);
        $(".CurrentPassNote").css("color","red");
    }
}

function checkNull(){
    var old = $("input[name='old']").val();
    if (old=="") {
        window.setTimeout( function(){ jQuery("input[name='old']").focus(); }, 0);
        $(".CurrentPassNote").css("color","red");
    }else{
        $(".CurrentPassNote").hide();
        window.setTimeout( function(){ jQuery("input[name='new']").focus(); }, 0);
    }
}
function checkPass(){
    var s = $("input[name='new']").val();
    var old = $("input[name='old']").val();
    if (old=="") {
        window.setTimeout( function(){ jQuery("input[name='old']").focus(); }, 0);
        $(".CurrentPassNote").css("color","red");
    }else if (s.length<6) {
        $(".CurrentPassNote").hide();
        //$(".CurrentPassNote").show();
        $(".NewPassNote").css("color","red");
        //$("input[name='new']").focus();
        window.setTimeout( function(){ jQuery("input[name='new']").focus(); }, 0);
        $("input[name='new']").clear();
    }else{
        $(".NewPassNote").hide();
        window.setTimeout( function(){ jQuery("input[name='new2']").focus(); }, 0); 
    }
}
function confirmPass(){
    var s = $("input[name='new']").val();
    var ConfirmPass = $("input[name='new2']").val();
    if (s=="") {
        $(".ConfirmPassNote").show();
        return false;
    }else if (s!=ConfirmPass && ConfirmPass!="") {
        $(".ConfirmPassNote").hide();
        $(".ConfirmPassError").show();
        $(".NewPassNote").show();
        window.setTimeout( function(){ jQuery("input[name='new']").focus(); }, 0);
        return false;
    }else{

        $(".ConfirmPassNote").hide();
        $(".ConfirmPassError").hide();
        return true;
    }
}
function check(){
    
    var s = $("input[name='new']").val();
    var old = $("input[name='old']").val();
    var ConfirmPass = $("input[name='new2']").val();
    if (s=="" || old==""||ConfirmPass=="" || s!=ConfirmPass) {
        cc();
        return false;
    }else{
        return true;
    }
}
//修改密码检查

//设置超级密码判断
function checkSuperPass(){
    $(".note").show();
    $(".note4").hide();
    var s = $("#suPass").val();
    var patrn = /^[0-9]{1,20}$/;
    if (s.length!=6 || !patrn.exec(s)) {
        $(".note").css("color","red");
        window.setTimeout( function(){ jQuery("#suPass").focus(); }, 0);//解决火狐聚焦问题
        document.getElementById('suPass').value = "";
       
    }else{
        $(".note").css("color","green");
        window.setTimeout( function(){ jQuery("#suPass2").focus(); }, 0);
        $(".note3").hide();
    }
}
function isRight(){
    var suPass = $("#suPass").val();
    var suPass2 = $("#suPass2").val();
    if(suPass==""){
        $(".note").css("color","red");
        window.setTimeout( function(){ jQuery("#suPass").focus(); }, 0);
        return false;
    }else if (suPass2=="") {
        $(".note2").hide();
        $(".note3").show();
        window.setTimeout( function(){ jQuery("#suPass").focus(); }, 0);//解决火狐聚焦问题
        document.getElementById('suPass2').value = "";
        return false;
    }else if (suPass!=suPass2) {
        $(".note2").show();
        window.setTimeout( function(){ jQuery("#suPass").focus(); }, 0);//解决火狐聚焦问题
        document.getElementById('suPass2').value = "";
        return false;
    }else{
        $(".note2").hide();
        return true;
    }
}
//设置超级密码判断
//修改超级密码判断
function checkMoSuperPass(){

    $("#modifySuNotice1").show();
    $("#modifySuNotice4").hide();
    var s = $("#newSuPass").val();
    var patrn = /^[0-9]{1,20}$/;
    if (s.length<6 || !patrn.exec(s)) {
        $("#modifySuNotice1").css("color","red");
        // window.setTimeout( function(){ jQuery("#newSuPass").focus(); }, 0);//解决火狐聚焦问题
        document.getElementById('newSuPass').value = "";
       
    }else{
        $("#modifySuNotice1").css("color","green");
        window.setTimeout( function(){ jQuery("#newSuPass2").focus(); }, 0);
        $("#modifySuNotice3").hide();
    }
}
function modifySuCheck () {
    var oldSuPass = $("#oldSuPass").val();
    var newSuPass = $("#newSuPass").val();
    var newSuPass2 = $("#newSuPass2").val();
    if (''!=oldSuPass&&''!=newSuPass&&''!=newSuPass2) {
        return true;
    }else{
        window.setTimeout( function(){ jQuery("#oldSuPass").focus(); }, 0);
        return false;
    }
}
function isMoRight(){
    var suPass = $("#newSuPass").val();
    var suPass2 = $("#newSuPass2").val();
    if(suPass==""){
        $("#modifySuNotice1").css("color","red");
        window.setTimeout( function(){ jQuery("#newSuPass").focus(); }, 0);
        return false;
    }else if (suPass2=="") {
        $("#modifySuNotice2").hide();
        $("#modifySuNotice3").show();
        window.setTimeout( function(){ jQuery("#newSuPass").focus(); }, 0);//解决火狐聚焦问题
        document.getElementById('newSuPass2').value = "";
        return false;
    }else if (suPass!=suPass2) {
        $("#modifySuNotice2").show();
        window.setTimeout( function(){ jQuery("#newSuPass").focus(); }, 0);//解决火狐聚焦问题
        document.getElementById('newSuPass2').value = "";
        return false;
    }else{
        $("#modifySuNotice2").hide();
        return true;
    }
}
</script>
<?php include 'footer.html';?>

