<?php 
session_start();
if ($_SESSION['phone'] && $_SESSION['phone']>0) 
{
  require_once (dirname(__FILE__) . '/../../hfrm/Frm.php');
  require_once (Frm::$ROOT_PATH . 'client/classes/MemberClient.php');
  $mc=new MemberClient();
  if($mc->haveRightToLogin())
  {
    header('location: recharge.php');
    exit;
  } 
}
include "header.php"; 
?>
    <div class="auth-form" id="login">
        <form accept-charset="UTF-8" action="#" method="post">
            <div style="margin:0;padding:0;display:inline">
            </div>      
            <div class="auth-form-header">
                <h1>登录</h1>
            </div>
            <div class="auth-form-body">
                <!-- <div class="alert-error">用户名或密码错误！</div>
                <div class="alert-warn" >密码过于简单！</div>
                <div class="alert-success" >已成功提交！</div> -->
                    <label for="login_field">手机号码</label>
                <input autocapitalize="off" autofocus="autofocus" class="input-block" id="login_field" name="login" tabindex="1" type="text" />
                    <label for="password">登录密码</label>
                <input autocomplete="disabled" class="input-block" id="password" name="password" tabindex="2" type="password" />
                <input type="button" class="button" id="loginSubmit" name="commit" tabindex="3" value="提交" style="width:60px" />
            </div>
        </form>
    </div>

    <script>
       //var Until=new Until();
       $(document).ready(function()
       {

       $("#loginSubmit").click(function()
       {  
          //alert(G.COOKIE_NAME);
          var cookieName=G.COOKIE_NAME;
          var token=$.cookie(cookieName);
          if(token)
            { 
              $.post(G.publicApi + "?item=login&access_token="+token) 
              window.location.href="recharge.php";
            }
          var form=$('.auth-form-body');
          var userName = $("#login_field").val();
          var password = $("#password").val();
          if ("" == userName) 
            {
              Util.createMessageBox(form ,'error','用户名不能为空！');
              //alert("用户名不能为空。");
              $("#login_field").focus();
              return false;
            }
          if (!Util.isPhoneNumber(userName)) 
            {
              Util.createMessageBox(form ,'error','无效的手机号码！');
              //alert("用户名不能为空。");
              $("#login_field").focus();
              return false;
            }

          if (""==password)
            {
              Util.createMessageBox(form ,'error','密码不能为空！');
              //alert("密码不能为空。");
              $("#password").focus();
              return false;
            }

          if (!Util.isPhoneNumber(userName)) 
            {
              Util.createMessageBox(form ,'error','登录密码只能由字母数字下划线组成！');
              //alert("用户名不能为空。");
              $("#password").focus();
              return false;
            }
        
          $.post(G.publicApi + "?item=login&user_name=" + userName + "&password="+password + "&access_token="+token,function(data)
            {
              data=eval('('+data+')');
              if(data.errno===2000)
              {
                token=data.content['accessToken'];
                $.post(G.memberApi + "?item=haveRightToLogin", function(obj)
                {
                  obj=eval('('+obj+')');
                  if(obj.errno===2000)
                  {
                    if(obj.content)
                    {
                      $.cookie(cookieName, token, {expires: 7, path: '/', domain: 'eduo.com', secure: true});
                      window.location.href="recharge.php";
                      return;
                      //window.open("recharge.php");
                      //$.load("recharge.php");
                    }
                    else
                    {
                      Util.createMessageBox(form ,'error','sorry，您无权登录！');
                      return;
                    } 
                  }
                  else
                  {
                    Util.createMessageBox(form ,'error',data.errstr);
                    return;
                  } 
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