<?PHP
require_once('./front.php');

mt_srand(quickpay_service::make_seed());

$strOrdNumber = date('YmdHis') . strval(mt_rand(100, 999));
$strOrdTimer	= date('YmdHis');
$dOrdAmount		= 21000;
$strFNUrl			= "banktest/example/NotifyF.php";
$strBNUrl			= "banktest/example/NotifyB.php";
$strQid				= "201301161623097919872";

// ǰ̨����
pay($strOrdNumber, $strOrdTimer, $dOrdAmount, $strFNUrl, $strBNUrl);
?>