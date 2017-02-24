<?php
/*======================================================================*\
|| #################################################################### ||
|| # vBulletin 4.2.0 Patch Level 2 - Licence Number VBF0DF625C
|| # ---------------------------------------------------------------- # ||
|| # Copyright ©2000-2012 vBulletin Solutions Inc. All Rights Reserved. ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # ---------------- VBULLETIN IS NOT FREE SOFTWARE ---------------- # ||
|| # http://www.vbulletin.com | http://www.vbulletin.com/license.html # ||
|| #################################################################### ||
\*======================================================================*/
if (!VB_API) die;

$VB_API_WHITELIST = array(
	'response' => array(
		'checked', 'disablesmiliesoption', 'forumrules', 'polldate',
		'pollnewbits', 'polloptions', 'pollpreview', 'question',
		'threadinfo' => $VB_API_WHITELIST_COMMON['threadinfo'],
		'timeout'
	)
);
function api_result_prerender_2($t, &$r)
{
	switch ($t)
	{
		case 'newpoll':
			$r['polldate'] = TIMENOW;
			break;
	}
}

vB_APICallback::instance()->add('result_prerender', 'api_result_prerender_2', 2);

/*======================================================================*\
|| ####################################################################
|| # Downloaded: 06:01, Fri Aug 3rd 2012
|| # CVS: $RCSfile$ - $Revision: 35584 $
|| ####################################################################
\*======================================================================*/