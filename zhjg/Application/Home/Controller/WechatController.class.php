<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

use Think\Controller;
use Com\Wechat;
use Com\WechatAuth;
use Home\Model\UserMsgModel;
use Home\Model\UserWorkModel;
use Home\Model\BaseUserModel;
use Home\Model\ConstTextModel;

define("TOKEN", "zhihuijingang");

class WechatController extends Controller{
    
    private $_userMsgModel=null;
    private $_userWorkModel=null;
    private $_baseUserModel=null;
    private $_constTextModel=null;


    public function __construct()
    {

        error_reporting(E_ALL);
        ini_set("display_errors", 1);


        parent::__construct();
        $this->_userMsgModel = new UserMsgModel();
        $this->_userWorkModel = new UserWorkModel();
        $this->_baseUserModel=new BaseUserModel();
        $this->_constTextModel= new ConstTextModel();
        $s_language = session("language");
        if(empty($s_language))
        {
            session("language", "zh-cn");
        }
    }

    //该方法用于初次绑定微信公众号验证服务器信息
	public function index2(){
		$this->valid();
	}


    /**
     * 微信消息接口入口
     * 所有发送到微信的消息都会推送到该操作
     * 所以，微信公众平台后台填写的api地址则为该操作的访问地址
     */
    public function index($id = ''){
        //调试
        try{
            //$appid = 'wx5295795b253393ea'; //AppID(应用ID)
            $appid=C('WECHAT_APPID');
            //$token = 'zhihuijingang'; //微信后台填写的TOKEN
            $token =C('WECHAT_APPTOKEN');
            //$crypt = 'x1Qbc9uOpH439nZG5ViRWCc8H8qocoivghi6Ujkeesu'; //消息加密KEY（EncodingAESKey）
            $crypt = C('WECHAT_CRYPT');

            /* 加载微信SDK */
            $wechat = new Wechat($token, $appid, $crypt);
            
            /* 获取请求信息 */
            $data = $wechat->request();

            if($data && is_array($data)){
                /**
                 * 你可以在这里分析数据，决定要返回给用户什么样的信息
                 * 接受到的信息类型有10种，分别使用下面10个常量标识
                 * Wechat::MSG_TYPE_TEXT       //文本消息
                 * Wechat::MSG_TYPE_IMAGE      //图片消息
                 * Wechat::MSG_TYPE_VOICE      //音频消息
                 * Wechat::MSG_TYPE_VIDEO      //视频消息
                 * Wechat::MSG_TYPE_SHORTVIDEO //视频消息
                 * Wechat::MSG_TYPE_MUSIC      //音乐消息
                 * Wechat::MSG_TYPE_NEWS       //图文消息（推送过来的应该不存在这种类型，但是可以给用户回复该类型消息）
                 * Wechat::MSG_TYPE_LOCATION   //位置消息
                 * Wechat::MSG_TYPE_LINK       //连接消息
                 * Wechat::MSG_TYPE_EVENT      //事件消息
                 *
                 * 事件消息又分为下面五种
                 * Wechat::MSG_EVENT_SUBSCRIBE    //订阅
                 * Wechat::MSG_EVENT_UNSUBSCRIBE  //取消订阅
                 * Wechat::MSG_EVENT_SCAN         //二维码扫描
                 * Wechat::MSG_EVENT_LOCATION     //报告位置
                 * Wechat::MSG_EVENT_CLICK        //菜单点击
                 */

                //记录微信推送过来的数据
                file_put_contents('./data.json', json_encode($data));

                /* 响应当前请求(自动回复) */
                //$wechat->response($content, $type);

                /**
                 * 响应当前请求还有以下方法可以使用
                 * 具体参数格式说明请参考文档
                 * 
                 * $wechat->replyText($text); //回复文本消息
                 * $wechat->replyImage($media_id); //回复图片消息
                 * $wechat->replyVoice($media_id); //回复音频消息
                 * $wechat->replyVideo($media_id, $title, $discription); //回复视频消息
                 * $wechat->replyMusic($title, $discription, $musicurl, $hqmusicurl, $thumb_media_id); //回复音乐消息
                 * $wechat->replyNews($news, $news1, $news2, $news3); //回复多条图文消息
                 * $wechat->replyNewsOnce($title, $discription, $url, $picurl); //回复单条图文消息
                 * 
                 */
                
                //执行Demo
                $this->demo($wechat, $data);
            }
        } catch(\Exception $e){
            file_put_contents('./error.json', json_encode($e->getMessage()));
        }
        
    }

    /**
     * DEMO
     * @param  Object $wechat Wechat对象
     * @param  array  $data   接受到微信推送的消息
     */
    private function demo($wechat, $data){

        $appid=C('WECHAT_APPID');
        //$appid = "wx5295795b253393ea";
        //$appsecret = "3d6208234f0f8b763b4898ddb161781c";
        $appsecret=C('WECHAT_APPSECRET');
        $baseuri_test="http://ew-2jjr9u55x.aliapp.com/zhjg/index.php/Home/Testtp/testOauth";
        $enuri_test=urlencode($baseuri_test);
        $testuri="https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$enuri_test}&response_type=code&scope=snsapi_base&state=1#wechat_redirect";

        switch ($data['MsgType']) {
            case Wechat::MSG_TYPE_EVENT:
                switch ($data['Event']) {
                    case Wechat::MSG_EVENT_SUBSCRIBE:
                        $this->_baseUserModel->saveBaseUser($data['FromUserName']);
                        $wText=$this->_constTextModel->getText(C('TEXT_KEY_WELCOME'));
                        //$wechat->replyText('欢迎您关注智慧金刚！');//必须放最后
                        $wechat->replyText($wText);
                        break;

                    case Wechat::MSG_EVENT_UNSUBSCRIBE:
                        //取消关注，记录日志
                        $this->_baseUserModel->deleteBaseUser($data['FromUserName']);
                        break;
                    case Wechat::MSG_EVENT_CLICK:
                        //菜单点击
                        switch ($data['EventKey']) {
                            /*
                            case 'work_input':
                                //$wechat->replyText('录入今日功课');
                                $wechat->replyNewsOnce(
                                "录入今日功课",
                                "打开链接录入今日功课。您的微信OpenID是".$data['FromUserName'], 
                                "http://ap-zhihuijingang.aliapp.com/zhjg/index.php/Home/Testtp/testInputPage?"."OpenID=".$data['FromUserName'],
                                C('WECHAT_HEAD_IMAGE_URL')
                                ); //回复单条图文消息
                                break;
                            case 'work_search':
                                //$wechat->replyText('查询功课');
                                //$queryResult = $this->_userWorkModel->queryMsg($data['FromUserName']);
                                //$wechat->replyText($queryResult);
                                $wechat->replyNewsOnce(
                                "查询功课",
                                "打开链接查询功课。您的微信OpenID是".$data['FromUserName'], 
                                "http://ap-zhihuijingang.aliapp.com/zhjg/index.php/Home/Testtp/testQueryWork?"."OpenID=".$data['FromUserName'],
                                C('WECHAT_HEAD_IMAGE_URL')
                                );
                                break;
                            case 'work_template':
                                $wechat->replyNewsOnce(
                                "批量录入功课",
                                "打开链接查询功课。您的微信OpenID是".$data['FromUserName'], 
                                "http://ap-zhihuijingang.aliapp.com/zhjg/index.php/Home/Testtp/testInputTemplate?"."OpenID=".$data['FromUserName'],
                                C('WECHAT_HEAD_IMAGE_URL')
                                );
                                break;
                            case 'cowork_create':
                                $wechat->replyText('发起共修');
                                break;
                            case 'cowork_search':
                                $wechat->replyText('我发起的共修');
                                break;
                            case 'cowork_join':
                                $wechat->replyText('加入共修');
                                break;
                            case 'testoauth':
                                $wechat->replyText($testuri);
                                break;
                                */
                            case 'aboutus':
                                //$wechat->replyText('关于智慧金刚');
                            /*
                                                        $wechat->replyNewsOnce(
                            "关于智慧金刚",
                            "这是一个测试页面。您的微信OpenID是".$data['FromUserName'], 
                            "http://ap-zhihuijingang.aliapp.com/zhjg/index.php/Home/Testtp/testCss?"."OpenID=".$data['FromUserName'],
                            C('WECHAT_HEAD_IMAGE_URL')
                        ); //回复单条图文消息
                        */  
                                //error_log('aboutus');
                                //error_log(C('TEXT_KEY_WELCOME'));
                                $wText=$this->_constTextModel->getText(C('TEXT_KEY_WELCOME'));
                        //$wechat->replyText('欢迎您关注智慧金刚！');//必须放最后
                                $wechat->replyText($wText);
                                
                                break;
                            default:
                                # code...
                                break;
                        }
                        break;
                    default:
                        $wechat->replyText("欢迎您关注智慧金刚！您的事件类型：{$data['Event']}，EventKey：{$data['EventKey']}, 用户名：{$data['FromUserName']}");
                        break;
                }
                break;

            case Wechat::MSG_TYPE_TEXT:
                switch ($data['Content']) {
                    case '功课':
                        $wechat->replyText('功课');
                        break;

                    case '共修':
                        //$media_id = $this->upload('image');
                        $wechat->replyText('共修');
                        break;
                    case '更多':
                        //$media_id = $this->upload('image');
                        $wechat->replyText('更多');
                        break;
                    
                    default:
                        //error_log($data['FromUserName']);
                        $this->_userMsgModel->addMsg($data['FromUserName'],$data['Content']);
                        /*
                        $input = $this->_userWorkModel->addMsg($data['FromUserName'],$data['Content']);
                        if($input){
                            $wechat->replyText("录入成功！");
                        }else{
                            $wechat->replyText("输入信息有误！");
                        }
                        */

                        //$wechat->replyText("欢迎访问智慧金刚！您输入的内容是：{$data['Content']}, 用户名：{$data['FromUserName']}");
                        break;
                }
                break;
            
            default:
                # code...
                break;
        }
    }

    /**
     * 资源文件上传方法
     * @param  string $type 上传的资源类型
     * @return string       媒体资源ID
     */
    private function upload($type){
        $appid     = 'wx58aebef2023e68cd';
        $appsecret = 'bf818ec2fb49c20a478bbefe9dc88c60';

        $token = session("token");

        if($token){
            $auth = new WechatAuth($appid, $appsecret, $token);
        } else {
            $auth  = new WechatAuth($appid, $appsecret);
            $token = $auth->getAccessToken();

            session(array('expire' => $token['expires_in']));
            session("token", $token['access_token']);
        }

        switch ($type) {
            case 'image':
                $filename = './Public/image.jpg';
                $media    = $auth->materialAddMaterial($filename, $type);
                break;

            case 'voice':
                $filename = './Public/voice.mp3';
                $media    = $auth->materialAddMaterial($filename, $type);
                break;

            case 'video':
                $filename    = './Public/video.mp4';
                $discription = array('title' => '视频标题', 'introduction' => '视频描述');
                $media       = $auth->materialAddMaterial($filename, $type, $discription);
                break;

            case 'thumb':
                $filename = './Public/music.jpg';
                $media    = $auth->materialAddMaterial($filename, $type);
                break;
            
            default:
                return '';
        }

        if($media["errcode"] == 42001){ //access_token expired
            session("token", null);
            $this->upload($type);
        }

        return $media['media_id'];
    }


	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }


	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}

}
