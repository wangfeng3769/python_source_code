<?php

function IsFileExist($strFilePath)
{
	if ($strFilePath == "" || !is_file($strFilePath) || !file_exists($strFilePath))
		return false;

	return true;
}

function GetFileData($strFilePath, &$strFileData)
{
	if (!IsFileExist($strFilePath) || !is_readable($strFilePath))
	{
		echo "failed to GetFileData: file ".$strFilePath." is not exist<br>";
		return false;
	}

	$handle = fopen($strFilePath, "r");
	if (filesize($strFilePath) <= 0)
	{
		echo '文件为空';
		return false;
	}
	$strFileData = fread($handle, filesize($strFilePath));
	fclose($handle);

	return true;
}

function SaveFileData($strFilePath, $strFileData, $bRewrite = TRUE)
{
	if (!CreatePath($strFilePath))
		return false;

	if (!$handle = fopen($strFilePath, ($bRewrite == TRUE) ? "w+" : "a+"))
	{
		echo "failed to open file: ".$strFilePath."<br>";
		return false;
	}

	if (!fwrite($handle, $strFileData))
	{
		echo "failed to write data to file: ".$strFilePath."<br>";
		return false;
	}

	fclose($handle);
	return true;
}

function AddFileData($strFilePath, $strFileData)
{
	if (!IsFileExist($strFilePath) || !is_writable($strFilePath))
	{
		if (!SaveFileData($strFilePath, $strFileData))
			return false;
		return true;
	}

	if (!$handle = fopen($strFilePath, "a+"))
	{
		echo "failed to open file: ".$strFilePath."<br>";
		return false;
	}

	if (!fwrite($handle, $strFileData))
	{
		echo "failed to write data to file: ".$strFilePath."<br>";
		return false;
	}

	fclose($handle);
	return true;
}

function CreatePath($strFilePath)
{
	$dirArr = explode('/', $strFilePath);
	$path = "";

	for ($i = 0; $i < count($dirArr); $i++)
	{
		if (($i == count($dirArr) - 1) && (strchr($dirArr[$i], '.') != NULL))
			break;

		$path .= $dirArr[$i].'/';
		
		if (!is_dir($path) && !mkdir($path, 0777))
		{
			echo "failed to CreatePath: ".$path."<br>";
			return false;
		}
	}

	return true;
}

function ReadDirectory($strDirPath, &$strPathArr)
{
	if ($strDirPath[strlen($strDirPath) - 1] != '/')
		$strDirPath .= '/';

	if (!is_dir($strDirPath))
	{
		echo $strDirPath." is not a directory<br>";
		return false;
	}

	if (!($handle = opendir($strDirPath)))
	{
		echo "failed to open dir: ".$strDirPath."<br>";
		return false;
	}

	while (false !== ($file = readdir($handle)))
	{
		if (strcmp($file, ".") == 0 || strcmp($file, "..") == 0)
			continue;

		$file = $strDirPath.$file;
		array_push($strPathArr, $file);
	}

	closedir($handle);
	return true;
}

function RemoveSubDir($strDirPath)
{
	$strPathArr = array();
	if (!ReadDirectory($strDirPath, $strPathArr))
		return false;

	while (count($strPathArr) > 0 && ($path = array_shift($strPathArr)))
	{
		if (is_file($path))
		{
			if (!DelFile($path))
			{
				echo "no authorise to delete file: ".$path."<br>";
				return false;
			}
		}
		else if (is_dir($path))
		{
			RemoveSubDir($path);
			if (!rmdir($path))
			{
				echo "no authorise to rm dir: ".$path."<br>";
				return false;
			}
		}
		else
			continue;
	}

	unset($strPathArr);
	return true;
}

function RemoveDir($strDirPath)
{
	if (!RemoveSubDir($strDirPath))
		return false;

	if (is_dir($strDirPath) && !rmdir($strDirPath))
	{
		echo "no authorise to rm dir: ".$path."<br>";
		return false;
	}

	return true;
}

function CopyFile($strDesFile, $strSrcFile)
{
	if (!IsFileExist($strSrcFile))
	{
		echo "file is not exist: ".$strSrcFile."<br>";
		return false;
	}

	if (!CreatePath($strDesFile))
		return false;

	if (!copy($strSrcFile, $strDesFile))
	{
		echo "failed to copy file from ".$strSrcFile." to ".$strDesFile."<br>";
		return false;
	}

	return true;
}

function DelFile($strFilePath)
{
	if (is_file($strFilePath) && unlink($strFilePath))
		return true;

	return false;
}

?>
