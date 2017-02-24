<?php
// Do not output errors to screen
ini_set('display_errors', 0);

class SportEnter_API_Init
{
	public static function doAuth()
	{
		if ($_SERVER['REMOTE_ADDR'] == '84.108.120.61' || $_SERVER['REMOTE_ADDR'] == '87.69.121.172') return; // for debugging		

		$authHeaderName = 'HTTP_SPORTENTER_AUTH_KEY';
		$authHeaderVal = '1kdk90akxoql';
		
		if (!isset($_SERVER[$authHeaderName]) || $_SERVER[$authHeaderName] != $authHeaderVal) {
			die('Invalid authentication key');
		}
	}
}

SportEnter_API_Init::doAuth();


