<?php
require_once dirname(__FILE__).'/lib/init.php';

chdir('./..');
define('CWD', (($getcwd = getcwd()) ? $getcwd : '.'));
define('VB_AREA', 'Remote_API');
require_once(CWD . '/includes/init.php');
require_once(CWD . '/includes/functions.php');

header('Content-Type: text/xml; charset=UTF-8');

echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";

$vbphrase='NO';
echo '<userinfo>';
foreach (fetch_userinfo($_GET['userid']) as $k=>$v) {
	echo "<$k>".htmlspecialchars_uni($v)."</$k>";
}
echo '</userinfo>';
