<?PHP
require_once('./back.php');

mt_srand(quickpay_service::make_seed());

$strOrdNumber = date('YmdHis') . strval(mt_rand(100, 999));
$strOrdTimer	= date('YmdHis');
$dOrdAmount		= 90000;
$strFNUrl			= "PayAPI/UPPop/web/NotifyF.php";
$strBNUrl			= "PayAPI/UPPop/web/NotifyB.php";
$strQid				= "201301171533267529592";

// 后台交易
Refund($strQid, $strOrdNumber, $strOrdTimer, $dOrdAmount, "", $strBNUrl, quickpay_conf::PRE_AUTH_COMPLETE);
?>