window.onload=function(){
	//在线选座tab切换
	var oOrder_tab_title=document.getElementById('order_tab_title');
	var oOrder_time_tab_box=document.getElementById('order_time_tab_box');
	var oOrder_tab_titleLi=oOrder_tab_title.getElementsByTagName('li');
	var aTime_box=getByClass(oOrder_time_tab_box,'time_tab_box_inner');
	
	Tab(oOrder_tab_titleLi,aTime_box);

	
	//座位选择、时间选择
	var oMov_play_time=document.getElementById('mov_play_time');
	var oMov_play_time_span=oMov_play_time.getElementsByTagName('span')[0];
	
	var oTime_box_today=document.getElementById('time_box_today');
	var aTime_box_today_li=oTime_box_today.getElementsByTagName('li');
	
	var oTime_box_tomorrow=document.getElementById('time_box_tomorrow');
	
	for(var i=0;i<aTime_box_today_li.length;i++){
		
		aTime_box_today_li[i].index=i;
		aTime_box_today_li[i].onclick=function(){
			
			oMov_play_time_span.innerHTML='';
			oMov_play_time_span.innerHTML=aTime_box_today_li[this.index].innerHTML;
			
		};
		
	}
	
	//在线选座位
	var oSel_seating_area=document.getElementById('sel_seating_area');
	var oSel_seating_area_table=oSel_seating_area.getElementsByTagName('table')[0];
	var aSel_seating_area_table_td=oSel_seating_area_table.getElementsByTagName('td');
	var i=0;
	var oldSrc='';
	var aSel_seating_area_table_tr=oSel_seating_area_table.getElementsByTagName('tr');
	
	var aSel_seating_area_table_img=oSel_seating_area_table.getElementsByTagName('img');
	var oSelected_seat_num=document.getElementById('selected_seat_num');
	var oSelected_seat_num_span=oSelected_seat_num.getElementsByTagName('span')[0];
	var oSelected_seat_num_em=oSelected_seat_num.getElementsByTagName('em')[0];
	
	for(var i=0;i<aSel_seating_area_table_img.length;i++){
		
		var aOwnSelectedSeatCount=[];
		var aOwnSelectedSeatNum=[];
		
		aSel_seating_area_table_img[i].index=i;
		aSel_seating_area_table_img[i].bBtn=true;
	    aSel_seating_area_table_img[i].onclick = function(){

			
			if(aOwnSelectedSeatCount.length>3){
				alert('每个订单最多可选择4个座位');
				return false;
								
			}
			else{
				if(this.bBtn){
					//oldSrc=this.src;
					this.src = 'images/seat_own.png';
					this.bBtn = false;
					
				}
				else{
					//oldSrc=this.src;
					this.src = 'images/seat_null.png';
					this.bBtn = true;
					
				}
			}
			
			aOwnSelectedSeatCount.push(1);
			oSelected_seat_num_span.innerHTML+='<span>'+this.title+'</span>';
			oSelected_seat_num_em.innerHTML=aOwnSelectedSeatCount.length+'个';
		
			
			
		};
		
	}

	
	//表单提交 手机号、验证码
	var oTelCode=document.getElementById('TelCode');
	var oTextCode=document.getElementById('TextCode');
	
	Search(TelCode,'请输入手机号用于接收电子兑换码');
	
	Search(TextCode,'请输入验证码');
	
	oTelCode.onblur=function(){
		checkMobile();
	}
	
	function checkMobile(){ 
		var tel = document.getElementById('TelCode').value;
		
	    if(/^13\d{9}$/g.test(tel)||/^18\d{9}$/g.test(tel)||(/^15[8,9]\d{8}$/g.test(tel))){
			
	    }
	    else{
			alert("请输入正确的手机号");
	   }
	   
	} 
}