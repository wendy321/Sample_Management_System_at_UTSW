<?php

/*
 *********************************************************************
 *  DEMO VERSION NOTICE:
 *  The demo version of this product contains a static barcode that may be
 *  used for evaluation purposes only. The static barcode cannot be
 *  changed because this would reveal the complete script, which is only
 *  available in the licensed version. The licensed version is provided 
 *  with a money back satisfaction guarantee. if it is necessary to 
 *  test dynamic barcodes with this product, the licensed version is required.
 *  
 *  Order: http://www.idautomation.com/barcode-components/php-generator-script/
 *  
 *  IDAutomation Native PHP Barcode Generator Version 2017
 *  Copyright, IDAutomation.com, Inc. All rights reserved.
 **********************************************************************
 */

// URL params
// ----------
// D    (string)   data to encode 
// X    (integer)  zoom factor, default 2, range [1 .. n]
// O    (integer)  orientation, default 0, range [0, 90, 180, 270]
// PT   (boolean)  apply tilde, default "T", range ["T", "F"]
// MODE (char)     encoding, default 0, range [0 .. 3], 0 = ASCII, 1 = C40, 2 = TEXT, 3 = BASE256
// PFMT (integer)  preferred format, default 0, range [0 .. 29]
// Q    (integer)  quite zone, default 1, range [0 .. n]

$Demo = false;
$DemoText = "Generated @ bcgen.com";

function createBarcode() {
  global $gFNC1;
  global $DemoText, $Demo;
  global $gLengthRows, $gLengthCols;
  global $gData, $gDataCount, $gFormat;
  global $gE_ASCII;
  global $gFinal, $gQuietZone, $gEncodingMode;

  // global variables
  $gQuietZone = 0;                   // size, in module, of Quiet Zone
  $gFinal = array();                 // Barcode array to be returned which is readable by VB.
  // set this variable to true only for making debug of script
  $DebugMode = false;

  $xDim = 2;

  $textWidth = 0;
  $textHeight = 0;

  $height = 20;
  $width = 20;

  $gFinal[19] = array(1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0);
  $gFinal[18] = array(1, 0, 1, 0, 0, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 1, 0, 0, 1, 1);
  $gFinal[17] = array(1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 0, 0, 1, 0, 0, 0, 1, 1, 0);
  $gFinal[16] = array(1, 0, 0, 0, 1, 0, 1, 0, 1, 1, 0, 1, 1, 0, 1, 0, 1, 0, 0, 1);
  $gFinal[15] = array(1, 1, 0, 1, 0, 1, 0, 1, 0, 0, 0, 0, 1, 1, 1, 0, 0, 1, 0, 0);
  $gFinal[14] = array(1, 0, 1, 0, 0, 0, 1, 0, 1, 0, 1, 0, 1, 0, 0, 1, 1, 0, 1, 1);
  $gFinal[13] = array(1, 1, 1, 0, 1, 1, 0, 1, 0, 1, 1, 1, 1, 0, 1, 0, 1, 1, 0, 0);
  $gFinal[12] = array(1, 0, 0, 0, 1, 1, 1, 0, 1, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1);
  $gFinal[11] = array(1, 1, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 0, 1, 1, 0, 0, 0);
  $gFinal[10] = array(1, 0, 1, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 1, 1);
  $gFinal[9] = array(1, 1, 1, 0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 1, 1, 1, 0);
  $gFinal[8] = array(1, 1, 0, 1, 1, 0, 1, 0, 0, 0, 1, 0, 1, 0, 0, 0, 1, 1, 1, 1);
  $gFinal[7] = array(1, 0, 1, 0, 1, 1, 0, 1, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0);
  $gFinal[6] = array(1, 0, 0, 0, 0, 0, 1, 1, 1, 0, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1);
  $gFinal[5] = array(1, 1, 0, 0, 1, 1, 1, 1, 0, 0, 1, 0, 0, 0, 0, 1, 1, 0, 1, 0);
  $gFinal[4] = array(1, 1, 0, 0, 0, 1, 0, 1, 1, 1, 1, 0, 1, 1, 0, 1, 0, 0, 1, 1);
  $gFinal[3] = array(1, 1, 0, 1, 0, 0, 1, 0, 0, 0, 0, 1, 1, 1, 0, 1, 0, 1, 0, 0);
  $gFinal[2] = array(1, 0, 1, 1, 0, 1, 0, 1, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0, 1);
  $gFinal[1] = array(1, 1, 1, 1, 0, 0, 0, 1, 1, 1, 0, 1, 0, 1, 1, 0, 0, 0, 1, 0);
  $gFinal[0] = array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);



  ZoomFactor($height, $width, $xDim, $textHeight, $textWidth);

  if ($DebugMode) {
    echo "<pre>";
    for ($i = 0; $i < $height; $i++) {
      for ($k = 0; $k < $width; $k++) {
        if ($gFinal[$i][$k] == 1) {
          echo "#";
        } else {
          echo " ";
        }
      }
      echo "<br>";
    }
    echo "</pre>";
  } else {
    writeBarcode(GetBMP($height, $width));
  }
}

function GetBMP($SizeY, $SizeX) {
  global $gFinal;

  $height = $SizeY;
  $width = $SizeX;
  $strPad = "";

  // Determine width of rows in the BMP file (padded to 4-byte boundary).
  $intTemp = (floor($width / 8) + (cIf($width % 8 > 0, 1, 0))) % 4;
  if ($intTemp != 0)
    $strPad = str_repeat(chr(0), 4 - $intTemp);

  $strBMP = "BM" .
    chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) .
    chr(62) . chr(0) . chr(0) . chr(0) . chr(40) . chr(0) . chr(0) . chr(0) .
    size($width) . chr(0) . chr(0) . size($height) . chr(0) . chr(0) .
    chr(1) . chr(0) . chr(1) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(37) . chr(14) . chr(0) .
    chr(0) . chr(37) . chr(14) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(255) . chr(255) . chr(255) .
    chr(0) . chr(0) . chr(0) . chr(0) . chr(0);

  for ($y = 0; $y <= $SizeY - 1; $y++) { // for each row
    $pixels = 0;
    $pos = 8;
    $strRow = "";
    for ($x = 0; $x < $SizeX; $x++) { // for each column
      $pos--;
      // draw a single barcode cell
      if ($gFinal[$y][$x] >= 1)
        $pixels = $pixels | pow(2, $pos);
      if ($pos == 0) {
        $strRow .= chr($pixels);
        $pos = 8;
        $pixels = 0;
      }
    }
    if ($pos != 8)
      $strRow .= chr($pixels);

    $strRow .= $strPad;
    $strBMP .= $strRow;
  }

  return $strBMP;
}

function ZoomFactor(&$Height, &$Width, $xDim, $textHeight, $textWidth) {
  global $gFinal;

  $newHeight = $Height * $xDim;
  $newWidth = $Width * $xDim;

  $xOffset = 0;
  if ($textWidth > $newWidth) {
    $textWidth = $textWidth - $newWidth;
    $xOffset = intval($textWidth / 2);
  } else {
    $textWidth = 0;
  }

  $newMatrix = array();
  for ($y = 0; $y < $newHeight + $textHeight; $y++) {
    $newMatrix[$y] = array();
    for ($x = 0; $x < $newWidth + $textWidth; $x++) {
      $newMatrix[$y][$x] = 0;
    }
  }

  for ($y = 0; $y < $newHeight; $y++) {
    for ($x = 0; $x < $newWidth; $x++) {
      $newMatrix[$y][$xOffset + $x] = $gFinal[intval($y / $xDim)][intval($x / $xDim)];
    }
  }

  $gFinal = $newMatrix;
  $Height = $newHeight + $textHeight;
  $Width = $newWidth + $textWidth;
}

function cIf($a, $b, $c) {
  if ($a)
    return $b;
  else
    return $c;
}

function size($length) {

  $length = (double) $length;

  if ($length > 255) {
    if ($length > 65535)
      $length = 65535;

    return chr($length % 256) . chr(floor($length / 256));
  } else {
    return chr($length) . chr(0);
  }
}

function writeBarcode($data) {
  header("Content-Type: image/bmp");
  echo $data;
}

createBarcode();




