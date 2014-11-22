window.onload = function(){
	
//轮播图
	//正在上映影片声明
	var oMovieList = document.getElementById('movieList');
	var oMovieListUl = document.getElementById('MovieListUl');
	var aMovieListLi = oMovieListUl.getElementsByTagName('li');
    var aPicHrefUl = getByClass(oMovieListUl,'PicHref');

	var oMovePic = document.getElementById('movePic');
	var oMovePicUl = document.getElementById('MovePicUl');
	var aMovePicLi = oMovePicUl.getElementsByTagName('li');
	var aMovePicUlA = oMovePicUl.getElementsByTagName('a');

	var oPrev = document.getElementById('arrow_l');
	var oNext = document.getElementById('arrow_r');


	//即将上映影片声明
	var oMovieListOl = document.getElementById('MovieListOl');
	var aMovieListOlLi = oMovieListOl.getElementsByTagName('li');
    var aPicHrefOl=getByClass(oMovieListOl,'PicHref');

	var oMovePicOl = document.getElementById('MovePicOl');
	var aMovePicOlLi = oMovePicOl.getElementsByTagName('li');
	var aMovePicOlA = oMovePicOl.getElementsByTagName('a');

	var oMovePic2 = document.getElementById('movePic2');
	var oPrev2 = document.getElementById('arrow_l_2');
	var oNext2 = document.getElementById('arrow_r_2');

	var oMovie_list_btn2 = document.getElementById('movie_list_btn2');
	var oMovie_list_btn = document.getElementById('movie_list_btn');

//	var index = 2;
    var indexUl =2;
    var indexOl =2;
//	var index2 = 2;
    var index2Ul = 2;
    var index2Ol = 2;
	var bBtn = true;

    if(aMovieListLi.length > 0 && aMovieListLi.length < 3){
        indexUl = 0;
        indexOl = 0;
        index2Ul =  0;
        index2Ol =  0;
    }
	
	
	
	//鼠标悬停显示提示影片名称
	var aNumMovieTitle=getByClass(oMovieListUl,'NumMovieTitle');
	var aNumMovieTitle2=getByClass(oMovieListOl,'NumMovieTitle');

	//联动效果显示隐藏
	var oMain_left=document.getElementById('main_left');
	var aMainLeftTabBox=getByClass(oMain_left,'main_left_tab_box');

	var oMain_left2=document.getElementById('main_left2');
	var aMainLeftTabBox2=getByClass(oMain_left2,'main_left_tab_box2');

	showText();

	function showText(){
		var oMain_left_tab_box_active=getByClass(document,'main_left_tab_box active')[0];

        if(oMain_left_tab_box_active){
            var aStart_now=getByClass(oMain_left_tab_box_active,'start_now');

            var oTimer=[];
            oTimer.length=aStart_now.length;
            var timecount = 0;

            for(var i=0;i<aStart_now.length;i++){
                aStart_now[i].style.left='628px';
            }

            if (oTimer.length > 0){
                clearTimeout(oTimer[0]);
                oTimer[0]=setTimeout(function(){
                    startMove(aStart_now[0],{left:0});
                },0);
            }
            if (oTimer.length > 1){
                clearTimeout(oTimer[1]);
                oTimer[1]=setTimeout(function(){
                    startMove(aStart_now[1],{left:0});
                },200);
            }
            if (oTimer.length > 2){
                oTimer[2]=setTimeout(function(){
                    startMove(aStart_now[2],{left:0});
                },300);
            }
            if (oTimer.length > 3){
                oTimer[3]=setTimeout(function(){
                    startMove(aStart_now[3],{left:0});
                },400);
            }
            if (oTimer.length > 4){
                oTimer[4]=setTimeout(function(){
                    startMove(aStart_now[4],{left:0});
                },500);
            }
            if (oTimer.length > 5){
                oTimer[5]=setTimeout(function(){
                    startMove(aStart_now[5],{left:0});
                },600);
            }
        }
	}
	if(aMovieListLi && aMovieListLi.length>0){
        //正在上映部分
        //正在热映和即将上映按钮切换
        var OlButtonState = true;

        oMovie_list_btn2.onclick=function(){

            if(OlButtonState){
                startMove(oMovieListOl,{left:oMovieListOl.offsetWidth-(aMovieListOlLi.length+1)*aMovieListOlLi[0].offsetWidth+1},function(){
                    oMovePic.style.display = 'none';
                    oMovePic2.style.display = 'block';
                    oMain_left.style.display = 'none';
                    oMain_left2.style.display = 'block';
                    oMovie_list_btn2.style.backgroundPosition='0 -285px';
                });
                aMovieListLi[index2Ul].className = '';
                OlButtonState = false;
            }else{
                startMove(oMovieListOl,{left:873},function(){
                    oMovePic.style.display = 'block';
                    oMovePic2.style.display = 'none';
                    oMain_left.style.display = 'block';
                    oMain_left2.style.display = 'none';
                    oMovie_list_btn2.style.backgroundPosition='0 -320px';
                });
                aMovieListLi[index2Ul].className = 'active';
                OlButtonState = true;
            }
        };

        oMovie_list_btn.onclick = function(){
            startMove(oMovieListOl,{left:873},function(){
                oMovePic.style.display = 'block';
                oMovePic2.style.display = 'none';
                oMain_left.style.display = 'block';
                oMain_left2.style.display = 'none';
                oMovie_list_btn2.style.backgroundPosition='0 -320px';
            });
            aMovieListLi[index2Ul].className = 'active';
            OlButtonState = true;
        };




        //左侧按钮点击事件
        oPrev.onclick = function (){
            toRunLeft(1,aMovePicLi,oMovePicUl,aMovieListLi,aMainLeftTabBox,aMovePicUlA,aPicHrefUl,true);
        };

        //右侧按钮点击事件
        oNext.onclick = function (){
            toRunRight(1,aMovePicLi,oMovePicUl,aMovieListLi,aMainLeftTabBox,aMovePicUlA,aPicHrefUl,true);
        };

        for(var i = 0;i<aMovieListLi.length;i++){
            aMovieListLi[i].index = i;
            aMovieListLi[i].onclick = function(){
                if(this.index<index2Ul){
                    toRunLeft((index2Ul - this.index),aMovePicLi,oMovePicUl,aMovieListLi,aMainLeftTabBox,aMovePicUlA,aPicHrefUl,true);
                }
                else if(this.index>index2Ul){
                    toRunRight((this.index - index2Ul),aMovePicLi,oMovePicUl,aMovieListLi,aMainLeftTabBox,aMovePicUlA,aPicHrefUl,true);
                }
                if(!OlButtonState){
                    startMove(oMovieListOl,{left:873},function(){
                        oMovePic.style.display = 'block';
                        oMovePic2.style.display = 'none';
                        oMain_left.style.display = 'block';
                        oMain_left2.style.display = 'none';
                        oMovie_list_btn2.style.backgroundPosition='0 -320px';
                    });
                    OlButtonState = true;
                }
            };

            aMovieListLi[i].onmouseover=function(){

                var aTitleName=aNumMovieTitle[this.index].getElementsByTagName('p');
                var nameLength = aTitleName[0].innerHTML.length;
                count = Math.ceil(-35*(nameLength/7));
                aNumMovieTitle[this.index].style.left=count+'px';
                aNumMovieTitle[this.index].style.right=count+'px';
                aNumMovieTitle[this.index].style.display='block';

            }

            aMovieListLi[i].onmouseout=function(){
                aNumMovieTitle[this.index].style.display='none';
            }


        }

        show(aMovePicLi,oMovePicUl,aMovieListLi,aMainLeftTabBox,aMovePicUlA,aPicHrefUl,true);

        function show(obj1,obj2,obj3,obj4,obj5,obj6,objindex2){
            for(var i=0;i<obj1.length;i++){

                obj1[i].onclick = function(){
                    for(var i=0;i<obj1.length;i++){
                        obj1[i].index = i;
                    }
                    if(objindex2){
                        if(this.index<indexUl){
                            toRunLeft((indexUl - this.index),obj1,obj2,obj3,obj4,obj5,obj6,objindex2);
                        }
                        else if(this.index>indexUl){
                            toRunRight((this.index - indexUl),obj1,obj2,obj3,obj4,obj5,obj6,objindex2);
                        }
                    }else{
                        if(this.index<indexOl){
                            toRunLeft((indexOl - this.index),obj1,obj2,obj3,obj4,obj5,obj6,objindex2);
                        }
                        else if(this.index>indexOl){
                            toRunRight((this.index - indexOl),obj1,obj2,obj3,obj4,obj5,obj6,objindex2);
                        }
                    }
                };
            }
        }

        function toRunLeft(iNum,obj1,obj2,obj3,obj4,obj5,obj6,objindex2){
            if(bBtn){
                bBtn = false;
                var L = obj1.length-1;
                for(var i=L,len=L-iNum;i>len;i--){
                    var oLi = obj1[L].cloneNode(true);
                    obj2.style.left =  - iNum * obj1[0].offsetWidth + 'px';
                    obj2.insertBefore(oLi,obj1[0]);
                }
                for(var i=0;i<obj1.length;i++){
                    obj1[i].className = '';
                }

                for(var i=0;i<obj3.length;i++){
                    obj3[i].className = '';
                    obj4[i].className='main_left_tab_box';
                    obj5[i].href = 'javascript:;';
                }
                if(objindex2){
                    if(index2Ul==0){
                        index2Ul = obj3.length-1;
                    }
                    else{
                        index2Ul -= iNum;
                    }

                    if(index2Ul<0){
                        index2Ul = obj3.length + index2Ul;
                    }

                    obj1[indexUl].className = 'active';
                    obj3[index2Ul].className = 'active';
                    obj4[index2Ul].className = 'main_left_tab_box active';


                    if(obj4[index2Ul].className == 'main_left_tab_box active'){
                        showText();
                    }

                    startMove(obj2,{left : 0},function(){

                        for(var i=obj1.length-1,len=obj1.length-1-iNum;i>len;i--){
                            obj2.removeChild(obj1[obj1.length-1]);
                        }
                        show(obj1,obj2,obj3,obj4,obj5,obj6,objindex2);
                        bBtn = true;
                        obj5[indexUl].href = '/lykweb/movie/info/'+getValueAjax(obj6[index2Ul]);
                    });
                }else{
                    if(index2Ol==0){
                        index2Ol = obj3.length-1;
                    }
                    else{
                        index2Ol -= iNum;
                    }

                    if(index2Ol<0){
                        index2Ol = obj3.length + index2Ol;
                    }

                    obj1[indexOl].className = 'active';
                    obj3[index2Ol].className = 'active';
                    obj4[index2Ol].className = 'main_left_tab_box active';


                    if(obj4[index2Ol].className == 'main_left_tab_box active'){
                        showText();
                    }

                    startMove(obj2,{left : 0},function(){

                        for(var i=obj1.length-1,len=obj1.length-1-iNum;i>len;i--){
                            obj2.removeChild(obj1[obj1.length-1]);
                        }
                        show(obj1,obj2,obj3,obj4,obj5,obj6,objindex2);
                        bBtn = true;
                        obj5[indexOl].href = '/lykweb/movie/info/'+getValueAjax(obj6[index2Ol]);
                    });
                }
            }
        }

        function toRunRight(iNum,obj1,obj2,obj3,obj4,obj5,obj6,objindex2){
            if(bBtn){
                bBtn = false;
                for(var i=0;i<iNum;i++){
                    var oLi = obj1[i].cloneNode(true);
                    obj2.appendChild(oLi);
                }
                for(var i=0;i<obj1.length;i++){
                    obj1[i].className = '';
                }
                for(var i=0;i<obj3.length;i++){
                    obj3[i].className = '';
                    obj4[i].className='main_left_tab_box';
                    obj5[i].href = 'javascript:;';
                }
                if(objindex2){
                    if(index2Ul==obj3.length-1){
                        index2Ul = iNum - 1;
                    }
                    else{
                        index2Ul += iNum;
                    }
                    if(index2Ul>=obj3.length){
                        index2Ul = index2Ul - obj3.length;
                    }

                    obj1[indexUl+iNum].className = 'active';
                    obj3[index2Ul].className = 'active';
                    obj4[index2Ul].className = 'main_left_tab_box active';

                    if(obj4[index2Ul].className == 'main_left_tab_box active'){
                        showText();
                    }

                    startMove(obj2,{left :  - iNum * obj1[0].offsetWidth},function(){

                        for(var i=0;i<iNum;i++){
                            obj2.removeChild(obj1[0]);
                            obj2.style.left = '0';
                        }
                        show(obj1,obj2,obj3,obj4,obj5,obj6,objindex2);
                        bBtn = true;
                        obj5[indexUl].href = '/lykweb/movie/info/'+getValueAjax(obj6[index2Ul]);
                    });
                }else{
                    if(index2Ol==obj3.length-1){
                        index2Ol = iNum - 1;
                    }
                    else{
                        index2Ol += iNum;
                    }
                    if(index2Ol>=obj3.length){
                        index2Ol = index2Ol - obj3.length;
                    }

                    obj1[indexOl+iNum].className = 'active';
                    obj3[index2Ol].className = 'active';
                    obj4[index2Ol].className = 'main_left_tab_box active';

                    if(obj4[index2Ol].className == 'main_left_tab_box active'){
                        showText();
                    }

                    startMove(obj2,{left :  - iNum * obj1[0].offsetWidth},function(){

                        for(var i=0;i<iNum;i++){
                            obj2.removeChild(obj1[0]);
                            obj2.style.left = '0';
                        }
                        show(obj1,obj2,obj3,obj4,obj5,obj6,objindex2);
                        bBtn = true;
                        obj5[indexOl].href = '/lykweb/movie/info/'+getValueAjax(obj6[index2Ol]);
                    });
                }

            }
        }

//即将上映部分
        oPrev2.onclick = function (){
            toRunLeft(1,aMovePicOlLi,oMovePicOl,aMovieListOlLi,aMainLeftTabBox2,aMovePicOlA,aPicHrefOl,false);
        };

        oNext2.onclick = function (){
            toRunRight(1,aMovePicOlLi,oMovePicOl,aMovieListOlLi,aMainLeftTabBox2,aMovePicOlA,aPicHrefOl,false);
        };

        for(var i=0;i<aMovieListOlLi.length;i++){
            aMovieListOlLi[i].index = i;
            aMovieListOlLi[i].onclick = function(){
                if(this.index<index2Ol){
                    toRunLeft((index2Ol - this.index),aMovePicOlLi,oMovePicOl,aMovieListOlLi,aMainLeftTabBox2,aMovePicOlA,aPicHrefOl,false);
                }
                else if(this.index>index2Ol){
                    toRunRight((this.index - index2Ol),aMovePicOlLi,oMovePicOl,aMovieListOlLi,aMainLeftTabBox2,aMovePicOlA,aPicHrefOl,false);
                }
            };

            aMovieListOlLi[i].onmouseover=function(){
//            var ev=ev||window.event;
//            disX2=ev.clientX;

                var aTitleName=aNumMovieTitle2[this.index].getElementsByTagName('p');
                var nameLength = aTitleName[0].innerHTML.length;
                count = Math.ceil(-35*(nameLength/7));
                aNumMovieTitle2[this.index].style.left=count+'px';
                aNumMovieTitle2[this.index].style.right=count+'px';

                aNumMovieTitle2[this.index].style.display='block';
            }

            aMovieListOlLi[i].onmouseout=function(){
                aNumMovieTitle2[this.index].style.display='none';
            }
        }

        show(aMovePicOlLi,oMovePicOl,aMovieListOlLi,aMainLeftTabBox2,aMovePicOlA,aPicHrefOl,false);


        //全部影片按钮(正在热映)--更多热门影片弹窗
        var oAllPicBtn1=document.getElementById('AllPicBtn1');

        MoreMovieWindow(oAllPicBtn1);

        //全部影片按钮(即将上映)--更多热门影片弹窗
        var oAllPicBtn2=document.getElementById('AllPicBtn2');

        MoreMovieWindow(oAllPicBtn2);

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