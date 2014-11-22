
	<?php 
		// $jsPaths=array('../js/jquery.form.js');
		include 'header.html';
	?>

	<div class="neirong">

<?php  
	//初始化
    $op=selectOutput('ArrOutput','PublicClient');
    $ret=$op->output('twGetCarsInfo');
	$myCarList=$ret['content'];

	$tNow=time();
	$dFormat='m月d日';
	$date1=date($dFormat);
	$date2=date($dFormat,$tNow+86400);
	$date3=date($dFormat,$tNow+86400*2);

	$tDate1=date('Y-m-d');
	$tDate2=date('Y-m-d',$tNow+86400);
	$tDate3=date('Y-m-d',$tNow+86400*2);
 ?>
    <div class="guide" id="guide"  style="display:none; position: absolute; 
 	z-index:9999; 
 	left:0px;
 	top:100px;                                                                         
 	width:2000px;
 	height:1000px;
 	background-color: rgb(0,0,0);
 	filter: Alpha(Opacity=70);
 	opacity: 0.7;                                                                 
 	-moz-opacity:0.7;">
    	<li><img id="guide4060" src="img/guide4060.png" alt="请登录！" style="z-index:9999; position: relative; left:40%;"/></li>
    </div>


		<div class="neirong-1">
			<?php foreach ($myCarList as $v) { ?>
			<div class="dingche">
				<div class="test" style="width:451px; height:285px; float:left; margin:8px 0 0 12px">
					<span style="display:block;" class="dingche-1 animate <?php echo sprintf('time-line-%s',$v['id']) ?>
						"  >
						<img src="<?php echo sprintf('../../cp/%s',$v['icon']); ?>" alt="More about ea"/>
					</span>
					<span class="animating" style="display:none;width:451px; height:285px; float:left; margin:8px 0 0 12px;left:0px">
						loading...
					</span>
					<span  class="dingche-2 animate" style="display:none;">
						<p href="#" class="dl-bt-4">空闲</p>
						<p href="#" class="dl-bt-5">已被预订</p>
						<?php for ($i=0; $i < 3; $i++) { ?>
						<p class="gongche-denglu-4">
							<?php echo date('Y-m-d',$tNow+($i*86400)) ?></p>
						<p class="shijianzhou" id="<?php echo sprintf('car-%s-date-%s',$v['id'],$i)?>">
							<!-- <b class="sjz-1" style="width:80px; margin-left:20px;"></b>
						-->
					</p>
					<p class="shijianzhou-bj"> <b class="shijianzhou-bj-1"></b>
						<b class="shijianzhou-bj-1"></b>
						<b class="shijianzhou-bj-1"></b>
						<b class="shijianzhou-bj-1"></b>
						<b class="shijianzhou-bj-1"></b>
						<b class="shijianzhou-bj-1"></b>
						<b class="shijianzhou-bj-1"></b>
						<b class="shijianzhou-bj-1"></b>
					</p>
					<p class="shijianzhou-bj">
						<b class="shijianzhou-bj-1">0:00</b>
						<b class="shijianzhou-bj-1">3:00</b>
						<b class="shijianzhou-bj-1">6:00</b>
						<b class="shijianzhou-bj-1">9:00</b>
						<b class="shijianzhou-bj-1">12:00</b>
						<b class="shijianzhou-bj-1">15:00</b>
						<b class="shijianzhou-bj-1">18:00</b>
						<b class="shijianzhou-bj-1">21:00</b>
					</p>
					<?php } ?></span>

			</div>

			<div class="order-form" car-id='<?php echo $v['id'] ?>' car-number='<?php echo $v['number'] ?>'>
				<span class="qucheshijian date-time-selection get-date-time">
					<p class="gongche-denglu-3" style="font-weight:bold;">取车时间：</p>
					<a href="#" class="dl-bt-2 date-select 0" start-num="0" date="<?php echo $tDate1 ?>">
						<?php echo $date1 ?></a>
					<a href="#" class="dl-bt-2-1 date-select 1" start-num="1" date="<?php echo $tDate2 ?>">
						<?php echo $date2 ?></a>
					<a href="#" class="dl-bt-2-1 date-select 2" start-num="2" date="<?php echo $tDate3 ?>">
						<?php echo $date3 ?></a>
					<select name="" class="xiala time-select select-hour" style="text-align:center;"></select>
					<p class="gongche-denglu-3" >时</p>
					<select name="" class="xiala time-select select-min"></select>
					<p class="gongche-denglu-3" >分</p>
					<input type="text" value="<?php echo date('Y-m-d H:i',$tNow);?>" name='start_time' style="display:none;"></span>
				<span class="qucheshijian date-time-selection back-date-time">
					<p class="gongche-denglu-3" style="font-weight:bold;">还车时间：</p>
					<a href="#" class="dl-bt-2 date-select 0" end-num="0" date="<?php echo $tDate1 ?>">
						<?php echo $date1 ?></a>
					<a href="#" class="dl-bt-2-1 date-select 1" end-num="1" date="<?php echo $tDate2 ?>">
						<?php echo $date2 ?></a>
					<a href="#" class="dl-bt-2-1 date-select 2" end-num="3" date="<?php echo $tDate3 ?>">
						<?php echo $date3 ?></a>

					<select name="" class="xiala time-select select-hour"></select>
					<p class="gongche-denglu-3" >时</p>
					<select name="" class="xiala time-select select-min"></select>
					<p class="gongche-denglu-3" >分</p>
					<input type="text" value="<?php echo date('Y-m-d H:i',$tNow);?>" name='end_time' style="display:none;"></span>
				<a href="#" class="dl-bt-3 submi-order-click">预订</a>
			</div>
		</div>
		<?php } ?>
	</div>

</div>
<div id="msg-box" style="display:none;z-index:103">

</div>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.flip.min.js"></script>


	<script id="sureTmp" type="text/template">
		<div class="tanchu-dd">
			<span class="yuyue-1" style="width:580px;">预订确认</span>
			<a href="#" class="guanbi close-msg-window"></a>
			<p class="tanchu-dd-1">请确认以下信息后提交预订。</p>
			<span class="tanchu-2" style="height:170px;">
				<p class="tanchu-huizi" style="width:540px; height:30px; margin:20px 0 0 15px; padding-left:10px;border-bottom:1px solid #ddd;">
					驾驶用户姓名： <b style=" font-weight:bold;s">{#user_name#}</b>
				</p>
				<p class="tanchu-huizi" style="width:240px; margin:12px 0 0 15px; padding-left:10px;">取车时间：{#start_time#}</p>
				<p class="tanchu-huizi" style="width:240px; margin:12px 0 0 15px; padding-left:10px;">还车时间：{#end_time#}</p>
				<p class="tanchu-huizi" style="width:240px; margin:12px 280px 0 15px; padding-left:10px;">车&nbsp&nbsp牌&nbsp号：{#car_number#}</p>
				<p class="tanchu-huizi" style="width:55px; margin:15px 0 0 15px; padding-left:10px;">用&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;途&nbsp;：</p>
				<input name="reason" type="text"  class="gongche-dl-sr" id='use-car-reason'  style=" width:380px; height:18px;margin:8px 0 0 0;"/>	
			</span>
			<a href="#" class="dl-bt-1 create-order" style=" margin:15px 0 0 210px;" >确认</a>
			<a href="#" class="dl-bt-1 close-msg-window" style=" margin:15px 0 0 10px;">取消</a>
		</div>
	</script>
	<script id='msgTmp' type="text/template" >
		<span class="tanchu-2" style="margin-left:200px; z-index:300;">
			<p class="tanchu-dd-1" style=" width:500px;margin-top:45px;text-align:center;">{#msg#}</p>
			<a href="#" class="dl-bt-1 close-msg-window" style=" margin:35px 0 0 230px;">返回</a>
		</span>
	</script>
</body>
<script  type="text/javascript">
isClick=false;
<?php
	//封装车辆信息结构,以复用时间轴添加函数
		$jsonCarTime=array();
		$carList=array(); 
 		foreach ($myCarList as $car) {
 				$jsonCarTime[$car['id']] = $car['orderTime'];
 				$carList[$car['id']]=$car;
		}
 ?>
// var timeObj=<?php echo json_encode($jsonCarTime); ?>;
var carList=<?php  echo json_encode($carList);?>

$(document).ready()
{
	var orderObj=new Object();
	var timeLineTools=new TimeLineClass();
	timeLineTools.defaultData.style='sjz-1';
	appendSelectMin();
	appendSelectHour();
	//强制执行一次时间选择,使得提交订单的初始状态的时间为时间选择器默认选择的时间
	indexPageInit();
	for (var i = 0; i < $('.date-select').length; i+=3) {
		$('.date-select')[i].click();
	};
}

//初始化公用按钮的绑定
function butttonInit()
{
	closeWindowClick();
	reflashClickButton();

}
function indexPageInit()
{
	initTimeLine();
	animateInit();
	
	// showAllCarIcon();
	selectDateTimeInit($('.date-time-selection'));
	orderSubmit($('.order-form'));

	//动画效果初始化函数
	function animateInit()
	{

		$('.animate').live('click',function()
		{
			//显示处理,假如是dingche-1页面时,showAllCarIcon就已经翻转了图片,不需要再翻转
			var me = this;
			showAllCarIcon();
			if ($(me).hasClass('dingche-1'))
			{
				exchangeShowStat($(me));
			}
			
		});

	}
	
	//表单提交初始化
	function orderSubmit(jqForm)
	{
		var submiter=jqForm.find('.submi-order-click');
		submiter.live('click',function(e){
			var jqForm=$(this).parent();
			var carId=jqForm.attr('car-id');
			var carNumber=jqForm.attr('car-number');

			var param='';
			var inputs=jqForm.find('input');
			for (var i = 0; i < inputs.length; i++) {
				var jqInput=$(inputs[i]);
				param+=jqInput.attr('name')+'='+jqInput.val()+'&';
				eval('orderObj.'+jqInput.attr('name')+'="'+jqInput.val()+'"');
			};
			orderObj.carId=carId;
			orderObj.carNumber=carNumber;

			param+='car_id='+carId;
			orderObj.param=param;

			$.post(rootApi+'client/http/member.php?item=calculateDeposit',param,submitCallback);
		});
		//登陆回调函数
		function submitCallback(data)
		{
			var ret = decodeJson(data);
			if (ret.errno!=2000)
			{
				if(ret.errno==4060)
				{
					showGuide(ret.errno);
				}
                else
                {
                	showMsg(ret.errstr);
                }
				
			}else
			{
				sureMsg();
			}
		}

		function showGuide(errno)
		{   
            var guide="guide"+errno;
			var guideHolder=document.getElementById("guide");
			var guideNo=document.getElementById(guide);
			var h = $(document).height();
	        var w = $(document).width();
	         $(this).width();
	        //alert(h+"+"+w);
			if(guideHolder!=null)
			{
			//alert(guideHolder);
		    guideHolder.style.width=w+"px";
		    guideHolder.style.height=h-100+"px";
			guideHolder.style.display="";
			guideNo.style.display="";
			isClick=true;
            //alert(guideHolder.style.width+"+"+guideHolder.style.height);
		    //if(guideNo!=null)
		    	//alert("你好");
		    //guideNo.style.display="";
		       //alert("eeeee");
		    $('.yetou').click(function(){$('#'+guide).hide();isClick=false;});   
			$('.yetou').click(function(){$('#guide').hide();isClick=false;});
		    }

		
			$(window).resize(function(){
			//alert(isClick);
			if(isClick)
			{
				var w = $(this).width();
            	var h = $(document).height();
            	//w = $(this).width();
            	//h = $(this).height();
	 
            	var guide="guide"+errno;
				var guideHolder=document.getElementById("guide");
				var guideNo=document.getElementById(guide);
				//var h = $(document).height();
	        	//var w = $(document).width();
	        	 $(this).width();
	        	//alert(h+"+"+w);
				if(guideHolder!=null)
				{
				//alert(guideHolder);
		    	guideHolder.style.width=w+"px";
		    	guideHolder.style.height=h-100+"px";
				guideHolder.style.display="";
				guideNo.style.display="";
            	//alert(guideHolder.style.width+"+"+guideHolder.style.height);
		    	//if(guideNo!=null)
		    		//alert("你好");
		    	//guideNo.style.display="";
		    	   //alert("eeeee");
		    	//$('.yetou').click(function(){$('#'+guide).hide()});   
				//$('.yetou').click(function(){$('#guide').hide()});
		        }
		    }
            
        });

			
		}
        

		function sureMsg()
		{
			//初始化msg-box容器
			$('#msg-box').html('');
			var msgBox=$('#msg-box');
			var html=$('#sureTmp').html();

			html=html.replace(/\{\#user_name\#\}/g,userName);
			html=html.replace(/\{\#start_time\#\}/g,orderObj.start_time);
			html=html.replace(/\{\#end_time\#\}/g,orderObj.end_time);
			html=html.replace(/\{\#car_number\#\}/g,orderObj.carNumber);

			msgBox.append(html);
			msgBox.center();
			msgBox.fadeIn();
			closeWindowClick();
		}

		//create order绑定提交订单操作
		(function(jqObj){
			jqObj.live('click',function(){
				var param=orderObj.param;
				var reason=$('#use-car-reason').val();
				param+='&reason='+reason;
				$.post(rootApi+'client/http/member.php?item=twPayforOrder',param,function(data){
					var ret = decodeJson(data);
					if (ret.errno!=2000)
					{
						showMsg(ret.errstr);
					}else
					{
						//刷新时间轴时间信息
						var carInfo=ret.content.carInfo;
						//清除时间轴信息,再重新添加
						$('.time-line-'+carInfo.id).parent().find('.sjz-1').remove();//清除
						//添加
						for (var i = 0; i < carInfo.orderTime.length; i++) {
							timeLineTools.addTimeLine('car-'+carInfo.id+'-date-'+i,carInfo.orderTime[i]);
						};

						showMsg('预订已提交成功，请按时取车');
						// showMsg('预订已提交成功，请按时取车！');

					}
				});
			})
		})($('.create-order'));
	}

	//时间轴显示,为获取时间轴的长度,时间轴页面初始状态必须是显示,读取完时间轴后再用showAllCarIcon翻转成车辆图片信息
	function initTimeLine()
	{		
		for(i in carList)
		{
			for (var ii = 0; ii < carList[i].orderTime.length; ii++) {
				timeLineTools.addTimeLine('car-'+i+'-date-'+ii,carList[i].orderTime[ii]);
			};
			
		}

		
	}

	//日期时间选择初始化
	function selectDateTimeInit(jqSelectionObj)
	{
		var selectClass='dl-bt-2';
		var unSelectClass='dl-bt-2-1';
		var dateSelecter=jqSelectionObj.find('.date-select');
		var timeSelecter=jqSelectionObj.find('.time-select');

		// selecter.click(function(){
		// 	alert('sss');
		// });

		dateSelecter.live('click',function(e){
			var conterParent=$(this).parent();
			//选择按钮显示效果
			if (!$(this).hasClass(selectClass))
			{
				
				//去除已选择按钮效果
				var selectedButton=conterParent.find('.'+selectClass);
				selectedButton.removeClass(selectClass);
				selectedButton.addClass(unSelectClass);

				//显示选中按钮的效果
				var selectButton=$(this);
				selectButton.removeClass(unSelectClass);
				selectButton.addClass(selectClass);

				//关联还车时间选中
				if(conterParent.hasClass('get-date-time'))
				{
					var startNum=selectButton.attr('start-num');

					var endDateSelector=conterParent.siblings('.back-date-time');
					var backDateButton=endDateSelector.find('.'+selectClass);
					var endNum=backDateButton.attr('end-num');
					if (startNum>endNum)
					{
						endDateSelector.find('.'+startNum).click();
					};
				}
				else
				{
					var endNum=selectButton.attr('end-num');

					var startDateSelector=conterParent.siblings('.get-date-time');
					var startDateButton=startDateSelector.find('.'+selectClass);
					var startNum=startDateButton.attr('start-num');
					if (startNum>endNum)
					{
						startDateSelector.find('.'+endNum).click();
					};
				}
				
			};

			//更新选择的时间
			updateDateTime(conterParent);
		});

		timeSelecter.bind('change',function(e){

			var conterParent=$(this).parent();
			updateDateTime(conterParent);
		}).live('change',function(){
			var conterParent=$(this).parent();
			updateDateTime(conterParent);
		}).live('click',function(){
			if ($.data(this, 'events') == null || $.data(this, 'events').change == undefined)                     {
                $(this).bind('change', function () {
                	var conterParent=$(this).parent();
					updateDateTime(conterParent);
                });
            }
		})

		function updateDateTime(jqFormObj)
		{
			var dateTime=synthesizeDateTime(jqFormObj);
			// alert(dateTime);
			jqFormObj.find('input').val(dateTime);
		}

		function synthesizeDateTime(jqFormObj)
		{
			var selectDate=jqFormObj.find('.'+selectClass).attr('date');
			var selectTime=trimStr(jqFormObj.find('.select-hour').val())+':'+trimStr(jqFormObj.find('.select-min').val());
			var dateTime=selectDate+' '+selectTime;
			return dateTime;
		}

		function trimStr(str)
		{
			return str.replace(/[&nbsp]/g,'');
		}
	}

	
}
//切换订车时间条信息和图片信息
function exchangeShowStat(jqAnimateObj)
{
	var conterParent=jqAnimateObj.parent();
	// var a
	conterParent.find('.animate').hide(0,function(){
		conterParent.find('.animating').show();
   		// alert("Animation Done.");
 	});
 	// alert('ss');
 	conterParent.find('.animating').flip({
		direction:'rl',
		speed: 150,
		onBefore: function(){
			// Insert the contents of the .sponsorData div (hidden from view with display:none)
			// into the clicked .sponsorFlip div before the flipping animation starts:
			
			// elem.html(elem.siblings('.sponsorData').html());
		},
		onEnd:function(){

			onFinish();
		},
		color:'#FFF'
	});
 	

	conterParent.find('.animating').hide();

	function onFinish()
	{	
		if (jqAnimateObj.hasClass('dingche-1'))
		{
				conterParent.find('.dingche-2').show();
			// $('.dingche-1').hide();
		}
		else
		{
				conterParent.find('.dingche-1').show();
		}
	}
}

function showAllCarIcon()
{
	var allAnimate=$('.animate');

	for (var i = 0; i < allAnimate.length; i++)
	{
		var jqAnim=$(allAnimate[i]);
		//如果有显示订车2时间条,则把订车2隐藏,显示订车1
		if (jqAnim.hasClass('dingche-2')&&jqAnim.css('display')=='block')
		{
			exchangeShowStat(jqAnim);
		};
		
	}
}
function getHashCode()
{
	hash = window.location.hash;
	if (hash.length>1)
	{	
		//parseInt()hash=='car'
		hash=hash.replace('#','')
	}
	return hash;
}

function appendSelectMin()
{
	var selectMin=$('.select-min');
	var dateNow=new Date();

	for (var i = 0; i < 6; i++) {
		var opt='<option '+getNowMin(i,dateNow)+' value="'+i*10+'">'+'&nbsp&nbsp'+i*10+'</option>';
		selectMin.append(opt);
	};

	function getNowMin(i,now){
		var minite = dateNow.getMinutes();
		if (i*1==Math.ceil(minite/10))
		{
			return 'selected';
		}
		else
		{
			return;
		}

	}
}
function appendSelectHour()
{
	var selectHour=$('.select-hour');
	var dateNow=new Date();

	for (var i = 0; i < 24; i++) {
		var opt='<option ' +getNowHour(i,dateNow)+ ' value="'+i+'">'+'&nbsp&nbsp'+i+'</option>';
		selectHour.append(opt);
	};

	function getNowHour(i,now){
		var hour = dateNow.getHours();
		if (i*1==hour)
		{
			return 'selected';
		}
		else
		{
			return;
		}

	}
}
function showMsgWithFlash(msgStr,url)
{
	//初始化msg-box容器
	$('#msg-box').html('');
	var msgBox=$('#msg-box');
	var html=$('#msgTmp').html();

	html=html.replace(/\{\#msg\#\}/g,msgStr);
	if (url==null||url==undefined)
	{
		url='#';
	}
	html=html.replace(/\{\#url\#\}/g,url);
	//处理阴影层
	var h = $(document).height();
	var w = $(document).width();
	html=html.replace(/\{\#height\#\}/g,h);
	html=html.replace(/\{\#width\#\}/g,w);	
	msgBox.append(html);
	msgBox.center();
	msgBox.fadeIn();
	closeWindowClick();
}
function getCookie(name){ 
     var strCookie=document.cookie; 
     var arrCookie=strCookie.split("; "); 
     for(var i=0;i<arrCookie.length;i++){ 
           var arr=arrCookie[i].split("="); 
           if(arr[0]==name)return arr[1]; 
     } 
     return ""; 
} 
</script>
</html>