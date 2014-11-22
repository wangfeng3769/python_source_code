<?PHP
require_once('./back.php');

mt_srand(quickpay_service::make_seed());

$strOrdNumber = date('YmdHis') . strval(mt_rand(200, 999));
$strOrdTimer	= date('YmdHis');
$dOrdAmount		= 21000;
$strFNUrl			= "banktest/example/NotifyF.php";
$strBNUrl			= "banktest/example/NotifyB.php";
$strQid				= "201301161519577917192";

// 后台交易
Refund($strQid, $strOrdNumber, $strOrdTimer, $dOrdAmount, "", $strBNUrl);
?>