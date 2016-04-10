<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\UserMsgModel;
use Home\Model\UserWorkModel;
use Home\Model\BaseUserModel;
use Home\Model\CoworkModel;
use Home\Model\CoworkJoinModel;

class CoworkController extends Controller {

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

    public function newCowork()//新建共修
    {
        $code=$_GET["code"];
        $oid=getOidByOauth($code);
        //$oid="oMWyDwwtqVRU33zBB492IOz_fBkI";
        $baseuser = $this->_baseUserModel->getBaseUser($oid);

        $this->assign('duedate',date('Y-m-d'));


        $this->assign('nickname',$baseuser["nickname"]);
        $this->assign('openid',$oid);
        $this->display();
    }

    public function addCowork()
    {
        $iCoworkName = $_POST['inputCoworkName'];
        $iOpenerName = $_POST['inputOpenerName'];
        $iKeyText = $_POST['inputKeytext'];
        $iOid=$_POST['openid'];
        $iNickname=$_POST['nickname'];
        $iDueDate=$_POST['dueDate'];

        $iCoid=$this->_coworkModel->addCowork($iCoworkName,$iKeyText,$iOid,$iOpenerName,$iDueDate);
        
        $this->_coworkJoinModel->addJoin($iOid,$iCoid,$iNickname,$iCoworkName);//默认自己参加共修

        $this->assign('coid',$iCoid);
        $this->assign('openid',$iOid);
        $this->assign('coworkName',$iCoworkName);
        $workList = $this->_coworkModel->getTemplate($iCoid);
        $this->assign('res',$workList);
        $this->assign('openerName',$iOpenerName);

        $this->display('editCoworkTemplate');

        //$this->assign('outPutStr',$iKeyText.'----'.$iCoid);
        //$this->display('Testtp/testOutput');

    }

    public function editCoworkTemplate()
    {
        $coid=$_POST['coid'];
        $iOpenerName = $_POST['openerName'];
        $iCoworkName = $_POST['coworkName'];

        $iOid=$_POST['openid'];
        $iWorkName = $_POST['inputWorkName'];

        $inputWorkNumber=$_POST['inputWorkNumber'];

        $this->_coworkModel->addCoworkTemplate($coid,$iWorkName,$inputWorkNumber);



        $this->assign('coid',$coid);
        $this->assign('openid',$iOid);
        $workList = $this->_coworkModel->getTemplate($coid);
        $this->assign('res',$workList);
        $this->assign('openerName',$iOpenerName);
        $this->assign('coworkName',$iCoworkName);

        $this->display();


    }

    public function myOpenCoworkList()//我发起的共修列表
    {
        $code=$_GET["code"];
        $oid=getOidByOauth($code);
        //$oid="oMWyDwwtqVRU33zBB492IOz_fBkI";

        $coworkList=$this->_coworkModel->getCoworkByOpenerID($oid);
        $this->assign('openid',$oid);
        $this->assign('res',$coworkList);
        $this->display();


    }


    public function coworkDetail()
    {
        $code=$_GET["code"];
        $oid=getOidByOauth($code);

        $coid = I('get.coid');
/*
        $this->assign('outPutStr',$oid.'--'.$coid);
        $this->display('Testtp/testOutput');
        */
        $baseuser = $this->_baseUserModel->getBaseUser($oid);
        if($baseuser){
            $cowork = $this->_coworkModel->getCoworkByCoid($coid);
            $workList = $this->_coworkModel->getTemplate($coid);

            $comp=array();
            $all_comp=$this->_coworkModel->getAllCompletedByCoid($coid);
            foreach ($workList as $value) {//直接用SQL解决
                foreach($all_comp as $value2)
                {
                    if($value["workname"]==$value2["workname"]){
                        $comp[$value["id"]]=$value2["number"];
                    }
                }
            }

            $comp_my=array();
            $all_comp_my=$this->_coworkJoinModel->getAllMyCompleted($coid,$oid);
            foreach ($workList as $value) {//直接用SQL解决
                foreach($all_comp_my as $value2)
                {
                    if($value["workname"]==$value2["workname"]){
                        $comp_my[$value["id"]]=$value2["number"];
                    }
                }
            }
            $this->assign('coworkName',$cowork["coname"]);
            $this->assign('openerName',$cowork["openername"]);
            $this->assign('coid',$coid);
            $this->assign('res',$workList);
            $this->assign('res2',$comp);
            $this->assign('res_my',$comp_my);
            $this->assign('openid',$oid);

            $coworkJoin = $this->_coworkJoinModel->isJoin($oid,$coid);
            if($coworkJoin)
            {
                error_log('join');
                $this->display('Cowork/joinedCowork');
            }
            else
            {
                error_log('nojoin');
                $this->display('Cowork/noJoinedCowork');
            }

            /*
            $this->assign('outPutStr',$oid.'--'.$coid);
            $this->display('Testtp/testOutput');
            */
        }
        else{
            redirect(C('WECHAT_SUBSCRIBE_URL'));
        }
        


        

    }

    public function addJoinCowork()
    {
        $iOid=$_POST['openid'];
        $iconame=$_POST['coworkname'];
        $iusername=$_POST['inputUserName'];
        $coid=$_POST['coid'];
        $this->_coworkJoinModel->addJoin($iOid,$coid,$iusername,$iconame);//默认自己参加共修


        $cowork = $this->_coworkModel->getCoworkByCoid($coid);
        redirect($cowork['mydetailurl']);
    }


    public function myOpenCoworkDetail()//这个方法非常关键，需要判断打开的用户身份
    {
        $code=$_GET["code"];
        $oid=getOidByOauth($code);
        //$oid="oMWyDwwtqVRU33zBB492IOz_fBkI";

        $coid = I('get.coid');
        //$this->assign('outPutStr',$coid);
        //$this->display('Testtp/testOutput');
        $cowork = $this->_coworkModel->getCoworkByCoid($coid);

        if($oid==$cowork["openerid"])//判断打开者与发起人是否一致，如果不一致的话就跳转
        {
            $this->assign('openid',$oid);
        }
        $workList = $this->_coworkModel->getTemplate($coid);

        $comp=array();
        $all_comp=$this->_coworkModel->getAllCompletedByCoid($coid);

        foreach ($workList as $value) {//直接用SQL解决
            foreach($all_comp as $value2)
            {
                if($value["workname"]==$value2["workname"]){
                    $comp[$value["id"]]=$value2["number"];
                }
            }
        }
        //var_dump($comp);
        $this->assign('coworkName',$cowork["coname"]);
        $this->assign('openerName',$cowork["openername"]);
        $this->assign('coid',$coid);
        $this->assign('res',$workList);

        $this->assign('res2',$comp);
        $this->display();


    }


}