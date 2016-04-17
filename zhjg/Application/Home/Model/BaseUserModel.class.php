<?php

namespace Home\Model;
use Think\Model;

class BaseUserModel extends Model
{
    //protected $tablePrefix = '';
    protected $trueTableName = 'baseuser';
    protected $tableName = 'baseuser';


    

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


    public function addMsg($openID = null, $msg = null)
    {
        
        error_log('in Model......');
        try{
            //$dao = D('testusermsg');
            $Data["OpenID"]=$openID;
            $Data["MsgTxt"]=$msg;
            $Data["Date"]=date('Y-m-d H:i:s');
            //echo date('Y-m-d H:i:s');
            //$dao->add($Data);
            $this->add($Data);
        }catch(\Think\Exception $e){
            error_log('eeeee');
            echo "eeee";
        }
        
        //
        error_log('leave Model......');
    }

    public function getBaseUser($openID=null){
        $oldData=$this->where('OpenId="'.$openID.'"')->find();
        return $oldData;
        /*
        if($oldData){
            //var_dump($oldData);
            return $oldData;
        }
        else{
            $this->saveBaseUser($openID);
            $oldData=$this->where('OpenId="'.$openID.'"')->find();
            return $oldData;
        }       
        */
    }

    public function saveBaseUser($openID=null){
        $jsonData= getWechatUserInfo($openID);
        error_log('in saveBaseUser');
        //$data["openid"]=$jsonData["openid"];
        
        $data["OpenID"]=$openID;
        if($jsonData["nickname"]){
            $data["NickName"]=$jsonData["nickname"];
        }else{
            $data["NickName"]=$openID;
        }
        error_log('in saveBaseUser2');
        
        $this->add($data);

        
    }

    public function deleteBaseUser($openID=null){
        error_log('in deleteBaseUser');

/*
        $oldData=$this->where('OpenId="'.$openID.'"')->find();
        if($oldData){
            error_log('in deleteBaseUser2 found');
            $oldData->delete();
            //$this->delete($oldData);
        }
        */
        $this->where('OpenId="'.$openID.'"')->delete();
        error_log('in deleteBaseUser deleted');
    }

}
?>