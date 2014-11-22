<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>会员导入中心登陆</title>

<style type="text/css">
form 
{
  width: 20em;
  margin: 40px auto;
  padding: 0px  50px 20px 50px;
  overflow: auto;
  background:#ffffff;
 
  border-radius: 5px;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;  
  box-shadow: 0 0 .3em rgba(0, 0, 0, 1);
  -moz-box-shadow: 0 0 .3em rgba(0, 0, 0, 1);
  -webkit-box-shadow: 0 0 .3em rgba(0, 0, 0, 1);

}
form ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

form ul li {
  margin: 1.0em 0 0 0;
  padding: 0;
}

/* Give each fieldset legend a nice curvy green box with white text */

label
 {
  font-size: 0.9em;
  font-weight: bold;

  float: left;
  clear: left;
  text-align: right;
  width: 20%;
  padding: .4em 0 0 0;
  margin: .15em .5em 0 0;
}

/* Submit button */

input[type="submit"] 
{
  margin:20px auto 0 120px;
  width: 5em;
  height:1.6em;
  padding:3px;
  border: 1px solid #9a9a9a;
  border-radius: 5px;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;  
  color: #454545;
  font-size: 1.0em;
  font-weight: bold;
  background:rgb(230,230,230);
  

}
input[type="submit"]:active 
{
  background: #eee;
  box-shadow: 0 0 .5em rgba(0, 0, 0, .8) inset;
  -moz-box-shadow: 0 0 .5em rgba(0, 0, 0, .8) inset;
  -webkit-box-shadow: 0 0 .5em rgba(0, 0, 0, .8) inset;
}
input 
{
  font-size: 1.0em;
}
h2
{
align:center;
text-align: center;
}

</style>


</head>
<body>
<form id="orderForm" method="post" 
action="/client/http/public.php?item=dataImportLogin">

    <h2>登陆</h2>

    <ul>

      <li>
        <label for="user_name">用户名</label>
        <input type="text" name="user_name" id="user_name" placeholder="  phone number" 
        required="required" maxlength="50"autofocus="autofocus" />
      </li>

      <li>
        <label for="password">密   码</label>
        <input type="password" name="password" id="password" placeholder="  password" 
        required="required" maxlength="50" />
      </li>
      
    </ul>

 
 <input  align="center" type="submit" name="Submit" value="登录" />
 </form>
</body>
</html>