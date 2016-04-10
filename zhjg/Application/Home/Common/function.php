<?php
/**
 * 常量定义区
 * **/
define('HAVE_DATA',1);//有数据返回
define('NONE_DATA',0);//没有数据返回
define('SUCCESS_CODE',0);      //成功
define('ERROR_CODE',1);      //失败
define("SHOW_NUMBER",24);//每页显示个数
/**
 * 模块常量定义
 * **/
define('MOD_AIR',1000);//航空
define('MOD_TRAIN',2000);//铁路 

/**
 * JSON数据封装
 * @param $mod 模块CODE
 * @param $code 返回状态 ０成功　非零表示失败
 * @param $msg 返回信息
 * @param $isdata 是否有数据返回
 * @param $data 返回的数据
 * @param $isjump 是否跳转
 * @param $url 跳转路径
 * @author ryz <609873271@qq.com>
 * @date 2014-09-23
 * **/
function jsonResult($mod,$code,$msg='',$isdata=NONE_DATA,$data=null,$isjump=0,$url='')
{
	$result['mod']=$mod;
	$result['code']=$code;
	$result['msg']=urlencode($msg);
	$result['isdata']=$isdata;
	//$result['data'] = $data;
	$d["data"] = $data;
	$result['data'] = $d;
	$result['isjump']=$isjump;
	$result['url']=$url;
	header("content-type:text/html; charset=UTF-8");
	echo urldecode(json_encode($result));
}


/**
解析火车城市XML
zh_cn表示中文城市
en 表示城市拼音
**/
/*
header("Content-type: text/html; charset=UTF8");
function parseXml($xmlStr)
{
 if(!empty($xmlStr))
 {
 	$p = xml_parser_create();
 	xml_parse_into_struct($p, $xmlStr, $vals, $index);
 	xml_parser_free($p);
 	$data["zh-cn"] = $vals[1]["value"];
 	$data["en"] = $vals[3]["value"];
 	return $data;
 }
 return null;
}
*/

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

function getWechatToken(){

	$appid=C('WECHAT_APPID');
	//$appid = "wx5295795b253393ea";
	//$appsecret = "3d6208234f0f8b763b4898ddb161781c";
	$appsecret=C('WECHAT_APPSECRET');



	$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
	//var_dump($url);
	//echo "<br>";
	$output = https_request($url);
	$jsoninfo = json_decode($output, true);

	//var_dump($jsoninfo);

	$access_token = $jsoninfo["access_token"];
	return $access_token;
}

function getWechatUserInfo($openID=null){
	$access_token=getWechatToken();
	$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token;
	$url=$url."&openid=".$openID;
	$url=$url."&lang=zh_CN";
	$output = https_request($url);
	$jsoninfo = json_decode($output, true);
	//var_dump($jsoninfo);
	return $jsoninfo;
}

function getOidByOauth($code=null){
	
	$appid=C('WECHAT_APPID');
	//$appid = "wx5295795b253393ea";
	//$appsecret = "3d6208234f0f8b763b4898ddb161781c";
	$appsecret=C('WECHAT_APPSECRET');
	$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code";
	$output = https_request($url);
	$jsoninfo = json_decode($output, true);
	//error_log('in getOidByOauth');
	//error_log($output);
	return $jsoninfo["openid"];
}

function getOauthURL($baseURL){
	$enuri=urlencode($baseURL);
	$newUrl="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx5295795b253393ea&redirect_uri=".$enuri."&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
	return $newUrl;
}

/**
 * 获取当前系统时间戳
 * @author ryz <609873271@qq.com>
 * @date 2015-01-23
 * **/
function getTime()
{
    return time();
}
