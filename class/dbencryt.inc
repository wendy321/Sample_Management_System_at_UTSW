<?php

# set up encryption for database connection

class Encryption
{
	private static $key = 'FS1%DL3R?20qc14T';

	public static function encrypt($string)
	{
		$key = Encryption::$key;
		$result = "";
		for ($i = 0; $i < strlen($string); $i++)
		{
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) + ord($keychar));
			$result.= $char;
		}
		return base64_encode($result);
	}
	public static function decrypt($string)
	{
		$key = Encryption::$key;
		$result = "";
		$string = base64_decode($string);
                for ($i = 0; $i < strlen($string); $i++)
		{
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) - ord($keychar));
			$result.=$char;
		}
		return $result;
	}
}
?>
