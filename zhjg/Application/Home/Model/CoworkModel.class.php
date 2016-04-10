<?php

namespace Home\Model;
use Think\Model;

class CoworkModel extends Model
{
    //protected $tablePrefix = '';
    protected $trueTableName = 'coworkstatic';
    protected $tableName = 'coworkstatic';

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


    public function addCowork($coname = null, $keyText = null,$openerID=null,$openerName=null,$dueDate=null)
    {
        
        try{
            //$dao = D('testusermsg');
            $Data["CoName"]=trim($coname);
            $Data["KeyText"]=$keyText;
            $Data["OpenerId"]=$openerID;
            $Data["OpenDate"]=date('Y-m-d');
            $Data["OpenerName"]=$openerName;
            $Data["DueDate"]=$dueDate;

            $oldData=$this->where('CoName="'.trim($coname).'"')->find();

            if($oldData){
                //var_dump($oldData);
                $Data["id"]=$oldData["id"];
                //cho "find";
                $coid=$oldData["id"];
                $this->save($Data);
            }
            else{
                //echo "no find";
                //$dao->add($Data);
                $coid=$this->add($Data);
                $baseURL_detail=C('SITE_BASE').'/Home/Cowork/coworkDetail?coid='.$coid;
                $Data["MyDetailURL"]=getOauthURL($baseURL_detail);

                $baseURL_open=C('SITE_BASE').'/Home/Cowork/myOpenCoworkDetail?coid='.$coid;
                $Data["MyOpenURL"]=getOauthURL($baseURL_open);
                $Data["id"]=$coid;
                $this->save($Data);
            }


            
            return $coid;
        }catch(\Think\Exception $e){
            error_log('eeeee');
            echo "eeee";
        }
        
    }

    public function getCoworkByOpenerID($openerID=null)
    {
        $coworkList = $this->where('OpenerId="'.$openerID.'"')->select();
        return $coworkList;
    }
    
    public function getCoworkByCoid($coid=null)
    {
        $cowork = $this->where('id="'.$coid.'"')->find();
        return $cowork;
    }



    public function getTemplate($coid=null)
    {
        $dao = D('coworktemplate');//这里必须输入完整的数据库表名;
        $queryTemplate = $dao->where('coid="'.$coid.'"')->select();
        //var_dump($queryWork);
        return $queryTemplate;
    }

    public function getAllCompletedByCoid($coid=null)
    {
        $dao = D('usercowork');//这里必须输入完整的数据库表名;
        $comp=$dao->query( 'select workname,sum(readnumber) as number from usercowork where coid="'.$coid.'" group by workname'); 
        //error_log('select workname,sum(readnumber) as number from usercowork where coid="'.$coid.'" group by workname');
        return $comp;
    }

    public function clearEmptyCowork($coid=null){
        $dao = D('coworktemplate');//这里必须输入完整的数据库表名;
        $dao->where('coid="'.$coid.'" AND TargetNumber=0')->delete();
    }

    public function addCoworkTemplate($coid=null, $work=null, $number=null)
    {
        //error_log('in Model......');
        try{
            $dao = D('coworktemplate');//这里必须输入完整的数据库表名;
            if(is_numeric($number) && $coid!=null && $work!=null){
              $Data["coid"]=$coid;//必须大写
              $Data["WorkName"]=trim($work);
              $Data["TargetNumber"]=$number;
              $oldData=$dao->where('coid="'.$coid.'" AND WorkName="'.trim($work).'"')->find();

              if($oldData){
                  //var_dump($oldData);
                  $Data["id"]=$oldData["id"];
                  //cho "find";
                  $dao->save($Data);
              }
              else{
                  //echo "no find";
                  $id=$dao->add($Data);
                  $Data["id"]=$id;
                  $Data["DetailURL"]="/Cowork?".$id;//先不管了，乱写的
                  $dao->save($Data);

              }
              $this->clearEmptyCowork($coid);


              
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

}
?>