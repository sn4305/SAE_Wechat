<?php

/**

  * wechat php test

  */

//define your token

define("TOKEN", "yangdong");

$wechatObj = new wechatCallbackapiTest();


if($_GET['echostr']){
    $wechatObj->valid(); //如果发来了echostr则进行验证
}else{
    $wechatObj->responseMsg(); //如果没有echostr，则返回消息
}


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

                $fromUsername = $postObj->FromUserName;

                $toUsername = $postObj->ToUserName;

                $keyword = trim($postObj->Content);        

if(!empty( $keyword ))

{
        if (strstr($keyword, "温度")) {
        $con = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS); 
        mysql_select_db("app_t0ny0ung", $con);//修改数据库名
 
        $result = mysql_query("SELECT * FROM sensor");
        while($arr = mysql_fetch_array($result)){
          if ($arr['ID'] == 1) {
                  $tempr = $arr['data'];
          }
        }
        mysql_close($con);
 
    $retMsg = "报告老大："."\n"."室温为".$tempr."℃ ";
}else if (strstr($keyword, "开灯")) {
        $con = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS); 
 
 
        $dati = date("h:i:sa");
        mysql_select_db("app_t0ny0ung", $con);//修改数据库名
 
        $sql ="UPDATE switch SET timestamp='$dati',state = '1'
        WHERE ID = '1'";//修改开关状态值
 
        if(!mysql_query($sql,$con)){
            die('Error: ' . mysql_error());
        }else{
                mysql_close($con);
                $retMsg = "是， 老大， 让我亮瞎你的眼！";
        }
}else if (strstr($keyword, "关灯")) {
        $con = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS); 
 
 
        $dati = date("h:i:sa");
        mysql_select_db("app_t0ny0ung", $con);//修改数据库名
 
        $sql ="UPDATE switch SET timestamp='$dati',state = '0'
        WHERE ID = '1'";//修改开关状态值
 
        if(!mysql_query($sql,$con)){
            die('Error: ' . mysql_error());
        }else{
                mysql_close($con);
                $retMsg = "是， 老大， 关灯走人";
        }        
}else{
        $retMsg = "仅支持以下命令： 开灯，关灯， 温度";
}
 
//装备XML
$retTmp = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                <FuncFlag>0</FuncFlag>
                </xml>";
$resultStr = sprintf($retTmp, $fromUsername, $toUsername, time(), $retMsg);
 
//反馈到微信服务器
echo $resultStr;

}               else{

                echo "Input something...";

                     }

                       }

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
