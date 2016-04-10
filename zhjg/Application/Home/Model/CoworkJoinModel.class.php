<?php

namespace Home\Model;
use Think\Model;

class CoworkJoinModel extends Model
{
    //protected $tablePrefix = '';
    protected $trueTableName = 'userjoincowork';
    protected $tableName = 'userjoincowork';


    public function getCoworkJoinByOpenId($oid=null){
    	$coworkJoinList = $this->where('OpenId="'.$oid.'"')->select();
        return $coworkJoinList;
    }

    public function getMyCompleted($coid=null,$OpenId=null,$workDate=null)
    {
    	$dao = D('usercowork');//这里必须输入完整的数据库表名;
    	$comp=$dao->where('coid="'.$coid.'" AND OpenID="'.$OpenId.'" AND WorkDate="'.$workDate.'"')->select();
      return $comp;
    }

    public function getAllMyCompleted($coid=null,$OpenId=null)
    {
        $dao = D('usercowork');//这里必须输入完整的数据库表名;
        $comp=$dao->query( 'select openid,workname,sum(readnumber) as number from usercowork where coid="'.$coid.'" AND openid="'.$OpenId.'" group by workname'); 
        return $comp;
    }

    public function isJoin($oid=null,$coid=null)
    {
        $coworkJoin = $this->where('OpenId="'.$oid.'" AND coid="'.$coid.'"')->select();
        return $coworkJoin;
    }

    public function addJoin($oid=null,$coid=null,$username=null,$coname=null)
    {
        $Data['coid']=$coid;
        $Data['OpenId']=$oid;
        $Data['JoinDate']=Date('Y-m-d');
        $Data['UserName']=$username;
        $Data['CoName']=trim($coname);
        //error_log($username.$coname);
        $oldData=$this->where('OpenId="'.$oid.'" AND coid="'.$coid.'"')->find();
        if($oldData)
        {
            $this->save($Data);
        }
        else
        {
            $this->add($Data);
        }
    }

    public function addUserWork($coid=null,$OpenId=null,$workName=null,$readNumber=null,$workDate=null,$mode=null)
    {
                //error_log('in Model......');
        //Log::write('add work'.$OpenId.'+'.$work.'+'.$number, Log::DEBUG);
        try{
            //error_log('in addUserWork......');
            //error_log('try'.$coid.$OpenId.$workName.$readNumber);
            $dao = D('usercowork');//这里必须输入完整的数据库表名;
            if(is_numeric($readNumber) && $OpenId!=null && $workName!=null){

              //error_log($coid.$OpenId.$workName.$readNumber);
              $Data["OpenID"]=$OpenId;//必须大写
              $Data["WorkName"]=trim($workName);
              $Data["ReadNumber"]=$readNumber;
              $Data["WorkDate"]=$workDate;
              $Data["coid"]=$coid;
              $oldData=$dao->where('OpenID="'.$OpenId.'" AND WorkName="'.trim($workName).'" AND WorkDate="'.$workDate.'" AND coid="'.$coid.'"')->find();

              if($oldData){
                  //var_dump($oldData);
                  $Data["id"]=$oldData["id"];
                  if($mode=="m1"){//累加
                      //error_log("in m1");
                      $Data["ReadNumber"]=$oldData["readnumber"]+$readNumber;
                      //error_log($oldData["readnumber"]);
                      //error_log($number);
                      //error_log($Data["ReadNumber"]);
                  }
                  //cho "find";
                  $dao->save($Data);
              }
              else{
                  //echo "no find";
                  $dao->add($Data);
              }
              $this->clearEmptyWork($openID);


              
              return true;
            }
            else{
              return false;
            }
            
        }catch(\Think\Exception $e){
            error_log($e->getMessage());
            echo "eeee";
        }
    }

    public function clearEmptyWork($openID=null){
        $dao = D('usercowork');//这里必须输入完整的数据库表名;
        $dao->where('OpenID="'.$openID.'" AND ReadNumber=0')->delete();
    }

}