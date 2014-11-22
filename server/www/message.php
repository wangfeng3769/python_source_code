<?php include 'header.html';?>
<div class="list-div">
  <div style="background:#FFF; padding: 20px 50px; margin: 2px;">
    <table align="center" width="400">
      <tr>
        <td width="50" valign="top">
         
          <img src="images/information.gif" width="32" height="32" border="0" alt="information" />
      
        </td>
        <td style="font-size: 14px; font-weight: bold">{$msg_detail}</td>
      </tr>
      <tr>
        <td></td>
        <td id="redirectionMsg">
          自动跳转
        </td>
      </tr>
      <tr>
        <td></td>
        <td>
          <ul style="margin:0; padding:0 10px" class="msg-link">
            <li><a href="/login.php" {if $link.target}target="{$link.target}"{/if}>{$link.text}</a></li>
          </ul>

        </td>
      </tr>
    </table>
  </div>
</div>
<script language="JavaScript">
<!--
var seconds = 3;
var defaultUrl = "{$default_url}";
onload = function()
{
  if (defaultUrl == 'javascript:history.go(-1)' && window.history.length == 0)
  {
    document.getElementById('redirectionMsg').innerHTML = '';
    return;
  }

  window.setInterval(redirection, 1000);
}
function redirection()
{
  if (seconds <= 0)
  {
    window.clearInterval();
    return;
  }

  seconds --;
  document.getElementById('spanSeconds').innerHTML = seconds;

  if (seconds == 0)
  {
    window.clearInterval();
    location.href = defaultUrl;
  }
}
//-->
</script>
{include file="pagefooter.htm"}