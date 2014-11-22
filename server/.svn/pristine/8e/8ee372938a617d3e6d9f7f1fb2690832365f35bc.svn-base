<?php 
//记录访问充值接口的参数记录
$cache_name=date('Y-m-d');
$cache_data = "<?php\n".'$data[\''.$cache_name."'] = ".var_export($_REQUEST, true).";\n\n?>";

writeFile('./'.$cache_name.'.log.php',(string)$cache_data);
//end

require_once('../quickpay_service.php');
$strWebURL = "http://www.eduoauto.com/PayAPI/UPPop/example/callback.php";

try {
    header("Content-type: text/html; charset=utf-8");
    foreach ($_POST as $k => $v) {
        $req.=sprintf("&%s=%s",$k,$v);
    }
    $strNotify = $_REQUEST['notify'];
    if (strpos($strNotify, 'back')>-1) {
        $strNotifyURL = $strWebURL. "?q=". $strNotify.$req;
        echo file_get_contents($strNotifyURL); 
    }
    elseif (strpos($strNotify, 'front')>-1) {
        // $strNotifyURL = $strWebURL. "?q=". $strNotify.$req;
        // echo $strNotifyURL;

        Header("HTTP/1.1 303 See Other"); 
        Header("Location: http://www.eduoauto.com/zhanghu_view.php#consume-list-click"); 
    }


    //以下仅用于测试
    file_put_contents('notify.txt', var_export($arr_ret, true));
    die();  


}
catch(Exception $exp) {
    //后台通知出错
    file_put_contents('notify.txt', var_export($exp, true));
}


/**
 * 写入文件内容
 * @param string $filepat 文件路径
 * @param string $content 写入内容
 * @param string $type 写入方式 w:将文件指针指向文件头并将文件大小截为零 a:将文件指针指向文件末尾
 * @return string
 */
function writeFile($filepath,$content,$type='a')
{
    $is_success = false;

    if($fp = fopen($filepath,$type))
    {
        /*$start_time = microtime();
        do
        {
            $is_write = flock($fp, LOCK_EX);
            if(!$is_write)
                usleep(round(rand(0,100) * 1000));
        }
        while(!$is_write && ((microtime() - $start_time) < 10000));

        if ($is_write && fwrite($fp, $content))
            $is_success = true;*/
        @flock($fp, LOCK_EX);
        if (fwrite($fp, $content))
            $is_success = true;
        @flock($fp,LOCK_UN);
        @fclose($fp);
        @chmod($filepath, 0777);
    }

    return $is_success;
}
 ?>