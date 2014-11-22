<?php
require_once dirname(__FILE__) . '/../../hfrm/Frm.php';
class ExcelManager
{
    /*构造函数*/
	public function __construct() 
	{
		
    }
    /*读入EXCEL文件*/
	public function readExcelFile($importTmpFile,$numR)
	{   
		if($importTmpFile!==null)
		{
			require_once(Frm::$ROOT_PATH .'www/tool/Excel/reader.php'); 
			$data = new Spreadsheet_Excel_Reader(); 
	        $data->setOutputEncoding('utf-8'); 
	        $data->read($importTmpFile); 

			$array =array(); 
			for ($i = $numR; $i <= $data->sheets[0]['numRows']; $i++) 
			{
				for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++)
				{ 
					$array[$i][$j] = $data->sheets[0]['cells'][$i][$j]; 
	            } 
	        }
	        if(!empty($array))
	        	{ return $array;}
	       else
	           throw new Exception('', 4039);
	    }
	}
}

