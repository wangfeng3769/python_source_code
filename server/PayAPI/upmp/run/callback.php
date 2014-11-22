<?php 
$cache_name=date('Y-m-d');
$cache_data = "<?php\n".'$data[\''.$cache_name."'] = ".var_export($_REQUEST, true).";\n\n?>";
// print_r($cache_data);
writeFile('./'.$cache_name.'.log.php',(string)$cache_data);


require_once (dirname(__FILE__) . '/../../../hfrm/Frm.php');
require_once (Frm::$ROOT_PATH . 'PayAPI/upmp/classes/UnionMobilePayManager.php');
$umpm = new UnionMobilePayManager();
$umpm->log($_REQUEST);
onpurchase();
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
function onpurchase(){
    $req = $_REQUEST;
    // var_dump($req);
    try {
        if ( $req['respCode'] == '00') {

            $umpm = new UnionMobilePayManager();
            $umpm-> backNotify($req);
        }
    } catch (Exception $e) {
        // $umpm = new UnionMobilePayManager();
        // $transType = upmp_config::CONSUME_VOID;
        // $amount = $req['settleAmount'];
        // $qn = $req['qn'];
        // $umpm-> refund($transType,$amount,$qn);
        // throw new Exception("支付失败", $e);
        
    }

}
 ?>
