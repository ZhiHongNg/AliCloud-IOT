<?php
namespace AlicloudIotFramework;
session_start();

class Iot{
    private static $appKey;
    private static $appSecret;
    private static $Id;
    private static $productKey;


    static function init($appKey,$appSecret,$projectId,$productKey){
        self::$appKey = $appKey;
        self::$appSecret = $appSecret;
        self::$Id = $projectId;
        self::$productKey = $productKey;
        self::getToken();
    }
    static function getUserList($offset=0,$count=50){
        return self::request('/cloud/account/queryIdentityByPage','1.0.4',["offset"=>intval($offset),"count"=>intval($count)]);
    }
    static function getUser($identityId){
        return self::request('/cloud/account/getByIdentityId','1.0.4',["identityId"=>$identityId,"openIdAppKey"=>self::$appKey]);
    }
    static function getUserBindDevice($identityId){
        return self::request('/cloud/device/queryByUser','1.0.6',["identityId"=>$identityId,"openIdAppKey"=>self::$appKey]);
    }
    static function getDeviceAttribute($iotId){
        return self::request('/cloud/thing/properties/get','1.0.2',["iotId"=>$iotId,'productKey'=>self::$productKey]);
    }
    static function getDeviceInfo($iotId){
        return self::request('/cloud/thing/info/get','1.0.2',["iotId"=>$iotId,'productKey'=>self::$productKey]);
    }
    static function sendMessageToDevice($iotId,$messageContent){
        return self::request('/living/cloud/device/customizedmessage/notify','1.0.0',["iotId"=>$iotId,'productKey'=>self::$productKey,'messageContent'=>$messageContent]);
    }
    static function sendServeToDevice($iotId,$deviceName,$identifier,$args){
        return self::request('/cloud/thing/service/invoke','1.0.2',["iotId"=>$iotId,'deviceName'=>$deviceName,'productKey'=>self::$productKey,'identifier'=>$identifier,'args'=>$args]);
    }
    static function setDevice($iotId,$deviceName,$item){
        return self::request('/cloud/thing/properties/set','1.0.2',["iotId"=>$iotId,'deviceName'=>$deviceName,'productKey'=>self::$productKey,'items'=>$item]);
    }
    static function getToken($reFresh=false){
        if($reFresh){
            $data =  self::request('/cloud/token','1.0.1',['grantType'=>"project","res"=>"a123umecKcfV80WT"]);
            $_SESSION['token'] = $data;
            $_SESSION['token']['expireIn'] = time()+$_SESSION['token']['expireIn'];
            return $_SESSION['token']['cloudToken'];
        }
        if(isset($_SESSION['token'])){
            if($_SESSION['token']['cloudToken']&&$_SESSION['token']['expireIn']>=time()){
                return $_SESSION['token']['cloudToken'];
            }
        }
        $data =  self::request('/cloud/token','1.0.1',['grantType'=>"project","res"=>"a123umecKcfV80WT"]);
        $_SESSION['token'] = $data;
        $_SESSION['token']['expireIn'] = time()+$_SESSION['token']['expireIn'];
        return $_SESSION['token']['cloudToken'];
    }
    static function request($path,$version,$param) {
        $host      = "https://api.link.aliyun.com";
        $request = new HttpRequest($host, $path, HttpMethod::POST, self::$appKey, self::$appSecret);
        //设置API版本和参数，其中，res为授权的资源ID。grantType为project时，res的值为project的ID。
        $bodyArr = [];
        $bodyArr['id'] = self::$Id;
        $bodyArr['version'] = "1.0";
        if($path=='/cloud/token'){
            $bodyArr['request'] = array('apiVer'=>$version);
        }else{
            $bodyArr['request'] = array('apiVer'=>$version,"cloudToken"=>self::getToken());
        }
        $bodyArr['params'] = $param;
        $body = json_encode($bodyArr);
        //设定Content-Type
        $request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_TYPE,
            ContentType::CONTENT_TYPE_JSON);

        //设定Accept
        $request->setHeader(HttpHeader::HTTP_HEADER_ACCEPT,
            ContentType::CONTENT_TYPE_JSON);

        if (strlen($body) > 0) {
            $request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_MD5,
                base64_encode(md5($body, true)));
            $request->setBodyString($body);
        }

        //指定参与签名的header
        $request->setSignHeader(SystemHeader::X_CA_TIMESTAMP);

        $response = HttpClient::execute($request);

        $responseData = json_decode($response->getBody(),1);
        if($responseData['code']==20017){
            self::getToken(true);
            self::request($path,$version,$param);
            return;
        }
//        print_r($responseData);
        return $responseData['data'];
    }

    /**
     *method=GET请求示例
     */
    public function doGet() {
        //域名后、query前的部分
        $path = "/get";
        $request = new HttpRequest($this::$host, $path, HttpMethod::GET, $this::$appKey, $this::$appSecret);

        //设定Content-Type，根据服务器端接受的值来设置
        $request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_TYPE, ContentType::CONTENT_TYPE_TEXT);

        //设定Accept，根据服务器端接受的值来设置
        $request->setHeader(HttpHeader::HTTP_HEADER_ACCEPT, ContentType::CONTENT_TYPE_TEXT);
        //如果是调用测试环境请设置
        //$request->setHeader(SystemHeader::X_CA_STAG, "TEST");


        //注意：业务header部分，如果没有则无此行(如果有中文，请做Utf8ToIso88591处理)
        //mb_convert_encoding("headervalue2中文", "ISO-8859-1", "UTF-8");
        $request->setHeader("b-header2", "headervalue2");
        $request->setHeader("a-header1", "headervalue1");

        //注意：业务query部分，如果没有则无此行；请不要、不要、不要做UrlEncode处理
        $request->setQuery("b-query2", "queryvalue2");
        $request->setQuery("a-query1", "queryvalue1");

        //指定参与签名的header
        $request->setSignHeader(SystemHeader::X_CA_TIMESTAMP);
        $request->setSignHeader("a-header1");
        $request->setSignHeader("b-header2");

        $response = HttpClient::execute($request);
        print_r($response);
    }

    /**
     *method=POST且是表单提交，请求示例
     */
    public function doPostForm() {
        //域名后、query前的部分
        $path = "/postform";
        $request = new HttpRequest($this::$host, $path, HttpMethod::POST, $this::$appKey, $this::$appSecret);

        //设定Content-Type，根据服务器端接受的值来设置
        $request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_TYPE, ContentType::CONTENT_TYPE_FORM);

        //设定Accept，根据服务器端接受的值来设置
        $request->setHeader(HttpHeader::HTTP_HEADER_ACCEPT, ContentType::CONTENT_TYPE_JSON);
        //如果是调用测试环境请设置
        //$request->setHeader(SystemHeader::X_CA_STAG, "TEST");


        //注意：业务header部分，如果没有则无此行(如果有中文，请做Utf8ToIso88591处理)
        //mb_convert_encoding("headervalue2中文", "ISO-8859-1", "UTF-8");
        $request->setHeader("b-header2", "headervalue2");
        $request->setHeader("a-header1", "headervalue1");

        //注意：业务query部分，如果没有则无此行；请不要、不要、不要做UrlEncode处理
        $request->setQuery("b-query2", "queryvalue2");
        $request->setQuery("a-query1", "queryvalue1");

        //注意：业务body部分，如果没有则无此行；请不要、不要、不要做UrlEncode处理
        $request->setBody("b-body2", "bodyvalue2");
        $request->setBody("a-body1", "bodyvalue1");

        //指定参与签名的header
        $request->setSignHeader(SystemHeader::X_CA_TIMESTAMP);
        $request->setSignHeader("a-header1");
        $request->setSignHeader("b-header2");

        $response = HttpClient::execute($request);
        print_r($response);
    }

    /**
     *method=POST且是非表单提交，请求示例
     */
    public function doPostString() {
        //域名后、query前的部分
        $path = "/poststring";
        $request = new HttpRequest($this::$host, $path, HttpMethod::POST, $this::$appKey, $this::$appSecret);
        //传入内容是json格式的字符串
        $bodyContent = "{\"inputs\": [{\"image\": {\"dataType\": 50,\"dataValue\": \"base64_image_string(此行)\"},\"configure\": {\"dataType\": 50,\"dataValue\": \"{\"side\":\"face(#此行此行)\"}\"}}]}";

        //设定Content-Type，根据服务器端接受的值来设置
        $request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_TYPE, ContentType::CONTENT_TYPE_JSON);

        //设定Accept，根据服务器端接受的值来设置
        $request->setHeader(HttpHeader::HTTP_HEADER_ACCEPT, ContentType::CONTENT_TYPE_JSON);
        //如果是调用测试环境请设置
        //$request->setHeader(SystemHeader::X_CA_STAG, "TEST");


        //注意：业务header部分，如果没有则无此行(如果有中文，请做Utf8ToIso88591处理)
        //mb_convert_encoding("headervalue2中文", "ISO-8859-1", "UTF-8");
        $request->setHeader("b-header2", "headervalue2");
        $request->setHeader("a-header1", "headervalue1");

        //注意：业务query部分，如果没有则无此行；请不要、不要、不要做UrlEncode处理
        $request->setQuery("b-query2", "queryvalue2");
        $request->setQuery("a-query1", "queryvalue1");

        //注意：业务body部分，不能设置key值，只能有value
        if (0 < strlen($bodyContent)) {
            $request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_MD5, base64_encode(md5($bodyContent, true)));
            $request->setBodyString($bodyContent);
        }

        //指定参与签名的header
        $request->setSignHeader(SystemHeader::X_CA_TIMESTAMP);
        $request->setSignHeader("a-header1");
        $request->setSignHeader("b-header2");

        $response = HttpClient::execute($request);
        print_r($response);
    }


    /**
     *method=POST且是非表单提交，请求示例
     */
    public function doPostStream() {
        //域名后、query前的部分
        $path = "/poststream";
        $request = new HttpRequest($this::$host, $path, HttpMethod::POST, $this::$appKey, $this::$appSecret);
        //Stream的内容
        $bytes = array();
        //传入内容是json格式的字符串
        $bodyContent = "{\"inputs\": [{\"image\": {\"dataType\": 50,\"dataValue\": \"base64_image_string(此行)\"},\"configure\": {\"dataType\": 50,\"dataValue\": \"{\"side\":\"face(#此行此行)\"}\"}}]}";

        //设定Content-Type，根据服务器端接受的值来设置
        $request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_TYPE, ContentType::CONTENT_TYPE_STREAM);

        //设定Accept，根据服务器端接受的值来设置
        $request->setHeader(HttpHeader::HTTP_HEADER_ACCEPT, ContentType::CONTENT_TYPE_JSON);
        //如果是调用测试环境请设置
        //$request->setHeader(SystemHeader::X_CA_STAG, "TEST");


        //注意：业务header部分，如果没有则无此行(如果有中文，请做Utf8ToIso88591处理)
        //mb_convert_encoding("headervalue2中文", "ISO-8859-1", "UTF-8");
        $request->setHeader("b-header2", "headervalue2");
        $request->setHeader("a-header1", "headervalue1");

        //注意：业务query部分，如果没有则无此行；请不要、不要、不要做UrlEncode处理
        $request->setQuery("b-query2", "queryvalue2");
        $request->setQuery("a-query1", "queryvalue1");

        //注意：业务body部分，不能设置key值，只能有value
        foreach($bytes as $byte) {
            $bodyContent .= chr($byte);
        }
        if (0 < strlen($bodyContent)) {
            $request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_MD5, base64_encode(md5($bodyContent, true)));
            $request->setBodyStream($bodyContent);
        }

        //指定参与签名的header
        $request->setSignHeader(SystemHeader::X_CA_TIMESTAMP);
        $request->setSignHeader("a-header1");
        $request->setSignHeader("b-header2");

        $response = HttpClient::execute($request);
        print_r($response);
    }

    //method=PUT方式和method=POST基本类似，这里不再举例

    /**
     *method=DELETE请求示例
     */
    public function doDelete() {
        //域名后、query前的部分
        $path = "/delete";
        $request = new HttpRequest($this::$host, $path, HttpMethod::DELETE, $this::$appKey, $this::$appSecret);

        //设定Content-Type，根据服务器端接受的值来设置
        $request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_TYPE, ContentType::CONTENT_TYPE_TEXT);

        //设定Accept，根据服务器端接受的值来设置
        $request->setHeader(HttpHeader::HTTP_HEADER_ACCEPT, ContentType::CONTENT_TYPE_TEXT);
        //如果是调用测试环境请设置
        //$request->setHeader(SystemHeader::X_CA_STAG, "TEST");


        //注意：业务header部分，如果没有则无此行(如果有中文，请做Utf8ToIso88591处理)
        //mb_convert_encoding("headervalue2中文", "ISO-8859-1", "UTF-8");
        $request->setHeader("b-header2", "headervalue2");
        $request->setHeader("a-header1", "headervalue1");

        //注意：业务query部分，如果没有则无此行；请不要、不要、不要做UrlEncode处理
        $request->setQuery("b-query2", "queryvalue2");
        $request->setQuery("a-query1", "queryvalue1");

        //指定参与签名的header
        $request->setSignHeader(SystemHeader::X_CA_TIMESTAMP);
        $request->setSignHeader("a-header1");
        $request->setSignHeader("b-header2");

        $response = HttpClient::execute($request);
        print_r($response);
    }


    /**
     *method=HEAD请求示例
     */
    public function doHead() {
        //域名后、query前的部分
        $path = "/head";
        $request = new HttpRequest($this::$host, $path, HttpMethod::HEAD, $this::$appKey, $this::$appSecret);

        //设定Content-Type，根据服务器端接受的值来设置
        $request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_TYPE, ContentType::CONTENT_TYPE_TEXT);

        //设定Accept，根据服务器端接受的值来设置
        $request->setHeader(HttpHeader::HTTP_HEADER_ACCEPT, ContentType::CONTENT_TYPE_TEXT);
        //如果是调用测试环境请设置
        //$request->setHeader(SystemHeader::X_CA_STAG, "TEST");


        //注意：业务header部分，如果没有则无此行(如果有中文，请做Utf8ToIso88591处理)
        //mb_convert_encoding("headervalue2中文", "ISO-8859-1", "UTF-8");
        $request->setHeader("b-header2", "headervalue2");
        $request->setHeader("a-header1", "headervalue1");

        //注意：业务query部分，如果没有则无此行；请不要、不要、不要做UrlEncode处理
        $request->setQuery("b-query2", "queryvalue2");
        $request->setQuery("a-query1", "queryvalue1");

        //指定参与签名的header
        $request->setSignHeader(SystemHeader::X_CA_TIMESTAMP);
        $request->setSignHeader("a-header1");
        $request->setSignHeader("b-header2");

        $response = HttpClient::execute($request);
        print_r($response);
    }
}