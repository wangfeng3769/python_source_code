<?php
define("TOKEN", "eduoauto999");
$wechatObj = new wechatCallbackapiTest();
// $wechatObj->writeMsg();
$wechatObj->valid();
// $wechatObj->responseMsg();
// $UserName='o0e3fjll23YJ92-lNBUkwrtgZWJY';
// $captcha1='12345';
class wechatCallbackapiTest
{
    
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            switch ($RX_TYPE)
            {
                case "text":
                    $resultStr = $this->receiveText($postObj);
                    break;
                case "event":
                    $resultStr = $this->receiveEvent($postObj);
                    break;
                default:
                    $resultStr = "unknow msg type: ".$RX_TYPE;
                    break;
            }
            echo $resultStr;
        }else {
            echo "";
            exit;
        }
    }

    function writeMsg($echoStr)
    {
         $weixinF=fopen('/home/hoheart/www/www/weixin.txt', 'w+');
            fwrite($weixinF, $echoStr);
            fclose($weixinF);
    }
    
    private function receiveText($object)
    {
        $funcFlag = 0;
        $keyword = trim($object->Content);
        $user_name = trim($object->FromUserName);
        $resultStr = "";
        //$cityArray = array();
        $contentStr = "";
        $needArray = false;
        $illegal = false;
        $saytome = false;
        $captcha = "";
        $event_type=substr($keyword,0,2);
        
        if ($event_type=='zc') {
            $phone = substr($keyword, 2);
            if ($phone != ""&&strlen($phone)==11){
                
                $html = file_get_contents("http://192.168.0.105/client/index.php?class=SmsCaptcha&item=get&phone=$phone");
                var_dump($html);
                $contentStr = "验证码会以短信的形式发送给您，请您回复'yz+收到的验证码'";
                $resultStr = $this->transmitText($object, $contentStr, $funcFlag);                                                                                            
                return $resultStr;
            }else{
                $contentStr = "亲，您的手机号码输入有误喔~";
                $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
                return $resultStr;
            }
        }else 
            if($event_type == 'yz'){
            $captcha = substr($keyword, 2);
            $this->writeMsg($keyword);
            if($captcha!=""&&strlen($captcha)==5){

                $contentStr = "注册成功";
                $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
                return $resultStr;
            }else{
                $contentStr = "您的验证码输入有误哦！";
                $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
                return $resultStr;
            }
        }
        
    }


    private function receiveEvent($object)
    {
        $contentStr = "";
        switch ($object->Event)
        {
            case "subscribe":
                $contentStr = "您好，欢迎关注易多汽车共享。新感觉，新体验，回复'zc+您的手机号码'即可成为我们的会员哦，如果您已注册，那么即可免费体验一小时自驾，

用车时请提前与小Y预约，或者拨打客服电话400-600-3430进行预约！O(∩_∩)O!";
                break;
        }
        $resultStr = $this->transmitText($object, $contentStr);
        return $resultStr;
    }
    
    private function transmitText($object, $content, $flag = 0)
    {
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>%d</FuncFlag>
                    </xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
        return $resultStr;
    }
    private function checkSignature()
    {

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];    
         
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}

?>