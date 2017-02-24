<?php if (!defined('VB_ENTRY')) die('Access denied.');

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

/**
 * @package vbdbsearch
 * @author Kevin Sours, vBulletin Development Team
 * @version $Revision: 28678 $
 * @since $Date: 2008-12-03 16:54:12 +0000 (Wed, 03 Dec 2008) $
 * @copyright vBulletin Solutions Inc.
 */

require_once (DIR . '/packages/vbdbsearch/indexer.php');
require_once (DIR . '/packages/vbdbsearch/coresearchcontroller.php');
require_once (DIR . '/packages/vbdbsearch/postindexcontroller.php');

/**
*/
class vBDBSearch_Core extends vB_Search_Core
{
	/**
	 * Enter description here...
	 *
	 */
	static function init()
	{
		//register implementation objects with the search system.
		$search = vB_Search_Core::get_instance();
		$search->register_core_indexer(new vBDBSearch_Indexer());
		$search->register_index_controller('vBForum', 'Post', new vBDBSearch_PostIndexController());
		$__vBDBSearch_CoreSearchController = new vBDBSearch_CoreSearchController();
		$search->register_default_controller($__vBDBSearch_CoreSearchController);
//		$search->register_search_controller('vBForum', 'Post',$__vBDBSearch_CoreSearchController);
	}

}

/*======================================================================*\
|| ####################################################################
|| # Downloaded: 06:01, Fri Aug 3rd 2012
|| # SVN: $Revision: 28678 $
|| ####################################################################
\*======================================================================*/

