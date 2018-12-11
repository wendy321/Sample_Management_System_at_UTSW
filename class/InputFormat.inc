<?php
class InputFormat
{
    public static function checkInputFormat($data,$canbeempty,$pattern) {
        if (empty($data) && !$canbeempty){
            return FALSE;
        }
        return (!empty($data) && !preg_match($pattern, $data))?FALSE:TRUE;
    }
}