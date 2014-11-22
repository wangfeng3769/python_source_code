window.onload=function(){
	var oHot_movie_tle=document.getElementById('hot_movie_tle');
	var oHot_movie_tle_A=oHot_movie_tle.getElementsByTagName('a')[0];
	var oHot_movie_Box=document.getElementById('hot_movie_Box');
	var oClose = document.getElementById('close');
	var oMark_bg=document.getElementById('mark_bg');
	
	oHot_movie_tle_A.onclick=function(){
		oHot_movie_Box.style.display='block';
		oMark_bg.style.display='block';
		
		
		oHot_movie_Box.style.left = (document.documentElement.clientWidth - oHot_movie_Box.offsetWidth)/2 + 'px';
		oHot_movie_Box.style.top = (document.documentElement.clientHeight - oHot_movie_Box.offsetHeight)/2 + scrollY() + 'px';	
		
		
		oMark_bg.style.width = document.documentElement.clientWidth + 'px';
		oMark_bg.style.height = documentHeight() + 'px';
		
	};
	
		
	oClose.onclick=function(){
		oHot_movie_Box.style.display='none';
		oMark_bg.style.display='none';
	};
}