window.onload=function(){
	//搜索框提示文字
	var oSearch_input=document.getElementById('search_input');
	oSearch_input.value='影院索引';
	
	Search(oSearch_input,'影院索引');
	
	//单选框
	var oCinema_search_checked=document.getElementById('cinema_search_checked');
	var aCheckedEm=oCinema_search_checked.getElementsByTagName('em');

	var aMovie_list_IMAX=getByClass(document,'movie_list_IMAX');
	var oMovie_list_left_ul=document.getElementById('movie_list_left_ul');
	var bBtn_checkde=true;
	
	var oCinema_search_btn=document.getElementById('cinema_search_btn');
	var aMovie_list_left_ul_li=oMovie_list_left_ul.getElementsByTagName('li');
	
	var aParking_space=getByClass(document,'parking_space');
	var aMovie_list_IMAX=getByClass(document,'movie_list_IMAX');
	
	var parkingstatus = false;
	var imaxstatus = false;
	
		
	for(var i=0;i<aCheckedEm.length;i++){
		aCheckedEm[i].index=i;
		aCheckedEm[i].onclick=function(){
			var sSearch_input=oSearch_input.value;
			var sValueInTxt=sSearch_input.toLowerCase();
			var arr=sValueInTxt.split(' ');
			if(this.index==0){
				if(parkingstatus){
					parkingstatus = false;
					aCheckedEm[this.index].style.backgroundPosition='-45px -63px';
				}else{
					parkingstatus = true;
					aCheckedEm[this.index].style.backgroundPosition = '-66px -63px';
				}
				
			}else{
				if(imaxstatus){
					imaxstatus = false;
					aCheckedEm[this.index].style.backgroundPosition='-45px -63px';
				}else{
					imaxstatus = true;
					aCheckedEm[this.index].style.backgroundPosition = '-66px -63px';
				}
			}
			for(var j=0;j<aMovie_list_left_ul_li.length;j++){
				
				var parkem = getByClass(aMovie_list_left_ul_li[j],'parking_space')
				var imaxem = getByClass(aMovie_list_left_ul_li[j],'movie_list_IMAX')
				var namediv = getByClass(aMovie_list_left_ul_li[j],'movie_list_left_name fl')
				var sValueInUl=namediv[0].innerHTML.toLowerCase();
				var bFound=false;
				for(var jj=0;jj<arr.length;jj++){
					if(sValueInUl.search(arr[jj])!=-1){
						bFound=true;
						break;
					}
				}
				if(sValueInTxt=='影院索引'){
					bFound=true;
				}
				
				if(parkingstatus && imaxstatus){
					if(parkem.length==1 && imaxem.length==1 && bFound){
						aMovie_list_left_ul_li[j].style.display='block';
					}else{
						aMovie_list_left_ul_li[j].style.display='none';		
					}
				}
				else if(parkingstatus){
					if(parkem.length==1 && bFound){
						aMovie_list_left_ul_li[j].style.display='block';
					}else{
						aMovie_list_left_ul_li[j].style.display='none';		
					}
				}else if(imaxstatus){
					if(imaxem.length==1 && bFound){
						aMovie_list_left_ul_li[j].style.display='block';
					}else{
						aMovie_list_left_ul_li[j].style.display='none';		
					}
				}else{
					if(bFound){
						aMovie_list_left_ul_li[j].style.display='block';	
					}else{
						aMovie_list_left_ul_li[j].style.display='none';	
					}
				}
			}
			
			//oPage.style.display='none';
			
			var aMovie_list_left_ul_li2=[];
			
			for(var i=0;i<aMovie_list_left_ul_li.length;i++){
				if(aMovie_list_left_ul_li[i].style.display=='block'){
					aMovie_list_left_ul_li2.push(aMovie_list_left_ul_li[i]);
				}
			}
			
			page({
				id : 'page',
				nowNum : 1,
				allNum : 10,
				start:0,
				end:10
			}, aMovie_list_left_ul_li2);
			
		};
	}
	
	
	//点击影片场次数量下拉列表显示
	var aMovie_list_left_num2=getByClass(document,'movie_list_left_num2');
	var aMovie_list_left_onclick=getByClass(document,'movie_list_left_onclick');
	var aArrow_up=getByClass(document,'arrow_up');
	var bBtn=true;
	
	for(var i=0;i<aMovie_list_left_num2.length;i++){
		
		aMovie_list_left_num2[i].index=i;
		aMovie_list_left_num2[i].onclick=function(){
			
			if(bBtn){
				aMovie_list_left_onclick[this.index].style.display='block';
				aArrow_up[this.index].style.display='block';
				bBtn=false;
			}
			else{
				aMovie_list_left_onclick[this.index].style.display='none';
				aArrow_up[this.index].style.display='none';
				bBtn=true;
			}
			
		};
		
	}

	//更多热门影片弹窗
	var oHot_movie_tle=document.getElementById('hot_movie_tle');
	var oHot_movie_tle_A=oHot_movie_tle.getElementsByTagName('a')[0];
	
	MoreMovieWindow(oHot_movie_tle_A);
	
	
	//更多影片自定义滚动条(正在热映)
	var oHot_movie_content=document.getElementById('hot_movie_content');
	var oHot_movie_tab_box=document.getElementById('hot_movie_tab_box');
	var oDefine_scroll_bar_outer=document.getElementById('define_scroll_bar_outer');
	var oDefine_scroll_bar=document.getElementById('define_scroll_bar');
	var disY = 0;

	ScrollBar(oDefine_scroll_bar,oDefine_scroll_bar_outer,oHot_movie_tab_box,oHot_movie_content);
	
	//更多影片自定义滚动条(即将上映)
	var oHot_movie_content2=document.getElementById('hot_movie_content2');
	var oHot_movie_tab_box2=document.getElementById('hot_movie_tab_box2');
	var oDefine_scroll_bar_outer2=document.getElementById('define_scroll_bar_outer2');
	var oDefine_scroll_bar2=document.getElementById('define_scroll_bar2');

	//更多影片tab切换
	var oHot_movie_tab_title=document.getElementById('hot_movie_tab_title');
	var aHot_movie_tab_title_ul_li=oHot_movie_tab_title.getElementsByTagName('li');
	var aPop_window_tab_box=getByClass(document,'pop_window_tab_box');

    for(var i=0;i<aHot_movie_tab_title_ul_li.length;i++){

        aHot_movie_tab_title_ul_li[i].index=i;
        aHot_movie_tab_title_ul_li[i].onclick=function(){
            for(var i=0;i<aHot_movie_tab_title_ul_li.length;i++){
                aHot_movie_tab_title_ul_li[i].className='';
                aPop_window_tab_box[i].style.display='none';
            }
            this.className='active';
            aPop_window_tab_box[this.index].style.display='block';
            if(aPop_window_tab_box[1].style.display=='block'){
                ScrollBar(oDefine_scroll_bar2,oDefine_scroll_bar_outer2,oHot_movie_tab_box2,oHot_movie_content2);
            }
        };
    }
	
	//排序
	var oCinema_btn=document.getElementById('cinema_btn');
	var oTotal_btn=document.getElementById('total_btn');
	var oMovie_list_left_ul=document.getElementById('movie_list_left_ul');
	var oCinema_btn_em=document.getElementById('cinema_btn_em');
	var oCinema_btn_em2=document.getElementById('cinema_btn_em2');
	var bBtn=true;
	
	oCinema_btn.order='none';
	oCinema_btn.onclick=function(){
		
		sortByCinema();
		if(bBtn){
			oCinema_btn_em.style.backgroundPosition='-32px -120px';
			bBtn=false;
		}
		else{
			oCinema_btn_em.style.backgroundPosition='-24px -120px';
			bBtn=true;
		}
		
	};
	
	oTotal_btn.order='none';
	oTotal_btn.onclick=function(){
		sortByTotal();
		if(bBtn){
			oCinema_btn_em2.style.backgroundPosition='-32px -120px';
			bBtn=false;
		}
		else{
			oCinema_btn_em2.style.backgroundPosition='-24px -120px';
			bBtn=true;
		}
	};
	
	function sortUl(fnCmp){
		var aCinemaTotalDiv=getByClass(document,'movie_list_left_num1');
		var aCinemaTotalDivForSort=[];
		var oFragment=document.createDocumentFragment();
		var i=0;
		
		for(i=0;i<aCinemaTotalDiv.length;i++){
			aCinemaTotalDivForSort.push(aCinemaTotalDiv[i]);
		}
		
		aCinemaTotalDivForSort.sort(fnCmp);

		for(i=0;i<aCinemaTotalDivForSort.length;i++){
			oFragment.appendChild(aCinemaTotalDivForSort[i].parentNode.parentNode);
		}
		
		oMovie_list_left_ul.appendChild(oFragment);
	}
	
	function sortByCinema(){

		var result=1;
		
		switch(oCinema_btn.order){
			case 'none':
			case 'asc':
				oCinema_btn.order='desc';
				result=1;
				break;
			case 'desc':
				oCinema_btn.order='asc';
				result=-1;
				break;
		}
		
		sortUl(
			function (vRow1, vRow2){
				var aCinemaTotalDiv1=vRow1.getElementsByTagName('span')[0].innerHTML;
				var aCinemaTotalDiv2=vRow2.getElementsByTagName('span')[0].innerHTML;

                if(aCinemaTotalDiv1=='&nbsp;'){
					aCinemaTotalDiv1=0;
				}
				if(aCinemaTotalDiv2=='&nbsp;'){
					aCinemaTotalDiv2=0;
				}
				
				var fCinemaTotalDiv1=parseFloat(aCinemaTotalDiv1);
				var fCinemaTotalDiv2=parseFloat(aCinemaTotalDiv2);
				
				if(fCinemaTotalDiv1<fCinemaTotalDiv2){
					
					return result;
				}
				else if(fCinemaTotalDiv1>fCinemaTotalDiv2){
					
					return -result;
				}
				else{
					return 0;
				}
			}
		);
        var aMovie_list_left_li=getByClass(document,'movie_list_left_li');

        page({
            id : 'page',
            nowNum : 1,
            allNum : 10,
            start:0,
            end:10
        }, aMovie_list_left_li);
	}
	
	function sortUlTotal(fnCmp){
		var aCinemaTotalDiv=getByClass(document,'movie_list_left_num3');
		var aCinemaTotalDivForSort=[];
		var oFragment=document.createDocumentFragment();
		var i=0;
		
		for(i=0;i<aCinemaTotalDiv.length;i++){
			aCinemaTotalDivForSort.push(aCinemaTotalDiv[i]);
		}
		
		aCinemaTotalDivForSort.sort(fnCmp);

		for(i=0;i<aCinemaTotalDivForSort.length;i++){
			oFragment.appendChild(aCinemaTotalDivForSort[i].parentNode.parentNode);
		}
		
		oMovie_list_left_ul.appendChild(oFragment);
	}
	
	function sortByTotal(){

		var result=1;
		
		switch(oTotal_btn.order){
			case 'none':
			case 'asc':
				oTotal_btn.order='desc';
				result=1;
				break;
			case 'desc':
				oTotal_btn.order='asc';
				result=-1;
				break;
		}
		
		sortUlTotal(
			function (vRow1, vRow2){
				var aTotalDiv1=vRow1.getElementsByTagName('span')[0].innerHTML;
				var aTotalDiv2=vRow2.getElementsByTagName('span')[0].innerHTML;
				
				var fTotal1=parseFloat(aTotalDiv1);
				var fTotal2=parseFloat(aTotalDiv2);
				
				if(fTotal1<fTotal2){
					return result;
				}
				else if(fTotal1>fTotal2){
					return -result;
				}
				else{
					return 0;
				}
			}
		);
        var aMovie_list_left_li=getByClass(document,'movie_list_left_li');

        page({
            id : 'page',
            nowNum : 1,
            allNum : 10,
            start:0,
            end:10
        }, aMovie_list_left_li);
	}
	
	//城区下拉菜单
	var oCin_area_list_up_btn=document.getElementById('cin_area_list_up_btn');
	var sCin_location=document.getElementById('cin_location');
	var oCin_area_list_up=document.getElementById('cin_area_list_up');
	var oArea_list_up_ol=document.getElementById('area_list_up_ol');
	var oCin_area_list_up_ol=document.getElementById('cin_area_list_up_ol');
	var aArea_list_up_ol_li=oCin_area_list_up_ol.getElementsByTagName('li');
	
	var bBtn_city_list=true;
	
	oCin_area_list_up_btn.onmousedown=function (ev){
		var oEvent=window.event||ev;
		
		oEvent.cancelBubble=true;
		
		if(oCin_area_list_up.style.display === 'none'){
			oCin_area_list_up.style.display='block';
		}
		else{
			oCin_area_list_up.style.display='none';
		}
	};
	
	document.onmousedown=function (){
		oCin_area_list_up.style.display='none';
	};
	
	setDefaultText(sCin_location, '全部', '#000', 'black');
	
	setDropDown2(
	 	oCin_area_list_up,
		function (sText){
			defaultTextSetText(sCin_location, sText);
		}
		
	);
	
	function setDropDown2(oUl, fnOnItemSelect){
		var aLis=oUl.getElementsByTagName('li');
		var i;
		
		g_aUl.push(oUl);
		g_aFnOnItemSelect.push(fnOnItemSelect);
		
		oUl.style.display='none';
		
		for(i=0;i<aLis.length;i++){
			aLis[i].index=g_iDropDownMaxIndex;
			aLis[i].onmousedown=__dropDownOnMouseDownHandler2__;
			aLis[i].onmouseover=__dropDownOnMouseOverHandler__;
			aLis[i].onmouseout=__dropDownOnMouseOutHandler__;
		}
		
		g_iDropDownMaxIndex++;
	}
	
	function __dropDownOnMouseDownHandler2__(ev){
		
		g_aFnOnItemSelect[this.index](this.innerHTML);	
		g_aUl[this.index].style.display='none';
		
		for(var j=0;j<aMovie_list_left_ul_li.length;j++){
			
			var aMovie_list_left_addr = getByClass(aMovie_list_left_ul_li[j],'movie_list_left_addr fr');
			
			if(sCin_location.value==aMovie_list_left_addr[0].innerHTML){
				
				aMovie_list_left_ul_li[j].style.display='block';
				
			}
			else if(sCin_location.value=='全部'){
				aMovie_list_left_ul_li[j].style.display='block';
			}else{
				
				aMovie_list_left_ul_li[j].style.display='none';	
				
			}
		}
		

        var aMovie_list_left_ul_li2=[];
        
        for(var i=0;i<aMovie_list_left_ul_li.length;i++){
            if(aMovie_list_left_ul_li[i].style.display=='block'){
                aMovie_list_left_ul_li2.push(aMovie_list_left_ul_li[i]);
            }
        }
        
        page({
            id : 'page',
            nowNum : 1,
            allNum : 10,
            start:0,
            end:10
        }, aMovie_list_left_ul_li2);
		
	}	
	
	
	//分页效果
	var aMovie_list_left_li=getByClass(document,'movie_list_left_li');

	page({
		id : 'page',
		nowNum : 1,
		allNum : 10,
		start:0,
		end:10
	}, aMovie_list_left_li);

	function page(opt, objectlist){

        var oCountLi = objectlist.length;
        var PerCount = 12;
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
				
				}, objectlist);
				
				return false;
				
			};
		}
	
	}

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
	
	//影院列表搜索
	var oPage=document.getElementById('page');
	
	oCinema_search_btn.onclick=function(){
		
		var sSearch_input=oSearch_input.value;
		for(var i=0;i<aMovie_list_left_ul_li.length;i++){
			
			var parkem = getByClass(aMovie_list_left_ul_li[i],'parking_space')
			var imaxem = getByClass(aMovie_list_left_ul_li[i],'movie_list_IMAX')
			var namediv = getByClass(aMovie_list_left_ul_li[i],'movie_list_left_name fl')
			
			aMovie_list_left_ul_li[i].index=i;
			
			var sValueInUl=namediv[0].innerHTML.toLowerCase();
			var sValueInTxt=sSearch_input.toLowerCase();
			var arr=sValueInTxt.split(' ');
			
			var bFound=false;
			
			for(var j=0;j<arr.length;j++){
				if(sValueInUl.search(arr[j])!=-1){
					bFound=true;
					break;
				}
			}
			if(sValueInTxt=='影院索引' || sValueInTxt==' '){
				bFound=true;
				page({
					id : 'page',
					nowNum : 1,
					allNum : 10,
					start:0,
					end:10
				}, aMovie_list_left_li);
			}
			
			if(bFound){
				if(parkingstatus && imaxstatus){
					if(parkem.length==1 && imaxem.length==1){
						aMovie_list_left_ul_li[i].style.display='block';
					}else{
						aMovie_list_left_ul_li[i].style.display='none';		
					}
				}
				else if(parkingstatus){
					if(parkem.length==1){
						aMovie_list_left_ul_li[i].style.display='block';
					}else{
						aMovie_list_left_ul_li[i].style.display='none';		
					}
				}else if(imaxstatus){
					if(imaxem.length==1){
						aMovie_list_left_ul_li[i].style.display='block';
					}else{
						aMovie_list_left_ul_li[i].style.display='none';		
					}
				}else{
					aMovie_list_left_ul_li[i].style.display='block';
				}
				
			}
			else{
				aMovie_list_left_ul_li[i].style.display='none';
			}
			
		}

        if(sValueInTxt=='影院索引' || sValueInTxt==' '){
            bFound=true;
            page({
                id : 'page',
                nowNum : 1,
                allNum : 10,
                start:0,
                end:10
            }, aMovie_list_left_li);
        }else{
            var aMovie_list_left_ul_li2=[];
            for(var ii=0;ii<aMovie_list_left_ul_li.length;ii++){
                if(aMovie_list_left_ul_li[ii].style.display=='block'){
                    aMovie_list_left_ul_li2.push(aMovie_list_left_ul_li[ii]);
                }
            }
            page({
                id : 'page',
                nowNum : 1,
                allNum : 10,
                start:0,
                end:10
            }, aMovie_list_left_ul_li2);
        }
	};



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