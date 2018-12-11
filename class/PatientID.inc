<?php

class PatientID
{
    public static function generatePatientID($db,$isfakepid)
    {

        $pidlen=7;
        $maxpidnum=999999;
        $sql = ($isfakepid)?"SELECT max(Patient_ID) FROM Patient WHERE Patient_ID REGEXP '^[A-Z]{".$pidlen."}$'":
            "SELECT max(Patient_ID) FROM Patient WHERE Patient_ID REGEXP '^[A-Z]{1}[0-9]{".($pidlen-1)."}$'";

        $maxpid=null;
        if ($result = $db->prepare($sql)) {
            $result->execute();
            $result->bind_result($maxpid);
            $result->fetch();
            $result->close();
        }

        $finalpid="";
        if($maxpid===null){
            $finalpid = ($isfakepid)?str_repeat("A",$pidlen):"A".str_repeat("0",$pidlen-1);
        }else{
            if($isfakepid){

                $postfixpid="";
                $carry=true;
                for($i=$pidlen-1;$i>=0 && $carry==true;$i--){
                    $char=substr($maxpid,$i,1);
                    if($char==="Z"){
                        $char="A";
                    }else{
                        $char=chr(ord($char)+1);
                        $carry=false;
                    }
                    $postfixpid=$char.$postfixpid;
                }

                $len=strlen($postfixpid);
                $start=$pidlen-$len;
                $finalpid=substr_replace($maxpid,$postfixpid,$start,$len);

            }else{
                $carry=false;
                $firstchar=substr($maxpid,0,1);
                $res=substr($maxpid,1,$pidlen-1);

                $num=intval($res);
                if($num >= 0 && $num < $maxpidnum){
                    $num+=1;
                }else if($num == $maxpidnum){
                    $carry=true;
                    $num=0;
                }
                $str=strval($num);
                $resultres=str_pad($str,$pidlen-1,'0',STR_PAD_LEFT);

                $ascii=ord($firstchar);
                if($carry == true){
                    if($ascii >= 65 && $ascii <= 89){
                        $ascii+=1;
                    }else if($ascii == 90){
                        $ascii=65;
                    }
                }
                $resultfirstchar=chr($ascii);

                $finalpid=$resultfirstchar.$resultres;
            }
        }

        return $finalpid;
    }

}