<?php

/**
 * 类名：配置类
 * 功能：配置类
 * 类属性：公共类
 * 版本：1.0
 * 日期：2012-10-11
 * 作者：中国银联UPMP团队
 * 版权：中国银联
 * 说明：以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己的需要，按照技术文档编写,并非一定要使用该代码。该代码仅供参考。
 * */

class upmp_config
{
    
    static $timezone                = "Asia/Shanghai"; //时区
    
    static $version                 = "1.0.0"; // 版本号
    static $charset                 = "UTF-8"; // 字符编码
    static $sign_method             = "MD5"; // 签名方法，目前仅支持MD5
    
    

    //测试环境
    static $mer_id                  = "860000000000055"; // 商户号
    static $security_key            = "fHuMPinyZIHmHQWm7kq7y5PRtoJQVfyX"; // 商户密钥
    static $upmp_trade_url          = "http://222.66.233.198:8080/gateway/merchant/trade";
    static $upmp_query_url          = "http://222.66.233.198:8080/gateway/merchant/query";  

    //正式环境
    // static $mer_id                  = "898110148994677"; // 商户号
    // static $security_key            = "aM2ioz0qrPzzX51UbKuG2TB8T3w9OQ8B"; // 商户密钥
    // static $upmp_trade_url          = "https://202.96.255.146/gateway/merchant/trade";
    // static $upmp_query_url          = "https://202.96.255.146/gateway/merchant/query";


    static $mer_back_end_url        = "http://www.eduoauto.com/sns/PayAPI/UPPop/example/router.php?notify=mb"; // 后台通知地址
    static $mer_front_end_url       = "http://www.eduoauto.com/sns/PayAPI/UPPop/example/router.php?notify=fb"; // 前台通知地址
    


    
    
    const VERIFY_HTTPS_CERT         = false;
    
    const RESPONSE_CODE_SUCCESS     = "00"; // 成功应答码
    const SIGNATURE                 = "signature"; // 签名
    const SIGN_METHOD               = "signMethod"; // 签名方法
    const RESPONSE_CODE             = "respCode"; // 应答码
    const RESPONSE_MSG              = "respMsg"; // 应答信息
    
    const QSTRING_SPLIT             = "&"; // &
    const QSTRING_EQUAL             = "="; // =
    
}

?>
