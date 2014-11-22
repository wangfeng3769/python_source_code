<?php 
session_start();
if ($_SESSION['phone'] && $_SESSION['phone']>0) 
{
  require_once (dirname(__FILE__) . '/../../hfrm/Frm.php');
  require_once (Frm::$ROOT_PATH . 'client/classes/MemberClient.php');
  $mc=new MemberClient();
  if(!$mc->haveRightToLogin())
  {
    header('location: recharge_login.php');
    exit;
  } 
}
else
{
  header('location: recharge_login.php');
  exit;
}
include "header.php"; 
?> 
    <div class="auth-form" id="login">
        <form accept-charset="UTF-8" action="#" method="post">
            <div style="margin:0;padding:0;display:inline">
               <!--  <input name="authenticity_token" type="hidden" value="ZFQJ7f5OVvxyjcjKqjcGbZb8kp2cZsmluC2sfy7TnXQ=" /> -->
            </div>      
            <div class="auth-form-header">
                <h1>会员充值</h1>
            </div>
            <div class="auth-form-body">
                    <label for="phone_field">手机号码</label>
                <input autocapitalize="off" class="input-block" id="phone_field" name="phone" tabindex="1" type="text" autofocus="autofocus"/>
                    <label for="amount_field"> 充值金额(元) </label>
                <input autocomplete="disabled" class="input-block" id="amount_field" name="amount" tabindex="2" type="text" />

                    <label for="reason_field"> 充值原因 </label>
                <textarea cols="60" rows="4" maxlength="500" node-type="autoSuggest" class="input-area" id="reason_field" name="reason" tabindex="3" type="text" /> </textarea>

                <input type="button" class="button" id="rechargeSubmit"name="commit" tabindex="4" value="提交" style="width:60px" />
            </div>
        </form>
    </div>
     <script>
       //var Until=new Until();
       $(document).ready(function()
       {

       $("#rechargeSubmit").click(function()
       {  
          //alert(G.COOKIE_NAME);
          var cookieName=G.COOKIE_NAME;
          var token=$.cookie(G.COOKIE_NAME);
          var form=$('.auth-form-body');
          var userName;
          var phone = $("#phone_field").val();
          var amount = $("#amount_field").val()*100;
          var reason= $("#reason_field").val();

          if ("" == phone) 
            {
              Util.createMessageBox(form ,'error','手机号码不能为空！');
              //alert("用户名不能为空。");
              $("#phone_field").focus();
              return false;
            }
          if (!Util.isPhoneNumber(phone)) 
            {
              Util.createMessageBox(form ,'warnning','不是有效的手机号码！');
              //alert("用户名不能为空。");
              $("#phone_field").focus();
              return false;
            }
          if (""==amount)
            {
              Util.createMessageBox(form ,'error','充值金额不能为空！');
              //alert("密码不能为空。");
              $("#amount_field").focus();
              return false;
            }
          if (!Util.isValidMoney(amount)) 
            {
              Util.createMessageBox(form ,'warnning','不是有效的充值金额！');
              //alert("用户名不能为空。");
              $("#amount_field").focus();
              return false;
            }

          if (""==reason)
            {
              Util.createMessageBox(form ,'warnning','充值原因不能为空！');
              //alert("密码不能为空。");
              $("#reason_field").focus();
              return false;
            }
/*update by matao 2013.6.27 start
将中文输入字符设置为utf8*/
          //$.post(G.memberApi + "?item=showMemberInformation&user_phone=" + phone + "&recharge_amount="+amount + "&recharge_reason="+reason, function(data) 
          $.post(G.memberApi , {item:"showMemberInformation" , user_phone:phone , recharge_amount:amount , recharge_reason:reason}, function(data) 
          //$.post(G.memberApi + "?item=showMemberInformation&user_phone=" + phone + "&recharge_amount="+amount + "&recharge_reason="+encodeURI(reason), function(data)
/*update by matao 2013.6.27 end*/  
            {
              data=eval('('+data+')');
              if(data.errno===2000)
              {
                //alert("nihao");
                phone=data.content.user_phone;
                amount=data.content.recharge_amount;
                userName=data.content.user_name;
//reason=data.content.recharge_reason;alert(reason);                
                var messageTpl = " <div><p>你确定要为</p><p>用户名为：{#userName}</p><p>手机号为：{#phone} 的用户</p><p>充值：{#amount}元吗？</p> </div> ";

                // var message=$('<div></div>');
                messageTpl = messageTpl.replace('{#userName}',userName);
                messageTpl = messageTpl.replace('{#phone}',phone);
                messageTpl = messageTpl.replace('{#amount}',amount/100);

                Util.createConfirmBox(form,messageTpl);

             
              $("#alert-confirm-confirm").click(function()
               {   
                $(".alert-confirm").remove();
/*update by matao 2013.6.27 start
将中文输入字符设置为utf8*/
                //$.post(G.memberApi + "?item=memberRecharge&user_phone=" + phone + "&recharge_amount="+amount + "&recharge_reason="+reason, function(data)  
                $.post(G.memberApi , {item:"memberRecharge" , user_phone:phone , recharge_amount:amount , recharge_reason:reason}, function(data)
                //$.post(G.memberApi + "?item=memberRecharge&user_phone=" + phone + "&recharge_amount="+amount + "&recharge_reason="+encodeURI(reason), function(data)
/*update by matao 2013.6.27 end*/                   
                  {
                    data=eval('('+data+')');
                    if(data.errno===2000)
                    {
                      var balance=data.content.balance;
                      phone=data.content.user_phone;
                      amount=data.content.recharge_amount;
                      userName=data.content.user_name;
                      var messageTp2 = "你已经成功为用户名为:"+userName+"\n手机号为："+phone+"的用户\n充值:"+(amount/100)+"元！\n该用户现在的余额为："+(balance/100)+"元。";
             
                      Util.createMessageBox(form ,'success',messageTp2);
                      //alert("nihao");
                    }
                    else
                    {
                      Util.createMessageBox(form ,'error',data.errstr);
                      return;
                    } 
                  });
                })

                $("#alert-confirm-cancel").click(function()
                {   
                  $(".alert-confirm").remove(); 
                  return false;
                });

              }
              else
              {
                Util.createMessageBox(form ,'error',data.errstr);
                return;
              }                               
            }); 
        });
      });
    </script>
<?php include "end.php"; ?>