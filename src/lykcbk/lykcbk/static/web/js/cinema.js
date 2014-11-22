window.onload=function(){

    //喜欢不喜欢ajax请求
    var oLike_btn=document.getElementById('like_btn');
    var oDislike_btn=document.getElementById('dislike_btn');

    var oCinemaID=document.getElementById('cinema_id');
    var oLove=document.getElementById('Love');
    if(oLove.value=='-1'){
        oLike_btn.style.backgroundImage='url(/static/web/images/attention_btn.png)';
        oDislike_btn.style.backgroundImage='url(/static/web/images/attentioned_btn_del.png)';
        oLike_btn.style.cursor='pointer';
        oDislike_btn.style.cursor='default';
    }else{
        oLike_btn.style.backgroundImage='url(/static/web/images/attentioned_btn.png)';
        oDislike_btn.style.backgroundImage='url(/static/web/images/attention_btn_del.png)';
        oLike_btn.style.cursor='default';
        oDislike_btn.style.cursor='pointer';
    }

    oLike_btn.onclick=function(){
        if(oLove.value=='-1'){
            
            ajax('http://www.leyingke.com/lykweb/user/favorupdate.json?id='+getValueAjax(oCinemaID)+'&ftype=0&op=1',function(str){

                var j = eval('('+str+')');
                var oResult=j.result;
                if(oResult=='success'){
                    //改样式css红心
                    oLike_btn.style.backgroundImage='url(/static/web/images/attentioned_btn.png)';
                    oDislike_btn.style.backgroundImage='url(/static/web/images/attention_btn_del.png)';
                    oLike_btn.style.cursor='default';
                    oDislike_btn.style.cursor='pointer';
					oLove.value='1';
                }
                else if(oResult=='error01'){
                    //弹出登录
					Login();
                }
                else if(oResult=='error02'){
                    alert('关注失败');
                }

            });
        }
    };

    oDislike_btn.onclick=function(){
        if(oLove.value=='1'){
            
            ajax('http://www.leyingke.com/lykweb/user/favorupdate.json?id='+getValueAjax(oCinemaID)+'&ftype=0&op=-1',function(str){

                var j = eval('('+str+')');
                var oResult=j.result;

                if(oResult=='success'){
                    //改样式css红心
                    oLike_btn.style.backgroundImage='url(/static/web/images/attention_btn.png)';
                    oDislike_btn.style.backgroundImage='url(/static/web/images/attentioned_btn_del.png)';
                    oLike_btn.style.cursor='pointer';
                    oDislike_btn.style.cursor='default';
					oLove.value='-1';
                }
                else if(oResult=='error01'){
                    //弹出登录
					Login();
                }
                else if(oResult=='error02'){
                    alert('取消失败');
                }

            });
        }
    };

	//影院信息tab切换
	var oCinema_tab_title=document.getElementById('cinema_tab_title');
	var oCinema_tab_box=document.getElementById('cinema_tab_box');
	var aCinema_tab_titleLi=oCinema_tab_title.getElementsByTagName('li');
	var aTab_content=getByClass(oCinema_tab_box,'tab_content');
	
	Tab(aCinema_tab_titleLi,aTab_content);
	
	
	//搜索框
	var oCinemaMessage_search_input=document.getElementById('CinemaMessage_search_input');
	
	Search(oCinemaMessage_search_input,'影片索引');
	
	//页面影院搜索
	var oCinemaMessage_Search_Btn=document.getElementById('CinemaMessage_Search_Btn');
	var oCinema_select_table=document.getElementById('cinema_select_table');
	
	
	oCinemaMessage_Search_Btn.onclick=function(){
		
		var sCinemaSearchInput=oCinemaMessage_search_input.value;
        if(sCinemaSearchInput=='影片索引'){
            sCinemaSearchInput='';
        }
		
		ajax('http://www.leyingke.com/lykweb/cinema/filmsessions?id='+getValueAjax(oCinemaID)+'&keyword='+sCinemaSearchInput+'',function(str){
			
			var j = eval('('+str+')');

			oMovie_order.innerHTML='';
            console.log(j);
            for(var i=0;i<j.filmsessions.length;i++){
                var oLi = document.createElement('li');
                oLi.innerHTML='<div class="movie_list_left fl">'+
                        '<div class="movie_title">'+
                        j.filmsessions[i].movie_title+
                        '</div>'+
                        '<div class="movie_info">'+
                            '<p>片长：<span>'+
                            j.filmsessions[i].movie_mins+
                            '</span>语言：<span>'+
                            j.filmsessions[i].movie_language+
                            '</span></p>'+
                        '</div>'+
                    '</div>'+
                    '<div class="movie_list_right fl">'+
                        '<span class="movie_time">'+
                        j.filmsessions[i].showtime+
                        '</span>'+
                    '</div>'+
                    '<div class="movie_list_right_money fl">'+
                        '<span class="movie_money">'+
                        j.filmsessions[i].price+
                        '</span>'+
                        '<em class="movie_site">'+
                        j.filmsessions[i].hallnum+
                        '</em>'+
                    '</div>';
                oMovie_order.appendChild(oLi);
            }
				
			//分页效果
			var aMovie_order_li=oMovie_order.getElementsByTagName('li');
		
			page({
				id : 'page',
				nowNum : 1,
				allNum : 10,
				start:0,
				end:10
			}, aMovie_order_li,9);
			
		});
		
	}
	
	//影片时间、票价排序
	var oMovie_order=document.getElementById('movie_order');
	var oTb_sort1=document.getElementById('tb_sort1');
	var oTb_sort2=document.getElementById('tb_sort2');
	var oTb_sort_em=document.getElementById('tb_sort_em');
	var oTb_sort_em2=document.getElementById('tb_sort_em2');
	var oTb_sortByName=document.getElementById('tb_sortByName');
	var oTb_sortByName_em=document.getElementById('tb_sortByName_em');
	
	var bBtn=true;
	
	oTb_sort1.order='none';
	oTb_sort1.onclick=function(){
		sortByCinema();
		if(bBtn){
			oTb_sort_em.style.backgroundPosition='-32px -120px';
			bBtn=false;
		}
		else{
			oTb_sort_em.style.backgroundPosition='-24px -120px';
			bBtn=true;
		}
		
	};
	
	oTb_sort2.order='none';
	oTb_sort2.onclick=function(){
		sortByTotal();
		if(bBtn){
			oTb_sort_em2.style.backgroundPosition='-32px -120px';
			bBtn=false;
		}
		else{
			oTb_sort_em2.style.backgroundPosition='-24px -120px';
			bBtn=true;
		}
	};
	
	oTb_sortByName.order='none';
	oTb_sortByName.onclick=function(){
		sortByName();
		if(bBtn){
			oTb_sortByName_em.style.backgroundPosition='-32px -120px';
			bBtn=false;
		}
		else{
			oTb_sortByName_em.style.backgroundPosition='-24px -120px';
			bBtn=true;
		}
	};
	
	function sortUl(fnCmp){
		var aCinemaTotalDiv=getByClass(document,'movie_list_right');
		var aCinemaTotalDivForSort=[];
		var oFragment=document.createDocumentFragment();
		var i=0;
		
		for(i=0;i<aCinemaTotalDiv.length;i++){
			aCinemaTotalDivForSort.push(aCinemaTotalDiv[i]);
		}
		
		aCinemaTotalDivForSort.sort(fnCmp);

		for(i=0;i<aCinemaTotalDivForSort.length;i++){
			oFragment.appendChild(aCinemaTotalDivForSort[i].parentNode);
		}
		
		oMovie_order.appendChild(oFragment);
	}
	
	function sortByCinema(){

		var result=1;
		
		switch(oTb_sort1.order){
			case 'none':
			case 'asc':
				oTb_sort1.order='desc';
				result=1;
				break;
			case 'desc':
				oTb_sort1.order='asc';
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
				
				var fCinemaTotalDiv1=aCinemaTotalDiv1;
				var fCinemaTotalDiv2=aCinemaTotalDiv2;
				
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
        var aMovie_order_li=oMovie_order.getElementsByTagName('li');

        page({
            id : 'page',
            nowNum : 1,
            allNum : 10,
            start:0,
            end:10
        }, aMovie_order_li,9);
	}
	
	function sortUlTotal(fnCmp){
		var aCinemaTotalDiv=getByClass(document,'movie_list_right_money');
		var aCinemaTotalDivForSort=[];
		var oFragment=document.createDocumentFragment();
		var i=0;
		
		for(i=0;i<aCinemaTotalDiv.length;i++){
			aCinemaTotalDivForSort.push(aCinemaTotalDiv[i]);
		}
		
		aCinemaTotalDivForSort.sort(fnCmp);

		for(i=0;i<aCinemaTotalDivForSort.length;i++){
			oFragment.appendChild(aCinemaTotalDivForSort[i].parentNode);
		}
		
		oMovie_order.appendChild(oFragment);
	}
	
	function sortByTotal(){

		var result=1;
		
		switch(oTb_sort2.order){
			case 'none':
			case 'asc':
				oTb_sort2.order='desc';
				result=1;
				break;
			case 'desc':
				oTb_sort2.order='asc';
				result=-1;
				break;
		}
		
		sortUlTotal(
			function (vRow1, vRow2){
				var aTotalDiv1=vRow1.getElementsByTagName('span')[0].innerHTML;
				var aTotalDiv2=vRow2.getElementsByTagName('span')[0].innerHTML;
				
				var fTotal1=parseFloat(aTotalDiv1.substring(1));
				var fTotal2=parseFloat(aTotalDiv2.substring(1));
				
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
        var aMovie_order_li=oMovie_order.getElementsByTagName('li');

        page({
            id : 'page',
            nowNum : 1,
            allNum : 10,
            start:0,
            end:10
        }, aMovie_order_li,9);
	}
	
	function sortUlName(fnCmp){
		var aCinemaNameDiv=getByClass(document,'movie_list_left');
        console.log('aCinemaNameDiv length: '+aCinemaNameDiv.length);
		var aCinemaTotalDivForSort=[];
		var oFragment=document.createDocumentFragment();
		var i=0;
		
		for(i=0;i<aCinemaNameDiv.length;i++){
			aCinemaTotalDivForSort.push(aCinemaNameDiv[i]);
		}
		
		aCinemaTotalDivForSort.sort(fnCmp);

		for(i=0;i<aCinemaTotalDivForSort.length;i++){
			oFragment.appendChild(aCinemaTotalDivForSort[i].parentNode);
		}
		
		oMovie_order.appendChild(oFragment);
	}
	
	function sortByName(){

		var result=1;
		
		switch(oTb_sortByName.order){
			case 'none':
			case 'asc':
				oTb_sortByName.order='desc';
				result=1;
				break;
			case 'desc':
				oTb_sortByName.order='asc';
				result=-1;
				break;
		}
		
		sortUlName(
			function (vRow1, vRow2){
				var aNameDiv1=vRow1.getElementsByTagName('span')[0].innerHTML;
				var aNameDiv2=vRow2.getElementsByTagName('span')[0].innerHTML;
                console.log('aNameDiv1 '+aNameDiv1);
                console.log('aNameDiv2 '+aNameDiv2);
				var fName1=aNameDiv1;
				var fName2=aNameDiv2;
                console.log('fName1 '+fName1);
                console.log('fName2 '+fName2);
				if(fName1<fName2){
					return result;
				}
				else if(fName1>fName2){
					return -result;
				}
				else{
					return 0;
				}
			}
		);
        var aMovie_order_li=oMovie_order.getElementsByTagName('li');

        page({
            id : 'page',
            nowNum : 1,
            allNum : 10,
            start:0,
            end:10
        }, aMovie_order_li,9);
	}
	
	//分页效果
	var aMovie_order_li=oMovie_order.getElementsByTagName('li');

	page({
		id : 'page',
		nowNum : 1,
		allNum : 10,
		start:0,
		end:10
	}, aMovie_order_li,9);

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
            if(allNum==1){
                var oA = document.createElement('a');
                oA.href = '#' + i;
                oA.innerHTML = '第1页';
                obj.appendChild(oA);
            }else{
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
				
				}, objectlist,9);
				
				return false;
				
			};
		}
	
	}

    var oCinema_lon=document.getElementById('cinema_lon');
    var oCinema_lat=document.getElementById('cinema_lat');
    var oCinema_name=document.getElementById('cinema_name');
    var oCinema_tel=document.getElementById('cinema_tel');
    var oCinema_addr=document.getElementById('cinema_addr');
    var oCinema_logo=document.getElementById('cinema_logo');


	//弹窗百度地图
	var oMapA=document.getElementById('MapA');
	
	oMapA.onclick=function(){
		
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

		function getValue(obj){
			return obj.value;
		}

		var map = new BMap.Map("MapShowDiv");            // 创建Map实例
		var point = new BMap.Point(getValue(oCinema_lon), getValue(oCinema_lat));     // 创建点坐标
		map.centerAndZoom(point,15);                     // 初始化地图,设置中心点坐标和地图级别。
		map.enableScrollWheelZoom(); 
		
		var opts = {type: BMAP_NAVIGATION_CONTROL_LARGE}; //初始化地图控件
		map.addControl(new BMap.NavigationControl(opts));		
		
		var marker = new BMap.Marker(point); //初始化地图标记
		
		var sContent =
		"<div style='width:360px; height:120px;'>"+
		"<p style='float:left;margin:4px;text-align:center;vertical-align:middle;'>"+
		"<img  id='imgDemo' src='"+getValue(oCinema_logo)+"' width='139' height='104' title=''/>" +
		"</p>"+
		"<p style='float:right;text-align:center;'>"+
		"<p style='margin:0 0 2px 0;padding:0.2em 0;font-size:12px; font-weight:bold; height:26px'>"+
		getValue(oCinema_name)+
		"</p>" + 
		"<p style='margin:0;line-height:1.5;font-size:12px;width:330px; height:40px;'>地址："+
		getValue(oCinema_addr)+
		"</p>"+
		"<p style='margin:0;line-height:1.5;font-size:12px;width:330px; height:40px;'>电话："+
		getValue(oCinema_tel)+
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

    /*-------------------------------------- Header页面 -----------------------------------*/
    //城市列表下拉自定义滚动条
    var oCity_list_up_box_outer=document.getElementById('city_list_up_box_outer');
    var oCity_list_up_box=document.getElementById('city_list_up_box');
    var oCity_list_scroll_bar_outer=document.getElementById('city_list_scroll_bar_outer');
    var oCity_list_scroll_bar=document.getElementById('city_list_scroll_bar');

    ScrollBar(oCity_list_scroll_bar,oCity_list_scroll_bar_outer,oCity_list_up_box,oCity_list_up_box_outer);

    //点击显示登录页面
    var oSet_up_a=document.getElementById('set_up_a');

    if(oSet_up_a){
        oSet_up_a.onclick = function(){
			Login();
		};
		
    }

    /*oLogin_del_btn.onclick=function(){
        oMain_login.style.display='none';
    };*/
	
	function Login(){
		var oMain_login=document.getElementById('main_login');
    	var oLogin_del_btn=document.getElementById('login_del_btn');
		
		oMain_login.style.display = 'block';

		oMain_login.style.left = (document.documentElement.clientWidth - oMain_login.offsetWidth)/2 + 'px';
		oMain_login.style.top = (document.documentElement.clientHeight - oMain_login.offsetHeight)/2 + scrollY() + 'px';

		//登录框语言提示
		var oLogin_name=document.getElementById('login_name');
		var oLogin_password=document.getElementById('login_password');

		Search(oLogin_name,'请输入您的账号');
		Search(oLogin_password,'');


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