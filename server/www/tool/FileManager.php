<?php
class FileManager
{
	const excelMaxSize=100000;
	const imageMaxSize=200000;
	static $excelExtens=array('xls');
	static $imageExtens=array('png','jpg','jpeg','pjpeg');

	public function FileManager()
	{
		//echo "123343";
		//echo self::excelMaxSize;
		//print_r(self::$imageExtens);
    }

    public function getExtension($file)//获得文件扩展名;
    {   
        return strtolower(substr(strchr($file['name'],'.'),1));
    }
    
    public function isInlimitSize($file)//判断文件是否符合限定大小;
    {
        //$extension=$this->getExtension();
        $extension=$this->getExtension($file);
        
        if(in_array($extension,self::$excelExtens))
        {
        	if($file['size'] <= self::excelMaxSize)
        		{return true;}
            else
                throw new Exception('', 4035);
        }
        else if(in_array($extension,self::$imageExtens))
        {
        	if($file['size'] <= self::imageMaxSize)
        		{return true;}
            else
                throw new Exception('', 4035);  
        }
        else
        {
        	throw new Exception('', 4034);
        }

    }

}

