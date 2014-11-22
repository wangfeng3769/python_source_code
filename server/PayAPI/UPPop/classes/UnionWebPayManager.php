<?php 
require_once('quickpay_service.php');

// if (function_exists("date_default_timezone_set")) {
//     date_default_timezone_set(quickpay_conf::$timezone);
// }


class UnionWebPayManager
{
	static protected $front="?notify=front_notify";
	static protected $back="?notify=back_notify";
	static protected $callbackUri="PayAPI/UPPop/example/router.php";
	static protected $serverUrl="http://www.eduoauto.com/";


	function __construct()
	{

	}

	function Pay($strOrdNumber, $strOrdTime, $dOrdAmount, $TransType=quickpay_conf::CONSUME)
	{
		switch ($TransType) {
					case 'preAuth':
						$TransType=quickpay_conf::PRE_AUTH;
						break;
					
					default:
						$TransType=quickpay_conf::CONSUME;
						break;
				}		
		$param['transType']             = $TransType;  		//交易类型，CONSUME or PRE_AUTH
		
		$param['orderAmount']           = $dOrdAmount; 		//交易金额
		$param['orderNumber']           = $strOrdNumber; 	//订单号，必须唯一
		$param['orderTime']             = $strOrdTime;   	//交易时间, YYYYmmhhddHHMMSS
		$param['orderCurrency']         = quickpay_conf::CURRENCY_CNY;  //交易币种，CURRENCY_CNY=>人民币
		
		$param['customerIp']            = $_SERVER['REMOTE_ADDR'];  //用户IP
		$param['frontEndUrl']           = self::$serverUrl.self::$callbackUri.self::$front;    //前台回调URL
		$param['backEndUrl']            = self::$serverUrl.self::$callbackUri.self::$back;    //前台回调URL
		
		/* 可填空字段
		   $param['commodityUrl']          = "http://www.example.com/product?name=商品";  //商品URL
		   $param['commodityName']         = '商品名称';   //商品名称
		   $param['commodityUnitPrice']    = 11000;        //商品单价
		   $param['commodityQuantity']     = 1;            //商品数量
		//*/
		
		//其余可填空的参数可以不填写
		$pay_service = new quickpay_service($param, quickpay_conf::FRONT_PAY);
		$html = $pay_service->create_html();
		header("Content-Type: text/html; charset=" . quickpay_conf::$pay_params['charset']);
		echo $html; //自动post表单
		die();
	}



	/*
	参数定义如下：
	$strOrdNumber：订单的ID号，string类型，例如："2013520148963174"
	$strOrdTime：订单的时间，string类型，格式为YYYYmmhhddHHMMSS，例如："20130114153919"
	$dOrdAmount：订单金额，int类型，数值表示为金额*100，例如：100.50元，输入格式应为10050
	$strFNUrl：前台回调页面参数，具体为URL，此URL作为回调后调用的地址，地址为相对地址，例如："PayAPI/UPPop/web/Notify.php"
	$strBNUrl：前台回调页面参数，具体为URL，此URL作为回调后调用的地址，地址为相对地址，例如："PayAPI/UPPop/web/Notify.php"
	*/
	//预授权完成
	public function refund($strQID, $strOrdNumber, $strOrdTime, $dOrdAmount, $strFNUrl, $strBNUrl, $TransType=quickpay_conf::REFUND)
	{

		
		//交易类型 退货=REFUND 或 消费撤销=CONSUME_VOID, 如果原始交易是PRE_AUTH，那么后台接口也支持对应的
		//  PRE_AUTH_VOID(预授权撤销), PRE_AUTH_COMPLETE(预授权完成), PRE_AUTH_VOID_COMPLETE(预授权完成撤销)
		$param['transType']             = $TransType;
		
		$param['origQid']				= $strQID; //原交易返回的qid, 从数据库中获取
		
		$param['orderAmount']           = $dOrdAmount;        //交易金额
		$param['orderNumber']           = $strOrdNumber; //订单号，必须唯一(不能与原交易相同)
		$param['orderTime']             = $strOrdTime;   //交易时间, YYYYmmhhddHHMMSS
		$param['orderCurrency']         = quickpay_conf::CURRENCY_CNY;  //交易币种，
		
		$param['customerIp']            = $_SERVER['REMOTE_ADDR'];  //用户IP
		$param['frontEndUrl']           = $strFNUrl;    //前台回调URL, 后台交易可为空
		$param['backEndUrl']            = self::$serverUrl."PayAPI/UPPop/web/router.php?notify=".$strBNUrl;    //后台回调URL
		
		// print_r($param);
		// die();
		//其余可填空的参数可以不填写

		//提交
		$pay_service = new quickpay_service($param, quickpay_conf::BACK_PAY);
		$ret = $pay_service->post();

		//同步返回（表示服务器已收到后台接口请求）, 处理成功与否以后台通知为准；或使用主动查询
		$response = new quickpay_service($ret, quickpay_conf::RESPONSE);
		// var_dump($response);
		// die();
		if ($response->get('respCode') != quickpay_service::RESP_SUCCESS) { //错误处理
		    $err = sprintf("Error: %d => %s", $response->get('respCode'), $response->get('respMsg'));
		    throw new Exception($err);
		}

		//后续处理
		$arr_ret = $response->get_args();
		
		// echo "后台交易返回：\n" . var_export($arr_ret, true); //此行仅用于测试输出

		return $arr_ret;
	}



	//$data=array('qid'=>$qid,'orderNumber'=>$orderNumber,'orderAmount'=>$orderAmount,'transType'=>$transType);
	function backNotify($data)
	{
		try {

		    $response = new quickpay_service($data, quickpay_conf::RESPONSE);

		    if ($response->get('respCode') != quickpay_service::RESP_SUCCESS) {
		        $err = sprintf("Error: %d => %s", $response->get('respCode'), $response->get('respMsg'));
		        throw new Exception($err);
		    }

		    $arr_ret = $response->get_args();
		    $paymentId=$this->logPaymentInfo($arr_ret['qid'], $arr_ret['orderNumber'], $arr_ret['orderAmount'],$arr_ret['q'], $arr_ret['q'],$arr_ret['transType']);
			// $arr_ret=$data;

		    // $strNotify = $_REQUEST['notify'];
		    // $strNotifyURL = self::$serverUrl. "/". $strNotify. 
		    // 								"?qid=". $arr_ret['qid']. "&orderNumber=". $arr_ret['orderNumber']. 
		    // 								"&orderAmount=". $arr_ret['orderAmount']. 
		    // 								"&transType=". $arr_ret['transType'];

		    //更新数据库，将交易状态设置为已付款
		    //注意保存qid，以便调用后台接口进行退货/消费撤销
		    // $this->log($data);
		    
	    	$submitTime=$this->analyzeDate($arr_ret['qid'],$arr_ret['traceNumber']);
	    	

	  //       $paymentId = $this->log( array(
			// 	'trans_type' => $data['transType'] ,
			// 	'submit_time' => date('Y-m-d H:i:s',$submitTime),
			// 	'order_id' => $arr_ret['orderNumber'] ,
			// 	'order_generate_time' => date('Y-m-d H:i:s',$submitTime),
			// 	'settle_date' => $this->getSettleDate($submitTime,$arr_ret['settleDate']),
			// 	'transmit_time' => $this->getTraceTime($submitTime,$arr_ret['traceTime']),
			// 	'bill_amount' => '',
			// 	'account_number1' => '',
			// 	'trans_serial_number' => $arr_ret['qid'],
			// 	'trans_amount' => $arr_ret['orderAmount'],
			// 	'errcode'=>$arr_ret['respCode'],
			// ) );
		    try {
		    	$upType=substr($arr_ret['orderNumber'], 0,6);
				if ( '100000' !=$upType ) {
					
					require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
			    	$orderCharge = new OrderCharge();
			    	$orderArr=$this->splitOrderNumber($arr_ret['orderNumber']);

			        $orderCharge -> orderPayment($orderArr['orderId'], $arr_ret['orderAmount'], $arr_ret['qid'], OrderCharge::$PAYMENT_TYPE_UPWEB);
			        // print_r(sprintf('%s,%s,%s,%s',$orderArr['orderId'], $arr_ret['orderAmount'], $arr_ret['qid'], OrderCharge::$PAYMENT_TYPE_UPWEB));
			        //记录此订单支付的记录

				}
				else
				{

					require_once (Frm::$ROOT_PATH . 'PayAPI/upmobile/classes/Payment.php');
					$p = new PurchaseObj();
					$p -> id = $paymentId;
					$p -> orderId = $arr_ret['orderNumber'];
					$p -> amount = $arr_ret['orderAmount'];
					$p -> time = strtotime($submitTime);
					require_once (Frm::$ROOT_PATH . 'moneyAccountManager/CashAccountManager.php');
					$cam = new CashAccountManager();
					$cam->onPurchaseWeb($p);
				}

		    	
		        
		    } catch (Exception $e) {
		        //修改状态出错,退钱到银联账户
		        if ($e->code!=4231 || $e->code!=4232) {
		        	if (0 != strpos($arr_ret['orderNumber'], 'charge')) {
		        		//订单退款操作
		        		require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
					    $om = new OrderManager();
					    $order = $om -> getOrder($orderArr['orderId']);
				        $this->refund($arr_ret['qid'], $arr_ret['orderNumber'], $order->add_time, $arr_ret['orderAmount'], $strFNUrl, $strBNUrl, $TransType=quickpay_conf::CONSUME_VOID);
		        	}
		        	else
		        	{
		        		print_r($e);

		        	}
		        	
		        	
		        }
	         	
		    }

		    // echo file_get_contents($strNotifyURL); 
		    // die();  
			// Header("HTTP/1.1 303 See Other"); 
			// Header("Location: $strNotifyURL"); 

		    //以下仅用于测试
		    file_put_contents('notify.txt', var_export($arr_ret, true));

		}
		catch(Exception $exp) {
		    //后台通知出错
		    file_put_contents('notify.txt', var_export($exp, true));
		}
	}

	//$data=array('qid'=>$qid,'orderNumber'=>$orderNumber,'orderAmount'=>$orderAmount,'transType'=>$transType);
	function frontNotify($data)
	{
		try {
			// $data=array('qid'=>$qid,'orderNumber'=>$orderNumber,'orderAmount'=>$orderAmount,'transType'=>$transType);
		    
			//测试服务器无认证url,暂时去掉
		    $response = new quickpay_service($data, quickpay_conf::RESPONSE);
		    if ($response->get('respCode') != quickpay_service::RESP_SUCCESS) {
		        $err = sprintf("Error: %d => %s", $response->get('respCode'), $response->get('respMsg'));
		        throw new Exception($err);
		    }
		    $arr_ret = $response->get_args(); 
			// $arr_ret=$data;

		    //告诉用户交易完成
		    $strNotify = $_REQUEST['notify'];
		    $strNotifyURL = self::$serverUrl. $strNotify. 
		    								"?qid=". $arr_ret['qid']. "&orderNumber=". $arr_ret['orderNumber']. 
		    								"&orderAmount=". $arr_ret['orderAmount']. 
		    								"&transType=". $arr_ret['transType'];
		    // echo file_get_contents($strNotifyURL);	
		    // die();	
		    //临时显示		
		    echo $data['respMsg'];			
				// Header("HTTP/1.1 303 See Other"); 
				// Header("Location: $strNotifyURL"); 

		}
		catch(Exception $exp) {
		    $str .= var_export($exp, true);
		    die("error happend: " . $str);
		}

	}



		/*
	返回值信息
	0:成功
	1:失败
	2:超时
	3:不存在
	*/
	//查询接口
	function query($dTransType, $strOrdNumber, $strOrdTime)
	{
		//需要填入的部分
		$param['transType']     = $dTransType;   //交易类型
		$param['orderNumber']   = $strOrdNumber; //订单号
		$param['orderTime']     = $strOrdTime;   //订单时间
		
		$nRet = 3;
		
		//提交查询
		$query  = new quickpay_service($param, quickpay_conf::QUERY);
		$ret    = $query->post();

		//返回查询结果
		$response = new quickpay_service($ret, quickpay_conf::RESPONSE);
		if ($response->get('respCode') != quickpay_service::RESP_SUCCESS) { //错误处理
		    $err = sprintf("Error: %d => %s", $response->get('respCode'), $response->get('respMsg'));
		    throw new Exception($err);
		}
		
		//后续处理
		$arr_ret = $response->get_args();
		echo "查询请求返回：\n" .  var_export($arr_ret, true);
		
		$queryResult = $arr_ret['queryResult'];
		
		//更新数据库
		if ($queryResult == quickpay_service::QUERY_SUCCESS) {
		    echo "操作成功";
			$nRet = 0;
		}
		else if ($queryResult == quickpay_service::QUERY_FAIL) {
		    echo "操作失败";
			$nRet = 1;
		}
		else if ($queryResult == quickpay_service::QUERY_WAIT) {
		    echo "过一段时间再查吧";
			$nRet = 2;
		}
		else {
		    echo "交易不存在";
		}
	
		return $nRet;
	}

	function splitOrderNumber($orderNumber)
	{
		$arrOrderStr=explode('-', $orderNumber);
		$orderArr=array('orderNumber'=>$arrOrderStr[0],'orderId'=>$arrOrderStr[1]);
		return $orderArr;
	}

	protected function log($arr) {
		$sql = "INSERT INTO upp_payment_log( trans_type , submit_time , order_id , order_generate_time  ,
			settle_date , bill_amount , account_number1 , trans_serial_number , trans_amount,transmit_time,payment_type ) 
			VALUES 
			( '{$arr['trans_type']}' , '{$arr['submit_time']}' , '{$arr['order_id']}' , '{$arr['order_generate_time']}' ,
			'{$arr['settle_date']}' , '{$arr['bill_amount']}' , '{$arr['account_number1']}' , 
			'{$arr['trans_serial_number']}' , '{$arr['trans_amount']}','{$arr['transmit_time']}','web' )";
		$db = Frm::getInstance() -> getDb();
		$trans = $db -> startTransaction();
		$db -> execSql($sql);
		$id = $trans -> lastId();
		$trans -> commit();

		return $id;
	}

	function analyzeDate($qid,$traceNumber)
	{
		$a = explode($traceNumber, $qid);
		return strtotime($a[0]);
	}
	function getSettleDate($tTime,$strDate)
	{
		$dateArr=getdate($tTime);
		$tDate=strtotime($dateArr['year'].$strDate);
		return date('Y-m-d',$tDate);
	}
	function getTraceTime($tTime,$strDate)
	{
		$dateArr=getdate($tTime);
		$tDate=strtotime($dateArr['year'].$strDate);
		return date('Y-m-d H:i:s',$tDate);
	}

	function onRefund($extId, $amount)
	{
		$sql=" SELECT * FROM web_upp_log WHERE qid='$extId' AND type=".quickpay_conf::PRE_AUTH.
			" ORDER BY order_time DESC ";
		$db = Frm::getInstance() -> getDb();
		$ret=$db->getRow($sql);

		$orderArr=$this->splitOrderNumber($ret['order_number']);
	    $orderId=$orderArr['orderId'];
	    require_once (Frm::$ROOT_PATH . 'order/classes/OrderManager.php');
	    $om = new OrderManager();
	    $order= $om->getOrder($orderId);
	    $orderTime=date('YmdHis');

		$strFNUrl='';
		$strBNUrl='test';
		$orderNumber=date('YmdHis').'-'.$orderId;
		if ($amount>0)
		{
			$TransType=quickpay_conf::PRE_AUTH_COMPLETE;
		}
		else
		{
			$TransType=quickpay_conf::PRE_AUTH_VOID;
		}
		

		
		$this->refund($ret['qid'], $orderNumber, $orderTime, $amount, $strFNUrl, $strBNUrl, $TransType);

		return array('rspCode'=>'000','logId'=>$extId);
	}

	function logPaymentInfo($strQID, $strOrdNumber, $dOrdAmount, $strFNUrl, $strBNUrl,$TransType)
	{
		$sql = "INSERT INTO web_upp_log( type  , order_number , qid  ,
			amount , fron_url , back_url,order_time) 
			VALUES 
			( '{$TransType}' ,'{$strOrdNumber}','{$strQID}' ,'{$dOrdAmount}','{$strFNUrl}','{$strBNUrl}' ,now())";
		$db = Frm::getInstance() -> getDb();
		$trans = $db -> startTransaction();
		$db -> execSql($sql);
		$id = $trans -> lastId();
		$trans -> commit();

		return $id;
	}
}
 ?>