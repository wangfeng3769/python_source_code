<?php include "header.html"; ?>
 <!-- Style -->
    <link href="imaPlayer/css/skitter.styles.css" type="text/css" media="all" rel="stylesheet" />
    <!--Scripts-->
    <script src="imaPlayer/js/jquery-1.6.3.min.js"></script>
    <script src="imaPlayer/js/jquery.skitter.min.js"></script>
    <script src="imaPlayer/js/jquery.easing.1.3.js"></script>
    <script src="imaPlayer/js/jquery.animate-colors-min.js"></script>
  <script>
        $(function()
        {
            var options = {};
            options['dots'] = true;
            $('.border_box').css({'marginBottom': '0px'});
            $('.box_skitter_large').skitter(options);
            //$('.box_skitter_large').skitter();
        });
    </script>

<div id="planImage">
	<div class="border_box">
		<center>
        <div class="box_skitter box_skitter_large" style="text-align: center">

    		<ul>
                <li>
    		        <a href="#"><img src="img/poster.jpg" class="randomSmart" /></a>
		
    		        <div class="label_text">
    		            <p></p>
    		        </div>
    		    </li>

    		    <li>
    		        <a href="#"><img src="img/lunbo1.jpg" class="randomSmart" /></a>
		
    		        <div class="label_text">
    		            <p></p>
    		        </div>
    		    </li>
    		    <li>
		
    		        <a href="#"><img src="img/lunbo2.jpg" class="randomSmart" /></a>
    		        <div class="label_text">
    		            <p></p>
		
    		        </div>
    		    </li>
    		    <li>
		
    		        <a href="#"><img src="img/lunbo3.jpg" class="randomSmart" /></a>
    		        <div class="label_text">
    		            <p></p>
		
    		        </div>
    		    </li>
    		</ul>
        </div>
        </center>
     </div>
  
</div>



 <div style="text-align: center">
 	<img src="img/poster2.jpg" border="0" usemap="#Map" />
    <map name="Map">
    	<area shape="rect" coords="0,30,310,140" href="dingche.php"/>
    	<area shape="rect" coords="311,30,609,140" href="zhaoche.php"/>
        <area shape="rect" coords="610,30,980,140" href="../eduo.apk"/>
    </map>
</div>

	<!--修改-->
	<div style="width:200px;height:300px;position:absolute;top:100px;left:20px;background:url(img/note1.png) no-repeat">
	<style type="text/css">
		.fon{
			font-family: '宋体', Simsun;" mce_style="font-family: '宋体', Simsun;
		}
	</style>
	<!--修改-->
	<script type="text/javascript">
	        function isToday(){
	                var myDate = new Date();                    //创建日期
	                var rs = myDate.getDay();                   //获取当前星期几
	                if (rs!=0 && rs!=6){                        //排除周六、日
	                    var fon = document.getElementById(rs);  //根据周几获取元素
	                    fon.style.color = "red";                //更改样式
	                }
	            }
	</script>
	<!--修改-->
	<br/><br/>
	<p style="text-align: center">北京地区7月7日-10月5日限行通告</p><br/>
	<p id='1'><font class="fon">&nbsp;&nbsp;&nbsp;&nbsp;周一&nbsp;&nbsp;&nbsp;限行车辆尾号：3，8</font></p>
	<p id='2'><font class="fon">&nbsp;&nbsp;&nbsp;&nbsp;周二&nbsp;&nbsp;&nbsp;限行车辆尾号：4，9</font></p>
	<p id='3'><font class="fon">&nbsp;&nbsp;&nbsp;&nbsp;周三&nbsp;&nbsp;&nbsp;限行车辆尾号：5，0</font></p>
	<p id='4'><font class="fon">&nbsp;&nbsp;&nbsp;&nbsp;周四&nbsp;&nbsp;&nbsp;限行车辆尾号：1，6</font></p>
	<p id='5'><font class="fon">&nbsp;&nbsp;&nbsp;&nbsp;周五&nbsp;&nbsp;&nbsp;限行车辆尾号：2，7</font></p>
	</div>
	<!--修改-->

	<div class="bottom" style="display: none">
		<!-- modify by yangbei 2013-4-8 替换cnzz网站统计 -->
		<!--<script src="http://s4.cnzz.com/stat.php?id=4799495&web_id=4799495" language="JavaScript"></script>-->
		<script src="http://s4.cnzz.com/stat.php?id=4799495&web_id=4799495&show=pic1" language="JavaScript"></script>
	</div>
	<div class="yewei">
		<div class="yewei-1">

			<a href="index.php"><img src="img/logo-2.jpg"/></a>

			<p class="yewei-1-1">
				<a href="http://www.eduoauto.com/guanyugongsi.php" class="anniu-7">关于我们</a>
				&nbsp;|&nbsp;
				<a href="http://www.eduoauto.com/lianxiwomen.php" class="anniu-7">联系我们</a>
				&nbsp;|&nbsp;
				<a href="http://www.eduoauto.com/xieyi.php" class="anniu-7">服务条款</a>
				<a href="#" class="anniu-7" style="display:none;">网站地图</a>
				<a href="#" class="anniu-7" style="display:none;">友情链接</a>
				<a href="#" class="anniu-7" style="display:none;">易多论坛</a>
			</p>
			<p class="yewei-1-1">Copyright &copy; 2013 Eduoauto.com Inc. All Rights Reserved. 易多新捷 版权所有 京ICP备13006080号</p>
		</div>
	</div>
</body>
</html>
<script type="text/javascript">
	window.onload=isToday();
</script>
