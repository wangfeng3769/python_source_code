<?php
        session_start();
        if (!$_SESSION['uid']||!$_SESSION['phone']) 
        {
           header('Location: data_import_login.php');
           exit; 
        }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>导入excel文件</title>
	<script type="text/javascript" src="../js/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="../js/eduo.js"></script>
	<script type="text/javascript" src="../js/jquery.form.js"></script>
	<style type="text/css">
	input[type="submit"],button
    {
        position: relative;
        display: inline-block;
        padding: 7px 15px;
        font-size: 13px;
        font-weight: bold;
        color: #333;
        text-shadow: 0 1px 0 rgba(255,255,255,0.9);
        white-space: nowrap;
        background-color: #eaeaea;
        background-image: -moz-linear-gradient(#fafafa, #eaeaea);
        background-image: -webkit-linear-gradient(#fafafa, #eaeaea);
        background-image: linear-gradient(#fafafa, #eaeaea);
        background-repeat: repeat-x;
        border-radius: 3px;
        border: 1px solid #ddd;
        border-bottom-color: #c5c5c5;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        vertical-align: middle;
        cursor: pointer;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        -webkit-appearance: none;
    }
</style>
</head>
<body>
    <form enctype="multipart/form-data" action="member_import_list.php"
    method="post" id="frm">
   <!--  <form enctype="multipart/form-data">  -->
		<!-- <input type="hidden" name="MAX_FILE_SIZE" value="100000"/>  -->
		<input name="my_file" type="file" id="file"/>
		<!-- <button type="button"><a href="#">选择文件</a></button> -->
		<input type="submit" value="提交文件" id="btn"/> 
		<!-- <div name="hidden_frame" id= "showDataForm" style="display:block"></div> -->
    </form>
<script>
    $(document).ready(function()
      {
    	$("#btn").click(function()
            {
            	var fileName=$('input[type=file]').val();
            	var ext = fileName.split('.').pop().toLocaleLowerCase();
            	if(ext=="")
            	{
            		//alert("请选择文件")
            		window.location.reload();
            		parent.location.reload();
                    opener.location.reload();
                    top.location.reload();

            	}
            	else if(ext!="xls")
            	{
            		//alert("请选择正确的文件");
            		window.location.reload();         
                    parent.location.reload();
                    opener.location.reload();
                    top.location.reload();
            	}
            });
       });
</script>
</body>
</html>