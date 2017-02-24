<?php
chdir('./..');
define('CWD', (($getcwd = getcwd()) ? $getcwd : '.'));

require_once('global.php');
require_once(DIR . '/includes/functions_login.php');

if (empty($_POST['username']) || empty($_POST['password'])) {
	die('-1');
}

$ret = verify_authentication($_POST['username'], $_POST['password'], '', '', false, false);

if ($ret) {
	echo $vbulletin->userinfo['userid'];
} else {
	echo '0';
}
