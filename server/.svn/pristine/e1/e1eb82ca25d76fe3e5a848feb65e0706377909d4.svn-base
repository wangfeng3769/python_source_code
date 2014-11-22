<?PHP
include_once ("../include/PubMarco.php");
require_once (ROOT_PATH."/include/FileUtile.php");
require_once (ROOT_PATH."/include/Logger.php");

function Write($strIni , $data)
{
	$strFile = strrchr($strIni, "\\/");
	$strPath	= substr($strIni, 0, strlen($strIni) - strlen($strFile));
	
	$strErrorFile = ROOT_PATH."/Error.log";

	if (!file_exists($strPath))
	{
		if (!CreatePath($strPath))
		{
		Logger("Write client config file failed.", $strErrorFile, true);
			return false;
		}
	}
	
	DelFile($strIni);
	
	if (!AddFileData($strIni, "ClientAddr=".$data['ClientAddr']."\r\n")						||
		!AddFileData($strIni, "ClientPort=".$data['ClientPort']."\r\n")						||
		!AddFileData($strIni, "sysstatue=".$data['sysstatue']."\r\n")					||
		!AddFileData($strIni, "svs=".$data['svs']."\r\n")					||
		!AddFileData($strIni, "cts=".$data['cts']."\r\n")								||
		!AddFileData($strIni, "oil=".$data['oil']."\r\n")								||
		!AddFileData($strIni, "dicH=".$data['dicH']."\r\n")							||
		!AddFileData($strIni, "dicL=".$data['dicL']."\r\n")							||
		!AddFileData($strIni, "VssRecTime=".$data['VssRecTime']."\r\n")							||
		!AddFileData($strIni, "ClientLon=".$data['ClientLon']."\r\n")							||
		!AddFileData($strIni, "ClientLat=".$data['ClientLat']."\r\n")							||
		!AddFileData($strIni, "RecTime=".(string)(date('Y-m-d H:i:s'))."\r\n"))
	{
		Logger("Write client config file failed.", $strErrorFile, true);
		return false;
	}
	
	return true;
}

function WritePn($nId, $nPn, $strCfg)
{
	$strFile = strrchr($strCfg, "\\/");
	$strPath	= substr($strCfg, 0, strlen($strCfg) - strlen($strFile));
	
	$strErrorFile = ROOT_PATH."/Error.log";

	if (!file_exists($strPath))
	{
		if (!CreatePath($strPath))
		{
		Logger("Write pn file failed.", $strErrorFile, true);
			return false;
		}
	}
	
	DelFile($strCfg);
	
	if (!AddFileData($strCfg, "Id=".$nId."\r\n")	||
			!AddFileData($strCfg, "Pn=".$nPn."\r\n"))
	{
		Logger("Write pn file failed.", $strErrorFile, true);
		return false;
	}
	
	return true;
}

?>