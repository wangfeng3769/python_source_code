<?php
session_start();
	if (!$_SESSION['uid']||!$_SESSION['phone']) 
	{
	   header('Location: data_import_login.php');
	   exit; 
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>会员导入</title>
	</head>
<body>
	<?php
	require_once (dirname(__FILE__) . '/../../hfrm/Frm.php');
	require_once (Frm::$ROOT_PATH . 'client/classes/ArrOutput.php');
	require_once (Frm::$ROOT_PATH . 'client/classes/DataImportAdmin.php');

	$arrOutput=new ArrOutput();
	$arrOutput->accessType='DataImportAdmin';
	$methodName='showMemberImportList';
	$result=array();
	$result=$arrOutput->output($methodName);

	if($result['errno']!=2000)
	{ ?>
		<h2><?php echo $result['errstr'] ?></h2>
	  <?php
	}
    else
    {   
        $sameArray=array();
        $existArray=array();
        $importSuccessArray=array();
        $importErrorArray=array();
        $sendErrorArray=array();
       
        $sameArray=$result['content']['sameArray'];
		$existArray=$result['content']['existArray'];
		$importSuccessArray=$result['content']['importSuccessArray'];
		$importErrorArray=$result['content']['importErrorArray'];
		$sendErrorArray=$result['content']['sendErrorArray'];


    	if(!empty($sameArray))
			{ ?>
    		    <table width="300px" border="5" cellspacing="0" bordercolor="#888" style="text-align:center;border-collapse:collapse;">
					<tr style="font-weight: bold;background:rgb(134,202,182);height:40px;line-height:40px;">
						<td width="300px"><?php echo "以下".count($sameArray)."		个手机号码有重复,请确保手机号码不重复然后导入:"; ?></td> 
    		        </tr>
					<?php 
						foreach( $sameArray as $row )
						{ ?>
						  <tr><td><?php echo $row; ?></td></tr>
						  <?php
					    } ?>
				</table>
    		 <?php
    		} 


    		if(!empty($existArray))
    		{?>
    			<table width="300px" border="5" cellspacing="0" bordercolor="#888" style="text-align:center;border-collapse:collapse;">
					<tr style="font-weight: bold;background:rgb(134,202,182);height:40px;line-height:40px;">
						<td width="300px"><?php echo "以下".count($existArray)."		个手机号码的用户已经存在,请删除后重新导入:"; ?></td>
					</tr>
				<?php 
					foreach( $existArray as $row )
					{ ?>
					   <tr><td><?php echo $row; ?></td></tr>
					   <?php
					} ?>
				</table>
    		 <?php
    		} 
    
    		if(empty($sameArray) && empty($existArray))
    		{?>	      
    		    <h2>导入统计</h2>
    		    <table width="300px" border="5" cellspacing="0" bordercolor="#888" style="text-align:center;border-collapse:collapse;">
    		    	<tr style="font-weight: bold;background:rgb(134,202,182);height:40px;line-height:40px;">
			    		<td>数据总量</td><td>导入成功</td><td>导入失败</td><td>发送成功</td><td>发送失败</td>
    		    	</tr>
    		    	<tr>
			    		<td><?php echo (count($importSuccessArray)+count($importErrorArray)); ?></td>
			    		<td><?php echo count($importSuccessArray); ?></td>
			    		<td><?php echo count($importErrorArray); ?></td>
			    		<td><?php echo (count($importSuccessArray)-count($sendErrorArray)); ?></td>
			    		<td><?php echo count($sendErrorArray); ?></td>   
    		    	</tr>
    		    </table>
    		    <?php
    		}

    		if(!empty($importErrorArray))
    		{?>
    			<table width="300px" border="5" cellspacing="0" bordercolor="#888"style="text-align:center;border-collapse:collapse;">
					<tr style="font-weight: bold;background:rgb(134,202,182);height:40px;line-height:40px;">
						<td width="300px"><?php echo "以下".count($importErrorArray)."		个手机号码的用户导入失败,请重新导入:"; ?></td>
					</tr>
				<?php 
					foreach( $importErrorArray as $row )
					{ ?>
					  <tr><td><?php echo $row; ?></td></tr>
					  <?php
					} ?>
				</table>
    		 <?php
    		}  


			if(!empty($sendErrorArray))
    		{?>
    			<table width="300px" border="5" cellspacing="0" bordercolor="#888" style="text-align:center;border-collapse:collapse;">
					<tr style="font-weight: bold;background:rgb(134,202,182);height:40px;line-height:40px;">
						<td width="300px"><?php echo "以下".count($sendErrorArray)."个手机号码的用户简讯发送失败:"; ?></td>
					</tr>
				<?php 
					foreach( $sendErrorArray as $row )
					{ ?>
					  <tr><td><?php echo $row; ?></td></tr>
						  <?php
					    } ?>
				</table>
    		 <?php
    		} 
    }?>
	  
</body>
</html>
