<?php
require_once dirname(__FILE__) . '/../../hfrm/Frm.php';
require_once Frm::$ROOT_PATH . 'client/classes/DataImportAuth.php';
class DataImportAdmin extends DataImportAuth
{   
	/*构造函数*/
    public function __construct() 
		{ parent::__construct();}


    /*执行函数*/
    public function showMemberImportList()
    {

    	require_once Frm::$ROOT_PATH.'client/classes/MemberImport.php';
    	$mi=new MemberImport();

        $importSuccessArray=array();
        $importErrorArray=array();
        $sendErrorArray=array();
        $sameArray=array();
        $existArray=array();
        //echo "string";
    	$file=$mi->getFile('my_file');
    	if($mi->verifyFile($file))
    	{   
            $Marray=$mi->getMembers($file);
            $sameArray=$mi->findSameMember($Marray);
            if(empty($sameArray))
            {
            	$existArray=$mi->findExistMember($Marray);
    	        if(empty($existArray))
    	        {
                    $importArray=$mi->importMembers($Marray);
                    $importSuccessArray=$importArray['sArray'];
                    $importErrorArray=$importArray['fArray'];
            		$sendErrorArray=$mi->sendMessages($importSuccessArray);
    	        }
            } 
        }
        return array('importSuccessArray'=>$importSuccessArray,'importErrorArray'=>$importErrorArray,'sendErrorArray'=>$sendErrorArray,'sameArray'=>$sameArray,'existArray'=>$existArray);
    }      
}
