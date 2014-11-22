var G = 
{
	baseUrl:'http://192.168.0.23/',
	memberApi:'/client/http/member.php',
	publicApi:'/client/http/public.php',
	loginApi:'/client/index.php',
	pageStack:[],
	nowPageClassName:'init',
	user:{},
	COOKIE_NAME:'eduo_cookie_name'
};

(function Util(k)
{
	jq = k.$;
	func = 
	{
    /*-------------页面弹框---
    --------------*/
    /*消息框：
       location(位置)：父元素id,制定消息框的范围
       type(类型)：error(错误)/warnning(警告)/success(成功)/制定消息框的类型
       message(消息):文本信息,制定消息框的内容*/
    
        createMessageBox : function(location,type,message)
		{
			var mb;
			switch(type)
			{
				case 'error': mb = $('<div class="alert-error"></div>');
				break;
				case 'warnning': mb = $('<div class="alert-warnning"></div>');
				break;
				case 'success': mb = $('<div class="alert-success"></div>');
				break;
				/*case 'confirm': mb = $('<div class="alert-confirm"></div>');
				break;*/
                default:return;
			}
			mb.css({display:"none"});
            mb.text(message);
			location.prepend(mb);
			mb.slideDown(1000);
			mb.fadeOut(3000);
			//mb.remove();
            //mb.show();mb.fadeOut("slow");
			setTimeout(function(){
				
				mb.remove();
			},5000);
			//return mb;		
		},
        
        createConfirmBox : function(location,message)
        {
            var mb=$('<div class="alert-confirm"></div>');
            var mbContent=$('<div class="alert-confirm-content"></div>');
            var mbConfirm=$('<input id="alert-confirm-confirm" class="button" type="button" value="确认"></input>');
            var mbCancel=$('<input id="alert-confirm-cancel" class="button" type="button" value="取消"></input>');

            mb.css({display:"none"});
            mbConfirm.css({display:"inline",margin:"5px auto",width:"30px"});
            mbCancel.css({display:"inline",margin:"5px auto",width:"30px"});
            mbContent.css({display:"block",margin:"5px",width:"100%"});

            mbContent.prepend(message);

            mb.prepend(mbCancel);
            mb.prepend(mbConfirm);
            mb.prepend(mbContent);
			location.prepend(mb);
			mb.slideDown(300);
        },


        /*以13,14,15,18 开头的所有11位号码*/
        isPhoneNumber : function(number)  
        {
        	var patrn=/^((13[0-9])|(14[0-9])|(15[0-9])|(18[0-9]))\d{8}$/;
            if (!patrn.exec(number)) 
            {
            	return false;
            }
            else
            {
            	return true;
            }	              
        },

        /*6-20位的数字 字母 下划线 组合*/
        isNormalPassword : function(password)  
        {
        	var patrn=/^[\w]{6,20}$/;
            if (!patrn.exec(password)) 
            {
            	return false;
            }
            else
            {
            	return true;
            }	               
        },
       /*判断金额是否合法（以分为单位）*/
        isValidMoney : function(amount)  
        {
        	var patrn=/^[-]?[1-9]{1}\d*$/;
            if (!patrn.exec(amount)) 
            {
            	return false;
            }
            else
            {
            	return true;
            }	               
        },

	/*-------------单次访问-----------------*/
		SingleRequest:(function()
		{
			var me=this;
			this.isRequesting = false;
			this.callback=null;
		
			this.startRequest = function (url,param,Callback,type)
			{
				if(!me.isRequesting)
				{
					me.isRequesting=true;
					me.callback=Callback;
					$.post(url,param,me.completeRequest,type);

				}
				else
				{
					return;
				}
			}
			this.completeRequest = function(data)
			{
    		  me.callback(data);
    		  me.isRequesting=false;
			}
			return this;
		})()
	};
    //return this;
   k.Util=func; 
})(window);