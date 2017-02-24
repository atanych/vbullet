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

loadCommonWhiteList();

$VB_API_WHITELIST = array(
	'response' => array(
		'anncount',
		'announcebits' => $VB_API_WHITELIST_COMMON['postbits'],
		'foruminfo' => $VB_API_WHITELIST_COMMON['foruminfo']
	),
	'show' => array(
		'viewall', 'post_new_announcement'
	)
);

/*======================================================================*\
|| ####################################################################
|| # Downloaded: 06:01, Fri Aug 3rd 2012
|| # CVS: $RCSfile$ - $Revision: 35584 $
|| ####################################################################
\*======================================================================*/