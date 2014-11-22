

window.onload=function(){
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


	