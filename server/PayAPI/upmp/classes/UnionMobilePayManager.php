<?php 
// header('Content-Type:text/html;charset=utf-8');
require_once("upmp_service.php");
class UnionMobilePayManager
{
    public static $WEB = 'http://www.eduoauto.com/';
    public static $NOTIFY_BACK = 'PayAPI/upmp/run/callback.php';
    

    //生成银联支付的订单号,默认使用充值类型
    function generateUpOrderNumber($id,$type='charge')
    {
        if (!in_array($type, array('order','charge'))||$type=='charge') {
            $type='charge';
        }
        else
        {
            $type='order';   
        }
        return $type.date('YmdHis').$id;
    }

    function chargeOrderNumGenerate($id)
    {
        return $this->generateUpOrderNumber($id,'charge');
    }


    function payOrderNumGenerate($id)
    {
        return $this->generateUpOrderNumber($id,'order');
    }


     /**
      * 解析回调时的orderNumber,用来定位是充值还是预授权支付
     * @param  array $orderNumber 银联回调时的orderNumber;
     * @return array('type'=>'order/charge','id'=>$id);
     */
    function analyzeOrderNumber($orderNumber)
    {
        $strArr=array();
        if(strpos($orderNumber, 'order')>-1)
        {
            $strArr['type']='order';
            $strArr['id']=substr($orderNumber, strlen('order20130605000000'));
        }
        else
        {
            $strArr['type']='charge';
            $strArr['id']=substr($orderNumber, strlen('charge20130605000000'));
        }
        return $strArr;
    }

    /**
     * @param  array $req 银联回调时的$_REQUEST;
     * @return void
     */
    function backNotify($req)
    {
        //需要判断是充值还是预授权支付,然后通知成功支付后的操作
        $orderNumber=$req['orderNumber'];
        $orderArr=$this->analyzeOrderNumber($orderNumber);
        switch ($orderArr['type']) {
            case 'order':
                require_once (Frm::$ROOT_PATH . 'order_charge/classes/OrderCharge.php');
                $orderCharge = new OrderCharge();
                $orderCharge -> orderPayment($orderArr['id'], $req['settleAmount'], $req['qn'], OrderCharge::$PAYMENT_TYPE_UPMOBILE);
                break;
            
            case 'charge':
                $purchaseObj['orderId'] = $orderArr['id'];
                $purchaseObj['amount']  = $req['settleAmount'];
                require_once (Frm::$ROOT_PATH . 'moneyAccountManager/CashAccountManager.php');
                $cam = new CashAccountManager();
                try{
                    $cam->onPurchaseMobile($purchaseObj);
                }
                catch(Exception $e)
                {
                    $transType = upmp_config::CONSUME_VOID;
                    $amount = $req['settleAmount'];
                    $qn = $req['qn'];
                    $this-> refund($transType,$amount,$qn);
                }
                break;
        }
    }

    function getTn($id,$amount,$type='charge')
    {
        if ($type=='charge') {
            $orderNumber = $this->chargeOrderNumGenerate($id);
            $transType = upmp_config::CONSUME; 
        }
        else
        {
            $orderNumber = $this->payOrderNumGenerate($id);
            $transType = upmp_config::PRE_AUTH;
        }
        $resp = $this->purchase($transType,$orderNumber,$amount);
        $tn = $resp['tn'];
        // echo($tn);
        return $tn; 
    }

    function charge($id,$amount)
    {
        $tn=$this->getTn($id,$amount);
        // echo($tn);
        return $tn;
    }

    function orderPay($id,$amount)
    {
        $tn=$this->getTn($id,$amount,'order');
        // echo($tn);
        return $tn;
    }

    function purchase($transType,$orderNumber,$amount)
    {   
        $req['version']             = upmp_config::$version; // 版本号
        $req['charset']             = upmp_config::$charset; // 字符编码
        $req['transType']           = $transType; // 交易类型
        $req['merId']               = upmp_config::$mer_id; // 商户代码
        $req['backEndUrl']          = upmp_config::$mer_back_end_url; // 通知URL
        $req['frontEndUrl']         = upmp_config::$mer_front_end_url; // 前台通知URL(可选)
        // $req['orderDescription'] = "订单描述";// 订单描述(可选)
        $req['orderTime']           = date("YmdHis"); // 交易开始日期时间yyyyMMddHHmmss
        // $req['orderTimeout']         = date("YmdHis",time()+1800); // 订单超时时间yyyyMMddHHmmss(可选)
        $req['orderNumber']         = $orderNumber; //订单号(商户根据自己需要生成订单号)
        $req['orderAmount']         = $amount; // 订单金额
        $req['orderCurrency']       = "156"; // 交易币种(可选)
        // $req['reqReserved']      = "透传信息"; // 请求方保留域(可选，用于透传商户信息)

        // 保留域填充方法
        $merReserved['test']        = "test";
        $req['merReserved']         = UpmpService::buildReserved($merReserved); // 商户保留域(可选)
        // print_r($req);
        // die();
        $resp = array ();
        $validResp = UpmpService::trade($req, $resp);

        // 商户的业务逻辑argN

        if ($validResp){
            return $resp;
        }else {
            // 服务器应答签名验证失败
            print_r($resp);
        }
    }
    

    //消费撤销,预授权撤销,预授权完成,预授权完成撤销,退货操作
    function refund($transType,$amount,$qn)
    {
        $req['version']         = upmp_config::$version; // 版本号
        $req['charset']         = upmp_config::$charset; // 字符编码
        $req['transType']       = $transType; // 交易类型
        $req['merId']           = upmp_config::$mer_id; // 商户代码
        $req['backEndUrl']      = upmp_config::$mer_back_end_url; // 通知URL
        $req['orderTime']       = date("YmdHis"); // 交易开始日期时间yyyyMMddHHmmss（撤销交易新交易日期，非原交易日期）
        $req['orderNumber']     = date("YmdHiss"); // 订单号（撤销交易新订单号，非原订单号）
        $req['orderAmount']     = $amount; // 订单金额
        $req['orderCurrency']   = "156"; // 交易币种(可选)
        $req['qn']              = $qn; // 查询流水号（原订单支付成功后获取的流水号）
        $req['reqReserved']     = "透传信息"; // 请求方保留域(可选，用于透传商户信息)

        // 保留域填充方法
        $merReserved['test']    = "test";
        $req['merReserved']     = UpmpService::buildReserved($merReserved); // 商户保留域(可选)

        $resp = array ();
        $validResp = UpmpService::trade($req, $resp);

        // 商户的业务逻辑
        if ($validResp){
            // 服务器应答签名验证成功
            return $resp;
        }else {
            // 服务器应答签名验证失败
            print_r($resp);
        }
    }


    //预授权完成接口
    function onRefund($extId, $amount)
    {
        if ($amount==0) {
            $transType = upmp_config::PRE_AUTH_VOID; 
        }   
        else
        {
            $transType = upmp_config::PRE_AUTH_COMPLETE;     
        }
        
        $this->refund($transType,$amount,$extId);
        return array('rspCode'=>'000','logId'=>$extId);
    }

    //消费查询接口
    function query()
    {

    }

    //记录成功支付的银联返回信息
    function log($arr)
    {
        $sql = "INSERT INTO upp_mobile_log (id, order_time, settleDate, respCode, orderNumber, exchangeRate, charset, signature, sysReserved, acqCode, traceNumber, settleCurrency, version, transType, settleAmount, signMethod, transStatus, merId, qn)
         VALUES (NULL, '{$arr['orderTime']}', '{$arr['settleDate']}', '{$arr['respCode']}', '{$arr['orderNumber']}', '{$arr['exchangeRate']}', '{$arr['charset']}', '{$arr['signature']}', '{$arr['sysReserved']}', '{$arr['acqCode']}', '{$arr['traceNumber']}', '{$arr['settleCurrency']}', '{$arr['version']}', '{$arr['transType']}', '{$arr['settleAmount']}', '{$arr['signMethod']}', '{$arr['transStatus']}', '{$arr['merId']}', '{$arr['qn']}')";
        $db = Frm::getInstance() -> getDb();
        $trans = $db -> startTransaction();
        $db -> execSql($sql);
        $trans -> commit();

    }
}
    // $UnionMobilePayManager = new UnionMobilePayManager();
    // $UnionMobilePayManager->charge(11,100);
 ?>
