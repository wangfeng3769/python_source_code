<?PHP
require_once (ROOT_PATH."/include/PubMarco.php");
require_once (ROOT_PATH."/include/FileUtile.php");

date_default_timezone_set('Asia/Shanghai');

function Logger($strLogger, $strFilePath, $bTime)
{
	$strTime = "";
	
	if ($strFilePath == "")
		return false;

	$strLogFile = strrchr($strFilePath, "\\/");
	$strLogPath	= substr($strFilePath, 0, strlen($strFilePath) - strlen($strLogFile));
	
	if (!file_exists($strLogPath))
	{
		if (!CreatePath($strLogPath))
			return false;
	}
	
	if ($bTime)
		$strTime = (string)(date('Y-m-d H:i:s'). " ");
				
	if (!AddFileData($strFilePath, $strTime . $strLogger . "\r\n"))
		return false;
	
	return true;
}
?>