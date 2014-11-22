window.onload=function(){
	
	//�۽��ֲ�ͼ
	var oMovieList=document.getElementById('movieList');
	var aMovieListLi=oMovieList.getElementsByTagName('li');
	var aMovieListA=oMovieList.getElementsByTagName('a');
	var oMovePic=document.getElementById('movePic');
	var oMovePicUl=oMovePic.getElementsByTagName('ul')[0];
	var aMovePicLi=oMovePic.getElementsByTagName('li');
	
	oMovePicUl.innerHTML+=oMovePicUl.innerHTML;
	oMovePicUl.style.width=aMovePicLi[0].offsetWidth*aMovePicLi.length+'px';
	
	var aMove_pic_title=getByClass(oMovePic,'move_pic_title');
	var oPicBg=document.getElementById('picBg');
	var oPrev=document.getElementById('arrow_l');
	var oNext=document.getElementById('arrow_r');
	var iNum=2;
	
	var oMain_left=document.getElementById('main_left');
	var aMovie_message_box=getByClass(oMain_left,'movie_message_box');
	
	for(var i=0;i<aMovieListA.length;i++)
	{
		aMovieListA[i].index=i;
		aMovieListA[i].onclick=function (){
			for(var i=0;i<aMovieListLi.length;i++)
			{
				aMovieListLi[i].className='';
				aMove_pic_title[i].style.display='none';
				aMove_pic_title[i+aMovePicLi.length/2].style.display='none';
				aMovie_message_box[i].style.display='none';
			}
			
			iNum=this.index;
			aMovieListLi[iNum].className='active';
			aMove_pic_title[this.index].style.display='block';
			
			aMove_pic_title[this.index+aMovePicLi.length/2].style.display='block';
			
			aMovie_message_box[this.index].style.display='block';
			
			if(iNum==1){
				
				oMovePicUl.style.left=-(oMovePicUl.offsetWidth/2+115)+'px';
				
				startMove(oMovePicUl,{left:-(oMovePicUl.offsetWidth/2-138)});
				
			}
			else if(iNum<1){
				oMovePicUl.style.left=-(oMovePicUl.offsetWidth/2+115)+'px';
				
				startMove(oMovePicUl,{left:-(oMovePicUl.offsetWidth/2-391)});
				
			}
			else{
			
				startMove(oMovePicUl,{left:-(iNum-2)*aMovePicLi[0].offsetWidth-115});
			
			}
			
			
		};
		
	}
	
	
	
	oPrev.onclick=function (){
		if(oMovePicUl.offsetLeft==-115){
			oMovePicUl.style.left=-(oMovePicUl.offsetWidth/2+115)+'px';
		}
		
		for(var i=0;i<aMovieListLi.length;i++){
			aMovieListLi[i].className = '';
		}
		if(iNum==0){
			iNum = aMovieListLi.length-1;
		}
		else
		{
			iNum--;
		}
		aMovieListLi[iNum].className = 'active';
		
		startMove(oMovePicUl,{left:oMovePicUl.offsetLeft+aMovePicLi[0].offsetWidth});
		
	};
	
	oNext.onclick=function (){
		if(oMovePicUl.offsetLeft==-oMovePicUl.offsetWidth/2-115)
		{
			oMovePicUl.style.left='-115px';	
		}
		
		for(var i=0;i<aMovieListLi.length;i++){
			aMovieListLi[i].className = '';
		}
		if(iNum==aMovieListLi.length-1){
			iNum =0;
		}
		else
		{
			iNum++;
		}
		aMovieListLi[iNum].className = 'active';
		
		startMove(oMovePicUl,{left:oMovePicUl.offsetLeft-aMovePicLi[0].offsetWidth});
	};	
	
	
	//�������ʾ����ʱ��
	//var oMain_left=document.getElementById('main_left');
	var oMovie_order=document.getElementById('movie_order');
	var aMovie_order_Li=getByClass(oMain_left,'movie_order_li');
	var aMovie_list_hover=getByClass(oMain_left,'movie_list_hover');
	var aMovie_name=getByClass(document,'movie_name');
	var bBtn=true;
	
	for(var i=0;i<aMovie_name.length;i++){
		aMovie_name[i].index=i;
		aMovie_name[i].onclick=function(){
			
			if(bBtn){
				aMovie_list_hover[this.index].style.display='block';
				bBtn=false;
			}
			else{
				aMovie_list_hover[this.index].style.display='none';
				bBtn=true;
			}
				
			
		};
		
	}
	
	//tab���졢���쳡���л�
	/*var aTitle_bg=getByClass(document,'title_bg');
	var aTab_box=getByClass(document,'tab_box');
	
	for(var i=0;i<aTitle_bg.length;i++){
		aTitle_bg[i].index=i;
		aTitle_bg[i].onclick=function(){
			
			for(var i=0;i<aTitle_bg.length;i++){
				aTitle_bg[i].className='title_bg';
				aTab_box[i].style.display='block';
			}
			this.className='title_bg active';
			aTab_box[this.index].style.display='none';
			
		}
	}*/
	var aTitle_bg1=getByClass(document,'title_bg1');
	var aTitle_bg2=getByClass(document,'title_bg2');
	var aTab_box=getByClass(document,'tab_box');
	
	for(var i=0;i<aTitle_bg1.length;i++){
		aTitle_bg1[i].index=i;
		aTitle_bg1[i].onclick=function(){
			for(var i=0;i<aTab_box.length;i++){
                aTab_box[i].style.display='none';
            }
			for(var i=0;i<aTitle_bg1.length;i++){
				aTitle_bg1[i].className='title_bg1 active';
				aTitle_bg2[i].className='title_bg2';
                aTab_box[2*i].style.display='block';
			}
		}
	}
	for(var i=0;i<aTitle_bg2.length;i++){
		aTitle_bg2[i].index=i;
		aTitle_bg2[i].onclick=function(){
            for(var i=0;i<aTab_box.length;i++){
                aTab_box[i].style.display='none';
            }
			for(var i=0;i<aTitle_bg2.length;i++){
				aTitle_bg2[i].className='title_bg2 active';
				aTitle_bg1[i].className='title_bg1';
                aTab_box[2*i+1].style.display='block';
			}
		}
	}
	
	
		
}