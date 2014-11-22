window.onload=function(){
	//影片信息tab切换
	var oTab_box=document.getElementById('tab_box');
	var oTab_content=document.getElementById('tab_content');
	var oTab_title=document.getElementById('tab_title');
	var aTab_titleLi=oTab_title.getElementsByTagName('li');
	var aBox=getByClass(oTab_content,'box');
	
	//Tab(aTab_titleLi,aBox);
	
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
	
	//影院选择自定义滚动条
	var oCinema_select=document.getElementById('cinema_select');
	var oCinema_select_box=document.getElementById('cinema_select_box');
	var oCinema_select_bar_outer=document.getElementById('cinema_select_bar_outer');
	var oCinema_select_bar=document.getElementById('cinema_select_bar');
	
	ScrollBar(oCinema_select_bar,oCinema_select_bar_outer,oCinema_select_box,oCinema_select);
	
		//今天明天切换后
	var oCinema_select2=document.getElementById('cinema_select2');
	var oCinema_select_box2=document.getElementById('cinema_select_box2');
	var oCinema_select_bar_outer2=document.getElementById('cinema_select_bar_outer2');
	var oCinema_select_bar2=document.getElementById('cinema_select_bar2');
	var oCinema_select_table2=document.getElementById('cinema_select_table2');
	
	//ScrollBar(oCinema_select_bar2,oCinema_select_bar_outer2,oCinema_select_box2,oCinema_select2);	
	
	//影片场次对比结果自定义滚动条
	var oCinema_result=document.getElementById('cinema_result');
	var oCinema_result_box=document.getElementById('cinema_result_box');
	var oCinema_result_bar_outer=document.getElementById('cinema_result_bar_outer');
	var oCinema_result_bar=document.getElementById('cinema_result_bar');
	
	//ScrollBar(oCinema_result_bar,oCinema_result_bar_outer,oCinema_result_box,oCinema_result);
	
		//今天明天切换后
	var oCinema_result2=document.getElementById('cinema_result2');
	var oCinema_result_box2=document.getElementById('cinema_result_box2');
	var oCinema_result_bar_outer2=document.getElementById('cinema_result_bar_outer2');
	var oCinema_result_bar2=document.getElementById('cinema_result_bar2');
	var ocinema_result_table2=document.getElementById('cinema_result_table2');
	
	//ScrollBar(oCinema_result_bar2,oCinema_result_bar_outer2,oCinema_result_box2,oCinema_result2);

	//观影导航时间tab切换
	var oRelease_time=document.getElementById('release_time');
	var aRelease_time_Li=oRelease_time.getElementsByTagName('li');
	var aCinema_data=getByClass(document,'cinema_data');
	
	for(var i=0;i<aRelease_time_Li.length;i++){
		
		aRelease_time_Li[i].index=i;
		aRelease_time_Li[i].onclick=function(){
			for(var i=0;i<aRelease_time_Li.length;i++){
				aRelease_time_Li[i].className='';
				aCinema_data[i].style.display='none';
			}
			this.className='active';
			aCinema_data[this.index].style.display='block';
			if(aCinema_data[1].style.display=='block'){
				ScrollBar(oCinema_select_bar2,oCinema_select_bar_outer2,oCinema_select_box2,oCinema_select2);	
			}
		};
		
	}
	
	//剧情放送自定义滚动条
	var oPlot=document.getElementById('plot');
	var oPlot_box=document.getElementById('plot_box');
	var oCinema_result_bar_outer_plot=document.getElementById('cinema_result_bar_outer_plot');
	var oCinema_select_bar_plot=document.getElementById('cinema_result_bar_plot');
	
	for(var i=0;i<aTab_titleLi.length;i++){
		
		aTab_titleLi[i].index=i;
		aTab_titleLi[i].onclick=function(){
			for(var i=0;i<aTab_titleLi.length;i++){
				aTab_titleLi[i].className='';
				aBox[i].style.display='none';
			}
			this.className='active';
			aBox[this.index].style.display='block';
			if(aBox[2].style.display=='block'){
				ScrollBar(oCinema_select_bar_plot,oCinema_result_bar_outer_plot,oPlot_box,oPlot);
			}
		};
		
	}
	
	
	
	//搜索
	var oCinemaSearchInput=byId('cinema_search_input');
	
	Search(oCinemaSearchInput,'影院索引');

	//全部城区下拉菜单
	var oCity_list_icon=document.getElementById('city_list_icon');
	var sSearch_city_list=document.getElementById('search_city_list');
	var oArea_list_up=document.getElementById('area_list_up');
	var oArea_list_up_ol=document.getElementById('area_list_up_ol');
	var aArea_list_up_ol_li=oArea_list_up_ol.getElementsByTagName('li');

	var aAreaValue=getByClass(document,'AreaValue');

	var bBtn_city_list=true;

	oCity_list_icon.onmousedown=function (ev){
		var oEvent=window.event||ev;

		oEvent.cancelBubble=true;

		if(oArea_list_up.style.display == 'none'){
			oArea_list_up.style.display='block';
		}
		else{
			oArea_list_up.style.display='none';
		}
	};

	document.onmousedown=function (){
		oArea_list_up.style.display='none';
	};
	
	setDefaultText(sSearch_city_list, '全部城区', '#555555', '#000');
	
	setDropDown(
		oArea_list_up,
		function (sText){
			defaultTextSetText(sSearch_city_list, sText);
			
		}
	);
	
	//微影评分页效果
	var oSmall_appraise_content_ul=document.getElementById('small_appraise_content_ul');
	var aSmall_appraise_content_ul_li=[];
    if (oSmall_appraise_content_ul){
        aSmall_appraise_content_ul_li=oSmall_appraise_content_ul.getElementsByTagName('li');
    }
	
	page({
		id : 'page',
		nowNum : 1,
		allNum : 10,
		start:0,
		end:10
	},aSmall_appraise_content_ul_li,4);
	
	//剧照分页效果
	var oStills_pic_ul=document.getElementById('stills_pic_ul');
    var aStills_pic_ul_li = [];
    if(oStills_pic_ul){
        aStills_pic_ul_li=oStills_pic_ul.getElementsByTagName('li');
    }
	
	page({
		id : 'pagePic',
		nowNum : 1,
		allNum : 10,
		start:0,
		end:10
	},aStills_pic_ul_li,16);

    //剧照图片点击显示大图显示
    if(oStills_pic_ul){
        var aStillsPicUlLi=oStills_pic_ul.getElementsByTagName('li');
        var iNow=0;

        for(var i=0;i<aStillsPicUlLi.length;i++){
            aStillsPicUlLi[i].index = i;
            aStillsPicUlLi[i].onclick = function(){

                //可视区的宽
                var iViewWidth = document.documentElement.clientWidth;
                //可视区的高
                var iViewHeight = document.documentElement.clientHeight;
                //横向滚动条
                var iScrollLeft = document.documentElement.scrollLeft || document.body.scrollLeft;
                //纵向滚动条
                var iScrollTop = document.documentElement.scrollTop || document.body.scrollTop;

                //生成遮罩层
                var oPicMark = document.createElement('div');
                oPicMark.id = 'PicMark';
                document.body.appendChild(oPicMark);

                oPicMark.style.width = Math.max(iViewWidth, document.body.offsetWidth) + 'px';
                oPicMark.style.height = Math.max(iViewHeight, document.body.offsetHeight) + 'px';

                //生成图片弹窗层
                var oPicBox = document.createElement('div');
                oPicBox.id = 'PicBox';
                document.body.appendChild(oPicBox);
                oPicBox.style.left = iScrollLeft + (iViewWidth - oPicBox.offsetWidth) / 2 + 'px';
                oPicBox.style.top = iScrollTop + (iViewHeight - oPicBox.offsetHeight) / 2 + 'px';

                oPicBox.innerHTML=
                    '<div id="PicBoxArrow_l" class="PicBoxArrow_l pa"></div>'+
                        '<div id="PicBoxArrow_r" class="PicBoxArrow_r pa"></div>';

                var oPicBoxInner = document.createElement('table');
                oPicBoxInner.id = 'PicBoxInner';
                oPicBox.appendChild(oPicBoxInner);

                var oTBody = document.createElement('tBody');
                oPicBoxInner.appendChild(oTBody);

                var oTr = document.createElement('tr');
                oTBody.appendChild(oTr);

                var oTd = document.createElement('td');
                oTr.appendChild(oTd);

                var iNow=this.index;
                var aStillsPic = aStillsPicUlLi[iNow].getElementsByTagName('img');
                var oStillsPicSrc = aStillsPic[0].src;
                oStillsPicSrc = oStillsPicSrc.replace('220X350', '640X960');
                oStillsPicSrc = oStillsPicSrc.replace('stills/s_s', 'stills/s');

                oTd.innerHTML = '<img src='+oStillsPicSrc+' alt="" title="" />'+
                        '<span class="TotalNum">'+
                        (iNow+1)+'/'+aStillsPicUlLi.length+
                        '</span>';


                //关闭按钮
                var oStillsPicClose = document.createElement('a');
                oStillsPicClose.id = 'StillsPicClose';
                oStillsPicClose.innerHTML = '×';
                oStillsPicClose.href = 'javascript:;';
                oPicBox.appendChild(oStillsPicClose);

                oStillsPicClose.onclick = function() {

                    document.body.removeChild(oPicMark);
                    document.body.removeChild(oPicBox);

                }

                //左右点击按钮切换图片
                var oPicBoxArrow_l = document.getElementById('PicBoxArrow_l');
                var oPicBoxArrow_r = document.getElementById('PicBoxArrow_r');

                oPicBoxArrow_l.onclick = function(){
                    if(iNow>0){
                        iNow--;
                    }
                    console.log('2:'+iNow);
                    var aStillsPic = aStillsPicUlLi[iNow].getElementsByTagName('img');
                    var oStillsPicSrc = aStillsPic[0].src;
                    oStillsPicSrc = oStillsPicSrc.replace('220X350', '640X960');
                    oStillsPicSrc = oStillsPicSrc.replace('stills/s_s', 'stills/s');

                    oTd.innerHTML = '';
                    oTd.innerHTML = '<img src='+oStillsPicSrc+' alt="" title="" />'+
                        '<span class="TotalNum">'+
                        (iNow+1)+'/'+aStillsPicUlLi.length+
                        '</span>';


                };

                oPicBoxArrow_r.onclick = function(){
                    if(iNow<aStillsPicUlLi.length-1){
                        iNow++;
                    }

                    var aStillsPic = aStillsPicUlLi[iNow].getElementsByTagName('img');
                    var oStillsPicSrc = aStillsPic[0].src;
                    oStillsPicSrc = oStillsPicSrc.replace('220X350', '640X960');
                    oStillsPicSrc = oStillsPicSrc.replace('stills/s_s', 'stills/s');

                    oTd.innerHTML = '';
                    oTd.innerHTML = '<img src='+oStillsPicSrc+' alt="" title="" />'+
                        '<span class="TotalNum">'+
                        (iNow+1)+'/'+aStillsPicUlLi.length+
                        '</span>';
                };

                window.onscroll = window.onresize = function() {
                    //可视区的宽
                    var iViewWidth = document.documentElement.clientWidth;
                    //可视区的高
                    var iViewHeight = document.documentElement.clientHeight;
                    //横向滚动条
                    var iScrollLeft = document.documentElement.scrollLeft || document.body.scrollLeft;
                    //纵向滚动条
                    var iScrollTop = document.documentElement.scrollTop || document.body.scrollTop;

                    oPicBox.style.left =iScrollLeft + (iViewWidth - oPicBox.offsetWidth)/2 + 'px';
                    oPicBox.style.top = (iViewHeight - oPicBox.offsetHeight)/2 + iScrollTop + 'px';

                    oPicMark.style.width = Math.max(iViewWidth, document.body.offsetWidth) + 'px';
                    oPicMark.style.height = Math.max(iViewHeight, document.body.offsetHeight) + 'px';
                }

            };
        }
    }

    //预告片分页效果
    var oTrailers_pic_ul=document.getElementById('trailers_pic_ul');
    var aTrailers_pic_ul_li = [];
    if(oTrailers_pic_ul){
        aTrailers_pic_ul_li=oTrailers_pic_ul.getElementsByTagName('li');
    }

    page({
        id : 'pageTrailer',
        nowNum : 1,
        allNum : 10,
        start:0,
        end:10
    },aTrailers_pic_ul_li,16);
	
	
	function page(opt, objectlist,objPageCount){
        if(objectlist && objectlist.length > 0){
            var oCountLi = objectlist.length;
            var PerCount = objPageCount;
            var TotalPage = 1;

            TotalPage=Math.ceil(oCountLi/PerCount);

            if(!opt.id){return false};

            var obj = document.getElementById(opt.id);
            if(obj){
                obj.innerHTML = '';
            }

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

                    }, objectlist, objPageCount);

                    return false;

                };
            }
        }
	}
	
	//ajax场次对比结果
	var oCinema_result_table=document.getElementById('cinema_result_table');
	var oMovieID=document.getElementById('MovieID');
	var oCinema_result_table2=document.getElementById('cinema_result_table2');

	var aCinemaChecked=getByClass(document,'CinemaChecked');
	var aCinema_ids=[];
	
	for(var i=0;i<aCinemaChecked.length;i++){
		aCinemaChecked[i].index=i;
		aCinemaChecked[i].checked='';
		
		aCinemaChecked[i].onclick=function(){
			if(aCinemaChecked[this.index].checked==true){
				aCinema_ids.push(aCinemaChecked[this.index].value);
			}else{
                for(var j=0;j<aCinema_ids.length;j++){
                    if(aCinema_ids[j]==aCinemaChecked[this.index].value){
                        aCinema_ids.splice(j,1)
                    }
                }
			}
			
			var sCinema_ids=aCinema_ids.join(',');
	
			var oToday=document.getElementById('Today');
			var oTomorrow=document.getElementById('Tomorrow');
			var sPlayDate='';
			
			for(var k=0;k<aRelease_time_Li.length;k++){
				if(aRelease_time_Li[k].className=='active'){
					if(k==0){
						sPlayDate=oToday.value;
					}
					else if(k==1){
						sPlayDate=oTomorrow.value;
					}
				}
			}
			
			
			ajax('http://www.leyingke.com/lykweb/filmsession/contrast.json?movie_id='+getValueAjax(oMovieID)+'&cinema_ids='+sCinema_ids+'&date='+sPlayDate+'',function(str){

				var j = eval('('+str+')');

                oCinema_result_box.innerHTML='';

                if(j.sessionlist){
                    var oCinema_result_table = document.createElement('table');
                    oCinema_result_table.id ='cinema_result_table';
                    oCinema_result_table.className = 'cinema_result_table';
                    oCinema_result_box.appendChild(oCinema_result_table);
                    var oTbody = document.createElement('tbody');
                    oCinema_result_table.appendChild(oTbody);

                    for(var ii=0;ii<j.sessionlist.length;ii++){
                        var oTr = document.createElement('tr');
                        oTbody.appendChild(oTr);

                        var oResultTimeTd = document.createElement('td');
                        oResultTimeTd.className='result_time';
                        oResultTimeTd.innerHTML = j.sessionlist[ii].showtime;
                        if (j.sessionlist[ii].played){
                            oResultTimeTd.style.color='#A29790';
                        }
                        oTr.appendChild(oResultTimeTd);

                        var oResultNameTd = document.createElement('td');
                        oResultNameTd.className='result_name';
                        oResultNameTd.innerHTML = j.sessionlist[ii].cinemaname;
                        if (j.sessionlist[ii].played){
                            oResultNameTd.style.color='#A29790';
                        }
                        oTr.appendChild(oResultNameTd);

                        var oResultBank1Td = document.createElement('td');
                        oResultBank1Td.className='result_bank1';
                        oResultBank1Td.innerHTML = j.sessionlist[ii].hallnum;
                        if (j.sessionlist[ii].played){
                            oResultBank1Td.style.color='#A29790';
                        }
                        oTr.appendChild(oResultBank1Td);

                        var oResultBank2Td = document.createElement('td');
                        oResultBank2Td.className='result_bank2';
                        oResultBank2Td.innerHTML = j.sessionlist[ii].sessiontype;
                        if (j.sessionlist[ii].played){
                            oResultBank2Td.style.color='#A29790';
                        }
                        oTr.appendChild(oResultBank2Td);

                        var oResultMoneyTd = document.createElement('td');
                        oResultMoneyTd.className='result_money';
                        oResultMoneyTd.innerHTML = j.sessionlist[ii].price;
                        if (j.sessionlist[ii].played){
                            oResultMoneyTd.style.color='#A29790';
                        }
                        oTr.appendChild(oResultMoneyTd);
                    }
                }
                ScrollBar(oCinema_result_bar,oCinema_result_bar_outer,oCinema_result_box,oCinema_result);
			});
		}
	}
	
		
    var aCinemaChecked2=getByClass(document,'CinemaChecked2');
    var aCinema_ids2=[];

    for(var i=0;i<aCinemaChecked2.length;i++){
        aCinemaChecked2[i].index=i;
        aCinemaChecked2[i].checked='';

        aCinemaChecked2[i].onclick=function(){
            if(aCinemaChecked2[this.index].checked==true){
                aCinema_ids2.push(aCinemaChecked2[this.index].value);
            }else{
                for(var j=0;j<aCinema_ids2.length;j++){
                    if(aCinema_ids2[j]==aCinemaChecked2[this.index].value){
                        aCinema_ids2.splice(j,1)
                    }
                }
            }
            var sCinema_ids=aCinema_ids2.join(',');

            var oToday=document.getElementById('Today');
            var oTomorrow=document.getElementById('Tomorrow');
            var sPlayDate='';

            for(var k=0;k<aRelease_time_Li.length;k++){
                if(aRelease_time_Li[k].className=='active'){
                    if(k==0){
                        sPlayDate=oToday.value;
                    }
                    else if(k==1){
                        sPlayDate=oTomorrow.value;
                    }
                }
            }

            ajax('http://www.leyingke.com/lykweb/filmsession/contrast.json?movie_id='+getValueAjax(oMovieID)+'&cinema_ids='+sCinema_ids+'&date='+sPlayDate+'',function(str){

                var j = eval('('+str+')');

                oCinema_result_box2.innerHTML='';

                if(j.sessionlist){
                    var oCinema_result_table = document.createElement('table');
                    oCinema_result_table.id ='cinema_result_table';
                    oCinema_result_table.className = 'cinema_result_table';
                    oCinema_result_box2.appendChild(oCinema_result_table);

                    var oTbody = document.createElement('tbody');
                    oCinema_result_table.appendChild(oTbody);

                    for(var ii=0;ii<j.sessionlist.length;ii++){

                        var oTr = document.createElement('tr');
                        oTbody.appendChild(oTr);
						
                        var oResultTimeTd = document.createElement('td');
                        oResultTimeTd.className='result_time';
                        oResultTimeTd.innerHTML = j.sessionlist[ii].showtime;
                        if (j.sessionlist[ii].played){
                            oResultTimeTd.style.color='#A29790';
                        }
                        oTr.appendChild(oResultTimeTd);

                        var oResultNameTd = document.createElement('td');
                        oResultNameTd.className='result_name';
                        oResultNameTd.innerHTML = j.sessionlist[ii].cinemaname;
                        if (j.sessionlist[ii].played){
                            oResultNameTd.style.color='#A29790';
                        }
                        oTr.appendChild(oResultNameTd);

                        var oResultBank1Td = document.createElement('td');
                        oResultBank1Td.className='result_bank1';
                        oResultBank1Td.innerHTML = j.sessionlist[ii].hallnum;
                        if (j.sessionlist[ii].played){
                            oResultBank1Td.style.color='#A29790';
                        }
                        oTr.appendChild(oResultBank1Td);

                        var oResultBank2Td = document.createElement('td');
                        oResultBank2Td.className='result_bank2';
                        oResultBank2Td.innerHTML = j.sessionlist[ii].sessiontype;
                        if (j.sessionlist[ii].played){
                            oResultBank2Td.style.color='#A29790';
                        }
                        oTr.appendChild(oResultBank2Td);

                        var oResultMoneyTd = document.createElement('td');
                        oResultMoneyTd.className='result_money';
                        oResultMoneyTd.innerHTML = j.sessionlist[ii].price;
                        if (j.sessionlist[ii].played){
                            oResultMoneyTd.style.color='#A29790';
                        }
                        oTr.appendChild(oResultMoneyTd);
                    }
                }
                ScrollBar(oCinema_result_bar2,oCinema_result_bar_outer2,oCinema_result_box2,oCinema_result2);
            });

        }
    }
		 
		


	//页面影院搜索
	var oCinema_search_btn=document.getElementById('cinema_search_btn');
	var oCinema_select_table=document.getElementById('cinema_select_table');
	
	
	oCinema_search_btn.onclick=function(){
		var sCinemaSearchInput=oCinemaSearchInput.value;
		
		for(var i=0;i<oCinema_select_table.tBodies[0].rows.length;i++){
			
			var sValueInTab=oCinema_select_table.tBodies[0].rows[i].cells[1].innerHTML.toLowerCase();
			var sValueInTxt=sCinemaSearchInput.toLowerCase();
			var arr=sValueInTxt.split(' ');
			
			var bFound=false;
			var bBtn=false;
			
			if(sSearch_city_list.value=='全部城区' || sSearch_city_list.value=='影院索引'){
				bBtn=true;
			}
			
			if(sSearch_city_list.value == aAreaValue[i].value || bBtn){
				
				for(var j=0;j<arr.length;j++){
					
					if(sValueInTab.search(arr[j])!=-1){
						bFound=true;
						break;
					}
				}
				
			}
			
			if(bFound){	
				oCinema_select_table.tBodies[0].rows[i].style.display='block';
			}
			else{
				oCinema_select_table.tBodies[0].rows[i].style.display='none';
				
			}
			
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
