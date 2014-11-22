<?php
require_once dirname(__FILE__) . '/../../hfrm/Frm.php';
class MemberImport
{  

    ///*从EXCEL文件第4行读起*/
    const numR=4;
    ///*用户手机号码所在的列*/  
    const numC=3;
    ///*excel文件扩展名*/
    const excelEx='xls';
    ///*初始密码*/
    const iniPassword=666666;
    ///*简讯内容*/
    const messages="亲!您好，感谢您加入易多汽车共享！登陆易多官网:www.eduoauto.com，实名认证通过后,即可免费享受属于您的美妙旅程。";
    const accounts="登陆账号为您的手机号：";
    const password=",初始密码为:";
    const end      =";绿色出行，共享快乐！";
    


    /*构造函数*/
	public function __construct() 
	{ 

    }

    /*获取文件*/
    public function getFile($fileName)
    {
        $file = $_FILES[$fileName];

        if($file['error']==0)
          { return $file; } 
        else if($file['error']==1)
          {throw new Exception('', 4035);}  
        else if($file['error']==2)
          {throw new Exception('', 4036);}
        else if($file['error']==3)
          {throw new Exception('', 4037);}
        else //($file['error']==4)
          throw new Exception('', 4038);      
    }

    /*验证文件*/
    public function verifyFile($file)
    {
        require_once Frm::$ROOT_PATH . 'www/tool/FileManager.php';
        $fm=new FileManager();       
        if($fm->getExtension($file)==self::excelEx && $fm->isInlimitSize($file))
            { 
                return true;
            }
        else /*文件类型错误*/
           throw new Exception('', 4034);
    }

    /*获得会员数据*/
    public function getMembers($file)
    {   //print_r($file);
        require_once Frm::$ROOT_PATH.'www/tool/ExcelManager.php';
        $em=new ExcelManager();
        $mArray=array();
        $mArray=$em->readExcelFile($file["tmp_name"],self::numR);
        $mArray=$this->transition($mArray); 
        return $mArray;
    }

    /*提取用户手机号码,并返回*/
    public function transition($excelArray)
    {
        $phone=array();
        foreach( $excelArray as $tmp ) 
        {
            $phone[]=$tmp[self::numC];
        }
        return $phone;
    }
    
    /*查找并返回重复的手机号码*/
    public function findSameMember($array)
    {
        $sameMember=array();
        $num=count($array);
        for($i=0;$i<$num;$i++)
        {
           for($j=$i+1;$j<$num;$j++)
           {
            if($array[$i]==$array[$j])
                { 
                    $sameMember[]=$array[$i];
                }
           }
        }
        return $sameMember;
    }

    /*查找并返回已注册的手机号码*/
    public function findExistMember($array)
    {
        $execute=frm::getInstance()->getDb();
        $ssql="SELECT 1 FROM edo_user WHERE phone= ";
        $existMember=array();
        foreach( $array as $tmp ) 
        {
            $phone="{$tmp}";
            if($execute->getOne($ssql.$phone))
            { 
                $existMember[]=$tmp;
            }
        }
        return $existMember;
    }
  
    /*导入注册会员并返回导入成功和导入失败的手机号码*/
    public function importMembers($array)
    {
        $sArray=array();
        $fArray=array();
        require_once (Frm::$ROOT_PATH .'user/classes/UserManager.php');
        $um=new UserManager();
        foreach( $array as $phone ) 
        {
            if($um->register($phone,self::iniPassword)>0)
              { $sArray[]=$phone;}
            else
              { $fArray[]=$phone;}
        }
        return array('sArray'=>$sArray,'fArray'=>$fArray);
    }
        		
    /*发送短信,并返回发送失败的手机号码*/
    public function sendMessages($array)
    {
        $senErro=array();
        require_once (Frm::$ROOT_PATH . 'system/sms_platform/classes/SmsSender.php');
        $Sender=new SmsSender();
        //$messages=self::messages.self::iniPassword;
        foreach ($array as $phone) 
        {
           $messages= self::messages.self::accounts."$phone".self::password.self::iniPassword.self::end;     
           if(!$Sender->send($messages,$phone))
            {
                $senErro[]=$phone;
            }
        }
        return $senErro;
    }  
}

















