<?php
require_once dirname(__FILE__).'/lib/init.php';

chdir('./..');
define('CWD', (($getcwd = getcwd()) ? $getcwd : '.'));

require_once('global.php');

header('Content-Type: text/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";

// validate input
$fields = array('username', 'password', 'email', 'birthday_day', 
				'birthday_month', 'birthday_year');

foreach ($fields as $field) {
	if (empty($_POST[$field])) {
		echo '<errors><error code="missing_field" field="'.$field.'">Missing field '.$field.'</error></errors>';
		die;
	}
}

function die_error($code, $message='') {
	echo '<errors><error code="'.$code.'">'.htmlspecialchars($message).'</error></errors>';
	die;
}

// init user datamanager class
$userdata =& datamanager_init('User', $vbulletin, ERRTYPE_ARRAY);

$success = $userdata->set('username', $_POST['username']);
if (!$success) {
	die_error(-1, 'invalid_username');
}

$success = $userdata->set('password', $_POST['password']);
if (!$success) {
	die_error(-2, 'invalid_password');
}


$success = $userdata->set('email', $_POST['email']);
if (!$success) {
	die_error(-3, 'invalid_email');
}

$success = $userdata->set('birthday', array(
	'day'   => $_POST['birthday_day'],
	'month' => $_POST['birthday_month'],
	'year'  => $_POST['birthday_year']
));
if (!$success) {
	die_error(-4, 'invalid_birthday');
}

// defaults
$dst = 2; // automaticallty adjust
$userdata->set_dst($dst);
$userdata->set('timezoneoffset', 2); // gmt+2 offset
$userdata->set('usergroupid', 3);

// save
$userdata->pre_save();
// check for errors
if (!empty($userdata->errors)) {
	echo '<errors>';
	foreach ($userdata->errors as $code => $message) {
		echo '<error code="5">'.htmlspecialchars($message).'</error>';
	}
	echo '</errors>';
	die;
}

$vbulletin->userinfo['userid']
  	= $userid
  	= $userdata->save();
	
if ($userid) {
	echo '<success userid="'.$userid.'"/>';
} else {
	echo '<errors><error>Unknown error occurred after save</error></errors>';
}


