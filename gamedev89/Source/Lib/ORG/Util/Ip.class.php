<?php
class Ip {
	/**
	 * Attempt to find the client's IP Address
	 *
	 * @param bool Should the IP be converted using ip2long?
	 * @return string|long The IP Address
	 */
	public static function GetRealRemoteIp($ForDatabase = false, $DatabaseParts = 2) {
		$Ip = '0.0.0.0';
		if (isset ( $_SERVER ['HTTP_CLIENT_IP'] ) && $_SERVER ['HTTP_CLIENT_IP'] != '')
			$Ip = $_SERVER ['HTTP_CLIENT_IP'];
		elseif (isset ( $_SERVER ['HTTP_X_FORWARDED_FOR'] ) && $_SERVER ['HTTP_X_FORWARDED_FOR'] != '')
			$Ip = $_SERVER ['HTTP_X_FORWARDED_FOR'];
		elseif (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] != '')
			$Ip = $_SERVER ['REMOTE_ADDR'];
		if (($CommaPos = strpos ( $Ip, ',' )) > 0)
			$Ip = substr ( $Ip, 0, ($CommaPos - 1) );
		$Ip = self::IPv4To6 ( $Ip );
		return ($ForDatabase ? self::IPv6ToLong ( $Ip, $DatabaseParts ) : $Ip);
	}
	/**
	 * Convert an IPv4 address to IPv6
	 *
	 * @param string IP Address in dot notation (192.168.1.100)
	 * @return string IPv6 formatted address or false if invalid input
	 */
	public static function IPv4To6($Ip) {
		static $Mask = '::ffff:'; // This tells IPv6 it has an IPv4 address
		$IPv6 = (strpos ( $Ip, '::' ) === 0);
		$IPv4 = (strpos ( $Ip, '.' ) > 0);
		
		if (! $IPv4 && ! $IPv6)
			return false;
		if ($IPv6 && $IPv4)
			$Ip = substr ( $Ip, strrpos ( $Ip, ':' ) + 1 ); // Strip IPv4 Compatibility notation
		elseif (! $IPv4)
			return $Ip; // Seems to be IPv6 already?
		$Ip = array_pad ( explode ( '.', $Ip ), 4, 0 );
		if (count ( $Ip ) > 4)
			return false;
		for($i = 0; $i < 4; $i ++)
			if ($Ip [$i] > 255)
				return false;
		
		$Part7 = base_convert ( ($Ip [0] * 256) + $Ip [1], 10, 16 );
		$Part8 = base_convert ( ($Ip [2] * 256) + $Ip [3], 10, 16 );
		return $Mask . $Part7 . ':' . $Part8;
	}
	
	/**
	 * Replace '::' with appropriate number of ':0'
	 */
	public static function ExpandIPv6Notation($Ip) {
		if (strpos ( $Ip, '::' ) !== false)
			$Ip = str_replace ( '::', str_repeat ( ':0', 8 - substr_count ( $Ip, ':' ) ) . ':', $Ip );
		if (strpos ( $Ip, ':' ) === 0)
			$Ip = '0' . $Ip;
		return $Ip;
	}
	
	/**
	 * Convert IPv6 address to an integer
	 *
	 * Optionally split in to two parts.
	 *
	 * @see http://stackoverflow.com/questions/420680/
	 */
	public static function IPv6ToLong($Ip, $DatabaseParts = 2) {
		$Ip = self::ExpandIPv6Notation ( $Ip );
		$Parts = explode ( ':', $Ip );
		$Ip = array ('', '' );
		for($i = 0; $i < 4; $i ++)
			$Ip [0] .= str_pad ( base_convert ( $Parts [$i], 16, 2 ), 16, 0, STR_PAD_LEFT );
		for($i = 4; $i < 8; $i ++)
			$Ip [1] .= str_pad ( base_convert ( $Parts [$i], 16, 2 ), 16, 0, STR_PAD_LEFT );
		
		if ($DatabaseParts == 2)
			return array (base_convert ( $Ip [0], 2, 10 ), base_convert ( $Ip [1], 2, 10 ) );
		else
			return base_convert ( $Ip [0], 2, 10 ) + base_convert ( $Ip [1], 2, 10 );
	}
}
