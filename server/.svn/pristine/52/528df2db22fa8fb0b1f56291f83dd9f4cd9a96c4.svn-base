<?php
class DataImportAuth 
{

	public function __construct()
	{
		require_once (Frm::$ROOT_PATH . 'user/classes/UserManager.php');

		if (!UserManager::isLogined())
		{

			echo "<script>alert('请先登录！');</script>";
			header('Location:/client/http/public.php?item=dataImportLoginPage');
			//echo '请先登录!';
			exit;
		} 
		else 
		{
			$moniterArr = array('13910398687','15210329896');
			if (!in_array($_SESSION['phone'], $moniterArr))
			{
				echo '<meta http-equiv="Content-Type" content="text/html; charset=utf8">';
				//echo '您无权导入!';
				echo "<script>alert('您无权导入数据!');</script>";
				exit ;
			}
		}
	}

}
?>