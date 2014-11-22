window.onload=function(){
	//左侧tab切换
	var oLeft_title=document.getElementById('left_title');
	var aLeft_title_li=oLeft_title.getElementsByTagName('li');
	var oRight_box_outer=document.getElementById('right_box_outer');
	var aRight_box=getByClass(oRight_box_outer,'right_box');
	var oldClass=[];
	
	for(var i=0;i<aLeft_title_li.length;i++){
		oldClass[i]=aLeft_title_li[i].className;
		aLeft_title_li[i].index=i;
		aLeft_title_li[i].onclick=function(){
			//alert(oldClass);
			for(var i=0;i<aLeft_title_li.length;i++){
				aLeft_title_li[i].className=oldClass[i];
				aRight_box[i].style.display='none';
			}
			this.className+=' active';
			aRight_box[this.index].style.display='block';
		};
	}
    aLeft_title_li[0].className+=' active';

    var oModify_personal_message = document.getElementById('modify_personal_message');
    var oCancel_btn = document.getElementById('cancel_btn');
    var oSave_btn = document.getElementById('save_btn');
    var oUser_center_info = document.getElementById('user_center_info');
    var oModify_message = document.getElementById('modify_message');
    var oPhonenum = document.getElementById('phonenum');
    var oCode = document.getElementById('code');
    var oSubmit_tel_code_btn = document.getElementById('submit_tel_code_btn');
    var oBind_btn = document.getElementById('bind_btn');
    oModify_personal_message.onclick=function(){
        oUser_center_info.style.display='none';
        oModify_message.style.display='block';
    };
    oCancel_btn.onclick=function(){
        oUser_center_info.style.display='block';
        oModify_message.style.display='none';
    };
    oSave_btn.onclick=function(){
        if(oPhonenum.value == '输入手机号'){
            oPhonenum.value = '';
        }
    };
    oSubmit_tel_code_btn.onclick=function(){
        ajax('http://www.leyingke.com/lykweb/user/phonenumvaild?phonenum='+oPhonenum.value,function(str){

            var j = eval('('+str+')');
            var oResult=j.result;
            if(oResult=='success'){
                alert('亲，验证码已发送~请用手机查看~');
            }
        });
    };
    oBind_btn.onclick=function(){
        ajax('http://www.leyingke.com/lykweb/user/phonebind?phonenum='+oPhonenum.value+'&code='+oCode.value,function(str){
            var j = eval('('+str+')');
            var oResult=j.result;
            if(oResult=='success'){
                alert('亲，绑定手机号成功~');
            }
        });
    };




	//手机验证码图层
	var oTel_btn_bg=document.getElementById('tel_btn_bg');
	var oModify_content_hidden=document.getElementById('modify_content_hidden');
	var bBtn=true;
	
	oTel_btn_bg.onclick=function(){
		if(bBtn){
			oModify_content_hidden.style.display='block';
			bBtn=false;
		}
		else{
			oModify_content_hidden.style.display='none';
			bBtn=true;
		}
	};

    //弹窗百度地图
    var aMovie_list_addr=getByClass(document,'movie_list_addr');

    var aCinema_list_lon=getByClass(document,'cinema_list_lon');
    var aCinema_list_lat=getByClass(document,'cinema_list_lat');
    var aCinema_list_name=getByClass(document,'cinema_list_name');
    var aCinema_list_tel=getByClass(document,'cinema_list_tel');
    var aCinema_list_addr=getByClass(document,'cinema_list_addr');
    var aCinema_list_logo=getByClass(document,'cinema_list_logo');

    for(var i=0;i<aMovie_list_addr.length;i++){

        aMovie_list_addr[i].index=i;
        aMovie_list_addr[i].onclick=function(){
            //可视区的宽
            var iViewWidth = document.documentElement.clientWidth;
            //可视区的高
            var iViewHeight = document.documentElement.clientHeight;
            //横向滚动条
            var iScrollLeft = document.documentElement.scrollLeft || document.body.scrollLeft;
            //纵向滚动条
            var iScrollTop = document.documentElement.scrollTop || document.body.scrollTop;
            //地图外部div
            var oMapDiv=document.createElement('div');
            oMapDiv.id = 'MapBoxDiv';
            document.body.appendChild(oMapDiv);
            oMapDiv.style.left = iScrollLeft + (iViewWidth - oMapDiv.offsetWidth) / 2 + 'px';
            oMapDiv.style.top = iScrollTop + (iViewHeight - oMapDiv.offsetHeight) / 2 + 'px';
            //创建遮罩层
            var oMapMask = document.createElement('div');
            oMapMask.id = 'MapMask';
            document.body.appendChild(oMapMask);

            oMapMask.style.width = Math.max(iViewWidth, document.body.offsetWidth) + 'px';
            oMapMask.style.height = Math.max(iViewHeight, document.body.offsetHeight) + 'px';
            //关闭按钮
            var oMapCloseDiv=document.createElement('div');
            var oMapClose = document.createElement('a');

            oMapCloseDiv.id='MapCloseDiv';
            oMapDiv.appendChild(oMapCloseDiv);

            oMapClose.id = 'MapClose';
            oMapClose.innerHTML = '×';
            oMapClose.href = 'javascript:;';
            oMapCloseDiv.appendChild(oMapClose);
            oMapClose.onclick = function() {

                document.body.removeChild(oMapMask);
                document.body.removeChild(oMapDiv);

            }
            //地图显示层
            var oMapShowDiv=document.createElement('div');

            oMapShowDiv.id='MapShowDiv';
            oMapDiv.appendChild(oMapShowDiv);

            function getCinemaListValue(obj){
                return obj.value;
            }

            var map = new BMap.Map("MapShowDiv");            // 创建Map实例
            var point = new BMap.Point(getCinemaListValue(aCinema_list_lon[this.index]), getCinemaListValue(aCinema_list_lat[this.index]));     // 创建点坐标
            map.centerAndZoom(point,15);                     // 初始化地图,设置中心点坐标和地图级别。
            map.enableScrollWheelZoom();
            var opts = {type: BMAP_NAVIGATION_CONTROL_LARGE}; //初始化地图控件
            map.addControl(new BMap.NavigationControl(opts));

            var marker = new BMap.Marker(point); //初始化地图标记
            var sContent =
                "<div style='width:360px; height:120px;'>"+
                    "<p style='float:left;margin:4px;text-align:center;vertical-align:middle;'>"+
                    "<img  id='imgDemo' src='"+getCinemaListValue(aCinema_list_logo[this.index])+"' width='139' height='104' title=''/>" +
                    "</p>"+
                    "<p style='float:right;text-align:center;'>"+
                    "<p style='margin:0 0 2px 0;padding:0.2em 0;font-size:12px; font-weight:bold; height:26px'>"+
                    getCinemaListValue(aCinema_list_name[this.index])+
                    "</p>" +
                    "<p style='margin:0;line-height:1.5;font-size:12px;width:330px; height:40px;'>地址："+
                    getCinemaListValue(aCinema_list_addr[this.index])+
                    "</p>"+
                    "<p style='margin:0;line-height:1.5;font-size:12px;width:330px; height:40px;'>电话："+
                    getCinemaListValue(aCinema_list_tel[this.index])+
                    "</p>" +
                    "</p>"+
                    "</div>"+
                    "</div>";

            var infoWindow = new BMap.InfoWindow(sContent);  // 创建信息窗口对象
            map.addOverlay(marker);
            marker.openInfoWindow(infoWindow);
            //图片加载完毕重绘infowindow
            document.getElementById('imgDemo').onload = function (){
                infoWindow.redraw();
            }
            window.onscroll = window.onresize = function() {
                //可视区的宽
                var iViewWidth = document.documentElement.clientWidth;
                //可视区的高
                var iViewHeight = document.documentElement.clientHeight;
                //横向滚动条
                var iScrollLeft = document.documentElement.scrollLeft || document.body.scrollLeft;
                //纵向滚动条
                var iScrollTop = document.documentElement.scrollTop || document.body.scrollTop;

                oMapMask.style.width = Math.max(iViewWidth, document.body.offsetWidth) + 'px';
                oMapMask.style.height = Math.max(iViewHeight, document.body.offsetHeight) + 'px';

                oMapDiv.style.left = iScrollLeft + (iViewWidth - oMapDiv.offsetWidth) / 2 + 'px';
                oMapDiv.style.top = iScrollTop + (iViewHeight - oMapDiv.offsetHeight) / 2 + 'px';
            }
        };

    }

    var oLike_btn=getByClass(document,'like_btn');
    var oDislike_btn=getByClass(document,'dislike_btn');

    var oCinemaID=getByClass(document,'cinema_id');
    var oLove=getByClass(document,'Love');

    for(var i=0;i<oLove.length;i++){
        if(oLove.value=='-1'){
            oLike_btn[i].style.backgroundImage='url(/static/web/images/attention_btn.png)';
            oDislike_btn[i].style.backgroundImage='url(/static/web/images/attentioned_btn_del.png)';
            oLike_btn[i].style.cursor='pointer';
            oDislike_btn[i].style.cursor='default';
        }else{
            oLike_btn[i].style.backgroundImage='url(/static/web/images/attentioned_btn.png)';
            oDislike_btn[i].style.backgroundImage='url(/static/web/images/attention_btn_del.png)';
            oLike_btn[i].style.cursor='default';
            oDislike_btn[i].style.cursor='pointer';
        }
        oLike_btn[i].index=i;
        oLike_btn[i].onclick=function(){
            if(oLove[this.index].value=='-1'){
                oLove[this.index].value='1';
                ajax('http://www.leyingke.com/lykweb/user/favorupdate.json?id='+getValueAjax(oCinemaID[this.index])+'&ftype=0&op=1&loveid='+this.index,function(str){

                    var j = eval('('+str+')');
                    var oResult=j.result;
                    var oLoveId=j.loveid;
                    if(oResult=='success'){
                        //改样式css红心
                        oLike_btn[oLoveId].style.backgroundImage='url(/static/web/images/attentioned_btn.png)';
                        oDislike_btn[oLoveId].style.backgroundImage='url(/static/web/images/attention_btn_del.png)';
                        oLike_btn[oLoveId].style.cursor='default';
                        oDislike_btn[oLoveId].style.cursor='pointer';
                    }
                    else if(oResult=='error01'){
                        //弹出登录
                        oMain_login.style.display='block';

                        oMain_login.style.left = (document.documentElement.clientWidth - oMain_login.offsetWidth)/2 + 'px';
                        oMain_login.style.top = (document.documentElement.clientHeight - oMain_login.offsetHeight)/2 + scrollY() + 'px';

                    }
                    else if(oResult=='error02'){
                        alert('关注失败');
                    }

                });
            }
        };
        oDislike_btn[i].index=i;
        oDislike_btn[i].onclick=function(){
            if(oLove[this.index].value=='1'){
                oLove[this.index].value='-1';
                ajax('http://www.leyingke.com/lykweb/user/favorupdate.json?id='+getValueAjax(oCinemaID[this.index])+'&ftype=0&op=-1&loveid='+this.index,function(str){

                    var j = eval('('+str+')');
                    var oResult=j.result;
                    var oLoveId=j.loveid;
                    if(oResult=='success'){
                        //改样式css红心
                        oLike_btn[oLoveId].style.backgroundImage='url(/static/web/images/attention_btn.png)';
                        oDislike_btn[oLoveId].style.backgroundImage='url(/static/web/images/attentioned_btn_del.png)';
                        oLike_btn[oLoveId].style.cursor='pointer';
                        oDislike_btn[oLoveId].style.cursor='default';
                    }
                    else if(oResult=='error01'){
                        //弹出登录
                        oMain_login.style.display='block';

                        oMain_login.style.left = (document.documentElement.clientWidth - oMain_login.offsetWidth)/2 + 'px';
                        oMain_login.style.top = (document.documentElement.clientHeight - oMain_login.offsetHeight)/2 + scrollY() + 'px';

                    }
                    else if(oResult=='error02'){
                        alert('取消失败');
                    }

                });
            }
        };
    }


    //预告片分页效果
    var oLove_cinema_ul=document.getElementById('love_cinemas');
    var aLove_cinema_ul_li=oLove_cinema_ul.getElementsByTagName('li');

    page({
        id : 'pageLove',
        nowNum : 1,
        allNum : 10,
        start:0,
        end:10
    },aLove_cinema_ul_li,4);


    function page(opt, objectlist,objPageCount){

        var oCountLi = objectlist.length;
        var PerCount = objPageCount;
        var TotalPage = 1;

        TotalPage=Math.ceil(oCountLi/PerCount);

        if(!opt.id){return false};

        var obj = document.getElementById(opt.id);
        obj.innerHTML = '';

        var nowNum = opt.nowNum || 1;
        var allNum = TotalPage;
        var start = (nowNum-1)*PerCount;
        var end = nowNum*PerCount;

        for(var i=0; i<oCountLi; i++){
            if(i>=start&&i<end){
                // 显示
                objectlist[i].style.display='block';
            }else{
                //不显示
                objectlist[i].style.display='none';
            }
        }

        var callBack = opt.callBack || function(){};

        if( nowNum>=4 && allNum>=6 ){

            var oA = document.createElement('a');
            oA.href = '#1';
            oA.innerHTML = '首页';
            obj.appendChild(oA);

        }

        if(nowNum>=2){
            var oA = document.createElement('a');
            oA.href = '#' + (nowNum - 1);
            oA.innerHTML = '< 前页';
            obj.appendChild(oA);
        }

        if(allNum<=5){
            for(var i=1;i<=allNum;i++){
                var oA = document.createElement('a');
                oA.href = '#' + i;
                if(nowNum == i){
                    oA.innerHTML = i;
                }
                else{
                    oA.innerHTML = '[' + i + ']' ;
                }
                obj.appendChild(oA);
            }
        }
        else{

            for(var i=1;i<=5;i++){
                var oA = document.createElement('a');


                if(nowNum == 1 || nowNum == 2){

                    oA.href = '#' + i;
                    if(nowNum == i){
                        oA.innerHTML = i;
                    }
                    else{
                        oA.innerHTML =  '[' + i + ']' ;
                    }

                }
                else if( (allNum - nowNum) == 0 || (allNum - nowNum) == 1 ){

                    oA.href = '#' + (allNum - 5 + i);

                    if((allNum - nowNum) == 0 && i==5){
                        oA.innerHTML = (allNum - 5 + i);
                    }
                    else if((allNum - nowNum) == 1 && i==4){
                        oA.innerHTML = (allNum - 5 + i);
                    }
                    else{
                        oA.innerHTML = '[' + (allNum - 5 + i) + ']';
                    }

                }
                else{
                    oA.href = '#' + (nowNum - 3 + i);

                    if(i==3){
                        oA.innerHTML = (nowNum - 3 + i);
                    }
                    else{
                        oA.innerHTML = '[' + (nowNum - 3 + i) + ']';
                    }
                }
                obj.appendChild(oA);

            }

        }

        if( (allNum - nowNum) >= 1 ){
            var oA = document.createElement('a');
            oA.href = '#' + (nowNum + 1);
            oA.innerHTML = '后页 >';
            obj.appendChild(oA);
        }

        if( (allNum - nowNum) >= 3 && allNum>=6 ){

            var oA = document.createElement('a');
            oA.href = '#' + allNum;
            oA.innerHTML = '尾页';
            obj.appendChild(oA);

        }

        callBack(nowNum,allNum);

        var aA = obj.getElementsByTagName('a');

        for(var i=0;i<aA.length;i++){

            aA[i].index=i;
            aA[i].onclick = function(){

                var nowNum = parseInt(this.getAttribute('href').substring(1));

                obj.innerHTML = '';

                page({

                    id : opt.id,
                    nowNum : nowNum,
                    allNum : allNum,
                    callBack : callBack

                }, objectlist, objPageCount);

                return false;

            };
        }

    }

    /*-------------------------------------- Header页面 -----------------------------------*/
    //城市列表下拉自定义滚动条
    var oCity_list_up_box_outer=document.getElementById('city_list_up_box_outer');
    var oCity_list_up_box=document.getElementById('city_list_up_box');
    var oCity_list_scroll_bar_outer=document.getElementById('city_list_scroll_bar_outer');
    var oCity_list_scroll_bar=document.getElementById('city_list_scroll_bar');

    ScrollBar(oCity_list_scroll_bar,oCity_list_scroll_bar_outer,oCity_list_up_box,oCity_list_up_box_outer);

    //点击显示登录页面
    var oSet_up_a=document.getElementById('set_up_a');
    var oMain_login=document.getElementById('main_login');
    var oLogin_del_btn=document.getElementById('login_del_btn');

    if(oSet_up_a){
        oSet_up_a.onclick = function(){
            oMain_login.style.display = 'block';

            oMain_login.style.left = (document.documentElement.clientWidth - oMain_login.offsetWidth)/2 + 'px';
            oMain_login.style.top = (document.documentElement.clientHeight - oMain_login.offsetHeight)/2 + scrollY() + 'px';

            //登录框语言提示
            var oLogin_name=document.getElementById('login_name');
            var oLogin_password=document.getElementById('login_password');

            Search(oLogin_name,'请输入您的账号');
            Search(oLogin_password,'请输入您的密码');


            //生成遮罩层
            var oPicMark = document.createElement('div');
            oPicMark.className = 'PicMark';
            document.body.appendChild(oPicMark);

            oPicMark.style.width = Math.max(viewWidth(), document.body.offsetWidth) + 'px';
            oPicMark.style.height = Math.max(viewHeight(), document.body.offsetHeight) + 'px';

            oLogin_del_btn.onclick=function(){
                oMain_login.style.display='none';

                //删除创建的遮罩层
                document.body.removeChild(oPicMark);
            };

            window.onscroll = window.onresize = function() {

                oPicMark.style.width = Math.max(viewWidth(), document.body.offsetWidth) + 'px';
                oPicMark.style.height = Math.max(viewHeight(), document.body.offsetHeight) + 'px';
            }

        };
    }



    //点击城市列表显示下拉菜单
    var oList=document.getElementById('list');
    var oList_icon=document.getElementById('list_icon');
    var sList_text=document.getElementById('list_text');
    var oCity_list_up=document.getElementById('city_list_up');
    var oCity_list_up_ul=document.getElementById('city_list_up_ul');
    var oCity_list_up_box=document.getElementById('city_list_up_box');
    var aCity_list_up_box_li=oCity_list_up_box.getElementsByTagName('li');

    var aCity_list_up_ol=getByClass(document,'city_list_up_ol');

    var bBtn_city_list=true;

    oList.onmouseover=function (ev){
        var oEvent=window.event||ev;

        oEvent.cancelBubble=true;

        if(oCity_list_up.style.visibility == 'hidden' || oCity_list_up.style.visibility=='' ){
            oList_icon.className='list_icon_up pa';
            oCity_list_up.style.visibility='visible';
        }
        else{
            oList_icon.className='list_icon pa';
            oCity_list_up.style.visibility='hidden';
        }

        for(var i=0;i<aCity_list_up_ol.length;i++){
            var aCity_list_up_ol_li=aCity_list_up_ol[i].getElementsByTagName('li');

            for(var j=0;j<aCity_list_up_ol_li.length;j++){
                aCity_list_up_ol_li[j].onmouseover=function(){
                    this.style.backgroundColor='#383731';
                }
                aCity_list_up_ol_li[j].onmouseout=function(){
                    this.style.backgroundColor='';
                }
            }
        }

    };

    oList.onmouseout=function (ev){
        oList_icon.className='list_icon pa';
        oCity_list_up.style.visibility='hidden';
    };

    //鼠标悬停显示用户中心设置
    var oUserCenterListUp=document.getElementById('UserCenterListUp');

    if(oUserCenterListUp){
        var aUserCenterListUpLi=oUserCenterListUp.getElementsByTagName('li');
        var oSet_up=document.getElementById('set_up');

        oSet_up.onmouseover=function(){
            oUserCenterListUp.style.display='block';

            for(var i=0;i<aUserCenterListUpLi.length;i++){
                aUserCenterListUpLi[i].onmouseover = function(){
                    this.style.backgroundColor='#383731';
                }
                aUserCenterListUpLi[i].onmouseout = function(){
                    this.style.backgroundColor='';
                }
            }

        }
        oSet_up.onmouseout=function(){
            oUserCenterListUp.style.display='none';
        }
    }
}
