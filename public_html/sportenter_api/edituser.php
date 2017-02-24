<?php
require_once dirname(__FILE__).'/lib/init.php';

chdir('./..');
define('CWD', (($getcwd = getcwd()) ? $getcwd : '.'));

require_once('global.php');

header('Content-Type: text/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";

function die_error($code, $message='') {
	echo '<errors><error code="'.$code.'">'.htmlspecialchars($message).'</error></errors>';
	die;
}

if (!isset($_POST['userid'])) {
	die_error('invalid_userid');
}

$userinfo = fetch_userinfo($_POST['userid']);
if ($userinfo === false) {
		die_error('userid_not_found');
}

// init user datamanager class
$userdata =& datamanager_init('User', $vbulletin, ERRTYPE_ARRAY); 
$userdata->set_existing($userinfo);

if (isset($_POST['usergroupid'])) {
	$success = $userdata->set('usergroupid', $_POST['usergroupid']);
	if (!$success) {
		die_error('invalid_usergroupid');
	}
}

// save
$userdata->pre_save();
// check for errors
if (!empty($userdata->errors)) {
	echo '<errors>';
	foreach ($userdata->errors as $code => $message) {
		echo '<error>'.htmlspecialchars($message).'</error>';
	}
	echo '</errors>';
	die;
}

$success = $userdata->save();
	
if ($success) {
	echo '<success/>';
} else {
	echo '<errors><error>Unknown error occurred after save</error></errors>';
}


