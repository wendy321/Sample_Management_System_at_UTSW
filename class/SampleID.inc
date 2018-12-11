<?php

class SampleID
{
    /* Generate a new Sample ID:
     * This is a function to generate a new sample id based on inputs of patient id, patholigical status, and sample class.
     * The format of sample_id is 6-digit patient_id, 2-digit patholigical_status, 2-digit sample_class, and 2-digit auto-increment.
     * Input variables: database, sample_uuid, patient-id, patholigical_status, sample_class
     * Output variables: sample_id
     * Author: Shin-Yi Lin at UTSW edited on May, 14, 2018
     * */
    public static function generateSampleID($db,$patientid,$pathologicalstatus,$sampleclass)
    {
        $pidlen=7;
        $finalsampleid=$maxsampleid=$finalpid=null;
        if($patientid===null)
        {
            $sql = "SELECT max(Sample_ID) FROM Sample WHERE Patient_ID IS NULL AND Pathological_Status=? AND Sample_Class=?";
        }else
        {
            $sql = "SELECT max(Sample_ID) FROM Sample WHERE Patient_ID=? AND Pathological_Status=? AND Sample_Class=?";
        }

        if ($result = $db->prepare($sql)) {
            if($patientid===null)
            {
                $result->bind_param('ss', $pathologicalstatus,$sampleclass);
            }else
            {
                $result->bind_param('sss', $patientid, $pathologicalstatus,$sampleclass);
            }

            $result->execute();
            $result->bind_result($maxsampleid);
            $result->fetch();
            $result->close();
        }

        if($maxsampleid===null){
            if($patientid===null)
            {
                $finalpid=str_repeat("A",$pidlen);
                $finalsampleid=$finalpid.$pathologicalstatus.$sampleclass."00";
            }else
            {
                $finalpid=$patientid;
                $finalsampleid=$finalpid.$pathologicalstatus.$sampleclass."00";
            }
        }else{
            $carry=false;
            $last2digit=(int)substr($maxsampleid,-2,2);
            if($last2digit<99)
            {
                $last2digit+=1;
                if($last2digit<10)
                {
                    $last2digit="0".(string)$last2digit;
                }
            }else
            {
                $carry=true;
                $last2digit="00";
            }

            if($patientid===null)
            {
                $newpid="";
                $prepid=substr($maxsampleid,0,$pidlen);
                for($i=$pidlen-1;$i>=0 && $carry==true;$i--){
                    $char=substr($prepid,$i,1);
                    if($char==="Z"){
                        $char="A";
                    }else{
                        $carry=false;
                        $char=chr(ord($char)+1);
                    }
                    $newpid=$char.$newpid;
                }
                $len=strlen($newpid);
                $start=$pidlen-$len;
                $finalpid=substr_replace($prepid,$newpid,$start,$len);
                $finalsampleid=$finalpid.$pathologicalstatus.$sampleclass.$last2digit;
            }else
            {
                $finalpid=$patientid;
                $finalsampleid=$finalpid.$pathologicalstatus.$sampleclass.$last2digit;
            }
        }

        return array($finalsampleid,$patientid);
    }

    /* Get the Sample ID:
     * This is a function to get the sample_id by sample_uuid. The sample_id will be displayed on web UI.
     * If a fake patient_id is presented in sample_id, it'd be converted to local_patient_id.
     * Input variables: database, sample_uuid
     * Output variables: a converted sample_id
     * Author: Shin-Yi Lin at UTSW edited on May, 14, 2018
     * */
    public static function getConvertedSampleID($db,$sampleuuid){
        $pidlen=7;
        $finalsampleid=null;
        $sampleid=$pid=null;
        $sql="SELECT Sample_ID,Patient_ID FROM Sample WHERE UUID=? AND isDelete=0";
        if ($result = $db->prepare($sql)) {
            $result->bind_param('s', $sampleuuid);
            $result->execute();
            $result->bind_result($sampleid,$pid);
            $result->fetch();
            $result->close();
        }

        if($sampleid!==null && $pid!==null){
            if(preg_match("/^[A-Z]{".$pidlen."}$/",$pid)){
                $localpid=null;
                $sql="SELECT Local_Patient_ID FROM Patient WHERE Patient_ID=? AND isDelete=0";
                if ($result = $db->prepare($sql)) {
                    $result->bind_param('s', $pid);
                    $result->execute();
                    $result->bind_result($localpid);
                    $result->fetch();
                    $result->close();
                }

                if($localpid!==null){
                    $finalsampleid=substr_replace($sampleid,$localpid,0,$pidlen);
                }else{
                    $finalsampleid=$sampleid;
                }
            }else{
                $finalsampleid=$sampleid;
            }
        }

        return $finalsampleid;
    }
}