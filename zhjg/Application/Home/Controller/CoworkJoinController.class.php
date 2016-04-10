<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\UserMsgModel;
use Home\Model\UserWorkModel;
use Home\Model\BaseUserModel;
use Home\Model\CoworkModel;
use Home\Model\CoworkJoinModel;

class CoworkJoinController extends Controller {

    private $_userMsgModel=null;
    private $_userWorkModel=null;
    private $_baseUserModel=null;
    private $_coworkModel=null;
    private $_coworkJoinModel=null;

    public function __construct()
    {

        error_reporting(E_ALL);
        ini_set("display_errors", 1);


        parent::__construct();
        $this->_userMsgModel = new UserMsgModel();
        $this->_userWorkModel = new UserWorkModel();
        $this->_baseUserModel=new BaseUserModel();
        $this->_coworkModel=new CoworkModel();
        $this->_coworkJoinModel=new CoworkJoinModel();
        $s_language = session("language");
        if(empty($s_language))
        {
            session("language", "zh-cn");
        }
    }


    public function index(){
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>!!!!!!!!欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
    }

    public function myJoinCoworkList()//我参加的共修列表
    {
        $code=$_GET["code"];
        $oid=getOidByOauth($code);
        //$oid="oMWyDwwtqVRU33zBB492IOz_fBkI";
        
        $coworkJoinList=$this->_coworkJoinModel->getCoworkJoinByOpenId($oid);

        $urlList=array();

        foreach ($coworkJoinList as $value) {//直接用SQL解决
        	$cowork =$this->_coworkModel->getCoworkByCoid($value["coid"]);
            $urlList[$value["coid"]]=$cowork["mydetailurl"];
        }


        $this->assign('openid',$oid);
        $this->assign('res',$coworkJoinList);
        $this->assign('res2',$urlList);
        //var_dump($urlList);
        $this->display();

        //$this->assign('outPutStr',$oid);
        //$this->display('testOutput');


    }

    public function inputUserCowork()
    {
    	$iOid=$_POST['openid'];//微信id
        $iUserName=$_POST['username'];//法号

        $coid= $_POST['coid'];

        $cowork=$this->_coworkModel->getCoworkByCoid($coid);

        $templates=$this->_coworkModel->getTemplate($coid);
        $comp=$this->_coworkJoinModel->getMyCompleted($coid,$iOid,date('Y-m-d'));

        $this->assign('coid',$coid);
        $this->assign('openid',$iOid);
        $this->assign('username',$iUserName);
        $this->assign('coname',$cowork['coname']);
        $this->assign('res_template',$templates);
        $this->assign('res_today',$comp);
        $this->assign('inputDate',date('Y-m-d'));

        $this->display();

    }

    public function inputUserCoworkHandle()//待测
    {
        $iOid=$_POST['openid'];//微信id
        $iUserName=$_POST['username'];//法号

        //error_log('in inputUserCoworkHandle');
        $coid= $_POST['coid'];

        $iInputDate=$_POST['inputDate'];

        $inputMode1=$_POST['radio1'];

        $cowork=$this->_coworkModel->getCoworkByCoid($coid);//

        $templates=$this->_coworkModel->getTemplate($coid);

        //var_dump($templates);


        foreach ($templates as $record) {
            //$result = $result.$record["workname"].':'.$record["readnumber"]."\r";//必须小写
            //echo $record["workname"];
            $iReadNumber=$_POST[$record["workname"]];
//error_log('for in inputUserCoworkHandle-'.$iReadNumber.'-'.$coid.$iOid);
            $this->_coworkJoinModel->addUserWork($coid,$iOid,$record["workname"],$iReadNumber,$iInputDate,$inputMode1);

        }


        $cowork=$this->_coworkModel->getCoworkByCoid($coid);

        $templates=$this->_coworkModel->getTemplate($coid);
        $comp=$this->_coworkJoinModel->getMyCompleted($coid,$iOid,$iInputDate);

        $this->assign('openid',$iOid);
        $this->assign('coid',$coid);
        $this->assign('username',$iUserName);
        $this->assign('coname',$cowork['coname']);
        $this->assign('res_template',$templates);
        $this->assign('res_today',$comp);
        $this->assign('inputDate',$iInputDate);
        $this->display('CoworkJoin/inputUserCowork');
    }

}