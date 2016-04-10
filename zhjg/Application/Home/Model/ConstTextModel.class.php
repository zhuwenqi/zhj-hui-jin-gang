<?php

namespace Home\Model;
use Think\Model;
use Think\Log;


class ConstTextModel extends Model
{
    //protected $tablePrefix = '';
    protected $trueTableName = 'consttext';
    protected $tableName = 'consttext';


    public function getText($key=null)
    {

        error_log('in ConstTextModel');

        try{
          error_log('in ConstTextModel1');
          $Data=$this->where('TextKey="'.$key.'"')->find();

          error_log($Data['textvalue']);
        }catch(\Think\Exception $e){
            error_log($e->getMessage());
        }


        return $Data['textvalue'];
    }

}
?>