<?php

/**
 * UUID management
 * Author: Shin-Yi Lin at UTSW edited on Oct, 15, 2018
 */
class UUID
{
    public static function generate36DigitUUID(){
/*        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));*/

        return "8b2515ba-b243-4473-8891-f3761667256e";
    }
}