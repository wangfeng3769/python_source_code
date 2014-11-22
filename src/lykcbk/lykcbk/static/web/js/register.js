 //onload = regs;      //页面载入完执行
 function regs(click){
	var stat = true;        //返回状态， 提交数据时用到
	var password = document.getElementById('register_password');
	var chkpass = document.getElementById('register_chkpass');
	var email = document.getElementById('register_email');
 
	check(password, "密码必须在6-20位之间", function(val){
		if (val.match(/^\S+$/) && val.length >= 6 && val.length <=20){
			return true;
		} else {
			stat = false;
			return false;
		}
	}, click);
 
	 
	check(chkpass, "确定密码要和上面一致，规则也要相同", function(val){
		if (val.match(/^\S+$/) && val.length >=6 && val.length <=20 && val == password.value){
			return true;
		} else {
			stat = false;
			return false;
		}
	}, click);
 
	check(email, "请按邮箱规则输入", function(val){
		if (val.match(/\w+@\w+\.\w/)){
			return true;
		} else {
			stat = false;
			return false;
		}
	}, click);
	return stat;
}

//获取表单后的span 标签 显示提示信息
 function gspan(cobj){
     if (cobj.parentNode.nextSibling.nodeName != 'SPAN'){
         gspan(cobj.parentNode.nextSibling);
     } else {
         return cobj.parentNode.nextSibling;
     }
 }



 //检查表单 obj【表单对象】， info【提示信息】 fun【处理函数】  click 【是否需要单击， 提交时候需要触发】
function check(obj, info, fun, click){
    var sp = gspan(obj);
    obj.onfocus = function(){
        sp.innerHTML = info;
        sp.className = 'stats2';
    }
 
    obj.onblur = function(){
        if (fun(this.value)){
            sp.innerHTML = "输入正确！";
            sp.className = "stats4";
        } else {
            sp.innerHTML = info;
            sp.className = "stats3";
        }
    }
 
    if (click == 'click'){
        obj.onblur();
    }
}

 window.onload=function(){
	
	regs();
	
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