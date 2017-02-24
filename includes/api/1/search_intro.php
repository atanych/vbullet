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
		'type_options', 'selectedtypes', 'input_search_types', 'errorlist',
		'search_ui' => array(
			'contenttypeid',  'human_verify',
			'search_forum_options', 'prefix_selected', 'search_prefix_options',
			'sortbyselected', 'titleonlyselected', 'searchdateselected',
			'beforeafterselected', 'exactnamechecked',
		)
	)
);

/*======================================================================*\
|| ####################################################################
|| # Downloaded: 06:01, Fri Aug 3rd 2012
|| # CVS: $RCSfile$ - $Revision: 35584 $
|| ####################################################################
\*======================================================================*/