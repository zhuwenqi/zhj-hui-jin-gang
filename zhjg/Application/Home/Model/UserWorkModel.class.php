<?php

namespace Home\Model;
use Think\Model;
use Think\Log;

class UserWorkModel extends Model
{
    //protected $tablePrefix = '';
    protected $trueTableName = 'userwork';
    protected $tableName = 'userwork';
    protected $MSG_SPLITTER = ' ';

//    public function index()
//    {
//        $sql = "SELECT * FROM ".C("DB_ORACLE_PREFIX")."F_3_TERMINAL";
//        return $this->query($sql);
//    }
/*      public function index($flightNo = null, $startstation = null, $terninalstation = null, $date = null,$currentPage = 1)
      {
          $start = ($currentPage - 1) * SHOW_NUMBER;
          $end = $currentPage * SHOW_NUMBER;
          $sql = "SELECT * FROM (SELECT F.FLIGHT_NO,F.ORIGIN_AIRPORT_IATA,(SELECT NAME_XML FROM ".C("DB_ORACLE_PREFIX")."F_3_AIRPORT F3 WHERE F3.AIRPORT_IATA = F.ORIGIN_AIRPORT_IATA) FROMCITY,";
          $sql .= "TO_CHAR(NVL(F.ATD,NVL(F.ETD,F.STD)),'HH24:MI') STARTTIME,F.DEST_AIRPORT_IATA,(SELECT NAME_XML FROM ".C("DB_ORACLE_PREFIX")."F_3_AIRPORT F3 WHERE F3.AIRPORT_IATA = F.DEST_AIRPORT_IATA) TOCITY,";
          $sql .= "TO_CHAR(NVL(F.ATA,NVL(F.ETA,F.STA)),'HH24:MI') ENDTIME,F.RECENT_ABNORMAL_STATUS,F.REMARK,(SELECT NAME_XML FROM ".C("DB_ORACLE_PREFIX")."F_3_REMARK FR ";
          $sql .= "WHERE FR.REMARK_CODE = REMARK) REMARK_XML,ROWNUM RN FROM  (SELECT * FROM ".C("DB_ORACLE_PREFIX")."F_1_DYNFLIGHT F1";
          $sql .= " WHERE TO_CHAR(OPERATION_DATE,'yyyy-mm-dd')='".$date."' ";
          if(!is_null($flightNo) && "" != trim($flightNo) && $flightNo != "null")
          {
              $sql .= " AND FLIGHT_NO LIKE '%".strtoupper(trim($flightNo))."%' ";
          }
          if(!is_null($startstation) && !is_null($terninalstation) && $startstation != "null" && $terninalstation != "null")
          {
              $sql .= " AND ORIGIN_AIRPORT_IATA = '".$startstation."' AND DEST_AIRPORT_IATA = '".$terninalstation."'";
          }
          $sql .= " ORDER By TO_CHAR(NVL(F1.ATD,NVL(F1.ETD,F1.STD)),'HH24:MI') ASC) F ";

          $sql .= " WHERE ROWNUM <= $end  ORDER BY STARTTIME ASC ) FD WHERE  RN > $start";
          return $this->query($sql);
      }*/

    //测试是否断网


/*
    public function addWork($openID=null, $work=null, $number=null,$mode=null,$inputDate=null)
    {
        //error_log('in Model......');
        Log::write('add work'.$openID.'+'.$work.'+'.$number, Log::DEBUG);
        try{
            if(is_numeric($number) && $openID!=null && $work!=null){
              $Data["OpenId"]=$openID;//必须大写
              $Data["WorkName"]=trim($work);
              $Data["ReadNumber"]=$number;
              //$Data["WorkDate"]=date('Y-m-d');
              $Data["WorkDate"]=$inputDate;
              $Data["InputTime"]=date('Y-m-d H:i:s');
              $oldData=$this->where('OpenId="'.$openID.'" AND WorkName="'.trim($work).'" AND WorkDate="'.$inputDate.'"')->find();

              //error_log('in addwork:'.$mode);

              if($oldData){//今天已录入过
                  //var_dump($oldData);
                  $Data["Id"]=$oldData["id"];
                  if($mode=="m1"){//累加模式
                      //error_log("in m1");
                      $Data["ReadNumber"]=$oldData["readnumber"]+$number;
                      //error_log($oldData["readnumber"]);
                      //error_log($number);
                      //error_log($Data["ReadNumber"]);
                  }
                  //cho "find";
                  $this->save($Data);
              }
              else{//今天未录入过
                  //echo "no find";
                  $this->add($Data);
                  if($mode=="m1"){//若未查询到，并且为逐次录入，则需要检查模版是否存在，若不存在则更新模版
                      $this->addNewTemplate($Data);
                  }
              }
              $this->clearEmptyWork($openID);
              if($mode=="m2"){
                  $this->updateTemplate($Data);
              }
              


              
              return true;
            }
            else{
              return false;
            }
            
        }catch(\Think\Exception $e){
            error_log('eeeee');
            echo "eeee";
        }

    }
    */

    public function addWork($openID=null, $work=null, $number=null,$mode=null,$inputDate=null)
    {
        //error_log('in Model......');
        Log::write('add work'.$openID.'+'.$work.'+'.$number, Log::DEBUG);
        try{
            if(is_numeric($number) && $openID!=null && $work!=null){
              $Data["OpenId"]=$openID;//必须大写
              $Data["WorkName"]=trim($work);
              $Data["ReadNumber"]=$number;
              //$Data["WorkDate"]=date('Y-m-d');
              $Data["WorkDate"]=$inputDate;
              $Data["InputTime"]=date('Y-m-d H:i:s');
              $oldData=$this->where('OpenId="'.$openID.'" AND WorkName="'.trim($work).'" AND WorkDate="'.$inputDate.'"')->find();

              //error_log('in addwork:'.$mode);
              if($number>0)
              {
                  if($oldData){//假如今天已经录入过
                      $Data["Id"]=$oldData["id"];
                      if($mode=="m1"){//累加模式
                      //error_log("in m1");
                        $Data["ReadNumber"]=$oldData["readnumber"]+$number;
                      }else
                      {
                        $Data["ReadNumber"]=$number;
                      }
                      $this->save($Data);
                  }
                  else
                  {//今天未录入过
                  //echo "no find";
                      $this->add($Data);//无论是更新或者累加，都需要添加记录
                      if($mode=="m1"){//如果今天未录入过，且为累加模式，则需要去检查并更新模版
                          $this->addNewTemplate($Data);
                      }
                  }
                  if($mode=="m2"){//更新模式
                      $this->updateTemplate($Data);
                  }
              }
              else{
                  if($number<0){
                      $this->updateTemplate($Data);
                  }
              }

              return true;
            }
            else{
              return false;
            }
            
        }catch(\Think\Exception $e){
            error_log('eeeee');
            echo "eeee";
        }

    }

    public function addMsg($openID = null, $msg = null)//调试用
    {
        
        error_log('in Model......');
        Log::write('测试调试错误信息', Log::DEBUG);
        try{
            //$dao = D('testusermsg');
            

            $messages = explode($MSG_SPLITTER, $msg);
            if(is_numeric($messages[1])){
              $Data["OpenId"]=$openID;//必须大写
              $Data["WorkName"]=$messages[0];
              $Data["ReadNumber"]=$messages[1];
              $Data["WorkDate"]=date('Y-m-d');
              $Data["InputTime"]=date('Y-m-d H:i:s');
              $oldData=$this->where('OpenId="'.$openID.'" AND WorkName="'.$messages[0].'" AND WorkDate="'.date('Y-m-d').'"')->find();

              if($oldData){
                //var_dump($oldData);
                  $Data["Id"]=$oldData["id"];
                  //echo "find";
                  $this->save($Data);
              }
              else{
                  //echo "no find";
                  $this->add($Data);
              }
              $this->clearEmptyWork($openID);
              $this->updateTemplate($Data);

              
              return true;
            }
            else{
              return false;
            }
            
        }catch(\Think\Exception $e){
            error_log('eeeee');
            echo "eeee";
        }
    }

    public function clearEmptyWork($openID=null){
        $this->where('OpenId="'.$openID.'" AND ReadNumber<=0')->delete();
    }

    public function updateTemplate($newData=null){
        $Data["OpenId"]=$newData["OpenId"];//必须大写
        $Data["WorkName"]=$newData["WorkName"];
        $Data["ReadNumber"]=$newData["ReadNumber"];
        $Data["WorkDate"]=$newData["WorkDate"];
        $Data["InputTime"]=date('Y-m-d H:i:s');
        $dao = D('userworktemplate');//这里必须输入完整的数据库表名;
        $oldData=$dao->where('OpenId="'.$newData['OpenId'].'" AND WorkName="'.$newData['WorkName'].'"')->find();
        if($oldData){
                //var_dump($oldData);
            $Data["Id"]=$oldData["id"];
                  //echo "find";
            $dao->save($Data);
        }
        else{
                //echo "no find";
            $dao->add($Data);
        }
        $dao->where('OpenId="'.$newData['OpenId'].'" AND ReadNumber<=0')->delete();

    }

    public function addNewTemplate($newData=null){//这个逻辑仅在累加模式时使用，即没有出现过的时候更新
        $Data["OpenId"]=$newData["OpenId"];//必须大写
        $Data["WorkName"]=$newData["WorkName"];
        $Data["ReadNumber"]=$newData["ReadNumber"];
        $Data["WorkDate"]=$newData["WorkDate"];
        $Data["InputTime"]=date('Y-m-d H:i:s');
        $dao = D('userworktemplate');//这里必须输入完整的数据库表名;
        $oldData=$dao->where('OpenId="'.$newData['OpenId'].'" AND WorkName="'.$newData['WorkName'].'"')->find();
        if($oldData){//若存在，则比较下数值
                //var_dump($oldData);
            if($oldData["readnumber"]<$Data["ReadNumber"]){
                $Data["Id"]=$oldData["id"];
                //echo "find";
                $dao->save($Data);
            }

        }
        else{
                //echo "no find";
            $dao->add($Data);
        }
        $dao->where('OpenId="'.$newData['OpenId'].'" AND ReadNumber<0')->delete();

    }

    public function queryMsg($openID = null){
        $queryWork = $this->where('OpenId="'.$openID.'" AND WorkDate="'.date('Y-m-d').'"')->select();
        //var_dump($queryWork);

        $result = '';
        foreach ($queryWork as $record) {
          $result = $result.$record["workname"].':'.$record["readnumber"]."\r";//必须小写
          //echo $record["workname"];
        }
        if($result==''){
            $result='暂无数据';
        }
        echo $result;
        return $result;
    }

    public function queryTodayWork($openID=null){
        $queryWork = $this->where('OpenId="'.$openID.'" AND WorkDate="'.date('Y-m-d').'"')->select();
        //var_dump($queryWork);
        return $queryWork;
    }

    public function queryWorkTemplate($openID=null){
        $dao = D('userworktemplate');//这里必须输入完整的数据库表名;
        $queryWork = $dao->where('OpenId="'.$openID.'"')->select();
        //var_dump($queryWork);
        return $queryWork;
    }

    public function queryWorkByDate($openID=null,$date=null){
        $queryWork = $this->where('OpenId="'.$openID.'" AND WorkDate="'.$date.'"')->select();
        //var_dump($queryWork);
        return $queryWork;
    }


}
?>