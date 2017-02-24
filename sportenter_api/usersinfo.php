<?php
require_once dirname(__FILE__).'/lib/init.php';

chdir('./..');
define('CWD', (($getcwd = getcwd()) ? $getcwd : '.'));
define('VB_AREA', 'Remote_API');
require_once(CWD . '/includes/init.php');
require_once(CWD . '/includes/functions.php');

header('Content-Type: text/xml; charset=UTF-8');

echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";

function echo_userinfo($userid)
{
	$GLOBALS['vbphrase']='NO';
	$userinfo = fetch_userinfo($userid);

	if (empty($userinfo)) {
		return; // do nothing if invalid
	}

	echo '<userinfo>';
	$fields = array(
		'userid',
		'username',
		'email',
		'password',
		'usergroupid'
	);
	foreach ($fields as $field) {
		if (isset($userinfo[$field])) {
			echo "<$field>".htmlspecialchars_uni($userinfo[$field])."</$field>";
		}
	}
	echo '</userinfo>';
}

if (isset($_GET['userid'])) {
	echo_userinfo($_GET['userid']);
} else if (isset($_GET['userids'])) {
	echo '<multiple_userinfo>';
	foreach (explode(',', $_GET['userids']) as $userid) {
		echo_userinfo($userid);
	}
	echo '</multiple_userinfo>';
}
