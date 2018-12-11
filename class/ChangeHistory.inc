<?php
class ChangeHistory
{
    private $db;

    function __construct($db) {
        $this->db =$db;
    }

    public function recordChangeHistory($table,$primarykey,$primarykeyid,$fieldname,$newvalue,$account)
    {

        $sql="SELECT ".$fieldname." FROM ".$table." WHERE ".$primarykey."=\"".$primarykeyid."\"";
        $oldvalue=null;
        if($result = $this->db->prepare($sql)){
            $result->execute();
            $result->bind_result($oldvalue);
            $result->fetch();
            $result->close();

            if(!($oldvalue===null && $newvalue===null) && ((string)$oldvalue)!==$newvalue ){
                //insert change history
                $sql="INSERT INTO ChangeHistory (TableName,Primary_Key,Field_Name,From_Value,To_Value,Account,".
                    "ChangeTime) VALUES (?,?,?,?,?,?,NOW())";

                if($result = $this->db->prepare($sql)){
                    $result->bind_param("ssssss",$table,$primarykeyid,$fieldname,$oldvalue,$newvalue,$account);
                    $result->execute();
                    $result->close();
                }
            }
        }
    }
}