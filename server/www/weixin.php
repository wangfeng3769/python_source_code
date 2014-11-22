<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "eduoauto");
include (dir(__FILE__).'../hfrm/Frm.php');
include Frm::$ROOT_PATH.'weixin/WeixinSession.php';
new WeixinSession();

$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
$wechatObj->responseMsg();
 
 
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
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
 
          //extract post data
        if (!empty($postStr)){

                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $RX_TYPE = trim($postObj->MsgType);            
        switch ($RX_TYPE){                
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
        
        // $this->writeMsg($resultStr);
        echo "$resultStr";

        }else {            
            echo "";            
            exit;        
        }    
     }    

    function writeMsg($resultStr)
    {
         $weixinF=fopen('/home/hoheart/www/www/weixin.txt', 'w+');
            fwrite($weixinF, $resultStr);
            fclose($weixinF);
    }
    private function receiveText($object){    
        $funcFlag = 0;        
        $keyword = trim($object->Content);        
        $resultStr = "";        
        $cityArray = array();        
        $contentStr = "";        
        $needArray = false;        
        $illegal = false;        
        $saytome = false;                
        if (!empty($keyword)&&$keyword.length==11&&){ 
            $html = file_get_contents('http://eduoauto.com/client/index.php?class=SmsCaptcha&item=get&phone=$keyword');
            var_dump($html);
            $contentStr = "请输入您的验证码:";            
            $resultStr = $this->transmitText($object, $contentStr, $funcFlag);                    
        }else {
             break;                
        }    
    }      
    private function receiveEvent($object){       
        $contentStr = "";        
        switch ($object->Event){            
        case "subscribe":                
        $contentStr = "您好，欢迎关注易多汽车共享。新感觉，新体验！输入您的电话号码就能成为我们的会员哦！";                
        break;        
      }        
      $resultStr = $this->transmitText($object, $contentStr);       
       return $resultStr;    
    }        
       private function transmitText($object, $content, $flag = 0){        
        $textTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[text]]></MsgType>
        <Content><![CDATA[%s]]></Content>
        <FuncFlag>%d</FuncFlag>
        </xml>";        
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
        $this-> writeMsg($resultStr);        
        return $resultStr;    
       }
        private function checkSignature(){
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