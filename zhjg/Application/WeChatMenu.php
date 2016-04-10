<?php

$appid = "wx5295795b253393ea";
$appsecret = "3d6208234f0f8b763b4898ddb161781c";
$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";

$output = https_request($url);
$jsoninfo = json_decode($output, true);

$access_token = $jsoninfo["access_token"];




//测试页面认证回调
$baseuri_test="http://ap-zhihuijingang.aliapp.com/zhjg/index.php/Home/Testtp/testOauth";
$enuri_test=urlencode($baseuri_test);
//echo $enuri_test;

$jsonmenu = '{
      "button":[
      {
            "name":"我的功课",
            "sub_button":[
            {
               "type":"view",
               "name":"添加功课",
               "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx5295795b253393ea&redirect_uri=http%3A%2F%2Fap-zhihuijingang.aliapp.com%2Fzhjg%2Findex.php%2FHome%2FTesttp%2FtestInputPage&response_type=code&scope=snsapi_base&state=1#wechat_redirect"
  
            },
            {
               "type":"view",
               "name":"模版导入功课",
               "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx5295795b253393ea&redirect_uri=http%3A%2F%2Fap-zhihuijingang.aliapp.com%2Fzhjg%2Findex.php%2FHome%2FTesttp%2FtestInputTemplate&response_type=code&scope=snsapi_base&state=1#wechat_redirect"
  
            },
            {
               "type":"view",
               "name":"查询功课",
               "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx5295795b253393ea&redirect_uri=http%3A%2F%2Fap-zhihuijingang.aliapp.com%2Fzhjg%2Findex.php%2FHome%2FTesttp%2FtestQueryWork&response_type=code&scope=snsapi_base&state=1#wechat_redirect"
  
            }]
       },
       {
           "name":"共修",
           "sub_button":[
            {
               "type":"view",
               "name":"发起新的共修",
               "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx5295795b253393ea&redirect_uri=http%3A%2F%2Fap-zhihuijingang.aliapp.com%2Fzhjg%2Findex.php%2FHome%2FCowork%2FnewCowork&response_type=code&scope=snsapi_base&state=1#wechat_redirect"
            },
            {
               "type":"view",
               "name":"我发起的共修",
               "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx5295795b253393ea&redirect_uri=http%3A%2F%2Fap-zhihuijingang.aliapp.com%2Fzhjg%2Findex.php%2FHome%2FCowork%2FmyOpenCoworkList&response_type=code&scope=snsapi_base&state=1#wechat_redirect"
            },
            {
               "type":"view",
               "name":"共修邀请",
               "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx5295795b253393ea&redirect_uri=http%3A%2F%2Fap-zhihuijingang.aliapp.com%2Fzhjg%2Findex.php%2FHome%2FCoworkJoin%2FmyJoinCoworkList&response_type=code&scope=snsapi_base&state=1#wechat_redirect"
            }]
       

       },
       {
           "name":"更多",
           "sub_button":[
            {
               "type":"click",
               "name":"关于我们",
               "key":"aboutus"
            }]
       }]
 }';


$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
$result = https_request($url, $jsonmenu);
var_dump($result);
//echo "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$enuri_test}&response_type=code&scope=snsapi_base&state=1#wechat_redirect";


function https_request($url,$data = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

?>