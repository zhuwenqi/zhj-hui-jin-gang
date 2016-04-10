<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\UserMsgModel;
use Home\Model\UserWorkModel;
use Home\Model\BaseUserModel;
class TesttpController extends Controller {

    private $_userMsgModel=null;
    private $_userWorkModel=null;
    private $_baseUserModel=null;

    public function __construct()
    {

        error_reporting(E_ALL);
        ini_set("display_errors", 1);


        parent::__construct();
        $this->_userMsgModel = new UserMsgModel();
        $this->_userWorkModel = new UserWorkModel();
        $this->_baseUserModel=new BaseUserModel();
        $s_language = session("language");
        if(empty($s_language))
        {
            session("language", "zh-cn");
        }
    }


    public function index(){
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>!!!!!!!!欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
    }

    public function testdb(){//测试写数据库
    	error_log('in controller');
        $this->_userMsgModel->addMsg("222222","4444444");

    }
    public function testInputWork(){//测试录入消息
        error_log('in controller');
        $this->_userWorkModel->addMsg("222222","test123+123");

    }

    public function testQuery(){//测试查询数据
        $this->_userWorkModel->queryMsg("oMWyDwwtqVRU33zBB492IOz_fBkI");
    }

    public function testGetOpenID(){

        $oid = I('get.OpenID');
        $pageStr = '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style>';
        $pageStr=$pageStr.'<div style="padding: 24px 48px;">';
        $pageStr=$pageStr.'<h1>:)</h1><p>!欢迎使用 <b>智慧金刚</b>！</p><br/>';
        $pageStr=$pageStr.'Your Wechat OpenID='.$oid;
        $pageStr=$pageStr.'</div>';

        $this->show($pageStr,'utf-8');
  
    }

    public function testGetUserInfo(){

        $oid = I('get.OpenID');
        $baseuser = $this->_baseUserModel->getBaseUser($oid);
        $pageStr = '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style>';
        $pageStr=$pageStr.'<div style="padding: 24px 48px;">';
        $pageStr=$pageStr.'<h1>:)</h1><p>!欢迎使用 <b>智慧金刚</b>！</p><br/>';
        $pageStr=$pageStr.'Your Wechat nickname ='.$baseuser["nickname"];
        $pageStr=$pageStr.'</div>';

        $this->show($pageStr,'utf-8');
  
    }

    public function testCss(){
        $this->display();
    }

    public function testToast(){
        $this->display();
    }

        public function testWeui(){
        $this->display();
    }

    public function testInputPage(){
        //$oid = I('get.OpenID');


        $code=$_GET["code"];
        $oid=getOidByOauth($code);


        $baseuser = $this->_baseUserModel->getBaseUser($oid);

        $queryWork = $this->_userWorkModel->queryTodayWork($oid);
        $this->assign('res',$queryWork);


        $this->assign('nickname',$baseuser["nickname"]);
        $this->assign('openid',$oid);
        $this->assign('inputDate',date('Y-m-d'));
        $this->display();
    }

    public function testInputHandle(){
        $iWorkName = $_POST['inputWorkName'];
        $iWorkNumber = $_POST['inputWorkNumber'];
        $iOid=$_POST['openid'];
        $iNickname=$_POST['nickname'];

        $iInputDate=$_POST['inputDate'];
        $inputMode1=$_POST['radio1'];


        $this->assign('inputDate',$iInputDate);
        //echo $iInputDate;

        $this->assign('openid',$iOid);
        $this->assign('nickname',$iNickname);


        $this->_userWorkModel->addWork($iOid,$iWorkName,$iWorkNumber,$inputMode1,$iInputDate);

        $queryWork = $this->_userWorkModel->queryWorkByDate($iOid,$iInputDate);
        $this->assign('res',$queryWork);

        //$this->success('新增成功', 'testInputPage');
        //$this->redirect('testInputPage','OpenID='.$iOid,1,'页面跳转中...');
        //redirect('testInputPage');
        //redirect('testInputPage', 1, '页面跳转中...');
        /*
        $outPutStr = $iWorkName."_".$iWorkNumber."_".$iOid;
        $this->assign('outPutStr',$outPutStr);
        $this->display('testOutput');
        */
        $this->display('Testtp/testInputPage');
    }

    public function testQueryWork(){
        //$oid = I('get.OpenID');

        $code=$_GET["code"];
        $oid=getOidByOauth($code);

        $baseuser = $this->_baseUserModel->getBaseUser($oid);

        $queryWork = $this->_userWorkModel->queryTodayWork($oid);
        $this->assign('res',$queryWork);


        $this->assign('nickname',$baseuser["nickname"]);
        $this->assign('openid',$oid);
        $this->assign('querydate',date('Y-m-d'));
        $this->display();
    }

    public function testQueryHandle(){
        $iOid=$_POST['openid'];
        $iNickname=$_POST['nickname'];
        $iQueryDate=$_POST['queryDate'];

        $queryWork = $this->_userWorkModel->queryWorkByDate($iOid,$iQueryDate);
        $this->assign('res',$queryWork);

        $this->assign('nickname',$iNickname);
        $this->assign('openid',$iOid);
        $this->assign('querydate',$iQueryDate);
        $this->display();
    }


    public function testInputTemplate(){
        //$oid = I('get.OpenID');

        $code=$_GET["code"];
        $oid=getOidByOauth($code);

        $baseuser = $this->_baseUserModel->getBaseUser($oid);

        $queryWork = $this->_userWorkModel->queryWorkTemplate($oid);
        $this->assign('res_template',$queryWork);

        $queryWork2 = $this->_userWorkModel->queryTodayWork($oid);
        $this->assign('res_today',$queryWork2);

        $this->assign('nickname',$baseuser["nickname"]);
        $this->assign('openid',$oid);

        $this->assign('inputDate',date('Y-m-d'));
        //error_log('testInputTemplate');
        $this->display();
    }

    public function testInputTemplateHandle(){
        //$iWorkName = $_POST['inputWorkName'];
        //$iWorkNumber = $_POST['inputWorkNumber'];
        $iOid=$_POST['openid'];
        $iNickname=$_POST['nickname'];
        $iInputDate=$_POST['inputDate'];
        $inputMode1=$_POST['radio1'];
        //var_dump($inputMode1);
        //error_log($inputMode1);

        $queryWork = $this->_userWorkModel->queryWorkTemplate($iOid);
        foreach ($queryWork as $record) {
            //$result = $result.$record["workname"].':'.$record["readnumber"]."\r";//必须小写
            //echo $record["workname"];
            $iReadNumber=$_POST[$record["workname"]];

            $this->_userWorkModel->addWork($iOid,$record["workname"],$iReadNumber,$inputMode1,$iInputDate);

        }

        $queryWork = $this->_userWorkModel->queryWorkTemplate($iOid);
        $this->assign('res_template',$queryWork);

        $queryWork2 = $this->_userWorkModel->queryWorkByDate($iOid,$iInputDate);
        $this->assign('res_today',$queryWork2);

        $this->assign('nickname',$iNickname);
        $this->assign('openid',$iOid);
        $this->assign('inputDate',$iInputDate);
        error_log('testInputTemplateHandle');
        $this->display('Testtp/testInputTemplate');
        //$this->assign('openid',$iOid);
        //$this->assign('nickname',$iNickname);
        //$this->assign('res',$queryWork);
        //$this->redirect('testInputTemplate','OpenID='.$iOid,1,'页面跳转中...');
        //redirect('testInputPage');
        //redirect('testInputPage', 1, '页面跳转中...');
        /*
        $outPutStr = $iWorkName."_".$iWorkNumber."_".$iOid;
        $this->assign('outPutStr',$outPutStr);
        $this->display('testOutput');
        */
    }

    public function testOauth(){
        $code=$_GET["code"];
        $oid=getOidByOauth($code);
        error_log('in testOauth');
        error_log($code);
        error_log($oid);
        $this->assign('outPutStr',$code.'----'.$oid);
        $this->display('testOutput');

    }

}